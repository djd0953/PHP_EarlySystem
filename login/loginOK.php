<?php
	session_start();

	$data = json_decode(file_get_contents('php://input'), true);

	$id = urldecode(base64_decode(addcslashes($data['id'],'')));
	$password = base64_decode($data['pw']);
	$pw = strtoupper(hash("sha512", $password));
	$ip = base64_decode($data["ip"]);
	$result = array();

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
	
	$loginOKDao = new WB_USER_DAO;
	$loginOKVo = new WB_USER_VO;
	
	$logDao = new WB_LOG_DAO;
	$logVo = new WB_LOG_VO;
	$logSearchVo = new WB_LOG_VO;
	
	
	$logVo->RegDate = date("Y-m-d H:i:s");
	$logVo->ip = $ip;
	$logVo->userID = $id;
	$logVo->pType = "login";
	$logVo->Page = "login.php";
	
	// 들어온 IP를 토대로 Log 뒤적뒤적
	$failDate = date("Y-m-d H:i:s", strtotime("-10 minutes"));
	$logSearchVo = $logDao->SELECT_SINGLE("userID = '{$id}' AND RegDate >= '{$failDate}' AND EventType = 'login Block'");

	if( isset($logSearchVo->{key($logSearchVo)}) )
	{
		// Block 중인 Log 확인했을때 남은시간과 함께 Return!!
		$diffTime = new DateTime($logSearchVo->RegDate);
		$inputTime = new DateTime();
		$interval = $inputTime->diff($diffTime);

		$h = 9 - $interval->i;
		$s = 60 - $interval->s;

		$result["code"] = "03";
		$result["msg"] = "block";
		$result["endTime"] = "{$h}분 {$s}초";
	}
	else
	{
		// ID PW가 일치하는지 확인!
		$loginOKVo = $loginOKDao->SELECT_SINGLE("uId = '{$id}' AND uPwd = '{$pw}'");

		if( isset($loginOKVo->{key($loginOKVo)}) )
		{
			$ipChk = true;
			if( $loginOKVo->ipUse == "Y" )
			{
				if( $loginOKVo->ip )
				{
					$mask = 4;
					$allIpArea = explode(".", $loginOKVo->ip);
					$sIpArea = explode(".", $ip);
					for( $i = 0; $i < 4; $i++ )
					{
						if( $allIpArea[$i] == "*" ) $mask--;
					}
	
					for( $i = 0; $i < $mask; $i++ )
					{
						if( $allIpArea[$i] != $sIpArea[$i] ) $ipChk = false;
					}
				}
				else
				{
					// ipUse는 Y인데 ip가 비어있다면 최초 로그인으로 DB에 IP등록
					$loginOKVo->ip = $ip;
					$loginOKDao->UPDATE($loginOKVo);
				}
			}

			if( !$ipChk )
			{
				// ID PW는 일치하지만 IP가 일치하지 않음
				$result["code"] = "02";
				$result["msg"] = "ip";

				$logVo->EventType = "login Fail";
				$logDao->INSERT($logVo);
			}
			else if( $loginOKVo->Auth == null )
			{
				// 관리자에 의한 승인이 되지 않음
				$result["code"] = "04";
			}
			else
			{
				// Login 성공
				$result["code"] = "00";

				$logVo->EventType = "login Success";
				$logDao->INSERT($logVo);

				// 로그인 이후 강제 세션 만료 시킨 후 다시 세션을 시작함 (로그인 창에서 세션 만료되고 들어오는 사용자를 위한 로직)
				if( !isset($_SESSION["system"]) ) $system = "ai"; else $system = $_SESSION["system"];

				session_destroy();
				session_start();

				$_SESSION['system'] = $system;
			
				if($_SESSION['system'] == 'flood') 
				{
					$_SESSION['title'] = '둔치주차장 침수차단시스템';
					$_SESSION['enTitle'] = 'dangerous area of slope system';
					$_SESSION['color'] = "#6c34dd;";
					$_SESSION['backgroundColorHover'] = "background-color:#3C1097;";
				}
				else if($_SESSION['system'] == 'warning') 
				{
					$_SESSION['title'] = '조기예경보시스템';
					$_SESSION['enTitle'] = 'ealry warning system';
					$_SESSION['color'] = "#01a25e;";
					$_SESSION['backgroundColorHover'] = "background-color:#0A7747;";
				}
				else if($_SESSION['system'] == 'dplace') 
				{
					$_SESSION['title'] = '급경사지시스템';
					$_SESSION['enTitle'] = 'dangerous area of slope system';
					$_SESSION['color'] = "#01a25e;";
					$_SESSION['backgroundColorHover'] = "background-color:#0A7747;";
				}
				else if($_SESSION['system'] == 'ai') 
				{
					$_SESSION['title'] = '지능형 통합관제 시스템 v1.0';
					$_SESSION['enTitle'] = 'Intelligent integrated control system';
					$_SESSION['color'] = "#6c34dd;";
					$_SESSION['backgroundColorHover'] = "background-color:#3C1097;";
				}

				$_SESSION["sessionUseTime"] = 30 * 60; // Session 유지 시간
				$_SESSION["lastSessionUseTime"] = time();
				$_SESSION['ip'] = $ip;
				$_SESSION["userIdx"] = $loginOKVo->idx;
				if( $loginOKVo->Auth == "root" ) $_SESSION["Auth"] = 0;
				else if( $loginOKVo->Auth == "admin" ) $_SESSION["Auth"] = 1;
				else if( $loginOKVo->Auth == "guest" ) $_SESSION["Auth"] = 2;
				else $_SESSION["Auth"] = 3;
			}
		}
		else
		{
			// ID PW가 일치하지 않음! 만약 9회째 실패라면 Block 처리 (10분 지연 예정)
			$logSearchVo = $logDao->SELECT("userID = '{$id}' AND RegDate >= '{$failDate}' AND pType = 'login'", "idx ASC");
			if( isset($logSearchVo[0]->{key($logSearchVo[0])}) )
			{
				$logCnt = 0;
				foreach( $logSearchVo as $v )
				{
					if( $v->EventType == "login Fail") $logCnt++;
					else if( $v->EventType == "login Success") $logCnt = 0;
				}

				// Log 실패 기록이 있다면!
				if( $logCnt >= 9 )
				{
					// 9회째 실패라면 블록으로 처리
					$result["code"] = "01";
					$result["msg"] = "block";
					$result["endTime"] = "10분 0초";

					$logVo->EventType = "login Fail";
					$logDao->INSERT($logVo);

					$logVo->EventType = "login Block";
					$logDao->INSERT($logVo);
				}
				else
				{
					// 아직 실패 기록이 9회 미만이라면 실패 횟수 올리고 Return
					$result["code"] = "01";
					$result["msg"] = $logCnt + 1;

					$logVo->EventType = "login Fail";
					$logDao->INSERT($logVo);
				}
			}
			else
			{
				// 로그에 실패기록이 없던 사용자 실패 횟수 올리고 Return
				$result["code"] = "01";
				$result["msg"] = 1;

				$logVo->EventType = "login Fail";
				$logDao->INSERT($logVo);
			}
		}
	}

	echo json_encode($result);
?>
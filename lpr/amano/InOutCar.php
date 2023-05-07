<?php
	header('Content-Type: application/json; charset=UTF-8');
	include_once "db.php";

	//if (!in_array('application/json', explode(';', $_SERVER['CONTENT_TYPE']) ) )
	//{
	//	echo json_encode(array('result_code'=>'400'));
	//	exit;
	//}

	$req_json = file_get_contents("php://input");

	$fpJson = fopen("log/logJson_".date("ymd",strtotime("Now")).".txt", "a");
	fwrite($fpJson, date("H:i:s", time())."] \r\n{$req_json}\r\n");
	fclose($fpJson);

    $fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
	fwrite($fp, date("H:i:s", time())."");

	// $req_json = file_get_contents("LPRResult.json");
	// $req_json = preg_replace('/[\x00-\x1F\x7F]/u', '', $req_json);

	// $req_json = '{
	// 	"eventName":"Entered car event",
	// 	"eventType":"EnteredCar",
	// 	"aptIdx":"1",
	// 	"eqpmID":9,
	// 	"lotArea":20,
	// 	"carNumber":"58가7868",
	// 	"eventTime":"20200717210432",
	// 	"dongcode":"",
	// 	"hocode":"",
	// 	"userName":"",
	// 	"isCustDef":false,
	// 	"iID":12933,
	// 	"inEqpmID":9,
	// 	"inDtm":"20220315153050",
	// 	"passType":"normal",
	// 	"isCustDc":false,
	// 	"custDefUserID":100,
	// 	"custDcUserID":11,
	// 	"carImagePath":"http://{IP or Domain}/image/20210414/CH01_20200717210432_58가7868.jpg"
	// }';
	
	try
	{
		if (strlen($req_json) < 1)
		{
			fwrite($fp, " / Request json value Null!\r\n");
			fclose($fp);
			throw new Exception("Request json value Null", 400);
		}
	
		$arrv = json_decode($req_json, true);	//배열로 변환
	
		$chk = false;
		$iID = $arrv['iID'];
		$bin = "NULL";
	
		$carNum = iconv('utf-8', 'euc-kr', $arrv["carNumber"]);
		$url = substr($arrv["carImagePath"],strpos(substr($arrv["carImagePath"],8),'/')+8,strlen($arrv["carImagePath"]));
		$path = iconv('utf-8', 'euc-kr', $url);
	
		$carNum = $arrv["carNumber"];
		$path = $arrv["carImagePath"];
	
		if(strlen($carNum) > 0) $chk = true;			//차량번호 수신시 이후 처리
	
		// Database Insert
		if ($chk)
		{
			// serial Code define
			// 0000-0000-0000-0000-0000-ABCD  A:ParkGroupCode (0~9) , B:IN/OUT (0:IN, 1:OUT) , CD:index, amn_Equip.CD_dist_OBSV매칭 (00~99)
	
			$sGcode = $arrv['lotArea'];					// 시리얼코드 1번째자리,  주차장그룹코드 (0~9)
			if(strlen($sGcode) > 3) $sGcode = '10';
	
			if (strpos(strtolower($arrv["eventType"]),"entered") !== false) $sType = '0';   // 시리얼코드 2번째자리,  0:입차, 1:출차
			else if (strpos(strtolower($arrv["eventType"]),"exited") !== false) $sType = '1';
			else
			{
				fwrite($fp, " / EventType incorrectly entered!\r\n");
				fclose($fp);
				throw new Exception("EventType incorrectly entered", 400);
			}
			
			$code = sprintf("%02d", intval($arrv["eqpmID"]));
			// if(intval($arrv['eqpmID']) < 10) $code = '0'.$arrv['eqpmID'];
			// else $code = $arrv['eqpmID'];
	
			// if($sGcode == '20' && ($code == '01' || $code == '03' || $code == '05')) $sType = "0";
			// else $sType = '1';
			
			$serial = $sGcode.$sType.$code;
	
			$sDateTime = date("Y-m-d H:i:s",strtotime($arrv["eventTime"]));		// YYYY-MM-DD hh:mm:ss type
			$sHour = (int) date("H",strtotime($arrv["eventTime"]));     		// Hour
			
			if($sHour == 0) 
			{
				$sDate = date("Ymd",strtotime($arrv["eventTime"]) - 1000000);
				$sHour = 24;
			}
			else $sDate = date("Ymd",strtotime($arrv["eventTime"]));					// YYYYMMDD type date
			fwrite($fp, " / GateSerial : ".$serial." / CarNum : ".$carNum." / ".$arrv["eventType"]);
			
			//No_Detection 테이블 따로 관리
			//if($carNum == "No_Detection") $tableName = "wb_parkcarerr";
			//else $tableName = "wb_parkcarhist";
			
			$tableName = "wb_parkcarhist";
			
			//fclose($fp);
			
			/* 20211019 처리보완 v0.3 Hong
			입차시 >> 
				[조회] 내역조회(최근1분, 차번) --> 내역 중복 없을시
					[입출차내역] 입차기록 (입차기록은 시간만 차이나면 계속 입력해서 기록보존)
					[현재주차내역] 내역조회(차번) --> 추가 또는 업데이트 처리(차번 유일하게) 
					[입차카운트] 입차카운트 ++
	
			출차시 >>
				[조회] 내역조회(최근1분, 차번) --> 내역 중복 없을시
					[입출차내역] 출차기록 (출차기록은 시간만 차이나면 계속 입력해서 기록보존)
					[현재주차내역] 현재주차 삭제 (차번으로 일괄 삭제)
					[출차카운트] 출차카운트 ++ 
			*/
	
			//$Prevdate  = date("Y-m-d H:i:s", strtotime("-1 minute"));	//비교 기준시간
		
			$info_vo = new WB_PARKCAR_VO();
			$info_vo->GateDate = $sDateTime;
			$info_vo->GateSerial = $serial;
			$info_vo->CarNum = $carNum;
			$info_vo->CarNum_Img = $sType;
			$info_vo->CarNum_Imgname = $path;
	
			$hist_dao = new DAO("wb_parkcarhist", "GateDate DESC", "WB_PARKCAR_VO");
			$now_dao = new DAO("wb_parkcarnow", "GateDate DESC", "WB_PARKCAR_VO");
			$hist_dao->INSERT($info_vo);
			
			if ($sType== 0)		// 시리얼코드 2번째 자리가 0이면 입차 - - - -
			{
				$now_dao->INSERT($info_vo);
	
				{
					$count_dao = new DAO("jhcarin", "JHDate DESC", "JHCar");
					$count_vo = $count_dao->SINGLE("JHDate = '{$sDate}' AND JHAreaCode = '{$serial}'");
	
					$G = date("G") + 1;
	
					if ($count_vo->{key($count_vo)})
					{
						$count_vo->{"JHHour{$G}"} = intval($count_vo->{"JHHour{$G}"}) + 1;
						$count_dao->UpdateCarCount($count_vo);
					}
					else
					{
						$count_vo->JHAreaCode = $serial;
						$count_vo->JHDate = $sDate;
						$count_vo->{"JHHour{$G}"} = 1;
						$count_dao->INSERT($count_vo);
					}
				}
				// //amn_ParkCarHist (차량출입내역 추가-입출차 공통)
				// $sql2 = "INSERT into ".$tableName." (GateDate, GateSerial, CarNum, CarNum_img, CarNum_imgname) 
				// 			values ('".$sDateTime."', '".$serial."', '".$carNum."', '".$bin."', '".$path."' )";
				// //$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// //fwrite($fp, "sql2 : ".$sql2."\r\n");
				// //fclose($fp);
				// mysql_query($sql2, $conn);
				
				// $sql3 = "INSERT into wb_ParkCarNow (GateDate, GateSerial, CarNum, CarNum_img, CarNum_imgname) 
				// 			values ('".$sDateTime."', '".$serial."', '".$carNum."', '".$bin."', '".$path."' )";
				// //$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// //fwrite($fp, "sql3 : ".$sql3."\r\n");
				// //fclose($fp);
				// mysql_query($sql3, $conn);
	
				// //amn_ParkCarInCnt (입차카운트 누적-기존내역 조회)
				// $sql4 = "SELECT JHDate from jhcarin where JHDate = '".$sDate."' and JHAreaCode = '".$serial."'";
				// //$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// //fwrite($fp, "sql4 : ".$sql4."\r\n");
				// //fclose($fp);
				// $res4 = mysql_query($sql4, $conn);
				// $Incount = mysql_num_rows($res4);
	
				// if($Incount > 0) 
				// {
				// 		$sql5 = "UPDATE jhcarin set JHHour".$sHour."=JHHour".$sHour."+1 
				// 					where JHAreaCode='".$serial."' and JHDate='".$sDate."'";
				// 		//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// 		//fwrite($fp, "sql5 : ".$sql5."\r\n");
				// 		//fclose($fp);
				// 		mysql_query($sql5, $conn);
				// }
				// else
				// {
					
				// 		$sql5 = "INSERT into jhcarin (JHAreaCode, JHDate, JHHour1, JHHour2, JHHour3, JHHour4, JHHour5, JHHour6, JHHour7, JHHour8, JHHour9, JHHour10,
				// 				JHHour11, JHHour12, JHHour13, JHHour14, JHHour15, JHHour16, JHHour17, JHHour18, JHHour19, JHHour20, JHHour21, JHHour22, JHHour23, JHHour24) 
				// 				values ('".$serial."', '".$sDate."', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
				// 					0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
				// 		//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// 		//fwrite($fp, "sql5 : ".$sql5."\r\n");
				// 		//fclose($fp);
				// 		mysql_query($sql5, $conn);
	
				// 		$sql5 = "UPDATE jhcarin set JHHour".$sHour."=JHHour".$sHour."+1 
				// 					where JHAreaCode='".$serial."' and JHDate='".$sDate."'";
				// 		//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// 		//fwrite($fp, "sql5-1 : ".$sql5."\r\n");
				// 		//fclose($fp);
				// 		mysql_query($sql5, $conn);
				// }				
			}
			else if ($sType==1)		// 시리얼코드 2번째 자리가 1이면 출차 - - - - 
			{
				$info_vo = $now_dao->SINGLE("CarNum = '{$info_vo->CarNum}'");
				$now_dao->DELETE($info_vo);
	
				{
					$count_dao = new DAO("jhcarout", "JHDate DESC", "JHCar");
					$count_vo = $count_dao->SINGLE("JHDate = '{$sDate}' AND JHAreaCode = '{$serial}'");
					
					$G = date("G") + 1;
					
					if ($count_vo->{key($count_vo)})
					{
						$count_vo->{"JHHour{$G}"} = intval($count_vo->{"JHHour{$G}"}) + 1;
						$count_dao->UpdateCarCount($count_vo);
					}
					else
					{
						$count_vo->JHAreaCode = $serial;
						$count_vo->JHDate = $sDate;
						$count_vo->{"JHHour{$G}"} = 1;
						$count_dao->INSERT($count_vo);
					}
					
				}
				// //amn_ParkCarHist (차량출입내역 추가-입출차 공통)
				// $sql2 = "INSERT into ".$tableName." (GateDate, GateSerial, CarNum, CarNum_img, CarNum_imgname) 
				// values ('".$sDateTime."', '".$serial."', '".$carNum."', '".$bin."', '".$path."' )";
				// //$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// //fwrite($fp, "sql2 : ".$sql2."\r\n");
				// //fclose($fp);
				// mysql_query($sql2, $conn);
				
				// $sql3 = "DELETE FROM wb_ParkCarNow WHERE CarNum = '{$carNum}'";
				// //$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// //fwrite($fp, "sql3 : ".$sql3."\r\n");
				// //fclose($fp);
				// mysql_query($sql3, $conn);
	
				// //amn_ParkcaroutCnt (입차카운트 누적-기존내역 조회)
				// $sql4 = "SELECT JHDate from jhcarout where JHDate = '".$sDate."' and JHAreaCode = '".$serial."'";
				// //$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// //fwrite($fp, "sql4 : ".$sql4."\r\n");
				// //fclose($fp);
				// $res4 = mysql_query($sql4, $conn);
				// $Incount = mysql_num_rows($res4);
	
				// if($Incount > 0) 
				// {
				// 		$sql5 = "UPDATE jhcarout set JHHour".$sHour."=JHHour".$sHour."+1 
				// 					where JHAreaCode='".$serial."' and JHDate='".$sDate."'";
				// 		//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// 		//fwrite($fp, "sql5 : ".$sql5."\r\n");
				// 		//fclose($fp);
				// 		mysql_query($sql5, $conn);
				// }
				// else
				// {
				// 		$sql5 = "INSERT into jhcarout (JHAreaCode, JHDate, JHHour1, JHHour2, JHHour3, JHHour4, JHHour5, JHHour6, JHHour7, JHHour8, JHHour9, JHHour10,
				// 				JHHour11, JHHour12, JHHour13, JHHour14, JHHour15, JHHour16, JHHour17, JHHour18, JHHour19, JHHour20, JHHour21, JHHour22, JHHour23, JHHour24) 
				// 				values ('".$serial."', '".$sDate."', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
				// 					0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";
				// 		//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// 		//fwrite($fp, "sql5 : ".$sql5."\r\n");
				// 		//fclose($fp);
				// 		mysql_query($sql5, $conn);
	
				// 		$sql5 = "UPDATE jhcarout set JHHour".$sHour."=JHHour".$sHour."+1 
				// 					where JHAreaCode='".$serial."' and JHDate='".$sDate."'";
				// 		//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
				// 		//fwrite($fp, "sql5-1 : ".$sql5."\r\n");
				// 		//fclose($fp);
				// 		mysql_query($sql5, $conn);
				// }
			}
			
			//$fp = fopen("log/log_".date("ymd",strtotime("Now")).".txt", "a");
			$gResult = iconv("UTF-8","EUC-KR"," / 정상 입력");
			//fwrite($fp, $gResult."\r\n");
			fwrite($fp, " / 정상 입력 \r\n");
			fclose($fp);
			
			echo json_encode(array('result_code'=>'200'));
		}
		else
		{
			fwrite($fp, " / Request Value is Null\r\n");
			fclose($fp);
			throw new Exception("Request Value is Null", 400);
		}
	}
	catch(Exception $e)
	{
		$dao = new DAO("wb_parkcarhist", "GateDate DESC", "WB_PARKCAR_VO");
		$vo = new WB_PARKCAR_VO();

		$vo->json = $req_json;
		$dao->INSERT($vo);
		
		$res = array("result_code" => $e->getCode(), "result" => $e->getMessage());
		echo json_encode($res);
	}
?>
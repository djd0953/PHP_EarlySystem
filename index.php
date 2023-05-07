<?php
	session_start();

	if( !isset($_SESSION['system']) ) $_SESSION["system"] = 'ai'; // Login창에서 세션 유지 시간을 넘는 유저를 위해 loginOK.php에서 한번 더 설정
	
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

	
	header("Content-Type:text/html;charset=utf-8");
	
	if( !isset($_SESSION["lastSessionUseTime"]) ) 
	{
		echo "<script>window.location.replace('login/login.php')</script>";
	} 
	else 
	{
		$_SESSION["sessionUseTime"] = 30 * 60; // Session 유지 시간
		$_SESSION["lastSessionUseTime"] = time();
		echo "<script>window.location.replace('/main.php')</script>";
	}
?>

<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";

	if( isset($_SESSION['userIdx']) ) 
	{
		include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

		$dao = new WB_USER_DAO;
		$userIdx = $_SESSION['userIdx'];
		$vo = $dao->SELECT_SINGLE("idx = '{$userIdx}'");

		$uid = $vo->uId;
		$Auth = $vo->Auth;
	}
	else
	{
		echo "<script>window.location.replace('/index.php')</script>";
	}
?>

<!doctype html>
<html>
<head>
<title><?=$_SESSION['title']?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="shortcut icon" href="image/favicon.ico">	<!-- ico 파일 -->
<link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumSquare.css" />
<link rel="stylesheet" type="text/css" href="/css/frame.css" />
<link rel="stylesheet" type="text/css" href="/css/include.css" />
</head>

<body>
	<?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/menu.php"; ?>
    <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/top.php"; ?>
	<?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/popup.php"; ?>
  	
    <div class="cs_frame_box" id="id_frame_box" style="margin-top:90px;overflow:hidden;background-size:100% 100%;">
	</div>
	<div class="cs_loading" id="id_loading" style="display:none; background-color:rgba(0,0,0,0.10);top:90px;">
		<div class="cs_message" style="top:70%;left:63%;height:40px;margin-top:0px;margin-left:0px;padding-top:8px;">The Map is Not Connected</div>
	</div>
    
<script> 
    var pType = "main";
	sessionStorage.setItem('uid', "<?=$uid?>");
	sessionStorage.setItem('ip',"<?=$_SESSION['ip']?>");
	sessionStorage.setItem("auth", "<?=$Auth?>")
	sessionStorage.setItem("systemType", "<?=$_SESSION['system']?>");
</script>
<script src="/js/include.js"></script>
<script src="/js/Chart.min.js"></script>

<!-- Map 호출 -->
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=f4592e97c349ab41d02ff73bd314a201&libraries=services"></script>
<script src="/js/map.js"></script>

</body>
</html>
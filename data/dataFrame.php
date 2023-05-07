<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	
	if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = "";}
	if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "first";}

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";

	if($dType == "first")
	{
		$menuKindSql = "SELECT DISTINCT GB_OBSV AS GB FROM wb_equip WHERE USE_YN = '1' ORDER BY GB_OBSV";
		$menuKindRes = mysqli_query($conn, $menuKindSql);
		$i = 0;
		$menuKindArr = array();
		while($menuKindRow = mysqli_fetch_assoc($menuKindRes)) $menuKindArr[$i++] = $menuKindRow['GB'];

		if($menuKindArr[0] == "01") $equip = "rain";
		else if($menuKindArr[0] == "02") $equip = "water";
		else if($menuKindArr[0] == "03") $equip = "dplace";
		else if($menuKindArr[0] == "21") $equip = "flood";
		else $equip = "rain";
		$dType = "na";
	}
?>
<!doctype html>
<html>
<head>
<title><?=$_SESSION['title']?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumsquare.css" />
<link rel="stylesheet" type="text/css" href="/css/include.css" />
<link rel="stylesheet" type="text/css" href="/css/frame.css" />
<link rel="shortcut icon" href="/image/favicon.ico">	<!-- ico 파일 -->

<style>
	canvas
	{
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;

		margin-bottom:60px;
	}

	.cs_datatable th
	{
		border:none;
	}

	.cs_datatable td
	{
		border:none;
		border-bottom:1px solid #cfcfcf;
	}

	select
	{
		margin-left:auto;
	}
</style>
</head>
<body>
	<?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php"; ?>
  	<?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/menu.php"; ?>
    <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/top_sub.php"; ?>
	<?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/popup.php"; ?>
    
	<div class="cs_frame_box" id="id_frame_box"></div>
    
	<script> 
		let type = "<?=$dType?>";
		let equip = "<?=$equip?>";
		let pType = "data"; 
	</script>
   	<script src="/js/jquery-1.9.1.js"></script>
   	<script src="/js/include.js"></script>
   	<script>
	$(document).ready(function(e) 
	{
		$(document).on("click", "#id_search", function(e)
		{
			e.stopPropagation();
			sub_mit("data");
		});

		$(document).on("change", "#id_select", function(e)
		{
			e.stopPropagation();
			sub_mit("data");
		});

		$(document).on("click", "#id_search_graph", function(e)
		{
			e.stopPropagation();
			sub_mit("graph");
		});

		$(document).on("change", "#id_select_graph", function(e)
		{
			e.stopPropagation();
			sub_mit("graph");
		});
		
		$(document).on("click", "#id_excel", function()
		{
			let url = $("#id_form").serialize();

			
			if($("#id_addr").val() == "Time.php")
			{
				url = "TimeExcel" + url.substr(9,(url.length-9));
			}
			else if($("#id_addr").val() == "Day.php")
			{
				url = "DayExcel" + url.substr(8,(url.length-8));
			}
			else if($("#id_addr").val() == "Month.php")
			{
				url = "MonthExcel" + url.substr(10,(url.length-10));
			}
			else if($("#id_addr").val() == "Year.php")
			{
				url = "YearExcel" + url.substr(9,(url.length-9));
			}
			else if($("#id_addr").val() == "Period.php")
			{
				url = "PeriodExcel" + url.substr(11,(url.length-11));
			}

			url = "table/excel/" + url.replace('&','?');
			
			window.location.href = url;
		});
    });

	function sub_mit(type)
	{
		let url = $("#id_form").serialize();
		let dType = document.getElementsByName("dType");
		let idx = 0;

		switch(dType[0].value)
		{
			case "rain":
				idx = 0;
				dType = "rain";
				break;
			case "water":
				idx = 1;
				dType = "water";
				break;
			case "dplace":
				idx = 2;
				dType = "dplace";
				break;
			case "snow":
				idx = 3;
				dType = "snow";
				break;
			case "flood":
				idx = 4;
				dType = "flood";
				break;
			default :
				idx = 0;
				dType = "rain";
		}

		url = url.substr(5,(url.length-5));
		if( type == "data" ) url = "table/" + url.replace('&','?');
		else 
		{
			idx += 5;
			url = "graph/" + url.replace('&','?');
		}

		getFrame(`${url}&dType=${dType}`, pType, idx, "false");
	}
   	</script>
</body>
</html>

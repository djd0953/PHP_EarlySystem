<!doctype html>
<html>
<head>
	<title>침수차단알림 안내문자 발송</title>
	<link rel="stylesheet" type="text/css" href="/css/include.css" />
	<link rel="stylesheet" type="text/css" href="/css/frame.css" />
</head>
<body>
	<?php
		$num = $_GET['num'];

		include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";

		$sql = "SELECT * FROM wb_parksmsment";
		$res = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($res);
	?>
	<div>
		<div><textarea name="content" id="id_content"style="font-family:'Nanum Square';resize:none;border:none;width:500px;height:80px;border:1px solid #d9d9d9;"><?=$row['Content']?></textarea></div>
		<div class="cs_btn" id="id_sendbtn" >차량안내문자 발송요청</div>
		<input type="hidden" id="id_number" value="<?=$num?>">
	</div>
<script src="/js/include.js"></script>
<script src="/js/jquery-1.9.1.js"></script>
<script>
	$(document).ready(function()
	{
		$(document).on("click", "#id_sendbtn", function()
		{
			let content = document.querySelector("#id_content").value;
			let num = document.querySelector("#id_number").value;

			if(content == "")
			{
				alert("전송할 내용을 입력해주세요.");
				return false;
			}

			$.ajax(
			{
				url: "../server/usersms.php",
				type:"POST", 
				async:true,
				cache:false,
				data:{ type:"insert", content:content, num:num },
				success: function(data) 
				{
					let num = JSON.parse(data);
					alert("정상적으로 처리되었습니다.");
					getlog("gate", "frame/parkingCar.php", "Car SMS Send", num['num'], "", "", content);
					window.close();
				},
				error:function(request,status,error)
				{
					//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			})
		})
	})
</script>
</body>
</html>
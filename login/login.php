<?php
	session_start();

	if( !isset($_SESSION['system']) )
	{
		echo "<script>";
		echo "window.location.href = '/index.php'";
		echo "</script>";
	}

	if(isset($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else $ip = $_SERVER['REMOTE_ADDR'];
	$_SESSION['ip'] = $ip;
?>

<!doctype html>
<html>
<head>
<title><?=$_SESSION['title']?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="stylesheet" type="text/css" href="/css/login.css">
<link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumSquare.css" />
<link rel="shortcut icon" href="/image/favicon.ico">

<style>
	.cs_loginLeft, .cs_btn
	{
		background-color:<?=$_SESSION['color']?>;
	}
	.cs_btn:hover
	{
		background-color:<?=$_SESSION['backgroundColorHover']?>;
	}
</style>
</head>
<body>
<div class="cs_login_frame" style="padding:0px;">
	<div class="cs_container">
		<div class="cs_loginLeft">
			<div class="cs_SystemTitle">
				<div class="cs_icon" style="background-image:url(../image/<?=$_SESSION['system']?>/parkIcon.png)"></div>
				<div class="cs_Stitle"><?=$_SESSION['title']?></div>
				<div class="cs_Etitle"><?=$_SESSION['enTitle']?></div>
			</div>
		</div>    
		<div class="cs_center">
			<div class="cs_title">W E L C O M E</div>        
			<form name="form" id="id_form" action="loginOK.php" method="post">            
				<input type="text" placeholder = " ID | 아이디" id="id_id" name="id" autocomplete="off" maxlength="25" style="margin-top:42px; padding-left:10px">
				<br>
				<input type="password" placeholder = " PW | 비밀번호" id="id_pw" name="pw" maxlength="25" style="margin-top:14px;padding-left:10px">                
				<div style='display:flex;'>
					<div class="cs_btn" id='id_login'>L O G I N</div>
					<div style='width:50px;'></div>
					<div class='cs_btn' id='id_join' style='width:180px;'>J O I N</div>
				</div>
			</form>        
		</div> 
	</div>
</div>

<script>
    let item = sessionStorage.getItem("histoy");
    if(item == null)
    {
        var history = 
        {
            mode : "",
            url : "",
            type : "",
            idx : "",
			evt : ""
        };
        sessionStorage.setItem('history', JSON.stringify(history));
        sessionStorage.setItem('pStatus', "false");
    }
</script>
<script src="/js/jquery-1.9.1.js"></script>
<script>
	$(document).ready(function(e) {
		$(document).on("keyup", "#id_pw, #id_id", function(e){
			
				if (e.keyCode == 13) {
				$("#id_login").trigger("click");
			}
		});
		
		$(document).on("click", "#id_login", function(){
			var id = $("#id_id").val();
			var pwd = $("#id_pw").val();
			var pattern = /[#$^&()_+|<>?:;{}]/g;
			var strId = id.replace(/\s/g,"");
			var strpwd = pwd.replace(/\s/g,"");
			
			if(strId == '') {
				alert("아이디를 입력하세요");
				return;	
			}

			if(strpwd == '') {
				alert("비밀번호를 입력하세요");
				return;	
			}

			let form = FormToObject(document.querySelector("#id_form"));
			form.ip = btoa(encodeURI("<?=$ip?>"));
			form.id = btoa(encodeURI(form.id));
			form.pw = btoa(encodeURI(form.pw));
			loginOKChk(form);
		});

		$(document).on("click", "#id_join", () => 
		{
			window.location.href = "/login/join.php";
		})
	});

	function loginOKChk(f)
	{
		const loginAjax = new XMLHttpRequest();

		loginAjax.open("POST", "loginOK.php");
		loginAjax.setRequestHeader('content-type', 'application/json');
		loginAjax.send(JSON.stringify(f));

		loginAjax.onreadystatechange = (e) => 
		{
			const xhr = e.target;
			if( xhr.readyState === 4 && xhr.status === 200 )
			{
				let data = JSON.parse(xhr.response);

				switch( data.code )
				{
					case "00" :
						window.location.href = "/index.php";
						break;
	
					case "01" :
						if( data.msg == "block" ) alert(`10회 접속시도에 실패하여 10 후 다시 시도해주세요.`);
						else alert(`아이디/패스워드가 일치하지 않습니다. 10회 연속으로 실패할 경우 10분 접속이 제한됩니다. (접속시도 : ${data.msg})`);

						break;
						
					case "02" :
						alert(`해당 계정을 생성한 장소에서 접속하시기 바랍니다.`);
						break;
					
					case "03" :
						alert(`10회 접속시도에 실패하여 10분 후 다시 시도해주세요.`);
						break;
	
					case "04" :
						alert("접근이 허용되지 않았습니다. 관리자에게 문의해주세요.");
						break;

					case "6666" :
						alert("Reset 완료");
						break;
	
				}
			}
		}
	}

	function FormToObject(f)
	{
		let returnObject = new Object();
		try
		{
			let myFormData = new FormData(f);

			myFormData.forEach((value, key) => 
			{
				if( returnObject[key] == undefined )  returnObject[key] = value;
				else returnObject[key] += `,${value}`;
			});
		}
		catch(e)
		{
			console.log(e.message);
		}
		return returnObject;
	}
</script>
</body>
</html>
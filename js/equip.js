// JavaScript Document

$(document).ready(function(e) 
{
	$(document).on("click", "#id_allCheck", function()
	{
		var checked = $(this).is(":checked");
	
		if( checked == true )
		{
			$(".cs_eChk").prop("checked",true);	
		}
		else
		{
			$(".cs_eChk").prop("checked",false);
		}		
	});

	$(document).on("click", "#id_addbtn", function(){ getFrame("frame/equipChange.php?dType=add/0", pType, 0, "true"); })

	$(document).on("click", "#id_updbtn", function()
	{
		let equip = "";
		let count = 0;
		$(".cs_eChk:checked").each(function() 
		{ 
			if(count++ == 0) { equip = "'" + $(this).val() + "'"; } 
			else { equip = equip + ",'" + $(this).val() + "'"; }
		});
		
		if(equip == "")
		{
			alert("장비를 선택해주세요.");
			return false;
		}

		if(equip.includes(",") != true) getFrame(`frame/equipChange.php?dType=upd/${equip}`, pType, 0, "true");
		else getFrame("upds/" + equip, "frame/equipChange.php", pType, 0, "true");
	})

	$(document).on("click", ".cs_equiList", function()
	{
		let equip = "'" + $(this).attr("value") + "'";
		getFrame(`frame/equipChange.php?dType=upd/${equip}`, pType, 0, "true");
	})

	$(document).on("click", "#id_map", function()
	{
		window.open("/map.php");
	})

	$(document).on("click", "#id_groupChk", function()
	{
		var num = $(this).attr("value");

		$(".cs_groupChk").removeClass("active");
		$(this).addClass("active");

		setGroupPanel(num);
	});

	$(document).on("click", "#brdSetSend", function(e)
	{
		let code = e.target.attributes["data-type"].value;
		let parm1 = "";
		let parm2 = "";
		let equip = "";
		let chk = document.querySelectorAll(".cs_eChk");
		let cnt = 0;

		for(let i = 0; i < chk.length; i++)
		{
			if(chk[i].checked)
			{
				if(cnt++ == 0) equip = chk[i].value;
				else equip += "," + chk[i].value;
			}
		}

		if(equip == "")
		{
			alert("설정할 장비를 선택하세요.");
			return false;
		}
		else cnt = 0;

		if(code == "S080")
		{
			parm1 = document.querySelector("#vType").value;
			parm2 = document.querySelector("#volume").value;
		}
		else if(code == "S100")
		{
			let out = document.querySelectorAll(".output");

			for(let i = 0; i < out.length; i++)
			{
				if(out[i].checked)
				{
					if(cnt++ == 0) parm1 = out[i].value;
					else parm1 += "," + out[i].value;
				}
				else
				{
					if(cnt++ != 0) parm1 += ",";
				}
			}
		}
		else if(code == "S120")
		{
			parm1 = document.querySelector("#relay1").value;
			parm2 = document.querySelector("#relay2").value;
		}
		else if(code == "S140")
		{
			parm1 = document.querySelector("#sBell").value;
			parm2 = document.querySelector("#eBell").value;
		}

		setBrdSetting(code, parm1, parm2, equip);
	});
	
	
	
	$(document).on("click", ".cs_disEquList", function(e) 
	{
		let num = $(this).attr("data-num");
		getFrame(`frame/disequipDetail.php?num=${num}`, pType, 2, "true");
	});

	$(document).on("click", "#disSetSend", function(e)
	{
		let num = document.querySelector("#num").value;
		let code = e.target.attributes["data-type"].value;
		let parm1 = "";

		if(code == "S020")
		{
			let bright = [];
			for(let i = 0; i<24; i++ ) bright[i] = $("#id_bright_"+(i+1)).find("option:selected").val();

			parm1 = bright.join("/");
		}
		else if(code == "S040")
		{
			let nowDate = new Date();
			let year = nowDate.getFullYear();
			let month = ('0' + (nowDate.getMonth() +1)).slice(-2);
			let day = ('0' + (nowDate.getDate())).slice(-2);
			let hour = ('0' + (nowDate.getHours())).slice(-2);
			let minute = ('0' + (nowDate.getMinutes())).slice(-2);
			let second = ('0' + (nowDate.getSeconds())).slice(-2);
			
			parm1 = `${year}-${month}-${day} ${hour}:${minute}:${second}`;
		}
		else if(code == "S060")
		{
			let relay = [];
			for(let i = 1; i <= 4; i++)
			{
				relay[i] = document.querySelector(`input[name=relay${i}]:checked`).value;

				if(relay[i] == "ON") parm1 += "1";
				else parm1 += "0";
			}

			parm1 = parseInt(parm1,2);
		}
		
		setDisSetting(num, code, parm1);
	})	

	$(document).on("click", "#id_chgbtn", function()
	{
		let formobj = $("#id_form").serializeObject();

		let numberChk = /[^0-9]/g;
		let spcChk = /[~!@#$%^&*()_+-=?/,.|<>{}:;'"]/;
		let dateChk = /([0-2][0-9]{3})-([0-1][0-9])-([0-3][0-9]) ([0-5][0-9]):([0-5][0-9]):([0-5][0-9])(([\-\+]([0-1][0-9])\:00))?/;
		let errChk = /-?[0-5]/;
		let phoneChk = /([0-9]{10,12})/;
		let ipChk = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\:([0-9]{1,5})/;
		let latlonChk = /(3[0-9]).([0-9]{1,15}), ?(1[2-3][0-9]).([0-9]{1,15})/;

		if(formobj['equip21'] != "upds" && formobj['equip0'] == "") if(confirm("장비 번호가 입력되지 않으면 Auto_Increment로 인해 일련번호가 자동으로 입력됩니다.") != true) return false;

		if(formobj['equip21'] != "upds" && formobj['equip0'] != "" && numberChk.test(formobj['equip0']))
		{
			alert("장비 번호는 숫자만 입력이 가능합니다.");
			return false;
		}
		if(formobj['equip21'] != "upds" && formobj['equip1'] == "")
		{
			alert("장비 식별을 위해 식별 가능한 장비명을 입력해주세요 (7자 이하 권고)");
			return false;
		}
		if(formobj['equip21'] != "upds" && formobj['equip2'] != "" && !ipChk.test(formobj['equip2']))
		{
			alert("IP:Port 형식이 올바르지 않습니다.");
			return false;
		}
		if(formobj['equip11'] == "17" && formobj['equip16'] == "") if(confirm("방송 장비는 CDMA 번호가 비어있을 경우 Error발생 위험이 높아 입력을 권고드립니다. 그냥 진행 하시겠습니까?") != true) return false;
		if(formobj['equip21'] != "upds" && formobj['equip3'] != "" && !phoneChk.test(formobj['equip3']))
		{
			alert("CDMA번호 형식이 올바르지 않습니다.");
			return false;
		}
		if(formobj['equip4'] != "" && !latlonChk.test(formobj['equip4']))
		{
			alert("위/경도를 형식에 맞추어 입력해주세요. (ex:38.1234,127.1234)");
			return false;
		}
		if(formobj['equip11'] == "18" && (formobj['equip6'] == "" || formobj['equip7'])) if(confirm("전광판은 Size가 비어있을 경우 Error발생 위험이 높아 입력을 권고드립니다. 그냥 진행 하시겠습니까?") != true) return false;
		if(formobj['equip6'] != "" && numberChk.test(formobj['equip6']))
		{
			alert("전광판 Size는 숫자만 입력 가능합니다.");
			return false;
		}
		if(formobj['equip7'] != "" && numberChk.test(formobj['equip7']))
		{
			alert("전광판 Size는 숫자만 입력 가능합니다.");
			return false;
		}
		if(formobj['equip10'] != "" && !errChk.test(formobj['equip10']))
		{
			alert("ErrorCheck 번호는 -5~5 사이로 입력해주세요.");
			return false;
		}
		if(formobj['equip21'] != "upds" && formobj['equip11'] == "") if(confirm("GB_OBSV는 입력하시길 권고드립니다. 그냥 진행 하시겠습니까?") != true) return false;
		if(formobj['equip11'] != "" && numberChk.test(formobj['equip11']))
		{
			alert("GB_OBSV는 숫자만 입력이 가능합니다.");
			return false;
		}
		if(formobj['equip12'] != "" && !dateChk.test(formobj['equip12']))
		{
			alert("날짜 형식이 올바르지 않습니다.");
			return false;
		}
		if(formobj['equip14'] != "" && numberChk.test(formobj['equip14']))
		{
			alert("RainBit는 숫자만 입력이 가능합니다.");
			return false;
		}
		if(formobj['equip15'] != "" && numberChk.test(formobj['equip15']))
		{
			alert("SeeLevelUse는 숫자만 입력이 가능합니다.");
			return false;
		}
		if(formobj['equip11'] == "03" && formobj['equip16'] == "") if(confirm("변위 계측기는 SubOBCount가 비어있을 경우 Error발생 위험이 높아 입력을 권고드립니다. 그냥 진행 하시겠습니까?") != true) return false;
		if(formobj['equip16'] != "" && numberChk.test(formobj['equip16']))
		{
			alert("변위센서 갯수(SubOBCount)는 숫자만 입력이 가능합니다.");
			return false;
		}
		if(formobj['equip11'] == "17" && formobj['equip17']) if(confirm("방송 장비는 DetCode가 비어있을 경우 Error발생 위험이 높아 입력을 권고드립니다. 그냥 진행 하시겠습니까?") != true) return false;
		if(formobj['equip17'] != "" && numberChk.test(formobj['equip17']))
		{
			alert("방송 장비 일련번호는 숫자만 입력이 가능합니다.");
			return false;
		}

		if(confirm("DB에 입력 하시겠습니까??") == true) 
		{
			console.log(formobj);
			updatedb(JSON.stringify(formobj));
		}
	});
});

function updatedb(obj)
{
	$.ajax(
	{
		url: "server/equipUpdate.php",
		type:"POST",
		data: obj,
		async:true,
		cache:false,
		success: function(data) 
		{
			//console.log(data);
			let json = JSON.parse(data);
			if(json['code'] == "00") 
			{
				alert("정상적으로 처리되었습니다.");
				getFrame("frame/equip.php", pType, 0, "false");
			}
			else
			{
				alert("장비번호가 중복되어 DB에 반영되지 않았습니다. 장비번호를 다시 한번 확인해주세요.");
				console.log(json['code'] + " - " + json['msg']);
			}
		},
		error:function(request,status,error)
		{
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

function setGroupPanel(group_code)
{
	$.ajax({
		url: "server/equipBrdGroup.php",
		dataType:"html",
		type:"post", 
		data:{ group_code : group_code }, 
		async:false,
		cache:false,
		success: function(data) 
		{
			$("#id_equip").empty().append(data);
		},
		error:function(request,status,error){
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});		
}

function setBrdSetting(sType, param1, param2, equip )
{
	
	$.ajax({
		url: "server/setBrdSetting.php",
		dataType:"json",
		type:"post", 
		data:{ sType : sType, param1 : param1, param2 : param2, equip : equip }, 
		async:false,
		cache:false,
		success: function(data) 
		{
			if(data.code == "00")
			{
				alert("장비 상태 변경 중입니다.");
			}
		},
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});	
	
}

function setDisSetting( num, type, sendData )
{
	$.ajax({
		url: "server/setDisSetting.php",
		dataType:"json",
		type:"post", 
		data:{ num:num, type:type, sendData:sendData }, 
		async:false,
		cache:false,
		success: function(data) 
		{
			if(data.code == "00")
			{
				alert("장비제어중 입니다.");
			}
		},
		error:function(request,status,error)
		{
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});	
	
}
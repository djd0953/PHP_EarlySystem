// JavaScript Document

$(document).ready(function(e) 
{  
	/* Checkbox 전체 체크 (공용) */
	$(document).on("click", "#id_allCheck", function()
	{
		var checked = $(this).is(":checked");
		
		if( checked == true )
		{
			$(".cs_disChk").prop("checked",true);	
		}
		else
		{
			$(".cs_disChk").prop("checked",false);
		}
	});

	/* 날짜, 장비 검색! (공용) */
	$(document).on("click", "#id_search", function(event)
	{
		event.preventDefault();

		var url = $("#id_form").serialize();
		url = url.substr(4,url.length);
		url = "frame/" + url.replace('&','?');

		console.log(url);

		getFrame(url, pType, -1, "false");
	});

	/* Page (공용) */
	$(document).on("click","#id_page", function()
	{
		let url = $(this).attr("data-url");
		let idx = $(this).attr("data-idx");
		getFrame(url, pType, idx, "true");
	});

	////////////////* eachEquList 영역 *//////////////////
	/* 해당 장비 정보 진입! */
	$(document).on("click", "#id_disList", function()
	{	
		let num = $(this).attr("data-num");
		getFrame("frame/sendEachScen.php?num="+num+"&page=",pType, 0, "true");
	});

	////////////////* sendEachScen 영역 *//////////////////
	/* 시나리오 추가 */
	$(document).on("click", "#id_addEachScen",function()
	{
		let num = $(this).attr("data-num");
		let page = $(this).attr("data-page");
		
		getFrame("frame/eachScenForm.php?dType=insert&page="+page+"&num=&areaCode="+num, pType, 0, "true");
	});
	
	/* 시나리오 변경 */
	$(document).on("click", "#id_updEachScen", function()
	{
		let equNum = $(this).attr("value");
		let num = $(this).attr("data-num");
		let type = $(this).attr("data-type");
		let page = $(this).attr("data-page");
		
		if( type == "mois" ){
			alert("행안부에서 등록된 시나리오는 수정할 수 없습니다.");
			return;	
		}
		
		getFrame("frame/eachScenForm.php?dType=update&page="+page+"&num="+equNum+"&areaCode="+num, pType, 0, "true");
	});

	/* 해당 시나리오 종료 */
	$(document).on("click", "#id_endEachScen", function()
	{	
		let scen = $(this).attr("value");
		let num = $(this).attr("data-num");
		let page = $(this).attr("data-page");

		if(confirm('해당 시나리오를 종료하시겠습니까?'))
		{
			let formobj = {scen : scen, type : "end"};
			getScenario(formobj, num, page);	
		}
	});

	/* 해당 시나리오 삭제!!!! */
	$(document).on("click", "#id_delEachScen", function()
	{	
		let scen = "";
		let num = $(this).attr("data-num");
		let page = $(this).attr("data-page");
		
		$(".cs_disChk:checked").each(function() 
		{
			scen = scen + "," + $(this).attr("value");
		});

		if(scen == "")
		{
			alert("삭제할 시나리오가 선택되지 않았습니다.");
			return;
		}

		if(confirm('정말 삭제하시겠습니까?')) 
		{
			let formobj = {scen : scen.substring(1, scen.length), type : "delete"};
			getScenario(formobj, num, page);	
		}
	});

	/* 시나리오 전송~! */
	$(document).on("click", "#id_savEachScen", function(){
		
		let startDate = $("#startDate").val();
		let endDate = $("#endDate").val();
		let num = 1;
		let page = 1;
		let type = document.getElementsByName("mode");

		try
		{
			let dateChk = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
			let dateNum = /[^0-9]/;
			let dateRegex = startDate.split("-");
			dateRegex.forEach((e) => 
			{
				if( dateNum.test(e) ) throw "시나리오 시작/종료일을 다시 확인해 주세요";
			});

			let y = parseInt(dateRegex[0], 10),
				m = parseInt(dateRegex[1], 10),
				d = parseInt(dateRegex[2], 10);

			if( !dateChk.test(d + '-' + m + '-' + y) )
			{
				alert("시나리오 시작일을 다시 확인해 주세요");
				return;
			}

			dateRegex = endDate.split("-");
			dateRegex.forEach((e) => 
			{
				if( dateNum.test(e) ) throw "시나리오 시작/종료일을 다시 확인해 주세요";
			});

			y = parseInt(dateRegex[0], 10);
			m = parseInt(dateRegex[1], 10);
			d = parseInt(dateRegex[2], 10);

			if( !dateChk.test(d + '-' + m + '-' + y) )
			{
				alert("시나리오 종료일을 다시 확인해 주세요");
				return;
			}
		}
		catch(err)
		{
			alert(err);
			return;
		}

		if( type[0].value == "group")
		{
			if( startDate == "" )
			{
				alert("시나리오 시작일을 입력해 주세요.");
				return;	
			}
			if( endDate == "" )
			{
				alert("시나리오 종료일을 입력해 주세요.");
				return;	
			}
			var equip = "";

			$(".cs_disChk:checked").each(function()
			{
				equip = equip + "," + $(this).attr("value");
			})
			if(equip == "")
			{
				alert("장비를 선택해주세요.");
				return;
			}
			else 
			{
				equip = equip.substring(1,equip.length);
			}

		}
		else
		{
			num = document.getElementById("id_areaCode").value;
			page = $(this).attr("data-page");
			
			if( startDate == "" )
			{
				alert("시나리오 시작일을 입력해 주세요.");
				return;	
			}
			if( endDate == "" )
			{
				alert("시나리오 종료일을 입력해 주세요.");
				return;	
			}
		}	
		$(".cs_loading").css("display", "block");
		html2canvas(document.querySelector(".note-editable")).then(function(canvas)
		{
			var img = canvas.toDataURL("image/png");
			document.getElementById("imageTag").value = img;
		});
		
		setTimeout(function()
		{ 
			let formobj = $("#id_form").serializeObject();
			formobj.type = "save";
			if(type[0].value == "group") formobj.equip = equip;

			getScenario(formobj, num, page);
		},1000);
	});
});

function test(date)
{
	let dateChk = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
	let dateNum = /^[0-9]/;
	let dateRegex = date.split("-");
	dateRegex.forEach((e) => 
	{
		if( dateNum.test(e) )
		{
			alert("시나리오 시작일에는 숫자를 이용한 날짜 기입만 가능합니다.");
			return;
		}
	});

	let y = parseInt(dateRegex[0], 10),
		m = parseInt(dateRegex[1], 10),
		d = parseInt(dateRegex[2], 10);

	if( !dateChk.test(d + '-' + m + '-' + y) )
	{
		alert("시나리오 시작일을 다시 확인해 주세요");
		return;
	}
}

function getScenario(idx, num, page)
{
	$.ajax(
	{
		url: "server/displayScenario.php",
		dataType:"json",
		type:"POST", 
		data: JSON.stringify(idx),
		async:true,
		cache:false,
		success: function(data) 
		{
			if(data.code == "00")
			{
				alert("정상적으로 처리되었습니다.");
				getlog("display", "frame/sendEachScen.php", "Scenario Delete", num, data.msg);
				getFrame("frame/sendEachScen.php?num="+num+"&page="+page, pType, -1, "false");
			}
			else if(data.code == "10")
			{
				setTimeout(function()
				{ 
					$(".cs_loading").css("display", "none");
					getlog("display", "frame/sendEachScen.php", "Scenario Update", num, data.msg, data.after);
					getFrame("frame/sendEachScen.php?num="+num+"&page="+page, pType, -1, "false");
				}, 1000);
			}
			else if(data.code == "20")
			{
				setTimeout(function()
				{ 
					$(".cs_loading").css("display", "none");
					alert("정삭적으로 처리되었습니다.");
					getlog("display", "frame/groupEachScen.php", "Scenarios Update", num, "", data.msg);
					getFrame("frame/eachScenForm.php?dType=group", pType, -1, "false");
				}, 1000);
			}
		},
		error:function(request,status,error)
		{
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}
// JavaScript Document

$(document).ready(function() 
{
	$(document).on("click", "#id_allcheck", function()
	{
		var checked = $(this).is(":checked");
	
		if( checked == true )
		{
			$(".cs_gateChk").prop("checked",true);	
		}
		else
		{
			$(".cs_gateChk").prop("checked",false);
		}		
	});

	/* Page (공용) */
	$(document).on("click","#id_page", function()
	{
		let url = $(this).attr("data-url");
		let idx = $(this).attr("data-idx");

		if(idx == '1')
		{
			let form = $("#id_form").serialize();
			url += `&${form}&dType=after`;
		}
		else url += `dType=before`;
		getFrame(`${url}`, idx, "true");
	});

	/* Search! */
	$(document).on("click","#id_search", function()
	{
		let url = '';
		let type = $('input[name=mode]').val();
		let form = $("#id_form").serialize();
		form = form.substr(4,form.length);
		form = form.replace("&","?");

		if(type == "result") url = "frame/" + form + "&page=1&dType=after";
		else url = "frame/" + form + "dType=after";

		getFrame(`${url}`, -1, "false");
	});

	/* Excel! */
	$(document).on("click", "#id_excel", function()
	{
		let form = $("#id_form").serialize();
		form = form.substr(4,form.length);
		form = form.replace("&","?");
		form = form.replace(".","Excel.");

		let url = "frame/excel/" + form;
				
		window.location.href = url;
	});		

	/* 주차장그룹 디테일 진입 */
	$(document).on("click","#id_grpList", function() 
	{
		let num = $(this).attr("data-num");
		getFrame("frame/parkingCareAdd.php?num="+num, 0, "true");
	});

	/* 주차장그룹 등록/수정/삭제 */
	$(document).on("click", "#id_addbtn", function()
	{
		let num = "";
		let name = $("#id_title").val();	
		let addr1 = $("#id_addr1").val();
		let addr2 = $("#id_addr2").val();
		let type = $(this).attr("data-type");
		let code = '';
		
		if(type != "delete")
		{
			let count = 0;
			$(".cs_gateChk:checked").each(function() 
			{ 
				if(count++ == 0) { code = $(this).val(); } 
				else { code = code+","+ $(this).val(); }
			});
			
			if(name == "") 
			{
				alert("이름을 입력하세요");
				return;	
			}			
			
			if(addr1 == "") 
			{
				alert("주소를 입력하세요");
				return;	
			}
			
			if(code == "") 
			{
				alert("차단기를 선택하세요");	
				return;
			}
		}

		if(type != "insert") num = $(this).attr("data-num");
		
		saveAddr(num, name, addr1, addr2, code, type);
	});    

	/* 차량 입출차 내역 Delete! */
	$(document).on("click","#id_delbtn", function()
	{
		let num = '';
		let type = $(this).attr("data");
		let count = 0;

		$('.cs_gateChk:checked').each(function() 
		{
			if(count++ == 0) num = $(this).val();	
			else num = num+","+$(this).val();	
		});
		
		if(confirm("선택하신 내역을 정말 삭제하시겠습니까?") == true) removeCarHist(num, type);
	});

	/* Check 문자 발송 위해 popup 띄우기 */
	$(document).on("click","#id_msgbtn", function()
	{
		let num = '';
		let count = 0;

		$('.cs_gateChk:checked').each(function() 
		{
			if(count++ == 0) num = $(this).val();	
			else num = num+","+$(this).val();	
		});	

		if(num == "")
		{
			alert("문자 전송 할 차량을 선택하세요.");
			return false;
		}

		window.open(`frame/msgbox.php?num=${num}`,"Message Box","width=510, height=170, left=10, top=10, status=no, toolbar=no, scrollbars=no");
	});

	/* 차량 입출차 내역 차량 번호 이미지 활성화 */
	$(document).on("mouseover",".cs_imgLink",function(e)
	{
		var url = $(this).attr("data-url");
		let pageYHeight = 0;

		if(e.pageY >= 625) pageYHeight = 625;
		else pageYHeight = e.pageY;

        $("body").append("<div class='cs_imgBox'></div>");
        $(".cs_imgBox")
		.css('border','2px solid #5E60CD')
        .css("top",pageYHeight + "px")
        .css("left",e.pageX - 385 + "px")
        .fadeIn("fast");

		$.ajax(
		{
			url: url,
			dataType:"html",
			type:"GET", 
			async:true,
			cache:false,
			success: function(data) 
			{ 
				$(".cs_imgBox").html(data);
			},
			error:function(request,status,error)
			{
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		})
    });

    $(document).on("mouseout",".cs_imgLink",function(){ $(".cs_imgBox").remove(); });

	/* 일단 보류 */
	$(document).on("click", "#id_remove", function()
	{
		var code = "";
		var count = 0;
		$('.userCode:checked').each(function() 
		{
			if(count++ == 0) code = $(this).val();	
			else code = code+","+$(this).val();
		});
		
		if(code == "") 
		{
			alert("삭제할 번호를 선택해주세요");
			return;	
		}
					
		if(confirm("삭제하시겠습니까?") == true) { removeuser(code); }					
	});

	$(document).on("click","#id_addmsgbtn",function(){ getFrame("frame/parkingMentAdd.php?dType=before", 4, "true"); });

	/* 차단기 수동 제어 */
	$(document).on("click", "#id_gatebtn", function(e)
	{
		let num = $(this).attr("data-num");
		let gate = $(this).attr("data-type");
		
		if(confirm("차단기 상태를 변경하시겠습니까?") == true) 
		{
			let gateBtn = document.querySelectorAll(`.gate${num}`);
			
			gateBtn.forEach((el) => 
			{
				el.style.backgroundColor = "#5e60cd";
			})
			
			e.target.style.backgroundColor = "#282bca";
			saveGate(num,gate);	
		}	
	});
});


/* 차량 입출차 내역 삭제 */
function removeCarHist(num, type)
{
	$.ajax(
	{
		url: "server/removeuser.php",
		type:"POST", 
		data: { num:num, type:type },
		async:true,
		cache:false,
		success: function(data) 
		{
			let num = JSON.parse(data);
			alert("정상적으로 처리되었습니다.");
			getlog("gate", "frame/parkingCar.php", "Car History Delete", num['num']);
			getFrame("frame/parkingCar.php?dType=before", -1, "false");
		},
		error:function(request,status,error)
		{
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}

//주소 검색하기!! (오픈소스)
function sample6_execDaumPostcode() 
{
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

			// 각 주소의 노출 규칙에 따라 주소를 조합한다.
			// 내려오는 변수가주소 변수
			var extraAddr = ''; // 참고항목 변수

			//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
			if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
				addr = data.roadAddress;
			} else { // 사용자가 지번 주소를 선택했을 경우(J)
				addr = data.jibunAddress;
			}
		   

			// 우편번호와 주소 정보를 해당 필드에 넣는다.
			document.getElementById("id_addr1").value = addr;
			// 커서를 상세주소 필드로 이동한다.
			document.getElementById("id_addr2").focus(); 
			//값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
			var addr = ''; // 
		}
	}).open();    
}

function saveAddr(num, name, addr1, addr2, code, type) 
{
	$.ajax({
		url:'server/serverAddr.php',
		dataType:"json",
		type:"POST",
		async:true,
		cache:false,
		data:{ num:num, name:name, addr1:addr1, addr2:addr2, code:code, type:type },
		success: function(data){
			alert("저장되었습니다");

			if(data.code == "00") getlog("gate", "frame/parkingCare.php", "Parking Area Insert", data.equip, data.before, data.after);
			else if(data.code == "10") getlog("gate", "frame/parkingCare.php", "Parking Area Update", data.equip, data.before, data.after);
			else getlog("gate", "frame/parkingCare.php", "Parking Area Delete", data.equip, data.before, data.after);

			getFrame("frame/parkingCare.php",-1,"false");
		},
		error:function(request,status,error){
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}		
	});
}

function saveGate(num,gate) 
{
	$.ajax({
		url : "server/serverGate.php",
		dataType:"json",
		type:"POST",
		async:true,
		cache:false,
		data:{num:num, gate:gate, saveType:"save"},
		success: function(data){
			if(data.code == "00") 
			{
				alert("변경되었습니다");
				getlog("gate", "frame/passiveGate.php", "Gate Control", data.equip, "", gate);
			} 
			else 
			{
				alert(data.message);	
			}		
		},
		error:function(request,s0tatus,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});
}
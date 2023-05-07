// JavaScript Document
try
{
	var infowindow = new kakao.maps.InfoWindow({zIndex:1});
	var mapContainer = document.getElementById('id_frame_box'), // 지도를 표시할 div  
	mapOption = { 
		center: new kakao.maps.LatLng(37.57300926064723, 127.12835737202485), // 지도의 중심좌표
		level: 3// 지도의 확대 레벨
	};
	var positions = [];
	var map = new kakao.maps.Map(mapContainer, mapOption); // 지도를 생성합니다
	var bounds = new kakao.maps.LatLngBounds();

	/* 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다*/
	var mapTypeControl = new kakao.maps.MapTypeControl();
	map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPLEFT);

	/* 지도 확대 축소를 제어할 수 있는 줌 컨트롤을 생성합니다. */
	var zoomControl = new kakao.maps.ZoomControl();
	map.addControl(zoomControl, kakao.maps.ControlPosition.LEFT);

	$(document).ready(function() 
	{
		getMarker();
		
		$(document).on("click", ".cs_viewBtn", function()
		{
			var type = $(this).attr("data-type");
			var areaCode = $(this).attr("data-num");
			
			if( type == "rain" || type == "water"  || type == "dplace" || type == "flood" || type == "snow" ) window.location.href = "data/dataFrame.php?dType="+type+"&equip="+areaCode;
			else if( type == "alert" ) window.location.href = "broad/broadFrame.php?type="+type+"&areaCode="+areaCode;	
			else if( type == "display" ) window.location.href = "display/displayFrame.php?type="+type+"&areaCode="+areaCode;
			else if( type == "gate" ) window.location.href = "gate/gateFrame.php?type="+type+"&areaCode="+areaCode;
		});
	});
}
catch(e)
{
	let frame = document.getElementById("id_frame_box");
	frame.style.backgroundImage = "url('/image/map.png')";
	frame.style.backgroundSize = "100% 100%";
	$("#id_loading").css("display","block");
}

function getMarker()
{
	$.ajax({
		url: "/include/server/mainMap.php",
		dataType:"json",
		type:"post", 
		async:false,
		cache:false,
		success: function(data) {
			
			console.log(data);
			$.each(data,function(index){
				displayMarker(this.title, this.JHlat, this.JHLong, this.ImageFile, this.InfoBox);
			});
			map.setBounds(bounds);
			
		},
		error:function(request,status,error){
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	});	
	
}

var markers = [];
function displayMarker(title, JHLat, JHLong, ImageFile, InfoBox) 
{	
	//console.log( "add" ); // 디버그
	var latlng =  new kakao.maps.LatLng(JHLat, JHLong)
	
	if (JHLat != null && JHLong != null) bounds.extend(latlng);
	// 마커 이미지의 이미지 크기 입니다
	var imageSize = new kakao.maps.Size(35, 51); 
	
	// 마커 이미지를 생성합니다    
	var markerImage = new kakao.maps.MarkerImage(ImageFile, imageSize); 
		
	// 마커를 생성하고 지도에 표시합니다
	var marker = new kakao.maps.Marker(
	{
		map: map,
		position: latlng, // 마커를 표시할 위치
		title : title, // 마커의 타이틀, 마커에 마우스를 올리면 타이틀이 표시됩니다
		image : markerImage // 마커 이미지 
	});
	
	markers.push(marker);

	// 마커에 클릭이벤트를 등록합니다
	kakao.maps.event.addListener(marker, 'click', function() 
	{
		// 마커를 클릭하면 장소명이 인포윈도우에 표출됩니다
		infowindow.setContent(InfoBox);
		infowindow.open(map, marker);
	});
}

function removeMarker()
{
	//console.log( "remove" ); // 디버그
	
	for( var i = 0; i< markers.length; i++ ){
		markers[i].setMap(null);	
	}
	markers = [];
}


// JavaScript Document

////////////////////////////////////////////////////////////////////////////////
// USER DEFINED VARIABLE
////////////////////////////////////////////////////////////////////////////////
var lat = '37.5730';
var lon = '127.1283';
var zoom_level = 12;
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////
// KAKAO MAP
////////////////////////////////////////////////////////////////////////////////
// 지도를 표시할 div
var mapContainer = document.getElementById('id_frame_box'); 
var mapOption = {
	center: new kakao.maps.LatLng(lat, lon), // 지도의 중심좌표
	level: zoom_level, // 지도의 확대 레벨
	maxLevel: zoom_level // 최대의 최대 레벨
};

/* 지도를 생성합니다. */
var map = new kakao.maps.Map(mapContainer, mapOption); // 지도를 생성합니다

/* 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다*/
var mapTypeControl = new kakao.maps.MapTypeControl();
map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPRIGHT);

/* 지도 확대 축소를 제어할 수 있는 줌 컨트롤을 생성합니다. */
var zoomControl = new kakao.maps.ZoomControl();
map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);

/* 지도를 클릭한 위치에 표출할 마커입니다 */
// 마커 이미지를 생성합니다    
var marker = new kakao.maps.Marker({
    // 지도 중심좌표에 마커를 생성합니다 
    position: map.getCenter()
}); 
marker.setMap(map);
marker.setVisible(false);

/* 인포윈도우를 생성하고 지도에 표시합니다*/
var infowindow = new kakao.maps.InfoWindow({zIndex:1});

/* 장소 검색 객체를 생성합니다. */
var ps = new kakao.maps.services.Places();

////////////////////////////////////////////////////////////////////////////////
// PUBLIC STATIC VARIABLE
////////////////////////////////////////////////////////////////////////////////
var markers = [];
var infos = [];

////////////////////////////////////////////////////////////////////////////////
// PUBLIC STATIC EVENT
////////////////////////////////////////////////////////////////////////////////
window.onload = function()
{
	//alert("onload");
	
	//document.getElementById("id_frame_box").style.width = window.innerWidth - 100 + 'px';
	//document.getElementById("id_frame_box").style.height = window.innerHeight - 100 + 'px';

    let pStatus = true;

    $(document).on("click","#pop",function()
	{
		if( pStatus == true)
		{
			$(".src_frame_box").animate({ left : '-350px' });
			$("#pop").css("background-image", "url(/image/open.png)");
			pStatus = false;
		}
		else if(pStatus == false)
		{
			$(".src_frame_box").animate({ left : '0px' } );
			$("#pop").css("background-image", "url(/image/close.png)");
			pStatus = true;
		}
	});
}

window.onresize = function()
{
	//alert("onresize");
	
	// 빠르게 이동
	//map.setCenter(new kakao.maps.LatLng(lat, lon));
	
	// 부드럽게 이동
	map.panTo(new kakao.maps.LatLng(lat, lon));
}

function displayMarker(title, JHLat, JHLong, ImageFile, InfoBox) {
	
	//console.log( "add" ); // 디버그
	
	var latlng =  new kakao.maps.LatLng(JHLat, JHLong)
	
	// 마커 이미지의 이미지 크기 입니다
	//var imageSize = new kakao.maps.Size(35, 51); 
	var img_height = document.getElementById('maplevel') * 35;
	var img_width = img_height;
	var imageSize = new kakao.maps.Size(35, 51); 
	
	// 마커 이미지를 생성합니다    
	var markerImage = new kakao.maps.MarkerImage(ImageFile, imageSize); 
		
	// 마커를 생성하고 지도에 표시합니다
	var marker = new kakao.maps.Marker({
		map: map,
		position: latlng, // 마커를 표시할 위치
		title : title, // 마커의 타이틀, 마커에 마우스를 올리면 타이틀이 표시됩니다
		image : markerImage // 마커 이미지 
	});
	
	markers.push(marker);

	// 마커에 클릭이벤트를 등록합니다
	kakao.maps.event.addListener(marker, 'click', function() {
		// 마커를 클릭하면 장소명이 인포윈도우에 표출됩니다
		infowindow.setContent(InfoBox);
		infowindow.open(map, marker);
	});
}

function removeMarker(){
	//console.log( "remove" ); // 디버그
	
	for( var i = 0; i< markers.length; i++ ){
		markers[i].setMap(null);	
	}
	markers = [];
}

/* 키워드 검색을 요청하는 함수입니다. */
function searchPlaces()
{
    var keyword = document.getElementById('keyword').value;
    let type = document.querySelector("#type").value;

    if (!keyword.replace(/^\s+|\s+$/g, ''))
	{
        alert('키워드를 입력해주세요!');
        return false;
    }

    // 장소검색 객체를 통해 키워드로 장소검색을 요청합니다
    //ps.keywordSearch(keyword, placesSearchCB); 
    $.ajax(
        {
            url: "/map/loadMap.php",
            dataType:"html",
            type:"GET", 
            data: { type:type, query:keyword },
            async:true,
            cache:false,
            success: function(result) 
            {
                $("#resultTable").empty().html(result);
            },
            error:function(request,status,error)
            {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });	
}

$(document).on('click','.trList', function() 
{
    lat = $(this).attr("data-y");
    lon = $(this).attr("data-x");

    let latlon = new kakao.maps.LatLng(lat, lon);

    let message = ""
				  + "<div style=\"display:flex;\">" 
	              + "<div style=\"font-size:1em;width:85px;\">위/경도</div> "
				  + `<input id=\"latlon_text\" type=\"text\" value=\"${latlon.getLat().toFixed(4)}, ${latlon.getLng().toFixed(4)}\" style=\"height:100%;\"></input>`
				  + "<button style=\"font-size:1em;width:60px;height:30px;\" onclick=\"copy_latlon()\">복사</button>"
				  + "<button style=\"font-size:1em;width:60px;height:30px;\" onclick=\"save_site()\">등록</button>"
				  + "</div>";
	
	// 마커 위치를 클릭한 위치로 옮깁니다.
	marker.setPosition(latlon);
	marker.setTitle(`${latlon.getLat()}, ${latlon.getLng()}`);
	marker.setVisible(true);
	
	// 정보창 위치를 클릭한 위치로 옮깁니다.
	//if (infowindow.getContent() == "")
	infowindow.open(map, marker);
	infowindow.setContent(message);
	infowindow.setPosition(latlon);

    map.setLevel(3);
    map.panTo(latlon);
});

/* 장소검색이 완료됐을 때 호출되는 콜백함수 입니다. */
function placesSearchCB(data, status, pagination)
{
    if (status === kakao.maps.services.Status.OK)
	{
        // 정상적으로 검색이 완료됐으면
        // 검색 목록과 마커를 표출합니다
        displayPlaces(data);
		
        // 페이지 번호를 표출합니다
        displayPagination(pagination);
    }
	else if (status === kakao.maps.services.Status.ZERO_RESULT)
	{
        alert('검색 결과가 존재하지 않습니다.');
        
    }
	else if (status === kakao.maps.services.Status.ERROR)
	{
        alert('검색 결과 중 오류가 발생했습니다.');
    }
}

/* 검색 결과 목록과 마커를 표출하는 함수입니다. */
function displayPlaces(places)
{
    var listEl = document.getElementById('placesList'), 
    menuEl = document.getElementById('src_frame_box'),
    fragment = document.createDocumentFragment(), 
    bounds = new kakao.maps.LatLngBounds(), 
    listStr = '';
	
    // 검색 결과 목록에 추가된 항목들을 제거합니다
    removeAllChildNods(listEl);
	
	
    // 지도에 표시되고 있는 마커를 제거합니다
    removeMarker();
	
    for ( var i = 0; i < places.length; i++ )
	{
        // 마커를 생성하고 지도에 표시합니다
        var placePosition = new kakao.maps.LatLng(places[i].y, places[i].x),
            marker = addMarker(placePosition, i), 
            itemEl = getListItem(i, places[i]); // 검색 결과 항목 Element를 생성합니다

        // 검색된 장소 위치를 기준으로 지도 범위를 재설정하기위해
        // LatLngBounds 객체에 좌표를 추가합니다
        bounds.extend(placePosition);

        // 마커와 검색결과 항목에 mouseover 했을때
        // 해당 장소에 인포윈도우에 장소명을 표시합니다
        // mouseout 했을 때는 인포윈도우를 닫습니다
        (function(marker, title) {
            kakao.maps.event.addListener(marker, 'mouseover', function() {
                displayInfowindow(marker, title);
            });

            kakao.maps.event.addListener(marker, 'mouseout', function() {
                infowindow.close();
            });

            itemEl.onmouseover =  function () {
                displayInfowindow(marker, title);
            };

            itemEl.onmouseout =  function () {
                infowindow.close();
            };
        })(marker, places[i].place_name);

        fragment.appendChild(itemEl);
    }

    // 검색결과 항목들을 검색결과 목록 Element에 추가합니다
    listEl.appendChild(fragment);
    menuEl.scrollTop = 0;

    // 검색된 장소 위치를 기준으로 지도 범위를 재설정합니다
    //map.setBounds(bounds);
}

// 검색결과 항목을 Element로 반환하는 함수입니다
function getListItem(index, places) {

    var el = document.createElement('li'),
    itemStr = '<span class="markerbg marker_' + (index+1) + '"></span>' +
                '<div class="info">' +
                '   <h5>' + places.place_name + '</h5>';

    if (places.road_address_name) {
        itemStr += '    <span>' + places.road_address_name + '</span>' +
                    '   <span class="jibun gray">' +  places.address_name  + '</span>';
    } else {
        itemStr += '    <span>' +  places.address_name  + '</span>'; 
    }
                 
      itemStr += '  <span class="tel">' + places.phone  + '</span>' +
                '</div>';           

    el.innerHTML = itemStr;
    el.className = 'item';

    return el;
}

// 마커를 생성하고 지도 위에 마커를 표시하는 함수입니다
function addMarker(position, idx, title) {
    var imageSrc = 'https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/marker_number_blue.png', // 마커 이미지 url, 스프라이트 이미지를 씁니다
        imageSize = new kakao.maps.Size(36, 37),  // 마커 이미지의 크기
        imgOptions =  {
            spriteSize : new kakao.maps.Size(36, 691), // 스프라이트 이미지의 크기
            spriteOrigin : new kakao.maps.Point(0, (idx*46)+10), // 스프라이트 이미지 중 사용할 영역의 좌상단 좌표
            offset: new kakao.maps.Point(13, 37) // 마커 좌표에 일치시킬 이미지 내에서의 좌표
        },
        markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize, imgOptions),
            marker = new kakao.maps.Marker({
            position: position, // 마커의 위치
            image: markerImage 
        });

    marker.setMap(map); // 지도 위에 마커를 표출합니다
    markers.push(marker);  // 배열에 생성된 마커를 추가합니다

    return marker;
}

// 지도 위에 표시되고 있는 마커를 모두 제거합니다
function removeMarker()
{
    for (var i = 0; i < markers.length; i++ )
	{
        markers[i].setMap(null);
    }
	
    markers = [];
}

// 검색결과 목록 하단에 페이지번호를 표시는 함수입니다
function displayPagination(pagination) {
    var paginationEl = document.getElementById('pagination'),
        fragment = document.createDocumentFragment(),
        i; 

    // 기존에 추가된 페이지번호를 삭제합니다
    while (paginationEl.hasChildNodes()) {
        paginationEl.removeChild (paginationEl.lastChild);
    }

    for (i=1; i<=pagination.last; i++) {
        var el = document.createElement('a');
        el.href = "#";
        el.innerHTML = i;

        if (i===pagination.current) {
            el.className = 'on';
        } else {
            el.onclick = (function(i) {
                return function() {
                    pagination.gotoPage(i);
                }
            })(i);
        }

        fragment.appendChild(el);
    }
    paginationEl.appendChild(fragment);
}

// 검색결과 목록 또는 마커를 클릭했을 때 호출되는 함수입니다
// 인포윈도우에 장소명을 표시합니다
function displayInfowindow(marker, title)
{
    var content = '<div style="padding:5px;z-index:1;">' + title + '</div>';

    infowindow.setContent(content);
    infowindow.open(map, marker);
}

 // 검색결과 목록의 자식 Element를 제거하는 함수입니다
function removeAllChildNods(el)
{   
    while (el.hasChildNodes())
	{
        el.removeChild (el.lastChild);
    }
}

$('#searchBtn').click(function(){

	// 주소-좌표 변환 객체를 생성합니다
	var geocoder = new kakao.maps.services.Geocoder();

	//주소로 좌표를 검색합니다.
	geocoder.addressSearch($('#address').val(), function(result,status)
	{
		if(status === kakao.maps.services.Status.OK)
		{
			var coords = new kakao.maps.LatLng(result[0].y, result[0].x);
			map.setCenter(coords);
			marker.setPosition(coords);
			var message = '위/경도:  ' + result[0].y.substr(0,7) + ' , ' + result[0].x.substr(0,8);
			var resultDiv = document.getElementById('clickLatlng'); 
			resultDiv.innerHTML = message;
		}
	})
})

////////////////////////////////////////////////////////////////////////////////
// KAKAO MAP EVENT
////////////////////////////////////////////////////////////////////////////////


// 지도가 확대 또는 축소되면 마지막 파라미터로 넘어온 함수를 호출하도록 이벤트를 등록합니다
kakao.maps.event.addListener(map, 'zoom_changed', function()
{
	//alert(map.getLevel());
	
	zoom_level = map.getLevel();	
	
	//marker.height = map.getLevel() * 35;
	//marker.width = map.getLevel() * 35;
	
	//map.setCenter(new kakao.maps.LatLng(lat, lon));
	//map.relayout();
});

// 지도에 클릭 이벤트를 등록합니다.
// 지도를 클릭하면 마지막 파라미터로 넘어온 함수를 호출합니다.
kakao.maps.event.addListener(map, 'click', function(mouseEvent) {
    
	// 클릭한 위도, 경도 정보를 가져옵니다. 
	var latlng = mouseEvent.latLng;
	lat = latlng.getLat().toFixed(4);
	lon = latlng.getLng().toFixed(4);

    console.log(`lat:${latlng.getLat()}\nlon:${latlng.getLng()}`);
	
	var message = ""
				  + "<div style=\"display:flex;\">" 
	              + "<div style=\"font-size:1em;width:85px;\">위/경도</div> "
				  + `<input id=\"latlon_text\" type=\"text\" value=\"${latlng.getLat().toFixed(4)}, ${latlng.getLng().toFixed(4)}\" style=\"height:100%;\"></input>`
				  + "<button style=\"font-size:1em;width:60px;height:30px;\" onclick=\"copy_latlon()\">복사</button>"
				  + "<button style=\"font-size:1em;width:60px;height:30px;\" onclick=\"save_site()\">등록</button>"
				  + "</div>";
	
	// 마커 위치를 클릭한 위치로 옮깁니다.
	marker.setPosition(latlng);
	marker.setTitle(`${lat}, ${lon}`);
	marker.setVisible(true);
	
	// 정보창 위치를 클릭한 위치로 옮깁니다.
	//if (infowindow.getContent() == "")
		infowindow.open(map, marker);
	infowindow.setContent(message);
	infowindow.setPosition(latlng);
	
	
});

function save_site()
{
	/* TODO 저장 로직
	
	*/
}

function copy_latlon()
{
	const latlon_text = document.getElementById("latlon_text");
	latlon_text.select();

	document.execCommand('copy');
}
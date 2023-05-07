<!DOCTYPE html>
<html>
	<head>
		<title>위/경도 찾기</title>
		<style>
			* {margin:0;padding:0;font-family:'Malgun Gothic',dotum,'맑은 고딕',sans-serif;font-size:20px;text-align:center;}
			html, body{width:100%;height:100%;}
			
			.cs_frame_box
			{
				width:100%;
				height:100%;				
				box-sizing: border-box;
				-moz-box-sizing: border-box;
				-ms-box-sizing: border-box;
				-o-box-sizing: border-box;
				-webkit-box-sizing: border-box;
			}
			.id_frame_box
			{
				position:relative;
				width:100%;
				height:100%;
				overflow:hidden;
			}
			.src_frame_box
			{
			    position:absolute;
				left:0;
				width:30%;
				max-width:350px;
				background:rgba(255, 255, 255, 0.7);
				z-index: 5;
				margin:2% 0 1% 0;
			}
			#keyword {width:100%;}
			#submit {width:100%;}
			
			#resultTable {margin:auto;}
			.trList {cursor: pointer;}
			#placesList li {list-style: none;}
			#placesList .item {position:relative;border-bottom:1px solid #888;overflow: hidden;cursor: pointer;min-height: 65px;}
			#placesList .item span {display: block;margin-top:4px;}
			#placesList .item h5, #placesList .item .info {text-overflow: ellipsis;overflow: hidden;white-space: nowrap;}
			#placesList .item .info{padding:10px 0 10px 55px;}
			#placesList .info .gray {color:#8a8a8a;}
			#placesList .info .jibun {padding-left:26px;}
			#placesList .info .tel {color:#009900;}
			#placesList .item .markerbg {float:left;position:absolute;width:36px; height:37px;margin:10px 0 0 10px;background:url(https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/marker_number_blue.png) no-repeat;}
			#placesList .item .marker_1 {background-position: 0 -10px;}
			#placesList .item .marker_2 {background-position: 0 -56px;}
			#placesList .item .marker_3 {background-position: 0 -102px}
			#placesList .item .marker_4 {background-position: 0 -148px;}
			#placesList .item .marker_5 {background-position: 0 -194px;}
			#placesList .item .marker_6 {background-position: 0 -240px;}
			#placesList .item .marker_7 {background-position: 0 -286px;}
			#placesList .item .marker_8 {background-position: 0 -332px;}
			#placesList .item .marker_9 {background-position: 0 -378px;}
			#placesList .item .marker_10 {background-position: 0 -423px;}
			#placesList .item .marker_11 {background-position: 0 -470px;}
			#placesList .item .marker_12 {background-position: 0 -516px;}
			#placesList .item .marker_13 {background-position: 0 -562px;}
			#placesList .item .marker_14 {background-position: 0 -608px;}
			#placesList .item .marker_15 {background-position: 0 -654px;}
		</style>
	</head>

	<body>
		<div class="cs_frame_box">
			<div class="id_frame_box" id="id_frame_box">
				<div class="src_frame_box" id="src_frame_box">
				<div id="pop" style="position: absolute;height: 30px;width: 30px;top: -60%;left: 5px;background-image:url('/image/close.png');"></div>
					<div id="research">
						<form onsubmit="searchPlaces(); return false;">
							<div style="display: flex;margin: auto;padding: unset;justify-content: space-around;align-items: center;">
								<select name="type" id="type">
									<option value="key">키워드</option>
									<option value="adr">주소</option>
								</select>
								<div><input type="text" value="" id="keyword" size="15"></div>
								<div><button type="submit" id="submit">검색</button></div>
							</div>
						</form>
					</div>
					<hr style="height:3px;border:0;border-top:2px solid #5F5F5F;margin:5px">
					<table id="resultTable">
					</table>
					<ul id="placesList"></ul>
					<div id="pagination"></div>
				</div>
			</div>
		</div>
		
		<script src="/js/jquery-1.9.1.js"></script>

		<!-- Map 호출 -->
		<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=36a82a2ce2f8cbe962640ed643307c45&libraries=services"></script>
		<script src="js/researchMap.js"></script>
	</body>
</html>
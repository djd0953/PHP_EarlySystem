<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
	include "dbconn.php";
	
	/*
		오늘 강우, 연간강우, 풍향, 풍속, 전일, 온도, 미세먼지, 오존, 적설량
		적설량의 경우 11/1~4/30일까지만 표시( 오존과 적설량을 교체해서 표시해줌 )
	*/
	
	$code = 1; // 지역코드
	
	// 지역정보 가져오기
	$sql_area = "select * from `info_region` where ID = '".$code."'";
	$res_area = @mysql_query( $sql_area , $conn );
	$row_area = @mysql_fetch_assoc( $res_area );
	
	$areaArr = '{ "Region" : "'.$row_area["Name"].'", "Release" : "'.date("YmdHi00", time()).'" }';
	
	$sql_cols = "SELECT CONCAT(GROUP_CONCAT('`',COLUMN_NAME ORDER BY COLUMN_NAME ASC SEPARATOR '`+'),'`') as cols,  
						GROUP_CONCAT('a.',COLUMN_NAME ORDER BY COLUMN_NAME ASC SEPARATOR '+') as acols,
						GROUP_CONCAT('b.',COLUMN_NAME ORDER BY COLUMN_NAME ASC SEPARATOR '+') as bcols 
				FROM INFORMATION_SCHEMA.COLUMNS   
	 			WHERE TABLE_SCHEMA='weathersi' AND TABLE_NAME='rain_hour' AND COLUMN_NAME LIKE 'hour%'";
	$res_cols = @mysql_query( $sql_cols , $conn );
	$row_cols = @mysql_fetch_assoc( $res_cols );

	//연간 강우
	$columns = $row_cols["cols"];
	$yrain_sql="SELECT sum(".$columns.") as adder FROM Rain_HOUR WHERE LEFT(DATE,4)=Date_Format(NOW(),'%Y') AND id='".$code."' ";
	$yrain_res = @mysql_query( $yrain_sql, $conn );
	$yrain_row = @mysql_fetch_assoc( $yrain_res );
	
	//오늘, 어제 강우량
	$acolumns = $row_cols["acols"];
	$bcolumns = $row_cols["bcols"];
	$rain_sql = "select (".$acolumns.") as tRain, (".$bcolumns.") AS yRain       
				 from `rain_hour` as a left join `rain_hour` as b on a.id= b.id and Date_Format(b.Date,'%Y%m%d') = DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -1 DAY),'%Y%m%d')  
				 where a.ID = '1' and Date_Format(a.Date,'%Y%m%d')=Date_Format(NOW(),'%Y%m%d')";
	$rain_res = @mysql_query( $rain_sql, $conn );
	$rain_row = @mysql_fetch_assoc( $rain_res );

	$yearRainArr = '{ "Title" : "연간강우", "Level" : "'.number_format($yrain_row["adder"]/10 ).'" }';
	$tRainArr = '{ "Title" : "오늘강우", "Level" : "'.number_format($rain_row["tRain"]/10 , 1).'" }';
	$yRainArr = '{ "Title" : "어제강우", "Level" : "'.number_format($rain_row["yRain"]/10 , 1).'" }';

	
	// 오존, 미세먼지 정보 
	$sky_sql = "select * from `kma_air` where id = 1";
	$sky_res = @mysql_query( $sky_sql, $conn );
	$sky_row = @mysql_fetch_assoc( $sky_res );
	
	$o3Arr = '{ "Title" : "오존", "Level" : "'.$sky_row["o3"].'" }';
	$pm10Arr = '{ "Title" : "미세먼지", "Level" : "'.$sky_row["pm10"].'" }';
	
	
	// aws 정보
	$aws_sql = "select * from `aws_min` order by `Date` desc limit 1";
	$aws_res = @mysql_query( $aws_sql, $conn );
	$aws_row = @mysql_fetch_assoc( $aws_res );
	
	$wd = $aws_row["WD"]/10;
	
	
	$selWind = 'kor';
	
	if( $wd == 0 ){ $wind_loc="N"; $wind_loc_k = "북"; }
	else if( $wd > 0 && $wd <= 22.5 ){ $wind_loc = "N";   $wind_loc_k = "북"; }
	else if( $wd > 22.5 && $wd <= 45 ){ $wind_loc = "NNE"; $wind_loc_k = "북북동"; }
	else if( $wd > 45 && $wd <= 67.5 ){ $wind_loc = "NE"; $wind_loc_k = "북동"; }
	else if( $wd > 67.5 && $wd <= 90 ){ $wind_loc = "ENE";  $wind_loc_k = "동북동"; }
	else if( $wd > 90 && $wd <= 112.5 ){ $wind_loc = "E";  $wind_loc_k = "동"; }
	else if( $wd > 112.5 && $wd <= 135 ){ $wind_loc = "ESE"; $wind_loc_k = "동남동"; }
	else if( $wd > 135 && $wd <= 157.5 ){ $wind_loc = "SE"; $wind_loc_k = "남동"; }
	else if( $wd > 157.5 && $wd <= 180 ){ $wind_loc = "SSE"; $wind_loc_k = "남남동"; }
	else if( $wd > 180 && $wd <= 202.5 ){ $wind_loc = "S"; $wind_loc_k = "남"; }
	else if( $wd > 202.5 && $wd <= 225 ){ $wind_loc = "SSW"; $wind_loc_k = "남남서"; }
	else if( $wd > 225 && $wd <= 247.5 ){ $wind_loc = "SW"; $wind_loc_k = "남서"; }
	else if( $wd > 247.5 && $wd <= 270 ){ $wind_loc = "WSW"; $wind_loc_k = "서남서"; }
	else if( $wd > 270 && $wd <= 292.5 ){ $wind_loc = "W"; $wind_loc_k = "서"; }
	else if( $wd > 292.5 && $wd <= 315 ){ $wind_loc = "WNW"; $wind_loc_k = "서북서"; }
	else if( $wd > 315 && $wd <= 337.5 ){ $wind_loc = "NW";  $wind_loc_k = "북서"; }
	else if( $wd > 337.5 && $wd <= 360 ){ $wind_loc = "NNW"; $wind_loc_k = "북북서"; }
	else if( $wd > 360 ){ $wind_loc = "N"; $wind_loc_k = "북";}
	
	if( $selWind == 'eng' ){
		$sWind = $wind_loc;	
	}else{
		$sWind = $wind_loc_k;	
	}
		
	$tempArr = '{ "Title" : "온도", "Level" : "'. number_format( $aws_row["TMP"]/10 , 1 ).'" }';
	$wdArr = '{ "Title" : "풍향", "Level" : "'.$sWind.'" }';
	$wsArr = '{ "Title" : "풍속", "Level" : "'.number_format( $aws_row["WS"]/10 , 1 ).'" }';
	
	
	$toMonth = date("n", time() );
	if( $toMonth <= 4 || $toMonth >= 11 ){
		
		$snow_sql = "select Hour".date("H", time())." as nowSnow from snow_hour where `ID` = 5 and `Date` = '".date("Ymd", time())."'";
		$snow_res = @mysql_query( $snow_sql, $conn );
		$snow_row = @mysql_fetch_assoc( $snow_res );
		
		$snow = $snow_row["nowSnow"];
		
		$snowArr = '{ "Title" : "현재적설", "Level" : "'. number_format($snow_row["nowSnow"]/10, 1) .'" }';
		
		
		$data = "[".$areaArr.",".$tRainArr.",".$yearRainArr.",".$wdArr.",".$wsArr.",".$yRainArr.",".$tempArr.",".$pm10Arr.",".$snowArr."]";
			
	}else{
		
		$data = "[".$areaArr.",".$tRainArr.",".$yearRainArr.",".$wdArr.",".$wsArr.",".$yRainArr.",".$tempArr.",".$pm10Arr.",".$o3Arr."]";	
	}
	
	
	mysql_close( $conn );
	
	$file_name = 'ibp1.do'; // 저장될 파일 이름
	$file = '../ibp1.do'; // 파일의 전체 경로
	 
	$fp = fopen($file, 'w');
	fwrite($fp, $data);
	fclose($fp);
	
	
?>
<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

 	$radarDao = new KMA_SATELLITE_DAO;
	$radarVo = new KMA_SATELLITE_VO;

	$radarVo = $radarDao->SELECT("SAT");

	if( isset($radarVo->{key($radarVo)}) )
	{
		$pos = strrpos($radarVo->filename, '/');
		if( $pos > 0 ) $satimg = "/files/radar/".substr($radarVo->filename, $pos);
		else $satimg = "/files/radar/{$radarVo->filename}";
	}
	else $satimg = "/image/radar.gif";

	$radarVo = $radarDao->SELECT("RDR");
	
	if( isset($radarVo->{key($radarVo)}) ) $rdrimg = "/files/radar/{$radarVo->filename}";
	else $rdrimg = "/image/radar.gif";
	
	$rdrimg = "/cgi-bin/getCmpImg.exe";
	
	echo "<div class='cs_radarPart'>";
	echo "<div class='cs_pLargeTitle' value='satimg' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 위성영상</div>";
	echo "<div class='cs_radarImage' id='satimg' style='background-image:url({$satimg});'></div>";
	echo "<div class='cs_pLargeTitle' value='rdrimg' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 레이더영상</div>";
	echo "<div class='cs_radarImage' id='rdrimg' style='background-image:url({$rdrimg});'></div>";
	echo "</div>";
	
	
	
?>
<!--
 <div class="radarImage" style="background-image:url(http://www.weather.go.kr/repositary/image/rdr/img/<?//=$row_rsrdr["filename"] ?>);"></div>
 <div class="radarImage" style="background-image:url(http://www.weather.go.kr/repositary/image/sat/<?//=$row_rssat["filename"] ?>);"></div>
-->
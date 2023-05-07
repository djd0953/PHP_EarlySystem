<?php
function getPowerStatus( $power )
{
	$retArray = array("Error", "Error", "Error");
	
	if( $power == "0" ) $retArray = array("OFF", "OFF", "OFF", "OFF");
	else if( $power == "1" ) $retArray = array("OFF", "OFF", "OFF", "ON");
	else if( $power == "2" ) $retArray = array("OFF", "OFF", "ON", "OFF");
	else if( $power == "3" ) $retArray = array("OFF", "OFF", "ON", "ON");
	else if( $power == "4" ) $retArray = array("OFF", "ON", "OFF", "OFF");
	else if( $power == "5" ) $retArray = array("OFF", "ON", "OFF", "ON");
	else if( $power == "6" ) $retArray = array("OFF", "ON", "ON", "OFF");
	else if( $power == "7" ) $retArray = array("OFF", "ON", "ON", "ON");
	else if( $power == "8" ) $retArray = array("ON", "OFF", "OFF", "OFF");
	else if( $power == "9" ) $retArray = array("ON", "OFF", "OFF", "ON");
	else if( $power == "10" ) $retArray = array("ON", "OFF", "ON", "OFF");
	else if( $power == "11" ) $retArray = array("ON", "OFF", "ON", "ON");
	else if( $power == "12" ) $retArray = array("ON", "ON", "OFF", "OFF");
	else if( $power == "13" ) $retArray = array("ON", "ON", "OFF", "ON");
	else if( $power == "14" ) $retArray = array("ON", "ON", "ON", "OFF");
	else if( $power == "15" ) $retArray = array("ON", "ON", "ON", "ON");
	else $retArray = array("-", "-", "-", "-");

	return $retArray;
}

function getRelay( $relay )
{
	$retArray = array("OFF", "OFF", "OFF", "OFF");
	
	if( $relay == "0" ) $retArray = array("OFF", "OFF", "OFF", "OFF");
	else if( $relay == "1" ) $retArray = array("OFF", "OFF", "OFF", "ON");
	else if( $relay == "2" ) $retArray = array("OFF", "OFF", "ON", "OFF");
	else if( $relay == "3" ) $retArray = array("OFF", "OFF", "ON", "ON");
	else if( $relay == "4" ) $retArray = array("OFF", "ON", "OFF", "OFF");
	else if( $relay == "5" ) $retArray = array("OFF", "ON", "OFF", "ON");
	else if( $relay == "6" ) $retArray = array("OFF", "ON", "ON", "OFF");
	else if( $relay == "7" ) $retArray = array("OFF", "ON", "ON", "ON");
	else if( $relay == "8" ) $retArray = array("ON", "OFF", "OFF", "OFF");
	else if( $relay == "9" ) $retArray = array("ON", "OFF", "OFF", "ON");
	else if( $relay == "10" ) $retArray = array("ON", "OFF", "ON", "OFF");
	else if( $relay == "11" ) $retArray = array("ON", "OFF", "ON", "ON");
	else if( $relay == "12" ) $retArray = array("ON", "ON", "OFF", "OFF");
	else if( $relay == "13" ) $retArray = array("ON", "ON", "OFF", "ON");
	else if( $relay == "14" ) $retArray = array("ON", "ON", "ON", "OFF");
	else if( $relay == "15" ) $retArray = array("ON", "ON", "ON", "ON");
	else $retArray = array("-", "-", "-", "-");

	return $retArray;
}

function getIntRelay( $relay )
{	
	$data = explode( "/", $relay );
	$returnData = 0;

	if( $data[0] == "OFF" && $data[1] == "OFF" && $data[2] == "OFF" && $data[2] == "OFF" ) $returnData = 0;
	else if( $data[0] == "OFF" && $data[1] == "OFF" && $data[2] == "OFF" && $data[2] == "ON" ) $returnData = 1;
	else if( $data[0] == "OFF" && $data[1] == "OFF" && $data[2] == "ON" && $data[2] == "OFF" ) $returnData = 2;
	else if( $data[0] == "OFF" && $data[1] == "OFF" && $data[2] == "ON" && $data[2] == "ON" ) $returnData = 3;
	else if( $data[0] == "OFF" && $data[1] == "ON" && $data[2] == "OFF" && $data[2] == "OFF" ) $returnData = 4;
	else if( $data[0] == "OFF" && $data[1] == "ON" && $data[2] == "OFF" && $data[2] == "ON" ) $returnData = 5;
	else if( $data[0] == "OFF" && $data[1] == "ON" && $data[2] == "ON" && $data[2] == "OFF" ) $returnData = 6;
	else if( $data[0] == "OFF" && $data[1] == "ON" && $data[2] == "ON" && $data[2] == "ON" ) $returnData = 7;
	else if( $data[0] == "ON" && $data[1] == "OFF" && $data[2] == "OFF" && $data[2] == "OFF" ) $returnData = 8;
	else if( $data[0] == "ON" && $data[1] == "OFF" && $data[2] == "OFF" && $data[2] == "ON" ) $returnData = 9;
	else if( $data[0] == "ON" && $data[1] == "OFF" && $data[2] == "ON" && $data[2] == "OFF" ) $returnData = 10;
	else if( $data[0] == "ON" && $data[1] == "OFF" && $data[2] == "ON" && $data[2] == "ON" ) $returnData = 11;
	else if( $data[0] == "ON" && $data[1] == "ON" && $data[2] == "OFF" && $data[2] == "OFF" ) $returnData = 12;
	else if( $data[0] == "ON" && $data[1] == "ON" && $data[2] == "OFF" && $data[2] == "ON" ) $returnData = 13;
	else if( $data[0] == "ON" && $data[1] == "ON" && $data[2] == "ON" && $data[2] == "OFF" ) $returnData = 14;
	else if( $data[0] == "ON" && $data[1] == "ON" && $data[2] == "ON" && $data[2] == "ON" ) $returnData = 15;
	
	return $returnData;
}
?>
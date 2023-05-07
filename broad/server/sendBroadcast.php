<?php

	include "../../include/dbconn.php";
	
	// equip, title, tType, sDate, sTime, sMin, repeat, type, ment, content
	
	$equip = $_POST["equip"];
	$title = $_POST["title"];
	$tType = $_POST["tType"];
	$sDate = $_POST["sDate"];
	$sTime = $_POST["sTime"];
	$sMin = $_POST["sMin"];
	$repeat = $_POST["repeat"];
	$type = $_POST["type"];
	$ment = $_POST["ment"];
	$content = $_POST["content"];
	
	if( $tType == "general" ){
		
		$bDate = date("Y-m-d H:i:s", time() );
		$bTime = "now";
		
	}else if( $tType == "reserve" ){
		
		$bDate = $sDate. " " . $sTime . ":" . $sMin . ":00" ;
		$bTime = "reserve";
		
	}
	
	// 1. 방송list 추가 
	$sql = "insert into wb_brdlist ( CD_DIST_OBSV, Title, BType, BrdType, AltMent, TTSContent, RevType, BrdDate, BRepeat, RegDate ) 
			values ('".$equip."','".$title."','".$tType."','".$type."','".$ment."','".$content."','".$bTime."','".$bDate."','".$repeat."',now())";
	$res = mysqli_query( $conn, $sql );
	
	$insertID = mysqli_insert_id( $conn ); // 직전에 저장된 방송 내역
	
	// 2. 방송 list 상세 추가 
	$equipList = explode(",", $equip );
	for( $i = 0; $i < count( $equipList ); $i++ ){
		$subSql = "insert into wb_brdlistdetail ( BCode, CD_DIST_OBSV, BrdStatus, RegDate ) 
				   values ('".$insertID."', '".$equipList[$i]."', 'start', now() )";	
		$subRes = mysqli_query( $conn, $subSql );
		
		// 3. 즉시 방송인경우 jhbsend에 등록
		if( $tType == "general" ){
			if( $type == "tts" ){
				$cmd = "B010";
				$param3 = preg_replace('/\r\n|\r|\n/',' ',$content); //TTS문구
			}else if( $type == "alert" ){
				$cmd = "B020";
				$param3 = $ment; //TTS문구
			}
			
			$param1 = "00000000"; //그룹코드
			$param2 = $repeat; //방송횟수
			$param4 = $insertID; //리스트번호
			
			$bSql = "insert into wb_brdsend ( CD_DIST_OBSV, RCMD, Parm1, Parm2, Parm3, Parm4, RegDate, BStatus )
					 values ('".$equipList[$i]."', '".$cmd."', '".$param1."', '".$param2."', '".$param3."', '".$param4."', now(), 'start' )";
			$bRes = mysqli_query( $conn, $bSql );
		}
		
	}
	
	$resultArray = array("code" => "00");
	echo json_encode( $resultArray );	
?>
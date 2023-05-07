<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";

$data = json_decode(file_get_contents('php://input'), true);
$resultArray = array();
$disstr = "";
$strsub = "";

if($data['type'] == "delete")
{
	
	$discode = explode(",", $data['scen']);
	$cnt = 0;
	for($i=0; $i<count($discode); $i++)
	{
		$gSql = "select * from wb_display where DisCode = {$discode[$i]}";
		$gRes = mysqli_query( $conn, $gSql ); 
		$gRow = mysqli_fetch_array($gRes);

		if($cnt++ == 0) 
		{
			$resultArray['msg'] = strip_tags($gRow['HtmlData']);
			$sql = "delete from wb_display where DisCode = {$discode[$i]} ";
		}
		else 
		{
			$resultArray['msg'] = $resultArray['msg']." // ".strip_tags($gRow['HtmlData']);
			$sql = $sql." or DisCode = {$discode[$i]}";
		}
	}
	$res = mysqli_query( $conn, $sql );

	$resultArray['code'] = "00";
}
elseif($data['type']  == "end")
{
	$sql = "update wb_display 
			set Exp_YN = 'N'
			where DisCode = ".$data['scen'];
	$res = mysqli_query( $conn, $sql );
	
	$gSql = "select * from wb_display where DisCode = ".$data['scen'];
	$gRes = mysqli_query( $conn, $gSql ); 
	$gRow = mysqli_fetch_array( $gRes );
	
	$sendSql = "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
				values ('".$gRow["CD_DIST_OBSV"]."', 'D060', '', now(), 'start' )";
	$sendRes = mysqli_query( $conn, $sendSql );

	$resultArray['code'] = "00";
	$resultArray['msg'] = strip_tags($gRow['HtmlData']);
}
elseif($data['type'] == "save")
{
	function imageResize($name)
    {
        $info = getimagesize($name);
        $img_width = round($info[0]*0.5);
        $img_height = round($info[1]*0.5);

        $image = imagecreatefrompng($name);

        $new_image = imagecreatetruecolor($img_width, $img_height);
		imagecopyresampled($new_image, $image, 0, 0, 0, 0, $img_width, $img_height, $info[0], $info[1]);
        imagepng($new_image, $name);

        imagedestroy($image); 
		imagedestroy($new_image);
    }

	$type = $data['mode'];
	if($type != "group")
	{
		$num = $data['num'];
		$areaCode = $data['areaCode'];
		$count = 1;
	}
	else 
	{
		$equip = $data['equip'];
		$arrEqu = explode(",",$equip);
		$count = count($arrEqu);
	}
	$width = $data['width'];
	$height = $data['height'];
	
	$disEffect = $data['disEffect'];
	$disSpeed = $data['disSpeed'];
	$endEffect = $data['endEffect'];
	$endSpeed = $data['endSpeed'];
	$relay = 0;
	if(!empty($data['relay1'])) $relay += 8;
	if(!empty($data['relay2'])) $relay += 4;
	if(!empty($data['relay3'])) $relay += 2;
	if(!empty($data['relay4'])) $relay += 1;
	$disTime = $data['disTime'];
	$startDate = $data['startDate'];
	$startTime = $data['startTime'];
	$endDate = $data['endDate'];
	$endTime = $data['endTime'];
	$summernote = $data['summernote'];
	$imageTag = $data['imageTag'];
	
	// 이미지 저장 처리
	for($i=0; $i<$count; $i++)
	{
		if($type == "group") $areaCode = $arrEqu[$i];
		$imageName = $areaCode."_text_".date("YmdHis",time()).".png";
		$imageThumb = $areaCode."_thumb_".date("YmdHis",time()).".png";

		if($imageTag != "")
		{
			$path = "../../displayImage/".$imageName;
			
			// 썸네일용 파일 생성
			$img = str_replace('data:image/png;base64,', '', $imageTag);
			$img = str_replace(' ', '+', $img);
			$imgData = base64_decode($img);
			file_put_contents($path, $imgData);
			
			// 전송파일 생성
			if( file_exists( $path) )
			{
				copy($path, "../../displayImage/".$imageThumb);
			}
			imageResize("../../displayImage/".$imageThumb);
		}
	}
	
	$imageFile = "displayImage/".$imageName;
	$sendImage = "displayImage/".$imageThumb;
	
	if( $type == "insert" )
	{
		$sql = "insert into wb_display ( CD_DIST_OBSV, saveType, DisEffect, DisSpeed, DisTime, EndEffect, EndSpeed, StrTime, EndTime, Relay, ViewImg, SendImg, HtmlData, DisType, Exp_YN, RegDate )
				 values ('".$areaCode."', 'local', '".$disEffect."', '".$disSpeed."', '".$disTime."', '".$endEffect."', '".$endSpeed."', '".$startDate." ".$startTime.":00:00"."',
						'".$endDate." ".$endTime.":00:00"."', '".$relay."', '".$imageFile."', '".$sendImage."', '".$summernote."', 'ad', 'Y', now() )";
		
		$res = mysqli_query( $conn, $sql );

		$sendSql = "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
					values ('".$areaCode."', 'D060', '', now(), 'start' )";
		$sendRes = mysqli_query( $conn, $sendSql );

		$resultArray['code'] = "10";
		$resultArray['msg'] = "";
		$resultArray['after'] = strip_tags($summernote);
	}
	else if( $type == "update" )
	{
		
		// 기존 이미지 삭제
		$imgSql = "select * from wb_display where DisCode = '".$data['num']."'";
		$imgRes = mysqli_query( $conn, $imgSql );
		$imgRow = mysqli_fetch_array( $imgRes );
		
		unlink("../../".$imgRow["ViewImg"]); // 표시 이미지 삭제
		unlink("../../".$imgRow["SendImg"]); // 전송 이미지 삭제

		$sql = "update wb_display 
				set DisEffect = '".$disEffect."',
					DisSpeed = '".$disSpeed."',
					DisTime = '".$disTime."',
					EndEffect = '".$endEffect."',
					EndSpeed = '".$endSpeed."',
					StrTime = '".$startDate." ".$startTime.":00:00"."',
					EndTime = '".$endDate." ".$endTime.":00:00"."',
					Relay = '".$relay."',
					ViewImg = '".$imageFile."',
					SendImg = '".$sendImage."',
					HtmlData = '".$summernote."',
					Exp_YN = 'Y'
				where DisCode = '".$data['num']."'";
		
		$res = mysqli_query( $conn, $sql );

		$sendSql = "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
					values ('".$areaCode."', 'D060', '', now(), 'start' )";
		$sendRes = mysqli_query( $conn, $sendSql );

		$resultArray['code'] = "10";
		$resultArray['msg'] = strip_tags($imgRow['HtmlData']);
		$resultArray['after'] = strip_tags($summernote);
	}
	else if( $type == "group")
	{
		for($i=0; $i<$count; $i++)
		{
			$areaCode = $arrEqu[$i];
			$sql = "insert into wb_display ( CD_DIST_OBSV, saveType, DisEffect, DisSpeed, DisTime, EndEffect, EndSpeed, StrTime, EndTime, Relay, ViewImg, SendImg, HtmlData, DisType, Exp_YN, RegDate )
				 values ('".$areaCode."', 'local', '".$disEffect."', '".$disSpeed."', '".$disTime."', '".$endEffect."', '".$endSpeed."', '".$startDate." ".$startTime.":00:00"."',
						'".$endDate." ".$endTime.":00:00"."', '".$relay."', '".$imageFile."', '".$sendImage."', '".$summernote."', 'ad', 'Y', now() )";
						
			$res = mysqli_query( $conn, $sql );
			$sendSql = "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
						values ('".$areaCode."', 'D060', '', now(), 'start' )";
			$sendRes = mysqli_query( $conn, $sendSql );
		}
		$resultArray['code'] ="20";
		$resultArray['msg'] = strip_tags($summernote);
	}
}
echo json_encode( $resultArray );
?>
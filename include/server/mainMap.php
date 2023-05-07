<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";
	
    $sql = "select CD_DIST_OBSV, NM_DIST_OBSV, GB_OBSV, LAT, LON, ErrorChk, SubOBCount, DetCode
            from wb_equip 
            where USE_YN >= '1'";
    $res = mysqli_query( $conn, $sql );
    $count = 0;
	
	$saveArray = array();
	
    while( $row = mysqli_fetch_array( $res ) )
    {
        if( $row["GB_OBSV"] == "01" )
        {
            $color = "#42569d";
            $subQuery = "select * from wb_raindis where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."'";
            $subRes = mysqli_query( $conn, $subQuery );
            $subRow = mysqli_fetch_array( $subRes );
            
            $imgSrc = "/image/rainMarker";

            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';

            if($subRow)
                $info = $info.'<tr>'.
                                    '<th width="20%" style="background-color:'.$color.'">금일</th>'.
                                    '<td>'.number_format($subRow["rain_today"],1).' mm</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<th style="background-color:'.$color.'">시간</th>'.
                                    '<td>'.number_format($subRow["rain_hour"],1).' mm</td>'.
                                '</tr>';

            $info = $info.'<tr>'.
                            '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn rain" data-type="rain" data-num="'.$row["CD_DIST_OBSV"].'">데이터 보기</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
            
        }
        else if( $row["GB_OBSV"] == "02" )
        {
            $color="#329fe0";
            $subQuery = "select * from wb_waterdis where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."'";
            $subRes = mysqli_query( $conn, $subQuery );
            $subRow = mysqli_fetch_array( $subRes );
            $subCnt = mysqli_num_rows($subRes);
            
            $imgSrc = "/image/waterMarker";			
            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';
            if($subRow)
                $info = $info.'<tr>'.
                                    '<th width="40%" style="background-color:'.$color.'">금일최고</th>'.
                                    '<td>'.number_format($subRow["water_today"]/1000,1).' M</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<th style="background-color:'.$color.'">현재</th>'.
                                    '<td>'.number_format($subRow["water_now"]/1000,1).' M</td>'.
                                '</tr>';
            $info = $info.'<tr>'.
                            '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn water" data-type="water" data-num="'.$row["CD_DIST_OBSV"].'">데이터 보기</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
                    
        }
        else if( $row["GB_OBSV"] == "03" )
        {
            $color="#a5614a";
            $imgSrc = "/image/dPlaceMarker";
            
            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';
							
							
			for( $i = 1; $i <= $row["SubOBCount"] ; $i++ )
            {
				
				//$before_date = date('Ymd', strtotime('-1hour'));
				$now_date = date('Ymd', time());
				//$bMR = date('G', strtotime('-1hour'));
				$MR = date("G", time());
				
                $subQuery = "SELECT MR{$MR} as dplace_now FROM wb_dplace1hour WHERE RegDate = '{$now_date}' AND CD_DIST_OBSV = '{$row['CD_DIST_OBSV']}' AND SUB_OBSV = '{$i}'";
				//$subQuery = "SELECT MR{$MR} as dplace_now, 
				//			((SELECT ABS(MR{$MR}) FROM wb_dplace1hour WHERE RegDate = '{$now_date}' and SUB_OBSV = {$i} and CD_DIST_OBSV = '{$row['CD_DIST_OBSV']}') - 
				//			(SELECT ABS(MR{$bMR}) FROM wb_dplace1hour WHERE RegDate = '{$before_date}' and SUB_OBSV = {$i} and CD_DIST_OBSV = '{$row['CD_DIST_OBSV']}')) as dplace_speed
				//			FROM wb_dplace1hour WHERE RegDate = '{$now_date}' and CD_DIST_OBSV = '{$row['CD_DIST_OBSV']}' and SUB_OBSV = '{$i}'";
				$subRes = mysqli_query( $conn, $subQuery );
				$subRow = mysqli_fetch_array( $subRes );
				
                if(!$subRow)
                {
                    break;
                }
                else
                {
                    $dplaceNow =  $subRow["dplace_now"];
                    
                    $info = $info.  '<tr>'.
                                        '<th width="40%" style="background-color:'.$color.'">'.$i.'</th>'.
                                        '<td>'.$dplaceNow.' mm</td>'.
                                    '</tr>';
                }
            }                    
                    $info = $info.  '<tr>'.
                                        '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn dplace" data-type="dplace" data-num="'.$row["CD_DIST_OBSV"].'">데이터 보기</div></td>'.
                                    '</tr>'.
                                    '</table>'.
                                '</div>'.
                            '</div>';
    
        }
        else if( $row["GB_OBSV"] == "04" )
        {
            $color="#8643ae";
            $subQuery = "select * from wb_snowdis where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."'";
            $subRes = mysqli_query( $conn, $subQuery );
            $subRow = mysqli_fetch_array( $subRes );
            
            $imgSrc = "/image/snowMarker";
            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';

            if($subRow)
            {
                $info = $info.  '<tr>'.
                                    '<th width="20%" style="background-color:'.$color.'">금일</th>'.
                                    '<td>'.number_format($subRow["snow_today"]/10,1).' cm</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<th style="background-color:'.$color.'">시간</th>'.
                                    '<td>'.number_format($subRow["snow_hour"]/10,1).' cm</td>'.
                                '</tr>';
            }
            $info = $info.  '<tr>'.
                                '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn snow" data-type="snow" data-num="'.$row["CD_DIST_OBSV"].'">데이터 보기</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
        }
        else if( $row["GB_OBSV"] == "17" )
        {
            $color="#f3732c";
            $imgSrc = "/image/broadMarker";
            
            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">'.
                            '<tr>'.
                                '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn alert" data-type="alert" data-num="'.$row["CD_DIST_OBSV"].'">방송하기</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
			
        }
        else if( $row["GB_OBSV"] == "18" )
        {
            $color="#ffb200";
            $subQuery = "select * from wb_disstatus where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."'";
            $subRes = mysqli_query( $conn, $subQuery );
            $subRow = mysqli_fetch_array( $subRes );
            
            $imgSrc = "/image/displayMarker";

            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';

            if($subRow)
            {
                if( $subRow["ExpStatus"] == "ad" ){ $type = "일반"; }else if( $subRow["ExpStatus"] == "emg" ){ $type = "긴급"; } else { $type = "-"; }
                $power =  explode("/", $subRow["Power"]);
                
                $info = $info.  '<tr>'.
                                    '<th width="50%" style="background-color:'.$color.'">전원상태</th>'.
                                    '<td>';
                                            for( $i = 0; $i<count($power); $i++ )
                                            {
                                                if( $i > 0 ){ $info = $info. "/"; }
                                                if( $power[$i] == 0 ){ $info = $info. "OFF"; }
                                                else if( $power[$i] == 1 ){ $info = $info. "ON"; }
                                            }	
                $info = $info . '</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<th width="50%" style="background-color:'.$color.'">표출타입</th>'.
                                    '<td>'.$type.'</td>'.
                                '</tr>';
            }
            $info = $info.  '<tr>'.
                                '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn display" data-type="display" data-num="'.$row["CD_DIST_OBSV"].'">상태 확인</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
        }
        else if( $row["GB_OBSV"] == "20" )
        {
            $color="#e66ba1";
            $imgSrc = "/image/gateMarker";
            $subQuery = "select * from wb_gatestatus where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."'";
            $subRes = mysqli_query( $conn, $subQuery );
            $subRow = mysqli_fetch_array( $subRes );
			
            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';
            if($subRow)
            {
                if( $subRow["Gate"] == "open" ){ $gate = "열림"; }else{ $gate = "닫힘"; } 
                $info = $info.  '<tr>'.
                                    '<th width="50%" style="background-color:'.$color.'">차단기</th>'.
                                    '<td>'.$gate.'</td>'.
                                '</tr>';
            }
            $info = $info.  '<tr>'.
                                '<td colspan="2" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn gate" data-type="gate" data-num="'.$row["CD_DIST_OBSV"].'">데이터 보기</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';  
        }
        else if( $row["GB_OBSV"] == "21" )
        {
            $color = "#f94045";
            $imgSrc = "/image/floodMarker";
			
			$subQuery = "select ifnull( (select MR". (date("G", time())+1)." from wb_water1hour where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."' and RegDate = '".date("Ymd", time())."' ) , 0) as water,
      							ifnull( (select MR". (date("G", time())+1)." from wb_flood1hour where CD_DIST_OBSV = '".$row["CD_DIST_OBSV"]."' and RegDate = '".date("Ymd", time())."' ) , '000') as flood";
			$subRes = mysqli_query( $conn, $subQuery );
			$subRow = mysqli_fetch_array( $subRes );
			
            $info = '<div class="cs_info">'.
                        '<div class="cs_title" style="background-color:'.$color.'">'.$row["NM_DIST_OBSV"].'</div>'.
                        '<div>'.
                            '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="cs_infotable">';

            if($subRow)
            {
                $flood = preg_split('//u', $subRow["flood"], -1, PREG_SPLIT_NO_EMPTY);
                
                $floodData = array("X","X","X");

                if( !empty($flood[0]) && $flood[0] == "1" ){ $floodData[0] = "O"; } 
                else{ $floodData[1] = "X"; }
                
                if( !empty($flood[1]) && $flood[1] == "1" ){ $floodData[1] = "O"; } 
                else{ $floodData[1] = "X"; }
                
                if( !empty($flood[2]) && $flood[2] == "1" ){ $floodData[2] = "O"; } 
                else{ $floodData[2] = "X"; }
                    
                $info = $info.  '<tr>'.
                                    '<th width="50%" style="background-color:'.$color.'">수위</th>'.
                                    '<td colspan="3">'.number_format($subRow["water"]*100,1).'</td>'.
                                '</tr>'.
                                '<tr>'.
                                    '<th width="50%" style="background-color:'.$color.'">침수</th>'.
                                    '<td width="16.6%">'.$floodData[0].'</td>'.
                                    '<td width="16.6%">'.$floodData[1].'</td>'.
                                    '<td width="16.6%">'.$floodData[2].'</td>'.
                                '</tr>';
            }
            $info = $info.  '<tr>'.
                                '<td colspan="4" style="padding:3px;background-color:'.$color.'"><div class="cs_viewBtn flood" data-type="flood" data-num="'.$row["CD_DIST_OBSV"].'">데이터 보기</div></td>'.
                            '</tr>'.
                            '</table>'.
                        '</div>'.
                    '</div>';
        }
        else { continue; }
        
        if( $row["ErrorChk"] > 0 ) $imgSrc = $imgSrc.".png";
        else $imgSrc = $imgSrc."_error.png";
        
		$data = array( "title" => $row["NM_DIST_OBSV"] , "JHlat" => $row["LAT"], "JHLong" => $row["LON"], "ImageFile" => $imgSrc, "InfoBox" => $info );
		
		array_push( $saveArray, $data );
		            
        $count++;
    } 
	
	echo json_encode( $saveArray );
?>
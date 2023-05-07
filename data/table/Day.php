<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
    
    if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y",time());}
    if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m",time());}
    if(isset($_GET['day'])) {$day = $_GET['day'];} else {$day = date("d",time());}
    if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}

    $selectDate = $year.$month.$day;

    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

    $equip_dao = new WB_EQUIP_DAO;
    $equip_vo = new WB_EQUIP_VO;
    $equip_vo = $equip_dao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");

    $dao = new WB_DATA1HOUR_DAO($dType);
    $vo = new WB_DATA1HOUR_VO;
?>
<div class="cs_frame">
    <div class="cs_selectBox">
        <div style="font-size: 16px;margin-bottom: 5px;"><?=$year?>년<?=$month?>월<?=$day?>일</div>
        <?php
            if( $dType == "water" ) echo "<div class='cs_unit'>(단위 : m)</div>";
            else if( $dType == "soil" ) echo "<div class='cs_unit'>(단위 : %)</div>";
            else if( $dType == "tilt" ) echo "<div class='cs_unit'>(단위 : °)</div>";
            else if( $dType == "snow" || $dType == "flood" ) echo "<div class='cs_unit'>(단위 : cm)</div>";
            else echo "<div class='cs_unit'>(단위 : mm)</div>";
        ?>
        <div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
                <input type="hidden" name="addr" id="id_addr" value="Day.php">
                <input type="hidden" name="dType" value="<?=$dType?>">

                <select name="year" id="id_select">
                <?php 
                    for( $y = 2021; $y < date("Y", time())+1; $y++ ) 
                    {
                        if( $year == $y ) { $selected = "selected"; } else { $selected = ""; }
                        echo "<option value='{$y}' {$selected}>{$y}</option>";
                    } 
                ?>
                </select> 년

                <select name="month" id="id_select">
                <?php 
                    for($m = 1; $m <= 12; $m++) 
                    {
                        if( $m < 10 ) { $date = "0{$m}"; } else { $date = $m; }
                        if( $month == $date ) { $selected = "selected"; } else { $selected = ""; }
                        echo "<option value='{$date}' {$selected}>{$date}</option>";
        
                    } 
                ?>
                </select> 월

                <select name="day" id="id_select">
                <?php 
                    for( $d = 1; $d <= 31; $d++ ) 
                    {
                        if( $d < 10 ) { $date = "0{$d}"; } else { $date = $d; }
                        if( $day == $date ) { $selected = "selected"; } else { $selected = ""; }

                        echo "<option value='{$date}' {$selected}>{$date}</option>";
                    } 
                ?>
                </select> 일
                <div class="cs_search" id="id_search">검색</div>
            </form>
			<div class="cs_excel" id="id_excel">엑셀다운</div>
        </div>
    </div> <?php //selectBox?>

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows" style="border:1px solid <?=$border_color?>;">
        <tr style="position:sticky;top:0px; background-color: <?=$border_color?>;">
        <?php
            if( $dType == "dplace" ) echo "<th colspan='2' width='150'>지역명</th>";
            else echo "<th width='100'>지역명</th>";

            if( $dType == 'flood' ) 
            {
                echo"<th>타입</th>";
            }

            for( $i = 0; $i < 24; $i++ )
            {
                echo "<th>{$i}</th>";
            }

            if( $dType == "rain" )
            {
                echo "<th width='50'>최고</th>";
                echo "<th width='60'>계</th>";
            }
            else if( $dType == "water" )
            {
                echo "<th width='50'>최대</th>";
                echo "<th width='50'>최소</th>";
            }
            else if( $dType == "dplace" || $dType == "snow" )
            {
                echo "<th width='50'>최고</th>";
            }
            echo "</tr>";

            foreach($equip_vo as $evo)
            {
                echo "<tr>";

                /* 지역명 & Sub_OBSV || 침수 Row 갯수 */
                if( $dType == "dplace" ) $row = $evo->SubOBCount;
                else if( $dType == "flood" ) $row = '4';
                else $row = '1';

                echo "<td rowspan='{$row}' style='font-weight:bold; background-color:#f2f2f2; border-right:2px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
                
                /* 변위, 침수는 한 쿼리에 두줄 이상 사용하기에 따로 구현 */
                if( $dType != "dplace" && $dType != "flood" )
                {
                    // 강우, 수위, 함수비, 적설, 경사
                    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");
                    $max = 0;

                    for( $i = 1; $i <= 24; $i++)
                    {
                        echo "<td>";
                        if( $vo->{"MR{$i}"} !== null )
                        {
                            if( $dType == "snow" ) echo number_format($vo->{"MR{$i}"}/10, 1); // 적설 Cm
                            else if( $dType == "water" ) echo number_format($vo->{"MR{$i}"}/1000, 1); // 수위 M
                            else if( $dType == "rain" )
                            {
                                echo number_format($vo->{"MR{$i}"}, 1);
                                if( $max < $vo->{"MR{$i}"} ) $max = $vo->{"MR{$i}"}; // rain1hour Table에는 DayMax Column이 없어서 따로 구함
                            }
                            else echo number_format($vo->{"MR{$i}"}, 1); 
                        }
                        else echo "-";
                        echo "</td>";
                    }

                    // DB는 mm 수집 & (Rain : mm , Water : M , Snow : Cm) 소수점 한자리까지만 표출 (Min, Max, Sum)
                    switch($dType)
                    {
                        case 'snow' :
                            $strMax = is_numeric($vo->DayMax)? number_format($vo->DayMax/10, 1) : "-";        
                            break;
                            
                        case 'water' :
                            $strMax = is_numeric($vo->DayMax)? number_format($vo->DayMax/1000, 1) : "-";
                            $strMin = is_numeric($vo->DayMin)? number_format($vo->DayMin/1000, 1) : "-";
                            break;

                        case 'rain' :
                            $strMax = is_numeric($max)? number_format($max, 1) : "-";
                            $strSum = is_numeric($vo->DaySum)? number_format($vo->DaySum, 1) : "-";
                            break;
                            
                    }

                    if( $dType == "rain" ) 
                    {
                        echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                        echo "<td style='color:a30003; font-weight:bold'>{$strSum}</td>";
                    }
                    else if( $dType == "snow" )
                    {
                        echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                    }
                    else if( $dType == "water" )
                    {
                        echo "<td style='background:#E7E9C9; font-weight:bold'>{$strMax}</td>";
                        echo "<td style='background:#D8E5F8; font-weight:bold'>{$strMin}</td>";
                    }
                }
                else if( $dType == "dplace" )
                {
                    // 변위
                    // $i = Sub_OBSV로 $i++ 될때마다 쿼리로 데이터값 받아와 mm로 표출
                    for( $i = 1; $i <= $evo->SubOBCount; $i++ )
                    {
                        if( $i != 1 ) echo "<tr>";
                        echo "<td width='20' style='background-color:#f2f2f2;'>{$i}</td>";

                        $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'", $i);

                        for( $j = 1; $j <= 24; $j++)
                        {
                            echo "<td>";
                            if( $vo->{"MR{$j}"} !== null )
                            {
                                echo number_format($vo->{"MR{$j}"}, 1);
                            }
                            else echo "-";
                            echo "</td>";
                        }

                        $strMax = is_numeric($vo->DayMax)? number_format($vo->DayMax, 1) : "-";
                        echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";

                        if( $i != $evo->SubOBCount ) echo "</tr>";
                    }
                }
                else if( $dType == "flood" )
                {
                    // 침수
                    // 침수수위 $data 배열에 담기
                    $dao = new WB_DATA1HOUR_DAO("water");
                    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");

                    // 침수상태 $flood 배열에 담기
                    $dao = new WB_DATA1HOUR_DAO("flood");
                    $floodvo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");

                    // 침수수위 표출
                    echo "<td style='background-color:#f2f2f2;'>수위</td>";
                    for( $i = 1; $i <= 24; $i++ )
                    {
                        echo "<td>";
                        if( $vo->{"MR{$i}"} !== null ) echo number_format($vo->{"MR{$i}"}/10, 1);
                        else echo "-";
                        echo "</td>";
                    }
                    echo "</tr>";
                    
                    // 침수상태 표출
                    for( $j = 0; $j <= 2; $j++ )
                    {
                        echo "<tr>";
                            echo "<td style='background-color:#f2f2f2;'>침수".($j+1)."</td>";
                            for( $i = 1; $i <= 24; $i++ )
                            {
                                echo "<td>";
                                
                                    if( $floodvo->{"MR{$i}"} === null )
                                    {
                                        echo "-";
                                    }
                                    else
                                    {
                                        if( $floodvo->{"MR{$i}"}[$j] === "0" ){ echo "X"; }
                                        elseif( $floodvo->{"MR{$i}"}[$j] === "1" ){ echo "O"; }
                                        else{ echo "-";} 
                                    }
                                
                                echo "</td>";
                            }
                        if( $j != 3 ) echo "</tr>";
                    }
                }

                echo "</tr>";
            }
        ?>
    </table>
</div> <?php //frame?>
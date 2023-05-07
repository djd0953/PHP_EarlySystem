<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
    
    if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y",time());}
    if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m",time());}
    if(isset($_GET['day'])) {$day = $_GET['day'];} else {$day = date("d",time());}
    if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}
    if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = "";}
    if(isset($_GET['floodType'])) {$floodType = $_GET['floodType'];} else {$floodType = 'water';}
    
    $tableName = $dType;
    $selectDate = $year.$month.$day;
    $nextDate = $selectDate + 1;

    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

    $equip_dao = new WB_EQUIP_DAO;
    $equip_vo = new WB_EQUIP_VO;
    $data_vo = new WB_DATA1MIN_VO;

    if(isset($_GET['area'])) {$area = $_GET['area'];} 
    else 
    {
        $equip_vo = $equip_dao->SELECT_SINGLE("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
        $area = $equip_vo->CD_DIST_OBSV;
    }
?>

<div class="cs_frame">
	<div class="cs_selectBox">
        <?php
        $floodTypeEcho = "";
        if( $dType =="flood" )
        {
            if( $floodType == "water" ) $floodTypeEcho = "( 침수수위 )";
            else if( $floodType == "flood" ) $floodTypeEcho = "( 침수상태 )";
        }
        echo "<div style='font-size: 16px;margin-bottom: 5px;'>{$year}년{$month}월{$day}일 {$floodTypeEcho}</div>";

        if( $dType == "water" ) echo "<div class='cs_unit'>(단위 : m)</div>";
        else if( $dType == "soil" ) echo "<div class='cs_unit'>(단위 : %)</div>";
        else if( $dType == "tilt" ) echo "<div class='cs_unit'>(단위 : °)</div>";
        else if( $dType == "flood" ) echo "<div class='cs_unit'>(단위 : cm)</div>";
        else echo "<div class='cs_unit'>(단위 : mm)</div>";
        ?>
        <div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">  
                <input type="hidden" name="addr" id="id_addr" value="Time.php">
            	
                <?php
                    // 해당 장비 전체 불러오기 (Select Box)
                    $equip_vo = $equip_dao->SELECT("GB_OBSV  = '{$area_code}' and USE_YN = '1'");

                    echo "<select name='area' id='id_select'>";
                    foreach( $equip_vo as $v )
                    {
                        if( $area == "" ) $area = $v->CD_DIST_OBSV;
                        if( $area == $v->CD_DIST_OBSV ) $sel = "selected";

                        echo "<option value='{$v->CD_DIST_OBSV}' {$sel}>{$v->NM_DIST_OBSV}</option>"; 
                    }
                    echo "</select>&nbsp;&nbsp;";

                    // 변위: Sub OBSV 불러와서 Select Box 만들기, 침수: 침수수위/침수상태 Select Box 만들기
                    if( $dType == "dplace" )
                    {
                        if( $equip == "" ) $equip = 1;
                        $equip_vo = $equip_dao->SELECT_SINGLE("CD_DIST_OBSV = '{$area}'");

                        echo "<select name='equip' id='id_select'>";
                        for($i = 1; $i <= $equip_vo->SubOBCount; $i++)
                        {
                            echo "<option value='{$i}'";
                            if($equip == $i) echo "selected";
                            echo ">{$i}</option>";
                        }
                        echo "</select>&nbsp;&nbsp;";
                        echo "<input type='hidden' name='dType' value='dplace'>";
                    }
                    else if($dType == "flood")
                    { 
                        echo "<select name='floodType' id='id_select'>";
                            if ( $floodType == "water" ) $sel = "selected"; else $sel = "";
                            echo "<option value='water' {$sel}>침수수위</option>";
                            if ( $floodType == "flood" ) $sel = "selected"; else $sel = "";
                            echo "<option value='flood' {$sel}>침수상태</option>";
                        echo "</select>&nbsp;&nbsp;";

                        echo "<input type='hidden' name='dType' value='flood'>";
                    }
                    else echo "<input type='hidden' name='dType' value='{$dType}'>"; 

                    // Date 년/월/일 (Select Box)
                    echo "<select name='year' id='id_select'>";
                    for($y = 2021; $y < date("Y", time())+1; $y++) 
                    { 
                        if($year == $y) {$selected = "selected";} else {$selected = "";}
                        echo "<option value='{$y}' {$selected}>{$y}</option>";
                    }
                    echo "</select> 년&nbsp;&nbsp;";
                    
                    echo "<select name='month' id='id_select'>";
                    for($m = 1; $m <= 12; $m++) 
                    { 
                            if($m < 10) {$date = "0{$m}";} else {$date = $m;}
                            if($month == $date) {$selected = "selected";} else {$selected = "";}

                            echo "<option value='{$date}' {$selected}>{$date}</option>";
                    }
                    echo "</select> 월&nbsp;&nbsp;";

                    echo "<select name='day' id='id_select'>";
                    for($d = 1; $d <= 31; $d++) 
                    {
						if($d < 10) {$date = "0{$d}";} else {$date = $d;}
						if($day == $date) {$selected = "selected";} else {$selected = "";}

                        echo "<option value='{$date}' {$selected}>{$date}</option>";
                    }
                    echo "</select> 일";
                ?>
                <div class="cs_search" id="id_search">검색</div>
            </form>   
			<div class="cs_excel" id="id_excel">엑셀다운</div>
        </div>
    </div> <?php //selectBox?>

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows" style="border:1px solid <?=$border_color?>;">
    	<tr style="position:sticky;top:0px; background-color: <?=$border_color?>;">
        	<th width="100">시간</th>
            <?php for($i = 0; $i < 24; $i++) echo "<th>{$i}</th>"; ?>
	    </tr>  

        <?php
            // 침수 시간별 데이터 표출 시 침수위,상태 선택에 따라 표출해야 할 테이블이 다름
            if( $dType == 'flood' )
            {
                if ( $floodType == 'water') $tableName = 'water';
                else if ( $floodType == 'flood') $tableName = 'flood';
            }

            $data_dao = new WB_DATA1MIN_DAO($tableName);
            $data_vo = $data_dao->SELECT("RegDate BETWEEN '{$selectDate}' AND '{$nextDate}' AND CD_DIST_OBSV = '{$area}'", $equip);

            if( $floodType == 'flood' ) $array = array_fill(0,24,array_fill(0,60,"222"));
            else $array = array_fill(0,24,array_fill(0,60,""));

            // 검색 결과 array에 담기
            foreach( $data_vo as $v )
            {
                $data = $v->MRMin_array();
                for($i = 0; $i < count($data); $i++)
                {
                    $array[($v->idx * 1)][$i] =$data[$i];
                }
            }

            // 표출
            for($i = 0; $i < 60; $i++) 
            {
                echo "<tr>";
                    echo "<td style='font-weight:bold; background-color:#f2f2f2;'>{$i}분</td>";
                
                    for($j = 0; $j < 24; $j++) 
                    {
                        echo "<td>";
                        if( $floodType == "flood" )
                        {
                            //기본 "222"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                            if( $array[$j][$i] === "" || $array[$j][$i] === '222' ) echo "-";
                            else
                            {
                                if( $array[$j][$i][0] === "0" ){ echo "X"; }
                                else if( $array[$j][$i][0] === "1" ){ echo "O"; }

                                if( $array[$j][$i][1] === "0" ){ echo "X"; }
                                else if( $array[$j][$i][1] === "1" ){ echo "O"; }

                                if( $array[$j][$i][2] === "0" ){ echo "X"; }
                                else if( $array[$j][$i][2] === "1" ){ echo "O"; }
                            }
                        }
                        else
                        {
                            // 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                            if($array[$j][$i] === "" || !is_numeric($array[$j][$i])) echo "-";
                            else 
                            {
                                //Water는 M표출, 그 외 Rain, dplace는 mm표출
                                if($dType == "water") echo "<font color='#4900FF'>".number_format($array[$j][$i]/1000,1)."</font>";
                                else if( $dType == "flood" ) echo "<font color='#4900FF'>".number_format($array[$j][$i]/10,1)."</font>";
                                else echo "<font color='#4900FF'>".number_format($array[$j][$i],1)."</font>";
                            }
                        }
                        echo "</td>";
                    }
                    echo "</tr>";
            } //for
        ?>
    </table>
    <div style="height:150px;"></div>
</div> <?php //frame?>
<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	
	if(isset($_GET['year1'])) {$year1 = $_GET['year1'];} else {$year1 = date("Y",strtotime("-7days"));}
	if(isset($_GET['month1'])) {$month1 = $_GET['month1'];} else {$month1 = date("m",strtotime("-7days"));}
	if(isset($_GET['day1'])) {$day1 = $_GET['day1'];} else {$day1 = date("d",strtotime("-7days"));}

	if(isset($_GET['year2'])) {$year2 = $_GET['year2'];} else {$year2 = date("Y",time());}
	if(isset($_GET['month2'])) {$month2 = $_GET['month2'];} else {$month2 = date("m",time());}
	if(isset($_GET['day2'])) {$day2 = $_GET['day2'];} else {$day2 = date("d",time());}

	if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}
	if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = "";}
	if(isset($_GET['floodType'])) {$floodType = $_GET['floodType'];} else {$floodType = 'water';}

	$selectDate1 = $year1.$month1.$day1;
	$selectDate2 = $year2.$month2.$day2;

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

	$equip_dao = new WB_EQUIP_DAO;
	$equip_vo = new WB_EQUIP_VO;

	$datadao = new WB_DATA1HOUR_DAO($dType);
	$datavo = new WB_DATA1HOUR_VO;

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
			if($dType == "water") echo "<div class='cs_unit'>(단위 : m)</div>";
			else if($dType == "soil") echo "<div class='cs_unit'>(단위 : %)</div>";
			else if($dType == "tilt") echo "<div class='cs_unit'>(단위 : °)</div>";
			else if($dType == "snow" || $dType == "flood" ) echo "<div class='cs_unit'>(단위 : cm)</div>";
			else echo "<div class='cs_unit'>(단위 : mm)</div>";
        ?>
        <div class="cs_date">
			<form name="form" id="id_form" method="get" action="" style="display:inline-block;">  
                <input type="hidden" name="addr" id="id_addr" value="Period.php">

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

					// Date1 년/월/일 (Select Box)
                    echo "<select name='year1' id='id_select'>";
                    for($y = 2021; $y < date("Y", time())+1; $y++) 
                    { 
                        if($year1 == $y) {$selected = "selected";} else {$selected = "";}
                        echo "<option value='{$y}' {$selected}>{$y}</option>";
                    }
                    echo "</select> 년&nbsp;&nbsp;";
                    
                    echo "<select name='month1' id='id_select'>";
                    for($m = 1; $m <= 12; $m++) 
                    { 
                            if($m < 10) {$date = "0{$m}";} else {$date = $m;}
                            if($month1 == $date) {$selected = "selected";} else {$selected = "";}

                            echo "<option value='{$date}' {$selected}>{$date}</option>";
                    }
                    echo "</select> 월&nbsp;&nbsp;";

                    echo "<select name='day1' id='id_select'>";
                    for($d = 1; $d <= 31; $d++) 
                    {
						if($d < 10) {$date = "0{$d}";} else {$date = $d;}
						if($day1 == $date) {$selected = "selected";} else {$selected = "";}

                        echo "<option value='{$date}' {$selected}>{$date}</option>";
                    }
                    echo "</select> 일";

					// Date2 년/월/일 (Select Box)
                    echo "<select name='year2' id='id_select'>";
                    for($y = 2021; $y < date("Y", time())+1; $y++) 
                    { 
                        if($year2 == $y) {$selected = "selected";} else {$selected = "";}
                        echo "<option value='{$y}' {$selected}>{$y}</option>";
                    }
                    echo "</select> 년&nbsp;&nbsp;";
                    
                    echo "<select name='month2' id='id_select'>";
                    for($m = 1; $m <= 12; $m++) 
                    { 
                            if($m < 10) {$date = "0{$m}";} else {$date = $m;}
                            if($month2 == $date) {$selected = "selected";} else {$selected = "";}

                            echo "<option value='{$date}' {$selected}>{$date}</option>";
                    }
                    echo "</select> 월&nbsp;&nbsp;";

                    echo "<select name='day2' id='id_select'>";
                    for($d = 1; $d <= 31; $d++) 
                    {
						if($d < 10) {$date = "0{$d}";} else {$date = $d;}
						if($day2 == $date) {$selected = "selected";} else {$selected = "";}

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
		<th width="110">날짜</th>
		<?php
			for($i = 0; $i < 24; $i++)
			{
				echo "<th>{$i}</th>";
			}
	
			if($dType == "rain")
			{
				echo "<th width='50'>최고</th>";
				echo "<th width='60'>계</th>";
			}
			else if($dType == "water")
			{
				echo "<th width='50'>최대</th>";
				echo "<th width='50'>최소</th>";
			}
			else if($dType == "dplace" || $dType == "snow")
			{
				echo "<th width='50'>최고</th>";
			}
			echo "</tr>";

			// 침수 중 침수수위의 경우 Data가 Water1Hour Table에 있으니 DAO Type 변경!
			if( $dType == "flood" && $floodType == "water" ) $datadao = new WB_DATA1HOUR_DAO("water");

			$datavo = $datadao->SELECT("RegDate BETWEEN '{$selectDate1}' AND '{$selectDate2}' AND CD_DIST_OBSV = '{$area}'", $equip);

			foreach($datavo as $v)
			{
				$strMin = '';
				$strMax = '';
				$strSum = '';

				$max = "";
				$min = "";
				$sum = 0;

				echo "<tr>";
				echo "<td style='font-weight:bold; background-color:#f2f2f2'>".date("Y월 m월 d일", strtotime($v->RegDate))."</td>";

				for($i = 1; $i <= 24; $i++)
				{
					echo "<td>"; // 요~부터
					if( $dType == "flood" && $floodType == "flood" )
					{
						if( $v->{"MR{$i}"} === "" || $v->{"MR{$i}"} === null ) echo "-";
						else
						{
							if( $v->{"MR{$i}"}[0] === "0" ){ echo "X"; }
							elseif( $v->{"MR{$i}"}[0] === "1" ){ echo "O"; }
							
							if( $v->{"MR{$i}"}[1] === "0" ){ echo "X"; }
							elseif( $v->{"MR{$i}"}[1] === "1" ){ echo "O"; }
							
							if( $v->{"MR{$i}"}[2] === "0" ){ echo "X"; }
							elseif( $v->{"MR{$i}"}[2] === "1" ){ echo "O"; }
						}
					}
					else
					{
						if( $v->{"MR{$i}"} === "" ) echo "-";
						else
						{
							echo "<font color='#4900FF'>";
							if($dType == "snow" || $dType == "flood" ) echo number_format($v->{"MR{$i}"}/10,1);
							else if($dType == "water") echo number_format($v->{"MR{$i}"}/1000,1);
							else if($dType == "dplace") echo number_format($v->{"MR{$i}"},3);
							else echo number_format($v->{"MR{$i}"},1);
		
							if( $sum == 0 )
							{
								$max = $v->{"MR{$i}"};
								$min = $v->{"MR{$i}"};
							}
							else
							{
								if($max < $v->{"MR{$i}"}) $max = $v->{"MR{$i}"};
								if($min > $v->{"MR{$i}"}) $min = $v->{"MR{$i}"};
							}
							$sum += $v->{"MR{$i}"};
							echo "</font>";
						}
					}
					echo "</td>"; // 요~까지 한칸!
				}

				switch( $dType )
				{
					case 'rain' :
						$strMax = is_numeric($max)? number_format($max,1) : $max;
						echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";

						$strSum = is_numeric($sum)? number_format($sum,1) : $sum;
						echo "<td style='color:a30003; font-weight:bold'>{$strSum}</td>";
						break;
					
					case 'snow' :
						$strMax = is_numeric($max)? number_format($max/10,1) : $max;
						echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";       
						break;

					case 'water' :
						$strMax = is_numeric($max)? number_format($max/1000,1) : $max;
						echo "<td style='background:#E7E9C9; font-weight:bold'>{$strMax}</td>";

						$strMin = is_numeric($min)? number_format($min/1000,1) : $min;
						echo "<td style='background:#D8E5F8; font-weight:bold'>{$strMin}</td>";
						break;

					case 'dplace' :
						$strMax = is_numeric($max)? number_format($max,3) : $max;
						echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
						break;

					case "flood" :
						break;
						
					default :
						$strMax = is_numeric($max)? number_format($max,1) : $max;
						echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
				}
				echo "</tr>";

			}
		?>
	</table>
</div> <?php //frame?>
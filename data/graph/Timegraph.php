<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	
	if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y", time());}
	if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m", time());}
	if(isset($_GET['day'])) {$day = $_GET['day'];} else {$day = date("d", time());}
	if(isset($_GET['hour'])) {$hour = $_GET['hour'];} else {$hour = date("H", time());}
	if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = "";}
	if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}

	$selectDate = $year.$month.$day.$hour;
	$select = $year.$month.$day;

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

	$equipdao = new WB_EQUIP_DAO;
	$equipvo = new WB_EQUIP_VO;

	$data_dao = new WB_DATA1MIN_DAO($dType);
	$data_vo = new WB_DATA1MIN_VO;

	if(isset($_GET['area'])) {$area = $_GET['area'];} 
	else 
	{
		$equipvo = $equipdao->SELECT_SINGLE("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
		$area = $equipvo->CD_DIST_OBSV;
	}

	//수위 그래프는 침수용 수위 그래프도 표출해야함.
	if( $dType == "water" ) $gbobsv = "'02', '21'";
	else $gbobsv = "'{$area_code}'";
?>

<div class="cs_frame">
    <div class="cs_selectBox">
		<?php
        if($dType == "water") echo "<div class='cs_unit'>(단위 : m)</div>";
        else echo "<div class='cs_unit'>(단위 : mm)</div>";
        ?>
    	<div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">  
                <input type="hidden" name="addr" id="id_addr" value="Timegraph.php">
				
				<select name="area" id="id_select_graph">
					<?php
						$equipvo = $equipdao->SELECT("GB_OBSV IN ({$gbobsv}) AND USE_YN = '1'");
						foreach($equipvo as $evo)
						{
							$selected = "";
							if( $area == $evo->CD_DIST_OBSV ) 
							{
								$areaName = $evo->NM_DIST_OBSV;
								$selected = "selected";
							}
							echo "<option value='{$evo->CD_DIST_OBSV}' {$selected}>{$evo->NM_DIST_OBSV}</option>";
						}
					?>
                </select>&nbsp;&nbsp;

                <?php
					if($dType == "dplace")
					{
						$equipvo = $equipdao->SELECT_SINGLE("CD_DIST_OBSV = '{$area}'");

						echo "<select name='equip' id='id_select_graph'>";
						for($i = 1; $i <= $equipvo->SubOBCount; $i++)
						{
							$selected = "";
							if( $equip == $i ) $selected = "selected";
							echo "<option value='{$i}' {$selected}>{$i}</option>";
						}
						echo "</select>&nbsp;&nbsp;";
						echo "<input type='hidden' name='dType' value='dplace'>";
					}
					else echo "<input type ='hidden' name='dType' value='{$dType}'>";
				?>

                <select name="year" id="id_select_graph">
                <?php 
					for($y = 2021; $y < date("Y", time())+1; $y++) 
					{ 
                        if($year == $y) {$selected = "selected";} else {$selected = "";}
						echo "<option value='{$y}'{$selected}>{$y}</option>";
					}
                 ?>
                </select> 년
                
                <select name="month" id="id_select_graph">
                <?php 
					for($m = 1; $m <= 12; $m++) 
					{ 
						if($m < 10) {$date = "0".$m;} else {$date = $m;}
						if($month == $date) {$selected = "selected";} else {$selected = "";}
						echo "<option value='{$date}'{$selected}>{$date}</option>";
                	} 
				?>
                </select> 월
                
                <select name="day" id="id_select_graph">
                <?php 
					for($d = 1; $d <= 31; $d++) 
					{
						if($d < 10) {$date = "0".$d;} else {$date = $d;}
						if($day == $date) {$selected = "selected";} else {$selected = "";}
						echo "<option value='{$date}'{$selected}>{$date}</option>";
					}
				?>
                </select> 일
                
                <select name="hour" id="id_select_graph">
                <?php 
					for($m = 0; $m < 24; $m++) 
					{ 
						if($m < 10) {$date = "0".$m;} else {$date = $m;}
						if($hour == $date) {$selected = "selected";} else {$selected = "";}
						echo "<option value='{$date}'{$selected}>{$date}</option>";
					}
				?>
                </select> 시
                
                <div class="cs_search" id="id_search_graph">검색</div>
            </form>   
        </div>
    </div>
    <canvas id="id_myChart" width="400" height="130"></canvas>
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows" style="border:1px solid <?=$border_color?>;">
     	<tr style="position:sticky;top:0px; background-color: <?=$border_color?>;">
        	<th width="100">시간</th>
            <?php 
				for($i = 0; $i < 10; $i++) echo "<th>{$i}</th>"; 
			?>
	    </tr>  

        <?php
            $data_vo = $data_dao->SELECT_SINGLE("RegDate BETWEEN '{$selectDate}' AND '{$selectDate}' AND CD_DIST_OBSV = '{$area}'", $equip);

			$array = array_fill(0,60,"");

            // 검색 결과 array에 담기
			$array = $data_vo->MRMin_array();

            // 표출
            for($i = 0; $i < 6; $i++) 
            {
                echo "<tr>";
                    echo "<td style='font-weight:bold; background-color:#f2f2f2;'>{$i}0분</td>";
                
                    for($j = 0; $j < 10; $j++) 
                    {
                        echo "<td>";

						// 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
						if( $array[($i*10)+$j] === "" ) 
						{
							$array[($i*10)+$j] = 0;
							echo "-";
						}
						else 
						{
							//Water는 M표출, 그 외 Rain, dplace는 mm표출
							if( $dType == "water" ) echo "<font color='#4900FF'>".number_format( $array[($i*10)+$j]/1000,1)."</font>";
							else echo "<font color='#4900FF'>".number_format( $array[($i*10)+$j],1)."</font>";
						}
                        echo "</td>";
                    }
                    echo "</tr>";
            } //for
        ?>
    </table>
    </table>
</div> <?php //frame?>

<script src="/js/jquery-1.9.1.js"></script>
<script src="/js/Chart.min.js"></script>

<script>
	var hour = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23',
				'24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50',
				'51', '52', '53', '54', '55', '56', '57', '58', '59'];

<?php
	$label = "{$areaName}";
	$data = "";
	$bColor = "'rgba(255, 99, 132, 1)'";

	for ($i = 0; $i < 59; $i++)
	{
		$bColor = "{$bColor},'rgba(255, 99, 132, 1)'";
	}

	// Chart.min.js 차트 표현식 중 데이터 입력 방식 [0,0,0,0,...,n]
	for( $i = 0; $i < 60; $i++ )
	{
		if( $array[$i] !== "" ) 
		{
			// 데이터가 있다면 데이터 입력
			if( $i == 0 ) 
			{
				if( $dType == "snow" ) $data = ($array[$i] / 10)."";
				elseif( $dType == "water" ) $data =  ($array[$i] / 1000)."";
				else $data = ($array[$i])."";
			}
			else 
			{
				if( $dType == "snow" ) $data = $data.",".($array[$i] / 10);
				elseif( $dType == "water" ) $data =  $data.",".($array[$i] / 1000);
				else $data = $data.",".($array[$i]);
			}	
		}
		else 
		{
			// 라인 형태의 그래프이기때문에 중간 값이 비면 자연스러운 연결을 위해 앞과 뒤의 값을 이용해 연결 (처음 값과 마지막 값은 상관 없으므로 예외처리)
			if( $i == 0 ) {$data = "";}
			else if ( $i == 59 ) {$data = "{$data},";}
			else
			{
				// 강우 데이터는 막대 그래프 표시로 예외 처리
				if( $dType == "rain" ) $data = "{$data},0";
				else
				{
					$pre_idx = 0;
					$post_idx = 0;
					$pre_data = 0;
					$post_data = 0;
					
					// 앞쪽에 유효한 값 찾기
					for( $j = $i; $j >= 0; $j-- )
					{
						if( $array[$i] !== "" )
						{
							$pre_idx = $j;
	
							if( $dType == "snow" ) $pre_data = $array[$i] / 10;
							elseif( $dType == "water" ) $pre_data = $array[$i] / 1000;
							else $pre_data = $array[$i];
	
							break;
						}
					}
					
					// 뒷쪽에 유효한 값 찾기
					for( $j = $i; $j <= 59; $j++ )
					{
						if( $array[$i] !== "" )
						{
							$post_idx = $j;
							
							if( $dType == "snow" ) $post_data = $array[$i] / 10;
							elseif( $dType == "water" ) $post_data = $array[$i] / 1000;
							else $post_data = $array[$i];
	
							break;
						}
					}
					
					// 앞이나 뒷쪽 값이 없다면 예외 처리
					if( $pre_idx == 0 || $post_idx == 0 ) $data = "{$data},";
					else 
					{
						// 앞, 뒤 값이 있다면 몇번 건너뛰었는지 계산
						$j = ($i - $pre_idx) / ($post_idx - $pre_idx);
						// 건너뛴 갯수와 값에 비례하여 데이터 계산
						$data = "{$data},".($pre_data + ($post_data - $pre_data) * $j);
					}
				}
			}
		}
	}

	echo "let {$dType} = { label: '{$label}', backgroundColor: [{$bColor}], borderColor: ['rgba(255, 99, 132, 1)'], fill: false, data : [{$data}]};";
?>			
	var ctx = document.getElementById('id_myChart').getContext('2d');
	let chartList = {
		type: <?php if( $dType == "rain" ) { echo "'bar'"; } else { echo "'line'"; } ?>,
		data: {
			labels: hour,
			datasets: [ <?=$dType?> ]},
		options : {
			reponsive : true,
			title : {
				display : false
			},
			tooltips : {
				enabled: true
			},
			hover : {
				mode : 'nearest',
				intersect : true	
			},
			scales : {
				xAxes : [{
					display : true,
					scaleLabel : {
						display : true,
						labelString : '<?=date("Y-m-d", strtotime($select))?> 데이터 통계'	
					}	
				}],
				yAxes : [{
					display : true,
					scaleLabel : {
						display : false	
					},
					gridLines : {
						drawBorder : false	
					},
					ticks : {
						display : true
					}	
				}]	
			}  
		}
	};
	
	let type = '<?=$dType?>';
	switch(type)
	{
		case 'rain' :
			chartList.options.scales.yAxes[0].ticks['min'] = 0;
			break;
		case 'water' :
			chartList.options.scales.yAxes[0].ticks['max'] = 15;
			chartList.options.scales.yAxes[0].ticks['min'] = 0;
			break;
		case 'dplace' :
			break;
		case 'soil' :
			chartList.options.scales.yAxes[0].ticks['max'] = 100;
			chartList.options.scales.yAxes[0].ticks['min'] = 0;
			break;
	}
	
	var myChart = new Chart(ctx, chartList);	
</script>

<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	
	if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y", time());}
	if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m", time());}
	if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}

	$selectDate = $year.$month;

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

	$equipdao = new WB_EQUIP_DAO;
	$equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN='1'");

	$dao = new WB_DATA1HOUR_DAO($dType);
	$vo = new WB_DATA1HOUR_VO;
?>
<div class="cs_frame">
	<div class="cs_selectBox">
		<?php
        if($dType == "water" || $dType == "flood") echo "<div class='cs_unit'>(단위 : m)</div>";
        else if($dType == "soil") echo "<div class='cs_unit'>(단위 : %)</div>";
        else if($dType == "tilt") echo "<div class='cs_unit'>(단위 : °)</div>";
        else if($dType == "snow") echo "<div class='cs_unit'>(단위 : cm)</div>";
        else echo "<div class='cs_unit'>(단위 : mm)</div>";
        ?>
    	<div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
                <input type="hidden" name="addr" id="id_addr" value="Monthgraph.php">
                <input type="hidden" name="dType" value="<?=$dType?>">

                <select name="year" id="id_select_graph">
                <?php 
					for($y = 2021; $y < date("Y", time())+1; $y++) 
					{
						if($year == $y) {$selected = "selected";} else {$selected = "";}
						echo "<option value='{$y}' {$selected}>{$y}</option>";
					} 
				?>
                </select> 년

                <select name="month" id="id_select_graph">
                <?php 
					for($m = 1; $m <= 12; $m++) 
					{
						if($m < 10) {$date = "0".$m;} else {$date = $m;}
						if($month == $date) {$selected = "selected";} else {$selected = "";}

						echo "<option value='{$date}' {$selected}>{$date}</option>";
					} 
				?>
                </select> 월
                
                <div class="cs_search" id="id_search_graph">검색</div>
            </form>
        </div>
    </div>
    <canvas id="id_myChart" width="400" height="130"></canvas>
	
	<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows" style="border:1px solid <?=$border_color?>;">
        <tr style="position:sticky;top:0px; background-color: <?=$border_color?>;">
        <?php
			if( $dType=="dplace" ) echo "<th colspan='2' width='150'>지역명</th>";
			else echo "<th width='100'>지역명</th>";

			for($i = 1; $i <= 31; $i++) echo "<th>".$i."</th>";

			echo "</tr>";

			$r = 0;
			$graphData = array();
			foreach($equipvo as $evo)
			{
				// 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
				if( $dType != "dplace" )
				{
					$vo = $dao->SELECT_MONTH("ifnull(date_format(RegDate,'%Y%m'),left(RegDate,6)) = '{$selectDate}' and CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
					$data = array_fill(0, 32, "");
					foreach($vo as $v)
					{
						if( $dType == 'rain' ) $data[$v->idx] = (double)$v->DaySum;
						else $data[$v->idx] = (double)$v->DayMax;
					}

					for($i = 1; $i <= 31; $i++)
					{
						switch($dType)
						{
							case 'rain' :
								$strArr[$i] = ($data[$i] !== "")? "<font color='#4900FF'>".number_format($data[$i],1)."<font>" : '-';
								break;
							
							case 'snow' :
								$strArr[$i] = ($data[$i] !== "")? "<font color='#4900FF'>".number_format($data[$i]/10,1)."</font>" : '-';    
								break;

							case 'water' :
								$strArr[$i] = ($data[$i] !== "")? "<font color='#4900FF'>".number_format($data[$i]/1000,1)."</font>" : '-';
								break;

							default :
								$strArr[$i] = ($data[$i] !== "")? "<font color='#4900FF'>".number_format($data[$i],1)."</font>" : '-';
						}
					}

					echo "<tr>";
					echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
					for($i = 1; $i <= 31; $i++) echo "<td>{$strArr[$i]}</td>";
					echo "</tr>";

					$graphData[$r] = $data;
					$graphData[$r++][0] = $evo->NM_DIST_OBSV;
				}
				else
				{
					echo "<tr>";
					echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";

					for( $e = 1; $e <= $evo->SubOBCount; $e++)
					{
						$vo = $dao->SELECT_MONTH("ifnull(date_format(RegDate,'%Y%m'),left(RegDate,6)) = '{$selectDate}' and CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}'", $e);
						$data = array_fill(0, 32, "");
						foreach($vo as $v) $data[$v->idx] = (double)$v->DayMax;

						for($i = 1; $i <= 31; $i++) $strArr[$i] = ($data[$i] !== "") ? "<font color='#4900FF'>".number_format($data[$i],1)."<font>" : '-';

						if( $e != 1 ) echo "<tr>";
						echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$e}</td>";
						for($i = 1; $i <= 31; $i++) echo "<td>{$strArr[$i]}</td>";
						echo "</tr>";

						$graphData[$r] = $data;
						$graphData[$r++][0] = "{$evo->NM_DIST_OBSV}_{$e}";
					}
				}
			}
		?>
    </table>	 						 							
</div> <?php //frame?>
</body>
<script src="/js/jquery-1.9.1.js"></script>
<script src="/js/Chart.min.js"></script>

<script>
	var hour = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'];

<?php
    $color = array('rgba(205, 101, 255, 1)','rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 61, 64, 1)', 
                    'rgba(30, 34, 64, 1)', 'rgba(166, 161, 131, 1)', 'rgba(97, 127, 157, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 102, 255, 1)', 'rgba(51, 255, 255, 1)', 
                    'rgba(255, 102, 178, 1)', 'rgba(241, 130, 9, 1)', 'rgba(250, 190, 0, 1)', 'rgba(128, 206, 227, 1)', 'rgba(23, 144, 212, 1)', 'rgba(139, 198, 108, 1)', 
                    'rgba(37, 168, 60, 1)', 'rgba(205, 101, 255, 1)');	

	for($r = 0; $r < count($graphData); $r++)
	{
		$label = "{$graphData[$r][0]}";
		$data = "";
		$bColor = "'{$color[$r]}'";

		for ($i = 0; $i < 59; $i++)
		{
			$bColor = "{$bColor},'{$color[$r]}'";
		}

		// Chart.min.js 차트 표현식 중 데이터 입력 방식 [0,0,0,0,...,n]
		for( $i = 1; $i <= 31; $i++ )
		{
			if( $graphData[$r][$i] !== "" ) 
			{
				// 데이터가 있다면 데이터 입력
				if($i == 1) 
				{
					if( $dType == "snow" ) $data = ($graphData[$r][$i] / 10)."";
					elseif( $dType == "water" ) $data =  ($graphData[$r][$i] / 1000)."";
					else $data = ($graphData[$r][$i])."";
				}
				else 
				{
					if( $dType == "snow" ) $data = $data.",".($graphData[$r][$i] / 10);
					elseif( $dType == "water" ) $data =  $data.",".($graphData[$r][$i] / 1000);
					else $data = $data.",".($graphData[$r][$i]);
				}	
			}
			else 
			{
				// 라인 형태의 그래프이기때문에 중간 값이 비면 자연스러운 연결을 위해 앞과 뒤의 값을 이용해 연결 (처음 값과 마지막 값은 상관 없으므로 예외처리)
				if( $i == 1 ) {$data = "";}
				else if ( $i == 31 ) {$data = "{$data},";}
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
						for( $j = $i; $j >= 1; $j-- )
						{
							if( $graphData[$r][$j] !== "" )
							{
								$pre_idx = $j;
	
								if( $dType == "snow" ) $pre_data = $graphData[$r][$j] / 10;
								elseif( $dType == "water" ) $pre_data = $graphData[$r][$j] / 1000;
								else $pre_data = $graphData[$r][$j];
	
								break;
							}
						}
						
						// 뒷쪽에 유효한 값 찾기
						for( $j = $i; $j <= 31; $j++ )
						{
							if( $graphData[$r][$j] !== "" )
							{
								$post_idx = $j;
								
								if( $dType == "snow" ) $post_data = $graphData[$r][$j] / 10;
								elseif( $dType == "water" ) $post_data = $graphData[$r][$j] / 1000;
								else $post_data = $graphData[$r][$j];
	
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

		echo "let {$dType}{$r} = { label: '{$label}', backgroundColor: [{$bColor}], borderColor: ['{$color[$r]}'], fill: false, data : [{$data}]};";
		if( $r == 0 ) $dataset = "{$dType}{$r}";
		else $dataset = "{$dataset},{$dType}{$r}";
	}
?>
	var ctx = document.getElementById('id_myChart').getContext('2d');
	let chartList = {
		type: <?php if( $dType == "rain" ) { echo "'bar'"; } else { echo "'line'"; } ?>,
		data: {
			labels: hour,
			datasets: [
			<?=$dataset?>
		]},
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
						labelString : '<?=date("Y-m-d", strtotime($selectDate))?> 데이터 통계'	
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
	
	let type = '<?= $dType ?>';
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
<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	
	if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y", time());}
	if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m", time());}
	if(isset($_GET['day'])) {$day = $_GET['day'];} else {$day = date("d", time());}
	if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = 1;}
	if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}

	$selectDate = $year.$month.$day;
	$gbobsv = "";
	if( $dType == "water" ) $gbobsv = "','21";

	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

	$equip_dao = new WB_EQUIP_DAO;
	$equip_vo = new WB_EQUIP_VO;
	$equip_vo = $equip_dao->SELECT("GB_OBSV IN ('{$area_code}{$gbobsv}') AND USE_YN = '1'");

	$dao = new WB_DATA1HOUR_DAO($dType);
	$vo = new WB_DATA1HOUR_VO;
?>
<div class="cs_frame">
	<div class="cs_selectBox">
        <?php
        if( $dType == "water" ) echo "<div class='cs_unit'>(단위 : m)</div>";
        else if( $dType == "soil" ) echo "<div class='cs_unit'>(단위 : %)</div>";
        else if( $dType == "tilt" ) echo "<div class='cs_unit'>(단위 : °)</div>";
        else if( $dType == "snow" ) echo "<div class='cs_unit'>(단위 : cm)</div>";
        else echo "<div class='cs_unit'>(단위 : mm)</div>";
        ?>
        <div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
                <input type="hidden" name="addr" id="id_addr" value="Daygraph.php">
                <input type="hidden" name="dType" value="<?=$dType?>">

                <select name="year" id="id_select_graph">
                <?php 
                for($y = 2021; $y < date("Y", time())+1; $y++) 
                {
                    if($year == $y) {$selected = "selected";} else {$selected = "";}
                ?>
                    <option value="<?=$y?>"<?=$selected?>><?=$y?></option>
                <?php 
                } ?>
                </select> 년

                <select name="month" id="id_select_graph">
                <?php 
                for($m = 1; $m < 13; $m++) 
                {
                    if($m < 10) {$date = "0".$m;} else {$date = $m;}
                    if($month == $date) {$selected = "selected";} else {$selected = "";}
				?>
                    <option value="<?=$date?>"<?=$selected?>><?=$date?></option>
                <?php
                } ?>
                </select> 월

                <select name="day" id="id_select_graph">
                <?php 
                for($d = 1; $d < 32; $d++) 
                {
                    if($d < 10) {$date = "0".$d;} else {$date = $d;}
                    if($day == $date) {$selected = "selected";} else {$selected = "";}
				?>
                    <option value="<?=$date?>"<?=$selected?>><?=$date?></option>
                <?php 
                } ?>
                </select> 일

                <div class="cs_search" id="id_search_graph">검색</div>
            </form>
        </div>
    </div>

    <canvas id="id_myChart" width="400" height="130"></canvas>

	<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows" style="border:1px solid <?=$border_color?>;">
		<tr style="position:sticky;top:0px; background-color: <?=$border_color?>;">
			<?php
				if( $dType == "dplace" ) echo "<th colspan='2' width='150'>지역명</th>";
				else echo "<th width='100'>지역명</th>";

				for( $i = 0; $i < 24; $i++ )
				{
					echo "<th>{$i}</th>";
				}

				echo "</tr>";

				$graphData = array();
				$r = 1;
				foreach($equip_vo as $evo)
				{
					echo "<tr>";

					/* 지역명 & Sub_OBSV */
					if( $dType == "dplace" ) $row = $evo->SubOBCount;
					else $row = '1';

					echo "<td rowspan='{$row}' style='font-weight:bold; background-color:#f2f2f2; border-right:2px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
					
					/* 변위는 한 쿼리에 두줄 이상 사용하기에 따로 구현 */
					if( $dType != "dplace" && $dType != "flood" )
					{
						// 강우, 수위, 함수비, 적설, 경사
						$vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");
						$data = array_fill(0,25,"");
						if( $vo != null ) $data = $vo->MR_array();
						else $vo = new WB_DATA1HOUR_VO;

						for( $i = 1; $i <= 24; $i++)
						{
							echo "<td>";
							// DB는 mm 수집 & (Rain : mm , Water : M , Snow : Cm) 소수점 한자리까지만 표출 (Data)
							if( $data[$i] !== "" )
							{
								if( $dType == "snow" ) echo number_format($data[$i]/10, 1);
								else if( $dType == "water" ) echo number_format($data[$i]/1000, 1);
								else echo number_format($data[$i], 1);
							}
							else echo "-";
							echo "</td>";
						}

						$graphData[$r] = $data;
						$graphData[$r++][0] = $evo->NM_DIST_OBSV;

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
							$data = array_fill(0,25,"");
							if( $vo != null ) $data = $vo->MR_array();
							else $vo = new WB_DATA1HOUR_VO;

							for( $j = 1; $j <= 24; $j++)
							{
								echo "<td>";
								if( $data[$j] !== "" )
								{
									echo number_format($data[$j], 1);
								}
								else echo "-";
								echo "</td>";
							}

							if( $i != $evo->SubOBCount ) echo "</tr>";

							$graphData[$r] = $data;
							$graphData[$r++][0] = "{$evo->NM_DIST_OBSV}_{$i}";
						}
					}
					echo "</tr>";
				}
			?>
    </table>
</div> <?php //frame?>

<script src="/js/jquery-1.9.1.js"></script>
<script src="/js/Chart.min.js"></script>

<script>
	var hour = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];

	<?php
		$color = array('','rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 61, 64, 1)', 
						'rgba(30, 34, 64, 1)', 'rgba(166, 161, 131, 1)', 'rgba(97, 127, 157, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 102, 255, 1)', 'rgba(51, 255, 255, 1)', 
						'rgba(255, 102, 178, 1)', 'rgba(241, 130, 9, 1)', 'rgba(250, 190, 0, 1)', 'rgba(128, 206, 227, 1)', 'rgba(23, 144, 212, 1)', 'rgba(139, 198, 108, 1)', 
						'rgba(37, 168, 60, 1)', 'rgba(205, 101, 255, 1)');	

		for($r = 1; $r <= count($graphData); $r++)
		{
			$label = "{$graphData[$r][0]}";
			$data = "";
			$bColor = "'{$color[$r]}'";

			for ($i = 0; $i < 24; $i++)
			{
				$bColor = "{$bColor},'{$color[$r]}'";
			}

			// Chart.min.js 차트 표현식 중 데이터 입력 방식 [0,0,0,0,...,n]
			for( $i = 1; $i <= 24; $i++ )
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
					else if ( $i == 24 ) {$data = "{$data},";}
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
							for( $j = $i; $j <= 24; $j++ )
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
			if( $r == 1 ) $dataset = "{$dType}{$r}";
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
</html>

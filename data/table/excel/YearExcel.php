<?php
	if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y",time());}
	if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}

	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=".$dType."Year_".date("YmdHis", time()).".xls");
	header("Content-Description:PHP4 Generated Data");
	header('Content-Type: text/html; charset=euc-kr');

    $selectDate = $year;

    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

    $equipdao = new WB_EQUIP_DAO;
    $equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN='1'");

    $dao = new WB_DATA1HOUR_DAO($dType);
    $vo = new WB_DATA1HOUR_VO;

	echo "<table border='1'>";
    echo "<tr>";
    if($dType=="dplace") echo "<th colspan='2' width='200'>지역명</th>";
	else echo "<th width='150'>지역명</th>";

	for($i = 1; $i <= 12; $i++) echo "<th width='50'>".$i."</th>";
	
	echo "</tr>";   
	$r = 0;
	$graphData = array();
	foreach($equipvo as $evo)
	{
		// 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
		if( $dType != "dplace" )
		{
			$vo = $dao->SELECT_YEAR("left(RegDate,4) like '{$selectDate}' and CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
			$data = array_fill(0, 13, "");
			if( $vo ) foreach($vo as $v) $data[$v->idx] = (double)$v->Data;

			for($i = 1; $i <= 12; $i++)
			{
				switch($dType)
				{
					case 'rain' :
						$strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i],1)."<font>" : '-';
						break;
					
					case 'snow' :
						$strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i]/10,1)."</font>" : '-';    
						break;

					case 'water' :
						$strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i]/1000,1)."</font>" : '-';
						break;

					default :
						$strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i],1)."</font>" : '-';
				}
			}

			echo "<tr>";
			echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
			for($i = 1; $i <= 12; $i++) echo "<td>{$strArr[$i]}</td>";
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
				$vo = $dao->SELECT_YEAR("left(RegDate,4) like '{$selectDate}' and CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}'", $e);
				$data = array_fill(0, 13, "");
				if( $vo ) foreach($vo as $v) $data[$v->idx] = (double)$v->Data;

				for($i = 1; $i <= 12; $i++) $strArr[$i] = ($data[$i] != "") ? "<font color='#4900FF'>".number_format($data[$i],1)."<font>" : '-';

				if( $e != 1 ) echo "<tr>";
				echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$e}</td>";
				for($i = 1; $i <= 12; $i++) echo "<td>{$strArr[$i]}</td>";
				echo "</tr>";

				$graphData[$r] = $data;
				$graphData[$r++][0] = "{$evo->NM_DIST_OBSV}_{$e}";
			}
		}
	}
?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
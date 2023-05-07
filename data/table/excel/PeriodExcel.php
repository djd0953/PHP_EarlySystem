<?php
$area = $_GET['area'];
$dType = $_GET['dType'];
if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = "";}
if(isset($_GET['floodType'])) {$floodType = $_GET['floodType'];} else {$floodType = 'water';}

$year1 = $_GET['year1'];
$month1 = $_GET['month1'];
$day1 = $_GET['day1'];

$year2 = $_GET['year2'];
$month2 = $_GET['month2'];
$day2 = $_GET['day2'];

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=".$dType."_".date("YmdHis", time()).".xls");
header("Content-Description:PHP4 Generated Data");
header('Content-Type: text/html; charset=euc-kr');

$selectDate1 = $year1.$month1.$day1;
$selectDate2 = $year2.$month2.$day2;

include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

$equip_dao = new WB_EQUIP_DAO;
$equip_vo = new WB_EQUIP_VO;

$datadao = new WB_DATA1HOUR_DAO($dType);
$datavo = new WB_DATA1HOUR_VO;

?>
<table border="1">
	<tr>
	<th width="150">날짜</th>
		<?php
			for($i = 0; $i < 24; $i++)
			{
				echo "<th width='50'>{$i}</th>";
			}
	
			if($dType == "rain")
			{
				echo "<th width='50'>최고</th>";
				echo "<th width='50'>계</th>";
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
					echo "<td>";
					if( $dType == "flood" && $floodType == "flood" )
					{
						if( $v->{"MR{$i}"} == "" ) echo "-";
						else
						{
							if( $v->{"MR{$i}"}[0] == "0" ){ echo "X"; }
							elseif( $v->{"MR{$i}"}[0] == "1" ){ echo "O"; }
							
							if( $v->{"MR{$i}"}[1] == "0" ){ echo "X"; }
							elseif( $v->{"MR{$i}"}[1] == "1" ){ echo "O"; }
							
							if( $v->{"MR{$i}"}[2] == "0" ){ echo "X"; }
							elseif( $v->{"MR{$i}"}[2] == "1" ){ echo "O"; }
						}
					}
					else
					{
						if( $v->{"MR{$i}"} == "" ) echo "-";
						else
						{
							echo "<font color='#4900FF'>";
							if($dType == "snow") echo number_format($v->{"MR{$i}"}/10,1);
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
					echo "</td>";
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
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
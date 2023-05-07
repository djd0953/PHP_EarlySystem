<?php
    if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y",time());}
    if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m",time());}
    if(isset($_GET['dType'])) {$dType = $_GET['dType'];} else {$dType = "rain";}

    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=".$dType."Month_".date("YmdHis", time()).".xls");
    header("Content-Description:PHP4 Generated Data");
    header('Content-Type: text/html; charset=euc-kr');

    $selectDate = $year.$month;

    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

    $equipdao = new WB_EQUIP_DAO;
    $equipvo = new WB_EQUIP_VO;

    $dao = new WB_DATA1HOUR_DAO($dType);
    $vo = new WB_DATA1HOUR_VO;

    echo "<table border='1'>";

    if( $dType=="dplace" || $dType=="water" ) echo "<th colspan='2' width='200'>지역명</th>";
    else echo "<th width='150'>지역명</th>";

    for($i = 1; $i <= 31; $i++)
    {
        echo "<th width=50>{$i}</th>";
    }

    if($dType == "rain")
    {
        echo "<th width='50'>최고</th>";
        echo "<th width='60'>계</th>";
    }
    else if($dType == "snow")
    {
        echo "<th width='50'>최고</th>";
    }
    echo "</tr>";

    $equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
    foreach($equipvo as $evo)
    {
        // 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
        if( $dType != "dplace" )
        {
            /**
             * @param $data     : 데이터 담는 배열 (수위는 최대값)
             * @param $dataMin  : 수위용 최소 데이터 담는 배열
             * @param $max      : 최대치 계산
             * @param $min      : 최소치 계산
             * @param $sum      : 합계 계산
             */
            $vo = $dao->SELECT_MONTH("IFNULL(DATE_FORMAT(RegDate, '%Y%m'), LEFT(RegDate, 6)) = '{$selectDate}' AND CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
            $data = array_fill(0, 32, "");
            $dataMin = array_fill(0, 32, "");
            $max = 0;
            $min = 0;
            $sum = 0;
            foreach($vo as $v)
            {
                if( $dType == 'rain' ) 
                {
                    $data[$v->idx] = (double)$v->DaySum;
                    $sum += (double)$v->DaySum;
                    if( $max < $v->DaySum ) $max = $v->DaySum;
                }
                else if( $dType == "water" )
                {
                    $data[$v->idx] = (double)$v->DayMax;
                    $dataMin[$v->idx] = (double)$v->DayMin;
                }
                else 
                {
                    $data[$v->idx] = (double)$v->DayMax;

                    if( $sum == 0 ) 
                    {
                        $min = $v->DayMax;
                        $max = $v->DayMax;
                    }
                    else 
                    {
                        if( $max < $v->DayMax ) $max = $v->DayMax;
                        if( $min > $v->DayMin ) $min = $v->DayMin;
                    }
                    $sum += $v->DayMax;

                }
            }

            /**
             * DataBase는 mm로 값이 들어오는 기준
             * 강우 mm
             * 적설 Cm
             * 수위 M
             * Default mm
             * 소수점 1번째 자리까지! 
             */
            for($i = 1; $i <= 31; $i++)
            {
                switch($dType)
                {
                    case 'rain' :
                        $strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i],1)."<font>" : '-';
                        $strMax = ($max != 0)? "<font color='#4900FF'>".number_format($max,1)."<font>" : '-';
                        $strSum = ($sum != 0)? "<font color='#4900FF'>".number_format($sum,1)."<font>" : '-';
                        break;
                    
                    case 'snow' :
                        $strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i]/10,1)."</font>" : '-';
                        $strMax = ($max != 0)? "<font color='#4900FF'>".number_format($max/10,1)."<font>" : '-';  
                        break;

                    case 'water' :
                        $strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i]/1000,1)."</font>" : '-';
                        $strMin[$i] = ($dataMin[$i] != "")? "<font color='#4900FF'>".number_format($dataMin[$i]/1000,1)."</font>" : '-';
                        break;

                    default :
                        $strArr[$i] = ($data[$i] != "")? "<font color='#4900FF'>".number_format($data[$i],1)."</font>" : '-';
                        $strMax = ($max != 0)? "<font color='#4900FF'>".number_format($max,1)."<font>" : '-';
                }
            }

            // 수위는 최대/최소로 표출하기에 따로 표출
            if( $dType == "water" )
            {
                echo "<tr>";
                echo "<td rowspan='2' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
                echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>최대</td>";
                for($i = 1; $i <= 31; $i++) echo "<td>{$strArr[$i]}</td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>최소</td>";
                for($i = 1; $i <= 31; $i++) echo "<td>{$strMin[$i]}</td>";
                echo "</tr>";
            }
            else
            {
                echo "<tr>";

                echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
                for($i = 1; $i <= 31; $i++) echo "<td>{$strArr[$i]}</td>";

                echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                if($dType == 'rain') echo "<td style='color:#a30003; font-weight:bold'>{$strSum}</td>";

                echo "</tr>";
            }
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

                for($i = 1; $i <= 31; $i++) $strArr[$i] = ($data[$i] != "") ? "<font color='#4900FF'>".number_format($data[$i],1)."<font>" : '-';

                if( $e != 1 ) echo "<tr>";
                echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$e}</td>";
                for($i = 1; $i <= 31; $i++) echo "<td>{$strArr[$i]}</td>";
                echo "</tr>";
            }
        }
    }
?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
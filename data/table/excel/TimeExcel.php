<?php
	$area = $_GET['area'];
	$dType = $_GET['dType'];
    if(isset($_GET['equip'])) {$equip = $_GET['equip'];} else {$equip = "";}
    if(isset($_GET['floodType'])) {$floodType = $_GET['floodType'];} else {$floodType = 'water';}
	
	$year = $_GET['year'];
	$month = $_GET['month'];
	$day = $_GET['day'];

    $tableName = $dType;
    $selectDate = $year.$month.$day;
    $nextDate = $selectDate + 1;

	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:attachment;filename=".$dType."Time_".date("YmdHis", time()).".xls");
	header("Content-Description:PHP4 Generated Data");
	header('Content-Type: text/html; charset=euc-kr');
	
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    include_once $_SERVER["DOCUMENT_ROOT"]."/data/server/dataInfo.php";

    $equip_dao = new WB_EQUIP_DAO;
    $equip_vo = new WB_EQUIP_VO;
    $data_vo = new WB_DATA1MIN_VO;
?>
<table border="1">
<?php
    echo "<tr>";
        echo "<th style='background:{$border_color};' width='50'>시간</th>";
        for($i = 0; $i < 24; $i++)
        {
            echo "<th style='background:{$border_color};' width='50'>{$i}</th>";
        }
    echo "</tr>";

    // 침수 시간별 데이터 표출 시 침수위,상태 선택에 따라 표출해야 할 테이블이 다름
    if( $dType == 'flood' )
    {
        if ( $floodType == 'water') $tableName = 'water';
        else if ( $floodType == 'flood') $tableName = 'flood';
    }

    $data_dao = new WB_DATA1MIN_DAO($tableName);
    $data_vo = $data_dao->SELECT("RegDate BETWEEN '{$selectDate}' AND '{$nextDate}' AND CD_DIST_OBSV = '{$area}'", $equip);

    if( $floodType == 'flood' ) $array = array_fill(0,24,array_fill(0,60,"222"));
    else $array = array_fill(0,24,array_fill(0,60,-10000));

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
                    if( $array[$j][$i] == "" || $array[$j][$i] == '222' ) echo "-";
                    else
                    {
                        if( $array[$j][$i][0] == "0" ){ echo "X"; }
                        else if( $array[$j][$i][0] == "1" ){ echo "O"; }

                        if( $array[$j][$i][1] == "0" ){ echo "X"; }
                        else if( $array[$j][$i][1] == "1" ){ echo "O"; }

                        if( $array[$j][$i][2] == "0" ){ echo "X"; }
                        else if( $array[$j][$i][2] == "1" ){ echo "O"; }
                    }
                }
                else
                {
                    //기본 "-10000"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                    if($array[$j][$i] == -10000 || !is_numeric($array[$j][$i])) echo "-";
                    else 
                    {
                        //Water는 M표출, 그 외 Rain, dplace는 mm표출
                        if($tableName == "water") echo "<font color='#4900FF'>".number_format($array[$j][$i]/1000,1)."</font>";
                        else echo "<font color='#4900FF'>".number_format($array[$j][$i],1)."</font>";
                    }
                }
                echo "</td>";
            }
            echo "</tr>";
    } //for
?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
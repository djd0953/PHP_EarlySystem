<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";

	// 2021.10.26 CarNumber Search 추가
	if(isset($_GET['dType'])){$qType = $_GET['dType'];} else {$qType = "before";}
	if(isset($_GET['page'])){$page = $_GET['page'];} else {$page = 1;}
	if(isset($_GET['reqnum'])) {$reqnum = $_GET['reqnum'];} else {$reqnum = '';}

	if(isset($_GET['parkcode'])) {$parkcode = $_GET['parkcode'];} else {$parkcode = '';}
	if(isset($_GET['type'])) {$type = $_GET['type'];} else {$type = '0';}
	
	if(isset($_GET['year1'])) {$year1 = $_GET['year1'];} else {$year1 = date("Y",strtotime("-0days"));}
	if(isset($_GET['month1'])) {$month1 = $_GET['month1'];} else {$month1 = date("m",strtotime("-0days"));}
	if(isset($_GET['day1'])) {$day1 = $_GET['day1'];} else {$day1 = date("d",strtotime("-0days"));}
	
	if(isset($_GET['year2'])) {$year2 = $_GET['year2'];} else {$year2 = date("Y",time());}
	if(isset($_GET['month2'])) {$month2 = $_GET['month2'];} else {$month2 = date("m",time());}
	if(isset($_GET['day2'])) {$day2 = $_GET['day2'];} else {$day2 = date("d",time());}
	
	$strNowDate = date("Y-m-d", strtotime("-3 day"));
	$selectDate1 = $year1."-".$month1."-".$day1;
	$selectDate2 = $year2."-".$month2."-".$day2;

	$url = "frame/parkingCar.php?page=";
	$reqnumWhere = "";
	$parkcodeWhere = "";
	$typeWhere = " mid(GateSerial,2,1) = '".$type."' and";
	$table = "wb_parkcarhist";
	if($reqnum != '') $reqnumWhere = " and CarNum like '%".$reqnum."%'";
	if($parkcode != '') $parkcodeWhere = " left(GateSerial,1) = '".$parkcode."' and";
	if($type == '2') 
	{
		$table = "wb_parkcarnow";
		$typeWhere = "";
	}
	else if($type == '3') $typeWhere = " (mid(GateSerial,2,1) = '0' or mid(GateSerial,2,1) = '1') and";

	if($qType == "before") $sql = "select a.idx,a.CarNum,a.GateDate,a.GateSerial, mid(a.GateSerial,2,1) as type, 
									b.ParkGroupName from wb_parkcarhist as a left join wb_parkgategroup as b on left(a.GateSerial,1) = b.ParkGroupCode";
	else $sql = "select a.idx,a.CarNum,a.GateDate,a.GateSerial, mid(a.GateSerial,2,1) as type, b.ParkGroupName 
				from ".$table." as a left join wb_parkgategroup as b on left(a.GateSerial,1) = b.ParkGroupCode 
				where ".$typeWhere.$parkcodeWhere." (GateDate between '".$selectDate1." 00:00:00' and '".$selectDate2." 23:59:59')".$reqnumWhere;

	$res = mysqli_query( $conn, $sql );
	$countRec = mysqli_num_rows( $res );
	
	$list = 25;
	$block = 20;
	
	$pageNum = ceil($countRec/$list); // 총 페이지
	$blockNum = ceil($pageNum/$block); // 총 블록
	$nowBlock = ceil($page/$block);
	
	$s_page = ($nowBlock * $block) - ($block - 1);
	
	if ($s_page <= 1) 
	{
		$s_page = 1;
	}
	$e_page = $nowBlock*$block;
	if ($pageNum <= $e_page) 
	{
		$e_page = $pageNum;
	}
?>
<div class="cs_frame"> <!-- 차량 입출차 내역 -->
    <div class="cs_selectBox">
    	<div class="cs_date">
			<form id="id_form" name="form" method="get" action="">
				<input type="hidden" name="arr" value="parkingCar.php">
				<?php 
					$parkingSql = "select * from wb_parkgategroup where 1";
					$parkingRes = mysqli_query($conn, $parkingSql);
				?>
				<select name="parkcode">
					<option value='' <?php if($parkcode == ''){echo "selected";} ?>>전체</option>  
					<?php while($parkingRow = mysqli_fetch_assoc($parkingRes)) { ?>                	              
					<option value="<?=$parkingRow['ParkGroupCode']?>" <?php if($parkcode == $parkingRow['ParkGroupCode']) {echo "selected";}?>><?=$parkingRow['ParkGroupName']?></option>
					<?php } ?>                
				</select>

				<select name="type">            	
					<option value="0" <?php if($type == '0') {echo "selected";}?>>입차</option>
					<option value="1" <?php if($type == '1') {echo "selected";}?>>출차</option>
					<option value="3" <?php if($type == '3') {echo "selected";}?>>입/출차</option>   
					<option value="2" <?php if($type == '2') {echo "selected";}?>>현재 주차</option>            
				</select>

				<select name="year1">
					<?php for($y = 2020; $y < date("Y", time())+1; $y++) { 
							if($year1 == $y) {$selected = "selected";} else {$selected = "";}
					?>
						<option value="<?=$y?>"<?=$selected?>><?=$y?></option>
					<?php } ?>
					</select> 년
					
					<select name="month1">
					<?php for($m = 1; $m < 13; $m++) { 
							if($m < 10) {$date = "0".$m;} else {$date = $m;}
							if($month1 == $date) {$selected = "selected";} else {$selected = "";}
					?>
						<option value="<?=$date?>"<?=$selected?>><?=$date?></option>
					<?php } ?>
					</select> 월
					
					<select name="day1">
					<?php for($d = 1; $d < 32; $d++) {
							if($d < 10) {$date = "0".$d;} else {$date = $d;}
							if($day1 == $date) {$selected = "selected";} else {$selected = "";}
					?>
						<option value="<?=$date?>"<?=$selected?>><?=$date?></option>
					<?php } ?>
					</select> 일
					~
					<select name="year2">
					<?php for($y = 2020; $y < date("Y", time())+1; $y++) { 
							if($year2 == $y) {$selected = "selected";} else {$selected = "";}
					?>
						<option value="<?=$y?>"<?=$selected?>><?=$y?></option>
					<?php } ?>
					</select> 년
					
					<select name="month2">
					<?php for($m = 1; $m < 13; $m++) { 
							if($m < 10) {$date = "0".$m;} else {$date = $m;}
							if($month2 == $date) {$selected = "selected";} else {$selected = "";}
					?>
						<option value="<?=$date?>"<?=$selected?>><?=$date?></option>
					<?php } ?>
					</select> 월
					
					<select name="day2">
					<?php for($d = 1; $d < 32; $d++) {
							if($d < 10) {$date = "0".$d;} else {$date = $d;}
							if($day2 == $date) {$selected = "selected";} else {$selected = "";}
					?>
						<option value="<?=$date?>"<?=$selected?>><?=$date?></option>
					<?php } ?>
				</select> 일
				&nbsp;
				<input type="text" name="reqnum" maxlength="15" size="15" placeholder="차량번호 검색" value=<?=$reqnum?>>
				&nbsp;
				<input type="hidden" name="mode" value="result">
				<div class="cs_search" id="id_search">검색</div>
			</form>
        </div>
    </div>
    
	<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
		<tr>
			<th width="3%"><input type="checkbox" id="id_allcheck" name="allcheck"></th>
			<th width="3%">no</th>
			<th>게이트번호</th>
			<th>구분</th>
			<th>차량번호</th>                
			<th>시간</th>
		</tr>            
		<?php
			$count = ($page-1) * $list;
			$listCnt = $countRec - $count;
			
			$sql = $sql." order by GateDate desc limit ".$count.",".$list;
			$res = mysqli_query($conn, $sql);
			while( $row = mysqli_fetch_assoc($res) ) 
			{ ?>
				<tr>
					<td style="text-align: center;"><input type="checkbox" class="cs_gateChk" value="<?=$row["idx"] ?>"></td>
					<td><?=$listCnt-- ?></td>
					<td><?=$row['ParkGroupName']."(".$row['GateSerial'].")"?></td>
					<td>
					<?php 
						if($row['type'] == '0') { 
							echo "입차";
						} else if($row['type'] == '1') {
							echo "출차";	
						} else if($row['type'] == '2') {
							echo "현재 주차";	
						}
					?>
					</td>
					<td class="cs_imgLink" data-url="frame/imgView.php?carnum=<?=$row['CarNum']?>&caridx=<?=$row['idx']?>" style="cursor:pointer;"><?=$row['CarNum']?></td>
					<td><?=$row['GateDate']?></td>
				</tr>
			<?php } ?>     
	</table>

	<!-- Pageing block begin -->
	<div class="cs_page">
		<?php if( $page != 1 )
		{
			echo "<div class='cs_pages' id='id_page' data-url='".$url.($page-1)."' data-idx='1'>이전</div>";
		} 
		for ($p=$s_page; $p<=$e_page; $p++) 
		{
			$act = "";
			if($p == $page) $act = "active";
			echo "<div class='cs_pages ".$act."' id='id_page' data-url='".$url.$p."' data-idx='1'>".$p."</div>";
		}
		if( $page != $pageNum )
		{
			echo "<div class='cs_pages' id='id_page' data-url='".$url.($page+1)."' data-idx='1'>다음</div>";
		}?>
	</div>
	<!-- Pageing block end-->

	<div class="cs_btnBox" style="justify-content: flex-end;">
		<div class="cs_btn" id="id_msgbtn" data="<?=$type?>">안내문자 발송</div>
		<div class="cs_btn" id="id_delbtn" data="<?=$type?>">입출차내역 삭제</div>
	</div>
</div>   
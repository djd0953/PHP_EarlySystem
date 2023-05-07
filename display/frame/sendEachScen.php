<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/display/server/displayStatus.php";

$num = $_GET["num"];
if(!isset($_GET['page'])){$page = $_GET['page'];} else {$page = 1;}

$allRecSql = "select * from wb_display where CD_DIST_OBSV='".$num."' and DisType = 'ad'";
$allRecRes = mysqli_query( $conn, $allRecSql );
$countRec = mysqli_num_rows( $allRecRes );

$url = $_SERVER['PHP_SELF']."?num=".$num."&page=";

$list = 5;
$block = 10;

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

$sql = "select * from wb_equip where CD_DIST_OBSV = '".$num."'";
$res = mysqli_query( $conn, $sql );
$row = mysqli_fetch_array( $res );
?>

<div class="cs_frame" >
	<div>◈ 전광판 기본정보</div>
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
        <tr> 
            <th width="16%">장비명</th>
            <td width="16%" style="text-align:left; padding-left:10px;"><?=$row["NM_DIST_OBSV"] ?></td>
            <th width="16%">장비사이즈</th>
            <td width="16%" style="text-align:left; padding-left:10px;"><?=$row["SizeX"]."×".$row["SizeY"] ?></td>
            <th width="16%">IP(Port)</th>
            <td width="16%" style="text-align:left; padding-left:10px;"><?=$row["ConnIP"]." (".$row["ConnPort"].")" ?></td>
        </tr>
        
        <tr> 
            <th>설치주소</th>
            <td colspan="5" style="text-align:left; padding-left:10px;"><?=$row["DTL_ADRES"] ?></td>
        </tr>
        </table>

        <div style="margin-top:20px;">◈ 표출중 시나리오 리스트</div>
        <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
        <tr> 
            <th width="6%">No</th>
            <th width="34%">표시일자</th>
            <th width="40%">내용</th>
            <th width="20%">시나리오 종료</th>
        </tr>
        <?php
			$nowDate = strtotime(date("Y-m-d H:i:s"));
			$dispSql = "select * from wb_display 
						where CD_DIST_OBSV='".$num."' and Exp_YN = 'Y' and DisType = 'ad'  
						and EndTime >= '".date("Y-m-d H:i:s", time())."'
						order by DisCode asc";
			$dispRes = mysqli_query( $conn, $dispSql );
			$dispCount = mysqli_num_rows( $dispRes );
			
			$count = 0;
			while( $dispRow = mysqli_fetch_array( $dispRes ) ){
			  $strDate = strtotime($dispRow["StrTime"]);
			  
		?>
        <tr> 
            <td style="text-align: center;"><?=++$count ?></td>
            <td style="text-align: center;">
            	<?php  if( $strDate > $nowDate){ echo '<span style="color:red">[표시대기]</span>'; } ?>
				<?=date("Y-m-d H", strtotime($dispRow["StrTime"]))." ~ ".date("Y-m-d H", strtotime($dispRow["EndTime"])) ?>
            </td>
            <td>
				<img src="../../<?=$dispRow["ViewImg"] ?>" width="300" style="cursor: pointer;" id="id_updEachScen" value="<?=$dispRow['DisCode'] ?>" data-num="<?=$num?>" data-page="<?=$page?>" data-type="<?=$dispRow["SaveType"] ?>">
            </td>
            <td style="text-align: center;">
            	<div class="cs_btn" id="id_endEachScen" data-num="<?=$num?>" data-page="<?=$page?>" value="<?=$dispRow["DisCode"] ?>" style="float:none; margin:auto;">시나리오 종료</div>
            </td>
        </tr>
        <?php } ?>
	</table>
        
	<div style="margin-top:20px;">◈ 등록된 리스트</div>
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
	<tr>
		<th width="3%"><label><input type="checkbox" name="allCheck" id="id_allCheck"></label></th>
		<th width="3%">No</th>
		<th width="34%">표시일자</th>
		<th width="40%">내용</th>
		<th width="20%">표시상태</th>
	</tr>
	<?php
		$s_point = ($page-1) * $list;
		$dispSql = "select * from wb_display where CD_DIST_OBSV='".$num."' and DisType = 'ad' order by DisCode asc limit " . $s_point.",".$list;
		$dispRes = mysqli_query( $conn, $dispSql );
		
		$count = ($page-1) * $list;
		while( $dispRow = mysqli_fetch_array( $dispRes ) )
		{
			$strDate = strtotime($dispRow["StrTime"]);
			$endDate = strtotime($dispRow["EndTime"]);
	?>
	<tr> 
		<td style="text-align: center;"><label><input type="checkbox" name="disChk" class="cs_disChk" value="<?=$dispRow["DisCode"] ?>"></label></td>
		<td style="text-align: center;"><?=++$count ?></td>
		<td style="text-align: center;">
			<?php  if( $strDate > $nowDate){ echo '<span style="color:red">[표시대기]</span>'; } ?>
			<?=date("Y-m-d H", strtotime($dispRow["StrTime"]))." ~ ".date("Y-m-d H", strtotime($dispRow["EndTime"])) ?>
		</td>
		<td>
			<img src="../../<?=$dispRow["ViewImg"] ?>" width="300" style="cursor: pointer;" id="id_updEachScen" value="<?=$dispRow["DisCode"] ?>" data-num="<?=$num?>" data-page="<?=$page?>" data-type="<?=$dispRow["SaveType"] ?>">
		</td>
		<td style="text-align: center;">
			<?php 
				
				if( ($dispRow["Exp_YN"] == "Y") && ($strDate <= $nowDate) && ($endDate >= $nowDate) ){
					echo '<span style="color:blue">표시중</span>';
				}
				else if( $dispRow["Exp_YN"] == "N"){
					echo '<span style="color:gray">수동 종료</span>';
				}
				else if( $strDate > $nowDate){
					echo '<span style="color:red">표시 대기</span>';
				}
				else if( $endDate < $nowDate){
					echo '<span style="color:gray">표시 종료</span>';
				}
				
			?>
		</td>
	</tr>
	<?php  } ?>
	</table>
	
	<div class="cs_page">
		<?php if( $page != 1 )
		{
			echo "<div class='cs_pages' id='id_page' date-url='".$url.($page-1)."'>이전</div>";
		} 
		for ($p=$s_page; $p<=$e_page; $p++) 
		{
			$act = "";
			if($p == $page) $act = "active";
			echo "<div class='cs_pages ".$act."' id='id_page' date-url='".$url.$p."'>".$p."</div>";
		}
		if( $page != $pageNum )
		{
			echo "<div class='cs_pages' id='id_page data-url='".$url.($page+1)."'>다음</div>";
		}?>
	</div>
	
	<?php if( $dispCount < 255 ){ ?>
		<div class='cs_btnBox'>
			<div class="cs_btn" data-num="<?=$num?>" data-page="<?=$page?>" id="id_addEachScen">시나리오 추가</div>
			<div class="cs_btn" data-num="<?=$num?>" data-page="<?=$page?>" id="id_delEachScen">시나리오 삭제</div>
		</div>
	<?php } ?>

	<div id="id_helpForm">
		<div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
			◈ 전광판 기본정보<br/>
			&nbsp;- 장비명, 사이즈, IP, 주소 등 설치된 전광판의 기본 정보입니다.<br/><br/>
			
			◈ 표출중 시나리오 리스트<br/>
			&nbsp;- 전광판에 현재 표출중인 내용(시나리오)입니다.<br/><br/>
			
			◈ 등록된 리스트 <br/>
			&nbsp;- 이전에 전송했었던 시나리오 목록입니다.<br/><br/>
			
			[시나리오 추가/삭제]<br/>
			&nbsp;- 하단의 [시나리오 추가] 또는 [시나리오 삭제]를 클릭합니다.<br/><br/>
			
			[시나리오 수정]<br/>
			&nbsp;- ‘◈표출중 시나리오 리스트’ 또는 ‘◈등록된 리스트’의 내용(검은 화면)부분을 클릭합니다.
		</div>
	</div>

	<div class="blank" style="padding-bottom: 50px;"></div>
</div>

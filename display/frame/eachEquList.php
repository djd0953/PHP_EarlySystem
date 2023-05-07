<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/display/server/displayStatus.php";
?>
<div class="cs_frame">
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:20px;">
		<tr align="center"> 
			<th width="3%">no</th>
			<th width="15%" style="padding-left:15px;">장비명</th>
			<th width="15%">사이즈</th>
			<th>설치지역</th>
			<th width="15%">전원상태</th>
			<th width="15%">표출상태</th>
		</tr>
		<?php
		$sql = "select a.NM_DIST_OBSV, a.SizeX, a.SizeY, a.DTL_ADRES, a.LastStatus, b.*
				from wb_equip as a left join wb_disstatus as b
				on a.CD_DIST_OBSV = b.CD_DIST_OBSV
				where GB_OBSV = '18' and USE_YN = '1' GROUP BY a.CD_DIST_OBSV
				order by a.CD_DIST_OBSV asc ";
		$res = mysqli_query( $conn, $sql );
		$count = 0;

		while( $row = mysqli_fetch_assoc( $res ) )
		{
			?>
		<tr align="center" id="id_disList" data-num="<?=$row["CD_DIST_OBSV"] ?>" style="cursor:pointer;">
			<td><?=++$count ?></td>
			<td style="text-align: left; padding-left:10px;"><?=$row["NM_DIST_OBSV"] ?></td>
			<td><?=$row["SizeX"]."×".$row["SizeY"] ?></td>
			<td><?=$row["DTL_ADRES"] ?></td>
			<td>
			<?php
				if(strtolower($row['LastStatus']) == "ok") echo "<span style='color:blue'>정상</span>";
				else echo "<span style='color:red'>점검요망</span>";
			?>
			</td>
			<td><?php if( $row['ExpStatus'] == "ad" ){ echo "일반"; }else{ echo "<span style='color:red'>긴급</span>"; }?></td>
		</tr>
		<?php } // end whild ?>
	</table>
</div>
<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";
?>
		
<div class="cs_frame">
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:10px;">
		<tr align="center" style="background-color:#f9d9ca;"> 
			<th width="3%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
			<th width="5%">장비번호(CD)</th>
			<th>장비이름(NM)</th>
			<th>주소</th>
			<th width="5%">사용유무</th>
			<th width="10%">위도</th>
			<th width="10%">경도</th>
			<th width="10%">LastDate</th>
			<th width="5%">LastStatus</th>
		</tr>
		<?php 
		$sql = "SELECT * FROM wb_equip ORDER BY GB_OBSV, CD_DIST_OBSV";
		$res = mysqli_query( $conn, $sql );
		while( $row = mysqli_fetch_assoc( $res ) )
		{
			switch($row['GB_OBSV'])
			{
				case "01" :
				{
					$type = "[강우]";
					break;
				}
				case "02" :
				{
					$type = "[수위]";
					break;
				}
				case "03" :
				{
					$type = "[변위]";
					break;
				}
				case "04" :
				{
					$type = "[함수비]";
					break;
				}
				case "06" :
				{
					$type = "[적설]";
					break;
				}
				case "08" :
				{
					$type = "[경사]";
					break;
				}
				case "17" :
				{
					$type = "[방송]";
					break;
				}
				case "18" :
				{
					$type = "[전광판]";
					break;
				}
				case "19" :
				{
					$type = "[CCTV]";
					break;
				}
				case "20" :
				{
					$type = "[차단]";
					break;
				}
				case "21" :
				{
					$type = "[침수]";
					break;
				}
				default :
					$type = "[N/A]";
			}
			echo "<tr align='center'>";
			echo "<td style='text-align:center;'><input type='checkbox' name='eChk' class='cs_eChk' value='{$row['CD_DIST_OBSV']}'</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['CD_DIST_OBSV']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$type}{$row['NM_DIST_OBSV']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['DTL_ADRES']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['USE_YN']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['LAT']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['LON']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['LastDate']}</td>";
			echo "<td class='cs_equiList' value='{$row['CD_DIST_OBSV']}'>{$row['LastStatus']}</td>";
			echo "</tr>";
		}
		?>
	</table>

	<div style="float:right;">
		<div class="cs_btn" id="id_updbtn">동시 수정</div>
		<div class="cs_btn" id="id_addbtn">장비 추가</div>
		<div class="cs_btn" id="id_delbtn">장비 삭제</div>
	</div>

</div>
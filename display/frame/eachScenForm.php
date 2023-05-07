<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
?>
<div class="cs_loading">
	<div class="cs_message">데이터 전송중입니다.<br>잠시만 기다려주세요.</div>
</div>

<div class="cs_frame">
	<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/display/server/displayStatus.php";
	
	if( isset($_GET["dType"]) ) { $type = $_GET["dType"]; } else { $type = "normal"; }
	
	if($type != "group")
	{
		$areaCode = $_GET["areaCode"];
		$page = $_GET["page"];
		$num = $_GET["num"];

		$sql = "select * from wb_equip where CD_DIST_OBSV = '".$areaCode."'";
		$res = mysqli_query( $conn, $sql );
		$row = mysqli_fetch_array( $res );

		$saveRes = mysqli_query( $conn, "select * from wb_display where CD_DIST_OBSV='".$areaCode."' and Exp_YN = 'Y' and DisType = 'ad' and EndTime >= '".date("Y-m-d H:i:s", time())."'" );
		$saveCount = mysqli_num_rows( $saveRes );
	}
	else
	{
		$row['SizeX'] = 320;
		$row['SizeY'] = 64;
	}

	if( $type == "insert" || $type == "group" )
	{
		$disEffect = "1";
		$disSpeed = "3";
		$endEffect = "1";
		$endSpeed = "3";
		$relay = "0";
		$disTime = "5";
		$startDate = date("Y-m-d", time());
		$startTime = "00";
		$endDate = date("Y-12-31", strtotime("+1 years"));
		$endTime = "00";
		$summernote = "";
	}
	else if( $type == "update" )
	{
		$subSql = "select * from wb_display where DisCode = '".$num."'";
		$subRes = mysqli_query( $conn, $subSql );
		$subRow = mysqli_fetch_array( $subRes );
		
		$disEffect = $subRow["DisEffect"];
		$disSpeed = $subRow["DisSpeed"];
		$endEffect = $subRow["EndEffect"];
		$endSpeed = $subRow["EndSpeed"];
		$relay = $subRow["Relay"];
		$disTime = $subRow["DisTime"];
		$startDate = date("Y-m-d", strtotime($subRow["StrTime"]));
		$startTime = date("H", strtotime($subRow["StrTime"]));

		if( date("Y-m-d H:i:s") > $subRow["EndTime"] )
		{
			$endDate = date("Y-12-31", strtotime("+1 years"));
			$endTime = "00";
		}
		else
		{
			$endDate = date("Y-m-d", strtotime($subRow["EndTime"]));
			$endTime = date("H", strtotime($subRow["EndTime"]));
		}

		$summernote = $subRow["HtmlData"];
		
		$end = strtotime($subRow["EndTime"]);
		$now = strtotime(date("Y-m-d H:i:s"));
	}
	?>
<style>
	.cs_datatable td
	{
		text-align: left; 
		padding-left:10px;
	}
</style>
	<?php if($type != "group")
	{
		echo "<div id='id_eachscenario'>";
			echo "<div>◈ 전광판 기본정보</div>";
			echo "<table border='0' cellpadding='0' cellspacing='0' class='cs_datatable' rules='all'>";
			echo "<tr> ";
				echo "<th width='16%'>장비명</th>";
				echo "<td width='16%'>".$row['NM_DIST_OBSV']."</td>";
				echo "<th width='16%'>장비사이즈</th>";
				echo "<td width='16%'>".$row['SizeX'].'×'.$row['SizeY']."</td>";
				echo "<th width='16%'>IP(Port)</th>";
				echo "<td width='16%'>".$row['ConnIP'].' ('.$row['ConnPort'].')'."</td>";
			echo "</tr>";
			echo "<tr> ";
				echo "<th>설치주소</th>";
				echo "<td colspan='5'>".$row['DTL_ADRES']."</td>";
			echo "</tr>";
			echo "</table>";
		echo "</div>";
	}
	else
	{
		echo "<div id='id_groupscenario'>";
			echo "<div style='margin-top:20px;'>◈ 전광판 선택</div>";
			echo "<table border='0' cellpadding='0' cellspacing='0' class='cs_datatable' rules='rows'>";
				echo "<tr align='center'>";
					echo "<th width='3%'><input type='checkbox' name='allCheck' id='id_allCheck'></th>";
					echo "<th width='15%'>장비명</th>";
					echo "<th width='15%'>사이즈</th>";
					echo "<th>설치지역</th>";
					echo "<th width='15%'>전원상태</th>";
					echo "<th width='15%'>표출상태</th>";
				echo "</tr>";
		$eSql = "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, a.SizeX, a.SizeY, a.DTL_ADRES, a.LastStatus, 
				b.Power, b.ExpStatus from wb_equip a left join wb_disstatus b on a.CD_DIST_OBSV=b.CD_DIST_OBSV 
				where a.GB_OBSV = '18' and a.USE_YN = '1'";
		$eRes = mysqli_query($conn, $eSql);
		while($eRow = mysqli_fetch_array($eRes))
		{
			
			$power = explode("/", $eRow["Power"]);
			if($eRow['ExpStatus'] == "ad") $state = '일반';
			else $state = "<span style='color:red'>긴급</span>";
				echo "<tr>";
					echo "<td style='text-align:center; padding-left: 0px;'><input type='checkbox' class='cs_disChk' value='".$eRow['CD_DIST_OBSV']."'></td>";
					echo "<td style='text-align:left; padding-left: 10px;'>".$eRow['NM_DIST_OBSV']."</td>";
					echo "<td style='text-align:center; padding-left: 0px;'>".$eRow['SizeX']."x".$eRow['SizeY']."</td>";
					echo "<td style='text-align:center; padding-left: 0px;'>".$eRow['DTL_ADRES']."</td>";
					echo "<td style='text-align:center; padding-left: 0px;'>";
					if(strtolower($eRow['LastStatus']) == "ok") echo "<span style='color:blue'>정상</span>";
					else echo "<span style='color:red'>점검요망</span>";
					echo "</td>";
					echo "<td style='text-align:center;'>".$state."</td>";
				echo "</tr>";
		}
			echo "</table>";
		echo "</div>";
	} ?>

	<form action="" method="post" name="eachScen" id="id_form">
	<input type="hidden" name="mode" value="<?=$type ?>">
	<input type="hidden" name="num" value="<?=$num ?>">
	<input type="hidden" name="areaCode" id="id_areaCode" value="<?=$areaCode ?>">     
	
	<input type="hidden" name="width" value="<?=$row["SizeX"] ?>">    
	<input type="hidden" name="height" value="<?=$row["SizeY"] ?>">    
		
	<div style="margin-top:20px;">◈ 시나리오 설정</div>
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
	<tr> 
		<th width="16%">표시효과</th>
		<td width="34%">
			<select name="disEffect" id="disEffect">
				<option value="1" <?php if( $disEffect == "1" ){ echo "selected"; } ?>>즉시 표시</option>
				<option value="2" <?php if( $disEffect == "2" ){ echo "selected"; } ?>>좌측으로 스크롤</option>
				<option value="3" <?php if( $disEffect == "3" ){ echo "selected"; } ?>>위로 스크롤</option>
				<option value="4" <?php if( $disEffect == "4" ){ echo "selected"; } ?>>아래로 스크롤</option>
				<option value="5" <?php if( $disEffect == "5" ){ echo "selected"; } ?>>레이저 효과</option>
				<option value="6" <?php if( $disEffect == "6" ){ echo "selected"; } ?>>중심에서 상하로 벌어짐</option>
				<option value="7" <?php if( $disEffect == "7" ){ echo "selected"; } ?>>상하에서 중심으로 모여듬</option>
				<option value="8" <?php if( $disEffect == "8" ){ echo "selected"; } ?>>1단으로 좌측스크롤</option>
			</select>
		</td>
		<th width="16%">표시속도</th>
		<td width="34%">
			<select name="disSpeed" id="disSpeed" style="width:45%;">
				<option value="1" <?php if( $disSpeed == "1" ){ echo "selected"; } ?>>1</option>
				<option value="2" <?php if( $disSpeed == "2" ){ echo "selected"; } ?>>2</option>
				<option value="3" <?php if( $disSpeed == "3" ){ echo "selected"; } ?>>3</option>
				<option value="4" <?php if( $disSpeed == "4" ){ echo "selected"; } ?>>4</option>
				<option value="5" <?php if( $disSpeed == "5" ){ echo "selected"; } ?>>5</option>
				<option value="6" <?php if( $disSpeed == "6" ){ echo "selected"; } ?>>6</option>
				<option value="7" <?php if( $disSpeed == "7" ){ echo "selected"; } ?>>7</option>
				<option value="8" <?php if( $disSpeed == "8" ){ echo "selected"; } ?>>8</option>
			</select>
			
			<div class="info" style="display: inline-block;">&nbsp;※ 1(빠름) ~ 8(느림)</div>
		</td>
	</tr>
	<tr> 
		<th>완료효과</th>
		<td>
			<select name="endEffect" id="endEffect">
				<option value="1" <?php if( $endEffect == "1" ){ echo "selected"; } ?>>위로 스크롤</option>
				<option value="2" <?php if( $endEffect == "2" ){ echo "selected"; } ?>>아래로 스크롤</option>
				<option value="3" <?php if( $endEffect == "3" ){ echo "selected"; } ?>>위아래로 벌어짐</option>
				<option value="4" <?php if( $endEffect == "4" ){ echo "selected"; } ?>>중심으로 모여듬</option>
				<option value="5" <?php if( $endEffect == "5" ){ echo "selected"; } ?>>즉시 사라짐</option>
				<option value="6" <?php if( $endEffect == "6" ){ echo "selected"; } ?>>화면반전</option>
				<option value="7" <?php if( $endEffect == "7" ){ echo "selected"; } ?>>좌측으로 사라짐</option>
			</select>
		</td>
		<th>완료속도</th>
		<td>
			<select name="endSpeed" id="endSpeed" style="width:45%;">
				<option value="1" <?php if( $endSpeed == "1" ){ echo "selected"; } ?>>1</option>
				<option value="2" <?php if( $endSpeed == "2" ){ echo "selected"; } ?>>2</option>
				<option value="3" <?php if( $endSpeed == "3" ){ echo "selected"; } ?>>3</option>
				<option value="4" <?php if( $endSpeed == "4" ){ echo "selected"; } ?>>4</option>
				<option value="5" <?php if( $endSpeed == "5" ){ echo "selected"; } ?>>5</option>
				<option value="6" <?php if( $endSpeed == "6" ){ echo "selected"; } ?>>6</option>
				<option value="7" <?php if( $endSpeed == "7" ){ echo "selected"; } ?>>7</option>
				<option value="8" <?php if( $endSpeed == "8" ){ echo "selected"; } ?>>8</option>
			</select>
			
			<div class="info" style="display: inline-block;">&nbsp;※ 1(빠름) ~ 8(느림)</div>
		</td>
	</tr>
	<tr> 
		<th>표시유지시간</th>
		<td>
			<select name="disTime" id="disTime" style="width:20%;">
				<?php for( $i=1 ; $i<=20; $i++ ){ ?>
				<option value="<?=$i ?>"  <?php if( $disTime == $i ){ echo "selected"; } ?>><?=$i ?></option>
				<?php } ?>
			</select> 초 
		</td>
		<th>릴레이 동작여부</th>
		<td>
		<div class="cs_container" style="font-size: 13px;">
				<div>1번</div>
				<input type="checkbox" class="cs_relay1" name="relay1" value="8" style="margin-right: 25px;zoom: 1.2;" <?php if($relay >= 8) { $relay -= 8; echo "checked"; } ?>>
				<div>2번</div>
				<input type="checkbox" class="cs_relay2" name="relay2" value="4" style="margin-right: 25px;zoom: 1.2;" <?php if($relay >= 4) { $relay -= 4; echo "checked"; } ?>>
				<div>3번</div>
				<input type="checkbox" class="cs_relay3" name="relay3" value="2" style="margin-right: 25px;zoom: 1.2;" <?php if($relay >= 2) { $relay -= 2; echo "checked"; } ?>>
				<div>4번</div>
				<input type="checkbox" class="cs_relay4" name="relay4" value="1" <?php if($relay == 1) { echo "checked"; } ?>>
			</div>
		</td>
	</tr>
	<tr> 
		<th>표시시간</th>
		<td>
			<input type="text" name="startDate" id="startDate" value="<?=$startDate ?>" style="width:100px;">
			
			<select name="startTime" id="startTime" style="width:10%; text-align:center; text-indent:0px;">
				<?php for( $i=0; $i<24; $i++ ){ 
							$printI = "";
						if( $i < 10 ){ $printI = "0".$i; }else{ $printI = $i; }
				?>
				<option value="<?=$printI ?>" <?php if( $startTime == $printI ){ echo "selected"; } ?>><?=$printI ?></option>
				<?php } ?>
			</select>
			시
		</td>
		<th>완료시간</th>
		<td>
			<input type="text" name="endDate" id="endDate" value="<?=$endDate ?>"  style="width:100px;">
			
			<select name="endTime" id="endTime" style="width:10%; text-align:center; text-indent:0px;">
				<?php for( $i=0; $i<24; $i++ ){ 
							$printI = "";
						if( $i < 10 ){ $printI = "0".$i; }else{ $printI = $i; }
				?>
				<option value="<?=$printI ?>" <?php if( $endTime == $printI ){ echo "selected"; } ?>><?=$printI ?></option>
				<?php } ?>
			</select> 시
		</td>
	</tr>
	</table>
	
	<div style="margin-top:20px; margin-bottom:10px;">◈ 시나리오 내용</div>
	
	<div style="font-size: 12px; margin:0 auto">
		<textarea name="summernote" id="summernote"><?=$summernote ?></textarea>
		<textarea name="imageTag" id="imageTag" style="display:none;"></textarea>
	</div>
	</form>
	
	<div class='cs_btnBox'>
		<div class="cs_btn" id="id_savEachScen" data-page="<?=$page?>" style="float:none; margin-top: 25px;">전 송</div>
		<div class="cs_btn" id="id_preEachScen" style="display: none;">미리보기</div>
	</div>
	<div id="id_helpForm">
		<div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
			<?php
				if($type != "group") echo "◈ 전광판 기본정보<br/>&nbsp;- 장비명, 사이즈, IP, 주소 등 설치된 전광판의 기본 정보입니다.<br/><br/>";
				else echo "◈ 전광판 선택<br/>&nbsp;- 일괄적으로 시나리오를 전송할 전광판(들)을 선택합니다.<br/><br/>";
			?>
			◈ 시나리오 설정<br/><br/>
			
			&nbsp;<font class="cs_helpIcon">●</font> 표시/완료효과<br/>
			&nbsp;&nbsp;- 전광판에 문구가 표출/종료될 때의 효과를 설정합니다.<br/>
			&nbsp;<font class="cs_helpIcon">●</font> 표시/완료속도<br/>
			&nbsp;&nbsp;- 전광판에 문구가 표출/종료되는 속도입니다.<br/>
			&nbsp;&nbsp;- 속도가 1에 가까울수록 문구가 빠르게 표시되거나, 사라집니다.<br/>
			&nbsp;<font class="cs_helpIcon">●</font> 표시/완료시간<br/>
			&nbsp;&nbsp;- 전광판에 문구를 표출/종료할 기간을 설정합니다.<br/>
			&nbsp;<font class="cs_helpIcon">●</font> 표시유지시간<br/>
			&nbsp;&nbsp;- 전광판에 문구가 유지되는 시간입니다.<br/>
			&nbsp;&nbsp;- 최대 20초까지 설정할 수 있습니다.<br/>
			&nbsp;<font class="cs_helpIcon">●</font> 릴레이 동작여부<br/>
			&nbsp;&nbsp;- 시나리오 표출시, 전광판에 연결된 장비들을 같이 동작시킬 수 있습니다.<br/><br/>
			
			◈ 시나리오 내용<br/>
			&nbsp;- 글씨 색상, 정렬, 글씨체, 글씨 크기, 진하기를 결정한 후, 전광판에 전송할 내용을 입력합니다.<br/><br/>
			
			&nbsp;- 전송된 시나리오는 ‘표출중 시나리오 리스트’에서 확인할 수 있습니다.
		</div>
	</div>
</div>

<script src="/js/summernote-lite.min.js"></script>
<script>
	$(document).ready(function(e){
		$( "#startDate" ).datepicker(
			{
					dayNames: ['월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'],
					dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], 
					monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dateFormat:"yy-mm-dd"
			}
		);
		
		$( "#endDate" ).datepicker(
			{
					dayNames: ['월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'],
					dayNamesMin: ['월', '화', '수', '목', '금', '토', '일'], 
					monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dateFormat:"yy-mm-dd"
			}
		);
		
		$('#summernote').summernote({
			disableResizeEditor : true,
			height: <?=($row['SizeY']*2)?>,
			width: <?=(($row['SizeX']*2)+2)?>,
			toolbar: [
			['color', ['forecolor']],
			['para', ['paragraph']],
			['font', ['fontname','fontsize','bold'  ]]
			],
			fontNames: ['sans-serif', 'Arial','NanumGothic','NanumSquare' ],
			fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36', '40', '46', '52', '56'],
			lineHeights : 1
		});
		
		$("#summernote").summernote('fontSize',40);
		$('#summernote').summernote('backColor', 'black');
		$("#summernote").summernote('foreColor', '#ffffff');
		$("#summernote").summernote('lineHeight', 1.3);
		
	});
</script>

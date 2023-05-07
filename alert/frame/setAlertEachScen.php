<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

	if( isset($_GET['level']) ){ $level = $_GET['level']; } else { $level = 1; }
	if( isset($_GET['mentNum']) ){ $mentNum =  $_GET['mentNum']; } else { $mentNum = null; }

	$disDao = new WB_DISPLAY_DAO;
	$disVo = new WB_DISPLAY_VO;

	$mentDao = new WB_ISUMENT_DAO;
	$mentVo = new WB_ISUMENT_VO;
?>

<style>
	.cs_btn
	{
		float:none;
		margin:0 auto;
	}

	.cs_btn.select
	{
		background-color:#f94143;
	}

	.note-editable
	{
		padding: 0px !important;	
	}

	p
	{
		margin:0px !important;
	}

	input 
	{
		border:1px solid #d9d9d9;	
	}

	/* 2021.10.06 추가 by Park Jong-Sung */
	.overlay 
	{
		position: absolute; /* Sit on top of the page content */
		display: none; /* Hidden by default */
		width: 100%; /* Full width (cover the whole page) */
		height: 100%; /* Full height (cover the whole page) */
		top: 0;
		left: 0;
		background-color: rgba(0,0,0,0.5); /* Black background with opacity */
		z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
		cursor: default; /* Add a pointer on hover */
	}
</style>

	<div class="cs_loading">
		<div class="cs_message">데이터 전송중입니다.<br>잠시만 기다려주세요.</div>
	</div>

<div class="cs_frame" > 
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="box-shadow:none; border:none;">
		<tr style="border: none;">
			<td width="16%" style="border: none;"><div class="cs_btn <?php if( $level == 1 ) echo "select"; ?>" id="id_levelBtn" data-num="1">1단계</div></td>
			<td width="16%" style="border: none;"><div class="cs_btn <?php if( $level == 2 ) echo "select"; ?>" id="id_levelBtn" data-num="2">2단계</div></td>
			<td width="16%" style="border: none;"><div class="cs_btn <?php if( $level == 3 ) echo "select"; ?>" id="id_levelBtn" data-num="3">3단계</div></td>
			<td width="16%" style="border: none;"><div class="cs_btn <?php if( $level == 4 ) echo "select"; ?>" id="id_levelBtn" data-num="4">4단계</div></td>
		</tr>
	</table>

	<table id="1" border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
		<tr> 
			<th width="5%">No</th>
			<th width="20%">사이즈</th>
			<th>내용</th>
			<th width="15%">시나리오 종료</th>
		</tr>
		<?php
			$mentVo = $mentDao->SELECT();
			if( $mentVo->{"DisMent{$level}"} )
			{
				$disList = explode(",", $mentVo->{"DisMent{$level}"});
				$count = 1;
				$n = "";
				
				foreach( $disList as $l )
				{
					$disVo = $disDao->SELECT_SINGLE("DisCode = '{$l}'");
	
					echo "<tr align='center'>";
						echo "<td>".$count++."</td>";
						echo "<td> 320 x 64 </td>";
						echo "<td>";
							echo "<img alt='이미지를 찾을 수 없습니다.' src='/{$disVo->ViewImg}' id='id_disBtn' data-type='select' title='코멘트 변경' value='{$disVo->DisCode}' style='width:320px;cursor:pointer;'>";
						echo "</td>";
						echo "<td>";
							echo "<div class='cs_btn deleteBtn_{$disVo->DisCode}' id='id_disBtn' value='{$disVo->DisCode}' data-type='delete' style='width:80%;'>삭제</div>";
						echo "</td>";
					echo "</tr>";
				}
			}
		?>
	</table>

	<div class="blank" style="padding-bottom: 50px;"></div>

	<form action="" method="post" id="id_form">
		<div style="margin-top:15px;">◈ 시나리오 내용</div>
			
		<div class="cs_frame" style="font-size: 12px;">
			<textarea name="summernote" id="id_summernote"></textarea>
			<textarea name="imageTag" id="id_imageTag" style="display:none;"></textarea>
		</div>

		<input type="hidden" name="level" value="<?=$level?>">
	</form>

	<div class="cs_btnBox" style="margin-top:20px;">
		<div class="cs_btn updateBtn" id="id_disBtn" style="display:none;" data-type="update" value="">코멘트 수정</div>
		<div class="cs_btn insertBtn" id="id_disBtn" data-type="insert" value="0">코멘트 추가</div>
	</div>

	<div id="id_helpForm">
		<div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
			- 단계별 경보발령시, 전광판에 표출될 문구입니다.<br/>
			- [1단계], [2단계], [3단계], [4단계]를 클릭하여 문구를 추가/삭제할 수 있습니다.<br/><br/>

			◈ 시나리오 내용<br/>
			&nbsp;- 글씨 색상, 정렬, 글씨체, 글씨 크기, 진하기를 결정한 후, 전광판에 전송할 내용을 입력합니다.<br/>
		</div>
	</div>
</div>

<script src="/js/summernote-lite.min.js"></script>
<script>
	$(document).ready(function(e) 
	{
		$('#id_summernote').summernote(
		{
			disableResizeEditor : true,
			height: <?=(64*2) ?>,
			width:<?=(320*2)+2 ?>,
			toolbar: [
			['color', ['forecolor']],
			['para', ['paragraph']],
			['font', ['fontname','fontsize','bold']]
			],
			fontNames: ['sans-serif', 'Arial','NanumGothic','NanumSquare'],
				fontSizes : ['5','10','15','20','40','44','48','52','56','60','72','80','88','92','100'],
			lineHeights : 1
		});
		
		$("#id_summernote").summernote('fontSize', 40);
		$('#id_summernote').summernote('backColor', 'black');
		$("#id_summernote").summernote('foreColor', '#ffffff');
		$("#id_summernote").summernote('lineHeight', 1.3);

	});	
</script>
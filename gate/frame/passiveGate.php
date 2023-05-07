<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
?>
<div class="cs_frame">
   	<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
    	<tr>
    		<th>차단기</th>
            <th>상태변경</th>
    	</tr>

        <?php
			include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

			$statusDao = new WB_GATESTATUS_DAO;
			$equipDao = new WB_EQUIP_DAO;

			$statusVo = new WB_GATESTATUS_VO;
			$equipVo = new WB_EQUIP_VO;

			$equipVo = $equipDao->SELECT("GB_OBSV = '20' AND USE_YN = '1'");
			if( isset($equipVo[0]->{key($equipVo[0])}) )
			{
				foreach( $equipVo as $v )
				{
					$statusVo = $statusDao->SELECT_SINGLE("CD_DIST_OBSV='{$v->CD_DIST_OBSV}'");
					if( !isset($statusVo->{key($statusVo)}) )
					{
						$statusVo->CD_DIST_OBSV = $v->CD_DIST_OBSV;
						$statusVo->RegDate = date("Y-m-d H:i:s");
						$statusVo->Gate = "open";

						$statusDao->INSERT($statusVo);
					}

					echo "<tr>";
						echo "<td>{$v->NM_DIST_OBSV}</td>";
						echo "<td>";
							if( $statusVo->Gate == "open" ) $bgcolor = "282bca"; else $bgcolor = "5e60cd";
							echo "<div class='cs_btn gate{$v->CD_DIST_OBSV}' id='id_gatebtn' data-num='{$v->CD_DIST_OBSV}' data-type='open' style='margin-top:0px;background-color:#{$bgcolor}'>열림</div>";
							if( $statusVo->Gate == "close" ) $bgcolor = "282bca"; else $bgcolor = "5e60cd";
							echo "<div class='cs_btn gate{$v->CD_DIST_OBSV}' id='id_gatebtn' data-num='{$v->CD_DIST_OBSV}' data-type='close' style='margin-top:0px;background-color:#{$bgcolor}'>닫힘</div>";
						echo "</td>";
					echo "</tr>";
				}
			}
		?>
    </table>

	<div id="id_helpForm">
		<div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
			- 제어하려는 차단기의 상태(열림/닫힘)를 클릭합니다.</br>
			- 차단기의 상태는 파란색 버튼 색상의 내용과 같습니다.
		</div>
	</div>

</div> <?php //frame?>
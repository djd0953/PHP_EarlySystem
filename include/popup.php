<style>
    .cs_popup .cs_pCate.active
    {
	color:<?=$_SESSION['color']?>;
	border-top:1px solid <?=$_SESSION['color']?>;
    }
</style>
<div class="cs_popup" id="id_popup">
	<div class="cs_pBtn" id="id_pBtn"></div>
    <div class='cs_successMessage'>작업을 완료했습니다.</div>
        <div class="cs_pTitle">
            <div><span>지능형 어시스턴트</span></div>
            <div style="margin-bottom:4px;"><span class="material-symbols-outlined psychology_alt" style="cursor:unset;">psychology_alt</span></div>
        </div>
    
    <div class="cs_pCateTab">
        <div class="cs_pCate active" data-type="alert">실시간현황</div>
    	<div class="cs_pCate" data-type="data">계측현황</div>
    	<div class="cs_pCate" data-type="equip">장비현황</div>
    	<div class="cs_pCate" data-type="as">A/S접수</div>
    	<div class="cs_pCate" data-type="radar">위성영상</div>
    </div>
    
    <div class="cs_pContent">
        <div class="cs_dataForm" id="id_data_alert" style="background-color:blue;overflow:hidden;">
            <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/server/alertPopup.php"; ?>
        </div>
        <div class="cs_dataForm" id="id_data_data" style="display:none;">
            <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/server/dataPopup.php"; ?>
        </div>
        <div class="cs_dataForm" id="id_data_equip" style="display:none;">
            <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/server/equipPopup.php"; ?>
        </div>
        <div class="cs_dataForm" id="id_data_as" style="display:none;">
            <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/server/asPopup.php"; ?>
        </div>
        <div class="cs_dataForm" id="id_data_radar" style="display:none;">
            <?php include_once $_SERVER["DOCUMENT_ROOT"]."/include/server/radarPopup.php"; ?>
        </div>
    </div>
</div>
<script src="/js/jquery-1.9.1.js"></script>
<script src="/js/popup.js"></script>
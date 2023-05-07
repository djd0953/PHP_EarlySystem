<?php
    session_start();

    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php"; 

    $getText = $_GET['dType'];
    $explode = explode("/", $getText);
    $type = $explode[0];
    $equip = $explode[1];

    $dis = "";
    $num = "";
    $name = "";
    $ip = "";
    $phone = "";
    $latlon = "";
    $add = "";
    $xSize = "";
    $ySize = "";
    $connType = "";
    $connModel = "";
    $err = "";
    $GB = "";
    $date = "";
    $stat = "";
    $rain = "";
    $see = "";
    $sub = "";
    $det = "";
    $ds = "";
    $use = "";

    if($type == "upds") $dis = "disabled";
    else if($type == "upd")
    {
        $sql = "SELECT * FROM wb_equip WHERE CD_DIST_OBSV = {$equip}";
        $res = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($res);

        $num = "{$row['CD_DIST_OBSV']}";
        $name = "{$row['NM_DIST_OBSV']}";
        if(empty($row['ConnIP']) && empty($row['ConnPort'])) $ip = "";
        else $ip = "{$row['ConnIP']}:{$row['ConnPort']}";
        $phone = "{$row['ConnPhone']}";
        if(empty($row['LAT']) && empty($row['LON'])) $latlon = "";
        else $latlon = "{$row['LAT']}, {$row['LON']}";
        $add = "{$row['DTL_ADRES']}";
        $xSize = "{$row['SizeX']}";
        $ySize = "{$row['SizeY']}";
        $connType = "{$row['ConnType']}";
        $connModel = "{$row['ConnModel']}";
        $err = "{$row['ErrorChk']}";
        $GB = "{$row['GB_OBSV']}";
        $date = "{$row['LastDate']}";
        $stat = "{$row['LastStatus']}";
        $rain = "{$row['RainBit']}";
        $see = "{$row['SeeLevelUse']}";
        $sub = "{$row['SubOBCount']}";
        $det = "{$row['DetCode']}";
        $ds = "{$row['DSCODE']}";
        $use = "{$row['USE_YN']}";
    }
?>
<style>
    table td
    {
        text-align: left;
        padding-left: 10px;
    }
</style>
<div class="cs_frame">
    <form name="date" id="id_form" method="post" action="">
        <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
            <tr>
                <th width="15%">장비번호</th>
                <td><input type="text" maxlength="25" name="equip0" <?=$dis?> value="<?=$num?>">&nbsp;&nbsp;&nbsp;* 숫자로만 적어주세요.</td>
                <th width="15%">장비이름</th>
                <td><input type="text" maxlength="25" name="equip1" <?=$dis?> value="<?=$name?>">&nbsp;&nbsp;&nbsp;* 최대 7글자 이상 넘기지 않는 것을 추천합니다.</td>
            </tr>
            <tr>
                <th width="15%">ConnIP:Port</th>
                <td><input type="text" maxlength="25" name="equip2" <?=$dis?> value="<?=$ip?>">&nbsp;&nbsp;&nbsp;* 장비 IP:Port  ex)192.168.0.0:9999</td>
                <th width="15%">ConnPhone</th>
                <td><input type="text" maxlength="11" name="equip3" <?=$dis?> value="<?=$phone?>">&nbsp;&nbsp;&nbsp;* CDMA번호  ex)01212345678</td>
            </tr>
            <tr>
                <th width="15%">LAT, LON</th>
                <td><input type="text" name="equip4" value="<?=$latlon?>">&nbsp;&nbsp;&nbsp;* 소수점 이하 4자리까지 작성.</td>
                <th width="15%">주소</th>
                <td ><input type="text" name="equip5" value="<?=$add?>" style="width:80%;">&nbsp;&nbsp;&nbsp;</td>
            </tr>
            <tr>
                <th width="15%">전광판 X Size</th>
                <td><input type="text" maxlength="5" name="equip6" value="<?=$xSize?>">&nbsp;&nbsp;&nbsp;* 숫자 2~3자리로 구성 (보통 320).</td>
                <th width="15%">전광판 Y Size</th>
                <td><input type="text" maxlength="5" name="equip7" value="<?=$ySize?>">&nbsp;&nbsp;&nbsp;* 숫자 2~3자리로 구성 (보통 64).</td>
            </tr>
            <tr>
                <th width="15%">ConnType</th>
                <td><input type="text" maxlength="25" name="equip8" value="<?=$connType?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
                <th width="15%">ConnModel</th>
                <td><input type="text" maxlength="25" name="equip9" value="<?=$connModel?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
            </tr>
            <tr>
                <th width="15%">ErrorChk</th>
                <td><input type="text" maxlength="1" name="equip10" value="<?=$err?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지. (0~5) </td>
                <th width="15%">GB_OBSV</th>
                <td><input type="text" maxlength="2" name="equip11" value="<?=$GB?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
            </tr>
            <tr>
                <th width="15%">LastDate</th>
                <td><input type="text" maxlength="19" name="equip12" value="<?=$date?>" placeholder="yyyy-mm-dd hh:mm:ss">&nbsp;&nbsp;&nbsp;* yyyy-mm-dd hh:mm:ss</td>
                <th width="15%">LastStatus</th>
                <td><input type="text" maxlength="4" name="equip13" value="<?=$stat?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지. ("OK"/"Fail")</td>
            </tr>
            <tr>
                <th width="15%">RainBit</th>
                <td><input type="text" maxlength="5" name="equip14" value="<?=$rain?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
                <th width="15%">SeeLevelUse</th>
                <td><input type="text" maxlength="1" name="equip15" value="<?=$see?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
            </tr>
            <tr>
                <th width="15%">SubOBCount</th>
                <td><input type="text" maxlength="2" name="equip16" value="<?=$sub?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
                <th width="15%">DetCode</th>
                <td><input type="text" maxlength="2" name="equip17" value="<?=$det?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
            </tr>
            <tr>
                <th width="15%">DSCode</th>
                <td><input type="text" maxlength="10" name="equip18" value="<?=$ds?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지.</td>
                <th width="15%">USE_YN</th>
                <td><input type="text" maxlength="1" name="equip19" value="<?=$use?>">&nbsp;&nbsp;&nbsp;* 개발자 외 작성 금지. (0/1)</td>
            </tr>
        </table>
        <input type="hidden" name="equip20" value="<?=$equip?>">
        <input type="hidden" name="equip21" value="<?=$type?>">
    </form>

	<div style="float:right;">
        <div class="cs_btn" id="id_map">지도</div>
		<div class="cs_btn" id="id_chgbtn"><?= ($type == "add") ? "추가" : "수정" ?></div>
	</div>
</div>
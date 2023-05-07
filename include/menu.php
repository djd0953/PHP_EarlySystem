<?php
    if( !session_id() ) session_start();

    if( isset($_SESSION['userIdx']) && isset($_SESSION['system']) )
    {
        include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
        $userDao = new WB_USER_DAO;
        $userVo = $userDao->SELECT_SINGLE("idx = '{$_SESSION['userIdx']}'");
        $auth = $userVo->Auth;
    }
    else
    {
        echo "<script>";
        echo "alert('세션이 만료되었습니다.');";
        echo "window.location.href = '/login/logout.php';";
        echo "</script>";
    }

    switch($_SESSION['system'])
    {
        case "flood"    :
            $logOutColor = "#4c19b4";
            $logOutColorHover = "#3C1097";
            break;
        case "warning"  :
            $logOutColor = "#037c47";
            $logOutColorHover = "#0A7747";
            break;
        case "dplace"   :
            $logOutColor = "#037c47";
            $logOutColorHover = "#0A7747";
            break;
        case "ai"       :
            $logOutColor = "#4c19b4";
            $logOutColorHover = "#3C1097";
            break;
    }

    $url = explode("/", $_SERVER['PHP_SELF'] );
    $lastUrl = $url[1];
    $headerfix = ( strpos($lastUrl, ".") ) ? substr($lastUrl, 0, strpos($lastUrl, ".")) : $lastUrl;
    
	$menuDao = new DAO_T;
	$menuVo = new WB_EQUIP_VO;
	$menuArr = array();
    $sessionfix = $_SESSION["system"];
    $menuList = array("data", "broad", "display", "gate", "sms", "alert");
    $rootMenuArr = array("data", "broad", "display", "gate", "sms", "alert");

	$menuVo = $menuDao->SELECT_QUERY("SELECT DISTINCT GB_OBSV as GB FROM wb_equip WHERE USE_YN = '1'");
	foreach($menuVo as $v)
	{
        switch($v['GB'])
        {
            case "01":
            case "02":
            case "03":
            case "04":
            case "05":
            case "06":
            case "08":
            case "21":
                array_push($menuArr, "data");
                break;
            case "17":
                array_push($menuArr, "sms");
                array_push($menuArr, "broad");
                break;
            case "18":
                array_push($menuArr, "display");
                break;
            case "20":
                array_push($menuArr, "gate");
                break;
        }
	}
    array_push($menuArr, "alert");
?>	

<style>
    .cs_menu
    {
        background-color:<?=$_SESSION['color']?>;
    }
    .cs_menu .cs_icon_box
    {
        background-image:url("/image/<?=$_SESSION['system']?>/parkIcon2.png");
    }
    .cs_sub_link.active
    {
        color:<?=$_SESSION['color']?>;
    }
    .cs_logout
    {
        background-color:<?=$logOutColor?>;
    }
    .cs_logout:hover
    {
        background-color:<?=$logOutColorHover?>;
    }
</style>

<div class="cs_menu">
	<div class="cs_icon_box" id="id_menu_list" data-url="/main.php"></div>
    <div class="cs_icon_line"></div>
<?php
	if( $auth == 'guest' ) 
    {	
        /******** Guest Menu ********/
        //Main
        $postfix = ( $headerfix == "main" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/main.php' style='background-image:url(/image/{$sessionfix}/main_menu{$postfix}.jpg);'></div>";
        
        //Data
        $postfix = ( $headerfix == "data" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/data/dataFrame.php' style='background-image:url(/image/{$sessionfix}/data_menu{$postfix}.jpg);'></div>";
        
        //Report
        $postfix = ( $headerfix == "report" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/report/reportFrame.php' style='background-image:url(/image/{$sessionfix}/report_menu{$postfix}.jpg);'></div>";
        
        //LogOut
        echo "<div class='cs_logout' id='id_menu_list' data-url = '/login/logout.php'>로그아웃</div>";
    } 
    else if( $auth == 'admin' )
    {
        /******** Admin Menu ********/
        //Main
        $postfix = ( $headerfix == "main" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/main.php' style='background-image:url(/image/{$sessionfix}/main_menu{$postfix}.jpg);'></div>";

        //Data, Broad, Display, Gate, SMS, Alert 
        foreach($menuList as $m)
        {
            if( in_array($m, $menuArr) )
            {
                $postfix = ( $headerfix == $m ) ? "_active" : "";
                echo "<div class='cs_menu_list' id='id_menu_list' data-url='/{$m}/{$m}Frame.php' style='background-image:url(/image/{$sessionfix}/{$m}_menu{$postfix}.jpg);'></div>";    
            }
        }

        //Report
        $postfix = ( $headerfix == "report" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/report/reportFrame.php' style='background-image:url(/image/{$sessionfix}/report_menu{$postfix}.jpg);'></div>";

        //AdminiRegist
        $postfix = ( $headerfix == "adminRegist" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/adminRegist/adminFrame.php' style='background-image:url(/image/{$sessionfix}/user_menu{$postfix}.png);'></div>";

        //LogOut
        echo "<div class='cs_logout' id='id_menu_list' data-url = '/login/logout.php'>로그아웃</div>";
    } 
    else if( $auth == 'root' )
    {
        /******** Develop Menu ********/
        //Main
        $postfix = ( $headerfix == "main" ) ? "_active" : "";
        echo "<div class='cs_menu_list rootMenu' id='id_menu_list' data-url='/main.php' style='background-image:url(/image/{$sessionfix}/main_menu{$postfix}.jpg);'></div>";

        //Data, Broad, Display, Gate, SMS, Alert 
        foreach($menuList as $m)
        {
            if( in_array($m, $rootMenuArr) )
            {
                $postfix = ( $headerfix == $m ) ? "_active" : "";
                echo "<div class='cs_menu_list rootCloseMenu' id='id_menu_list' data-url='/{$m}/{$m}Frame.php' style='background-image:url(/image/{$sessionfix}/{$m}_menu{$postfix}.jpg);display:none;'></div>";    
            }
        }

        //Report
        $postfix = ( $headerfix == "report" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/report/reportFrame.php' style='background-image:url(/image/{$sessionfix}/report_menu{$postfix}.jpg);'></div>";
        
        //Equip
        $postfix = ( $headerfix == "equip" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/equip/equipFrame.php' style='background-image:url(/image/{$sessionfix}/equip_menu{$postfix}.png);'></div>";

        //Log
        $postfix = ( $headerfix == "log" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/log/logFrame.php' style='background-image:url(/image/{$sessionfix}/log_menu{$postfix}.png);'></div>";

        //AdminiRegist
        $postfix = ( $headerfix == "adminRegist" ) ? "_active" : "";
        echo "<div class='cs_menu_list' id='id_menu_list' data-url='/adminRegist/adminFrame.php' style='background-image:url(/image/{$sessionfix}/user_menu{$postfix}.png);'></div>";

        //LogOut
        echo "<div class='cs_logout' id='id_menu_list' data-url = '/login/logout.php'>로그아웃</div>";
    } 
?>
</div>
<script>
    let rootMenu = document.querySelector(".rootMenu");
    if( rootMenu != null )
    {
        rootMenu.addEventListener("mouseover", ()=>
        {
            let menu = document.querySelectorAll(".rootCloseMenu");
    
            for(let i = 0; i < menu.length; i++)
            {
                menu[i].style.display = "block";
            }
        });
    }
</script>
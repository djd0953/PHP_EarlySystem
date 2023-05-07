<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

    $dao = new WB_EQUIP_DAO;
    $vo = new WB_EQUIP_VO;
    $vo = $dao->SELECT("USE_YN = '1'");

    $nowDate = new DateTime();

    $result = array();
    $coment = array();
    $asSend = array();
    $alert = array();

    $alertType = $_GET['alertType'];

    if( $alertType == "equip" )
    {      
        $asdao = new WB_ASRECEIVED_DAO;
        $asvo = new WB_ASRECEIVED_VO;
    
        foreach($vo as $v)
        {
            // 장비의 LastDate 가져오기
            $equipLastDate = new DateTime($v->LastDate);
            $equipInterval = $nowDate->diff($equipLastDate);

            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", "RegDate DESC");
            // A/S 접수를 한번도 안했던 장비에 대한 예외처리
            if( isset($asvo->RCode) ) $asDate = new DateTime($asvo->RegDate);
            else $asDate = new DateTime(date("Ymd His",strtotime("-8 days")));

            $asInterval = $nowDate->diff($asDate);

            if( $equipInterval->days > 0 || $equipInterval->h > 1 ) $receive = true;
            else $receive = false;

            if( strtolower($v->LastStatus) != "fail" )
            {
                if( $receive && $asInterval->days > 6 )
                {
                    $coment[$v->CD_DIST_OBSV] = $v->NM_DIST_OBSV;
                    $asSend[$v->CD_DIST_OBSV] = "Auto Received";
                }
                else continue;
            }
            else
            {
                if( $receive && $asInterval->days > 6 )
                {
                    $coment[$v->CD_DIST_OBSV] = $v->NM_DIST_OBSV;
                    $asSend[$v->CD_DIST_OBSV] = "Auto Received";
                }
                else
                {
                    $alert[$v->CD_DIST_OBSV] = $v->NM_DIST_OBSV; // A/S안하고 점검요망만 띄울 포인트 찾기   
                }
            }
        }
    }
    else if( $alertType == "alert" )
    {
        $issuedao = new WB_ISSUESTATUS_DAO;
        $issuevo = new WB_ISSUESTATUS_VO;

        $isugroupdao =new WB_ISUALERTGROUP_DAO;
        $isugroupvo = new WB_ISUALERTGROUP_VO;

        $issuevo = $issuedao->SELECT("issueGrade IN ('Level1', 'Level2', 'Level3')");

        if( isset($issuevo[0]->idxCode) )
        {
            foreach($issuevo as $v)
            {
                $isugroupvo = $isugroupdao->SELECT_SINGLE("GCode = '{$v->GCode}'");
                $coment[$v->issueGrade] = $isugroupvo->GName; // 어떤 경보인지 알려주는 바로가기 포인트 만들지 고민..
            }
        }
    }

    $result[0] = $coment;
    $result[1] = $asSend;
    $result[2] = $alert;
    
    echo json_encode($result);
?>
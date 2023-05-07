<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    class LPR_VO
    {
        public $idx;
        public $id;
        public $type;
        public $pos;
        public $carNum;
        public $timeStamp;
        public $retData;
    }

    class LPRIMG_VO
    {
        public $idx;
        public $carNum;
        public $carBin;
    }

    class LPR_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new FOCUS_DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "lpr";
        }

        function INSERT_LPR(LPR_VO $lpr,LPRIMG_VO $img)
        {
            $select = "";
            $value = "";

            foreach($lpr as $key => $val)
            {
                if( key($lpr) != $key ) 
                {
                    $select .= ", ";
                    $value .= ", " ;
                }

                $select .= "{$key}";
                $value .= ( $lpr->{$key} == "" ) ? "NULL" : "'{$val}'" ;
            }

            $this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} )";
            $this->SQL($this->sql);

            $idx = $this->conn->db_conn->lastInsertId();
            $img->idx = $idx;

            $select = "";
            $value = "";

            foreach($img as $key => $val)
            {
                if( key($img) != $key ) 
                {
                    $select .= ", ";
                    $value .= ", " ;
                }

                $select .= "{$key}";
                $value .= ( $img->{$key} == "" ) ? "NULL" : "'{$val}'" ;
            }

            $this->sql = "INSERT INTO lprimg( {$select} ) VALUES ( {$value} )";
            $this->SQL($this->sql);
        }
    }    

    header('Content-Type: application/json; charset=UTF-8');
    $data = file_get_contents('php://input');
    $data = preg_replace('/[\x00-\x1F\x7F]/u', '', $data);

    $fp = fopen("log/log_".date("ym",strtotime("Now")).".txt", "a");
    fwrite($fp, date("Y-m-d H:i:s", strtotime("Now")));
    fwrite($fp, "\r\n{$data}\r\n=====================================================\r\n");
    fclose($fp);

    $dao = new LPR_DAO;
    $lpr = new LPR_VO;
    $img = new LPRIMG_VO;
    $res = array();

    try
    {
        if( strlen($data) < 1 ) throw new Exception("Body내용이 없습니다", 400);

        $json = array();
        $json = json_decode($data, true);
        
        $errString = array("ID", "TYPE", "POS", "CAR-NUM", "CAR-BIN", "TIME-STAMP");
        foreach($errString as $s)
        {
            if( !isset($json[$s]) ) throw new Exception("{$s} 값이 없습니다.", 400);
        }

        $lpr->id = $json["ID"];
        $lpr->type = $json["TYPE"];
        $lpr->pos = $json["POS"];
        $lpr->carNum = $json["CAR-NUM"];
        $lpr->timeStamp = date("Y-m-d H:i:s", strtotime($json["TIME-STAMP"]));
        $img->carNum = $json["CAR-NUM"];
        $img->carBin = $json["CAR-BIN"];

        $dao->INSERT_LPR($lpr, $img);

        $res["code"] = "200";
        $res["msg"] = "OK";
    }
    catch(Exception $ex)
    {
        $lpr->timeStamp = date("Y-m-d H:i:s");
        $lpr->retData = $data;
        if( $data ) $dao->INSERT($lpr);

        $res["code"] = $ex->getCode();
        $res["msg"] = $ex->getMessage();
    }

    echo json_encode($res);
?>
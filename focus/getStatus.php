<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    $data = json_decode(file_get_contents('php://input'),true);

    class STATUS_VO
    {
        public $idx;
        public $id;
        public $type;
        public $cmd;
        public $reqStamp;
        public $regDate;
        public $retData;
    }

    class STATUS_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new FOCUS_DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "status";
        }

        function INSERT_LPR(STATUS_VO $vo)
        {
            $select = "";
            $value = "";

            foreach($vo as $key => $val)
            {
                if( key($vo) != $key ) 
                {
                    $select .= ", ";
                    $value .= ", " ;
                }

                $select .= "{$key}";
                $value .= ( $vo->{$key} == "" ) ? "NULL" : "'{$val}'" ;
            }

            $this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} )";
            $this->SQL($this->sql);
        }

        public function SELECT($where = "1", $order = "idx DESC", $limit = 15)
		{
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
		}

        public function SELECT_SINGLE($where = "1", $order = "idx DESC", $limit = 15)
		{
            return $this->SELECT($where, $order, $limit)[0];
		}

	}    

    $dao = new STATUS_DAO;
    $vo = new STATUS_VO;
    
    $fp = fopen("log/log_".date("ym",strtotime("Now")).".txt", "a");
    fwrite($fp, date("Y-m-d H:i:s", strtotime("Now")));
    fwrite($fp, "\r\n".json_encode($data)."\r\n");
    fwrite($fp, "=============================================================\r\n");
    fclose($fp);

    try
    {
        if( !isset($data["TYPE"]) ) throw new Exception("TYPE이 없습니다", 400);
        if( !isset($data["ID"]) ) throw new Exception("ID가 없습니다", 400);
        if( !isset($data["STATUS"]) ) throw new Exception("STATUS가 없습니다", 400);
        if( !isset($data["RET-STAMP"]) ) throw new Exception("RET-STAMP가 없습니다", 400);

        $vo->id = $data["ID"];
        $vo->type = $data["TYPE"];
        $vo->cmd = $data["STATUS"];
        $vo->reqStamp = date("Y-m-d H:i:s", strtotime($data["RET-STAMP"]));
        $vo->regDate = date("Y-m-d H:i:s");
        $dao->INSERT_LPR($vo);

        $res["code"] = "200";
        $res["msg"] = "OK";
    }
    catch(Exception $ex)
    {
        $vo->reqStamp = date("Y-m-d H:i:s");
        $vo->retData = json_encode($data);
        $dao->INSERT_LPR($vo);

        $res["code"] = $ex->getCode();
        $res["msg"] = $ex->getMessage();
    }

    echo json_encode($res);
?>
<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
    $data = file_get_contents('php://input');

    class STATUS_VO
    {
        public $idx;
        public $url;
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
    
    try
    {
        $json_data = json_decode($data, true);
        if( !isset($json_data["url"]) ) throw new Exception("URL이 입력되지 않았습니다.", 400);
        else $vo->url = $json_data["url"];
        $body = $data;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $vo->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST , 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json'));

        $res = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if( $responseCode >= 400 ) throw new Exception("입력된 URL 정보가 잘못되었습니다.", $responseCode);
        if( $responseCode <= 0 ) throw new Exception("{$vo->url}에 연결할 수 없습니다.", $responseCode);
        if( !isset($json_data["id"]) ) throw new Exception("ID 값이 없습니다.", 400);
        if( !isset($json_data["type"]) ) throw new Exception("TYPE 값이 없습니다.", 400);
        if( !isset($json_data["cmd"]) ) throw new Exception("CMD 값이 없습니다.", 400);
        if( !isset($json_data["reqStamp"]) ) throw new Exception("REQ-STAMP 값이 없습니다.", 400);

        $vo->id = $json_data["id"];
        $vo->type = $json_data["type"];
        $vo->cmd = $json_data["cmd"];
        $vo->reqStamp = $json_data["reqStamp"];
        $vo->regDate = date("Y-m-d H:i:s");
        $dao->INSERT_LPR($vo);     
    }
    catch(Exception $ex)
    {
        $vo->timeStamp = date("Y-m-d H:i:s");
        $vo->retData = $data;
        $dao->INSERT_LPR($vo);

        $res = array();
        $res["code"] = $ex->getCode();
        $res["msg"] = $ex->getMessage();
    }

    echo json_encode($res);
?>
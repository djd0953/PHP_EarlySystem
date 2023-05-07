<?php
    class DBConnect
    {
        private $host = 'localhost';
        private $port = '3306';
        private $dbname = 'amano';
        private $charset = 'utf8';
        private $username = 'userWooboWeb';
        private $password = 'wooboWeb!@';

        public $db_conn;

        function connect()
        {
            $this->db_conn = new PDO("mysql:host={$this->host}:{$this->port};dbname={$this->dbname};charset={$this->charset}", "{$this->username}", "{$this->password}");
            $this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->db_conn;
        }
    }

    class VO
    {
        public $idx;
        public $url;
        public $RegDate;
        public $actionType;
        public $remoteIP;
        public $eqpmID;
        public $id;
        public $pw;
        public $json;
    }

    class DAO
    {
        const STDLIMIT = 1000;

        public $conn;
        public $sql = "";
        public $table = "";
        public $selectKey = "";
        public $voName = "";

        function __construct()
        {
            $dbconn = new DBConnect;
            $this->conn = $dbconn->connect();
        }

        function InsertGateControl($array)
        {
            $this->sql = "INSERT INTO control(url, RegDate, actionType, remoteIP, eqpmID, id, pw) VALUES (:url, NOW(), :action, :ip, :eId, :id, :pw)";

            $stmt = $this->conn->prepare($this->sql);
            $stmt->bindParam(":url", $array["url"], PDO::PARAM_STR);
            $stmt->bindParam(":action", $array["actionType"], PDO::PARAM_STR);
            $stmt->bindParam(":ip", $array["remoteIP"], PDO::PARAM_STR);
            $stmt->bindParam(":eId", $array["eqpmID"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $array["id"], PDO::PARAM_STR);
            $stmt->bindParam(":pw", $array["pw"], PDO::PARAM_STR);
            $stmt->execute();
        }

        function ErrorJsonValue($value)
        {
            $this->sql = "INSERT INTO control(json) VALUES (:json)";
            $stmt = $this->conn->prepare($this->sql);
            $stmt->execute(array("json" => $value));
        }
    }

    $dao = new DAO;
    $vo = new VO;
    
    try
    {
        $data = file_get_contents('php://input');
        $json_data = json_decode($data, true);
        if( !isset($json_data["url"]) ) throw new Exception("URL이 입력되지 않았습니다.", 400);
        else $vo->url = $json_data["url"];
        $body = $data;
        $Authorization = base64_encode("{$json_data["id"]}:{$json_data["pw"]}");

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
        curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', "Authorization: {$Authorization}"));

        $res = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if( $responseCode >= 400 ) throw new Exception("입력된 URL 정보가 잘못되었습니다.", $responseCode);
        if( $responseCode <= 0 ) throw new Exception("{$vo->url}에 연결할 수 없습니다.", $responseCode);
        if( !isset($json_data["actionType"]) ) throw new Exception("ActionType 값이 없습니다.", 400);
        if( !isset($json_data["remoteIP"]) ) throw new Exception("RemoteIP 값이 없습니다.", 400);
        if( !isset($json_data["eqpmID"]) ) throw new Exception("EqpmId 값이 없습니다.", 400);
        if( !isset($json_data["id"]) ) throw new Exception("id 값이 없습니다.", 400);
        if( !isset($json_data["pw"]) ) throw new Exception("pw 값이 없습니다.", 400);

        $vo->actionType = $json_data["actionType"];
        $vo->remoteIP = $json_data["remoteIP"];
        $vo->eqpmID = $json_data["eqpmID"];
        $vo->id = $json_data["id"];
        $vo->pw = $json_data["pw"];
        $vo->RegDate = date("Y-m-d H:i:s");
        $dao->InsertGateControl($json_data);

        $response = array("result_code" => 200);
    }
    catch(Exception $ex)
    {
        $dao->ErrorJsonValue($data);

        $response = array();
        $response["result_code"] = $ex->getCode();
        $response["result"] = $ex->getMessage();
    }

    echo json_encode($response);
?>
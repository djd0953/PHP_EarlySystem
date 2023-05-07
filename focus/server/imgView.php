<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";
	
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
            $this->table = "lprimg";
        }

        public function SELECT($where = "1", $order = "idx DESC", $limit = 1000)
		{
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
		}

        public function SELECT_SINGLE($where = "1", $order = "idx DESC", $limit = 1000)
		{
            return $this->SELECT($where, $order, $limit)[0];
		}

	}  

    $dao = new LPR_DAO;
    $vo = $dao->SELECT_SINGLE("idx = {$_GET["idx"]}");

    if( $_GET["type"] == "img")
    {
        if($vo->{key($vo)})
        {
            echo "<div><img alt='양식에 맞지 않는 이미지 코드' src='data:image/jpeg;base64,{$vo->carBin}' width='375' style='font-size:22px;background-color:white;'></div>";
            echo "<div style='font-size:20px;text-align:center;color:#fff;background-color:#5e60cd;line-height:40px;position:absolute;bottom:0px;right:0px;'>{$vo->carNum}</div>";
        }
    }
    else
    {
        $res = array();
        
        $res["bin"] = $vo->carBin;
        echo json_encode($res);
    }
?>

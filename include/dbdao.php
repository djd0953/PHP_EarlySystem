<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";

    #region DAO 공용
    class DAO_T
    {
        /**
         * $brief DB 연결 및 Query 실행시 기본으로 사용 할 Class
         * @param $conn     : DBCONNECT Class와 연결하여 DB Connecter로 사용 할 변수
         * @param $sql      : 실행할 SQL문 담을 변수
         * @param $table    : SQL 실행 시 진행 될 Table 명
         */

        public $conn;
        public $sql = "";
		public $table = "";

        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
        }

        //Create PDO::Statement
        function Exec($sql)
        {
            return $this->conn->db_conn->query($sql);
        }

        function SQL($sql)
		{
            /**
             * SQL문을 가지고 접근 시 PDO::Statement 생성 (query)
             * Statement 생성과 동시에 Query 실행
             * 
             * @param $vo : PDO::Statement 담을 변수
             * 
             * @return PDO::Statement 형태
             * PDOStatement Object([queryString] => SQL문)
             */

            try
            {
                $vo = $this->conn->db_conn->query($sql);
                return $vo;
            }
            catch (PDOException $e)
            {
                return $e;
            }
            catch(Exception $e)
            {
                return $e;
            }
		}

		function Read($vo, $vo_name)
		{
            /**
             * @param $vo       : PDO::Statement 객체를 담고 있는 변수
             * @param $vo_name  : Query 실행 후 결과를 Mapping하여 담을 Class명 (DBVo.php에 Class 정의)
             * FETCH Mode 종류  : https://www.php.net/manual/en/pdo.constants.php
             */

            try
            {
                $vo->setFetchMode(PDO::FETCH_CLASS, $vo_name);
                $result = $vo->FetchAll();

                if( $result == null ) $result[0] = new $vo_name;
                return $result;
            }
            catch(PDOException $e)
            {
                return $e;
            }
            catch(Exception $e)
            {
                return $e;
            }
		}

        public function SELECT_QUERY($sql)
		{
            /**
             * 정해진 SELECT Query 형태 말고
             * 각 Join 등 복잡한 Query 형태가 필요 할 때 사용하기
             * 
             * @return Array [Array[[column] => [value], [col2] => [val2]..., [calN] => [valN]], Array2[[column] => [value], [col2] => [val2]..., [calN] => [valN]]...] (N : Record 갯수)
             */

            $rtv = array();
            $res = $this->SQL($sql);
            while ($row = $res->fetch(PDO::FETCH_ASSOC))
            {
                array_push($rtv, $row);
            }
            return $rtv;
		}

        public function INSERT($vo)
        {
            /**
             * INSERT문
             * 변수로 들어온 DB Table과 Mapping시킨 Class에 들어있는 값을 Insert 함
             * (DUPLICATE 문 실행시 현재 사용 중인 DB Table 구조 상 Error나서 주석처리)
             * 
             * @param $vo       : DBVo.php에 정의된 Class 형태 (첫번째 정의된 변수를 Key로 사용)
             * @param $select   : Column 명
             * @param $value    : VALUE 값
             * 
             * @return Integer ... Insert 된 Record 갯수?
             */

            $select = "";
            $value = "";
            //$duplicate = "";

            foreach($vo as $key => $val)
            {
                if( key($vo) != $key ) 
                {
                    $select .= ", ";
                    $value .= ", " ;
                    //$duplicate .= ", ";
                }

                $select .= "{$key}";
                $value .= ( $vo->{$key} === "" || $vo->{$key} === null ) ? "NULL" : "'{$val}'" ;
                //$duplicate .= ( $vo->{$key} == "" ) ? "" : "{$key} = '{$val}'";
            }

            //$this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} ) ON DUPLICATE KEY UPDATE {$duplicate}";
            $this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} )";
            
            return $this->SQL($this->sql);
        }

        public function TEST_INSERT($vo)
        {
            /**
             * INSERT문
             * 변수로 들어온 DB Table과 Mapping시킨 Class에 들어있는 값을 Insert 함
             * (DUPLICATE 문 실행시 현재 사용 중인 DB Table 구조 상 Error나서 주석처리)
             * 
             * @param $vo       : DBVo.php에 정의된 Class 형태 (첫번째 정의된 변수를 Key로 사용)
             * @param $select   : Column 명
             * @param $value    : VALUE 값
             * 
             * @return Integer ... Insert 된 Record 갯수?
             */

            $select = "";
            $value = "";
            //$duplicate = "";

            foreach($vo as $key => $val)
            {
                if( key($vo) != $key ) 
                {
                    $select .= ", ";
                    $value .= ", " ;
                    //$duplicate .= ", ";
                }

                $select .= "{$key}";
                $value .= ( $vo->{$key} == "" ) ? "NULL" : "'{$val}'" ;
                //$duplicate .= ( $vo->{$key} == "" ) ? "" : "{$key} = '{$val}'";
            }

            //$this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} ) ON DUPLICATE KEY UPDATE {$duplicate}";
            $this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} )";
            
            return $this->sql;
        }

        public function UPDATE($vo)
        {
            /**
             * UPDATE문
             * 
             * @param $vo       : DBVo.php에 정의된 Class 형태 (첫번째 정의된 변수를 Key로 사용)
             * @param $set      : Column = Value 형태의 Set 값
             * @param $keyword  : Class 첫번째 변수로 정의된 값 Column 이름과 Mapping
             * 
             * @return Integer ... Update 된 Record 갯수?
             */

            $set = "";
            $keyword = key($vo);
            foreach($vo as $key => $val)
            {
                if( $vo->{$key} !== "" )
                {
                    if( $keyword != $key ) $set .= ", ";
                    $set .= "{$key} = '{$val}'";
                }
            }

            $this->sql = "UPDATE {$this->table} SET {$set} WHERE {$keyword} = '{$vo->{$keyword}}'";
            return $this->SQL($this->sql);
        }

        public function TEST_UPDATE($vo)
        {
            /**
             * UPDATE문
             * 
             * @param $vo       : DBVo.php에 정의된 Class 형태 (첫번째 정의된 변수를 Key로 사용)
             * @param $set      : Column = Value 형태의 Set 값
             * @param $keyword  : Class 첫번째 변수로 정의된 값 Column 이름과 Mapping
             * 
             * @return Integer ... Update 된 Record 갯수?
             */

            $set = "";
            $keyword = key($vo);
            foreach($vo as $key => $val)
            {
                if( $vo->{$key} !== "" )
                {
                    if( $keyword != $key ) $set .= ", ";
                    $set .= "{$key} = '{$val}'";
                }
            }

            $this->sql = "UPDATE {$this->table} SET {$set} WHERE {$keyword} = '{$vo->{$keyword}}'";
            return $this->sql;
        }

        // DELETE ( 사용하기 위해 WHERE 절의 Key값 배치 조정 필요 )
        public function DELETE($vo)
        {
            /**
             * DELETE문
             * 
             * @param $vo       : DBVo.php에 정의된 Class 형태 (첫번째 정의된 변수를 Key로 사용)
             * @param $key      : Class 첫번째 변수로 정의된 값 Column 이름과 Mapping
             * 
             * @return Integer ... Delete 된 Record 갯수?
             */

            $key = key($vo);
            $this->sql = "DELETE FROM {$this->table} WHERE {$key} = '{$vo->{$key}}'";
            return $this->SQL($this->sql);
        }

        function INSERTID()
        {
            /**
             * Insert Query 실행 후 Key값(PK 값) 가져오기
             * 
             * @return $마지막 INSERT 된 Record PK 값
             */

            return $this->conn->db_conn->lastInsertId();
        }
    }
    #endregion
    #region EQUIP
    class WB_EQUIP_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_equip";
        }

		public function SELECT($where = "1", $order = "CD_DIST_OBSV", $limit = 1000)
		{
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
		}

        public function SELECT_SINGLE($where = "1", $order = "CD_DIST_OBSV", $limit = 1000)
		{
            return $this->SELECT($where, $order, $limit)[0];
		}

        public function FAIL_QUERY($cd_dist_osbv)
        {
            $this->sql = "UPDATE {$this->table} SET LastStatus = 'fail' WHERE CD_DIST_OBSV = '{$cd_dist_osbv}'";
        }
	}
    #endregion
    #region BROADCAST DAO
    class WB_BRDALERT_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_brdalert";
        }

        public function SELECT($where = "1", $order = "AltCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "wb_brdment_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "AltCode")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_BRDCID_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_brdcid";
        }

        public function SELECT($where = "1", $order = "CidCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function INSERT($vo_value)
        {
            $vo = $vo_value;
            $vo->CD_DIST_OBSV = ($vo->CD_DIST_OBSV == "") ? "NULL" : "'{$vo->CD_DIST_OBSV}'";
            $vo->Cid = ($vo->Cid == "") ? "NULL" : "'{$vo->Cid}'";

            $subQuery = "SELECT * FROM wb_brdcid WHERE CD_DIST_OBSV = {$vo->CD_DIST_OBSV}";
            $cnt = count($this->SELECT_QUERY($subQuery));
            if( $cnt > 0 )
            {
                $this->sql = "UPDATE {$this->table} SET Cid = {$vo->Cid}, CStatus = 'start', RegDate = now() WHERE CD_DIST_OBSV = {$vo->CD_DIST_OBSV}";
            }
            else
            {
                $this->sql = "INSERT INTO {$this->table} (CD_DIST_OBSV, Cid, CStatus, RegDate) VALUES ( {$vo->CD_DIST_OBSV}, {$vo->Cid}, 'start', now())";
            }
            return $this->Exec($this->sql);
        }
    }

    class WB_BRDGROUP_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_brdgroup";
        }

        public function SELECT($where = "1", $order = "GCODE")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "GCODE")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_BRDLIST_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_brdlist";
        }

        public function SELECT($where = "1", $order = "BCode DESC", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "BCode DESC", $limit = "")
        {
            return $this->SELECT($where, $order, $limit)[0];
        }
    }

    class WB_BRDLISTDETAIL_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_brdlistdetail";
        }

        public function SELECT($where = "1", $order = "RegDate DESC")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "RegDate DESC")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_BRDMENT_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_brdment";
        }

        public function SELECT($where = "1", $order = "AltCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "AltCode")
        {
            return $this->SELECT($where, $order)[0];
        }

    }

    class WB_BRDSEND_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_brdsend";
        }

		public function SELECT($where = "1", $order = "SendCode", $limit = 1000)
		{
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
            
            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
		}

        public function SELECT_SINGLE($where = "1", $order = "SendCode")
        {
            return $this->SELECT($where, $order)[0];
        }

        public function UPDATE($vo)
        {
            $this->sql = "UPDATE {$this->table} SET RegDate = now(), BStatus = 'start' WHERE CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}' and Parm4 = '{$vo->Parm4}'";
            return $this->Exec($this->sql);
        }

        public function FAIL_QUERY($idx)
        {
            $this->sql = "UPDATE {$this->table} SET BStatus = 'fail' WHERE SendCode = {$idx}";
            return $this->Exec($this->sql);
        }
	}
    #endregion
    #region DISPLAY DAO
    class WB_DISPLAY_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_display";
        }

        public function SELECT($where = "1", $order = "DisCode DESC", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "DisCode DESC", $limit = "")
        {
            return $this->SELECT($where, $order, $limit)[0];
        }

        public function TEST($where = "1", $order = "DisCode DESC", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            return $this->sql;
        }

        public function imageResize($name)
        {
            $info = getimagesize($name);
            $img_width = round($info[0]*0.5);
            $img_height = round($info[1]*0.5);

            $image = imagecreatefrompng($name);

            $new_image = imagecreatetruecolor($img_width, $img_height);
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $img_width, $img_height, $info[0], $info[1]);
            imagepng($new_image, $name);

            imagedestroy($image); 
            imagedestroy($new_image);
        }
    }

    class WB_DISSEND_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_dissend";
        }

        public function SELECT($where = "1", $order = "SendCode DESC")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "SendCode")
        {
            return $this->SELECT($where, $order)[0];
        }

        public function FAIL_QUERY($idx)
        {
            $this->sql = "UPDATE {$this->table} SET BStatus = 'fail' WHERE SendCode = {$idx}";
            return $this->Exec($this->sql);
        }
    }

    class WB_DISSTATUS_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_disstatus";
        }

        public function SELECT($where = "1", $order = "CD_DIST_OBSV")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }
    }
    #endregion
    #region GATE DAO
    class WB_GATECONTROL_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_gatecontrol";
        }

        public function SELECT($where = "1", $order = "GCtrCode DESC", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "GCtrCode")
        {
            return $this->SELECT($where, $order)[0];
        }

        public function FAIL_QUERY($idx)
        {
            $this->sql = "UPDATE {$this->table} SET GStatus = 'fail' WHERE GCtrCode = {$idx}";
            return $this->Exec($this->sql);
        }
    }

    class WB_GATESTATUS_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_gatestatus";
        }

        public function SELECT($where = "1", $order = "CD_DIST_OBSV")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "CD_DIST_OBSV")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_PARKCAR_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_parkcarhist";
        }

        public function SELECT($where = "GateDate LIKE CONCAT(DATE_FORMAT(NOW(), '%Y-%m-%d'), '%')", $order = "CD_DIST_OBSV", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function DELETE_NOW()
        {
            $date = date("Y-m-d", strtotime("-3 day"));
            $this->sql = "DELETE FROM {$this->table} WHERE GateDate <= '{$date}'";
            return $this->Exec($this->sql);
        }
    }

    class WB_PARKCARIMG_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_parkcarimg";
        }

        public function SELECT($idx)
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE idx = {$idx}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }
    }

    class WB_PARKGATEGROUP_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_parkgategroup";
        }

        public function SELECT($where = "1", $order = "ParkGroupCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }       
    }

    class WB_PARKSMSLIST_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_parksmslist";
        }  
    }

    class WB_PARKSMSMENT_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_parksmsment";

            $cnt = $this->SELECT_QUERY("SELECT * FROM wb_parksmsment");

            if( count($cnt) < 1 ) 
            {
                $ment = "[재난안전문자] 둔치주차장 침수위험! 신속 이동주차 요망! -재난안전대책본부-";
                $this->Exec("INSERT INTO {$this->table} (Title, Content) VALUES ('침수위험알림', '{$ment}')");
            }
        }   
    }
    #endregion
    #region SMS DAO
    class WB_SENDMESSAGE_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_sendmessage";
        }

        public function SELECT($where = "1", $order = "MsgCode DESC")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }
    }

    class WB_SMSLIST_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_smslist";
        }

        public function SELECT($where = "1", $order = "SCode DESC", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }
    }

    class WB_SMSUSER_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "wb_smsuser";
        }

        public function SELECT($where = "1", $order = "GCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} ";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "GCode")
        {
            return $this->SELECT($where, $order)[0];
        }
    }
    #endregion
    #region ALERT & CRITICAL DAO
    class WB_ISSUESTATUS_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_issuestatus";
        }

        public function SELECT($where = "1", $order = "idxCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "idxCode")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_ISUALERT_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_isualert";
        }

        public function SELECT($where = "1", $order = "AltCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "AltCode")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_ISUALERTGROUP_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_isualertgroup";
        }

        public function SELECT($where = "1", $order = "GCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "GCode")
        {
            return $this->SELECT($where, $order)[0];
        }
    }

    class WB_ISULIST_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_isulist";
        }

        public function SELECT($where = "1", $order = "IsuCode", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "IsuCode", $limit = "")
        {
            return $this->SELECT($where, $order, $limit)[0];
        }
    }

    class WB_ISUMENT_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; $this->conn = $dbconn;
            $this->table = "wb_isument";

            $vo = $this->SELECT_QUERY("SELECT * FROM {$this->table}");
            if( count($vo) < 1 ) $this->Exec("INSERT INTO wb_isument (MentCode) VALUES (1)");
            
        }

        public function SELECT()
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE MentCode = '1'";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo[0];
        }
    }
    #endregion
    #region LOG & USER & SATALLITE & AS DAO
    class WB_LOG_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_log";
        }

        public function SELECT($where = "1", $order = "idx DESC", $limit = "")
        {
            if( $limit != "" ) $limit = "LIMIT {$limit}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "idx DESC", $limit = "")
        {
            return $this->SELECT($where, $order, $limit)[0];
        }
    }

    class WB_USER_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_user";
        }

        public function SELECT($where = "1", $order = "idx")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "idx")
        {
            return $this->SELECT($where, $order)[0];
        } 
    }

    class WB_ASRECEIVED_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "wb_asreceived";
        }

        public function SELECT($where = "1", $order = "RCode")
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $order = "RCode")
        {
            return $this->SELECT($where, $order)[0];
        } 
    }

    class KMA_SATELLITE_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;
            $this->table = "kma_satellite";
        }

        public function SELECT($where = "RDR")
        {
            if($where == "RDR") $this->sql = "SELECT * FROM kma_satellite WHERE type = 'RDR' ORDER BY filename DESC LIMIT 1";
            else $this->sql = "SELECT * FROM kma_satellite WHERE type = 'SAT' ORDER BY SUBSTR(filename, INSTR(filename, concat(date1, date2)), 12) DESC LIMIT 1";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo[0];
        }
    }
    #endregion
    #region DATA
    class WB_DATA1MIN_DAO extends DAO_T
    {
        function __construct($type)
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;

            $date = date("Y");
            $this->table = "wb_{$type}1min_{$date}";
        }

        public function SELECT($where = "1", $subobsv = "", $order = "RegDate DESC", $limit = "100")
        {
            if( $subobsv != "" ) $where = "{$where} and Sub_OBSV = {$subobsv}";
            $this->sql = "SELECT *, IFNULL(DATE_FORMAT(RegDate, '%h'), SUBSTR(RegDate, 9, 2)) as idx FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "WB_DATA1MIN_VO");

            return $vo;
        }

        public function SELECT_SINGLE($where = "1", $subobsv = "", $order = "CD_DIST_OBSV")
        {
            return $this->SELECT($where, $subobsv, $order)[0];
        }
    }

    class WB_DATA1HOUR_DAO extends DAO_T
    {
        /// $type : Data Type에 따라 조회할 Table위치 변경
        /// SELECT : Day Data 및 기본 조회
        /// SELET_MONTH : Month (Graph) Data 조회
        /// SELET_YEAR : Year (Graph) Data 조회
        /// SELECT_SINGLE : 한개의 Row만 조회 할때 사용

        private $type = "";

        function __construct($type)
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;

            $this->type = $type;
            $this->table = "wb_{$type}1hour";
        }

        public function SELECT($where = "1", $subobsv = "", $order = "RegDate DESC", $limit = "100")
        {
            if( $this->type == "dplace" ) $where = "{$where} and Sub_OBSV = {$subobsv}";
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "WB_DATA1HOUR_VO");

            if( $vo != null ) return $vo;
            else return false;
        }

        public function SELECT_MONTH($where = "1", $subobsv = "", $order = "RegDate DESC", $limit = "100")
        {
            if( $this->type == "dplace" ) $where = "{$where} and Sub_OBSV = {$subobsv}";
            $this->sql = "SELECT *, day(RegDate) as idx FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "WB_DATA1HOUR_VO");

            if( $vo != null ) return $vo;
            else return false;
        }

        public function SELECT_YEAR($where = "1", $subobsv = "", $order = "RegDate DESC", $limit = "100")
        {
            if( $this->type == "rain" ) $select = "sum(DaySum) as Data";
            else $select = "max(DayMax) as Data";

            if( $this->type == "dplace" ) $where = "{$where} and Sub_OBSV = {$subobsv}";
            $this->sql = "SELECT {$select}, month(RegDate) as idx FROM {$this->table} WHERE {$where} GROUP BY idx ORDER BY {$order} LIMIT {$limit}";

            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "WB_DATA1HOUR_VO");

            if( $vo != null ) return $vo;
            else return false;
        }

        public function SELECT_SINGLE($where = "1", $subobsv = "", $order = "CD_DIST_OBSV")
        {
            $vo = $this->SELECT($where, $subobsv, $order);
            if( $vo ) return $vo[0];
        }
    }

    class WB_DATADIS_DAO extends DAO_T
    {
        private $type = "";

        function __construct($type)
        {
            $dbconn = new DBCONNECT; 
            $this->conn = $dbconn;

            $this->type = $type;
            $this->table = "wb_{$type}1hour";
        }

        public function SELECT($where = "1", $subobsv = "", $order = "RegDate DESC", $limit = "1")
        {
            if( $subobsv != "" ) $where = "{$where} and Sub_OBSV = {$subobsv}";

            $G = date("G", time()) + 1;

            if($this->type == "flood") $this->sql = "SELECT (SELECT IFNULL(MR{$G}, 0) from wb_water1hour WHERE {$where}) as flood_water, (SELECT IFNULL(MR{$G}, '000') from {$this->table} WHERE {$where}) as flood_flood";
            else $this->sql = "SELECT MR{$G} AS {$this->type}_now FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
            
            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "WB_DATADIS_VO");

            if( isset($vo[0]) ) return $vo[0];
            else return $vo;
        }
    }
    #endregion
?>
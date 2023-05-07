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

    function warningLog($message)
    {
        $dir_path = $_SERVER["DOCUMENT_ROOT"]."/log";
        if( !is_dir($dir_path) ) mkdir($dir_path, 0775, true);

        $path = $dir_path."/".date("Y-m").".txt";
        $fb = fopen($path, "a");
        fwrite($fb, "[".date("Y-m-d H:i:s")."] {$message}\r\n");
        fclose($fb);
    }

    class DAO_T
    {
        const STDLIMIT = 1000;

        public $conn;
        public $sql = "";
        public $table = "";
        public $selectKey = "";
        public $voName = "";

        //////////////////////////////////////////////////////////////////////////
        //////////                DB 기본 구동 로직                      //////////
        //////////////////////////////////////////////////////////////////////////
        function __construct($table, $selectKey, $voName = "")
        {
            $dbconn = new DBConnect;
            $this->conn = $dbconn->connect();

            $this->table = $table;
            $this->selectKey = $selectKey;

            if ($voName == "") $this->voName = "{$this->table}_vo";
            else $this->voName = $voName;
        }

        function EXECUTE() : void
        {
            try
            {
                $statement = $this->conn->query($this->sql);


                if( !$statement ) throw new PDOException("SQL 문구에 오류가 있습니다. ({$this->sql})");
            }
            catch(PDOException $e)
            {
                warningLog("{$e->getMessage()}({$this->sql})");
            }
        }

        function QUERY() : array
        {
            try
            {
                $statement = $this->conn->query($this->sql);
                $statement->setFetchMode(PDO::FETCH_CLASS, "{$this->voName}");
                $rtv = $statement->FetchAll();

                if( !$rtv ) throw new PDOException("SQL 문구에 오류가 있습니다. ({$this->sql})");
            }
            catch(PDOException $e)
            {
                warningLog("{$e->getMessage()}({$this->sql})");
                $rtv = Array();
            }

            return $rtv;
        }

        function PREPARE(string $sql, $vo) : array
        {
            try
            {
                $stmt = $this->conn->prepare($sql);
                foreach( $vo as $k => $v )
                {
                    switch(gettype($v))
                    {
                        case "double":
                        case "string":
                            $stmt->bindParam($k, $v, PDO::PARAM_STR);
                            break;
                            
                        case "integer":
                            $stmt->bindParam($k, $v, PDO::PARAM_INT);
                            break;
    
                        case "array":
                            for($i = 0; $i < count($v); $i++)
                            {
                                $stmt->bindValue($i+1, $v[$i]);
                            }
                            break;

                        default:
                            throw new PDOException("값이 잘못되었습니다.(".print_r($v).")");
                    }
                }
    
                $stmt->execute();
                $stmt->setFetchMode(PDO::FETCH_CLASS, "{$this->voName}");
                $vo = $stmt->fetchAll();
    
                return $vo;
            }
            catch(PDOException $e)
            {
                warningLog("{$e->getMessage()}({$sql} : ".gettype($v).")");
            }
        }

        //////////////////////////////////////////////////////////////////////////
        //////////                  기본 DML  로직                       //////////
        //////////////////////////////////////////////////////////////////////////
        public function SQL(string $sql) : array
        {
            $this->sql = $sql;

            if( strpos($sql, "SELECT") === 0 ) return $this->QUERY();
            else $this->EXECUTE();
        }

        public function SQLTOARRAY(string $sql) : array // VO Class를 갖지 않는 SQL문
        {
            $this->sql = $sql;

            try
            {
                $statement = $this->conn->query($this->sql);
                $statement->setFetchMode(PDO::FETCH_ASSOC);
                $rtv = $statement->FetchAll();

                if( !$rtv ) throw new PDOException("SQL 문구에 오류가 있습니다. ({$this->sql})");
            }
            catch(PDOException $e)
            {
                warningLog("{$e->getMessage()}({$this->sql})");
                $rtv = Array();
            }

            return $rtv;
        }

        public function SELECT(string $where = "1=1", string $order = null, int $limit = self::STDLIMIT, int $count = 0) : array
        {
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY ";

            $this->sql .= $order ?? "{$this->selectKey}";
            if ($limit > 0) $this->sql .= " LIMIT {$limit}";
            if ($count > 0) $this->sql .= ",{$count}";

            $vo = $this->QUERY();

            return $vo;
        }

        public function SINGLE(string $where = "1=1", string $order = null, int $limit = self::STDLIMIT, int $count = 0)
        {

            $voArray = $this->SELECT($where, $order, $limit, $count);
            $voName = "{$this->voName}";
            $cnt = 0;

            foreach( $voArray as $l )
            {
                if( $cnt++ === 0 ) $rtv = $l;
                if( $cnt >= 1 ) break;
            }

            if( $cnt === 0 ) $rtv = new $voName();

            return $rtv;
        }

        public function ARRAYTOSINGLE(array $voArray)
        {
            $voName = "{$this->voName}";
            $cnt = 0;

            foreach( $voArray as $l )
            {
                if( $cnt++ === 0 ) $rtv = $l;
                if( $cnt >= 1 ) break;
            }

            if( $cnt === 0 ) $rtv = new $voName();

            return $rtv;
        }

        public function INSERT($vo) : void
        {
            try
            {
                $select = "";
                $value = "";
    
                foreach($vo as $key => $val)
                {
                    if( $select !== "" ) $select .= ", ";
                    if( $value !== "" ) $value .= ", " ;
    
                    if( key($vo) === $key && $val === null ) { } // Key가 Auto_Increment로 입력하지 않아도 될 경우
                    else
                    {
                        $select .= "{$key}";
        
                        if( $val === null ) $value .= "NULL";
                        else if( gettype($val) == "integer" || gettype($val) == "double" ) $value .= "{$val}" ;
                        else if( gettype($val) == "string" ) $value .= "'{$val}'";
                        else throw new PDOException("INSERT VALUE 값이 잘못되었습니다. ({$vo->{key($vo)}})");
                    }
                }

                $this->sql = "INSERT INTO {$this->table}( {$select} ) VALUES ( {$value} )";
                $this->EXECUTE();
            }
            catch(PDOException $e)
            {
                warningLog($e->getMessage());
            }
        }

        public function UPDATE($vo) : void
        {
            try
            {
                $set = "";
                $keyword = key($vo);
    
                foreach( $vo as $key => $val )
                {
                    if( $val !== "" )
                    {
                        if( $keyword != $key ) $set .= ", ";
                        
                        if( $val === null ) $set .= "{$key} = NULL";
                        else if( gettype($val) == "integer" || gettype($val) == "double" ) $set .= "{$key} = {$val}";
                        else if( gettype($val) == "string" ) $set .= "{$key} = '{$val}'";
                        else throw new PDOException("UPDATE SET 입력값이 잘못되었습니다. ({$vo->{key($vo)}})");
                    }
                }
    
                $this->sql = "UPDATE {$this->table} SET {$set} WHERE {$keyword} = '{$vo->{$keyword}}'";
                $this->EXECUTE();
            }
            catch(PDOException $e)
            {
                warningLog($e->getMessage());
            }
        }

        public function DELETE($vo, $where = "") : void
        {
            try
            {
                if( $where === "" )
                {
                    $where .= key($vo);

                    if( gettype($vo->{key($vo)}) == "integer" ) $where .= " = {$vo->{key($vo)}}" ;
                    else if( gettype($vo->{key($vo)}) == "string" ) $where .= " = '{$vo->{key($vo)}}'" ;
                    else throw new PDOException("Delete Where절 조건이 설정되지 않았습니다 ({$vo->{key($vo)}})");
                }
    
                $this->sql = "DELETE FROM {$this->table} WHERE {$where}";
                $this->EXECUTE();
            }
            catch(PDOException $e)
            {
                warningLog($e->getMessage());
            }
        }
    }

    class DAO extends DAO_T
    {
        function __construct($table, $comlumn, $voName = "")
        {
            parent::__construct($table, $comlumn, $voName);
        }

        function UpdateCarCount($vo) : void
        {
            $this->sql = "UPDATE {$this->table} SET ";

            foreach($vo as $k => $v)
            {
                if (strpos($k, "JHFree") === false)
                {
                    if ($k != "JHAreaCode" || $k != "JHDate" || $k != "JHHour24")
                        $this->sql .= "{$k} = '{$v}', ";
    
                    if ($k == "JHHour24")
                        $this->sql .= "{$k} = '{$v}'";
                }
            }

            $this->sql .= " WHERE JHAreaCode = {$vo->JHAreaCode} AND JHDate = '{$vo->JHDate}'";

            $this->EXECUTE();
        }
    }

    class WB_PARKCAR_VO
    {
        public $idx;
        public $GateDate;
        public $GateSerial;
        public $CarNum;
        public $CarNum_Img;
        public $CarNum_Imgname;
        public $json;
    }

    //입/출 이미지 정보
    class WB_PARKCARIMG_VO
    {
        public $idx;
        public $CarNum_Img;
        public $CarNum_Imgname;
    }

    //차단기 제어 send 및 내역
    class WB_GATECONTROL_VO
    {
        public $GCtrCode;
        public $CD_DIST_OBSV;
        public $RegDate;
        public $Gate;
        public $GStatus;
    }

    //차단기 현재 상태 정보
    class WB_GATESTATUS_VO
    {
        public $CD_DIST_OBSV;
        public $RegDate;
        public $Gate;
    }

    class WB_PARKCARCNT_VO
    {
        public $ParkGroupCode;
        public $RegDate;
        public $MR0;
        public $MR1;
        public $MR2;
        public $MR3;
        public $MR4;
        public $MR5;
        public $MR6;
        public $MR7;
        public $MR8;
        public $MR9;
        public $MR10;
        public $MR11;
        public $MR12;
        public $MR13;
        public $MR14;
        public $MR15;
        public $MR16;
        public $MR17;
        public $MR18;
        public $MR19;
        public $MR20;
        public $MR21;
        public $MR22;
        public $MR23;
        public $DaySum;
    }

    class JHCAR
    {
        public $JHAreaCode;
        public $JHDate;
        public $JHHour1;
        public $JHHour2;
        public $JHHour3;
        public $JHHour4;
        public $JHHour5;
        public $JHHour6;
        public $JHHour7;
        public $JHHour8;
        public $JHHour9;
        public $JHHour10;
        public $JHHour11;
        public $JHHour12;
        public $JHHour13;
        public $JHHour14;
        public $JHHour15;
        public $JHHour16;
        public $JHHour17;
        public $JHHour18;
        public $JHHour19;
        public $JHHour20;
        public $JHHour21;
        public $JHHour22;
        public $JHHour23;
        public $JHHour24;
    }
?>
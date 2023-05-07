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

        function GetControlGate()
        {
            $this->sql = "SELECT * FROM `control` ORDER BY `idx` DESC";

            $statement = $this->conn->query($this->sql);
            $statement->setFetchMode(PDO::FETCH_CLASS, "VO");
            $rtv = $statement->FetchAll();

            return $rtv;
        }
    }

    $dao = new DAO;
    $vo = new VO;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>정보 보내기</title>
        <link rel="stylesheet" type="text/css" href="/css/include.css" />
        <link rel="stylesheet" type="text/css" href="/css/frame.css" />
        <style>
            .cs_datatable tr.active
            {
                background-color: #36b8f4;
                color:#fff;
            }
            .cs_datatable th 
            {
                background-color:#36b8f4;
            }
            .cs_datatable input
            {
                width:90%;
                height:25px;
                padding-left:15px;
                
            }

            .cs_datatable select
            {
                height:25px;
            }

            .cs_btn
            {
                background-color: #36b8f4;
            }
        </style>
    </head>
    <body>
        <div class="cs_frame">
            <div class="cs_btnBox" style="justify-content:space-between;align-items:flex-end;">
                <div style="font-size:13px;">* LIST 항목 및 아래 버튼을 클릭하면 정보가 TEXT에 채워집니다.</div>
                <div class="cs_btn" dType="lprList">LPR LIST 페이지</div>
            </div>

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th width="30%">URL</th>
                    <td><input type="text" name="url" value=""></td>
                </tr>
                <tr>
                    <th>actionType</th>
                    <td><input type="number" name="actionType" value=""></td>
                </tr>
                <tr>
                    <th>remoteIP</th>
                    <td><input type="text" name="remoteIP" value=""></td>
                </tr>
                <tr>
                    <th>eqpmID</th>
                    <td><input type="number" name="eqpmID" value=""></td>
                </tr>
                <tr>
                    <th width="30%">ID</th>
                    <td><input type="text" name="id" value=""></td>
                </tr>
                <tr>
                    <th width="30%">PW</th>
                    <td><input type="text" name="pw" value=""></td>
                </tr>
            </table>

            <div class="cs_btnBox" style="justify-content:space-between;align-items:flex-end;">
                <div class="cs_btn" dType="open">차단기 상태</div>
                <div class="cs_btn" dType="lock">차단기 열기 고정</div>
                <div class="cs_btn" dType="unlock">차단기 열림 고정 해제</div>
                <div class="cs_btn" dType="close">차단기 닫기</div>
                <div class="cs_btn" dType="send" style="background-color:#5c86b9">전송</div>
            </div>

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th width="30%">URL</th>
                    <th>TimeStemp</th>
                    <th>actionType</th>
                    <th>remoteIP</th>
                    <th>eqpmID</th>
                    <th>ID</th>
                    <th>PW</th>
                </tr>
                <?php
                    $vo = $dao->GetControlGate();
                    if( $vo )
                    {
                        foreach( $vo as $v )
                        {
                            echo "<tr class='trList{$v->idx}' onclick=\"btnEvent('trList',{$v->idx})\" style='cursor:default;'>";
                                echo "<td>{$v->url}</td>";
                                echo "<td>{$v->RegDate}</td>";
                                echo "<td>{$v->actionType}</td>";
                                echo "<td>{$v->remoteIP}</td>";
                                echo "<td>{$v->eqpmID}</td>";
                                echo "<td>{$v->id}</td>";
                                echo "<td>{$v->pw}</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
        </div>
        <script src="/js/jquery-1.9.1.js"></script>
        <script>
            $(document).ready((e) => 
            {
                /* 차량 입출차 내역 차량 번호 이미지 활성화 */
                $(document).on("click",".cs_btn",function()
                {
                    let type = $(this).attr("dType");
                    btnEvent("btn", type);
                })
            })

            function btnEvent(type, item)
            {
                if( type == "btn" )
                {
                    let c = "";

                    switch(item)
                    {
                        case "open" :
                            c = 1;
                            break;

                        case "lock" :
                            c = 3;
                            break;

                        case "unlock" :
                            c = 4;
                            break;

                        case "close" :
                            c = 2;
                            break;

                        case "lprList" :
                            window.location.href = "InOutList.php";
                            return;

                        case "send" :
                            let obj = new Object;

                            obj.url = $("input[name='url']").val();
                            obj.actionType = parseInt($("input[name='actionType']").val());
                            obj.remoteIP = $("input[name='remoteIP']").val();
                            obj.eqpmID = parseInt($("input[name='eqpmID']").val());
                            obj.id = $("input[name='id']").val();
                            obj.pw = $("input[name='pw']").val();

                            $.ajax(
                            {
                                url: `./server/send.php`,
                                dataType:"json",
                                type:"post", 
		                        data: JSON.stringify(obj),
                                async:true,
                                cache:false,
                                success: function(data) 
                                {
                                    alert(`${data.code}\r\n${data.msg}`);
                                    window.location.reload();
                                },
                                error:function(request,status,error)
                                {
                                    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                                }
                            })
                            return;
                    }

                    $("input[name='actionType']").val(c);
                }
                else
                {
                    let trList = document.querySelector(`.trList${item}`);

                    $("input[name='url']").val(trList.childNodes[0].innerText);
                    $("input[name='actionType']").val(trList.childNodes[2].innerText);
                    $("input[name='remoteIP']").val(trList.childNodes[3].innerText);
                    $("input[name='eqpmID']").val(trList.childNodes[4].innerText);
                    $("input[name='id']").val(trList.childNodes[5].innerText);
                    $("input[name='pw']").val(trList.childNodes[6].innerText);
                }
            }
        </script>
    </body>
</html>
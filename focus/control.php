<?php
    include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

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

    // API KEY 만들기 ( 사용자 IP + 날짜 이용 )
    // $apiKey = strtoupper(base64_encode("_".hash("sha1", $_SERVER["REMOTE_ADDR"].date("Y-m-d H:i"))));
?>

<!DOCTYPE html>
<html>
    <head>
        <title>정보 보내기~</title>
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
                    <th>ID</th>
                    <td><input type="text" name="id" value=""></td>
                </tr>
                <tr>
                    <th>TYPE</th>
                    <td><input type="text" name="type" value=""></td>
                </tr>
                <tr>
                    <th>CMD</th>
                    <td><input type="text" name="cmd" value=""></td>
                </tr>

                <tr>
                    <th>REQ-STAMP</th>
                    <td><input type="text" name="reqStamp" value="<?=date("Y-m-d H:i:s")?>"></td>
                </tr>
            </table>

            <div class="cs_btnBox" style="justify-content:space-between;align-items:flex-end;">
                <div class="cs_btn" dType="state">차단기 상태</div>
                <div class="cs_btn" dType="lock">차단기 열기(고정)</div>
                <div class="cs_btn" dType="open">차단기 열기</div>
                <div class="cs_btn" dType="close">차단기 닫기</div>
                <div class="cs_btn" dType="send" style="background-color:#5c86b9">전송</div>
            </div>

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th>ID</th>
                    <th>TYPE</th>
                    <th>CMD(STATUS)</th>
                    <th>REQ-STAMP</th>
                </tr>
                <?php
                    $vo = $dao->SELECT();
                    $cnt = 1;
                    if( $vo[0]->{key($vo[0])} )
                    {
                        foreach( $vo as $v )
                        {
                            echo "<tr class='trList{$cnt}' onclick=\"btnEvent('trList',{$cnt})\" style='cursor:default;'>";
                                echo "<input type='hidden' value='{$v->url}'>";
                                echo "<td>{$v->id}</td>";
                                echo "<td>{$v->type}</td>";
                                echo "<td>{$v->cmd}</td>";
                                echo "<td>{$v->reqStamp}</td>";
                            echo "</tr>";

                            $cnt++;
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
                    let t = "GATE-CTL";
                    let c = "";

                    switch(item)
                    {
                        case "state" :
                            t = "GATE-STATUS";
                            c = "STATUS";
                            break;

                        case "lock" :
                            c = "OPEN(LOCK)";
                            break;

                        case "open" :
                            c = "UP-AUTO";
                            break;

                        case "close" :
                            c = "DOWN-EMG"
                            break;

                        case "lprList" :
                            window.location.href = "lprList.php";
                            return;

                        case "send" :
                            let obj = new Object;

                            obj.url = $("input[name='url']").val();
                            obj.id = $("input[name='id']").val();
                            obj.type = $("input[name='type']").val();
                            obj.cmd = $("input[name='cmd']").val();
                            obj.reqStamp = $("input[name='reqStamp']").val();

                            $.ajax(
                            {
                                url: `./server/sendRest.php`,
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

                    $("input[name='type']").val(t);
                    $("input[name='cmd']").val(c);
                }
                else
                {
                    let trList = document.querySelector(`.trList${item}`);

                    $("input[name='url']").val(trList.childNodes[0].value);
                    $("input[name='id']").val(trList.childNodes[1].innerText);
                    $("input[name='type']").val(trList.childNodes[2].innerText);
                    $("input[name='cmd']").val(trList.childNodes[3].innerText);
                    $("input[name='reqStamp']").val(trList.childNodes[4].innerText);
                }
            }
        </script>
    </body>
</html>
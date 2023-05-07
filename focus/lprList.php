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

    class LPR_DAO extends DAO_T
    {
        function __construct()
        {
            $dbconn = new FOCUS_DBCONNECT;
            $this->conn = $dbconn;
            $this->table = "lpr";
        }

        public function SELECT($where = "1", $order = "idx DESC", $limit = 30)
		{
            $this->sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$order} LIMIT {$limit}";
            $result = $this->SQL($this->sql);
            $vo = $this->Read($result, "{$this->table}_vo");

            return $vo;
		}

        public function SELECT_SINGLE($where = "1", $order = "idx DESC", $limit = 30)
		{
            return $this->SELECT($where, $order, $limit)[0];
		}

	}    

    $dao = new LPR_DAO;
    $vo = new LPR_VO;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>LPR 정보 확인 리스트</title>
        <link rel="stylesheet" type="text/css" href="/css/include.css" />
        <link rel="stylesheet" type="text/css" href="/css/frame.css" />
        <style>
            body
            {
                overflow: auto;
            }

            .cs_datatable tr.active
            {
                background-color: #36b8f4;
                color:#fff;
            }
            .cs_datatable th 
            {
                background-color:#36b8f4;
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
                <div style="font-size:13px;">* CAR-NUM에 마우스를 올리면 이미지가 나옵니다. (들어온 데이터가 JSON 형식이 아니거나 불확실하면 들어온 전체 데이터를 보여줍니다)</div>
                <div class="cs_btn" onclick="controlPage()">차단기 제어 페이지</div>
            </div>

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th>ID</th>
                    <th>TYPE</th>
                    <th>POS</th>
                    <th>CAR-NUM</th>
                    <th>TIME-STAMP</th>
                </tr>
                <?php
                    $vo = $dao->SELECT();
                    if( $vo[0]->{key($vo[0])} )
                    {
                        foreach( $vo as $v )
                        {
                            echo "<tr>";
                                try
                                {
                                    $err = false;
                                    if( $v->id === null ) $err = true;
                                    if( $v->type === null ) $err = true;
                                    if( $v->pos === null ) $err = true;
                                    if( $v->carNum === null ) $err = true;
                                    if( $v->timeStamp === null ) $err = true;

                                    if( $err ) throw new Exception();

                                    echo "<td>{$v->id}</td>";
                                    echo "<td>{$v->type}</td>";
                                    echo "<td>{$v->pos}</td>";
                                    echo "<td class='cs_imgLink' data-num='{$v->idx}' style='cursor:default;'>{$v->carNum}</td>";
                                    echo "<td>{$v->timeStamp}</td>";
                                }
                                catch(Exception $e)
                                {
                                    echo "<td colspan='5'>{$v->retData}</td>";
                                }
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
            <div style="height:100px;"></div>
        </div>
        <script src="/js/jquery-1.9.1.js"></script>
        <script>
            $(document).ready((e) => 
            {
                /* 차량 입출차 내역 차량 번호 이미지 활성화 */
                $(document).on("mouseover",".cs_imgLink",function(e)
                {
                    let num = $(this).attr("data-num");
                    let pageYHeight = 0;

                    if(e.pageY >= 625) pageYHeight = 625;
                    else pageYHeight = e.pageY;
    
                    $("body").append("<div class='cs_imgBox'></div>");
                    $(".cs_imgBox")
                    // .css("width", "500px")
                    // .css("height", "500px")
                    .css("position", "absolute")
                    .css("overflow", "hidden")
                    .css("margin", "0px")
                    .css("padding", "0px")
                    .css('border','2px solid #5E60CD')
                    .css("top",pageYHeight - 150 + "px")
                    .css("left",e.pageX - 641 + "px")
                    .fadeIn("fast");
    
                    $.ajax(
                    {
                        url: `./server/imgView.php?type=img&idx=${num}`,
                        dataType:"html",
                        type:"GET", 
                        async:true,
                        cache:false,
                        success: function(data) 
                        {
                            $(".cs_imgBox").html(data);
                        },
                        error:function(request,status,error)
                        {
                            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                        }
                    })
                });
    
                $(document).on("mouseout",".cs_imgLink",function(){ $(".cs_imgBox").remove(); });

                $(document).on("click", ".cs_imgLink", function(e)
                {
                    let num = $(this).attr("data-num");
                    const textArea = document.createElement("textarea");
                    let txt = "";
                    textArea.style.position = "absolute";
                    textArea.style.top = "5000%";
                    textArea.style.border = "none";
                    textArea.style.color = "#fff";
                    textArea.style.outline = "none";
                    textArea.style.resize = "none";

                    $.ajax(
                    {
                        url: `./server/imgView.php?type=bin&idx=${num}`,
                        dataType:"json",
                        type:"GET", 
                        async:true,
                        cache:false,
                        success: function(data) 
                        {
                            txt = data.bin;
                        },
                        error:function(request,status,error)
                        {
                            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                        }
                    })

                    document.body.appendChild(textArea);
                    textArea.value = txt;
                    textArea.select();

                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                })
            })

            function controlPage()
            {
                window.location.href="control.php";
            }
        </script>
    </body>
</html>
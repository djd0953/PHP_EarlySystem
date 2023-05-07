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
                    <div class="cs_btn">CONTROL</div>
                </div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th>GateSerial</th>
                    <th>GateDate</th>
                    <th>EventType</th>
                    <th>CarNum</th>
                    <th>Path</th>
                </tr>
                <?php
                    include_once "db.php";
                    $dao = new DAO("wb_parkcarhist", "idx DESC", "WB_PARKCAR_VO");
                    $vo = $dao->SELECT();

                    if( $vo )
                    {
                        foreach( $vo as $v )
                        {
                            echo "<tr>";
                                try
                                {
                                    $err = false;
                                    if( $v->GateDate === null ) $err = true;
                                    if( $v->GateSerial === null ) $err = true;
                                    if( $v->CarNum === null ) $err = true;
                                    if( $v->CarNum_Imgname === null ) $err = true;

                                    if( $err ) throw new Exception();

                                    echo "<td>{$v->GateSerial}</td>";
                                    echo "<td>{$v->GateDate}</td>";
                                    echo "<td>".($v->CarNum_Img == '0' ? "IN" : "OUT")."</td>";
                                    echo "<td>{$v->CarNum}</td>";
                                    echo "<td>{$v->CarNum_Imgname}</td>";
                                }
                                catch(Exception $e)
                                {
                                    echo "<td colspan='5'>{$v->json}</td>";
                                }
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
            <div style='height:100px;'></div>
        </div>
        <script>
            document.querySelector(".cs_btn").addEventListener("click", () => 
            {
                window.location.href = "control.php";
            })
        </script>
    </body>
</html>
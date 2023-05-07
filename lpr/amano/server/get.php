<?php
    function warningLog($message)
    {
        $dir_path = $_SERVER["DOCUMENT_ROOT"]."/log";
        if( !is_dir($dir_path) ) mkdir($dir_path, 0775, true);

        $path = $dir_path."/".date("Y-m").".txt";
        $fb = fopen($path, "a");
        fwrite($fb, "[".date("Y-m-d H:i:s")."] {$message}\r\n");
        fclose($fb);
    }

    $data = file_get_contents('php://input');
    warningLog($data);
?>
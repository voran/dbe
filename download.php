<?php

$file['name'] = htmlspecialchars($_REQUEST['file'], ENT_QUOTES);

$path = "temp/";
$fullPath = $path . $file['name'];

if ($file['handle'] = fopen($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);

    switch ($ext) {
        case "csv" :
            header("Content-type: application/csv");
            break;

        case "xml" :
            header("Content-type: text/xml");
            break;

        case "sql" :
            header("Content-type: text/plain");
            break;
    }

    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\"");
    header("Content-length: $fsize");
    header("Cache-control: private");
    //use this to open files directly

    while (!feof($file['handle'])) {
        $buffer = fread($file['handle'], 2048);
        echo $buffer;
    }

    fclose($file['handle']);
    unlink($fullPath);
    //destroy file

}

exit;
?>
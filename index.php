<?php
$shortFile = 'links.json';
$links = file_exists($shortFile) ? json_decode(file_get_contents($shortFile), true) : [];

if(isset($_GET['id'])){
    $id = $_GET['id'];
    if(isset($links[$id]) && file_exists($links[$id])){
        $file = $links[$id];
        $filename = basename($file);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        echo "❌ Invalid or expired link!";
    }
} else {
    echo "❌ No file ID provided!";
}
?>



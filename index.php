<?php
$uploadDir = 'uploads/';
if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$shortFile = 'links.json';
$links = file_exists($shortFile) ? json_decode(file_get_contents($shortFile), true) : [];

// ======== Helper ========
function generateShortID($length=6){
    return substr(md5(time().rand()),0,$length);
}

// ======== Handle Download ========
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
        exit;
    }
}

// ======== Handle Upload ========
if(isset($_FILES['appFile'])){
    $file = $_FILES['appFile'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $shortID = generateShortID();
    $target = $uploadDir . $shortID . '.' . $ext;

    if(move_uploaded_file($file['tmp_name'], $target)){
        $links[$shortID] = $target;
        file_put_contents($shortFile, json_encode($links));
        $shortLink = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?id=$shortID";
        echo "<p>✅ Short Link Generated:</p>";
        echo "<p><a href='$shortLink' target='_blank'>$shortLink</a></p>";
    } else {
        echo "<p>❌ File upload failed!</p>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>App Short Link Generator</title>
<style>
body {font-family: Arial; background:#f4f4f4; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0;}
.container {background:#fff; padding:24px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:100%; max-width:400px;}
input[type="file"], button {width:100%; padding:10px; margin-bottom:12px; border-radius:8px;}
button {background:#06b6d4; color:#fff; border:none; cursor:pointer;}
button:hover {background:#0891b2;}
</style>
</head>
<body>
<div class="container">
<h2>Upload App & Generate Short Link</h2>
<form method="POST" enctype="multipart/form-data">
<input type="file" name="appFile" required>
<button type="submit">Generate Short Link</button>
</form>
</div>
</body>
</html>

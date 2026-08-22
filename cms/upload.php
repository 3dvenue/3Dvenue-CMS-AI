<?php
require_once('./auth.php');
// 保存先ディレクトリ（共通リソース）
$saveDir = '../common/img/';

// ディレクトリがなければ作成（権限チェックも兼ねて）
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

if (isset($_FILES['file'])) {
    // JSの formData.append('file', blob, '名前.webp') から名前を取得
    $fileName = $_FILES['file']['name'];
    $tempPath = $_FILES['file']['tmp_name'];
    $targetPath = $saveDir . $fileName;

    // 上書きだろうと何だろうと、そのまま保存（移動）
    if (move_uploaded_file($tempPath, $targetPath)) {
        echo "Success: {$fileName} saved to common/img/";
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Error: Upload failed.";
    }
}
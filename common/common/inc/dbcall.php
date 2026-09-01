<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
$db_file = __DIR__ . '/../../3d_venue_data.qox';
try {
    // 接続オプション（リトライ設定 5秒）
    $options = [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    $conn = new PDO("sqlite:$db_file");

} catch (PDOException $e) {
    // 接続エラーが出たら即座に表示して止める
    exit("DB接続エラー: " . $e->getMessage());
}
?>

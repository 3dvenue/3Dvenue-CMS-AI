<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pid = $_POST['pid'] ?? '';
    $table = $_POST['table'] ?? '';
    $column = $_POST['column'] ?? '';
    $data = $_POST['data'] ?? '';
    $sitename = $_POST['sitename'] ?? '';

	include_once('../common/inc/dbcall.php');
    
    if ($pid !== '' && $table !== '' && $column !== '') {
	$sql = "UPDATE $table SET $column = :data WHERE pid = :pid";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':data'       => $data,
        ':pid'      => $pid
        ]);
      exit;
    }

    if (($_POST['submit'] ?? '') === 'seo') {
        // SEOデータ
        $pagetitle = $_POST['pagetitle'] ?? '';
        $description = $_POST['description'] ?? '';
        $other = $_POST['other'] ?? '';
        $jsonld = $_POST['jsonld'] ?? '';
        $noidex = $_POST['noidex'] ?? 0;

        $sql = "UPDATE $table SET title = :title ,description = :description, other = :other ,jsonld = :jsonld ,robots = :robots WHERE pid = :pid";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':title' => $pagetitle,
            ':description' => $description,
            ':other' => $other,
            ':jsonld' => $jsonld,
            ':robots' => $noidex,
            ':pid'   => $pid
            ]);
      exit;
    }

    if (($_POST['submit'] ?? '') === 'sitename') {
        file_put_contents('../common/inc/sitename.txt', $sitename);
      exit;
    }

}   //if
  exit;

?>

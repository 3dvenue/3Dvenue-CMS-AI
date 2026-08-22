<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['object'])) {
	$obj = trim($_POST['object']);
	include_once('../common/inc/dbcall.php');

	$object = $conn->quote("%{$obj}%");
	$objects = [];
	$sql = "SELECT name FROM pages WHERE main LIKE {$object} OR css LIKE {$object} OR image LIKE {$object}";
	$stmt = $conn->query($sql);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	    $objects[] = [
	        'table' => 'pages',
	        'place'  => $row['name']
	    ];
	}

	$sql = "SELECT cname FROM contents WHERE dom LIKE {$object}";
	$stmt = $conn->query($sql);
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	    $objects[] = [
	        'table' => 'parts',
	        'place'  => $row['cname']
	    ];
	}

	$layout = file_get_contents('../common/layout/default.html');
	if (strpos($layout, $obj) !== false) {
	    $objects[] = [
	        'table' => 'templates',
	        'place' => 'default'
	    ];
	}

	$style = file_get_contents('../common/layout/default.css');
	if (strpos($style, $obj) !== false) {
	    $objects[] = [
	        'table' => 'templates.css',
	        'place' => 'default'
	    ];
	}

	exit(json_encode($objects));
}
?>

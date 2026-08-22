<?php
require_once('../auth.php');
$p = isset($_GET['p']) ? $_GET['p'] : 0;
if (!ctype_digit($p)) {$p = 0;}
$root = file_get_contents('../../common/inc/root.txt');
$main = @file_get_contents('./pages/'.$p.'.txt') ?: file_get_contents('./ai.txt');
$nav = file_get_contents('./nav.txt');
$color = file_get_contents('../common/css/color.css');
$style = file_get_contents('../common/layout/default.css');
$layout = file_get_contents('../common/layout/default.html');
$layout = str_replace('<v>main</v>', $main, $layout);
$layout = str_replace('<v>nav</v>',  $nav,  $layout);
$layout = str_replace('../common', $root . 'common', $layout);
$layout = str_replace('logo.webp', 'logo.webp?v='.time(), $layout);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>タイトル</title>
<style id="templateColor">
  <?=$color?>
</style>
<style>
main section{
  pointer-events: none;
}

main section *{
  user-select: none;
}

main.event section *{
  pointer-events:auto;
}

main.event section *:hover{
  outline:2px solid #666;
}

main.event section *.active{
  outline:2px solid #000;
  outline-offset: 4px;
}

  <?=$style?>
}
</style>
</head>
<body>
<?=$layout?>
</body>
</html>

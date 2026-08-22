<?php
session_start();
$url_slug = $_GET['slug'] ?? '/'; 
if($url_slug == '/'){
    $file = './common/inc/root.txt';
    if (file_exists($file)) {
        $roottext = file_get_contents($file);
        $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $url = str_replace('index.php', '', $url);
        if ($roottext !== $url) {
            file_put_contents($file, $url);
        }
    }
}

$root = file_get_contents('./common/inc/root.txt');
$map = file_get_contents('./common/inc/map.txt');
$nav = file_get_contents('./common/inc/nav.txt');
$sitename = file_get_contents('./common/inc/sitename.txt');
$color = file_get_contents('./common/layout/color.css');
$nav = str_replace('href="/', 'href="'. $root , $nav);


if (strpos($_SERVER['REQUEST_URI'], 'preview/') !== false) {
    $mapping[$url_slug] = $_SESSION['pageid'] ?? null;
}else{
    $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $map));
    $mapping = [];
    foreach ($lines as $line) {
        if (empty($line)) continue;
        list($id, $s) = explode(',', $line);
        $mapping[$s] = $id;
    }
}

if (!isset($mapping[$url_slug])) {
    header("HTTP/1.1 404 Not Found");
    exit('404 Not Found'); // あるいは自作の404ページを表示
}

$pid = $mapping[$url_slug];

include_once(__DIR__ . '/common/inc/dbcall.php');
$sql = "SELECT * FROM pages WHERE pid = ".$pid;
$stmt = $conn->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    header('Location: index.php');
    exit;
}

$name = $row["name"];
$slug = $row["slug"];
$title = $row["title"];
$description = $row["description"];
$main = $row["main"];
$css = $row["css"];
$js = $row["js"];
$public = $row["public"];
$sdate = $row["sdate"];
$image = $row["image"];
$jsonld = $row["jsonld"];
$other = $row["other"];
$noindex = $row["noindex"] ?? '0';

$main = str_replace('../common', $root . 'common', $main);
$css = str_replace('../common', $root . 'common', $css);
$other = str_replace('../common', $root . 'common', $other);

$nav = str_replace('p="'.$pid.'"','p="'.$pid.'" class="active"', $nav);

$meta_image = 'common/img/snsimage'.$pid.'.webp';
$meta_image = file_exists($meta_image) ? $meta_image : '';

$layout = file_get_contents('./common/layout/default.html');
$style = file_get_contents('./common/layout/default.css');
$layout = str_replace('../common', $root . 'common', $layout);
$layout = str_replace('<v>main</v>', $main, $layout);
$layout = str_replace('<v>nav</v>',  $nav,  $layout);


$mp3 = '';

if(strpos($main, 'class="audio"') !== false){
    $mp3 = file_get_contents('./common/inc/mp3.txt');
    $mp3 = str_replace('./common', $root . 'common', $mp3);
}

$lng = trim(file_get_contents('./common/inc/lang.txt'));
?>
<!DOCTYPE html>
<html lang="<?=$lng?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="generator" content="3Dvenue-CMS">
<?php if ($noindex == "1") { ?>
<meta name="robots" content="noindex,nofollow">
<?php } ?>
<meta name="description" content="<?=$description?>">
<title><?=$title?></title>
<?=$other?>
<meta property="og:title" content="<?=$title?>">
<meta property="og:description" content="<?=$description?>">
<meta property="og:url" content="<?=$root?><?=ltrim($url_slug, '/')?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?=$sitename?>">
<meta property="og:locale" content="ja_JP">
<?php if (!empty($meta_image)) { ?>
<meta property="og:image" content="<?=$root?><?=$meta_image?>">
<meta property="og:image:type" content="image/webp">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<?php } ?>
<link rel="icon" href="/favicon.ico">
<!-- LD_START -->
<?=$jsonld?>
<!-- LD_END -->
<style type="text/css">
<?=$style?>
<?=$color?>
<?=$css?>
</style>
</head>
<body>
<?=$layout?>
<?=$mp3?>
<?php 
if(strpos($main, 'class="glb"') !== false){
?>
<script>
document.querySelectorAll('.glbbox figure').forEach(function(el){
    el.addEventListener('click', function(){
        let name = this.dataset.name;
        let model = '<?=$root?>common/glb/' + name + '.glb';
        let win = window.open('<?=$root?>view/', 'glb', 'width=900,height=700');

        setTimeout(function(){
                win.loadGLTFModel(model);
        },500);
    });
});
</script>
<?php } ?>
<?=$js?>
</body>
</html>
<?php
$logfile = './common/log/'.date('Y-m',time()).'.txt';
$ip = $_SERVER['REMOTE_ADDR'];
$datetime = date('Y-m-d H:i:s',time());
$log = $datetime.','.$url_slug.','.$ip."\n";
$handle = fopen($logfile,'a');
fwrite($handle,$log);
fclose($handle);
?>

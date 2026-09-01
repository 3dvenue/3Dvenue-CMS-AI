<?php
require_once('auth.php');

$folder = __DIR__ . "/ai/pages/";
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

if (isset($_FILES['file'])) {
    if ($_FILES['file']['size'] > 0) {
        $fileName   = $_FILES['file']['name'];
        $tempPath   = $_FILES['file']['tmp_name'];
        $targetPath = '../common/img/' . $fileName;
        if (move_uploaded_file($tempPath, $targetPath)) {
            echo "Success: {$fileName} saved.";
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo "Error: Upload failed.";
        }
    exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submit = $_POST['submit'] ?? '';
    if ($submit == 'contentchange') {
        $p = $_POST['data'];
        $src = __DIR__ . "/ai/pages/".$p."/";
            if (is_dir($src)) {
                $files = glob($src . '*.txt');
                $files = array_map('basename', $files);
                rsort($files);
                echo json_encode($files);
            } else {
                echo '';
            }
        exit;
    }

    if ($submit == 'setkey') {
        $ai = trim($_POST['ai']);
        $api = trim($_POST['api']);
        $_SESSION[$ai] = $api;
        echo $submit;
    exit;
    }

    if ($submit == 'savePage') {
        $root = file_get_contents('../common/inc/root.txt');
        $p = $_POST['p'];
        $html = trim($_POST['html']);
        $html = str_replace($root . 'common/', '../common/', $html);
        $src = __DIR__ . "/ai/pages/".$p.".txt";
        file_put_contents($src,$html);
        echo 'ok';
    exit;
    }

    if ($submit == 'applyColor') {
        $filename = trim($_POST['filename']);
        $src = __DIR__ . "/common/css/archive/$filename";
            if (is_file($src)) {
                $css = file_get_contents($src);
                file_put_contents(__DIR__ . '/common/css/color.css', $css);
                echo 'ok';
            }
    exit;
    }

    if ($submit == 'delete') {
        $filename = trim($_POST['filename']);
        $src = __DIR__ . "/common/css/archive/$filename";
            if (is_file($src)) {
                unlink($src);
                echo 'ok';
            }
    exit;
    }

    if ($submit == 'changeList') {
        $file = trim($_POST['file']);
        $p = trim($_POST['p']);
        $src = __DIR__ . "/ai/pages/".$p."/$file";
        if (is_file($src)) {
            $html = file_get_contents($src);
            file_put_contents(__DIR__ . '/ai/pages/'.$p.'.txt', $html);
            echo 'ok';
        }
    exit;
    }

    if ($submit == 'delList') {
        $file = trim($_POST['file']);
        $p = trim($_POST['p']);
        $src = __DIR__ . "/ai/pages/".$p."/$file";
        if (is_file($src)) {
            unlink($src);
            echo 'ok';
        }
    exit;
    }

    if ($submit == 'brandColor') {
        $brandColor = json_decode($_POST['brandColor'] ?? '[]', true);
        file_put_contents(__DIR__ . '/ai/ccolor.txt', json_encode($brandColor, JSON_UNESCAPED_UNICODE));
        echo 'brandColor';
    }

    if($submit == 'makePage'){
        $index = $_POST['index'] ?? '1';
        $name = $_POST['name'] ?? '';
        $desc = $_POST['desc'] ?? '';
        $page = '<section><div class="inner"><h2>'.$name.'</h2><div class="text">'.$desc.'</div></div></section>';
        if ($page !== '') {
            file_put_contents(__DIR__ . '/ai/pages/'.$index.'.txt', $page);
        }
    }

    if ($submit == 'makeNavi') {
        $json = $_POST['json'] ?? [];
        file_put_contents(__DIR__ . '/ai/nav.json', $json);
    }

    if ($submit == 'setting') {
        $conditions = $_POST['conditions'] ?? [];
        $txt = '';
        foreach ($conditions as $key => $val) {
            $txt .= "$key:$val\n";
        }
        file_put_contents(__DIR__ . '/ai/setting.txt', $txt);
    }

    if ($submit == 'change') {
        $temp = trim($_POST['temp'] ?? 'default');
        copy('common/layout/'.$temp.'.html', 'common/layout/default.html');
        copy('common/layout/'.$temp.'.css', 'common/layout/default.css');
        echo 'change';
    }

    if($submit == 'apply') {

        $map = $_POST['map'];
        $maptext = __DIR__ . '/../common/inc/map.txt';
        file_put_contents($maptext, $map);
        echo 'map';
        
        include_once('../common/inc/dbcall.php');

            $jsonFile = 'ai/nav.json';

        if (!file_exists($jsonFile)) {
            header('HTTP/1.1 403 Forbidden');
            echo 'This operation is not allowed yet.';
            exit;
        }


            $jsonString = file_get_contents($jsonFile);
            $navData = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                header('HTTP/1.1 403 Forbidden');
                echo 'Parss Error';
                exit;
            }

            $conn->beginTransaction();
            $conn->exec("DELETE FROM pages");
            $stmt = $conn->prepare("INSERT INTO pages (`pid`, `name`, `title`, `description`, `main`) VALUES (:index, :name, :title, :description, :main)");

            foreach ($navData as $item) {
                $index = $item['index'];
                $name = $item['name'];
                $description = $item['desc'];

                $textFile = __DIR__ . '/ai/pages/' . $index . '.txt';
                $mainContent = '';

                if (file_exists($textFile)) {
                    $mainContent = file_get_contents($textFile);
                }

                $stmt->execute([
                    ':index'       => $index,
                    ':name'        => $name,
                    ':title'       => $name,
                    ':description' => $description,
                    ':main'        => $mainContent
                ]);
            }
            $conn->commit();

            //navfile copy
            $source = __DIR__ . '/ai/nav.txt';
            $dest1 = __DIR__ . '/../common/inc/nav.txt';
            $dest2 = __DIR__ . '/common/nav.txt';

            if (file_exists($source)) {
                $html = file_get_contents($source);
                $html = str_replace('data-slug', 'href', $html);
                file_put_contents($dest1, $html);
                file_put_contents($dest2, $html);
            }
            echo 'success';
            exit;
        }

    if ($submit == 'allRest') {
        // $target = __DIR__ . '/ai/setting.txt';
        $dir = __DIR__ . '/ai/';
        if (file_exists($target = $dir.'setting.txt')) unlink($target);
        if (file_exists($target = $dir.'pages.json')) unlink($target);
        if (file_exists($target = $dir.'nav.json')) unlink($target);

        file_put_contents(__DIR__ . '/ai/ccolor.txt',
            '[{"color":"#000000","per":"100"},{"color":"#000000","per":"0"},{"color":"#000000","per":"0"}]'
        );

        file_put_contents(__DIR__ . '/ai/nav.txt',
            '<ul class="nav0"><li p="index"><a data-slug="/">HOME</a></li></ul>'
        );

        // page archive delete
        if (is_dir($target = __DIR__ . '/ai/pages')) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
            rmdir($target);
        }
        // color archive delete
        if (is_dir($target = __DIR__ . '/common/css/archive')) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
            rmdir($target);
        }

        //basic color reset
        $basic = __DIR__ . '/common/css/basiccolor.css';
        $target = __DIR__ . '/common/css/color.css';
        if (file_exists($basic)) copy($basic, $target);

            echo '削除完了';
    }

    exit;
}


$pages = @json_decode(@file_get_contents(__DIR__ . '/ai/pages.json'), true);
$pcheck = $pages ? "" : "no";

$navi = @json_decode(@file_get_contents(__DIR__ . '/ai/nav.json'), true);
$ncheck = $navi ? "" : "nonavi";

$folderCount = count(glob(__DIR__ . '/ai/pages/*', GLOB_ONLYDIR));
$tcheck = $folderCount > 0 ? "" : "nofile";

$navcheck = file_exists(__DIR__ . '/ai/nav.txt') ? 'on' : '';

$templates = glob("common/layout/*.html", GLOB_BRACE);

$brandcolors = @json_decode(@file_get_contents(__DIR__ . '/ai/ccolor.txt'), true);

$bcolor1 = $brandcolors[0]['color'] ?? '#000000';
$per1    = $brandcolors[0]['per']   ?? 100;
$bcolor2 = $brandcolors[1]['color'] ?? '#000000';
$per2    = $brandcolors[1]['per']   ?? 0;
$bcolor3 = $brandcolors[2]['color'] ?? '#000000';
$per3    = $brandcolors[2]['per']   ?? 0;

$folder = __DIR__ . "/common/css/archive/";
$files = is_dir($folder) ? glob($folder . '*.txt') : [];
$fileCount = count($files);
rsort($files);

$settingPath = __DIR__ . '/ai/setting.txt';
$settingText = file_exists($settingPath) ? file_get_contents($settingPath) : '';

$unknown = "";

$chatgpt = $_SESSION['chatgpt'] ?? '';
$gemini = $_SESSION['gemini'] ?? '';
$claude = $_SESSION['claude'] ?? '';

if(!$chatgpt && !$gemini && !$claude){
    $unknown = "unknown";
}
include_once('./lang.php');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    html,body{
        background:#f5f5f5;
        margin:0;
        padding:0;
        color:#222;
        width:100%;
        height:100%;
    }

    body *{
        font-family: sans-serif;
        box-sizing:border-box;
    }

    .inner{
        width:100%;
        max-width:1240px;
        margin:0 auto;
        padding:0 30px;
    }

    header{
        padding:10px 0;
        height:59px;
        background:#333;
        position:relative;
    }

    header .inner{
        align-items: center;
        display: flex;
        justify-content: space-between;
    }

    header #aiheader{
        display: flex;
    }

    header h1{
        margin:0;
        padding:0;
        font-size:24px;
        color:#EEF;
        width:180px;
        border-right:1px solid #fff6;
    }

    header #aiselect{
        display: flex;
        align-items: center;
        gap:10px;
    }

    header #aiselect .title{
        margin-right:20px;
    }

    header #aiselect .button{
        display: flex;
        align-items: center;
        font-size:13px;
        border:1px solid #FFF3;
        border-radius: 5px;
        padding:0 20px;
        height:36px;
        background:#FFF1;
        cursor: pointer;
        /*font-weight: 600;*/
    }

   header #aiselect .button:hover{
        background:#FFF3;
        border-color:#FFF6;
   }

   header #aiselect .button:not(.c0),
   header #aiselect .button.select{
        background:#334EB8;
        color:#FFF;
   }

    header span{
        color:#FFF;
    }

    header #apikey{
        height:0;
        background:#FFF;
        position: absolute;
        top:100%;
        left:0;
        width:100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap:20px;
        border:1px solid #ddd;
        border-radius: 0 0 5px 5px;
        overflow: hidden;
        transition-duration: 0.5s;
    }

    header #apikey.active{
        height:50px;
    }

    header #apikey span{
        display: flex;
        color:#333;
        font-size:16px;
        font-weight: 700;
    }

   header #apikey span:after{
    content:'API-key';
    margin-left:0.2em;
   }

    header #apikey input{
        width:450px;
        border:1px solid #ccc;
        padding:5px 10px;
        border-radius: 5px;
    }

    header #apikey button{
        border:1px solid #334EB8;
        color:#334EB8;
        padding:5px 20px;
        border-radius: 5px;
        background:#FFF;
        cursor: pointer;
    }

    header button#apply{
      background-color: #ffffff;
      color: #222;
      border: 1px solid #666;
      border-radius: 4px;
      padding: 6px 12px;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    #apply:hover {
      background-color: #344EB4;
      color: #fff;
      border-color: #222;
      outline: 2px solid #fff;
    }

    #aiprompt .inner{
        padding:20px 30px;
    }

    #aiprompt h2{
        font-size:18px;
        padding:0 0 10px;
        margin:0;
    }

    #aiprompt h2 span{
        font-weight: 500;
        font-size:13px;
        margin-left:20px;
    }

    main{
        padding:00;
        height:calc(100vh - 140px);
        user-select: none;
    }

    main.unknown{
        background: #999 !important;
        pointer-events: none;
    }

    #aiprompt.unknown,
    main.unknown *{
        color:#999;
        /*background:#ccc;*/
    }

    main.unknown iframe{
        opacity:0.5;
    }

    main h3{
        color:#1F4E79;
    }

    main .inner{
        height:100%;
    }


    main #aibox{
        display: flex;
        gap:40px;
        height:calc(100% - 50px);
        width:100%;
    }

/* left
---------------------------------------------*/

    #left{
        width:50%;
        padding:0 0 20px;
        position: relative;
    }

    #left .close{
        position: absolute;
        top:70px;
        right:10px;
        display: flex;
        align-items: center;
        justify-content:center;
        font-size:20px;
        font-weight:700;
        cursor: pointer;
        width:30px;
        height:30px;
        display: none;
        line-height: 1.0;
        z-index:100;
    }

    #left .btnBox button{
        padding:7px 20px;
        border:1px solid #ccc;
        background:#F0F0F0;
        border-radius:5px;
        cursor: pointer;
    }


    #left .btnBox button:hover{
        background:#ccc;
        color:#FFF;       
    }

    #menu{
        padding-top:40px;
        display:grid;
        grid-template-columns:repeat(3,1fr);
        grid-template-rows:repeat(2,1fr);
        gap:20px;
    }

    #selectAi{
        margin:0;
        padding:7px 15px 10px;
        border-radius:10px;
        border:1px solid #DDD;
        justify-content:center;
        align-items: center;
        gap:20px;
        background:#FFF;
        display: none;
    }

    #left.form #selectAi{        
        display: flex;
    }

    #left.content #selectAi,
    #left.logosetup #selectAi{
        display:none;
    }

    #left.content .close,
    #left.logosetup .close{
        top:30px;
    }

    #left.form #menu{
        display:none;
    }

    #left  div.prompt{
        display: none;
    }

    #left.form  div.prompt{
        padding:5px;
        border-radius:10px;
        height:100%;
    }

    #left.form .close{
        display:flex;
    }

    #menu > div{
        display:flex;
        justify-content:center;
        align-items:center;
        min-height:120px;
        padding:20px;
        border:1px solid #dddddd;
        border-radius:8px;
        background:#ffffff;
        cursor:pointer;
        transition:.2s;
        text-align:center;
        font-size:16px;
    }

    #menu.no #template,
    #menu.no #siteMenu,
    #menu.no #navi,
    #menu.no #page,
    #menu.no #content{
        pointer-events: none;
        background:#ccc;
        color:#FFF;
        opacity:0.5;
    }

    #menu.nonavi #page,
    #menu.nonavi #content{
        pointer-events: none;
        background:#ccc;
        color:#FFF;
        opacity:0.5;
    }

    #menu.nofile #content{
        pointer-events: none;
        background:#ccc;
        color:#FFF;
        opacity:0.5;
    }


    #menu > div:hover{
        border-color:#4a8cff;
        box-shadow:0 4px 12px rgba(0,0,0,.08);
    }

    #left #selectAi input{
        margin-top:8px;
        padding:12px;
        border:1px solid #bbb;
        border-radius:6px
    }

    #left #selectAi label{
        display:block;
        font-size:12px;
    }

    #page-structure{
        display:none;
    }

    #left.content #section,
    #left.logosetup #logosetup,
    #left.templateMenu #templateMenu,
    #left.page #setting,
    #left.page-structure #page-structure,
    #left.siteMenu #siteMenu{
        display:block;
    }

    #left.logosetup #logosetup label#cinput,
    #left.templateMenu #templateMenu label,
    #left.form #page-structure label{
        display:flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom:15px;
    }

    #left.logosetup #logosetup label#cinput span,
    #left.templateMenu #templateMenu label span,
    #left.form #page-structure label span{
        margin-right:10px;
        display:inline-block;
        width:150px;
    }

    #left.logosetup #logosetup label#cinput input,
    #left.templateMenu #templateMenu label input,
    #left.form #page-structure label input{
        padding:5px 10px;
        border-radius:5px;
        border:1px solid #ccc;
        outline:none;
        width:calc(100% - 200px);
        height:auto;
    }

    #section #html{
        max-height:calc(100% - 100px);
        overflow-y: auto;
    }

    #section textarea#sectionedit{
        width: 100%;
        field-sizing: content;
        min-height:100px;
        max-height: 100%;
        padding: 10px 20px;
        border: 1px solid #D3E3FC;
        line-height: 1.2;
        resize: none;
        font-size: 14px;
        font-family: Consolas;
        background: #303841;
        color: #EEF;
        tab-size: 4;
        border-radius: 10px;
    }

    #left.logosetup #logosetup label#cinput input#corporatecolor{
        height: revert;
        padding: revert-layer;
        width: 50px;
        margin-left:30px;
    }

    #left #content-archive{
        margin-top:10px;
    }

    #left #content-list{
        background: #FFF;
        padding:0 20px;
        margin:0;
        list-style: none;
        border-radius: 10px;
    }

    #left #content-list li{
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin:0;
        cursor: pointer;
    }

    #left #content-list li p{
        margin:0;
        padding:10px;
        width:calc(100% - 40px);
    }

    #left #content-list li span{
        display: inline-block;
        padding:10px;
        font-weight: 700;
        color:#C00;
    }

    #left #templateMenu #colorForm{
        margin:20px 0;
    }

    #left.form .btnBox{
        padding-top:10px;
        text-align: right;
    }

    #left #colorArchive{
        background: #FFF;
        padding:20px;
        border-radius: 10px;
    }

    #left #colorArchive h4{
        margin:10px 0 0;
    }

    #left #colorArchive ul{
        padding:0;
        margin:0;
    }

    #left #colorArchive li{
        padding:0.3em 1em;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    #left #colorArchive li:hover{
        background:#eee;
    }

    #left #colorArchive li span.filename{
        display: inline-block;
    }

    #left #colorArchive li span.delete{
        font-weight: 700;
        color:#C00;
    }

    #allclear{
        position:fixed;
        bottom:0;
        left:0;
        display: flex;
        align-items: center;
        gap:20px;
        height:50px;
        padding-left:20px;
        transition-duration: .5s;
    }

    #allclear img{
        cursor: pointer;        
        transition-duration: .5s;
        opacity:0.8;
    }

    #allclear img:hover{
        filter: drop-shadow(0 0 3px #778);
    }

    #allclear button{
        transition-duration: .5s;
        opacity: 0;
        transform-origin: center left;
        transform:scale(0.0,1.0);
        border:none;
        background: #999;
        font-weight: 700;
        font-size:12px;
        color:#FFF;
        padding:5px 20px 7px;
        border-radius: 7px;
        cursor: pointer;
        pointer-events: none;
    }

    #allclear.open button{
        opacity: 1;
        transform:scale(1.0,1.0);
        pointer-events: auto;
    }

    #allclear button:hover{
        outline:1px solid #333;
        background:#eee;
        color:#333;
    }

/*　right
---------------------------------------------*/

    #right{
        width:50%;
        background:#FFF;
        border:1px solid #ccc;
        padding:0px;
        overflow: hidden;
        border-radius: 5px;
    }

    #right #text{
        width:100%;
        height:100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

   #right #text li{
    padding:20px 0;
   }

   #right iframe{
    width:200%;
    height:200%;
    transform: scale(0.5);
    transform-origin: top left;
    border:none;
    border-radius: 10px;
   }

    #send{
        padding:5px 20px;
        border:0;
        border-radius:6px;
        cursor:pointer;
        font-size:14px;
        background:#EEF;
        border:1px solid #ccc;
    }

    #siteMenu{
        display:none;
    }

    #loading{
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:10000;
        background:#0006;
        color:#FFF;
        text-shadow: 0 0 10px #000;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap:20px;
        animation:loadingBlur 10s ease-in-out infinite;
        transform: scale(.02);
        transform-origin: center;
        opacity:0;
        animation-duration: 5s;
        pointer-events: none;
    }

    #loading.active{
        transform: scale(1.0);
        opacity:1.0;
    }


    #loading h3{
        margin:0;
        font-size: 30px;
        font-weight: 700;
    }

    #loading p{
        margin:0;
        font-size:20px;
    }

    .svg-wrap{
        width:100px;
        height:100px;
    }


    #loading svg .steam{
    /*#loading.active svg .steam{*/
        animation: steam-rise 3.5s ease-in-out infinite;
        transform-origin: center;
    }

    #loading svg .steam:nth-of-type(odd) {
        animation-delay: 1.2s;
    }

    @keyframes steam-rise {
      0%  { transform: scale(1.0) translateX(0px);opacity: 0.9;filter: blur(10px);}
      50% { transform: scale(0.85) translateX(2px);opacity: 0.3;filter: blur(25px);}
      100%{ transform: scale(1.0) translateX(0px);opacity: 0.9;filter: blur(10px);}
    }


    @keyframes loadingBlur{
        0%{
            backdrop-filter:blur(0);
            -webkit-backdrop-filter:blur(0);
        }
        50%{
            backdrop-filter:blur(8px);
            -webkit-backdrop-filter:blur(8px);
        }
        100%{
            backdrop-filter:blur(0);
            -webkit-backdrop-filter:blur(0);
        }
    }

/* memo
---------------------------------------------*/
.memo{
    font-size:14px;
    margin:10px 0;
    padding:10px 20px;
    background:#FFF;
    border:1px solid #ddd;
    border-radius: 5px;
    line-height:1.6;
}

/* navi-list
-----------------------------------*/
#navi-list{
    border-radius: 5px;
    padding:20px;
    margin:0;
    list-style: none;
    background:#FFF;
    border:1px solid #ddd;
    height:calc(100% - 250px);
    overflow-y:auto;
}

#navi-list li{
    cursor: pointer;
    margin-bottom:5px;
}

#navi-list li:hover{
    background:#f0f0f0;
}

#navi-list .desc{
    display: none;
    padding:10px;
    background:#FFF;
    border-radius:5px;
    font-size:13px;
    line-height:1.6;
}

#navi-list .desc.open{
    display:block;
}

/* basic
---------------------------------------------*/
#basic label{
    display: flex;
    margin-bottom:10px;
}

#basic label span{
    display:block;
    font-size:14px;
    width:90px;
}

#basic label textarea,
#basic label input{
    padding:5px 10px;
    margin-left:10px;
    border-radius: 5px;
    border:1px solid #CCC;
    width:calc(100% - 100px);
}

#basic label textarea{
    field-sizing: content;
}

#structures{
    background:#FFF;
    border:1px solid #CCC;
    border-radius: 5px;
    padding:5px 10px;
    font-size:14px;
    line-height: 1.75;
    white-space: pre-wrap;
}

/* templates
--------------------------------*/
#templates{
    display: grid;
    grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
    gap:20px;
    list-style: none;
    padding:0;
    margin:0 0 20px;
}

#templates li{
    display: flex;
    justify-content: center;
    align-items: center;
    height:60px;
    background:#FFF;
    border:1px solid #ddd;
    border-radius: 5px;
    user-select: none;
    cursor: pointer;
    outline: none;
}

#templates li.default{
    display: none;
}

#templates li:hover{
        border-color:#4a8cff;
        box-shadow:0 4px 12px rgba(0,0,0,.08);
}

/*　logo-upload
---------------------------------------------*/
#logobox{
    display: flex;
    justify-content: space-between;
    position: relative;
    border-bottom:1px dotted #CCC;
}

#logobox #logopreview{
    width:250px;
    height:200px;
    padding:40px 20px;
    display:flex;
    align-items:center;
    justify-content:center;
}

#logobox .btnBox{
    position: absolute;
    bottom:30px;
    right:0;
}


#logobox #logopreview img{
    width:calc(100% - 40px);
    height:calc(100% - 40px);
    object-fit: contain;
}


#logo-upload{
    display:flex;
    align-items:center;
    justify-content:center;
    width:250px;
    height:250px;
    padding:20px;
    border:1px dashed #9eb7e8;
    border-radius:10px;
    background:#ffffff;
    box-sizing:border-box;
    cursor:pointer;
    margin:0 20px 30px;
    position: relative;
}

#logo-upload:hover{
    border-color:#3366cc;
    background:#f7f9ff;
}

#logo-upload input{
    display:none;
}

#logo-upload .empty{
    text-align:center;
    color:#444;
}

#logo-upload .empty .icon{
    display:block;
    width:50px;
    height:50px;
    margin:0 auto 10px;
    opacity:0.75;
}

#logo-upload .empty p{
    margin:0 0 8px;
    font-size:13px;
    font-weight:bold;
}

#logo-upload .empty span{
    display:block;
    margin-bottom:10px;
    font-size:12px;
    color:#777;
}

#logo-upload .empty b{
    display:inline-block;
    min-width:120px;
    padding:8px 18px;
    border-radius:4px;
    background:#2456c4;
    color:#ffffff;
    font-size:12px;
    font-weight:normal;
    box-sizing:border-box;
}

#logo-upload:hover .empty b{
    background:#17449f;
}

#logo-upload #preview{
    position: absolute;
    top:20px;
    left:20px;
    width:calc(100% - 40px);
    height:calc(100% - 40px);
    background-position: center;
    background-repeat:no-repeat;
    background-size:contain;
    pointer-events: none;

}

#logo-upload:hover #preview{
    opacity:0.05;
}

/* colorBox
---------------------------------------------*/
#colorBox{
    display: flex;
    padding-left:20px;
}

#colorBox label{
    margin-right:5px;
}

#colorBox label.bnumber{
    margin-right:20px;
}

#colorBox input{
    margin-right:5px;
    height:30px;
    border-radius:5px;
    border:1px solid #CCC;
}

#colorBox label.bnumber input{
    width:50px;
    text-align: right;
    margin-right:5px;
    padding: 0 10px;
}

#colorBox label.bnumber input::-webkit-outer-spin-button,
#colorBox label.bnumber input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}


#colorBox #per3{
    pointer-events: none;
    user-select: none;
}

#closePrompt,
#addMarkdown{
    margin:10px 0;
    text-align: right;
}

#addMarkdown button{
    background:#EDF2FA;
    border:1px solid #D3E3FC;
    border-radius: 7px;
    cursor: pointer;
    color:#474747;
    padding:5px 10px;
}

#closePrompt button{
    background:#333;
    color:#FFF;
    border-radius: 7px;
    cursor: pointer;
    padding:5px 10px;
    border:none;
}

#prompto{
    position:fixed;
    padding:10px 40px;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    background:#FFF;
    box-shadow: 3px 3px 10px #0003;
    z-index: 100;
    transition-duration: 0.3s;
    transform: scale(1.0,0.0);
    transform-origin: bottom center;
    opacity:0;
    pointer-events: none;
}

#prompto.active{
    transform: scale(1.0,1.0);
    opacity:1;
    pointer-events:auto;
}

#prompto h2{
    margin:0 0 0.5em;
}

#prompto p{
    font-size:14px;
    line-height: 1.75;
    max-width: 800px;
}

#prompto p span{
    font-size:12px;
    color:#000;
}

textarea#markdown{
    width: 100%;
    height: calc(100% - 250px);
    padding: 10px 20px;
    border: 1px solid #D3E3FC;
    line-height: 1.2;
    resize: none;
    font-size: 14px;
    font-family: Consolas;
    background: #303841;
    color: #EEF;
    tab-size: 4;
    border-radius:7px;
}

</style>
<title>AI Editor Assistant</title>
</head>
<body>
<header>
    <div class="inner">
        <div id="aiheader">
            <h1>AI Assistant</h1>
            <div id="aiselect">
                <span class="title">AI to Use:</span>
                <span class="button c<?=strlen($chatgpt)?>" data-name="chatgpt">ChatGPT</span>
                <span class="button c<?=strlen($gemini)?>" data-name="gemini">Gemini</span>
                <span class="button c<?=strlen($claude)?>" data-name="claude">Claude</span>
            </div>
        </div>
        <button id="apply">Export to CMS</button>
    </div>
    <div id="apikey"><input type="hidden" id="ai" name="ai"><span id="ainame"></span><label><input type="password" id="aikey" value=""></label><button id="aiset" type="button" value="set">Set</button></div>
</header>
<div id="aiprompt" class="<?=$unknown?>">
    <div class="inner">    
        <h2>WAIstyle<span>(Human・Concord（WA）・AI working together for optimal results.)</span></h2>
</div>
</div>
<main class="<?=$unknown?>">
  <div class="inner">
    <div id="aibox">
        <div id="left" class="">
            <div id="selectAi">
<?php if($chatgpt){ ?><label><input type="radio" class="ai" name="ai" value="chatgpt">ChatGPT</label><?php } ?>
<?php if($gemini){ ?><label><input type="radio" class="ai" name="ai" value="gemini">Gemini</label><?php } ?>
<?php if($claude){?><label><input type="radio" class="ai" name="ai" value="claude">Claude</label><?php } ?>
            </div>
           <div class="close">✕</div>
            <div id="menu" class="<?=$pcheck?> <?=$ncheck?> <?=$tcheck?>">
                <div data-name="logosetup" id="logo"><?=$lang['ai_logo'][$lng]?></div>
                <div data-name="page-structure" id="kosei"><?=$lang['ai_struct'][$lng]?></div>
                <div data-name="templateMenu" id="template"><?=$lang['ai_temp'][$lng]?></div>
                <div data-name="siteMenu" id="navi"><?=$lang['ai_navi'][$lng]?></div>
                <div data-name="page" id="page"><?=$lang['ai_page'][$lng]?></div>
                <div data-name="content" id="content"><?=$lang['ai_content'][$lng]?></div>
            </div>

            <div id="logosetup" class="prompt">
                <div id="colorForm">
                <h3>Logo Set</h3>
                <div id="logobox">
                    <label id="logo-upload">
                        <input type="file" id="logomark" accept="image/*">
                        <div class="empty">
                            <img src="lib/upload.svg" class="icon">
                            <p>Drag & Drop Logo Image</p>
                            <span>or</span>
                            <b>Select File</b>
                        </div>
                        <div id="preview"></div>
                    </label>
                    <div id="logopreview">
                        <img src="../common/img/logo.webp?t=<?=time()?>">
                    </div>
                    <div class="btnBox"><button type="button" id="logosetset">Upload</button></div>                        
                </div>

                <h3>Brand Colors</h3>
                <div id="colorBox">
                    <label class="binput" for="bcolor1"><input id="bcolor1" type="color"  value="<?=$bcolor1?>"></label>
                    <label  class="bnumber" for="per1"><input id="per1" type="number" max="100" min="0" step="1" value="<?=$per1?>">%</label>
                    <label  class="binput" for="bcolor2"><input id="bcolor2" type="color"  value="<?=$bcolor2?>"></label>
                    <label  class="bnumber" for="per2"><input id="per2" type="number" max="100" min="0" step="1" value="<?=$per2?>">%</label>
                    <label  class="binput" for="bcolor3"><input id="bcolor3" type="color"  value="<?=$bcolor3?>"></label>
                    <label  class="bnumber" for="per3"><input id="per3" type="number"  max="100" min="0" step="1" value="<?=$per3?>" disabled>%</label>
                </div>
                    <div class="btnBox"><button type="button" id="brandColor">Setup</button></div>
                </div>
            </div><!-- colorForm -->

            <div id="page-structure" class="prompt">
              <h3>Site Structure</h3>

              <label for="site-name">
                <span><?=$lang['ai_name'][$lng]?></span>
                <input id="site-name" type="text"  value="" placeholder="3Dvenue-CMS">
              </label>

              <label for="language">
                <span><?=$lang['ai_lang'][$lng]?></span>
                <input id="language" type="text"  value="" placeholder="Japanese">
              </label>

              <label for="industry">
                <span><?=$lang['ai_indus'][$lng]?></span>
                <input id="industry" type="text"  value="" placeholder="Web System Development">
              </label>

              <label for="purpose">
                <span><?=$lang['ai_purpo'][$lng]?></span>
                <input id="purpose" type="text"  value="" placeholder="Reduce unnecessary web traffic">
              </label>

              <label for="target">
                <span><?=$lang['ai_targe'][$lng]?></span>
                <input id="target" type="text"  value="" placeholder="Everyone involved in the web">
              </label>

              <label for="services">
                <span><?=$lang['ai_servi'][$lng]?></span>
                <input id="services" type="text"  value="" placeholder="MIT License Download">
              </label>

              <label for="usp">
                <span><?=$lang['ai_ups'][$lng]?></span>
                <input id="usp" type="text"  value="" placeholder="Achieve high scores on PageSpeed Insights">
              </label>

              <label for="required-pages">
                <span><?=$lang['ai_requi'][$lng]?></span>
                <input id="required-pages" type="text"  value="" placeholder="Home, About Product, Download">
              </label>

              <div class="btnBox">
                <button type="button" id="sitemap">SETUP</button>
              </div>
            </div>

            <div id="siteMenu" class="prompt">
                <h3>Navigation</h3>
                <?php
                if ($pages && isset($pages['pages'])) {
                ?>
                <ul id="navi-list">
                <?php
                $i = 0;
                foreach ($pages['pages'] as $page) {
                    $checked = $page['importance'] ? 'checked' : '';
                    $pageName = htmlspecialchars($page['pageName']);
                    $description = htmlspecialchars($page['description'])
                    ?>
                    <li draggable="true" data-pid="<?=$i?>">
                        <input type="checkbox" class="important" <?=$checked?>>
                        <span class="title"><?=$pageName?></span>
                        <div class="desc"><?=$description?></div>
                    </li>
                    <?php
                    $i++;
                     } ?>
                </ul>
                <?php } ?>
                <div class="memo"><?=$lang['ai_memo'][$lng]?></div>
               <div class="btnBox"><button type="button" id="makeNavi">MAKE NAVIGATION</button></div>
            </div>

            <div id="setting" class="prompt">
                <h3>Make Page</h3>
                <div id="basic">
                    <input type="hidden" id="p" name="p" value="">
                    <label for="pagename"><span>Page Name:</span><input type="text" id="pagename" name="pagename" value=""></label>
                    <label for="pagedescription"><span>Description:</span><textarea type="text" id="pagedescription" name="pagedescription"></textarea>
                </div>
                <div id="structures">
                    <div for="site-name"><span>Name</span><span id="site-name"></span></div>
                    <div for="language"><span>Language</span><span id="language"></span></div>
                    <div for="industry"><span>Industry</span><span id="industry"></span></div>
                    <div for="purpose"><span>Purpose</span><span id="purpose"></span></div>
                    <div for="target"><span>Target</span><span id="target"></span></div>
                    <div for="services"><span>Services</span><span id="services"></span></div>
                    <div for="usp"><span>USP</span><span id="usp"></span></div>
                </div>
                <div id="addMarkdown"><button id="addPrompto">add Prompto</button></div>
                <div class="btnBox"><button type="button" id="makeContent">MAKE CONTEBTS</button></div>
                <div id="content-archive">
                    <ul id="content-list"></ul>
                </div>
            </div>


            <div id="section" class="prompt">
                <h3>Contents</h3>
                <div id="html">
                    <input type="hidden" id="p" name="p" value="">
                    <textarea type="text" id="sectionedit" name="sectionedit"></textarea>
                </div>
                <div class="btnBox"><button type="button" id="savePage">SAVE</button></div>
            </div>


            <div id="templateMenu" class="prompt">
                <h3>Template Color Setup</h3>
                <ul id="templates">
                <?php
                foreach ($templates as $f) {
                $fname = basename($f, ".html");
                ?>
                    <li class="<?=$fname?>"><?=$fname?></li>
                <?php } ?>
                </ul>
            <div class="btnBox"><button type="button" id="makeColor">Generate Colors</button></div>
                <?php
               if ($fileCount > 0) {
                ?>
                    <h3>Color Archive</h3>
                        <div id="colorArchive">
                        <ul>
                        <?php foreach ($files as $file) {
                            $name = basename($file);
                        ?>
                            <li class='history-item'>
                                <span class="filename"><?=$name?></span>
                                <span class="delete" data-name="<?=$name?>">✕</span>
                            </li>
                        <?php } ?>
                        </ul>
                    </div>                
                    <?php } ?>
                </div>

            <div id="allclear">
                <img src="lib/settings.svg" alt="setting">
                <button id="allRest">START OVER</button>
            </div>

            </div>

            <div id="right">
            <?php
                if($navcheck == 'on'){
            ?>
           <iframe src="./ai/index.php"></iframe>
               <?php }else{ ?>
                    <div id="text">
                        <ul>
                        <li>「ここに生成されたページが表示されます」</li>
                        <li>「左のメニューから作成を開始できます」</li>
                        <li>「AIがページを構築すると、この領域にプレビューが表示されます」</li>
                        </ul>
                    </div>
               <?php } ?>
            </div>
        </div><!-- aibox -->
    </div><!-- inner -->

<div id="prompto">
    <h2>Add Prompto</h2>
    <p>
        掲載する内容が決まっている場合は、このエリアに貼り付けてください。AIが最優先の情報としてコンテンツを作成します。
        Markdown形式で入力すると、より正確に内容を伝えることができます。
        Markdown形式がわからない場合は、元の資料をAIに添付して「Markdown形式にまとめて」と指示すれば簡単に作成できます。<br>
        <span>※Markdownはテキスト形式なので、テキストエディターなどで簡単に編集できます。</span>
    </p>
    <textarea id="markdown" placeholder="Markdown Text..."></textarea>
    <div id="closePrompt"><button id="setpronpt">Setting</button></div>
</div>

</main>


<div id="loading" class="thinking">
<h3>GENERATING...</h3>
<p>AI is working hard for you.</p>
<?php echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'; ?>
<div class="svg-wrap">
<svg id="Artworks" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 569.7 599.9">
  <defs>
    <style>
      .cls-1 { fill:#ffffff; }
    </style>
  </defs>

  <!-- カップ部分 -->
  <path class="cls-1" d="M67.5,599.9h432.4c60,.3,67-51.8,67.5-52H0c.5.2,7.5,52.4,67.5,52Z"/>
  <path class="cls-1" d="M179.8,530.5h207.3c19.3-14.2,38.9-32.7,56.3-54.9,24.4-17.5,60.9-39.3,91.6-54.3,26-12.8,39.4-39.8,33.2-67.1-7.2-32-36.5-39.8-52-39.4H65.4c0,97.7,56.6,174.6,114.3,215.8l.1-.1ZM500.8,338.3h16.5c2.5,0,25.2.4,29.6,20.2,3.7,16.4-4.6,32.7-20.7,40.6-16.4,8.1-34,17.6-51,27.6,12.8-26.8,21.9-56.4,25.5-88.4h.1Z"/>

  <!-- 湯気部分（アニメーション対象） -->
  <path class="cls-1 steam" d="M285.3,170.7c-23.9,36-18.7,82.2,12.6,111.7,4.4,4.2,9.1,8,14.3,11.3-.6-6.2-1.5-12-2.3-17.7-1.9-14.8-4-30.1-1.1-45,4.2-21.4,19.9-38.1,35-52.5,23.8-22.8,38.8-55,34.8-88.4-1.1-9.2-3.7-18.7-8.1-26.9-1.5-2.7-3.1-5.4-5.1-7.8-.7,3-1.4,5.9-2.2,8.7-5,17.3-12.1,34.1-22.8,48.7-15.9,21.6-40.2,35.4-55.1,57.8v.1Z"/>
  <path class="cls-1 steam" d="M220.7,235.8c4.8,4.3,9.8,8.2,15.4,11.6-1-6.4-2.4-12.5-3.6-18.4-4.8-23.7-7.8-48.6,6.4-69.9,9.9-14.8,25.1-25.7,37.4-38.5,21.8-22.8,37-51.3,34.1-83.7-1.2-12.9-4.8-26.8-13.3-36.9,1.3,1.5-3.1,13.5-3.7,15.7-4.9,16.6-12.7,33.5-24.6,46.3-12.4,13.3-27,24.3-40.5,36.5-29.7,26.8-48.6,66.1-31.1,105.4,5.4,12.2,13.7,23.1,23.6,32l-.1-.1Z"/>
</svg>
</div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">   
$(function(){
    let root = '';
    $.get('../common/inc/root.txt',function(data){
        root = data;
    });

    const KEY = 'selectedAi';

  $('#aiselect .button').on('click',function(){
        $('#aiselect .button').removeClass('select');
        let ai = $(this).data('name');
        let ainame = $(this).addClass('select').text();
        $('#ainame').text(ainame);
        $('#ai').val(ai);
        $('#apikey').addClass('active');
  })

  $('#aiset').on('click',function(){
        const api = $('#aikey').val();
        const ai = $('#ai').val();
        $('#aiselect .button').removeClass('select');
        $('#apikey').removeClass();
        $.post('ai.php',{
            submit:'setkey',
            ai:ai,
            api:api
        },function(res){
            location.reload();    
        })
  })

    $('#template,#navi,#page,#kosei').on('click',function(){
        if ($('#selectAi input:checked').length === 0) {
          $('#selectAi').css({'outline':'2px solid red'});
        }
    })

    $('#selectAi label').on('click',function(){
          $('#selectAi').css({'outline':'none'});
    })

  const saved = sessionStorage.getItem(KEY);
  if (saved) {
    $('input.ai[name="ai"]').prop('checked', false);
    $(`input.ai[name="ai"][value="${saved}"]`).prop('checked', true);
  }

  // 2. 選択が変わったら保存
  $('input.ai[name="ai"]').on('change', function () {
    sessionStorage.setItem(KEY, $(this).val());
  });

    let canvas = document.createElement('canvas');
    let ctx = canvas.getContext('2d');

    $('#menu > div').on('click',function(){
        let idname = $(this).data('name');
        $('#left').removeClass().addClass('form '+idname);
        $('#right iframe').contents().find('main').removeClass();
        if(idname == 'content'){ 
            $('#right iframe').contents().find('main').addClass('event');
        }
    })

    let pid = localStorage.getItem('pageid') || 0;
    $('#right iframe').attr('src', './ai/index.php?p=' + pid);


    $('#logomark').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        console.log('ここまで');

        const reader = new FileReader();

        reader.onload = function (e) {
            $('#preview').css('background-image','URL(' + e.target.result + ')');
                        const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;
                const MAX_SIZE = 400;

                if (width > MAX_SIZE || height > MAX_SIZE) {
                    if (width > height) {
                        height *= MAX_SIZE / width;
                        width = MAX_SIZE;
                    } else {
                        width *= MAX_SIZE / height;
                        height = MAX_SIZE;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                hasImage = true;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // LOGO UPLOAD
    $('#logosetset').on('click', function() {

        canvas.toBlob(function(blob) {
            const formData = new FormData();

            if (blob) {
                formData.append('file', blob, 'logo.webp');
            }

            $.ajax({
                url: 'ai.php', 
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    location.reload();
                },
                error: function() {
                    alert("保存に失敗しました");
                }
            });
        }, 'image/webp', 0.85); // 85%の画質設定
    });


    // BRABD COLORSETUP
    $('#brandColor').on('click', function() {
        const brandColor = [
            { color: $('#bcolor1').val(), per: $('#per1').val() },
            { color: $('#bcolor2').val(), per: $('#per2').val() },
            { color: $('#bcolor3').val(), per: $('#per3').val() }
        ];
        $.ajax({
            url: 'ai.php',
            type: 'POST',
            data: { 
            submit:'brandColor',
            brandColor: JSON.stringify(brandColor)
        },success: function(res) {
                location.reload();
                // console.log(res);
            },
            error: function() {
                alert("保存に失敗しました");
            }
        });
    });


        $('#colorArchive li span.filename').on('click', function(){
            let filename = $(this).text().trim();
            $.post('ai.php', {
                submit: 'applyColor',
                filename: filename
            }, function(res){
                if (res === 'ok') {
                    $('#right iframe')[0].contentWindow.location.reload();
                }
            });
        });

        $('#colorArchive li span.delete').on('click', function(){
            let filename = $(this).data('name').trim();
            let $target = $(this).closest('li');
            $.post('ai.php', {
                submit: 'delete',
                filename: filename
            }, function(res){
                $target.remove();
            });
        });

    $('#colorBox label.bnumber input').on('change',function(){
        let per = $(this).val() * 1;
            if(isNaN(per) || per < 0 || per > 100){
                $('#per1').val(100);
                $('#per2').val(0);
                $('#per3').val(0);
                return;
            }
        let id = $(this).attr('id');
        if(id=='per1'){
            const per2 = 100 - per;
            $('#per2').val(per2);
            $('#per3').val(0);
        }

        if(id=='per2'){
            let per1 = $('#per1').val();
            if((per1*1 + per*1) <= 100){
                per = (per1 * 1 + per * 1);
                const per3 = (100 - per);
                $('#per3').val(per3);
            }else{
                per = 100 - per1 * 1;
                $('#per2').val(per);
                const per3 = (100 - (per1 * 1 + per * 1));
                $('#per3').val(per3);               
            }
        }
    })
    
    $('#page').on('click',function(){
        let src = $('#right iframe').attr('src');
        const p = new URL(src, location.href).searchParams.get('p');
        $('#p').val(p);
        $.getJSON('./ai/pages.json', function(data) {
            const page = data.pages[p];
            $('#pagename').val(page.pageName);
            $('#pagedescription').val(page.description);
        });

       $.get('ai/setting.txt', function(data) {
            $('#structures').html(data);
        });
        pagesList(p);
    })

    function pagesList(p){
      $('#content-list').empty();
       $.post('ai.php',{
            submit:'contentchange',
            data:p
       },function(res){
            if (res) {
              $.each(res, function(i, filepath) {
                $('#content-list').append(
                    '<li data-file="'+filepath+'" data-id="'+p+'"><p class="file">'+filepath+'</p><span class="del">✕</span>'
                  );
              });
            }
       },'json');
    }

    $('#content-list').on('click','li', function(e) {
      const id = $(this).data('id');
      const file = $(this).data('file');
      let submit = 'changeList';
      if ($(e.target).hasClass('del')) {
        submit = 'delList';
        $(this).remove();
        }
      $.post('ai.php',{
        submit:submit,
        p:id,
        file:file
      },function(res){
        if(res == 'ok'){
            $.get('ai/pages/' + id + '.txt', function(html) {
                const replacedHtml = html.replace(/\.\.\/common\//g, root + 'common/');
              $('#right iframe').contents().find('main').html(replacedHtml);
            });
        }
      })
    });

    $('#left .close').on('click',function(){
        $('#left').removeClass();
        $('#right iframe').contents().find('main').removeClass();
    })

    $('#navi-list li').on('click',function(){
        $(this).find('div.desc').toggleClass('open');
    })

    $('#right iframe').on('load', function(){
        const inside = $(this).contents();
         inside.find('head').append(`
            <style>
                main section:hover {
                    outline: 4px solid #00f;
                    outline-offset:-4px;
                }
            </style>
        `);
        inside.find('nav li a').on('click', function(){
            let pid = $(this).closest('li').attr('p');
            $('#p').val(pid);
            localStorage.setItem('pageid', pid);
            $('#right iframe').attr('src','./ai/index.php?p='+pid);

           $.getJSON('./ai/nav.json', function(data) {
                const page = data.find(item => item.index == pid);
                $('#pagename').val(page.name);
                $('#pagedescription').val(page.desc);
            });
           pagesList(pid);
        });
    });

    // content Edit
    $('#right iframe').on('load', function() {
        $('#right iframe').contents().find('main').on('click','section *', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $section = $(this).closest('section');
            $('#right iframe').contents().find('main *.active').removeClass('active');
            $(this).addClass('active');
            $('#sectionedit').html($(this).html().trim());
        });
    });


    $('#addPrompto,#setpronpt').on('click',function(){
        $('#prompto').toggleClass('active');        
    })

    $('#sectionedit').on('input',function(){
        let html = $(this).val().trim();
        $('#right iframe').contents().find('main .active').html(html);
    })

    $('#savePage').on('click', function() {
        $('#right iframe').contents().find('main *.active').removeClass('active');
        const html = $('#right iframe').contents().find('main').html().trim();
        const id = localStorage.getItem('pageid');
        $.post('ai.php', {
            submit: 'savePage',
            p: id,
            html: html
        }, function(res) {
            location.reload();
        });
    });

    $('#templates li').on('click',function(){
        const temp = $(this).text();
        $.post('ai.php',{
            submit:'change',
            temp:temp
        }, function(res) {
            // console.log(res);
            $('#right iframe').attr('src','./ai/index.php');
        })        
    })

    $('#makeColor').on('click', function(){
        $('#loading').addClass('active');
        let ai = $('#selectAi input[name="ai"]:checked').val();
        let f1 = $.get('ai/setting.txt?t=' + Date.now());
        let f2 = $.get('common/layout/default.html?t=' + Date.now());
        let f3 = $.get('ai/ai.txt?t=' + Date.now());
        let f4 = $.get('ai/nav.txt?t=' + Date.now());
        let f5 = $.get('common/css/basiccolor.css?t=' + Date.now());
        let f6 = $.get('ai/ccolor.txt?t=' + Date.now());

        let logo = $.Deferred();

        fetch('../common/img/logo.webp?t=' + Date.now())
            .then(res => res.blob())
            .then(blob => {
                const reader = new FileReader();
                reader.onload = function(){
                    logo.resolve(reader.result);
                };
                reader.readAsDataURL(blob);
            });

        $.when(f1, f2, f3, f4, f5, f6, logo).done(function(setting, layout, main, nav, color, ccolor, logo){

            const conditions = {
                setting: setting[0],
                layout: layout[0],
                main: main[0],
                nav: nav[0],
                color: color[0],
                ccolor: ccolor[0],
                logo: logo
            };

            $.post('result.php', {
                submit: 'makeColor',
                ai: ai,
                json: JSON.stringify(conditions)
            }, function(res) {
                // console.log(res);
                location.reload();
            });
        });
    });


    $('#makeContent').on('click', function(){
        $('#loading').addClass('active');
        let ai = $('#selectAi input[name="ai"]:checked').val();
        let pid = $('#p').val();
        let pagename = $('#pagename').val();
        let pagedescription = $('#pagedescription').val();
        let markdown = $('#markdown').val().trim();
        let structures = $.get('ai/setting.txt?t=' + Date.now());
        let html = $.get('common/layout/default.html?t=' + Date.now());
        let css = $.get('common/layout/default.css?t=' + Date.now());
        let color = $.get('common/css/color.css?t=' + Date.now());

        const conditions = {
            p: pid,
            pagename: pagename,
            description: pagedescription,
            structures: structures,
            markdown:markdown,
            html: html,
            css: css,
            color:color
        }

        $.post('result.php', {
            submit: 'makeContent',
            pid:pid,
            ai: ai,
            json: JSON.stringify(conditions)
        }, function(res) {
            // console.log(res);
            location.reload();
        });
    });


    $('#sitemap').on('click', function(){
        $('#loading').addClass('active');
        let ai = $('#selectAi input[name="ai"]:checked').val();
        let siteName = $('#site-name').val();
        let language = $('#language').val();
        let industry = $('#industry').val();
        let purpose = $('#purpose').val();
        let target = $('#target').val();
        let services = $('#services').val();
        let usp = $('#usp').val();
        let requiredPages = $('#required-pages').val();
        const conditions = {
            siteName: siteName,
            language: language,
            industry: industry,
            purpose: purpose,
            target: target,
            services: services,
            usp: usp,
            requiredPages: requiredPages
        }

        $.post('ai.php',{
            submit:'setting',
            conditions:conditions
        })

        $.post('result.php', {
            submit: 'makePage',
            ai: ai,
            json: JSON.stringify(conditions)
        }, function(res) {
            // console.log(res);
            location.reload();
        });
    });


    $('#makeNavi').on('click', function(){
        $('#loading').addClass('active');
        let ai = $('#selectAi input[name="ai"]:checked').val();
        const pages = [];
        let i = 0;
        $('#navi-list li').each(function(){
            const input = $(this).find('input');
            if (input.prop('checked')) {
                const name = $(this).find('.title').text().trim();
                const desc = $(this).find('.desc').text().trim();
                $.post('ai.php',{
                    submit:'makePage',
                    index:i,
                    name:name,
                    desc:desc
                })
                pages.push({
                    index: i,
                    name: name,
                    desc:desc
                });
                i++;
            }
        });

        $.post('ai.php', {
            submit: 'makeNavi',
            json: JSON.stringify(pages)
        }, function(res){
            console.log(res);
            // location.reload();
        });

        // ここで AI に送る
        $.post('result.php', {
            submit: 'makeNavi',
            ai: ai,
            json: JSON.stringify(pages)
        }, function(res){
            // console.log(res);
            location.reload();
        });
    });


    $('#apply').on('click', function(){
        let map = '';
        $.get('ai/nav.txt', function(data) {
            let $nav = $(data);
            $nav.find('li').each(function() {
                var p = $(this).attr('p');
                var slug = $(this).find('a').attr('data-slug');
                map += p + ',' + slug + '\n';
            });

            $.ajax({
                url: 'ai.php',
                type: 'POST',
                data: {
                    submit: 'apply',
                    map: map
                },
                success: function(res) {
                    alert('success');
                    location.href = 'editor.php';
                },
                error: function(jqXHR) {
                    console.log("error/not allowed:", jqXHR.responseText);
                    alert('error');
                }
            });
        }, 'html');
    });

    $('#allclear img').on('click', function() {
      $('#allclear').toggleClass('open');
    });

    $('#allclear button#allRest').on('click', function() {
      if (confirm('This will clear all current work.\nHowever, data already saved to the CMS will not be deleted.\n\nAre you sure?')) {
        $.post('ai.php',{
            submit:'allRest'
        },function(res){
            // localStorage.removeItem('pageid');
            location.reload();
            // console.log(res);

        })
      }
    });


})
</script>
</body>
</html>
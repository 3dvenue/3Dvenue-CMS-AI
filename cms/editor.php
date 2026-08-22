<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {

    $page = $_POST['page'] ?? '';
    $status = $_POST['status'] ?? '';
    $newname = $_POST['newname'] ?? '';

    if(is_numeric($page) && $status == "preview"){
        $_SESSION['pageid'] = $page;
        echo 'success';
        exit;
    }

    if(is_numeric($page) && $status == "changename"){
        include_once('../common/inc/dbcall.php');
        $sql = "UPDATE pages SET name = :name WHERE pid = :pid";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $newname,
            ':pid'  => $page
        ]);
        echo 'changename';
        exit;
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $pagename = $_POST['pagename'] ?? '';
    $main = $_POST['dom'] ?? '';
    $page = $_POST['page'] ?? '';
    $status = $_POST['status'] ?? '';
    $submit = $_POST['submit'] ?? '';

    if($submit == 'pageAsset'){
        $dom = $_POST['html'];
        $name = $_POST['name'];
        $type = '2';
        include_once('../common/inc/dbcall.php');
        $sql = "INSERT INTO contents (type,cname, dom) VALUES (:type, :name, :dom)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':type' => $type,
            ':name' => $name,
            ':dom'  => $dom
        ]);
        exit;
    }

    if($submit == 'sectionAsset'){
        $dom = $_POST['html'];
        $name = $_POST['name'];
        $memo = $_POST['category'];
        $type = '0';
        include_once('../common/inc/dbcall.php');
        $sql = "INSERT INTO contents (type,cname, dom, memo) VALUES (:type, :name, :dom, :memo)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':type' => $type,
            ':name' => $name,
            ':dom'  => $dom,
            ':memo'  => $memo
        ]);
        exit;
    }

    if ($submit == 'delPage') {
        if (!isset($_POST['p'])) exit;
        $p = $_POST['p'];
        if (!is_numeric($p)) {
            header('Location: index.php');
            exit;
        }
        include_once('../common/inc/dbcall.php');
        $sql = "DELETE FROM pages WHERE pid = :pid";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':pid' => $p]);
        exit;
    }

    if ($main !== '' && $pagename !== '') {
        include_once('../common/inc/dbcall.php');
        $sql = "INSERT INTO pages (name, main) VALUES (:name, :main)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $pagename,
            ':main'  => $main
        ]);
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $pid = $_GET['pid'] ?? '0';
    if (!is_numeric($pid)) {
        header('Location: top.php');
        exit;
    }

    $mapLines = file(__DIR__ . '/../common/inc/map.txt', FILE_IGNORE_NEW_LINES);
    $usedIds = [];
    foreach ($mapLines as $line) {$usedIds[] = explode(',', $line)[0];}
    $delcheck = in_array((string)$pid, $usedIds) ? 'unable' : '';


    include_once('../common/inc/dbcall.php');
    $sql = "SELECT * FROM pages WHERE pid = ".$pid;
    $stmt = $conn->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: top.php');
        exit;
    }

    $name = $row["name"];
    $title = $row["title"];
    $description = $row["description"];
    $jsonld = $row["jsonld"];
    $robots = $row['robots'];
    $main = $row["main"];
    $css = $row["css"];
    $js = $row["js"];
    $public = $row["public"];
    $sdate = $row["sdate"];
    $image = $row["image"];
    $ld = $row["ld"];
    $other = $row["other"];
    $sitename = $row["sitename"] ?? '';
    $pages = [];
    $sql = "SELECT * FROM pages";
    $stmt = $conn->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pages[] = $row;
    }

    $nav = file_get_contents('../common/inc/nav.txt');
    $sitename = file_get_contents('../common/inc/sitename.txt');
    $root = file_get_contents('../common/inc/root.txt');

    $sns_img = '../common/img/snsimage'.$pid.'.webp';
    $sns_img = file_exists($sns_img) ? $sns_img : '';

    $layout = file_get_contents('../common/layout/default.html');
    $layout = str_replace('<v>main</v>', $main, $layout);
    $layout = str_replace('<v>nav</v>',  $nav,  $layout);

    function domGet($url,$id){
        ob_start();
        include $url;
        $html = ob_get_clean();

        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        libxml_clear_errors();

        $x = new DOMXPath($dom);

        return $dom->saveHTML(
            $x->query("//*[@id='$id']")->item(0)
        );
    }

include_once('./lang.php');
?>
<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($robots == "1") { ?>
    <meta name="robots" content="noindex,nofollow">
    <?php } ?>
    <meta name="description" content="<?=$description?>">
    <title><?=$title?></title>
    <meta property="og:title" content="<?=$title?>">
    <meta property="og:description" content="<?=$description?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?=$sitename?>">
    <meta property="og:locale" content="ja_JP">
    <?php if (!empty($meta_image)) { ?>
    <meta property="og:image" content="<?=$root?>common/img/<?=$meta_image?>">
    <meta property="og:image:type" content="image/webp">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <?php } ?>
<?=$other?>
    <link rel="stylesheet" type="text/css" href="../common/layout/default.css?t=<?=time()?>">
    <link rel="stylesheet" type="text/css" href="../common/layout/color.css?t=<?=time()?>">
    <link rel="stylesheet" type="text/css" href="./css/editor.css?t=<?=time()?>">
    <link rel="icon" href="/favicon.ico">
    <style type="text/css" id="pagestyle">
        <?=$css?>
    </style>
</head>
<!----------- TOPEDITOR ----------->
<div id="editor">
    <div class="inner">

        <div id="tools">
            <div id="scriptBtn">Script</div>
            <div id="styleBtn">Styles</div>
        </div>

        <div id="pagechange">
            <select id="selectPage">
            <?php foreach ($pages as $row) { ?>
                <option value="<?=$row['pid']?>"><?=$row['name']?></option>
            <?php } ?>
            </select>

            <div id="changenamebox"><input type="text" id="changepagename" value=""><button id="changename" class="btn"><?=$lang['update'][$lng]?></button></div>

            <span id="newpage"><img src="./lib/newpage.svg" alt="newpage" title="<?=$lang['newpage'][$lng]?>"></span>
            <span id="view"><img src="./lib/desktop.svg" alt="newpage" title="view"></span>
            <select id="bodysize">
                <option value="0.9">90%</option>
                <option value="0.8">80%</option>
                <option value="0.7">70%</option>
                <option value="0.6">60%</option>
                <option value="0.5">50%</option>
                <option value="0.4">40%</option>
                <option value="0.3">30%</option>
                <option value="0.2">20%</option>
                <option value="0.1">10%</option>
            </select>
        </div>
        <div id="pageasset">
            <button id="pageadd" class="btn">PAGE ASSET</button>
        </div>


<!--         <div id="device">
        <button class="response" id="pc"><img src="./lib/desktop.svg"></button>
        <button class="response" id="tb"><img src="./lib/tablet.svg"></button>
        <button class="response" id="sp"><img src="./lib/mobile.svg"></button>
        </div>
 -->
        <span id="pagedelete" class="<?=$delcheck?>"><img src="./lib/trash.svg" alt="delete"></span>
        <span id="setting"><img src="./lib/settings.svg" alt="setting" title="<?=$lang['setup'][$lng]?>"></span>
    </div>

    </div>
</div>

<body>
<div id="editor_area">
    <div id="body_wrap">
        <div id="scroll_body">
            <div id="body">
                <?=$layout?>
            </div><!-- #body -->
        </div><!-- #scroll_body -->

    <!-- CSS,JS,HTML -->
        <div id="js" class="bottompopup"><span class="hadle"></span>
            <h3>
                <p>Javascript</p>
                <p class="close">✕</p>
            </h3>
            <textarea class="codearea" name="jstextarea"><?=$js?></textarea>
            <div class="btn"><?=$lang['save'][$lng]?></div>
        </div>

        <div id="css" class="bottompopup"><span class="hadle"></span>
            <h3>
                <p>Style Sheet</p>
                <p class="close">✕</p>
            </h3>
            <textarea class="codearea" name="styletextarea"><?=$css?></textarea>
            <div class="btn"><?=$lang['save'][$lng]?></div>
        </div>

        <div id="html" class="bottompopup"><span class="hadle"></span>
            <h3>
                <p id="bottomhtml"><span id="tag">H1</span><span id="idname">id:<input type="text" id="domid" value="" placeholder="-"></span></p>
                <p class="close">✕</p>
            </h3>
            <div id="domcss">
                <input id="styleinput" type="text">
            </div>
            <textarea class="codearea" name="htmltextarea"></textarea>
        </div>

    </div><!-- body_wrap-->


    <div id="right_editor">
        <div id="right_editor_box">
            <div class="close">✕</div>
            <div class="head">
              <h3>Properties</h3>
            </div>

            <details class="movie">
                <summary>Movie Editor</summary>
                <div class="row3">
                        <input type="text" id="movieframe" name="" placeholder="Youtube iframe">
                        <button class="btn" id="movieSet">Save</button>
                </div>
            </details>

            <details class="map">
                <summary>Map Editor</summary>
                <div class="row3">
                        <input type="text" id="mapframe" name="" placeholder="google map iframe">
                        <button class="btn" id="mapSet">Save</button>
                </div>
            </details>

            <details class="table">
                <summary>Table Editor</summary>
                <div class="row">
                    <span>Table Editor</span>
                    <div class="row">
                        <button id="tableEditor">Edit</button>
                    </div>
                </div>
            </details>

            <details class="image mp3">
                <summary>Image</summary>
                <div class="row2">
                    <!-- <span>image</span> -->
                    <div id="imageView">
                    </div>
                    <div class="image"></div>
                </div>
                <div class="row2 selectbtn">
                    <button id="selectimage" data-type="image" class="btn">select</button>
                </div>

                <div class="row">
                    <span>alt</span>
                    <div>
                        <input id="alt" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>caption</span>
                    <div>
                        <input id="caption" type="text" value="">
                    </div>
                </div>
            </details>

            <details class="link span image">
                <summary>Link</summary>
                    <span>URL</span>
                <div class="row">
                    <input type="text" id="link" data-type="link"  placeholder="https://">
                </div>
                <div class="row">
                    <span>target</span>
                    <input type="text" list="linktarget" id="target" data-type="target">
                        <datalist id="linktarget">
                            <option value="_self"></option>
                            <option value="_blank"></option>
                            <option value="link"></option>
                        </datalist>
                </div>
                <div class="row">
                    <button id="unlink" data-type="remove" >Unlink</button>
                </div>
            </details>

            <details class="text span table">
                <summary>text</summary>
                <div class="row">
                    <span>color</span>
                    <div>
                        <input id="color" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>font-size</span>
                    <div>
                        <input id="font-size" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>line-height</span>
                    <div>
                        <input id="line-height" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>font-family</span>
                    <div>
                        <input id="font-family" type="text" value="">
                    </div>
                </div>

                <div class="row">
                    <span>font-weight</span>
                    <div>
                        <input type="text" id="font-weight">
                    </div>
                </div>

                <div class="row">
                    <span>font-style</span>
                    <div>
                        <input type="text" id="font-style">
                    </div>
                </div>

                <div class="row">
                    <span>text-decoration</span>
                    <div>
                        <input type="text" id="text-decoration">
                    </div>
                </div>

                <div class="row">
                    <span>text-align</span>
                    <div id="text-align" class="">
                        <button data-type="left">left</button>
                        <button data-type="center">center</button>
                        <button data-type="right">right</button>
                        <!-- <button data-type="justify">均</button> -->
                    </div>
                </div>
            </details>

            <details class="all" id="all" data-type="">
                <summary>Display</summary>
                <div class="row">
                    <span>display</span>
                    <div>
                        <input id="display" type="text" value="">
                    </div>
                </div>

                <div class="row">
                    <span>gap</span>
                    <div>
                        <input id="gap" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>flex</span>
                    <div>
                        <input id="flex" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>justify-content</span>
                    <div>
                        <input id="justify-content" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>align-items</span>
                    <div>
                        <input id="align-items" type="text" value="">
                    </div>
                </div>

                <div class="row">
                    <span>flex-wrap</span>
                    <div>
                        <input id="flex-wrap" type="text" value="">
                    </div>
                </div>

                <div class="row">
                    <span>flex-direction</span>
                    <div>
                        <input id="flex-direction" type="text">
                    </div>
                </div>
            </details>

            <details class="size" id="size" data-type="">
                <summary>Size</summary>
                <div class="row size">
                    <span>width</span>
                    <div>
                        <input id="width" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>max-width</span>
                    <div>
                        <input id="max-width" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>min-width</span>
                    <div>
                        <input id="min-width" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>height</span>
                    <div>
                        <input id="height" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>max-height</span>
                    <div>
                        <input id="max-height" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>min-height</span>
                    <div>
                        <input id="min-height" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>aspect-ratio</span>
                    <div>
                    <input type="text" list="aspect" id="aspect-ratio" data-type="aspect-ratio">
                        <datalist id="aspect">
                            <option value="16/9"></option>
                            <option value="3/2"></option>
                            <option value="1.618/1"></option>
                            <option value="1.414/1"></option>
                            <option value="1/1"></option>
                            <option value="4/5"></option>
                            <option value="9/16"></option>
                        </datalist>
                    </div>
                </div>

            </details>

            <details class="bgcolor text section span">
                <summary>Background Color</summary>
                <div class="row bg">
                    <span>background-color</span>
                    <div>
                        <input id="background-color" type="text" value="">
                    </div>
                </div>
            </details>

            <details class="section">
                <summary>Background Image</summary>
                <div class="row2 view">
                    <div class="image"></div>
                </div>
                <div class="row2 selectbtn">
                    <button id="background" data-type="background"  class="btn">select</button>
                    <button id="background-delete" data-type="background"  class="btn">delete</button>
                </div>

                <div class="row">
                    <span>background-repeat</span>
                    <div>
                    <input type="text" id="background-repeat" data-type="background-repeat">
                    </div>
                </div>

                <div class="row">
                    <span>background-size</span>
                    <div>
                    <input type="text" id="background-size" data-type="background-size">
                    </div>
                </div>

                <div class="row">
                    <span>background-position</span>
                    <div>
                    <input type="text" id="background-position" data-type="background-position">
                    </div>
                </div>
            </details>

            <details class="space text section">
                <summary>Space（margin / padding）</summary>
                    <div class="row">
                        <span>Margin</span>
                        <div id="margin">
                            <input type="text" id="margin-top" data-type="margin" class="top">
                            <div class="flex">
                                <input type="text" id="margin-left" data-type="margin" class="left">
                                <input type="text" id="margin-right" data-type="margin" class="right" >
                            </div>
                            <input type="text" id="margin-bottom" data-type="margin" class="bottom">
                        </div>
                    </div>

                    <div class="row">
                        <span>Padding</span>
                        <div id="padding">
                            <input type="text" id="padding-top" data-type="padding" class="top">
                            <div class="flex">
                                <input type="text" id="padding-left" data-type="padding" class="left">
                                <input type="text" id="padding-right" data-type="padding" class="right" >
                            </div>
                            <input type="text" id="padding-bottom" data-type="padding" class="bottom">
                        </div>
                    </div>
            </details>

            <details class="border text image">
                <summary>Border</summary>
                <div class="row">
                    <span>border-style</span>
                    <div>
                        <input id="border-style" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>border-color</span>
                    <div>
                        <input id="border-color" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>border-width</span>
                    <div>
                        <input id="border-width" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>border-radius</span>
                    <div>
                        <input id="border-radius" type="text" value="">
                    </div>
                </div>
                <div class="row">
                    <span>box-shadow</span>
                    <div>
                        <input id="box-shadow" type="text" value="">
                    </div>
                </div>

                <div class="row">
                    <span>outline</span>
                    <div>
                        <input id="outline" type="text">
                    </div>
                </div>

                <div class="row">
                    <span>outline-offset</span>
                    <div>
                        <input type="text" id="outline-offset">
                    </div>
                </div>
            </details>

            <details class="section">
                <summary>Section</summary>
                <div class="section">
                    <button data-type="addSection" id="addSection" class="btn">Add Section</button>
                    <button data-type="delSection" id="delSection" class="btn">Dlete Section</button>
                </div>

                <div class="move">
                    <button data-type="up" id="moveup" class="btn">up</button>
                    <button data-type="down" id="movedown" class="btn">down</button>
                </div>
                <div class="asset">
                    <button data-type="sectionasset" id="sectionAsset" class="btn">section Asset</button>
                </div>
            </details>

            <details class="parts text">
                <summary>Parts Add</summary>
                <div class="row">
                    <button data-type="addParts" id="addParts" class="btn">Add Parts</button>
                    <button data-type="delParts" id="delParts" class="btn">Dlete Parts</button>
                </div>
            </details>

            <details class="sound mp3">
                <summary>Sound</summary>
                <div class="row2 selectbtn">
                    <button id="mp3" data-type="mp3" class="btn">select</button>
                </div>
            </details>

            <details class="reset text link span">
                <summary>Reset</summary>
                <div class="row2 selectbtn">
                    <button id="reset" data-type="reset" class="btn">Strip Tags</button>
                    <button id="erase" data-type="erase" class="btn">Reset Style</button>
                </div>
            </details>

        </div><!-- right_editor_box -->
    </div><!-- right_editor -->

</div><!-- #editor_area -->

<div id="mainsave" class="btn"><?=$lang['update'][$lng]?></div>

<div id="audioeditor" class="objectbox">
    <div class="close">✕</div>
    <div id="audiobox">
    <h3>サウンド設定</h3>
    <div id="mp3s">
        <ul>
            <?php
                $directory = '../common/mp3/';
                $files = glob($directory . "*.mp3");
                foreach ($files as $file) {
                $filename = explode('.',basename($file))[0];
            ?>
                <li data-name="<?=basename($file)?>">
                    <span>♪</span><span><?=basename($file)?></span>
                </li>
                <?php } ?>
            </select>
            </ul>
        </div>
    </div>
</div>

<div id="glbeditor" class="objectbox">
    <div class="close">✕</div>
        <div id="glbbox">
            <h3>3D Model </h3>
            <div id="glbs">
            <ul>
                <?php
                    $directory = '../common/glb/';
                    $files = glob($directory . "*.webp");
                    foreach ($files as $file) {
                    $filename = explode('.',basename($file))[0];
                ?>
                <li data-name="<?=basename($file)?>">
                    <figure><img src="../common/glb/<?=basename($file)?>"></figure>
                </li>
                <?php } ?>
                </select>
            </ul>
        </div>
    </div>
</div>

<!-- images -->
<div id="images" class="objectbox">
    <div class="close">✕</div>
     <section class="images">
        <h2><span>Select Image<span>
            <label>Image Size:<input type="range" id="imagesize" max="300" min="130" step="1" value="130"></label>
            <div id="new">＋</div></h2>
            <?php
                $directory = '../common/img/';
                $files = glob($directory . "*.webp");
                $fileurl = $root.'common/img/';
            ?>
        <ul>
        <?php
            foreach ($files as $file) {
            $filename = explode('.',basename($file))[0];
        ?>
        <li data-url="<?=$directory.basename($file)?>" data-name="<?=$filename?>">
            <figure>
                <img src="<?=$directory.basename($file)?>?t=<?=time()?>">
                <figcaption><?=basename($file)?></figcaption>
            </figure>
        </li>
        <?php } ?>
        </ul>
    </section>
</div>

<div id="pdflist">
    <div class="close">×</div>
    <?=domGet('./pdf.php','pdfs')?>
</div>

<!-- addsection -->
<div id="parts">
    <div class="sectionClose">✕</div>

    <div id="sectionparts" class="select">
        <h2>Add Section</h2>
        <div id="addsection">
            <label><input type="radio" name="addsection" class="addsection" value="before">:Before</label>
            <label><input type="radio" name="addsection" class="addsection" value="after" checked>:After</label>
            <section id="sections">
            </section>
        </div>
    </div>

    <div id="contensparts" class="select">
        <h2>Add Parts</h2>
        <div id="addParts">
            <label><input type="radio" name="addparts" class="addparts" value="before">:Before</label>
            <label><input type="radio" name="addparts" class="addparts" value="after" checked>:After</label>
            <section id="elements">
            </section>
        </div>
    </div>

    <div id="pageparts" class="select">
        <h2>Add Page Sample</h2>
        <div id="addPage">
            <section id="pages">
            </section>

            <div id="pageview">
                <div class="view"></div>

                <div id="pageaddbutton">
                    <form method="post">
                        <textarea name="dom" id="newdom"></textarea>        
                        <label><span>Page Name:</span><input type="text" name="pagename" id="pagename" value="" required></label>
                        <button type="submit" name="submit" value="newpage">Add Page</button>
                    </form>
                </div>

            </div>


        </div>
    </div>

    <!-- 20260719 -->

</div>




<div id="seoeditor" class="baseMenu">
    <div class="inner">
        <div class="close">✕</div>
    <h3>SEO (Search Engine Optimization)</h3>
    <div id="seoBox">

        <details>
            <summary><?=$lang['seo_sitename'][$lng]?> (meta-sitename)</summary>
            <p><?=$lang['seo_sitename_memo'][$lng]?></p>
        <input type="text" name="sitename" id="sitename" value="<?=$sitename?>" placeholder="このサイトのサービス名（共通）">
        <div class="submit"><div class="btn" data-name="sitename"><?=$lang['save'][$lng]?></div></div>
        </details>
        <details>
            <summary><?=$lang['seo_pagetitle'][$lng]?> (titile)</summary>
            <p><?=$lang['seo_pagetitle_memo'][$lng]?></p>
            <input type="text" name="title" id="pagetitle" value="<?=$title?>" />
        <div class="submit"><div class="btn" data-name="pagetitle"><?=$lang['save'][$lng]?></div></div>
        </details>

        <details>
            <summary><?=$lang['seo_description'][$lng]?> (meta-description)</summary>
            <p><?=$lang['seo_description_memo'][$lng]?></p>
        <textarea type="text" name="description"  id="description" ><?=$description?></textarea>
        <div class="submit"><div class="btn" data-name="description"><?=$lang['save'][$lng]?></div></div>
        </details>

        <details>
            <summary><?=$lang['seo_other'][$lng]?></summary>
            <p><?=$lang['seo_other_memo'][$lng]?></p>
        <textarea type="text" name="other"  id="other" ><?=$other?></textarea>
        <div class="submit"><div class="btn" data-name="other"><?=$lang['save'][$lng]?></div></div>
        </details>

        <details for="image" class="snsimage">
            <summary><?=$lang['seo_ogimage'][$lng]?> (og:image)</summary>
            <div id="snsview"><label for="snsimage"><?=$lang['select'][$lng]?></label></div>
            <p><?=$lang['seo_ogimage_memo'][$lng]?></p>
            <input type="file" name="snsimage" id="snsimage" value="">
        <div class="submit"><div class="btn" data-name="snsimage"><?=$lang['save'][$lng]?></div></div>
        </details>

        <details>
            <summary><?=$lang['seo_jsonld'][$lng]?> (Json-LD)</summary>
            <p><?=$lang['seo_jsonld_memo'][$lng]?></p>
        <textarea name="jsonld" id="jsonld"><?=$jsonld?></textarea>
        <div class="submit"><div class="btn" data-name="jsonld"><?=$lang['save'][$lng]?></div></div>
        </details>
        <?php $checked = ($robots >= 1) ? 'checked' : ''; ?>
        <details>
            <summary><?=$lang['seo_noindex'][$lng]?> (meta robots->noindex,nofollow)</summary>
            <p><?=$lang['seo_noindex_memo'][$lng]?></p>
            <div class="submit"><input type="checkbox" name="noindex" id="noidex" value="1" <?=$checked?>>
                <div class="btn" data-name="noidex"><?=$lang['setup'][$lng]?></div></div>
        </details>
    </div>
    </div>
</div>

<div id="addasset">
    <div class="inner">
        <button id="closeasset" class="close">✕</button>
        <h3>PAGE as ASSET</h3>
        <div id="viewasset"></div>
        <div class="form">
            <label for="assetname">Asset Name:<input type="text" id="assetname" placeholder="Asset Name"></label>
            <button id="saveasset">SAVE</button>
        </div>
    </div>
</div>

<div id="addsasset">
    <div class="inner">
        <button id="closeasset" class="close">✕</button>
        <h3>SECTION as ASSET</h3>
        <div id="viewsasset"></div>
        <div class="form">
            <label for="sassetname">Asset Name:<input type="text" id="sassetname" placeholder="Asset Name"></label>
            <label for="assetcategory">Category:
                <select id="assetcategory">
                    <option value="1">Eyecatch</option>
                    <option value="2">Text</option>
                    <option value="3">Image</option>
                    <option value="4">Card</option>
                    <option value="5">Media</option>
                    <option value="6">Table</option>
                    <option value="7">Document</option>
                    <option value="8">Other</option>
                </select>
            </label>
            <button id="savesasset">SAVE</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.15.4/beautify-html.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script type="text/javascript">
$(function(){


$.get('parts.php', function(html) {
    var sectionsHtml = $(html).find('#sections .flex').prop('outerHTML');
    $('#sections').html(sectionsHtml);
});

$.get('parts.php', function(html) {
    var sectionsHtml = $(html).find('#elements .flex').prop('outerHTML');
    $('#elements').html(sectionsHtml);
});

$.get('parts.php', function(html) {
    var sectionsHtml = $(html).find('#pages .flex').prop('outerHTML');
    $('#pages').html(sectionsHtml);
});

const sns_img = '<?=$sns_img?>';

//tagName Add
    function setDataTag(){
        $('body *').each(function(){
            $(this).attr('data-tag', this.tagName);
        });
    }

    setDataTag();

    if(sns_img !== ''){
        $('#snsview').css('background-image','url('+sns_img+')');
    }

    //Disable Link
    $('main').on('click','a',function(e){
        e.preventDefault();
    });

    $('#view').on('click', function(){
        let page = $('#selectPage').val();
        $.post('editor.php', {
            page: page, 
            status: 'preview'
        }, function(data) {

        const html = "<?=$root?>preview/";
        popup = window.open(html, "preview", "width=1000,height=800,top=100,left=100");

        });
    });

    $('.response').on('click',function(){
        $('#body').removeClass().addClass(this.id);
    })

    $('#bodysize').on('input',function(){
        bodySize();
    })

    function bodySize(){
        let scale = $('#bodysize').val();
        $('#body').css({'transform':'scale('+scale+')'});
    }

    let page = sessionStorage.getItem('page');

    if(page === null){
        page = 0;
    }

    bodySize();

    $('#html .btn').on('click',function(){
        $('#html').removeClass('active');
    });

    $('.close').on('click',function(e){
        cleanUp();
    })

    $('#right_editor .close,#scroll_body').on('click',function(e){
        cleanUp();
        $('#editor_area').removeClass('right');
    })

    function cleanUp(){
        $('body').removeClass();
        $('main [contenteditable]').removeAttr('contenteditable');
        $('body').removeClass('topeditor');
        $('.bottompopup').removeClass('active');
        $('.active,.objectbox').removeClass('active');
        $('t').contents().unwrap();
        $('#scroll_body').css('height','100%');
        $('#images').removeClass();
    }

    function bottomEditor(){
        let heightBottom = $('.bottompopup.active').css('height');
        // console.log(heightBottom);
        $('#scroll_body').css('height','calc(100% - '+heightBottom+')');
    }

    $('main').on('click', 'section, section *', function(e) {
        cleanUp();
        e.stopPropagation();

        $('.active').removeClass('active');
        $(this).addClass('active');

        $(this)[0].scrollIntoView({
            block: 'center'
        });

        let cla = $(this).attr('class');
        cla = cla.replace('active', '');

        let id = $(this).attr('id');
        $('#idname').val(id);

        var tag = this.tagName.toLowerCase();
        $('#editor_area').addClass('right');

        const tagname = $(this).prop('tagName');
        $('#tag').text(tagname);

        $('#classname').val(cla);

        if (!$(this).is('section, figure, .inner, .content')) {
            $(this).attr('contenteditable', 'true').focus();
        }

        $('.bottompopup#html').addClass('active').css('height','130px');
        bottomEditor();

    // Get CSS
        changeEditor($(this));

    //sectionを選択
        if(tag == 'section'){
            $('#right_editor').removeClass().addClass('section');
        }

    //見出しを選択
        if ($(this).is('div, p, h1, h2, h3, h4, h5, h6, ol, ul')) {
            if($(this).hasClass('movie')){
                $('#right_editor').removeClass().addClass('movie');
                return;
            }
            if($(this).hasClass('map')){
                $('#right_editor').removeClass().addClass('map');
                return;
            }
                $('#right_editor').removeClass().addClass('text');
        }

        if ($(this).is('td, th')) {
            $('#right_editor').removeClass().addClass('table');
            return;
        }

        if ($(this).is('span')) {
            $('#right_editor').removeClass().addClass('span');
        }

        if ($(this).is('figure')) {
            $('#right_editor').removeClass().addClass('image');
            let tagname = $(this).prop("tagName").toLowerCase();
            let classname = $(this).attr("class");
            let alt = $(this).find('img').attr('alt');
            let img = $(this).find('img').attr('src');
            $('#alt').val(alt);
            $('.tagname').text(tagname);
            $('#f-class').val(classname);
            $('#figureeditor').addClass('active');
            $('#imageView').css('background-image','url('+img+')');
        }

        if ($(this).is('.audio .mp3box figure')) {
            $('#right_editor').removeClass().addClass('mp3');
        }

        if ($(this).is('a')) {
            $('#right_editor').removeClass().addClass('link');
        }

        if($(this).is('.glb figure')){
            $('#right_editor,#editor_area').removeClass();
            e.stopPropagation();
            $('#tageditor,#texteditor').removeClass();
            $('#glbeditor').addClass('active');
            return;
        }

        if ($(this).is('.pdf .pdfimage img')) {
            $('#right_editor,#editor_area').removeClass();
            $(this).closest('.pdfflex').addClass('active');
            $('#pdflist').addClass('active');
        }

    });

// Get CSS properties from selected element 
    function changeEditor($obj){
        const el = $obj[0];
        const css = name => el.style.getPropertyValue(name);
        [
            'gap','flex','justify-content','align-items','flex-wrap','flex-direction',
            'width','max-width','min-width',
            'height','max-height','min-height',
            'color','background-color',
            'font-size','line-height','font-family','font-weight','font-style','text-decoration',
            'aspect-ratio',
            'border-style','border-color','border-width','border-radius',
            'box-shadow','outline','outline-offset',
            'background-size','background-repeat','background-position'
        ].forEach(name => {
            $('#' + name).val(css(name));
        });

        ['top','right','bottom','left'].forEach(pos => {
            $('#margin-' + pos).val(css('margin-' + pos));
            $('#padding-' + pos).val(css('padding-' + pos));
        });

        // $('#display').val(css('display'));

        $('#right_editor_box').removeClass().addClass($obj.css('display'));
        console.log($obj.css('display'));

        $('#html input#styleinput').val($obj.attr('style') || '');

        formatHTML($obj);

        let href = "";
        let target = "";
        if($($obj).is('figure')){
            href = $obj.find('a').attr('href') || '';
            target = $obj.find('a').attr('target') || '';
        }
        if($($obj).is('a')){
            href = $obj.attr('href') || '';
            target = $obj.attr('target') || '';
        }
        $('#link').val(href);
        $('#target').val(target);

        let bgimage = $obj.css('background-image');
        let url = bgimage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
        let align = $obj.css('text-align');
        $('#text-align').removeClass().addClass(align);

        if(url !== 'none' && url !== ''){
            $('.section .row2 .image').css({'background-image':'url('+url+')'});
        }else{
            $('.section .row2 .image').css({'background-image':'none'});            
        }
    }

    //整形してhtmlエディター画面に表示
    function formatHTML($obj){
        let html = $obj.html().trim();
        html = html_beautify(html, {
            // indent_with_tabs: true,
            indent_size: 1,
            preserve_newlines: false,
            wrap_line_length: 0
        });

        html = html.replace(/\sdata-tag="[^"]*"/g, '');
        $('#html textarea.codearea').val(html);
    }

    $('main').on('input change','.active',function(){
        let html = $(this).html();
        html = html.replace(/\sdata-tag="[^"]*"/g, '');
        $('#html textarea').val(html);
    })

    //input change
    $('#right_editor details.section input,#right_editor details.all input, #right_editor details.text input,#right_editor details.bgcolor input,#right_editor details.size input,#right_editor details.image input').on('change',function(){
        const id = $(this).attr('id');
        const value = $(this).val();
        const $t = $('main t');
        if($t.length){
            $t.replaceWith(function(){
                return $('<span class="active" data-tag="SPAN"></span>').html($(this).html());
            });
        }
        if(id){
            $('main .active').css(id,value);
            formatHTML($('main .active'));
        }
        // 20260720
        $('#html input#styleinput').val($('main .active').attr('style'));
    });

    $('#alt').on('change',function(){
        let alt = $(this).val();
        $('main .active img').attr('alt',alt);
    });

    $('#caption').on('change',function(){
        let caption = $(this).val();
        $('main .active figcaption').text(caption);
    });

    //link
    $('#right_editor details input#link').on('change',function(){
        const value = $(this).val();
        const $active = $('main .active');
        const tag = $active.prop('tagName').toLowerCase();

        if(tag == 'a'){
            $active.attr('href',value);
        }

        if(tag == 't' || tag == 'span'){
                $active.replaceWith(function(){
                    return $('<a class="active" data-tag="A"></a>')
                    .html($(this).html())
                    .attr('href',value);
              });
            $('#tag').text('A');                
        }

        if(tag == 'figure'){
            if($active.find('a').length){
                $active.find('a').attr('href',value);
            }else{
                $active.find('img').wrap('<a href="'+value+'"></a>');
            }
        }
    });

    // image select
    $('#selectimage').on('click',function(){
        $('#images').addClass('figure');
    })

    $('#tools div').on('click',function(){
        $('.bottompopup').removeClass('active');
        $('#tools div').removeClass();
        $(this).addClass('active');
        let id = $(this).attr('id');
            switch (id) {
               case 'scriptBtn':
                $('#js').addClass('active');
            break;
            case 'styleBtn':
                $('#css').addClass('active');
            break;
        }
        $('body').removeClass();
        $('#sideeditor').removeClass('active');
        bottomEditor();
    })


    //Stylesheetを即時反映
    $('#css textarea').on('input', function(e) {
            $('#pagestyle').text($(this).val());
    });

    //Stylesheetの保存
    $('#css .btn').on('click',function(){
        let css = $('#css textarea').val();
            $.post('sqlupdate.php', {
                pid: '<?=$pid?>',
                table: 'pages',
                column: 'css',
                data: css
            }, function(res){
                $('#css').removeClass('active');
         });
    })

    $('#js .btn').on('click',function(){
        let js = $('#js textarea').val();
            $.post('sqlupdate.php', {
                pid: '<?=$pid?>',
                table: 'pages',
                column: 'js',
                data: js
            }, function(res){
                $('#js').removeClass('active');
         });
    })

    //js and css updown 
    $('span.hadle').on('mousedown', function(e) {
        e.preventDefault();
        let targetPopup = $(this).closest('.bottompopup');
        $(document).on('mousemove.resizer', function(moveEvent) {
            let h = $(window).height() - moveEvent.clientY;        
            if (h > 100 && h < $(window).height() * 0.95) {
                targetPopup.css('height', h + 'px');
                $('#scroll_body').css('height','calc(100% - '+h+'px)');
            }
        });
        $(document).one('mouseup', function() {
            $(document).off('mousemove.resizer');
        });
    });

    //id add
    $('#html #domid').on('change',function(){
        const id = $(this).val();
        $('main .active').attr('id',id);
    });

    //style chage
    $('#html #styleinput').on('change',function(){
        const style = $(this).val();
        if(style){
            $('main .active').attr('style',style);
        }else{
            $('main .active').removeAttr('style');
        }
    });

    //html change
    $('#html textarea.codearea').on('input',function(){
        const html = $(this).val();
        $('main .active').html(html);
    })

    //target
    $('#right_editor details input#target').on('change',function(){
        const value = $(this).val();
        $('main a.active').attr('target',value);
        $('main figure.active a').attr('target',value);
    });

    //Unlink 
    $('#right_editor details button#unlink').on('click',function(){
        $('main a.active').contents().unwrap();
        $('main figure.active a').contents().unwrap();
        $('#link').val('');
        $('#target').val('');
    });

    $('#right_editor details.text button').on('click',function(){
        const type = $(this).attr('data-type');
        $('#align').removeClass().addClass(type);
        $('main .active').css('text-align',type);
    })

    // reset
    $('#reset').on('click',function(){
        const obj = $('main .active');
        if(obj.is('span,a')){
            obj.contents().unwrap();
        }
        else{
            obj.find('span,a').contents().unwrap();
        }
        cleanUp();
        // $('#editor_area').removeClass();
    });

    //Reset STyle
    $('#erase').on('click',function(){
        const $obj = $('main .active');
        $obj.removeAttr('style');
        cleanUp();
        // $('#editor_area').removeClass();
    });

    $('#right_editor details').on('toggle',function(){
        if(this.open){
            $('#right_editor details').not(this).prop('open',false);
        }
    });

    $('main').on('keydown click','section, section *', function(e) {
           $('#mainsave').addClass('click');
    });

    $('header,nav,footer').on('click',function(){
        $('main section').removeClass('active');
        $('body').removeClass('active');
        $('#sideeditor').removeClass('active');
    });

    $('#mainsave').on('click',function(){
        const $clone = $('<div>').append($('main').contents().clone());
        $clone.find('[contenteditable]').removeAttr('contenteditable');
        $clone.find('.active').removeClass('active');
        $clone.find('[data-tag]').removeAttr('data-tag');

        const html = $clone.html().trim(); 

            $.post('sqlupdate.php', {
                pid: '<?=$pid?>',
                table: 'pages',
                column: 'main',
                data: html
            }, function(res){
                console.log(res);
         });
        $(this).removeClass('click');
    })

    $('.codearea').on('keydown', function(e) {
        const el = e.target;

        if (e.key === 'Tab') {
            e.preventDefault();
            el.setRangeText("\t", el.selectionStart, el.selectionEnd, 'end');
        } 
        else if (e.key === 'Enter') {
            const line = el.value.substring(el.value.lastIndexOf('\n', el.selectionStart - 1) + 1, el.selectionStart);
            const indent = line.match(/^\s+/);

            if (indent) {
                e.preventDefault();
                el.setRangeText('\n' + indent[0], el.selectionStart, el.selectionStart, 'end');
            }
        }
    });

    $('#audioeditor #audiobox').on('click',' ul li',function(){
        let name = $(this).attr('data-name');
        $mp3box = $('figure.active').closest('.mp3box');
            $mp3box.find('.player span')
            .text('play')
            .attr('data-name',name);
        $('#audioeditor').removeClass();
    })

    $('#glbeditor #glbbox').on('click',' ul li',function(){
        let name = $(this).attr('data-name');
        let data_name = name.replace(/\.webp$/i, '');
        let src = '../common/glb/' + name;
        $('.glbbox figure.active').attr('data-name',data_name);
        $('.glbbox figure.active img').attr('src',src).removeClass();
        $('#glbeditor').removeClass();
    })


    $('#right_editor details button.btn').on('click',function(){
        let id = $(this).attr('id');
        switch(id){
            case 'image':
                $('#images').addClass('active');
                break;
            case 'background':
                $('#images').addClass('active');
                break;
            case 'background-delete':
                $('.row2.view div.image,main .active').css('background-image','');
                break;
            case 'mp3':
                $('#audioeditor').addClass('active');
                break;
            case 'addSection':
                    addSection();
                break;
            case 'delSection':
                    delSection();
                break;
            case 'addParts':
                    addParts();
                break;
            case 'delParts':
                    delParts();
                break;
            }

    })

    $(window).on('keydown', e => {
      const s = $('main section.active');
      if (e.ctrlKey || e.metaKey) {
          if (e.key === 'ArrowUp') s.insertBefore(s.prev('section'));
          if (e.key === 'ArrowDown') s.insertAfter(s.next('section'));
      }
    });

    $('details.section .move button').on('click',function(){
      const s = $('main section.active');
      let move = $(this).data('type');
          if (move == 'up') s.insertBefore(s.prev('section'));
          if (move == 'down') s.insertAfter(s.next('section'));
    });


/* selectPage
-------------------------------------------*/

    $('#selectPage').val("<?=$pid?>");

    let pname = $('#selectPage option:selected').text();
    $('#changepagename').val(pname);

    $('#selectPage').on('change',function(){
        let p = $(this).val();
        location.href = "./editor.php?pid="+p;
    })

    $('#changename').on('click',function(){
        let pid = $('#selectPage').val();
        let newname = $('#changepagename').val();
        $.post('editor.php',{
            status:'changename',
            newname:newname,
            page:pid
        }).done(function(data) {
            $('#selectPage option[value="' + pid + '"]').text(newname);
        })
    })

    $('#imagesize').on('input',function(){
       let size = $(this).val();
       $('#images .images ul li').css('width',size+'px');
    })

    $('#images .images ul li').on('click',function(){
        const url = $(this).attr('data-url');
        $('#imageView').css('background-image','url(' + url + ')');
        $('details.section div.image,main section.active').css('background-image','url(' + url + ')');
        $('main figure.active img').attr('src',url);
        $('#images').removeClass();
    })

//section delete
    function delSection(){
        $('main section.active').remove();
    }

//section add
    function addSection(){
       $('#parts').removeClass().addClass('active addsection');
    }

//parts delete
    function delParts(){
        $('main .active').remove();
    }

//parts add
    function addParts(){
       $('#parts').removeClass().addClass('active contensparts');
    }


    $('#parts .sectionClose').on('click',function(){
        $('#parts').removeClass();
    })

    $('#addsection section').on('click','div.parts', function(){
        let section = $(this).find('.wrapDom').html().trim();
        // console.log(section);
        let place = $('#addsection .addsection:checked').val();
        const $section = $('main section.active');
            if(place == "before"){
                $section.before(section);
            }
            if(place == "after"){
                $section.after(section);
            }        
        $('#parts').removeClass();
        setDataTag();
    });


    $('#addParts section').on('click',' div.parts', function(){
        let element = $(this).find('.wrapDom').html().trim();
        console.log('parts');
        let place = $('#addParts .addparts:checked').val();
        const $parts = $('main .active');
            if(place == "before"){
                $parts.before(element);
            }
            if(place == "after"){
                $parts.after(element);
            }        
        $('#parts').removeClass();
        setDataTag();
    });



    $('#newpage').on('click',function(){
        $('#parts').removeClass().addClass('active pageparts');  
    })


    $('#parts #addPage #pages').on('click','.parts',function(){
        let name = $(this).find('.name').text().trim();
        let html = $(this).find('.wrapDom').html().trim();
        $('#pageview .view').html(html);
        $('#pagename').val(name);
        $('#newdom').val(html);
    });


    $('#parts .closeviw').on('click',function(){
        $('#pageview img').attr('src','');  
        $('#pageview').removeClass();
    });



/* select table
---------------------------------------------*/
$('#tableEditor').on('click', function(){
    const $wrap = $('main .active').closest('div.tablewrap');
    $('main table.table-select').removeClass('table-select');
    $wrap.addClass('table-select');
    const table = $wrap.find('table')[0].outerHTML;
    const name = $wrap.find('table').data('name');
    const cname = $wrap.find('table').attr('class');
    const css   = $wrap.find('style').html();
    const win = window.open(
        'table.php',
        'tableEditor',
        'width=1000,height=600,left=200,top=100'
    );

    const timer = setInterval(function(){
        if(typeof win.loadTable === 'function'){
            clearInterval(timer);
            win.loadTable(table, css, name, cname);
        }
    }, 50);
});

/* Change PDF
---------------------------------------------*/
    $('#pdflist #pdfs ul li').on('click',function(){
        let pdf = $(this).attr('data-image');
        let name = $(this).attr('data-name');
        let pdfurl = '../common/pdf/'+pdf;
        let imgurl = '../common/pdf/'+ name + '.webp';
        $('.pdfflex.active .pdftext .button a').attr('href',pdfurl);
        $('.pdfflex.active .pdfimage img').attr('src',imgurl);
        $('#pdflist').removeClass('active');
        $('#mainsave').addClass('click');
    });


    /* SELECT Text
    ---------------------------------------------*/
    let currentRange = null;
        $('main').on('mouseup',function(){
            $('t').contents().unwrap();
            const selection = window.getSelection();
            if(selection.toString() !== ''){
                currentRange = selection.getRangeAt(0);
                console.log(currentRange);
                setTimeout(function(){
                const node = document.createElement('t');
                currentRange.surroundContents(node);
                $('main .active').removeClass('active');
                $(node).addClass('active');
                $('#right_editor').removeClass().addClass('span');
                $('#tag').text('SPAN');
                $('#html textarea.codearea').val($('main .active').html());
            },50)
        }
    });

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    //SEO
    $('#setting').on('click',function(){
        $('#seoeditor').addClass('active');
        // $('body').toggleClass('base');
    })

    $('.baseMenu .close').on('click',function(){
        $('.baseMenu .close').removeClass('active');
    })


    $('#snsimage').on('change',function(){
        drawCanvas();
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#snsview').css('background-image', 'url(' + e.target.result + ')');
        };
        reader.readAsDataURL(file);  
    })

    $('#seoeditor .btn').on('click',function(){
          let pagetitle = $('#pagetitle').val();
          let description = $('#description').val();
          let other = $('#other').val();
          let snsimage = $('#snsimage').val();
          let jsonld = $('#jsonld').val();
          let noidex = $('#noidex').prop('checked') ? 1 : 0;
          let name = $(this).data('name');
        if(name !== 'sitename' && name !== 'snsimage'){
            $.post('sqlupdate.php', {
                pid: '<?=$pid?>',
                table: 'pages',
                pagetitle:pagetitle,
                description:description,
                other:other,
                jsonld:jsonld,
                noidex:noidex,
                submit:'seo'
            }, function(res){
                $('details').removeAttr('open');
                alert('SEO情報が更新されました');
                $('#seoeditor').removeClass('active');
             });
        }else if(name == 'sitename'){ //サイト名は共通のSEO
         let sitename = $('#sitename').val();
            $.post('sqlupdate.php', {
                sitename:sitename,
                submit:name
            }, function(res){
                $('details').removeAttr('open');
                alert('サイト名が更新されました');
                $('#seoeditor').removeClass('active');
             });
        }else if(name == 'snsimage'){
            snsImageSend();
        }
    });

    function drawCanvas() {
        const file = $('#snsimage').prop('files')[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // 大事な「規格統一」の作業
                const targetW = 1200;
                const targetH = 600;
                canvas.width = targetW;
                canvas.height = targetH;

                const ratio = Math.max(targetW / img.width, targetH / img.height);
                const x = (targetW - img.width * ratio) / 2;
                const y = (targetH - img.height * ratio) / 2;

                ctx.drawImage(img, x, y, img.width * ratio, img.height * ratio);
                hasImage = true;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }


    function snsImageSend() {
        if (!hasImage) return alert('画像を選択してください');

        canvas.toBlob(function(blob) {
            const formData = new FormData();

            formData.append('file', blob, 'snsimage' + '<?=$pid?>' + '.webp');

            $.ajax({
                url: './upload.php', 
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    location.reload();
                }
            });
        }, 'image/webp', 0.8);
    }

    //table editor apply
    window.applyTable = function(html, css){
        const data = html + "\n<style>\n" + css + "\n</style>";
        $('main div.table-select').html(data);
    };

    //table cell move
    $('body').on('keydown', 'table th, table td', function(e) {
        if (e.key !== 'Tab') return;
        e.preventDefault();
        const $items = $(this).closest('table').find('th, td');
        const index = $items.index(this);
        const next = e.shiftKey ? index - 1 : index + 1;
        $items.eq(next).trigger('click');
    });


    // Movie Map set
    $('#movieSet').on('click',function(){
        let iframe = $('#movieframe').val();
        $('main div.active').html(iframe);
        $('#movieframe').val('');
    })

    $('#mapSet').on('click',function(){
        let iframe = $('#mapframe').val();
        $('main div.active').html(iframe);
        $('#mapframe').val('');
    })

    $('#pageadd').on('click',function(){
        let name = $('#changepagename').val().trim();
        let content = $('#editor_area main').html().trim();
        let $temp = $('<div>').html(content);
        $temp.find('[data-tag]').removeAttr('data-tag');
        $temp.find('.active').removeClass('active');
        let result = $temp.html();
        html = html_beautify(result, { indent_size: 4 });
        $('#viewasset').html(html);
        $('#assetname').val(name);
        $('#addasset').addClass('active');
    })


    $('#saveasset').on('click',function(){
        const html = $('#viewasset').html().trim();
        const name = $('#assetname').val().trim();
        $.post('editor.php',{
            submit:'pageAsset',
            html:html,
            name:name
        },function(res){
            location.reload();
        })
    })

    $('#addasset .close,#addsasset .close').on('click',function(){
        $('#addasset,#addsasset').removeClass('active');
        $('#editor_area').removeClass();
    })


   $('#sectionAsset').on('click',function(){
        let content = $('main section.active').prop('outerHTML').trim();
        let $temp = $('<div>').html(content);
        $temp.find('[data-tag]').removeAttr('data-tag');
        $temp.find('.active').removeClass('active');
        let result = $temp.html();
        html = html_beautify(result, { indent_size: 4,preserve_newlines: false });
        $('#viewsasset').html(html);
        $('#addsasset').addClass('active');
    })


    $('#savesasset').on('click',function(){
        const html = $('#viewsasset').html().trim();
        const name = $('#sassetname').val().trim();
        const category = $('#assetcategory').val();
        if(name === ''){
            $('#sassetname').focus();
            return;
        }
        console.log(category);
        $.post('editor.php',{
            submit:'sectionAsset',
            html:html,
            name:name,
            category:category
        },function(res){
            location.reload();
        })
    })

    $('#pagedelete').on('click',function(){
        if(confirm('Delete button pressed. If no backup has been made, this cannot be undone.\n\nAre you sure you want to delete this?')){
            $.post('editor.php',{
                submit:'delPage',
                p:<?=$pid?>
            },function(res){
                location.reload();
            })
        }
    })

})
</script>
</body>
</html>
<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
$root = file_get_contents('../common/inc/root.txt');
include_once('./lang.php');
?>
<!DOCTYPE html>
<html lang="<?=$lng?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" href="/favicon.ico">
    <title>3DVenue: Open Source CMS (MIT Licensed)</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <style>
    /* main
    ---------------------------------------------*/

    #main h1 + p.memo{
        margin: 20px auto;
        font-size:18px;
        font-weight:700;
        text-align: center;
        font-size:16px;
        transform: scaleY(1.3);
        margin-bottom:30px;
    }

    /* content
    ---------------------------------------------*/
    section{
        padding:20px 0 0;
    }

    section h2{
        margin-bottom:40px;
        font-size:18px;
        text-align: left;
    }

    section h2 span{
        position:absolute;
        top:20px;
        right:0;
        font-size:12px;
        font-weight: normal;
        border:1px solid #ccc;
        border-radius: 5px;
        display: flex;
        padding:3px 12px;
        background:#FFF;
        cursor: pointer;
    }


    section div#files{
        width:100%;
        display: grid;
        gap: clamp(20px, 20vw, 30px);
        grid-template-columns: repeat(auto-fill, minmax(440px, 1fr));

        transition-duration: .5s;

    }


    section div#files div.content{
        width:100%;
    }

    #main section h3 {
        width: 100%;
        margin-bottom: 0px;
        font-size:16px;
    }

    section div#files div.content{
        position: relative;
        width:100%;
        background:#FFF;
        padding:20px 20px 60px;
        margin:0;
        border-radius: 10px;
        box-shadow: 0 0 5px #0001;
        display: flex;
        gap:20px;
    }

    section div#files div.content figure{
        width:60px;
        height:60px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background:#f0f0f0;
        border-radius:10px;
    }


    section div#files div.content figure img{
        width:40px;
        height:auto;
    }

    section div#files div.content a{
        position: absolute;
        bottom:20px;
        left:20px;
        color:#2763C9;
    }

    section div#files .content figure.image     {background:#13AD40}
    section div#files .content figure.pdf   {background:#ED3332}
    section div#files .content figure.audio     {background:#1D73DE}
    section div#files .content figure.glb   {background:#6826CB}
        
    </style>
</head>
<body>
<div id="main">
<div class="inner">

<section>
<div id="topmenu" class="contents">
    <div class="left">    
        <div id="toptitle">
            <h1>ファイル管理と編集</h1>
        </div>
    </div>
</div>
</section>

<section>
<h2><?=$lang['index01'][$lng]?></h2>

<div id="files" class="contents">

    <div class="content">
            <figure class="image">
            <img src="./lib/image.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['image_edit'][$lng]?></h3>
            <p><?=$lang['image_edit_memo'][$lng]?></p>
        </div>
        <a href="images.php"><?=$lang['open'][$lng]?> →</a>
    </div>

    <div class="content">
            <figure class="pdf">
            <img src="./lib/pdf.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['pdf_edit'][$lng]?></h3>
            <p><?=$lang['navi_edit_memo'][$lng]?></p>
        </div>
        <a href="pdf.php"><?=$lang['open'][$lng]?> →</a>
    </div>

    <div class="content">
            <figure class="audio">
            <img src="./lib/mp3.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['audio_edit'][$lng]?></h3>
            <p><?=$lang['audio_memo'][$lng]?></p>
        </div>
        <a href="mp3.php"><?=$lang['open'][$lng]?> →</a>
    </div>

    <div class="content">
            <figure class="glb">
            <img src="./lib/glb.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['glb_edit'][$lng]?></h3>
            <p><?=$lang['glb_memo'][$lng]?></p>
        </div>
        <a href="glb.php"><?=$lang['open'][$lng]?> →</a>
    </div>


</div>
</section>

</div>
</div><!-- main -->
<div id="footer">
<div class="inner">
    <div id="copy">&copy; 2026 3Dvenue. All rights reserved.</div>
</div>
</div>
</body>
</html>
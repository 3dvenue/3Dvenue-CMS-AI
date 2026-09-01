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


#toInsights{
    max-width: 200px;
    display: flex;
    justify-content: center;
    padding:5px 10px;
    margin:10px 0 0;
    text-decoration: none;
    font-size:16px;
    font-weight: 700;
    background:#034BB3;
}

/* content
---------------------------------------------*/
section{
    padding:20px 0 0;
}

section h2{
    margin-bottom:10px;
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


section div.contents{
    width:100%;
    display:flex;
    gap:20px;
    transition-duration: .5s;
}

section div.contents .left{
    flex:2;
}

section div.contents .left .card{
    padding:20px;
    display: flex;
    gap:40px;
}

section div.contents .left .card .memo{
    text-align: left;
}

section div.contents .left .card strong{
    font-size:16px;
    margin-bottom:5px;
}

section div.contents .left .card #score100{
    width:90px;
    height:90px;
    border-radius: 80px;
    border:8px solid #19B556;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size:24px;
    color:#19B556;
    font-weight: 700;
    line-height: 1.0;
    background:#EAFAF1;
    flex-shrink: 0;
}

section div.contents .left #toptitle{
    text-align: left;
    padding:20px 0;
}

section div.contents .card{
    padding:10px;
    background:#FFF;
    border-radius:10px;
    box-shadow: 0 0 5px #0001;
}

section div.contents .card.right{
    flex:1;
    padding:0 20px;
}

section div.contents .card.right a{
    color: #2763C9;
    text-decoration: none;
}

section div.contents .card.right h2{
    font-size:16px;
}

section div.contents .card.right table{
    width:100%;
}

section div.contents .card.right table tr th,
section div.contents .card.right table tr td{
    font-size:14px;
    font-weight: normal;
    padding:10px 0;
}

section div.contents .card.right table tr th{
    text-align: left;
}

section div.contents .card.right table tr td{
    text-align: right;
}


section div.contents .card .memo strong{
    display: block;
}

section div.contents open{
    display:block;
    width:100%;
}

#main section h3 {
    width: 100%;
    margin-bottom: 0px;
    font-size:16px;
}

section div.contents div.content{
    position: relative;
    width:100%;
    max-width:calc(100% / 3 - 15px);
    background:#FFF;
    padding:20px;
    margin:0;
    border-radius: 10px;
    display: flex;
    flex-direction:column;
    box-shadow: 0 0 5px #0001;
}

section div.contents div.content figure{
    display: inline-block;
    width:50px;
    height:50px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 7px;
    flex-shrink: 0;
}

.content figure.page    {background:#1C6AD6}
.content figure.image   {background:#26A851}
.content figure.navi    {background:#F7B33A}
.content figure.pdf     {background:#E74C3C}
.content figure.color   {background:#8046C8}
.content figure.template{background:#10AAA1}
.content figure.parts   {background:#F4603D}


section div.contents div.content .text{
    padding:15px 0;
    width:100%;
}

section div.contents div.content .text h3{
    color:#00284a;
}

section div.contents div.content .text p{
    text-align: left;
    font-size: 12px;
    margin-bottom:20px;
}

section div.contents div.content a{
    position: absolute;
    bottom:20px;
    left:20px;
    color:#2763C9;
    text-decoration: none;
    font-weight: 500;
}

section div.contents div.content figure img{
    width:30px;
    height:30px;
}


section div#technical.contents div.content{
    max-width: calc(100% / 2);  
}

section div#technical.contents div.content .flex{
    display: flex;
    align-items: center;
    gap:20px;
}

section div.contents.open{
    display:block;
}

section div.contents.open div.content{
    width:100%;
    display: flex;
    gap:30px;
    margin-bottom:30px;
    max-width:100%;
}

section div.contents.open  div.content figure{
    width:130px;
    height:130px;
    background:#FFF;
    outline:1px solid #ccc;
    padding:0;
    margin:0;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

section div.contents.open  div.content figure:hover{
    background:#FFF;
}

section div.contents.open  div.content figure img{
    width:30px;
    height:30px;
}

section div.contents.open  div.content .text{
    width:calc(100% - 180px);
    text-align: left;
}

section div.contents.open div.content .text h3{
    width:auto;
    font-size:20px;
    margin:0;
    padding:0;
    font-weight: 700;
}

section div.contents.open div.content .text p{
    text-align: left;
    display:block;
}



/* rootcheck
---------------------------------------------*/
section#rootcheck{
    margin-top:40px;
    background:#FFF;
    border-radius: 10px;
    outline: 1px solid #CCC;
    padding: 20px 40px;
}


section#rootcheck details p{
    text-align: left;
}

section#rootcheck summary{
    font-size:18px;
    font-weight: 700;
    cursor: pointer;
    margin-bottom:20px;
}

section#rootcheck div{
    font-size:16px;
    font-weight: 700;
    margin-bottom:5px;
}

section#rootcheck p{
    font-size:14px;
}

#pages ul{
    list-style: none;
    padding:0;
    margin:0;
}

#pages ul li{
    padding:0;
    margin:0 0 5px;
    border:1px solid #ccc;
    border-radius:5px;
    background:#f0f0f0;
}

#pages ul li a{
    display:block;
    padding:5px 10px;
    color:#333;
    text-decoration: none;
}

#pages ul li:hover{
    outline:2px solid #333;
    background:#FFF;
}


/* editor
---------------------------------------*/
#editor{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    background:#000C;
    transform: scale(0.0);
    opacity:0;
    pointer-events: none;
    transition-duration: .5s;
}

#editor.active{
    transform: scale(1.0);
    opacity:1.0;
    pointer-events:auto;
}


#editor #editormenu{
    padding:20px 0 0;
    margin:0;
    height: 50px;
}

#editor #editormenu *{
    color:#FFF;
}


#editor iframe{
    width:100%;
    height:calc(100vh - 50px);
    overflow-y: auto;
    background:#FFF;
}
</style>
</head>
<body>
<div id="main">
<div class="inner">

<section>
<div id="topmenu" class="contents">
    <div class="left">    
        <div id="toptitle">
            <h1><?=$lang['nav_header1'][$lng]?></h1>
            <div class="welcome">Welcome to 3Dvenue-CMS</div>
        </div>
        <div class="card">
            <div id="score100">100</div>
            <div>
                <p class="memo">
                <strong>Google PageSpeed Insights</strong>
                <?=$lang['indextop'][$lng]?>
                </p>
                <a href="https://pagespeed.web.dev/" target="insights" class="btn" id="toInsights">PageSpeed Insightsへ</a>
            </div>
        </div>
    </div>
    <div class="card right">
        <h2>Site Information</h2>
        <table>
            <tr><th>CMS Root<th><td><a href="<?=$root?>" target="_blank"><?=$root?></a><td></tr>
            <tr><th>CMS Version<th><td>1.2.0-RELEASE<td></tr>
            <tr><th>PHP Version<th><td><?=phpversion()?><td></tr>
            
        </table>
    </div>
</div>
</section>

<section>
<h2><?=$lang['index01'][$lng]?></h2>

<div id="analog" class="contents">

    <div class="content">
            <figure class="page">
            <img src="./lib/edit.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['page_edit'][$lng]?></h3>
            <p><?=$lang['page_edit_memo'][$lng]?></p>
        </div>
        <a href="editor.php"><?=$lang['open'][$lng]?> →</a>
    </div>

    <div class="content">
            <figure class="image">
            <img src="./lib/image.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['file_edit'][$lng]?></h3>
            <p><?=$lang['file_memo'][$lng]?></p>
        </div>
        <a href="file.php"><?=$lang['open'][$lng]?> →</a>
    </div>

    <div class="content">
            <figure class="navi">
            <img src="./lib/navigation.svg">
        </figure>
        <div class="text">
            <h3><?=$lang['navi_edit'][$lng]?></h3>
            <p><?=$lang['navi_edit_memo'][$lng]?></p>
        </div>
        <a href="navi.php"><?=$lang['open'][$lng]?> →</a>
    </div>

</div>
</section>

<section>

<h2><?=$lang['index02'][$lng]?></h2>

<div id="technical" class="contents">
    <div class="content">
        <div class="flex">
            <figure class="parts">
                <img src="./lib/parts.svg">
            </figure>
            <h3><?=$lang['parts_edit'][$lng]?></h3>
        </div>
        <div class="text">
            <p><?=$lang['parts_edit_memo'][$lng]?></p>
        </div>
        <a href="parts.php"><?=$lang['open'][$lng]?> →</a>
    </div>

    <div class="content">
        <div class="flex">
            <figure class="template">
                <img src="./lib/template.svg">
            </figure>
            <h3><?=$lang['template_edit'][$lng]?></h3>
        </div>
        <div class="text">
            <p><?=$lang['template_edit_memo'][$lng]?></p>
        </div>
        <a href="template.php"><?=$lang['open'][$lng]?> →</a>
    </div>

</div>
</section>

</div>
</div><!-- main -->
<div id="footer">
<div class="inner">
    <div id="copy">&copy; 2026 3Dvenue. All rights reserved.</div>
</div></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){

        $('a').on('click', function(){
            parent.sessionStorage.setItem('content', $(this).attr('href'));
        });

        $('#language').on('change',function(){
            const lng = $(this).val();
            $.post('lang.php',{
                language:lng
            },function(){
                location.reload();
            });
        });

        $('#pages li').on('click',function(){
            let pid = $(this).data('pid');
            $('#editor iframe').attr('src','editor.php?pid='+pid);
        })

        $('#editor .close').on('click',function(){
            $('#editor').removeClass();
        })

        $('#main section h2 span').on('click',function(){
            console.log('open');
          let id = $(this).data('id');
          $('div#'+id).toggleClass('open');
        })

    });
</script>
</body>
</html>
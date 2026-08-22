<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
$file = '../common/inc/root.txt';
if (file_exists($file)) {
    $roottext = file_get_contents($file);
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = str_replace(basename(__DIR__) . '/', '', $url);
    $url = str_replace('index.php', '', $url);
    if ($roottext !== $url) {
        file_put_contents($file, $url);
    }
}
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
    <style>

        html {
            padding:0;
            margin:0;
            box-sizing: border-box;
            font-size: 16px;
            height:100%;
        }

        body{
            padding:0;
            margin:0;
            height:100%;
            background:#F4F5F6;
            min-width:880px;
        }

        body * {
            font-family: "Segoe UI", "Meiryo", sans-serif;
            line-height: 1.5;
            box-sizing: border-box;
        }

        h1,h2,h3,h4{
            letter-spacing: .1em;
        }

        p {
            font-size: 14px;
        }



        #indexwrap{
            display: flex;
            height:100%;
        }


        #indexwrap #nav{
            width:240px;
            flex-shrink:0;
            height:100%;
            transition-duration: .3s;
            padding-bottom:50px;    
        }

        #indexwrap #contents{
            height:calc(100vh - 120px);
            overflow-y:auto;
        }

        #indexwrap #contents::-webkit-scrollbar{
            width:10px;
        }

        #indexwrap #contents::-webkit-scrollbar-track{
            background:#1A2535;
        }

        #indexwrap #contents::-webkit-scrollbar-thumb{
            background:#8A8F98;
            border-radius:10px;
            border:2px solid #1A2535;
        }

        #indexwrap #contents::-webkit-scrollbar-thumb:hover{
            background:#B0B4BC;
        }

        #index_mein{
            flex:1;
            height:100%;
        }

        #indexwrap.wide #nav{
            margin-left:-240px;
        }

        /* #header
        -------------------------------------------*/
        #header{
            line-height: 1.0;
            padding:0 20px;
            border-bottom:1px solid #DFE0E2;
            height:70px;
            background:#FFF;
            display: flex;
            align-items: center;
        }


        #header #menu{
            width:30px;
            height:30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        #header h2{
            margin-left:25px;
            font-size:15px;
            letter-spacing: 0.1rem;
            color:#1A2434;
        }

        #header #menu img{
            width:27px;
            height:27px;
            opacity:0.7;
            transition-duration: .3s;
        }

        #header #menu:hover img{
            opacity:1.0;
        }



        #selectLang{
            position:fixed;
            top:0;
            right:20px;
            display: flex;
            align-items: center;
            height:70px;
        }

        #selectLang #language{
            padding:10px 0px;
            border:none;
            outline:none;
            cursor: pointer;
        }

        /* nav
        -------------------------------------------*/

        #nav{
            background:#1A2434;
            position:relative;
        }


        #nav #logo{
            display: flex;
            justify-content: left;
            align-items: center;
            width:100%;
            padding:0 20px;
            height:70px;
            gap:10px;
            border-bottom:1px solid #DFE0E233;
        }

        #nav #logo img{
            height:50px;
            width:auto;
        }

        #nav #logo h1{
            font-size:24px;
            color:#EEF;
        }

        #nav h2{    
            font-size:14px;
            color:#EEF;
            font-weight:normal;
            text-align: left;
            padding:0 20px;
            margin:30px 0 10px;
            letter-spacing: 0.1em;
        }

        #nav ul{
            list-style: noone;
            padding:0;
            margin:0;
        }

        #nav ul li{
            padding:5px 10px;
        }

        #nav #naviclose li span,
        #nav ul a{
            color:#EEF;
            display: flex;
            align-items: center;
            width:100%;
            text-decoration: none;
            font-size:14px;
            font-weight: 500;
            height:40px;
            padding:0 10px;
            margin:0;
            position:relative;
            cursor: pointer;
        }

        #nav #naviclose li span img,
        #nav ul a img{
            margin-right:5px;
        }

        #nav ul a:after{
            position:absolute;
            right:5px;
            top:10px;
            content:"〉";
        }

        #nav #naviclose{
            position:absolute;
            bottom:0;
            left:0;
            width:100%;
        }

        #nav #naviclose li{
            border-top:1px solid #DFE0E233;
        }

        #nav #naviclose li span{
            color:#EEF;
            font-size:12px;
        }

        /* index_mein
        --------------------------------------------*/
        #index_mein #content{
            display: block;
            width:100%;
            height:calc(100% - 70px);
            border:none;
            line-height: 1.0;
            margin:0;
            padding:0;
        }
        
    </style>
</head>
<body>
<div id="indexwrap">
    <div id="nav">
        <div id="logo">
            <a href="./top.php" target="content">
                <img src="./lib/logo.webp" alt="logo">
            </a>
            <h1>3Dvenue</h1>
        </div>
        <div id="contents">
        <ul>
            <li id="dashbord">
                <a href="./top.php" target="content"><img src="./lib/home.svg"><?=$lang['dash'][$lng]?></a>
            </li>
        </ul>

        <h2><?=$lang['nav_header1'][$lng]?></h2>

        <ul>
            <li>
                <a href="./ai.php" target="content">
                    <img src="./lib/ai.svg">AI
                </a>
            </li>
            <li>
                <a href="./editor.php" target="content">
                    <img src="./lib/edit.svg"><?=$lang['page_edit'][$lng]?>
                </a>
            </li>
            <li>
                <a href="./images.php" target="content">
                    <img src="./lib/image.svg"><?=$lang['image_edit'][$lng]?>
                </a>
            </li>
            <li>
                <a href="./pdf.php" target="content">
                    <img src="./lib/pdf.svg"><?=$lang['pdf_edit'][$lng]?>
                </a>
            </li>
            <li>
                <a href="./mp3.php" target="content">
                    <img src="./lib/mp3.svg"><?=$lang['audio_edit'][$lng]?>
                </a>
            </li>
            <li>
                <a href="./glb.php" target="content">
                    <img src="./lib/glb.svg"><?=$lang['glb_edit'][$lng]?>
                </a>
            </li>
            <li>
                <a href="./navi.php" target="content">
                    <img src="./lib/navigation.svg"><?=$lang['navi_edit'][$lng]?>
                </a>
            </li>

        </ul>

        <h2><?=$lang['nav_header2'][$lng]?></h2>

        <ul>
            <li>
                <a href="./parts.php" target="content">
                    <img src="./lib/parts.svg"><?=$lang['parts_edit'][$lng]?>
                </a>
            </li>
            <li>
                <a href="./template.php" target="content">
                    <img src="./lib/template.svg"><?=$lang['template_edit'][$lng]?>
                </a>
            </li>
        </ul>

        <h2><?=$lang['other'][$lng]?></h2>

        <ul>
            <li>
                <a href="./report.php" target="content">
                    <img src="./lib/template.svg"><?=$lang['access'][$lng]?>
                </a>
            </li>
        </ul>
    </div>
    <ul id="naviclose">
    <li>
        <span><img src="./lib/close.svg"><?=$lang['nav_close'][$lng]?></span>
    </li>
    </ul>

    </div>
    <div id="index_mein">
        <div id="header">
            <div id="menu">
                <img src="./lib/menu.svg">
            </div>
            <h2><?=$lang['dash'][$lng]?></h2>

            <div id="selectLang">
            <img src="./lib/world.svg">
            <?=selectLanguage()?>
            </div>
        </div>
        <iframe src="top.php" id="content" name="content"></iframe>
    </div><!-- index_mein -->

</div><!-- indexwrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){

        const content = sessionStorage.getItem('content');

        if(content){
            $('#content').attr('src', content);
        }

        $('a[target="content"]').on('click', function(){
            sessionStorage.setItem('content', $(this).attr('href'));
        });

        $('#menu,#naviclose').on('click',function(){
            $('#indexwrap').toggleClass('wide');            
        })

        $('#language').on('change',function(){
            const lng = $(this).val();
            $.post('lang.php',{
                language:lng
            },function(){
                location.reload();
            });
        });

    });
</script>
</body>
</html>
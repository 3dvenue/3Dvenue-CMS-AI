<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submit = $_POST['submit'] ?? '';
    $html = $_POST['html'] ?? '';
    $map = $_POST['map'] ?? '';
    if($submit == 'admin'){
        file_put_contents('./common/nav.txt', $html);        
    }

    if($submit == 'public'){
        file_put_contents('./common/nav.txt', $html);        
        file_put_contents('../common/inc/nav.txt', $html);
        file_put_contents('../common/inc/map.txt', $map);
    }
}

include_once('../common/inc/dbcall.php');
$pages = [];
$sql = "SELECT * FROM pages";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pages[] = $row;
}
include_once('./lang.php');

$layout = file_get_contents('../common/layout/default.html');
// $layoutcss = file_get_contents('../common/layout/default.css');
$nav = file_get_contents('./common/nav.txt');

$main ='<div class="inner"><h2>'.$lang['navi_edit'][$lng].'</h2><p style="line-height:2.0;">'.$lang['navi_howto'][$lng].'</p></div>';
$layout = str_replace('<v>main</v>', $main, $layout);
$layout = str_replace('<v>nav</v>',  $nav,  $layout);

?>
<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" href="/favicon.ico">
    <title>3DVenue: Open Source CMS (MIT Licensed)</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <link rel="stylesheet" type="text/css" href="../common/layout/default.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    #body{
        border:1px solid #1A243477;
        background:#FFF;
        box-shadow: 0 0 10px #0002;
        border-radius: 10px;
        overflow: hidden;
    }

    #main{
        min-height:calc(100% - 50px);
        padding:0;
    }

    #main h2{
        padding:40px 0;
        color:#1A2434;
    }

/* content
---------------------------------------------*/

    header{
        background:#f0f0f0;
        padding:20px 0;
    }

   header .inner{
    display: flex;
    align-items: center;
    justify-content: space-between;
   }

    div#headertext{
        font-weight:600;
        color:#1A2434;
    }
    main{
        min-height:calc(100vh - 400px);
        padding-bottom:40px;
        position:relative;
    }

    #body main h2{
        padding:30px 0 20px;
        color:#1A2434CC;        
    }

    nav{
        background:#1A2434;
        user-select: none;
        border-bottom:1px solid #ccc;
    }

    nav .inner{
        position: relative;
    }

    nav .inner #new{
        position: absolute;
        right:0;
        top:0;
        display: none;
    }

    nav ul{
        list-style: none;
        padding:0;
        margin:0;
    }

    nav ul li{
        padding:0;
        margin:0;
        position:relative;
        cursor: pointer;
    }

    nav ul li:hover{
        background: #FFF3;
    }

    nav ul li a{
        display:block;
        text-decoration: none;
        color:#333;
        padding:0 20px;
        pointer-events: none;
        line-height: 1.0;
        font-size: 14px;
        color:#EEF;
        font-weight: 700;
    }

    nav ul li a:hover{
        background:#FFF9;
    }

    nav ul.nav0{
        display: flex;
        justify-content:left;
        border-left:1px solid #FFF9;
    }

    nav ul.nav0 > li{
        border-right:1px solid #FFF9;
    }

    nav ul.nav0 > li > a{
        display: flex;
        align-items: center;
        height:50px;
    }

    nav ul.nav1{
        position:absolute;
        top:100%;
        left:0;
        background:#1A2434;
        width:100%;
        z-index: 1000;
    }

    nav ul.nav1 > li > a{
        display: flex;
        align-items: center;
        height:0;
        overflow: hidden;
    }

    nav ul.nav0 li.active ul.nav1 > li > a,
    nav ul.nav0 li:hover ul.nav1 > li > a{
        height:40px;
    }

    nav ul.nav1 > li:after{
        height:40px;
    }

    nav ul.nav1 > li:hover{
        border-bottom:2px solid #FFF;
    }

    nav ul.nav2{
        display:none;
    }

    nav ul.nav0 > li.active{
        /*pointer-events: none;*/
    }

    nav ul li.active{
        background:#FFF3;
    }

    nav ul.nav1 li.active{
        background:#FFF6;
    }

    nav ul.nav0 li.hidden,
    nav ul.nav1 li.hidden{
        background:#999;
    }

    nav ul.nav0 li.hidden ul,
    nav ul.nav0 li.hidden ul li,
    nav ul.nav0 li.hidden ul li a{
        pointer-events: none;
    }

    nav ul.nav0 > li.active ul.nav1 > li{
        pointer-events: auto;
    }

    .ui-state-highlight{
        background:#0003;
    }

    ul.nav1 .ui-state-highlight{
        height: 40px;
    }

    ul#plus li{
        background:#1A2434;
        color:#FFF;
        text-align: center;
        padding:10px 0;
    }

    nav #menu{
        display: none;
    }


/*    #navieditor
---------------------------------------------*/
    #navieditor{
        position:fixed;
        bottom:80px;
        right:40px;
        padding:0px;
        background:#F0f0f0;
        border:2px solid #CCC;
        width:max-content;
        height:max-content;
        border-radius: 5px;
        transform: scale(0.0);
        box-shadow:3px 3px 10px #0002;
    }

    #navieditor.active{
        transform: scale(1.0);
    }

    #navieditor table{
        background:#FFF;
        border-collapse: collapse;
    }

    #navieditor table th,
    #navieditor table td{
        border:1px solid #ccc;
    }

    #navieditor table th{
        padding:3px 5px;
        text-align: left;
        font-size:14px;
        font-weight: normal;
    }

    #navieditor input,
    #navieditor select,
    #navieditor button{
        border:1px solid #D3E3FC;
        height:100%;
        width:100%;
        padding:5px 10px;
    }


    #navieditor table tr.url,
    #navieditor table.p0 tr.slug{
        display:none;
    }

    #navieditor table tr.slug,
    #navieditor table.p0 tr.url{
        display: table-row;
    }

    .handle {
        text-align: right;
        background:#1A2434;
        height:25px;
        cursor: move;
        width: 100%;
        display: flex;
        justify-content:left;
        align-items:center;
        color: #EEF;
        font-size: 14px;
        padding:0 1em;
        font-weight: 700;
    }


    #bottomMenu{
        position:fixed;
        bottom:0;
        right:0;
        width:280px;
        margin:0 auto -10px;
        height:70px;
        padding:2px 10px;
    }

    #savebtn{
        display: flex;
        gap:20px;
        align-items: center;
        justify-content: center;
    }

    #savebtn button{
        padding:5px 30px;
        border-radius:5px;
        border:none;
        background-color:#F6B34B;
        color:#FFF;
        cursor: pointer;
    }

    #savebtn button:hover{
        outline:2px solid #FFF;
    }

    #newtrash{
        display: flex;
        justify-content: right;
        align-items: center;
        gap:20px;
        width:100%;
        padding:1px 5px;
    }

    #newnav,
    #trash{
        width:30px;
        height:30px;
        display:flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size:20px;
        font-weight: bold;
        color:#666;
    }

    #newnav:hover,
    #trash:hover{
        background:#e0e0e0;
    }

    #newnav{
        border:1px solid #ccc;
    }

    #trash img{
        height:80%;
        width:auto;
    }    
</style>
</head>
<body>
<div id="main">
    <div class="inner">
    <h2><?=$lang['navi_edit'][$lng]?></h2>

<div id="body">
    <?=$layout?>
</div><!-- #body-->

    </div>
</div>
<div id="footer">
<div class="inner">
    <div id="copy">&copy; 2026 3Dvenue. All rights reserved.</div>
</div>
</div>


<div id="navieditor">
<h3></h3>
<div class="handle">Navigtion Editor</div>
<table>
    <tr><th>Name</th><td><input type="text" name="name" id="name" value=""/></td></tr>
    <tr><th>Link</th><td><select name="pid" id="pid">
        <?php foreach ($pages as $row) { ?>
            <option value="<?=$row['pid']?>"><?=$row['name']?></option>
        <?php } ?>
        <option value="0">URL</option>
    </select></td></tr>
    <tr class="url"><th>url</th><td><input type="text" name="link" id="link" value="" placeholder="https://cms.3dvenue.jp" /></td></tr>
    <tr class="slug"><th>slug</th><td><input type="text" name="slug" id="slug" value=""/></td></tr>
    <tr><th>TARGET</th><td><select name="target" id="target"><option value="_self">_self</option><option value="_blank">_blank</option><option value="sub">New Tab</option></select></td></tr>
    <tr>
        <td colspan="2" style="text-align: right;padding-right:5px">
            <div id="newtrash">
                <span id="newnav">＋</span>
                <span id="trash"><img src="./lib/trash.svg"></span>
            </div>
        </td>
    </tr>
</table>
</div>



<div id="bottomMenu">
    <div id="savebtn"><button id="admin"><?=$lang['save'][$lng]?></button><button id="public"><?=$lang['publish'][$lng]?></button></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function(){

makeMap();

    function makeMap(){
        let map = '';
        $('nav ul li').each(function(){
            let p = $(this).attr('p') ?? '';
            if($.isNumeric(p)){
             let slug = $(this).children('a').attr('href').trim();
             map += p+','+slug+'\n';
            }
        })
        return map;
    }

    $('nav ul.nav2').remove();

    $(document).on('click','nav ul.nav0 > li',function(){
        $('nav ul.nav0 > li').removeClass('active');
        $(this).addClass('active');
        targetInfoGet($(this),'nav0');
        $('#navieditor').addClass('active');
        addPulus();
    })

    $(document).on('click','nav ul.nav1 > li',function(e){
        e.stopPropagation();
        let id = $(this).parent('ul').attr('id');
        if(id == 'plus'){
            $(this).attr('p','1');
            $(this).html('<a href="/NewChild/" target="_self">NewChild</a>');
            $('ul#plus').removeAttr('id');
        }

        $('nav ul.nav1 > li').removeClass('active');
        $(this).addClass('active');    
        targetInfoGet($(this),'nav1');
        $('#navieditor').addClass('active');
    })


    function addPulus(){
        let licount = $('ul.nav0 > li ul#plus').length;
        if(licount > 0){
            $('ul.nav0 > li ul#plus').remove();
        }
        $('ul.nav0 > li ul:not(:has(li))').remove();
        licount = $('ul.nav0 > li.active ul').length;
        if(licount == 0){
            $('ul.nav0 > li.active').append('<ul id="plus" class="nav1"><li>＋</li></ul>');
        }
    }

    $(document).on('keydown', function(e){        
        let nav = $('#navieditor').attr('data-nav');
        // 削除処理
        if(e.key === 'Delete'){
            $('ul.'+nav+' li.active').addClass('hidden');
            if(confirm('<?=$lang['navi_confirm'][$lng]?>')){
                let licount = $('ul.'+nav+' li.active').parent().children('li').length;
                if(licount <= 1){
                    $('ul.'+nav+' li.active').parent('ul').remove();                    
                }else{
                    $('ul.'+nav+' li.hidden').remove();
                }
            }
        }

        // 復活処理
        if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z'){
             $('ul.'+nav+' li.active').removeClass('hidden');
        }

        // 入力確定
        if(e.key === 'Enter' && $('#navieditor').find(':focus').length){
            $('#navieditor :focus').blur();
            e.preventDefault();
        }

    });



    $('#trash').on('click',function(){
        let nav = $('#navieditor').attr('data-nav');
        $('ul.'+nav+' li.active').addClass('hidden');
        if(confirm('<?=$lang['navi_confirm'][$lng]?>')){
            let licount = $('ul.'+nav+' li.active').parent().children('li').length;
            if(licount <= 1){
                $('ul.'+nav+' li.active').parent('ul').remove();                    
            }else{
                $('ul.'+nav+' li.hidden').remove();
            }
        }
    })


    $('#newnav').on('click',function(){
        console.log('click');
        let nav = $('#navieditor').attr('data-nav');
        if(nav == 'nav0'){
            $('ul.nav0').append('<li p="1"><a href="/Newnavi/" target="_self">Newnavi</a></li>');
        }
        if(nav == 'nav1'){
            $('ul.nav0.active').css('background','red');
            $('ul.nav0 > li.active > ul.nav1').append('<li p="1"><a href="/NewChild/" target="_self">NewChild</a></li>');
        }

    })


    $('#new').on('click',function(){
        $('ul.nav0').append('<li p="1"><a href="/Newnavi/" target="_self">Newnavi</a></li>');
    })

    function targetInfoGet($this,nav){
      let p = $this.attr('p') ?? '0';
      let href = $this.children('a').attr('href');
      let target = $this.children('a').attr('target');
      let name = $this.children('a').text();
      if(p == '0'){
        // $('#slug').val('');
        $('#link').val(href);
      }else{        
        $('#slug').val(href);
        // $('#link').val('');
      }
      $('#name').val(name);
      $('#pid').val(p);
      $('#navieditor').attr('data-nav',nav);
      $('#target').val(target);
      $('#navieditor table').removeClass().addClass('p'+p);
    }

    $(document).on('click',function(e){
        if (!$(e.target).closest('nav,#navieditor').length) {
           $('nav ul li,#navieditor').removeClass('active');
           addPulus();
        }
    });

  $('ul.nav0').sortable({
    axis: 'x',
    cursor: 'move',
    opacity: 0.7,
    placeholder: 'ui-state-highlight',
    
    // ドラッグが終わった瞬間に実行される処理
    update: function(event, ui) {
      console.log('並べ替え完了！このままHTMLとして保存できます');
    }
  });

  $('ul.nav1').sortable({
    axis: 'y',
    cursor: 'move',
    opacity: 0.7,
    placeholder: 'ui-state-highlight',
    update: function(event, ui) {
      console.log('並べ替え完了！このままHTMLとして保存できます');
    }
  });

    $('ul.nav1').sortable({
        connectWith: '.nav1',
        placeholder: 'ui-sortable-placeholder',
        opacity: 0.8,
        update: function(event, ui) {
        }
    });

    $('#navieditor').draggable({
        handle: '.handle',
        containment: 'window',
        opacity: 0.8
    });

    $('#navieditor *').on('input',function(){
        let nav = $('#navieditor').attr('data-nav');
        let name = $('#name').val();
        let slug = $('#slug').val();
        let link = $('#link').val();
        let pid = $('#pid').val();
        let target = $('#target').val();
        if(pid == '0'){
            $('ul.'+nav+' li.active > a').attr('href',link);
        }else{
            $('ul.'+nav+' li.active > a').attr('href',slug);                       
        }
        $('ul.'+nav+' li.active > a').text(name);
        $('ul.'+nav+' li.active > a').attr('target',target);
        $('ul.'+nav+' li.active').attr('p',pid);
        $('#navieditor table').removeClass().addClass('p'+pid);
    })

    $('#savebtn button#admin,#savebtn button#public').on('click',function(){
        let map = makeMap();
        let navigation = $('#body nav ul').first().clone();
        navigation.removeClass('ui-sortable-handle ui-sortable');
        navigation.find('ul').removeClass('ui-sortable-handle ui-sortable');
        navigation.find('li').removeAttr('class');
        navigation.find('#new').remove();
        let submit = $(this).attr('id');
        $.post('navi.php',{
            html:navigation.prop('outerHTML').trim(),
            map:map,
            submit:submit
        });
    })


});
</script>
</body>
</html>
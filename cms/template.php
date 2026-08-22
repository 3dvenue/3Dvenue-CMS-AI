<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submit =   $_POST['submit'];
    $name   =   $_POST['name'] ?? '';
    $html   =   $_POST['html'] ?? '';
    $css    =   $_POST['css'] ?? '';
    $color    =   $_POST['data'] ?? '';

    $filehtml = $name.'.html';
    $filecss = $name.'.css';
    $cmsdir = './common/layout/';
    $rootdir = '../common/layout/';

    if($submit == 'save' || $submit == 'public'){
        file_put_contents($cmsdir.$filehtml,$html);
        file_put_contents($cmsdir.$filecss,$css);
    }

    if($submit == 'public'){
        //root save
        file_put_contents($rootdir.'default.html',$html);
        file_put_contents($rootdir.'default.css',$css);
    }

    if($submit == 'del'){
        if (file_exists($cmsdir.$filehtml)) unlink($cmsdir.$filehtml);
        if (file_exists($cmsdir.$filecss)) unlink($cmsdir.$filecss);        
    }

    if($submit == 'color'){
        file_put_contents('./common/css/color.css', $color);
        file_put_contents('../common/layout/color.css', $color);
    }

    exit;
}


include_once('../common/inc/dbcall.php');
$pid = $_GET['pid'] ?? null;
if (!$pid) {
    $sql = "SELECT pid FROM pages ORDER BY pid LIMIT 1";
    $stmt = $conn->query($sql);
    $pid = $stmt->fetchColumn();
}
$sql = "SELECT * FROM pages WHERE pid = ".$pid;
$stmt = $conn->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$main = $row["main"];
$css = $row["css"];

$sql = "SELECT * FROM pages";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pages[] = $row;
}

$color = file_get_contents('../common/layout/color.css');
$nav = file_get_contents('../common/inc/nav.txt');
$root = file_get_contents('../common/inc/root.txt');
// $color = file_get_contents('./common/css/color.css');
$color = preg_replace('/^[\t ]+/m', '', $color);

$directory = './common/layout/';

include_once('./lang.php');

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

<style>
body,html{
    padding:0;
    margin:0;
    height: 100%;
}

body{
    transition: all 0.5s ease;
}

/* headr
---------------------------------------------*/
 header{
    background:#FFF;
    height:50px;
 }

header .inner{
    display: flex;
    height:100%;
    align-items: center;
    justify-content: space-between;
}

 header .inner .left{
    display: flex;
    align-items: center;
    gap:10px;
 }

 header select{
    padding:5px 10px;
    border-radius: 7px;
    border:1px solid #ccC;
    user-select: none;
    cursor: pointer;
 }

 header #direct{
    background:#8047C4;
 }

 header .right{
    position: relative;
 }

header .right #colorbox{
    position: absolute;
    top:calc(100% + 5px);
    right:0;
    padding:2px 5px 5px;
    background:#EDF2FA;
    border-radius: 7px;
    box-shadow: 3px 3px 10px #0003;
    width:300px;
    z-index:1000;
    cursor: move;
    display: none;
 }

 header .right #colorbox.active{    
    display: block;
 }

header .right #colorbox h3{
    width:100%;
    font-size: 16px;
    padding:0 10px;
    color:#333;

}

header .right #colorbox h3:hover{
    opacity:0.5;
}

header .right #colorbox textarea{
    padding: 10px 20px;
    border: 1px solid #D3E3FC;
    line-height: 1.2;
    resize: none;
    font-size: 12px;
    font-family: Consolas;
    background: #303841;
    border-radius: 5px;
    width:100%;
    height:300px;
    overflow-y: auto;
    color: #EEF;
}

header .right #colorbox button{
    background: #FFF;
    border: 1px solid #ccc;
    color: #333;
    padding: 3px 20px;
    border-radius: 7px;
    cursor: pointer;
    display: block;
    margin:0 0 0 auto;
    margin-right:1px;
}

header .response{
    padding:3px 5px 0;
    border-radius: 7px;
    border: 1px solid #ccC;
    user-select: none;
    cursor: pointer;
    background:#FFF;
}

/* main
---------------------------------------------*/

main{
    padding:20px;
    height:calc(100vh - 80px);
    overflow: hidden;
}

main iframe{
    display: block;
    margin:0 auto;
    width:100%;
    height:calc(100vh - 180px);
    /*border:none;*/
    border:1px solid #ccc;
    transform-origin: top center;
}

main.sp iframe{
    border-radius:10px;
    width:390px;
}

main.tb iframe{
    border-radius:10px;
    width:768px;
}

/* CSS JS
----------------------------------------*/
#textEditor{
    position:absolute;
    bottom:0;
    left:0;
    width:100%;
    height:130px;
    padding:0;
    background:#EDF2FA;
    border: 1px solid #D3E3FC;
    text-align:right;
}


#textEditor.active{
    bottom:0;
    z-index: 1000;
    max-height:100%;
}

#textEditor.active span.hadle{
    position: absolute;
    top: -10px;
    left:0;
    display: block;
    height: 20px;
    width: 100%;
    cursor: ns-resize;
    user-select: none;
    z-index:1;
}

#textEditor.active .hadle:active {
    cursor: grabbing;
    /*background:#FFF3;*/
    border-color: #ddd #bbb #ddd #ccc;
    border-width: 1px 3px 3px 1px;
}

#textEditor h3{
    font-size:14px;
    font-weight: 500;
    height:25px;
    padding:0px 10px 0;
    cursor: pointer;
    border-bottom:none;
    font-weight: 500;
    box-sizing: border-box;
    margin:0;
    border:1px solid;
    border-color:#fff #ccc #ccc #eee;
    display: flex;
    align-items: center;
    justify-content:left;
    gap:0.5em;
}

#textEditor .close{
    position: static;
    background:none;
    border:none;
    font-weight: 900;
}

#textEditor #textarea{
    height:calc(100% - 65px);
    display: flex;
    gap:2px;
}


#textEditor textarea{
    width:100%;
    padding:10px 20px;
    border: 1px solid #D3E3FC;
    line-height:1.2;
    resize: none;
    font-size:14px;
    font-family: Consolas;
    background:#303841;
    color:#EEF;
    tab-size: 4;
}

#textEditor #savebtn{
    height:40px;
    padding:0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

#textEditor #savebtn > div{
    display: flex;
    gap:10px;
}

#savebtn button{
    padding:3px 20px;
    border-radius: 7px;
    border:none;
    outline: none;
    user-select: none;
    background:#1F6AD2;
    color:#FFF;
    cursor: pointer;
}

#savebtn input{
    max-width: 120px;
    padding:3px 10px;
    border-radius: 7px;
    border:1px solid #CCC;
    outline: none;
    user-select: none;
    background:#FFF;
    cursor: pointer;
}

#savebtn #tab button{
    background:#FFF;
    color:#333;
    font-weight: 700;
    background:#E8F1FF;
    border:1px solid #3B82F6;
    color:#3B82F6;
}

#savebtn.default #delTemplate{
    display: none;
}

#textEditor.html #tab #cssbtn,
#textEditor.css #tab #htmlbtn{
    background:#FFF;
    border:1px solid #ccc;
    color:#333;
}

#textEditor.html h3 span.css,
#textEditor.css h3 span.html,
#textEditor.html #css,
#textEditor.css #html{
    display:none;
}


</style>
</head>
<body>
<header>
<div class="inner">
<div class="left">
<form>
<select id="selectPage">
<?php foreach ($pages as $row) { ?>
    <option value="<?=$row['pid']?>"><?=$row['name']?></option>
<?php } ?>
</select>
</form>

<section id="template">
<?php
$files = glob($directory . "*.html");
?>
<select id="layout">
<?php
foreach ($files as $file) {
$filename = explode('.',basename($file))[0];
?>
<option value="<?=basename($file)?>"><?=$filename?></option>
<?php } ?>
</select>
</section>

<select id="scale">
<option value="1.0">100%</option>
<option value="0.9" selected="selected">90%</option>
<option value="0.8">80%</option>
<option value="0.7">70%</option>
<option value="0.6">60%</option>
<option value="0.5">50%</option>
</select>
</div>

<div id="center">
<button class="response" id="pc"><img src="./lib/desktop.svg"></button>
<button class="response" id="tb"><img src="./lib/tablet.svg"></button>
<button class="response" id="sp"><img src="./lib/mobile.svg"></button>
</div>

<div class="right">
    <button id="direct" class="btn">カラー編集</button>
    <div id="colorbox">
        <h3>Site Color</h3>
        <textarea id="colortext"><?=$color?></textarea>
        <button id="colorsave">Save</button>
    </div>
</div>
</div><!-- inner -->
</header>
<main>
<div class="inner">
<iframe id="preview" src=""></iframe>
</div>
</main>

<div id="textEditor" class="active css">
    <span class="hadle"></span>
    <h3>Template: <span class="css">CSS</span><span class="html">HTML</span></h3>
    <div id="textarea">
    <textarea id="html" class="codearea"></textarea>
    <textarea id="css" class="codearea"></textarea>
    </div>
    <div id="savebtn">
        <div id="tab">
            <button id="cssbtn" value="CSS">CSS</button>
            <span>⇄</span>
            <button id="htmlbtn" value="HTML">HTML</button>
        </div>

        <div id="save">
            <span>Name: <input type="text" id="templatename" name="" value="" required/></span>
            <button id="saveTemp" value="save">SAVE</button>
            <button id="publicTemp" value="public">PUBLIC</button>
            <!-- <button value="update">UPDATE</button> -->
            <button id="delTemplate" value="del">DELETE</button>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function(){

    let page = '';

    const layout = sessionStorage.getItem('layout');
    if (layout) {
        $('#layout').val(layout);
        $('#layout').val(layout).trigger('change');
    }

    $('#colorbox').draggable();

    const main = <?= json_encode($main) ?>;
    const layout_css = <?= json_encode($color) ?>;
    const layout_nav = <?= json_encode($nav) ?>;

    $('#selectPage').val(<?=$pid?>);

    const head_html = "<html>\n<head>\n" + 
        "<meta charset='UTF-8'>\n"+
        "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n"+
        "<style id='layout-color'>"+layout_css+"</style>\n<style id='templatecss'></style></head>\n<body>";

    const after_html = "</body></html>";

    async function setHTML(){

        let layout = $('#layout option:selected').text();
        let time = Date.now();

        let html = await $.get('./common/layout/' + layout + '.html?t=' + time);
        let css  = await $.get('./common/layout/' + layout + '.css?t=' + time);

        $('#html').val(html);
        $('#css').val(css);
        $('#templatename').val(layout);

        page = head_html + html + after_html;
        page = page.replace('<v>nav</v>', layout_nav);
        page = page.replace('<v>main</v>', main);

        let doc = $('#preview')[0].contentWindow.document;
        doc.open();
        doc.write(page);
        doc.close();

        $(doc).find('#templatecss').html(css);
        $('#preview').contents().find('#templatecss').html($('#css').val());
        $('#savebtn').removeClass().addClass(layout);
    }

    setHTML();

    $('#scale').on('change',function(){
        let scale = $(this).val();
        $('iframe').css('transform','scale('+scale+')');
    })

    $('#layout').on('change',function(){
        let layout = $(this).val();
        sessionStorage.setItem('layout', layout);
        setHTML();
    })


    // js and css updown 
    $('span.hadle').on('mousedown', function(e) {
        e.preventDefault();
        let targetPopup = $(this).closest('#textEditor');
        $(document).on('mousemove.resizer', function(moveEvent) {
            let h = $(window).height() - moveEvent.clientY;        
            if (h > 100 && h < $(window).height() * 0.95) {
                targetPopup.css('height', h + 'px');
            }
        });
        $(document).one('mouseup', function() {
            $(document).off('mousemove.resizer');
        });
    });

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


    $('.response').on('click',function(){
        $('main').removeClass().addClass(this.id);
    })

    // page change
    $('#selectPage').on('change', function(){
        location.href = 'template.php?pid=' + $(this).val();
    });

    $('#textEditor #tab button').on('click',function(){
        $('#textEditor').removeClass('css html')
        let tab = $(this).val();
        if(tab == 'CSS'){
            $('#textEditor').addClass('css');
        }else{
            $('#textEditor').addClass('html');
        }
    })

    $('#templatename').on('input',function(){
        let layout = $(this).val();
        $('#savebtn').removeClass().addClass(layout);
    })

    $('#save button').on('click',function(){
        let act = $(this).val();
        let name = $('#templatename').val();
        let html = $('#html').val();
        let css = $('#css').val();
        $.post('template.php', {
            submit: act,
            name: name,
            html: html,
            css: css
        }, function (data) {
            location.reload();
        });
    })

    $('#colortext').on('input',function(){
        let color = $(this).val();
        $('#preview').contents().find('#layout-color').text(color);
    })

    $('#direct').on('click',function(){
        $('#colorbox').toggleClass('active');

    })

    $('#colorsave').on('click',function(){
        let color = $('#colortext').val();
            $.post('template.php', {
                submit: 'color',
                data: color
            }, function (data) {
         $('#colorbox').removeClass('active');
         $('#colorbox').removeAttr('style');
        });
    })

})
</script>

</body>
</html>
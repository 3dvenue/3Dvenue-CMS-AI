<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $name = $_POST['name'] ?? '';
    if ($type == "delete" && !empty($name)) {
        if (file_exists($name)) {
            unlink($name);
        }
     }
     exit();
}

$directory = '../common/img/';
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
    #main h2{
        position:relative;
        padding-top:40px;
    }

    #main p{
        text-align: left;
        margin-bottom:40px;
    }

    #main h2 #new{
        position:absolute;
        top:20px;
        right:0;
    }

    #images ul{
        padding:20px;
        margin:0;
        list-style: none;
/*        display: grid;
        gap: clamp(10px, 10vw, 15px);
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
*/

        display:flex;
        flex-wrap:wrap;
        gap:20px;
        background:#1A243477;
        border-radius: 10px;
        box-shadow: 3px 3px 3px #0002 inset;
    }

    #images ul li{
        display: block;
        width: 130px;
        max-width:100%;
        font-size: 16px;
        padding: 5px 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-all;
        text-align: left;
        background:#FFF;
        border-radius: 5px;
        box-shadow: 0 0 5px #0001;
    }

    #images ul li figure{
        padding:0;
        margin:0;
        width:100%;
        /*height:100%;*/
        line-height:1.0;
        cursor: pointer;
        aspect-ratio: 3/2;
    }

    #images ul li:hover{
        outline:2px solid #26A851;
    }

    #images ul li figure figcaption{
        padding:0 10px;
        text-align: center;
        white-space: nowrap;     /* 改行させない */
        overflow: hidden;        /* はみ出た分を隠す */
        text-overflow: ellipsis; /* はみ出た分を「...」にする */
    }

    #images ul li figure img{
        width: 100%;
        height: 100%;
        object-fit: contain; /* 画像全体が収まるようにリサイズ */
        object-position: center; /* 中央に配置 */
    }


    /* imageupload
    ---------------------------------------*/
    #imagecheck,
    #imageupload{
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background:#000C;
        transform: scale(0.0);
        opacity:0;
        pointer-events: none;
        transition-duration: .5s;
    }

    #imagecheck .close,
    #imageupload .close{
        position: absolute;
        top: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        line-height: 1.0;
        background: #000;
        color: #FFF;
        font-weight: 500;
        font-size: 20px;
        width: 30px;
        height: 30px;
        cursor: pointer;
        z-index:1;
    }

    #imageupload .btn{
        background:#26A851;
        font-size:14px;
        padding:7px 20px;
    }

    #imagecheck.active,
    #imageupload.active{
        transform: scale(1.0);
        opacity:1;
        pointer-events: auto;
    }

    #imageupload #view{
        width:300px;
        height:300px;
        background-size:contain;
        background-repeat:no-repeat;
        background-position: center;
    }

    #imageupload #form{
        padding:20px 40px;
        background:#FFF;
        border-radius: 10px;
    }

    #inputbox button,
    /*#imagecrop input,*/
    #imageupload #form input{
        padding:5px 10px;
        border-radius: 5px;
        border:1px solid #666;
    }


    #imageupload #form p,
    #imageupload #form label{
        display: block;
        margin-bottom:20px;
    }

    #imageupload #form #btn{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #imageupload #form #delete{
        background:none;
        padding: 0 0px;
    }

    #imageupload #form label div{
        text-align: center;
        background:#FFF;
        color:#333;
        border:1px solid #333;
        cursor: pointer;
    }

    #imageupload #form #file{
        display: none;
    }

    #check{
        margin:20px;
        max-width:840px;
        max-height:calc(100vh - 300px);
        background: #FFF3;
        padding:2px;
    }

    #check #photo{
        width:100%;
        height:auto;
        max-height: 100%;
        object-fit:contain;
    }

     #inputbox #viewname{
        position: relative;
        user-select: none;
        background:#FFF;
        padding:5px 10px;
        cursor: pointer;
    }

    #viewname.copy::after{
        content:"copy";
        position:absolute;
        top:-40px;
        left:80%;
        background:#FFF;
        font-size:12px;
        padding:5px 10px;
        border-radius: 3px;
        box-shadow: 3px 3px 7px #0005;
    }

    #inputbox{
        position:fixed;
        gap:20px;
        bottom:0;
        left:0;
        width:100%;
        height:60px;
        display: flex;
        justify-content: center;
        align-items: center;
        background:#F0F0F0;
        z-index:10;
    }

    #inputbox #usagebox{
        position: relative;
    }

    #inputbox #usagebox #usagelist{
        position: absolute;
        bottom:calc(100% + 5px);
        left:0;
        min-width:100%;
        background:#FFF;
        font-size:14px;
        list-style: none;
        padding:0;
        border-radius: 5px;
        box-shadow: 3px 3px 7px #0003;
    }

    #inputbox #usagebox #usagelist li{
        display: flex;
        align-items: center;
        height:0;
        padding:0 20px;
        width:max-content;
        overflow: hidden;
        transition-duration: .3s;
    }

    #inputbox #usagebox #usagelist.active li{
        height:30px;
    } 

    #inputbox #usage{
        display: inline-block;
        background:#FFF;
        padding:2px 15px;
        border-radius: 3px;
        border:1px solid #666;
        margin-left:5px;
        cursor: pointer;
    }

    #inputbox button{
        background:#FFF;
        color:#333;
    }

    </style>
</head>
<body id="imagepage">
<div id="main">
<div class="inner">
    <h2><?=$lang['image_edit'][$lng]?><div id="new">＋</div></h2>
    <input type="range" id="imagesize" min="130" max="1280" step="1" value="150">
    <p><?=$directory?></p>
     <section id="images">
        <?php
            $files = glob($directory . "*.webp");
        ?>
    <ul>
    <?php
        foreach ($files as $file) {
        $filename = explode('.',basename($file))[0];
    ?>
    <li data-image="<?=basename($file)?>" data-name="<?=$filename?>">
        <figure>
            <img src="<?=$directory.basename($file)?>?t=<?=time()?>">
            <figcaption><?=basename($file)?></figcaption>
        </figure>
    </li>
    <?php } ?>
    </ul>
    </section>
    </div>
</div><!-- main -->

<div id="imageupload">
    <div class="close">✕</div>
    <div id="view"></div>
        <div id="form">
            <label><span class="btn"><?=$lang['image_select'][$lng]?></span>
            <input type="file" id="file" name="file" accept=".png, .jpg, .jpeg, .webp"></label>
            <p><input type="text" name="imgname" id="imgname" value="" placeholder="<?=$lang['image_name'][$lng]?>">.webp</p>
            <div id="btn">
            <button type="submit" id="submit" class="btn" name="submit" value="upload"><?=$lang['image_upload'][$lng]?></button>
            </div>
        </div>
    </div>
</div>

<div id="imagecheck">
    <div class="close">✕</div>
    
    <div id="check"><img id="photo" src="https://ai.3dvenue.jp/common/svg/photo.svg"></div>

        <div id="inputbox">
            <div id="viewname" value=""></div>
            <div id="usagebox">Usage:<span id="usage"></span><ul id="usagelist"></ul></div>
            <button type="submit" id="delete"  class="btn" name="submit" value="delete"><img src="./lib/trash.svg"></button>
        </div>
</div>

<div id="footer">
<div class="inner">
    <div id="copy">&copy; 2026 3Dvenue. All rights reserved.</div>
</div>
</div>
<div id="layout" style="display:none"><?= $layout ?></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
<script>
$(function() {
    let canvas = document.createElement('canvas');
    let ctx = canvas.getContext('2d');
    let hasImage = false;

    $('#imagesize').on('input',function(){
        let size = $(this).val();
        $('#images ul li').css('width',size+'px');
    })

    $('#file').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const validTypes = ['image/png', 'image/jpeg', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            $(this).val(''); 
            return;
        }

        if(!$('#imageupload').hasClass('edit')){
        const fileNameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
        $('#imgname').val(fileNameWithoutExt);
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            // プレビュー表示
            $('#view').css('background-image', 'url(' + e.target.result + ')');
            const img = new Image();
            img.onload = function() {
                let width = img.width;
                let height = img.height;
                const MAX_SIZE = 1280;

                if (width > MAX_SIZE || height > MAX_SIZE) {
                    if (width > height) {
                        height *= MAX_SIZE / width;
                        width = MAX_SIZE;
                    } else {
                        width *= MAX_SIZE / height;
                        height = MAX_SIZE;
                    }
                }

                // Canvasを画像ピッタリのサイズに設定（余白なし）
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                hasImage = true;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    $('#imgname').on('paste', function(e){
        const items = e.originalEvent.clipboardData.items;
        for(const item of items){
            if(item.type.startsWith('image/')){
                const dt = new DataTransfer();
                const file = item.getAsFile();
                dt.items.add(file);
                $('#file')[0].files = dt.files;
                $('#file').trigger('change');
                break;
            }
        }
    });

    // アップロード実行（#submitボタン）
    $('#submit').on('click', function() {
        if (!hasImage) {
            alert("画像を選択してください");
            return;
        }

        // #imgname から現在の値を取得（手入力で変更されていても反映）
        const finalFileName = $('#imgname').val();
        if (!finalFileName) {
            alert("画像名を入力してください");
            return;
        }

        // Canvasから高品質なWebPを作成して送信
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            
            // サーバーへ送るファイル名を「#imgnameの値.webp」に指定
            formData.append('file', blob, `${finalFileName}.webp`);

            $.ajax({
                url: './upload.php', 
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

    $('#new').on('click',function(){
        $('#imageupload').removeClass().addClass('active new');
        $('#imgname').val('');
        $('#view').css('background-image', '');
    });

let imageName = "";

    $('body#imagepage #images ul li').on('click',function(){
        $('#imagecheck').addClass('active');
        let image = $(this).data('image');
        let name = $(this).data('name');
        let imageurl = '../common/img/'+ image;
        $('#viewname').text(imageurl);
        $('img#photo').attr('src',imageurl);
        console.log(imageurl);
        imageName = name;
        $('#delete').attr('data-image',imageurl);

            $.post('objectUsage.php', {
                object: image
            }, function(res){
                console.log(res);
                $('#usage').text(res.length);
                $('#usagelist').empty();
                $.each(res, function(i, v){
                    $('#usagelist').append(
                        '<li>' + v.table + ' : ' + v.place + '</li>'
                    );
                });
            }, 'json').fail(function(xhr){
                console.log(xhr.status);
                console.log(xhr.responseText);
            });
    });

    $('#viewname').on('mousedown', function () {
        navigator.clipboard.writeText($(this).text());
        $(this).addClass('copy');
    }).on('mouseup mouseleave', function () {
        const $this = $(this);
        setTimeout(function(){
            $this.removeClass('copy');
        },500)
    });

    $('#usagebox').on('click',function(){
        $('#usagelist').toggleClass('active');
    })

    $('#imageupload .close,#imagecheck .close').on('click',function(){
        $('#imageupload,#imagecheck').removeClass('active');
    });


    $('#delete').on('click', function(){
        let image = $(this).attr('data-image');
        $.post('images.php', {
            type: 'delete',
            name: image
        }).done(function(data) {
            location.reload();
        })
    });

});    
</script>
</body>
</html>
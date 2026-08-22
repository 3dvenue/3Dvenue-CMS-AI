<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audio = $_FILES['audio'] ?? '';
    $name = $_POST['name'] ?? '';
    $submit = $_POST['submit'] ?? '';


    if($submit === '' || $name === ''){
        exit();
    }

    $mp3_dir = '../common/mp3/';
    $mp3_name = $name;

    if($submit == 'upload'){
        move_uploaded_file($audio['tmp_name'],$mp3_dir . $mp3_name);
        exit('ok');
    }

    if($submit == 'del'){
        unlink($mp3_dir . $mp3_name);
        exit('ok');
    }

}


$directory = '../common/mp3/';

if(!is_dir($directory)){
    mkdir($directory,0755,true);
}

$mp3dir = '../common/mp3/';

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
        body{
            font-family:sans-serif;
            padding:30px;
        }

        #new{
            position: absolute;
            top: 20px;
            right: 0;
        }


        #mp3{
            display: none;
        }

        #audios{
            padding: 2px;
            min-height:30px;
            margin:20px 0;
            list-style: none;
            display:grid;
            grid-template-columns: repeat(auto-fill, minmax(50%, 1fr));
            background: #1A243477;
            box-shadow: 3px 3px 3px #0002 inset;
        }

        #audios div{
            width:100%;
            display: flex;
            justify-content: left;
            gap:5px;
            height:30px;
            background:#FFF;
            padding:5px;
            border-bottom:1px solid #0009;
            border-right:1px solid #0009;
            cursor: pointer;
        }

        #audios div span{
            display: flex;
            align-items: center;
            height:100%;
        }

        #audios span.filename{
            font-size:12px;
        }

        #audios div:hover{
            background:#F0F0F4;
        }

        #audios div img{
            width:100%;
            height:100%;
            object-fit: contain;
        }


        #audios div .btnbox{
            display: flex;
            gap:5px;
            justify-content: right;
            margin: 0 0 0 auto;
        }

        #audios div .btnbox .btn{
            padding:2px 7px;
            background: #1D73DE;
            font-size: 10px;
            border-radius: 0;
            /*cursor: pointer;*/
        }



        #mp3upload{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap:20px;
            background: #000C;
            transform: scale(0.0);
            opacity: 0;
            pointer-events: none;
            transition-duration: .5s;
        }

        #mp3upload.active{
            transform: scale(1.0);
            opacity: 1;
            pointer-events: auto;
        }


        #mp3upload h2{
            color:#eef;
        }

        #mp3upload #view {
            width: 50vh;
            height: 50vh;
            max-width:300px;
            max-height:300px;
            aspect-ratio: 1 / 1;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }

        #mp3upload #view img{
            width:100%;
            height:100%;
            object-fit: contain;
        }

        #mp3upload #form {
            padding: 20px 40px;
            background: #FFF;
            border-radius: 10px;
        }

        #mp3upload #form p,#mp3upload #form label {
            display: block;
            margin-bottom: 20px;
        }

        #form input {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #666;
        }

        #mp3upload #form .btn{
            font-size: 14px;
            padding: 7px 20px;
            background: #1D73DE;
        }


        #mp3upload.check #form{
            display: none;
        }        
    </style>
</head>
<body id="mp3page">
<div id="main">
<div class="inner">
<h2><?=$lang['audio_edit'][$lng]?><div id="new">＋</div></h2>
<p><?=$directory?></p>

<section id="audios">
    <?php
        $files = glob($directory . "*.mp3");
        foreach ($files as $file) {
        $filename = explode('.',basename($file))[0];
    ?>
    <div data-image="<?=basename($file)?>" data-name="<?=$filename?>">
        <span>♪</span>
        <span class="filename"><?=basename($file)?></span>
        <span class="btnbox"><button class="del btn"><?=$lang['del'][$lng]?></button></span>
    </div>
    <?php } ?>
        </section>
    </div>
</div><!-- main -->

<div id="mp3upload">
    <div class="close">✕</div>
        <h2>check</h2>
        <div id="soundcheck"><audio src="" id="audio" controls></audio></div>
    <div id="form">
        <label for="mp3" class="btn"><?=$lang['select'][$lng]?></label>
        <input type="file" id="mp3" accept="audio/mpeg">
        <p>
            <input type="text" name="name" id="name" value="" placeholder="<?=$lang['name'][$lng]?>">
        </p>
        <div id="btn"><button type="submit" id="submit" class="btn" name="submit" value="upload"><?=$lang['save'][$lng]?></button></div>
   </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function(){

let canvas;

    $('#mp3').on('change', async function(){
        const file = this.files[0];
        if(!file) return;
        $('#name').val(file.name.replace(/\.mp3$/i,''));
        $('audio#audio').attr('src',URL.createObjectURL(file));
    });

    $(document).on('click','#audios > div',function(){
        let name = $(this).data('name');
        let file = '../common/mp3/'+name+'.mp3';
        $('#mp3upload').removeClass().addClass('active check');
        $('#mp3upload h2').text('check');
        $('audio#audio').attr('src',file);
    })

    $('#submit').on('click', function(e){
        e.preventDefault();

        let file = $('#mp3')[0].files[0];
        if(!file){
            alert('MP3 is Empty!');
            return;
        }

       let name = $('#name').val();
       if(name == ''){
            alert('Name is Empty!');
            return;
        }
       let audioname = name + '.mp3';
            const fd = new FormData();
            fd.append('audio',file);
            fd.append('name',audioname);
            fd.append('submit', 'upload');
            $.ajax({
                url:'mp3.php',
                type:'POST',
                data:fd,
                processData:false,
                contentType:false,
                success:function(res){
                    if(res == 'error'){
                        alert(res);
                        return;
                    }
                     location.reload();
                }
            });
        });


    $('#new').on('click',function(){
        $('#mp3upload').removeClass().addClass('active new');
        $('#mp3upload h2').text('new');
        $('audio#audio').attr('src','');
        $('#name').val('');
    });

    $('#mp3upload .close').on('click',function(){
        $('#mp3upload').removeClass();
        $('audio#audio').attr('src','');
        $('#name').val('');
    });

    $(document).on('click','#audios .del',function(e){
        e.preventDefault();
        e.stopPropagation();
        let name = $(this).closest('div').data('name');
        let filename = name+'.mp3';
        $.post('mp3.php',{
            submit:'del',
            name:filename
        },function(res){
            if(res == 'ok'){
                location.reload();
            }
        })
    })

    // $('.view').on('click',function(){
    //     let name = $(this).closest('li').data('name');
    //     window.open('../common/pdf/'+name+'.pdf','pdf');
    // })


})
</script>

</body>
</html>
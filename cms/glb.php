<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $glb = $_FILES['glb'] ?? '';
    $image = $_FILES['image'] ?? '';
    $name = $_POST['name'] ?? '';
    $submit = $_POST['submit'] ?? '';

    if($submit === '' || $name === ''){
        exit();
    }

    $glb_dir = '../common/glb/';
    $glb_name = $name . '.glb';
    $img_name = $name . '.webp';

    if($submit == 'upload'){
        move_uploaded_file($glb['tmp_name'],$glb_dir . $glb_name);
        move_uploaded_file($image['tmp_name'],$glb_dir . $img_name);
        exit('ok');
    }

    if($submit == 'update'){
        move_uploaded_file($image['tmp_name'],$glb_dir . $img_name);
        exit('ok');
    }

    if($submit == 'del'){
        unlink($glb_dir . $glb_name);
        unlink($glb_dir . $img_name);
        exit('ok');
    }

}


$directory = '../common/glb/';

if(!is_dir($directory)){
    mkdir($directory,0755,true);
}

$glbdir = '../common/glb/';

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


        #glb{
            display: none;
        }

        #glbs{
            padding:40px 0;
        }

        #glbs ul{
        padding: 20px;
            margin: 0;
            list-style: none;
            display: grid;
            gap: clamp(10px, 10vw, 15px);
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            background: #1A243477;
            border-radius: 10px;
            box-shadow: 3px 3px 3px #0002 inset;
        }


        #glbs ul li{
            width:100%;
            background:#FFF;
            border-radius: 10px;
            box-shadow: 0 0 5px #0001;
            padding:20px;
        }

        #glbs ul li figure{
            width:100%;
            aspect-ratio: 1/1;
        }

        #glbs ul li figure figcaption{
            text-align: center;
        }


        #glbs ul li figure img{
            width:100%;
            height:100%;
            object-fit: contain;
            cursor: pointer;
        }


        #glbs ul li .btnbox{
            padding-top:10px;
            display: flex;
            gap:10px;
            justify-content: right;
        }

        #glbs ul li .btnbox .btn{
            padding:2px 10px;
            background: #6826CB;
        }


        #glbupload{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap:10px;
            background: #000C;
            transform: scale(0.0);
            opacity: 0;
            pointer-events: none;
            transition-duration: .5s;
        }

        #glbupload.active{
            transform: scale(1.0);
            opacity: 1;
            pointer-events: auto;
        }


        #glbupload h2{
            color:#eef;
        }

        #glbupload p.memo{
            color:#FFF;
            font-size:12px;
        }

        #glbupload #form {
            padding: 20px 40px;
            background: #FFF;
            border-radius: 10px;
        }

        #glbupload.new #form p,#glbupload.new #form label {
            display: block;
            margin-bottom: 20px;
        }

        #form input {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #666;
        }

        #glbupload #form .btn{
            font-size: 14px;
            padding: 7px 20px;
            background: #6826CB;
        }


        #glbupload.new button#update{
            display:none;
        }

        #glbupload.view label.btn,
        #glbupload.view input#name,
        #glbupload.view button#submit{
            display:none;
        }
        
    </style>
</head>
<body id="glbpage">
<div id="main">
<div class="inner">
<h2><?=$lang['glb_edit'][$lng]?><div id="new">＋</div></h2>
<p><?=$directory?></p>

<section id="glbs">
    <?php
        $files = glob($directory . "*.glb");
    ?>
        <ul>
        <?php
            foreach ($files as $file) {
            $filename = explode('.',basename($file))[0];
        ?>
        <li data-image="<?=basename($file)?>" data-name="<?=$filename?>">
            <figure>
                <img src="<?=$glbdir.$filename?>.webp?t=<?=time()?>">
                <figcaption><?=basename($file)?></figcaption>
            </figure>
            <div class="btnbox"><button class="view btn"><?=$lang['check'][$lng]?></button><button class="del btn"><?=$lang['del'][$lng]?></button></div>
        </li>
        <?php } ?>
        </section>
    </div>
</div><!-- main -->

<div id="glbupload">
    <div class="close">✕</div>
        <h2>check</h2>
        <div id="3dcheck"><canvas id="model"></canvas></div>
        <p class="memo">Left Drag : Move / Wheel : Zoom / Right Drag : Rotate</p>
    <div id="form">
        <label for="glb" class="btn"><?=$lang['select'][$lng]?></label>
        <input type="file" id="glb" accept=".glb">
        <p>
            <input type="text" name="name" id="name" value="" placeholder="<?=$lang['name'][$lng]?>">
        </p>
        <div id="btn">
            <button type="submit" id="submit" class="btn" name="submit" value="upload"><?=$lang['save'][$lng]?></button>
            <button type="submit" id="update" class="btn" name="submit" value="update"><?=$lang['update'][$lng]?></button>
        </div>
   </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="importmap">
{
  "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js",
    "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.180.0/examples/jsm/"
  }
}
</script>

<script type="module">
import * as THREE from 'three';
import { OrbitControls } from "three/addons/controls/OrbitControls.js";
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

let currentModel = null;

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(40, 1, 0.1, 30);

const canvas = $('#model')[0];

const renderer = new THREE.WebGLRenderer({
    canvas,
    antialias:true,
    preserveDrawingBuffer:true
});

renderer.outputColorSpace = THREE.SRGBColorSpace;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.0;

renderer.setSize(400, 400);
renderer.setClearColor(new THREE.Color(0xe5e5e5));
renderer.setPixelRatio(window.devicePixelRatio);
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableRotate = true;
controls.enablePan = true;
controls.enableZoom = true;
controls.zoomSpeed = 0.3;
controls.panSpeed = 0.5;
controls.rotateSpeed = 0.5;

controls.mouseButtons = {
  LEFT: THREE.MOUSE.PAN,
  MIDDLE: THREE.MOUSE.DOLLY,
  RIGHT: THREE.MOUSE.ROTATE
};

const ambient = new THREE.AmbientLight(0xFFFFFF, 1.2);
scene.add(ambient);

const dirLight = new THREE.DirectionalLight(0xffffff, 1.5);
dirLight.position.set(10, 10, 10);
dirLight.castShadow = true;
dirLight.shadow.mapSize.width = 2048;
dirLight.shadow.mapSize.height = 2048;
dirLight.shadow.camera.left = -10;
dirLight.shadow.camera.right = 10;
dirLight.shadow.camera.top = 10;
dirLight.shadow.camera.bottom = -10;
dirLight.shadow.camera.near = 0.5;
dirLight.shadow.camera.far = 50;
scene.add(dirLight);


const hemiLight = new THREE.HemisphereLight(0xffffff, 0xdddddd, 1.2);
scene.add(hemiLight);

// スポットライトの設定
const spotLight = new THREE.SpotLight(0xffffff, 100);
spotLight.castShadow = true;
spotLight.position.set(3, 7, 3);
spotLight.angle = Math.PI / 6;
spotLight.penumbra = 0.8;
scene.add(spotLight);

    var stageGeometry = new THREE.CircleGeometry(30,64);
    var stageMaterial = new THREE.MeshStandardMaterial({
    color:0xFFFFFF});

    var stage = new THREE.Mesh(stageGeometry,stageMaterial);
    stage.receiveShadow = true;
    stage.name = 'stage';
    stage.scale.x = 1;
    stage.scale.y = 1;
    stage.rotation.x = -0.5 * Math.PI;
    stage.rotation.y = 0;
    scene.add(stage);

    // カメラの位置と注視点を設定
    camera.position.set(0, 1.5, 3); // カメラの位置を調整 (x, y, z)
    camera.lookAt(0, 0.8, 0); // カメラの注視点を調整 (x, y, z)
    controls.target.set(0, 0.5, 0);
    controls.update();

    function loadGLTFModel(model){
        if(currentModel){
            scene.remove(currentModel);
        }

        const loader = new GLTFLoader();

        loader.load(model, function(gltf){
            currentModel = gltf.scene;

            currentModel.traverse((object) => {
                if(object.isMesh){
                    object.material.metalness = 0.5;
                    object.geometry.computeVertexNormals();
                    object.castShadow = true;
                    object.receiveShadow = true;
                }
            });

            scene.add(currentModel);
        });
    }

    function animate(){
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    }

    animate();

$(function(){

    $('#glb').on('change', function(){
        camera.position.set(0, 1.5, 3);
        controls.target.set(0, 0.5, 0);
        controls.update();
        const file = this.files[0];
        if(!file) return;
        $('#name').val(file.name.replace(/\.glb$/i,''));
        const url = URL.createObjectURL(file);
        loadGLTFModel(url);
    });

    $('#submit').on('click', function(e){
        e.preventDefault();

        let file = $('#glb')[0].files[0];
        if(!file){
            alert('GLB is Empty!');
            return;
        }

       let name = $('#name').val();
       if(name == ''){
            alert('Name is Empty!');
            return;
        }
       let webp = name + '.webp';
        canvas.toBlob(function(blob){
            const fd = new FormData();
            fd.append('glb',file);
            fd.append('image', blob, webp);
            fd.append('name',name);
            fd.append('submit','upload');
            $.ajax({
                url:'glb.php',
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
        }, 'image/webp', 0.8);
    });


    $('#update').on('click', function(e){
        e.preventDefault();
       let name = $('#name').val();
       let webp = name + '.webp';
        canvas.toBlob(function(blob){
            const fd = new FormData();
            fd.append('image', blob, webp);
            fd.append('name',name);
            fd.append('submit','update');
            $.ajax({
                url:'glb.php',
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
        }, 'image/webp', 0.8);
    });


    $('#new').on('click',function(){
        $('#glbupload').removeClass().addClass('active new');
        $('#glbupload h2').text('new');
        $('#name').val('');
    });

    $('#glbupload .close').on('click',function(){
        $('#glbupload').removeClass();
        // $('audio#audio').attr('src','');
        $('#name').val('');
    });

    $('button.del').on('click',function(){
        let name = $(this).closest('li').data('name');
        $.post('glb.php',{
            submit:'del',
            name:name
        },function(res){
            if(res == 'ok'){
                location.reload();
            }
        })
    })

    $('button.view').on('click',function(){
        $('#glbupload h2').text('check');
        let name = $(this).closest('li').data('name');
        let image = $(this).closest('li').data('image');
        let file = '../common/glb/'+ image;
        $('#glbupload').removeClass().addClass('active view');
        $('#name').val(name);
        loadGLTFModel(file);
    })

})
</script>

</body>
</html>
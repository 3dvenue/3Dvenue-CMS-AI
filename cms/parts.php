<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
include_once('../common/inc/dbcall.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submit = $_POST['submit'] ?? '';
    $cid    = $_POST['cid'] ?? '';
    $type   = $_POST['type'] ?? '0';
    $cname  = $_POST['cname'];
    $dom    = $_POST['dom'];
    $memo   = $_POST['category'];

    if ($submit === 'add') {
        $sql = "INSERT INTO contents (type, cname, dom, memo) VALUES (:type, :cname, :dom, :memo)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':type'  => $type,
            ':cname' => $cname,
            ':dom'   => $dom,
            ':memo'  => $memo
        ]);
    }

    if ($submit === 'edit') {
        $sql = "UPDATE contents SET cname = :cname, type = :type, dom = :dom, memo = :memo WHERE cid = :cid";            
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':type'  => $type,
            ':cname' => $cname,
            ':dom'   => $dom,
            ':memo'  => $memo,
            ':cid'   => $cid
        ]);
        
    }

    if ($submit === 'del') {
        $sql = "DELETE FROM contents WHERE cid = :cid";            
        $stmt = $conn->prepare($sql);
        $stmt->execute([':cid' => $cid]);
        
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT * FROM contents WHERE Type < 3 ORDER BY cname";
$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" type="text/css" href="./css/parts.css?t=<?=time()?>">
    <style>  
    </style>
</head>
<body>

<header>
    <div class="inner">
        <h2><span><?=$lang['parts_edit'][$lng]?></span></h2>
        <div id="header-right">
            <div><button id="sampleaccet" class="btn">Assets</button></div>
            <div class="btn" id="new">＋</div>
        </div>
    </div>
</header>

<nav>
    <div class="inner">
        <ul id="selectparts">
            <li data-cid="0" value="sections" class="active">
            <span>Section Parts</span>
                <ul class="cattag sections">
                    <li data-cat="0">All</li>
                    <li data-cat="1">Eyecatch</li>
                    <li data-cat="2">Text</li>
                    <li data-cat="3">Image</li>
                    <li data-cat="4">Card</li>
                    <li data-cat="5">Media</li>
                    <li data-cat="6">Table</li>
                    <li data-cat="7">Document</li>
                    <li data-cat="8">Other</li>
                </ul>
            </li>
            <li data-cid="1" value="elements">
                <span>Element Parts</span>
                <ul class="cattag elements">
                    <li data-cat="0">All</li>
                    <li data-cat="1">Heading</li>
                    <li data-cat="2">Text/Image</li>
                    <li data-cat="3">List</li>
                    <li data-cat="4">Table/Divider</li>
                    <li data-cat="5">Button</li>
                    <li data-cat="6">Other</li>
                </ul>
            </li>
            <li data-cid="2" value="pages">
                <span>Web Pagedesign</span>
            </li>
        </ul>
    </div>
</nav>

<div id="main">
<div class="inner">
<section id="sections" class="active" data-tid="0">
    <h3>Section Parts</h3>
    <div class="flex">
        <?php foreach ($rows as $row): ?>
            <?php if ($row['type'] == '0'): ?>
                <div class="parts element" data-cid="<?= $row['cid'] ?>" data-cat="<?= $row['memo'] ?>">
                    <div class="wrapDom">
                        <?= $row['dom'] ?>
                    </div>
                    <div class="name"><?= $row['cname'] ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
       </div>
</section>

<section id="elements" data-tid="1">
    <h3>Element Parts</h3>
    <div class="flex">
        <?php foreach ($rows as $row): ?>
            <?php if ($row['type'] == '1'): ?>
                <div class="parts element" data-cid="<?= $row['cid'] ?>" data-cat="<?= $row['memo'] ?>">
                    <div class="wrapDom">
                        <?= $row['dom'] ?>
                    </div>
                    <div class="name"><?= $row['cname'] ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
       </div>
</section>

<section id="pages" data-tid="2">
    <h3>Web Pagedesign</h3>
    <div class="flex">
        <?php foreach ($rows as $row): ?>
            <?php if ($row['type'] == '2'): ?>
                <div class="parts element" data-cid="<?= $row['cid'] ?>">
                    <div class="wrapDom">
                        <?= $row['dom'] ?>
                    </div>
                    <div class="name"><?= $row['cname'] ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
       </div>
</section>

</div>
</div><!-- #main -->

<div id="editor">
    <div class="close">✕</div>
    <div id="form">
        <div id="wrapper"><div id="view"></div></div>
        <form method="POST" action="parts.php">
        <span class="hadle"></span>
        <h3>HTML</h3>
        <div id="domarea">
            <textarea name="dom" id="dom"></textarea>
        </div>
        <div id="inputarea">
            <div id="input">
                <input type="hidden" id="cid" name="cid" />
                <div id="typeselecter">
                    <label>
                        <select id="type" name="type">
                            <option value="0">Section</option>
                            <option value="1">Element</option>
                            <option value="2">Page</option>
                        </select><span>：Type</span>
                    </label>
                </div>
                <div>
                    <label>Name：
                        <input type="text" id="cname" name="cname" /><span></span>
                    </label>
                </div>
                <div class="category">
                    <label>Category：
                        <select id="category" name="category"></select><span></span>
                    </label>
                </div>
            </div><!-- input -->

            <div id="submit">
                <button type="submit" id="edit" class="btn" name="submit" value="edit"><?=$lang['save'][$lng]?></button>
                <button type="submit" id="add" class="btn" name="submit" value="add"><?=$lang['add'][$lng]?></button>
                <button type="submit" id="dell" class="btn" name="submit" value="del"><?=$lang['del'][$lng]?></button>
            </div><!-- submit -->
        </div>
        </form>
    </div><!-- form -->
</div><!-- editor-->

<div id="sampleurl">
    <div class="close">×</div>
    <h2>Sample SVG Assets</h2>
    <div class="inner">
    <?php
    $dir = '../common/svg/'; 
    $files = glob($dir . "*.svg");

    if ($files) {
        foreach ($files as $file) {
            $filename = basename($file);
    ?>
    <div class="svg" data-url="<?=$dir.$filename?>">
        <img src="<?=$file?>">
        <div><?=$filename?></div>
    </div>
     <?php
        }
    } else {
        echo "SVG file not found.";
    }
    ?>
    </div>
</div>

<div id="footer">
<div class="inner">
    <div id="copy">&copy; 2026 3Dvenue. All rights reserved.</div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(function(){

let type = sessionStorage.getItem('type') ?? '0';
const id = $('#selectparts li[data-cid="'+type+'"]').attr('value');
$('#selectparts li').removeClass('active');
$('#selectparts li[data-cid="'+type+'"]').addClass('active');
$('section').removeClass();
$('#'+id).addClass('active');
$('#selectparts li[data-cid="'+type+'"] ul.cattag li[data-cat="0"]').addClass('active');
$('#main .inner > section.active div.flex div').addClass('active');

let sectiondom = `<section class="">
   <div class="inner">
   </div>
</section>`;

let elemntdom = `<div class="">
</div>`;

    const sections = $('.cattag.sections li').map(function(){
        return {
            cat: $(this).data('cat'),
            text: $(this).text()
        };
    }).get();

    const elements = $('.cattag.elements li').map(function(){
        return {
            cat: $(this).data('cat'),
            text: $(this).text()
        };
    }).get();

    categorySet(sections);

    const catdata = {
        sections: sections,
        elements: elements
    };

    function categorySet(category){
        let $sel = $('select#category');
        $sel.empty();
        category.forEach(function(s){
            $sel.append(`<option value="${s.cat}">${s.text}</option>`);
        });
    }

    $('#selectparts > li').on('click',function(){
        let id = $(this).attr('value');
        let type = $(this).data('cid');
        sessionStorage.setItem('type', type);
        $('#type').val(type);
        $('#selectparts li').removeClass('active');
        $(this).addClass('active');
        $('section').removeClass();
        $('#'+id).addClass('active');
        $('.cattag li').removeClass('active');
        $('.cattag li[data-cat="0"]').addClass('active');
        if(type != '2'){
            categorySet(catdata[id]);
        }
        $('#main .inner > section div.flex div').removeClass('active');
        $('#main .inner > section.active div.flex div').addClass('active');
        $('#header-right').removeClass().addClass(id);
    })

    $('.cattag li').on('click',function(e){
        e.stopPropagation();
        $('.cattag li').removeClass('active');
        $(this).addClass('active');
        let cat = $(this).data('cat');
        console.log(cat);
            switch(cat){
              case 0:
                $('#main .inner > section div.flex div').removeClass('active');
                $('#main .inner > section.active div.flex div').addClass('active');
                break;
              default:
                $('#main .inner > section div.flex div').removeClass('active');
                $('#main .inner > section.active div.flex div[data-cat="'+cat+'"]').addClass('active');
            }
        })

        $('#new').on('click',function(e){
            $('#view').empty();
            let tid = $('section.active').data('tid');
            $('#cid').val('');
            $('select[name="type"]').val(tid);
            $('#cname').val('');
            $('#memo').val('');
            switch(tid){
                case 0:
                    $('#dom').val(sectiondom);
                break;
                case 1:
                    $('#dom').val(elemntdom);
                break;
                // case 2:
                //     $('#dom').val(pagedom);
                // break;    
                default:
                    alert('miss');
                break;
            }
            $('#editor').removeClass().addClass("active new");
        })


    const ro = new ResizeObserver(entries => {
        entries.forEach(entry => {
            const $parts = $(entry.target);
            const $wrapDom = $parts.find('.wrapDom');
            if (!$wrapDom.length) return;
            const scale = entry.contentRect.width / 1200;
            $wrapDom.css('transform', `scale(${scale})`);
        });
    });

    $('div.parts').each(function(){
        ro.observe(this);
    });

    $('#type').on('change',function(){
        let option = $(this).val();
        switch(option){
            case "0":
                $('#dom').val(sectiondom);
                hvewset(option,sectiondom);
                break;
            case "1":
                $('#dom').val(elemntdom);           
                hvewset(option,elemntdom);
                break;
            default:
                break;
        }
    })

    $('#editor .close,#sampleurl .close').on('click',function(){
        $('#editor,#sampleurl').removeClass();
    })

    $('#dom').on('input change',function(){
        let tid = $('#type').val();
        let dom = $(this).val();
        $('#view').html(dom);
    })


    function hvewset(tid,dom){
        let vewhtml = "";
        tid = Number(tid);
        switch(tid){
            case 0:
                vewhtml = '<main>'+dom+'</main>'; 
            break;

            case 1:
                vewhtml = '<main>'+dom+'</main>';
            break;
        }
        $('#view').html(vewhtml);
    }

    $('span.hadle').on('mousedown', function(e) {
        console.log('mousedows');
        e.preventDefault();
        let targetPopup = $(this).closest('#editor form');
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

    $('#elements div.parts,#sections div.parts,#pages div.parts').on('click', function(){
        const cid = $(this).data('cid');
        const cat = $(this).data('cat');
        const tid = $('section.active').data('tid');
        const dom = $(this).find('.wrapDom').html().trim();
        const cname = $(this).find('.name').html();
        $('#cid').val(cid);
        $('#form #view').html(dom);
        $('#dom').val(dom);
        $('#cname').val(cname);
        $('#type').val(tid);
        $('#category').val(cat);
        // console.log(cat);
        $('#editor').removeClass().addClass("active edit");
    });

    $('#sampleaccet').on('click',function(e){
        $('#sampleurl').addClass('active');
    });

    $('#sampleurl .inner .svg').on('click',function(){
        let copyurl = $(this).attr('data-url');
        if (copyurl) {
            navigator.clipboard.writeText(copyurl).then(() => {
                alert(copyurl + ' copied');
            });  
        }
    });

    $('#dom').on('keydown', function(e) {
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

});    

</script>
</body>
</html>
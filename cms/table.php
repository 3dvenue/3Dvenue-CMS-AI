<?php
/* 3Dvenue-CMS Copyright (c) 2026 yoshihiro Murai Licensed under MIT (https://opensource.org/licenses/MIT)*/
require_once('auth.php');
$directory = './common/table/';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $html = $_POST['html'];
    $css  = $_POST['css'];
    file_put_contents($directory . $name . '.html', $html);
    file_put_contents($directory . $name . '.css',  $css);
    exit('OK');
}
$root = file_get_contents('../common/inc/root.txt');
include_once('./lang.php');
$files = glob($directory . "*.css");
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
    body{
        background:#EDF2FA;
        min-width: auto;
    }


    #wraper{
        display: flex;
        height: calc(100% - 50px);
    }


    main{
        width:calc(100% - 300px);
        flex-shrink: 0;
        height:100%;
        margin:2px;
        background:#FFF;
        border-radius: 10px;
        padding:20px 0;
    }

    main .inner{
        height:100%;
    }

    main .inner section{
        height:100%;
    }


    main #savebox{
        display: flex;
        align-items: center;
        gap:5px;
        margin-bottom:20px;
    }

    main #savebox span{
        font-weight: 500;
        font-size:14px;
        margin-right:10px;
    }

    main #savebox #classname,
    main #savebox #tableName{
        display: flex;
        align-items: center;
        font-size: 14px;
        border-radius: 7px;
        border:1px solid #C7C7C7;
        padding:0 10px;
        font-weight: normal;
        width:120px;
        margin-right:10px;
    }

    main #savebox #saveBtn{
        margin-left:20px;
        height:30px;
        padding:0 20px;
    }

    /* #tableEditor
    ---------------------------------------------*/
    #tableEditor{
        width:300px;
        height:100%;
    }

    #tableTemplate ul{
        list-style: none;
        padding:0;
        margin:0;
        display: flex;
        gap:5px;
    }

    #tableTemplate ul li{
        padding:2px 20px;
        margin:0;
        border:1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        list-height: 1.0;
        font-weight:500;
    }

    /* control
    ---------------------------------------------*/
    #control{
        margin:2px;
        padding:5px;
        background:#FFF;
        padding:0;
        height:100%;
        border-radius:10px;
        overflow-y: auto;
        user-select: none;
    }

    #control details{
        border-top: 1px solid #ccc;
        padding:10px 20px;
    }

    #control details.Settings{
        border-top:none;
    }

    #control summary{
        margin-bottom:10px;
        cursor: pointer;
    }

    #control > div{
        padding:20px;
    }

    #control #controlHeader > div,
    #control details > div{
        display: flex;
        padding:10px 0 0;
    }

    #control summary{
        font-weight: 500;
        font-size:14px;
    }

    #control #controlHeader > div span,
    #control  details > div span{
        width:110px;
        flex-shrink: 0;
        font-size:12px;
    }

    #control select,
    #control input{
        width:140px;
        height:28px;
        border:1px solid #C7C7C7;
        border-radius:5px;
        padding:1px 5px;
        font-size:12px;
    }

    #control details button{
        /*border:1px solid #C7C7C7;*/
        border:none;
        padding:2px 15px;
        border-radius: 15px;
        background:#D3E3FC;
        color:#051E47;
        font-weight: 500;
        height:30px;
        cursor: pointer;
    }

    #control details button#openclose{
        padding:0 20px 2px; 
    }


    #control details.cell{
        display: none;
    }

    #control.cell details.cell{
        display: block;
    }

    #control details.cell button{
        /*width:30px;*/
        width:50px;
        margin-right:10px;
        cursor: pointer;
    }

    #control button#switch{
        width:auto;
    }

    #control div.restore{
        display: none;
    }

    #control #applybtn{
        display: none;
        border-top:1px solid #CCC;
    }

    #control.editor div.restore{
        display: flex;
    }

    #control.editor div#applybtn{
        display: block; 
    }

    #control button#apply{
        background:#3978C6;
        color:#FFF;
        font-size:16px;
        width:100%;
        border:none;
        padding:10px;
        border-radius:10px;
        cursor: pointer;
        font-weight: 700;
        box-shadow: 0 0 5px #0003;
    }

    #control button#apply:hover{
        box-shadow: 2px 2px 5px #0003;
    }

    /* table
    ------------------------------------------*/
    #table-box{
        padding:5px;
        /*border:1px solid #CCC;*/
        /*border-radius:5px;*/
        overflow-y: auto;
        max-height:calc(100% - 80px);
        margin-bottom:20px;
        background:#FFF;
    }

    #table-box table tr th.active,
    #table-box table tr td.active{
        outline:1px solid #000;
        outline-offset: -1px;
    }

    #table-box table tr th.hover,
    #table-box table tr td.hover,
    #table-box table tr.hover th,
    #table-box table tr.hover td{
    background:#000;
    color:#FFF;
    opacity:0.2;
    }


    /* html
    ------------------------------------------*/

    #html{
        position: fixed;
        bottom:0;
        left:0;
        width:100%;
        background: #F3F3F3;
        user-select: none;
        padding: 2px;
        height:200px;
        display: none;
    }

    #html.open{
        display: block;
    }

    #textarea{
        height:calc(100% - 20px);
        background:#F0F0F0;
        display: flex;
        gap:2px;
    }

    #textarea > div{
        width:50%;
    }

    #textarea > div h4{
        padding:0 15px;
        font-size:14px;
        border-radius: 10px 10px 0 0;
        background:#FFF;
    }

    #html #idname input{
        border:none;
        background: #fff;
        width:120px;
        font-size:14px;
        margin-left:10px;
        padding:0 10px;
        height:20px;
        border-radius: 5px;
    }

    #html span.hadle{
        position: absolute;
        top: -5px;
        left:0;
        display: block;
        height: 10px;
        width: 100%;
        cursor: ns-resize;
        user-select: none;
    }

    #html .hadle:active {
        cursor: grabbing;
        background:#E9EdF666;
        border-color: #ddd #bbb #ddd #ccc;
        border-width: 1px 3px 3px 1px;
    }

    #html h3{
        font-size:14px;
        font-weight: 500;
        height:25px;
        padding:0px 10px 0;
        border-bottom:none;
        font-weight: 500;
        box-sizing: border-box;
        margin:0;
        border:1px solid;
        border-color:#fff #ccc #ccc #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap:20px;
    }

    #html .close{
        display:flex;
        align-items: baseline;
        justify-content: center;
        position: static;
        background:none;
        border:none;
        font-weight: 900;
        width:24px;
        cursor: pointer;
        height:24px;
        color:#666;
    }

    #html .close:hover{
        color:#000;
    }

    #html h3 p{
        color:#515152;
    }


    #html textarea{
        width:100%;
        height:calc(100% - 25px);
        padding:10px 20px;
        border: 1px solid #D3E3FC;
        line-height:1.2;
        resize: none;
        font-size:14px;
        font-family: Consolas;
        background:#303841;
        color:#EEF;
        tab-size: 4;
        border-radius: 0 0 0 7px;
    }

    #celleditor{
        display: flex;
        gap:10px;
    }

    #celleditor input{
        width:90px;
    }

    footer{
        height:50px;
        padding:5px 2px;
    }

    footer .inner{
        height:100%;
        background:#FFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border-radius: 10px;
    }
    </style>

<style id="tableStyle">
</style>
</head>
<body>
<div id="wraper">
<main>
<div class="inner">
<div id="savebox">
    <span>Table Name:</span><input type="text" id="tableName" value="">
    <span>Class Name:</span><input type="text" id="classname" value="">
    <button id="saveBtn">Save</button> 
    </div>
<section>
<div id="table-box" contenteditable="true">
<table data-name="NEW" class="basic">
    <tr>
        <th>Item</th>
        <th>Content</th>
    </tr>
    <tr>
        <td>Item1</td>
        <td>Content1</td>
    </tr>
    <tr>
        <td>Item2</td>
        <td>Content2</td>
    </tr>
    <tr>
        <td>Item3</td>
        <td>Content3</td>
    </tr>
    <tr>
        <td>Item4</td>
        <td>Content4</td>
    </tr>
</table>   
</div><!-- table-box -->
</section>
</div>
</main>

<div id="tableEditor">
    <div id="control">
        <!-- <div id="controlHeader"> -->
    <details class="Settings" open>
        <summary>Settings</summary>
        <div>
            <span id="idname">Template</span>
            <select id="tableTemplate">
            <?php
            foreach ($files as $file) {
            $filename = explode('.',basename($file))[0];
            ?>
            <option value="<?=$file?>"><?=$filename?></option>
            <?php } ?>
            </select>
        </div>

        <dIv class="restore"><span>Original</span>
        <button class="restore" id="restore">Restore</button>
        </dIv>

        <dIv><span>Text Editor</span>
        <button class="openclose" id="openclose">Open ⇋ Close</button>
        </dIv>

        <div><span>Layout</span>
            <button id="layout" name="layout">change</button>
        </div>


    </details>
    <details class="table">
        <summary>Table Style</summary>
        <div><span>Table Background</span>
            <input type="text" id="tableBackground" data-target="table" data-name="background" value="">
        </div>
        <div><span>Border Color</span>
            <input type="text" id="tableBorderColor" data-target="table th,table td" data-name="border-color" value="">
        </div>
        <div><span>Width</span>
            <input type="text" id="tableWidth" data-target="table" data-name="width" value="">
        </div>
        <div><span>Min-Width</span>
            <input type="text" id="tableMinwidth" data-target="table" data-name="min-width" value="">
        </div>
        <div><span>margin</span>
            <input type="text" id="tableMargin" data-target="table" data-name="margin" value="">
        </div>
        <div><span>TH Background</span>
            <input type="text" id="thBackground" data-target="table th" data-name="background" value="">
        </div>
        <div><span>TH Color</span>
            <input type="text" id="thcolor" data-target="table th" data-name="color" value="">
        </div>
        <div><span>TD Color</span>
            <input type="text" id="tdcolor" data-target="table td" data-name="color" value="">
        </div>
    </details>

    <details class="cell">
        <summary>Structure</summary>
        <div><span>Copy Row</span>
            <button class="row" data-target="before">↑</button>
            <button class="row" data-target="after">↓</button>
        </div>
        <div><span>New Row</span>
            <button class="newrow" data-target="before">↑</button>
            <button class="newrow" data-target="after">↓</button>
        </div>
        <div><span>Add Columm</span>
            <button class="col" data-target="left">←</button>
            <button class="col" data-target="right">→</button>
        </div>
        <div><span>Delete</span>
            <button id="delrow">↕</button>
            <button id="delcol">↔</button>
        </div>
    </details>

    <details class="cell">
        <summary>Cell Style</summary>
        <div><span>Cell Background</span>
            <input type="text" id="cellBackground" data-target="table .active" data-name="background" value="">
        </div>
        <div><span>Cell Color</span>
            <input type="text" id="cellColor" data-target="table .active" data-name="color" value="">
        </div>
        <div><span>Cell FontSize</span>
            <input type="text" id="cellFontsize" data-target="table .active" data-name="font-size" value="">
        </div>
        <div><span>Cell FontFamily</span>
            <input type="text" id="cellFontfamily" data-target="table .active" data-name="font-family" value="">
        </div>
        <div><span>Cell FontWeight</span>
            <input type="text" id="cellfontWeight" data-target="table .active" data-name="font-weight" value="">
        </div>
        <div><span>Coll Span</span>
            <select id="colspan" class="span">
                <option value="">-</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div><span>Row Span</span>
            <select id="rowspan" class="span">
                <option value="">-</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div><span>Switch</span>
            <button id="switch">TD ⇋ TH</button>
        </div>
    </details>

    <div id="applybtn">
        <button id="apply">Apply</button>
    </div>

    </div>

</div>

</div><!-- wraper -->

<div id="html"><span class="hadle"></span>
<h3>TEXT EDITOR <span class="close">✕</span></h3>
    <div id="textarea">
        <div><h4>HTML</h4><textarea class="codearea" id="htmlarea"></textarea></div>
        <div><h4>CSS</h4><textarea class="codearea" id="cssarea"></textarea></div>
    </div>
</div>

<footer>
<div class="inner">
    <div id="copy">&copy; 2026 3Dvenue. All rights reserved.</div>
</div>    
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){

        $('#control details').on('toggle', function(){
            if(this.open){
                $('#control details').not(this).removeAttr('open');
            }
        });

        $('#html .close').on('click',function(){
            $('#html').removeClass('open');            
        })


        const cls = $('#table-box table').attr('class');
        const tablename = $('#table-box table').attr('data-name');
        const file = '<?=$directory?>'+cls+'.css';
        const tableHtml = $('#table-box').html();
        $('#tableName').val(tablename);
        $('#htmlarea').val(tableHtml);

        if(!window.opener){
            tableSetup(file);
        }

        function tableSetup(file){
            $('#tableTemplate').val(file);
            const html = file.replace(/\.css$/i, '.html');
            $.get(html + '?t=<?=time()?>', function(data){
                $('#table-box').html(data);
                $('#htmlarea').val(data);
                $('#classname').val($('#table-box table').attr('class'));
            });
            $.get(file + '?t=<?=time()?>', function(css){
                $('#cssarea').val(css);
                $('#tableStyle').html(css);
            });
        }
    
        function syncHtml(){
            setTimeout(function(){
                $('#htmlarea').val($('#table-box').html());
            },100)
        }

        setTimeout(function(){
            TableStyle();
        },100);

        function TableStyle(){
            const $dom = $('#table-box table');
            const $domth = $('#table-box table th');
            const $domtd = $('#table-box table td');
            $('#tableBackground').val($dom.css('background-color'));
            $('#tableBorderColor').val($dom.css('border-color'));
            $('#tableWidth').val($dom.css('width'));
            $('#tableMinwidth').val($dom.css('min-width'));
            $('#tableMargin').val($dom.css('margin'));
            $('#thBackground').val($domth.css('background-color'));
            $('#thcolor').val($domth.css('color'));
            $('#tdcolor').val($domtd.css('color'));
        }

        function CellStyle(){
            let $dom = $('table .active');
            $('#cellBackground').val($dom.css('background-color'));
            $('#cellColor').val($dom.css('color'));
            $('#cellFontsize').val($dom.css('font-size'));
            $('#cellfontWeight').val($dom.css('font-weight'));
            $('#cellFontfamily').val($dom.css('font-family'));
            $('#colspan').val($dom.attr('colspan') ?? '');
            $('#rowspan').val($dom.attr('rowspan') ?? '');
        }

        //StyleSheet
        $('#control input').on('change',function(){
            let value = $(this).val();
            let prop = $(this).data('name');
            let target = $(this).data('target');
            console.log(target);
            $('#table-box ' + target).css(prop,value);
            syncHtml();
        })

        //colspan rowspan
        $('#control select.span').on('change',function(){
            let num = $(this).val();
            let prop = $(this).attr('id');
            let $cel = $('#table-box table .active' );
            if(num == ''){
                $cel.removeAttr(prop);                
            }else{
                $cel.attr(prop,num);                
            }
            spanCheck();
        })

        function spanCheck(){
            $('#table-box table [hidden]').removeAttr('hidden');

            $('#table-box table').find('[colspan]').each(function(){
                let $cel = $(this);
                let col = Number($cel.attr('colspan'));
                let idx = $cel.index();
                let $tr = $cel.closest('tr');

                for(let i = 1; i < col; i++){
                    $tr.children().eq(idx + i).attr('hidden','');
                }
            });

            $('#table-box table').find('[rowspan]').each(function(){
                let $cel = $(this);
                let row = Number($cel.attr('rowspan'));
                let idx = $cel.index();
                let $tr = $cel.closest('tr');

                for(let i = 1; i < row; i++){
                    $tr = $tr.next('tr');
                    $tr.children().eq(idx).attr('hidden','');
                }
            });
         syncHtml();            
        }

        $('#table-box').on('click','table th,table td',function(){
            $('#table-box table th,#table-box table td').removeClass('active');
            $(this).addClass('active');
            $('#control').addClass('cell');
            CellStyle();
        })

        // table move
        // $('#table-box').on('keydown', function(e) {
        //     if (e.key !== 'Tab') return;
        //     e.preventDefault();
        //     const $cells = $(this).find('th, td');
        //     const i = $cells.index($cells.filter('.active'));
        //     const $t = $cells.eq(i + (e.shiftKey ? -1 : 1));
        //     $cells.removeClass('active');
        //     $t.addClass('active');
        //     const r = document.createRange();
        //     r.selectNodeContents($t[0]);
        //     r.collapse(true);
        //     const s = getSelection();
        //     s.removeAllRanges();
        //     s.addRange(r);
        // });

        $('#table-box').on('keydown', function(e) {
            if (e.key !== 'Tab') return;
            e.preventDefault();

            const c = $(this).find('th, td');
            const t = c.eq(c.index(c.filter('.active')) + (e.shiftKey ? -1 : 1));

            c.removeClass('active');
            t.addClass('active');

            const r = document.createRange();
            r.selectNodeContents(t[0]);
            r.collapse(false); // ← 末尾に置く
            const s = getSelection();
            s.removeAllRanges();
            s.addRange(r);
            CellStyle();
        });


        $(document).on('click', function(e){
            if (!$(e.target).closest('table,#control').length) {
                $('table th,table td').removeClass('active');
                $('#control').removeClass('active cell');
            }
        });

        $('#control button.col').on('click',function(){
            let target = $(this).data('target');
            let idx = $('table .active').index();
                $('table tr').each(function(){
                    let $dom = $(this).children().eq(idx);
                    let tag = $dom.prop('tagName').toLowerCase();
                    let html = '<' + tag + '>-</' + tag + '>';
                    if(target == 'right'){
                        $dom.after(html);
                    }else{              
                        $dom.before(html);
                    }
             });
            syncHtml();
        })

        $('#control button.row').on('click',function(){
            let target = $(this).data('target');
            let $tr = $('table .active').closest('tr');
            let $new = $tr.clone();
            $new.find('.active').removeClass('active');
                if(target == 'after'){
                    $tr.after($new);
                }else{              
                    $tr.before($new);
                }
            syncHtml();
        })

        $('#control button.newrow').on('click',function(){
            let target = $(this).data('target');
            let $tr = $('table .active').closest('tr');
            let $new = $tr.clone();
            $new.children().html('-');
            $new.find('.active').removeClass('active');
                if(target == 'after'){
                    $tr.after($new);
                }else{              
                    $tr.before($new);
                }
            syncHtml();
        })


        $('#delcol').on('click',function(){
            $('table .active').closest('tr').remove();
            syncHtml();
        })

        $('#delrow').on('click',function(){
            let idx = $('table .active').index();

            $('table tr').each(function(){
                $(this).children().eq(idx).remove();
            });
            syncHtml();        
        })        

        $('#delcol')
        .on('mouseenter',function(){
            $('table .active').closest('tr').addClass('hover');
        })
        .on('mouseleave',function(){
            $('table .hover').removeClass('hover');
        });

        $('#delrow')
        .on('mouseenter',function(){
          let idx = $('table .active').index();

            $('table tr').each(function(){
                $(this).children().eq(idx).addClass('hover');
            });
        })
        .on('mouseleave',function(){
            $('table .hover').removeClass('hover');
        });


        //html size
        $('span.hadle').on('mousedown', function(e) {
            e.preventDefault();
            let targetPopup = $(this).closest('.open');
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

        $('#tableTemplate').on('change',function(){
                let cla = $(this).text();
                let file = $(this).val();
                tableSetup(file);
                TableStyle();
        })

        function swapSpan(){
            $('table').find('[colspan],[rowspan]').each(function(){
                let $cel = $(this);
                let row = $cel.attr('rowspan');
                let col = $cel.attr('colspan');
                $cel.removeAttr('rowspan colspan');
                if(row) $cel.attr('colspan', row);
                if(col) $cel.attr('rowspan', col);
            });

            syncHtml();
        }

        $('#openclose').on('click',function(){
            $('#html').toggleClass('open');
        })


        // textEditor
        $('#htmlarea').on('change keyup',function(){
            $('#table-box').html($(this).val());
        })

        $('#cssarea').on('change keydown',function(){
            $('#tableStyle').html($(this).val());
        })

        // Table Transpose
        $('#layout').on('click',function(){
            tableRotate('table');
        });

        $('#switch').on('click', function () {
            let $cel = $('table .active');
            let tag = $cel.is('td') ? 'th' : 'td';

            $cel.replaceWith(function () {
                let $new = $('<' + tag + '>').html(this.innerHTML);

                $.each(this.attributes, function () {
                    $new.attr(this.name, this.value);
                });
                return $new.addClass('active');
            });
            syncHtml();
        });

        function tableRotate(table){
            let data = [];
            $(table).find('tr').each(function(r){
                $(this).children('th,td').each(function(c){
                    if(!data[c]) data[c] = [];
                    data[c][r] = $(this).prop('outerHTML');
                });
            });
            let html = '';
            $.each(data,function(r,row){
                html += '<tr>';
                $.each(row,function(c,cell){
                    html += cell || '<td></td>';
                });
                html += '</tr>';
            });
            $(table).html(html);
            setTimeout(function(){
               swapSpan();
            },100)
            syncHtml();
        }

        // Class Name Change
        $('#classname').on('change keyup', function () {
            const newClass = $(this).val().trim();
            const oldClass = $('#table-box table').attr('class');

            $('#table-box table')
                .removeClass()
                .addClass(newClass);

            let css = $('#cssarea').val();
            css = css.replaceAll('table.' + oldClass, 'table.' + newClass);

            $('#cssarea').val(css);
            $('#tableStyle').html(css);

            syncHtml();
        });

        //Table Name change
        $('#tableName').on('change keyup', function () {
            const name = $(this).val().trim();

            $('#table-box table').attr('data-name', name);

            syncHtml();
        });

        //データ保存
        $('#saveBtn').on('click', function () {
            $.post(location.href, {
                name: $('#tableName').val().trim(),
                html: $('#htmlarea').val(),
                css: $('#cssarea').val()
            }, function () {
                location.reload();
            });
        });

        // from Editor
        let originalTable = '';
        let originalCss = '';
        let originalname = '';
        let originalCname = '';

        window.loadTable = function(table, css, name, cname){
            $('#control').addClass('editor');
            originalTable = table;
            originalCss = css;
            originalname = name;
            originalCname = cname;
            $('#table-box').html(table);
            $('#htmlarea').val(table);
            $('#tableStyle').html(css);
            $('#cssarea').val(css);
            $('#tableName').val(name);
            $('#classname').val(cname);
            return;
        };

        $('#restore').on('click',function(){
            $('#table-box').html(originalTable);
            $('#htmlarea').val(originalTable);
            $('#tableStyle').html(originalCss);
            $('#cssarea').val(originalCss);
            $('#tableName').val(originalname);
            $('#classname').val(originalCname);
            console.log(originalCss);            
        })

        //Apply
        $('#apply').on('click', function(){
            const html = $('#htmlarea').val();
            const css  = $('#cssarea').val();
            if(window.opener && typeof window.opener.applyTable === 'function'){
                window.opener.applyTable(html, css);
                window.close();
            }
        });

    });
</script>
</body>
</html>
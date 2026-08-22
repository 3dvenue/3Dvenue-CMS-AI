<?php

$language = $_POST['language'] ?? '';
if($language !== ""){
    file_put_contents('../common/inc/lang.txt', $_POST['language']);
    exit;
}


$lng = trim(file_get_contents('../common/inc/lang.txt'));
$lang = [];

$fp = fopen('lang/lang.csv', 'r');

$head = fgetcsv($fp);

// BOM除去
$head[0] = preg_replace('/^\xEF\xBB\xBF/', '', $head[0]);

while($row = fgetcsv($fp)){

    if(count($row) !== count($head)) continue;

    $data = array_combine($head, $row);

    $key = trim($data['key']);
    $lang[$key] = $data;
}
fclose($fp);

function selectLanguage(){
    global $head,$lang,$lng;
    $selectLang = '<select id="language">';
    for($i=1;$i<count($head);$i++){
        $selected = '';
        if($head[$i] == $lng){
            $selected = ' selected';
        }
        $selectLang .= '<option value="'.$head[$i].'"'.$selected.'>';
        $selectLang .= $lang['language'][$head[$i]];
        $selectLang .= '</option>';
    }
    $selectLang .= '</select>';
    return $selectLang;
}

?>
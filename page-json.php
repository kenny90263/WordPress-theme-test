<?php
/* Template Name: json */
?>
<?php



// 探視日期欄位名稱
$field_date = GFAPI::get_field(5, 39)->label;
// 身分證字號
$field_id = GFAPI::get_field(5, 58)->label;



/**
 * 
 *    探視日期    field id 是   39
 *    病人姓名    field id 是   6
 *    訪客姓名    field id 是   5
 *    生日       field id 是   26   
 *    身分證字號  field id 是  58
 *    預約病房    field id 是  60
 *    時段       field id 是     
 *    病床號     field id 是  61~86
 *    
 * */

$entry = GFAPI::get_entries(5);


foreach ($entry as $value) {

    for ($i = 61; $i <= 86; $i++) {
        if ($value[$i]) {
            $bed_number = $value[$i];
        }
    }

    $person_info[] =  array(
        urlencode('探視日期')       => urlencode($value[39]),
        urlencode('病人姓名')       => urlencode($value[6]),
        urlencode('訪客姓名 ')      => urlencode($value[5]),
        urlencode('生日')          => urlencode($value[26]),
        urlencode('身分證字號')     => urlencode($value[58]),
        urlencode('預約病房 ')      => urlencode($value[60]),
        urlencode('病床號 ')        => urlencode($bed_number),
    );
}

// json
$person_info_json = urldecode(json_encode($person_info));

echo $person_info_json;


//echo $bytes = file_put_contents("myfile.json", $person_info_json); 


?>
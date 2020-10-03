<?php get_header(); ?>



<?php

// 如果標題是中文，此函數不適用於 HTML 屬性
/*$str = "這是字串";
$str = sanitize_title($str);
echo $str; // %e9%80%99%e6%98%af%e5%ad%97%e4%b8%b2
*/

// 清理文件名，用破折號替換空格
/*$filename = "TES15345//\\T .jpg";
$filename = sanitize_file_name($filename);
echo $filename;*/

// 如果 email 不合法會將會返回 false
/*$email = sanitize_email("testexample.com");
if ($email) {
    echo "good email";
} else {
    echo "it`s not email";
}
*/

//
$str ="<h1>TEST</h1>";
$str = sanitize_text_field($str);

echo $str;


?>








<?php get_footer(); ?>
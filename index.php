<?php get_header(); ?>


<?php


do_action("test");


add_action("test", "add", 999);

function add()
{
    echo "test";
}




?>

<script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    $.ajax({
        url: ajaxurl,
        type: "POST",
        data: {
            action: "my_test_action",
            data: 456
        },
        success: function(data) {
            console.log(data);
        },
        error: function(data) {
            console.log('error');
        }
    });
</script>



















<?php

/*global $jal_db_version;
$jal_db_version = '1.0';




$table_name = $wpdb->prefix . 'liveshoutbox';

$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		text text NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

add_option('jal_db_version', $jal_db_version);





$welcome_name = 'Mr. WordPress';
$welcome_text = 'Congratulations, you just completed the installation!';

$table_name = $wpdb->prefix . 'liveshoutbox';

$wpdb->insert(
    $table_name,
    array(
        'time' => current_time('mysql'),
        'name' => $welcome_name,
        'text' => $welcome_text,
    )
);

*/









/*echo "<pre>";
print_r($wpdb);
echo "</pre>";
*/
?>

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
/*$str ="<h1>TEST</h1>";
$str = sanitize_text_field($str);

echo $str;
*/

?>








<?php get_footer(); ?>
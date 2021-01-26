<?php get_header(); ?>



<?php

global $wpdb;
// page ID
$pageID = get_the_ID();

$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kenny_course WHERE page_id = {$pageID}");
// 現在報名人數
$regPeople = count($result);
// 最多報名人數
$maxRegister =  get_post_meta($pageID, 'course_max_people', true);

if ($maxRegister > $regPeople) { ?>

    <form action="" method="post">

        姓名：<input type="text" name="reg_name">
        生日：<input type="date" name="reg_birth">

        <input type="submit">
    </form>


<?php } else {
    echo "報名人數已經額滿了哦，下次請早。";
}





$table_name = $wpdb->prefix . 'kenny_course';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        page_id mediumint(9) NOT NULL,
		create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
        birthday datetime DEFAULT '0000-00-00' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);


if ($maxRegister > $regPeople) {



    if (!empty($_POST['reg_name'])) {

        $name = $_POST['reg_name'];
        $bday = $_POST['reg_birth'];


        $wpdb->insert(
            $table_name,
            array(
                'page_id' => $pageID,
                'create_date' => current_time('mysql'),
                'name' => $name,
                'birthday' => $bday,
            )
        );
    }



    


}

?>


















<?php get_footer(); ?>
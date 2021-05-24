<?php

// require wordpress engine function
require_once("../../../wp-load.php");


if (!isset($_POST['kenny_course']) || !wp_verify_nonce($_POST['kenny_course'], 'reg_course_action')) {
    echo '歹勢，驗證錯誤，請再試一次哦！';
    exit;
} else {
    // handle form data
    $pageID         = sanitize_text_field($_POST['pageid']);
    $name           = sanitize_text_field($_POST['reg_name']);
    $bday           = sanitize_text_field($_POST['reg_birth']);
    $phone          = sanitize_text_field($_POST['reg_tel']);
    $address        = sanitize_text_field($_POST['reg_address']);
    $company_name   = sanitize_text_field($_POST['reg_company_name']);

    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kenny_course WHERE page_id = {$pageID}");
    // 現在報名人數
    $regPeople = count($result);
    // 最多報名人數
    $maxRegister =  get_post_meta($pageID, 'course_max_people', true);

    if ($maxRegister > $regPeople) {
        $wpdb->insert(
            $wpdb->prefix . 'kenny_course',
            array(
                'page_id'           => $pageID,
                'create_date'       => current_time('mysql'),
                'name'              => $name,
                'birthday'          => $bday,
                'telphone'          => $phone,
                'address'           =>  $address,
                'companyname'       => $company_name
            )
        );
    }



    wp_redirect(get_permalink($pageID) . '?status=regfinish');
}



// global $wpdb;

// $table_name = $wpdb->prefix . 'kenny_course';
// $charset_collate = $wpdb->get_charset_collate();

// $sql = "CREATE TABLE $table_name (
		// id mediumint(9) NOT NULL AUTO_INCREMENT,
        // page_id mediumint(9) NOT NULL,
		// create_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		// name tinytext NOT NULL,
        // birthday datetime DEFAULT '0000-00-00' NOT NULL,
        // telphone tinytext NOT NULL,
        // address text NOT NULL,
        // companyname text,
		// PRIMARY KEY  (id)
// 	) $charset_collate;";

// require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
// dbDelta($sql);

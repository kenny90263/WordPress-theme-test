<?php get_header(); ?>

<?php

if ($_GET['status'] == 'regfinish') {
    echo '<p class="reg-status">您已完成報名，謝謝您。</p>';
    get_footer();
    exit;
}

global $wpdb;
// page ID
global $pageID;
$pageID = get_the_ID();

$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kenny_course WHERE page_id = {$pageID}");
// 現在報名人數
$regPeople = count($result);
// 最多報名人數
$maxRegister =  get_post_meta($pageID, 'course_max_people', true);



if ($maxRegister > $regPeople) { ?>

    <form action="<?= get_stylesheet_directory_uri(); ?>/course-data-handle.php" method="post" class="single-course-form">

        <label>
            姓名：<input type="text" name="reg_name">
        </label>
        <label>
            生日：<input type="date" name="reg_birth">
        </label>
        <label>
            聯絡電話：<input type="text" name="reg_tel">
        </label>
        <label>
            地址：<input type="text" name="reg_address">
        </label>
        <label>
            公司行號：<input type="text" name="reg_company_name">
        </label>

        <input type="submit">

        <input type="hidden" name="pageid" value="<?= $pageID; ?>">
        <?php wp_nonce_field('reg_course_action', 'kenny_course'); ?>
    </form>


<?php } else {
    echo "報名人數已經額滿了哦，下次請早。";
}


?>


















<?php get_footer(); ?>
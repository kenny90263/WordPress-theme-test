<?php


// 新增表單狀態處理
function add_handle_details_meta_box($meta_boxes, $entry, $form)
{
    $meta_boxes['progress'] = array(
        'title'         => '進度處理',
        'callback'      => 'progress_call_back',
        'context'       => 'side',
        'callback_args' => array($entry, $form),
    );
    return $meta_boxes;
}
add_filter('gform_entry_detail_meta_boxes', 'add_handle_details_meta_box', 10, 3);


function progress_call_back($entry)
{
    global $wpdb;
    // entry id
    $entry_id = $entry['entry']['id'];
    // send entry status to database
    if (!empty($_POST['at-status']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $wpdb->update($wpdb->prefix . 'gf_entry', array('payment_status' => $_POST['at-status']), array('id' => $entry_id));
    }
    $result = $wpdb->get_results("SELECT payment_status FROM {$wpdb->prefix}gf_entry WHERE id = {$entry_id}");
    $status = $result[0]->payment_status;
?>
    <select name="at-status">
        <option value="1" <?= $status == 1 ? "selected" : ""; ?>>申請資料處中...</option>
        <option value="2" <?= $status == 2 ? "selected" : ""; ?>>己預約成功，可按時探訪。</option>
        <option value="3" <?= $status == 3 ? "selected" : ""; ?>>本時段己無法預約，謝謝。</option>
        <option value="4" <?= $status == 4 ? "selected" : ""; ?>>預約己取消。</option>
        <option value="5" <?= $status == 5 ? "selected" : ""; ?>>無效申請單。</option>
    </select>
    <input type="submit">
<?php
}



















function course_cpt()
{
    // CPT 文字的命名陣列
    $labels_args = array(
        'add_new_item'       => '新增課程',
        'add_new'            => '新增課程',
        'edit_item'          => '編輯課程',
        'search_items'       => '搜尋課程',
        'not_found'          => '沒找到任何課程',
        'not_found_in_trash' => '垃圾桶中沒有課程',
    );
    // CPT 註冊時的參數陣列
    $register_args = array(
        'label'                 => "課程專區",                                       //後台選單上的文字
        'labels'                => $labels_args,                                    //文字名稱
        'description'           => '這是新增課程的地方，好好利用吧！',                //簡短描述這是哪種 CPT
        'public'                => true,                                           //是否通過管理界面 CPT
        'hierarchical'          => false,                                          //是否階層管理
        'exclude_from_search'   => false,                                          //是否從前端搜索結果中排除具有這種帖子類型的帖子,默認值為$public的相反值
        'publicly_queryable'    => true,                                           //是否可以在前端對帖子類型執行查詢
        'show_ui'               => true,                                           //是否在管理員介面中顯示
        'show_in_menu'          => true,                                           //是否在管理員介面中顯示  
        'show_in_nav_menus'     => true,                                          //是否使此 CPT 可在導航菜單中選擇
        'show_in_admin_bar'     => true,                                          //通過管理欄使此帖子類型可用
        'show_in_rest'          => false,                                         //是否在REST API中包括帖子類型    
        //'rest_base'
        //'rest_controller_class'
        'menu_position'         => 2,                                            //選單位置
        'menu_icon'             => 'dashicons-video-alt3',                       //選單 icon
        //'capability_type'
        //'capabilities'
        //'map_meta_cap'
        'supports'              => array('title', 'author', 'editor'),    //支援功能
        //'register_meta_box_cb'
        //'taxonomies'           =>
        'has_archive'          => true,
        //'rewrite'
        //'query_var'
        'can_export'            => true,                                        //是否允許匯出此CPT
        'delete_with_user'      => false,                                       //刪除用戶時是否刪除此類型的帖子
        //'_builtin'
        //'_edit_link'    => 'posttest.php?post=%d',         
    );
    register_post_type('course', $register_args);
}
add_action('init', 'course_cpt');



function kenny_course_entry_meta_boxes()
{
    add_meta_box(
        'kenny_course_entry_box',
        '報名人員',
        'kenny_course_entry_box_cb',
        'course'
    );

    add_meta_box(
        'kenny_course_max_people_box',
        '最多報名人數',
        'kenny_course_max_people_box_cb',
        'course',
        'side'
    );

    add_meta_box(
        'kenny_course_expect_openclass_box',
        '預計開課日期',
        'kenny_course_expect_openclass_cb',
        'course',
        'side'
    );
}
add_action('add_meta_boxes', 'kenny_course_entry_meta_boxes');

// 顯示報名人
function kenny_course_entry_box_cb($post)
{

    $courseID = $post->ID;
    global $wpdb;

    $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kenny_course WHERE page_id = {$courseID}");


    // 現在報名人數
    $regPeople = count($result);

    // echo "<pre>";
    // print_r($result);
    // echo "</pre>";
?>


    <div class="course-info">
        <p>現在報名人數：<?= $regPeople; ?></p>
        <p class="reg-now">最多報名人數：<?= get_post_meta($post->ID, 'course_max_people', true); ?></p>
    </div>
    <table class="at-entry-table-list">

        <thead>
            <tr>
                <th style="width: 5%;">刪除</th>
                <th style="width: 10%;">報名日期</th>
                <th style="width: 15%;">姓名</th>
                <th style="width: 10%;">生日</th>
                <th>聯絡電話</th>
                <th style="width: 30%;">地址</th>
                <th>公司行號</th>
            </tr>
        </thead>

        <tbody>
            <?php

            foreach ($result as $regdata) {
                echo "<tr>";
                echo '<td><input type="checkbox" name="id[]" value="' . $regdata->id . '"></td>';
                echo "<td>" . substr($regdata->create_date, 0, 10) . "</td>";
                echo "<td>" . $regdata->name . "</td>";
                echo "<td>" . substr($regdata->birthday, 0, 10) . "</td>";
                echo "<td>" . $regdata->telphone . "</td>";
                echo "<td>" . $regdata->address . "</td>";
                echo "<td>" . $regdata->companyname . "</td>";
                echo "</tr>";
            }

            ?>

        </tbody>
    </table>
    <?php wp_nonce_field('kenny_delete_reg_action', 'kenny_nonce_delete_reg'); ?>
    <style>
        .course-info p {
            font-size: 1.2em;
            line-height: 70%;
        }

        .at-entry-table-list {
            width: 100%;
            border-collapse: collapse;
        }

        .at-entry-table-list th {
            background: #D3DCE3;
        }

        .at-entry-table-list td,
        .at-entry-table-list th {
            border: 1px solid #000000;
            text-align: center;
            padding: 15px;
            font-size: 1.2em;
            color: #000000;
        }

        .at-entry-table-list tr:nth-child(even) {
            background: #D3DCE3;
        }
    </style>


<?php }



// 最多報名人數
function kenny_course_max_people_box_cb($post)
{
    echo '<input type="number" name="course_max_people" style="width: 80px;" value="' . get_post_meta($post->ID, 'course_max_people', true) . '"> 人';
    wp_nonce_field('kenny_course_action', 'kenny_nonce_expect_open');
}

// 預計開課日期
function kenny_course_expect_openclass_cb($post)
{
    echo '<input type="date" name="course_expect_open" value="' . get_post_meta($post->ID, 'course_expect_open', true) . '"> 人';
}


function kenny_course_save_meta_box($post_id)
{

    if (!isset($_POST['kenny_nonce_expect_open'], $_POST['kenny_nonce_delete_reg'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['kenny_nonce_expect_open'], 'kenny_course_action') && !wp_verify_nonce($_POST['kenny_nonce_delete_reg'], 'kenny_delete_reg_action')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['post_type']) && 'course' === $_POST['post_type']) {
        return;
    }





    $max_people = sanitize_text_field($_POST['course_max_people']);
    if (!preg_match("/^[1-9][0-9]*$/", $max_people)) {
        if (!$max_people == "") {
            return;
        }
    }

    update_post_meta($post_id, 'course_max_people',   $max_people);
    //update_post_meta($post_id, 'course_expect_open',   $_POST['course_expect_open']);

    global $wpdb;
    $delete_id = $_POST['id'];
    foreach ($delete_id as $ids) {
        $wpdb->delete($wpdb->prefix . 'kenny_course', array('id' => $ids));
    }
}
add_action('save_post', 'kenny_course_save_meta_box');



// function register_events_submenu_page()
// {
//     add_submenu_page(
//         'edit.php?post_type=events',
//         '已報名清單',
//         '已報名清單',
//         'manage_options',
//         'event-entries',
//         'event_entries_cb'
//     );
// }

// function event_entries_cb()
// {
//     echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
//     echo '<h2>My Custom Submenu Page</h2>';
//     echo '</div>';
// }
// add_action('admin_menu', 'register_events_submenu_page');





/*
function test(){
    the_modified_date();
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";


    wp_die();



}
add_action('save_post','test');*/


function test($content)
{


    if (is_page('test')) {

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

    }
}
add_action('the_content', 'test');



function populate_posts($form)
{

    foreach ($form['fields'] as $field) {

        if ($field->type != 'select' || strpos($field->cssClass, 'pick_seven_date') === false) {
            continue;
        }

        $sevenDate = array(
            date('Y-m-d'),
            date("Y-m-d", strtotime("1 day")),
            date("Y-m-d", strtotime("2 day")),
            date("Y-m-d", strtotime("3 day")),
            date("Y-m-d", strtotime("4 day")),
            date("Y-m-d", strtotime("5 day")),
            date("Y-m-d", strtotime("6 day"))
            //date("Y-m-d",strtotime("7 day")),
            //date("Y-m-d",strtotime("8 day")),
        );

        $choices = array();
        foreach ($sevenDate as $sevenDateDisplay) {
            $choices[] = array('text' => $sevenDateDisplay, 'value' => $sevenDateDisplay);
        }
        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = '選擇日期';
        $field->choices = $choices;
    }
    return $form;
}
add_filter('gform_pre_render_5', 'populate_posts');

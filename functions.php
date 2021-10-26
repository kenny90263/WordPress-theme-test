<?php

// $date_mod = date('Y-m-d');
//$string = "url>
// <loc>test123</loc>
// <lastmod>2021-5-5</lastmod>
// <changefreq>monthly</changefreq>
// <priority>0.5</priority>
// </url
// ";


//$xml = simplexml_load_file("../sitemap.xml");

// echo "<pre>";
// print_r($xml);
// echo "</pre>";

//$xml->addChild($string);

// Create the SimpleXML object from the string
// $xml = simplexml_load_file("../sitemap.xml");
// // add an <url> child to the <urlset> node
// $url = $xml->addChild("url");
// // add the <loc> and <priority> children to the <url> node 
// $url->addChild("loc", "http://domain.fake/link2.html");
// $url->addChild("lastmod", "2021-6-5");
// $url->addChild("priority", 0.98);

// // get the updated XML string
// $newXMLStr = $xml->asXML();
// //write it to the sitemap.xml file
// file_put_contents("../sitemap.xml", $newXMLStr);

function add_link_to_sitemapxml()
{

    wp_dequeue_script('jetpack-block-slideshow-js');
    wp_deregister_script('jetpack-block-slideshow-js');
}
add_action('wp_enqueue_scripts', 'add_link_to_sitemapxml');

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
    // add 2021/07/07
    date_default_timezone_set("Asia/Taipei");
    global $wpdb;
    // entry id
    $entry_id = $entry['entry']['id'];
    // send entry status to database
    if (!empty($_POST['at-status']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $wpdb->update($wpdb->prefix . 'gf_entry', array('payment_status' => $_POST['at-status']), array('id' => $entry_id));

        // updated date  // add 2021/07/07
        $wpdb->update($wpdb->prefix . 'gf_entry', array('payment_date' => date("Y-m-d H:i:s")), array('id' => $entry_id));
    }
    $result = $wpdb->get_results("SELECT payment_status FROM {$wpdb->prefix}gf_entry WHERE id = {$entry_id}");
    $status = $result[0]->payment_status;

    // add 2021/07/07
    $dated = $wpdb->get_results("SELECT payment_date FROM {$wpdb->prefix}gf_entry WHERE id = {$entry_id}")[0]->payment_date;
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
    if (!empty($dated)) {
        echo '更新日期：' . $dated;
    }
}






function at_create_menu()
{
    add_submenu_page(
        'edit.php?post_type=course',
        '',
        '報名課程人員名單',
        'administrator',
        'reg_members',
        'reg_members_callback',
        ''
    );

    function reg_members_callback()
    {
        $courseID = $_GET['id'];
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kenny_course WHERE page_id = {$courseID}");
        // 現在報名人數
        $regPeople = count($result);


    ?>

        <h2><?= get_the_title($courseID); ?></h2>

        <div class="course-info">
            <p>現在報名人數：<?= $regPeople; ?></p>
        </div>

        <table class="at-entry-table-list">

            <thead>
                <tr>
                    <th style="width: 5%;">刪除</th>
                    <th style="width: 15%;">報名日期</th>
                    <th style="width: 15%;">姓名</th>
                    <th style="width: 15%;">生日</th>
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



        <style>
            .course-info p {
                font-size: 1.2em;
                line-height: 70%;
            }

            .at-entry-table-list {
                margin: 32px auto;
                max-width: 90%;
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
}
add_action('admin_menu', 'at_create_menu');



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
        'kenny_course_write_entry_box',
        '匯入報名人員',
        'kenny_course_write_entry_box_cb',
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

// 匯入報名人員
function kenny_course_write_entry_box_cb($post)
{

    // echo "<pre>";
    // print_r($post);
    // echo "</pre>";
    ?>

    <h3>單筆匯入</h3>
    <label for="reg_date" style="display: block;">報名日期
        <input type="date" name="reg_date">
    </label>
    <label for="reg_name" style="display: block;">姓名
        <input type="text" name="reg_name">
    </label>
    <label for="reg_bdate style=" display: block;"">生日
        <input type="date" name="reg_bdate">
    </label>
    <label for="reg_phone" style="display: block;">聯絡電話
        <input type="text" name="reg_phone">
    </label>
    <label for="reg_address" style="display: block;">地址
        <input type="text" name="reg_address">
    </label>
    <label for="reg_company" style="display: block;">公司行號
        <input type="text" name="reg_company">
    </label>






<?php }

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
        <a href="<?= admin_url(); ?>edit.php?post_type=course&page=reg_members&id=<?= $courseID; ?>">查看全部報名人數</a>
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


    $reg_date = $_POST['reg_date'];
    $name = $_POST['reg_name'];
    $bday = $_POST['reg_bdate'];
    $phone = $_POST['reg_phone'];
    $address = $_POST['reg_address'];
    $company_name = $_POST['reg_company'];

    $wpdb->insert(
        $wpdb->prefix . 'kenny_course',
        array(
            'page_id'           => $post_id,
            'create_date'       => $reg_date,
            'name'              => $name,
            'birthday'          => $bday,
            'telphone'          => $phone,
            'address'           => $address,
            'companyname'       => $company_name
        )
    );
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


// function test($content)
// {



//     $ch = curl_init();
//     $timeout = 5;
//     curl_setopt($ch, CURLOPT_URL, 'http://localhost/_B-website-localhost/wordpress-test/test/');
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//     $file_contents = curl_exec($ch);
//     curl_close($ch);
//     echo $file_contents;




// }
// add_action('the_content', 'test');



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
add_filter('gform_pre_render_9', 'populate_posts');
add_filter('gform_pre_render_6', 'populate_posts');



// // 護理之家 - 時段上午 --- add the bed name and check the max-register people
// function at_bed_number($form)
// {
//     // 病床 data info
//     $sevenDate = array(
//         'N502-A',
//         'N502-B',
//         'N503-A',
//         'N503-B',
//         'N503-C',
//         'N505-A',
//         'N505-B',
//         'N505-C',
//         'N506-A',
//         'N506-B',
//         'N506-C',
//         'N507-A',
//         'N507-B',
//         'N507-C',
//         'N508-A',
//         'N508-B',
//         'N508-C',
//         'N509-A',
//         'N509-B',
//         'N509-C',
//         'N510-A',
//         'N510-B',
//         'N510-C',
//         'N511-A',
//         'N511-B',
//         'N511-C',
//         'N512-A',
//         'N512-B',
//         'N512-C',
//         'N513-A',
//         'N513-B',
//         'N513-C',
//         'N516-A',
//         'N516-B',
//         'N516-C',
//         'N517-A',
//         'N517-B',
//         'N517-C',
//         'N518-A',
//         'N518-B',
//         'N518-C',
//         'N519-A',
//         'N519-B',
//         'N519-C',
//         'N520-A',
//         'N520-B',
//         'N520-C',
//         'N521-A',
//         'N521-B',
//         'N521-C',
//         'N522-A',
//         'N522-B',
//         'N523-A',
//         'N523-B',
//         'N523-C',
//         'N524-A',
//         'N524-B',
//         'N524-C',
//         'N525-A',
//         'N525-B',
//         'N525-C',
//         'N5236-A',
//         'N526',
//         'N527'
//     );

//     /**
//      * 
//      *  探視日期 field id 39
//      *  時段 field id 52
//      *  病床分為兩組 
//      *      時段一上午 1000-1130 的 field id 44
//      *      時段二晚間 1800-1930 的 field id 58
//      */

//     /**
//      *  時段 value 值 
//      *  1.上午 1000-1130
//      *  2.晚間 1800-1930
//      * 
//      **/

//     $entry = GFAPI::get_entries(6);

//     if (!empty($entry)) {


//         foreach ($entry as $value) {
//             $bed_use_info[] = array($value[39], $value[52], $value[44]);
//         }


//         $temp_count = 0;
//         // 篩選掉時段為晚間的申請
//         foreach ($bed_use_info as $temp_bed_use_info) {


//             if ($temp_bed_use_info[1] == "晚間 1800-1930") {
//                 unset($bed_use_info[$temp_count]);
//             }



//             $temp_count++;
//         }

//         // 如有 unset 陣列的話 會將陣列補上空缺
//         $bed_use_info = array_values($bed_use_info);

//         $entry_count = count($bed_use_info);
//         $foreach_count = 0;

//         // start to check the register people
//         foreach ($bed_use_info as $temp_bed_use_info) {

//             if (!empty($repeat_remove))
//                 if (in_array($foreach_count, $repeat_remove)) {
//                     $foreach_count++;
//                     continue;
//                 }


//             for ($i = 0; $i < $entry_count; $i++) {

//                 if (!empty($repeat_remove))
//                     if (in_array($i, $repeat_remove))
//                         continue;


//                 if (!array_diff($bed_use_info[$foreach_count], $bed_use_info[$i])) {
//                     $full_bed[] = $bed_use_info[$foreach_count];
//                     $repeat_remove[] = $i;


//                     $group[] = $i;
//                 }
//             }

//             $room_count[] = array(count($group), $group);

//             // initial group array
//             unset($group);

//             $foreach_count++;
//         }


//         // check the register people wheather arrive to 2 people
//         foreach ($room_count as $room_count_info) {
//             if ($room_count_info[0] >= 2) {
//                 $room_name_info[] = $room_count_info[1][0];
//             }
//         }


//         // get final full bed name in an array
//         foreach ($room_name_info as $temp_room_name_info) {
//             $room_name_info_final[] = $bed_use_info[$temp_room_name_info][2];
//         }
//     } else {
//         $room_name_info_final = array();
//     }

//     // echo "<pre>";
//     // print_r($room_name_info_final);
//     // echo "</pre>";


//     // start to retrieve the option of form 
//     foreach ($form['fields'] as $field) {

//         if ($field->type != 'select' || strpos($field->cssClass, 'bed_number') === false) {
//             continue;
//         }

//         $choices = array();
//         foreach ($sevenDate as $sevenDateDisplay) {

//             if (empty($room_name_info_final)) {
//                 $choices[] = array('text' => $sevenDateDisplay, 'value' => $sevenDateDisplay);
//             } else {
//                 // if the bed room is full of people then value will add the 'full-' text in the front
//                 foreach ($room_name_info_final as $temp_room_name_info_final) {

//                     if ($temp_room_name_info_final == $sevenDateDisplay) {
//                         $choices[] = array('text' => $sevenDateDisplay . '(人數已達最高預約人數)', 'value' => 'full-' . $sevenDateDisplay);
//                     } else {
//                         $choices[] = array('text' => $sevenDateDisplay, 'value' => $sevenDateDisplay);
//                     }
//                 }
//             }



//             //
//         }

//         // update 'Select a Post' to whatever you'd like the instructive option to be
//         $field->placeholder = '選擇病床';
//         $field->choices = $choices;
//     }
//     return $form;
// }
// add_filter('gform_pre_render_6', 'at_bed_number');

// // 因為 gravity form 沒辦法用 PHP 輸出 disabled 屬性，所以用 Jquery 腳本使用
// function add_readonly_script($form)
// {
// 
?>

<script type="text/javascript">
    //         jQuery(document).ready(function() {

    //             jQuery('option').each(function() {

    //                 // option value 裡有 full 值的就添加 disabled 屬性
    //                 if (!jQuery(this).val().indexOf('full')) {
    //                     jQuery(this).attr("disabled", true);
    //                 }

    //             });


    //         });
    //     
</script>

<?php
//     return $form;
// }
// add_filter('gform_pre_render_6', 'add_readonly_script');

function my_test_action()
{
    $postData = $_POST['data'];
    wp_send_json($postData); // or return json_encode(), 若直接 return 會回傳0
    wp_die(); // or die()

}

add_action('wp_ajax_my_test_action', 'my_test_action');
add_action('wp_ajax_nopriv_my_test_action', 'my_test_action');





// Prevent the default content being output.
add_action('gform_print_entry_content', function () {
    remove_action('gform_print_entry_content', 'gform_default_entry_content', 10);
}, 1);

add_action('gform_print_entry_content', 'my_print_entry_content', 10, 3);
function my_print_entry_content($form, $entry, $entry_ids)
{
    // 表單名字
    $formTitle = $form['title'];
    // 申請日期
    $entryDate = $entry['date_created'];
    $entryYear = substr($entryDate, 0, 4) - 1911;
    $entryMonth = substr($entryDate, 5, 2);
    $entryDay = substr($entryDate, 8, 2);
    $entryDate = $entryYear . "年" . $entryMonth . "月" . $entryDay . "日";

    // echo "<pre>";
    // print_r($entry);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($form['fields']);
    // echo "</pre>";
?>
    <div class="afgh-print">
        <h1 class="text-center"><?= $formTitle ?></h1>
        <span class="reg-date">申請日期：<?= $entryDate ?></span>
        <table class="mb-4">
            <tr>
                <td style="width: 20%;">申請人</td>
                <td><?= $entry[1] ?></td>
                <td>簽章</td>
                <td style="width: 15%;"></td>
                <td rowspan="2">身分證明<br><br>文件號碼</td>
                <td>
                    <label><input type="checkbox" <?= checked("身分證", $entry[2]) ?>>身分證</label>
                    <label><input type="checkbox" <?= checked("居留證", $entry[2]) ?>>居留證</label>
                    <label><input type="checkbox" <?= checked("護照", $entry[2]) ?>>護照</label>
                </td>
            </tr>
            <tr>
                <td>出生日期</td>
                <td colspan="3"><?= $entry[6] ?></td>
                <td><?= $entry[10] ?><?= $entry[4] ?><?= $entry[5] ?></td>
            </tr>
            <tr>
                <td>法定代理人</td>
                <td><?= $entry[8] ?></td>
                <td>簽章</td>
                <td style="width: 15%;"></td>
                <td rowspan="2">身分證明<br><br>文件號碼</td>
                <td>
                    <label><input type="checkbox" <?= checked("身分證", $entry[13]) ?>>身分證</label>
                    <label><input type="checkbox" <?= checked("居留證", $entry[13]) ?>>居留證</label>
                    <label><input type="checkbox" <?= checked("護照", $entry[13]) ?>>護照</label>
                </td>
            </tr>
            <tr>
                <td>與申請人關係</td>
                <td colspan="3"><?= $entry[9] ?></td>
                <td><?= $entry[3] ?><?= $entry[11] ?><?= $entry[12] ?></td>
            </tr>
            <tr>
                <td>申請原因</td>
                <td colspan="5" class="reg-reason">
                    <label><input type="checkbox" <?= !empty($entry["14.1"]) ? "checked" : "" ?>>居家隔離/檢疫者，因親屬(或國外親屬)身故或重病等社會緊急需求，需外出奔喪或探視</label>
                    <label><input type="checkbox" <?= !empty($entry["14.2"]) ? "checked" : "" ?>>旅外親屬事故或重病等緊急特殊因素入境他國家/地區</label>
                    <label><input type="checkbox" <?= !empty($entry["14.3"]) ? "checked" : "" ?>>工作</label>
                    <label><input type="checkbox" <?= !empty($entry["14.4"]) ? "checked" : "" ?>>短期商務人士</label>
                    <label><input type="checkbox" <?= !empty($entry["14.5"]) ? "checked" : "" ?>>出國求學</label>
                    <label><input type="checkbox" <?= !empty($entry["14.6"]) ? "checked" : "" ?>>外國或中國大陸、香港、澳門人士出境</label>
                    <label><input type="checkbox" <?= !empty($entry["14.7"]) ? "checked" : "" ?>>相關出境適用對象之眷屬</label>
                    <label><input type="checkbox" <?= !empty($entry["14.8"]) ? "checked" : "" ?>>經嚴重特殊傳染性肺炎中央流行疫情指揮中心同意</label>
                    <label><input type="checkbox" <?= !empty($entry[21]) ? "checked" : "" ?>>其他因素：<?= $entry[21] ?></label>
                </td>
            </tr>
            <tr>
                <td rowspan="2">出境資料<br>【非出境免填】</td>
                <td colspan="3">出境日期</td>
                <td colspan="2"><?= $entry[19] ?></td>
            </tr>
            <tr>
                <td colspan="3">搭乘航空班機編號</td>
                <td colspan="2"><?= $entry[15] ?></td>
            </tr>
            <tr>
                <td>取得檢驗結果時間等需求</td>
                <td colspan="5"><?= $entry[20] ?></td>
            </tr>
            <tr>
                <td>個人自費檢驗資料蒐集處理及利用同意書</td>
                <td colspan="5" class="privacy-box">
                    就申請人於民國_______年_______月_______日於_____________________醫院接受COVID-19自費檢驗資料之個人資料（包括姓名、身分證字號、生日、檢驗結果等資料）：<br><br>
                    1.同意於簽署本申請表之日期起算□永久或_____年內，提供予衛生福利部中央健康保險署做為載入申請人之健康存摺及□健保醫療資訊雲端查詢系統，並得於本人醫療需要範圍內予以蒐集、處理或利用。<br>
                    ____________(簽章) ______________(法定代理人簽章)<br><br>
                    2. 同意於簽署本申請表之日期起算□永久或_____年內，提供衛生福利部疾病管制署作為相關疫情監測。<br>
                    ____________(簽章) ______________(法定代理人簽章)<br><br>
                    申請人已瞭解：不同意提供個人自費檢驗資料對申請自費檢驗並無影響。如同意提供，就提供之個人資料得依個人資料保護法第3條規定，保留隨時取消本同意書之權利，並得行使：申請查詢或請求閱覽、製給複製本、補充、更正、停止蒐集、處理或利用及請求刪除等權利。<br>
                    中華民國_______年_______月_______日
                </td>
            </tr>
        </table>

        <style>
            .privacy-box {
                text-align: left;
            }

            .reg-reason label {
                display: block;
                text-align: left;
            }

            .reg-date {
                float: right;
                font-size: 1.3em;
                font-weight: bold;
            }

            body {
                font-family: "Microsoft JhengHei";
            }

            .text-center {
                text-align: center;
            }

            .mb-4 {
                margin-bottom: 1em !important;
            }

            .afgh-print {
                width: 100%;
                margin: auto;
            }

            /* 防止列印時出現一頁空白 */
            @media print {

                html,
                body {
                    height: 99%;
                }
            }

            .afgh-print h1 {
                margin-bottom: 0px;
                font-weight: bold;
                font-size: 2em;
            }

            .afgh-print h2 {
                margin-bottom: 20px;
                font-weight: bold;
                font-size: 1.5em;
            }

            .afgh-print table {
                width: 100%;
                text-align: center;
                font-size: 1.4em;
                border-collapse: collapse;
            }

            .afgh-print table td {
                border: 2px solid #000000;
                padding: 10px 5px;
                font-weight: bold;
            }
        </style>







    <?php }

<?php


function event_cpt()
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
        'supports'              => array('title', 'author'),    //支援功能
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
    register_post_type('events', $register_args);
}
add_action('init', 'event_cpt');




function register_events_submenu_page()
{
    add_submenu_page(
        'edit.php?post_type=events',
        '已報名清單',
        '已報名清單',
        'manage_options',
        'event-entries',
        'event_entries_cb'
    );
}

function event_entries_cb()
{
    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    echo '<h2>My Custom Submenu Page</h2>';
    echo '</div>';
}
add_action('admin_menu', 'register_events_submenu_page');





/*
function test(){
    the_modified_date();
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";


    wp_die();



}
add_action('save_post','test');*/
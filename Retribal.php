<?php

/*
Plugin Name: Retribal
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: AlpineIO
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

add_action('init', 'create_performer');
add_action('init', 'create_performer_types', 0);
add_action('wp_enqueue_scripts', 'include_styles');
add_action('admin_init', 'my_admin');
add_action('save_post', 'save_meta', 10, 3);
add_filter( 'single_template', 'include_template_function');

function get_fields() {
    return array(
        'hometown'                 => ['Performer Hometown', 'text', 80],
        'pitch'                    => ['Pitch', 'wysiwyg'],
        'contact_name'             => ['Contact Name','text', 80],
        'contact_email'            => ['Contact Email','email', 80],
        'official_instagram'       => ['Official Instagram','url', 80],
        'official_youtube'         => ['Official Youtube','url', 80],
        'official_twitter'         => ['Official Twitter', 'url', 80],
        'official_website'         => ['Official Website','url', 80],
        'official_facebook'        => ['Official Facebook', 'url', 80],
        'official_vine'            => ['Official Vine', 'url', 80],
        'embed_code'               => ['Embedded Html', 'html']
    );
}

function my_admin() {
    add_meta_box(
        'performer_details_meta_box',
        'Details',
        'display_performer_details_meta_box',
        'performers', 'normal', 'high'
        );
}

function include_styles(){
    if(!is_admin()){
        $handle = 'retribal-stylesheet';
        wp_enqueue_style($handle, plugins_url( $handle . '.css', __FILE__ ));
        wp_enqueue_script('2169bbd34c', "https://use.fontawesome.com/2169bbd34c.js");
    }
}

function include_template_function( $single_template ){
    global $post;

    if ($post->post_type == 'performers') {
        $single_template = dirname(__FILE__) . '/single-performer.php';
    }
    return $single_template;
}

function create_performer() {
    register_post_type( 'retribal-performers',
        array(
            'labels' => array(
                'name'              => 'Performers',
                'singular_name'     => 'Performer',
                'add_new'           => 'Add New',
                'add_new_item'      => 'Add New Performer',
                'edit'              => 'Edit',
                'edit_item'         => 'Edit Performer',
                'new_item'          => 'New Performer',
                'view'              => 'View',
                'view_item'         => 'View Performer',
                'search_items'      => 'Search Performers',
                'not_found'         => 'No Performers Found',
                'not_found_in_trash'=> 'No Performers found in Trash',
                'parent'            => 'Parent Performer'
            ),
            'public'                => true,
            'menu_position'         => 15,
            'supports'              => array(
                                        'title',
                                        'editor',
                                        'comments',
                                        'thumbnail',
                                        ),
            'menu_icon'             => 'dashicons-microphone',
            'has_archive'           => true,
            'taxonomies'            => array('retribal-genre')
        )
        );
}

function create_performer_types() {
    $labels = array(
        'name'              => 'Genres',
        'singular_name'     => 'Genre',
        'search_items'      => 'Search Genres',
        'all_items'         => 'All Genres',
        'parent_item'       => 'Parent Genre',
        'parent_item_colon' => 'Parent Genre:',
        'edit_item'         => 'Edit Genre',
        'update_item'       => 'Update Genre',
        'add_new_item'      => 'Add New Genre',
        'new_item_name'     => 'New Genre',
        'menu_name'         => 'Genres',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'genre' ),
    );

    register_taxonomy( 'retribal-genre', array( 'retribal-performer' ), $args );
}

function save_meta( $performer_id, $performer, $update){
    if ($_POST['retribal']) {
        foreach ($_POST['retribal'] as $key=>$value) {
            update_post_meta($performer_id, $key, $value);
        }
    }
}

function display_performer_details_meta_box( $performer ){
    echo '<table style="margin-left: 10px; ">';
    foreach (get_fields() as $name => $args) {
        $last_val = get_post_meta($performer->ID, $name, true);
        echo '<tr>';
        echo "<td style='width: 200px; padding: 20px 0px;'><strong>{$args[0]}</strong></td>";
        echo "<td>";
        if ($args[1] == 'wysiwyg') {
            wp_editor($last_val, "retribal[{$name}]", array(
                'media_buttons' => false,
                'textarea_rows' => 8
            ));
        }elseif ($args[1] == 'html') {
            wp_editor($last_val, "retribal[{$name}]", array(
                'media_buttons' => false,
                'textarea_rows' => 8,
                'tabindex'      => 'text',
                'tinymce'       => false
            ));
        } else {
            echo "<input type='{$args[1]}' size='80' name='retribal[{$name}]' value='{$last_val}'/>";
        }
        echo "</td></tr>";
    }
    echo '</table>';
}

?>
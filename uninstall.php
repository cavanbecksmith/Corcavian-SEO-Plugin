<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_name = 'blog_homepage';
 
delete_option($option_name);
delete_option('show_page_title');

global $wpdb;

$wpdb->query( 
    $wpdb->prepare( 
        "
                    DELETE FROM $wpdb->postmeta
            WHERE meta_key = %s
            OR meta_key = %s
        ",
          '_meta_title', '_meta_description'
        )
);


?>
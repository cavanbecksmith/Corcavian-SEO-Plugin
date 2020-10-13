<?php

/*
Plugin Name: Corcavian SEO Plugin
Description: Basic SEO plugin
Version:     0.1
Author:      Cavan Becksmith
Author URI:  http://www.corcavian.site/
Text Domain: corcavian_seo_plugin
Domain Path: /languages/
License:     GPL v2 or later
*/

include_once 'inc/create_meta_box.php';

if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] )
	die();

class CORCAVIAN_SEO_PLUGIN {

	public function __construct() {

    add_action( 'get_header', array($this, 'set_id') );

    remove_action( 'wp_head', '_wp_render_title_tag', 1 ); // https://wordpress.stackexchange.com/questions/245894/how-can-i-remove-just-the-title-tag-from-wp-head-function
    add_filter( 'wp_title', array($this, 'update_wp_title') );

    add_action('admin_menu', array($this, 'create_seo_page'));
    $this->add_meta_boxes_to_posts();
    add_action( 'wp_head', array($this, 'add_links_to_head'), 1);

    add_action( 'admin_init', array($this, 'corcavian_register_settings') );

    // add_action( 'init', array($this, 'query_database') );

	}

	public function create_seo_page() {

    $capability  = apply_filters( 'corcavian_required_capabilities', 'manage_options' );
    $parent_slug = 'corcavian_seo_plugin';

    add_menu_page( __( 'Corcavian SEO', 'corcavian-seo-plugin' ), __( 'Corcavian SEO', 'corcavian-seo-plugin' ), $capability, $parent_slug, array($this, 'corcavian_seo_settings'), 'dashicons-forms' );
  }
  
  public function corcavian_seo_settings() {
    ?>
    <h1 class="test">Corcavian SEO Settings</h1>
    <p>Thank you for using the corcavian SEO plugin. We hope this lightweight plugin helps you with your site</p>
    <p>Please enter your blog url below</p>
    <form method="post" action="options.php">
    <?php settings_fields( 'corcavian_plugin_options_group' ); ?>
    <table>
    <tr valign="top">
    <th scope="row"><label for="blog_homepage">Blog Homepage Slug</label></th>
    <td><input type="text" id="blog_homepage" name="blog_homepage" value="<?php echo get_option('blog_homepage'); ?>" /></td>
    </tr>
    <tr valign="top">
    <th scope="row"><label for="show_page_title">Use Site title in title tag</label></th>
    <td>
      <select name="show_page_title" id="cars">
        <option selected="selected" disabled><?php echo get_option('show_page_title') ?></option>
        <option value="1">true</option>
        <option value="0">false</option>
      </select>
    </td>
    </tr>
    </table>
    <?php  submit_button(); ?>
    </form>
    <?php
  }

  public function corcavian_register_settings(){
   register_setting( 'corcavian_plugin_options_group', 'blog_homepage'); //, array($this, 'corcavian_register_settings_cb')
   register_setting( 'corcavian_plugin_options_group', 'show_page_title');
  }

  public function corcavian_register_settings_cb(){
    if(get_option('blog_homepage') == ''){
      delete_option('blog_homepage');
      delete_option('remove_me', 'test');
    }
  }

  public function add_meta_boxes_to_posts() {
    $id = $_GET['post'];
    // create_meta_box()
    create_meta_box( '_meta_title', $id, 'Meta Title', array('input_name'=>'meta_title', 'type'=>'text_area') );
    create_meta_box( '_meta_description', $id, 'Meta Description', array('input_name'=>'meta_description', 'type'=>'text_area') );
  }

  // Add action wp_head
  public function add_links_to_head(){
    $meta_description = get_post_meta( $this->id, '_meta_description' );
    ?>
    <title><?php wp_title('') ?></title>
    <?php 
    if($meta_description[0] != ''){
    ?>
    <meta name="description" content="<?php echo $meta_description[0] ?>">
    <?php
    }
    ?>
    <?php
  }

  /**
   * Customize the title for the home page, if one is not set.
   *
   * @param string $title The original title.
   * @return string The title to use.
   */
  public function update_wp_title( $title )
  {
    $post = get_post();
    $id = $this->id;

    $meta_title = get_post_meta( $id, '_meta_title' );

    if( $meta_title[0] == '' ){
      $meta_title = array(get_the_title($id));
    }

    if(get_option('show_page_title') == true){
      $title=$meta_title[0].' &#8211; '.get_bloginfo('site_title');
    } else {
      $title=$meta_title[0];
    }

    return $title;
  }


  public function set_id(){

      $post = get_post();
      $id = $post->ID;

      $url      = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $url_path = parse_url( $url, PHP_URL_PATH );
      $slug = pathinfo( $url_path, PATHINFO_BASENAME );

      $category_base =explode("/", get_option('category_base'));
      $split_str = explode('/', $url_path);
      $blog_homepage = get_option('blog_homepage');

      // --- Loop for blog default
      for($i = 0; $i < count($split_str); $i++ ){
        if($split_str[$i] == $blog_homepage && !$split_str[$i+1]){
          // Default blog page
          $id = get_option( 'page_for_posts' );
        } else if($split_str[$i] == $blog_homepage && $split_str[$i+1] == 'category') {
          // If the blog is using the default permalink and category is selected
          $id = get_option( 'page_for_posts' );
        }
      } 

      // -- Loop for custom permalink on category
      for($i = 0; $i < count($split_str); $i++){
        // echo $i . '<br/>';
        for($z = 0; $z < count($category_base); $z++){
          
          if($split_str[$i] == $category_base[$z] && count($category_base) < 2){
            // If there is only one part to the part and is custom permalink
            $id = get_option( 'page_for_posts' );
          } else if ($split_str[$i] == $category_base[$z] && $split_str[$i+1] == $category_base[$z+1]) {
            // If there is more than 2 parts of the path and is custom permalink
            $id = get_option( 'page_for_posts' );
          }
        }
      }

      $this->id = $id;

    }

    // public function query_database(){}

  }

new CORCAVIAN_SEO_PLUGIN();
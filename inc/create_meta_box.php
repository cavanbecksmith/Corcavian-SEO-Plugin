<?php 

/**
 * Create Meta Box
 *
 * Creates meta box on wordpress posts and pages
 *
 * @copyright  2020 Corcavian
 * @version    v1
 * @link       http://corcavian.site
 */ 

// https://www.sitepoint.com/adding-custom-meta-boxes-to-wordpress/


// https://code.tutsplus.com/tutorials/how-to-create-custom-wordpress-writemeta-boxes--wp-20336
// add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args );

// add_meta_box( string $id, 
// string $title, callable $callback, 
// string|array|WP_Screen $screen = null, string $context = 'advanced', string $priority = 'default', array $callback_args = null )

// -----------------------------------------------------------
// https://www.youtube.com/watch?v=ccbImB59JXQ

// $loop = 0;

if(!function_exists('create_meta_box')){
  function create_meta_box( $boxid, $page, $label, $args2 ) {

      // add_action( 'add_meta_boxes', 'add_meta_box_to_page' );
      // add_action( 'save_post', 'save_email_data' );
      $args = array($boxid, $page, $label, $args2);

      add_action( 'add_meta_boxes', function ( $post ) use ($args)  {

          add_meta_box( $args[0],
              $args[2],
              function ( $post ) use ($args) {

                  // --- Checks if a legit request
                  wp_nonce_field( basename( __FILE__ ), 'nonce_field' );

                  // --- Get value
                  $value = get_post_meta($post->ID, $args[0]);
              

                  // --- Init wp_editor Or any other input with textare name

                  if($args[3]['type'] == 'wp_editor') {

                    wp_editor( $value[0] , $args[3]['input_name'], array(
                    // wp_editor( $value[0] , $args[0], array(
                        'wpautop'       => false,
                        'media_buttons' => true,
                        'textarea_name' => $args[0],
                        'editor_class'  => $args[0],
                        'textarea_rows' => 10
                    ) );

                    $len = $value[0]; 

                  } else if ($args[3]['type'] == 'text_area') {
                    echo '<textarea size="15" type="textbox" id="'.$args[3]['input_name'].'" name="'.$args[0].'" >'. esc_attr( $value[0]  ) .'</textarea>';
                  } else if (!$args[3]['type']) {
                    // Default
                    echo '<input type="text" id="'.$args[3]['input_name'].'" name="'.$args[0].'" value="'.esc_attr( $value[0]  ).'"/>';
                  } else if ($args[3]['type'] == 'checkbox') {
                    echo '<input class="checkbox_check" type="checkbox" id="'.$args[3]['input_name'].'" name="'.$args[0].'" value="'.esc_attr( $value[0]  ).'"/>';
                  }


                  // echo $value[0];

              
                  if(esc_attr($value[0]) === '') {
                      echo '<br/>';
                      echo 'Empty fields';
                      delete_post_meta($post->ID, $args[0]);
                  }


              },
              '', // Previously page
              'side',
              'default'
          );

          // echo 'test';
          // echo $post_type;
          // print_r($pagenow);
          // print_r( $post );
      });


      add_action( 'save_post', function ( $post_id ) use ($args) {

          sanity_check( $post_id, 'nonce_field', 'sunset_save_email_data' );
      
          $my_data = sanitize_text_field( $_POST[$args[0]] );
      
          update_post_meta( $post_id, $args[0], $my_data );
      
      });


      // $loop++;

  }

  function sanity_check ( $post_id, $nonce_id, $for_func ){

      if( ! isset( $_POST[$nonce_id] ) ){
          return;
      }

      // if ( ! wp_verify_nonce( $_POST[$nonce_id], $for_func ) ){
      if ( ! wp_verify_nonce( $_POST[$nonce_id] ) ){
          return;
      }

      if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
          return;
      }

      if( ! current_user_can( 'edit_post', $post_id ) ){
          return;
      }

      if( ! isset( $_POST['sunset_contact_email_field']) ){
          return;
      }

}

// ----------------------------------------------------------------------
// OLD

// function add_meta_box_to_page( $post ) {

//         add_meta_box( 'contact_email',
//             'Experience Box Count',
//             'experience_box_count',
//             'page',
//             'side',
//             'default'
//         );

//         // echo 'test';
//         // echo $post_type;
//         // print_r($pagenow);
//         print_r( $post );
// }

// // --- Callback Function
// function experience_box_count( $post ) {

//     // print_r($post);

//     // --- Checks if a legit request
//     wp_nonce_field( 'save_email_data', 'nonce_filed' );

//     $value = get_post_meta($post->ID, '_contact_email_value_key');

//     echo '<label for="sunset_contact_email_field">User Email Address</label>';
//     echo '<input size="15" type="textbox" id="sunset_contact_email_field" name="sunset_contact_email_field" value="'. esc_attr( $value[0]  ) .'" />';
//     print_r($value);

//     $len = $value[0]; 


//     for($i = 0; $i <= $len; $i++) {
//         // echo $i;

//     }

// }

// function save_email_data( $post_id ) {

//     // sanity_check( $post_id, 'nonce_filed', 'sunset_save_email_data' );

//     $my_data = sanitize_text_field( $_POST['sunset_contact_email_field'] );

//     update_post_meta( $post_id, '_contact_email_value_key', $my_data );

// }



  function create_experience_box() {

  }

}
// -----------------------------------------------------------
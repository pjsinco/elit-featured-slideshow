<?php

require_once( 'vendor/autoload.php' );
require_once( 'includes/elit-slideshow.php' );


/**
 * Plugin Name: Elit Featured Slideshow
 * Description: Custom post type: Featured Slideshow
 * Version: 0.0.1
 * Author: Patrick Sinco
 * 
 */

if ( !defined( 'WPINC' ) ) {
  die;
}

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

add_action( 'init' , 'elit_featured_slideshow_cpt' );

function elit_featured_slideshow_cpt() {
  /**
   * SLIDESHOW POST custom post type
   *
   * For displaying a slideshow post
   */

  $labels = array(
    'name'               => 'Featured Slideshow',
    'singular_name'      => 'Featured Slideshow',
    'menu_name'          => 'Featured Slideshow',
    'name_admin_bar'     => 'Featured Slideshow',
    'add_new'            => 'Add new Featured Slideshow',
    'add_new_item'       => 'Add new Featured Slideshow',
    'edit_item'          => 'Edit Featured Slideshow',
    'view_item'          => 'View Featured Slideshow',
    'all_items'          => 'All Featured Slideshows',
    'search_items'       => 'Search Featured Slideshows',
    'not_found'          => 'No Featured Slideshows found',
    'not_found_in_trash' => 'No Featured Slideshows found in trash.',
  );
  
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'exclude_from_search' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'show_in_admin_bar' => true,
    'menu_position' => 20,
    'capability_type' => 'post',
    'has_archive' => false,
    'hierarchical' => false,
    'rewrite' => array( 'slug' => 'featured-slideshow'),
    'supports' => array( 'revision', 'title', 'author', 'thumbnail', 'comments' ),
  );
  
  register_post_type( 'elit_slideshow', $args );
  //flush_rewrite_rules( 'hard' );
}

/**
 * SHORTCODE META BOX
 *
 */
add_action( 'load-post.php', 'elit_featured_slideshow_shortcode_meta_box_setup' );
add_action( 'load-post-new.php', 'elit_featured_slideshow_shortcode_meta_box_setup' );

function elit_featured_slideshow_shortcode_meta_box_setup() {
  add_action( 'add_meta_boxes', 'elit_add_featured_slideshow_shortcode_meta_box' );
  add_action( 'save_post', 'elit_save_featured_slideshow_shortcode_meta', 10, 2 );
}

function elit_add_featured_slideshow_shortcode_meta_box() {
  add_meta_box(
    'elit-featured-slideshow-shortcode',
    esc_html( 'Slideshow shortcode' ),
    'elit_featured_slideshow_shortcode_meta_box',
    'elit_slideshow',
    'normal',
    'default'
  );
}

function elit_featured_slideshow_shortcode_meta_box( $object, $box ) {
  wp_nonce_field( basename(__FILE__), 'elit_featured_slideshow_shortcode_nonce' );
  ?>
  <p>
    <label for="widefat">Ex.: [elit-slideshow ids="180298, 180291, 180265"]. The IDs are the image IDs to use in the slideshow.</label>
    <br />
    <input class="widefat" type="text" name="elit-featured-slideshow-shortcode" id="elit-featured-slideshow-shortcode" value="<?php echo esc_attr( get_post_meta( $object->ID, 'elit_featured_slideshow_shortcode', true ) ); ?>" />
  </p>
  <?php 
}

function elit_save_featured_slideshow_shortcode_meta( $post_id, $post ) {
  // verify the nonce
  if ( !isset( $_POST['elit_featured_slideshow_shortcode_nonce'] ) || 
    !wp_verify_nonce( $_POST['elit_featured_slideshow_shortcode_nonce'], basename( __FILE__ ) )
  ) {
      // instead of just returning, we return the $post_id
      // so other hooks can continue to use it
      return $post_id;
  }

  // get post type object
  $post_type = get_post_type_object( $post->post_type );

  // if the user has permission to edit the post
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
    return $post_id;
  }

  // get the posted data and sanitize it
  $new_meta_value = 
    ( isset($_POST['elit-featured-slideshow-shortcode'] ) ? $_POST['elit-featured-slideshow-shortcode'] : '' );

  // set the meta key
  $meta_key = 'elit_featured_slideshow_shortcode';

  // get the meta value as a string
  $meta_value = get_post_meta( $post_id, $meta_key, true);

  // if a new meta value was added and there was no previous value, add it
  if ( $new_meta_value && $meta_value == '' ) {
    //add_post_meta( $post_id, 'elit_foo', 'bar');
    add_post_meta( $post_id, $meta_key, $new_meta_value, true);
  } elseif ($new_meta_value && $new_meta_value != $meta_value ) {
    // so the new meta value doesn't match the old one, so we're updating
    update_post_meta( $post_id, $meta_key, $new_meta_value );
  } elseif ( $new_meta_value == '' && $meta_value) {
    // if there is no new meta value but an old value exists, delete it
    delete_post_meta( $post_id, $meta_key, $meta_value );
  }
}

add_filter( 'template_include', 'include_template_function', 1 );
function include_template_function( $template_path ) {
  
  if ( get_post_type() == 'elit_slideshow' ) {
    if ( is_single() ) {
      // check if the file exists in the theme,
      // otherwise serve the file from the plugin
      if ( $theme_file = locate_template( array( 'single-elit_featured_slideshow.php' ) ) ) {
        $template_path = $theme_file;
      } else {
        $template_path = plugin_dir_path( __FILE__ ) . 'single-elit_featured_slideshow.php';
      }
    }
  }

  return $template_path;
  
}

//function get_custom_post_type_template( $single_template ) {
//  global $post;
//
//  if ( $post->post_type == 'elit_slideshow' ) {
//    $single_template = ABSPATH . 'wp-content/plugins/elit-featured-slideshow/includes/single-elit_featured_slideshow.php';
//  }
//
//  return $single_template;
//}
//add_filter( 'single_template', 'get_custom_post_type_template' );

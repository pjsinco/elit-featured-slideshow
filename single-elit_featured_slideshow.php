<?php get_header(); ?>

<?php

$shortcode = get_post_meta( $post->ID, 'elit_featured_slideshow_shortcode', true );

if ( !empty( $shortcode ) ) {
  
  echo $shortcode;
  echo do_shortcode( $shortcode );
}

?>
<?php get_footer(); ?>

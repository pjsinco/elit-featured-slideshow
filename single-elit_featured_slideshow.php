<?php get_header(); ?>

<?php get_template_part('sidebar', 'leaderboard'); ?>

    <div id="main" class="content">
      <div class="row--bleed-xl">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <?php
            $shortcode = 
              get_post_meta( $post->ID, 'elit_featured_slideshow_shortcode', true );
            if ( !empty( $shortcode ) ):
              echo do_shortcode( $shortcode );
            endif;
          ?>
        </article>
      </div>
    			<?php
    				// If comments are open or we have at least one comment, load up the comment template
    				if ( comments_open() || get_comments_number() ) :
    					comments_template();
    				endif;
    			?>


      </section> <!-- #primary -->


      </section>
    </div> <!-- #main -->

<?php get_footer(); ?>

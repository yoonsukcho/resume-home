<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * Includes the loop.
 *
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<?php get_header() ?>

  <?php
    /* SLIDERS : standard or slider of posts */
    if ( czr_fn_has('main_slider') ) {
      czr_fn_render_template( 'modules/slider/slider', array( 'model_id' => 'main_slider') );
    }

    elseif( czr_fn_has( 'main_posts_slider' ) ) {
      czr_fn_render_template( 'modules/slider/slider', array( 'model_id' => 'main_posts_slider') );
    }

  ?>

  <?php
    // This hook is used to render the following elements(ordered by priorities) :
    // singular thumbnail
    do_action('__before_main_wrapper')
  ?>

    <div id="main-wrapper" class="section">

            <?php
              //this was the previous implementation of the big heading.
              //The next one will be implemented with the slider module
            ?>
          <?php  if ( apply_filters( 'big_heading_enabled', false && ! czr_fn_is_home() && ! is_404() ) ): ?>
            <div class="container-fluid">
              <?php
                if ( czr_fn_has( 'archive_heading' ) )
                  $_heading_template = 'content/post-lists/headings/archive_heading';
                elseif ( czr_fn_has( 'search_heading' ) )
                  $_heading_template = 'content/post-lists/headings/search_heading';
                elseif ( czr_fn_has('post_heading') )
                  $_heading_template = 'content/singular/headings/post_heading';
                else //pages and fallback
                  $_heading_template = 'content/singular/headings/page_heading';

                czr_fn_render_template( $_heading_template );
              ?>
            </div>
          <?php endif ?>


          <?php do_action('__before_main_container') ?>

          <?php
            /* FEATURED PAGES */
            if ( czr_fn_has( 'featured_pages' ) )
              czr_fn_render_template( 'modules/featured-pages/featured_pages' );
          ?>

          <?php if ( czr_fn_has('breadcrumb') ) : ?>
            <div class="container">
              <?php czr_fn_render_template( 'modules/common/breadcrumb' ) ?>
            </div>
          <?php endif ?>


          <div class="<?php czr_fn_main_container_class() ?>" role="main">

            <?php do_action('__before_content_wrapper'); ?>

            <div class="<?php czr_fn_column_content_wrapper_class() ?>">

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php czr_fn_article_container_class() ?>">

                <?php

                  /* Archive regular headings */
                  if ( apply_filters( 'regular_heading_enabled', ! czr_fn_is_home() && ! is_404() ) ):

                    if ( czr_fn_has( 'archive_heading' ) )
                      czr_fn_render_template( 'content/post-lists/headings/regular_archive_heading',
                        array(
                          'model_class' => 'content/post-lists/headings/archive_heading'
                        )
                      );
                    elseif ( czr_fn_has( 'search_heading' ) )
                      czr_fn_render_template( 'content/post-lists/headings/regular_search_heading' );

                  endif;


                  do_action( '__before_main_loop' );

                  if ( ! czr_fn_is_home_empty() ) {
                      if ( have_posts() ) {
                          //Problem to solve : we want to be able to inject any loop item ( grid-wrapper, alternate, etc ... ) in the loop model
                          //=> since it's not set yet, it has to be done now.
                          //How to do it ?

                          //How does the loop works ?
                          //The loop has its model CZR_loop_model_class
                          //This loop model might setup a custom query if passed in model args
                          //this loop model needs a loop item which looks like :
                          // Array
                          // (
                          //     [0] => modules/grid/grid_wrapper
                          //     [1] => Array
                          //         (
                          //             [model_id] => post_list_grid
                          //         )

                          // )
                          // A loop item will be turned into 2 properties :
                          // 1) 'loop_item_template',
                          // 2) 'loop_item_args'
                          //
                          //Then, when comes the time of rendering the loop view with the loop template ( templates/parts/loop ), we will fire :
                          //czr_fn_render_template(
                          //     czr_fn_get_property( 'loop_item_template' ),//the loop item template is set the loop model. Example : "modules/grid/grid_wrapper"
                          //     czr_fn_get_property( 'loop_item_args' ) <= typically : the model that we inject in the loop item that we want to render
                          // );

                          //Here, we inject a specific loop item, the main_content, inside the loop
                          //What is the main_content ?
                          //=> depends on the current context, @see czr_fn_get_main_content_loop_item() in core/functions.php
                          czr_fn_render_template(
                              'loop',
                              array( 'model_args' => czr_fn_get_main_content_loop_item() )
                          );

                      } else {//no results

                          if ( is_search() )
                            czr_fn_render_template( 'content/no-results/search_no_results' );
                          else
                            czr_fn_render_template( 'content/no-results/404' );
                      }
                  }//not home empty
                  do_action( '__after_main_loop' );
                ?>
              </div>

              <?php do_action('__after_content'); ?>

              <?php
                /*
                * SIDEBARS
                */
                /* By design do not display sidebars in 404 or home empty */
                if ( ! ( czr_fn_is_home_empty() || is_404() ) ) {
                  if ( czr_fn_has('left_sidebar') )
                    get_sidebar( 'left' );

                  if ( czr_fn_has('right_sidebar') )
                    get_sidebar( 'right' );

                }
              ?>
            </div><!-- .column-content-wrapper -->

            <?php do_action('__after_content_wrapper'); ?>

            <?php if ( czr_fn_has('single_author_info') || czr_fn_has('related_posts') ) : ?>
              <div class="row single-post-info">
                <div class="col-12">
                <?php
                  if ( czr_fn_has('single_author_info') )
                     czr_fn_render_template( 'content/singular/authors/author_info' );

                  if ( czr_fn_has('related_posts') )
                    czr_fn_render_template( 'modules/related-posts/related_posts' )
                ?>
                </div>
              </div>
            <?php endif ?>

            <?php if ( czr_fn_has('comments') ) : ?>
              <div class="row">
                <div class="col-12">
                  <?php czr_fn_render_template( 'content/singular/comments/comments' ) ?>
                </div>
              </div>
            <?php endif ?>

          </div><!-- .container -->

          <?php do_action('__after_main_container'); ?>
          <?php if ( czr_fn_has('footer_push') ) czr_fn_render_template('footer/footer_push', 'footer_push') ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

    <?php
      if ( czr_fn_has('posts_navigation') ) :
    ?>
      <div class="container-fluid">
        <div class="row">
        <?php
          if ( !is_singular() )
            czr_fn_render_template( "content/post-lists/navigation/post_list_posts_navigation" );
          else
            czr_fn_render_template( "content/singular/navigation/singular_posts_navigation" );
        ?>
        </div>
      </div>
    <?php endif ?>

<?php get_footer() ?>

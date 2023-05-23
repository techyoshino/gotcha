<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BusinessPress
 */

get_header(); ?>

<section id="primary" class="content-area">
	<main id="main" class="site-main">

	<?php
global $post;
$args = array(
  'posts_per_page' => 20,
  'post_type'=> 'company'
);
$myposts = get_posts( $args );
foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
  <dl class='news'>
    <dt class='news-title'><?php the_title(); ?></dt>
    <dd class='news-date'><?php the_date('Y.m.d'); ?></dd>
    <dd class='news-content'><?php the_content(); ?></dd>
  </dl>
<?php
endforeach;
wp_reset_postdata();
?>

	</main><!-- #main -->
</section><!-- #primary -->

<?php if ( '3-column' !== get_theme_mod( 'businesspress_content_archive' ) ): ?>
	<?php get_sidebar(); ?>
<?php endif; ?>
<?php get_footer(); ?>

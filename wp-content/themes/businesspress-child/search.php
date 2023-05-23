<?php
/**
 * The template for displaying search results pages.
 *
 * @package BusinessPress
 */

get_header(); ?>

<section id="primary" class="archive-cont">
	<main id="main" class="site-main">

	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'businesspress' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		</header><!-- .page-header -->

		<div class="loop-wrapper">
		<?php
		/*
		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : the_post();?>
			<div class="post-full">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php if ( is_sticky() && is_home() && ! is_paged() ): ?>
					<div class="featured"><?php esc_html_e( 'Featured', 'businesspress' ); ?></div>
					<?php endif; ?>
					<?php businesspress_category(); ?>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<?php businesspress_entry_meta(); ?>
					<?php if ( has_post_thumbnail() && ! get_theme_mod( 'businesspress_hide_featured_image_on_full_text' ) ): ?>
					<div class="post-thumbnail">
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
					</div>
					<?php endif; ?>
				</header><!-- .entry-header -->
				<div class="entry-content">
					<?php the_content(); ?>
					<?php wp_link_pages( array(	'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'businesspress' ), 'after'  => '</div>', 'pagelink' => '<span class="page-numbers">%</span>',  ) ); ?>
				</div><!-- .entry-content -->
			</article><!-- #post-## -->
		</div><!-- .post-full -->
		<?php endwhile; ?>
		
		</div>

		<?php
		the_posts_pagination( array(
			'prev_text' => esc_html__( '&laquo; Previous', 'businesspress' ),
			'next_text' => esc_html__( 'Next &raquo;', 'businesspress' ),
		) );
		?>

	<?php else : ?>

		<?php get_template_part( 'template-parts/content', 'none' ); ?>

	<?php endif; ?>

	</main><!-- #main -->
</section><!-- #primary -->

<?php
/*
<?php if ( '3-column' !== get_theme_mod( 'businesspress_content_search' ) ): ?>
	<?php get_sidebar(); ?>
<?php endif; ?>
*/
?>
<?php get_footer(); ?>

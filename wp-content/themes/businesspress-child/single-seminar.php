<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package BusinessPress
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site school-list <?php echo get_post_field( 'post_name', get_the_ID() ); ?>">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'businesspress' ); ?></a>

	<header id="masthead" class="site-header">

		<?php if ( get_theme_mod( 'businesspress_enable_top_bar' ) ) : ?>
		<div class="top-bar">
			<div class="top-bar-content">
				<?php businesspress_top_bar_main(); ?>
				<?php businesspress_header_social_link(); ?>
			</div><!-- .top-bar-content -->
		</div><!-- .top-bar -->
		<?php endif; ?>

		<div class="main-header main-header-original">
			<div class="main-header-content">
				<div class="site-branding">
					<?php businesspress_logo(); ?>
					<?php businesspress_title(); ?>
				</div><!-- .site-branding -->
				<?php businesspress_main_navigation(); ?>
				<button class="drawer-hamburger">
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'businesspress' ); ?></span>
					<span class="drawer-hamburger-icon"></span>
				</button>
			</div><!-- .main-header-content -->
			<div class="drawer-overlay"></div>
			<div class="drawer-navigation">
				<div class="drawer-navigation-content">
				<?php businesspress_main_navigation(); ?>
				<?php if ( get_theme_mod( 'businesspress_enable_top_bar' ) ) : ?>
				<?php businesspress_header_social_link(); ?>
				<?php endif; ?>
				</div><!-- .drawer-navigation-content -->
			</div><!-- .drawer-navigation -->
		</div><!-- .main-header -->

		<?php if ( is_front_page() && get_theme_mod( 'businesspress_enable_home_header' ) ) : ?>
			<?php get_template_part( 'template-parts/content', 'home-header' ); ?>
		<?php elseif ( is_page() && ! get_post_meta( get_the_ID(), 'businesspress_hide_page_title', true ) ) : ?>
		
		<?php endif; ?>

		<?php if ( is_home() && ! is_paged() && get_theme_mod( 'businesspress_enable_featured_slider' ) ) : ?>
		<div class="featured-post">
			<?php
			$featured = new WP_Query( array(
				'cat'                 => get_theme_mod( 'businesspress_featured_category' ),
				'posts_per_page'      => get_theme_mod( 'businesspress_featured_slider_number', '4' ),
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true
			) );
			if ( $featured->have_posts() ) :
				while ( $featured->have_posts() ) : $featured->the_post();
					get_template_part( 'template-parts/content', 'featured' );
				endwhile;
			endif;
			wp_reset_postdata(); ?>
		</div><!-- .featured-post -->


		<?php elseif ( is_home() && ! is_paged() && ! is_front_page() && ! get_post_meta( get_option( 'page_for_posts' ), 'businesspress_hide_page_title', true ) ) : ?>
		<div class="jumbotron"<?php businesspress_post_background( get_post_thumbnail_id( get_option( 'page_for_posts' ) ) ); ?>>
			<div class="jumbotron-overlay">
				<div class="jumbotron-content">
					<?php if ( ! get_theme_mod( 'businesspress_hide_subheader' ) ) : ?>
					<div class="subheader"><?php echo esc_attr( str_replace( '-', ' ', get_post_field( 'post_name', get_option( 'page_for_posts' ) ) ) ); ?></div>
					<?php endif; ?>
					<h1 class="jumbotron-title"><?php echo get_the_title( get_option( 'page_for_posts' ) ); ?></h1>
				</div><!-- .jumbotron-content -->
			</div><!-- .jumbotron-overlay -->
		</div><!-- .jumbotron -->
		<?php endif; ?>

		<div class="jumbotron detail-head">
			<div class="jumbotron-overlay">
				<div class="jumbotron-content">
					<h2 class="jumbotron-title"><?php the_title(); ?></h2>


					<div class="list-cat List List02">

						<?php
						/*	

						<?php
						$term_parent = array_shift(get_terms('college-area',[
						'object_ids'=>$post->ID,
						'parent'=>0
						]));
						$term_parent_name = $term_parent->name;
						//echo '<span>' . esc_html($term_parent_name) . '</span>';

						echo "<a href=\"" . esc_url(get_term_link($term_parent)) . "\">" . esc_html($term_parent->name) . "</a>";
						?>

						<span>/</span>

				
						<?php
						$terms = get_the_terms( $post ->ID, 'college-area' );
						foreach( $terms as $term ) {
						if($term->parent){
							//echo $term->name;
							echo "<a href=\"" . esc_url(get_term_link($term)) . "\">" . esc_html($term->name) . "</a>";
						}
						}
						?>

						*/
						?>


					</div>	

				</div><!-- .jumbotron-content -->
			</div><!-- .jumbotron-overlay -->
		</div><!-- .jumbotron -->

		<div class="head-img">
			<div class="container">
				<ul>
					<li><img src="<?php echo $cfs->get('seminar-img-1'); ?>" alt=""></li>
					<li><img src="<?php echo $cfs->get('seminar-img-2'); ?>" alt=""></li>
				</ul>
			</div>	
		</div>	

		<?php if(get_post_meta($post->ID,'seminar-research',true)): ?>
		<div class="head-txt">
			<div class="container">
				<h2>研究テーマや特色</h2>
				<p>
				<?php echo $cfs->get('seminar-research'); ?>
				</p>
				
			</div>	
		</div>	
		<?php else: ?>
		<?php endif; ?>	



	</header><!-- #masthead -->

<div id="content-top" class="site-content-top">


<div class="school-info">

	<div class="container">

	<?php
	/*
		
		<h2>学校情報</h2>
		
		<table>
			<tr>
				<th>住　所</th>
				<td><?php echo $cfs->get('address-sc'); ?></td>	
			</tr>

			<tr>
				<th>電話番号</th>
				<td><?php echo $cfs->get('tell-sc'); ?></td>	
			</tr>

			<tr>
				<th>ホームページ</th>
				<td><a href="<?php echo $cfs->get('web-sc'); ?>"><?php echo $cfs->get('web-sc'); ?></a></td>	
			</tr>

			<tr>
				<th>メールアドレス</th>
				<td><?php echo $cfs->get('mail-sc'); ?></td>	
			</tr>

		
		</table>

		*/
		?>

		<button class="return-btn"><a href="<?php echo $cfs->get('seminar-url'); ?>">大学情報にもどる</a></button>

	</div>	

</div>




<?php get_sidebar( 'page' ); ?>
<?php get_footer(); ?>



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
						// get_the_terms(投稿IDを指定, タクソノミー名を指定)で、タームのオブジェクトを配列で取得する
						$term_obj = get_the_terms(get_the_ID(), 'college-area');
						$term_obj = $term_obj[0];
						// get_term_parents_list 現在の記事の親タームをセパレータを指定してリンク有りで取得して出力
						echo get_term_parents_list(
						$term_obj->term_id, // タームIDを指定
						'college-area', // タクソノミー名を指定
						array(
							'separator' => '<span>/</span>', // 区切り文字を指定 デフォルトは'/'
							'link' => true, // リンクにする デフォルトはtrue
						)
						);
						?>

					</div>	

				</div><!-- .jumbotron-content -->
			</div><!-- .jumbotron-overlay -->
		</div><!-- .jumbotron -->

		<div class="head-img">
			<div class="container">
				<ul>
					<li><img src="<?php echo $cfs->get('img-1'); ?>" alt=""></li>
					<li><img src="<?php echo $cfs->get('img-2'); ?>" alt=""></li>
				</ul>
			</div>	
		</div>	

		<div class="head-txt">
			<div class="container">
				<h2>研究テーマや特色</h2>
				<p>
				<?php echo $cfs->get('research'); ?>
				</p>
				
			</div>	
		</div>		



	</header><!-- #masthead -->

<div id="content-top" class="site-content-top">


<div class="school-info">

	<div class="container">
		
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

		<button class="return-btn"><a href="<?php echo home_url(); ?>">トップに戻る</a></button>

	</div>	

</div>




<?php get_sidebar( 'page' ); ?>
<?php get_footer(); ?>



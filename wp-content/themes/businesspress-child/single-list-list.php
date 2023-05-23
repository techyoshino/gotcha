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
<div id="page" class="site <?php echo get_post_field( 'post_name', get_the_ID() ); ?>">
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



		<?php
		if ($terms = get_the_terms($post->ID, 'company_area')) {
		foreach ( $terms as $term ):
		if($term->parent) echo esc_html($term->name);
		endforeach;
		} ?>




<?php

/*
$terms = get_terms('company_area'); // タクソノミーの指定
foreach ($terms as $term) {
	echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
}
*/
?>

<?php echo get_the_term_list($post->ID,'company_area'); ?>


<?php
/*
$terms = get_terms('company_area'); // タクソノミーの指定
foreach ($terms as $term) {
	echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
}
*/


//単体で記述する場合
get_term_link($term->slug, "company_area"); // タクソノミーの指定
 
//別の場所で指定して呼び出す場合
$terms = get_terms('company_area');  // タクソノミーの指定
foreach ($terms as $term) {
echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
}
?>

<?php
if ($terms = get_the_terms($post->ID, 'company_area')) {
foreach ( $terms as $term ):
if($term->parent) echo esc_html($term->name);
endforeach;
} ?>

<?php
$terms = get_the_terms($post->ID, 'company_area');
foreach($terms as $term){
$term_name = $term->name;
echo $term_name; break; };
?>

<?php
$term = get_the_terms($post->ID,'company_area');
echo '<li><a href="'.get_term_link($term[1]->slug,'company_area').'">'.$term[1]->name.'</a></li>';
?>








		

		
		<div class="jumbotron detail-head">
			<div class="jumbotron-overlay">
				<div class="jumbotron-content">
					<h2 class="jumbotron-title"><?php the_title(); ?></h2>


					
					<ul class="List List02" itemscope itemtype="http://schema.org/BreadcrumbList">

					
						<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
							<a href="＃" itemprop="item" class="List-Item-Link">
							<span itemprop="name">
								
							<?php
							/*
							$terms = get_the_terms( $post->ID, 'company_area' );
							if ( !empty( $terms ) ) {
								$output = array();
								foreach ( $terms as $term ){
								if( 0 == $term->parent )
								$output[] = '<a href="' . get_term_link( $term ) .'">' . $term->name . '</a>';
								}
								if( count( $output ) )
								echo '' . join( ", ", $output ) . '';
							}
							*/
							?>

							<?php
							$term = get_the_terms($post->ID,'company_area');
							echo '<a href="'.get_term_link($term[0]->slug,'company_area').'">'.$term[0]->name.'</a>';
							?>
			
							</span>
							</a>
							<meta itemprop="position" content="1" />
						</li>

						<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
							<!-- <a href="＃" itemprop="item" class="List-Item-Link"> -->

						<?php
						/*	
							<span itemprop="name">
								<?php $term_obj = get_the_terms( get_the_ID(), 'company_area'); ?>
								<a href="<?php echo esc_url(get_term_link($term_obj[0])); ?> "><?php echo $term_obj[0]->name; ?></a>
								
							</span>
						*/
						?>
						
						<?php
						$term = get_the_terms($post->ID,'company_area');
						echo '<a href="'.get_term_link($term[1]->slug,'company_area').'">'.$term[1]->name.'</a>';
						?>
							<!-- </a> -->
							<meta itemprop="position" content="2" />
						</li>

						<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
							<!-- <a href="＃" itemprop="item" class="List-Item-Link"> -->
							<span itemprop="name">
								
							<?php

							
							$terms = get_the_terms( $post->ID, 'company_category' );
							if ( !empty( $terms ) ) {
								$output = array();
								foreach ( $terms as $term ){
								if( 0 == $term->parent )
								$output[] = '<a href="' . get_term_link( $term ) .'">' . $term->name . '</a>';
								}
								if( count( $output ) )
								echo '' . join( ", ", $output ) . '';
							}
							
							?>
							
							<?php
							/*
							$terms = get_the_terms($post->ID,'company_category');
							foreach( $terms as $term ) {
							echo $term->name;
							}
							*/
							?>

							<?php
							$terms = get_the_terms($post->ID,'company_category');
							foreach( $terms as $term ) {
							echo '<a href="'.get_term_link($term->slug, 'company_category').'">'.$term->name.'</a>';
							}
							?>
							</span>
							<!-- </a> -->
							<meta itemprop="position" content="2" />
						</li>


					
					</ul>

				</div><!-- .jumbotron-content -->
			</div><!-- .jumbotron-overlay -->
		</div><!-- .jumbotron -->


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

	</header><!-- #masthead -->

	<div id="content-top" class="site-content-top">





<?php 
////////////////////////////////////////////////////////////
//ここから 
?>

<?php
/*

<?php
$args = array(
	'post_type' => 'company', //カスタム投稿タイプを指定
	'taxonomy' => 'company_category', //カスタムタクソノミーを指定

);
$news_query = new WP_Query( $args ); //サブループを変数に格納
if ( $news_query->have_posts() ) : 
	while ( $news_query->have_posts() ) : 
	$news_query->the_post(); 
?>

*/

?>


<!-- ここにhtml -->


<div class="detail-slide">
	<!-- Slider main container -->
		<div class="swiper-container card-swiper px-5">
			<!-- Additional required wrapper -->
			<div class="swiper-wrapper">
				
				<!-- Slides -->



				<?php

				
				$fields = $cfs->get('slide_image_loop');
				foreach ((array)$fields as $field) :
				?>
					<?php
					$iffield = $field['slide_image'];
					if($iffield) :?>

						<div class="swiper-slide">
							<div class="card">
								<img src="<?php echo $field['slide_image']; ?>" alt="">
								<div class="card-body">
									<h5 class="card-title">Card 01</h5>
								</div>
							</div>
						</div>

						<?php else : ?>
												
					<?php endif; ?>
				<?php endforeach; ?>

				

				
	 


				<?php
				/*

				<div class="swiper-slide">
					<div class="card">
						<img src="<?php echo get_template_directory_uri(); ?>/images/top-img8.png" alt="">
						<div class="card-body">
							<h5 class="card-title">Card 01</h5>
							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
						</div>
					</div>
				</div>

				<div class="swiper-slide">
					<div class="card">
						<img src="<?php echo get_template_directory_uri(); ?>/images/top-img9.png" alt="">
						<div class="card-body">
							<h5 class="card-title">Card 01</h5>
							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
						</div>
					</div>
				</div>

				<div class="swiper-slide">
					<div class="card">
						<img src="<?php echo get_template_directory_uri(); ?>/images/top-img7.png" alt="">
						<div class="card-body">
							<h5 class="card-title">Card 01</h5>
							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
						</div>
					</div>
				</div>

				<div class="swiper-slide">
					<div class="card">
						<img src="<?php echo get_template_directory_uri(); ?>/images/top-img8.png" alt="">
						<div class="card-body">
							<h5 class="card-title">Card 01</h5>
							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
						</div>
					</div>
				</div>

				<div class="swiper-slide">
					<div class="card">
						<img src="<?php echo get_template_directory_uri(); ?>/images/top-img9.png" alt="">
						<div class="card-body">
							<h5 class="card-title">Card 01</h5>
							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
						</div>
					</div>
				</div>

				*/
				?>


			</div>

			<!-- <div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div> -->
			<div class="swiper-pagination"></div>
 

		</div>

</div><!-- //detail-slide -->

<div class="detail-cont">

	<div class="container">
		
		<h2><img src="<?php echo get_template_directory_uri(); ?>/images/detail1-img.png" alt=""></h2>

		<div class="detail-cont-one">
			<div class="youtube">
				<?php
				/*
				<iframe width="560" height="315" src="https://www.youtube.com/embed/cKf4cqx3MXQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				*/
				?>

				<?php echo CFS()->get('youtube_url'); ?>
					
			</div>
		</div>
		
		<div class="detail-cont-two">
			<div class="detail-cont-two-head">
				<div class="detail-cont-two-l">
					<h3>取材後記</h3>
				
				
					<?php echo $cfs->get('Interview'); ?>

					
					<div class="detail-author">
						<?php echo $cfs->get('writer'); ?>
					</div>	
				</div>	
				<div class="detail-cont-two-r">
					<img src="<?php echo get_template_directory_uri(); ?>/images/detail2-img.png" alt="">
				</div>
			</div><!-- //detail-cont-two-head	 -->

			<div class="detail-cont-two-bottom">

				<dl class="detail-author">
					<dt>企業取材をした学校はこちら</dt>
					<dd><?php echo $cfs->get('school'); ?></dd>
				</dl>
				
				<button class="detail-school-btn"><a href="<?php echo $cfs->get('school-link'); ?>" target=”_blank”>学校を詳しく見る</a></button>


			</div>	<!-- //detail-cont-two-bottom -->



		</div><!-- //detail-cont-two -->

	</div>

</div><!-- //detail-cont -->

<div class="detail-info">

	<div class="container">
		
		<h2>企業情報</h2>
		
		<table>
			<tr>
				<th>住　所</th>
				<td><?php echo CFS()->get('address'); ?></td>	
			</tr>

			<tr>
				<th>電話番号</th>
				<td><?php echo CFS()->get('phone'); ?></td>	
			</tr>

			<tr>
				<th>代表者</th>
				<td><?php echo CFS()->get('representative'); ?></td>	
			</tr>

			<tr>
				<th>ホームページ</th>
				<td><a href="<?php echo CFS()->get('web'); ?>" target=”_blank”><?php echo CFS()->get('web'); ?></a></td>	
			</tr>

			<tr>
				<th>メールアドレス</th>
				<td><?php echo CFS()->get('mailaddress'); ?></td>	
			</tr>

			<tr>
				<th>事業内容</th>
				<td>
				<?php echo CFS()->get('about_company_top'); ?>
				</td>	
			</tr>
			
		</table>

		<button class="detail-return-btn">一覧戻る</button>

		

	</div>	

</div>



<?php 
////////////////////////////////////////////////////////////
//ここで終わり 
?>

<?php
/*

<?php endwhile;
endif;
wp_reset_postdata(); //サブループを抜ける
?>	

*/
?>





<div id="primary" class="content-area">
	<main id="main" class="site-main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'template-parts/content', 'page' ); ?>

		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		?>

	<?php endwhile; // End of the loop. ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar( 'page' ); ?>
<?php get_footer(); ?>

<script>
	
  const swiper = new Swiper('.card-swiper', {
  // Optional parameters
  slidesPerView: 1,
  spaceBetween: 10,
  autoplay: true,
  buttons: true,

  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },


  // Navigation arrows

  /*
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  */

  breakpoints: {
    // when window width is >= 480px
    480: {
      slidesPerView: 2,
      spaceBetween: 10
    },
    // when window width is >= 768px
    768: {
      slidesPerView: 3,
      spaceBetween: 15
    },
    // when window width is >= 1024px
    1024: {
      slidesPerView: 4,
      spaceBetween: 20
    }
  }
});
</script>

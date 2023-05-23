<?php
/**
 * The template for the blog posts index page.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BusinessPress
 */

get_header(1); ?>


<div id="top-img" class="top-cont-second">
	<img src="<?php echo get_template_directory_uri(); ?>/images/top-img.png" alt="">
</div><!-- //top-img -->


<div class="top-cont-third">
	<div class="container">
		<div class="container-in">
			
			<div class="container--in">
				学生が企業を訪問・取材をして<br />
				会社の魅力・情報を紹介するサイト<br />
				それが…
			</div>
			<h2>「中小企業GOTCHA!」です！</h2>
			<?php
			/*
			<img src="<?php echo get_template_directory_uri(); ?>/images/top-img2.png" alt="" class="container--in-img">
			*/
			?>
			<div class="about-btn"><a href="<?php echo home_url(); ?>/about">詳しくはこちら</a></div>	
		</div>
		
	</div><!-- //container -->
</div><!-- //top-cont-third -->



<div class="top-cont-four">
	<div class="container">
		<div class="container-in">
			<h2><img src="<?php echo get_template_directory_uri(); ?>/images/top-img3.png" alt=""></h2>
			
			<div class="container--in">

			<?php query_posts("post_type=company&posts_per_page=14");//カスタム投稿タイプ名 ?>
			<?php if(have_posts()): ?>
			<?php while(have_posts()): the_post(); ?>

			<div class="container---in">

				<div class="post-thumbnail">

					<a href="<?php the_permalink(); ?>">
					<?php
					if(has_post_thumbnail()):
						the_post_thumbnail();
					else:
					?>
					<img src="<?php echo get_template_directory_uri(); ?>/images/no-image.jpg" alt="" />
					<?php endif; ?>
					</a>
				</div>	
					
					<div class="container---in-top">

						<div class="contents-L">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

						</div>

						<div class="contents-R">
							<?php $txtid = CFS()->get('youtube_url'); ?>
							<?php if(empty($txtid)):?>
							<!--カスタムフィールドの値が無い場合（空欄）に表示されます(空でOK)。--> 		
							<?php else:?>
							<!--値がある場合表示される内容を記述。-->
							<span>movie</span>
							<?php endif;?>
						</div>

					</div>

					<div class="container---in-bottom">

						<div class="contents-L">


						<div class="List-Item">
							<?php
							// get_the_terms(投稿IDを指定, タクソノミー名を指定)で、タームのオブジェクトを配列で取得する
							$term_obj = get_the_terms(get_the_ID(), 'company_area');
							$term_obj = $term_obj[0];
							// get_term_parents_list 現在の記事の親タームをセパレータを指定してリンク有りで取得して出力
							echo get_term_parents_list(
							$term_obj->term_id, // タームIDを指定
							'company_area', // タクソノミー名を指定
							array(
								'separator' => '<span>/</span>', // 区切り文字を指定 デフォルトは'/'
								'link' => true, // リンクにする デフォルトはtrue
							)
							);
							?>	

							<?php
							$term = get_the_terms($post->ID,'company_category');
							echo '<a href="'.get_term_link($term[0]->slug,'company_category').'">'.$term[0]->name.'</a>';
							?>
						</div>

						<?php
						$terms = get_the_terms( $post ->ID, 'company_area' );
						foreach( $terms as $term ) {
						if($term->parent){
							echo $term->name;
						}
						}
						?>

						<?php
						$terms = get_the_terms( $post ->ID, 'company_area' );
						foreach( $terms as $term ) {
						if($term){
							echo $term->name;
						}
						}
						?>

<?php
$tax_name = 'company_area'; //タクソノミースラッグを指定
$terms = get_the_terms($post->ID, $tax_name);
$term = $terms[0];
?>
<p><a href="<?php echo get_term_link($term->slug, $tax_name) ?>"><?php echo $term->name ?></a></p>







						</div>

						<div class="contents-R">
							<span><?php the_time( get_option('date_format') ); ?></span>
						</div>

					</div>

				</div>
			
			<?php endwhile; ?>

				
			</div>

			<?php endif; wp_reset_query(); ?>	

			<div class="more">
				<button>view more</button>
			</div>
	
		</div>
		

		
		
	</div><!-- //container -->
</div><!-- //top-cont-four -->




<div class="top-cont-five">
	<div class="contents-top">
		<div class="container">
			<h2>▼▼▼　登録企業を検索できます。各項目を設定して「GOTCHA!」ボタンを押してください。　▼▼▼</h2>
		</div>	
	</div><!-- //contents-top -->

	<div class="contents-bottom">

		<div class="container">

			<div class="contents-L">
				
				<div class="seach_page_col_in">		
					<?php
					if ( function_exists( 'feas_search_form' ) ) {
						feas_search_form(0);
					}
					?>
					<?php wp_link_pages( array(	'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'businesspress' ), 'after'  => '</div>', 'pagelink' => '<span class="page-numbers">%</span>',  ) ); ?>
			

					<?php
					/*
					<img src="<?php echo get_template_directory_uri(); ?>/images/top-img5.png" alt="">
					*/
					?>
				<h2 style="margin: 0;color: #72D5D1;">clickはこちらより</h2>

			</div>

			

			<style>
				#feas-submit-button-0{
					border: 0px;
					margin-top:100px;
					width: 340px;
    				height: 340px;
					background: url(https://chusho-gotcha.com/wp-content/themes/businesspress-child/images/top-img5.png) left top no-repeat;
				}
			</style>

			<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>

			<script>
				document.getElementById( "feas-submit-button-0" ).value = "" ;
			//	$('#feas-submit-button-0').attr('src', 'http://localhost:8888/gotcha/wp-content/themes/businesspress-child/images/top-img5.png');
			</script>


		</div>

	</div><!-- //contents-bottom -->

</div><!-- //top-cont-five -->

<?php
/*
<div class="top-cont-six">
	<h2><img src="<?php echo get_template_directory_uri(); ?>/images/top-img6.png" alt=""></h2>

	<!-- Slider main container -->
		<div class="swiper-container card-swiper px-5">
			<!-- Additional required wrapper -->
			<div class="swiper-wrapper">
				
				<!-- Slides -->
				

				<?php
				$information= get_posts( array(
				'post_type' => 'seminar'
				));
				if( $information):
				?>
				<?php
				foreach( $information as $post ):
				setup_postdata( $post );
				?>

					<?php if(get_post_meta($post->ID, 'pr',true)): ?>
					<div class="swiper-slide">
						<div class="card">

						
						<a href="<?php the_permalink(); ?>"><img src="<?php echo $cfs->get('seminar-img-1'); ?>" alt=""></a>
							<div class="card-body">
								<h5 class="card-title"><?php the_title(); ?></h5>
								<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
							</div>
						</div>
					</div>
					<?php endif; ?>	

				<?php
				endforeach;
				wp_reset_postdata();
				?>
				
				<?php else: ?>
				<p>表示できる情報はありません。</p>
				<?php endif; ?>


		

			</div>

			<!-- <div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div> -->
			<div class="swiper-pagination"></div>
 

		</div>

</div><!-- //top-cont-six -->
*/
?>

<?php
/*
<?php
$information= get_posts( array(
'post_type' => 'seminar',
));
if( $information):
?>

<ul>
<?php
foreach( $information as $post ):
setup_postdata( $post );
?>
<?php if(get_post_meta($post->ID, 'pr',true)): ?>
<li>
<img src="<?php echo $cfs->get('seminar-img-1'); ?>" alt="">
<?php the_time('Y年n月j日'); ?> - <a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a>
</li>
<?php endif; ?>
<?php
endforeach;
wp_reset_postdata();
?>
</ul>
<?php else: ?>
<p>表示できる情報はありません。</p>
<?php endif; ?>




<?php
/*
<ul>
<?php
foreach( $information as $post ):
setup_postdata( $post );
?>
<li>
<img src="<?php echo $cfs->get('seminar-img-1'); ?>" alt="">
<?php the_time('Y年n月j日'); ?> - <a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a>
</li>
<?php
endforeach;
wp_reset_postdata();
?>
</ul>
<?php else: ?>
<p>表示できる情報はありません。</p>
<?php endif; ?>
*/
?>






<?php
/*

<?php if ( '3-column' !== get_theme_mod( 'businesspress_content' ) ): ?>
	<?php get_sidebar(); ?>
<?php endif; ?>

*/
?>


</div><!-- #content -->

	<div class="contact-cont" style="background: #fff;">
		<div class="container">
			<h2>「中小企業GOTCHA!」についてお問い合わせはこちら</h2>
			<button type="button"><a href="<?php echo home_url(); ?>/contact">問い合わせる</a></button>
		</div>	
	</div><!-- //contact-cont -->

	<footer id="colophon" class="site-footer">

		<?php get_sidebar( 'footer' ); ?>

		<?php if ( has_nav_menu( 'footer' ) || has_nav_menu( 'footer-social' ) || ! get_theme_mod( 'businesspress_hide_footer_text' ) || ! get_theme_mod( 'businesspress_hide_credit' ) ) : ?>
		<div class="site-bottom">
			<div class="site-bottom-content">

				<?php if ( has_nav_menu( 'footer' ) || has_nav_menu( 'footer-social' ) ) : ?>
				<div class="footer-menu">
					<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav class="footer-navigation">
						<?php wp_nav_menu( array( 'theme_location' => 'footer' , 'depth' => 1 ) ); ?>
					</nav><!-- .footer-navigation -->
					<?php endif; ?>
					<?php if ( has_nav_menu( 'footer-social' ) ) : ?>
					<nav class="footer-social-link social-link-menu">
						<?php wp_nav_menu( array( 'theme_location' => 'footer-social', 'depth' => 1, 'link_before'  => '<span class="screen-reader-text">', 'link_after'  => '</span>' ) ); ?>
					</nav><!-- .footer-social-link -->
					<?php endif; ?>
				</div><!-- .footer-menu -->
				<?php endif; ?>

				<?php businesspress_footer(); ?>

			</div><!-- .site-bottom-content -->
		</div><!-- .site-bottom -->
		<?php endif; ?>

	</footer><!-- #colophon -->
</div><!-- #page -->

<div class="back-to-top"></div>

<?php wp_footer(); ?>

</body>
</html>



<style>

.detail-slide .swiper-wrapper{
	justify-content: unset;
}


</style>

<script>
  const swiper = new Swiper('.card-swiper', {
  // Optional parameters
  slidesPerView: 1,
  spaceBetween: 10,
  buttons: true,
  autoplay: {
        disableOnInteraction: false
    },

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

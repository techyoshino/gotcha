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
			<h2>「中小企業GOTCHA!」とは？</h2>
			<div class="container--in">
			この文章はダミーですここには企画意図が入ります<br />
			大学生主導による中小企業様への訪問を通じて、<br />
			地元企業を目で見て、耳で聞いて、肌で感じてもらい<br />
			その魅力を知ってもらうことを目的とした活動です。<br />
			</div>
			<img src="<?php echo get_template_directory_uri(); ?>/images/top-img2.png" alt="" class="container--in-img">	
		</div>
		
	</div><!-- //container -->
</div><!-- //top-cont-third -->



<div class="top-cont-four">
	<div class="container">
		<div class="container-in">
			<h2><img src="<?php echo get_template_directory_uri(); ?>/images/top-img3.png" alt=""></h2>
			
			<div class="container--in">

			<?php query_posts("post_type=company&posts_per_page=10");//カスタム投稿タイプ名 ?>
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

						<?php
						/*

							<ul class="List List02" itemscope itemtype="http://schema.org/BreadcrumbList">

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">神奈川県</span>
									</a>
									<meta itemprop="position" content="1" />
								</li>
								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">横浜市</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>
								
							</ul>

							*/

							?>

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

						</div>

						<div class="contents-R">
							<span><?php the_date(); ?></span>
						</div>

					</div>

				</div>
			
			<?php endwhile; ?>

				
				
				
				<?php
				/*
				<div class="container---in">
					<img src="<?php echo get_template_directory_uri(); ?>/images/top-img4.png" alt="">
					
					<div class="container---in-top">

						<div class="contents-L">
							<h3>石山ネジ株式会社</h3>

						</div>

						<div class="contents-R">
							<span>movie</span>
						</div>

					</div>

					<div class="container---in-bottom">

						<div class="contents-L">

							<ul class="List List02" itemscope itemtype="http://schema.org/BreadcrumbList">

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">神奈川県</span>
									</a>
									<meta itemprop="position" content="1" />
								</li>
								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">横浜市</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>
								
							</ul>

						</div>

						<div class="contents-R">
							<span>2022.00.00</span>
						</div>

					</div>

				</div>	


				<div class="container---in">
					<img src="<?php echo get_template_directory_uri(); ?>/images/top-img4.png" alt="">
					
					<div class="container---in-top">

						<div class="contents-L">
							<h3>石山ネジ株式会社</h3>

						</div>

						<div class="contents-R">
							<span>movie</span>
						</div>

					</div>

					<div class="container---in-bottom">

						<div class="contents-L">

							<ul class="List List02" itemscope itemtype="http://schema.org/BreadcrumbList">

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">神奈川県</span>
									</a>
									<meta itemprop="position" content="1" />
								</li>
								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">横浜市</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>
								
							</ul>

						</div>

						<div class="contents-R">
							<span>2022.00.00</span>
						</div>

					</div>

				</div>	


				<div class="container---in">
					<img src="<?php echo get_template_directory_uri(); ?>/images/top-img4.png" alt="">
					
					<div class="container---in-top">

						<div class="contents-L">
							<h3>石山ネジ株式会社</h3>

						</div>

						<div class="contents-R">
							<span>movie</span>
						</div>

					</div>

					<div class="container---in-bottom">

						<div class="contents-L">

							<ul class="List List02" itemscope itemtype="http://schema.org/BreadcrumbList">

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">神奈川県</span>
									</a>
									<meta itemprop="position" content="1" />
								</li>
								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">横浜市</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>
								
							</ul>

						</div>

						<div class="contents-R">
							<span>2022.00.00</span>
						</div>

					</div>

				</div>	

				<div class="container---in">
					<img src="<?php echo get_template_directory_uri(); ?>/images/top-img4.png" alt="">
					
					<div class="container---in-top">

						<div class="contents-L">
							<h3>石山ネジ株式会社</h3>

						</div>

						<div class="contents-R">
							<span>movie</span>
						</div>

					</div>

					<div class="container---in-bottom">

						<div class="contents-L">

							<ul class="List List02" itemscope itemtype="http://schema.org/BreadcrumbList">

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">神奈川県</span>
									</a>
									<meta itemprop="position" content="1" />
								</li>
								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">横浜市</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>

								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>


								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>


								<li class="List-Item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
									<a href="＃" itemprop="item" class="List-Item-Link">
									<span itemprop="name">製造業</span>
									</a>
									<meta itemprop="position" content="2" />
								</li>
								
							</ul>

						</div>

						<div class="contents-R">
							<span>2022.00.00</span>
						</div>

					</div>

				</div>	
				*/
				?>

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


			</div>

			<style>
				#feas-submit-button-0{
					border: 0px;
					margin-top:100px;
					width: 340px;
    				height: 340px;
					background: url(http://monodukuri-s.sakura.ne.jp/gotcha/wp-content/themes/businesspress-child/images/top-img5.png) left top no-repeat;
				}
			</style>

			<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>

			<script>
				document.getElementById( "feas-submit-button-0" ).value = "" ;
			//	$('#feas-submit-button-0').attr('src', 'http://localhost:8888/gotcha/wp-content/themes/businesspress-child/images/top-img5.png');
			</script>

		</div>

	</div><!-- //contents-bottom -->

</div><!-- //top-cont-five -->


<div class="top-cont-six">
	<h2><img src="<?php echo get_template_directory_uri(); ?>/images/top-img6.png" alt=""></h2>

	<!-- Slider main container -->
		<div class="swiper-container card-swiper px-5">
			<!-- Additional required wrapper -->
			<div class="swiper-wrapper">
				
				<!-- Slides -->

				<div class="swiper-slide">
					<div class="card">
						<img src="<?php echo get_template_directory_uri(); ?>/images/top-img7.png" alt="">
						<div class="card-body">
							<h5 class="card-title">Card 01</h5>
							<!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
						</div>
					</div>
				</div>

			


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

</div><!-- //top-cont-six -->


<?php
$information= get_posts( array(
'post_type' => 'seminar',
'value' => 'pr'
));
if( $information):
?>
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






<?php
/*

<?php if ( '3-column' !== get_theme_mod( 'businesspress_content' ) ): ?>
	<?php get_sidebar(); ?>
<?php endif; ?>

*/
?>
<?php get_footer(); ?>


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

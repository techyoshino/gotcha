<?php
/**
 * The template for displaying all pages.
 * Template Name: about-page
 * @package BusinessPress
 */

get_header(2); ?>

<div class="about-first">
	<div class="container">
		<div class="site-content">

		<?php
		/*
		「中小企業」と聞いて<br />
		どんなイメージをお持ちですか？<br /><br />

		「給与や福利厚生は？」<br />
		「業績は安定している？」<br />
		「キャリアプランを立てられる？」<br /><br />

		マイナスなイメージを持っている方も少なくないと思います。<br />
		実際に調査をしてみると、独自性に溢れ<br />
		魅力たっぷりな優良中小企業もたくさん存在しました。<br /><br />

		ところがそんな優良中小企業も<br />
		「若い人材がほしい…」「人手が足りない…」<br />
		といった悩みを抱えています。<br /><br />

		そこで、中小企業と学生をマッチングさせ<br />
		お互いの発展に貢献するプロジェクト、<br />
		それが「中小企業GOTCHA!」です。<br />
		*/
		?>
		<?php echo $cfs->get('about-1'); ?>
		</div>	
	</div>	
</div>	<!-- //abuot-first -->



<?php
/*

<div class="about-second">
	<div class="container">
		<div class="site-content">
			<h3>
			中小企業GOTCHA!では、企業側と学生側それぞれの<br />
			心配や悩み事を解決できるよう、以下の取り組みを行っています。
			</h3>

			<div class="site-content-in">
				<div class="site-content-in-cont">
					<h4>
						現役学生が企業に訪問・取材し<br />
						あなたの会社を動画でご紹介
					</h4>
					<p>
					学生が企業訪問で調査をし、素直な感想をもとにPR動画を作成します。動画内容は、ランキング形式であったり、インタビュー形式であったり様々…。口コミやネット上の情報だけでは分からない部分を覗きます！あなたの身近にも素晴らしい企業があるかもしれません。
					</p>		
				</div>	
				<div class="site-content-in-img">
				<img src="<?php echo get_template_directory_uri(); ?>/images/about2-img.png" alt="">
				</div>	
			</div>


			<div class="site-content-in">
				<div class="site-content-in-cont">
					<h4>
						撮影機材の準備や、訪問のアポ取りは<br />
						テクトレージにお任せ
					</h4>
					<p>
					この文章はダミーです。学生が企業訪問で調査をし、素直な感想をもとにPR動画を作成します。動画内容は、ランキング形式であったり、インタビュー形式であったり様々…。口コミやネット上の情報だけでは分からない部分を覗きます！あなたの身近にも素晴らしい企業があるかもしれません。
					</p>		
				</div>	
				<div class="site-content-in-img">
				<img src="<?php echo get_template_directory_uri(); ?>/images/about3-img.png" alt="">
				</div>	
			</div>

			<div class="site-content-in">
				<div class="site-content-in-cont">
					<h4>
						取材・制作した動画データは<br />
						企業様へ無償提供いたします
					</h4>
					<p>
					この文章はダミーです。学生が企業訪問で調査をし、素直な感想をもとにPR動画を作成します。動画内容は、ランキング形式であったり、インタビュー形式であったり様々…。口コミやネット上の情報だけでは分からない部分を覗きます！あなたの身近にも素晴らしい企業があるかもしれません。
					</p>		
				</div>	
				<div class="site-content-in-img">
				<img src="<?php echo get_template_directory_uri(); ?>/images/about4-img.png" alt="">
				</div>	
			</div>
			
			
		</div>	
	</div>	
</div>	<!-- //abuot-second -->
*/
?>

<div class="about-btn">
	<div class="container">
		<div class="site-content">
			<a class="button" href="<?php echo home_url(); ?>/about-company">事業者様へ</a>
			<a class="button" href="<?php echo home_url(); ?>/about-students">学生の皆さん・教育機関の先生方へ</a>
		</div>

	</div>
</div>

<div class="detail-slide">
	<!-- Slider main container -->


		<div class="swiper-container card-swiper px-5">
			<!-- Additional required wrapper -->

			
			<div class="swiper-wrapper">

				<?php
				/*
					
					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>

					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>

					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>

					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>

					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>

					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>

					<div class="swiper-slide">
						<div class="card">
							<img src="<?php echo get_template_directory_uri(); ?>/images/about-1.png" alt="">
						</div>
					</div>
				*/
				?>	

				<!-- Slides -->
				<?php
				$fields = $cfs->get('about_image_loop');
				foreach ((array)$fields as $field) :
				?>
					<?php
					$iffield = $field['about_image'];
					if($iffield) :?>

						<div class="swiper-slide">
							<div class="card">
								<img src="<?php echo $field['about_image']; ?>" alt="">
							</div>
						</div>

						<?php else : ?>
												
					<?php endif; ?>
				<?php endforeach; ?>
			
			</div>

			

			<!-- <div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div> -->
			<div class="swiper-pagination"></div>

		</div>

		

	</div><!-- //detail-slide -->


<div class="about-third">
	<div class="container">
		<div class="site-content">
			<p>ご賛同・ご協力いただける企業様、教育機関のご担当者様を随時募集しています。<br />
			少しでも興味を持っていただけましたらテクトレージまでお気軽にお問い合わせください。</p>
		</div>

	</div>
</div>		





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
  buttons: true,
  autoplay: {
        disableOnInteraction: false
    },

  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },


  // Navigation arrows


//   navigation: {
//     nextEl: '.swiper-button-next',
//     prevEl: '.swiper-button-prev',
//   },


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
  },


});





</script>


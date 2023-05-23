<?php
/**
 * The template for displaying all pages.
 * Template Name: about-students/
 * @package BusinessPress
 */

get_header(2); ?>

<div class="about-first">
	<div class="container">
		<div class="site-content">
		
		ご自身の身近なところにも、企業というものはたくさん存在します。<br />
		就職をする際に企業選びって何を基準にするのか。自分と相性の良い企業の特徴探しなど、<br />
		気になること、不安なことを実際の取材を通して解消していきましょう。<br />
		全国の学生がおそらく同じような不安を抱えています。<br />
		そんな会社の外からは見えない部分を取材して、どんな会社なのかを発信していくのが本サイトの目的です。<br />
		取材内容の発表形式は自由です。動画や写真、あなたの得意な方法で様々な企業をぜひ紹介してください。

	
		</div>	
	</div>	
</div>	<!-- //abuot-first -->


<div class="about-company-img">
	<div class="container">
	
		<?php the_post_thumbnail('full'); ?>
	</div>	
</div>	




<div class="about-second">
	<div class="container">
		<div class="site-content">
			<h3>
			中小企業GOTCHA!の取り組みにご興味を持っていただきありがとうございます。<br />
			よくある質問をまとめましたので、ご心配が少しでも解消できれば幸いです。<br />
			その他ご質問がありましたら、お気軽にお問い合わせください。
			</h3>

			<div class="site-content-in">
				<div class="site-content-in-cont">

					<dl class="Qa-Box">

						<div class="Qa">
							<dt>
								<p>取材に必要な機材等はどうすれば良いですか？</p>
							</dt>
							<dd>
								<p>カメラ、マイクなど、取材に必要な機材はテクトレージが用意します。</p>
							</dd>
						</div>

						<div class="Qa">
							<dt>
								<p>インタビューや撮影が上手くできるか不安です…。</p>
							</dt>
							<dd>
								<p>皆さんが取材に慣れるその時までテクトレージのスタッフが同行します。撮影の仕方など技術面でのサポートもしますのでご安心ください。</p>
							</dd>
						</div>

						<div class="Qa">
							<dt>
								<p>動画編集まで手が回せない場合でも参加可能ですか？</p>
							</dt>
							<dd>
								<p>編集作業にまで手を回すのが難しい場合はテクトレージが編集作業をします。</p>
							</dd>
						</div>

						<div class="Qa">
							<dt>
								<p>訪問企業はどのようにして決まりますか？</p>
							</dt>
							<dd>
								<p>テクトレージでアポ取りをします。学生と企業間の連絡もテクトレージが仲介・サポートするため、安心して取材や編集を進めていただけます。</p>
							</dd>
						</div>

						
						
					</dl>	

				</div>	
			
			</div>
		</div>	
	</div>	
</div>	<!-- //abuot-second -->


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

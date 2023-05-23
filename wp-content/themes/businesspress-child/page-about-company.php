<?php
/**
 * The template for displaying all pages.
 * Template Name: about-company
 * @package BusinessPress
 */

get_header(2); ?>

<div class="about-first">
	<div class="container">
		<div class="site-content">
		
			<p>中小企業GOTCHA!は、若い世代の人材雇用や自社のPRに興味を持たれる企業様のために、<p>

			<ul>
				<li>現役の学生が貴社をPRするコンテンツを作成します。</li>
				<li>作成したコンテンツはこのサイトにアーカイブとして掲載します。</li>
				<li>コンテンツの内容は主にPR動画の作成です。</li>
				<li>「うちでの撮影はちょっと…」という場合は取材記事やブログという形で掲載をします。	</li>		
			</ul>

	
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
								<p>完成後の動画はサイトに掲載するだけですか？自由に使用したりできますか？</p>
							</dt>
							<dd>
								<p>撮影・編集した動画は本サイト、およびテクトレージ運用の動画ポータルサイト「VIDEFIT」にて公開いたします。 取材にご協力いただきました企業様には動画データを無償でお渡しいたしますので、HPへの掲載や求人等にぜひご活用ください。</p>
							</dd>
						</div>

						<div class="Qa">
							<dt>
								<p>取材はどれくらいの時間かかりますか？</p>
							</dt>
							<dd>
								<p>取材内容や撮影にもよりますが、平均2時間前後を想定しております。</p>
							</dd>
						</div>

						<div class="Qa">
							<dt>
								<p>事前にこちらが準備すること・ものはありますか？</p>
							</dt>
							<dd>
								<p>学生が質問事項を用意している場合は取材前に共有をいたします。会社のありのままを見させていただくためにも、大きな準備等は必要ありませんので、いつも通りの姿でお願いできればと思います。</p>
							</dd>
						</div>

						<div class="Qa">
							<dt>
								<p>取材で撮影してほしくないところがあります。隠してもらえますか？<br />また、顔出しはしなくても良いですか？</p>
							</dt>
							<dd>
								<p>撮影NGな部分はもちろん配慮いたしますのでご安心ください。顔出しにつきましても可能な限り皆様に出演していただきたいですが、質問等にお答えいただく代表者様一名でも構いませんので、事前にご相談ください。</p>
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

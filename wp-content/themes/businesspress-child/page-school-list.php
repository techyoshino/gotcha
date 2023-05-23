<?php
/**
 * The template for displaying all pages.
 *
 * @package BusinessPress
 */

get_header(3); ?>



<div class="school-info">

	<div class="container">
		
		<h2>学校情報</h2>
		
		<table>
			<tr>
				<th>住　所</th>
				<td>〒223-0057 神奈川県横浜市港北区新羽町878</td>	
			</tr>

			<tr>
				<th>電話番号</th>
				<td>045-717-8886</td>	
			</tr>

			<tr>
				<th>ホームページ</th>
				<td><a href="http://www.ishiyama-nezi.co.jp">http://www.ishiyama-nezi.co.jp</a></td>	
			</tr>

			<tr>
				<th>メールアドレス</th>
				<td>info@ishiyama-nezi.co.jp</td>	
			</tr>

		
		</table>

		<button class="return-btn">トップに戻る</button>

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



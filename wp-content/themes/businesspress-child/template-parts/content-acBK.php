<?php
/**
 * @package BusinessPress
 */
?>

<div class="post-full">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php if ( is_sticky() && is_home() && ! is_paged() ): ?>
			<div class="featured"><?php esc_html_e( 'Featured', 'businesspress' ); ?></div>
			<?php endif; ?>
			<?php businesspress_category(); ?>

			<?php
			/*
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			*/
			?>

			<?php businesspress_entry_meta(); ?>
			<?php if ( has_post_thumbnail() && ! get_theme_mod( 'businesspress_hide_featured_image_on_full_text' ) ): ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
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

				</div>

				<div class="contents-R">
					<span><?php the_date(); ?></span>
				</div>

			</div>


			<?php
			/*
			
			<!--タイトル-->
			<?php the_title(); ?>
			
			<!--日時-->
			<?php the_date(); ?>
			

			<?php $txtid = CFS()->get('youtube_url'); ?>

			<?php if(empty($txtid)):?>
			<!--カスタムフィールドの値が無い場合（空欄）に表示されます(空でOK)。--> 
				なし        
			<?php else:?>
			<!--値がある場合表示される内容を記述。-->
			あり
			<?php endif;?>

		

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
			*/
			?>

			
			

			<?php endif; ?>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array(	'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'businesspress' ), 'after'  => '</div>', 'pagelink' => '<span class="page-numbers">%</span>',  ) ); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
</div><!-- .post-full -->
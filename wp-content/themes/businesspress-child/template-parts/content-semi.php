<?php
/**
 * @package BusinessPress
 */
?>
<?php
/*
<div class="post-full">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php if ( is_sticky() && is_home() && ! is_paged() ): ?>
			<div class="featured"><?php esc_html_e( 'Featured', 'businesspress' ); ?></div>
			<?php endif; ?>
			<?php businesspress_category(); ?>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<?php businesspress_entry_meta(); ?>
			<?php if ( has_post_thumbnail() && ! get_theme_mod( 'businesspress_hide_featured_image_on_full_text' ) ): ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
			</div>
			<?php endif; ?>
		</header><!-- .entry-header -->
	
		<div class="entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages( array(	'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'businesspress' ), 'after'  => '</div>', 'pagelink' => '<span class="page-numbers">%</span>',  ) ); ?>
		</div><!-- .entry-content -->
		
	</article><!-- #post-## -->
</div><!-- .post-full -->
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
			
			
			<div class="post-thumbnail">
				<?php
				/*
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
				*/
				?>

				
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
			
		</header><!-- .entry-header -->

		
		<div class="entry-content">
		
			<div class="container---in-top">

				<div class="contents-L">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

				</div>

				<div class="contents-R">
				
				
				</div>

			</div>

			<div class="container---in-bottom">

				<div class="contents-L">

					<div class="List-Item">

					<?php
					$term_parent = array_shift(get_terms('seminar-area',[
					'object_ids'=>$post->ID,
					'parent'=>0
					]));
					$term_parent_name = $term_parent->name;
					//echo '<span>' . esc_html($term_parent_name) . '</span>';

					echo "<a href=\"" . esc_url(get_term_link($term_parent)) . "\">" . esc_html($term_parent->name) . "</a>";
					?>

					<span>/</span>

			
					<?php
					$terms = get_the_terms( $post ->ID, 'seminar-area' );
					foreach( $terms as $term ) {
					if($term->parent){
						//echo $term->name;
						echo "<a href=\"" . esc_url(get_term_link($term)) . "\">" . esc_html($term->name) . "</a>";
					}
					}
					?>

					</div>

				</div>

				<div class="contents-R">
					<span><?php echo get_the_date('Y年m月d日'); ?></span>
				</div>

			</div>
		
		
			<?php wp_link_pages( array('before' => '<div class="page-links">' . esc_html__( 'Pages:', 'businesspress' ), 'after'  => '</div>', 'pagelink' => '<span class="page-numbers">%</span>',  ) ); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
</div><!-- .post-full -->
<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BusinessPress
 */

get_header(); ?>


<?php
/*

<ul class="terms-list">
  <?php
  $taxonomy = 'company_area';
  $args = array(
    'orderby'    => 'id',//並び順をID順に変更
	'kanto' => 'slug',
  );
  $terms = get_terms($taxonomy,$args);
  foreach ($terms as $term) :
    $term_link = get_term_link($term->slug,$taxonomy);
  ?>
    <li><a href="<?php echo $term_link; ?>"><?php echo $term->name; ?></a></li>
  <?php endforeach; ?>
</ul>

*/
?>


<?php
/*

<ul>
  <?php
    $terms = get_terms('company_area'); // タクソノミースラッグを指定
    foreach ( $terms as $term ) {
      echo '<li><a href="'.get_term_link($term).'">'.$term->name.'</a></li>';
    }
  ?>
</ul>

<ul>
  <?php
    $terms = get_terms('company_area'); // タクソノミースラッグを指定
    foreach ( $terms as $term ) {
      echo '<li><a href="'.get_term_link($term).'">'.$term->name.'（'.$term->count.'）</a></li>';
    }
  ?>
</ul>



<?php 
  $my_tax = 'company_area';  //取得したいタクソノミー名
  $parent_terms = get_terms( $my_tax, array('hide_empty' => false, 'parent' => 0) );  //第一階層のタームだけ取得
  if ( !empty( $parent_terms ) ) :
    echo '<ul>';

    //第1ループ
    foreach ( $parent_terms as $pt ) : 
      $pt_id = $pt->term_id;
      $pt_name = $pt->name;
      $pt_url = get_term_link($pt);
?>
      <li>
        <a href="<?php echo $pt_url; ?>"><?php echo $pt_name; ?></a>
        <?php 
          $child_terms = get_terms( $my_tax, array('hide_empty' => false, 'parent' => $pt_id) );
          if ( !empty( $child_terms ) ) :
            echo '<ul class="child">';

           //第2ループ
            foreach ( $child_terms as $ct ) : 
              $ct_id = $ct->term_id;
              $ct_name = $ct->name;
              $ct_url = get_term_link($ct);
        ?>
              <li>
                <a href="<?php echo $ct_url; ?>"><?php echo $ct_name; ?></a>
              </li>
        <?php
            endforeach;  //End : 第２ループ
            echo '</ul>';
          endif;
        ?>
      </li>
<?php
    endforeach;  //End : 第1ループ
    echo '</ul>';
  endif;
?>

*/
?>


<section id="work_col">

	<h3><i class="fa fa-briefcase" aria-hidden="true"></i>エリア一覧</h3>

	<?php 
		$my_tax = 'company_area';  //取得したいタクソノミー名
		$parent_terms = get_terms( $my_tax, array('hide_empty' => true, 'parent' => 0) );  //第一階層のタームだけ取得
		if ( !empty( $parent_terms ) ) :

		//第1ループ
		foreach ( $parent_terms as $pt ) : 
			$pt_id = $pt->term_id;
			$pt_name = $pt->name;
			$term2 = $pt->slug;
			$term4 = $ct->count;
			$ct_url = get_term_link($pt);
			
			
	?>

			<?php /*<h4><a href="https://monodukurisearch.com/work/<?php echo $term2; ?>"><?php echo $pt_name; ?></a><button class="btn_inc btn_set_<?php echo $term2; ?>">▼</button></h4>*/?>
			<h4><a href="<?php echo $ct_url; ?>"><?php echo $pt_name; ?><?php echo $term4; ?></a></h4>
			<?php 
				$child_terms = get_terms( $my_tax, array('hide_empty' => true, 'parent' => $pt_id) );
				$page = get_post( get_the_ID() ); $slug = $page->post_name;
				if ( !empty( $child_terms ) ) :
				echo '<ul class="child flame_'.$term2.'">';

				//第2ループ
				foreach ( $child_terms as $ct ) : 
					$ct_id = $ct->term_id;
					$ct_name = $ct->name;
					$term3 = $ct->slug;
					$term4 = $ct->count;
					$ct_url = get_term_link($pt);
			?>
					<li>
					<a href="<?php echo $ct_url; ?>"><?php echo $ct_name; ?>(<?php echo $term4; ?>)</a>
					</li>
			<?php
				endforeach;  //End : 第２ループ
				echo '</ul>';
				endif;
			?>
			
	<?php
		endforeach;  //End : 第1ループ
		
		endif;
	?>

</section>



<?php
/*

<?php if ( '3-column' !== get_theme_mod( 'businesspress_content_archive' ) ): ?>
	<?php get_sidebar(); ?>
<?php endif; ?>
*/
?>
<?php get_footer(); ?>

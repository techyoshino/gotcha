<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package BusinessPress
 */

get_header(); ?>




<section id="work_col">

	<h3><i class="fa fa-briefcase" aria-hidden="true"></i>エリア一覧</h3>



<?php 

$my_tax = 'seminar-area';  //取得したいタクソノミー名
  $parent_terms = get_terms( $my_tax, array('hide_empty' => false, 'parent' => 0) );  //第一階層のタームだけ取得
  if ( !empty( $parent_terms ) ) :
    echo '<ul>';

    //第1ループ
    foreach ( $parent_terms as $pt ) : 
      $pt_id = $pt->term_id;
      $pt_name = $pt->name;
      $pt_url = get_term_link($pt);
      $term4 = $ct->count;
?>
      <li>
        <a href="<?php echo $pt_url; ?>"><?php echo $pt_name; ?>(<?php echo $term4; ?>)</a>
        <?php 
          $child_terms = get_terms( $my_tax, array('hide_empty' => false, 'parent' => $pt_id) );
          if ( !empty( $child_terms ) ) :
            echo '<ul class="child">';

           //第2ループ
            foreach ( $child_terms as $ct ) : 
              $ct_id = $ct->term_id;
              $ct_name = $ct->name;
              $ct_url = get_term_link($ct);
              $term4 = $ct->count;
        ?>
              <li>
                <a href="<?php echo $ct_url; ?>"><?php echo $ct_name; ?>(<?php echo $term4; ?>)</a>
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

<?php
/*
<ul>
  <?php
  $terms = get_terms('college-area');
  foreach ( $terms as $term ) {
    echo '<li><a href="'.get_term_link($term).'">'.esc_html($term->name).'</a></li>';
  }
  ?>
</ul>
*/
?>

<?php
//taxonomy_tree('college-area');

?>






<?php get_footer(); ?>

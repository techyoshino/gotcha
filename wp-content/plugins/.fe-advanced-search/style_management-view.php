<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	デザイン > 表示
/////////////////////////////////////////////////
?>
<div class="wrap">
	<div id="feas-admin">

		<?php
		/**
	 	* ロゴ, version, サポートリンク 他
	 	*/
		include( 'admin/header.php' );
		?>

		<?php
		/*============================
			フォームの選択プルダウン
		 ============================*/
		$output = '';

		for ( $i = 0; $i <= $get_form_max; $i++ ) {

			$form_name = $selected = $form_no_tmp = '';

			$form_no_tmp = get_option( $feadvns_form_no . $i );
			$form_name   = get_option( $feadvns_search_form_name . $form_no_tmp );
			if ( ! $form_name ) {
				$form_name = '（フォームID = ' . $form_no_tmp . '）';
			}

			if ( $form_id == $form_no_tmp ) {
				$selected = ' selected="selected"';
			}
			$output .= '<option value="' . esc_attr( $i ) . '"' . $selected . '>' . esc_html( $form_name ) . '</option>';
		}
		?>

		<div id="feas-contents-header">
			<h2 id="feas-sectitle">検索フォーム「<?php echo esc_html( get_option( $feadvns_search_form_name . $form_id ) ); ?>（No.<?php echo esc_html( $form_id ); ?>）」に対応するCSSの設定</h2>
			<form action="<?php menu_page_url( 'feas_style_management' ); ?>&noheader=true" method="post">
				<select name="c_style_number">
					<?php echo $output; ?>
				</select>
				<input type="hidden" name="current_form_no" value="<?php echo esc_attr( $form_id ); ?>" />
				<input type="submit" value="実行" class="button-secondary action" />
			</form>
		</div>

		<form action='<?php menu_page_url( 'feas_style_management' ); ?>&noheader=true' method='post'>

			<ul class="tab">
				<li class="active">コード</li>
			</ul>

			<!-- <input type="hidden" name="<?php //echo $feadvns_sort_current_tab; ?>" value="0" /> -->

			<div class="area">
				<ul class="show">

					<label><input type='checkbox' name='use_style' value='use'<?php echo $use_checked; ?>> 下記スタイルシートを使用する</label>
					<br />

					<p>スタイルシート（CSS）</p>
					<div class="pg-design paramTable">
						<textarea id="feas-style-css-content" name='style_body' style='width: 60%; height: 50em;'><?php echo stripslashes( $style_body ); ?></textarea>
					</div>
				</ul>
			</div>

			<input type='submit' value='設定を保存' class='button-primary action'>
			<input type='hidden' name='style_update' value='update'>
			<input type='hidden' name='style_id' value='<?php echo $form_id;?>'>
		</form>

	</div>
</div>

<?php
/*============================
	textareaにコードエディターを当てる
 ============================*/ ?>
<script>
jQuery(document).ready(function($) {
  wp.codeEditor.initialize($('#feas-style-css-content'));
})
</script>

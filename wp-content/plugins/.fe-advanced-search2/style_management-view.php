<?php 
/////////////////////////////////////////////////
//	デザイン > 表示
/////////////////////////////////////////////////
?>
<div class="wrap">
	<div id="feas-admin">
		<div id="feas-head" class="wp-header-end clearfix">
			<div id="feas-head-upper" class="clearfix">
				<h1>FE Advanced Search</h1>
				<a href="https://www.firstelement.co.jp/" id="feas-logo" target="_blank" title="開発会社HPへ移動"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/logo-feas-white-shadow-s@2x-min.png" width="106" height="27"></a>
			</div>
			<div id="feas-head-lower" class="clearfix">
				<div id="feas-version">version 1.8.2</div>
				<div id="feas-support">
					<a href="https://fe-advanced-search.com/manual/" target="_blank" title="使用説明書へ移動">使用説明書</a>
					<a href="https://fe-advanced-search.com/support/" target="_blank" title="フォーラムへ移動">フォーラム </a>
					<a href="https://chatwork.com/feas" target="_blank" title="チャットワークへ移動">チャットワーク</a>
					<a href="https://fe-advanced-search.com/contact/" target="_blank" title="メールフォームへ移動" class="icon icon_mail"></a>
					<a href="https://www.facebook.com/firstelementjp/" target="_blank" title="Facbookページへ移動" class="icon icon_fb"></a>
					<a href="https://twitter.com/feas_wp/" target="_blank" title="Twitterへ移動" class="icon icon_tw"></a>
				</div>
			</div>
		</div>

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
			<label><input type='checkbox' name='use_style' value='use'<?php echo $use_checked; ?>> 下記スタイルシートを使用する</label>
			<br />
		
			<p>スタイルシート（CSS）</p>
			<div class="style-wrap">
				<textarea id="feas-style-css-content" name='style_body' style='width: 60%; height: 50em;'><?php echo stripslashes( $style_body ); ?></textarea>
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
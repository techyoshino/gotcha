<?php 
/////////////////////////////////////////////////
//	キャッシュ設定 > 表示
/////////////////////////////////////////////////
?>
<div class="wrap">
	<div id="feas-admin" class="feas-cache">
		<div id="feas-head" class="wp-header-end clearfix">
			<div id="feas-head-upper" class="clearfix">
				<h1>FE Advanced Search</h1>
				<a href="https://www.firstelement.co.jp/" id="feas-logo" target="_blank" title="開発会社HPへ移動"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/logo-feas-white-shadow-s@2x-min.png" width="106" height="27"></a>
			</div>
			<div id="feas-head-lower" class="clearfix">
				<div id="feas-version">version 1.8.3</div>
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
		
		<div id="feas-contents-header">
			<h2 class="feas-title">キャッシュの設定</h2>
		</div>		
		
		<form action="<?php menu_page_url( 'feas_cache_management' ); ?>&noheader=true" method="post">
			<div class="param-table clearfix">
				<table class="widefat">
					<tr>
						<th>
							キャッシュを使用する（推奨）
						</th>
						<td>
							<input type="checkbox" name="feas_cache_enable" value="enable" <?php if ( $cache_flag == 'enable') { echo 'checked=checked'; } ?>>
						</td>
					</tr>
					<tr>
						<th>
							キャッシュ有効期限
						</th>
						<td>
							<p class="sub">0秒に設定すると永久にキャッシュし続けます</p>
							<input type="text" name="feas_cache_time" value="<?php if ( $cache_time !== null ) { echo esc_attr( $cache_time ); } ?>">秒
						</td>
					</tr>
				</table>
			</div>
			<input type="submit" class="button-primary action" name="feas_cache_page" value="設定を保存">
		</form>
		
		
		<h2 class="feas-title">キャッシュの削除</h2>
		<form action="<?php menu_page_url( 'feas_cache_management' ); ?>&noheader=true" method="post">
			<input type="submit" class="button-primary action" name="feas_cache_cache" value="全てのキャッシュを削除する">
		</form>
		
<!--
		<h3>キャッシュ状況</h3>
		<div>
			<?php /*
 
			if ( $get_transient_list != null ) {
				echo '<p>現在<ul>';
				foreach ( $get_transient_list as $key ) {
					
					echo '<li>ID.'.$key['id'].'：'.db_op_get_value( $feadvns_search_form_name . $key['id'] ).'</li>';
				}
				echo '</ul>がキャッシュされています</p>';
			} else {
				echo '<p>現在キャッシュされているフォームは有りません</p>';
			}
			
*/ ?>
		</div>
-->
	</div>
</div>
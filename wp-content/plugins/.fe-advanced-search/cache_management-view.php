<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	キャッシュ設定 > 表示
/////////////////////////////////////////////////
?>
<div class="wrap">
	<div id="feas-admin" class="feas-cache">

		<?php
		/**
	 	* ロゴ, version, サポートリンク 他
	 	*/
		include( 'admin/header.php' );
		?>

		<div id="feas-contents-header">
			<h2 class="feas-title">キャッシュの設定</h2>
		</div>

		<form action="<?php menu_page_url( 'feas_cache_management' ); ?>&noheader=true" method="post">

			<ul class="tab">
				<li class="active">設定</li>
			</ul>

			<!-- <input type="hidden" name="<?php //echo $feadvns_sort_current_tab; ?>" value="0" /> -->

			<div class="area">
				<ul class="show">

					<div id="genetalSettings" class="pg-cache paramTable">
						<div class="th th1-1">
							キャッシュを使用する（推奨）
						</div>
						<div class="td td1-1">
							<input type="checkbox" name="feas_cache_enable" value="enable" <?php if ( $cache_flag == 'enable') { echo 'checked=checked'; } ?>>
						</div>
						<div class="th th2-1">
							キャッシュ有効期限
						</div>
						<div class="td td2-1">
							<p class="sub">0秒に設定すると永久にキャッシュし続けます</p>
							<div class="inputCache"><input type="text" name="feas_cache_time" value="<?php if ( $cache_time !== null ) { echo esc_attr( $cache_time ); } ?>">秒</div>
						</div>
					</div>
				</ul>
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

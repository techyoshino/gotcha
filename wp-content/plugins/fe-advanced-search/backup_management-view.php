<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	管理 > 表示部
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
			エクスポート
		 ============================*/
		?>
		<div id="feas-contents-header">
			<h2 id="feas-sectitle">設定データのエクスポート</h2>
		</div>

		<form action="<?php echo plugins_url( 'fe-advanced-search' ); ?>/export.php" method="post">
			<input type="hidden" name="file" value="export">
			<?php wp_nonce_field( 'feas-nonce-key', 'feas-backup' ); ?>
			<input type="submit" value="ダウンロード" class="button-primary action" />
		</form>

		<?php
		/*============================
			インポート
		 ============================*/

	    $bytes = wp_max_upload_size();
	    $size  = size_format( $bytes );
		?>

		<h2>設定データのインポート</h2>

		<form enctype="multipart/form-data" id="import-upload-form" method="post" class="wp-upload-form" action="<?php echo menu_page_url( 'feas_backup_management', false ); ?>&noheader=true">
			<p><label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label></p>
			<p> (<?php printf( __('Maximum size: %s' ), $size ); ?>)<input type="file" id="upload" name="import" size="25" /></p>
			<p><input type="submit" value="ファイルをアップロードしてインポート" class="button-primary action" /></p>
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
			<?php wp_nonce_field( 'feas-nonce-key', 'feas-import-upload' ); ?>
		</form>

	</div>
</div>

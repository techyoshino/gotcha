<?php
/////////////////////////////////////////////////
//	管理 > 表示部
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

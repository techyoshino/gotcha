<?php
/////////////////////////////////////////////////
//	設定データをエクスポート
/////////////////////////////////////////////////

// view側のformのactionから当ファイルを直接呼び出すため、WP関連をセットアップ
require_once( dirname(__FILE__) . '/../../../wp-load.php' );
require_once( dirname(__FILE__) . '/../../../wp-admin/includes/plugin.php' );

global $wpdb;

// エクスポート
if ( array_key_exists( 'file', $_POST ) && isset( $_POST['file'] ) && "export" == $_POST['file'] ) {

	if ( check_admin_referer( 'feas-nonce-key', 'feas-backup' ) ) {
					
		$e = new WP_Error();
			
		// ファイルの保存場所を設定
		$filename = "feas_setting_data_" . date( "YmdHis" ) . ".csv";
		$filepath = plugin_dir_path( __FILE__ )  . $filename;
		
		header( 'Content-Type:application/octet-stream' );
		header( 'Content-Disposition:filename='.$filename );
										
		// FEAS関連を抽出
		$sql  = " SELECT * FROM {$wpdb->options} AS op ";
		$sql .= " WHERE op.option_name LIKE 'feadvns_%' ";
		$sql .= " OR ( op.option_name LIKE 'feas_%' ";
		$sql .= " AND op.option_name NOT LIKE '%_user_roles' ) ";
		
		$results  = $wpdb->get_results( $sql, ARRAY_A );
							
		if ( ! empty( $results ) ) {
						
			$fp = fopen( $filepath, 'w' );
					
			// 配列をカンマ区切りにしてファイルに書き込み
			foreach ( $results as $result ) {
				mb_convert_variables( 'SJIS-win', 'UTF-8', $result );
				fputcsv( $fp, $result );
			}
			fclose( $fp );
						
			readfile( $filepath );
			unlink( $filepath );
			
		} else {
		
			$e->add( 'error', 'FE Advanced Searchに関連するデータはありません' );
			set_transient( 'feas_import_settings_data', $e->get_error_messages(), 10 );
		
		}
				
		wp_safe_redirect( menu_page_url( 'feas_backup_management', false ) );
	}
}

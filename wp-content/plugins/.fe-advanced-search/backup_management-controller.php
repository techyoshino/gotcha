<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	管理
/////////////////////////////////////////////////

if ( isset( $_POST['action'] ) && "save" == $_POST['action'] ) {

	// インポート
	feas_import_settings_data();
}

/*============================
	設定データをインポート
 ============================*/
function feas_import_settings_data() {

	if ( check_admin_referer( 'feas-nonce-key', 'feas-import-upload' ) ) {

		$e = new WP_Error();

		if ( UPLOAD_ERR_OK == $_FILES['import']['error'] ) {

			$filename = '';

			// CSV形式のみ
			$mimes = array( 'application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv' );

			if ( in_array( $_FILES['import']['type'], $mimes ) ) {

				$tempfile = $_FILES['import']['tmp_name'];
				$filename = $_FILES['import']['name'];
				//$filename = mb_convert_encoding( $filename, 'UTF-8', 'SJIS-win' );

				// 一時ファイルから移動・保存
				$result = move_uploaded_file( $tempfile, $filename );

				if ( false == $result ) {

					$e->add( 'error', 'ファイルの移動に失敗しました' );
					set_transient( 'feas_message', $e->get_error_messages(), 10 );
					set_transient( 'feas_message_notice_flag', 'error', 10 );
				}

			} else {

				$e->add( 'error', 'CSV形式のデータではありません' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'error', 10 );
			}

		} else if ( UPLOAD_ERR_NO_FILE == $_FILES['import']['error'] ) {

			$e->add( 'error', 'ファイルがアップロードされませんでした' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'error', 10 );

		} else {

			$e->add( 'error', 'ファイルのアップロードに失敗しました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'error', 10 );
		}

		if ( ! empty( $filename ) ) {

			// DB上の既存データ削除
			global $wpdb;
			$table_name = $wpdb->prefix . "options";
			$wpdb->query( "
	        DELETE
	        FROM $table_name
	        WHERE `option_name` LIKE 'feadvns_%'
	        " );
	        $wpdb->query( "
	        DELETE
	        FROM $table_name
	        WHERE `option_name` LIKE 'feas_%'
	        " );

	        // キャッシュを削除
	        feas_delete_transient_all();

			$records = array();

			$handle = fopen( $filename, 'r' );
			if ( $handle ) {

				// 配列をカンマ区切りにしてファイルに書き込み
				while ( ( $data = fgetcsv( $handle, 1000, ',', '"' ) ) !== FALSE ) {
					mb_convert_variables( 'UTF-8', 'SJIS-win', $data );
					$records[] = $data;
				}
				fclose( $handle );
			}

			// オプション情報のキャッシュを削除
			wp_cache_delete( 'alloptions', 'options' );

			if ( $records ) {
				$status = null;
				$i = 0;
				foreach ( $records as $record ) {

					$name = ( ! empty( $record[1] ) ) ? $record[1] : false;
					if ( false === $name )
						continue;
					$value    = maybe_unserialize( $record[2] );
					$autoload = $record[3];

					// 行ごとにオプションテーブルに格納
					$status = add_option( $name, $value, $autoload );
					if ( true == $status ) {
						$i++;
					}
				}
			}

			if ( 0 < $i ) {
				$e->add( 'ok', 'ファイルのインポートに成功しました' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'updated', 10 );
			} else {
				$e->add( 'ok', 'データに変更はありません' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'updated', 10 );
			}
		}

		wp_safe_redirect( menu_page_url( 'feas_backup_management', false ) );
	}
}

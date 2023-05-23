<?php  
/////////////////////////////////////////////////
//	キャッシュ設定
/////////////////////////////////////////////////
global $feas_cache_enable, $feas_cache_time;

if ( isset( $_POST['feas_cache_page'] ) || isset( $_POST['feas_cache_time'] ) ) {
	
	// メッセージ初期化
	$e = new WP_Error();
	$save_status = false;
	wp_cache_delete( 'feas_message', 'options' );
		
	if ( isset( $_POST['feas_cache_page'] ) ) {
		
		if ( isset( $_POST['feas_cache_enable'] ) ) { 
			if ( 'enable' == $_POST['feas_cache_enable'] ) {
				$save_status = update_option( $feas_cache_enable, 'enable' );
				
				// キャッシュ有効期限の初期値を設定
				$cache_time = get_option( $feas_cache_time );
				if ( false === $cache_time ) {
					update_option( $feas_cache_time, '0' );
				}
			}
		} else {
			delete_option( $feas_cache_enable );
			$save_status = -1;
		}
		
		if ( true === $save_status ) {
			$e->add( 'ok', 'キャッシュが有効化されました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		} 
		elseif ( -1 === $save_status ) {
			$e->add( 'ok', 'キャッシュが無効になりました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );			
		} 
		else {
			$e->add( 'ok', 'キャッシュの設定に変更はありません' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		}
	}
	
	if ( isset( $_POST['feas_cache_time'] ) && '' != $_POST['feas_cache_time'] ) {
		
		if ( '' !== $_POST['feas_cache_time'] && ctype_digit( $_POST['feas_cache_time'] ) ) {	
			$save_status = update_option( $feas_cache_time, intval( $_POST['feas_cache_time'] ) );
		} else {
			delete_option( $feas_cache_time );
			$save_status = -1;
		}
		
		if ( $save_status ) {
			$e->add( 'ok', '有効期限が変更されました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		}
		elseif ( -1 == $save_status ) {
			$e->add( 'ok', '有効期限が消去されました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );			
		} 
		else {
			$e->add( 'ok', '有効期限に変更はありません' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		}
	}

	wp_safe_redirect( menu_page_url( 'feas_cache_management', false ) );
}

if ( isset( $_POST['feas_cache_cache'] ) ) {
	
	// メッセージ初期化
	$e = new WP_Error();
	$save_status = '';
	wp_cache_delete( 'feas_message', 'options' );
	
	if ( '全てのキャッシュを削除する' == $_POST['feas_cache_cache'] ) {
		$save_status = feas_delete_transient_all();
	}
	
	if ( '' != $save_status ) {
		$e->add( 'ok', 'キャッシュを削除しました' );
		set_transient( 'feas_message', $e->get_error_messages(), 10 );
		set_transient( 'feas_message_notice_flag', 'updated', 10 );
	} else {
		$e->add( 'ok', 'キャッシュは削除されませんでした' );
		set_transient( 'feas_message', $e->get_error_messages(), 10 );
		set_transient( 'feas_message_notice_flag', 'error', 10 );
	}
	
	wp_safe_redirect( menu_page_url( 'feas_cache_management', false ) );
}

//model

/*function feas_get_transient_list(){ //キャッシュされてる一覧
	global $cols,$manag_no,$feadvns_max_page;
	
	$return = array();
	$get_form_max = db_op_get_value( $feadvns_max_page );
	$get_form_max++;
	for($i = 0 ; $i <= $get_form_max ; $i++){
		if ($get = get_transient($cols[23].$i)){
			
			$return[] = array( 'id' => $i , 'val' => $get);
		}
	}
	return $return;
}

$get_transient_list = feas_get_transient_list();*/

$cache_flag = get_option( $feas_cache_enable );
$cache_time = get_option( $feas_cache_time );

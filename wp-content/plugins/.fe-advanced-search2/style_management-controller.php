<?php 
/////////////////////////////////////////////////
//	デザイン
/////////////////////////////////////////////////

// 初期のスタイル取得
require_once( dirname( __FILE__ ) . '/default_style.php' );

// 使用する場合はcheckedをつける
$use_checked = null;

/*============================
	保存
 ============================*/
if ( isset( $_POST['style_update'] ) ) {
	if ( 'update' == $_POST['style_update'] ) {
	
		$e = new WP_Error();
		$status = null;
		$i = 0;
	
		$use_value = 0;
		
		/**
		 *	styleを使用するかのcheck
		 */
		if ( isset( $_POST['use_style'] ) ) {
			$use_value = 1;
		}
		
		/**
		 *	styleのid
		 */			
		$form_id = intval( $_POST['style_id'] );
		
		$status = update_option( $use_style_key . $form_id, $use_value );
		if ( true == $status ) {
			$i++;
		}	

		/**
		 *	styleを取得
		 */
		if ( isset( $_POST['style_body'] ) ) {
			
			//$digest = md5( $_POST['style_body'] );
			$status = update_option( $style_body_key . $form_id, $_POST['style_body'] );
			if ( true == $status ) {
				$i++;
			}
		}		
		
		$_POST['c_form_number'] = $form_id;
		
		/**
		 *	メッセージ生成
		 */
		if ( 0 < $i ) {
			$e->add( 'ok', '設定が保存されました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		} else {
			$e->add( 'ok', 'データに変更はありません' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		}
		
		wp_safe_redirect( menu_page_url( 'feas_style_management', false ) );
	}
}

/*=================================
	実行
 =================================*/	
if ( isset( $_POST['c_style_number'] ) ) {
	
	$e = new WP_Error();
	$status = null;
	
	/**
	 *	form_idをセット
	 */
	$form_no = get_option( $feadvns_form_no . $_POST['c_style_number'] );
	if ( false === $form_no ) {
		$form_no = get_option( $feadvns_autoinc_no );
	}
	$form_id = $form_no;
	
	$status = update_option( $feadvns_current_form, $form_id, false );
		
	if ( false !== $status ) {
		$e->add( 'ok', 'フォームを切り替えました' );
		set_transient( 'feas_message', $e->get_error_messages(), 10 );
		set_transient( 'feas_message_notice_flag', 'updated', 10 );
	} else {
		$e->add( 'ok', 'フォームは同じです' );
		set_transient( 'feas_message', $e->get_error_messages(), 10 );
		set_transient( 'feas_message_notice_flag', 'updated', 10 );
	}
	
	wp_safe_redirect( menu_page_url( 'feas_style_management', false ) );
}

/*=================================
	ページ読み込み時の初期設定
 =================================*/	
 
/**
 *	作成済みフォームの数の初期値を保存
 */	
$get_form_max = get_option( $feadvns_max_page );
if ( false === $get_form_max ) {
	// 初めてのフォームの場合、現在のフォーム数 = 0を保存
	update_option( $feadvns_max_page, '0' );
	$get_form_max = 0;
}

/**
 *	ページ読み込み時のmanag_no設定
 */
$form_no = get_option( $feadvns_current_form );
if ( false === $form_no ) {
	update_option( $feadvns_current_form, '0' );
	$form_no = 0;
}
$form_id = $form_no;

/*
if ( ! isset( $_POST['style_id'] ) ) {
	$form_id = $manag_no;
}
*/

/**
 *	使用する・しない
 */
if ( 1 == get_option( $use_style_key . $form_id ) ) {
	$use_checked = ' checked="checked"';
}

/**
 *	CSS読み込み
 */
$get_style_body = get_option( $style_body_key . $form_id );

if ( false !== $get_style_body ) {
	$style_body = $get_style_body;
} else {
	$style_body = feas_default_style( $form_id );
}

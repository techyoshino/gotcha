<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	ソート
/////////////////////////////////////////////////

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

		// 一行目の検索フォーム - 初期値
		update_option( $cols[0] . '0_0', '0' );
		update_option( $cols[1] . '0_0', '0' );
		update_option( $cols[2] . '0_0', 'archive' );
		update_option( $cols[3] . '0_0', '' );
		update_option( $cols[4] . '0_0', '1' );
		update_option( $cols[5] . '0_0', '8' );
		update_option( $cols[6] . '0_0', '0' );
		update_option( $cols[7] . '0_0', '' );
		update_option( $cols[8] . '0_0', '' );
		update_option( $cols[9] . '0_0', '' );
		update_option( $cols[10] . '0_0', '-1' );
		update_option( $cols[11] . '0_0', '' );
		//update_option( $cols[12] . '0_0', '0' );
		update_option( $cols[13] . '0_0', 'post_title,post_content' );
		update_option( $cols[14] . '0_0', '' );
		update_option( $cols[15] . '0_0', 'no' );
		update_option( $cols[16] . '0_0', '0' );
		update_option( $cols[17] . '0_0', '' );
		update_option( $cols[18] . '0_0', 'yes' );
		update_option( $cols[19] . '0_0', '' );
		update_option( $cols[20] . '0_0', '' );
		//update_option( $cols[21] . '0_0', '0' );
		update_option( $cols[22] . '0_0', '' );
		//update_option( $cols[23] . '0_0', '0' );
		update_option( $cols[24] . '0_0', '' );
		update_option( $cols[25] . '0_0', '' );
		update_option( $cols[26] . '0_0', '1' );
		update_option( $cols[27] . '0_0', '---未指定---' );
		//update_option( $cols[28] . '0_0', '0' );
		update_option( $cols[29] . '0_0', 'int' );
		update_option( $cols[30] . '0_0', 'キーワードを入力' );

		// 一行目のフォーム - 初期値
		update_option( $cols_order[0] . '0_0', 'post_date' );
		update_option( $cols_order[1] . '0_0', '0' );
		update_option( $cols_order[2] . '0_0', '0' );
		update_option( $cols_order[3] . '0_0', '' );
		update_option( $cols_order[4] . '0_0', '' );
		update_option( $cols_order[5] . '0_0', '' );
		update_option( $cols_order[6] . '0_0', '' );
		update_option( $cols_order[7] . '0_0', '▲' );
		update_option( $cols_order[8] . '0_0', '▼' );
		update_option( $cols_order[9] . '0_0', '' );
		update_option( $cols_order[10] . '0_0', 'int' );
	}

	/**
	 *	ページ読み込み時のmanag_order_no設定
	 */
	$form_no = get_option( $feadvns_current_form );
	if ( false === $form_no ) {
		update_option( $feadvns_current_form, '0' );
		$form_no = 0;
	}
	$manag_order_no = $form_no;

	/**
	 *	何行表示するか(初期値)
	 */
	$line_cnt = 1;

	for ( $i = 0; $i <= $get_form_max; $i++ ) {

		$form_no = get_option( $feadvns_form_no . $i );

		// 初めてmax_lineを保存する場合
		$max_line = get_option( $feadvns_max_line_order . $form_no );
		if ( false === $max_line ) {
			wp_cache_delete( $feadvns_max_line_order . $form_no, 'options' );
			update_option( $feadvns_max_line_order . $form_no, '1' );
		}
	}
	$line_cnt = get_option( $feadvns_max_line_order . $manag_order_no );

	/**
	 *	ページ読み込み時
	 */
/*
	$current_tab = get_option( $feadvns_sort_current_tab );
	if ( false === $current_tab ) {
		update_option( $feadvns_sort_current_tab, '0' );
		$_POST[$feadvns_sort_current_tab] = 0;
	} else {
		$_POST[$feadvns_sort_current_tab] = $current_tab;
	}
*/

	/**
	 *	設定値を読み込む
	 */
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		for ( $i_cols = 0, $cnt_cols = count( $cols_order ); $i_cols < $cnt_cols; $i_cols++ ) {
			if ( ! isset( $_POST[$cols_order[$i_cols] . $manag_order_no . "_" . $i] ) ) {
				//wp_cache_delete( $cols_order[$i_cols] . $manag_order_no . "_" . $i, 'options' );
				$_POST[$cols_order[$i_cols] . $manag_order_no . "_" . $i] = get_option( $cols_order[$i_cols] . $manag_order_no . "_" . $i );
			}
		}
	}

	/*============================
		「実行」ボタン
	 ============================*/
	if ( isset( $_POST['c_order_number'] ) ) {

		// メッセージ初期化
		$e = new WP_Error();
		$status = null;
		wp_cache_delete( 'feas_message', 'options' );

		/**
		 *	manag_noをセット
		 */
		$form_no = get_option( $feadvns_form_no . $_POST['c_order_number'] );
		if ( false === $form_no ) {
			$form_no = get_option( $feadvns_autoinc_no );
		}
		$manag_order_no = $form_no;

		/**
		 *	現在表示中のフォームを設定
		 */
		$status = update_option( $feadvns_current_form, $manag_order_no );

		/**
		 *	行数
		 */
		$line_cnt = get_option( $feadvns_max_line_order . $manag_order_no );

		if ( false !== $status ) {
			$e->add( 'ok', 'フォームを切り替えました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		} else {
			$e->add( 'ok', 'フォームは同じです' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'error', 10 );
		}

		wp_safe_redirect( menu_page_url( 'feas_sort_management', false ) );
	}


	/*============================
		項目を追加
	 ============================*/
	if ( isset( $_POST['line_action'] ) ) {

		$save_line_number = null;

		if ( 'add_line' == $_POST['line_action'] ) {

			// 対象ページに留まるためにセット
			$manag_order_no = $_POST['current_order_no'];
			$max_line = get_option( $feadvns_max_line_order . $manag_order_no );

			// 一個ラインを増やす
			$save_line_number = $max_line + 1;

			update_option( $feadvns_max_line_order . $manag_order_no, $save_line_number );

			$line_cnt = $save_line_number;
		}
	}


	/*============================
		「設定を保存」ボタン
	 ============================*/
	if ( isset( $_POST['ac'] ) == true && $_POST['ac'] == "update" ) {

		/**
		 *	消去チェック
		 */
		$check_del = '';
		for ( $i = 0; $i < $line_cnt; $i++ ) {
			if ( isset( $_POST[$cols_order[3] . $manag_order_no . "_" . $i] ) && $_POST[$cols_order[3] . $manag_order_no . "_" . $i] == "del" ) {
				$check_del = "check"; // 1つでも消去があれば次の工程へ
			}
		}

		/**
		 *	消去にチェックされていたら処理
		 */
		if ( '' != $check_del ) {
			$line_cnt = check_del_line_order( $manag_order_no, $line_cnt );
		}

		/**
		 *	DB検索キーを格納
		 */
		$db_search_key_sort = array();

		for ( $i_line = 0; $i_line < $line_cnt; $i_line++ ) {
			if ( isset( $_POST["feadvns_order_sort_" . $manag_order_no . "_" . $i_line] ) ) {
				$db_search_key_sort[$i_line]['order'] = $_POST["feadvns_order_sort_" . $manag_order_no . "_" . $i_line];
			} else {
				$db_search_key_sort[$i_line]['order'] = null;
			}
			$db_search_key_sort[$i_line]['line'] = $i_line;
		}

		/**
		 *	並び順チェック
		 */
		$order_date = array();
		foreach ( $db_search_key_sort as $v ) {
			$order_date[] = $v['order'];
		}
		array_multisort( $order_date, SORT_ASC, $db_search_key_sort );

		/**
		 *	$_POSTに代入する値を作成
		 */
		$sort_data = array();
		for ( $i_p = 0, $cnt_p = count( $db_search_key_sort ); $i_p < $cnt_p; $i_p++ ) {
			// col
			for ( $i_cols = 0, $cnt_cols = count( $cols_order ); $i_cols < $cnt_cols; $i_cols++ ) {
				if ( isset( $_POST[$cols_order[$i_cols] . $manag_order_no . "_" . $db_search_key_sort[$i_p]['line']] ) ) {
					$sort_data[$cols_order[$i_cols] . $manag_order_no . "_" . $i_p] = $_POST[$cols_order[$i_cols] . $manag_order_no . "_" . $db_search_key_sort[$i_p]['line']];
				}
			}
		}

		/**
		 *	$_POSTへ代入
		 */
		$s_keys = array_keys( $sort_data );

		for ( $i_cp = 0, $cnt_cp = count( $s_keys ); $i_cp < $cnt_cp; $i_cp++ ) {
			$_POST[$s_keys[$i_cp]] = $sort_data[$s_keys[$i_cp]];
		}

		$db_search_key = array();

		for ( $i_line = 0; $i_line < $line_cnt; $i_line++ ) {
			// col
			for ( $i_cols = 0, $cnt_cols = count( $cols_order ); $i_cols < $cnt_cols; $i_cols++ ) {
				// OPのキー作成
				$s_key = $cols_order[$i_cols] . $manag_order_no . "_" . $i_line;

				// DB検索キーを格納
				$db_search_key[] = $s_key;
			}
		}

		/**
		 *	DB保存
		 */

		// メッセージ初期化
		$e = new WP_Error();
		$save_status = 0;
		$i = 0;
		wp_cache_delete( 'feas_message', 'options' );

		for ( $i_s_key = 0, $cnt_s_key = count( $db_search_key ); $i_s_key < $cnt_s_key; $i_s_key++ ) {
			if ( isset( $_POST[$db_search_key[$i_s_key]] ) ) {

				//wp_cache_delete( $db_search_key[$i_s_key], 'options' );
				//$save_status_del = delete_option( $db_search_key[$i_s_key] );
				//$save_status = add_option( $db_search_key[$i_s_key], $_POST[$db_search_key[$i_s_key]] );

				if ( null === db_op_get_value( $db_search_key[$i_s_key] ) ) {
					$save_status = db_op_insert_value( $db_search_key[$i_s_key], $_POST[$db_search_key[$i_s_key]] );
				} else {
					$save_status = db_op_update_value( $db_search_key[$i_s_key], $_POST[$db_search_key[$i_s_key]] );
				}

				if ( 0 != $save_status ) {
					$i++;
				}
			}
		}

		/**
		 *	保存後に現在のタブに留まるため
		 */
		if ( isset( $_POST[$feadvns_sort_current_tab] ) ) {

			$tab_index = $_POST[$feadvns_sort_current_tab];

			$status = update_option( $feadvns_sort_current_tab , $tab_index );
			if ( true == $save_status ) {
				$i++;
			}

		} else {

			$status = delete_option( $feadvns_sort_current_tab );
			if ( 1 == $save_status ) {
				$i++;
			}
		}

		if ( 0 < $i ) {
			$e->add( 'ok', '設定が保存されました' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		} else {
			$e->add( 'ok', 'データに変更はありません' );
			set_transient( 'feas_message', $e->get_error_messages(), 10 );
			set_transient( 'feas_message_notice_flag', 'updated', 10 );
		}

		// 現在のフォームに留まるためにmanag_noをセット
		$manag_order_no = $_POST['current_order_no'];

		// 行カウント
		$line_cnt = get_option( $feadvns_max_line_order . $manag_order_no );

		wp_safe_redirect( menu_page_url( 'feas_sort_management', false ) );

	} // 「設定を保存」ボタン

	/*************************************************************************
	 *
	 *
	 *	保存後の表示項目の準備
	 *
	 *
	 *************************************************************************/

	/**
	 *	設定値を読み込み
	 */
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		for ( $i_cols = 0, $cnt_cols = count( $cols_order ); $i_cols < $cnt_cols; $i_cols++ ) {
			if ( ! isset( $_POST[$cols_order[$i_cols] . $manag_order_no . "_" . $i] ) ) {
				$_POST[$cols_order[$i_cols] . $manag_order_no . "_" . $i] = maybe_unserialize( get_option( $cols_order[$i_cols] . $manag_order_no . "_" . $i ) );
			}
		}
	}

	/**
	 *	プレビュー｜デザインのCSSを適用
	 */
	if ( ! isset( $_POST[$pv_css . $manag_order_no] ) ) {

		$pv_css_sw = get_option( $pv_css . $manag_order_no );

		$_POST[$pv_css . $manag_order_no] = $pv_css_sw;
	}

	/**
	 *	保存する直前に選択されていたタブを開く
	 */
	if ( ! isset( $_POST[$feadvns_sort_current_tab] ) ) {
		$_POST[$feadvns_sort_current_tab] = get_option( $feadvns_sort_current_tab );
	}

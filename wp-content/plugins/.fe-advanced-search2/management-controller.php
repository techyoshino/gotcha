<?php
/////////////////////////////////////////////////
//	検索
/////////////////////////////////////////////////


	/*************************************************************************
	 *
	 *
	 *	ページ読み込み時の初期設定
	 *
	 *
	 *************************************************************************/

	// 未使用の設定項目
/*
	unset( $cols[12] );
	unset( $cols[21] );
	unset( $cols[23] );
*/
			 
	/**
	 *	作成済みフォームの数の初期値を保存
	 */	
	$get_form_max = get_option( $feadvns_max_page );
	if ( false === $get_form_max ) {
		
		// 初めてのフォームの場合、現在のフォーム数 = 0を保存
		update_option( $feadvns_max_page, '0' );
		$get_form_max = 0;
		
		// 検索対象の投稿タイプ - 初期値
		update_option( $feadvns_search_target . 0, 'post' );
		
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
		
		// ソートボタン - 初期値
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
	 *	フォームID初期値を保存
	 */
	for ( $i = 0; $i <= $get_form_max; $i++ ) {

		// 初めてfeadvns_form_noを保存する場合
		$form_no = get_option( $feadvns_form_no . $i );
		if ( false === $form_no ) {
			$autoinc_no = get_option( $feadvns_autoinc_no );		
			if ( $autoinc_no ) {
				// 初めて値を保存する場合、作成済みフォーム数を保存
				$autoinc_no = ( $autoinc_no + 1 ); // 次の値
				update_option( $feadvns_form_no . $i, $autoinc_no );
				update_option( $feadvns_autoinc_no, $autoinc_no );	
			} else {
				update_option( $feadvns_form_no . $i, $i );
				update_option( $feadvns_autoinc_no, $i );
			}
		}
	}

	/**
	 *	累積フォーム生成数の初期値を保存
	 */		
	$autoinc_no = get_option( $feadvns_autoinc_no );
	if ( false === $autoinc_no ) {
		if ( 0 < $get_form_max ) {
			// 初めて値を保存する場合、作成済みフォーム数を保存
			update_option( $feadvns_autoinc_no, $get_form_max );
		}
	}

	/**
	 *	ページ読み込み時のmanag_no設定
	 */
	$form_no = get_option( $feadvns_current_form );
	if ( false === $form_no ) {
		update_option( $feadvns_current_form, '0' );
		$form_no = 0;
	}
	$manag_no = $form_no;

	/**
	 *	行カウント(初期値)
	 */
	$line_cnt = 1;

	for ( $i = 0; $i <= $get_form_max; $i++ ) {

		$form_no = get_option( $feadvns_form_no . $i );
		
		// 初めてmax_lineを保存する場合
		$max_line = get_option( $feadvns_max_line . $form_no );
		if ( false === $max_line ) {
			update_option( $feadvns_max_line . $form_no, '1' );
		}
	}
	
	$line_cnt = get_option( $feadvns_max_line . $manag_no );
		
	/**
	 *	カテゴリー取得
	 */
	//$sql  =" SELECT " .$wpdb->term_taxonomy .".term_id, name , parent FROM " .$wpdb->terms;
	$sql  = " SELECT * FROM " . $wpdb->terms;
	$sql .= " LEFT JOIN " . $wpdb->term_taxonomy . " ON " . $wpdb->terms . ".term_id = " . $wpdb->term_taxonomy . ".term_id";
	$sql .= " WHERE " . $wpdb->term_taxonomy . ".taxonomy='category'";
	$get_cats = $wpdb->get_results( $sql );
	
	/**
	 *	カスタムタクソノミー取得
	 */
	global $wp_version;
	
	if ( $wp_version >= '3.0' ) {
		$args = array(
			'public'   => true,
			'_builtin'  => false
		);
		$taxonomies = get_taxonomies( $args ,'objects' );
		
		$cnt = 0;
		foreach ( $taxonomies as $taxonomy ) {
	
			$get_terms[$cnt]['label'] = $taxonomy->label;
			$get_terms[$cnt]['name'] = $taxonomy->name;
	
			$termlist = get_terms( $taxonomy->name, array( 'hide_empty' => 0, 'get' => 'all' ) );
	
			$cnt++;
		}
	}
	
	/**
	 *	ソート
	 */
	$sort_cat[0] = new StdClass();
	$sort_cat[0]->term_id = "par_cat";
	$sort_cat[0]->name = "トップカテゴリ（ID = 0）";
	for ( $i_sort = 0, $cnt_sort =count( $get_cats); $i_sort < $cnt_sort; $i_sort++ ) {
		$max_cat = count( $sort_cat );

		if ( $get_cats[$i_sort]->parent == 0 ) {
			$sort_cat[$max_cat] = new StdClass();
			$sort_cat[$max_cat]->term_id = $get_cats[$i_sort]->term_id;
			$sort_cat[$max_cat]->name = $get_cats[$i_sort]->name;
		}
	}
	$get_cats = $sort_cat;

	/**
	 *	postmetaを取得
	 */
	
	// 公開された投稿タイプの名称を取得
	$args = array(
		'public'   => true,
	);
	$output = 'names';
	$operator = 'and';
	
	$post_types = get_post_types( $args, $output, $operator ); 
	
	$pt = '';
	foreach ( $post_types  as $post_type ) {
		if ( ! empty( $pt ) )
			$pt .= ',';
		$pt .= "'$post_type'";
	}
	
	$sql  = " SELECT DISTINCT meta_key FROM {$wpdb->postmeta} as pm";
	$sql .= " LEFT JOIN {$wpdb->posts} as p ON pm.post_id = p.ID";
	$sql .= " WHERE meta_key NOT LIKE '\_%'";  // WPが自動生成するアンダースコアから始まるpostmetaを除外
	$sql .= " AND p.post_type IN ({$pt})"; // 開された投稿タイプに紐付いたpost_metaのみ取得
	$sql .= " AND p.post_status = 'publish'";
	$get_metas = $wpdb->get_results( $sql );
	

	/*************************************************************************
	 *
	 *
	 *	「項目を追加」で行を増やす
	 *
	 *
	 *************************************************************************/
	 
	if ( isset( $_POST['line_action'] ) ) {
		
		$save_line_number = null;

		if ( 'add_line' == $_POST['line_action'] ) {
			
			// 対象ページに留まるためにセット
			$manag_no = $_POST['current_form_no'];
			$max_line = get_option( $feadvns_max_line . $manag_no );
			
			// 一個ラインを増やす
			$save_line_number = $max_line + 1;

			update_option( $feadvns_max_line . $manag_no, $save_line_number );

			$line_cnt = $save_line_number;
		}
	}
	
	/*************************************************************************
	 *
	 *
	 *	「設定を保存」ボタン
	 *
	 *
	 *************************************************************************/
	if ( isset( $_POST['ac'] ) == true && $_POST['ac'] == "update" ) {

		/*=================================
			フォーム全体の設定
		 =================================*/
		 
		$e = new WP_Error();
		$i = 0;

		// オプション情報のキャッシュを削除
		//wp_cache_delete( 'alloptions', 'options' );
					
		/**
		 *	フォームの名称登録
		 */
		if ( isset( $_POST[$feadvns_search_form_name . $manag_no] ) ) {
			
			$form_name = $_POST[$feadvns_search_form_name . $manag_no];
			
			$status = update_option( $feadvns_search_form_name . $manag_no, $form_name );
			if ( true == $status ) {
				$i++;
			}
		} else {
			
			$status = delete_option( $feadvns_search_form_name . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索対象のpost_typeを設定
		 */
		if ( isset( $_POST[$feadvns_search_target . $manag_no] ) ) {
			
			$ptvalue = $_POST[$feadvns_search_target . $manag_no];
			
			$status = update_option( $feadvns_search_target . $manag_no, $ptvalue );
			if ( true == $status ) {
				$i++;
			}
		}
		
		/**
		 *	固定タクソノミ/ターム
		 */
		if ( isset( $_POST[$feadvns_default_cat . $manag_no] ) ) {
			
			$default_cat = $_POST[$feadvns_default_cat . $manag_no];
			
			$status = update_option( $feadvns_default_cat . $manag_no, $default_cat );
			if ( true == $status ) {
				$i++;
			}
		} else {
			
			$status = delete_option( $feadvns_default_cat . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順 - ターゲット
		 */
		if ( isset( $_POST[$feadvns_sort_target . $manag_no] ) ) {
			
			$sort_target = $_POST[$feadvns_sort_target . $manag_no];
			
			$status = update_option( $feadvns_sort_target . $manag_no, $sort_target );
			if ( true == $status ) {
				$i++;
			}
		} else {
			
			$status = delete_option( $feadvns_sort_target . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順 - 昇順降順
		 */
		if ( isset( $_POST[$feadvns_sort_order . $manag_no] ) ) {
			
			$sort_order = $_POST[$feadvns_sort_order . $manag_no];
			
			$status = update_option( $feadvns_sort_order . $manag_no, $sort_order );
			if ( true == $status ) {
				$i++;
			}
		} else {
			
			$status = delete_option( $feadvns_sort_order . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順 - カスタムフィールドのキー
		 */
		if ( isset( $_POST[$feadvns_sort_target_cfkey . $manag_no] ) ) {
			
			$cfkey = $_POST[$feadvns_sort_target_cfkey . $manag_no];
			
			$status = update_option( $feadvns_sort_target_cfkey . $manag_no , $cfkey );
			if ( true == $status ) {
				$i++;
			}
		} else {
			
			$status = delete_option( $feadvns_sort_target_cfkey . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順 - 数値or文字
		 */
		if ( isset( $_POST[$feadvns_sort_target_cfkey_as . $manag_no] ) ) {
			
			$cfkey_as = $_POST[$feadvns_sort_target_cfkey_as . $manag_no];
			
			$status = update_option( $feadvns_sort_target_cfkey_as . $manag_no , $cfkey_as );
			if ( true == $status ) {
				$i++;
			}
		} else {
			
			$status = delete_option( $feadvns_sort_target_cfkey_as . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}

		/**
		 *	検索結果の並び順（第二条件） - ターゲット
		 */
		if ( isset( $_POST[$feadvns_sort_target_2nd . $manag_no] ) ) {
			
			$sort_target = $_POST[$feadvns_sort_target_2nd . $manag_no];
			
			$status = update_option( $feadvns_sort_target_2nd . $manag_no, $sort_target );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_sort_target_2nd . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順（第二条件） - 昇順降順
		 */
		if ( isset( $_POST[$feadvns_sort_order_2nd . $manag_no] ) ) {
			
			$sort_order = $_POST[$feadvns_sort_order_2nd . $manag_no];
			
			$status = update_option( $feadvns_sort_order_2nd . $manag_no, $sort_order );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_sort_order_2nd . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順（第二条件） - カスタムフィールドのキー
		 */
		if ( isset( $_POST[$feadvns_sort_target_cfkey_2nd . $manag_no] ) ) {
			
			$cfkey = $_POST[$feadvns_sort_target_cfkey_2nd . $manag_no];
			
			$status = update_option( $feadvns_sort_target_cfkey_2nd . $manag_no , $cfkey );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_sort_target_cfkey_2nd . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	検索結果の並び順（第二条件） - 数値or文字
		 */
		if ( isset( $_POST[$feadvns_sort_target_cfkey_as_2nd . $manag_no] ) ) {
			
			$cfkey_as = $_POST[$feadvns_sort_target_cfkey_as_2nd . $manag_no];
			
			$status = update_option( $feadvns_sort_target_cfkey_as_2nd . $manag_no , $cfkey_as );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_sort_target_cfkey_as_2nd . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
							
		/**
		 *	検索条件が未指定の場合
		 */
		if ( isset( $_POST[$feadvns_empty_request . $manag_no] ) ) {
			
			$empty_request = $_POST[$feadvns_empty_request . $manag_no];
			
			$status = update_option( $feadvns_empty_request . $manag_no, $empty_request );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_empty_request . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
		}
		
		/**
		 *	ドロップダウン内に件数を表示する設定
		 */
		if ( isset( $_POST[$feadvns_show_count .$manag_no] ) ) {
			
			$showcnt = $_POST[$feadvns_show_count . $manag_no];
			
			$status = update_option( $feadvns_show_count . $manag_no , $showcnt );
			if ( true == $status ) {
				$i++;
			}
			
			// 除外項目を反映した各ターム毎の記事数をあらかじめ算出
			//feas_reculc_term_cnt( $manag_no );
			
		} else {
			
			$status = delete_option( $feadvns_show_count . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
		
		/**
		 *	固定記事（Sticky Posts）を検索対象に含む設定
		 */
		if ( isset( $_POST[$feadvns_include_sticky . $manag_no] ) ) {
			
			$target_sp = $_POST[$feadvns_include_sticky . $manag_no];
			
			$status = update_option( $feadvns_include_sticky . $manag_no , $target_sp );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_include_sticky . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
		
		/**
		 *	検索結果から除外｜記事ID
		 */
		if ( isset( $_POST[$feadvns_exclude_id . $manag_no] ) && $_POST[$feadvns_exclude_id . $manag_no] != '' ) {
			
			$exclude_id = $_POST[$feadvns_exclude_id . $manag_no];
			
			// 半角数字、カンマのみ保存
			if ( preg_match( "/^[0-9,]+$/", $exclude_id ) ) {
				$exclude_id = explode( ',', $exclude_id );
				$status = update_option( $feadvns_exclude_id . $manag_no , $exclude_id );
				if ( true == $status ) {
					$i++;
				}
			}
		
		} else {
			
			$status = delete_option( $feadvns_exclude_id . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
		
		/**
		 *	検索結果から除外｜タームID
		 */
		if ( isset( $_POST[$feadvns_exclude_term_id . $manag_no] ) && $_POST[$feadvns_exclude_term_id . $manag_no] != '' ) {

			$exclude_term_id = null;
			$exclude_term_id = $_POST[$feadvns_exclude_term_id . $manag_no];

			// 半角数字、カンマのみ保存
			if ( preg_match( "/^[0-9,]+$/", $exclude_term_id ) ) {			
				$status = update_option( $feadvns_exclude_term_id . $manag_no , $exclude_term_id );
				if ( true == $status ) {
					$i++;
				}
			}
		
		} else {
			
			$status = delete_option( $feadvns_exclude_term_id . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
		
		/**
		 *	検索結果から除外｜カスタムフィールド
		 */
/*
		if ( isset( $_POST[$feadvns_exclude_cf . $manag_no] ) && $_POST[$feadvns_exclude_cf . $manag_no] != '' ) {

			$cf_key = null;
			$cf_key = $_POST[$feadvns_exclude_cf . $manag_no];
			
			// 半角数字、カンマのみ保存
			if ( preg_match( "/^[0-9,]+$/", $cf_key ) ) {				
				$status = update_option( $feadvns_exclude_cf . $manag_no , $cf_key );
				if ( true == $status ) {
					$i++;
				}
			}
		
		} else {
			
			$status = delete_option( $feadvns_exclude_cf . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
*/

		/**
		 *	保存後に現在のタブに留まるため
		 */
/*
		if ( isset( $_POST[$feadvns_search_current_tab] ) ) {
			
			$tab_index = $_POST[$feadvns_search_current_tab];
			
			$status = update_option( $feadvns_search_current_tab , $tab_index );
			if ( true == $status ) {
				$i++;
			}
		
		} else {
			
			$status = delete_option( $feadvns_search_current_tab );
			if ( 1 == $status ) {
				$i++;
			}			
		}
*/

		/*=================================
			検索項目の作成
		 =================================*/

		/**
		 *	消去にチェックされていたら処理
		 */
		$check_del = null;
		for ( $i = 0; $i < $line_cnt; $i++ ) {
			if ( ( isset( $_POST[$cols[9] .$manag_no . "_" . $i] ) ) && ( $_POST[$cols[9] . $manag_no . "_" . $i] == "del" ) ) {
				$check_del ="check";
			}
		}
		if ( $check_del != null ) {
			$line_cnt = check_del_line( $manag_no, $line_cnt );
		}
		
		/**
		 *	オプション値によるcolsの調整
		 */
		for ( $i = 0; $i < $line_cnt; $i++ ) {
			
			
			// ============== 現在未使用 ===============
			$_POST[$cols[12] . $manag_no . "_" . $i] =
			$_POST[$cols[21] . $manag_no . "_" . $i] =
			$_POST[$cols[23] . $manag_no . "_" . $i] =
			$_POST[$cols[28] . $manag_no . "_" . $i] = '';
			
			// 消去にチェックが入って行はダミーの「-」をDBに食わせる（キャッシュのため）…要改善
			if ( ! isset( $_POST[$cols[9] . $manag_no . "_" . $i] ) && $_POST[$cols[9] . $manag_no . "_" . $i] != "del" ) {
				 $_POST[$cols[9] . $manag_no . "_" . $i] = '';
			 }
/*
			
			if ( isset( $_POST[$cols[11] . $manag_no . "_" . $i] ) && $_POST[$cols[11] . $manag_no . "_" . $i] != '' ) {
				// 半角数字、カンマのみ保存
				if ( preg_match( "/^[0-9,]+$/", $_POST[$cols[11] . $manag_no . "_" . $i] ) ) {
					$_POST[$cols[11] . $manag_no . "_" . $i] = explode( ',', $_POST[$cols[11] . $manag_no . "_" . $i] );
				} else {
					$_POST[$cols[11] . $manag_no . "_" . $i] = false;
				}
			}
*/
			
			//  フリーワード検索のターゲットを半角カンマ区切りで格納	
			if ( isset( $_POST[$cols[13] . $manag_no . "_" . $i] ) ) {
	
				$kw_cnt = count( $_POST[$cols[13] . $manag_no . "_" . $i] );
				
				$kwds_target = null;
				for ( $ii = 0; $ii < 6; $ii++ ) {
						//if ( $_POST[$cols[13] .$manag_no ."_" .$i][$ii] == '' ) {
						if ( ! isset( $_POST[$cols[13] .$manag_no . "_" . $i][$ii] ) ) {
							$kwds_target .= "0";
						} else {
							$kwds_target .= esc_sql( $_POST[$cols[13] . $manag_no . "_" . $i][$ii] );
						}
	
						if ( $ii + 1 != 6 ) {
							$kwds_target .= ",";
						}
				}
				$_POST[$cols[13] . $manag_no . "_" . $i]  = $kwds_target;
			
			} else {
				$_POST[$cols[13] . $manag_no . "_" . $i]  = ''; //チェックがすべて外れていた時
			}
			
			// 空のカテゴリは表示するorしない
			if ( isset( $_POST[$cols[14] . $manag_no . "_" . $i] ) == false )
				$_POST[$cols[14] . $manag_no . "_" . $i] = 'yes';
			
			// フリーワード検索時のゆらぎ検索指定：チェックが付いている時 'no'
			if ( isset( $_POST[$cols[15] . $manag_no . "_" . $i] ) == false )
				$_POST[$cols[15] . $manag_no . "_" . $i] = 'yes';
			
			//  カスタムフィールド検索時、数値を千の位毎に半角カンマで区切る指定：チェックが付いている時 'yes'
			if ( isset( $_POST[$cols[18] .$manag_no ."_" .$i] ) == false )
				$_POST[$cols[18] . $manag_no . "_" . $i] = 'no';
				
			//  Ajaxフィルタリング
			if ( isset( $_POST[$cols[19] .$manag_no ."_" .$i] ) == false )
				$_POST[$cols[19] . $manag_no . "_" . $i] = 'yes';
				
			//メタキー指定検索
			if ( ( isset( $_POST[$cols[20] .$manag_no . "_" . $i] ) ) && ( $_POST[$cols[20] . $manag_no . "_" . $i] != '' ) ) {
				
				// 余計なスペースを除去
				$kwds_keys = str_replace( '　', ' ', stripslashes( $_POST[$cols[20] . $manag_no . "_" . $i] ) );
				$kwds_keys = explode( ',', $kwds_keys );	
				$mod_keys = array();
				foreach( $kwds_keys as $kwd ) {
					$mod_kwd = trim( $kwd );
					if ( '' !== $mod_kwd ) {
						$mod_keys[] = $mod_kwd;
					}
				}	
				$_POST[$cols[20] . $manag_no . "_" . $i] = maybe_serialize( $mod_keys ); // 配列
				
				// メタキースイッチON
				$_POST[$cols[21] . $manag_no . "_" . $i] = 'no';
			
			} else {
			
				$_POST[$cols[21] . $manag_no . "_" . $i] = false;
			}
				
			if ( isset( $_POST[$cols[22] . $manag_no . "_" . $i] ) == false )
				$_POST[$cols[22] . $manag_no . "_" . $i] = false;
			
			if ( false === isset( $_POST[$cols[24] . $manag_no . "_" . $i] ) ) {
				$_POST[$cols[24] . $manag_no . "_" . $i] = false;
			}
						
			// 「要素内の並び順」が「自由記述」の場合、各行のテキスト・値・階層を配列にしてDBに格納（使用時に取り出しやすくするため）
			if ( isset( $_POST[$cols[36] . $manag_no . "_" . $i] ) !== false && '' !== ( $_POST[$cols[36] . $manag_no . "_" . $i] ) ) {
									
					// 改行で行を配列に格納
					$freetext = str_replace( array( "\r\n","\r","\n"), "\n", $_POST[$cols[36] . $manag_no . "_" . $i] );
					$lines  = explode( "\n", $freetext );
					
					// 空の行を配列から削除
					$lines  = array_filter( $lines, function( $value ) {
					    if ( empty( $value ) && $value !== '0' && $value !== 0 ) {
					        return false;
					    } else {
					        return true;
					    }
					});
					$lines = array_values( $lines );
						
					$options = array();
								
					// 行数分ループを回す
					for ( $ii = 0; $cnt = count( $lines ), $ii < $cnt; $ii++ ) {
						
						if ( empty( $lines[$ii] ) )
							continue;
						
						// 区切り文字「:」で表記と値に分割
						$contents = explode( ":", trim( $lines[$ii] ) );
							
						//$sterm_li[$i] = new stdClass();
						
						// 配列が1つ = 「:」で区切られていない場合は、表記も値も同じ値を渡す
						if ( 1 === count( $contents ) ) {
							$options[$ii]['value'] = $contents[0];
						} 
						// それ以外は「:」の後半を値として渡す
						else {
							$options[$ii]['value'] = $contents[1];
						}
						
						// 「:」の前半は表記
						$options[$ii]['text'] = $contents[0];
						
						// 階層の初期設定
						$options[$ii]['depth'] = 1;				
						
						// 表記テキストの冒頭に「--」がある場合はその個数に応じてインデントを設定
						// 「--」でない場合は何もせずスキップ
						if ( '--' !== mb_substr( $options[$ii]['text'], 0, 2 ) )
							continue;					
						
						$indent_cnt = substr_count( $options[$ii]['text'], '--' ); // --の出現回数
						$options[$ii]['depth'] = $options[$ii]['depth'] + $indent_cnt;
						$options[$ii]['text']  = mb_substr( $options[$ii]['text'], ( $indent_cnt * 2 ), mb_strlen( $options[$ii]['text'] ) ); // 「--」を除去
					}			
				
				// 配列で渡す⇒シリアライズされるはず
				$_POST[$cols[36] . $manag_no . "_" . $i] = maybe_serialize( $options );	
			}
		}
			
		/**
		 *	DB検索キーを格納
		 */
		$db_search_key_sort = array();
	
		for ( $i_line = 0; $i_line < $line_cnt; $i_line++ ) {
			if ( isset( $_POST["feadvns_disp_number_" . $manag_no . "_" . $i_line] ) ) {
				$db_search_key_sort[$i_line]['order'] = $_POST["feadvns_disp_number_" . $manag_no . "_" . $i_line]; 
			} else {
				$db_search_key_sort[$i_line]['order'] = null;
			}
			$db_search_key_sort[$i_line]['line'] = $i_line;
		}
			
		/**
		 *	ソート
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
			for ( $i_cols = 0, $cnt_cols = count( $cols ); $i_cols < $cnt_cols; $i_cols++ ) {
				if ( isset( $_POST[$cols[$i_cols] .$manag_no . "_" . $db_search_key_sort[$i_p]['line']] ) ) {
					$sort_data[$cols[$i_cols] .$manag_no . "_" . $i_p] = $_POST[$cols[$i_cols] . $manag_no . "_" . $db_search_key_sort[$i_p]['line']];
				} else {
					//$sort_data[$cols[$i_cols] . $manag_no . "_" . $i_p] = null;
					$sort_data[$cols[$i_cols] . $manag_no . "_" . $i_p] = '';
				}
			}
		}
					
		/**
		 *	 $_POSTへ代入
		 */
		$s_keys = array_keys( $sort_data );
		for ( $i_cp = 0, $cnt_cp = count( $s_keys ); $i_cp < $cnt_cp; $i_cp++ ) {
			$_POST[$s_keys[$i_cp]] = $sort_data[$s_keys[$i_cp]];
		}
		
		/**
		 *	DB検索キーを格納
		 */
		$db_search_key = array();
	
		for ( $i_line = 0; $i_line < $line_cnt; $i_line++ ) {
			// col
			for ( $i_cols = 0, $cnt_cols = count( $cols); $i_cols < $cnt_cols; $i_cols++ ) {
				// OPのキー作成
				$s_key = $cols[$i_cols] . $manag_no . "_" . $i_line;
	
				// DB検索キーを格納
				$db_search_key[] = $s_key;
				//$db_search_key[$i_line][$i_cols] = $s_key;
			}
		}
		
		/**
		 *	DB保存処理
		 */
				
		// メッセージ初期化
		$e = new WP_Error();
		$save_status = 0;
		$i = 0;
		wp_cache_delete( 'feas_message', 'options' );	 
		
		for ( $i_s_key = 0, $cnt_s_key = count( $db_search_key); $i_s_key < $cnt_s_key; $i_s_key++ ) {
			//wp_cache_delete( $db_search_key[$i_s_key], 'options' );
			//$save_status = update_option( $db_search_key[$i_s_key], $_POST[$db_search_key[$i_s_key]] );
				
			if ( null === db_op_get_value( $db_search_key[$i_s_key] ) ) {
				$save_status = db_op_insert_value( $db_search_key[$i_s_key], $_POST[$db_search_key[$i_s_key]] );
			} else {
				$save_status = db_op_update_value( $db_search_key[$i_s_key], $_POST[$db_search_key[$i_s_key]] );
			}
			
			if ( 0 != $save_status ) {
				$i++;
			}
		}
			
		/**
		 *	検索ボタンラベル
		 */
		if ( isset( $_POST[$feadvns_search_b_label . $manag_no] ) ) {
			
			$b_label = $_POST[$feadvns_search_b_label . $manag_no];
			//wp_cache_delete( $feadvns_search_b_label . $manag_no, 'options' );
			$status = update_option( $feadvns_search_b_label . $manag_no, $b_label );
			if ( true == $status ) {
				$i++;
			}
			// v1.8.1以前のデータ削除
			delete_option( 'search_button_label_' . $manag_no );			
		
		} else {
			
			$status = delete_option( $feadvns_search_b_label . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}
			// v1.8.1以前のデータ削除
			delete_option( 'search_button_label_' . $manag_no );			
		}
		
		/**
		 *	検索ボタン - 前に挿入
		 */			
		if ( isset( $_POST[$feadvns_search_b_label .$manag_no ."_before"] ) ) {
			
			$b_label_before = $_POST[$feadvns_search_b_label . $manag_no . "_before"];
			$status = update_option( $feadvns_search_b_label . $manag_no . "_before", $b_label_before );
			if ( true == $status ) {
				$i++;
			}	
			// v1.8.1以前のデータ削除
			delete_option( 'search_button_label_' . $manag_no . '_before' );			
		
		} else {
			
			$status = delete_option( $feadvns_search_b_label . $manag_no . '_before' );
			if ( 1 == $status ) {
				$i++;
			}
			// v1.8.1以前のデータ削除
			delete_option( 'search_button_label_' . $manag_no . '_before' );				
		}

		/**
		 *	検索ボタン - 後に挿入
		 */	
		if ( isset( $_POST[$feadvns_search_b_label .$manag_no ."_after"] ) ) {
			
			$b_label_after = $_POST[$feadvns_search_b_label . $manag_no . "_after"];
			$status = update_option( $feadvns_search_b_label . $manag_no . "_after", $b_label_after );
			if ( true == $status ) {
				$i++;
			}
			// v1.8.1以前のデータ削除
			delete_option( 'search_button_label_' . $manag_no . '_after' );				
		
		} else {
			
			$status = delete_option( $feadvns_search_b_label . $manag_no . '_after' );
			if ( 1 == $status ) {
				$i++;
			}
			// v1.8.1以前のデータ削除
			delete_option( 'search_button_label_' . $manag_no . '_after' );				
		}

		/**
		 *	プレビュー | テーマのCSSを読み込む
		 */	
/*
		if ( isset( $_POST[$pv_theme_css . $manag_no] ) ) {
			
			$load_flag = $_POST[$pv_theme_css . $manag_no];
			$status = update_option( $pv_theme_css . $manag_no, $load_flag );
			if ( true == $status ) {
				$i++;
			}			
		
		} else {
			
			$status = delete_option( $pv_theme_css . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
*/

		/**
		 *	プレビュー | 「デザイン」のCSSを読み込む
		 */	
		if ( isset( $_POST[$pv_css . $manag_no] ) ) {
			
			$load_flag = $_POST[$pv_css . $manag_no];
			$status = update_option( $pv_css . $manag_no, $load_flag );
			if ( true == $status ) {
				$i++;
			}			
		
		} else {
			
			$status = delete_option( $pv_css . $manag_no );
			if ( 1 == $status ) {
				$i++;
			}			
		}
		
		/**
		 *	結果ソート？
		 */
/*
		if ( isset( $_POST[$feadvns_search_b_label . $manag_no . "_sort"] ) ) {
			
			$b_label_sort = null;
			$b_label_sort = $_POST[$feadvns_search_b_label . $manag_no . "_sort"];
			
			$status = update_option( $feadvns_search_b_label . $manag_no . "_sort" , $b_label_sort, false );
			if ( true == $status ) {
				$i++;
			}			
		
		} else {
			
			$status = delete_option( $feadvns_search_b_label . $manag_no . '_sort' );
			if ( 1 == $status ) {
				$i++;
			}			
		}
*/
		
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

		/**
		 *	現在のフォームに留まるためにmanag_noをセット
		 */
		$manag_no = $_POST['current_form_no'];
		
		/**
		 *	行カウント
		 */
		$line_cnt = get_option( $feadvns_max_line . $manag_no );
				
		wp_safe_redirect( menu_page_url( 'feas_management', false ) );
	
	
	} // 設定を保存
	

	/*************************************************************************
	 *
	 *
	 *	フォーム切り替えドロップダウンの「実行」ボタン
	 *
	 *
	 *************************************************************************/	
	 
 	if ( isset( $_POST['c_form_number'] ) ) {
		
		
		/**
		 *	新規追加
		 */
		if ( 'new' == $_POST['c_form_number'] ) {

			// メッセージ初期化
			$e = new WP_Error();
			$new_status = array();
						
			// フォーム総数を更新
			$get_form_max = ( $get_form_max + 1 );
			$new_status[] = update_option( $feadvns_max_page , $get_form_max );
			
			// autoincを繰り上げ
			$autoinc_no = get_option( $feadvns_autoinc_no );			
			$autoinc_no = ( $autoinc_no + 1 ); // 次の値
			$new_status[] = update_option( $feadvns_autoinc_no, $autoinc_no );	
			
			// フォームID保存
			$new_status[] = update_option( $feadvns_form_no . $get_form_max, $autoinc_no );

			// 生成したばかりのフォームID（累積最大値）をセット
			$manag_no = $autoinc_no;
			
			// 新規フォーム生成後、そのフォームを表示するためにPOSTをセット
			//$_POST['c_form_number'] = $get_form_max;
			$new_status[] = update_option( $feadvns_current_form, $manag_no );
			
			// 行カウントを設定
			$new_status[] = update_option( $feadvns_max_line . $manag_no, '1' );
			//$line_cnt = 1;
			
			
			// メッセージ生成
			$false_cnt = 0;
			
			foreach ( $new_status as $k => $v ) {
				if ( false === $v ) {
					$false_cnt++;
				}
			}
			if ( 0 === $false_cnt ) {
				$e->add( 'ok', '新しいフォームが作成されました' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'updated', 10 );
			} else {
				$e->add( 'error', 'フォームは作成できませんでした' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'error', 10 );
			}
			
			wp_safe_redirect( menu_page_url( 'feas_management', false ) );
		
		/**
		 *	削除
		 */
		} else if ( 'del' == $_POST['c_form_number'] ) {

			global $use_style_key,
			$style_body_key,
			$feadvns_sort_target,
			$feadvns_sort_order,
			$feadvns_sort_target_cfkey,
			$feadvns_sort_target_cfkey_as,
			$pv_css,
			$feadvns_search_form_name,
			$feadvns_search_target,
			$feadvns_default_cat,
			$feadvns_empty_request,
			$feadvns_show_count,
			$feadvns_include_sticky,
			$feadvns_kwds_targets,
			$feadvns_kwds_yuragi,
			$cols_order,
			$feadvns_sort_target_2nd,
			$feadvns_sort_order_2nd,
			$feadvns_sort_target_cfkey_2nd,
			$feadvns_sort_target_cfkey_as_2nd,
			$feadvns_max_line;
			
			// メッセージ初期化
			$e = new WP_Error();
			$del_status = array();

			// 削除対象のフォームID
			if ( ! isset( $_POST['current_form_no'] ) ) {
				$e->add( 'error', '削除する検索フォームのフォームIDが指定されていません' );
				return;
			}			
			$form_no = $_POST['current_form_no'];
			
			// 現存のフォームIDリスト抽出
			$form_no_list = array();
			
			for ( $i = 0; $i <= $get_form_max; $i++ ) {
				$check = db_op_field_check( $feadvns_form_no . $i );		
				if ( ! $check ) {
					//db_op_insert_value( $feadvns_form_no . $i, $i );
					update_option( $feadvns_form_no . $i, $i );
				}
				$form_no_tmp = db_op_get_value( $feadvns_form_no . $i );
				$form_no_list[$i] = $form_no_tmp;
			}

			// リストから削除フォームIDをドロップ、更新	
			if ( $form_no_list ) {
				
				$del_form_id = null;
				
				for ( $i = 0, $cnt = count( $form_no_list ); $i < $cnt; $i++ ) {
					
					// 削除予定のフォームIDはリストから削除
					if ( $form_no == $form_no_list[$i] ) {
						$del_form_id = array_splice( $form_no_list, $i, 1 );
						$cnt--; // 削除した分カウントを減らす
					}
				}
				
				// 更新
				for ( $i = 0, $cnt = count( $form_no_list ); $i < $cnt; $i++ ) {
					update_option( $feadvns_form_no . $i, $form_no_list[$i] );		
				}
				
				// 削除してリストが前へズレた分、最後尾のレコードを削除
				if ( $del_form_id ) {
					db_op_delete_value( $feadvns_form_no . $get_form_max );
				}
			}
			
			// 検索フォーム関連項目の削除
			$line_cnt = get_option( $feadvns_max_line . $form_no );
						
			// 行ごとの項目
			if ( $line_cnt ) {
				for ( $line_i = 0; $line_i < $line_cnt; $line_i++ ) {
					for ( $cols_i = 0, $cols_cnt = count( $cols ); $cols_i < $cols_cnt; $cols_i++ ) {
						
						// ソート項目のoption_nameを抽出
						$del_status[$line_i][$cols_i] =	delete_option( $cols[$cols_i] . $form_no . '_' . $line_i );
						
						// おそらく未使用項目…
						$del_status[$line_i][$cols_i] =	db_op_delete_value( $feadvns_kwds_targets . $form_no . '_' . $line_i );
						$del_status[$line_i][$cols_i] =	db_op_delete_value( $feadvns_kwds_yuragi . $form_no . '_' . $line_i );
					}
				}
			}
			
			// 全体項目
			$del_status[$feadvns_search_form_name] =	delete_option( $feadvns_search_form_name . $form_no );
			$del_status[$use_style_key] =	delete_option( $use_style_key . $form_no );
			$del_status[$style_body_key] =	delete_option( $style_body_key . $form_no );
			$del_status[$pv_css] =	delete_option( $pv_css . $form_no );
			$del_status[$feadvns_search_target] =	delete_option( $feadvns_search_target . $form_no );
			$del_status[$feadvns_default_cat] =	delete_option( $feadvns_default_cat . $form_no );
			$del_status[$feadvns_empty_request] =	delete_option( $feadvns_empty_request . $form_no );
			$del_status[$feadvns_show_count] =	delete_option( $feadvns_show_count . $form_no );
			$del_status[$feadvns_include_sticky] =	delete_option( $feadvns_include_sticky . $form_no );
			$del_status[$feadvns_sort_target] =	delete_option( $feadvns_sort_target . $form_no );
			$del_status[$feadvns_sort_order] =	delete_option( $feadvns_sort_order . $form_no );
			$del_status[$feadvns_sort_target_cfkey] =	delete_option( $feadvns_sort_target_cfkey . $form_no );
			$del_status[$feadvns_sort_target_cfkey_as] =	delete_option( $feadvns_sort_target_cfkey_as . $form_no );
			$del_status[$feadvns_sort_target_2nd] =	delete_option( $feadvns_sort_target_2nd . $form_no );
			$del_status[$feadvns_sort_order_2nd] =	delete_option( $feadvns_sort_order_2nd . $form_no );
			$del_status[$feadvns_sort_target_cfkey_2nd] =	delete_option( $feadvns_sort_target_cfkey_2nd . $form_no );
			$del_status[$feadvns_sort_target_cfkey_as_2nd] =	delete_option( $feadvns_sort_target_cfkey_as_2nd . $form_no );
			$del_status[$feadvns_max_line] =	delete_option( $feadvns_max_line . $form_no );
			

			/**
			 *	ソート関連項目の削除	
			 */
			 		
			// 削除する検索フォームに対応するソートメニューの行数
			// ソートメニューがなければnull
			
			wp_cache_delete( $feadvns_max_line_order . $form_no );
			$line_cnt_order = get_option( $feadvns_max_line_order . $form_no );

			// 行ごとの項目	
			if ( $line_cnt_order ) {
				for ( $line_order_i = 0; $line_order_i < $line_cnt_order; $line_order_i++ ) {
					for ( $cols_order_i = 0, $cols_order_cnt = count( $cols_order ); $cols_order_i < $cols_order_cnt; $cols_order_i++ ) {
						$del_status[$line_order_i][$cols_order_i] = delete_option( $cols_order[$cols_order_i] . $form_no . '_' . $line_order_i );
					}
				}
			}
				
			// 全体項目
			$del_status[$feadvns_max_line_order] =	delete_option( $feadvns_max_line_order . $form_no );

			
			// Debug
			//update_option( 'feadvns_debug_' . $form_no, $del_status );
						

			// 諸々アップデート	
			if ( 0 < $get_form_max ) {
				
				// フォーム総数を1つ減らす
				$get_form_max = ( $get_form_max - 1 );
				
				// 削除時は最初のフォームに移動するため
				$form_no = get_option( $feadvns_form_no . '0' );
				
				if ( false === $form_no ) {
					// フォームIDがない場合は、累積ID最大値を代入
					$form_no = get_option( $feadvns_autoinc_no );
					if ( false ===  $form_no ) {
						// それもない場合は0を代入
						$form_no = 0;
					}
				}
				$manag_no = $form_no;
			
			} else {

				/**
				 * すべてのフォームが削除された場合、新規フォームを作成し、累積最大数をフォームIDにセット
				 */
				$autoinc_no = get_option( $feadvns_autoinc_no );			
				$autoinc_no = ( $autoinc_no + 1 ); // 次の値	
						
				// キャッシュ削除
				wp_cache_delete( 'feadvns_form_no_0', 'options' );
				
				// フォームID保存
				update_option( $feadvns_form_no . '0', $autoinc_no );
						
				// manag_noに代入
				$manag_no = $autoinc_no;
				
				// 累積値更新
				update_option( $feadvns_autoinc_no, $autoinc_no );

			}

			// フォーム総数を更新
			db_op_update_value( $feadvns_max_page , $get_form_max );
							
			// 削除後、最初のフォームをロードするため
			//$_POST['c_form_number'] = 0;
			update_option( $feadvns_current_form, $manag_no );
			
			// 最初のフォームの行数
			$form_no = get_option( $feadvns_form_no . '0' );
			$max_line = get_option( $feadvns_max_line . $form_no );
			if ( false !== $max_line ) {
				$line_cnt = (int) $max_line;
			} else {
				$line_cnt = 1;
				wp_cache_delete( $feadvns_max_line . $form_no, 'options' );
				update_option( $feadvns_max_line . $form_no, $line_cnt );
			}
			

			// メッセージ生成
			if ( 0 < count( $del_status ) ) {
				$e->add( 'ok', 'フォームが削除されました' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'updated', 10 );
			} else {
				$e->add( 'ok', 'フォームは削除されませんでした' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'error', 10 );
			}
			
			wp_safe_redirect( menu_page_url( 'feas_management', false ) );	
		
		
		/**
		 *	フォームID切り替え時
		 */
		
		} else {
			
			// メッセージ初期化
			$e = new WP_Error();
			$status = null;
			wp_cache_delete( 'feas_message', 'options' );
									
			// manag_noをセット
			$form_no = get_option( $feadvns_form_no . $_POST['c_form_number'] );
			if ( false === $form_no ) {
				$form_no = get_option( $feadvns_autoinc_no );
			}
			$manag_no = $form_no;
			
			//wp_cache_delete( $feadvns_current_form, 'options' );
			$status = update_option( $feadvns_current_form, $manag_no );
			
			// 行数
			$line_cnt = get_option( $feadvns_max_line . $manag_no );
			
			if ( false !== $status ) {
				$e->add( 'ok', 'フォームを切り替えました' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'updated', 10 );
			} else {
				$e->add( 'ok', 'フォームは同じです' );
				set_transient( 'feas_message', $e->get_error_messages(), 10 );
				set_transient( 'feas_message_notice_flag', 'error', 10 );
			}
			
			wp_safe_redirect( menu_page_url( 'feas_management', false ) );
		}
		
	}
	
	/*===========================================
		検索フォーム全体の「設定を保存」ボタン
	 ===========================================*/	
/*
	if ( isset( $_POST['gs'] ) && $_POST['gs'] == "update" ) {	
		

	}
*/

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
		for ( $i_cols = 0, $cnt_cols = count( $cols ); $i_cols < $cnt_cols; $i_cols++ ) {
			if ( ! isset( $_POST[$cols[$i_cols] . $manag_no . "_" . $i] ) ) {
				$_POST[$cols[$i_cols] . $manag_no . "_" . $i] = maybe_unserialize( get_option( $cols[$i_cols] . $manag_no . "_" . $i ) );
			}
		}
	}
	
	/**
	 *	検索ボタンのラベル取得
	 */
								 
	if ( ! isset( $_POST[$feadvns_search_b_label . $manag_no] ) ) {

		// v1.8.1以下互換
		$b_label_data = get_option( 'search_button_label_' . $manag_no );
		
		// v1.8.2〜
		if ( ! $b_label_data ) {
			$b_label_data = get_option( $feadvns_search_b_label . $manag_no );
		}		

		if ( $b_label_data ) {
			$_POST[$feadvns_search_b_label . $manag_no] = $b_label_data;
		} else {
			$_POST[$feadvns_search_b_label . $manag_no] = "検　索";
		}
	}
	
	/**
	 *	検索ボタン - 前に挿入
	 */	
	if ( ! isset( $_POST[$feadvns_search_b_label . $manag_no . "_before"] ) ) {
		
		// v1.8.1以下互換
		$feadvns_search_b_label_before = get_option( 'search_button_label_' . $manag_no . '_before' );
		
		// v1.8.2〜
		if ( ! $feadvns_search_b_label_before ) {
			$feadvns_search_b_label_before = get_option( $feadvns_search_b_label . $manag_no . "_before" );
		}
		
		$_POST[$feadvns_search_b_label . $manag_no . "_before"] = $feadvns_search_b_label_before;
	}

	/**
	 *	検索ボタン - 後に挿入
	 */	
	if ( ! isset( $_POST[$feadvns_search_b_label . $manag_no . "_after"] ) ) {
		
		// v1.8.1以下互換
		$feadvns_search_b_label_after = get_option( 'search_button_label_' . $manag_no . '_after' );
		
		// v1.8.2〜
		if ( ! $feadvns_search_b_label_after ) {
			$feadvns_search_b_label_after = get_option( $feadvns_search_b_label . $manag_no . '_after' );
		}
		
		$_POST[$feadvns_search_b_label . $manag_no . "_after"] = $feadvns_search_b_label_after;
	}
	
/*
	if ( ! isset( $_POST[$feadvns_search_b_label . $manag_no] ) ) {
		$_POST[$feadvns_search_b_label . $manag_no . "_sort"] = get_option( $feadvns_search_b_label . $manag_no . "_sort" );
	}
*/
	/**
	 *	保存する直前に選択されていたタブを開く
	 */	
	if ( ! isset( $_POST[$feadvns_search_current_tab] ) ) {
		$_POST[$feadvns_search_current_tab] = get_option( $feadvns_search_current_tab );
	}
	
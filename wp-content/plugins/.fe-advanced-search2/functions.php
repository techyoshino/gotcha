<?php
////////////////////////////////////
//	プラグイン共通関数
////////////////////////////////////

/*============================
	viewとcontrollerの登録、global読み込み
 ============================*/
function feas_func_management( $func_name = null ) {
	global $wpdb, $cols, $cols_order, $feadvns_max_line, $feadvns_max_line_order, $manag_no, $feadvns_search_b_label, $feadvns_max_page, $use_style_key, $style_body_key, $meta_sort_key, $pv_css, $feadvns_search_form_name, $feadvns_search_target, $feadvns_default_cat, $feadvns_sort_target, $feadvns_sort_order, $feadvns_sort_target_cfkey, $feadvns_sort_target_cfkey_as, $feadvns_empty_request, $feadvns_show_count, $feadvns_include_sticky, $kwds_for_view, $feadvns_kwds_target, $feadvns_kwds_yuragi, $feadvns_form_no, $feadvns_form_no_list, $feadvns_autoinc_no, $feadvns_current_form, $feadvns_exclude_id, $feadvns_exclude_term_id, $feadvns_exclude_cf, $feadvns_sort_target_2nd, $feadvns_sort_order_2nd, $feadvns_sort_target_cfkey_2nd, $feadvns_sort_target_cfkey_as_2nd, $feadvns_search_current_tab, $feadvns_sort_current_tab;
	
	if ( $func_name != null ) {
		require_once( $func_name . "-controller.php" );
		require_once( $func_name . "-view.php" );
	}
}

/*============================
	ラッパー関数
 ============================*/
function feas_search_form( $form_no = 0, $as_shortcode = null ) {
	return create_searchform( $form_no, $as_shortcode );
}
function feas_search_count( $form_no = 0, $output = true ) {
	return feas_count_posts( $form_no, $output );
}
function feas_search_query( $output = true, $separator = ',', $before = '<span>', $after = '</span>', $widget = false ) {
	if ( false === $output ) {
		if ( true === $widget ) {
			return search_result( null, 0, $separator, $before, $after, true );
		} else {
			return search_result_array( 'all', 0 );
		}
	} else {
		return search_result( null, 0, $separator, $before, $after );
	}
}

/*============================
	DBのoptionsに指定キーが存在してるかのcheck
 ============================*/
function db_op_field_check( $option_name = null ) {
	global $wpdb;

	if ( $option_name == null )
		return null;

	$sql  = " SELECT option_id FROM $wpdb->options";
	$sql .= " WHERE option_name = '" . esc_sql( $option_name ) ."'";
	$sql .= " LIMIT 1";
	$get_date = $wpdb->get_results( $sql );

	if ( isset( $get_date[0]->option_id ) )
		return $get_date[0]->option_id;
	else
		return null;
}
/*============================
	optionsのoption_value値を取得
 ============================*/
function db_op_get_value( $option_name = null ) {
	global $wpdb;
	
	//$ret = null;
	
	//if ( null != $option_name ) {
		$sql  = " SELECT option_value FROM {$wpdb->options}";
		$sql .= " WHERE option_name = '" . esc_sql( $option_name ) . "'";
		$sql .= " LIMIT 1";
		$check_data = $wpdb->get_results( $sql );

		if ( isset( $check_data[0]->option_value ) ) {
			return $check_data[0]->option_value;
		}
	//}
	//return $ret;
}

/*============================
	optionsのoption_value値を新規書き込み
 ============================*/
function db_op_insert_value( $option_name = null, $option_value = null ) {
	global $wpdb, $wp_version;

	$ret = null;
	
	$sql  = " SELECT option_id FROM $wpdb->options";
	$sql .= " ORDER BY option_id DESC";
	$sql .= " LIMIT 1";
	$insert_id = $wpdb->get_results( $sql );
	$insert_id = ( $insert_id[0]->option_id + 1 );
	
	if ( isset( $option_value ) && $option_value !== null ) {
		if ( $wp_version >= '3.4' ) {
			$insert_sql  = " INSERT INTO $wpdb->options";
			$insert_sql .= " (option_id,option_name,option_value,autoload )";
			$insert_sql .= " VALUES( " . esc_sql( $insert_id ) . ", '" . esc_sql( $option_name ) . "', '" . esc_sql( $option_value ) . "', 'yes' )";
		
		} else {
			
			$insert_sql  = " INSERT INTO $wpdb->options";
			$insert_sql .= " (option_id,blog_id,option_name,option_value,autoload )";
			$insert_sql .= " VALUES( " . esc_sql( $insert_id ) .", 0, '" . esc_sql( $option_name ) ."', '" . esc_sql( $option_value ) . "', 'yes' )";
		}
		$ret = $wpdb->get_results( $insert_sql );
	}
	
	return $ret;
}

/*============================
	optionsのoption_value値を更新
 ============================*/
function db_op_update_value( $option_name = null,  $option_value = null ) {
	global $wpdb;
	
	$ret = null;
	
	$sql  = " SELECT option_id FROM $wpdb->options";
	$sql .= " WHERE option_name= '" . esc_sql( $option_name ) . "'";
	$sql .= " LIMIT 1";
	$check_data = $wpdb->get_results( $sql );

	if ( isset( $check_data[0]->option_id ) ) {
		if ( isset( $option_value ) && $option_value !== null ) {
			$update_sql  = " UPDATE $wpdb->options"; 
			$update_sql .= " SET option_value = '" . esc_sql( $option_value ) . "'";
			$update_sql .= " WHERE option_id = " . esc_sql( $check_data[0]->option_id );
			$ret = $wpdb->get_results( $update_sql );
		}
	}
	
	return $ret;
}

/*============================
	optionsのデータを消去
 ============================*/
function db_op_delete_value( $option_name = null ) {
	global $wpdb;

	$num_rows = null;
	
	if ( $option_name == null )
		return;
	else {
		$del_sql  = " DELETE FROM " . $wpdb->options;
		$del_sql .= " WHERE option_name ='" . esc_sql( $option_name ) . "'";
		$num_rows = $wpdb->get_results( $del_sql );
	}
	return $num_rows;
}

/*============================
	optionsに新規登録（各種設定）
 ============================*/
function db_op_insert( $option_name = null ) {
	global $wpdb, $wp_version;

	$ret = null;
	
	$sql  = " SELECT option_id FROM $wpdb->options";
	$sql .= " ORDER BY option_id DESC";
	$sql .= " LIMIT 1";
	$insert_id = $wpdb->get_results( $sql );
	$insert_id = ( $insert_id[0]->option_id + 1 );

	if ( $wp_version >= '3.4' ) {
		$insert_sql  = " INSERT INTO $wpdb->options";
		$insert_sql .= " ( option_id, option_name, option_value, autoload )";
		if ( isset( $_POST[$option_name] ) )
			$insert_sql .= " VALUES( " . esc_sql( $insert_id ) . ", '" . esc_sql( $option_name ) . "', '" . esc_sql( $_POST[$option_name] ) . "', 'yes' )";
		else
			$insert_sql .= " VALUES( " . esc_sql( $insert_id ) . ",'" . esc_sql( $option_name ) . "', '', 'yes' )";
	} else {
		$insert_sql  = " INSERT INTO $wpdb->options";
		$insert_sql .= " (option_id,blog_id,option_name,option_value,autoload )";
		if ( isset( $_POST[$option_name] ) )
			$insert_sql .= " VALUES(" . esc_sql( $insert_id ) . ", 0, '" . esc_sql( $option_name ) . "', '" . esc_sql( $_POST[$option_name] ) . "', 'yes' )";
		else
			$insert_sql .= " VALUES(" . esc_sql( $insert_id ) . ", 0, '" . esc_sql( $option_name ) . "', '', 'yes' )";
	}
	
	$ret = $wpdb->get_results( $insert_sql );
	
	return $ret;
}

/*============================
	optionsに更新作業（各種設定）
 ============================*/
function db_op_update( $option_name = null ) {
	global $wpdb;

	$ret = null;
	
	$sql  = " SELECT option_id FROM $wpdb->options";
	$sql .= " WHERE option_name='" . esc_sql( $option_name ) ."'";
	$sql .= " LIMIT 1";
	$check_data = $wpdb->get_results( $sql );

	if ( isset( $check_data[0]->option_id ) ) {
		if ( isset( $_POST[$option_name] ) ) {
			$update_sql  = " UPDATE $wpdb->options"; 
			$update_sql .= " SET option_value = '" . esc_sql( $_POST[$option_name] ) . "'";
			$update_sql .= " WHERE option_id = " . esc_sql( $check_data[0]->option_id );
			$ret = $wpdb->get_results( $update_sql );
		}
	}
	return $ret;
}

/*============================
	optionsの値を取得
 ============================*/
function db_op_get_data( $get_op_id = null ) {
	global $wpdb;

	if ( $get_op_id != null ) {
		$sql  = " SELECT option_value FROM $wpdb->options";
		$sql .= " WHERE option_id =" . esc_sql( $get_op_id );
		$sql .= " LIMIT 1";
		$get_data = $wpdb->get_results( $sql );
	}
	else
		return null;

	return $get_data[0]->option_value;
}

/*============================
	消去にチェックがついていたら$_POSTずらす
 ============================*/
function check_del_line( $manag_no, $line_cnt ) {
	global $wpdb, $cols, $feadvns_max_line;

	$line_data = array();
	$ins_data = 0;

	// POSTずらす処理
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		if ( isset( $_POST[$cols[9] . $manag_no . "_" . $i] ) == false || $_POST[$cols[9] . $manag_no . "_" . $i] != "del" ) {
			for ( $i_ins =0, $cnt_ins = count( $cols ); $i_ins < $cnt_ins; $i_ins++ ) {
				if ( isset($_POST[$cols[$i_ins] . $manag_no . "_" . $i] ) )
					$line_data[$cols[$i_ins] . $manag_no . "_" . $ins_data] = $_POST[$cols[$i_ins] . $manag_no . "_" . $i];
				else
					$line_data[$cols[$i_ins] . $manag_no . "_" . $ins_data] = null;
			}
			$ins_data++;
		}
	}

	$_POST = $line_data;

	// 表示ラインの検索キー
	$line_key = $feadvns_max_line . $manag_no;

	// 新規
	$save_line_number = $ins_data;

	if ( get_option( $line_key ) == null )
		db_op_insert_value( $line_key, $save_line_number );
	else //更新
		db_op_update_value( $line_key, $save_line_number );

	// DBの設定データを一次的に消去
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		for ( $i_ins = 0, $cnt_ins = count( $cols ); $i_ins < $cnt_ins; $i_ins++ ) {
			db_op_delete_value( $cols[$i_ins] . $manag_no . "_" . $i );
		}
	}
	
	$_POST['ac'] = "update";
	return $ins_data;
}

/*============================
	消去にチェックがついていたら$_POSTずらす（ソート）
 ============================*/
function check_del_line_order( $manag_no, $line_cnt ) {
	global $wpdb, $cols, $cols_order, $feadvns_max_line_order;

	$line_data = array();
	$ins_data = 0;

	// POSTずらす処理
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		if ( isset( $_POST[$cols_order[3] . $manag_no . "_" . $i] ) == false || $_POST[$cols_order[3] . $manag_no . "_" . $i] != "del" ) {
			for ( $i_ins = 0, $cnt_ins = count( $cols_order ); $i_ins < $cnt_ins; $i_ins++ ) {
				if ( isset( $_POST[$cols_order[$i_ins] . $manag_no . "_" . $i] ) ) {
					$line_data[$cols_order[$i_ins] . $manag_no ."_" . $ins_data] = $_POST[$cols_order[$i_ins] . $manag_no . "_" . $i ];
				} else {
					$line_data[$cols_order[$i_ins] . $manag_no ."_" . $ins_data] = null;
				}
			}
			$ins_data++;
		}
	}

	$_POST = $line_data;

	// 表示ラインの検索キー
	$line_key = $feadvns_max_line_order . $manag_no;

	// 新規
	$save_line_number = $ins_data;

	if ( get_option($line_key) == null )
		db_op_insert_value( $line_key, $save_line_number );
	else // 更新
		db_op_update_value( $line_key, $save_line_number );

	// DBの設定データを一次的に消去
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		for ( $i_ins = 0, $cnt_ins = count( $cols_order ); $i_ins < $cnt_ins; $i_ins++ ) {
			db_op_delete_value( $cols_order[$i_ins] . $manag_no . "_" . $i );
		}
	}

	$_POST['ac'] = "update";
	return $ins_data;
}

/*============================
	検索条件を格納
 ============================*/
function insert_result( $data ) {
	global $wp_query;
	if ( $wp_query->is_main_query() ) {
		$_POST['search_result_data'][] = $data;
	}
}

function insert_kwds_result( $data , $key = 0 ) {
	global $wp_query;
	if ( $wp_query->is_main_query() ) {
		if ( isset( $_POST['kwds_result_data_' . $key ] ) && ( $_POST['kwds_result_data_' . $key ] !== null ) )
			$_POST['kwds_result_data_' . $key ] .= ' ' . $data;
		else
			$_POST['kwds_result_data_' . $key ] = $data;
		// 全てのフリーワードフォームに入力された文字,ハイライト表示のキーワードなどに
		$_POST['kwds_result_data_all'][] = $data;
	}
}

function feas_insert_keys_result( $data, $key = 0 ) { //カスタムフィールドのフィールド名
	$_POST['keys_result_data_' . $key][] = $data;
}

/*============================
	子カテゴリーがあった場合option作成
 ============================*/
function create_child_op( $par_id = null, $term_depth = -1, $class_cnt = 2, $q_term_id = array(), $nocnt = false, $exids = null, $sticky = array(), $showcnt = null, $manage_line = null, $taxonomy = false, $par_no = 0, $number = 0, $sp = array(), $to = " t.term_id ASC ") {
	global $wpdb, $cols, $manag_no, $form_count, $feadvns_search_target, $feadvns_include_sticky, $feadvns_search_b_label, $feadvns_default_cat;
	
/**
 * ToDo:
 * 引数を配列にまとめる
 */
/*
	$defaults = array(
		'manag_no'  => $manag_no,
		'counter'   => 0,
		'parent'    => 0,
		'tax_name'  => 'category',
		'tax_label' => 'カテゴリー',
		'depth'     => 0,
		'echo'      => true,
	);
	$args = wp_parse_args( $args, $defaults );
*/

	$ret_ele = null;
	$get_cats_cnt = '';
	$get_cnt = '';
	
	if ( $term_depth == 0 || $par_id == null )
		return;
		
	
	/**
	 *	検索対象のpost_typeを取得
	 */
	$target_pt_tmp = get_option( $feadvns_search_target . $manag_no );
	if ( $target_pt_tmp ) {
		$target_pt = "'" . implode( "','", (array) $target_pt_tmp ) . "'";
	} else {
		$target_pt = "'post'";
	}

	/**
	 *  投稿ステータス
	 */
	if ( in_array( 'attachment', (array) $target_pt_tmp ) ) {
		$post_status = "'publish', 'inherit'";
	} else {
		$post_status = "'publish'";
	}
		
	/**
	 *	固定タクソノミ／タームの設定を取得
	 */
	$fixed_term = get_option( $feadvns_default_cat . $manag_no );
		
	/**
	 *	キャッシュから取得／ない場合は実行してキャッシュ保存
	 */
	if ( false === ( $get_cats = feas_cache_judgment( $manag_no, 'taxonomy', $par_id ) ) ) {
			
		// ターム一覧を取得
		$sql  = " SELECT t.term_id, t.name FROM {$wpdb->terms} AS t";
		$sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
		$sql .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id";
		$sql .= " WHERE tt.parent = " . esc_sql( $par_id );
		if ( $taxonomy ) $sql .= " AND tt.taxonomy = '" . esc_sql( $taxonomy ) . "'";
		if ( $exids )    $sql .= " AND t.term_id NOT IN (" . esc_sql( $exids ) . ")";
		$sql .= " GROUP BY t.term_id";
		$sql .= " ORDER BY " . esc_sql( $to );			
		$get_cats = $wpdb->get_results( $sql );
			
		feas_cache_create( $manag_no, 'taxonomy', $par_id, $get_cats );
	}
	
	$cnt_ele = count( $get_cats );

	/**
	 *	件数を取得してキャッシュ保存
	 */	
	if ( $get_cats ) {
		
		$term_cnt = array();
		foreach( $get_cats as $term_id ) {
								
			if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'term_cnt_' . $term_id->term_id, false ) ) ) {	
				$sql  = " SELECT count( p.ID ) AS cnt FROM {$wpdb->posts} AS p";
				$sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
				if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr2 ON p.ID = tr2.object_id";
				$sql .= " WHERE 1=1";
				if ( $sp ) $sql .= " AND p.ID NOT IN ( $sp )";
				$sql .= " AND tr.term_taxonomy_id = " . esc_sql( $term_id->term_id );
				if ( $fixed_term ) $sql .= " AND tr2.term_taxonomy_id = " . esc_sql( $fixed_term );
				$sql .= " AND p.post_type IN( $target_pt )";
				$sql .= " AND p.post_status IN ( {$post_status} )";
						
				$cnt = $wpdb->get_row( $sql );
				feas_cache_create( $manag_no, 'term_cnt_' . $term_id->term_id, false, $cnt );
			}
			$term_cnt[] = $cnt;
		}
	}
		
	for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {
		
		// 0件タームは表示しない場合（post_status処理後の件数を再評価）
		if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
			continue;

		// $_GETで値が取得できないため
		$selected = null;	
		for ( $i_lists = 0, $cnt_lists = count( $q_term_id ); $i_lists < $cnt_lists; $i_lists++ ) {
			if ( $q_term_id[$i_lists] ) {
				if ( $q_term_id[$i_lists] == $get_cats[$i_ele]->term_id )
					$selected =' selected="selected"';
			}
		}
		
		// 管理ページ用
		if ( isset( $manage_line ) ) {
			if ( $_POST[$cols[2] . $manag_no . "_" . $manage_line ] ) {
				if ( $_POST[$cols[2] . $manag_no . "_" . $manage_line ] == $get_cats[$i_ele]->term_id )
					$selected = ' selected="selected" ';
			}
		}

		// $class_cntが10以下なら0を付ける
		if ( $class_cnt < 10 )
			$d_class_cnt = "0" . $class_cnt;
		else
			$d_class_cnt = $class_cnt;

		$cat_cnt = null;
		if ( "yes" == $showcnt )
			$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";
				
		$nbsp = null;
		for ( $i = 0; $i < $d_class_cnt; $i++) {
			$nbsp .= "&nbsp;&nbsp;";
		}
		
		$ret_ele .= "<option id='feas_". esc_attr( $manag_no . "_" . $number . "_" . $par_no . "_" . $form_count ) . "' class='feas_clevel_" . esc_attr( $d_class_cnt ) . "' value='" . esc_attr( $get_cats[$i_ele]->term_id ) . "'" . $selected . ">" 
		. $nbsp . esc_html( $get_cats[$i_ele]->name . $cat_cnt ) 
		. "</option>\n";
		$form_count++;
		
		// 階層の指定がある場合
		if ( $term_depth > 1 )
			$ret_ele .= create_child_op( $get_cats[$i_ele]->term_id, ( $term_depth - 1 ), ( $class_cnt + 1 ), $q_term_id, $nocnt, $exids, $sticky, $showcnt, $manage_line, $taxonomy, $par_no, $number, $sp, $to );
		// 階層が未指定(=無制限)の場合
		else if ( $term_depth === -1 )
			$ret_ele .= create_child_op( $get_cats[$i_ele]->term_id, $term_depth , ( $class_cnt + 1 ), $q_term_id, $nocnt, $exids, $sticky, $showcnt, $manage_line, $taxonomy, $par_no, $number, $sp, $to );

	}

	return $ret_ele;
}

/*============================
	子カテゴリーがあった場合checkbox作成
 ============================*/
function create_child_check( $par_id = null, $ele_class = null, $check_cnt = 0, $class_cnt = 2, $nocnt = null, $exids = null, $sticky = array(), $showcnt = null, $taxonomy = false, $par_no = 0, $number = 0, $sp = array(), $to = "t.term_id ASC" ) {
	global $wpdb, $manag_no, $form_count, $total_cnt, $feadvns_search_target, $feadvns_include_sticky, $feadvns_search_b_label, $feadvns_default_cat;
	
	$ret_ele = $ret_chi = null;
	
	if ( $check_cnt == 0 || $par_id == null )
		return;
		
	/**
	 *	検索対象のpost_typeを取得
	 */
	$target_pt_tmp = get_option( $feadvns_search_target . $manag_no );
	if ( $target_pt_tmp ) {
		$target_pt = "'" . implode( "','", (array) $target_pt_tmp ) . "'";
	} else {
		$target_pt = "'post'";
	}

	/**
	 *  投稿ステータス
	 */
	if ( in_array( 'attachment', (array) $target_pt_tmp ) ) {
		$post_status = "'publish', 'inherit'";
	} else {
		$post_status = "'publish'";
	}
		
	/**
	 *	固定タクソノミ／タームの設定を取得
	 */
	$fixed_term = get_option( $feadvns_default_cat . $manag_no );
	
	/**
	 *	キャッシュから取得／ない場合は実行してキャッシュ保存
	 */
	if ( false === ( $get_cats = feas_cache_judgment( $manag_no, 'taxonomy', $par_id ) ) ) {
			
		// ターム一覧を取得
		$sql  = " SELECT t.term_id, t.name FROM {$wpdb->terms} AS t";
		$sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
		$sql .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id";
		$sql .= " WHERE tt.parent = " . esc_sql( $par_id );
		if ( $taxonomy ) $sql .= " AND tt.taxonomy = '" . esc_sql( $taxonomy ) . "'";
		if ( $exids )    $sql .= " AND t.term_id NOT IN (" . esc_sql( $exids ) . ")";
		$sql .= " GROUP BY t.term_id";
		$sql .= " ORDER BY " . esc_sql( $to );			
		$get_cats = $wpdb->get_results( $sql );
			
		feas_cache_create( $manag_no, 'taxonomy', $par_id, $get_cats );
	}
	
	$cnt_ele = count( $get_cats );
	
	/**
	 *	件数を取得してキャッシュ保存
	 */	
	if ( $get_cats ) {
		
		$term_cnt = array();
		foreach( $get_cats as $term_id ) {
								
			if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'term_cnt_' . $term_id->term_id, false ) ) ) {	
				$sql  = " SELECT count( p.ID ) AS cnt FROM {$wpdb->posts} AS p";
				$sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
				if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr2 ON p.ID = tr2.object_id";
				$sql .= " WHERE 1=1";
				if ( $sp ) $sql .= " AND p.ID NOT IN ( $sp )";
				$sql .= " AND tr.term_taxonomy_id = " . esc_sql( $term_id->term_id );
				if ( $fixed_term ) $sql .= " AND tr2.term_taxonomy_id = " . esc_sql( $fixed_term );
				$sql .= " AND p.post_type IN( $target_pt )";
				$sql .= " AND p.post_status IN ( {$post_status} )";
						
				$cnt = $wpdb->get_row( $sql );
				feas_cache_create( $manag_no, 'term_cnt_' . $term_id->term_id, false, $cnt );
			}
			$term_cnt[] = $cnt;
		}
	}

	for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {
		// 0件タームは表示しない場合（post_status処理後の件数を再評価）
		if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
			continue;
		
		$checked = '';
		if ( isset( $_GET["search_element_" . $number] ) ) {
			for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
				if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
					if ( $_GET["search_element_" . $number][$i_lists] == $get_cats[$i_ele]->term_id ) {
						$checked = ' checked="checked"';
					}
				}
			}
		}

		// $class_cntが10以下なら0を付ける
		if ( $class_cnt < 10 )
			$d_class_cnt = "0" . $class_cnt;
		else
			$d_class_cnt = $class_cnt;

		$cat_cnt = null;
		if ( "yes" == $showcnt )
			$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";

		// Sanitize
		$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$par_no}_{$form_count}" );
		$ret_name = esc_attr( "search_element_{$number}[]" );
		$ret_val  = esc_attr( $get_cats[$i_ele]->term_id );
		$ret_text = esc_html( $get_cats[$i_ele]->name . $cat_cnt );
		$ret_dclass_cnt = esc_attr( "feas_clevel_{$d_class_cnt}" );
					
		$ret_ele .= "<label for='{$ret_id}' class='{$ret_dclass_cnt}'>";
		$ret_ele .= "<input id='{$ret_id}' type='checkbox' name='{$ret_name}' value='{$ret_val}' $checked />";
		$ret_ele .= "<span>{$ret_text}</span>";
		$ret_ele .= "</label>\n";
		
		$total_cnt++;
		$form_count++;
		
		// 階層の指定がある場合
		if ( $check_cnt > 1 )
			$ret_chi = create_child_check( $get_cats[$i_ele]->term_id, "feas_clevel_", ( $check_cnt - 1 ), ( $class_cnt + 1 ), $nocnt, $exids, $sticky, $showcnt, $taxonomy, ( $par_no + 1 ), $number, $sp, $to );
		
		// 階層が未指定(=無制限)の場合
		else if ( $check_cnt == -1 )
			$ret_chi = create_child_check( $get_cats[$i_ele]->term_id, "feas_clevel_", $check_cnt, ( $class_cnt + 1 ), $nocnt, $exids, $sticky, $showcnt, $taxonomy, $par_no, $number, $sp, $to );
		
		// 生成されたチェックボックスを格納
		if ( isset( $ret_chi ) )
			$ret_ele .= $ret_chi;
	}
	
	return $ret_ele;
}

/*============================
	子カテゴリーがあった場合radiobutton作成
 ============================*/
function create_child_radio( $par_id = null, $ele_class = null, $check_cnt = 0, $class_cnt = 2, $nocnt, $exids, $sticky, $showcnt, $taxonomy = false, $par_no, $number = 0, $sp = array(), $to = "t.term_id ASC" ) {
	global $wpdb, $manag_no, $form_count, $feadvns_search_target, $feadvns_include_sticky, $feadvns_search_b_label, $feadvns_default_cat;

	$ret_ele = null;
		
	if ( $par_id == null )
		return;
		
	/**
	 *	検索対象のpost_typeを取得
	 */
	$target_pt_tmp = get_option( $feadvns_search_target . $manag_no );
	if ( $target_pt_tmp ) {
		$target_pt = "'" . implode( "','", (array) $target_pt_tmp ) . "'";
	} else {
		$target_pt = "'post'";
	}

	/**
	 *  投稿ステータス
	 */
	if ( in_array( 'attachment', (array) $target_pt_tmp ) ) {
		$post_status = "'publish', 'inherit'";
	} else {
		$post_status = "'publish'";
	}
	
	/**
	 *	固定タクソノミ／タームの設定を取得
	 */
	$fixed_term = get_option( $feadvns_default_cat . $manag_no );
	
	/**
	 *	キャッシュから取得／ない場合は実行してキャッシュ保存
	 */
	if ( false === ( $get_cats = feas_cache_judgment( $manag_no, 'taxonomy', $par_id ) ) ) {
			
		// ターム一覧を取得
		$sql  = " SELECT t.term_id, t.name FROM {$wpdb->terms} AS t";
		$sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
		$sql .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id";
		$sql .= " WHERE tt.parent = " . esc_sql( $par_id );
		if ( $taxonomy ) $sql .= " AND tt.taxonomy = '" . esc_sql( $taxonomy ) . "'";
		if ( $exids )    $sql .= " AND t.term_id NOT IN (" . esc_sql( $exids ) . ")";
		$sql .= " GROUP BY t.term_id";
		$sql .= " ORDER BY " . esc_sql( $to );			
		$get_cats = $wpdb->get_results( $sql );
			
		feas_cache_create( $manag_no, 'taxonomy', $par_id, $get_cats );
	}	
	
	$cnt_ele = count( $get_cats );
	
	/**
	 *	件数を取得してキャッシュ保存
	 */	
	if ( $get_cats ) {
		
		$term_cnt = array();
		foreach( $get_cats as $term_id ) {
								
			if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'term_cnt_' . $term_id->term_id, false ) ) ) {	
				$sql  = " SELECT count( p.ID ) AS cnt FROM {$wpdb->posts} AS p";
				$sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
				if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr2 ON p.ID = tr2.object_id";
				$sql .= " WHERE 1=1";
				if ( $sp ) $sql .= " AND p.ID NOT IN ( $sp )";
				$sql .= " AND tr.term_taxonomy_id = " . esc_sql( $term_id->term_id );
				if ( $fixed_term ) $sql .= " AND tr2.term_taxonomy_id = " . esc_sql( $fixed_term );
				$sql .= " AND p.post_type IN( $target_pt )";
				$sql .= " AND p.post_status IN ( {$post_status} )";
						
				$cnt = $wpdb->get_row( $sql );
				feas_cache_create( $manag_no, 'term_cnt_' . $term_id->term_id, false, $cnt );
			}
			$term_cnt[] = $cnt;
		}
	}
	
	for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {
		// 0件タームは表示しない場合（post_status処理後の件数を再評価）
		if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
			continue;
			
		$checked = null;
		if ( isset( $_GET['search_element_' . $number ] ) ) {
			if ( $_GET['search_element_' . $number] == $get_cats[$i_ele]->term_id )
				$checked = ' checked="checked"';
		}

		//$class_cntが10以下なら0を付ける
		if ( $class_cnt < 10 )
			$d_class_cnt = "0" . $class_cnt;
		else
			$d_class_cnt = $class_cnt;
		
		$cat_cnt = null;
		if ( "yes" == $showcnt )
			$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";

		$ret_ele .= "<label for='feas_" . esc_attr( $manag_no . "_" . $number . "_" . $par_no . "_" . $form_count ) . "' class='feas_clevel_" . esc_attr( $d_class_cnt ) . "' >";
		$ret_ele .= "<input id='feas_" . esc_attr( $manag_no . "_" . $number . "_" . $par_no . "_" . $form_count ) . "' type='radio' name='search_element_" . esc_attr( $number ) . "' value='" . esc_attr( $get_cats[$i_ele]->term_id ) . "' " . $checked . " />";
		$ret_ele .= "<span>" . esc_html( $get_cats[$i_ele]->name . $cat_cnt ) . "</span>";
		$ret_ele .= "</label>";
		$form_count++;

		// 階層の指定がある場合
		if ( $check_cnt >1 )
			$ret_ele .= create_child_radio( $get_cats[$i_ele]->term_id, "feas_clevel_", ( $check_cnt - 1 ), ( $class_cnt + 1 ), $nocnt, $exids, $sticky, $showcnt, $taxonomy, $par_no, $number, $sp, $to );
		// 階層が未指定(=無制限)の場合
		else if ( $check_cnt == -1 )
			$ret_ele .= create_child_radio( $get_cats[$i_ele]->term_id, "feas_clevel_", $check_cnt, ( $class_cnt + 1 ), $nocnt ,$exids, $sticky, $showcnt, $taxonomy, $par_no, $number, $sp, $to );
	}

	return $ret_ele;
}

/*============================
	子カテゴリー検索にチェックが合った場合に子カテゴリを取得する用
 ============================*/
function get_cat_chi_ids( $par_id = null ) {
	global $wpdb;

	if ( $par_id == null )
		return;

	$ret_ids = array();

	$sql  = " SELECT term_taxonomy_id FROM $wpdb->term_taxonomy";
	$sql .= " WHERE parent = " . esc_sql( $par_id );
	$get_ids = $wpdb->get_results( $sql );

	if ( isset( $get_ids[0]->term_taxonomy_id) == true ) {
		for ( $i = 0, $cnt = count( $get_ids ); $i < $cnt; $i++ ) {
			$ret_ids[] = $get_ids[$i]->term_taxonomy_id;
	
			$get_chi_ids = get_cat_chi_ids( $get_ids[$i]->term_taxonomy_id );
	
			for ( $is =0, $cnt_s = count( $get_chi_ids ); $is < $cnt_s; $is++ ) {
				$ret_ids[] = $get_chi_ids[ $is ];
			}
		}
	}
	return $ret_ids;
} 

/////////////////////////////////////////////////////////////////
//	view側
/////////////////////////////////////////////////////////////////

/*============================
	optionsの値を取得
 ============================*/
function data_to_post( $element_name = null ) {
	
	if ( isset( $_POST[$element_name] ) ) {
		$_POST[$element_name] = str_replace( "'", "\"", stripslashes( $_POST[$element_name] ) );

		return $_POST[$element_name];
	}
	else
		return null;
}

/*============================
	posttypeを取得
 ============================*/
function feas_posttype_lists( $manag_no = 0 ) {
	global $wp_version, $feadvns_search_target;

	$ret = null;
	$target_pt = array();
	
	// DBに登録された対象post_typeの値を取得
	$target_pt_data = get_option( $feadvns_search_target . $manag_no );
	if ( false === $target_pt_data ) {
		$target_pt = array( 'post' );
	} elseif ( is_string( $target_pt_data ) ) {
		$target_pt = explode( ',', $target_pt_data );
	} else {
		$target_pt = maybe_unserialize( $target_pt_data );
	}
		
	if ( $wp_version >= '3.0' ) {
		$args   = array( 'public'  => true );
		$output = 'objects';
		$ptlist = get_post_types( $args, $output );		
		foreach( $ptlist as $pt ) {
			
			$pt_checked = '';
			if ( in_array( $pt->name, $target_pt ) )
				$pt_checked = ' checked="checked"';
					
			$ret .= "<label><input type='checkbox' ";
			$ret .= "name='" . esc_attr( $feadvns_search_target . $manag_no ) . "[]' ";
			$ret .= "value='" . esc_attr( $pt->name ) . "' ";
			$ret .= $pt_checked . " /> ";
			$ret .= esc_html( $pt->label );
			$ret .= "（" . esc_html( $pt->name ) . "）</label>"; 
		}	

	}
	
	print( $ret );
}

function feas_delete_transient_all() {
	global $cols,$wpdb;
	/*
	$sql = "SELECT option_name FROM `$wpdb->options` WHERE `option_name` LIKE '_transient_feadvns_cache_number_%'";
	$get_cane_name = $wpdb->get_results($sql);
	
	if (is_array($get_cane_name)) {
		$return = array();
		foreach($get_cane_name as $key) {
			preg_match('/\d*$/',$key->option_name,$matches);
			delete_transient( $cols[23].$matches[0] );
			$return[] = $matches[0];
		}
	}*/
	$sql = "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '_transient_feadvns_cache_number_%' OR `option_name` LIKE '_transient_timeout_feadvns_cache_number_%'";
	if ( false === $wpdb->query( $sql ) )
		$return = 'DBエラー';
	else
		$return = 'キャッシュを削除しました';
		
	return $return;
}
/*
function feas_delete_transient() {
	global $cols,$feadvns_max_page;
	$return = array();
	$get_form_max = get_option( $feadvns_max_page );
	$get_form_max++;
	for ($i = 0 ; $i <= $get_form_max ; $i++) {
		if (get_transient($cols[23].$i)) {
			delete_transient($cols[23].$i);
			$return[] = $i;
		}
	}
	return $return;
}
add_action('wp_insert_post','feas_delete_transient');
add_action('edit_category','feas_delete_transient');
add_action('create_category','feas_delete_transient');*/

function feas_fix_pagenation( $url ) {
	if ( 'page' != get_option( 'show_on_front' ) )
		return $url;
	
	//$fixed_paged = get_query_var('page');
	
	$fixed_url = add_query_arg( array(
		'paged' => 2
	), $url );
	
	return $fixed_url;
}
//add_filter( 'get_pagenum_link', 'feas_fix_pagenation' );

function feas_add_query_vars_filter( $vars ) {
  $vars[] = "csp";
  return $vars;
}
add_filter( 'query_vars', 'feas_add_query_vars_filter' );

/*============================
	Twenty Fifteenで「最近の投稿」ウィジェット使用時にFEASが効いてしまうので回避
 ============================*/
/*
function feas_remove_filters( $title ) {
	
	remove_filter( 'posts_where', 'search_where_add' );
	
	return $title;
}
*/
// remove_filter後、単純にタイトルだけ返す
//add_action( 'widget_title', 'feas_remove_filters' );

/*============================
	カテゴリ/タームの階層構造をインデント表示するoptionタグを生成
 ============================*/
function feas_get_hierarchical_term_list( $args = '' ) {
			
	global $manag_no, $feadvns_default_cat, $cols;
	
	$list_html  = '';
	
	$defaults = array(
		'manag_no'  => $manag_no,
		'counter'   => 0,
		'parent'    => 0,
		'tax_name'  => 'category',
		'tax_label' => 'カテゴリー',
		'depth'     => 0,
		'echo'      => true,
	);
	$args = wp_parse_args( $args, $defaults );
		
	if ( 'post_format' == $args['tax_name'] )
		return;
	if ( -1 != $args['counter'] && 'post_tag' == $args['tax_name'] )
		return;								
	
	$term_args = array(
		'taxonomy'   => $args['tax_name'],
		'hide_empty' => 0,
		'parent'     => $args['parent'],
		'orderby'    => 'term_id',
		'order'      => 'ASC',
		);
	$terms = get_categories( $term_args );

	if ( $terms ) :
	
		// -1 = 全体設定の場合
		if ( -1 == $args['counter'] ) {
			// DBに登録されたデフォルトカテゴリ値（ID）を取得
			$saved_val = get_option( $feadvns_default_cat . $args['manag_no'] );
		} else {
			//$param_num  = $cols[2] . $args['manag_no'] . "_" . $args['counter'];
			$saved_val = get_option( $cols[2] . $args['manag_no'] . '_' . $args['counter'] );
		}
			
		// 全体設定の場合はトップカテゴリ/タームを表示しない
		if ( 0 == $args['parent'] && -1 != $args['counter'] ) {
			
			$selected = '';
			if ( 'par_' . $args['tax_name'] == $saved_val ) {
				$selected = ' selected="selected"';
			}
			
			$list_html = '<option value="par_' . esc_attr( $args['tax_name'] ) . '"' . $selected . ' data-visible-ctl="c">' .  esc_html( $args['tax_label'] ) . ' (top)</option>';
		}

		
		foreach ( $terms as $term ) :
		
			$selected = '';
			if ( $term->term_id == $saved_val ) {
				$selected = ' selected="selected"';
			}
			
			$anc     = array();
			$nbsp    = '';
			$cnt_anc = 0;
			
			// 階層に応じてインデント表示
			if ( 0 != $args['depth'] ) {
				for ( $i_tab = 0; $i_tab < $args['depth']; $i_tab++ ) {
					$nbsp .= '&nbsp;&nbsp;';
				}
			}
			$list_html .= '<option value="' . esc_attr( $term->term_id ) . '"' . $selected . ' data-visible-ctl="c">' . $nbsp . esc_html( $term->name ) . '</option>';
			
			// 子ターム以下を再帰生成
			$args2 = array(
				'manag_no'  => $args['manag_no'],
				'counter'   => $args['counter'],
				'parent'    => $term->term_id,
				'tax_name'  => $args['tax_name'],
				'tax_label' => $args['tax_label'],
				'depth'     => $args['depth'] + 1,
				'echo'      => false,
			);
			$list_html .= feas_get_hierarchical_term_list( $args2 );
			
		endforeach;
	
	endif;
	
	if ( true == $args['echo'] ) {
		echo $list_html;
	} else {
		return $list_html;
	}
}

/*============================
	プラグインのアップデート通知箇所にメッセージを追記
 ============================*/
function feas_add_message_on_plugins_page( $plugin_file, $plugin_data, $status ) {
	global $wp_list_table;
	
	if ( 'Upgrade' != $status ) {
		return;
	}
		
	list( $columns, $hidden ) = $wp_list_table->get_column_info();
	$colspan = count( $columns );
	$colspan = $colspan - 2;
?>
<tr class="active update">
	<th scope="row" class="check-column"></th>
	<td class="plugin-title column-primary"></td>
	<td colspan="<?php echo esc_attr( $colspan ); ?>">
		<p class="arrow_box"><b>ダッシュボード</b> > <b>更新</b> から更新してください．</p>	
	</td>
	<style>
	.arrow_box {
		position: relative;
		background: #ffdcdc;
		border: 4px solid #ffdcdc;
		padding: 0.2em !important;
	}
	.arrow_box:after, .arrow_box:before {
		top: 100%;
		left: 50%;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
	}
	
	.arrow_box:after {
		border-color: rgba(255, 220, 220, 0);
		border-top-color: #ffdcdc;
		border-width: 5px;
		margin-left: -5px;
	}
	.arrow_box:before {
		border-color: rgba(255, 220, 220, 0);
		border-top-color: #ffdcdc;
		border-width: 11px;
		margin-left: -11px;
	}
	</style>
</tr>
		
<?php
}
add_action( 'after_plugin_row_fe-advanced-search/plugin.php', 'feas_add_message_on_plugins_page', 10, 3 );

/*============================
	$manag_noを設定
 ============================*/
function feas_set_manag_no() {
	global $feadvns_max_page, $manag_no, $manag_order_no, $feadvns_form_no, $feadvns_autoinc_no, $feadvns_order_no, $feadvns_max_line;
	
	// フォーム総数を取得
	$get_form_max = get_option( $feadvns_max_page );
	if ( ! $get_form_max ) {
		$get_form_max = 0;
	}
			
	if ( isset( $_POST['c_form_number'] ) ) {
		if ( 'new' == $_POST['c_form_number'] ) {

			// 累積フォーム生成数		
/*
			$autoinc_no = get_option( $feadvns_autoinc_no );
			if ( ! $autoinc_no ) {
				$manag_no = ( $get_form_max + 1 );
			} else {
				$manag_no = ( $autoinc_no + 1 );
			}
			
*/

			$autoinc_no = get_option( $feadvns_autoinc_no );
			if ( $autoinc_no ) {
				
				$manag_no = ( $autoinc_no + 1 );
				
				$autoinc_no = ( $autoinc_no + 1 );
				update_option( $feadvns_form_no . $get_form_max, $autoinc_no );
				update_option( $feadvns_autoinc_no, $autoinc_no );			
			}
			else {
				
				$manag_no = ( $get_form_max + 1 );
				
				$autoinc_no = ( $get_form_max + 1 );
				update_option( $feadvns_form_no . $get_form_max, $autoinc_no );
				update_option( $feadvns_autoinc_no, $autoinc_no );	
			}
			
			// フォーム総数を更新
			update_option( $feadvns_max_page , $get_form_max );
		
		} else if ( 'del' == $_POST['c_form_number'] ) {
		
			if ( 0 < $get_form_max ) {
				
				// 削除時は最初のフォームに移動するため
				$form_no = get_option( $feadvns_form_no . '0' );
				if ( ! $form_no ) {
					$form_no = get_option( $feadvns_autoinc_no );
				}
				$manag_no = $form_no;
				
				// フォーム総数を1つ減らす
				$get_form_max = ( $get_form_max - 1 );			
			
			} else {
				
				// 1件もフォームがなくなったときは、新しいフォームのために累積フォーム数+1をセットする
				$form_no = get_option( $feadvns_autoinc_no );
				if ( ! $form_no ) {
					$manag_no = 0;
				}
				$manag_no = $form_no;			
			}
			
			/*============================
				諸々アップデート
			 ============================*/	
			
			// フォーム総数を更新
			db_op_update_value( $feadvns_max_page , $get_form_max );
					
			// 削除後、フォームID=0をロードするため
			$_POST['c_form_number'] = 0;
			
			// 削除した上で、最初のフォームのフォームIDをmanag_noにセット
/*
			$form_no = get_option( $feadvns_form_no . '0' );
			if ( ! $form_no ) {
				$form_no = get_option( $feadvns_autoinc_no );
			}
			$manag_no = $form_no;
*/
			
			// フォームID=0の行数
			$max_line = get_option( $feadvns_max_line . $_POST['c_form_number'] );
						
			if ( $max_line ) {
				$line_cnt = (int) $max_line;
			} else {
				$line_cnt = 1;
				db_op_insert_value( $feadvns_max_line . $_POST['c_form_number'], $line_cnt );
			}
		
		} else {
					
			$form_no = get_option( $feadvns_form_no . $_POST['c_form_number'] );
			if ( ! $form_no ) {
				$form_no = get_option( $feadvns_autoinc_no );
			}
			$manag_no = ( $form_no + 1 );
		}
	
	} else {
		
		$manag_no = 0;
	}
	
	if ( isset( $_POST['c_order_number'] ) && $_POST['c_order_number'] != null ) {

		//$manag_order_no = $_POST['c_order_number'];
		$order_no = get_option( $feadvns_order_no . $_POST['c_order_number'] );
		$manag_order_no = $order_no;
		
	} else {
		$manag_order_no = 0;
	}
}

/*============================
	style表示用関数
 ============================*/
function feas_header_style() {
	global $feadvns_form_no, $use_style_key, $style_body_key,$feadvns_max_page;
	
	$get_form_max = intval( get_option( $feadvns_max_page ) );
	$use_style    = null;
	$get_db_body  = null;

	for ( $i = 0; $i <= $get_form_max; $i++ ) {
			
		$form_no_tmp = '';
		
		$form_no_tmp = get_option( $feadvns_form_no . $i );
						
		if ( get_option( $use_style_key . $form_no_tmp ) == 1 ) {
			$get_db_body .= get_option( $style_body_key . $form_no_tmp);
		}
	}
	
	if ( $get_db_body !== null ) {
		$use_style  = '<style type="text/css">' . "\n";
		$use_style .= stripslashes( $get_db_body ) .  "\n";
		$use_style .= '</style>' . "\n";
	}
	echo $use_style;
}
/*============================
	「フォーム外観」CSSをプレビューに適用
 ============================*/
function feas_apply_css_to_preview( $manag_no = 0 ) {
	global $pv_css, $style_body_key;
	
	$applycss = null;

	$applycss = get_option( 'feadvns_style_body' );
	$applycss = get_option( $style_body_key . $manag_no );
	print '<style type="text/css">' . "\n";
	print stripslashes( $applycss ) . "\n";
	print '</style>' . "\n";
}

/*============================
	ORDER BY時に呼ばれる関数
 ============================*/
function custom_order_by( $order_by ) {
//	global $wpdb, $feadvns_search_b_label, $manag_no;

	return $ret_order;
}

/*============================
	ajax_filteringで結果を返す
 ============================*/
function feas_retrun_child( $parent_id = 0, $get_manag_no = 0, $form_no = 0 ) {
	global $wpdb, $manag_no, $feadvns_search_target, $feadvns_include_sticky, $feadvns_search_b_label, $cols, $feadvns_exclude_id, $feadvns_default_cat;
	
	$exids = null;
	$taxonomy = false;
	$retrun_list = array();
	$target_sp = $excat = $sp = '';
	
	if ( isset( $_GET['parent'] ) && ( $_GET['parent'] != null ) ) {
				
		$parent_id = intval( $_GET['parent'] );
		
		if ( isset( $_GET['manag_no'] ) )
			$manag_no = intval( $_GET['manag_no'] );
		if ( isset( $_GET['form_no'] ) )
			$form_no = intval( $_GET['form_no'] );
		if ( isset( $_GET['depth'] ) )
			$depth = intval( $_GET['depth'] );
			
		// 保存データ取得
		$get_data = get_db_save_data();
		$get_data = $get_data[$form_no];
			
		/**
		 *	検索対象のpost_typeを取得
		 */
		$target_pt_tmp = get_option( $feadvns_search_target . $manag_no );
		if ( $target_pt_tmp ) {
			$target_pt = "'" . implode( "','", (array) $target_pt_tmp ) . "'";
		} else {
			$target_pt = "'post'";
		}

		/**
		 *  投稿ステータス
		 */
		if ( in_array( 'attachment', (array) $target_pt_tmp ) ) {
			$post_status = "'publish', 'inherit'";
		} else {
			$post_status = "'publish'";
		}
			
		/**
		 *	固定記事(Sticky Posts)を検索対象から省く設定の場合、カウントに含めない
		 */
		$target_sp = get_option( $feadvns_include_sticky . $manag_no );
		if ( 'yes' != $target_sp ) {
			
			$sticky = get_option( 'sticky_posts' );
			if ( ! empty( $sticky ) ) {
				$sp = array_merge( $sp, $sticky ); // 除外IDにマージ
			}
		}
		
		/**
		 *	除外する記事ID
		 */
		$exclude_id = get_option( $feadvns_exclude_id . $manag_no );
		if ( $exclude_id ) {
			$sp = array_merge( $sp, $exclude_id ); // 除外IDにマージ
		}

		/**
		 *	固定タクソノミ／タームの設定を取得
		 */
		$fixed_term = get_option( $feadvns_default_cat . $manag_no );

		/**
		 *	カテゴリ毎の件数を表示する/しないの設定を取得
		 */
		//$showcnt = get_option( $feadvns_show_count . $manag_no );
		
		/**
		 *	0件のタームを表示しない設定の場合
		 */
/*
		if ( isset( $get_data[$cols[14]] ) && $get_data[$cols[14]] == 'no' ) {
			$nocnt = true;
		}
*/

		/**
		 *	タクソノミのトップ階層の場合
		 */
/*
		if ( substr( $get_data[$cols[2]], 0, 4 ) == "par_" ) {
			
			// タクソノミ名を指定
			$taxonomy = substr( $get_data[$cols[2]], 4, strlen( $get_data[$cols[2]] ) - 4 );
			
			// parentとして0を代入
			$get_data[$cols[2]] = 0;
		}
*/
		
		/**
		 *	除外タームIDが設定されている場合
		 */
		if ( isset( $get_data[$cols[11]] ) && $get_data[$cols[11]] != '' ) {
			$exids = implode( ',', (array) $get_data[$cols[11]] );
		}
		
		/**
		 *	除外IDをカンマ区切りにする
		 */
		if ( $sp ) {
			$sp = implode( ',', $sp );
		}

		/**
		 *	条件内の並び順
		 */
		$order_by = " t.term_id ASC ";
			
		if ( isset( $get_data[$cols[5]] ) ) {
				
			switch ( $get_data[$cols[5]] ) {
				
				case 'c':
					$order_by = " t.term_id ";
					break;
				case 'd':
					$order_by = " t.name ";
					break;
				case 'e':
					$order_by = " t.slug ";
					break;
				case 'f':
					$order_by = " t.term_order ";
					break;
				case 'g':
					$order_by = " RAND() ";
					break;
				default:
					$order_by = " t.term_id ";
					break;
			}
			// 'b'（自由記述）については712行目〜にて
		}
		
		/**
		 *	条件内の並び順 昇順/降順
		 */
		$order = " ASC";
		
		if ( isset( $get_data[$cols[35]] ) ) {
			switch ( $get_data[$cols[35]] ) {
				
				case 'asc':
					$order = " ASC";
					break;
				case 'desc':
					$order = " DESC";
					break;
				default:
					$order = " ASC";
					break;
			}	
		}

		// 「要素内の並び順」が「自由記述」の場合は、ターム一覧をDBから呼び出す代わりに記述内容で配列get_catsを構成
		if ( 'b' === $get_data[$cols[5]] ) {
	
			$options = $get_data[$cols[36]];
							
			if ( ! empty( $options ) ) {
					
				$get_cats = array();
							
				// 行数分ループを回す
				for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {
					
					if ( empty( $options[$i] ) )
						continue;
						
					$get_cats[$i] = new stdClass();
					
					// 値
					$get_cats[$i]->term_id = $options[$i]['value'];
					
					// 表記
					$get_cats[$i]->name = $options[$i]['text'];
					
					// 階層
					$get_cats[$i]->depth = $options[$i]['depth'];
				}			
			}
		} 
			
		// 「自由記述」以外
		else {
					
			/**
			 *	キャッシュから取得／ない場合は実行してキャッシュ保存
			 */
			if ( false === ( $get_cats = feas_cache_judgment( $manag_no, 'taxonomy', $parent_id ) ) ) {
					
				// ターム一覧を取得
				$sql  = " SELECT t.term_id, t.name FROM {$wpdb->terms} AS t";
				$sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
				$sql .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id";
				//$sql .= " LEFT JOIN {$wpdb->posts} AS p ON tr.object_id = p.ID";
				$sql .= " WHERE tt.parent = " . esc_sql( $parent_id ); 
				if ( $taxonomy ) $sql .= " AND tt.taxonomy = '" . esc_sql( $taxonomy ) . "'";
				if ( $exids )    $sql .= " AND t.term_id NOT IN (" . esc_sql( $exids ) . ")";
				//$sql .= " AND p.post_type IN( {$target_pt} )";
				$sql .= " GROUP BY t.term_id";
				$sql .= " ORDER BY " . $order_by . $order;
				$get_cats = $wpdb->get_results( $sql );
								
				feas_cache_create( $manag_no, 'taxonomy', $parent_id, $get_cats );
			}
		}

		/**
		 *	件数を取得してキャッシュ保存
		 */	
		if ( $get_cats ) {
			
			$term_cnt = array();
			foreach( $get_cats as $val ) {
									
				if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'term_cnt_' . $val->term_id, false ) ) ) {	
					$sql  = " SELECT count( p.ID ) AS cnt FROM {$wpdb->posts} AS p";
					$sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
					if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr2 ON p.ID = tr2.object_id";
					$sql .= " WHERE 1=1";
					if ( $sp ) $sql .= " AND p.ID NOT IN ( $sp )";
					$sql .= " AND tr.term_taxonomy_id = " . esc_sql( $val->term_id );
					if ( $fixed_term ) $sql .= " AND tr2.term_taxonomy_id = " . esc_sql( $fixed_term );
					$sql .= " AND p.post_type IN( $target_pt )";
					$sql .= " AND p.post_status IN ( {$post_status} )";
							
					$cnt = $wpdb->get_row( $sql );
					feas_cache_create( $manag_no, 'term_cnt_' . $val->term_id, false, $cnt );
				}
								
				$retrun_list[] = array( 'name' => $val->name , 'id' => $val->term_id , 'count' => $cnt->cnt );
			}
		}
			
		if ( 1 > count( $retrun_list ) )
			$retrun_list = false;

		@header( 'Content-Type: application/json; charset=' . get_bloginfo( 'charset' ) );
		echo json_encode( $retrun_list );
		exit;
	}
}

/**
 * make preview of iframe.
 * iframeでのプレビューを作る
 *
 * @echo html
 */
function feas_print_preview() {
	global $pv_theme_css, $pv_css;
		
	if ( !isset( $_GET['feas_pv'] ) ) { return; }
								
	$pv_mng_no = intval( $_GET['feas_mng_no'] );
	$pv_type   = $_GET['feas_pv_type'];
	
	header( "Content-Type: text/html; charset=UTF-8" );
	header( "Expires: Thu, 01 Dec 1994 16:00:00 GMT" );
	header( "Last-Modified: ". gmdate("D, d M Y H:i:s" ). " GMT" );
	header( "Cache-Control: no-cache, must-revalidate" );
	header( "Cache-Control: post-check=0, pre-check=0", false );
	header( "Pragma: no-cache" );
	?>
	<html>
		<head>
			<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
			<script type="text/javascript" src="<?php bloginfo( 'wpurl' ); ?>/wp-admin/load-scripts.php?c=1&amp;load%5B%5D=jquery-core,jquery-migrate,utils"></script>
			<script type="text/javascript" src="<?php echo plugins_url() . '/' . str_replace( basename(__FILE__), "", plugin_basename(__FILE__) ) . 'ajax_filtering.js'; ?>"></script>
			<style>
			html{overflow-y: scroll;}
			body{margin:0;}
			</style>
			<?php
			// テーマのCSSを読み込むかどうか
			$load_flag = get_option( $pv_theme_css . $pv_mng_no );
			if ( 'yes' === $load_flag ) {
				?>
				<link rel='stylesheet' href='<?php echo get_stylesheet_uri(); ?>' type='text/css' media='all' />
				<?php
			}
			// 「デザイン」のCSSを読み込むかどうか
			$load_flag = get_option( $pv_css . $pv_mng_no );
			if ( 'yes' === $load_flag ) {
				feas_apply_css_to_preview( $pv_mng_no ); 
			} 	
			?>
		</head>
		<body>
			<div id="feas-<?php echo $pv_mng_no; ?>">
				<div id="feas-form-<?php echo $pv_mng_no; ?>">
					<?php if ( 'search' == $pv_type ) { ?>
						
						<?php create_searchform( $pv_mng_no ); ?>
					
					<?php } else if ( 'sort' == $pv_type ) { ?>
						
						<div id="feas-sort-menu"><?php feas_sort_menu( $pv_mng_no ); ?></div>
						
					<?php } else {} ?>
				</div>
			</div>
		</body>
	</html>
<?php
	exit;
}

/*============================
	メッセージ
 ============================*/	
function feas_admin_notices() {
	
	$messages    = get_transient( 'feas_message' );
	$notice_flag = get_transient( 'feas_message_notice_flag' );
	
	if ( 'updated' == $notice_flag ) {
		$add_class = 'updated';
	} else {
		$add_class = 'error';
	}
	
	if ( $messages ) {
	?>
		<div id="message" class="<?php echo esc_attr( $add_class ); ?>">
			<ul>
				<?php foreach ( $messages as $message ) { ?>
					<li><?php echo esc_html( $message ); ?></li>
				<?php } ?>
			</ul>
		</div>
	<?php
	}
}

/*============================
	キャッシュ判定
 ============================*/
function feas_cache_judgment( $manag_no, $location = false, $parent_id ) {

/*
	ToDo:
	すべてのキャッシュを一括で呼び出す
*/	
	global $feas_cache_enable, $cols, $cols_transient;
	
	//if ( !in_array( $location, $cols_transient ) )
	//	return false;
	
	if ( get_option( $feas_cache_enable ) == 'enable' ) {
		
		if ( $parent_id ) {
			if ( false === ( $output_form = get_transient( $cols[23] . $manag_no . '_' . $location . '_' . $parent_id ) ) )
				return false;
		// ターム別カウント数など、検索パーツに無関係にキャッシュ保持）
		} else {
			if ( false === ( $output_form = get_transient( $cols[23] . $manag_no . '_' . $location ) ) )
				return false;			
		}
		return $output_form;
	
	} else {
		return false;
	}
}

/*============================
	キャッシュ作成
 ============================*/
function feas_cache_create( $manag_no, $location = false, $parent_id, $output_form ) {
	
	global $feas_cache_enable, $cols, $cols_transient, $feas_cache_time;
	
	if ( 'enable' != get_option( $feas_cache_enable ) )
		return;
	
	if ( isset( $output_form ) ) {
		if ( $parent_id ) {
			set_transient( $cols[23] . $manag_no . '_' . $location . '_' . $parent_id, $output_form, intval( get_option( $feas_cache_time ) ) );
		// ターム別カウント数など、検索パーツに無関係にキャッシュ保持）
		} else {
			set_transient( $cols[23] . $manag_no . '_' . $location, $output_form, intval( get_option( $feas_cache_time ) ) );			
		}
	}
	
	return;
}

/*============================
	DBから保存データを取得
 ============================*/
function get_db_save_data() {
	
	global $wpdb, $cols, $feadvns_max_line, $manag_no;

	$line_cnt = get_option( $feadvns_max_line . $manag_no );

	if ( $line_cnt == 0 ) {
		$line_cnt = $line_cnt + 1;
	}

	$cnt_cols = count( $cols );

	$get_data = array();
	for ( $i_line = 0; $i_line < $line_cnt; $i_line++ ) {
		for ( $i_col = 0; $i_col < $cnt_cols; $i_col++ ) {
			$s_key = $cols[$i_col] . $manag_no . "_" . $i_line;
			$get_data[$i_line][$cols[$i_col]] = get_option( $s_key );
		}
	}

	return $get_data;
}

/*============================
	保存データを並び替え
 ============================*/
function sort_db_save_data( $get_data = array() ) {

	//ソート
	/*$order_date = array();
	foreach ($get_data as $v){
		$order_date[] = $v[$cols[$i_col]];
	}
	array_multisort($order_date, SORT_ASC, $get_data);*/

	return $get_data;
}

/*============================
	除外項目を反映した各ターム毎の記事カウントをあらかじめ算出
 ============================*/	
function feas_reculc_term_cnt( $manag_no ) {
/*
	$sql = 'SELECT *, count(object_id) AS cnt
			FROM `wp_term_relationships`
			WHERE object_id NOT IN ( 468 )
			GROUP BY term_taxonomy_id';
*/
}

/*============================
	カスタムフィールドの値を取得
 ============================*/	
function feas_get_cf_value_list( $meta_key ) {
	global $wpdb, $manag_no;
	
	if ( ! $meta_key ) 
		return;
	
	if ( false === ( $meta_values = feas_cache_judgment( 'meta_values_of_' . $meta_key, $manag_no ) ) ) {
	
		$sql  = "SELECT DISTINCT meta_value ";
		$sql .= "FROM {$wpdb->postmeta} ";
		$sql .= "WHERE 1=1 ";
		$sql .= "AND meta_value IS NOT NULL ";
		$sql .= "AND meta_key = '" . esc_sql( $meta_key ) . "' ";
		$sql .= "ORDER BY meta_id ";
		$meta_values = $wpdb->get_results( $sql, ARRAY_A );
		
		feas_cache_create( 'meta_values_of_' . $meta_key, $manag_no, $meta_values );
	}		
	return $meta_values;
}
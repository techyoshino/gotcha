<?php
/*============================
	ソート表示用関数
 ============================*/
function custom_sort( $order = null ) {
	global $wpdb, $wp_query, $cols, $cols_order, $manag_no, $feadvns_sort_target, $feadvns_sort_order, $meta_sort_key, $feadvns_sort_target_cfkey_as, $feadvns_sort_target_2nd, $feadvns_sort_order_2nd, $feadvns_sort_target_cfkey_2nd, $feadvns_sort_target_cfkey_as_2nd;
	
	// FEASによる検索ではない場合
	if ( ! ( isset( $_GET['csp'] ) && $_GET['csp'] == "search_add" ) ) {
		return $order;
	}

	if ( isset( $_GET['fe_form_no'] ) && is_numeric( $_GET['fe_form_no'] ) ) {
		$manag_no = intval( $_GET['fe_form_no'] );
	} else {
		$manag_no = 0;
	}

	$sTarget = null;
	
	/**
	 *	ターゲットを取得
	 */
	if ( isset( $_GET['s_target'] ) ) {
		$sTarget = $_GET['s_target'];
	} else {
		$sTarget = get_option( $feadvns_sort_target . $manag_no );
	}
	
	$get_sort_data = null;
	
	/**
	 *	並び順を取得
	 */
	if ( isset( $_GET['s_order'] ) == true ) {
		$get_sort_data = $_GET['s_order'];
	} else {
		$get_sort_data = get_option( $feadvns_sort_order . $manag_no );
	}

	/**
	 *	並び順 - SQL用
	 */
	$get_sort = 'DESC';
	if ( isset( $get_sort_data ) == true && $get_sort_data != null ) {
		if ( 'up' == $get_sort_data || 'asc' == $get_sort_data ) {
			$get_sort = 'ASC';
		}
	}
		
	$get_cfkey_as = '';

	/**
	 *	カスタムフィールド - 数値か文字か
	 */
	if ( isset( $_GET['csfk_as'] ) ) {
		$get_cfkey_as = $_GET['csfk_as'];
	} else {	
		$get_cfkey_as = get_option( $feadvns_sort_target_cfkey_as . $manag_no );
	}

	/**
	 *	ターゲットを取得（第二条件）
	 */
	if ( isset( $_GET['s_target_2'] ) ) {
		$sTarget_2nd = $_GET['s_target_2'];
	} else {
		$sTarget_2nd = get_option( $feadvns_sort_target_2nd . $manag_no );
	}
	
	$get_sort_data_2nd = null;
	
	/**
	 *	並び順を取得（第二条件）
	 */
	if ( isset( $_GET['s_order_2'] ) ) {
		$get_sort_data_2nd = $_GET['s_order'];
	} else {
		$get_sort_data_2nd = get_option( $feadvns_sort_order_2nd . $manag_no );
	}

	/**
	 *	並び順 - SQL用（第二条件）
	 */
	$get_sort_2nd = 'DESC';
	if ( isset( $get_sort_data ) == true && $get_sort_data != null ) {
		if ( 'up' == $get_sort_data_2nd || 'asc' == $get_sort_data_2nd ) {
			$get_sort_2nd = 'ASC';
		}
	}
		
	$get_cfkey_as_2nd = '';

	/**
	 *	カスタムフィールド - 数値か文字か（第二条件）
	 */
	if ( isset( $_GET['csfk_as_2'] ) ) {
		$get_cfkey_as_2nd = $_GET['csfk_as_2'];
	} else {	
		$get_cfkey_as_2nd = get_option( $feadvns_sort_target_cfkey_as_2nd . $manag_no );
	}
	
	$sort_order = '';
	
	if ( false !== $sTarget ) {	
		
		switch( $sTarget ) {		
			
			case "post_date": // 投稿日
				$sort_order  = "{$wpdb->posts}.post_date $get_sort";
				break;
				
			case "post_title": // 投稿タイトル
				$sort_order  = "{$wpdb->posts}.post_title $get_sort";
				break;
				
			case "post_name": // 投稿スラッグ
				$sort_order  = "{$wpdb->posts}.post_name $get_sort";
				break;
			
			case "post_meta": // カスタムフィールド
				$sort_order  = "cf IS NULL ASC, ";
				$sort_order .= "cf ASC, ";
				if ( 'int' == $get_cfkey_as )
					$sort_order .= "LPAD( cf_v, 15, '0' )+0 $get_sort"; // 桁を合わせる
				else
					$sort_order .= "cf_v $get_sort";
				break;
			
			case "rand": // ランダム
				$sort_order = " RAND()";
/*
				$nowH = date_i18n( 'm' );
				if ( $nowH > 16 ) {
					$seed = strtotime( date( 'Y-m-d 16:00:00' ) );
				} elseif  ( $nowH > 8 ) {
					$seed = strtotime( date( 'Y-m-d 8:00:00' ) );
				} elseif  ( $nowH > 0 ) {
					$seed = strtotime( date( 'Y-m-d 0:00:00' ) );
				}
				mt_srand( $seed );
				$sort_order .= " RAND(" . mt_rand() . ")";
*/
				break;
						
			default:
				$sort_order = "{$wpdb->posts}.post_date DESC";
				break;
		}
		
	} else {
		return $order;
	}

	if ( false !== $sTarget_2nd ) {	
		
		switch( $sTarget_2nd ) {		
			
			case "post_date": // 投稿日
				$sort_order .= ", {$wpdb->posts}.post_date $get_sort_2nd";
				break;
				
			case "post_title": // 投稿タイトル
				$sort_order .= ", {$wpdb->posts}.post_title $get_sort_2nd";
				break;
				
			case "post_name": // 投稿スラッグ
				$sort_order .= ", {$wpdb->posts}.post_name $get_sort_2nd";
				break;
			
			case "post_meta": // カスタムフィールド
				$sort_order .= ", cf2 IS NULL ASC";
				$sort_order .= ", cf2 ASC";
				if ( 'int' == $get_cfkey_as_2nd ) {
					$sort_order .= ", LPAD( cf_v2, 15, '0' )+0 $get_sort_2nd"; // 桁を合わせる
				} else {
					$sort_order .= ", cf_v2 $get_sort_2nd";
				}
				break;
			
			case "rand": // ランダム
				$sort_order .= ", RAND()";
/*
				$nowH = date_i18n( 'G' );
				if ( $nowH > 16 ) {
					$seed = strtotime( date( 'Y-m-d 16:00:00' ) );
				} elseif  ( $nowH > 8 ) {
					$seed = strtotime( date( 'Y-m-d 8:00:00' ) );
				} elseif  ( $nowH > 0 ) {
					$seed = strtotime( date( 'Y-m-d 0:00:00' ) );
				}
				mt_srand( $seed );
				$sort_order .= ", RAND(" . mt_rand() . ")";
*/
				break;
			
			case "none": // なし
				break;
						
			default:
				$sort_order .= ", {$wpdb->posts}.post_date DESC";
				break;
		}
	}
	
	return $sort_order;
}


/*============================
	ソート用に連結する
 ============================*/
function join_datas( $get_data = null ) {
    global $wpdb, $feadvns_sort_target, $manag_no;
    
    if ( ! ( isset( $_GET['csp'] ) && $_GET['csp'] == "search_add" ) ) {
        return $get_data;
    }
     
    $get_date = new stdClass;
     
    // ターゲットを取得
    if ( isset( $_GET['s_target'] ) ) {
        
        $get_date->option_value = $_GET['s_target'];
    
    } else {
        $sql  = " SELECT * FROM {$wpdb->options}";
        $sql .= " WHERE option_name ='" . esc_sql( $feadvns_sort_target . $manag_no ) . "'";
        $sql .= " LIMIT 1 ";
        $get_date = $wpdb->get_results( $sql );
        if ( $get_date ) {
            $get_date = $get_date[0];
        }
    }
      
    $join_ret = '';
     
    if ( isset( $get_date->option_value ) && $get_date->option_value != null ) {
        switch ( $get_date->option_value )  {       
            case "post_date":
            case "post_title":
            case "post_name": 
                break;
            case "post_meta":
                $join_ret .= " LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.id = {$wpdb->postmeta}.post_id";
                break;
            case "rand":
                break;
        }
    }
/*
    $join_ret .= " LEFT JOIN " .$wpdb->postmeta ." ON " .$wpdb->posts .".id = " .$wpdb->postmeta .".post_id";
    $join_ret .= " LEFT JOIN " .$wpdb->term_relationships ." ON " .$wpdb->posts .".id = " .$wpdb->term_relationships .".object_id";
    $join_ret .= " LEFT JOIN " .$wpdb->term_taxonomy ." ON " .$wpdb->term_relationships .".term_taxonomy_id = " .$wpdb->term_taxonomy .".term_taxonomy_id";
    $join_ret .= " LEFT JOIN " .$wpdb->terms ." ON " .$wpdb->term_taxonomy .".term_id = " .$wpdb->terms .".term_id";
*/   
    return $join_ret;
}

/*============================
	GROUP BYをする
 ============================*/
function groupby_datas( $get_data = null ) {
	global $wpdb;
	
	if ( ! ( isset( $_GET['csp'] ) && $_GET['csp'] == 'search_add' ) ) {
		return $get_data;
	}

	$group_ret = $wpdb->posts .".ID";
	//$group_ret = "p.ID";
	
	return $group_ret;
}

/*============================
	カスタムフィールド用のサブクエリ
 ============================*/
function sort_add_field( $get_sql_data = null ) {
	global $wpdb, $manag_no, $feadvns_sort_target, $feadvns_sort_target_cfkey, $feadvns_sort_target_2nd, $feadvns_sort_target_cfkey_2nd;
	
	$or_sort = '';
	
	if ( ! ( isset( $_GET[ 'csp' ] ) && "search_add" === $_GET[ 'csp' ] ) ) {
		return $get_sql_data;
	}
	
	if ( isset( $_GET['s_target'] ) && ! is_null( $_GET['s_target'] ) ) {
	
		// ユーザーソートのターゲットを取得
		$sortTarget1st = $_GET['s_target'];
	
	} else {
		
		// 「検索結果の並び順」第一条件取得
		$sortTarget1st = get_option( $feadvns_sort_target . $manag_no );		
	}
	
	// 「検索結果の並び順」第二条件取得
	$sortTarget2nd = get_option( $feadvns_sort_target_2nd . $manag_no );
	
	if ( 'post_meta' === $sortTarget1st || 'post_meta' === $sortTarget2nd ) {
		
		// post_metaが指定されている場合のみサブクエリ実行してcf、cf_v生成
		if ( 'post_meta' === $sortTarget1st ) {
			
			// ユーザーソートのキー
			if ( isset( $_GET['csfk'] ) && ! is_null( $_GET['csfk'] ) ) {
				$sortKey = $_GET['csfk'];
			
			// 初期
			} else {
				// ソートのキーを取得
				$sortKey = $feadvns_sort_target_cfkey . $manag_no;
				$sortKey = get_option( $sortKey );
			}
			
			// 指定キーのある記事 →　ない記事の順
			$or_sort  = " ( SELECT if ( pm1.meta_key != '" . esc_sql( $sortKey ) . "', 1, 0 ) FROM {$wpdb->posts} AS p1";
			$or_sort .= " LEFT JOIN {$wpdb->postmeta} AS pm1 ON p1.ID = pm1.post_id";
			$or_sort .= " WHERE p1.ID = {$wpdb->posts}.ID";
			$or_sort .= " AND pm1.meta_key = '" . esc_sql( $sortKey ) . "'";
			$or_sort .= " LIMIT 1";
			$or_sort .= " ) AS cf,";
			
			$or_sort .= " ( SELECT if ( pm2.meta_key !='" . esc_sql( $sortKey ) . "', NULL, REPLACE( pm2.meta_value, ',', '' ) )" . " FROM {$wpdb->posts} AS p2";
			$or_sort .= " LEFT JOIN {$wpdb->postmeta} AS pm2 ON p2.ID = pm2.post_id";
			$or_sort .= " WHERE p2.ID = {$wpdb->posts}.ID";
			$or_sort .= " AND pm2.meta_key = '" . esc_sql( $sortKey ) . "'";
			$or_sort .= " LIMIT 1";
			$or_sort .= " ) AS cf_v";
		}
		
		// post_metaが指定されている場合のみサブクエリ実行してcf、cf_v生成
		if ( 'post_meta' === $sortTarget2nd ) {	
		
			// ソートのデータを取得
			$sortKey2 = $feadvns_sort_target_cfkey_2nd . $manag_no;
			$sortKey2 = get_option( $sortKey2 );
			
			// 指定キーのある記事 →　ない記事の順
			if ( $or_sort ) $or_sort .= ",";
			$or_sort .= " ( SELECT if ( pm3.meta_key != '" . esc_sql( $sortKey2 ) . "', 1, 0 ) FROM {$wpdb->posts} AS p3";
			$or_sort .= " LEFT JOIN {$wpdb->postmeta} AS pm3 ON p3.ID = pm3.post_id";
			$or_sort .= " WHERE p3.ID = {$wpdb->posts}.ID";
			$or_sort .= " AND pm3.meta_key = '" . esc_sql( $sortKey2 ) . "'";
			$or_sort .= " LIMIT 1";
			$or_sort .= " ) AS cf2,";
			
			$or_sort .= " ( SELECT if ( pm4.meta_key !='" . esc_sql( $sortKey2 ) . "', NULL, REPLACE( pm4.meta_value, ',', '' ) )" . " FROM {$wpdb->posts} AS p4";
			$or_sort .= " LEFT JOIN {$wpdb->postmeta} AS pm4 ON p4.ID = pm4.post_id";
			$or_sort .= " WHERE p4.ID = {$wpdb->posts}.ID";
			$or_sort .= " AND pm4.meta_key = '" . esc_sql( $sortKey2 ) . "'";
			$or_sort .= " LIMIT 1";
			$or_sort .= " ) AS cf_v2";
		}	
		
		$get_sql_data = str_replace( $wpdb->posts . ".* FROM {$wpdb->posts}", "{$wpdb->posts}.*, $or_sort FROM {$wpdb->posts}", $get_sql_data );
	}

		// ======================== 未使用 ======================== 
/*
		if ( $get_date->option_value == "tag" ) {
			
			// 並び順を取得
			if ( isset( $_GET['s_order'] ) == true )
				$get_sort_data->option_value = $_GET['s_order'];
			else
			{
				$sql  = " SELECT * FROM " .$wpdb->options;
				$sql .= " WHERE option_name = '" .$feadvns_sort_order .$manag_no ."'";
				$sql .= " LIMIT 1";
				$get_sort_data = $wpdb->get_results( $sql );
			}

			// 降順昇順を取得
			$get_sort = " DESC ";
			if ( isset( $get_sort_data->option_value ) == true && $get_sort_data->option_value != null )
			{
				if ( $get_sort_data->option_value == "up" )
					$get_sort = " ASC ";
			}

			$or_sort  = "( SELECT " .$wpdb->term_taxonomy .".taxonomy FROM " .$wpdb->posts ." AS pt ";
			$or_sort .= " LEFT JOIN " .$wpdb->postmeta ." ON pt.id = " .$wpdb->postmeta .".post_id";
			$or_sort .= " LEFT JOIN " .$wpdb->term_relationships ." ON pt.id = " .$wpdb->term_relationships .".object_id";
			$or_sort .= " LEFT JOIN " .$wpdb->term_taxonomy ." ON " .$wpdb->term_relationships .".term_taxonomy_id = " .$wpdb->term_taxonomy .".term_taxonomy_id";
			$or_sort .= " LEFT JOIN " .$wpdb->terms ." ON " .$wpdb->term_taxonomy .".term_id = " .$wpdb->terms .".term_id";
			$or_sort .= " WHERE pt.id =" .$wpdb->posts .".id";
			$or_sort .= " ORDER BY " .$wpdb->term_taxonomy .".taxonomy DESC";
			$or_sort .= " LIMIT 1";
			$or_sort .= " ) AS tn,";

			$or_sort .= "( SELECT " .$wpdb->terms .".name FROM " .$wpdb->posts ." AS pt ";
			$or_sort .= " LEFT JOIN " .$wpdb->postmeta ." ON pt.id = " .$wpdb->postmeta .".post_id";
			$or_sort .= " LEFT JOIN " .$wpdb->term_relationships ." ON pt.id = " .$wpdb->term_relationships .".object_id";
			$or_sort .= " LEFT JOIN " .$wpdb->term_taxonomy ." ON " .$wpdb->term_relationships .".term_taxonomy_id = " .$wpdb->term_taxonomy .".term_taxonomy_id";
			$or_sort .= " LEFT JOIN " .$wpdb->terms ." ON " .$wpdb->term_taxonomy .".term_id = " .$wpdb->terms .".term_id";
			$or_sort .= " WHERE pt.id =" .$wpdb->posts .".id";
			$or_sort .= " ORDER BY " .$wpdb->terms .".name " .$get_sort;
			$or_sort .= " LIMIT 1";
			$or_sort .= " ) AS tname";

			$get_sql_data = str_replace( $wpdb->posts .".* FROM ".$wpdb->posts, $wpdb->posts .".*, " .$or_sort ." FROM " .$wpdb->posts, $get_sql_data );
		}
*/
	
	return $get_sql_data;
}

/*============================
	ソート用並び替えを表示
 ============================*/
function feas_sort_menu( $id = 0, $shortcode_f = null ) {
	global $wpdb, $feadvns_max_line_order, $manag_order_no, $cols, $cols_order, $meta_sort_key;

	if ( is_numeric( $id ) ) {
		$id = absint( $id );
	} else {
		$id = 0;
	}
		
	$manag_order_no = $id;
	
	$keys = array_keys( $_GET );
	$get_st = null;
	
	for ( $i = 0, $cnt = count( $keys ); $i < $cnt; $i++ ) {
		if ( $keys[$i] != "s_target" && $keys[$i] != "s_order" ) {
			if ( $i > 0 ) {
				$get_st .= "&amp;";
			}
			
			// リストボックス形式の場合
			if ( is_array( $_GET[$keys[$i]] ) ) {
				for( $i_key = 0, $cnt_key = count( $_GET[$keys[$i]] ); $i_key < $cnt_key; $i_key++ ) {
					if ( $i_key > 0 ) {
						$get_st .= "&amp;";
					}
					$get_st .= $keys[$i] . "%5B%5D=" . urlencode( $_GET[$keys[$i]][$i_key] );
				}
			}
			// その他
			else {
				$get_st .= $keys[$i] . "=" . urlencode( $_GET[$keys[$i]] );
			}
		}
	}

	// オーダー番号取得
	$sql  = " SELECT option_name FROM {$wpdb->options}";
	$sql .= " WHERE option_name LIKE '" . $cols_order[2]. $manag_order_no ."_"."%'";
	$sql .= " ORDER BY option_value ASC";
	$get_op_sort = $wpdb->get_results( $sql );

	for ( $i = 0, $cnt = count( $get_op_sort ); $i < $cnt; $i++ ) {
		// 並び順取得
		$get_order[] = substr( $get_op_sort[$i]->option_name, -1 );
	}

	// ソートのデータを取得
	$line_key = $feadvns_max_line_order . $manag_order_no;
	$line_cnt = get_option( $line_key );
		
	// ソートデータ取得
	$ret_disp = null;
	for ( $i = 0; $i < $line_cnt; $i++ ) {
		
		// 「表示する」場合
		if ( '0' == get_option( $cols_order[1] . $manag_order_no . "_" . $i ) ) {
						
			$ret_disp .= str_replace( '\\', '', get_option( $cols_order[4] . $manag_order_no . '_' . $get_order[$i] ) );
			$ret_disp .= str_replace( '\\', '', get_option( $cols_order[6] . $manag_order_no . '_' . $get_order[$i] ) );
			
			$sort_btn_up   = str_replace( '\\', '', get_option( $cols_order[7] . $manag_order_no . '_' . $get_order[$i] ) );
			$sort_btn_down = str_replace( '\\', '', get_option( $cols_order[8] . $manag_order_no . '_' . $get_order[$i] ) );

			// カスタムフィールド
			if ( 'post_meta' == get_option( $cols_order[0] . $manag_order_no . '_' . $get_order[$i] ) ) {
				$get_meta_key = get_option( $cols_order[9] . $manag_order_no . '_' . $get_order[$i] );
				$get_csfk_as = get_option( $cols_order[10] . $manag_order_no . '_' . $get_order[$i] );

				$ret_disp .= "<span class='feas-sl-" . ( $i + 1 ) . "-up'><a href='" . esc_attr ( get_option( "home" ) ) . "/?" . esc_attr ( $get_st ) . "&amp;s_target=post_meta&amp;s_order=up&amp;csfk=" . esc_attr ( $get_meta_key ) . "&amp;csfk_as=" . esc_attr ( $get_csfk_as ) . "'>" . $sort_btn_up ."</a></span>";
				$ret_disp .= "<span class='feas-sl-" . ( $i + 1 ) . "-down'><a href='" . esc_attr ( get_option( "home" ) ) . "/?" . esc_attr ( $get_st ) . "&amp;s_target=post_meta&amp;s_order=down&amp;csfk=" . esc_attr ( $get_meta_key ) . "&amp;csfk_as=". esc_attr ( $get_csfk_as ) . "'>" . $sort_btn_down ."</a></span>";
			
			} else {
				
				$ret_disp .= "<span class='feas-sl-" . ( $i + 1 ) . "-up'><a href='" . esc_attr ( get_option( "home" ) ) . "/?" . esc_attr ( $get_st ) . "&amp;s_target=" . esc_attr ( get_option( $cols_order[0] . $manag_order_no . "_" . $get_order[$i] ) ) . "&amp;s_order=up'>" . $sort_btn_up . "</a></span>";
				$ret_disp .= "<span class='feas-sl-" . ( $i + 1 ) . "-down'><a href='" . esc_attr ( get_option( "home" ) ) . "/?" . esc_attr ( $get_st ) . "&amp;s_target=" . esc_attr ( get_option( $cols_order[0] . $manag_order_no . "_" .$get_order[$i] ) ) . "&amp;s_order=down'>" . $sort_btn_down . "</a></span>";
			}
			
			$ret_disp .= str_replace( '\\', '', get_option( $cols_order[5] . $manag_order_no . '_' . $get_order[$i] ) );
		}
	}

	if ( $shortcode_f == null ) {
		print( $ret_disp );
	} else {
		return $ret_disp;
	}
}

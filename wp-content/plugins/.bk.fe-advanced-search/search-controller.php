<?php
//////////////////////////////////////////////
//	検索本体
//////////////////////////////////////////////
function search_where_add( $where ) {
	
	global
		$wpdb,
		$wp_query,
		$cols,
		$manag_no,
		$add_where,
		$w_keyword,
		$feadvns_search_target,
		$manag_no,
		$feadvns_default_cat,
		$feadvns_empty_request,
		$feadvns_include_sticky,
		$feadvns_exclude_id,
		$feadvns_exclude_term_id;
		
	$add_where =
	$keywords  =
	$w_keyword =
	$exid =
	$ids_tmp =
	$ids =
	$cwhere =
	$target_pt = '';
	
	$kw_keys =
	$sp =
	$dcat =
	$get_cond = array();
		
	/*============================
		フォームNo.取得
	 ============================*/
	if ( isset( $_GET['fe_form_no'] ) == true && is_numeric( $_GET['fe_form_no'] ) ) {
		$manag_no = (int) $_GET['fe_form_no'];
	} else {
		$manag_no = 0;
	}
	
	/*============================
		FEASの場合のみクエリをカスタム
	 ============================*/
	if ( isset( $_GET['csp']) && $_GET['csp'] == "search_add" && $wp_query->is_main_query() ) {
	
		/**
		 *	条件数（行数）取得
		 */
		if ( is_array( $_GET['feadvns_max_line_' . $manag_no] ) ) {
			$max_c_cnt = 1;
		} else {
			$max_c_cnt = ( (int) $_GET['feadvns_max_line_' . $manag_no] + 1 );
		}
		
		/**
		 *	メインデータ取得
		 */
		$get_ret_data = create_where_get_data( $max_c_cnt, $keywords );
			
		/**
		 *	フリーワード検索とそれ以外を分離処理
		 */
		$add_where = $get_ret_data[0];
		$keywords  = $get_ret_data[1];
	
		/**
		 *	各キーワード検索ボックスの行数を取得
		 */
		if ( is_array( $keywords ) == true ) {
			$kw_keys = array_keys( $keywords );
		}
		
		/**
		 *	フリーワード入力があったら、検索窓の出現回数分キーワード検索を実行
		 */
		if ( $keywords ) {
			for ( $i_kw = 0, $kw_cnt = count( $keywords ); $i_kw < $kw_cnt; $i_kw++ ) {	
				if ( $keywords[$kw_keys[$i_kw]] != '' && $keywords[$kw_keys[$i_kw]] != '' && $keywords[$kw_keys[$i_kw]] != ' ' ) {
									
					// 検索対象を取得
					$kwds_target = get_option( $cols[13] . $manag_no . "_" . $kw_keys[$i_kw] );
					
					// ゆらぎ：全角半角の区別
					$kwds_yuragi = get_option( $cols[15] . $manag_no . "_" . $kw_keys[$i_kw] );
					
					// 検索実行
					$w_keyword .= create_where_keyword( $keywords[$kw_keys[$i_kw]], $kwds_target, $kwds_yuragi, $manag_no, $kw_keys[$i_kw], 'sql' );
				}
			}
		}
				
		/**
		 *	固定記事(Sticky Posts)を検索対象から省く設定の場合、カウントに含めない
		 */
		$target_sp = get_option( $feadvns_include_sticky . $manag_no );
		if ( 'yes' != $target_sp ) {
			$sticky = get_option( 'sticky_posts' );
			if ( $sticky != array() ) {
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
		 *	除外IDのSQLを構成
		 */
		if ( $sp ) {
			$sp = implode( ',', $sp );
			$exid = ' AND ' . $wpdb->posts . '.ID NOT IN (' . esc_sql( $sp ) . ')';
		}
		
		/**
		 *	初期設定カテゴリ取得
		 */
		if ( false === ( $default_taxonomy = feas_cache_judgment( $manag_no, 'default_taxonomy', 'global' ) ) ) {	
			
			$default_taxonomy = '';
			$dcat['cat'] = get_option( $feadvns_default_cat . $manag_no );
			$dcat['format'] = 'array';
									
			if ( '' != $dcat['cat'] ) {
				$default_taxonomy = create_where_single_cat( $dcat );
			}
			feas_cache_create( $manag_no, 'default_taxonomy', 'global', $default_taxonomy );
		}
			
		if ( $default_taxonomy ) {
			$cwhere = implode( ',', $default_taxonomy );		
			$cwhere = " AND {$wpdb->posts}.ID IN (" . esc_sql( $cwhere ) . ")";
		}

		/**
		 *	除外するタームID
		 */
		$exclude_post_ids = '';
		
		$exclude_term_id = get_option( $feadvns_exclude_term_id . $manag_no );
		if ( $exclude_term_id ) {
			$args['cat'] = $exclude_term_id;
			$args['format'] = 'array';
			$args['mode'] = 'exclude';
			$exclude_post_ids = create_where_single_cat( $args );
		}
		
		/**
		 *	除外タームのSQLを構成
		 */
		if ( $exclude_post_ids ) {
			$exclude_post_ids = implode( ',', $exclude_post_ids );
			$exclude_post_ids = " AND {$wpdb->posts}.ID NOT IN (" . esc_sql( $exclude_post_ids ) . ")";
		}
								
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
		 *	基本SQL文生成
		 */
		$where = " AND {$wpdb->posts}.post_type IN ( {$target_pt} ) AND {$wpdb->posts}.post_status IN ( {$post_status} )";
			
		/**
		 *	各要素を合体してWHERE文を構成
		 */
		if ( $add_where != '' || $w_keyword != '' ) {
		
			$ret_where = $exid . $exclude_post_ids . $add_where . $w_keyword . $cwhere . $where;
									
		} else {
			
			// 検索条件が指定されていなかった時に返す内容（初期カテゴリ or 0件）
			$ereq = get_option( $feadvns_empty_request . $manag_no );
			
			if ( '1' === $ereq ) {
				$ret_where = $where . $exid . $cwhere;
			} else {
				$ret_where = $where . " AND ( " . $wpdb->posts . ".ID = -9999)";
			}
		}
	
	} else {
		
		$ret_where = $where;
	}
	
	return $ret_where;
}

//////////////////////////////////////////////
//	where作成のためにデータを取得
//////////////////////////////////////////////
function create_where_get_data( $line_max, $keywords ) {
	global $wpdb, $cols, $manag_no, $feadvns_search_target, $feadvns_include_sticky, $feadvns_exclude_id;

	$archive_datas =
	$meta_datas =
	$term_datas = 
	$keywords = array();
	
	for ( $line_cnt = 0, $arc_cnt = 0, $term_cnt = 0, $meta_cnt = 0; $line_cnt < $line_max; $line_cnt++ ) {

		// 「形式」取得
		$get_ele_value = get_option( $cols[4] . $manag_no . "_" . $line_cnt );
		
		/*============================
			アーカイブ検索
		 ============================*/
		if ( 'archive' === get_option( $cols[2] . $manag_no . '_' . $line_cnt ) ) {
						
			// 初期化
			$archive_value = '';
			$range_value = '';
			$get_archives = array();

			/**
			 *	フリーキーワード
			 */
			if ( 'e' === $get_ele_value || '5' == $get_ele_value ) {
				
				if ( isset( $_GET['s_keyword_' . $line_cnt] ) && ! empty( $_GET['s_keyword_' . $line_cnt] ) ) {
					$keywords[$line_cnt] = $_GET['s_keyword_' . $line_cnt];
				}
			}
			else {		

				// バリュー取得
				if ( isset( $_GET['search_element_' . $line_cnt] ) && ! empty( $_GET['search_element_' . $line_cnt] ) ) {
					$archive_value = $_GET['search_element_' . $line_cnt];
				}
				
				// テキスト範囲検索のバリュー取得
				if ( isset( $_GET['range_by_text_' . $line_cnt] ) && ! empty( $_GET['range_by_text_' . $line_cnt] ) ) {
					$range_value = $_GET['range_by_text_' . $line_cnt];
				}
				
				/**
				 *	単一選択（ドロップダウン、ラジオボタン）
				 */
				if ( 'a' === $get_ele_value || 'd' === $get_ele_value || '1' == $get_ele_value || '4' == $get_ele_value ) {
					
					// 空の場合は処理を抜ける
					if ( empty ( $archive_value ) && empty ( $range_value ) )
						continue;
					
					// テキストの範囲検索
					if ( $range_value ) {
						
						$archive_datas[$arc_cnt]['date']   = (int) esc_sql( $range_value );
						$archive_datas[$arc_cnt]['number'] = $line_cnt;
					} 
					else if ( $archive_value ) {
						
						$archive_datas[$arc_cnt]['date'] = (int) esc_sql( $archive_value );
					}
					// テキスト or 単一選択の共通項目
					$archive_datas[$arc_cnt]['plural'] = '0'; // 複数選択？ yes =1
					$archive_datas[$arc_cnt]['range']    = get_option( $cols[16] . $manag_no . "_" . $line_cnt );
					$archive_datas[$arc_cnt]['range_as'] = get_option( $cols[29] . $manag_no . "_" . $line_cnt );
				}
				
				/**
				 *	複数選択（セレクトボックス、チェックボックス）
				 */
				else if ( 'b' === $get_ele_value || 'c' === $get_ele_value || '2' == $get_ele_value || '3' == $get_ele_value ) {

					// 空の場合は処理を抜ける
					if ( empty ( $archive_value[0] ) && empty ( $range_value ) )
						continue;
											
					if ( $archive_value ) {
						for ( $i = 0, $i_max = count( $archive_value ); $i < $i_max; $i++ ) {
							$get_archives[] = (int) esc_sql( $archive_value[$i] );
						}
						$archive_datas[$arc_cnt]['date']   = $get_archives;
						$archive_datas[$arc_cnt]['plural'] = '1'; // 複数選択？ yes =1
						$archive_datas[$arc_cnt]['and']    = get_option( $cols[6] . $manag_no . "_" . $line_cnt );
					}
				}
				// 単一 or 複数選択の共通項目
				$archive_datas[$arc_cnt]['format']   = 'sql';
				$archive_datas[$arc_cnt]['orderby']  = get_option( $cols[5] . $manag_no . '_' . $line_cnt );
				$archive_datas[$arc_cnt]['freetext'] = get_option( $cols[36] . $manag_no . '_' . $line_cnt );
				
				$arc_cnt++;
			}
		}
			
		/*============================
			カスタムフィールド検索
		 ============================*/
		elseif ( 'meta_' === mb_substr( get_option( $cols[2] . $manag_no . '_' . $line_cnt ), 0, 5 ) ) {
						
			// 初期化
			$meta_key    = '';
			$meta_value  = '';
			$range_value = '';
			$get_metas   = array();
			
			/**
			 *	フリーキーワード
			 */
			if ( 'e' === $get_ele_value || '5' == $get_ele_value ) {
				
				if ( isset( $_GET['s_keyword_' . $line_cnt] ) && ! empty( $_GET['s_keyword_' . $line_cnt] ) ) {
					$keywords[$line_cnt] = $_GET['s_keyword_' . $line_cnt];
				}
			}
			else {
				
				// キー取得　…前方５文字（meta_）を除去した文字列
				$meta_key = mb_substr( get_option( $cols[2] . $manag_no . '_' . $line_cnt ), 5, mb_strlen( get_option( $cols[2] . $manag_no . '_' . $line_cnt ) ) );
				
				// バリュー取得
				if ( isset( $_GET['search_element_' . $line_cnt] ) && ! empty( $_GET['search_element_' . $line_cnt] ) ) {				
					$meta_value = $_GET['search_element_' . $line_cnt];
				}
							
				// テキスト範囲検索のバリュー取得
				if ( isset( $_GET['cf_limit_keyword_' . $line_cnt] ) && ! empty( $_GET['cf_limit_keyword_' . $line_cnt] ) ) {			
					$range_value = $_GET['cf_limit_keyword_' . $line_cnt];
				}
								
				/**
				 *	単一選択（ドロップダウン、ラジオボタン）
				 */
				if ( 'a' === $get_ele_value || 'd' === $get_ele_value || '1' == $get_ele_value || '4' == $get_ele_value ) {
				
					// 空の場合は処理を抜ける
					if ( empty ( $meta_value ) && empty ( $range_value ) )
						continue;
						
					// フリーワードの範囲検索
					if ( $range_value ) {
						$meta_datas[$meta_cnt]['metas'] = esc_sql( $range_value );
					}
					elseif ( $meta_value ) {
						$meta_datas[$meta_cnt]['metas'] = esc_sql( $meta_value );
					}
					$meta_datas[$meta_cnt]['plural']   = '0'; // 複数選択？ yes =1
					$meta_datas[$meta_cnt]['range']    = get_option( $cols[16] . $manag_no . '_' . $line_cnt );
					$meta_datas[$meta_cnt]['range_as'] = get_option( $cols[29] . $manag_no . '_' . $line_cnt );
				}
				
				/**
				 *	複数選択（チェックボックス、セレクトボックス）
				 */
				else if ( 'b' === $get_ele_value || 'c' === $get_ele_value || '2' == $get_ele_value || '3' == $get_ele_value ) {
										
					// 空の場合は処理を抜ける
					if ( empty ( $meta_value[0] ) && empty ( $range_value ) )
						continue;
												
					if ( $meta_value ) {	
						for ( $i = 0, $i_max = count( $meta_value ); $i < $i_max; $i++ ) {
							$get_metas[] = esc_sql( $meta_value[$i] );
						}
						$meta_datas[$meta_cnt]['metas']  = $get_metas; // meta_valueの配列
						$meta_datas[$meta_cnt]['plural'] = '1'; // 複数選択？ yes =1
					}
				}
				// 単一 or 複数選択の共通項目
				$meta_datas[$meta_cnt]['key'] = $meta_key;
				$meta_datas[$meta_cnt]['and'] = get_option( $cols[6] . $manag_no . '_' . $line_cnt ); // 検索方法は？ or =0, and =1
				$meta_datas[$meta_cnt]['unit'] = get_option( $cols[17] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['kugiri'] = get_option( $cols[18] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['free_word'] = get_option( $cols[22] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['number'] = $line_cnt;
				$meta_datas[$meta_cnt]['shingi'] = get_option( $cols[24] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['shingi_txt'] = get_option( $cols[25] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['tani_position'] = get_option( $cols[26] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['format'] = 'sql';
				$meta_datas[$meta_cnt]['scf'] = get_option( $cols[33] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['orderby'] = get_option( $cols[5] . $manag_no . '_' . $line_cnt );
				$meta_datas[$meta_cnt]['freetext'] = get_option( $cols[36] . $manag_no . '_' . $line_cnt );
				
				$meta_cnt++;
			}
		}
		
		/*============================
			カテゴリ/タグ/ターム検索
		 ============================*/
		else {
			
			// 初期化
			$term_value = '';
			$get_terms  = array();
			$has_data = false;
			
			/**
			 *	フリーキーワード
			 */
			if ( 'e' === $get_ele_value || '5' == $get_ele_value) {
				
				if ( isset( $_GET['s_keyword_' . $line_cnt] ) && ! empty( $_GET['s_keyword_' . $line_cnt] ) ) {
					$keywords[$line_cnt] = $_GET['s_keyword_' . $line_cnt];
				}
			}
			else {
				
				// タームのデータ取得
				if ( isset( $_GET['search_element_' . $line_cnt] ) && ! empty( $_GET['search_element_' . $line_cnt] ) ) {
					$term_value = $_GET["search_element_" . $line_cnt];
				}
												
				// 配列の場合
				if ( is_array( $term_value) ) {
					foreach ( $term_value as $q ) {
						if ( empty( $q ) )
							continue;
						$has_data = true;
					}
					if ( ! $has_data )
						continue;
				}
												
				// 空の場合は処理を抜ける
				if ( empty( $term_value ) )
					continue;
														
				/**
				 *	単一選択（ドロップダウン、ラジオボタン）
				 */
				if ( 'a' === $get_ele_value || 'd' === $get_ele_value || '1' == $get_ele_value || '4' == $get_ele_value ) {
					
					if ( is_array( $_GET['search_element_' . $line_cnt] ) ) {
										
						// ajaxフィルタリング時の未選択プルダウン分の配列を削除
						$get_terms = array_filter( $term_value, 'strlen' );
						$get_terms = (int) end( $get_terms );
						$term_datas[$term_cnt]['cat'] = $get_terms;
																
					} else {			
						$term_datas[$term_cnt]['cat'] = $term_value;
					}
					$term_datas[$term_cnt]['plural'] = '0'; // 複数選択？ yes =1
				}
				
				/**
				 *	複数選択（セレクトボックス、チェックボックス）
				 */
				else if ( 'b' === $get_ele_value || 'c' === $get_ele_value || '2' == $get_ele_value || '3' == $get_ele_value ) {
												
					if ( $term_value ) {	
						for ( $i = 0, $i_max = count( $term_value ); $i < $i_max; $i++ ) {
							$get_terms[] = esc_sql( $term_value[$i] );
						}
						$term_datas[$term_cnt]['cat'] = $get_terms; // term_idの配列
						$term_datas[$term_cnt]['plural'] = '1'; // 複数選択？ yes =1
						$term_datas[$term_cnt]['and'] = get_option( $cols[6] . $manag_no . '_' . $line_cnt ); // 検索方法は？ OR検索 = 0 または a, AND検索 = 1 または b
					}
				}
				// 単一 or 複数選択の共通項目
				$term_datas[$term_cnt]['format'] = 'sql';
				$term_datas[$term_cnt]['mode'] = 'include';
				$term_datas[$term_cnt]['orderby'] = get_option( $cols[5] . $manag_no . '_' . $line_cnt );
				$term_datas[$term_cnt]['freetext'] = get_option( $cols[36] . $manag_no . '_' . $line_cnt );
				
				$term_cnt++;
			}
		}
	}
	
	/*============================
		アーカイブの検索本体、該当記事ID取得
	 ============================*/
	$add_where = create_where_archive( $archive_datas, $arc_cnt );

	/*============================
		カスタムフィールドの検索本体、該当記事ID取得
	 ============================*/
	$add_where .= create_where_meta( $meta_datas, $meta_cnt );
	
	/*============================
		カテゴリの検索本体、該当記事ID取得
	 ============================*/
	$add_where .= create_where_category( $term_datas, $term_cnt );
	
	$ret[0] = $add_where;
	$ret[1] = $keywords;

	return $ret;
}

//////////////////////////////////////////////
//	アーカイブ検索
//////////////////////////////////////////////
function create_where_archive( $datas, $arc_cnt ) {
	global $wpdb;
			
	$r_ret = null;
		
	if ( $datas ) {
		for ( $i = 0, $i_max = count( $datas ); $i < $i_max; $i++ ) {
	
			if ( $i > 0 )
				$r_ret .= " AND ";
	
			// 単一選択形式（ドロップダウン・ラジオボタン）
			if ( '0' == $datas[$i]['plural'] ) {
				$r_ret .= create_where_single_archive( $datas[$i] );
			}
			// 複数選択形式（セレクトボックス・チェックボックス）
			else {
				$r_ret .= create_where_plural_archive( $datas[$i] );
			}
		}
	}

	$ret = null;
	
	if ( null !== $r_ret ) {
		$ret = " AND {$r_ret}";
	}

	if ( null === $ret && 0 !== $arc_cnt ) {
		$ret .= " AND {$wpdb->posts}.ID = -9999";
	}
	
	return $ret;
}

//////////////////////////////////////////////
//	アーカイブ(年月)検索｜単一選択形式 (ドロップダウン・ラジオボタン)
//////////////////////////////////////////////
function create_where_single_archive( $datas = array() ) {
	global $wpdb, $cols, $manag_no;

	/**
	 *
	 *	検索本体
	 *	 
	 */
	 
			// 「201810」の形式以外はfalseを返す
			if ( ! ( is_numeric( $datas['date'] ) && 6 === strlen( $datas['date'] ) ) ) {
		
				$get_ids = false;
			}
			else {
				
				// データの準備
				$year       = esc_sql( substr( $datas['date'], 0, 4 ) );
				$month      = esc_sql( substr( $datas['date'], 4, strlen( $datas['date'] ) ) );
				$last_day   = esc_sql( date( 't', mktime( 0, 0, 0, $month, 1, $year ) ) );
				$next_year  = $year;
				$next_month = $month +1;
				
				// 年をまたぐ月の場合の調整
				if ( $month == 12 ) {
					$next_year  = $year +1;
					$next_month = 1;
				}
				
				// SQLの準備
				$sql  = " SELECT ID FROM {$wpdb->posts}";
				$sql .= " WHERE";
				
				switch ( (int) $datas['range'] ) {
					case 1:
						$sql .= " ( post_date < '{$year}-{$month}-01 00:00:00' )";
						break;
					case 2:
						$sql .= " ( post_date <= '{$year}-{$month}-{$last_day} 00:00:00' )";
						break;
					case 3:
						$sql .= " ( post_date >= '{$year}-{$month}-01 00:00:00' )";
						break;
					case 4:
						$sql .= " ( post_date > '{$year}-{$month}-{$last_day} 00:00:00' )";
						break;
					
					// 範囲検索しない = 当月のみ
					default:
						$sql .= " ( ";
						$sql .= " post_date >= '{$year}-{$month}-01 00:00:00'";
						$sql .= " AND ";
						$sql .= " post_date < '{$next_year}-{$next_month}-01 00:00:00' )";
						break;
				}
				// SQL実行
				$get_ids = $wpdb->get_results( $sql );
			}
	
	/**
	 *
	 *	検索条件をテンプレートに表示させるための準備
	 *	「項目内の並び順」が「自由形式」の場合は、値の代わりに記述した「表記」をテンプレートに出力する
	 *  
	 */
	
			// 範囲検索の場合の付加する語句
			switch ( (int) $datas['range'] ) {
				case 1:
					$rangeKey = "前";
					break;
				case 2:
					$rangeKey = "以前";
					break;
				case 3:
					$rangeKey = "以後";
					break;
				case 4:
					$rangeKey = "後";
					break;
				
				// 範囲検索しない = 単位なし
				default:
					$rangeKey = "";
					break;
			}
			
			$output_text = '';
				
			// 「自由記述」の場合、記述した「表記」を格納	
			if ( 'b' === $datas['orderby'] ) {
				
				$options = $datas['freetext'];
						
				if ( ! empty( $options ) ) {
						
					$term_list = array();
								
					// 行数分ループを回す
					for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {
						
						if ( empty( $options[$i] ) )
							continue;
						
						// 値
						$term_list[$i]['ym'] = $options[$i]['value'];
						
						// 表記
						$term_list[$i]['name'] = $options[$i]['text'];
						
						// 階層
						$term_list[$i]['depth'] = $options[$i]['depth'];
					}			
				}
		
				// 検索クエリと同じ値を持つ"表記"（=DB上に実在する値ではなく）をinsert_resultに代入する
				foreach ( $term_list as $key => $value ) {
					if ( $datas['date'] == $value['ym'] ) {
						$output_text = $value['name'] . $rangeKey;				
					}
				}
			
			} 
			
			// 「自由記述」以外は値に"年月"を添えて格納
			else {	
				$output_text = $year . "年" . (int) $month . "月" . $rangeKey;
			}
			
			insert_result( $output_text );
			
			// テキストでの範囲検索時、テキスト入力欄に戻すため
			if ( isset( $datas['number'] ) && ! empty( $datas['number'] ) ) {
				insert_kwds_result( $year . $month, $datas['number'] );
			}

	/**
	 *
	 *	リターン値の準備
	 *	SQL、配列、または-9999
	 *  
	 */
			
			$ret = null;
		
			if ( $get_ids ) {	
				
				for ( $i = 0, $i_max = count( $get_ids ); $i < $i_max; $i++ ) {
					
					// 配列で返す
					if ( isset( $datas['format'] ) && 'array' === $datas['format'] ) {
						
						$ret[] = $get_ids[$i]->ID;
						
					// SQLで返すためのカンマ繋ぎ
					} else {
						
						if ( '' != $ret )
							$ret .= ',';
						
						$ret .= esc_sql( $get_ids[$i]->ID );
					}
				}
				
				if ( isset( $datas['format'] ) && 'sql' === $datas['format'] ) {
					$ret = "{$wpdb->posts}.ID IN ({$ret})";
				}	
			}
			else {
				$ret = "{$wpdb->posts}.ID = -9999";
			}
	
	return $ret;
}

//////////////////////////////////////////////
//	アーカイブ(年月)検索｜複数選択形式 (セレクトボックス・チェックボックス)
//////////////////////////////////////////////
function create_where_plural_archive( $datas = array() ) {
	global $wpdb, $cols, $manag_no;
	
	// 初期化
	$mod_ids = array();
	$ret = '';
	
	// 年月データを抜き出す（配列）
	$get_date = $datas['date'];
	
	// チェックされたカテゴリ１つずつ、該当する記事IDを取得して配列$idsに格納
	for ( $i = 0, $i_max = count( $get_date ); $i < $i_max; $i++ ) {
			
		// 検索条件をテンプレートに表示させるため
		$output_text = '';
					
		// 「自由記述」の場合、記述したとおりに出力	
		if ( 'b' === $datas['orderby'] ) {
			
			$options = $datas['freetext'];
								
			if ( ! empty( $options ) ) {
					
				$term_list = array();
							
				// 行数分ループを回す
				for ( $j = 0; $j_max = count( $options ), $j < $j_max; $j++ ) {
					
					if ( empty( $options[$j] ) )
						continue;
					
					// 値
					$term_list[$j]['ym'] = $options[$j]['value'];
					
					// 表記
					$term_list[$j]['name'] = $options[$j]['text'];
					
					// 階層
					$term_list[$j]['depth'] = $options[$j]['depth'];
				}			
			}
			
			// 検索クエリと同じ値を持つ"表記"（=DB上に実在する値ではなく）をinsert_resultに代入する
			foreach ( $term_list as $key => $value ) {
				
				if ( $get_date[$i] == $value['ym'] ) {
					$output_text = $value['name'];
					break;
				}
			}
			
		} else {			
			
			$year  = esc_sql( substr( $get_date[$i], 0, 4 ) );
			$month = esc_sql( substr( $get_date[$i], 4, strlen( $get_date[$i] ) ) );
			$output_text = $year . "年" . (int) $month . "月";
		}
			
		insert_result( $output_text );
		
		$list_ids = array();
		
		// 「201810」の形式以外は-9999を与える
		if ( ! ( is_numeric( $get_date[$i] ) && 6 === strlen( $get_date[$i] ) ) ) {
		
			$list_ids[] = '-9999';
		} 
		else {
			
			// データの準備
			$year       = esc_sql( substr( $get_date[$i], 0, 4 ) );
			$month      = esc_sql( substr( $get_date[$i], 4, strlen( $get_date[$i] ) ) );
			$last_day   = esc_sql( date( 't', mktime( 0, 0, 0, $month, 1, $year ) ) );
			$next_year  = $year;
			$next_month = $month +1;
			
			// 年をまたぐ月の場合
			if ( $month == 12 ) {
				$next_year  = $year +1;
				$next_month = 1;
			}
		
			/**
			 *	検索本体
			 */	
			
			$sql  = " SELECT ID FROM {$wpdb->posts}";
			$sql .= " WHERE";
			$sql .= " (";
			$sql .= " post_date >= '{$year}-{$month}-01 00:00:00'";
			$sql .= " AND";
			$sql .= " post_date < '{$next_year}-{$next_month}-01 00:00:00' )";
			$get_ids = $wpdb->get_results( $sql, ARRAY_A );
			
			// 記事IDのリストをつくる（array_mergeの準備）
			foreach ( $get_ids as $ids ) {
				$list_ids[] = $ids['ID'];
			}
		}
											
		// OR検索の時
		if ( 'a' === $datas['and'] || '0' == $datas['and'] ) {
		
			if ( 0 === $i ) {
				$mod_ids = $list_ids;
			}
			else {		
				// 前回のループの結果に現在のループの結果を結合
				$mod_ids = array_merge( $mod_ids, $list_ids );
							
				// 重複を削除
				$mod_ids = array_unique( $mod_ids );
			}
		}
	
		// AND検索の時
		else {
							
			if ( 0 === $i )
				$mod_ids = $list_ids;
							
			// 複数の年月に渡って該当する記事IDを、array_intersectで抽出（各配列に共通する値を選別）
			// １つ前の年月検索の結果（記事ID群）を、現在の年月検索の結果でフィルタリング
			$mod_ids = array_intersect( $mod_ids, $list_ids );
		}
	}
	
	if ( $mod_ids ) {
		for ( $i = 0, $i_max = count( $mod_ids ); $i < $i_max; $i++ ) {
						
			// 配列で返す
			if ( isset( $datas['format'] ) && 'array' === $datas['format'] ) {
				
				$ret[] = $mod_ids[$i];
				
			// SQLで返すためのカンマ繋ぎ
			} else {
				
				if ( '' != $ret )
					$ret .= ',';
				
				$ret .= esc_sql( $mod_ids[$i] );
			}
		}
		
		if ( isset( $datas['format'] ) && 'sql' === $datas['format'] ) {
			$ret = "{$wpdb->posts}.ID IN ({$ret})";
		}	
	}
	else {
		$ret = "{$wpdb->posts}.ID = -9999";
	}
				
	return $ret;
}

//////////////////////////////////////////////
//	タクソノミー検索
//////////////////////////////////////////////
function create_where_category( $datas, $i_counter ) {
	global $wpdb;
			
	$r_ret = null;
		
	if ( $datas ) {
		for ( $i_datas = 0, $cnt_datas = count( $datas ); $i_datas < $cnt_datas; $i_datas++ ) {
	
			if ( $i_datas > 0 )
				$r_ret .= " AND ";
	
			// 単一選択形式（ドロップダウン等）で尚且つ子カテゴリ検索がない場合
			if ( $datas[$i_datas]['plural'] == "0" ) {
				$r_ret .= create_where_single_cat( $datas[$i_datas] );
			}
			else {
				$r_ret .= create_where_plural_cat( $datas[$i_datas] );
			}
		}
	}

	$ret = null;
	if ( $r_ret != null ) {
		$ret = " AND ( {$r_ret} )";
	}

	if ( $ret == null && $i_counter != 0 ) {
		$ret .= " AND {$wpdb->posts}.ID = -9999";
	}

	return $ret;
}

//////////////////////////////////////////////
//	タクソノミー検索｜単一選択形式 (ドロップダウン・ラジオボタン)
//////////////////////////////////////////////
function create_where_single_cat( $data ) {
	global $wpdb;
	
	$ret = null;
	
	/**
	 *	検索本体
	 */	
	$sql  = " SELECT tr.object_id";
	$sql .= " FROM {$wpdb->term_relationships} AS tr";
	$sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
	$sql .= " LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id";
	if ( array_key_exists( 'notin', $data ) && $data['notin'] != '' ) {
		$sql .= " WHERE tt.term_id != '" . esc_sql( $data['notin'] ) . "'";
	} else {
		$sql .= " WHERE tt.term_id = '" . esc_sql( $data['cat'] ) . "'";
	}
	$get_ids = $wpdb->get_results( $sql );
	
	if ( $get_ids ) {
		
		$cnt_ids = count( $get_ids );
		
		for ( $i_ids = 0; $i_ids < $cnt_ids; $i_ids++ ) {
			
			// 配列で返す
			if ( isset( $data['format'] ) && 'array' === $data['format'] ) {
				
				$ret[] = $get_ids[$i_ids]->object_id;
				
			// SQLで返すためのカンマ繋ぎ
			} else {
				
				if ( '' != $ret )
					$ret .= ',';
				
				$ret .= esc_sql( $get_ids[$i_ids]->object_id );
			}
		}
		
		if ( isset( $data['format'] ) && 'sql' === $data['format'] ) {
			$ret = " {$wpdb->posts}.ID IN ( {$ret} )";
		}
		
	} else {
		
		// 除外タームの場合はnull、それ以外は-9999を返してヒット0件にする（nullだと他の条件にヒットしたものが抽出されてしまう）
		if ( 'exclude' !== $data['mode'] ) { 
			$ret = " ( {$wpdb->posts}.ID = -9999 )";
		}
	}
	
	if ( 'exclude' !== $data['mode'] ) {

		/**
		 *	検索条件をテンプレートに表示させるためにカテゴリ/タグ名を取得
		 */
		
		$output_text = '';
		
		// 「自由記述」の場合、記述したとおりに出力	
		if ( 'b' === $data['orderby'] ) {
			
			$options = $data['freetext'];
					
			if ( ! empty( $options ) ) {
					
				$term_list = array();
							
				// 行数分ループを回す
				for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {
					
					if ( empty( $options[$i] ) )
						continue;
					
					// 値
					$term_list[$i]['term_id'] = $options[$i]['value'];
					
					// 表記
					$term_list[$i]['name'] = $options[$i]['text'];
					
					// 階層
					$term_list[$i]['depth'] = $options[$i]['depth'];
				}			
			}
	
			// 検索クエリと同じ値を持つ"表記"（=DB上に実在する値ではなく）をinsert_resultに代入する
			foreach ( $term_list as $key => $value ) {
				if ( $data['cat'] === $value['term_id'] ) {
					$output_text = $value['name'];				
				}
			}
		
		} else {
		
			$term_data_all = get_term( $data['cat'] );
			$term_data     = get_term_by( 'id', $data['cat'], $term_data_all->taxonomy );
			$output_text   = $term_data->name;
		}
			
		// 検索条件を格納
		insert_result( $output_text );
	}
	
	return $ret;
}

//////////////////////////////////////////////
//	タクソノミー検索｜複数選択形式 (セレクトボックス・チェックボックス)
//////////////////////////////////////////////
function create_where_plural_cat( $data ) {
	global $wpdb, $cols, $manag_no;
	
	// 初期化
	$left_ids = array();
	$ret = null;
	
	// ajaxフィルタリング時の未選択プルダウン分の配列を削除
	$get_cats = array_filter( $data['cat'], 'strlen' );
	
	/**
	 *	検索本体
	 */
	$sql_id  = " SELECT object_id FROM {$wpdb->term_relationships} AS tr";
	$sql_id .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
	$sql_id .= " LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id";
	
	// チェックされたカテゴリ１つずつ、該当する記事IDを取得して配列$idsに格納
	for ( $i_cat = 0 , $cnt_cat = count( $get_cats ); $i_cat < $cnt_cat; $i_cat++ ) {

		$sql_id_add = " WHERE tt.term_id = '{$get_cats[$i_cat]}'";
		$get_ids = $wpdb->get_results( $sql_id . $sql_id_add );
								
		$ids = array();
		
		if ( $get_ids ) {					
			for ( $i_ids = 0, $cnt_ids = count( $get_ids ); $i_ids < $cnt_ids; $i_ids++ ) {
		
				if ( $get_ids[$i_ids]->object_id != null ) {
					$ids[] = $get_ids[$i_ids]->object_id;
				}
			}
		}
													
		// OR検索の時
		if ( '0' === $data['and'] || 'a' === $data['and'] ) {			
		
			if ( $i_cat == 0 ) {
				$left_ids = $ids;
			}
			else {		
				// 前回のループの結果に現在のループの結果を結合
				$left_ids = array_merge( $left_ids, $ids );
							
				// 重複を削除
				$left_ids = array_unique( $left_ids );
			}
		}
		
		// AND検索の時
		else {
							
			if ( $i_cat == 0 )
				$left_ids = $ids;
							
			// 複数のカテゴリに渡って該当する記事IDを、array_intersectで抽出（各配列に共通する値を選別）
			// １つ前のカテゴリ検索の結果（記事ID群）を、現在のカテゴリ検索の結果でフィルタリング
			$left_ids = array_intersect( $left_ids, $ids );
		}	
		
		/**
		 *	検索条件をテンプレートに表示させるためにカテゴリ/タグ名を取得
		 */
		
		// 「自由記述」の場合、記述したとおりに出力	
		if ( 'b' === $data['orderby'] ) {
			
			$options = $data['freetext'];
					
			if ( ! empty( $options ) ) {
					
				$term_list = array();
							
				// 行数分ループを回す
				for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {
					
					if ( empty( $options[$i] ) )
						continue;
					
					// 値
					$term_list[$i]['term_id'] = $options[$i]['value'];
					
					// 表記
					$term_list[$i]['name'] = $options[$i]['text'];
					
					// 階層
					$term_list[$i]['depth'] = $options[$i]['depth'];
				}			
			}
	
			// 検索クエリと同じ値を持つ"表記"（=DB上に実在する値ではなく）をinsert_resultに代入する
			$output_text = '';
			foreach ( $term_list as $key => $value ) {
				if ( $get_cats[$i_cat] === $value['term_id'] ) {
					$output_text = $value['name'];				
				}
			}
		
		} else {
			
			$term_data_all = get_term( $get_cats[$i_cat] );
			$term_data     = get_term_by( 'id', $get_cats[$i_cat], $term_data_all->taxonomy );
			$output_text   = $term_data->name;	
		}
		
		// 検索条件を格納
		insert_result( $output_text );	
	}
		
	if ( $left_ids ) {
		
		foreach ( $left_ids as $key => $left_id ) {
			
			// 配列で返す
			if ( isset( $data['format'] ) && 'array' === $data['format'] ) {
				
				$ret[] = $left_id;
				
			// SQLで返すためのカンマ繋ぎ
			} else {
			
				if ( '' != $ret )
					$ret .= ',';
						
				$ret .= esc_sql( $left_id );
			}	
		}
		if ( isset( $data['format'] ) && 'sql' === $data['format'] ) {
			$ret = " {$wpdb->posts}.ID IN (" . $ret . ")";
		}
	} 
	else
		$ret = " ( ".$wpdb->posts.".ID = -9999 )";
			
	return $ret;
}

//////////////////////////////////////////////
//	フリーワード検索
//////////////////////////////////////////////
function create_where_keyword( $keywords, $kwds_target = '', $kwds_yuragi = 'yes', $manag_no, $number, $format = 'sql' ) {
	global $wpdb, $feas_mojicode, $cols, $feadvns_search_target;
	
	$kwds = '';
	
	// 複数キーワードをスペースで分割
	$keywords = str_replace( '　', ' ', stripslashes( $keywords ) );
	preg_match_all( '/".*?("|$)|( (?<=[\\s])|^)[^\\s]+/', $keywords, $matches );
	$keywords = array_map( function( $a ) { 
		return trim( $a, "\n\r ");
	}, $matches[0] );
	
	// スペースだけなど無意味な場合は処理終了
	if ( empty( $keywords ) ) 
		return;
					
	// サニタイズ
	foreach ( $keywords as $word ) {

		// 検索条件としてテンプレートに表示させるため収納（esc_likeを通す前の生キーワード）
		insert_result( stripslashes( $word ) );
		insert_kwds_result( stripslashes( $word ) , $number );

		// ゆらぎの場合と階層レベルをあわせるため二重に
		$ret_ary[][0] = esc_sql( $wpdb->esc_like( $word ) );
	}
	$kwds = $ret_ary;
	
	// あいまいさ - 半角全角を区別しない
	if ( 'no' === $kwds_yuragi ) {
		
		$kwds_tmp = array();
		$cnt = 0;
		
		// キーワードごとに全半角変換して、違いがあればキーワードに追加
		foreach ( $kwds as $before ) {
			
			// オリジナル
			$kwds_tmp[$cnt][] = $before[0];
			
			// 全半角英数字
			$after = mb_convert_kana( $before[0], 'aA' );
			if ( $before[0] !== $after ) {
				$kwds_tmp[$cnt][] = $after;
			}	

			// 全角カタカナ <> 半角カタカナ
			$after = mb_convert_kana( $before[0], 'kKV' );
			//if ( mb_strwidth( $before[0], 'UTF-8' ) !== mb_strwidth( $after, 'UTF-8' ) ) {
			if ( $before[0] !== $after ) {
				$kwds_tmp[$cnt][] = $after;
			}
			
			// 全角ひらがな <> 半角カタカナ
			$after = mb_convert_kana( $before[0], 'hHV' );
			if ( $before[0] !== $after ) {
				$kwds_tmp[$cnt][] = $after;
			}
			
			// 全角カタカナ <> 全角ひらがな
			$after = mb_convert_kana( $before[0], 'cC' );
			if ( $before[0] !== $after ) {
				$kwds_tmp[$cnt][] = $after;
			}
			
			$cnt++;
		}
				
		// 元のキーワードに変換したキーワードを追加
		$kwds = $kwds_tmp;
	}
	
	// キーワードの数を取得
	$kw_cnt = count( $kwds );
	
	// 検索対象の指定がDBにない場合
	if ( $kwds_target == '' ) {
		$kwds_target = array();
		$kwds_target[0] = 'post_title';
		$kwds_target[1] = 'post_content';
		$kwds_target[2] = 'name';
		$kwds_target[3] = 'meta_value';
	} else {
		$kwds_target = explode( ',', $kwds_target );	
	}
	
	// DBから取得したデータから「0」を省く
	foreach ( $kwds_target as $k_target ) {
		if ( $k_target != '0' )
			$kt[] = $k_target;	
	}
	
	// 検索対象の数を取得
	$tg_cnt = count( $kt );
	
	// キーワードが複数だった場合にINNER JOINするためのフラグ
	$comment_join_flag = false;
	$term_join_flag    = false;
	$pm_join_flag      = false;
	
	if ( in_array( 'comment_content', $kt ) )
		$comment_join_flag = true;
	if ( in_array( 'name', $kt ) )
		$term_join_flag = true;
	if ( in_array( 'meta_value', $kt ) )
		$pm_join_flag = true;
	
	// メインクエリの前段
	// JOINの順番で速度が変わるので注意
	$pre_sql = " SELECT distinct {$wpdb->posts}.ID FROM {$wpdb->posts}";
	if ( true === $term_join_flag ) {
		$pre_sql .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON {$wpdb->posts}.ID = tr.object_id";
		$pre_sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		$pre_sql .= " LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id";
	}
	if ( true === $comment_join_flag ) {
		$pre_sql .= " LEFT JOIN {$wpdb->comments} AS comm ON {$wpdb->posts}.ID = comm.comment_post_ID";
	}
	if ( true === $pm_join_flag ) {
		$pre_sql .= " LEFT JOIN {$wpdb->postmeta} AS pm ON {$wpdb->posts}.ID = pm.post_id";
	}
	$pre_sql .= " WHERE 1=1";


	// メインクエリの後段	
	
	/**
	 *	検索対象のpost_typeを取得
	 */
	$target_pt_tmp = get_option( $feadvns_search_target . $manag_no );
	if ( $target_pt_tmp ) {
		$target_pt = "'" . implode( "','", (array) $target_pt_tmp ) . "'";
	} else {
		$target_pt = "'post'";
	}
	$post_sql = " AND {$wpdb->posts}.post_type IN ( $target_pt )";

	$post_sql .= " AND {$wpdb->posts}.post_status = 'publish'";		
	
	// メインクエリ
	$left_ids = array();
	
	for ( $i_key = 0; $i_key < $kw_cnt; $i_key++ ) {
		
		$sql = ' AND ';
		//if ( $i_key > 0) {$sql .= " OR ( ";} else {$sql .= " AND ( ";} //or検索用
		//$sql .= " AND ( ";
		
		for ( $i_tg = 0 , $i_cnt = 0; $i_tg < $tg_cnt; $i_tg++ ) {
			
			if ( 'post_title' === $kt[$i_tg] ) {
				foreach ( $kwds[$i_key] as $word ) {
					if ( $i_cnt != 0 )
						$sql .= " OR ";
					$sql .= "( {$wpdb->posts}.post_title LIKE '%{$word}%' )";
					$i_cnt++;
				}
			}
			if ( 'post_content' === $kt[$i_tg] ) {
				foreach ( $kwds[$i_key] as $word ) {
					if ( $i_cnt != 0 )
						$sql .= " OR ";
					$sql .= "( {$wpdb->posts}.post_content LIKE '%{$word}%' )";
					$i_cnt++;
				}
			}
			if ( 'post_excerpt' === $kt[$i_tg] ) {
				foreach ( $kwds[$i_key] as $word ) {
					if ( $i_cnt != 0 )
						$sql .= " OR ";
					$sql .= "( {$wpdb->posts}.post_excerpt LIKE '%{$word}%' )";
					$i_cnt++;
				}
			}
			if ( 'comment_content' === $kt[$i_tg] ) {
				foreach ( $kwds[$i_key] as $word ) {	
					if ( 0 !== $i_cnt )
						$sql .= " OR ";					
					$sql .= "( comm.comment_content LIKE '%{$word}%' )";
					$i_cnt++;
				}
			}			
			if ( 'name' === $kt[$i_tg] ) {
				foreach ( $kwds[$i_key] as $word ) {
					if ( $i_cnt != 0 )
						$sql .= " OR ";
					$sql .= "( t.name LIKE '%{$word}%' )";
					$i_cnt++;
				}
			}
			if ( 'meta_value' === $kt[$i_tg] ) {
							
				 // キー指定
				$specify_key_switch = get_option( $cols[21] . $manag_no . '_' . $number );	
											
				foreach ( $kwds[$i_key] as $word ) {				
					
					if ( 0 !== $i_cnt )
						$sql .= " OR ";
									
					if ( 'no' === $specify_key_switch ) {
										
						for ( $i_sk = 0 ; $i_sk <= (int) $_GET['cf_specify_key_length_' . $number]; $i_sk++ ) {
														
							if ( isset( $_GET['cf_specify_key_' . $number . '_' . $i_sk] ) && ( null != $_GET['cf_specify_key_' . $number . '_' . $i_sk] ) ) {
															
								if ( function_exists( 'is_ktai' ) && true == is_ktai() ) {
									$key = mb_convert_encoding( $_GET['cf_specify_key_' . $number . '_' . $i_sk], 'UTF-8', 'SJIS' );
								} else {
									$key = $_GET['cf_specify_key_' . $number . '_' . $i_sk];
								}
																				
								feas_insert_keys_result( $key, $manag_no ); // カスタムフィールドのキーを格納
								
								$word = esc_sql( $word );
								
								if ( 0 != $i_sk )
									$sql .= " OR ";
								
								$sql .= "(";
								$sql .= " pm.meta_key IN ( '{$key}' )";
								$sql .= " AND pm.meta_value LIKE '%{$word}%' ";
								$sql .= ")";
							}
						}
						
					} else {
						
						// 先頭にアンダースコアがつくmeta_keyのvalueを検索対象から「すべて」除外するには下記をコメントイン
						$sql .= "( pm.meta_key NOT LIKE '\_%'";
						$sql .= " AND pm.meta_value LIKE '%{$word}%' )";
					}
									
					$i_cnt++;
				}
			}
		}
						
		// 検索実行
		$get_ids = $wpdb->get_results( $pre_sql . $sql . $post_sql );
			
		$ids = array();
		
		// オブジェクトから記事IDだけを抽出して配列に入れる。array_intersectの準備。
		if ( $get_ids ) {					
			for ( $i_ids = 0, $cnt_ids = count( $get_ids ); $i_ids < $cnt_ids; $i_ids++ ) {
		
				if ( $get_ids[$i_ids]->ID != null ) {
					$ids[] = $get_ids[$i_ids]->ID;
				}
			}
		}
			
		if ( $ids ) {
											
			if ( $i_key == 0 )
				$left_ids = $ids;
									
			// 複数のカテゴリに渡って該当する記事IDを、array_intersectで抽出（各配列に共通する値を選別）
			// １つ前のカテゴリ検索の結果（記事ID群）を、現在のカテゴリ検索の結果でフィルタリング
			$left_ids = array_intersect( $left_ids, $ids );
		}
	}
	
	// array_intersectでキーごと抽出されたものを、キーを連番で振り直す
	$left_ids = array_values( $left_ids );
			
	$ret = '';
	
	if ( $left_ids ) {
		
		for ( $i_pid =0, $cnt_pid = count( $left_ids ); $i_pid < $cnt_pid; $i_pid++ ) {
			
			// 配列で返す
			if ( isset( $data['format'] ) && 'array' === $data['format'] ) {
				
				$ret[] = $left_ids[$i_pid];
				
			// SQLで返すためのカンマ繋ぎ
			} else {
				
				if ( '' != $ret )
					$ret .= ',';
									
				$ret .= esc_sql( $left_ids[$i_pid] );
			}
		}
		if ( 'sql' === $format) {
			$ret = " AND {$wpdb->posts}.ID IN ( {$ret} )";
		}
	}

	if ( empty( $ret ) ) {
		$ret .= " AND {$wpdb->posts}.ID = -9999";
	}
		
	return $ret;
}

//////////////////////////////////////////////
//	カスタムフィールド検索
//////////////////////////////////////////////
function create_where_meta( $datas, $i_cnt_meta ) {
	global $wpdb;
		
	$r_ret = null;
	
	for ( $i_datas = 0, $cnt_datas = count( $datas ); $i_datas < $cnt_datas; $i_datas++ ) {
		
		if ( $i_datas > 0 )
			$r_ret .= " AND ";

		// フリーワードで範囲検索
		if ( isset( $datas[$i_datas]['free_word']) && $datas[$i_datas]['free_word'] == 'yes' ) {
			$r_ret .= create_where_single_meta( $datas[$i_datas] );
		
		// 単一選択形式の場合（ドロップダウン/ ラジオボタン）
		} elseif ( $datas[$i_datas]['plural'] == "0" ) {
			$r_ret .= create_where_single_meta( $datas[$i_datas] );
		
		// 複数選択形式の場合（チェックボックス/ リストボックス）
		} else {
			$r_ret .= create_where_plural_meta( $datas[$i_datas] );
		}
	}

	$ret = null;
	
	if ( $r_ret != null )
		$ret = " AND {$r_ret} ";

	// 検索条件が設定され、かつ該当記事がない場合
	if ( $ret == null && $i_cnt_meta != 0 ) {
		$ret .= " AND ( {$wpdb->posts}.ID = -9999 ) ";
	}
	
	return $ret;
}

//////////////////////////////////////////////
//	カスタムフィールド検索本体｜単一選択形式
//////////////////////////////////////////////
function create_where_single_meta( $datas ) {
	global $wpdb, $cols, $manag_no;
	
	if ( $datas['free_word'] == 'yes' ) {
		$keywords = stripslashes( $datas['metas']);
		$keywords = str_replace( "　", " ", $datas['metas']);
		if ( mb_strlen( $keywords ) != strlen( $keywords ) ) {
			$keywords = mb_convert_kana( $keywords , "a" , "UTF-8" );
			//$keywords = mb_convert_kana( $keywords , "a" , "SJIS" );
			
		}/* else { 半角を全角にする必要は無いか？？
			$kwds[$ii_key][0] = $keywords[ $ii_key ];
			$kwds[$ii_key][1] = mb_convert_kana( $keywords , "A" , "UTF-8" );
		}*/

		$keywords = str_replace( ",", "", $datas['metas']);
		$keywords = explode( ' ', $keywords);
		$keywords = $keywords[0];
	}
		
	$get_id_data = $rangeKey = null;

	if ( function_exists( 'is_ktai' ) && true == is_ktai() ) {
		$datas['metas'] = mb_convert_encoding( $datas['metas'], 'UTF-8', 'SJIS' );
	}

	$value_is_int = false;
	
	if ( is_numeric( $unformatted_value = str_replace( ',', '', $datas['metas'] ) ) ) {
		$value_is_int = true;
	}
		
	// 検索条件( =meta_value )に該当する記事を取得
	$sql  = " SELECT post_id FROM " . $wpdb->postmeta;
	$sql .= " WHERE meta_key = '" . esc_sql( $datas['key'] ) . "'";
		
	// 範囲検索 and 値が数値である場合のみ
	if ( in_array( $datas['range'], array( 1, 2, 3, 4 ) ) ) {
		
		// 数値or文字フラグ
		$range_as = null;
		if ( 'int' == $datas['range_as'] ) {
			$range_as = '+0';
		}
	
		if ( '1' == $datas['range'] ) {
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} < '" . (int) esc_sql( $unformatted_value ) . "' )";
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} != '' )";
			$rangeKey = '未満';
		
		} elseif ( '2' == $datas['range'] ) {
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} <= '" . (int) esc_sql( $unformatted_value ) . "' )";
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} != '' )";
			$rangeKey = '以下';
		
		} elseif ( '3' == $datas['range'] ) {
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} >= '" . (int) esc_sql( $unformatted_value ) . "' )";
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} != '' )";
			$rangeKey = '以上';
		
		} elseif ( '4' == $datas['range'] ) {
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} > '" . (int) esc_sql( $unformatted_value ) . "' )";
			$sql .= " AND ( REPLACE( meta_value, ',', '' ){$range_as} != '' )";
			$rangeKey = '超';
		}
		
	// 範囲検索しない or 値が数値でない場合
	// カンマ付きの数字はカンマ削除して比較（入力値、DBともに）
	} else {				
		$sql .= " AND ( meta_value = '" . esc_sql( $datas['metas'] ) . "'";
		$sql .= " OR REPLACE( meta_value, ',', '' ) = REPLACE( '" . esc_sql( $datas['metas'] ) . "', ',', '' ) )";
		//$sql .= " AND meta_value ='" . mb_convert_encoding( $datas['metas'] , "UTF-8" ,"SJIS"). "')";
		$rangeKey = '';
	}
	
	$get_id_data = $wpdb->get_results( $sql );

	// 「自由記述」の場合、記述したとおりに出力	
	if ( 'b' === $datas['orderby'] ) {
		
		$options = $datas['freetext'];
		
		if ( ! empty( $options ) ) {
				
			$sterm_li = array();
						
			// 行数分ループを回す
			for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {
				
				if ( empty( $options[$i] ) )
					continue;
				
				// 値
				$sterm_li[$i]['meta_value'] = $options[$i]['value'];
				
				// 表記
				$sterm_li[$i]['text'] = $options[$i]['text'];
				
				// 階層
				$sterm_li[$i]['depth'] = $options[$i]['depth'];
			}			
		}

		// 検索クエリと同じ値を持つ"表記"（=DB上に実在する値ではなく）をinsert_resultに代入する
		$output_text = '';
		foreach ( $sterm_li as $key => $value ) {
			if ( $datas['metas'] === $value['meta_value'] ) {
				$output_text = $value['text'];
				break;
			}
		}
		$formatted_value = $output_text . $rangeKey;
		
	} else {
			
		// 真偽値の場合
		if ( '1' === $datas['scf'] || '1' === $datas['shingi'] ) {
		
			// DBデータそのもの（1 or 0）ではなく指定されたテキストを表示
			$formatted_value = $datas['shingi_txt'];
		
		// 「関連する投稿」の場合
		} elseif ( '2' === $datas['scf'] ) {
													
			$related_post = get_post( $unformatted_value );
			$formatted_value = $related_post->post_title;
		
		// 「関連するターム」の場合
		} elseif ( '3' === $datas['scf'] ) {
				
			$related_term = get_term( $unformatted_value );
			$formatted_value = $related_term->name;
		
		} else {
			
			// 区切り and 数値 = カンマ区切りしたもの
			if ( 'yes' == $datas['kugiri'] && $value_is_int ) {
				$formatted_value = number_format( $unformatted_value, 0, '.', ',' );
			
			// 区切り and 数値ではない = そのまま表示
			} elseif ( 'yes' == $datas['kugiri'] && false == $value_is_int ) {
				$formatted_value = $datas['metas'];
			
			// 区切らない = カンマ区切りしないもの
			} else {
				$formatted_value = $unformatted_value;	
			}
		
			// 「テキストによる範囲検索」では検索語句をキーワード欄に戻すので、単位を付ける前の値を渡す
			//insert_kwds_result( $formatted_value, $datas['number'] );
			
			if ( '0' == $datas['tani_position'] ) {
				$formatted_value = $datas['unit'] . $formatted_value . $rangeKey; // 単位が前 + 範囲
			} else {
				$formatted_value = $formatted_value . $datas['unit'] . $rangeKey; // 単位が後 + 範囲
			}
		}
	}

	insert_result( $formatted_value );
	
	$ret = null;
		
	// 該当記事からpost_idを抽出、戻り値(SQL文)を生成
	if ( $get_id_data ) {
		
		for ( $i_ids = 0, $cnt_ids = count( $get_id_data ); $i_ids < $cnt_ids; $i_ids++ ) {
			
			// 配列で返す
			if ( isset( $datas['format'] ) && 'array' === $datas['format'] ) {
				
				$ret[] = $get_id_data[$i_ids]->post_id;
				
			// SQLで返すためのカンマ繋ぎ
			} else {
			
				if ( '' != $ret )
					$ret .= ',';
						
				$ret .= esc_sql( $get_id_data[$i_ids]->post_id );
			}
		}
				
		if ( isset( $datas['format'] ) && 'sql' === $datas['format'] ) {
			$ret = " {$wpdb->posts}.ID IN (" . $ret . ")";
		}
		
	} else {
		$ret = " {$wpdb->posts}.ID = -9999";
	}
	
	return $ret;
}

//////////////////////////////////////////////
// カスタムフィールド検索本体｜複数選択形式
//////////////////////////////////////////////
function create_where_plural_meta( $datas ) {
	
	global $wpdb;
	
	$get_metas = $datas['metas']; // 配列
	
	if ( function_exists( 'is_ktai' ) && true == is_ktai() ) {
		foreach ( $get_metas as $key => $val ) {
			$get_metas[ $key ] = mb_convert_encoding( $val, "UTF-8", "SJIS" );
		}
	}
		
	$get_id_data = array();
		
	// 検索条件(=meta_value)ごとに該当する記事を取得
	for ( $i_data = 0, $cnt_data = count( $get_metas ); $i_data < $cnt_data; $i_data++ ) {

		if ( empty( $get_metas[$i_data] ) )
			continue;
					
		$sql  = " SELECT {$wpdb->postmeta}.post_id FROM {$wpdb->postmeta}";
		$sql .= " WHERE ( {$wpdb->postmeta}.meta_key = '" . esc_sql( $datas['key'] ) . "'";
		$sql .= " AND ( {$wpdb->postmeta}.meta_value = '" . esc_sql( $get_metas[$i_data] ) . "'";
		$sql .= " OR {$wpdb->postmeta}.meta_value = REPLACE( '" . esc_sql( $get_metas[$i_data] ) . "', ',', '' ) ) )";
		$get_id_data[] = $wpdb->get_results( $sql );
						
		$value_is_int = false;
	
		if ( is_numeric( $unformatted_value = str_replace( ',', '', $get_metas[$i_data] ) ) ) {
			$value_is_int = true;
		}
		
		// 「自由記述」の場合、記述したとおりに出力	
		if ( 'b' === $datas['orderby'] ) {
			
			$options = $datas['freetext'];
					
			if ( ! empty( $options ) ) {
					
				$sterm_li = array();
							
				// 行数分ループを回す
				for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {
					
					if ( empty( $options[$i] ) )
						continue;
					
					// 値
					$sterm_li[$i]['meta_value'] = $options[$i]['value'];
					
					// 表記
					$sterm_li[$i]['text'] = $options[$i]['text'];
					
					// 階層
					$sterm_li[$i]['depth'] = $options[$i]['depth'];
				}			
			}
	
			// 検索クエリと同じ値を持つ"表記"（=DB上に実在する値ではなく）をinsert_resultに代入する
			$output_text = '';
			foreach ( $sterm_li as $key => $value ) {
				if ( $get_metas[$i_data] === $value['meta_value'] ) {
					$output_text = $value['text'];				
				}
			}
			$formatted_value = $output_text;
		
		} else {
			
			// 真偽値の場合
			if ( '1' === $datas['scf'] || '1' === $datas['shingi'] ) {
			
				// DBデータそのもの（1 or 0）ではなく指定されたテキストを表示
				$formatted_value = $datas['shingi_txt'];
			
			// 「関連する投稿」の場合
			} elseif ( '2' === $datas['scf'] ) {
														
				$related_post = get_post( $unformatted_value );
				$formatted_value = $related_post->post_title;
			
			// 「関連するターム」の場合
			} elseif ( '3' === $datas['scf'] ) {
					
				$related_term = get_term( $unformatted_value );
				$formatted_value = $related_term->name;
						
			} else {
				
				// 区切り and 数値 = カンマ区切りしたもの
				if ( 'yes' == $datas['kugiri'] && $value_is_int ) {
					$formatted_value = number_format( $unformatted_value, 0, '.', ',' );
				
				// 区切り and 数値ではない = そのまま表示
				} elseif ( 'yes' == $datas['kugiri'] && false == $value_is_int ) {
					$formatted_value = $get_metas[$i_data];
				
				// 区切らない = カンマ除去したもの
				} else {
					$formatted_value = $unformatted_value;	
				}
						
				if ( '0' == $datas['tani_position'] ) {
					$formatted_value = $datas['unit'] . $formatted_value; // 単位が前
				} else {
					$formatted_value = $formatted_value . $datas['unit']; // 単位が後
				}
			}
		}
		
		// 検索条件をテンプレートに表示するために収納
		insert_result( $formatted_value );
	}
	
	$get_id = array();
	
	// 該当記事からpost_idを抽出
	for ( $i_data = 0, $cnt_data = count( $get_id_data ); $i_data < $cnt_data; $i_data++ ) {
		for ( $s_data = 0, $s_cnt = count( $get_id_data[ $i_data ] ); $s_data < $s_cnt; $s_data++ ) {
			$get_id[] = $get_id_data[ $i_data ][ $s_data ]->post_id;
		}
	}
		
	// 検索条件が１つ以上の時は重複チェックをする
	if ( count( $get_metas ) > 1 ) {
		
		$get_data = array();
			
		// OR検索
		if ( 'a' === $datas['and'] || '0' == $datas['and'] ) {
			
			// 重複を削除
			$get_id = array_unique( $get_id );
			
			foreach ( $get_id as $value ) {
				$get_data[] = $value;
			}		
		}
			
		// AND検索
		else { 
			
			// 重複しているpost_id ( = 複数条件に該当 ) のみ残す
			if ( true == is_array( $get_id ) ) {
				$arrayValue = array_count_values( $get_id ); // 取得したpost_idの出現回数をカウントする（key: post_id,  value: 出現回数）
				$arraykey = array_keys( $arrayValue, 1 ); // 重複していない値 ( = 出現回数が1 ) のキー(post_id)を取り出す
		
				for ( $i = 0; $i < count( $arraykey ); $i++ ) {
					unset( $arrayValue[ $arraykey[ $i ] ] ); // 重複していない要素 ( = 条件に当てはまらないpost_id ) を削除
				}
					
				if ( 0 != count( $arrayValue ) ) {
						
					$a_keys = array_keys( $arrayValue );
					
					// post_idの出現回数が条件の数と同じ(以上)の記事が該当データなので取得
					$check_cnt = count( $get_metas );  // 条件数
					for ( $i_data = 0, $i_cnt = count( $a_keys ); $i_data < $i_cnt; $i_data++ ) {
						if ( $arrayValue[ $a_keys[ $i_data ] ] >= $check_cnt ) {
							$get_data[] = $a_keys[ $i_data ];
						}
					}				
				}
			}
		}
	
	} else {
		$get_data = $get_id;
	}
	
	$ret = null;
		
	// 戻り値（SQL文）生成
	for ( $i_data = 0, $cnt_data = count( $get_data ); $i_data < $cnt_data; $i_data++ ) {
		
		// 配列で返す
		if ( isset( $datas['format'] ) && 'array' === $datas['format'] ) {
			
			$ret[] = $get_data[$i_data];
			
		// SQLで返すためのカンマ繋ぎ
		} else {
		
			if ( '' != $ret )
				$ret .= ',';
					
			$ret .= esc_sql( $get_data[$i_data] );
		}
	}
				
	// 検索にヒットした場合
	if ( null != $ret ) {

		if ( isset( $datas['format'] ) && 'sql' === $datas['format'] ) {
			$ret = " {$wpdb->posts}.ID IN (" . $ret . ")";
		}
	
	} else {

		// 検索条件が設定され、かつ該当記事がない場合
		if ( 0 != $get_metas && null == $ret ) {
			$ret .= " {$wpdb->posts}.ID = -9999";
		}
	}
	
	return $ret;
}

//////////////////////////////////////////////
// フリーワード、メタキー指定検索
//////////////////////////////////////////////
function feas_specify_key_srarch( $keywords, $number ) {
	global $wpdb, $manag_no;
	
	$cf_key = null;
	$cf_key_array = array();
	
	for ( $i = 0; $i <= (int) $_GET['cf_specify_key_length_' . $number]; $i++ ) {
		if ( isset( $_GET['cf_specify_key_' . $number . '_' . $i] ) && ( $_GET['cf_specify_key_' . $number . '_' . $i] != null ) ) {
			if ( $cf_key != null )
				$cf_key .= ',';
				
			if ( function_exists('is_ktai') && is_ktai() == true ) {
				$words = mb_convert_encoding( $_GET['cf_specify_key_'.$number.'_'.$i] , "UTF-8" , "SJIS" );
			} else {
				$words = $_GET['cf_specify_key_'.$number.'_'.$i];
			}
				
			$cf_key .= '\''.esc_sql( $words ) .'\'';
			
			insert_result( $words );
			feas_insert_keys_result( $words , $manag_no ); //カスタムフィールドのキーを格納
			
			array_push( $cf_key_array , $words );
		}
	}
	
	$sql  = " SELECT DISTINCT post_id FROM " .$wpdb->postmeta;
	$sql .= " LEFT JOIN " .$wpdb->posts. " ON " .$wpdb->postmeta.".post_id = ".$wpdb->posts.".ID ";
	$sql .= " WHERE meta_key IN (" .$cf_key .")";
	
	$sql .= ' AND (';
	$temporary = '';
	foreach ( $keywords as $val) {
		if ( $temporary != '') {$temporary .= ' OR';}
		$temporary .= ' meta_value LIKE \'%'.$val.'%\'';
		$keyword_array[] = $val;
	}
	$sql .= $temporary.' )';
	$sql .= " AND post_status ='publish'";
	$get_data = $wpdb->get_results( $sql );
	
	for ( $i_data = 0, $cnt_data = count( $get_data ); $i_data < $cnt_data; $i_data++ ) {
		
		$get_post_custom = get_post_custom( $get_data[ $i_data ]->post_id);
		//$mache_export = true; //2012.8.17 熊谷 “全角半角区別しない”がされないので修正
		foreach ( $keywords as $val) { //キーワード
			$mache_export = true; //2012.8.17 熊谷
			$val = stripslashes( $val);
			
			foreach ( $cf_key_array as $key) { //post_metaキー
				if (@strstr( $get_post_custom[$key][0], $val) ) {
					$mach_count = true;
					break;
				} else {
					$mach_count = false;
				}
			}
			
			if ( (!$mach_count) || (!$mache_export) ) {
				$mache_export = false;
			}
			if ( $mache_export) {
				if ( $get_data[ $i_data ] != null ) {
					if ( $ret == '' )
						$ret = "AND ( ";
					else
						$ret .= " OR ";
						
					$ret .= $wpdb->posts.".ID =" .$get_data[ $i_data ]->post_id;
				}
			}
		} //foreachキーワード
	}
	
	// 検索条件が設定され、かつ該当記事がない場合
	if ( $ret != null )
		$ret .= " )";
	
	return $ret;
}

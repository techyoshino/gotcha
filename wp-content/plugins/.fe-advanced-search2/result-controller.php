<?php
//////////////////////////////////////////////
//  検索文字列を出力（テンプレート表示用）
//////////////////////////////////////////////
function search_result( $sterm = null , $num = 0, $separator = ',', $before = '', $after = '', $widget = false ) {
	
	$result = array( 0 => null, 1 => null );
	
	if ( isset( $_GET['csp'] ) && ( $_GET['csp'] == "search_add" ) ) {
	 	
	 	// 全ての検索条件（配列）
	 	if ( isset( $_POST['search_result_data']) && ( $_POST['search_result_data'] != null ) ) {
				
			$keywords = $_POST['search_result_data'];
			foreach ( $keywords as $kw ) {
				$result_tmp[] = $before . esc_html( $kw ) . $after;
			}
			$result[0]  = implode( (string) $separator, $result_tmp );
		}	
		
		// 配列で返して加工できるように
		if ( isset( $_POST['kwds_result_data_' . $num ] ) && ( $_POST['kwds_result_data_' . $num ] != null ) ) {
			$result[1] = $_POST['kwds_result_data_' . $num];
		}
			
		if ( 'keywords' == $sterm ) {
			return esc_attr( $result[1] );
		} elseif ( true === $widget ) {
			return $result[0];
		} else {
			print $result[0];
		}
			
	} else {
		return;
	}
}

//////////////////////////////////////////////
//  検索文字列を出力（配列）
//////////////////////////////////////////////
function search_result_array( $sterm = 'all', $num = 0 ) {
		
	if ( isset( $_GET['csp'] ) && ( $_GET['csp'] == 'search_add' ) ) {
		
		// 全ての検索条件（カンマ区切り）
		switch ( $sterm ) {
			
			case 'all':
				if ( isset( $_POST['search_result_data'] ) && ( $_POST['search_result_data'] != null ) ) {
					$result_array = $_POST['search_result_data'];
				}
				break;
			
			case 'keys': // 何かに使えるかもしれない・・・
				if ( isset( $_POST['keys_result_data_' . $num] ) )
					$result_array = $_POST['keys_result_data_'. $num];
				else
					$result_array = null;
				break;
			
			// ハイライト表示のキーワードなどに
			default:
				if ( isset( $_POST['kwds_result_data_all'] ) && ( $_POST['kwds_result_data_all'] != null ) ) {
					$result_array = $_POST['kwds_result_data_all'];
				}
				break;
		}
				
		if ( isset( $result_array ) )
			return $result_array;
		else
			return false;
	
	} else {
		return false;
	}
}

//////////////////////////////////////////////
//  WP標準の検索クエリに流し込む
//////////////////////////////////////////////
function feas_merge_wp_search_query ( $search ) {
	
	if ( isset( $_GET['csp'] ) && ( $_GET['csp'] == 'search_add' ) ) {
	 	
	 	// 全ての検索条件（カンマ区切り）
	 	if ( isset( $_POST['search_result_data']) && ( $_POST['search_result_data'] != null ) ) {
			$search = implode( ',', $_POST['search_result_data'] );
		}
	}
	
	return $search;
}
add_filter( 'get_search_query', 'feas_merge_wp_search_query' );

//////////////////////////////////////////////
//  該当件数を取得
//////////////////////////////////////////////
function feas_count_posts( $form_id = 0, $print = true ) {
	global $wp_query, $feadvns_search_target, $feadvns_include_sticky, $feadvns_default_cat, $wpdb, $feadvns_exclude_id;
	
	$cnt_posts = 0;
	$sp = array();
	
	$manag_no = $form_id;
	
	/*============================
		検索後
	 ============================*/
	if ( is_search() ) {
	
		$cnt_posts = $wp_query->found_posts;
	
	/*============================
		検索実行前
	 ============================*/
	} else {
			
		if ( false === ( $cnt_posts = feas_cache_judgment( $form_id, 'total_cnt', 'global' ) ) ) {
					
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
			 *	固定記事(Sticky Posts)を検索対象から省く設定の場合、カウントに含めない
			 */
			$target_sp = get_option( $feadvns_include_sticky . $manag_no );
			if ( 'yes' != $target_sp ) {
				
				$sticky = get_option( 'sticky_posts' );
				
				// Post Typeの除外IDにマージ
				if ( $sticky != array() ) {
					$sp = array_merge( $sp, $sticky );
				}
			}
			
			/**
			 *	除外する記事ID
			 */
			$exclude_id = get_option( $feadvns_exclude_id . $manag_no );
			
			if ( $exclude_id ) {
				
				// 除外IDにマージ
				$sp = array_merge( $sp, $exclude_id );
			}
			
			/**
			 *	除外IDをカンマ区切りにする
			 */
			if ( $sp ) {
				$sp = implode( ',', $sp );
			}
		
			// 固定タクソノミ/タームを取得
			$fixed_tax = get_option( $feadvns_default_cat . $form_id );
			
			$sql = "SELECT count( DISTINCT p.ID ) AS cnt 
			FROM {$wpdb->posts} AS p 
			LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id 
			LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		    LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
			WHERE 1=1";
			if ( $sp ) $sql .= " AND p.ID NOT IN ( $sp )";
			if ( ! empty( $fixed_tax ) ) $sql .= " AND t.term_id = " . esc_sql( $fixed_tax );
			$sql .= " AND p.post_type IN ( $target_pt )";
			$sql .= " AND p.post_status = 'publish'";
			
			// カウント数取得
			$result = $wpdb->get_results( $sql );
			if ( $result ) {
				$cnt_posts = (int) $result[0]->cnt;
			}
			
			feas_cache_create( $form_id, 'total_cnt', 'global', $cnt_posts );
		}
	}
	
	// 画面出力
	if ( $print ) {
		print $cnt_posts;
	} else {
		return $cnt_posts;
	}
}

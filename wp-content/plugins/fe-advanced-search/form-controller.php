<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	フォーム作成関係
/////////////////////////////////////////////////

/*============================
	フォームを作成
 ============================*/
function create_searchform( $id = null, $shortcode_f = null ) {

	global $wpdb, $cols, $feadvns_max_line, $manag_no, $feadvns_search_b_label, $use_style_key, $style_body_key, $feadvns_search_target, $feadvns_reset_btn_sw, $feadvns_reset_btn_js, $feadvns_reset_btn_position, $feadvns_reset_btn_text;

	if ( is_admin() )
		return;

	// FEAS用スクリプト
	wp_enqueue_script( 'feas', plugins_url() . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) . 'feas.js', array( 'jquery' ), '1.0', true );
	// ajax_filtering用スクリプト
	wp_enqueue_script( 'ajax_filtering', plugins_url() . '/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) . 'ajax_filtering.js', array( 'jquery' ), '1.0.8', true );

	if ( $id != null && is_numeric( $id ) ) {
		$manag_no = $id;
	} else {
		$manag_no = 0;
	}

	$home_url = '/';

	// Polylang
	$lang = get_query_var( 'lang' );
	if ( $lang ) {
		$home_url = '/' . $lang . '/';
	}

	if ( is_ssl() ) {
		$action_url = home_url( $home_url, 'https' );
	} else {
		$action_url = home_url( $home_url );
	}

	/**
	 *
	 * フォームにインラインCSSを差し込むためのフック
	 *
	 */
	$inline_style = '';
	$args = array(
		'manag_no' => (int) $manag_no,
	);
	$inline_style = apply_filters( 'feas_form_inline_style', $inline_style, $args );
	if ( $inline_style ) {
		$inline_style = esc_attr( $inline_style );
		$inline_style = " style='{$inline_style}'";
	}

	/**
	 *
	 * フォームのattrにかけるフィルター
	 *
	 */
	$attr = '';
	$args = array(
		'manag_no' => (int) $manag_no,
	);
	$attr = apply_filters( 'feas_form_attr', $attr, $args );

	$output_form = "<form id='feas-searchform-{$manag_no}' action='{$action_url}' method='get' {$inline_style} {$attr}>\n";

	// 保存データ取得
	$get_data = get_db_save_data();

	// 取得データを並び替え
	$get_data = sort_db_save_data( $get_data );

	// 表示した場合チェックを入れる
	$ele_disp = null;

	// 対象投稿タイプをセットしていないとフォームを作らない
	$target_pt = get_option( $feadvns_search_target . $manag_no );

	if ( isset( $target_pt ) ) {

		for ( $i_gd = 0, $cnt_gd = count( $get_data ); $i_gd < $cnt_gd; $i_gd++ ) {

			$html = '';

			// 表示するかしないか取得
			if ( isset( $get_data[$i_gd][$cols[1]] ) && $get_data[$i_gd][$cols[1]] != 1 ) {

				// 前に挿入を取得
				if ( isset( $get_data[$i_gd][$cols[7]] ) && $get_data[$i_gd][$cols[7]] != null ) {
					$html .= str_replace( '\\', '', $get_data[$i_gd][$cols[7]] ) . "\n";
				}

				// ラベル取得
				if ( isset( $get_data[$i_gd][$cols[3]]) && $get_data[$i_gd][$cols[3]] != null ) {
					//$output_form .= "<div class='feas-item-header'>";
					$html .= str_replace( '\\', '', $get_data[$i_gd][$cols[3]] ) ."\n";
					//$output_form .= "</div>\n";
				}

				// エレメント取得
				$html .= create_element( $get_data[$i_gd], $i_gd );

				// 後に挿入を取得
				if ( isset( $get_data[$i_gd][$cols[8]] ) && $get_data[$i_gd][$cols[8]] != null ) {
					$html .= str_replace( '\\', '', $get_data[$i_gd][$cols[8]] ) . "\n";
				}

				/**
				 *
				 * 各検索項目のHTMLにかかるフック
				 *
				 */
				$html = apply_filters( 'feas_form_part_after_html', $html, $manag_no, $i_gd );

				$output_form .= $html;

				// 表示した場合は
				$ele_disp = "disp";
			}
		}
	}

	if ( null != $ele_disp ) {

		// 検索ボタンのラベル取得
		$s_b_label = "検　索";
		$get_data = get_option( $feadvns_search_b_label . $manag_no );

		if ( isset( $get_data ) && null != $get_data ) {
			$s_b_label = $get_data;
		}

		/**
		 * リセットボタン
		 */

		$resetBtnBefore = $resetBtnAfter  = '';

		$reset_btn_sw = get_option( $feadvns_reset_btn_sw . $manag_no );
		if ( $reset_btn_sw ) {

			// JavaScriptによる全項目解除
			$reset_btn_js = get_option( $feadvns_reset_btn_js . $manag_no );
			if ( $reset_btn_js ) {
				$onClickEvent = 'onClick="feas_clear_form(' . $manag_no . ')" ';
			}
			// ボタンの文字列
			$reset_btn_text = get_option( $feadvns_reset_btn_text . $manag_no );
			if ( ! $reset_btn_text ) {
				$reset_btn_text = 'リセット';
			}

			$btnHtml = "<input type='reset' value='{$reset_btn_text}' {$onClickEvent}/>\n";

			// 表示位置
			$reset_btn_position = get_option( $feadvns_reset_btn_position . $manag_no );
			if ( '0' === $reset_btn_position ) {
				$resetBtnBefore = $btnHtml;
			} else {
				$resetBtnAfter = $btnHtml;
			}
		}

		// 前に挿入を取得
		$before_btn   = get_option( $feadvns_search_b_label . $manag_no . "_before" );
		$output_form .= str_replace( '\\', '', $before_btn ) . "\n";

		$output_form .= $resetBtnBefore;

		$output_form .= "<input type='submit' name='searchbutton' id='feas-submit-button-" . esc_attr( $manag_no ) . "' class='feas-submit-button' value='" . esc_attr( $s_b_label ) . "' />\n";

		$output_form .= $resetBtnAfter;

		// 後に挿入を取得
		$after_btn    = get_option( $feadvns_search_b_label . $manag_no . "_after" );
		$output_form .= str_replace( '\\', '', $after_btn ) . "\n";
	}

	$output_form .= "<input type='hidden' name='csp' value='search_add' />\n";
	$output_form .= "<input type='hidden' name='" . esc_attr( $feadvns_max_line . $manag_no ) . "' value='" . esc_attr( get_option( $feadvns_max_line . $manag_no ) ) . "' />\n";

	if ( isset( $chi_manag_no ) && ( $chi_manag_no != 0 ) ) {
		$output_form .= "<input type='hidden' name='fe_form_no' value='" . esc_attr( $chi_manag_no ) . "' />\n";
	} else {
		$output_form .= "<input type='hidden' name='fe_form_no' value='" . esc_attr( $manag_no ) . "' />\n";
	}

	/**
	 *
	 * 検索パーツ類の後に他のプログラムによって差し込まれる処理用のフック（hiddenタグの挿入など）
	 *
	 */
	$output_form = apply_filters( 'feas_form_after_parts', $output_form, $manag_no );

	$output_form .= "</form>\n";

	/**
	 *
	 * 検索フォームの後に他のプログラムによって差し込まれる処理用のフック
	 *
	 */
	$output_form = apply_filters( 'feas_form_after_form', $output_form, $manag_no );

	if ( null == $shortcode_f ) {
		echo $output_form;
	} else {
		return $output_form;
	}
}

/*============================
	検索フォームを作成
 ============================*/
function create_element( $get_data = array(), $number = 0 ) {
	global $wpdb, $cols;

	// 表示しないの場合は処理しない
	if ( $get_data[$cols[1]] == 1 )
		return null;

	// 形式 - なし の場合も処理しない
	if ( ! $get_data[$cols[4]] )
		return null;

	// 並び順を取得する
	$option_order = null;

	// エレメント作成
	if ( "archive" == $get_data[$cols[2]] ) {

		$ret_ele = create_archive_element( $get_data, $number );

	} else if ( "meta_" == mb_substr( $get_data[$cols[2]], 0, 5 ) ) {

		$ret_ele = create_meta_element( $get_data, $number );

	} else if ( "sel_tag" == $get_data[$cols[2]] ) {

		$ret_ele = create_tag_element( $get_data, $number );

	} else {
		$ret_ele = create_category_element( $get_data, $number );

	}
	return $ret_ele;
}

/*============================
	アーカイブ（archive）のエレメント作成
 ============================*/
function create_archive_element( $get_data = array(), $number ) {
	global $wpdb, $cols, $manag_no, $feadvns_search_target, $feadvns_show_count, $feadvns_include_sticky, $feadvns_exclude_id, $feadvns_default_cat;

	$nocnt = false;
	$ret_ele = $showcnt =  null;
	$get_cond = $target_pt = '';
	$sp = array();

	/**
	 *	未選択時の文字列
	 */
	$noselect_text = $get_data[$cols[27]];

	/**
	 *	件数を表示するorしない
	 */
	$showcnt = get_option( $feadvns_show_count . $manag_no );

	/**
	 *	0件のタームを表示しない設定の場合
	 */
/*
	if ( isset( $get_data[$cols[34]] ) && $get_data[$cols[34]] == 'no' ) {
		$nocnt = true;
	}
*/

	/**
	 *	テキスト入力で範囲検索
	 */
	if ( get_option( $cols[22] . $manag_no . "_" . $number ) == 'yes' ) {
		$fe_limit_free_input = true;
		$get_data[$cols[4]] = 'range_by_text';
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

	/**
	 *	固定タクソノミ／タームの設定を取得
	 */
	$fixed_term = get_option( $feadvns_default_cat . $manag_no );

	/**
	 *	条件内の並び順
	 */
	$order_by = "ym"; // ymは年と月を繋いだ値。例：201203

	if ( isset( $get_data[$cols[5]] ) ) {

		switch ( (string) $get_data[$cols[5]] ) {

			case '8':
			case '9':
			case 'a':
				$order_by = "ym";
				break;
			default:
				$order_by = "ym";
				break;
		}
		// 'b'（自由記述）については288行目〜にて
	}

	/**
	 *	条件内の並び順 昇順/降順
	 */
	$order = "ASC";

	if ( isset( $get_data[$cols[35]] ) ) {
		switch ( (string) $get_data[$cols[35]] ) {

			case '8':
			case 'asc':
				$order = "ASC";
				break;
			case '9':
			case 'desc':
				$order = "DESC";
				break;
			default:
				$order = "ASC";
				break;
		}
	}

	// 「要素内の並び順」が「自由記述」の場合は、ターム一覧をDBから呼び出す代わりに記述内容で配列get_catsを構成
	if ( 'b' === $get_data[$cols[5]] ) {

		$options = $get_data[$cols[36]];

		if ( ! empty( $options ) ) {

			$get_archive = array();

			// 行数分ループを回す
			for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {

				if ( empty( $options[$i] ) )
					continue;

				$get_archive[$i] = new stdClass();

				// 値
				$get_archive[$i]->ym = $options[$i]['value'];

				// 表記
				$get_archive[$i]->text = $options[$i]['text'];

				// 階層
				$get_archive[$i]->depth = $options[$i]['depth'];
			}
		}
	}

	// 「自由記述」以外
	else {
		// キャッシュ
		if ( false === ( $get_archive = feas_cache_judgment( $manag_no, 'archive', $number ) ) ) {

			$sql  = " SELECT DISTINCT YEAR( post_date ) AS `year`, MONTH( post_date ) AS `month` , CONCAT( YEAR( post_date ), LPAD( MONTH( post_date ), 2, '0' ) ) AS ym FROM {$wpdb->posts} AS p";
			$sql .= " WHERE 1=1";
			$sql .= " AND p.post_type IN( $target_pt )"; // ToDo: 他の条件と同じくすべての選択肢（=全期間）を表示すべきか
			$sql .= " AND p.post_status IN ( {$post_status} )";
			$sql .= " GROUP BY ym";
			$sql .= " ORDER BY " . $order_by . " " . $order;

			$get_archive = $wpdb->get_results( $sql );

			feas_cache_create( $manag_no, 'archive', $number, $get_archive );
		}
	}

	$cnt_arc = count( $get_archive );

	/**
	 *	件数を取得してキャッシュ保存
	 */
	if ( $get_archive ) {

		$archive_cnt = array();
		foreach( $get_archive as $archive_ym ) {

			if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'arc_cnt_' . $archive_ym->ym, false ) ) ) {
				$sql  = " SELECT count( DISTINCT p.ID ) AS cnt FROM {$wpdb->posts} AS p";
				if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
				$sql .= " WHERE 1=1";
				$sql .= " AND CONCAT( YEAR( post_date ), LPAD( MONTH( post_date ), 2, '0' ) ) = {$archive_ym->ym}";
				if ( $sp ) $sql .= " AND p.ID NOT IN ( $sp )";
				if ( $fixed_term ) $sql .= " AND tr.term_taxonomy_id = " . esc_sql( $fixed_term );
				$sql .= " AND p.post_type IN ( {$target_pt} )";
				$sql .= " AND p.post_status IN ( {$post_status} )";

				$cnt = $wpdb->get_row( $sql );
				feas_cache_create( $manag_no, 'arc_cnt_' . $archive_ym->ym, false, $cnt );
			}
			$archive_cnt[] = $cnt;
		}
	}

	/**
	 *	デフォルト値
	 */
	$default_value = $get_data[$cols[39]];
	if ( $default_value ) {
		$default_value = explode( ',', $default_value );
	}

	switch ( $get_data[$cols[4]] ) {

		/**
		 *	ドロップダウン
		 */
		case 1:
		case 'a':

			$ret_opt = '';

			for ( $i_arc = 0; $i_arc < $cnt_arc; $i_arc++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $archive_cnt[$i_arc]->cnt == 0 )
					continue;

				$selected = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' . $number] ) ) {
						if ( $_GET['search_element_' . $number] == $get_archive[$i_arc]->ym ) {
							$selected = ' selected="selected" ';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_archive[$i_arc]->ym ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				$arc_cnt = '';
				if ( 'yes' == $showcnt ) {
					$arc_cnt = " (" . $archive_cnt[$i_arc]->cnt. ") ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( '1' !== $get_archive[$i_arc]->depth ) {
						$depth = str_pad( $get_archive[$i_arc]->depth, 2, '0', STR_PAD_LEFT );
						for ( $i_depth = 1; $i_depth < $get_archive[$i_arc]->depth; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				/**
				 *
				 * selectのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_arc,
					'value'    => esc_attr( $get_archive[$i_arc]->ym ),
					'selected' => $selected,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_archive_dropdown_attr', $attr, $args );

				// Sanitaize
				$ret_id  = esc_attr( "feas_{$manag_no}_{$number}_{$i_arc}" );
				$ret_val = esc_attr( $get_archive[$i_arc]->ym );

				// 自由記述
				if ( 'b' === $get_data[$cols[5]] ) {
					$ret_text = esc_html( $get_archive[$i_arc]->text . $arc_cnt );
				} else {
					$ret_text = esc_html( $get_archive[$i_arc]->year . "年" . $get_archive[$i_arc]->month . "月" . $arc_cnt );
				}

				$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' {$attr} {$selected}>";
				$html .= $indentSpace . $ret_text;
				$html .= "</option>\n";

				/**
				 *
				 * 各オプションごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no'  => (int) $manag_no,
					'number'    => (int) $number,
					'cnt'       => (int) $i_arc,
					'show_post_cnt'  => $showcnt,
				);
				$html = apply_filters( 'feas_archive_dropdown_html', $html, $args );

				// ループ前段に結合
				$ret_opt .= $html;

			}

			/**
			 *
			 * selectのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$attr = apply_filters( 'feas_archive_dropdown_group_attr', $attr, $args );

			// Sanitize
			$ret_name  = esc_attr( "search_element_{$number}" );
			$ret_id    = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_txt   = esc_html( $noselect_text );

			$html  = "<select name='{$ret_name}' id='{$ret_id}' {$attr}>\n";
			$html .= "<option id='{$ret_id}_none' value=''>\n";
			$html .= $ret_txt;
			$html .= "</option>\n";
			$html .= $ret_opt;
			$html .= "</select>\n";

			/**
			 *
			 * select全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$html = apply_filters( 'feas_archive_dropdown_group_html', $html, $args );

			// ループ前段に結合
			$ret_ele .= $html;

			break;

		/**
		 *	セレクトボックス
		 */
		case 2:
		case 'b':

			$ret_opt = '';
			$selected_cnt = 0;

			for ( $i_arc = 0, $cnt_arc = count( $get_archive ); $i_arc < $cnt_arc; $i_arc++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $archive_cnt[$i_arc]->cnt == 0 )
					continue;

				$selected = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
								if ( $_GET["search_element_" . $number][$i_lists] == $get_archive[$i_arc]->ym ) {
									$selected = ' selected="selected"';
									$selected_cnt++;
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_archive[$i_arc]->ym ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				$arc_cnt = '';
				if ( 'yes' == $showcnt ) {
					$arc_cnt = " (" . $archive_cnt[$i_arc]->cnt. ") ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( '1' !== $get_archive[$i_arc]->depth ) {
						$depth = str_pad( $get_archive[$i_arc]->depth, 2, '0', STR_PAD_LEFT );
						for ( $i_depth = 1; $i_depth < $get_archive[$i_arc]->depth; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				/**
				 *
				 * 各optionのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_arc,
					'value'    => esc_attr( $get_archive[$i_arc]->ym ),
					'selected' => $selected,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_archive_multiple_attr', $attr, $args );

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_arc}" );
				$ret_val  = esc_attr( $get_archive[$i_arc]->ym );

				// 自由記述
				if ( 'b' === $get_data[$cols[5]] ) {
					$ret_text = esc_html( $get_archive[$i_arc]->text . $arc_cnt );
				} else {
					$ret_text = esc_html( $get_archive[$i_arc]->year . "年" . $get_archive[$i_arc]->month . "月" . $arc_cnt );
				}

				$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' {$attr} {$selected}>";
				$html .= $indentSpace . $ret_text;
				$html .= "</option>\n";

				/**
				 *
				 * 各オプションごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no'  => (int) $manag_no,
					'number'    => (int) $number,
					'cnt'       => (int) $i_arc,
					'show_post_cnt'  => $showcnt,
				);
				$html = apply_filters( 'feas_archive_multiple_html', $html, $args );

				// ループ前段に結合
				$ret_opt .= $html;
			}

			// iOSではセレクトボックスが1行にまとめられてしまい、selectedが1件も付いていないと「0項目」と表示されてしまい、未選択時テキストが表示されないため。
			$selected = '';
			if ( 0 === $selected_cnt ) {
				if ( wp_is_mobile() ) {
					$selected = ' selected="selected"';
				}
			}

			/**
			 *
			 * selectのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$attr = apply_filters( 'feas_archive_multiple_group_attr', $attr, $args );

			// Sanitize
			$ret_name  = esc_attr( "search_element_{$number}[]" );
			$ret_id    = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_txt   = esc_html( $noselect_text );

			$html  = "<select name='{$ret_name}' id='{$ret_id}' size='5' multiple='multiple' {$attr}>\n";
			$html .= "<option id='{$ret_id}_none' value='' {$selected}>";
			$html .= $ret_txt;
			$html .= "</option>\n";
			$html .= $ret_opt;
			$html .= "</select>\n";

			/**
			 *
			 * セレクトボックス全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$html = apply_filters( 'feas_archive_multiple_group_html', $html, $args );

			// ループ前段に結合
			$ret_ele .= $html;

			break;

		/**
		 *	チェックボックス
		 */
		case 3:
		case 'c':

			for ( $i_arc = 0, $cnt_arc = count( $get_archive ); $i_arc < $cnt_arc; $i_arc++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $archive_cnt[$i_arc]->cnt == 0 )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
								if ( $_GET["search_element_" . $number][$i_lists] == $get_archive[$i_arc]->ym ) {
									$checked = ' checked="checked"';
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_archive[$i_arc]->ym ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				$arc_cnt = '';
				if ( 'yes' == $showcnt ) {
					$arc_cnt = " (" . $archive_cnt[$i_arc]->cnt. ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $get_archive[$i_arc]->depth, 2, '0', STR_PAD_LEFT );
				}

				/**
				 *
				 * 各チェックボックスのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_arc,
					'value'    => esc_attr( $get_archive[$i_arc]->ym ),
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_archive_checkbox_attr', $attr, $args );

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_arc}" );
				$ret_name = esc_attr( "search_element_{$number}[]" );
				$ret_val  = esc_attr( $get_archive[$i_arc]->ym );

				if ( 'b' === $get_data[$cols[5]] ) {
					$ret_text = esc_html( $get_archive[$i_arc]->text . $arc_cnt );
				} else {
					$ret_text = esc_html( $get_archive[$i_arc]->year . "年" . $get_archive[$i_arc]->month . "月" . $arc_cnt );
				}

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='checkbox' name='{$ret_name}' value='{$ret_val}' {$attr} {$checked} />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				/**
				 *
				 * 各チェックボックスごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no'  => (int) $manag_no,
					'number'    => (int) $number,
					'cnt'       => (int) $i_arc,
					'show_post_cnt'  => $showcnt,
				);
				$html = apply_filters( 'feas_archive_checkbox_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * チェックボックスグループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_archive_checkbox_group_html', $ret_ele, $args );

			break;

		/**
		 *	ラジオボタン
		 */
		case 4:
		case 'd':

			/**
			 *	ラジオボタンの「未選択」の表示/非表示
			 */
			$noselect_status = get_option( $cols[31] . $manag_no . '_' . $number );
			if ( $noselect_status ) {

				$ret_ele .= "<label for='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' class='feas_clevel_01'>";
				$ret_ele .= "<input id='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' type='radio' name='search_element_" . esc_attr( $number ) . "' value='' />";
				$ret_ele .= "<span>" . esc_html( $noselect_text ) . "</span>";
				$ret_ele .= "</label>\n";
			}

			for ( $i_arc = 0, $cnt_arc = count( $get_archive ); $i_arc < $cnt_arc; $i_arc++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $archive_cnt[$i_arc]->cnt == 0 )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' .$number] ) ) {
						if ( $_GET['search_element_' . $number] == $get_archive[$i_arc]->ym ) {
							$checked = ' checked="checked"';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_archive[$i_arc]->ym ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				$arc_cnt = '';
				if ( 'yes' == $showcnt ) {
					$arc_cnt = " (" . $archive_cnt[$i_arc]->cnt. ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $get_archive[$i_arc]->depth, 2, '0', STR_PAD_LEFT );
				}

				/**
				 *
				 * 各ラジオボタンのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_arc,
					'value'    => esc_attr( $get_archive[$i_arc]->ym ),
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_archive_radio_attr', $attr, $args );

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_arc}" );
				$ret_name = esc_attr( "search_element_{$number}" );
				$ret_val  = esc_attr( $get_archive[$i_arc]->ym );

				if ( 'b' === $get_data[$cols[5]] ) {
					$ret_text = esc_html( $get_archive[$i_arc]->text . $arc_cnt );
				} else {
					$ret_text = esc_html( $get_archive[$i_arc]->year . "年" . $get_archive[$i_arc]->month . "月" . $arc_cnt );
				}

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='radio' name='{$ret_name}' value='{$ret_val}' {$attr} {$checked} />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				/**
				 *
				 * 各ラジオボタンごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_arc,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_archive_radio_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * ラジオボタングループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_archive_radio_group_html', $ret_ele, $args );

			break;

		/**
		 *	フリーワード
		 */
		case 5:
		case 'e':

			$placeholder_data = '';
			$placeholder = '';
			$output_js = '';

			$placeholder_data = $get_data[$cols[30]];
			if ( $placeholder_data ) {
				$placeholder = ' placeholder="' . esc_attr( $placeholder_data ) . '"';
				$output_js = '<script>jQuery("#feas_' . esc_attr( $manag_no . '_' . $number ) . '").focus( function() { jQuery(this).attr("placeholder",""); }).blur( function() {
    jQuery(this).attr("placeholder", "' . esc_attr( $placeholder_data ) . '"); });</script>';
			}

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			} else if ( $default_value ) {
				if ( is_array( $default_value ) ) {
					for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
						if ( '' !== $s_keyword ) {
							$s_keyword .= ' ';
						}
						$s_keyword .= $default_value[$i_lists];
					}
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_archive_freeword_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "s_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html  = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$placeholder} {$attr} />";
			$html .= $output_js;

			if ( '' !== $get_data[$cols[20]] ) {
				$html .= create_specifies_the_key_element( $get_data, $number );
			}

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_archive_freeword_group_html', $html, $args );

			$ret_ele .= $html;

			break;

		/**
		 *	テキスト入力で範囲検索
		 */
		case 'range_by_text':

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_archive_range_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "cf_limit_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$attr} />";

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_archive_range_group_html', $html, $args );

			$ret_ele .= $html;

			break;

		/**
		 *	その他
		 */
		default:

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_archive_default_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "s_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html  = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$attr} />";

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_archive_default_group_html', $html, $args );

			$ret_ele .= $html;

			break;
	}

	return $ret_ele;
}

/*============================
	タクソノミー（taxonomy）のエレメント作成
 ============================*/
function create_category_element( $get_data = array(), $number ) {

	global $wpdb, $cols, $manag_no, $feadvns_search_target, $feadvns_show_count, $feadvns_include_sticky, $form_count, $total_cnt, $wp_version, $cols_transient, $feadvns_exclude_id, $feadvns_default_cat;

	/**
	 *	初期化
	 */
	$nocnt = false;
	$sql = $excat = $exids = $exid = $target_pt = $target_sp = $showcnt = $ret_ele = $order_by = $taxonomy = null;
	$excat_array = $sticky = $q_term_id = $sp = $get_cats = array();

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
	$showcnt = get_option( $feadvns_show_count . $manag_no );

	/**
	 *	0件のタームを表示しない設定の場合
	 */
	if ( isset( $get_data[$cols[14]] ) && $get_data[$cols[14]] == 'no' ) {
		$nocnt = true;
	}

	/**
	 *	タクソノミのトップ階層の場合
	 */
	if ( substr( $get_data[$cols[2]], 0, 4 ) == "par_" ) {

		// タクソノミ名を指定
		$taxonomy = substr( $get_data[$cols[2]], 4, strlen( $get_data[$cols[2]] ) - 4 );

		// parentとして0を代入
		$get_data[$cols[2]] = 0;
	}

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

		switch ( (string) $get_data[$cols[5]] ) {
			case '0':
			case '1':
			case 'c':
				$order_by = " t.term_id ";
				break;
			case '2':
			case '3':
			case 'd':
				$order_by = " t.name ";
				break;
			case '4':
			case '5':
			case 'e':
				$order_by = " t.slug ";
				break;
			case '6':
			case 'f':
				$order_by = " t.term_order ";
				break;
			case '7':
			case 'g':
				$order_by = " RAND() ";
				break;
			default:
				$order_by = " t.term_id ";
				break;
		}
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

	/**
	 *	キャッシュ準備
	 */
	if ( 0 === $get_data[$cols[2]] ) {
		$parent_id = $taxonomy;
	} else {
		$parent_id = (int) $get_data[$cols[2]];
	}

	/**
	 *	キャッシュから取得／ない場合は実行してキャッシュ保存
	 */

	// 「要素内の並び順」が「自由記述」の場合は、ターム一覧をDBから呼び出す代わりに記述内容で配列get_catsを構成
	if ( 'b' === $get_data[$cols[5]] ) {

		$options = $get_data[$cols[36]];

		if ( ! empty( $options ) ) {

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
		 * Polylang
		 */
		$lang = '';

		$polylang = get_option( 'polylang' );
		if ( $polylang ) {
			$lang = $polylang['default_lang'];
		}

		$langVar = get_query_var( 'lang' );
		if ( $langVar ) {
			$lang = $langVar;
		}

		$lang_id = '';

		if ( $lang ) {
			$sql = <<<SQL
SELECT tt.term_id
FROM {$wpdb->term_taxonomy} AS tt
LEFT JOIN {$wpdb->terms} AS t
ON tt.term_id = t.term_id
WHERE t.slug = %s
LIMIT 1
SQL;
			$sql = $wpdb->prepare( $sql, $lang );
			$lang_id = $wpdb->get_var( $sql );
		}

		// キャッシュ
		if ( false === ( $get_cats = feas_cache_judgment( $manag_no, 'taxonomy', $parent_id ) ) ) {
			$sql = <<<SQL
SELECT t.term_id, t.name
FROM {$wpdb->terms} AS t
LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
SQL;
			$addSql = ' WHERE tt.parent = %d ';
			$sql .= $wpdb->prepare( $addSql, $get_data[$cols[2]] );
			if ( $taxonomy ) {
				$addSql = ' AND tt.taxonomy = %s ';
				$sql .= $wpdb->prepare( $addSql, $taxonomy );
			}
			if ( $exids ) {
				$addSql = ' AND t.term_id NOT IN ( %d ) ';
				$sql .= $wpdb->prepare( $addSql, $exids );
			}
			if ( $lang_id ) {
				$addSql = <<<SQL
AND tr.object_id IN (
SELECT tr.object_id
FROM {$wpdb->terms} AS t
LEFT JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = t.term_id
LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
WHERE tr.term_taxonomy_id = %d
)
SQL;
				$sql .= $wpdb->prepare( $addSql, $lang_id );
			}
			$sql .= " GROUP BY t.term_id";
			$sql .= " ORDER BY {$order_by} {$order}";

			$get_cats = $wpdb->get_results( $sql );

			feas_cache_create( $manag_no, 'taxonomy', $parent_id, $get_cats );
		}
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

	/**
	 *	 表示する階層の深さの指定が未入力の場合、-1 (=無制限)を代入
	 */
	if ( $get_data[$cols[10]] == "" || $get_data[$cols[10]] == null || !is_numeric( $get_data[$cols[10]] ) ) {
		$term_depth = -1;
	} else {
		$term_depth = intval( $get_data[$cols[10]] );
	}

	/**
	 *	階層が0(=現在の階層のみ表示)以外の場合、子カテゴリ表示のためにGET値を代入してcreate_child_op等に渡す
	 */
	if ( 0 !== $term_depth ) {
		if ( isset( $_GET['search_element_' . $number] ) ) {
			if ( is_array( $_GET['search_element_' . $number] ) ) {
				$q_term_id = $_GET['search_element_' . $number];
			} else {
				$q_term_id[] = esc_html( $_GET['search_element_' . $number] );
			}
		}
	}

	/**
	 *	未選択時の文字列
	 */
	$noselect_text = $get_data[$cols[27]];
	$noselect_text_array = explode(',', $noselect_text );
	$labelCnt = count( $noselect_text_array );

	/**
	 *	デフォルト値
	 */
	$default_value = $get_data[$cols[39]];
	if ( $default_value ) {
		$default_value = explode( ',', $default_value );
	}

	/**
	 *	形式
	 */
	switch ( $get_data[$cols[4]] ) {

		/**
		 *	ドロップダウン
		 */
		case 1:
		case 'a':

			//$ret_ele  = "<div class='feas-item-content'>";

			/*========== Ajaxフィルタリング ==========*/

			if ( 'no' == $get_data[$cols[19]] ) {

				// 初期化
				$checked_before = '';
				$ret_opt = '';

				for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {

					// 0件タームは表示しない場合（post_status処理後の件数を再評価）
					if ( $nocnt && $term_cnt[$i_ele]->cnt === "0")
						continue;

					$selected = '';
					if ( isset( $_GET["fe_form_no"] ) && $_GET["fe_form_no"] == $manag_no ) {
						if ( isset( $_GET['search_element_' . $number] ) ) {
							if ( $_GET['search_element_' . $number][0] == $get_cats[$i_ele]->term_id ) {
								$selected = ' selected="selected"';
								$checked_before = $get_cats[$i_ele]->term_id;
							}
						}
					} else if ( $default_value ) {
						if ( is_array( $default_value ) ) {
							for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
								if ( $default_value[$i_lists] == $get_cats[$i_ele]->term_id ) {
									$selected = ' selected="selected"';
								}
							}
						}
					}

					// カテゴリ毎の件数を表示する設定の場合、件数を代入
					$cat_cnt = '';
					if ( 'yes' == $showcnt ) {
						$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";
					}

					$depth = '01';
					$indentSpace = '';

					// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
					if ( 'b' === $get_data[$cols[5]] ) {
						if ( '1' !== $get_cats[$i_ele]->depth ) {
							$depth = str_pad( $get_cats[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
							for ( $i_depth = 1; $i_depth < $get_cats[$i_ele]->depth; $i_depth++ ) {
								$indentSpace .= '&nbsp;&nbsp;';
							}
						}
					}

					/**
					 *
					 * 各optionのattrにかけるフィルター
					 *
					 */
					$attr = '';
					$args = array(
						'manag_no' => (int) $manag_no,
						'number'   => (int) $number,
						'cnt'      => (int) $i_ele,
						'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
						'selected' => $selected,
						'show_post_cnt' => $showcnt,
					);
					$attr = apply_filters( 'feas_term_dropdown_attr', $attr, $args );

					// Sanitaize
					$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_0_{$i_ele}" );
					$ret_val  = esc_attr( $get_cats[$i_ele]->term_id );
					$ret_text = esc_html( $get_cats[$i_ele]->name . $cat_cnt );

					$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' {$attr} {$selected}>";
					$html .= $indentSpace . $ret_text;
					$html .= "</option>\n";

					$form_count = 0;

					/**
					 *
					 * 各optionごとにかけるフィルター
					 *
					 */
					$args = array(
						'manag_no' => (int) $manag_no,
						'number'   => (int) $number,
						'cnt'      => (int) $i_ele,
						'depth'    => 1,
						'show_post_cnt'  => $showcnt,
						'ajax'     => 1,
					);
					$html = apply_filters( 'feas_term_dropdown_html', $html, $args );

					$ret_opt .= $html;

				}

				/**
				 *
				 * selectのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'parent'   => 0,
					'depth'    => 0,
					'show_post_cnt' => $showcnt,
					'ajax'     => 1,
				);
				$attr = apply_filters( 'feas_term_dropdown_group_attr', $attr, $args );

				// Sanitize
				$ret_name     = esc_attr( "search_element_{$number}[]" );
				$ret_id       = esc_attr( "feas_{$manag_no}_{$number}" );
				$ret_class    = esc_attr( "ajax_{$number}_0" );
				$ret_onchange = esc_attr( "ajax_filtering_next({$manag_no},{$number},0,'{$showcnt}',{$term_depth})" );
				$ret_txt      = esc_html( $noselect_text_array[0] );

				$ret_ele .= "<select name='{$ret_name}' class='{$ret_class}' onChange='{$ret_onchange}' {$attr}>\n";
				$ret_ele .= "<option id='{$ret_id}_none' value=''>";
				$ret_ele .= $ret_txt;
				$ret_ele .= "</option>\n";
				$ret_ele .= $ret_opt;
				$ret_ele .= "</select>\n";

				/**
				 *
				 * select全体にかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'parent'   => 0,
					'depth'    => 0,
					'show_post_cnt' => $showcnt,
					'ajax'     => 1,
				);
				$ret_ele = apply_filters( 'feas_term_dropdown_group_html', $ret_ele, $args );


				// 階層の指定がある場合で、検索実行前
				if ( 1 < $term_depth && ! is_search() && ! $default_value ) {

					// 空のドロップダウン生成。生成数は「フォームの内容」の「階層」にて指定
					for ( $iSel = 1; $iSel < $term_depth; $iSel++ ) {

						/**
						 *
						 * 各optionのattrにかけるフィルター
						 *
						 */
						$attr = '';
						$args = array(
							'manag_no' => (int) $manag_no,
							'number'   => (int) $number,
							'cnt'      => (int) $iSel,
							'selected' => $selected,
							'show_post_cnt' => $showcnt,
						);
						$attr = apply_filters( 'feas_term_dropdown_attr', $attr, $args );

						$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$iSel}" );
						$ret_txt  = esc_html( isset( $noselect_text_array[$iSel] ) ? $noselect_text_array[$iSel] : $noselect_text_array[$labelCnt-1] );

						$option = "<option id='{$ret_id}_none' value='' {$attr}>";
						$option .= $ret_txt;
						$option .= "</option>\n";

						/**
						 *
						 * 各optionごとにかけるフィルター
						 *
						 */
						$args = array(
							'manag_no' => (int) $manag_no,
							'number'   => (int) $number,
							'cnt'      => (int) $i_ele,
							'depth'    => (int) $iSel,
							'show_post_cnt'  => $showcnt,
							'ajax'     => 1,
						);
						$option = apply_filters( 'feas_term_dropdown_html', $option, $args );

						/**
						 *
						 * selectのattrにかけるフィルター
						 *
						 */
						$attr = '';
						$args = array(
							'manag_no' => (int) $manag_no,
							'number'   => (int) $number,
							'parent'   => 0,
							'depth'    => (int) $iSel,
							'show_post_cnt' => $showcnt,
						);
						$attr = apply_filters( 'feas_term_dropdown_group_attr', $attr, $args );

						// Sanitize
						$ret_name     = esc_attr( "search_element_{$number}[]" );
						$ret_id       = esc_attr( "feas_{$manag_no}_{$number}" );
						$ret_class    = esc_attr( "ajax_{$number}_{$iSel}" );
						$ret_onchange = esc_attr( "ajax_filtering_next({$manag_no},{$number},{$iSel},'{$showcnt}',{$term_depth})" );
						$ret_txt      = esc_html( $noselect_text_array[0] );

						$html  = "<select name='{$ret_name}' class='{$ret_class}' onChange='{$ret_onchange}' {$attr}>\n";
						$html .= $option;
						$html .= "</select>\n";

						/**
						 *
						 * select全体にかけるフィルター
						 *
						 */
						$args = array(
							'manag_no' => (int) $manag_no,
							'number'   => (int) $number,
							'parent'   => 0,
							'depth'    => (int) $iSel,
							'show_post_cnt' => $showcnt,
							'ajax'     => 1,
						);
						$html = apply_filters( 'feas_term_dropdown_group_html', $html, $args );

						$ret_ele .= $html;
					}

				} else {

					$childDepth = '';

					/**
					 *	検索実行後
					 *
					 *	search.php遷移時に子カテゴリのドロップダウンを生成
					 */
					if ( isset( $_GET['search_element_' . $number] ) && is_array( $_GET['search_element_' . $number] ) ) {

						// 階層指定がない場合、実際に条件指定された階層分のドロップダウンを生成
						if ( -1 === $term_depth ) {
							$childDepth = count( $_GET['search_element_' . $number] );
						} else {
							$childDepth = $term_depth;
						}

						// 検索クエリ（配列）
						$searchQuery = $_GET['search_element_' . $number];

					/**
					 *
					 *	デフォルト値の設定がある場合（検索前）
					 *
					 */
					} else if ( ! empty( $default_value ) ) {

						//$childDepth = count( $default_value );
						$childDepth = $term_depth;

						// デフォルト値（配列）
						$searchQuery = $default_value;
					}

					if ( $childDepth ) {

						// ドロップダウン二段目以降なので0ではなく1からカウンターを回す
						for ( $outer = 1; $outer < $childDepth; $outer++ ) {

							// 初期化
							$get_cats = '';

							// 条件が指定されないときはスルーして、空のドロップダウンのみ生成
							if ( isset( $searchQuery[$outer-1] ) && ! empty( $searchQuery[$outer-1] ) ) {
								if ( false === ( $get_cats = feas_cache_judgment( $manag_no, 'taxonomy', (int) $searchQuery[$outer-1] ) ) ) {
									// ターム一覧を取得
									$sql  = " SELECT t.term_id, t.name FROM {$wpdb->terms} AS t";
									$sql .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
									$sql .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id";
									//$sql .= " LEFT JOIN {$wpdb->posts} AS p ON tr.object_id = p.ID";
									$sql .= " WHERE tt.parent = " . esc_sql( $searchQuery[$outer-1] );
									if ( $taxonomy ) $sql .= " AND tt.taxonomy = '" . esc_sql( $taxonomy ) . "'";
									if ( $exids )    $sql .= " AND t.term_id NOT IN (" . esc_sql( $exids ) . ")";
									//$sql .= " AND p.post_type IN( {$target_pt} )";
									$sql .= " GROUP BY t.term_id";
									$sql .= " ORDER BY " . esc_sql( $order_by );
									$get_cats = $wpdb->get_results( $sql );

									feas_cache_create( $manag_no, 'taxonomy', (int) $searchQuery[$outer-1], $get_cats );
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
											$sql .= " AND p.post_status = 'publish'";

											$cnt = $wpdb->get_row( $sql );
											feas_cache_create( $manag_no, 'term_cnt_' . $term_id->term_id, false, $cnt );
										}
										$term_cnt[] = $cnt;
									}
								}
							}

							if ( $get_cats ) {

								$ret_opt = '';

								for ( $inner = 0; $inner < $cnt_ele; $inner++ ) {

									// 0件タームは表示しない場合
									if ( $nocnt && $term_cnt[$inner]->cnt === "0" )
										continue;

									$selected = '';
									if ( isset( $searchQuery[$outer] ) ) {
										if ( $searchQuery[$outer] == $get_cats[$inner]->term_id ) {
											$selected = ' selected="selected"';
											$checked_before = $get_cats[$inner]->term_id;
										}
									}

									$cat_cnt = '';
									if ( 'yes' == $showcnt ) {
										$cat_cnt = " (" . $term_cnt[$inner]->cnt . ") ";
									}

									/**
									 *
									 * 各optionのattrにかけるフィルター
									 *
									 */
									$attr = '';
									$args = array(
										'manag_no' => (int) $manag_no,
										'number'   => (int) $number,
										'cnt'      => (int) $inner,
										'depth'    => (int) $outer,
										'selected' => $selected,
										'show_post_cnt' => $showcnt,
										'ajax'     => 1,
									);
									$attr = apply_filters( 'feas_term_dropdown_attr', $attr, $args );

									// Sanitaize
									$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$outer}_{$inner}" );
									$ret_val  = esc_attr( $get_cats[$inner]->term_id );
									$ret_text = esc_html( $get_cats[$inner]->name . $cat_cnt );

									$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_01' {$attr} {$selected}>";
									$html .= $ret_text;
									$html .= "</option>\n";

									/**
									 *
									 * 各optionごとにかけるフィルター
									 *
									 */
									$args = array(
										'manag_no' => (int) $manag_no,
										'number'   => (int) $number,
										'cnt'      => (int) $inner,
										'depth'    => (int) $outer,
										'show_post_cnt'  => $showcnt,
										'ajax'     => 1,
									);
									$html = apply_filters( 'feas_term_dropdown_html', $html, $args );

									$ret_opt .= $html;

									$form_count = 0;
								}
							}

							/**
							 *
							 * selectのattrにかけるフィルター
							 *
							 */
							$attr = '';
							$args = array(
								'manag_no' => (int) $manag_no,
								'number'   => (int) $number,
								'parent'   => 0,
								'depth'    => (int) $outer,
								'show_post_cnt' => $showcnt,
								'ajax'     => 1,
							);
							$attr = apply_filters( 'feas_term_dropdown_group_attr', $attr, $args );

							// Sanitize
							$ret_name     = esc_attr( "search_element_{$number}[]" );
							$ret_id       = esc_attr( "feas_{$manag_no}_{$number}" );
							$ret_class    = esc_attr( "ajax_{$number}_{$outer}" );
							$ret_onchange = esc_attr( "ajax_filtering_next({$manag_no},{$number},{$outer},'{$showcnt}',{$term_depth})" );
							$ret_txt      = esc_html( ( isset( $noselect_text_array[$outer] ) ? $noselect_text_array[$outer] : $noselect_text_array[$labelCnt-1] ) );

							$ret_ele .= "<select name='{$ret_name}' class='{$ret_class}' onChange='{$ret_onchange}' {$attr}>\n";
							$ret_ele .= "<option id='{$ret_id}_none' value=''>";
							$ret_ele .= $ret_txt;
							$ret_ele .= "</option>\n";
							$ret_ele .= $ret_opt;
							$ret_ele .= "</select>\n";

							/**
							 *
							 * select全体にかけるフィルター
							 *
							 */
							$args = array(
								'manag_no' => $manag_no,
								'number'   => $number,
								'parent'   => 0,
								'depth'    => (int) $outer,
								'show_post_cnt' => $showcnt,
								'ajax'     => 1,
							);
							$ret_ele = apply_filters( 'feas_term_dropdown_group_html', $ret_ele, $args );
						}
					}
				}

				/**
				 *
				 * Ajaxフィルタリング全体にかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => $manag_no,
					'number'   => $number,
					'parent'   => 0,
					'depth'    => -1,
					'show_post_cnt' => $showcnt,
					'ajax'     => 1,
				);
				$ret_ele = apply_filters( 'feas_term_ajax_dropdown_group_html', $ret_ele, $args );


			/*========== 普通のドロップダウン ==========*/

			} else {

				$ret_opt = '';

				for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {

					// 0件タームは表示しない場合
					if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
						continue;

					$selected = '';
					if ( isset( $_GET["fe_form_no"] ) && $_GET["fe_form_no"] == $manag_no ) {
						if ( isset( $_GET['search_element_' . $number] ) ) {
							if ( $_GET['search_element_' . $number] == $get_cats[$i_ele]->term_id ) {
								$selected = ' selected="selected"';
							}
						}
					} else if ( $default_value ) {
						if ( is_array( $default_value ) ) {
							for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
								if ( $default_value[$i_lists] == $get_cats[$i_ele]->term_id ) {
									$selected = ' selected="selected"';
								}
							}
						}
					}

					// カテゴリ毎の件数を表示する設定の場合、件数を代入
					$cat_cnt = '';
					if ( "yes" == $showcnt ) {
						$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";
					}

					$depth = '01';
					$indentSpace = '';

					// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
					if ( 'b' === $get_data[$cols[5]] ) {
						if ( '1' !== $get_cats[$i_ele]->depth ) {
							$depth = str_pad( $get_cats[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
							for ( $i_depth = 1; $i_depth < $get_cats[$i_ele]->depth; $i_depth++ ) {
								$indentSpace .= '&nbsp;&nbsp;';
							}
						}
					}

					/**
					 *
					 * 各optionのattrにかけるフィルター
					 *
					 */
					$attr = '';
					$args = array(
						'manag_no' => (int) $manag_no,
						'number'   => (int) $number,
						'cnt'      => (int) $i_ele,
						'parent'   => 0,
						'depth'    => 1,
						'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
						'selected' => $selected,
						'show_post_cnt' => $showcnt,
						'ajax'     => 0,
					);
					$attr = apply_filters( 'feas_term_dropdown_attr', $attr, $args );

					// Sanitaize
					$ret_id  = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
					$ret_val = esc_attr( $get_cats[$i_ele]->term_id );
					$ret_txt = esc_html( $get_cats[$i_ele]->name . $cat_cnt );

					$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' {$attr} {$selected}>";
					$html .= $indentSpace . $ret_txt;
					$html .= "</option>\n";

					$form_count = 0;

					// todo: 引数を配列化する

					// 「自由記述」ではない、かつ階層が０(=現在の階層のみ表示)以外の場合、子カテゴリを表示
					if ( 'b' !== $get_data[$cols[5]] && 0 !== $term_depth ) {
						// 子カテゴリ取得
						$html .= create_child_op( $get_cats[$i_ele]->term_id, $term_depth, $class_cnt = 2, $q_term_id, $nocnt, $exids, $sticky, $showcnt, null, $taxonomy, $i_ele, $number, $sp, $order_by );
					}

					/**
					 *
					 * 各optionごとにかけるフィルター
					 *
					 */
					$args = array(
						'manag_no' => $manag_no,
						'number'   => $number,
						'cnt'      => $i_ele,
						'parent'   => 0,
						'depth'    => 1,
						'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
						'show_post_cnt'  => $showcnt,
						'ajax'     => 0,
					);
					$html = apply_filters( 'feas_term_dropdown_html', $html, $args );

					$ret_opt .= $html;

				}

				/**
				 *
				 * selectのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'parent'   => 0,
					'depth'    => 1,
					'show_post_cnt' => $showcnt,
					'ajax'     => 0,
				);
				$attr = apply_filters( 'feas_term_dropdown_group_attr', $attr, $args );

				// Sanitize
				$ret_name = esc_attr( "search_element_{$number}" );
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
				$ret_txt  = esc_html( $noselect_text_array[0] );

				$ret_ele .= "<select name='{$ret_name}' id='{$ret_id}' {$attr}>\n";
				$ret_ele .= "<option id='{$ret_id}_none' value=''>";
				$ret_ele .= $ret_txt;
				$ret_ele .= "</option>\n";
				$ret_ele .= $ret_opt;
				$ret_ele .= "</select>\n";

				/**
				 *
				 * select全体にかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => $manag_no,
					'number'   => $number,
					'parent'   => 0,
					'depth'    => 1,
					'show_post_cnt' => $showcnt,
					'ajax'     => 0,
				);
				$ret_ele = apply_filters( 'feas_term_dropdown_group_html', $ret_ele, $args );
			}

			break;

		/**
		 *	セレクトボックス
		 */
		case 2:
		case 'b':

			$ret_opt = '';
			$selected_cnt = 0;

			for ( $i_ele = 0, $cnt_ele = count( $get_cats ); $i_ele < $cnt_ele; $i_ele++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
					continue;

				$selected = '';

				if ( isset( $_GET["fe_form_no"] ) && $_GET["fe_form_no"] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
								if ( $_GET["search_element_" . $number][$i_lists] == $get_cats[$i_ele]->term_id ) {
									$selected = ' selected="selected"';
									$selected_cnt++;
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_cats[$i_ele]->term_id ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				// カテゴリ毎の件数を表示する設定の場合、件数を代入
				$cat_cnt = '';
				if ( 'yes' == $showcnt ) {
					$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( '1' !== $get_cats[$i_ele]->depth ) {
						$depth = str_pad( $get_cats[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
						$indentSpace = '';
						for ( $i_depth = 1; $i_depth < $get_cats[$i_ele]->depth; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				/**
				 *
				 * 各optionのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
					'selected' => $selected,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_term_multiple_attr', $attr, $args );

				// Sanitize
				$ret_id  = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_val = esc_attr( $get_cats[$i_ele]->term_id );
				$ret_txt = esc_html( $get_cats[$i_ele]->name . $cat_cnt );

				$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' {$attr} {$selected}>";
				$html .= $indentSpace . $ret_txt;
				$html .= "</option>\n";

				// 階層が０(=現在の階層のみ表示)以外の場合、子カテゴリを表示
				if ( 'b' !== $get_data[$cols[5]] && 0 !== $term_depth ) {
					$html .= create_child_op( $get_cats[$i_ele]->term_id, $term_depth, $class_cnt = 2, $q_term_id, $nocnt, $exids, $sticky, $showcnt, null, $taxonomy, $i_ele, $number, $sp, $order_by );
				}

				/**
				 *
				 * 各オプションごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no'  => (int) $manag_no,
					'number'    => (int) $number,
					'cnt'       => (int) $i_ele,
					'depth'     => 1,
					'show_post_cnt'  => $showcnt,
				);
				$html = apply_filters( 'feas_term_multiple_html', $html, $args );

				// ループ前段に結合
				$ret_opt .= $html;
			}

			// iOSではセレクトボックスが1行にまとめられてしまい、selectedが1件も付いていないと「0項目」と表示されてしまい、未選択時テキストが表示されないため。
			$selected = '';
			if ( 0 === $selected_cnt ) {
				if ( wp_is_mobile() ) {
					$selected = ' selected="selected"';
				}
			}

			/**
			 *
			 * セレクトボックスのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$attr = apply_filters( 'feas_term_multiple_group_attr', $attr, $args );

			// Sanitize
			$ret_name  = esc_attr( "search_element_{$number}[]" );
			$ret_id    = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_txt   = esc_html( $noselect_text );

			$html  = "<select name='{$ret_name}' id='{$ret_id}' size='5' multiple='multiple' {$attr}>\n";
			$html .= "<option id='{$ret_id}_none' value='' {$selected}>";
			$html .= $ret_txt;
			$html .= "</option>\n";
			$html .= $ret_opt;
			$html .= "</select>\n";

			/**
			 *
			 * セレクトボックス全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'parent'   => 0,
				'depth'    => 1,
				'show_post_cnt' => $showcnt,
			);
			$html = apply_filters( 'feas_term_multiple_group_html', $html, $args );

			// ループ前段に結合
			$ret_ele .= $html;

			break;

		/**
		 *	チェックボックス
		 */
		case 3:
		case 'c':

			$total_cnt = 0;	// 子カテゴリのチェックボックスと累積生成カウント数を取得のため

			for ( $i_ele = 0, $cnt_ele = count( $get_cats ); $i_ele < $cnt_ele; $i_ele++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
					continue;

				$checked = '';
				if ( isset( $_GET["fe_form_no"] ) && $_GET["fe_form_no"] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
								if ( $_GET["search_element_" . $number][$i_lists] == $get_cats[$i_ele]->term_id ) {
									$checked = ' checked="checked"';
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_cats[$i_ele]->term_id ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				$cat_cnt = '';
				if ( 'yes' == $showcnt ) {
					$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $get_cats[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
				}

				/**
				 *
				 * 各チェックボックスのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'parent'   => 0,
					'depth'    => 1,
					'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_term_checkbox_attr', $attr, $args );

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_name = esc_attr( "search_element_{$number}[]" );
				$ret_val  = esc_attr( $get_cats[$i_ele]->term_id );
				$ret_text = esc_html( $get_cats[$i_ele]->name . $cat_cnt );

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='checkbox' name='{$ret_name}' value='{$ret_val}' {$attr} {$checked} />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				$total_cnt++;

				// 「要素内の並び順」が「自由記述」以外、または階層が0(=現在の階層のみ表示)以外の場合、子カテゴリを表示
				if ( 'b' !== $get_data[$cols[5]] && 0 !== $term_depth ) {
					// 子カテゴリ取得
					$html .= create_child_check( $get_cats[$i_ele]->term_id, "feas_clevel_", $term_depth = -1, $class_cnt = 2, $nocnt, $exids, $sticky, $showcnt, $taxonomy, $i_ele, $number, $sp, $order_by );
				}

				/**
				 *
				 * 各チェックボックスごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'parent'   => 0,
					'depth'    => 1,
					'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
					'checked'  => $checked,
					'show_post_cnt'  => $showcnt,
				);
				$html = apply_filters( 'feas_term_checkbox_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * チェックボックスグループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'parent'   => 0,
				'depth'    => 1,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_term_checkbox_group_html', $ret_ele, $args );

			break;

		/**
		 *	ラジオボタン
		 */
		case 4:
		case 'd':

			/**
			 *	ラジオボタンの「未選択」の表示/非表示
			 */
			$noselect_status = get_option( $cols[31] . $manag_no . '_' . $number );
			if ( $noselect_status ) {

				$ret_ele .= "<label for='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' class='feas_clevel_01'>";
				$ret_ele .= "<input id='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' type='radio' name='search_element_" . esc_attr( $number ) . "' value='' />";
				$ret_ele .= "<span>" . esc_html( $noselect_text ) . "</span>";
				$ret_ele .= "</label>\n";
			}

			for ( $i_ele = 0, $cnt_ele = count( $get_cats ); $i_ele < $cnt_ele; $i_ele++ ) {

				// 0件タームは表示しない場合
				if ( $nocnt && $term_cnt[$i_ele]->cnt === "0" )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' . $number] ) ) {
						if ( $_GET['search_element_' . $number] == $get_cats[$i_ele]->term_id ) {
							$checked = ' checked="checked"';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_cats[$i_ele]->term_id ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				$cat_cnt = '';
				if ( "yes" == $showcnt ) {
					$cat_cnt = " (" . $term_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $get_cats[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
				}

				/**
				 *
				 * 各ラジオボタンのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'parent'   => 0,
					'depth'    => 1,
					'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_term_radio_attr', $attr, $args );

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_name = esc_attr( "search_element_{$number}" );
				$ret_val  = esc_attr( $get_cats[$i_ele]->term_id );
				$ret_text = esc_html( $get_cats[$i_ele]->name . $cat_cnt );

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='radio' name='{$ret_name}' value='{$ret_val}' {$attr} {$checked} />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				// 「要素内の並び順」が「自由記述」以外、または階層が0(=現在の階層のみ表示)以外の場合、子カテゴリを表示
				if ( 'b' !== $get_data[$cols[5]] && 0 !== $term_depth ) {
					// 子カテゴリ取得
					$html .= create_child_radio( $get_cats[$i_ele]->term_id, "feas_clevel_", $term_depth, $class_cnt = 2, $nocnt, $exids, $sticky, $showcnt, $taxonomy, $i_ele, $number, $sp, $order_by );
				}

				/**
				 *
				 * 各ラジオボタンごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'parent'   => 0,
					'depth'    => 1,
					'value'    => esc_attr( $get_cats[$i_ele]->term_id ),
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_term_radio_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * ラジオボタングループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'parent'   => 0,
				'depth'    => 1,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_term_radio_group_html', $ret_ele, $args );

			break;

		/**
		 *	フリーワード
		 */
		case 5:
		case 'e':

			$placeholder_data = '';
			$placeholder = '';
			$output_js = '';

			$placeholder_data = $get_data[$cols[30]];

			if ( $placeholder_data ) {
				$placeholder = ' placeholder="' . esc_attr( $placeholder_data ) . '"';
				$output_js = '<script>jQuery("#feas_' . esc_attr( $manag_no . '_' . $number ) . '").focus( function() { jQuery(this).attr("placeholder",""); }).blur( function() {
    jQuery(this).attr("placeholder", "' . esc_attr( $placeholder_data ) . '"); });</script>';
			}

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			} else if ( $default_value ) {
				if ( is_array( $default_value ) ) {
					for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
						if ( '' !== $s_keyword ) {
							$s_keyword .= ' ';
						}
						$s_keyword .= $default_value[$i_lists];
					}
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_term_freeword_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "s_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html  = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$placeholder} {$attr} />";
			$html .= $output_js;

			// hiddenタグ出力
			if ( '' !== $get_data[$cols[20]] ) {
				$html .= create_specifies_the_key_element( $get_data, $number );
			}

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_term_freeword_group_html', $html, $args );

			$ret_ele .= $html;

			break;

		/**
		 *	その他
		 */
		default:

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_term_default_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "s_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html  = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$placeholder} {$attr} />";

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_term_default_group_html', $html, $args );

			$ret_ele .= $html;

			break;
	}

	return $ret_ele;
}

/*============================
	カスタムフィールド（post_meta）のエレメント作成
 ============================*/
function create_meta_element( $get_data = array(), $number ) {
	global $wpdb, $cols, $feadvns_show_count, $manag_no, $feadvns_include_sticky, $feadvns_search_target, $feadvns_exclude_id, $feadvns_default_cat;

	$nocnt = false;
	$get_key = $get_unit = $kugiriFlag = '';
	$acfChoices = $sp = $savedValues = array();

	/**
	 * Smart Custom Fields 関連
	 */

	// 「真偽値」として扱う
	// ToDo: 下記[33]に統合
	$cf_shingi = get_option( $cols[24] . $manag_no . "_" . $number );

	// SmartCustomFields関連　1=真偽値　2=関連する投稿　3=関連するターム
	$cf_scf = get_option( $cols[33] . $manag_no . "_" . $number );

	/**
	 * Advanced Custom Fields 関連
	 */
	$acf_flag = $get_data[$cols[38]];

	// キー取得（meta_を除いた部分）
	$get_key = mb_substr( $get_data[$cols[2]], 5, mb_strlen( $get_data[$cols[2]] ) );

	// ACF複数選択形式で登録された値の場合は表記一覧を取得
	if ( $get_key ) {
		$sql = <<<SQL
SELECT post_content
FROM {$wpdb->posts}
WHERE post_excerpt = %s
LIMIT 1
SQL;
		$sql = $wpdb->prepare( $sql, $get_key );
		$acfData = $wpdb->get_var( $sql );
		if ( $acfData ) {
			$acfData = maybe_unserialize( $acfData );
			if ( array_key_exists( 'choices', $acfData ) ) {
				$acfChoices = $acfData['choices'];
			}
		}
	}

	/**
	 *	範囲検索
	 */

	$rangeValue = $get_data[$cols[16]];
	$rangeFlag = false;
	if ( 1 <= (int) $rangeValue ) {
		$rangeFlag = true;
	}

	// 単位を付与
	if ( $get_data[$cols[17]] . $number != "" ) {
		$cfUnit = $get_data[$cols[17]];
	}

	$kugiriFlag = $get_data[$cols[18]];

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

		// 除外IDにマージ
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

	/**
	 *	固定タクソノミ／タームの設定を取得
	 */
	$fixed_term = get_option( $feadvns_default_cat . $manag_no );

	/**
	 *	0件のカスタムフィールドを表示しない設定の場合
	 */
/*
	if ( isset( $get_data[$cols[33]] ) && $get_data[$cols[33]] == 'no' ) {
		$nocnt = true;
	}
*/

	/**
	 * formパーツ内の並び順
	 */

	// デフォルト値
	$order_by = "pm.meta_id";

	if ( isset( $get_data[$cols[5]] ) ) {
		switch ( (string) $get_data[$cols[5]] ) {

			// 開始番号は、management-viewの「並び順」ドロップダウンにおいて、「ターム（0〜7）」「年月（8〜9）」に続くもの
			case '10':
			case '11':
			case 'h':
				$order_by = "pm.meta_id";
				break;
			case '12':
			case '13':
			case '14':
			case '15':
			case 'i':
				$order_by = "REPLACE( pm.meta_value, ',', '' )";
				break;
			case '16':
			case 'j':
				$order_by = "RAND()";
				break;
			default:
				$order_by = "pm.meta_id";
				break;
		}
	}

	/**
	 *	条件内の並び順 数値or文字列
	 */
	$sort_as = "";

	if ( isset( $get_data[$cols[34]] ) ) {
		switch ( $get_data[$cols[34]] ) {

			case 'int':
				$sort_as = "+0";
				break;
			case 'str':
				$sort_as = "";
				break;
			default:
				$sort_as = "";
				break;
		}
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

	/**
	 *	キャッシュから取得／ない場合は実行してキャッシュ保存
	 */

	// 「要素内の並び順」が「自由記述」の場合は、ターム一覧をDBから呼び出す代わりに記述内容で配列$savedValuesを構成
	// キャッシュは利用しない
	if ( 'b' === $get_data[$cols[5]] ) {

		$options = $get_data[$cols[36]];

		if ( ! empty( $options ) ) {

			$savedValues = array();

			// 行数分ループを回す
			for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {

				if ( empty( $options[$i] ) )
					continue;

				// 自由記述において、コロンの右側にACFの記述どおりの内容をダブルクォーテーションで囲んで記述した場合の対処
				// v1.9.3以降、コロンの右側にはACFの値のみ記述すればOK
				$valueTmp = array();
				$valueTmp = explode( ':', $options[$i]['value'] );
				$valueTmp = trim( $valueTmp[0] ); // コロンの左側の値のみ取得
				$savedValues[$i]['meta_value'] = $valueTmp;

				// 「:」の前半は表記
				$savedValues[$i]['text'] = $options[$i]['text'];

				// 階層
				$savedValues[$i]['depth'] = $options[$i]['depth'];
			}
		}
	}

	// 「自由記述」ではない場合
	else {

		// キャッシュがない場合
		if ( false === ( $savedValues = feas_cache_judgment( $manag_no, 'post_meta', $get_key ) ) ) {
			$sql  = " SELECT meta_id, post_id, meta_value FROM {$wpdb->postmeta} AS pm";
			$sql .= " LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id";
			$sql .= " WHERE pm.meta_key = '" . esc_sql( $get_key ) . "'";
			if ( '1' === $cf_scf ) $sql .= " AND pm.meta_value = '1'";
			$sql .= " AND pm.meta_value IS NOT NULL";
			$sql .= " AND pm.meta_value != ''";
			$sql .= " AND p.post_type IN( {$target_pt} )";
			$sql .= " AND p.post_status IN ( {$post_status} )";
			$sql .= " GROUP BY pm.meta_value";
			$sql .= " ORDER BY " . $order_by . $sort_as . " " . $order;
			$savedValues = $wpdb->get_results( $sql, ARRAY_A );
			feas_cache_create( $manag_no, 'post_meta', $get_key, $savedValues );
		}
	}

	if ( get_option( $cols[22] . $manag_no . "_" . $number ) == 'yes' ) {
		$fe_limit_free_input = true;
		$get_data[$cols[4]] = 'cf_limit_keyword';
	}

	if ( is_array( $savedValues ) ) {
		$metaValues = $metaLabels = array();
		foreach( $savedValues as $k => $v ) {

			// ACFなどシリアライズされた値を分解して選択肢に追加
			$unserValue = maybe_unserialize( $v['meta_value'] );

			// 複数選択形式で保存された値
			if ( is_array( $unserValue ) ) {
				foreach( $unserValue as $uv ) {
					if ( array_key_exists( $uv, $acfChoices ) ) {
						$metaLabels[] = $acfChoices[$uv];
					} else {
						$metaLabels[] = $uv;
					}
					$metaValues[] = $uv;
				}

			// それ以外
			} else {
				if ( array_key_exists( $unserValue, $acfChoices ) ) {
					$metaLabels[] = $acfChoices[$unserValue];
				} else {
					$metaLabels[] = $unserValue;
				}
				$metaValues[] = $unserValue;
			}
		}

		// 重複削除
		$metaValues = array_unique( $metaValues );
		// インデックス振り直し
		$metaValues = array_values( $metaValues );
	}

	// 検索条件の選択肢の数
	$cntValues = count( $metaValues );

	/**
	 *	件数を取得してキャッシュ保存
	 */
	if ( $metaValues ) {

		$cf_cnt = array();
		foreach( $metaValues as $cf_data ) {

			if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'cf_cnt_' . $cf_data, false ) ) ) {
				$sql  = " SELECT count( meta_value ) AS cnt FROM {$wpdb->postmeta} AS pm";
				$sql .= " INNER JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID";
				if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
				$sql .= " WHERE 1=1";
				$sql .= " AND meta_key = '" . esc_sql( $get_key ) . "'";
				if ( $acf_flag ) {
					$cf_data = esc_sql( $cf_data );
					$sql .= " AND ( meta_value LIKE '%\"{$cf_data}\"%' )";
				} else {
					if ( $rangeFlag ) {
						if ( '1' === $rangeValue ) {
							$sql .= " AND meta_value < " . esc_sql( $cf_data );
						} elseif ( '2' === $rangeValue ) {
							$sql .= " AND meta_value <= " . esc_sql( $cf_data );
						} elseif ( '3' === $rangeValue ) {
							$sql .= " AND meta_value >= " . esc_sql( $cf_data );
						} elseif ( '4' === $rangeValue ) {
							$sql .= " AND meta_value > " . esc_sql( $cf_data );
						}
					} else {
						$sql .= " AND meta_value = '" . esc_sql( $cf_data ) . "'";
					}
				}
				if ( $sp ) $sql .= " AND p.ID NOT IN ( {$sp} )";
				if ( $fixed_term ) $sql .= " AND tr.term_taxonomy_id = " . esc_sql( $fixed_term );
				$sql .= " AND p.post_type IN( {$target_pt} )";
				$sql .= " AND p.post_status IN ( {$post_status} )";
				$cnt = $wpdb->get_row( $sql );
				feas_cache_create( $manag_no, 'cf_cnt_' . $cf_data, false, $cnt );
			}
			$cf_cnt[] = $cnt;
		}
	}

	// 未選択時の文字列
	$noselect_text = $get_data[$cols[27]];

	/**
	 *	デフォルト値
	 */
	$default_value = $get_data[$cols[39]];
	if ( $default_value ) {
		$default_value = explode( ',', $default_value );
	}

	// カウント表示
	$showcnt = get_option( $feadvns_show_count . $manag_no );

	$ret_ele = '';

	switch ( (string) $get_data[$cols[4]] ) {

		/**
		 *	ドロップダウン
		 */
		case '1':
		case 'a':

			$ret_opt = '';

			for ( $i = 0; $i < $cntValues; $i++ ) {

				// 0件のカスタムフィールドは表示しない場合
				if ( $nocnt && $cf_cnt[$i]->cnt == 0 )
					continue;

				$selected = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' . $number] ) ) {
						if ( $_GET['search_element_' . $number ] == $metaValues[$i] ) {
							$selected = ' selected="selected"';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $metaValues[$i] ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				// 「要素内の並び順」が「自由記述」以外の場合
				if ( 'b' !== $get_data[$cols[5]] ) {

					// 真偽値の場合
					if ( '1' === $cf_scf || '1' === $cf_shingi ) {

						// 真の場合の文字列
						$cfText = get_option( $cols[25] . $manag_no . "_" . $number );

					// 「関連する投稿」
					} else if ( '2' === $cf_scf ) {

						$relatedPost = get_post( $metaValues[$i] );
						$cfText = $relatedPost->post_title;

					// 「関連するターム」
					} else if ( '3' === $cf_scf ) {

						$relatedTerm = get_term( $metaValues[$i] );
						$cfText = $relatedTerm->name;

					} else {

						if ( 'yes' === $kugiriFlag && is_numeric( $metaValues[$i] ) ) {
							$cfText = number_format( $metaValues[$i] );
						} else {
							$cfText = $metaLabels[$i];
						}
					}

					if ( '0' === $get_data[$cols[26]] ) {
						$cfText = $cfUnit . $cfText; // 単位が前
					} else {
						$cfText = $cfText . $cfUnit; // 単位が後
					}

				} else {
					$cfText = $savedValues[$i]['text'];
				}

				if ( 'yes' == $showcnt ) {
					$cfText = $cfText . " (" . $cf_cnt[$i]->cnt . ") ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( 1 !== $savedValues[$i]['depth'] ) {
						$depth = str_pad( $savedValues[$i]['depth'], 2, '0', STR_PAD_LEFT );
						for ( $i_depth = 1; $i_depth < $savedValues[$i]['depth']; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				/**
				 *
				 * 各optionのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'value'    => $metaValues[$i],
					'selected' => $selected,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_meta_dropdown_attr', $attr, $args );

				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i}" );
				$ret_val  = esc_attr( $metaValues[$i] );
				$ret_txt  = esc_html( $cfText );

				$html  = "<option id='{$ret_id}' class='feas_clevel_{$depth}' value='{$ret_val}' {$attr} {$selected}>";
				$html .= $indentSpace . $ret_txt;
				$html .= "</option>\n";

				/**
				 *
				 * 各optionごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_meta_dropdown_html', $html, $args );

				$ret_opt .= $html;

			}

			/**
			 *
			 * selectのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$attr = apply_filters( 'feas_meta_dropdown_group_attr', $attr, $args );

			// Sanitize
			$ret_name = esc_attr( "search_element_{$number}" );
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_txt  = esc_html( $noselect_text );

			$ret_ele .= "<select name='{$ret_name}' id='{$ret_id}' {$attr}>\n";
			$ret_ele .= "<option id='{$ret_id}_none' value=''>";
			$ret_ele .= $ret_txt;
			$ret_ele .= "</option>\n";
			$ret_ele .= $ret_opt;
			$ret_ele .= "</select>\n";

			/**
			 *
			 * select全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_meta_dropdown_group_html', $ret_ele, $args );

			break;

		/**
		 *	セレクトボックス
		 */
		case '2':
		case 'b':

			$ret_opt = '';
			$selected_cnt = 0;

			for ( $i = 0; $i < $cntValues; $i++ ) {

				// 0件のカスタムフィールドは表示しない場合
				if ( $nocnt && $cf_cnt[$i]->cnt == 0 )
					continue;

				$selected = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) && is_array( $_GET['search_element_' . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {

								// シリアライズされたデータなどの場合
								$searchQuery = stripslashes( $_GET["search_element_" . $number][$i_lists] );

								if ( $searchQuery == $metaValues[$i] ) {
									$selected = ' selected="selected"';
									$selected_cnt++;
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $metaValues[$i] ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				// 「要素内の並び順」が「自由記述」以外の場合
				if ( 'b' !== $get_data[$cols[5]] ) {

					// 真偽値の場合
					if ( '1' === $cf_scf || '1' === $cf_shingi ) {

						// 真の場合の文字列
						$cfText = get_option( $cols[25] . $manag_no . "_" . $number );

					// 「関連する投稿」
					} elseif ( '2' === $cf_scf ) {

						$relatedPost = get_post( $metaValues[$i] );
						$cfText = $relatedPost->post_title;

					// 「関連するターム」
					} elseif ( '3' === $cf_scf ) {

						$relatedTerm = get_term( $metaValues[$i] );
						$cfText = $relatedTerm->name;

					} else {

						if ( 'yes' === $kugiriFlag && is_numeric( $metaValues[$i] ) ) {
							$cfText = number_format( $metaValues[$i] );
						} else {
							$cfText = $metaLabels[$i];
						}
					}

					if ( '0' === $get_data[$cols[26]] ) {
						$cfText = $cfUnit . $cfText; // 単位が前
					} else {
						$cfText = $cfText . $cfUnit; // 単位が後
					}

				} else {
					$cfText = $savedValues[$i]['text'];
				}

				if ( 'yes' === $showcnt ) {
					$cfText = $cfText . " ({$cf_cnt[$i]->cnt}) ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( '1' !== $savedValues[$i]['depth'] ) {
						$depth = str_pad( $savedValues[$i]['depth'], 2, '0', STR_PAD_LEFT );
						for ( $i_depth = 1; $i_depth < $savedValues[$i]['depth']; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				/**
				 *
				 * 各optionのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'value'    => $metaValues[$i],
					'selected' => $selected,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_meta_multiple_attr', $attr, $args );

				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i}" );
				$ret_val  = esc_attr( $metaValues[$i] );
				$ret_txt  = esc_html( $cfText );

				$html  = "<option id='{$ret_id}' class='feas_clevel_{$depth}' value='{$ret_val}' {$attr} {$selected}>";
				$html .= $indentSpace . $ret_txt;
				$html .= "</option>\n";

				/**
				 *
				 * 各オプションごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_meta_multiple_html', $html, $args );

				// ループ前段に結合
				$ret_opt .= $html;
			}

			// iOSではセレクトボックスが1行にまとめられてしまい、selectedが1件も付いていないと「0項目」と表示されてしまい、未選択時テキストが表示されないため。
			$selected = '';
			if ( 0 === $selected_cnt ) {
				if ( wp_is_mobile() ) {
					$selected = ' selected="selected"';
				}
			}

			/**
			 *
			 * Multipleのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$attr = apply_filters( 'feas_meta_multiple_group_attr', $attr, $args );

			// Sanitize
			$ret_name = esc_attr( "search_element_{$number}[]" );
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_txt  = esc_html( $noselect_text );

			$html  = "<select name='{$ret_name}' id='{$ret_id}' size='5' multiple='multiple' {$attr}>\n";
			$html .= "<option id='{$ret_id}_none' value='' {$selected}>";
			$html .= $ret_txt;
			$html .= "</option>\n";
			$html .= $ret_opt;
			$html .= "</select>\n";

			/**
			 *
			 * セレクトボックス全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$html = apply_filters( 'feas_meta_multiple_group_html', $html, $args );

			// ループ前段に結合
			$ret_ele .= $html;

			break;

		/**
		 *	チェックボックス
		 */
		case '3':
		case 'c':

			for ( $i = 0; $i < $cntValues; $i++ ) {

				// 0件のカスタムフィールドは表示しない場合
				if ( $nocnt && $cf_cnt[$i]->cnt == 0 )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) && is_array( $_GET['search_element_' . $number] ) ) {

						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {

								// シリアライズされたデータなどの場合
								$searchQuery = stripslashes( $_GET["search_element_" . $number][$i_lists] );

								if ( $searchQuery == $metaValues[$i] )
									$checked = ' checked="checked"';
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $metaValues[$i] ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				// 要素内の並び順 = カスタム「以外」
				if ( 'b' != $get_data[$cols[5]] ) {

					// 真偽値の場合
					if ( '1' === $cf_scf || '1' === $cf_shingi ) {

						// 真の場合の文字列
						$cfText = get_option( $cols[25] . $manag_no . "_" . $number );

					// 「関連する投稿」
					} elseif ( '2' === $cf_scf ) {

						$relatedPost = get_post( $metaValues[$i] );
						$cfText = $relatedPost->post_title;

					// 「関連するターム」
					} elseif ( '3' === $cf_scf ) {

						$relatedTerm = get_term( $metaValues[$i] );
						$cfText = $relatedTerm->name;

					} else {

						if ( 'yes' == $kugiriFlag && is_numeric( $metaValues[$i] ) ) {
							$cfText = number_format( $metaValues[$i] );
						} else {
							$cfText = $metaLabels[$i];
						}
					}

					if ( '0' === $get_data[$cols[26]] ) {
						$cfText = $cfUnit . $cfText; // 単位が前
					} else {
						$cfText = $cfText . $cfUnit; // 単位が後
					}

				} else {
					$cfText = $savedValues[$i]['text'];
				}

				if ( 'yes' == $showcnt ) {
					$cfText = $cfText . " (" . $cf_cnt[$i]->cnt . ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $savedValues[$i]['depth'], 2, '0', STR_PAD_LEFT );
				}

				/**
				 *
				 * 各チェックボックスのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'value'    => $metaValues[$i],
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_meta_checkbox_attr', $attr, $args );

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i}" );
				$ret_name = esc_attr( "search_element_{$number}[]" );
				$ret_val  = esc_attr( $metaValues[$i] );
				$ret_text = esc_html( $cfText );

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='checkbox' name='{$ret_name}' value='{$ret_val}' {$attr} {$checked} />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				/**
				 *
				 * 各チェックボックスごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_meta_checkbox_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * チェックボックスグループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_meta_checkbox_group_html', $ret_ele, $args );

			break;

		/**
		 *	ラジオボタン
		 */
		case '4':
		case 'd':

			/**
			 *	ラジオボタンの「未選択」の表示/非表示
			 */
			$noselect_status = get_option( $cols[31] . $manag_no . '_' . $number );
			if ( $noselect_status ) {

				$ret_ele .= "<label for='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' class='feas_clevel_01'>";
				$ret_ele .= "<input id='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' type='radio' name='search_element_" . esc_attr( $number ) . "' value='' />";
				$ret_ele .= "<span>" . esc_html( $noselect_text ) . "</span>";
				$ret_ele .= "</label>\n";
			}

			for ( $i = 0; $i < $cntValues; $i++ ) {

				// 0件のカスタムフィールドは表示しない場合
				if ( $nocnt && $cf_cnt[$i]->cnt == 0 )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' . $number] ) ) {
						$searchTerm = stripslashes( $_GET['search_element_' . $number] );
						if ( $searchTerm == $metaValues[$i] ) {
							$checked = ' checked="checked"';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $metaValues[$i] ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				// 要素内の並び順 = カスタム「以外」
				if ( 'b' != $get_data[$cols[5]] ) {

					// 真偽値の場合
					if ( '1' === $cf_scf || '1' === $cf_shingi ) {

						// 真の場合の文字列
						$cfText = get_option( $cols[25] . $manag_no . "_" . $number );

					// 「関連する投稿」
					} elseif ( '2' === $cf_scf ) {

						$relatedPost = get_post( $metaValues[$i] );
						$cfText = $relatedPost->post_title;

					// 「関連するターム」
					} elseif ( '3' === $cf_scf ) {

						$relatedTerm = get_term( $metaValues[$i] );
						$cfText = $relatedTerm->name;

					} else {

						if ( 'yes' == $kugiriFlag && is_numeric( $metaValues[$i] ) ) {
							$cfText = number_format( $metaValues[$i], 0, '.', ',' );
						} else {
							$cfText = $metaLabels[$i];
						}
					}

					if ( '0' === $get_data[$cols[26]] ) {
						$cfText = $cfUnit . $cfText; // 単位が前
					} else {
						$cfText = $cfText . $cfUnit; // 単位が後
					}

				} else {
					$cfText = $savedValues[$i]['text'];
				}

				if ( 'yes' == $showcnt ) {
					$cfText = $cfText . " ({$cf_cnt[$i]->cnt}) ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $savedValues[$i]['depth'], 2, '0', STR_PAD_LEFT );
				}

				/**
				 *
				 * 各ラジオボタンのattrにかけるフィルター
				 *
				 */
				$attr = '';
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'value'    => $metaValues[$i],
					'checked'  => $checked,
					'show_post_cnt' => $showcnt,
				);
				$attr = apply_filters( 'feas_meta_radio_attr', $attr, $args );

				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i}" );
				$ret_name = esc_attr( "search_element_{$number}" );
				$ret_val  = esc_attr( $metaValues[$i] );
				$rel_txt  = esc_html( $cfText );

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input type='radio' id='{$ret_id}' name='{$ret_name}' value='{$ret_val}' {$attr} {$checked} />";
				$html .= "<span>{$rel_txt}</span>";
				$html .= "</label>";

				/**
				 *
				 * 各ラジオボタンごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_meta_radio_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * ラジオボタングループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_meta_radio_group_html', $ret_ele, $args );

			break;

		/**
		 *	フリーワード
		 */
		case '5':
		case 'e':

			$placeholder_data = '';
			$placeholder = '';
			$output_js = '';

			$placeholder_data = $get_data[$cols[30]];
			if ( $placeholder_data ) {
				$placeholder = ' placeholder="' . esc_attr( $placeholder_data ) . '"';
				$output_js = '<script>jQuery("#feas_' . esc_attr( $manag_no . '_' . $number ) . '").focus(function(){jQuery(this).attr("placeholder",""); }).blur(function(){
    jQuery(this).attr("placeholder", "' . esc_attr( $placeholder_data ) . '");});</script>';
			}

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			} else if ( $default_value ) {
				if ( is_array( $default_value ) ) {
					for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
						if ( '' !== $s_keyword ) {
							$s_keyword .= ' ';
						}
						$s_keyword .= $default_value[$i_lists];
					}
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_meta_freeword_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "s_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html  = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$placeholder} {$attr} />";
			$html .= $output_js;

			if ( '' !== $get_data[$cols[20]] ) {
				$html .= create_specifies_the_key_element( $get_data, $number );
			}

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_meta_freeword_group_html', $html, $args );

			$ret_ele .= $html;

			break;

		/**
		 *	テキスト入力で範囲検索
		 */
		case 'cf_limit_keyword':

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['cf_limit_keyword_' . $number] ) ) {
					$s_keyword = $_GET['cf_limit_keyword_' . $number];
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_meta_range_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "cf_limit_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$attr} />";

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_meta_range_group_html', $html, $args );

			$ret_ele .= $html;

			break;

		/**
		 *	その他
		 */
		default:

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			}

			/**
			 *
			 * inputのattrにかけるフィルター
			 *
			 */
			$attr = '';
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
				'value'    => esc_attr( stripslashes( $s_keyword ) ),
			);
			$attr = apply_filters( 'feas_meta_freeword_attr', $attr, $args );

			// Sanitize
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_name = esc_attr( "s_keyword_{$number}" );
			$ret_val  = esc_attr( stripslashes( $s_keyword ) );

			$html  = "<input type='text' name='{$ret_name}' id='{$ret_id}' value='{$ret_val}' {$placeholder} {$attr} />";

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_meta_freeword_group_html', $html, $args );

			$ret_ele .= $html;

			break;
	}

	return $ret_ele;
}


/*============================
	タグ（tag）のエレメント作成
 ============================*/
function create_tag_element( $get_data = array(), $number ) {

	global $wpdb, $cols, $feadvns_show_count, $manag_no, $feadvns_include_sticky, $feadvns_search_target, $feadvns_exclude_id, $feadvns_default_cat;

	/**
	 *	初期化
	 */
	$nocnt = false;
	$target_sp = $ret_ele = $showcnt = $ret_ele = $exids = '';
	$sticky = $sp = array();

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
	$showcnt = get_option( $feadvns_show_count . $manag_no );

	/**
	 *	0件のタームを表示しない設定の場合
	 */
	if ( isset( $get_data[$cols[14]] ) && $get_data[$cols[14]] == 'no' ) {
		$nocnt = true;
	}

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

		switch ( (string) $get_data[$cols[5]] ) {

			case '0':
			case '1':
			case 'c':
				$order_by = " t.term_id ";
				break;
			case '2':
			case '3':
			case 'd':
				$order_by = " t.name ";
				break;
			case '4':
			case '5':
			case 'e':
				$order_by = " t.slug ";
				break;
			case '6':
			case 'f':
				$order_by = " t.term_order ";
				break;
			case '7':
			case 'g':
				$order_by = " RAND() ";
				break;
			default:
				$order_by = " t.term_id ";
				break;
		}
		// 'b'（自由記述）については2046行目〜にて
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

			$get_tags = array();

			// 行数分ループを回す
			for ( $i = 0; $cnt = count( $options ), $i < $cnt; $i++ ) {

				if ( empty( $options[$i] ) )
					continue;

				$get_tags[$i] = new stdClass();

				// 値
				$get_tags[$i]->term_id = $options[$i]['value'];

				// 表記
				$get_tags[$i]->name = $options[$i]['text'];

				// 階層
				$get_tags[$i]->depth = $options[$i]['depth'];
			}
		}
	}
	else {

		/**
		 * Polylang
		 */
		$lang = '';

		$polylang = get_option( 'polylang' );
		if ( $polylang ) {
			$lang = $polylang['default_lang'];
		}

		$langVar = get_query_var( 'lang' );
		if ( $langVar ) {
			$lang = $langVar;
		}

		$lang_id = '';

		if ( $lang ) {
			$sql = <<<SQL
SELECT tt.term_id
FROM {$wpdb->term_taxonomy} AS tt
LEFT JOIN {$wpdb->terms} AS t
ON tt.term_id = t.term_id
WHERE t.slug = %s
LIMIT 1
SQL;
			$sql = $wpdb->prepare( $sql, $lang );
			$lang_id = $wpdb->get_var( $sql );
		}

		// キャッシュ
		if ( false === ( $get_tags = feas_cache_judgment( $manag_no, 'tag', $number ) ) ) {
			$sql = <<<SQL
SELECT t.name, t.term_id
FROM {$wpdb->terms} AS t
LEFT JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = t.term_id
LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
WHERE tt.taxonomy = 'post_tag'
SQL;
			if ( $exids ) {
				$addSql = ' AND t.term_id NOT IN ( %d ) ';
				$sql .= $wpdb->prepare( $addSql, $exids );
			}
			if ( $lang_id ) {
				$addSql = <<<SQL
AND tr.object_id IN (
	SELECT tr.object_id
	FROM {$wpdb->terms} AS t
	LEFT JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = t.term_id
	LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
	WHERE tr.term_taxonomy_id = %d
)
SQL;
				$sql .= $wpdb->prepare( $addSql, $lang_id );
			}
			$sql .= " GROUP BY t.term_id";
			$sql .= " ORDER BY {$order_by} {$order}";

			$get_tags = $wpdb->get_results( $sql );

			feas_cache_create( $manag_no, 'tag', $number, $get_tags );
		}
	}

	$cnt_ele = count( $get_tags );

	/**
	 *	件数を取得してキャッシュ保存
	 */
	if ( $get_tags ) {

		$tag_cnt = array();
		foreach( $get_tags as $term_id ) {

			if ( false === ( $cnt = feas_cache_judgment( $manag_no, 'tag_cnt_' . $term_id->term_id, false ) ) ) {
				$sql  = " SELECT count( p.ID ) AS cnt FROM {$wpdb->posts} AS p";
				$sql .= " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
				if ( $fixed_term ) $sql .= " INNER JOIN {$wpdb->term_relationships} AS tr2 ON p.ID = tr2.object_id";
				$sql .= " WHERE 1=1";
				if ( $sp ) $sql .= " AND p.ID NOT IN ( {$sp} )";
				$sql .= " AND tr.term_taxonomy_id = " . esc_sql( $term_id->term_id );
				if ( $fixed_term ) $sql .= " AND tr2.term_taxonomy_id = " . esc_sql( $fixed_term );
				$sql .= " AND p.post_type IN( {$target_pt} )";
				$sql .= " AND p.post_status IN ( {$post_status} )";

				$cnt = $wpdb->get_row( $sql );
				feas_cache_create( $manag_no, 'tag_cnt_' . $term_id->term_id, false, $cnt );
			}
			$tag_cnt[] = $cnt;
		}
	}

	/**
	 *	未選択時の文字列
	 */
	$noselect_text = $get_data[$cols[27]];

	/**
	 *	デフォルト値
	 */
	$default_value = $get_data[$cols[39]];
	if ( $default_value ) {
		$default_value = explode( ',', $default_value );
	}

	switch ( (string) $get_data[$cols[4]] ) {

		/**
		 *	ドロップダウン
		 */
		case '1':
		case 'a':

			//$ret_ele  = "<div class='feas-item-content'>";
			$ret_ele .= "<select name='search_element_" . esc_attr( $number ) . "' id='feas_" . esc_attr( $manag_no . "_" . $number ) . "'>\n";
			$ret_ele .= "<option id='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' value=''>";
			$ret_ele .= esc_html( $noselect_text );
			$ret_ele .= "</option>\n";

			for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++) {

				// 0件のタグは表示しない場合
				if ( $nocnt && $tag_cnt[$i_ele]->cnt == 0 )
					continue;

				$selected = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' . $number] ) ) {
						if ( $_GET['search_element_' . $number] == $get_tags[$i_ele]->term_id ) {
							$selected = ' selected="selected"';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_tags[$i_ele]->term_id ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				$cat_cnt = '';
				if ( 'yes' == $showcnt ) {
					$cat_cnt = " (" . $tag_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( '1' !== $get_tags[$i_ele]->depth ) {
						$depth = str_pad( $get_tags[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
						for ( $i_depth = 1; $i_depth < $get_tags[$i_ele]->depth; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				// Sanitaize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_val  = esc_attr( $get_tags[$i_ele]->term_id );
				$rel_text = esc_html( $get_tags[$i_ele]->name . $cat_cnt );

				$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' $selected>";
				$html .= $indentSpace . $rel_text;
				$html .= "</option>\n";

				/**
				 *
				 * 各optionごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no'  => $manag_no,
					'number'    => $number,
					'cnt'       => $i_ele,
					'show_post_cnt'  => $showcnt,
				);
				$html = apply_filters( 'feas_tag_dropdown_html', $html, $args );

				$ret_ele .= $html;
			}

			$ret_ele .= "</select>\n";

			/**
			 *
			 * select全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => $manag_no,
				'number'        => $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_tag_dropdown_group_html', $ret_ele, $args );

			break;

		/**
		 *	セレクトボックス
		 */
		case '2':
		case 'b':

			$ret_opt = '';
			$selected_cnt = 0;

			for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {

				// 0件のタグは表示しない場合
				if ( $nocnt && $tag_cnt[$i_ele]->cnt == 0 )
					continue;

				$selected = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
								if ( $_GET["search_element_" . $number][$i_lists] == $get_tags[$i_ele]->term_id ) {
									$selected = ' selected="selected"';
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_tags[$i_ele]->term_id ) {
								$selected = ' selected="selected"';
							}
						}
					}
				}

				$cat_cnt = '';
				if ( 'yes' == $showcnt ) {
					$cat_cnt = " (" . $tag_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';
				$indentSpace = '';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassとインデントを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					if ( '1' !== $get_tags[$i_ele]->depth ) {
						$depth = str_pad( $get_tags[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
						$indentSpace = '';
						for ( $i_depth = 1; $i_depth < $get_tags[$i_ele]->depth; $i_depth++ ) {
							$indentSpace .= '&nbsp;&nbsp;';
						}
					}
				}

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_val  = esc_attr( $get_tags[$i_ele]->term_id );
				$ret_text = esc_html( $get_tags[$i_ele]->name . $cat_cnt );

				$html  = "<option id='{$ret_id}' value='{$ret_val}' class='feas_clevel_{$depth}' $selected>";
				$html .= $indentSpace . $ret_text;
				$html .= "</option>\n";

				/**
				 *
				 * 各オプションごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_tag_multiple_html', $html, $args );

				// ループ前段に結合
				$ret_opt .= $html;
			}

			// iOSではセレクトボックスが1行にまとめられてしまい、selectedが1件も付いていないと「0項目」と表示されてしまい、未選択時テキストが表示されないため。
			$selected = '';
			if ( 0 === $selected_cnt ) {
				if ( wp_is_mobile() ) {
					$selected = ' selected="selected"';
				}
			}

			// Sanitize
			$ret_name = esc_attr( "search_element_{$number}[]" );
			$ret_id   = esc_attr( "feas_{$manag_no}_{$number}" );
			$ret_txt  = esc_html( $noselect_text );

			$html  = "<select name='{$ret_name}' id='{$ret_id}' size='5' multiple='multiple'>\n";
			$html .= "<option id='{$ret_id}_none' value='' {$selected}>";
			$html .= $ret_txt;
			$html .= "</option>\n";
			$html .= $ret_opt;
			$html .= "</select>\n";

			/**
			 *
			 * セレクトボックス全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => (int) $showcnt,
			);
			$html = apply_filters( 'feas_tag_multiple_group_html', $html, $args );

			// ループ前段に結合
			$ret_ele .= $html;

			break;

		/**
		 *	チェックボックス
		 */
		case '3':
		case 'c':

			//$ret_ele = "<div class='feas-item-content'>";
			for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {

				// 0件のタグは表示しない場合
				if ( $nocnt && $tag_cnt[$i_ele]->cnt == 0 )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET["search_element_" . $number] ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $_GET["search_element_" . $number] ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( isset( $_GET["search_element_" . $number][$i_lists] ) ) {
								if ( $_GET["search_element_" . $number][$i_lists] == $get_tags[$i_ele]->term_id ) {
									$checked = ' checked="checked"';
								}
							}
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_tags[$i_ele]->term_id ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				$cat_cnt = '';
				if ( 'yes' == $showcnt ) {
					$cat_cnt = " (" . $tag_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $get_tags[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
				}

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_name = esc_attr( "search_element_{$number}[]" );
				$ret_val  = esc_attr( $get_tags[$i_ele]->term_id );
				$ret_text = esc_html( $get_tags[$i_ele]->name . $cat_cnt );

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='checkbox' name='{$ret_name}' value='{$ret_val}' $checked />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				/**
				 *
				 * 各チェックボックスごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_tag_checkbox_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;
			}

			/**
			 *
			 * チェックボックスグループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_tag_checkbox_group_html', $ret_ele, $args );

			break;

		/**
		 *	ラジオボタン
		 */
		case '4':
		case 'd':

			/**
			 *	ラジオボタンの「未選択」の表示/非表示
			 */
			$noselect_status = get_option( $cols[31] . $manag_no . '_' . $number );
			if ( $noselect_status ) {

				$ret_ele .= "<label for='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' class='feas_clevel_01'>";
				$ret_ele .= "<input id='feas_" . esc_attr( $manag_no . "_" . $number ) . "_none' type='radio' name='search_element_" . esc_attr( $number ) . "' value='' />";
				$ret_ele .= "<span>" . esc_html( $noselect_text ) . "</span>";
				$ret_ele .= "</label>\n";
			}

			for ( $i_ele = 0; $i_ele < $cnt_ele; $i_ele++ ) {

				// 0件のタグは表示しない場合
				if ( $nocnt && $tag_cnt[$i_ele]->cnt == 0 )
					continue;

				$checked = '';
				if ( isset( $_GET['fe_form_no'] ) && $_GET['fe_form_no'] == $manag_no ) {
					if ( isset( $_GET['search_element_' . $number] ) ) {
						if ( $_GET['search_element_' . $number] == $get_tags[$i_ele]->term_id ) {
							$checked = ' checked="checked"';
						}
					}
				} else if ( $default_value ) {
					if ( is_array( $default_value ) ) {
						for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
							if ( $default_value[$i_lists] == $get_tags[$i_ele]->term_id ) {
								$checked = ' checked="checked"';
							}
						}
					}
				}

				$cat_cnt = '';
				if ( 'yes' == $showcnt ) {
					$cat_cnt = " (" . $tag_cnt[$i_ele]->cnt . ") ";
				}

				$depth = '01';

				// 「要素内の並び順」が「自由記述」の場合、階層に応じてclassを準備
				if ( 'b' === $get_data[$cols[5]] ) {
					$depth = str_pad( $get_tags[$i_ele]->depth, 2, '0', STR_PAD_LEFT );
				}

				// Sanitize
				$ret_id   = esc_attr( "feas_{$manag_no}_{$number}_{$i_ele}" );
				$ret_name = esc_attr( "search_element_{$number}" );
				$ret_val  = esc_attr( $get_tags[$i_ele]->term_id );
				$ret_text = esc_html( $get_tags[$i_ele]->name . $cat_cnt );

				$html  = "<label for='{$ret_id}' class='feas_clevel_{$depth}'>";
				$html .= "<input id='{$ret_id}' type='radio' name='{$ret_name}' value='{$ret_val}' $checked />";
				$html .= "<span>{$ret_text}</span>";
				$html .= "</label>\n";

				/**
				 *
				 * 各ラジオボタンごとにかけるフィルター
				 *
				 */
				$args = array(
					'manag_no' => (int) $manag_no,
					'number'   => (int) $number,
					'cnt'      => (int) $i_ele,
					'show_post_cnt' => $showcnt,
				);
				$html = apply_filters( 'feas_tag_radio_html', $html, $args );

				// ループ前段に結合
				$ret_ele .= $html;

			}

			/**
			 *
			 * ラジオボタングループ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no'      => (int) $manag_no,
				'number'        => (int) $number,
				'show_post_cnt' => $showcnt,
			);
			$ret_ele = apply_filters( 'feas_tag_radio_group_html', $ret_ele, $args );

			break;

		/**
		 *	フリーワード
		 */
		case '5':
		case 'e':

			$placeholder_data = '';
			$placeholder = '';
			$output_js = '';

			$placeholder_data = $get_data[$cols[30]];
			if ( $placeholder_data ) {
				$placeholder = ' placeholder="' . esc_attr( $placeholder_data ) . '"';
				$output_js = '<script>jQuery("#feas_' . esc_attr( $manag_no . '_' . $number ) . '").focus( function() { jQuery(this).attr("placeholder",""); }).blur( function() {
    jQuery(this).attr("placeholder", "' . esc_attr( $placeholder_data ) . '"); });</script>';
			}

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			} else if ( $default_value ) {
				if ( is_array( $default_value ) ) {
					for ( $i_lists = 0, $cnt_lists = count( $default_value ); $i_lists < $cnt_lists; $i_lists++ ) {
						if ( '' !== $s_keyword ) {
							$s_keyword .= ' ';
						}
						$s_keyword .= $default_value[$i_lists];
					}
				}
			}

			$html  = "<input type='text' name='s_keyword_" . esc_attr( $number ) . "' id='feas_" . esc_attr( $manag_no . "_" . $number ) . "' " . $placeholder . " value='" . esc_attr( stripslashes( $s_keyword ) ) . "' />\n";
			$html .= $output_js;

			if ( '' !== $get_data[$cols[20]] ) {
				$html .= create_specifies_the_key_element( $get_data, $number );
			}

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_tag_freeword_group_html', $html, $args );

			$ret_ele .= $html;

			break;

		/**
		 *	その他
		 */
		default:

			$s_keyword = '';
			if ( isset( $_GET['fe_form_no'] ) && $manag_no == $_GET['fe_form_no'] ) {
				if ( isset( $_GET['s_keyword_' . $number] ) ) {
					$s_keyword = $_GET['s_keyword_' . $number];
				}
			}
			$html = "<input type='text' name='s_keyword_" . esc_attr( $number ) . "' id='feas_" . esc_attr( $manag_no . "_" . $number ) . "' value='" . esc_attr( stripslashes( $s_keyword ) ) . "' />\n";

			/**
			 *
			 * inputタグ全体にかけるフィルター
			 *
			 */
			$args = array(
				'manag_no' => (int) $manag_no,
				'number'   => (int) $number,
			);
			$html = apply_filters( 'feas_tag_range_group_html', $html, $args );

			$ret_ele .= $html;

			break;
	}

	return $ret_ele;
}

/*============================
	フリーワード検索時 カスタムフィールドのキー限定
 ============================*/
function create_specifies_the_key_element( $get_data = array(), $number ) {
	global $wpdb, $cols, $feadvns_show_count, $manag_no, $feadvns_include_sticky;

	$html = null;

	// キーの配列
	$meta_keys = $get_data['feadvns_cf_specify_key_'];

	if ( is_array( $meta_keys ) ) {

		$i_ele = 0;

		foreach( $meta_keys as $val ) {
			$html .= "<input type='hidden' name='cf_specify_key_" . esc_attr( $number ) . "_" . esc_attr( $i_ele ) . "' value='" . esc_attr( $val ) . "' />";
			$i_ele++;
		}
		$html .= "<input type='hidden' name='cf_specify_key_length_" . esc_attr( $number ) . "' value='" . ( esc_attr( $i_ele ) - 1 ) . "'/>";

		/**
		 *
		 * hiddenタグの追加など
		 *
		 */
		$args = array(
			'manag_no' => (int) $manag_no,
			'number'   => (int) $number,
		);
		$html = apply_filters( 'feas_freeword_specifies_key_after_html', $html, $args );

	}

	return $html;
}

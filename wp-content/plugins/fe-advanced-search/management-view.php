<?php

defined( 'ABSPATH' ) || exit;

/////////////////////////////////////////////////
//	検索 > 表示部
/////////////////////////////////////////////////
?>
<div class="wrap">
	<div id="feas-admin">

		<?php
		/**
		 * ロゴ, version, サポートリンク 他
		 */
		include( 'admin/header.php' );
		?>

		<?php
		/*============================
			フォームの選択プルダウン
		 ============================*/
		$output = '';
		for ( $i = 0; $i <= $get_form_max; $i++ ) {
			$form_name = $selected = $form_no_tmp = '';
			$form_no_tmp = get_option( $feadvns_form_no . $i );
			$form_name   = get_option( $feadvns_search_form_name . $form_no_tmp );
			if ( ! $form_name ) {
				$form_name = '（フォームID = ' . $form_no_tmp . '）';
			}
			if ( $manag_no == $form_no_tmp ) {
				$selected = ' selected="selected"';
			}
			$output .= '<option value="' . esc_attr( $i ) . '"' . $selected . '>' . esc_html( $form_name ) . '</option>';
		}
		$output .= '<option value="new">　新規作成　</option>';
		$output .= '<option value="del">　削除　</option>';
		?>

		<?php
		/*============================
			ページタイトル
		 ============================*/
		?>
		<div id="feas-contents-header">
			<h2 id="feas-sectitle" class="left">検索フォーム「<?php echo esc_html( db_op_get_value( $feadvns_search_form_name . $manag_no ) ); ?>（No.<?php echo esc_html( $manag_no ); ?>）」の設定</h2>
			<form action="<?php menu_page_url( 'feas_management' ); ?>&noheader=true" method="post">
				<select name="c_form_number">
					<?php echo $output; ?>
				</select>
				<input type="hidden" name="current_form_no" value="<?php echo esc_attr( $manag_no ); ?>" />
				<input type="submit" value="実行" class="button-secondary action" />
			</form>
		</div>

		<?php
		/*============================
			設定（タブ全体）
		 ============================*/
		?>
		<form action="<?php menu_page_url( 'feas_management' ); ?>&noheader=true" method="post" name="fm" id="fm" onSubmit="return checkItemOrderRepeated('#searchItemsBody .alternate:not(:last-child)');">

			<ul class="tab">
				<li class="active">フォーム全体の設定</li>
				<li>検索項目の作成</li>
				<li>プレビュー</li>
				<li>コード</li>
			</ul>

			<div class="area">
				<ul class="show">

					<?php
					/*============================
						検索フォーム全体の設定
					 ============================*/
					?>
					<div id="generalSettings" class="pg-search paramTable">

						<?php
						/*============================
							名称
						============================*/
						?>
						<div class="th th1-1">名称</div>
						<div class="td td1-1">
							<input type="text" id="<?php echo esc_attr( $feadvns_search_form_name . $manag_no ); ?>" name="<?php echo esc_attr( $feadvns_search_form_name . $manag_no ); ?>" value="<?php echo esc_attr( get_option( $feadvns_search_form_name . $manag_no ) ); ?>" />
						</div>
						<div class="th th1-2">フォームID</div>
						<div class="td td1-2"><?php echo esc_html( $manag_no ); ?></div>

						<?php
						/*============================
							検索対象の投稿タイプ
						 ============================*/
						?>
						<div class="th th2-1">検索対象の投稿タイプ</div>
						<div class="td td2-1" colspan="3">

							<?php
							// PostTypeのチェックボックスを出力。functions.php参照。
							feas_posttype_lists( $manag_no );

							// StickyPosts
							$sp_checked = '';
							$target_sp = get_option( $feadvns_include_sticky . $manag_no );
							if ( 'yes' == $target_sp ) {
								$sp_checked = ' checked="checked"';
							}
							?>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( $feadvns_include_sticky . $manag_no ); ?>" value="yes"<?php echo $sp_checked; ?> /> 固定記事（Sticky Posts）
							</label>
						</div>

						<?php
						/*============================
							固定タクソノミ/ターム
						 ============================*/
						?>
						<div class="th th3-1">固定タクソノミ/ターム</div>
						<div class="td td3-1">
							<?php
							$args = array( 'public' => true );
							$allTaxs = get_taxonomies( $args, 'objects' );
							if ( $allTaxs ) {
							?>
								<select name="<?php echo esc_attr( $feadvns_default_cat . $manag_no ); ?>">
									<option value=""> なし </option>
									<?php
									foreach ( $allTaxs as $tax ) {
										if ( 'post_format' != $tax->name ) {
											?>
											<optgroup label="<?php echo esc_attr( $tax->label ); ?>">
											<?php
										}
										$args = array(
											'manag_no'  => $manag_no,
											'counter'   => -1,
											'parent'    => 0,
											'tax_name'  => $tax->name,
											'tax_label' => $tax->label,
											'depth'     => 0,
											'echo'      => true,
										);
										feas_get_hierarchical_term_list( $args );
										if ( 'post_format' != $tax->name ) {
											?>
											</optgroup>
											<?php
										}
									}
									?>
								</select>
							<?php
							}
							?>
						</div>

						<?php
						/*============================
							検索条件に件数を表示
						 ============================*/
						?>
						<div class="th th3-2">
							検索条件に件数を表示
						</div>
						<div class="td td3-2">
							<?php
							$sc_checked = '';
							if ( 'yes' == get_option( $feadvns_show_count . $manag_no ) ) {
								$sc_checked = ' checked="checked"';
							}
							?>
							<label><input type="checkbox" name="<?php echo esc_attr( $feadvns_show_count . $manag_no ); ?>" value="yes" <?php echo $sc_checked; ?>> 表示する</label>
						</div>

						<?php
						/*============================
							検索結果から除外
						 ============================*/
						?>
						<?php
						$exclude_id = get_option( $feadvns_exclude_id . $manag_no );
						if ( $exclude_id ) {
							$exclude_id = implode( ',', $exclude_id );
						}
						?>

						<div class="th th4-1">検索結果から除外</div>
						<div class="td td4-1">
							<label for="<?php echo esc_attr( $feadvns_exclude_id . $manag_no ); ?>">記事（ID）</label>
							<input id="<?php echo esc_attr( $feadvns_exclude_id . $manag_no ); ?>" type="text" name="<?php echo esc_attr( $feadvns_exclude_id . $manag_no ); ?>" value="<?php echo esc_attr( $exclude_id ); ?>" />
							<label for="<?php echo esc_attr( $feadvns_exclude_term_id . $manag_no ); ?>">ターム（term_id）</label>
							<input id="<?php echo esc_attr( $feadvns_exclude_term_id . $manag_no ); ?>" type="text" name="<?php echo esc_attr( $feadvns_exclude_term_id . $manag_no ); ?>" value="<?php echo esc_attr( get_option( $feadvns_exclude_term_id . $manag_no ) ); ?>" />
<!--
							<label for="<?php //echo esc_attr( $feadvns_exclude_cf . $manag_no ); ?>">カスタムフィールド（meta_key:meta_value）</label> <br>
							<input id="<?php //echo esc_attr( $feadvns_exclude_cf . $manag_no ); ?>" type="text" name="<?php //echo esc_attr( $feadvns_exclude_cf . $manag_no ); ?>" value="<?php //echo esc_attr( get_option( $feadvns_exclude_cf . $manag_no ) ); ?>" /><br>
-->
						</div>

						<?php
						/*============================
							検索条件が指定されずに検索された場合
						 ============================*/
						?>
						<div class="th th4-2">検索条件が指定されずに検索された場合</div>
						<div class="td td4-2">
							<?php
							$ereq_selected_0 = $ereq_selected_1 = '';
							$ereq = get_option( $feadvns_empty_request . $manag_no );
							if ( $ereq == 0 ) {
								$ereq_selected_0 = ' selected="selected"';
							} else {
								$ereq_selected_1 = ' selected="selected"';
							}
							?>
							<select name="<?php echo esc_attr( $feadvns_empty_request . $manag_no ); ?>">
								<option value="0"<?php echo $ereq_selected_0; ?>>0件を返す</option>
								<option value="1"<?php echo $ereq_selected_1; ?>>固定タクソノミ/タームの記事一覧を表示</option>
							</select>
						</div>

						<?php
						/*============================
							検索結果の並び順
						 ============================*/
						?>
						<div class="th th5-1">検索結果の並び順</div>
						<div class="td td5-1" colspan="1">

							<?php
							/*
							 * 第一条件
							 * ターゲット
							 *
							 */
							?>

							<div class="feas-members">
								<label for="feadvns_sort_target">第一条件</label>
								<select id="feadvns_sort_target" name="<?php echo esc_attr( $feadvns_sort_target . $manag_no ); ?>">
									<?php
									$op_keys = array(
										'post_date'  => '投稿日時',
										'post_title' => 'タイトル',
										'post_name'  => 'スラッグ',
										'post_meta'  => 'カスタムフィールド',
										'menu_order' => '外部プラグイン',
										'rand'       => 'ランダム'
									);
									foreach ( $op_keys as $k => $v ) {
										$selected = '';
										$cTarget = get_option( $feadvns_sort_target . $manag_no );
										if ( $cTarget === $k ) {
											$selected = ' selected="selected"';
										}
										?>
										<option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $v; ?></option>
										<?php
									}
									?>
								</select>

								<?php
								/*
								 *
								 * カスタムフィールドのキー
								 *
								 */
								?>
								<select id="feadvns_sort_target_cfkey" name="<?php echo esc_attr( $feadvns_sort_target_cfkey . $manag_no ); ?>">
									<?php
									$metaKeys = feas_get_cf_key_list( $manag_no );
									foreach ( $metaKeys as $key ) {
										$selected = feas_selected( $key, $manag_no, false, $feadvns_sort_target_cfkey );
										?>
										<option value="<?php echo esc_attr( $key ); ?>" data-visible-ctl="d" <?php echo $selected; ?>>
											<?php echo esc_html( $key ); ?>
										</option>
										<?php
									}
									?>
								</select>

								<?php
								/*
								 *
								 * 数値か文字か
								 *
								 */
								$selected_1 = $selected_2 = '';
								$cOrder_as = get_option( $feadvns_sort_target_cfkey_as . $manag_no );
								if ( 'str' === $cOrder_as ) {
									$selected_2 = ' selected="selected"';
								} else {
									$selected_1 = ' selected="selected"';
								}
								?>
								<select id="feadvns_sort_target_cfkey_as" name="<?php echo esc_attr( $feadvns_sort_target_cfkey_as . $manag_no ); ?>">
									<option value="int"<?php echo $selected_1; ?>>数値</option>
									<option value="str"<?php echo $selected_2; ?>>文字列</option>
								</select>

								<?php
								/*
								 *
								 * 昇順・降順
								 *
								 */
								$selected_1 = $selected_2 = '';
								$cOrder = get_option( $feadvns_sort_order . $manag_no );
								if ( 'asc' == $cOrder ) {
									$selected_1 = ' selected="selected"';
								} else {
									$selected_2 = ' selected="selected"';
								}
								?>
								<select id="feadvns_sort_order" name="<?php echo esc_attr( $feadvns_sort_order . $manag_no ); ?>">
									<option value="asc"<?php echo $selected_1; ?>>昇順</option>
									<option value="desc"<?php echo $selected_2; ?>>降順</option>
								</select>
							</div>

							<?php
							/*
							 * 第二条件
							 * ターゲット
							 *
							 */
							?>
							<div class="feas-members">
								<label for="feadvns_sort_target_2nd">第二条件</label>
								<select id="feadvns_sort_target_2nd" name="<?php echo esc_attr( $feadvns_sort_target_2nd . $manag_no ); ?>">
									<?php
									$op_keys = array(
										'post_date'  => '投稿日時',
										'post_title' => 'タイトル',
										'post_name'  => 'スラッグ',
										'post_meta'  => 'カスタムフィールド',
										'menu_order' => '外部プラグイン',
										'rand'       => 'ランダム',
										'none'       => 'なし',
									);
									$cTarget = get_option( $feadvns_sort_target_2nd . $manag_no );
									foreach ( $op_keys as $k => $v ) {
										$selected = '';
										if ( false === $cTarget ) {
											if ( 'none' == $k ) {
												$selected = ' selected="selected"';
											}
										} elseif ( $cTarget === $k ) {
											$selected = ' selected="selected"';
										}
										?>
										<option value="<?php echo $k; ?>"<?php echo $selected; ?>><?php echo $v; ?></option>
										<?php
									}
									?>
								</select>

								<?php
								/*
								 *
								 * カスタムフィールドのキー
								 *
								 */
								?>
								<select id="feadvns_sort_target_cfkey_2nd" name="<?php echo esc_attr( $feadvns_sort_target_cfkey_2nd . $manag_no ); ?>">
									<?php
									$metaKeys = feas_get_cf_key_list( $manag_no );
									if ( $metaKeys ) {
										foreach ( $metaKeys as $key ) {
											$selected = feas_selected( $key, $manag_no, false, $feadvns_sort_target_cfkey_2nd );
											?>
											<option value="<?php echo esc_attr( $key ); ?>" data-visible-ctl="d" <?php echo $selected; ?>>
												<?php echo esc_html( $key ); ?>
											</option>
											<?php
										}
									}
									?>
								</select>

								<?php
								/*
								 *
								 * 数値か文字か
								 *
								 */
								$selected_1 = $selected_2 = '';
								$cOrder_as = get_option( $feadvns_sort_target_cfkey_as_2nd . $manag_no );
								if ( 'str' == $cOrder_as ) {
									$selected_2 = ' selected="selected"';
								} else {
									$selected_1 = ' selected="selected"';
								}
								?>
								<select id="feadvns_sort_target_cfkey_as_2nd" name="<?php echo esc_attr( $feadvns_sort_target_cfkey_as_2nd . $manag_no ); ?>">
									<option value="int"<?php echo $selected_1; ?>>数値</option>
									<option value="str"<?php echo $selected_2; ?>>文字列</option>
								</select>

								<?php
								/*
								 *
								 * 昇順・降順
								 *
								 */
								$selected_1 = $selected_2 = '';
								$cOrder = get_option( $feadvns_sort_order_2nd . $manag_no );
								if ( 'asc' == $cOrder ) {
									$selected_1 = ' selected="selected"';
								} else {
									$selected_2 = ' selected="selected"';
								}
								?>
								<select id="feadvns_sort_order_2nd" name="<?php echo esc_attr( $feadvns_sort_order_2nd . $manag_no ); ?>">
									<option value="asc"<?php echo $selected_1; ?>>昇順</option>
									<option value="desc"<?php echo $selected_2; ?>>降順</option>
								</select>
							</div>

							<?php
							/*
							 *
							 * 階層毎にソート
							 *
							 */
							$sbpt_checked = '';
							if ( '1' === get_option( $feadvns_sort_by_posttype . $manag_no ) ) {
								$sbpt_checked = ' checked="checked"';
							}
							?>
							<div class="feas-members" style="padding-top: 1em;">
								<label><input type="checkbox" name="<?php echo esc_attr( $feadvns_sort_by_posttype . $manag_no ); ?>" value="1" <?php echo $sbpt_checked; ?>> 投稿タイプ毎にソート</label>
							</div>
						</div>

						<?php
						/*
						 *
						 * フォームの表示方法
						 *
						 */
						?>
						<div class="th th5-2">フォームの表示方法</div>
						<div class="td td5-2" colspan="1">

							<?php
							/*
							 *
							 * フォームの表示スタイル
							 *
							 */

							// フォームの表示方法の選択肢
							$form_style = get_option( $feadvns_form_style );

							// 現在設定中の検索フォームの表示方法
							$apply_style = get_option( $feadvns_form_apply_style . $manag_no );

							?>
							<select id="<?php echo esc_attr( $feadvns_form_apply_style . $manag_no ); ?>" name="<?php echo esc_attr( $feadvns_form_apply_style . $manag_no ); ?>">
								<?php
								if ( $form_style ) {
									foreach ( $form_style as $index => $style ) :
										$selected = '';
										if ( (string) $index === $apply_style ) {
											$selected = 'selected';
										}
										?>
										<option value="<?php echo esc_attr( $index ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $style ); ?></option>
										<?php
									endforeach;
								}
								else {
									?>
										<option value="0" selected="selected">通常</option>
									<?php
								}
								?>
							</select>

							<?php
							/*
							 *
							 * 「フォームの表示スタイル」下部にて実行するフィルター
							 * オプション項目を追加するなど
							 *
							 */
							apply_filters( 'feas_admin_management_general_form_style_selector', '', $manag_no );
							?>

					</div>
				</ul><!-- フォーム全体のタブ -->

				<ul>
					<div id="searchItems">
						<div class="thead">
							<div class="th"></div>
							<div class="th">見出し</div>
							<div class="th">条件</div>
							<div class="th">形式</div>
							<div class="th">項目内の並び順</div>
							<div class="th">項目内の検索方法</div>
						</div>
						<div id="searchItemsBody" class="tbody">

						<?php
						/*============================
							行数分繰り返す
						 ============================*/
						for ( $i = 0; $i < $line_cnt; $i++ ) {

							// 「表示しない」設定の行は背景をグレイに
							$addclass_gray = '';
							if ( '1' == data_to_post( $cols[1] .$manag_no ."_" . $i ) ) {
								$addclass_gray = "grayout";
							}

							/** ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
							 *
							 *	一段目
							 *
							 * ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/ ?>

							<div class="widefat alternate tr <?php echo $addclass_gray; ?>">
								<div class="firstRow">
									<div class="td">
										<span class="btn-toggle"></span>
									</div>
									<div class="td">

										<?php
										/*============================
											見出し
										============================*/ ?>
										<div>
											<label
												for="<?php echo esc_attr( $cols[3] . $manag_no . "_" . $i ) ?>">
											</label>
											<input
												type="text"
												name="<?php echo esc_attr( $cols[3] . $manag_no . "_" . $i ) ?>"
												id="<?php echo esc_attr( $cols[3] . $manag_no . "_" . $i ) ?>"
												value="<?php echo esc_attr( data_to_post( $cols[3] . $manag_no . "_" . $i ) ); ?>">
										</div>
									</div>
									<div class="td">

										<?php
										/*============================
											条件
										============================*/
										?>
										<select
											data-visible-ctl-current=""
											class="trigger sTerm n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[2] . $manag_no . "_" . $i ); ?>"
											id="<?php echo esc_attr( $cols[2] . $manag_no . "_" . $i ); ?>">

											<?php $selected = feas_selected( 'archive', $manag_no, $i, $cols[2] ); ?>
											<optgroup label="アーカイブ">
												<option value="archive" <?php echo $selected; ?> data-visible-ctl="a">投稿年月</option>
											</optgroup>

											<?php $selected = feas_selected( 'sel_tag', $manag_no, $i, $cols[2] ); ?>
					                    	<optgroup label="タグ">
												<option value="sel_tag" <?php echo $selected; ?> data-visible-ctl="b">タグ</option>
											</optgroup>

											<?php foreach ( $allTaxs as $tax ) : ?>

												<?php if ( 'post_format' != $tax->name && 'post_tag' != $tax->name ) : ?>
													<optgroup label="<?php echo esc_attr( $tax->label ); ?>">
												<?php endif; ?>

												<?php
													$args = array(
														'manag_no'  => $manag_no,
														'counter'   => $i,
														'parent'    => 0,
														'tax_name'  => $tax->name,
														'tax_label' => $tax->label,
														'depth'     => 0,
														'echo'      => true,
													);
													feas_get_hierarchical_term_list( $args );
												?>

												<?php if ( 'post_format' != $tax->name && 'post_tag' != $tax->name ) : ?>
													</optgroup>
												<?php endif; ?>

											<?php endforeach; ?>

											<optgroup label="カスタムフィールド">
												<?php
												$cnt_sel = 0;
												$metaKeys = feas_get_cf_key_list( $manag_no );
												if ( $metaKeys ) {
													foreach ( $metaKeys as $key ) {
														$selected = feas_selected( 'meta_' . $key, $manag_no, $i, $cols[2] );
														if ( $selected ) $cnt_sel++;
														?>
														<option
															value="meta_<?php echo esc_attr( $key ); ?>"
															<?php echo $selected; ?>
															data-visible-ctl="d">
															<?php echo esc_html( $key ); ?>
														</option>
														<?php
													}
												}

												// 設定中のカスタムフィールドの値が0件になった場合、カッコ書きでカスタムフィールドのキーを表示
												if ( 'meta_' === substr( $_POST[$cols[2] . $manag_no . "_" . $i], 0, 5 ) && 0 === $cnt_sel ) :
													?>
													<option
														value="<?php echo esc_attr( $_POST[$cols[2].$manag_no."_".$i] ); ?>"
														selected="selected"
														data-visible-ctl="d">
														(<?php echo esc_attr( substr( $_POST[$cols[2].$manag_no."_".$i], 5 ) ); ?>)
													</option>
													<?php
												endif;
												?>
											</optgroup>
										</select>
									</div>
									<div class="td">
										<?php
										/*============================
											形式
									 	============================*/	?>
										<select
											data-visible-ctl-current=""
											class="trigger sForm n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[4] . $manag_no . "_" . $i ); ?>"
											id="<?php echo esc_attr( $cols[4] . $manag_no . "_" . $i ); ?>">
											<?php
											$saved_data = data_to_post( $cols[4] . $manag_no . "_" . $i );
											if ( '1' == $saved_data ) {
												$saved_data = 'a';
											} else if ( '2' == $saved_data ) {
												$saved_data = 'b';
											} else if ( '3' == $saved_data ) {
												$saved_data = 'c';
											} else if ( '4' == $saved_data ) {
												$saved_data = 'd';
											} else if ( '5' == $saved_data ) {
												$saved_data = 'e';
											}

											$op_keys = array(
												'a' => 'ドロップダウン',
												'b' => 'セレクトボックス',
												'c' => 'チェックボックス',
												'd' => 'ラジオボタン',
												'e' => 'フリーワード',
											);

											foreach ( $op_keys as $key => $value ) {
												$selected = feas_selected( $key, $manag_no, $i, $cols[4] );
												?>
												<option
													value="<?php echo esc_attr( $key ); ?>"
													<?php echo $selected; ?>
													data-visible-ctl="<?php echo $key; ?>">
													<?php echo esc_attr( $value ); ?>
												</option>
												<?php
											}
											?>
										</select>
									</div>
									<div class="td">
										<?php
										/*============================
											項目内の並び順
										============================*/	?>
										<select
											data-visible-ctl-current=""
											data-term-ctl="a"
											data-form-ctl="abcd"
											class="trigger sOrder arc-order n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[5] . $manag_no . "_" . $i ); ?>"
											id="<?php echo esc_attr( $cols[5] . $manag_no . "_" . $i ); ?>_arc"
											disabled="disabled">
											<?php
											// 旧versionとの互換性をたもつため、DBの値（数字）に応じてアルファベットに置き換える
											$saved_data = data_to_post( $cols[5] . $manag_no . "_" . $i );
											if ( '8' == $saved_data || '9' == $saved_data ) {
												$saved_data = 'a';
											}

											$op_keys = array(
												'a' => '年月',
												'b' => '自由記述',
											);

											foreach ( $op_keys as $key => $value ) {
												$selected = feas_selected( $key, $manag_no, $i, $cols[5] );
												?>
												<option
													value="<?php echo esc_attr( $key ); ?>"
													<?php echo $selected; ?>
													data-visible-ctl="<?php echo $key; ?>">
													<?php echo esc_html( $value ); ?>
												</option>
												<?php
											}
											?>
										</select>

										<select
											data-visible-ctl-current=""
											data-term-ctl="bc"
											data-form-ctl="abcd"
											class="trigger sOrder term-order n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[5] . $manag_no . "_" . $i ); ?>"
											id="<?php echo esc_attr( $cols[5] . $manag_no . "_" . $i ); ?>_cat"
											disabled="disabled">
											<?php
											// 旧versionとの互換性をたもつため、DBの値（数字）に応じてアルファベットに置き換える
											$saved_data = data_to_post( $cols[5] . $manag_no . "_" . $i );

											if ( '0' == $saved_data || '1' == $saved_data ) {
												$saved_data = 'c';
											} else if ( '2' == $saved_data || '3' == $saved_data ) {
												$saved_data = 'd';
											} else if ( '4' == $saved_data || '5' == $saved_data ) {
												$saved_data = 'e';
											} else if ( '6' == $saved_data ) {
												$saved_data = 'f';
											} else if ( '7' == $saved_data ) {
												$saved_data = 'g';
											}

											$op_keys = array(
												'c' => "term_id",
												'd' => "name",
												'e' => "slug",
												'f' => "外部プラグイン",
												'g' => "ランダム",
												'b' => "自由記述",
												);

											foreach ( $op_keys as $key => $value ) {
												$selected = feas_selected( $key, $manag_no, $i, $cols[5] );
												?>
												<option
													value="<?php echo esc_attr( $key ); ?>"
													<?php echo $selected; ?>
													data-visible-ctl="<?php echo $key; ?>">
													<?php echo esc_html( $value ); ?>
												</option>
												<?php
											}
											?>
										</select>

										<select
											data-visible-ctl-current=""
											data-term-ctl="d"
											data-form-ctl="abcd"
											class="trigger sOrder meta-order n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[5] . $manag_no . "_" . $i ); ?>"
											id="<?php echo esc_attr( $cols[5] . $manag_no . "_" . $i ); ?>_meta"
											disabled="disabled">
											<?php
											// 旧versionとの互換性をたもつため、DBの値（数字）に応じてアルファベットに置き換える
											$saved_data = data_to_post( $cols[5] . $manag_no . "_" . $i );
											if ( '10' == $saved_data || '11' == $saved_data ) {
												$saved_data = 'h';
											} else if ( '12' == $saved_data || '13' == $saved_data || '14' == $saved_data || '15' == $saved_data ) {
												$saved_data = 'i';
											} else if ( '16' == $saved_data ) {
												$saved_data = 'j';
											}
											$op_keys = array(
												'h' => "meta_id",
												'i' => "meta_value",
												'j' => "ランダム",
												'b' => "自由記述",
												);
											foreach ( $op_keys as $key => $value ) {
												$selected = feas_selected( $key, $manag_no, $i, $cols[5] );
												?>
												<option
													value="<?php echo esc_attr( $key ); ?>"
													<?php echo $selected; ?>
													data-visible-ctl="<?php echo $key; ?>">
													<?php echo esc_html( $value ); ?>
												</option>
												<?php
											}
											?>
										</select>

										<?php
										/*============================
											数値か文字か
										============================*/
										$selected_1 = $selected_2 = '';

										$cOrder_as = get_option( $cols[34] . $manag_no . "_" . $i );

										// 旧versionとの互換性をたもつため、DB上の「並び順」の値に応じてstrとintに置き換える
										if ( '12' == $saved_data || '13' == $saved_data ) {
											$cOrder_as = 'int';
										} else if ( '14' == $saved_data || '15' == $saved_data ) {
											$cOrder_as = 'str';
										}

										if ( 'str' === $cOrder_as ) {
											$selected_2 = ' selected="selected"';
										} else {
											$selected_1 = ' selected="selected"';
										}
										?>
										<select
											id="<?php echo esc_attr( $cols[34] . $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[34] . $manag_no . "_" . $i ); ?>"
											class="ctl sOrderBy"
											data-term-ctl="d"
											data-form-ctl="abcd"
											data-order-ctl="hi">
											<option value="int"<?php echo $selected_1; ?>>数値</option>
											<option value="str"<?php echo $selected_2; ?>>文字列</option>
										</select>

										<?php
										/*============================
											昇順・降順
										============================*/
										$selected_1 = $selected_2 = '';

										$cOrder = get_option( $cols[35] . $manag_no . "_" . $i );

										// 旧versionとの互換性をたもつため、DB上の「並び順」の値に応じてstrとintに置き換える
										if ( '10' == $saved_data || '12' == $saved_data || '14' == $saved_data ) {
											$cOrder = 'asc';
										} else if ( '11' == $saved_data || '13' == $saved_data || '15' == $saved_data ) {
											$cOrder = 'desc';
										}

										if ( 'asc' === $cOrder ) {
											$selected_1 = ' selected="selected"';
										} else {
											$selected_2 = ' selected="selected"';
										}
										?>
										<select
											id="<?php echo esc_attr( $cols[35] . $manag_no . "_" . $i ); ?>"
											name="<?php echo esc_attr( $cols[35] . $manag_no . "_" . $i ); ?>"
											class="ctl sOrderWay"
											data-term-ctl="abcd"
											data-form-ctl="abcd"
											data-order-ctl="acdehi">
											<option value="asc"<?php echo $selected_1; ?>>昇順</option>
											<option value="desc"<?php echo $selected_2; ?>>降順</option>
										</select>
									</div>
									<div class="td">

										<?php
										/*============================
											複数選択時の検索方法
										============================*/	?>
										<select
											id="<?php echo esc_attr( $cols[6] . $manag_no . "_" ); ?><?php echo esc_attr( $i ); ?>"
											name="<?php echo esc_attr( $cols[6] . $manag_no . "_" . $i ); ?>"
											class="ctl sAndor n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
											data-term-ctl="abcd"
											data-form-ctl="bc"
											data-order-ctl="abcdefghij">

											<?php
											$saved_data = data_to_post( $cols[6] . $manag_no . "_" . $i );

											// 旧versionとの互換性をたもつため、DB上の「並び順」の値に応じてstrとintに置き換える
											if ( '0' == $saved_data ) {
												$saved_data = 'a';
											} else if ( '1' == $saved_data ) {
												$saved_data = 'b';

											}
											$op_keys = array( 'a' => 'OR検索', 'b' => 'AND検索' );

											foreach ( $op_keys as $key => $value ) {
												$selected = feas_selected( $key, $manag_no, $i, $cols[6] );
												?>
												<option
													value="<?php echo esc_attr( $key ); ?>"
													<?php echo $selected; ?>>
													<?php echo esc_html( $value ); ?>
												</option>
												<?php
											}
											?>
										</select>
									</div>
									<!-- <div class="td">
										<div class="duplicate">
											<span></span>
										</div>
									</div> -->
									<div class="td">
										<div class="grab">
											<span></span>
										</div>
									</div>
								</div>

								<?php
								/** ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
								 *
								 *	二段目
								 *
								 * ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
								?>

								<div class="secondRow">
									<div class="td">
										<div class="ele-wrap">
											<div class="wrap-left">

												<?php
												/*============================
													ターム詳細項目
												 ============================*/
												?>
												<div
													id="cat_more_setting_<?php echo esc_attr( $i ); ?>"
													class="ctl term-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="bc"
													data-form-ctl="abcd"
													data-order-ctl="abcdefghij">

													<div class="ele-title">
														<h3>ターム詳細項目</h3>
													</div>

													<div class="ele-content">
														<?php
														// 除外IDの配列をカンマ区切りに
														if ( is_array( $_POST[$cols[11] . $manag_no . "_" . $i] ) ) {
															$_POST[$cols[11] . $manag_no . "_" . $i] = implode( ',', $_POST[$cols[11] . $manag_no . "_" . $i] );
														}
														?>
														<div
															id="term-ex_<?php echo esc_attr( $i ); ?>"
															class="ctl"
															data-term-ctl="bc"
															data-form-ctl="abcd"
															data-order-ctl="acdefghij">
															<h4>除外ID</h4>
															<input
																type="text"
																name="<?php echo esc_attr( $cols[11] . $manag_no . "_" . $i ); ?>"
																value="<?php echo esc_attr( $_POST[$cols[11] . $manag_no . "_" . $i] ); ?>" />
														</div>
														<div
															id="term-depth_<?php echo esc_attr( $i ); ?>"
															class="ctl"
															data-term-ctl="bc"
															data-form-ctl="abcd"
															data-order-ctl="acdefghij">
															<h4>階層</h4>
															<input
																type="text"
																name="<?php echo esc_attr( $cols[10] . $manag_no . "_" . $i ); ?>"
																value="<?php echo esc_attr( data_to_post( $cols[10] . $manag_no . "_" . $i ) ); ?>" />
														</div>
													</div>
												</div>
												<div
													id="cat_more_setting_<?php echo esc_attr( $i ); ?>_2nd"
													class="ctl term-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="bc"
													data-form-ctl="abcd"
													data-order-ctl="abcdefghij">

													<div class="ele-content">
														<?php $emptycat_checked = feas_checked( 'no', $manag_no, $i, $cols[14] ); ?>
														<label
															id="cat_more_setting_<?php echo esc_attr( $i ); ?>_2nd_1"
															for="<?php echo esc_attr( $cols[14] . $manag_no . "_" . $i ); ?>"
															class="ctl empty n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
															data-term-ctl="bc"
															data-form-ctl="abcd"
															data-order-ctl="cdefg">
															<input
																id="<?php echo esc_attr( $cols[14] . $manag_no . "_" . $i ); ?>"
																type="checkbox" name="<?php echo esc_attr( $cols[14] . $manag_no . "_" . $i ); ?>"
																value="no" <?php echo $emptycat_checked; ?>
																class="ctl n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
																data-term-ctl="bc"
																data-form-ctl="abcd"
																data-order-ctl="cdefg">
															&nbsp;登録件数が0件のカテゴリ/タームは表示しない
														</label>
														<?php $emptycat_checked = feas_checked( 'no', $manag_no, $i, $cols[19] ); ?>
														<label
															id="cat_more_setting_<?php echo esc_attr( $i ); ?>_2nd_2"
															for="<?php echo esc_attr( $cols[19] . $manag_no . "_" . $i ); ?>"
															class="ctl ajax n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
															data-term-ctl="c"
															data-form-ctl="a"
															data-order-ctl="cdefg">
															<input
																id="<?php echo esc_attr( $cols[19] . $manag_no . "_" . $i ); ?>"
																type="checkbox"
																name="<?php echo esc_attr( $cols[19] . $manag_no . "_" . $i ); ?>"
																value="no" <?php echo $emptycat_checked; ?>
																class="ctl n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
																data-term-ctl="c"
																data-form-ctl="a"
																data-order-ctl="cdefg">
															&nbsp;Ajaxフィルタリング
														</label>
													</div>
												</div>

												<?php
												/*============================
													フリーワード詳細項目
												 ============================*/
												?>
												<div
													id="<?php echo esc_attr( $cols[13] . $manag_no . "_" . $i ); ?>"
													class="ctl keyword-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="_abcd"
													data-form-ctl="e"
													data-order-ctl="_abcdefghij">
													<div class="ele-title">
														<h3>フリーワード詳細項目</h3>
													</div>
													<div class="ele-content">
														<h4>検索対象</h4>
														<?php
														$kwds_target = get_option( $cols[13] . $manag_no . "_" . $i );  // DBに保存済みのデータ取得（カンマ区切り）
														$kwds_target = explode( "," , $kwds_target ); // カンマで分解、配列に格納

														$k_data[0]['name']  = 'タイトル (post_title)';
														$k_data[0]['value'] = 'post_title';
														$k_data[1]['name']  = '本文 (post_content)';
														$k_data[1]['value'] = 'post_content';
														$k_data[2]['name']  = '抜粋 (post_excerpt)';
														$k_data[2]['value'] = 'post_excerpt';
														$k_data[3]['name']  = 'カスタムフィールド (meta_value)';
														$k_data[3]['value'] = 'meta_value';
														$k_data[4]['name']  = 'コメント (comment_content)';
														$k_data[4]['value'] = 'comment_content';
														$k_data[5]['name']  = '記事が属するターム名 (terms > name)';
														$k_data[5]['value'] = 'name';

														$kwds_checked = array();

														for ( $i_t = 0; $i_t < 6; $i_t++ ) {
															$kwds_checked[$i_t] = '';
															if ( isset( $kwds_target[$i_t] ) ) {
																for ( $ii_t = 0 , $k_cnt = count( $kwds_target ); $ii_t < $k_cnt; $ii_t++ ) {
																	//$kwds_checked[$i_t] = feas_checked( $k_data[$i_t]['value'], $manag_no, $i, $cols[13] );
																	if ( $kwds_target[$ii_t] == $k_data[$i_t]['value'] ) {
																		$kwds_checked[$i_t] = ' checked="checked"';
																	}
																}
															}
															?>
															<label>
																<input
																	type="checkbox"
																	name="<?php echo esc_attr( $cols[13] . $manag_no . "_" . $i ); ?>[]"
																	id="<?php echo esc_attr( $cols[13] . $manag_no . "_" . $i . "_" . $i_t ); ?>"
																	value="<?php echo esc_attr( $k_data[$i_t]['value'] ); ?>"
																	<?php echo $kwds_checked[$i_t]; ?>/>
																&nbsp;<?php echo esc_attr( $k_data[$i_t]['name'] ); ?>
															</label>
															<?php if ( 3 === $i_t ) {
																$kwds_keys = get_option( $cols[20] . $manag_no . '_' . $i );
																$kwds_keys = maybe_unserialize($kwds_keys);
																if ( $kwds_keys ) {
																	$kwds_keys = implode( ',', (array) $kwds_keys );
																} else {
																	$kwds_keys = '';
																}
																?>
																<p style="padding-left: 3em;">
																	<label>キーを限定（meta_key）&nbsp;
																		<input
																			type="text"
																			name="<?php echo esc_attr( $cols[20] . $manag_no . "_" . $i ); ?>"
																			value="<?php echo esc_attr( $kwds_keys ) ?>"
																			style="width: 60%" />
																	</label>
																</p>
																<?php
															}
														}
														?>
													</div>
													<div class="ele-content">
														<div id="<?php echo esc_attr( $cols[15] . $manag_no . "_" . $i ); ?>">
															<h4>あいまいさ</h4>
															<?php $kwds_yuragi_checked = feas_checked( 'no', $manag_no, $i, $cols[15] ); ?>
															<label>
																<input
																	type="checkbox"
																	name="<?php echo esc_attr( $cols[15] . $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[15] . $manag_no . "_" . $i ); ?>"
																	value="no"
																	<?php echo $kwds_yuragi_checked; ?> />
																&nbsp;半角/全角、ひらがな/カタカナを区別しない
															</label>
														</div>
													</div>
												</div>

												<div
													id="<?php echo esc_attr( $cols[13] . $manag_no . "_" . $i ); ?>_2nd"
													class="ctl keyword-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="_abcd"
													data-form-ctl="e"
													data-order-ctl="_abcdefghij">
													<div class="ele-content">
														<h4>プレースホルダーテキスト</h4>
															<label>
																<input
																	type="text"
																	class="freeword-placeholderText"
																	name="<?php echo esc_attr( $cols[30] . $manag_no . "_" . $i ); ?>"
																	value="<?php echo esc_attr( data_to_post( $cols[30] . $manag_no . '_' . $i ) ); ?>" />
															</label>
													</div>
												</div>

												<?php
												/*============================
													範囲検索
												 ============================*/

												/**
												 *	動作
												 */
												$cfrange_keys   = array( '0' => "しない", '1' => "前", '2' => "以前", '3' => "以後", '4'=> "後" );
												$cfrange_keys_2 = array( '0' => "しない", '1' => "未満", '2' => "以下", '3' => "以上", '4' => "超" );
												$output =
												$output_2 = '';

												for ( $i_cfr = 0, $cnt_cfr = count( $cfrange_keys ); $i_cfr < $cnt_cfr; $i_cfr++ ) {

													$selected = '';
													if ( $i_cfr == data_to_post( $cols[16] . $manag_no . "_" . $i ) ) {
														$selected = ' selected="selected"';
													}
													$output   .= '<option value="' . esc_attr( $i_cfr ) .'"' . $selected . '>' . esc_html( $cfrange_keys[$i_cfr] ) . '</option>';
													$output_2 .= '<option value="' . esc_attr( $i_cfr ) .'"' . $selected . '>' . esc_html( $cfrange_keys_2[$i_cfr] ) . '</option>';
												}

												/**
												 *	数値か文字か
												 */
												$selected_1 = $selected_2 = '';

												$rangeOrder_as = get_option( $cols[29] . $manag_no . "_" . $i );
												if ( 'str' == $rangeOrder_as ) {
													$selected_2 = ' selected="selected"';
												} else {
													$selected_1 = ' selected="selected"';
												}

												/**
												 *	テキスト入力か
												 */
												$cf_freeword_checked = feas_checked( 'yes', $manag_no, $i, $cols[22] );
												?>

												<div
													id="cf_range_setting_<?php echo esc_attr( $i ); ?>_0"
													class="ctl range-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="ad"
													data-form-ctl="ad"
													data-order-ctl="abcdefghij">
													<div class="ele-title">
														<h3>範囲検索</h3>
													</div>
													<div class="ele-content">
														<div>
															<!-- 投稿年月 -->
															<select
																class="ctl range-arc n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
																name="<?php echo esc_attr( $cols[16] . $manag_no . "_" . $i ); ?>"
																id="<?php echo esc_attr( $cols[16] . $manag_no . "_" . $i ); ?>_0"
																data-term-ctl="a"
																data-form-ctl="ad"
																data-order-ctl="abcdefghij"
																disabled="disabled">
																<?php echo $output; ?>
															</select>
															<!-- カスタムフィールド -->
															<select
																class="ctl range-cf n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
																name="<?php echo esc_attr( $cols[16] . $manag_no . "_" . $i ); ?>"
																id="<?php echo esc_attr( $cols[16] . $manag_no . "_" . $i ); ?>_1"
																data-term-ctl="d"
																data-form-ctl="ad"
																data-order-ctl="abcdefghij"
																disabled="disabled">
																<?php echo $output_2; ?>
															</select>
															<select
																class="ctl range-as n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
																id="<?php echo esc_attr( $cols[29] . $manag_no . "_" . $i ); ?>"
																name="<?php echo esc_attr( $cols[29] . $manag_no . "_" . $i ); ?>"
																data-term-ctl="d"
																data-form-ctl="ad"
																data-order-ctl="abcdefghij">
																<option value="int"<?php echo $selected_1; ?>>数値</option>
																<option value="str"<?php echo $selected_2; ?>>文字列</option>
															</select>
														</div>
														<div>
															<label>
																<input
																	id="<?php echo esc_attr( $cols[22] . $manag_no . "_" . $i ); ?>"
																	class="ctl range-by-text n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[29] . $manag_no . "_" . $i ); ?>"
																	type="checkbox"
																	name="<?php echo esc_attr( $cols[22] . $manag_no . "_" . $i ); ?>"
																	value="yes" <?php echo $cf_freeword_checked; ?>
																	data-term-ctl="ad"
																	data-form-ctl="ad"
																	data-order-ctl="abcdefghij">
																テキスト入力で範囲検索
															</label>
														</div>
													</div>
												</div>

												<?php
												/*============================
													カスタムフィールド詳細項目
												 ============================*/	?>
												<div
													id="cf_more_title"
													class="ele-title ctl"
													data-term-ctl="d"
													data-form-ctl="abcd"
													data-order-ctl="abcdefghij">
													<h3>カスタムフィールド詳細項目</h3>
												</div>

												<div
													id="cf_more_setting_<?php echo esc_attr( $i ); ?>"
													class="ctl meta-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="d"
													data-form-ctl="abcd"
													data-order-ctl="acdefghij">

													<div class="ele-content">
														<div>
															<label>単位を付与する&nbsp;
																<input
																	type="text"
																	name="<?php echo esc_attr( $cols[17] . $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[17] . $manag_no . "_" . $i ); ?>"
																	value="<?php echo esc_attr( data_to_post( $cols[17] . $manag_no . "_" . $i ) ); ?>" >
															</label>
															<?php
															$selected_0 = $selected_1 = '';
															if ( 0 == data_to_post( $cols[26] . $manag_no . "_" . $i ) ) {
																$selected_0 = ' selected="selected"';
															} else if ( 1 == data_to_post( $cols[26] . $manag_no . "_" . $i ) ) {
																$selected_1 = ' selected="selected"';
															}
															?>
															<label for="<?php echo esc_attr( $cols[26] . $manag_no . "_" . $i ); ?>">位置&nbsp;</label>
															<select name="<?php echo esc_attr( $cols[26] . $manag_no . "_" . $i ); ?>" id="<?php echo esc_attr( $cols[26] . $manag_no . "_" . $i ); ?>">
																<option value="0"<?php echo $selected_0; ?>>前</option>
																<option value="1"<?php echo $selected_1; ?>>後</option>
															</select>
														</div>

														<?php $cf_kugiri_checked = feas_checked( 'yes', $manag_no, $i, $cols[18] ); ?>
														<div>
															<label>
																<input
																	type="checkbox"
																	name="<?php echo esc_attr( $cols[18] . $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[18] . $manag_no . "_" . $i ); ?>"
																	value="yes"
																	<?php echo $cf_kugiri_checked; ?> />
																&nbsp;3桁ごとに半角カンマで区切る
															</label>
														</div>
													</div>
												</div>

												<div
													id="cf_more_setting_<?php echo esc_attr( $i ); ?>_2nd"
													class="ctl meta-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="d"
													data-form-ctl="abcd"
													data-order-ctl="abcdefghij">
													<div class="ele-content">
														<h4>Smart Custom Fields 関連</h4>
														<?php
														/*============================
															Smart Custom Fields
														 ============================*/

														// 真偽値
														$cf_shingi_checked = feas_checked( '1', $manag_no, $i, $cols[33] );
														?>
														<div
															id="<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>_0"
															class="ctl shingi-more n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
															data-term-ctl="d"
															data-form-ctl="ad"
															data-order-ctl="abcdefghij">
															<label>
																<input
																	type="radio"
																	name="<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>"
																	value="1"
																	<?php echo $cf_shingi_checked; ?> />
																&nbsp;真偽値として扱う
															</label>
															<div style="padding-left:3em">
																<label>真の場合の文字列&nbsp;
																	<input
																		type="text"
																		name="<?php echo esc_attr( $cols[25] . $manag_no . "_" . $i ); ?>"
																		value="<?php echo esc_attr( data_to_post( $cols[25] . $manag_no . '_' . $i ) ); ?>"
																		style="width: 60%" />
																</label>
															</div>
														</div>

														<?php
														// 関連する投稿
														$cf_related_post_checked = feas_checked( '2', $manag_no, $i, $cols[33] );
														?>
														<div>
															<label>
																<input
																	type="radio"
																	name="<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>_1"
																	value="2" <?php echo $cf_related_post_checked; ?> />
																&nbsp;関連する投稿
															</label>
														</div>

														<?php
														// 関連するターム
														$cf_related_term_checked = feas_checked( '3', $manag_no, $i, $cols[33] );
														?>
														<div>
															<label>
																<input
																	type="radio"
																	name="<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>_2"
																	value="3"
																	<?php echo $cf_related_term_checked; ?> />
																&nbsp;関連するターム
															</label>
														</div>

														<!-- ラジオボタンの選択解除 -->
														<script>
															jQuery(function(){
															  var nowchecked = jQuery('input[name=<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>]:checked').val();
															  jQuery('input[name=<?php echo esc_attr( $cols[33] . $manag_no . "_" . $i ); ?>]').click(function(){
																if(jQuery(this).val() == nowchecked) {
																  jQuery(this).prop('checked', false);
																  nowchecked = false;
																} else {
																  nowchecked = jQuery(this).val();
																}
															  });
															});
														</script>
													</div>
													<div class="ele-content">

														<h4>Advanced Custom Fields 関連</h4>
														<?php
														/*============================
															Advanced Custom Fields
														 ============================*/

														// 複数選択形式
														$cf_acf_multiple_checked = feas_checked( '1', $manag_no, $i, $cols[38] );
														?>
														<div>
															<label>
																<input
																	type="checkbox"
																	name="<?php echo esc_attr( $cols[38] . $manag_no . "_" . $i ); ?>"
																	id="<?php echo esc_attr( $cols[38] . $manag_no . "_" . $i ); ?>"
																	value="1"
																	<?php echo $cf_acf_multiple_checked; ?> />
																&nbsp;複数選択形式で登録した値
															</label>
														</div>
													</div>
												</div>

												<?php
												/**
												 *
												 *
												 * 追加設定項目を別のプログラムから追加するフック
												 *
												 *
												 */
												do_action( 'feas_admin_management_item_left', $manag_no, $i );
												?>

											</div><!-- .wrap-left -->
											<div class="wrap-right">

												<?php
												/*============================
													自由記述
												 ============================*/
												$freetext = get_option( $cols[36] . $manag_no . "_" . $i );
												$freetext = maybe_unserialize( $freetext );

												$contents = '';

												if ( is_array( $freetext ) ) {
													foreach( $freetext as $lines ) {
														if ( 1 !== $lines['depth'] ) {
															for ( $i_depth = 1; $i_depth < $lines['depth']; $i_depth++ ) {
																$contents .= '--';
															}
														}
														$sepFlag = false;
														if ( $lines['text'] !== $lines['value'] ) {
															$sepFlag = true;
														}
														$hasColon = strstr( $lines['value'], ':' );
														if ( false !== $hasColon ) {
															$lines['value'] = '"' . $lines['value'] . '"';
														}
														if ( $sepFlag ) {
															$contents .= $lines['text'] . ':' . $lines['value'] . "\n";
														} else {
															$contents .= $lines['value'] . "\n";
														}
													}
												}
												?>
												<div
													id="<?php echo esc_attr( $cols[36] . $manag_no . "_" . $i ); ?>"
													class="ctl disp_op_order_freetext n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
													data-term-ctl="abcd"
													data-form-ctl="abcd"
													data-order-ctl="b">
													<div class="ele-title">
														<h3>検索パーツの表示項目（自由記述の内容）</h3>
													</div>
													<div class="ele-content">
														<h4>要素の選択肢（表記:値）</h4>
														<textarea
															name="<?php echo esc_attr( $cols[36] . $manag_no . "_" . $i ); ?>"
															rows="8"
															cols="30"
															placeholder="例1）&#13;&#10;1,000円:1000&#13;&#10;2,000円:2000&#13;&#10;5,000円:5000&#13;&#10;&#13;&#10;例2）&#13;&#10;東京都&#13;&#10;--23区&#13;&#10;----千代田区&#13;&#10;----中央区&#13;&#10;----港区"><?php echo esc_attr( $contents ); ?></textarea>
			<!-- 											<button type="button" name="load_current_term" class="button-secondary" value="load">選択中の「条件」から読み込む</button> -->
													</div>
												</div>

												<div class="ele-title">
													<h3>その他の詳細項目</h3>
												</div>
												<div class="ele-content">

													<?php
													/*============================
														未選択時の文字列
													 ============================*/

													$text = data_to_post( $cols[27] . $manag_no . '_' . $i );
													if ( ! $text ) {
														$text = '---未指定---';
													}
													?>
													<span
														id="<?php echo esc_attr( $cols[27] . $manag_no . "_" . $i ); ?>"
														class="ctl noselect-text n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
														data-term-ctl="abcd"
														data-form-ctl="abcd"
														data-order-ctl="abcdefghij">
														<h4>未選択時の文字列</h4>
														<input
															type="text"
															name="<?php echo esc_attr( $cols[27] . $manag_no . "_" . $i ); ?>"
															class="inputNoSelect"
															value="<?php echo esc_attr( $text ); ?>" />
													</span>

													<?php
													/*============================
														未選択（value=0）の表示
													 ============================*/
													$noselect_checked = feas_checked( '1', $manag_no, $i, $cols[31] );
													?>
													<span
														id="<?php echo esc_attr( $cols[31] . $manag_no . "_" . $i ); ?>"
														class="ctl noselect-check n<?php echo esc_attr( $manag_no . "_" . $i ); ?>"
														data-term-ctl="abcd"
														data-form-ctl="d"
														data-order-ctl="abcdefghij">
														<label
															for="<?php echo esc_attr( $cols[31] . $manag_no . "_" . $i ); ?>">
															表示
															<input
																type="checkbox"
																name="<?php echo esc_attr( $cols[31] . $manag_no ."_" . $i ); ?>"
																value="1"
																<?php echo $noselect_checked; ?> />
														</label>
													</span>
												</div>

												<?php
												/*============================
													デフォルト値
												 ============================*/
												?>
												<div class="ele-content">
													<div>
														<h4>デフォルト値</h4>
														<input
															type="text"
															id="<?php echo esc_attr( $cols[39] . $manag_no . "_" . $i ); ?>"
															class="inputDefaultValue" name="<?php echo esc_attr( $cols[39] . $manag_no . "_" . $i ); ?>"
															value="<?php echo esc_attr( data_to_post( $cols[39] . $manag_no . "_" . $i ) ); ?>" />
													</div>
												</div>

												<?php
												/*============================
													前に挿入／後に挿入
												 ============================*/
												?>
												<div class="ele-content">
													<div>
														<h4>前に挿入するHTML/CSS</h4>
														<input
															type="text"
															name="<?php echo esc_attr( $cols[7] . $manag_no . "_" . $i ); ?>"
															id="<?php echo esc_attr( $cols[7] . $manag_no . "_" . $i ); ?>"
															class="insertTextBeforeItem"
															value="<?php echo esc_attr( data_to_post( $cols[7] . $manag_no . "_" . $i ) ); ?>" >
													</div>
												</div>
												<div class="ele-content">
													<div>
														<h4>後に挿入するHTML/CSS</h4>
														<input
															type="text"
															name="<?php echo esc_attr( $cols[8] . $manag_no . "_" . $i ); ?>"
															id="<?php echo esc_attr( $cols[8] . $manag_no . "_" . $i ); ?>"
															class="insertTextAfterItem"
															value="<?php echo esc_attr( data_to_post( $cols[8] . $manag_no . "_" . $i ) ); ?>" >
													</div>
												</div>

												<?php

												/**
												 *
												 *
												 * 追加設定項目を別のプログラムから追加するフック
												 *
												 *
												 */
												do_action( 'feas_admin_management_item_right', $manag_no, $i );

												?>

												<div class="ele-content" style="text-align:right">
													<div>
														<?php
														/*============================
															並び順
														 ============================*/
														?>
														<label
															for="<?php echo esc_attr( $cols[0] . $manag_no . "_" . $i ); ?>">
															並び順
														</label>
														<select
															class="itemOrder"
															name="<?php echo esc_attr( $cols[0] . $manag_no . "_" . $i ); ?>"
															id="<?php echo esc_attr( $cols[0] . $manag_no . "_" . $i ); ?>">
															<?php
															for ( $i_no = 0; $i_no < $line_cnt; $i_no++ ) {
																$selected = '';
																if ( $i == $i_no ) {
																	$selected = ' selected="selected"';
																}
																?>
																<option
																	value="<?php echo esc_attr( $i_no ); ?>"
																	<?php echo $selected; ?>>
																	<?php echo esc_html( $i_no + 1 ); ?>
																</option>
																<?php
															}
															?>
														</select>

														<?php
														/*============================
															表示／非表示
														 ============================*/
														$selected_0 = $selected_1 = '';

														if ( 0 === data_to_post( $cols[1] . $manag_no . "_" . $i ) ) {
															$selected_0 = ' selected="selected"';
														} else if ( 1 === data_to_post( $cols[1] . $manag_no . "_" . $i ) ) {
															$selected_1 = ' selected="selected"';
														}
														?>
														<label
															for="<?php echo esc_attr( $cols[1] . $manag_no . "_" . $i ); ?>">
															表示
														</label>
														<select
															name="<?php echo esc_attr( $cols[1] . $manag_no . "_" . $i ); ?>"
															id="<?php echo esc_attr( $cols[1] . $manag_no . "_" . $i ); ?>">
															<option value="0"<?php echo $selected_0; ?>>表示する</option>
															<option value="1"<?php echo $selected_1; ?>>表示しない</option>
														</select>

														<?php
														/*============================
															消去
														 ============================*/
														?>
														<label
															for="<?php echo esc_attr( $cols[9] . $manag_no . "_" . $i ); ?>"
															class="ele-delete">
															項目を削除
															<input
																type="checkbox"
																name="<?php echo esc_attr( $cols[9] . $manag_no ."_" . $i ); ?>"
																id="<?php echo esc_attr( $cols[9] . $manag_no . "_" . $i ); ?>"
																value="del" />
														</label>
													</div>
												</div><!-- .wrap-content -->

											</div><!-- .wrap-right -->
										</div><!-- .ele-wrap -->
									</div>
								</div>
							</div><!-- .alternate .tr -->

					<?php } if ( $i > 0 ) { ?>

							<?php
							/*============================
								検索ボタン
							 ============================*/
							?>
							<div class="widefat alternate">
								<div class="firstRow">
									<div class="td">
										<div>
											<span class="btn-toggle"></span>
										</div>
									</div>
									<div>
										<input
											type="text"
											name="<?php echo esc_attr( $feadvns_search_b_label . $manag_no ); ?>"
											style="width: 100%"
											value="<?php echo esc_attr( data_to_post( $feadvns_search_b_label . $manag_no ) ); ?>" />
									</div>
									<div colspan="4" class="description">
									（検索ボタンの文字列）
									</div>
								</div>
								<div class="secondRow">
									<div>
										<div class="ele-wrap">
											<div class="wrap-left">
												<div class="ele-content resetBtn1">

													<?php
													$reset_btn_sw_checked = feas_checked( '1', $manag_no, false, $feadvns_reset_btn_sw );
													$reset_btn_js_checked = feas_checked( '1', $manag_no, false, $feadvns_reset_btn_js );
													?>

													<div>
														<h4>リセットボタン</h4>
														<label
															for="<?php echo esc_attr( $feadvns_reset_btn_sw . $manag_no ); ?>"
															class="">
															<input
																type="checkbox"
																name="<?php echo esc_attr( $feadvns_reset_btn_sw . $manag_no ); ?>"
																id="<?php echo esc_attr( $feadvns_reset_btn_sw . $manag_no ); ?>"
																value="1"
																<?php echo esc_attr( $reset_btn_sw_checked ); ?> />
															リセットボタンを表示
														</label>
														<label
															for="<?php echo esc_attr( $feadvns_reset_btn_js . $manag_no ); ?>"
															class="">
															<input
																type="checkbox"
																name="<?php echo esc_attr( $feadvns_reset_btn_js . $manag_no ); ?>"
																id="<?php echo esc_attr( $feadvns_reset_btn_js . $manag_no ); ?>"
																value="1"
																<?php echo esc_attr( $reset_btn_js_checked ); ?> />
															JavaScriptによる全選択解除
														</label>
													</div>
												</div>
												<div class="ele-content resetBtn2">
													<?php
													$selected_0 = $selected_1 = '';
													if ( 0 == data_to_post( $feadvns_reset_btn_position . $manag_no ) ) {
														$selected_0 = 'selected="selected"';
													} else if ( 1 == data_to_post( $feadvns_reset_btn_position . $manag_no ) ) {
														$selected_1 = 'selected="selected"';
													}
													?>
													<div>
														<label
															for="<?php echo esc_attr( $feadvns_reset_btn_position . $manag_no ); ?>">
															表示位置&nbsp;
														</label>
														<select
															name="<?php echo esc_attr( $feadvns_reset_btn_position . $manag_no ); ?>"
															id="<?php echo esc_attr( $feadvns_reset_btn_position . $manag_no ); ?>">
															<option value="0" <?php echo $selected_0; ?>>前</option>
															<option value="1" <?php echo $selected_1; ?>>後</option>
														</select>
													</div>
												</div>
												<div class="ele-content resetText">
													<div>
														<h4>リセットボタンの文字列</h4>
														<input
															type="text"
															name="<?php echo esc_attr( $feadvns_reset_btn_text . $manag_no ); ?>"
															value="<?php echo esc_attr( data_to_post(  $feadvns_reset_btn_text . $manag_no  ) ); ?>">
													</div>
												</div>
											</div>
											<div class="wrap-right">
												<div class="ele-content">
													<div>
														<h4>前に挿入するHTML/CSS</h4>
														<input
															type="text"
															name="<?php echo esc_attr( $feadvns_search_b_label . $manag_no . '_before' ); ?>"
															style="width: 100%"
															value="<?php echo esc_attr( data_to_post( $feadvns_search_b_label . $manag_no . '_before' ) ); ?>">
													</div>
												</div>
												<div class="ele-content">
													<div>
														<h4>後に挿入するHTML/CSS</h4>
														<input
															type="text"
															name="<?php echo esc_attr( $feadvns_search_b_label . $manag_no . '_after' ); ?>"
															style="width: 100%"
															value="<?php echo esc_attr( data_to_post( $feadvns_search_b_label . $manag_no . '_after' ) ); ?>">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

					<?php } else { ?>

							<?php
							/*============================
								検索項目がゼロの場合
							 ============================*/
							?>
							<div>
								<div style="text-align:center;">検索条件がありません</div>
							</div>

					<?php } ?>

						</div>
					</div>

					<button type="submit" form="line_action" value="add_line" class="button-secondary action">項目を追加</button>

				</ul><!-- 検索項目の作成タブ -->
				<ul>

					<?php
					/*============================
						プレビュー
					 ============================*/
					?>
					<div id="feas-pv">

						<div id="feas_pv_upper">
							<div class="pv_nav">
								<span><a href="" onclick="frames['feas_pv'].history.back(); return false">&lt;&nbsp;前へ戻る</a></span>
								<span><a href="" onclick="frames['feas_pv'].history.go(); return false">次へ進む&nbsp;&gt;</a></span>
							</div>

							<?php
							// テーマのCSSを読み込むかどうか
/*
							$theme_css_checked = '';
							$load_flag = get_option( $pv_theme_css . $manag_no );
							if ( 'yes' === $load_flag ) {
								$theme_css_checked = ' checked="checked"';
							}
*/
							// 「デザイン」のCSSを読み込むかどうか
							$css_checked = feas_checked( 'yes', $manag_no, false, $pv_css );
							?>
							<div class="pv_option">
<!--
								<input id="<?php //echo esc_attr( $pv_theme_css . $manag_no ); ?>" type="checkbox" name="<?php //echo esc_attr( $pv_theme_css . $manag_no ); ?>" value="yes" <?php //echo $theme_css_checked; ?>>
								<label for="<?php //echo esc_attr( $pv_theme_css . $manag_no ); ?>">テーマのstyle.cssを適用する</label>
-->
								<input
									id="<?php echo esc_attr( $pv_css . $manag_no ); ?>"
									type="checkbox"
									name="<?php echo esc_attr( $pv_css . $manag_no ); ?>"
									value="yes"
									<?php echo $css_checked; ?>>
								<label
									for="<?php echo esc_attr( $pv_css . $manag_no ); ?>">
									「デザイン」のCSSを適用する
								</label>
							</div>
						</div>

						<?php
						if ( is_ssl() ) {
							$src_url = home_url( "/", "https" );
						} else {
							$src_url = home_url( "/" );
						}
						?>
						<div id="feas_pv_lower">
							<iframe
								class=""
								name="feas_pv" src="<?php echo esc_url( $src_url ); ?>?feas_pv=1&amp;feas_mng_no=<?php echo esc_attr( $manag_no ); ?>&amp;feas_pv_type=search"
								width="100%"
								marginheight="0"
								scrolling="auto">
								プレビュー
							</iframe>
						</div>

					</div>

				</ul><!-- プレビュータブ -->
				<ul>

					<?php
					/*============================
						コード
					 ============================*/
					?>
					<div id="feas-code-sample">
						<p style="color:black;">
						テンプレートに設置する場合はPHPのコード、投稿本文やテキストウィジェット等に設置する場合はショートコードをコピー＆ペーストしてご使用ください。<br />
						検索フォームのデザインは、「デザイン」ページにCSSを記述するか、テーマフォルダ内のstyle.cssに直接追記して下さい。</p>

						<?php
						$disp_no = null;
						if ( $manag_no > 0 )
							$disp_no = $manag_no;
						?>

						<!-- 検索フォーム -->
						<div class="ele-title">
							<h3>検索フォームを設置・表示する関数</h3>
						</div>
						<div class="ele-content">
							<textarea onfocus="SelectText( this );" style="width: 300px; height: 2em;">feas_search_form(<?php echo esc_attr( $disp_no ); ?>)</textarea>
						</div>
						<div class="ele-content">
							<h4>記述例</h4>
<pre>&lt;?php
if ( function_exists( 'feas_search_form' ) ) {
	feas_search_form(<?php echo esc_attr( $disp_no ); ?>);
}
?&gt;</pre>

							<h4>ショートコード</h4>
							<textarea onfocus="SelectText( this );" style="width: 300px; height: 2em;">[feas-search-form<?php if ( $manag_no > 0 ){ print( " id=" . $disp_no ); } ?>]</textarea>
						</div>

						<br>

						<!-- 件数 -->
						<div class="ele-title">
							<h3>対象の記事数または検索にヒットした件数を表示する関数</h3>
						</div>
						<div class="ele-content">
							<textarea onfocus="SelectText( this );" style="width: 300px; height: 2em;">feas_search_count(<?php echo esc_attr( $disp_no ); ?>)</textarea>
						</div>
						<div class="ele-content">
							<h4>記述例</h4>
	<pre>現在の登録件数：&lt;?php feas_search_count(<?php echo esc_attr( $disp_no ); ?>); ?&gt; 件</pre>
							<h4>ショートコード</h4>
							<textarea onfocus="SelectText( this );" style="width: 300px; height: 2em;">[feas-search-count<?php if ( $manag_no > 0 ){ print( " id=" . $disp_no ); } ?>]</textarea>
						</div>

						<br>

						<!-- 検索条件 -->
						<div class="ele-title">
							<h3>検索結果のページに検索条件を列記する関数</h3>
						</div>
						<div class="ele-content">
							<textarea onfocus="SelectText( this );" style="width: 300px; height: 2em;">feas_search_query()</textarea>
						</div>
						<div class="ele-content">
							<h4>引数</h4>
							<pre>feas_search_query( $output = true, $separator = ',', $before = '&lt;span&gt;', $after = '&lt;/span&gt;', $widget = false );</pre>
							<p>初期値：画面に出力 / 半角カンマ区切り / キーワードをspanで囲む / ウィジェットではない</p>
							<h4>記述例１</h4>
	<pre>「&lt;?php feas_search_query(); ?&gt;」の検索結果 &lt;?php feas_search_count(); ?&gt; 件</pre>
							<h4>記述例２</h4>
	<pre>&lt;?php feas_search_query( true, ' ', '&lt;span class="word"&gt;', '&lt;/span&gt;', false ); ?&gt;</pre>
							<p>画面に出力 / 半角スペース区切り / キーワードをspan.wordで囲む / ウィジェットではない</p>
							<h4>ショートコード</h4>
							<textarea onfocus="SelectText( this );" style="width: 300px; height: 2em;">[feas-search-query]</textarea>
						</div>

						<br>

						<div class="ele-title">
							<h3>フィルター/アクションフック</h3>
						</div>
						<div class="ele-content">
							<p>フィルター/アクションフックを使用すると、デフォルトの出力内容をカスタマイズしたり、設定画面に独自の設定項目を追加することなどができます。下記ページをご確認ください。</p>
							<a href="https://fe-advanced-search.com/manual/action-filter-hook/" target="_blank" title="アクション/フィルターフック一覧 - FE Advanced Search">アクション/フィルターフック一覧 - FE Advanced Search</a>
						</div>

						<br>

					</div>
				</ul><!-- コードタブ -->
			</div><!-- タブ全体 -->

			<?php
			/*============================
				「設定を保存」ボタン
			 ============================*/
			?>
			<?php if ( isset( $_POST['c_form_number'] ) ){ ?>
				<input type="hidden" name="c_form_number" value="<?php echo esc_attr( $_POST['c_form_number'] ); ?>" />
			<?php } ?>
			<input type="hidden" name="<?php echo esc_attr( $feadvns_form_no . $manag_no ); ?>" value="<?php echo esc_attr( $manag_no ); ?>" />
			<input type="hidden" name="current_form_no" value="<?php echo esc_attr( $manag_no ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $feadvns_search_current_tab ); ?>" value="0" />
			<input type="hidden" name="ac" value="update" />
			<!-- <input type="hidden" name="line_cnt" value="<?php //echo esc_attr( $line_cnt ); ?>" /> -->
			<input type="submit" value="設定を保存" class="button-primary action" />

		</form>

		<?php
		/*============================
			「項目を追加」ボタン
		 ============================*/
		?>

		<div>
			<form id="line_action" action="" method="post">
				<input type="hidden" name="line_action" value="add_line" />
				<input type="hidden" name="c_form_no" value="<?php echo esc_attr( $manag_no ); ?>" />
				<input type="hidden" name="current_form_no" value="<?php echo esc_attr( $manag_no ); ?>" />
			</form>
		</div>

	</div>
</div>


<script type="text/javascript">

	jQuery(function($){

		// タブコントロール - ページ読み込み時
		var index = 0<?php //echo esc_html( $_POST[$feadvns_search_current_tab] ); ?>;
		$('.tab li').removeClass('active').eq(index).addClass('active');
		$('.area ul').removeClass('show').eq(index).addClass('show');
		$("input[name='<?php echo $feadvns_search_current_tab; ?>']").val(index);

		// タブコントロール - クリック時
		$('.tab li').click(function(){
			var index = $('.tab li').index(this);
			$('.tab li').removeClass('active');
			$(this).addClass('active');
			$('.area ul').removeClass('show').eq(index).addClass('show');
			$("input[name='<?php echo $feadvns_search_current_tab; ?>']").val(index);
		});

		/*==============================================================
		 * ページ読み込み時に、全体設定「検索結果の並び順」の追加ドロップダウンの表示/非表示をコントロール
		 */

		// 第一条件
		if ( 'post_meta' == $( '#feadvns_sort_target' ).val() ) {

			$( '#feadvns_sort_target_cfkey' ).fadeIn();
			$( '#feadvns_sort_target_cfkey_as' ).fadeIn();
			$( '#feadvns_sort_order' ).fadeIn();

		} else if ( 'rand' == $( '#feadvns_sort_target' ).val() ) {

			$( '#feadvns_sort_target_cfkey' ).hide();
			$( '#feadvns_sort_target_cfkey_as' ).hide();
			$( '#feadvns_sort_order' ).hide();

		} else {

			$( '#feadvns_sort_target_cfkey' ).hide();
			$( '#feadvns_sort_target_cfkey_as' ).hide();
			$( '#feadvns_sort_order' ).fadeIn();
		}

		// 第二条件
		if ( 'post_meta' == $( '#feadvns_sort_target_2nd' ).val() ) {

			$( '#feadvns_sort_target_cfkey_2nd' ).fadeIn();
			$( '#feadvns_sort_target_cfkey_as_2nd' ).fadeIn();
			$( '#feadvns_sort_order_2nd' ).fadeIn();

		} else if ( 'rand' == $( '#feadvns_sort_target_2nd' ).val() ) {

			$( '#feadvns_sort_target_cfkey_2nd' ).hide();
			$( '#feadvns_sort_target_cfkey_as_2nd' ).hide();
			$( '#feadvns_sort_order_2nd' ).hide();

		} else if ( 'none' == $( '#feadvns_sort_target_2nd' ).val() ) {

			$( '#feadvns_sort_target_cfkey_2nd' ).hide();
			$( '#feadvns_sort_target_cfkey_as_2nd' ).hide();
			$( '#feadvns_sort_order_2nd' ).hide();

		} else {

			$( '#feadvns_sort_target_cfkey_2nd' ).hide();
			$( '#feadvns_sort_target_cfkey_as_2nd' ).hide();
			$( '#feadvns_sort_order_2nd' ).fadeIn();
		}

		/*==============================================================
		 * 全体設定「検索結果の並び順」ドロップダウン選択で追加ドロップダウンの表示コントロール
		 */

		// 第一条件
		jQuery( '#feadvns_sort_target' ).change( function() {

			if ( 'post_meta' == $( this ).val() ) {

				$( '#feadvns_sort_target_cfkey' ).fadeIn();
				$( '#feadvns_sort_target_cfkey_as' ).fadeIn();
				$( '#feadvns_sort_order' ).fadeIn();

			} else if ( 'rand' == $( this ).val() ) {

				$( '#feadvns_sort_target_cfkey' ).hide();
				$( '#feadvns_sort_target_cfkey_as' ).hide();
				$( '#feadvns_sort_order' ).hide();

			} else {

				$( '#feadvns_sort_target_cfkey' ).hide();
				$( '#feadvns_sort_target_cfkey_as' ).hide();
				$( '#feadvns_sort_order' ).fadeIn();
			}
		});

		// 第二条件
		jQuery( '#feadvns_sort_target_2nd' ).change( function() {

			if ( 'post_meta' == $( this ).val() ) {

				$( '#feadvns_sort_target_cfkey_2nd' ).fadeIn();
				$( '#feadvns_sort_target_cfkey_as_2nd' ).fadeIn();
				$( '#feadvns_sort_order_2nd' ).fadeIn();

			} else if ( 'rand' == $( this ).val() ) {

				$( '#feadvns_sort_target_cfkey_2nd' ).hide();
				$( '#feadvns_sort_target_cfkey_as_2nd' ).hide();
				$( '#feadvns_sort_order_2nd' ).hide();

			} else if ( 'none' == $( '#feadvns_sort_target_2nd' ).val() ) {

				$( '#feadvns_sort_target_cfkey_2nd' ).hide();
				$( '#feadvns_sort_target_cfkey_as_2nd' ).hide();
				$( '#feadvns_sort_order_2nd' ).hide();

			} else {

				$( '#feadvns_sort_target_cfkey_2nd' ).hide();
				$( '#feadvns_sort_target_cfkey_as_2nd' ).hide();
				$( '#feadvns_sort_order_2nd' ).fadeIn();
			}
		});

		/*==============================================================
		 * Ajaxフィルタリングにチェックが入っているときは、「0件」にもチェック
		 * 「0件」のチェックを外したときに、Ajaxにチェックが入っているときは外す
		 */
		$( '.ctl.ajax input' ).on( 'click', function() {

			// for末尾の数値=行番目を取得
			var form_no = $( this ).parent().attr('for').slice(-3);

			// 0件
			var ck_empty = $( '.ctl.empty.n' + form_no + ' input' ).attr( 'checked' );
			// Ajax
			var ck_ajax = $( '.ctl.ajax.n' + form_no + ' input' ).attr( 'checked' );

			if ( ck_ajax ) {
				if ( ! ck_empty ) {
					$( '.ctl.empty.n' + form_no + ' input' ).attr( 'checked', true );
				}
			}
		});
		$( '.ctl.empty input' ).on( 'click', function() {

			// for末尾の数値=行番目を取得
			var form_no = $( this ).parent().attr( 'for' ).slice( -3 );

			// 0件
			var ck_empty = $( '.ctl.empty.n' + form_no + ' input' ).attr( 'checked' );
			// Ajax
			var ck_ajax = $( '.ctl.ajax.n' + form_no + ' input' ).attr( 'checked' );

			if ( ! ck_empty ) {
				if ( ck_ajax ) {
					$( '.ctl.ajax.n' + form_no + ' input' ).attr( 'checked', false );
				}
			}
		});

		/*==============================================================
		 *	矢印クリックで二段目を開閉
		 */
		var speed = 300;
		$('.btn-toggle').on('click', function(event){
			var parent_tr = $(this).closest('.alternate');
			var target = $(this).closest('.alternate').find('.secondRow');
			$(target).slideToggle(speed, block_to_table());
			if( $(parent_tr).hasClass('active') ) {
				$(this).closest('.alternate').removeClass('active');
			} else {
				$(this).closest('.alternate').addClass('active');
			}
		});

		/**
		 * slideToggleで表示にするとdisplay属性がblockになりtableレイアウトが崩れるのを防ぐため
		 * slideToggle完了時のフォールバック関数としてdisplay:table-rowを設定する。
		 */
		function block_to_table() {
		    try {
		        $('form#fm .tbody .alternate').css('display', 'grid');
		    }
		    catch(e) {}
		}

		/*==============================================================
		 * 「条件」「形式」「要素内の並び順」の３つのドロップダウンの状態によって
		 * 詳細オプションの表示/非表示をコントロールする
		 * 以下の４つのdata属性をもちいる。
		 *
		 * １）data-visible-ctl-current　データ保存前の現在のリアルタイム値の一時格納
		 * ２）data-term-ctl　「条件」ドロップダウンの種類に応じてa〜dを割り当てる
		 *
		 *		- 投稿年月				-> a
		 *		- タグ					-> b
		 *		- カテゴリ/ターム		-> c
		 *		- カスタムフィールド	-> d
		 *
		 * ３）data-form-ctl　「形式」ドロップダウンの種類に応じてa〜eを割り当てる
		 *
		 *		- ドロップダウン		-> a
		 *		- セレクトボックス		-> b
		 *		- チェックボックス		-> c
		 *		- ラジオボタン			-> d
		 *		- フリーワード			-> e
		 *
		 * ４）data-order-ctl　「要素内の並び順」ドロップダウンの種類に応じてa〜eを割り当てる
		 *
		 *		- 年月					-> a
		 *		- 自由記述				-> b
		 *		- term_id				-> c
		 *		- name					-> d
		 *		- slug					-> e
		 *		- 外部プラグイン		-> f
		 *		- ランダム				-> g
		 *		- meta_key				-> h
		 *		- meta_value			-> i
		 *
		 * 表示/非表示をコントロールしたい項目には.ctlというクラスを割り当てると同時に
		 * 上記の下3つのdata属性を付与し、それぞれのデータには、その項目を表示したい条件に合致するアルファベットを単数〜複数羅列する。
		 * 例）data-term-ctl="ac" は、「条件」が投稿年月かカテゴリ/タームの場合にのみ表示
		 * ３つのdata属性のいずれも合致するアルファベットが含まれる場合のみ表示し（fadeIn）、他は非表示（hide）。
		 * なお、表示コントロールしたい項目には.ctlの他、一意のidも付与する（ないとindexOfに値が渡されずUndefinedエラーが出る）。
		 * チェックボックスなどform要素に直接上記.ctlとdata属性を与えるとともに、labelなど周辺要素にも同様に付与し、表示をコントロールする。
		 *
		 * 以下、コードは、ページ読み込み時に初期値を設定する段と、３つのドロップダウン（class="trigger"）がchangeした際に実行される段に別れる。
		 */

		/*==============================================================
		 *
		 * ページのローディング時に、DBから呼び出した 「条件」「形式」「要素内の並び順」のselect値に基づいて
		 * .ctl というクラスがついたすべての要素の表示/非表示をコントロールする
		 *
		 */

		// 初期化
		var manag_no = <?php echo esc_html( $manag_no ); ?>,
		    max = <?php echo esc_html( $line_cnt ); ?>,
		    i = 0,
		    cnt,
		    form_no = '0_0';

		// 設定済みの条件数（行数）ループを回す
		for ( i = 0; i < max; i++ ) {

			var form_no = manag_no + '_' + i;

			/* ===================== 条件 ===================== */
			var sTermTemp = $(".sTerm.n"+form_no+" option:selected").data('visible-ctl'); // アルファベット

			// display:noneなどで取得できなかった場合はアンダースコアを代入
			if ( ! sTermTemp ) {
				sTermTemp = '_';
			}

			// 「条件」selectのdata-visible-ctr-currentの現在地を設定
			$(".sTerm.n"+form_no).data('visible-ctl-current', sTermTemp);

			/* ===================== 形式 ===================== */
			var sFormTemp = $(".sForm.n"+form_no+" option:selected").data('visible-ctl');

			// 取得できなかった場合はアンダースコアを代入
			if ( ! sFormTemp ) {
				sFormTemp = '_';
			}

			// 「条件」selectのdata-visible-ctr-currentの現在地を設定
			$(".sForm.n"+form_no).data('visible-ctl-current', sFormTemp);


				/** ========================
				 *	まず「条件」と「形式」のドロップダウンの現地値を設定し、
				 *	それらの値に基づき、表示する「項目内の並び順」ドロップダウンをコントロールする
				 * ========================*/

				var _tCondTemp = { 'term': sTermTemp, 'form': sFormTemp }

				var _cTarget = $(".sTerm.n"+form_no).closest('.firstRow').find('.sOrder');

				// すべての.ctl要素
				$(_cTarget).each( function( index2, cond2 ){

					var id = $(cond2).attr('id');
					var flag = 0;

					// selectの状態
					$.each( _tCondTemp, function( index, cond ){

						var target = $('#' + id).data(index + '-ctl');

						// data-visible-ctlにcondの値が含まれている場合はflagカウントを回す
						if( -1 != target.indexOf(cond) ) {
							flag++;
						}
					});
					// すべてのselectに対してフラグがtrueの.ctlだけ表示
					if ( flag >= Object.keys(_tCondTemp).length ) {
						$(cond2).fadeIn();
						if ( $(cond2).hasClass('sOrder') ) {
							$(cond2).prop("disabled", false);
						}
					} else {
						if ( $(cond2).hasClass('sOrder') ) {
							$(cond2).prop("disabled", true);
						}
					}
				});

				/** ===========================
				 * 「形式」が「フリーワード」の場合は、「条件」ドロップダウンを非表示
				 *
				 * ============================ */

				if ( 'e' === sFormTemp ) {
					$(".sTerm.n"+form_no).closest('.firstRow').find('.sTerm').hide();
				} else {
					$(".sTerm.n"+form_no).closest('.firstRow').find('.sTerm').fadeIn();
				}

			/* ===================== 項目内の並び順 ===================== */
			var sOrderTemp = $(".sOrder.n"+form_no+":not(:disabled) option:selected").data('visible-ctl');

			// 取得できなかった場合はアンダースコアを代入
			if ( ! sOrderTemp ) {
				sOrderTemp = '_';
			}

			// 「要素内の並び順」selectのdata-visible-ctr-currentの現在地を設定
			$(".sOrder.n"+form_no).data('visible-ctl-current', sOrderTemp);

			// ============== 二段目のすべての.ctl要素 ===============

			// ３つのドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCondTemp  = { 'term': sTermTemp, 'form': sFormTemp, 'order': sOrderTemp }

			// 二段目のすべての.ctl要素を格納
			var cTarget = $(".sTerm.n"+form_no).closest('.firstRow').next('.secondRow').find('.ctl');

			// 一旦すべて非表示・無効化
			cTarget.hide();
			cTarget.prop("disabled", true);

			// 取得した二段目のすべての.ctl要素をループで回す
			$(cTarget).each( function( index2, cond2 ){

				var id = $(cond2).attr('id');
				var flag = 0;

				// 「条件」「形式」「要素内の並び順」の３つのドロップダウンをループで回す
				$.each( tCondTemp, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット

					var target = $('#' + id).data(index + '-ctl');

					// data-visible-ctlに上記３つドロップダウンの値が含まれている場合はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}
				});

				// ３つのドロップダウンに対してすべてのフラグがたった.ctlだけ表示
				if ( flag >= Object.keys(tCondTemp).length ) {
					$(cond2).fadeIn();
					$(cond2).prop("disabled", false);
				} else {
					$(cond2).prop("disabled", true);
				}
			});

			// ============== 一段目のすべての.ctl要素のコントロール ===============

			// ３つのドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCondTemp_2nd  = { 'term': sTermTemp, 'form': sFormTemp, 'order': sOrderTemp }

			// 一段目のすべての.ctl要素を格納
			var cTargetTemp_2nd = $(".sTerm.n"+form_no).closest('.firstRow').find('.ctl');

			// 取得した一段目のすべての.ctl要素をループで回す
			$(cTargetTemp_2nd).each( function( index2, cond2 ){

				var id = $(cond2).attr('id');
				var flag = 0;

				// 「条件」「形式」「要素内の並び順」の３つのドロップダウンをループで回す
				$.each( tCondTemp_2nd, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット

					var target = $('#' + id).data(index + '-ctl');

					// data-visible-ctlにcondの値が含まれている場合はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}
				});

				// すべてのselectに対してフラグがtrueの.ctlだけ表示
				if ( flag >= Object.keys(tCondTemp_2nd).length ) {
					$(cond2).fadeIn();
					$(cond2).prop("disabled", false);
				} else {
					$(cond2).prop("disabled", true);
				}
			});
		}

		/*==============================================================
		 *
		 * 「条件」「形式」「要素内の並び順」のselect（class="trigger"）がchangeされたら
		 * ３つのドロップダウンの値に基づいて、.ctl というクラスがついたすべての要素の表示/非表示をコントロールする
		 *
		 */

		$('.trigger').on('change', function(){

			// 今選択したドロップダウンのdeta-visible-ctl値を親selectのdata-visible-ctl-current値にセット
			var sElemTemp = $("option:selected", this).data('visible-ctl');
			$(this).data('visible-ctl-current', sElemTemp);

			// 現在の各selectのdata-visible-ctl-currentの値を取得
			var sTermTemp = $(this).closest('.firstRow').find('select.sTerm option:selected').data('visible-ctl');
			$(this).closest('.firstRow').find('select.sTerm').data('visible-ctl-current', sTermTemp);

			// 現在有効化されているselect.sTermのdata-visible-ctl-currentの値を取得
			var sTerm  = $(this).closest('.firstRow').find('select.sTerm').data('visible-ctl-current');

			// 取得できなかった場合はアンダースコアを代入
			if ( ! sTerm ) {
				sTerm = '_';
			}

			// 現在の各selectのdata-visible-ctl-currentの値を取得
			var sFormTemp = $(this).closest('.firstRow').find('select.sForm option:selected').data('visible-ctl');
			$(this).closest('.firstRow').find('select.sForm').data('visible-ctl-current', sFormTemp);

			// 現在有効化されているselect.sFormのdata-visible-ctl-currentの値を取得
			var sForm  = $(this).closest('.firstRow').find('select.sForm').data('visible-ctl-current');

			// 取得できなかった場合はアンダースコアを代入
			if ( ! sForm ) {
				sForm = '_';
			}

				/** ========================
				 *	「要素内の並び順」のドロップダウンについては
				 *	まず「条件」と「形式」のドロップダウンの現地値を設定し
				 *	それらの値に基づき、表示する「要素内の並び順」ドロップダウンを３つのうちから表示コントロールする
				 * ========================*/

				var _tCond = { 'term': sTerm, 'form': sForm }

				var _cTarget = $(this).closest('.firstRow').find('.sOrder');
				_cTarget.hide();

				// 取得した一段目のすべての.ctl要素をループで回す
				$(_cTarget).each( function( index2, cond2 ){

					var id = $(cond2).attr('id');
					var flag = 0;

					// 「条件」と「形式」の２つのドロップダウンをループで回す
					$.each( _tCond, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット

						var target = $('#' + id).data(index + '-ctl');

						// data-visible-ctlにcondの値が含まれている場合はflagカウントを回す
						if( -1 != target.indexOf(cond) ) {
							flag++;
						}
					});
					// すべてのselectに対してフラグがtrueの.ctlだけ表示
					if ( flag >= Object.keys(_tCond).length ) {
						$(cond2).fadeIn();
						if ( $(cond2).hasClass('sOrder') ) {
							$(cond2).prop("disabled", false);
						}
					} else {
						if ( $(cond2).hasClass('sOrder') ) {
							$(cond2).prop("disabled", true);
						}
					}
				});

				// 「形式」が「フリーワード」の場合は、「条件」ドロップダウンを非表示
				if ( 'e' === sForm ) {
					$(this).closest('.firstRow').find('.sTerm').hide();
				} else {
					$(this).closest('.firstRow').find('.sTerm').fadeIn();
				}

			// 現在の各selectのdata-visible-ctl-currentの値を取得
			var sOrderTemp = $(this).closest('.firstRow').find('select.sOrder:not(:disabled) option:selected').data('visible-ctl');
			$(this).closest('.firstRow').find('select.sOrder').data('visible-ctl-current', sOrderTemp);

			// 現在有効化されているselect.sOrderのdata-visible-ctl-currentの値を取得
			var sOrder  = $(this).closest('.firstRow').find('select.sOrder:not(:disabled)').data('visible-ctl-current');

			// 取得できなかった場合はアンダースコアを代入
			if ( ! sOrder ) {
				sOrder = '_';
			}

			// ============== 二段目のすべての.ctl要素 ===============

			// ３つのドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCond   = { 'term': sTerm, 'form': sForm, 'order': sOrder }

			// 二段目のすべての.ctl要素を格納
			var cTarget = $(this).closest('.firstRow').next('.secondRow').find('.ctl');

			// 一旦、すべての.ctl要素を削除
			cTarget.hide();
			cTarget.prop("disabled", true);

			// 取得した一段目のすべての.ctl要素をループで回す
			$(cTarget).each( function( index2, cond2 ){

				var id = $(cond2).attr('id');
				var flag = 0;

				// 「条件」「形式」「要素内の並び順」の３つのドロップダウンをループで回す
				$.each( tCond, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット

					var target = $('#' + id).data(index + '-ctl');

					// data-visible-ctlにcondの値が含まれている場合はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}
				});
				// すべてのselectに対してフラグがtrueの.ctlだけ表示
				if ( flag >= Object.keys(tCond).length ) {
					$(cond2).fadeIn();
					$(cond2).prop("disabled", false);
				}
			});

			// ============== 一段目のすべての.ctl要素 ===============

			// ３つのドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCond_2nd  = { 'term': sTerm, 'form': sForm, 'order': sOrder }

			// 一段目のすべての.ctl要素を格納
			var cTarget_2nd = $(this).closest('.firstRow').find('.ctl');

			// 一旦、すべての.ctl要素を削除
			cTarget_2nd.hide();
			cTarget_2nd.prop("disabled", true);

			// 取得した一段目のすべての.ctl要素をループで回す
			$(cTarget_2nd).each( function( index2, cond2 ){

				var id = $(cond2).attr('id');
				var flag = 0;

				// 「条件」「形式」「要素内の並び順」の３つのドロップダウンをループで回す
				$.each( tCond_2nd, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット

					var target = $('#' + id).data(index + '-ctl');

					// data-visible-ctlにcondの値が含まれている場合はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}
				});

				// ３つのすべてのselectに対してフラグがtrueである.ctlだけ表示
				if ( flag >= Object.keys(tCond_2nd).length ) {
					$(cond2).fadeIn();
					$(cond2).prop("disabled", false);
				} else {
					$(cond2).prop("disabled", true);
				}
			});
		});
	});

	/**
	 *
	 * 検索項目の複製
	 *
	 */
// 	jQuery(function($){
// 		$('.duplicate').on('click', function(){
// 			let parent = $(this).closest('.alternate');
// 			$(parent).clone(true).insertAfter(parent);
//
// 			// 「並び順」ドロップダウンの選択肢を複製した数分だけ増やす
// 			let nodeCnt = $('#searchItemsBody .alternate').length - 1; // 検索ボタン分を除く
// 			$('input[name="line_cnt"]').val(nodeCnt);
// 			$('select.itemOrder').append('<option value="' + (nodeCnt - 1) + '">' + nodeCnt + '</option>');
//
// 			$('#searchItemsBody .alternate').each(function(index){
// 				$(this).find('select.itemOrder option[value="' + index + '"]').prop('selected',true);
// 			});
// 		});
// 	});

	/**
	 *
	 * Sortable
	 * 検索項目のドラッグ＆ドラッグ
	 *
	 */
	var searchItems = new Sortable(searchItemsBody, {
		group: {
			name: 'shared',
			pull: 'clone' // To clone: set pull to 'clone'
		},
		handle: '.grab',
		animation: 150,
		ghostClass: 'bgItem__dragging',
		onEnd: function (evt) {

			jQuery('#searchItemsBody .alternate').each(function(index){
				jQuery(this).find('select.itemOrder').val(index);
			});

			var itemEl = evt.item;  // dragged HTMLElement
			evt.to;    // target list
			evt.from;  // previous list
			evt.oldIndex;  // element's old index within old parent
			evt.newIndex;  // element's new index within new parent
			evt.oldDraggableIndex; // element's old index within old parent, only counting draggable elements
			evt.newDraggableIndex; // element's new index within new parent, only counting draggable elements
			evt.clone // the clone element
			evt.pullMode;  // when item is in another sortable: `"clone"` if cloning, `true` if moving

			//console.log(evt.oldDraggableIndex);
		},
	});

</script>

<?php

// 設定画面下部に出力するフック
do_action( 'feas_admin_management_footer', $manag_no );

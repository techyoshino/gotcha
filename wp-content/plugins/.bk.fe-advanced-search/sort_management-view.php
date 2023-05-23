<?php
/////////////////////////////////////////////////
//	ソート > 表示部
/////////////////////////////////////////////////
?>
<div class="wrap">
	<div id="feas-admin">
		<div id="feas-head" class="wp-header-end clearfix">
			<div id="feas-head-upper" class="clearfix">
				<h1>FE Advanced Search</h1>
				<a href="https://www.firstelement.co.jp/" id="feas-logo" target="_blank" title="開発会社HPへ移動"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/logo-feas-white-shadow-s@2x-min.png" width="106" height="27"></a>
			</div>
			<div id="feas-head-lower" class="clearfix">
				<div id="feas-version">version 1.8.3</div>
				<div id="feas-support">
					<a href="https://fe-advanced-search.com/manual/" target="_blank" title="使用説明書へ移動">使用説明書</a>
					<a href="https://fe-advanced-search.com/support/" target="_blank" title="フォーラムへ移動">フォーラム </a>
					<a href="https://chatwork.com/feas" target="_blank" title="チャットワークへ移動">チャットワーク</a>
					<a href="https://fe-advanced-search.com/contact/" target="_blank" title="メールフォームへ移動" class="icon icon_mail"></a>
					<a href="https://www.facebook.com/firstelementjp/" target="_blank" title="Facbookページへ移動" class="icon icon_fb"></a>
					<a href="https://twitter.com/feas_wp/" target="_blank" title="Twitterへ移動" class="icon icon_tw"></a>
				</div>
			</div>
		</div>
		
		<?php
		/*============================
			設定するソートメニューの選択
		 ============================*/
		$output = '';
		
		for ( $i = 0; $i <= $get_form_max; $i++ ) {
				
			$form_name = $selected = $form_no_tmp = '';
			
			$form_no_tmp = get_option( $feadvns_form_no . $i );
			$form_name   = get_option( $feadvns_search_form_name . $form_no_tmp );
			if ( ! $form_name ) {
				$form_name = '（フォームID = ' . $form_no_tmp . '）';
			}

			if ( $manag_order_no == $form_no_tmp ) {
				$selected = ' selected="selected"';
			}
			$output .= '<option value="' . $i . '"' . $selected . '>' . esc_html( $form_name ) . '</option>';
		}
		?>
		
		<div id="feas-contents-header">
			<h2 id="feas-sectitle" class="left">検索フォーム「<?php echo esc_html( db_op_get_value( $feadvns_search_form_name . $manag_order_no ) ); ?>（No.<?php echo esc_html( $manag_order_no ); ?>）」に対応するソートボタンの設定</h2>
			<form action="<?php menu_page_url( 'feas_sort_management' ); ?>&noheader=true" method="post">		
				<select name="c_order_number">
					<?php echo $output; ?>
				</select>
				<input type="hidden" name="current_order_no" value="<?php echo esc_attr( $manag_order_no ); ?>" />
				<input type="submit" value="実行" class="button-secondary action" />
			</form>
		</div>

		<div class="clearfix" style="clear:both;">
			
			<?php
			/*============================
				ソートボタンの設定
			 ============================*/	
			?>
			
			<!-- ソートボタン -->
			<form action='<?php menu_page_url( 'feas_sort_management' ); ?>&noheader=true' method='POST' name='fm' onSubmit ="return order_check();">
	
				<ul class="tab clearfix">
					<li class="active">ソートボタンの作成</li>
					<li>プレビュー</li>
					<li>コード</li>
				</ul>
					
				<input type="hidden" name="<?php echo $feadvns_sort_current_tab; ?>" value="0" />
				
				<div class="area">
					<ul class="show">				
	
						<table class="widefat" style='clear:both;'>
							
							<thead>
								<tr>
									<th></th>
									<th style="width: 10%;">見出し</th>
									<th style="width: 50%;">ターゲット</th>
								</tr>
							</thead>
							
							<tbody>
								
								<?php
								for ( $i = 0; $i < $line_cnt; $i++ ) { 
								
									// 「表示しない」設定の行は背景をグレイに
									$addclass_gray = null;
									if ( isset( $_POST[$cols_order[1] . $manag_order_no . "_" . $i] ) && $_POST[$cols_order[1] . $manag_order_no . "_" . $i] == 1 ) {
										$addclass_gray = "grayout";
									}
									?>
									
									<!-- 一段目 -->
									<tr class="widefat alternate <?php echo esc_attr( $addclass_gray ); ?>">
										
										<?php
										/*============================
											開閉ボタン
										 ============================*/	
										?>
										<td><span class="btn-toggle"></span></td>
										<?php
										/*============================
											見出し
										 ============================*/	
										?>
										<td>
											<input type='text' name='<?php echo esc_attr( $cols_order[6] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[6] . $manag_order_no . "_" . $i ); ?>' style='width:150px' value='<?php echo esc_attr( data_to_post( $cols_order[6] . $manag_order_no . "_" . $i ) ); ?>'>
										</td>
										
										<?php
										/*============================
											ターゲット
										 ============================*/	
										?>
										<td>
											<select data-visible-ctl-current="" class="trigger sTerm n<?php echo esc_attr( $manag_order_no . "_" . $i ); ?>" id="<?php echo esc_attr( $cols_order[0] . $manag_order_no . "_" . $i ); ?>" name="<?php echo esc_attr( $cols_order[0] . $manag_order_no . "_" . $i ); ?>" style="float:left;">
												<?php
												$op_keys = array(
													'post_date'  => array( 'text' => '投稿日時', 'visible_ctl' => 'a' ),
													'post_title' => array( 'text' => 'タイトル', 'visible_ctl' => 'b' ),
													'post_name'  => array( 'text' => 'スラッグ', 'visible_ctl' => 'c' ),
													'post_meta'  => array( 'text' => 'カスタムフィールド', 'visible_ctl' => 'd' ),
													'rand'       => array( 'text' => 'ランダム', 'visible_ctl' => 'e' ),
												);
												
												foreach ( $op_keys as $k => $v ) {
													$selected = '';
													if ( isset( $_POST[$cols_order[0] . $manag_order_no . "_" . $i] ) && $_POST[$cols_order[0] . $manag_order_no . "_" . $i] == $k ) {
														$selected = 'selected="selected"';
													}
													?>
													<option value="<?php echo esc_attr( $k ); ?>" <?php echo $selected; ?> data-visible-ctl="<?php echo esc_attr( $v['visible_ctl'] ); ?>"><?php echo esc_attr( $v['text'] ); ?></option>
													<?php
												}
												?>
											</select>
												
											<?php
											/* カスタムフィールドのキー */
											?>
											<select id="<?php echo esc_attr( $cols_order[9] . $manag_order_no . "_" . $i ); ?>" class="ctl cfkey feadvns_sort_target_cfkey n<?php echo esc_attr( $manag_order_no . "_" . $i ); ?>" name="<?php echo $cols_order[9] . $manag_order_no . "_" . $i; ?>" data-term-ctl="d">
												<?php
												for ( $i_meta = 0, $cnt_meta = count( $get_metas ); $i_meta < $cnt_meta; $i_meta++ ) {
													$selected = '';
													if ( isset( $_POST[$cols_order[9] . $manag_order_no . "_" . $i] ) && $_POST[$cols_order[9] . $manag_order_no . "_" . $i] == $get_metas[$i_meta]->meta_key ) {
														$selected = 'selected="selected"';
													}
													?>
													<option value="<?php echo esc_attr( $get_metas[$i_meta]->meta_key ); ?>" <?php echo $selected; ?>><?php echo esc_html( $get_metas[$i_meta]->meta_key ); ?></option>
													<?php
												}
												?>
											</select>
																	
											<?php
											/* 数値か文字か */
											$selected_1 = $selected_2 = '';
											if ( isset( $_POST[$cols_order[10] . $manag_order_no . "_" . $i] ) && 'str' == $_POST[$cols_order[10] . $manag_order_no . "_" . $i] ) {
												$selected_2 = 'selected="selected"';
											} else {
												$selected_1 = 'selected="selected"';
											}
											?>
											
											<select id="<?php echo esc_attr( $cols_order[10] . $manag_order_no . "_" . $i ); ?>" class="ctl cfas feadvns_sort_target_cfkey_as n<?php echo esc_attr( $manag_order_no . "_" . $i ); ?>" name="<?php echo $cols_order[10] . $manag_order_no . "_" . $i; ?>" data-term-ctl="d">
												<option value="int" <?php echo $selected_1; ?>>数値</option>
												<option value="str" <?php echo $selected_2; ?>>文字</option>
											</select>
										
										</td>
									</tr>
									
									<!-- 二段目 -->
									<tr class="<?php echo esc_attr( $addclass_gray ); ?>">
										
										<td colspan="3" style="vertical-align: top;">
											
											<div class="ele-wrap">
												<div class="wrap-left">
													<div class="ele-title">
														<h3>ソートボタン</h3>
													</div>
													
													<?php
													/*============================
														テキスト/画像
													 ============================*/
													$asc_text_data = data_to_post( $cols_order[7] . $manag_order_no . "_" . $i );
													if ( ! empty( $asc_text_data ) ) {
														$asc_text = data_to_post( $cols_order[7] . $manag_order_no . "_" . $i );
													} else {
														$asc_text = '▲';
													}
													$desc_text_data = data_to_post( $cols_order[8] . $manag_order_no . "_" . $i );
													if ( ! empty( $desc_text_data ) ) {
														$desc_text = data_to_post( $cols_order[8] . $manag_order_no . "_" . $i );
													} else {
														$desc_text = '▼';
													}
													?>
													<div class="ele-content">
														<div>
															<h4>昇順テキスト/画像</h4>
															<input type='text' name='<?php echo esc_attr( $cols_order[7] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[7] . $manag_order_no . "_" . $i ); ?>' style='width:100%' value='<?php echo esc_attr( $asc_text ); ?>' >
														</div>
													</div>
													<div class="ele-content">
														<div>
															<h4>降順テキスト/画像</h4>
															<input type='text' name='<?php echo esc_attr( $cols_order[8] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[8] . $manag_order_no . "_" . $i ); ?>' style='width:100%' value='<?php echo esc_attr( $desc_text ); ?>'>
														</div>
													</div>
												</div>
												<div class="wrap-right">
													<div class="ele-title">
														<h3>その他の詳細項目</h3>
													</div>
													<div class="ele-content">
														<div>
															<h4>前に挿入するHTML/CSS</h4>
																<input type='text' name='<?php echo esc_attr( $cols_order[4] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[4] . $manag_order_no . "_" . $i ); ?>' style='width:100%' value='<?php echo esc_attr( data_to_post( $cols_order[4] . $manag_order_no . "_" . $i ) ); ?>' >
														</div>
													</div>
													<div class="ele-content">
														<div>
															<h4>後に挿入HTML/CSS</h4>
																<input type='text' name='<?php echo esc_attr( $cols_order[5] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[5] .  $manag_order_no . "_" . $i ); ?>' style='width:100%' value='<?php echo esc_attr( data_to_post( $cols_order[5] . $manag_order_no . "_" . $i ) ); ?>' >
														</div>
													</div>
													
													<div class="ele-content" style="text-align:right">
														<div>
															<label for='<?php echo esc_attr( $cols_order[2] . $manag_order_no . "_" . $i ); ?>'>並び順</label>
															<select name='<?php echo esc_attr( $cols_order[2] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[2] . $manag_order_no . "_" . $i ); ?>'>
																<?php
																for ( $i_no = 0; $i_no < $line_cnt; $i_no++ ) {
																	$selected = null;
																	if ( isset( $_POST[$cols_order[2] . $manag_order_no . "_" . $i]) ) {
																		if ( $i == $i_no ) {
																			$selected = 'selected="selected"';
																		}
																	} else {
																		
																		if ( $i_no == $line_cnt - 1 ) {
																			$selected ='selected="selected"';
																		}
																	}
																?>
																<option value='<?php echo esc_attr( $i_no ); ?>' <?php echo $selected; ?> ><?php echo esc_attr( $i_no + 1 ); ?></option>
																<?php
																}
																?>
															</select>
															
															<label for='<?php echo esc_attr( $cols_order[1] . $manag_order_no . "_" . $i ); ?>'>表　示</label>
															<select name='<?php echo esc_attr( $cols_order[1] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[1] . $manag_order_no . "_" . $i ); ?>'>
																<?php
																$selected_0 = $selected_1 = '';
																
																$disp_order = $_POST[$cols_order[1] .$manag_order_no . "_" . $i];
																if ( 1 == $disp_order ) {
																	$selected_1 = ' selected="selected"';
																} else {
																	$selected_0 = ' selected="selected"';
																}
																?>
																<option value='0' <?php echo $selected_0; ?> >表示する</option>
																<option value='1' <?php echo $selected_1; ?> >表示しない</option>
															</select>
															
															<label for='<?php echo esc_attr( $cols_order[3] . $manag_order_no . "_" . $i ); ?>' class="ele-delete">項目を削除
																<input type='checkbox' name='<?php echo esc_attr( $cols_order[3] . $manag_order_no . "_" . $i ); ?>' id='<?php echo esc_attr( $cols_order[3] .$manag_order_no . "_" . $i ); ?>' value='del'>
															</label>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<?php 
								} 
									
								if ( $i == 0 ) {
									?>
									<tr>
										<td colspan="3" style='text-align:center;'>ソートボタンがありません</td>
									</tr>
									<?php 	
								}
								?>
							
							</tbody>
							
							<tfoot>
							</tfoot>
							
						</table>
						
						<button type="submit" form="line_action" value="add_line" class="button-secondary action">項目を追加</button>
	
					</ul><!-- フォーム全体のタブ -->
					<ul>
										
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
								$css_checked = '';
								$load_flag = get_option( $pv_css . $manag_order_no );
								if ( 'yes' === $load_flag ) {
									$css_checked = ' checked="checked"';
								}
								?>		
								<div class="pv_option">
	<!--
									<input id="<?php //echo esc_attr( $pv_theme_css . $manag_no ); ?>" type="checkbox" name="<?php //echo esc_attr( $pv_theme_css . $manag_no ); ?>" value="yes" <?php //echo $theme_css_checked; ?>>
									<label for="<?php //echo esc_attr( $pv_theme_css . $manag_no ); ?>">テーマのstyle.cssを適用する</label>
	-->
									<input id="<?php echo esc_attr( $pv_css . $manag_order_no ); ?>" type="checkbox" name="<?php echo esc_attr( $pv_css . $manag_order_no ); ?>" value="yes" <?php echo $css_checked; ?>>
									<label for="<?php echo esc_attr( $pv_css . $manag_order_no ); ?>">「デザイン」のCSSを適用する</label>
								</div>
							</div>
							
							
							<?php
							if ( is_ssl() ) {
								$src_url = home_url( "/", "https" );
							} else {
								$src_url = home_url( "/" );
							}
							?>
							<div id="feas_pv_lower"><iframe class="" src="<?php echo esc_url( $src_url ); ?>?feas_pv=1&amp;feas_mng_no=<?php echo esc_attr( $manag_order_no ); ?>&amp;feas_pv_type=sort" width="100%" marginheight="0" scrolling="auto">プレビュー</iframe></div>
							
						</div>
					
					</ul><!-- フォーム全体のタブ -->
					<ul>

						<?php
						$disp_no = '';
						if ( $manag_order_no > 0 ) {
							$disp_no = $manag_order_no;
						}
						?>	
						<div id="feas-code-sample">
							
							<p style="color:black;">
							テーマ/テンプレートに設置する際はPHPのコード、投稿本文やテキストウィジェット等に設置するにはショートコードをコピー／ペーストしてご使用ください。<br />
							ソートボタンのデザインは、「デザイン」ページにCSSを記述するか、テーマフォルダ内のstyle.cssに直接追記して下さい。</p>
				
							<h4>PHP</h4>
							<textarea onfocus="SelectText( this );" style="width: 20%; height: 2em;">&lt;?php feas_sort_menu(<?php echo esc_textarea( $disp_no ); ?>); ?&gt;</textarea>
							
							<h4>テーマ内での記述例</h4>
			
<pre>&lt;?php if ( function_exists( 'feas_sort_menu' ) ) { ?&gt;
	&lt;h4&gt;検索結果を並べ替える&lt;/h4&gt;
	&lt;div id=&quot;feas-sort-menu&quot;&gt;
		&lt;?php feas_sort_menu(<?php echo esc_textarea( $disp_no ); ?>); ?&gt;
	&lt;/div&gt;
&lt;?php } ?&gt</pre>
							
							<h4>ショートコード</h4>
							<textarea onfocus="SelectText( this );" style="width: 20%; height: 2em;">[feas-sort-menu<?php if ( $manag_order_no > 0 ) { echo esc_textarea( " id=" . $disp_no ); } ?>]</textarea>
						
						</div>
			
					</ul><!-- コードタブ -->
				</div><!-- タブ全体 -->
			
				<input type='hidden' name='ac' value='update' />
				<input type="hidden" name="current_order_no" value="<?php echo esc_attr( $manag_order_no ); ?>" />
				<input type='submit' value='設定を保存' class='button-primary action' />
			
			</form>
			
			<!-- 項目を追加ボタン -->
			<form id="line_action" action="" method="post">
<!-- 				<input type='submit' value='項目を追加' class='button-secondary action' /> -->
				<input type='hidden' name='line_action' value='add_line' />
				<input type="hidden" name="current_order_no" value="<?php echo esc_attr( $manag_order_no ); ?>" />
			</form>
			
		</div>
		
	</div>
</div>

<script type="text/javascript">

	// コード自動選択
	function SelectText( element ) {
	  window.setTimeout(
	      function() { element.select(); },
	      0
	      );
	}
	
	// 並び順チェック
	function order_check() {
		
		for ( i = 0; i < <?php echo esc_html( $line_cnt ); ?>; i++ ) {
			var ele_order_name = '<?php echo esc_html( $cols_order[2] . $manag_order_no ); ?>_' + i;
			var obj = document.getElementById( ele_order_name );
			
			for ( i_s = 0; i_s < i; i_s++ ) {
				var check_order_name = '<?php echo esc_html( $cols_order[2] . $manag_order_no ); ?>_' + i_s;
				var check_obj = document.getElementById( check_order_name );
	
				if ( obj.value == check_obj.value ) {
					alert( "並び順が同じものがあります。" );
					return false;
				}
			}
		}
		return true;
	}

	jQuery( document ).ready( function( $ ) {
		
		// タブコントロール - ページ読み込み時
		var index = 0<?php //echo esc_html( $_POST[$feadvns_sort_current_tab] ); ?>;
		$('.tab li').removeClass('active').eq(index).addClass('active');
		$('.area ul').removeClass('show').eq(index).addClass('show');
		$("input[name='<?php echo $feadvns_sort_current_tab; ?>']").val(index);
		
		// タブコントロール - クリック時
		$('.tab li').click(function(){  
			var index = $('.tab li').index(this);
			$('.tab li').removeClass('active');
			$(this).addClass('active');
			$('.area ul').removeClass('show').eq(index).addClass('show');
			$("input[name='<?php echo $feadvns_sort_current_tab; ?>']").val(index);
		});
		
		/*==============================================================
		 * 「ターゲット」ドロップダウンの値でカスタムフィールドの副項目の2つのドロップダウンの表示をコントロール
		 * 
		 */
		function feas_change_form_ele_status( line_cnt ) {
			
			var targetSel = jQuery( '.sTarget.n' + line_cnt ).val();
			
			// カスタムフィールド
			if ( 'post_meta' == targetSel ) {
				
				$( '.ctl.cfkey.n' + line_cnt ).fadeIn().attr( 'disabled', false );
				$( '.ctl.cfas.n' + line_cnt ).fadeIn().attr( 'disabled', false );
			
			} else {
				
				$( '.ctl.cfkey.n' + line_cnt ).hide().attr( 'disabled', true );
				$( '.ctl.cfas.n' + line_cnt ).hide().attr( 'disabled', true );		
			}
		}
		
		// ページ読み込み時
		for ( var i = 0; i < <?php echo esc_html( $line_cnt ); ?>; i++ ) {
			
			feas_change_form_ele_status( '<?php echo esc_html( $manag_order_no ); ?>_' + i );
		}
		
		// 「ターゲット」変更時
		jQuery( '.sTarget' ).change( function() {
			
			// id末尾の数値 = 行番目を取得
			var line_cnt = $( this ).attr( 'id' ).slice( -3 );		
			
			feas_change_form_ele_status( line_cnt );
		});
		
		/*==============================================================
		 * 「＋」クリックで二段目を開閉
		 */
		var speed = 300;
		$('.btn-toggle').on('click', function(event){
			var parent_tr = $(this).closest('.alternate');
			var target = $(this).closest('.alternate').next().find('div.ele-wrap');
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
		        $('form tbody tr').css('display', 'table-row');
		    }
		    catch(e) {}
		}
		
		/*==============================================================
		 * 「ターゲット」のドロップダウンの状態によって
		 * 詳細オプションの表示/非表示をコントロールする
		 *
		 * １）data-visible-ctl-current　データ保存前の現在のリアルタイム値の一時格納
		 * ２）data-term-ctl　「ターゲット」ドロップダウンの種類に応じてa〜eを割り当てる
		 *
		 *		- 投稿年月				-> a
		 *		- タイトル				-> b
		 *		- スラッグ		　　	-> c
		 *		- カスタムフィールド	-> d
		 *      - ランダム　　　　　　　-> e
		 *
		 * 表示/非表示をコントロールしたい項目には.ctlというクラスを割り当てると同時に
		 * 上記２のdata属性を付与し、それぞれのデータには、その項目を表示したい条件に合致するアルファベットを単数〜複数羅列する。
		 * 例）data-term-ctl="ac" は、「条件」が投稿年月かカテゴリ/タームの場合にのみ表示
		 * data属性のいずれも合致するアルファベットが含まれる場合のみ表示し（fadeIn）、他は非表示（hide）。
		 * なお、表示コントロールしたい項目には.ctlの他、一意のidも付与する（ないとindexOfに値が渡されずUndefinedエラーが出る）。
		 * 
		 * 以下、コードは、ページ読み込み時に初期値を設定する段と、３つのドロップダウン（class="trigger"）がchangeした際に実行される段に別れる。
		 */
		 
		/*==============================================================
		 *
		 * ページのローディング時に、DBから呼び出した 「ターゲット」のselect値に基づいて
		 * .ctl というクラスがついたすべての要素の表示/非表示をコントロールする
		 *
		 */
		 
		// 初期化
		var manag_no = <?php echo esc_html( $manag_order_no ); ?>,
		    max = <?php echo esc_html( $line_cnt ); ?>,
		    i = 0,
		    cnt,
		    form_no = '0_0';
		
		// 設定済みの条件数（行数）ループを回す
		for ( i = 0; i < max; i++ ) {
			
			var form_no = manag_no + '_' + i;
			
			
			/* ===================== ターゲット ===================== */
			var sTermTemp = $(".sTerm.n"+form_no+" option:selected").data('visible-ctl'); // アルファベット
					
			// display:noneなどで取得できなかった場合はアンダースコアを代入
			if ( ! sTermTemp ) {
				sTermTemp = '_';
			}
			
			// 「ターゲット」selectのdata-visible-ctr-currentの現在値を設定
			$(".sTerm.n"+form_no).data('visible-ctl-current', sTermTemp);
			
			
			// ============== 二段目のすべての.ctl要素 ===============
			
			// ドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCondTemp  = { 'term': sTermTemp }
					
			// 二段目のすべての.ctl要素を格納
			var cTarget = $(".sTerm.n"+form_no).closest('tr').next('tr').find('.ctl');
		
			// 取得した二段目のすべての.ctl要素をループで回す
			$(cTarget).each( function( index2, cond2 ){
				
				var id = $(cond2).attr('id');
				var flag = 0;
							
				// ドロップダウンをループで回す
				$.each( tCondTemp, function( index, cond ){ // indexは「term」、condはaからzのアルファベット
													
					var target = $('#' + id).data(index + '-ctl');
									
					// data-visible-ctlにcondの値が含まれている場合はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}			
				});
				
				// フラグがtrueの.ctlだけ表示
				if ( true === flag >= Object.keys(tCondTemp).length ) {
					$(cond2).fadeIn();				
				}
			});
			
			// ============== 一段目のすべての.ctl要素のコントロール ===============
			
			// ドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCondTemp_2nd  = { 'term': sTermTemp }
			
			// 一段目のすべての.ctl要素を格納
			var cTargetTemp_2nd = $(".sTerm.n"+form_no).closest('tr').find('.ctl');
			
			// 取得した一段目のすべての.ctl要素をループで回す
			$(cTargetTemp_2nd).each( function( index2, cond2 ){
				
				var id = $(cond2).attr('id');
				var flag = 0;
				
				// ドロップダウンをループで回す
				$.each( tCondTemp_2nd, function( index, cond ){ // indexは「term」、condはaからzのアルファベット
													
					var target = $('#' + id).data(index + '-ctl');
					
					// 現在、親ループで回している.ctlのdata-visible-ctlにcondの値が含まれている場合（＝表示OK）はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}
				});
				
				// すべてのselectに対してフラグがtrueの.ctlだけ表示
				if ( flag >= Object.keys(tCondTemp_2nd).length ) {
					
					$(cond2).fadeIn();
					
					// カスタムフィールドのキーと数値/文字のselectのdisabledを解除
					if ( $(cond2).hasClass('cfkey') || $(cond2).hasClass('cfas') ) {
						$(cond2).prop("disabled", false);
					}
				}
			});
		}

		/*==============================================================
		 *
		 * 「ターゲット」のselect（class="trigger"）がchangeされたら
		 * ドロップダウンの値に基づいて、.ctl というクラスがついたすべての要素の表示/非表示をコントロールする
		 *
		 */
		 		
		$('.trigger').on('change', function(){
			
			// 今選択したドロップダウンのdeta-visible-ctl値を親selectのdata-visible-ctl-current値にセット
			var sElemTemp = $("option:selected", this).data('visible-ctl');
			$(this).data('visible-ctl-current', sElemTemp);
						
			// 現在の各selectのdata-visible-ctl-currentの値を取得
			var sTermTemp = $(this).closest('tr').find('select.sTerm option:selected').data('visible-ctl');
			$(this).closest('tr').find('select.sTerm').data('visible-ctl-current', sTermTemp);
					
			// 現在有効化されているselect.sTermのdata-visible-ctl-currentの値を取得
			var sTerm  = $(this).closest('tr').find('select.sTerm').data('visible-ctl-current');
					
			// 取得できなかった場合はアンダースコアを代入
			if ( ! sTerm ) {
				sTerm = '_';
			}	
				
			// ============== 二段目のすべての.ctl要素 ===============
			
			// ドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCond   = { 'term': sTerm }	
				
			// 二段目のすべての.ctl要素を格納
			var cTarget = $(this).closest('tr').next('tr').find('.ctl');
			
			// 一旦、すべての.ctl要素を削除
			cTarget.hide();
			
			// 取得した一段目のすべての.ctl要素をループで回す
			$(cTarget).each( function( index2, cond2 ){
				
				var id = $(cond2).attr('id');
				var flag = 0;
				
				// ドロップダウンをループで回す
				$.each( tCond, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット
													
					var target = $('#' + id).data(index + '-ctl');
								
					// 現在、親ループで回している.ctlのdata-visible-ctlにcondの値が含まれている場合（＝表示OK）はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}			
				});
				// すべてのselectに対してフラグがtrueの.ctlだけ表示
				if ( flag >= Object.keys(tCond).length ) {
					$(cond2).fadeIn();
				}
			});
			
			// ============== 一段目のすべての.ctl要素 ===============
			
			// ドロップダウンのdata-visible-ctl-currentの値を配列に格納
			var tCond_2nd  = { 'term': sTerm }
			
			// 一段目のすべての.ctl要素を格納
			var cTarget_2nd = $(this).closest('tr').find('.ctl');
			
			// 一旦、すべての.ctl要素を削除
			cTarget_2nd.hide();
					
			// 取得した一段目のすべての.ctl要素をループで回す		
			$(cTarget_2nd).each( function( index2, cond2 ){
							
				var id = $(cond2).attr('id');
				var flag = 0;
				
				// ドロップダウンをループで回す
				$.each( tCond_2nd, function( index, cond ){ // indexは「term」または「form」、condはaからzのアルファベット
													
					var target = $('#' + id).data(index + '-ctl');
													
					// 現在、親ループで回している.ctlのdata-visible-ctlにcondの値が含まれている場合（＝表示OK）はflagカウントを回す
					if( -1 != target.indexOf(cond) ) {
						flag++;
					}			
				});
			
				// すべてのselectに対してフラグがtrueである.ctlだけ表示
				if ( flag >= Object.keys(tCond_2nd).length ) {
					
					$(cond2).fadeIn();
					
					// カスタムフィールドのキーと数値/文字のselectのdisabledを解除
					if ( $(cond2).hasClass('cfkey') || $(cond2).hasClass('cfas') ) {
						$(cond2).prop("disabled", false);
					}
				
				} else {
					
					if ( $(cond2).hasClass('cfkey') || $(cond2).hasClass('cfas') ) {
						$(cond2).prop("disabled", true);
					}						
				}
			});
		});

	
	});

</script>
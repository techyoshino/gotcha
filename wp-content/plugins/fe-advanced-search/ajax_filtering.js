/**
 *
 * Ajax_filtering 1.0.10
 *
 */

function ajax_filtering_next( manag_no, item_no, elem_no, showcnt, term_depth ) {

	// ドロップダウンの親要素
	var parentObj = jQuery( '#ajax_filtering_' + manag_no + '_' + item_no );

	// 現在操作中のドロップダウン
	var current = event.target;

	// 現在操作中のドロップダウンの値
	var current_id = jQuery( current ).val();

	// 現在操作中の一つ前のドロップダウンの値
	var prev_id = jQuery( current ).prev().val();

	// 未選択時の文字列
	var dataFeasLabel = jQuery( parentObj ).data( 'feas-label' ).split( ',' );
	if( dataFeasLabel[ ( elem_no + 1 ) ] ) {
		labelTxt = dataFeasLabel[ ( elem_no + 1 ) ];
	} else {
		labelTxt = dataFeasLabel[dataFeasLabel.length-1];
	}

	/*
	 *
	 * 既存の子要素を一旦削除
	 *
	 */
	if ( 1 < term_depth ) { // 初期表示の階層指定がある場合

		var nextAll = jQuery( current ).nextAll( 'select' );
		//var selNo = elem_no + 1;
		// 子要素の階層
		var childDepth = elem_no + 1;

		nextAll.each( function ( index ) {
			jQuery( this ).empty();
			jQuery( this ).append( '<option id="feas_' + manag_no + '_' + item_no + '_' + childDepth + '_none" selected="selected" value="">' + ( dataFeasLabel[ childDepth ] ? dataFeasLabel[ childDepth ] : dataFeasLabel[dataFeasLabel.length-1] ) + '</option>' );
			childDepth++
		});

	} else { // 階層指定がない場合は全削除

		jQuery( current ).nextAll().remove();
	}

	// 値がある場合
	if ( ( current_id !== prev_id ) && ( current_id !== '' ) ) {

		/*
		 *
		 * 子要素を生成
		 *
		 */
		var target_sel = 'ajax' + '_' + item_no + '_' + ( elem_no + 1 );
		make_following_elements( manag_no, item_no, current_id, elem_no + 1, target_sel, labelTxt, showcnt, term_depth );
	}
}


function make_following_elements( manag_no, item_no, current_id, elem_no, target_sel, labelTxt, showcnt, term_depth ) {

	var div_id = jQuery( '#ajax_filtering_' + manag_no + '_' + item_no );

	( manag_no + '_' + item_no ).match( /(\d+)_/ );

	var get_durl = '#feas-searchform-' + RegExp.$1;
	var json_url = jQuery( get_durl ).attr( 'action' ); // initでフックされるURL
	var search_element_id = jQuery( '#ajax_filtering_' + manag_no + '_' + item_no ).attr( 'class' );

	json_url = json_url + '?parent=' + current_id;

	if ( ! elem_no ) { elem_no = 0; }

	json_url = json_url + '&manag_no=' + manag_no + '&item_no=' + item_no;

	if ( -1 === term_depth ) {
		div_id.append( '<span class="loading">読み込み...</span>' );
	}

	jQuery.getJSON( json_url, function( json ) {

		if ( json ) {

			if ( 1 < term_depth ) {

				var select_form;

				jQuery.each( json, function() {

					if ( '0' === this.count )  // 0件のタームは表示しない
						return true;

					if ( 'yes' === showcnt ) {
						select_form = '<option value="' + this.id + '">' + this.name + ' (' + this.count + ') </option>';
					} else {
						select_form = '<option value="' + this.id + '">' + this.name + '</option>';
					}

					jQuery( 'select.' + ( target_sel ) ).append( select_form );
				});

			} else {

				var select_form = '<select name="' + search_element_id + '[]" class="ajax_' + item_no + '_' + elem_no + '" onChange="ajax_filtering_next( ' + manag_no + ', ' + item_no + ', ' + elem_no + ', \'' + showcnt + '\', ' + term_depth + ' )">';
				select_form += '<option value="" selected>' + labelTxt + '</option>';

				jQuery.each( json, function() {
					if ( 'yes' == showcnt ) {
						select_form += '<option value="' + this.id + '">' + this.name + ' (' + this.count + ') </option>';
					} else {
						select_form += '<option value="' + this.id + '">' + this.name + '</option>';
					}
					jQuery( 'select.' + ( target_sel ) ).append( select_form );
				});

				select_form += '</select>';
				div_id.children( '.loading' ).remove();
				div_id.append( select_form );
			}

		} else {
			div_id.children( '.loading' ).remove();
		}
	});

	div_id.ajaxComplete( function() {
		if ( div_id.children().is( '.loading' ) ) {
			div_id.children( '.loading' ).remove();
			div_id.append( '<span>( 通信エラー )</span>' );
		}
	});
}

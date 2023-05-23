// Ajax_filtering 1.0.8

function ajax_filtering_next( manag_no, form_no, class_id, noselect_text, showcnt, term_depth ) {
	
	// 親タームのID
	var cild_id      = jQuery( '#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id + ' option:selected' ).val();	
	
	var class_length = ('ajax_' + form_no + '_').length;
	
	// 現在操作中のドロップダウンのclass末尾の数字
	var nest_id      = jQuery( '#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id ).attr( 'class' ).slice(class_length); // クラス名前半を除去、class数値を取り出す
	
	// 現在操作中のドロップダウンの一つ前のドロップダウンの値
	var prev_cild_id = jQuery( '#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id ).prev().val();
	
	// 最初のドロップダウンのclass末尾の数字
	var first_id     = jQuery( '#ajax_filtering_' + manag_no + '_' + form_no + ' select:first' ).attr( 'class' ).slice(class_length); // クラス名前半を除去、class数値を取り出す
	
	nest_id++;
	
	// 値がある場合
	if ( ( ! ( cild_id == prev_cild_id ) ) && ( ! ( cild_id == '' ) ) ) {
		
		// 1つめのドロップダウン and 値が「0」の場合
		if ( ( nest_id == first_id ) && ( cild_id == 0 ) ) {
	    	
	    	if ( 1 < term_depth ) {
		    	
		    	var nextAll = jQuery('#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id).nextAll('select');
				var selNo = class_id + 1;
		      
				nextAll.each(function () {
			        jQuery(this).empty();
			        jQuery(this).append('<option id="feas_' + manag_no + '_' + form_no + '_' + sel_no_ajax + '_' + selNo + '" selected="selected" value="">' + noselect_text + '</option>'); // ToDo: sel_no_ajax
				    selNo++
				});
				var target_sel = 'ajax' + '_' + form_no + '_' + (class_id + 1);
				make_following_elements( manag_no, form_no, cild_id, nest_id, target_sel, noselect_text, showcnt, term_depth );
			
			} else {
			
				var target_sel = 'ajax' + '_' + form_no + '_' + (class_id + 1);
				jQuery( '#ajax_filtering_' + class_id + ' select' ).remove();
				make_following_elements( manag_no, form_no, cild_id, nest_id, target_sel, noselect_text, showcnt, term_depth );
			}
						
		// それ以外
		} else {
						
	    	if ( 1 < term_depth ) {
		    					
				var nextAll = jQuery('#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id).nextAll('select');
				var selNo = class_id + 1;
				
				nextAll.each(function () {
					jQuery(this).empty();
					jQuery(this).append('<option id="feas_' + manag_no + '_' + form_no + '_' + selNo + '_none" selected="selected" value="">' + noselect_text + '</option>');
					selNo++
				});
			
				var target_sel = 'ajax' + '_' + form_no + '_' + (class_id + 1);
				make_following_elements( manag_no, form_no, cild_id, nest_id, target_sel, noselect_text, showcnt, term_depth );
			
			} else {			
				
				var target_sel = 'ajax' + '_' + form_no + '_' + (class_id + 1);
				jQuery( '#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id ).nextAll().remove();	
				make_following_elements( manag_no, form_no, cild_id, nest_id, target_sel, noselect_text, showcnt, term_depth );
			}
		}
		
	} 
	
	// 未選択
	else {
	    
	    if ( 1 < term_depth ) {
		    var nextAll = jQuery('#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id ).nextAll('select');
		    var selNo = class_id + 1;
	    	
			nextAll.each(function () {
		      jQuery(this).empty();
		      jQuery(this).append('<option id="feas_' + manag_no + '_' + form_no + '_' + selNo + '_none" selected="selected" value="">' + noselect_text + '</option>');
		      selNo++
		    })
	    
	    } else {
		    
		    jQuery( '#ajax_filtering_' + manag_no + '_' + form_no + ' select.ajax_' + form_no + '_' + class_id ).nextAll().remove();
	    }
	}
}

function make_following_elements( manag_no, form_no, cild_id, nest_id, target_sel, noselect_text, showcnt, term_depth ) {
	
	var div_id = jQuery( '#ajax_filtering_' + manag_no + '_' + form_no );
	
	( manag_no + '_' + form_no ).match( /(\d+)_/ );
	
	var get_durl = '#feas-searchform-' + RegExp.$1;
	var json_url = jQuery( get_durl ).attr( 'action' ); // initでフックされるURL
	var search_element_id = jQuery( '#ajax_filtering_' + manag_no + '_' + form_no ).attr( 'class' );
	
	json_url = json_url + '?parent=' + cild_id;
	
	if ( nest_id == null ) { nest_id = 0; }
	
	json_url = json_url + '&manag_no=' + manag_no + '&form_no=' + form_no;
	
	if ( -1 === term_depth ) {
		div_id.append( '<span class="loading">読み込み...</span>' );
	}
		
	jQuery.getJSON( json_url, function( json ) {
				
		if ( json ) {
					
			if ( 1 < term_depth ) {
				
				var select_form;
				
				jQuery.each(json,function(){
					
					if ( '0' === this.count )  // 0件のタームは表示しない
						return true;
					
					if ( 'yes' == showcnt ) {
						select_form = '<option value="' + this.id + '">' + this.name + ' (' + this.count + ') </option>';
					} else {
						select_form = '<option value="' + this.id + '">' + this.name + '</option>';
					}
					jQuery( 'select.' + ( target_sel ) ).append( select_form );
				});
					
			} else {

				var select_form = '<select name="' + search_element_id + '[]" class="ajax_' + form_no + '_' + nest_id + '" onChange="ajax_filtering_next( ' + manag_no + ', ' + form_no + ', ' + nest_id + ', \'' + noselect_text + '\', \'' + showcnt + '\', ' + term_depth + ' )">';
				select_form += '<option value="" selected>' + noselect_text + '</option>';
				
				jQuery.each(json,function(){
					if ( 'yes' == showcnt ) {
						select_form += '<option value="' + this.id + '">' + this.name + ' (' + this.count + ') </option>';
					} else {
						select_form += '<option value="' + this.id + '">' + this.name + '</option>';
					}
					jQuery('select.'+(target_sel)).append( select_form );
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

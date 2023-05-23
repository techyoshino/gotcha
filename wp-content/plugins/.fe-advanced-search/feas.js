/**
 *
 *
 * FE Advanced Search
 *
 *
 */

/**
 * iOSのセレクトボックスにおいて初期値(value="")のselectedをコントロールして「未選択時の文字列」を表示できるようにする
 */
jQuery('select[name^=search_element_]').change(function(){

	// 他の要素を選択したら初期値のselectedを解除
	if("" != jQuery(this).val()){
		jQuery(this).find('>:first-child').attr("selected", false);
	// 初期値を選択したらselected
	} else {
		jQuery(this).find('>:first-child').attr("selected", true);
	}
});

/**
 * フォームの選択を強制解除
 *
 * @param int targetFormId リセット対象のフォームID
 *
 */
function feas_clear_form(targetFormId){

	jQuery(function($){

		// すべてのform要素をクリア
		$('form#feas-searchform-' + targetFormId).each(function(){
			$(this).find('input,select,option,textarea').not(':button,:submit,:reset,input[type=hidden]')
			.each(function(){
				if('text' == $(this).attr('type')){
					$(this)
					.val('');
				}else if('checkbox' == $(this).attr('type')){
					$(this)
					.prop('checked',false)
					.removeAttr('checked');
				}else if('radio' == $(this).attr('type')){
					$(this)
					.prop('checked',false)
					.removeAttr('checked');
				}else{ // select
					$(this)
					.prop('selected',false)
					.removeAttr('selected');
					$(this).parent().find('li').each(function(){
						$(this).removeClass('active selected');
					});
				}
			});
		});
	});
}

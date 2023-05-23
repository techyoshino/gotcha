// FE Advanced Search
// iOSのセレクトボックスにおいて、初期値(value="")のselectedをコントロールして「未選択時の文字列」を表示できるようにする
jQuery('select[name^=search_element_]').change(function() {
	
	// 他の要素を選択したら初期値のselectedを解除
	if( "" != jQuery(this).val()) {
		jQuery(this).find('>:first-child').attr("selected", false);
	// 初期値を選択したらselected
	} else {
		jQuery(this).find('>:first-child').attr("selected", true);
	}
});

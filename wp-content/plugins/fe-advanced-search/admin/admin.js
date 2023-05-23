/**
 * コード自動選択
 */
function SelectText( element ) {
	window.setTimeout(
		function() { element.select(); },
		0
	);
}

/**
 * form送信時に「並び順」のダブりチェック
 */
function checkItemOrderRepeated(target) {

	let orderState = [];

	// 検索項目の行を取得（検索ボタンは除く）
	//let targetNode = jQuery('#sortItemsBody .alternate');
	let targetNode = jQuery(target);
	jQuery(targetNode).each( function(index) {
		let order = jQuery(this).find('.itemOrder').val(); //「並び順」の値を配列にまとめる
		 orderState.push(order);
	});

	// 重複をなくしたもの
	const filterdState = new Set(orderState);

	// 重複削除後と削除前でサイズが違うなら、同じ並びが存在する
	if (filterdState.size !== orderState.length) {
		alert( "並び順が同じものがあります。" );
		return false; // 「設定を保存」プロセスを中断
	}
	return true;
}

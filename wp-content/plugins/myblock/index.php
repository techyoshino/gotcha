
<?php
/**
 * Plugin Name: MyBlock
 */



	// ブロック用のスクリプトを登録
	wp_register_script(
		'my-block-script-stores',
		plugins_url( 'block-stores.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-editor' ),
		'1.0.0',
		true
	);

	// ブロック用のスクリプトを登録
	wp_register_script(
		'my-block-script-store',
		plugins_url( 'block-store.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-editor' ),
		'1.0.0',
		true
	);

	// CSSの読み込み割愛
	// 「my-block-style-editor」と「my-block-style-front」の読み込みが入ります。

	// ブロックの定義を登録
	register_block_type(
		'my-block/my-stores',
		array(
			'editor_script' => 'my-block-script-stores',
			'editor_style'  => 'my-block-style-editor',
			'style'         => 'my-block-style-front',
		)
	);

	// ブロックの定義を登録
	register_block_type(
		'my-block/my-store',
		array(
			'editor_script' => 'my-block-script-store',
			'editor_style'  => 'my-block-style-editor',
			'style'         => 'my-block-style-front',
		)
	);



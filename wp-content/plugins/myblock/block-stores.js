
( function( blocks, element, blockEditor ) {
	var el = element.createElement;
	var InnerBlocks = blockEditor.InnerBlocks;
	const ALLOWED_BLOCKS = [ 'my-block/my-store' ]

	blocks.registerBlockType( 'my-block/my-stores', {
			title: '店舗リスト',
			category: 'layout',

			edit: function( props ) {
					return el(
							'div',
							{
								className: props.className,
							},
							el( InnerBlocks,
								{
									allowedBlocks: ALLOWED_BLOCKS,
									templateLock: false,
								}
							)
					);
			},

			save: function( props ) {
					return el(
							'div',
							{ className: props.className },
							el( InnerBlocks.Content )
					);
			},
	} );
} (
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
) );
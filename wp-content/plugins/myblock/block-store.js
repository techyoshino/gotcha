

( function( blocks, editor, element, blockEditor ) {
	var el = element.createElement;
	var InnerBlocks = blockEditor.InnerBlocks;
	var RichTextTitle = editor.RichText;
	var RichTextContent = editor.RichText;

	const MY_STORE_IMAGE = [
    [ 'core/image', {} ],
	];

	blocks.registerBlockType( 'my-block/my-store', {
			title: '店舗情報',
			category: 'layout',
			attributes: {
				title: {
					type: 'array',
					source: 'children',
					selector: '.store-title',
				},
				content: {
					type: 'array',
					source: 'children',
					selector: '.store-content',
				},
			},
			edit: function( props ) {
				var title = props.attributes.title;
				var content = props.attributes.content;

		    function onChangeTitle( newTitle ) {
			    props.setAttributes( { title: newTitle } );
				}

		    function onChangeContent( newContent ) {
			    props.setAttributes( { content: newContent } );
				}

				return el(
					'div',
					{ className: 'store' },
					el( 'div',
						{ className: 'store-img' },
						el( InnerBlocks,
							{
								template: MY_STORE_IMAGE,
								templateLock: "all",
							} )
					),
					el( 'div',
						{ className: 'store-body' },
						el( RichTextTitle, {
							tagName: 'div',
							className: 'store-title',
							placeholder: '店舗タイトル',
							onChange: onChangeTitle,
							value: title,
						} ),
						el( RichTextContent, {
							tagName: 'p',
							className: 'store-content',
							placeholder: '店舗紹介文',
							onChange: onChangeContent,
							value: content,
						} ),
					)
				)
			},

			save: function( props ) {

					return el(
						'div',
						{
							className: 'store',
						},
							el( 'div',
								{
									className: 'store-img'
								},
								el( InnerBlocks.Content )
							),
							el( 'div',
								{
									className: 'store-body',
									value: props.attributes.body
								},
								el( RichTextTitle.Content, {
									tagName: 'div',
									className: 'store-title',
									value: props.attributes.title,
								} ),
								el( RichTextContent.Content, {
									tagName: 'p',
									className: 'store-content',
									value: props.attributes.content,
								} ),
							)
					);
			},
	} );
} (
	window.wp.blocks,
	window.wp.editor,
	window.wp.element,
	window.wp.blockEditor,
) );

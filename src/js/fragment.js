const {
	i18n: { __ },
	element: { createElement: e },
	richText: { registerFormatType, toggleFormat },
	blockEditor: { RichTextToolbarButton },
} = window.wp;

registerFormatType( 'slide/fragment', {
	title: __( 'Slide Fragment', 'wbvb-presentation' ),
	tagName: 'span',
	className: 'fragment',
	edit: ( { value, onChange } ) =>
		e( RichTextToolbarButton, {
			icon: 'editor-textcolor',
			title: __( 'Slide Fragment', 'wbvb-presentation' ),
			onClick: () => {
				onChange( toggleFormat( value, { type: 'slide/fragment' } ) );
			},
		} ),
} );

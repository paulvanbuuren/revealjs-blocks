const {
	i18n: { __ },
	element: { createElement: e },
	components: { SelectControl },
} = window.wp;

export default function FontWeightPicker( { fontFamily, ...props } ) {
	const options = [
		{ value: '100', label: __( 'Thin', 'wbvb-presentation' ) },
		{ value: '200', label: __( 'Extra Light', 'wbvb-presentation' ) },
		{ value: '300', label: __( 'Light', 'wbvb-presentation' ) },
		{ value: '400', label: __( 'Normal', 'wbvb-presentation' ) },
		{ value: '500', label: __( 'Medium', 'wbvb-presentation' ) },
		{ value: '600', label: __( 'Semi Bold', 'wbvb-presentation' ) },
		{ value: '700', label: __( 'Bold', 'wbvb-presentation' ) },
		{ value: '800', label: __( 'Extra Bold', 'wbvb-presentation' ) },
		{ value: '900', label: __( 'Black', 'wbvb-presentation' ) },
	];

	const weights = new Set();

	if ( document.fonts && document.fonts.forEach ) {
		document.fonts.forEach( ( font ) => {
			if ( font.family !== fontFamily ) {
				return;
			}

			weights.add( font.weight );
		} );
	}

	if ( weights.size ) {
		options.forEach( ( option ) => {
			if ( weights.has( option.value ) ) {
				return;
			}

			option.disabled = true;
		} );
	}

	return e( SelectControl, {
		...props,
		options,
	} );
}

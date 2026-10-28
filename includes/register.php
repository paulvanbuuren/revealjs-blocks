<?php
/**
 * Register prsentations post type.
 *
 * @package slide
 */

$presentation_labels = [
	'name'                  => _x( 'Presentations', 'Post type general name', 'wbvb-presentation' ),
	'singular_name'         => _x( 'Presentation', 'Post type singular name', 'wbvb-presentation' ),
	'menu_name'             => _x( 'Presentations', 'Admin Menu text', 'wbvb-presentation' ),
	'name_admin_bar'        => _x( 'Presentation', 'Add New on Toolbar', 'wbvb-presentation' ),
	'add_new'               => __( 'Add New', 'wbvb-presentation' ),
	'add_new_item'          => __( 'Add New Presentation', 'wbvb-presentation' ),
	'new_item'              => __( 'New Presentation', 'wbvb-presentation' ),
	'edit_item'             => __( 'Edit Presentation', 'wbvb-presentation' ),
	'view_item'             => __( 'View Presentation', 'wbvb-presentation' ),
	'all_items'             => __( 'All Presentations', 'wbvb-presentation' ),
	'search_items'          => __( 'Search Presentations', 'wbvb-presentation' ),
	'parent_item_colon'     => __( 'Parent Presentations:', 'wbvb-presentation' ),
	'not_found'             => __( 'No presentations found.', 'wbvb-presentation' ),
	'not_found_in_trash'    => __( 'No presentations found in Trash.', 'wbvb-presentation' ),
	'featured_image'        => _x( 'Presentation Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'wbvb-presentation' ),
	'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'wbvb-presentation' ),
	'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'wbvb-presentation' ),
	'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'wbvb-presentation' ),
	'archives'              => _x( 'Presentation archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'wbvb-presentation' ),
	'insert_into_item'      => _x( 'Insert into presentation', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'wbvb-presentation' ),
	'uploaded_to_this_item' => _x( 'Uploaded to this presentation', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'wbvb-presentation' ),
	'filter_items_list'     => _x( 'Filter presentations list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'wbvb-presentation' ),
	'items_list_navigation' => _x( 'Presentations list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'wbvb-presentation' ),
	'items_list'            => _x( 'Presentations list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'wbvb-presentation' ),
];

register_post_type(
	'presentation',
	array(
		'labels'             => $presentation_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-media-interactive',
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'wbvb-presentation' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'revisions',
		),
		'show_in_rest'       => true,
	)
);

$presentation_meta_keys = [
	'css',
	'color',
	'background-color',
	'background-gradient',
	'background-url',
	'background-id',
	'background-position',
	'background-opacity',
	'font-size',
	'font-family',
	'font-family-url',
	'font-family-heading',
	'font-family-heading-url',
	'font-weight-heading',
	'transition',
	'background-transition',
	'transition-speed',
	'controls',
	'progress',
	'width',
	'horizontal-padding',
	'vertical-padding',
	'color-palette',
	'contain',
];

foreach ( $presentation_meta_keys as $presentation_meta_key ) {
	register_post_meta(
		'wbvb-presentation',
		"presentation-$presentation_meta_key",
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		)
	);
}

<?php
/*
 * Plugin Name: WBVB - Presentation Block
 * Plugin URI:  https://github.com/paulvanbuuren/revealjs-blocks
 * Description: Creates a custom post type 'presentation', which will render as a presentation on the front end using Reveal.js.
 * Version:     1.0.1
 * Author:      Paul van Buuren
 * Author URI:  https://wbvb.nl/
 * License:     GPL-2.0-or-later
 * License URI  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: wbvb-presentation
 * Domain Path: /languages
 */

namespace presentation;

define( 'PRESENTATION_VER', '1.0.3' );

/**
 * Add scripts and style for presentation post type on admin.
 */
function presentation_admin_enqueue_scripts() {
	if ( get_post_type() !== 'presentation' ) {
		return;
	}

	wp_enqueue_code_editor( array( 'type' => 'text/css' ) );

	wp_enqueue_script(
		'wbvb-presentation',
		plugins_url( 'build/js/index.js', __FILE__ ),
		array(
			'wp-element',
			'wp-i18n',
			'wp-blocks',
			'wp-rich-text',
			'wp-plugins',
			'wp-edit-post',
			'wp-data',
			'wp-components',
			'wp-block-editor',
			'wp-url',
			'wp-compose',
			'wp-hooks',
		),
		filemtime( dirname( __FILE__ ) . '/build/js/index.js' ),
		true
	);

	wp_enqueue_style(
		'wbvb-presentation',
		plugins_url( 'index.css', __FILE__ ),
		array(),
		filemtime( dirname( __FILE__ ) . '/index.css' )
	);

	wp_deregister_style( 'wp-block-library-theme' );
	wp_register_style(
		'wp-block-library-theme',
		plugins_url( 'common.css', __FILE__ ),
		array(),
		filemtime( dirname( __FILE__ ) . '/common.css' )
	);

	global $wp_styles;

	$template_directory_uri = get_template_directory_uri();

	foreach ( $wp_styles->queue as $handle ) {
		$info = $wp_styles->registered[ $handle ];

		if ( substr( $info->src, 0, strlen( $template_directory_uri ) ) === $template_directory_uri ) {
			wp_dequeue_style( $handle );
		}
	}
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\presentation_admin_enqueue_scripts', 99999 );

// Register presentation custom post type.
add_action( 'init',
	function () {
		require 'includes/register.php';
	}
);

register_activation_hook(
	__FILE__,
	function () {
		require 'includes/register.php';
		flush_rewrite_rules();
	}
);

/**
 * Use special template for presentation post type.
 *
 * @param string $path the template path.
 */
function presentation_template_include( $path ) {
	if ( ! is_singular( 'wbvb-presentation' ) ) {
		return $path;
	}

	if ( isset( $_GET['speaker'] ) ) { // phpcs:ignore.
		return dirname( __FILE__ ) . '/speaker.php';
	}

	the_post();

	return dirname( __FILE__ ) . '/template.php';
}

add_filter( 'template_include', __NAMESPACE__ . '\presentation_template_include' );

/**
 * Add scripts and style for presentation post type.
 * Dequeue the theme style. It is not needed for the presentations, as they are individually crafted.
 */
function presentation_frontend_enqueue_scripts() {
	if ( ! is_singular( 'wbvb-presentation' ) ) {
		return;
	}

	global $wp_styles;

	$template_directory_uri = get_template_directory_uri();

	foreach ( $wp_styles->queue as $handle ) {
		$info = $wp_styles->registered[ $handle ];

		if ( substr( $info->src, 0, strlen( $template_directory_uri ) ) === $template_directory_uri ) {
			wp_dequeue_style( $handle );
		}
	}

	if ( isset( $_GET['speaker'] ) ) { // phpcs:ignore.
		wp_enqueue_script(
			'slide-speaker',
			plugins_url( 'build/js/speaker.js', __FILE__ ),
			array(),
			filemtime( dirname( __FILE__ ) . '/build/js/speaker.js' ),
			true
		);

		wp_enqueue_style(
			'slide-speaker',
			plugins_url( 'speaker.css', __FILE__ ),
			array(),
			filemtime( dirname( __FILE__ ) . '/speaker.css' )
		);

		return;
	}

	wp_enqueue_script(
		'slide-reveal',
		plugins_url( 'assets/revealjs/reveal.js', __FILE__ ),
		array(),
		'4.3.1',
		true
	);

	wp_enqueue_script(
		'slide-reveal-notes',
		plugins_url( 'assets/js/notes.min.js', __FILE__ ),
		array( 'slide-reveal' ),
		filemtime( dirname( __FILE__ ) . '/assets/js/notes.min.js' ),
		true
	);

	wp_enqueue_script(
		'slide-template',
		plugins_url( 'build/js/template.js', __FILE__ ),
		array( 'slide-reveal', 'slide-reveal-notes', 'wp-i18n' ),
		filemtime( dirname( __FILE__ ) . '/build/js/template.js' ),
		true
	);

	$post_id = get_the_ID();
	$contain = (bool) get_post_meta( $post_id, 'presentation-contain', true ) ? get_post_meta( $post_id, 'presentation-contain', true ) : false;

	wp_localize_script(
		'slide-template',
		'slideTemplate',
		array(
			'revealSettings' => array(
				'transition'           => get_post_meta( $post_id, 'presentation-transition', true ) ? get_post_meta( $post_id, 'presentation-transition', true ) : 'none',
				'backgroundTransition' => get_post_meta( $post_id, 'presentation-background-transition', true ) ? get_post_meta( $post_id, 'presentation-background-transition', true ) : 'none',
				'transitionSpeed'      => get_post_meta( $post_id, 'presentation-transition-speed', true ) ? get_post_meta( $post_id, 'presentation-transition-speed', true ) : 'default',
				'controls'             => (bool) get_post_meta( $post_id, 'presentation-controls', true ) ? get_post_meta( $post_id, 'presentation-controls', true ) : false,
				'progress'             => (bool) get_post_meta( $post_id, 'presentation-progress', true ) ? get_post_meta( $post_id, 'presentation-progress', true ) : false,
				'hash'                 => true,
				'history'              => true,
				'preloadIframes'       => true,
				'hideAddressBar'       => true,
				'height'               => 720,
				'width'                => (int) get_post_meta( $post_id, 'presentation-width', true ) ? get_post_meta( $post_id, 'presentation-width', true ) : 960,
				'margin'               => $contain ? 0 : 0.08,
				'keyboard'             => array(
					'38' => 'prev',
					'40' => 'next',
				),
				'overview'             => false,
				// We center in CSS.
				'center'               => false,
				'pdfMaxPagesPerSlide'  => 1,
			),
			'contain'        => $contain,
		)
	);

	wp_enqueue_style(
		'slide-reveal',
		plugins_url( 'assets/css/reveal.min.css', __FILE__ ),
		array(),
		'4.3.1'
	);

	if ( isset( $_GET['print-pdf'] ) ) { // phpcs:ignore.
		wp_enqueue_style(
			'slide-reveal-pdf',
			plugins_url( 'assets/css/pdf.min.css', __FILE__ ),
			array(),
			'3.8.0'
		);
	}

	$font_url = get_post_meta( get_the_ID(), 'presentation-font-family-url', true );

	if ( $font_url ) {
		wp_enqueue_style(
			'slide-default-font',
			$font_url,
			array(),
			PRESENTATION_VER
		);
	}

	$heading_font_url = get_post_meta( get_the_ID(), 'presentation-font-family-heading-url', true );

	if ( $heading_font_url ) {
		wp_enqueue_style(
			'slide-heading-font',
			$heading_font_url,
			array(),
			PRESENTATION_VER
		);
	}

	wp_deregister_style( 'wp-block-library-theme' );
	wp_enqueue_style(
		'slide-common',
		plugins_url( 'common.css', __FILE__ ),
		array(),
		filemtime( dirname( __FILE__ ) . '/common.css' )
	);
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\presentation_frontend_enqueue_scripts', 99999 );

/**
 * Update editor settings on presentation post type.
 */
function presentation_editor_settings() {
	if ( 'wbvb-presentation' !== get_current_screen()->post_type ) {
		return;
	}

	remove_editor_styles();
	remove_theme_support( 'editor-color-palette' );
	remove_theme_support( 'editor-font-sizes' );
	add_theme_support( 'align-wide' );

	if ( ! isset( $_GET['post'] ) ) {  // phpcs:ignore.
		return;
	}

	$post = get_post( $_GET['post'] ); // phpcs:ignore.

	if ( ! $post ) {
		return;
	}

	$palette = get_post_meta( $post->ID, 'presentation-color-palette', true );

	if ( ! $palette ) {
		return;
	}

	$palette = explode( ',', $palette );
	$palette = array_map( 'trim', $palette );
	$palette = array_map( 'sanitize_hex_color', $palette );
	$palette = array_map(
		function ( $hex ) {
			return array( 'color' => $hex );
		},
		$palette
	);

	if ( count( $palette ) ) {
		add_theme_support( 'editor-color-palette', $palette );
	}
}

foreach ( array( 'load-post.php', 'load-post-new.php' ) as $presentation_screen ) {
	add_action( $presentation_screen, __NAMESPACE__ . '\presentation_editor_settings', 99999 );
}

/**
 * Prepare styles array for block settings.
 *
 * @param array $settings the setting data.
 */
function presentation_block_editor_settings( $settings ) {
	if ( 'presentation' !== get_current_screen()->post_type ) {
		return $settings;
	}

	$settings['styles'] = array();

	return $settings;
}

add_filter( 'block_editor_settings', __NAMESPACE__ . '\presentation_block_editor_settings', 99999 );

/**
 * Add default template for presentation type.
 *
 * @param string $post_content the content of post type.
 * @param object $post the post data.
 */
function presentation_add_default_content( $post_content, $post ) {
	if ( 'presentation' !== $post->post_type ) {
		return $post_content;
	}

	return file_get_contents( __DIR__ . '/default-content.html' ); // phpcs:ignore.
}

add_filter( 'default_content', __NAMESPACE__ . '\presentation_add_default_content', 10, 2 );

/**
 * Update slide block content for presentation.
 *
 * @param string $block_content the content of block.
 * @param object $block the block data.
 */
function presentation_render_block( $block_content, $block ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $block_content;
	}

	if ( 'slide/slide' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( empty( $block['attrs']['notes'] ) ) {
		return $block_content;
	}

	$pos   = strrpos( $block_content, '</section>', - 1 );
	$notes = '<aside class="notes">' . $block['attrs']['notes'] . '</aside>';

	return substr_replace( $block_content, $notes, $pos, 0 );
}

add_filter( 'render_block', __NAMESPACE__ . '\presentation_render_block', 10, 2 );

/**
 * Only allow formanting and media blocks on presentation cpt.
 *
 * @param array $allowed_block_types the list of blocks.
 * @param object $editor_context the editor context data.
 */
function presentation_allowed_block_types( $allowed_block_types, $editor_context ) {
	if ( ! empty( $editor_context->post ) ) {
		if ( 'presentation' === $editor_context->post->post_type ) {
			return array(
				'core/heading',
				'core/paragraph',
				'core/list',
				'core/list-item',
				'core/quote',
				'core/image',
				'core/media-text',
				'core/group',
				'slide/slide',
			);
		}
	}

	return $allowed_block_types;
}

add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\presentation_allowed_block_types', 10, 2 );

/**
 * Registers the block pattern categories.
 */
function presentation_register_block_pattern_categories() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category( 'wbvb-presentation', array( 'label' => esc_html_x( 'Slide', 'Slide patterns category', 'wbvb-presentation' ) ) );
	}
}

add_action( 'init', __NAMESPACE__ . '\presentation_register_block_pattern_categories' );

/**
 * Registers the block patterns.
 */
function presentation_register_block_patterns() {

	$files = array(
		'home.php'
	);

	$path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'patterns/';

	foreach ( $files as $file ) {
		if ( file_exists( $path . $file ) ) {
			require_once $path . $file;
		}
	}
}

add_action( 'init', __NAMESPACE__ . '\presentation_register_block_patterns', 10, 1 );

/**
 * Remove core patterns on presentation.
 */
function remove_block_patterns_support() {
	remove_theme_support( 'core-block-patterns', array( 'wbvb-presentation' ) );
}

add_action( 'init', __NAMESPACE__ . '\remove_block_patterns_support' );



function wbvb_pres_load_textdomain() {
	load_plugin_textdomain(
		'wbvb-presentation',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);

//	echo '<h1>Le poepie kakkie ' . _x( 'Presentations', 'Post type general name', 'wbvb-presentation' ) . '</h1>';

}

add_action( 'init',__NAMESPACE__ . '\wbvb_pres_load_textdomain' );

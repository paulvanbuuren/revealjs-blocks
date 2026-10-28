<?php
/**
 * Home block patterns.
 *
 * @package slide
 */

register_block_pattern(
	'slide/home',
	array(
		'title'      => __( 'Home slide', 'wbvb-presentation' ),
		'categories' => array( 'wbvb-presentation' ),
		'content'    => '<!-- wp:group {"templateLock":"all","lock":{"move":true,"remove":true},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group"><!-- wp:paragraph -->
		<p>Lorem Ipsum</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading -->
		<h2 class="wp-block-heading">Lorem ipsum dolor sit amet</h2>
		<!-- /wp:heading --></div>
		<!-- /wp:group -->',
	)
);

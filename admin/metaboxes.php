<?php 


function gridgets_metaboxes() {
	
	// Get all post types 
	$args = array(
		'public'   => true
	);
	$post_types = get_post_types ( $args, 'names' );
	$exclude = array ( 'attachment' );
	$available = array_diff ( $post_types, $exclude );
	
	$screens = array( 'page' );
	
	$metaboxes = array (
		array (
			'ID'			=> 'gridgets_widget_area_before_content',
			'title'			=> __( 'Widget Areas Before Content', 'super-hero-slider' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> $screens,
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> array (
				array (
					'ID'		=> 'gridgets_widget_before_content',
					'name'		=> 'gridgets_widget_before_content',
					'title'		=> __( 'Add Widget Area', 'gridgets' ),
					'type'		=> 'add_widget_area',
					'class'		=> 'gridgets-add-area'
				),
				array (
					'ID'		=> 'gridgets_hide_page_title',
					'name'		=> 'gridgets_hide_page_title',
					'title'		=> __( 'Hide Page Title', 'super-hero-slider' ),
					'type'		=> 'checkbox',
					'default'	=> 0,
					'description'	=> __( 'Will filter <code>the_title</code> and return null', 'gridgets' )
				),
			/*	array (
					'ID'		=> 'gridgets_widget_before_content_layout',
					'name'		=> 'gridgets_widget_before_content_layout',
					'title'		=> __( 'Layout', 'gridgets' ),
					'type'		=> 'select',
					'options'	=> array(
						'1-col'		=> '1 column',
						'2-cols'	=> '2 columns',
						'3-cols'	=> '3 columns',
						'4-cols'	=> '4 columns',
					),
					'class'		=> 'gridgets-dependent'
				), */
			)
		),
		
	);

	return $metaboxes;
	
}
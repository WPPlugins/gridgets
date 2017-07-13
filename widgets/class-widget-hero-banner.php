<?php
/**
 * Widget API: Widget_Hero_Banner class
 *
 * @package Gridgets
 * @since 0.1.0
 */

/**
 * Class used to implement the widget.
 *
 * @see WP_Widget
 */
class Widget_Hero_Banner extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		
		$widget_ops = array(
			'classname' => 'hero_banner',
			'description' => __( 'Display an image with text.', 'gridgets' ),
		//	'customize_selective_refresh' => true,
		);
		$control_ops = array();
		parent::__construct( 'hero_banner', __( 'Hero Banner' ), $widget_ops, $control_ops );
		
		// Add media upload scripts
		add_action ( 'admin_enqueue_scripts', array ( $this, 'upload_scripts' ) );
	}
	
	public function upload_scripts() {
		wp_enqueue_media();
        wp_enqueue_script ( 'upload_media_widget',  GRIDGETS_PLUGIN_URL . 'assets/js/upload-media.js', array ( 'jquery' ) );
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		$output = '';

		$title 			= ( ! empty ( $instance['title'] ) ) ? $instance['title'] : '';
		$heading_style	= ( ! empty ( $instance['heading_style'] ) ) ? $instance['heading_style'] : 'h2';
		$sub 			= ( ! empty ( $instance['sub'] ) ) ? $instance['sub'] : '';
		$sub_style 		= ( ! empty ( $instance['sub_style'] ) ) ? $instance['sub_style'] : 'h3';
		$button			= ( ! empty ( $instance['button'] ) ) ? $instance['button'] : '';
		$button_class	= ( ! empty ( $instance['button_class'] ) ) ? $instance['button_class'] : '';
		$button_url		= ( ! empty ( $instance['button_url'] ) ) ? $instance['button_url'] : '';
		$image 			= ( ! empty ( $instance['image_uri'] ) ) ? $instance['image_uri'] : '';
		
		/**
		 * Filter the arguments for the widget.
		 *
		 */

		echo $args['before_widget']; ?>

			<div class="ctbwp-banner-wrap">
				<?php if ( $image ) { ?>
					<img src="<?php echo esc_url ( $image ); ?>" alt="" >
				<?php } ?>
				<div class="ctbwp-banner-content">
					<?php if ( $title ) {
						echo '<' . $heading_style . '>' . sanitize_text_field ( $title ) . '</' . $heading_style . '>';
					} ?>
					<?php if ( $sub ) {
						echo '<' . $sub_style . '>' . sanitize_text_field ( $sub ) . '</' . $sub_style . '>';
					} ?>
					<?php if ( $button && $button_url ) { ?>
						<a href="<?php echo esc_url ( $button_url ); ?>" class="<?php echo esc_attr ( str_replace ( '.', '', $button_class ) ); ?>"><?php echo sanitize_text_field ( $button ); ?></a>
					<?php } ?>
				</div><!-- ctbwp-banner-content -->
				
			</div><!-- .ctbwp-banner-wrap -->

		<?php echo $args['after_widget'];

	}

	/**
	 * Handles updating settings for the current Recent Comments widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= sanitize_text_field ( $new_instance['title'] );
		$instance['heading_style'] 	= sanitize_text_field ( $new_instance['heading_style'] );
		$instance['sub'] 			= sanitize_text_field ( $new_instance['sub'] );
		$instance['sub_style'] 		= sanitize_text_field ( $new_instance['sub_style'] );
		$instance['button'] 		= sanitize_text_field ( $new_instance['button'] );
		$instance['button_class'] 	= sanitize_text_field ( $new_instance['button_class'] );
		$instance['button_url'] 	= sanitize_text_field ( $new_instance['button_url'] );
		$instance['image_uri'] 		= ( $new_instance['image_uri'] );
		return $instance;
	}

	/**
	 * Outputs the settings form for the Recent Comments widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title			= isset( $instance['title'] ) ? $instance['title'] : '';
		$heading_style	= isset( $instance['heading_style'] ) ? $instance['heading_style'] : 'h2';
		$sub 			= isset( $instance['sub'] ) ? $instance['sub'] : '';
		$sub_style 		= isset( $instance['sub_style'] ) ? $instance['sub_style'] : 'h3';
		$button			= isset( $instance['button'] ) ? $instance['button'] : '';
		$button_class	= isset( $instance['button_class'] ) ? $instance['button_class'] : '';
		$button_url		= isset( $instance['button_url'] ) ? $instance['button_url'] : '';
		$image 			= isset( $instance['image_uri'] ) ? $instance['image_uri'] : '';
		?>
		
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
		$title = sanitize_text_field( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:' ); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea></p>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>

		<p>
			<label for="<?php echo $this->get_field_id('image_uri'); ?>">Image</label>
			<div id="<?php echo $this->get_field_id('image_uri'); ?>-img">
			<?php
				if ( $image != '' ) { ?>
					<img class="custom_media_image" src="<?php echo $image; ?>" style="margin:0;padding:0;max-height:100px;float:none;" />
				<?php }
			?>
			</div>
			<input type="hidden" class="widefat custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo $image; ?>" style="margin-top:5px;">
		</p>
		<p style="clear: left;">
			<input type="button" class="button button-secondary custom_media_button" id="<?php echo $this->get_field_id('image_uri'); ?>_button" name="<?php echo $this->get_field_name('image_uri'); ?>" value="Upload Image" style="margin-top:5px;" />
		</p>
    


		<?php
	}

	/**
	 * Flushes the Recent Comments widget cache.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @deprecated 4.4.0 Fragment caching was removed in favor of split queries.
	 */
	public function flush_widget_cache() {
		_deprecated_function( __METHOD__, '4.4' );
	}
}

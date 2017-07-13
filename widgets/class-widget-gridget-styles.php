<?php
/**
 * Widget API: Widget_Gridget_Styles class
 *
 */

/**
 * Core class used to implement a Sidebar Styles widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class Widget_Gridget_Styles extends WP_Widget {

	/**
	 * Sets up a new Gridget Styles widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_gridget_styles',
			'description' => __( 'Set the sidebar styles.', 'gridgets' ),
		//	'customize_selective_refresh' => true,
		);
		// $control_ops = array( 'width' => 400, 'height' => 350 );
		$control_ops = array();
		parent::__construct( 'gridget_styles', __( 'Gridget Styles' ), $widget_ops, $control_ops );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'print_scripts' ), 9999 );
	}

	/**
	 * This widget doesn't display any content
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Text widget instance.
	 */
	public function widget( $args, $instance ) {

	}

	/**
	 * Handles updating settings for the current Sidebar Styles widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['bgcolor'] 			= sanitize_text_field( $new_instance['bgcolor'] );
		$instance['image_id'] 			= absint( $new_instance['image_id'] );
		$instance['layout'] 			= sanitize_text_field( $new_instance['layout'] );
		$instance['padding'] 			= sanitize_text_field( $new_instance['padding'] );
		$instance['margin'] 			= sanitize_text_field( $new_instance['margin'] );
		$instance['class'] 				= sanitize_text_field( $new_instance['class'] );
		$instance['full_width']			= ! empty( $new_instance['full_width'] );
		$instance['bordertopcolor'] 	= sanitize_text_field( $new_instance['bordertopcolor'] );
		$instance['bordertopwidth'] 	= absint( $new_instance['bordertopwidth'] );
		$instance['borderbotcolor'] 	= sanitize_text_field( $new_instance['borderbotcolor'] );
		$instance['borderbotwidth'] 	= absint( $new_instance['borderbotwidth'] );
		
		return $instance;
	}

	/**
	 * Outputs the Sidebar Styles widget settings form.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance			= wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$layout				= isset( $instance['layout'] ) ? $instance['layout'] : '1-col';
		$bgcolor			= isset( $instance['bgcolor'] ) ? sanitize_text_field( $instance['bgcolor'] ) : '';
		$image				= isset( $instance['image_id'] ) ? absint( $instance['image_id'] ) : '';
		$padding			= isset( $instance['padding'] ) ? sanitize_text_field( $instance['padding'] ) : '';
		$margin				= isset( $instance['margin'] ) ? sanitize_text_field( $instance['margin'] ) : '';
		$class				= isset( $instance['class'] ) ? sanitize_text_field( $instance['class'] ) : '';
		$full_width 		= isset( $instance['full_width'] ) ? $instance['full_width'] : 0;
		$bordertopcolor		= isset( $instance['bordertopcolor'] ) ? sanitize_text_field( $instance['bordertopcolor'] ) : '';
		$bordertopwidth		= isset( $instance['bordertopwidth'] ) ? sanitize_text_field( $instance['bordertopwidth'] ) : '';
		$borderbotcolor		= isset( $instance['borderbotcolor'] ) ? sanitize_text_field( $instance['borderbotcolor'] ) : '';
		$borderbotwidth		= isset( $instance['borderbotwidth'] ) ? sanitize_text_field( $instance['borderbotwidth'] ) : '';
		?>
		
		<p><input id="<?php echo $this->get_field_id('full_width'); ?>" name="<?php echo $this->get_field_name('full_width'); ?>" type="checkbox"<?php checked( $full_width ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('full_width'); ?>"><?php _e('Full Width', 'gridgets' ); ?></label></p>
		
		<p>
			<label class="gridget-label" for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php _e( 'Layout', 'gridgets' ); ?></label>
			<?php $options = array(
				'1-col'						=> 'Single column',
				'2-cols'					=> 'Equal halves',
				'3-cols'					=> 'Equal thirds',
				'4-cols'					=> 'Equal fourths',
				'5-cols'					=> 'Equal fifths',
				'6-cols'					=> 'Equal sixths',
				'two-thirds-one-third'		=> 'Two thirds | One third',
				'one-third-two-thirds'		=> 'One third | Two thirds',
				'three-fourths-one-fourth'	=> 'Three fourths | One fourth',
				'one-fourth-three-fourths'	=> 'One fourth | Three fourths',
				'four-fifths-one-fifth'		=> 'Four fifths | One fifth',
				'one-fifth-four-fifths'		=> 'One fifth | Four fifths',
				'five-sixths-one-sixth'		=> 'Five sixths | One sixth',
				'one-sixth-five-sixths'		=> 'One sixth | Five sixths'
			); ?>
			<select id="<?php echo $this->get_field_id( 'layout' ); ?>" name="<?php echo $this->get_field_name( 'layout' ); ?>">
				<?php foreach ( $options as $key => $value ) : ?>
				
					<option value="<?php echo $key; ?>" <?php selected( $layout, $key ); ?>>
						<?php echo esc_html( $value ); ?>
					</option>
					
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label class="gridget-label" for="<?php echo $this->get_field_id( 'bgcolor' ); ?>"><?php _e( 'Background Color:', 'gridgets' ); ?></label>
			<input type="text" class="wp-color-picker" id="<?php echo $this->get_field_id('bgcolor'); ?>" name="<?php echo $this->get_field_name('bgcolor'); ?>" value="<?php echo esc_attr( $bgcolor ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('image_id'); ?>"><?php _e( 'Background Image:', 'gridgets' ); ?></label>
			<span id="<?php echo $this->get_field_id('image_id'); ?>-img">
			<?php
				if ( $image != '' ) { ?>
					<img class="custom_media_image" src="<?php echo wp_get_attachment_url( absint( $image ) ); ?>" style="margin:0;padding:0;max-height:100px;float:none;" />
				<?php }
			?>
			</span>
			<input type="hidden" class="widefat custom_media_id" name="<?php echo $this->get_field_name('image_id'); ?>" id="<?php echo $this->get_field_id('image_id'); ?>" value="<?php echo $image; ?>" style="margin-top:5px;">
		
			<span style="display: block; clear: left;">
				<input type="button" class="button button-secondary custom_media_button" id="<?php echo $this->get_field_id('image_id'); ?>_button" name="<?php echo $this->get_field_name('image_id'); ?>" value="Upload Image" style="margin-top:5px;" />
				<?php
					if ( $image != '' ) { ?>
						<input type="button" class="button button-secondary remove_custom_media_button" id="<?php echo $this->get_field_id('image_id'); ?>_remove_button" name="<?php echo $this->get_field_name('image_id'); ?>_remove_button" value="Remove Image" style="margin-top:5px;" />
				<?php } ?>
			</span>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'padding' ); ?>"><?php _e( 'Padding:', 'gridgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('padding'); ?>" name="<?php echo $this->get_field_name('padding'); ?>" value="<?php echo esc_attr( $padding ); ?>" placeholder="top,right,bottom,left">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'margin' ); ?>"><?php _e( 'Margin:', 'gridgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('margin'); ?>" name="<?php echo $this->get_field_name('margin'); ?>" value="<?php echo esc_attr( $margin ); ?>" placeholder="top,right,bottom,left">
		</p>
		
		<p>
			<label class="gridget-label" for="<?php echo $this->get_field_id( 'bordertopcolor' ); ?>"><?php _e( 'Border Top Color:', 'gridgets' ); ?></label>
			<input type="text" class="wp-color-picker" id="<?php echo $this->get_field_id('bordertopcolor'); ?>" name="<?php echo $this->get_field_name('bordertopcolor'); ?>" value="<?php echo esc_attr( $bordertopcolor ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'bordertopwidth' ); ?>"><?php _e( 'Border Top Width:', 'gridgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('bordertopwidth'); ?>" name="<?php echo $this->get_field_name('bordertopwidth'); ?>" value="<?php echo esc_attr( $bordertopwidth ); ?>">
		</p>
		
		<p>
			<label class="gridget-label" for="<?php echo $this->get_field_id( 'borderbotcolor' ); ?>"><?php _e( 'Border Bottom Color:', 'gridgets' ); ?></label>
			<input type="text" class="wp-color-picker" id="<?php echo $this->get_field_id('borderbotcolor'); ?>" name="<?php echo $this->get_field_name('borderbotcolor'); ?>" value="<?php echo esc_attr( $borderbotcolor ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'borderbotwidth' ); ?>"><?php _e( 'Border Bottom Width:', 'gridgets' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('borderbotwidth'); ?>" name="<?php echo $this->get_field_name('borderbotwidth'); ?>" value="<?php echo esc_attr( $borderbotwidth ); ?>">
		</p>
		
		
		<p><label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Class:', 'gridgets' ); ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" value="<?php echo esc_attr( $class ); ?>" placeholder=""></p>

		<?php
	}
	
	/*
	 * Enqueue styles and scripts
	 * @since 1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {
		
		if( '$widgets.php' !== $hook_suffix ) {
			return;
		}
		
		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

	}
	
	/*
	 * Enqueue styles and scripts
	 * @since 1.0.0
	 */
	public function print_scripts() { ?>
		<script>
			( function( $ ){
				function initColorPicker( widget ) {
					widget.find( '.wp-color-picker' ).wpColorPicker( {
						change: _.throttle( function() { // For Customizer
							$(this).trigger( 'change' );
						}, 3000 )
					});
				}

				function onFormUpdate( event, widget ) {
					initColorPicker( widget );
				}

				$( document ).on( 'widget-added widget-updated', onFormUpdate );

				$( document ).ready( function() {
					$( '#widgets-right .widget:has(.wp-color-picker)' ).each( function () {
						initColorPicker( $( this ) );
					} );
			
					$('body').on('click','#mode-widget-toggle a',function(e){
						e.preventDefault();
						$(this).parent().parent().toggleClass('hide-mode-widget-styles');
					});
				} );
			    function media_upload(button_class) {
			        var _custom_media = true,
			        _orig_send_attachment = wp.media.editor.send.attachment;

			        $('body').on('click', button_class, function(e) {
			            var button_id ='#'+$(this).attr('id');
						var field_id = $(this).attr('id').replace('_button','');
			            var self = $(button_id);
			            var send_attachment_bkp = wp.media.editor.send.attachment;
			            var button = $(button_id);
			            var id = button.attr('id').replace('_button', '');
			            _custom_media = true;
			            wp.media.editor.send.attachment = function(props, attachment){
			                if ( _custom_media  ) {
			                    $('#'+field_id).val(attachment.id);
								$('.custom_media_id').trigger('change');
			             //       $('.custom_media_url').val(attachment.sizes.large.url); // We add the large image
								$('#'+id+'-img').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
			                    $('#'+id+'-img .custom_media_image').attr('src',attachment.sizes.large.url).css('display','block');
			                } else {
			                    return _orig_send_attachment.apply( button_id, [props, attachment] );
			                }
			            }
			            wp.media.editor.open(button);
			                return false;
			        });
			    }
			    media_upload('.custom_media_button.button');
				
				function media_remove(button_class) {
					$('body').on('click', button_class, function(e) {
						var button_id = $(this).attr('id').replace('_remove_button','');
						$('#'+button_id).val('');
						$('#'+button_id).trigger('change');
						$('#'+button_id+'-img').html('');
						$(this).hide();
					});
				}
				media_remove('.remove_custom_media_button.button');
			}( jQuery ) );
		</script>
		<?php
		
	}
	
}

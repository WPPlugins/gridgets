<?php
/*
 * Gridgets public class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'Gridgets_Public' ) ) {

	class Gridgets_Public {
		
		// An array of our registered widget areas
		protected $gridgets = array();

		public function __construct( $gridgets = array() ) {
			//
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action ( 'wp_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
			add_action( 'wp_head', array( $this, 'inline_css' ) );
			
			add_action ( 'widgets_init', array( $this, 'widgets_init' ) );
			
			add_action( 'wp_ajax_add_gridget', array( $this, 'add_gridget' ) ); 
			add_action( 'wp_ajax_delete_gridget', array( $this, 'delete_gridget' ) ); 
			
			add_filter( 'the_content', array( $this, 'filter_content' ) );
			add_action( 'gridgets_before_content', array( $this, 'add_gridget_before_content' ) );
			
			add_filter( 'the_title', array( $this, 'filter_title' ), 10, 2 );
			add_filter( 'body_class', array( $this, 'filter_body' ) );
		}
	
		/*
		 * Filter the content
		 * @since 1.0.0
		 */
		public function filter_content( $content ) {
			$content = do_action( 'gridgets_before_content' ) . $content;
			return $content;
		}
		
		/*
		 * Find any gridgets to add before the content
		 * @since 1.0.0
		 */
		public function add_gridget_before_content() {
			
			global $post;
			$postid = $post -> ID;
			$post_type = get_post_type();
			
			// Get the gridgets
			$gridgets_before = get_post_meta( $postid, 'gridgets_widget_before_content', true );
			
			if( ! empty( $gridgets_before ) ) {
				
				foreach( $gridgets_before as $gridget ) {
					
					if( isset( $gridget['id'] ) ) {
						
						$sidebar = $gridget['id'];
					
						// Set gridget classes
						$classes = array ( 'gridgets-before-content-wrapper' );
						$styles = array();
						$settings = $this -> sidebar_settings( $sidebar );
					
						$background_image = '';
						$padding = '';
						$margin = '';
			
						// Get the layout
						if( ! isset( $settings['layout'] ) ) {
							$classes[] = 'gridget-1-col-wrapper';
						} else {
							$classes[] = 'gridget-' . esc_attr( $settings['layout'] ) . '-wrapper';
						}
					
						// Get the layout
						if( isset( $settings['full_width'] ) ) {
							if( $settings['full_width'] ) {
								$classes[] = 'gridgets-full-width';
							}
						}
					
						// Add any classes
						if( isset( $settings['class'] ) ) {
							$classes[] = $settings['class'];
						}
					
						// Add a background colour
						if( isset( $settings['background-color'] ) ) {
							$styles['background-color'] = $settings['background-color'];
						}
						
						// Add a top border
						if( isset( $settings['bordertopcolor'] ) ) {
							$styles['bordertopcolor'] = $settings['bordertopcolor'];
						}
						if( isset( $settings['bordertopwidth'] ) ) {
							$styles['bordertopwidth'] = $settings['bordertopwidth'];
						}
						
						// Add a bottom border
						if( isset( $settings['borderbotcolor'] ) ) {
							$styles['borderbotcolor'] = $settings['borderbotcolor'];
						}
						if( isset( $settings['borderbotwidth'] ) ) {
							$styles['borderbotwidth'] = $settings['borderbotwidth'];
						}
					
						// Add a background image?
						if( isset( $settings['image_id'] ) && $settings['image_id'] > 0 ) {
							$background_image = $settings['image_id'];
						}
					
						if( isset ( $settings['padding'] ) ) {
							$padding = $settings['padding'];
							$padding = explode( ',', $padding );
							foreach( $padding as $key => $value ) {
								$padding[$key] = $value . 'px';
							}
							$padding = join( ' ', $padding );
							$styles['padding'] = 'padding:' . $padding;
						}
					
						if( isset ( $settings['margin'] ) ) {
							$margin = $settings['margin'];
							$margin = explode( ',', $margin );
							foreach( $margin as $key => $value ) {
								$margin[$key] = $value . 'px';
							}
							$margin = join( ' ', $margin );
							$styles['margin'] = 'margin:' . $margin;
						}
					
						// Classes for the Gridgets markup
						$container_classes = array( 'gridgets-content-container' );
						$inner_classes = array( 'gridgets-before-content-inner-wrapper' );
					
						// Get additional classes for the markup from our settings
						$options_structure = get_option( 'gridgets_structure_settings' );
						if( isset( $options_structure['container_classes'] ) ) {
							// Take out any commas, just in case
							$container_classes[] = str_replace( ',', ' ', $options_structure['container_classes'] );
						}
						if( isset( $options_structure['inner_classes'] ) ) {
							// Take out any commas, just in case
							$inner_classes[] = str_replace( ',', ' ', $options_structure['inner_classes'] );
						}
					
						// Allow classes to be filtered
						$classes = apply_filters( 'gridget_before_content_wrapper_classes', $classes );
						$container_classes = apply_filters( 'gridget_container_classes', $container_classes );
						$inner_classes = apply_filters( 'gridget_inner_classes', $inner_classes );
					
						// Allow styles to be filtered
						$styles = apply_filters( 'gridget_before_content_wrapper_styles', $styles );
					
						if( is_active_sidebar( $sidebar ) ) { ?>
							<div class="<?php echo esc_attr( join( ' ', $classes ) ); ?>" style="<?php echo esc_attr( join( ';', $styles ) ); ?>">
								<?php // Check for a background image
								if( ! empty( $background_image ) ) { ?>
									<div class="gridget-background-image" style="background-image:url(<?php echo wp_get_attachment_url( absint( $background_image ) ); ?>);">
									</div>
								<?php } ?>
								<div class="<?php echo esc_attr( join( ' ', $container_classes ) ); ?>">
									<div class="<?php echo esc_attr( join( ' ', $inner_classes ) ); ?>">
										<?php dynamic_sidebar( $sidebar ); ?>
									</div><!-- .gridgets-before-content-inner-wrapper -->
								</div><!-- .gridgets-before-content -->
							
							
							</div><!-- .gridgets-before-content-wrapper -->
						<?php }
				
					}
					
				}
				
			}
			
		}
		
		/*
		 * Find styles for sidebar / widget area and add wrapper on frontend
		 * Considered hooking to dynamic_sidebar_before but widgets were being added outside the wrapper in the Customizer
		 * @param $index	ID of the sidebar
		 * @since 1.0.0
		 */
		public function sidebar_settings( $index ) {
			global $wp_registered_sidebars, $wp_registered_widgets;
			
			$sidebar_styles = array();
	
			if( isset( $index ) && isset( $wp_registered_sidebars[$index] ) ) {
				$sidebar = $wp_registered_sidebars[$index];
			} else {
				return;
			}
			// The ID of the current widget area
			$current_sidebar_id = $sidebar['id'];
	
			// Get the list of all widgets by widget area
			$sidebars_widgets = wp_get_sidebars_widgets();
	
			// The widgets in this widget area
			if( isset( $sidebars_widgets[$current_sidebar_id] ) ) {
				$current_widgets = $sidebars_widgets[$current_sidebar_id];
			} else {
				// 
			}
	
			// Get all our widgets' styles
			$style_widget_option = get_option( 'widget_gridget_styles' );

			if( isset ( $style_widget_option ) ) {
	
				// Drill down to find the widget styles for this widget area only
				if( ! empty( $current_widgets ) && is_array( $current_widgets ) ) {
					
					// Look for the first Sidebar Styles widget and grab its settings
					foreach( $current_widgets as $current_widget ) {
						if( substr( $current_widget, 0, 14 ) == 'gridget_styles' ) {
							// We've found our style widget
							$style_widget_id = substr( $current_widget, 15 );
							// Don't look for any more, we only use the first one
							break;
						}
					}
					
					// These are our style settings
					if( isset( $style_widget_id ) ) {
					
						$styles = $style_widget_option[$style_widget_id];
					
						if( isset( $styles['full_width'] ) ) {
							$sidebar_styles['full_width'] = $styles['full_width'];
						}
						
						if( isset( $styles['bgcolor'] ) ) {
							$sidebar_styles['background-color'] = 'background-color:' . $styles['bgcolor'];
						}
						
						if( isset( $styles['layout'] ) ) {
							$sidebar_styles['layout'] = $styles['layout'];
						}
						
						if( isset( $styles['image_id'] ) ) {
							$sidebar_styles['image_id'] = $styles['image_id'];
						}
						
						if( isset( $styles['background_image_style'] ) ) {
							$sidebar_styles['background_image_style'] = $styles['background_image_style'];
						}
						
						if( isset( $styles['class'] ) ) {
							$sidebar_styles['class'] = $styles['class'];
						}
						
						if( ! empty( $styles['padding'] ) ) {
							
							// Make sure the padding is set correctly
							// Only numeric values
							// Max 4 elements
							$padding = stripslashes( strip_tags( $styles['padding'] ) );
							
							// Remove anything that's not numeric or a comma
							// Split into array
							$split = explode( ',', preg_replace("/[^0-9,]/", "", $padding ) );
							// Lose any empty elements
							if( ! empty( $split ) ) {
								foreach( $split as $key => $value ) {
									if( ! is_numeric( $split[$key] ) ) {
										unset( $split[$key] );
									}
								}
							}
							// Return first 4 elements only
							$padding = join(',',array_slice($split,0,4));
							$sidebar_styles['padding'] = $padding;
							
						}
						
						if( ! empty( $styles['margin'] ) ) {
							// Make sure the padding is set correctly
							// Only numeric values
							// Max 4 elements
							$margin = stripslashes( strip_tags( $styles['margin'] ) );
					
							// Remove anything that's not numeric or a comma
							// Split into array
							$split = explode( ',', preg_replace("/[^0-9,-]/", "", $margin ) );
							// Lose any empty elements
							if( ! empty( $split ) ) {
								foreach( $split as $key => $value ) {
									if( ! is_numeric( $split[$key] ) ) {
										unset( $split[$key] );
									}
								}
							}
							// Return first 4 elements only
							$margin = join(',',array_slice($split,0,4));
							$sidebar_styles['margin'] = $margin;
							
						}
						
						if( isset( $styles['bordertopcolor'] ) ) {
							$sidebar_styles['bordertopcolor'] = 'border-top: 1px solid ' . $styles['bordertopcolor'];
						}
						if( isset( $styles['bordertopwidth'] ) ) {
							$sidebar_styles['bordertopwidth'] = 'border-top-width: ' . absint( $styles['bordertopwidth'] ) . 'px';
						}
						
						if( isset( $styles['borderbotcolor'] ) ) {
							$sidebar_styles['borderbotcolor'] = 'border-bottom: 1px solid ' . $styles['borderbotcolor'];
						}
						if( isset( $styles['borderbotwidth'] ) ) {
							$sidebar_styles['borderbotwidth'] = 'border-bottom-width: ' . absint( $styles['borderbotwidth'] ) . 'px';
						}
						
					}
					$sidebar_styles = apply_filters( 'gridgets_sidebar_wrapper_styles', $sidebar_styles );
					
				}
				
			}
			
			return $sidebar_styles;
	
		}
		
	
		/*
		 * Filter the title
		 * @since 1.0.0
		 */
		public function filter_title( $title, $id=null ) {
			// Only filter the main page title, not any others (e.g. in sidebars etc)
			$page_id = get_the_id();
			if( $id === $page_id && in_the_loop() ) {
				$hide_title = get_post_meta( $id, 'gridgets_hide_page_title', true );
				if( ! empty( $hide_title ) ) {
					$title = null;
				}
			}
			return $title;
		}
		
		/*
		 * Filter the body classes
		 * @since 1.0.0
		 */
		public function filter_body( $classes ) {
			global $post;
			
			// If we've hidden the page title
			if( isset( $post->ID ) ) {
				$hide_title = get_post_meta( $post->ID, 'gridgets_hide_page_title', true );
				if( ! empty( $hide_title ) ) {
					$classes[] = 'gridgets-hide-page-title';
				}
			}
			
			return $classes;
		}	
	
		/*
		 * Enqueue styles and scripts
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_style ( 'gridgets-style', GRIDGETS_PLUGIN_URL . 'assets/css/gridgets-style.css' );
			wp_enqueue_script ( 'gridgets-script', GRIDGETS_PLUGIN_URL . 'assets/js/gridgets.js', array( 'jquery' ), '0.2.0', true );
		}
		
		/*
		 * Some additional media queries
		 * @since 1.0.0
		 */
		public function inline_css() {
			$options_structure = get_option( 'gridgets_structure_settings' );
			$styles = '';
			if( isset( $options_structure['mobile_breakpoint'] ) && absint( $options_structure['mobile_breakpoint'] > 0 ) ) {
				$breakpoint = absint( $options_structure['mobile_breakpoint'] );
				$styles .= '@media (max-width: ' . $breakpoint . 'px) {
					.gridgets-before-content-wrapper .gridgets-content-container .gridget-wrapper {
						width: 100%;
						margin-bottom: 1.5em;
						padding-left: 15px;
						padding-right: 15px;
					}
				}';

			}
			if( isset( $options_structure['content_width'] ) && absint( $options_structure['content_width'] > 0 ) ) {
				$content_width = absint( $options_structure['content_width'] );
				$styles .= '.gridgets-content-container {
					max-width: ' . $content_width . 'px;
					margin: 0 auto;
				}';
				// When the screen goes below this width, set max-width to 95%
				$styles .= '@media (max-width: ' . $content_width . 'px) {
					.gridgets-content-container {
					max-width: 95%;
					margin: 0 auto;
					}
				}';
			}
			if( $styles != '' ) {
				echo '<style type="text/css">' . $styles . '</style>';
			}
			
		}
		
		/*
		 * Register the widgets
		 * @since 1.0.0
		 */
		public function widgets_init() {
			register_widget ( 'Widget_Gridget_Styles' );
		}

	}
	
}
<?php
/*
 * Register_Gridgets public class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'Register_Gridgets' ) ) {

	class Register_Gridgets {
		
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
	//		add_action( 'init', array( $this, 'clear_all_gridgets' ) );
			add_action( 'init', array( $this, 'register_sidebars' ) , 1000 );
			
			add_action( 'wp_ajax_add_gridget', array( $this, 'add_gridget' ) ); 
			add_action( 'wp_ajax_delete_gridget', array( $this, 'delete_gridget' ) );
			add_action( 'wp_ajax_reorder_gridgets', array( $this, 'reorder_gridgets' ) ); 

		}
		
		/**
		 * Register the widget areas
		 *
		 * @since 0.1.0
		 */
		public function register_sidebars() {
			global $wp_registered_widgets;
			
			// Get widget areas
			if ( empty( $this->gridgets ) ) {
				$this->gridgets = $this->get_gridgets();
			}
			
			// Get widget tags and classes
			$options = get_option( 'gridgets_general_settings' );
			$widget_tag = $options['widget_tag'];
			$widget_classes = $options['widget_classes'];
			$widget_classes .= ' gridget-wrapper';
			$widget_title_tag = $options['widget_title_tag'];
			$widget_title_classes = $options['widget_title_classes'];
			$widget_title_classes .= ' widget-title';

			// If widget areas are defined add a sidebar area for each
			if ( is_array( $this->gridgets ) ) {
				foreach ( $this->gridgets as $gridget ) {
					if( isset( $gridget['id'] ) ) {
						$args = array(
							'id'			=> sanitize_key( $gridget['id'] ),
							'name'			=> $gridget['name'],
							'description'	=> $gridget['description'],
							'class'			=> 'gridget',
							'before_widget'	=> '<' . $widget_tag . ' class="' . esc_attr( $widget_classes ) . '">',
							'after_widget'	=> '</' . $widget_tag . '>',
							'before_title'	=> '<' . $widget_title_tag . ' class="' . esc_attr( $widget_title_classes ) . '">',
							'after_title'	=> '</' . $widget_title_tag . '>',
						);
						register_sidebar( $args );
					}
				}
			}
		}
		
		/**
		 * Add a gridget.
		 *
		 * @since 0.1.0
		 */
		public function add_gridget() {
			check_ajax_referer( 'add-gridget-nonce', 'security' );
			$return = 'fail';
			if ( ! empty( $_REQUEST['name'] ) && ! empty( $_REQUEST['id'] ) && ! empty( $_REQUEST['postid'] ) ) {
				$name = strip_tags( ( stripslashes( $_REQUEST['name'] ) ) );
				$id = strip_tags( ( stripslashes( $_REQUEST['id'] ) ) );
				$theme = strip_tags( ( stripslashes( $_REQUEST['theme'] ) ) );
				$postid = absint( $_REQUEST['postid'] );
				$description = strip_tags( ( stripslashes( $_REQUEST['description'] ) ) );
				
				// @todo check for duplicate IDs
				
				$new_gridget = array(
					'name' 			=> $name,
					'id' 			=> $id,
					'description'	=> $description,
					'theme'			=> $theme
				);
				$this->gridgets[$id] = $new_gridget;
				$this->save_gridgets();
				$this->register_sidebars();
				
				// We need to update the post meta field in case the user leaves the page without saving
				$existing_gridgets = get_post_meta( $postid, 'gridgets_widget_before_content', true );
				$existing_gridgets[$id] = $new_gridget;
				
				update_post_meta( $postid, 'gridgets_widget_before_content', $existing_gridgets );
			
				$return = 'add';

			}
			echo $return;
			die();
		}
		
		/**
		 * Add a gridget.
		 *
		 * @since 0.1.0
		 */
		public function delete_gridget() {
			check_ajax_referer( 'add-gridget-nonce', 'security' );
			$return = 'fail';
			if ( ! empty( $_REQUEST['id'] ) && ! empty( $_REQUEST['postid'] ) ) {
				$id = strip_tags( ( stripslashes( $_REQUEST['id'] ) ) );
				$postid = absint( $_REQUEST['postid'] );
				
				if( ! empty( $this->gridgets ) && is_array( $this->gridgets ) ) {
					$key = array_search($id, $this->gridgets );
					if ( $key >= 0 ) {
						unset( $this->gridgets[$id] );
						$this->save_gridgets();
						// We need to update the post meta field in case the user leaves the page without saving
						$existing_gridgets = get_post_meta( $postid, 'gridgets_widget_before_content', true );
						if( is_array( $existing_gridgets ) ) {
							unset( $existing_gridgets[$id] );
						}
						update_post_meta( $postid, 'gridgets_widget_before_content', $existing_gridgets );
					}
				
					$return = 'delete';
				}
				
			}
			echo $return;
			die();
		}
		
		public function save_gridgets() {
		//	set_theme_mod( 'gridgets', $this->gridgets );
		//	$theme = get_option('stylesheet');
		//	$gridgets_option = get_option( 'gridgets_option' );
		//	$gridgets_option[$theme] = $this->gridgets;
			update_option( 'gridgets_option', $this->gridgets );
		}
		
		public function clear_all_gridgets() {
			update_option( 'gridgets', array() );
		}
		
		/**
		 * Return the gridgets array.
		 *
		 * @since 0.1.0
		 */
		public function get_gridgets() {

			// If the single instance hasn't been set, set it now.
	//		if ( ! empty( $this->gridgets ) ) {
	//			return $this->gridgets;
	//		}

			// Get widget areas saved in theme mod
		//	$gridgets = get_theme_mod( 'gridgets', array() );
			$gridgets = get_option( 'gridgets_option' );
			$this->gridgets = $gridgets;
			
			// If option isn't empty set to class widget area var
		//	if ( ! empty( $gridgets ) && is_array( $gridgets ) ) {
			//	$this->gridgets = array_unique( array_merge( $this->gridgets, $gridgets ) );
		//	}

			// Return gridgets
			return $this->gridgets;
			
		}
		
		/**
		 * Reorder the array after user sorts
		 *
		 * @since 1.2.0
		 */
		public function reorder_gridgets() {

			// Get widget areas
			if ( empty( $this->gridgets ) ) {
				$this->gridgets = $this->get_gridgets();
			}
			
			$all_gridgets = $this->gridgets;
			
			check_ajax_referer( 'add-gridget-nonce', 'security' );
			$return = 'fail';
			if ( ! empty( $_REQUEST['neworder'] ) && ! empty( $_REQUEST['postid'] ) ) {
				$neworder = $_REQUEST['neworder'];
				$postid = absint( $_REQUEST['postid'] );
				
				// @credit http://stackoverflow.com/questions/348410/sort-an-array-by-keys-based-on-another-array#answer-15730056
				// Flip the keys and values in $neworder so that we can update the gridgets array with the new order
				$flippedorder = array_flip( $neworder );
				
				// Resort our array of gridgets to reflect the new order for our post
				$sorted = array_replace( array_flip( $neworder ), $all_gridgets );
				
				// Save the new order
				$this->gridgets = $sorted;
				$this->save_gridgets();
				// We don't update the post meta here as we haven't passed enough information
				// Perhaps we'll do this in the future
				
				$return = $sorted;
				
			}
			
			echo json_encode($return);
			die();
			
		}
	
	}
	
}
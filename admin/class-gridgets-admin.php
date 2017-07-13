<?php
/*
 * Gridgets admin class
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Plugin public class
 **/
if ( ! class_exists( 'Gridgets_Admin' ) ) {

	class Gridgets_Admin {
		
		public function __construct() {
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.4
		 */
		public function init() {
			add_action ( 'admin_menu', array ( $this, 'add_admin_menu' ) );
			add_action ( 'admin_init', array ( $this, 'register_options_general_init' ) );
			add_action ( 'admin_init', array ( $this, 'register_options_structure_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
		
		public function enqueue_scripts() {
		//	wp_enqueue_script( 'jquery-ui-sortable', '', array( 'jquery' ) );
		}
		
		// Add the menu item
		public function add_admin_menu() { 
			add_options_page ( __('Gridgets', 'gridgets'), __('Gridgets', 'gridgets'), 'manage_options', 'gridgets', array ( $this, 'options_page' ) );
		}
		
		public function register_options_general_init() {
		
			register_setting ( 'gridgets_general', 'gridgets_general_settings' );
			
			add_settings_section (
				'gridgets_general_section', 
				__( 'General settings', 'gridgets' ), 
				array ( $this, 'general_settings_section_callback' ), 
				'gridgets_general'
			);
			
			add_settings_field ( 
				'widget_tag', 
				__( 'Widget Tag', 'gridgets' ), 
				array ( $this, 'widget_tag_render' ),
				'gridgets_general', 
				'gridgets_general_section'
			);
			
			add_settings_field ( 
				'widget_classes', 
				__( 'Widget Classes', 'gridgets' ), 
				array ( $this, 'widget_classes_render' ),
				'gridgets_general', 
				'gridgets_general_section'
			);
			
			add_settings_field ( 
				'widget_title_tag', 
				__( 'Widget Title Tag', 'gridgets' ), 
				array ( $this, 'widget_title_tag_render' ),
				'gridgets_general', 
				'gridgets_general_section'
			);
			
			add_settings_field ( 
				'widget_title_classes', 
				__( 'Widget Title Classes', 'gridgets' ), 
				array ( $this, 'widget_title_classes_render' ),
				'gridgets_general', 
				'gridgets_general_section'
			);
			
			// Set default options
			$options = get_option ( 'gridgets_general_settings' );
			if ( false === $options ) {
				// Get defaults
				$defaults = $this -> get_default_general_settings();
				update_option ( 'gridgets_general_settings', $defaults );
			}
			
		}
		
		public function register_options_structure_init() {
		
			register_setting ( 'gridgets_structure', 'gridgets_structure_settings' );
			
			add_settings_section (
				'gridgets_structure_section', 
				__( 'Structure settings', 'gridgets' ), 
				array ( $this, 'structure_settings_section_callback' ), 
				'gridgets_structure'
			);
			
			add_settings_field ( 
				'content_width', 
				__( 'Content Width', 'gridgets' ), 
				array ( $this, 'content_width_render' ),
				'gridgets_structure', 
				'gridgets_structure_section'
			);
			
			add_settings_field ( 
				'mobile_breakpoint', 
				__( 'Mobile Breakpoint', 'gridgets' ), 
				array ( $this, 'mobile_breakpoint_render' ),
				'gridgets_structure', 
				'gridgets_structure_section'
			);
			
			add_settings_field ( 
				'container_classes', 
				__( 'Container Classes', 'gridgets' ), 
				array ( $this, 'container_classes_render' ),
				'gridgets_structure', 
				'gridgets_structure_section'
			);
			
			add_settings_field ( 
				'inner_classes', 
				__( 'Inner Classes', 'gridgets' ), 
				array ( $this, 'inner_classes_render' ),
				'gridgets_structure', 
				'gridgets_structure_section'
			);
			
			// Set default options
			$options = get_option ( 'gridgets_structure_settings' );
			if ( false === $options ) {
				// Get defaults
				$defaults = $this -> get_default_structure_settings();
				update_option ( 'gridgets_structure_settings', $defaults );
			}
			
		}
			
		/*
		 * Defaults
		 */
		public function get_default_general_settings() {
			$defaults = array (
				'widget_tag'				=> 'div',
				'widget_classes'			=> '',
				'widget_title_tag'			=> 'h5',
				'widget_title_classes'		=> ''
			);
			return $defaults;
		}
		
		public function get_default_structure_settings() {
			$defaults = array (
				'content_width'				=> 0,
				'mobile_breakpoint'			=> 768,
				'container_classes'			=> 'container',
				'inner_classes'				=> 'row',
			//	'gridget_classes'			=> ''
			);
			return $defaults;
		}
		
		/*
		 * Here are all our settings
		 */
		public function widget_tag_render() { 
			$options = get_option( 'gridgets_general_settings' ); ?>
			<select name='gridgets_general_settings[widget_tag]'>
				<option value='div' <?php selected( $options['widget_tag'], 'div' ); ?>>div</option>
				<option value='span' <?php selected( $options['widget_tag'], 'span' ); ?>>span</option>
				<option value='section' <?php selected( $options['widget_tag'], 'section' ); ?>>section</option>
			</select>
		<?php
		}
		
		public function widget_classes_render() { 
			$options = get_option( 'gridgets_general_settings' ); ?>
			<input type="text" name="gridgets_general_settings[widget_classes]" value="<?php echo $options['widget_classes']; ?>">
			<p class="description"><?php _e ( 'Enter class names separated by spaces', 'gridgets' ); ?></p>
		<?php
		}
		
		public function widget_title_tag_render() { 
			$options = get_option( 'gridgets_general_settings' ); ?>
			<select name='gridgets_general_settings[widget_title_tag]'>
				<option value='span' <?php selected( $options['widget_title_tag'], 'span' ); ?>>span</option>
				<option value='p' <?php selected( $options['widget_title_tag'], 'p' ); ?>>p</option>
				<option value='h1' <?php selected( $options['widget_title_tag'], 'h1' ); ?>>h1</option>
				<option value='h2' <?php selected( $options['widget_title_tag'], 'h2' ); ?>>h2</option>
				<option value='h3' <?php selected( $options['widget_title_tag'], 'h3' ); ?>>h3</option>
				<option value='h4' <?php selected( $options['widget_title_tag'], 'h4' ); ?>>h4</option>
				<option value='h5' <?php selected( $options['widget_title_tag'], 'h5' ); ?>>h5</option>
				<option value='h6' <?php selected( $options['widget_title_tag'], 'h6' ); ?>>h6</option>
			</select>
		<?php
		}
		
		public function widget_title_classes_render() { 
			$options = get_option( 'gridgets_general_settings' ); ?>
			<input type="text" name="gridgets_general_settings[widget_title_classes]" value="<?php echo $options['widget_title_classes']; ?>">
			<p class="description"><?php _e ( 'Enter class names separated by spaces', 'gridgets' ); ?></p>
		<?php
		}
		
		public function content_width_render() { 
			$options = get_option( 'gridgets_structure_settings' ); ?>
			<input type="number" name="gridgets_structure_settings[content_width]" value="<?php echo $options['content_width']; ?>">
			<p class="description"><?php _e ( 'You can set a maximum width (in px) for your content to ensure Gridget content aligns nicely with the rest of the page. Only use this setting if you don\'t want to use classes below or your own CSS.', 'gridgets' ); ?></p>
		<?php
		}
		
		public function mobile_breakpoint_render() { 
			$options = get_option( 'gridgets_structure_settings' ); ?>
			<input type="number" name="gridgets_structure_settings[mobile_breakpoint]" value="<?php echo $options['mobile_breakpoint']; ?>">
			<p class="description"><?php _e ( 'Value (in px) below which Gridget columns will render at 100% width', 'gridgets' ); ?></p>
		<?php
		}
		
		public function container_classes_render() { 
			$options = get_option( 'gridgets_structure_settings' ); ?>
			<input type="text" name="gridgets_structure_settings[container_classes]" value="<?php echo $options['container_classes']; ?>">
			<p class="description"><?php _e ( 'See the documentation for information on these classes', 'gridgets' ); ?></p>
		<?php
		}
		
		public function inner_classes_render() { 
			$options = get_option( 'gridgets_structure_settings' ); ?>
			<input type="text" name="gridgets_structure_settings[inner_classes]" value="<?php echo $options['inner_classes']; ?>">
			<p class="description"><?php _e ( 'See the documentation for information on these classes', 'gridgets' ); ?></p>
		<?php
		}
		
		public function gridget_classes_render() { 
			$options = get_option( 'gridgets_structure_settings' ); ?>
			<input type="text" name="gridgets_structure_settings[gridget_classes]" value="<?php echo $options['gridget_classes']; ?>">
			<p class="description"><?php _e ( 'See the documentation for information on these classes', 'gridgets' ); ?></p>
		<?php
		}
		
		// Callback for General settings
		public function general_settings_section_callback() { ?>
			<p>
				<?php echo __( 'Define the markup for your widgets.', 'gridgets' ); ?>
			</p>
		<?php
		}
		
		// Callback for Structure settings
		public function structure_settings_section_callback() { ?>
			<p>
				<?php echo __( 'Use these settings to change how Gridgets are displayed.', 'gridgets' ); ?>
			</p>
		<?php
		}
	
		public function options_page() {
	
			$current = isset ( $_GET['tab'] ) ? $_GET['tab'] : 'general';
			$title =  __( 'Gridgets', 'gridgets' );
			$tabs = array (
				'general'		=>	__( 'General', 'gridgets' ),
				'structure'		=>	__( 'Structure', 'gridgets' ),
			//	'styles'		=>	__( 'Styles', 'gridgets' )
			); ?>
		
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
				<div class="ctdb-outer-wrap">
					<div class="ctdb-inner-wrap">
						<h2 class="nav-tab-wrapper">
							<?php foreach( $tabs as $tab => $name ) {
								$class = ( $tab == $current ) ? ' nav-tab-active' : '';
								echo "<a class='nav-tab $class' href='?page=gridgets&tab=$tab'>$name</a>";
							} ?>
						</h2>
						<form action='options.php' method='post'>
							<?php
							settings_fields( 'gridgets_' . $current );
							do_settings_sections( 'gridgets_' . $current );
							submit_button();
							?>
						</form>
					</div><!-- .ctdb-inner-wrap -->
					<div class="ctdb-banners">
						<div class="ctdb-banner hide-dbpro">
							<a href="http://discussionboard.pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=gridgets&utm_campaign=dbpro"><img src="<?php echo GRIDGETS_PLUGIN_URL . 'assets/images/dbpro-ad-view.png'; ?>" alt="" ></a>
						</div>
						<div class="ctdb-banner">
							<a href="http://superheroslider.catapultthemes.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=gridgets&utm_campaign=superhero"><img src="<?php echo GRIDGETS_PLUGIN_URL . 'assets/images/superhero-ad1.png'; ?>" alt="" ></a>
						</div>
						<div class="ctdb-banner">
							<a href="https://sellastic.com/?ref=1&utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=gridgets&utm_campaign=sellastic"><img src="<?php echo GRIDGETS_PLUGIN_URL . 'assets/images/sellastic-ad1.jpg'; ?>" alt="" ></a>
						</div>	
						<div class="ctdb-banner">
							<a href="http://mode.catapultthemes.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=gridgets&utm_campaign=themes"><img src="<?php echo GRIDGETS_PLUGIN_URL . 'assets/images/themes-ad1.png'; ?>" alt="" ></a>
						</div>			
					</div>
				</div><!-- .ctdb-outer-wrap -->
			</div><!-- .wrap -->
			<?php
		}
	}
	
}
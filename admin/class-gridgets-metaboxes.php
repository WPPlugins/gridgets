<?php
/*
 * Gridgets metaboxes
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin public class
 **/
if ( ! class_exists( 'Gridgets_Metaboxes' ) ) {

	class Gridgets_Metaboxes {
	
		public $metaboxes;

		public function __construct ( $metaboxes ) {
			$this -> metaboxes = $metaboxes;
		}
		
		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action ( 'admin_enqueue_scripts', array ( $this, 'enqueue_scripts' ) );
			add_action ( 'add_meta_boxes', array ( $this, 'add_meta_box' ) );
			add_action ( 'save_post', array ( $this, 'save_metabox_data' ) );
			add_action( 'save_post', array ( $this, 'save_before_content_gridgets' )  );
		}
		
		/*
		 * Register the metabox
		 * @since 1.0.0
		 */
		public function add_meta_box() {
			
			$screens = array ( 'post', 'page' );
			$metaboxes = $this -> metaboxes;
			
			foreach ( $metaboxes as $metabox ) {
	
				add_meta_box (
					$metabox['ID'],
					$metabox['title'],
					array ( $this, $metabox['callback'] ),
					$metabox['screens'],
					$metabox['context'],
					$metabox['priority'],
					$metabox['fields']
				);
				
			}
			
		}
		
		/*
		 * Metabox callbacks
		 * @since 1.0.0
		*/
		public function meta_box_callback ( $post, $fields ) {

			wp_nonce_field ( 'save_metabox_data', 'gridgets_metabox_nonce' );
			
			if ( $fields['args'] ) {
				
				foreach ( $fields['args'] as $field ) {
						
					switch ( $field['type'] ) {
						
						case 'add_widget_area':
							$this -> add_widget_area( $post, $field );
							break;
						case 'checkbox':
								$this -> metabox_checkbox_output( $post, $field );
								break;
					
					}
						
				}
				
			}

		}
		
		/*
		 * Metabox callback for new widget area
		 * @since 1.0.0
		 * @link https://gist.github.com/helen/1593065
		 */
		public function add_widget_area( $post, $field ) {
			
			$gridgets = get_post_meta ( $post -> ID, $field['ID'], true );
			
			// Use the current theme if we only want theme-specific gridgets
			$current_theme = get_option('stylesheet');
			$id = get_post_type() . '-' . $post -> ID . '-sidebar';
			$name = __( 'Before content', 'gridgets' ) . ' ' . get_post_type() . ' ' . $post -> ID . ' sidebar';
			$description = __( 'Sidebar before content for', 'gridgets' ) . ' ' . get_the_title( $post->ID );
			$ajax_nonce = wp_create_nonce( 'add-gridget-nonce' );
			
			wp_nonce_field( 'gridget_add_widget_area_nonce', 'gridget_add_widget_area_nonce' );

			?>
			<div class="gridgets_metafield <?php echo $field['class'] ; ?>">
				
				<table id="gridgets-before-content" class="widefat" width="100%">
					<thead>
						<tr>
							<th></th>
							<th><?php _e( 'Name', 'gridgets' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					
					<?php
					
					if ( ! empty( $gridgets ) ) :
	
						foreach ( $gridgets as $gridget ) { 
							if( isset( $gridget['id'] ) ) { ?>
								<tr id="<?php echo $gridget['id']; ?>" data-rowid="<?php echo $gridget['id']; ?>">
									<td><span class="dashicons dashicons-sort"></span></td>
									<td>
										<input type="hidden" name="id[]" value="<?php if($gridget['id'] != '') echo esc_attr( $gridget['id'] ); ?>" />
										<input type="hidden" name="theme[]" class="gridget-theme" value="<?php if( $gridget['theme'] != '') echo esc_attr( $gridget['theme'] ); ?>" />
										<input readonly type="text" name="name[]" class="gridget-name" value="<?php if( $gridget['name'] != '') echo esc_attr( $gridget['name'] ); ?>" />
									</td>
									
									<td><a class="button remove-row" href="#" data-gridget-id="<?php echo $gridget['id']; ?>"><?php _e( 'Delete Widget Area', 'gridgets' ); ?></a></td>
								</tr>
							<?php
							}
						}
						
					endif; ?>

						<!-- This is the one the user can edit -->
						<tr class="empty-row" data-rowid='0'>
							
							<td><span class="dashicons dashicons-sort"></span></td>
							<td>
								<input type="hidden" class="gridget-id" name="id[]" />
								<input type="hidden" class="gridget-theme" name="theme[]" value="<?php echo $current_theme; ?>" />
								<input type="text" class="gridget-name" name="name[]" />
							</td>
							
							<td>
								<a class="add-row button button-primary" href="#"><?php _e( 'Add Widget Area', 'gridgets' ); ?></a>
								<a class="button remove-row" style="display:none;" href="#"><?php _e( 'Delete Widget Area', 'gridgets' ); ?></a>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="gridgets-spinner-wrapper">
					<span class="spinner"></span>
				</div>
			</div>
			<script>
				jQuery(document).ready(function($){
					
					// Prevent the page being submitted when the user hits Enter
					$('.gridget-name').keypress(function(e){
						if(e.which == 13){
							e.preventDefault();
						}
					});
					
					// @todo - add new row to sortable list
					var sortList = $('#gridgets-before-content tbody');
					var listElements = sortList.children();
					
					// Fires when the list is reordered
					var sortHandler = function(e,ui){
						$('.gridgets-add-area').addClass('updating');
						newOrder = $('#gridgets-before-content tbody').sortable('toArray');
						// Remove the empty array
						newOrder = newOrder.filter(Boolean);
						// console.log(newOrder);
						// Send the new order via AJAX
						$.ajax({
							type: "POST",
							url: window.ajaxurl,
							data: {
								action: 'reorder_gridgets',
								postid: <?php echo $post -> ID; ?>,
								neworder: newOrder,
								security: '<?php echo $ajax_nonce; ?>'
							},
							success: function(response) {
								// console.log(response.toString());
								$('.gridgets-add-area').removeClass('updating');
								if(response!='fail'){
								//	alert('<?php _e( "Reordered", "gridgets" ); ?>')
								} else {
								//	alert('<?php _e( "Failed", "gridgets" ); ?>');
								}
							}
						});
						$('#gridgets-before-content tbody').sortable({
						    connectWith: '#gridgets-before-content tbody'
						});
						
					}
					
					$('#gridgets-before-content tbody').sortable({
						helper: gridgetsFixWidthHelper,
						stop: sortHandler,
						items: 'tr:not(.empty-row)'
					}).disableSelection();
					
					// @credit https://paulund.co.uk/fixed-width-sortable-tables
					function gridgetsFixWidthHelper(e,ui){
						ui.children().each(function(){
							$(this).width($(this).width());
						});
						return ui;
					}
					
					// Add a new row
					$( '.add-row' ).on('click', function() {
						$('.gridgets-add-area').addClass('updating');
						// Get the next ID
						var baseID = '<?php echo $id; ?>';
						var baseName = '<?php echo $name; ?>';
						// We've always got 2 rows - the header and empty one
						var rowCount = $('#gridgets-before-content tr').length;
						if(rowCount==2){
							// We're adding our first row
							var rowID = rowCount-1;
						} else {
							var highScore = 1;
							var splitID;
							$('#gridgets-before-content').find('tr').each(function(i,el){
								if($(this).attr('data-rowid')!=undefined){
									splitArr=$(this).attr('data-rowid').split('-');
									splitID=splitArr[splitArr.length-1];
									if(splitID>highScore){
										highScore = parseInt(splitID);
									}
								}
							});
							rowID = parseInt(highScore) + 1;
						}
						var row = $('.empty-row').clone(true);
						row.removeClass('empty-row');
						var oldRow = $('.empty-row');
						// Clean the empty row up
						oldRow.find('input.gridget-name').val('');
						row.find('.add-row').hide();
						row.find('.remove-row').css('display','inline-block');
						row.insertBefore('#gridgets-before-content tbody>tr:last');
						// Set the ID and Name
						row.closest('tr').attr('data-rowid',rowID);
						row.find('input.gridget-id').val(baseID+'-'+rowID);
						// If we haven't entered a name, make one up
						if(row.find('input.gridget-name').val()==''){
							row.find('input.gridget-name').val(baseName+'-'+rowID);
						}
						// Pass this to the AJAX function
						var newName = row.find('input.gridget-name').val();
						// User can't rename widget now
						row.find('input.gridget-name').attr('readonly',true);
						
						// Add the widget area via AJAX
						$.ajax({
							type: "POST",
							url: window.ajaxurl,
							data: {
								action: 'add_gridget',
								postid: <?php echo $post -> ID; ?>,
								id: baseID+'-'+rowID,
								description: '<?php echo $description; ?>',
								name: newName,
								theme: '<?php echo $current_theme; ?>',
								security: '<?php echo $ajax_nonce; ?>'
							},
							success: function(response) {
								$('.gridgets-add-area').removeClass('updating');
								if(response=='add'){
							//		alert('<?php _e( "Widget area added", "gridgets" ); ?>')
								} else {
									alert('<?php _e( "Widget area failed", "gridgets" ); ?>');
								}
							}
						});
						
						return false;
					});

					$( '.remove-row' ).on('click', function() {
						$('.gridgets-add-area').addClass('updating');
						var removeRow = $(this).parents('tr');
						var removeID = removeRow.attr('data-rowid');
						$.ajax({
							type: "POST",
							url: window.ajaxurl,
							data: {
								action: 'delete_gridget',
								postid: <?php echo $post -> ID; ?>,
								id: removeID,
								name: '<?php echo $name; ?>',
								theme: '<?php echo $current_theme; ?>',
								security: '<?php echo $ajax_nonce; ?>'
							},
							success: function(response) {
								$('.gridgets-add-area').removeClass('updating');
								if(response=='delete'){
							//		alert('<?php _e( "Widget area deleted", "gridgets" ); ?>')
									removeRow.remove();
								} else {
									alert('<?php _e( "Widget area deletion failed", "gridgets" ); ?>');
									
								}
							}
						});
						return false;
					});
					
					$('#delete-gridget-before-button').on('click', function(e){
						e.preventDefault();
						$('#<?php echo $field['name']; ?>').val('');
						$.ajax({
							type: "POST",
							url: window.ajaxurl,
							data: {
								action: 'delete_gridget',
								postid: <?php echo $post -> ID; ?>,
								name: '<?php echo $name; ?>',
								security: '<?php echo $ajax_nonce; ?>'
							},
							success: function(response) {
								if(response=='delete'){
									alert('<?php _e( "Widget area deleted", "gridgets" ); ?>')
								} else {
									alert('<?php _e( "Widget area deletion failed", "gridgets" ); ?>');
								}
							}
						});
					});
				});
			</script>
			<?php
		}
		
		/*
		 * Metabox callback for checkbox
		 * @since 1.0.0
		 */
		public function metabox_checkbox_output( $post, $field ) {
			
			$field_value = 0;
			
			wp_nonce_field( 'gridget_add_metabox_nonce', 'gridget_add_metabox_nonce' );
			
			// First check if we're on the post-new screen
			global $pagenow;
			if ( in_array ( $pagenow, array( 'post-new.php' ) ) ) {
				// This is a new post screen so we can apply the default value
				$field_value = $field['default'];
			} else {		
				$custom = get_post_custom ( $post->ID );
				if ( isset ( $custom[$field['ID']][0] ) ) {
					$field_value = $custom[$field['ID']][0];
				}
			}
			?>
			<div class="gridgets_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<input type="checkbox" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="1" <?php checked ( 1, $field_value ); ?>>
				<?php if ( ! empty ( $field['label'] ) ) { ?>
					<?php echo $field['label']; ?>
				<?php } ?>
				<?php if ( ! empty ( $field['description'] ) ) { ?>
					<p class="description"><?php echo $field['description']; ?></p>
				<?php } ?>
			</div>
			<?php
		}
		
		
		function save_before_content_gridgets($post_id) {
			if ( ! isset( $_POST['gridget_add_widget_area_nonce'] ) ||
			! wp_verify_nonce( $_POST['gridget_add_widget_area_nonce'], 'gridget_add_widget_area_nonce' ) )
				return;
	
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;
	
			if (!current_user_can('edit_post', $post_id))
				return;
	
			$old = get_post_meta( $post_id, 'gridgets_widget_before_content', true);
			$new = array();
			
			$ids = $_POST['id'];
			$names = $_POST['name'];
			$themes = $_POST['theme'];

			$count = count( $names );
	
			for ( $i = 0; $i < $count; $i++ ) {
				if ( $names[$i] != '' ) :
					$new[$i]['id'] = stripslashes( strip_tags( $ids[$i] ) );
					$new[$i]['name'] = stripslashes( strip_tags( $names[$i] ) );
					$new[$i]['theme'] = stripslashes( strip_tags( $themes[$i] ) );
				endif;
			}
			if ( !empty( $new ) && $new != $old )
				update_post_meta( $post_id, 'gridgets_widget_before_content', $new );
			elseif ( empty($new) && $old )
				delete_post_meta( $post_id, 'gridgets_widget_before_content', $old );
		}
		
		
		
		/*
		 * Metabox callback for select
		 * @since 1.0.0
		 */
		public function metabox_select_output( $post, $field ) {
			
			$field_value = get_post_meta ( $post -> ID, $field['ID'], true );
			
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if ( empty ( $field_value ) && ! empty ( $field['default'] ) ) {
				$field_value = $field['default'];
			}
			
			?>
			<div class="gridgets_metafield <?php echo $field['class']; ?>">
				<label for="<?php echo $field['name']; ?>"><?php echo $field['title']; ?></label>
				<select id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
					<?php if ( $field['options'] ) {
						foreach ( $field['options'] as $key => $value ) { ?>
							<option value="<?php echo $key; ?>" <?php selected ( $field_value, $key ); ?>><?php echo $value; ?></option>
						<?php }
					} ?>
				</select>
			</div>
			<?php
		}
		
		/*
		 * Save
		 * @since 1.0.0
		 */
		public function save_metabox_data( $post_id ) {
			
			// Check the nonce is set
			if ( ! isset ( $_POST['gridget_add_metabox_nonce'] ) ) {
				return;
			}
			
			// Verify the nonce
			if ( ! wp_verify_nonce ( $_POST['gridget_add_metabox_nonce'], 'gridget_add_metabox_nonce' ) ) {
				return;
			}
			
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			
			// Save all our metaboxes
			$metaboxes = $this -> metaboxes;
			foreach ( $metaboxes as $metabox ) {
				if ( $metabox['fields'] ) {
					foreach ( $metabox['fields'] as $field ) {
						
						if ( $field['type'] != 'divider' ) {
							
							if ( isset ( $_POST[$field['name']] ) ) {
								if ( $field['type'] == 'wysiwyg' ) {
									$data = $_POST[$field['name']];
								} else {
									$data = sanitize_text_field ( $_POST[$field['name']] );
								}
								update_post_meta ( $post_id, $field['ID'], $data );
							} else {
								delete_post_meta ( $post_id, $field['ID'] );
							}
						}
					}
				}
			}
			
		}
	
		
		
		/*
		 * Enqueue styles and scripts
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_style ( 'gridgets-admin-style', GRIDGETS_PLUGIN_URL . 'assets/css/admin-style.css' );
			wp_enqueue_media();

		/*	wp_register_script( 'gridgets-admin', GRIDGETS_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ) );
			wp_localize_script( 'gridgets-admin', 'meta_image',
				array(
					'title' => __( 'Add Image', 'gridgets' ),
					'button' => __( 'Select', 'gridgets' ),
				)
			); */
			
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
		}

	}
	
}
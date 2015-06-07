<?php

/**
 * The validation class for the plugin.
 *
 * @link       https://github.com/pehaa/pht-simple-post-types
 * @since      1.0.0
 *
 * @package    PHT_Simple_Post_Types
 * @subpackage PHT_Simple_Post_Types/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * 
 *
 * @package    PHT_Simple_Post_Types
 * @subpackage PHT_Simple_Post_Types/admin
 * @author     PeHaa THEMES <info@pehaa.com>
 */
class PHT_Simple_Post_Types_Admin_Display {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = PHT_Simple_Post_Types::$options;
		$this->reserved_slugs();

	}

	private function reserved_slugs() {
		
		$reserved_terms = PHT_Simple_Post_Types_Admin::get_reserved_terms();
		$this->reserved_post_type_terms = implode( ', ', array_values( $reserved_terms['post_type'] ) );
		$this->reserved_taxonomy_terms = implode( ', ', array_values($reserved_terms['taxonomy'] ) );

	}

	public function slug_form_field( $itemtype ) {
		$label = 'post_type' === $itemtype ? __( 'New custom post type slug (required):', $this->plugin_name ) : __( 'New custom taxonomy slug:', $this->plugin_name );
		$placeholder = __( 'Put a unique name here', $this->plugin_name );
		$data_regex = "data-regex='$itemtype-slug'";
		$this->form_field_text( 'phtspt_field-' . $itemtype . '-key', 'phtspt_field[key]', 'phtspt-required phtspt-regex', $label, $placeholder, $this->slug_form_field_legend( $itemtype ), $data_regex );
	}

	public function name_form_field( $itemtype ) {
		$label = 'post_type' === $itemtype ? __( 'Custom post type name in plural form (required):', $this->plugin_name ) : __( 'Custom taxonomy name (plural form):', $this->plugin_name );
		$placeholder = __( 'Put a name here', $this->plugin_name );
		$legend = 'post_type' === $itemtype ? __( 'General name for post type, <b>usually plural.</b> This string will be also used  for menu items.', $this->plugin_name ) : __( 'General name for taxonomies, <b>usually plural.</b> This string will be also used  for menu items.' );
		$data_regex = 'data-regex="label"';
		$this->form_field_text( 'phtspt_field-' . $itemtype . '-name', 'phtspt_field[name]', 'phtspt-required phtspt-regex', $label, $placeholder, $legend, $data_regex );
	}

	public function singular_name_form_field( $itemtype ) {
		$label = 'post_type' === $itemtype ? __( 'Custom post type name in singular form (required):', $this->plugin_name ) : __( 'Custom taxonomy name (singular form):', $this->plugin_name );
		$placeholder = __( 'Put a name here', $this->plugin_name );
		$legend = 'post_type' === $itemtype ? __( 'Name for one object of this post type. Also used for the "Add New" dropdown on admin bar. ', $this->plugin_name ) : __( 'Name for one object of this taxonomy.' );
		$data_regex = 'data-regex="label"';
		$this->form_field_text( 'phtspt_field-' . $itemtype . '-singular_name', 'phtspt_field[singular_name]', 'phtspt-required phtspt-regex', $label, $placeholder, $legend, $data_regex );
	}

	public function hierarchical_form_field( $id = 'phtspt-hierarchical-new', $hierarchical = NULL ) { ?>

		<div class="form-field">
			<label for="<?php echo $id; ?>"><?php _e( 'Hierarchical', $this->plugin_name ); ?></label>
			<select id="<?php echo $id; ?>" name="phtspt_field[phtspt-hierarchical]">
				<option value="no" <?php $hierarchical ? selected( $hierarchical, 'no' ) : 'selected'; ?>><?php _e( 'Not hierarchical, like tags', $this->plugin_name ); ?></option>
				<option value="yes" <?php $hierarchical ? selected( $hierarchical, 'yes' ) : ''; ?>><?php _e( 'Hierarchical, like categories', $this->plugin_name ); ?></option>		
			</select>
			<div class='phtspt-legend'><?php _e( 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags?', $this->plugin_name ); ?></div>
		</div>

	<?php }

	public function object_types_form_field( $key = 'new', $object_types = array() ) { ?>
		
		<div class="form-field">
			
			<?php foreach ( $this->options['data']['post_type'] as $post_type_key => $post_type_array ) { ?>
				<input type="checkbox" id="phtspt-object-type-<?php echo $key;?>-<?php echo $post_type_key;?>" name="phtspt_field[phtspt-object-type][]" value="<?php echo $post_type_key;?>" <?php checked( in_array( $post_type_key, $object_types ) ) ?>/>
				<label for="phtspt-object-type-<?php echo $key;?>-<?php echo $post_type_key;?>" class="phtspt-label--inline"><?php echo $post_type_array['name']; ?></label>								
			<?php } ?>
			<div class='phtspt-legend'><?php _e( 'Name of the object type for the taxonomy object.', $this->plugin_name ); ?></div>
		</div>

	<?php }
				
	private function form_field_text( $id, $name, $class, $label, $placeholder, $legend, $attr, $value = '' ) { ?>
		<div class="form-field">
			<?php 
			echo "<label for='$id'>$label</label>";
			echo "<input id='$id' name='$name' class='$class' type='text' placeholder='$placeholder' value='$value' $attr/>";
			echo "<div class='phtspt-legend'>$legend</div>"; ?>
		</div>

	<?php }

	private function slug_form_field_legend( $itemtype ) {
		$property_name = 'reserved_' . $itemtype . '_terms';
		return sprintf( __( 'The unique slug for your post type, max. 20 characters, can not contain capital letters or spaces. Don\'t use any of the following terms: %s', $this->plugin_name ), $this->$property_name );
	}


}
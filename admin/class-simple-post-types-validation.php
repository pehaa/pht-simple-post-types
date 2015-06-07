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
class PHT_Simple_Post_Types_Validation {

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
	
	private $items = array( 'post_type', 'taxonomy' );
	
	private $regex_pattern = array(
		'post_type' => '/^[a-z][a-z0-9_]{0,19}$/',
		'taxonomy' => '/^[a-z][a-z0-9_]{0,31}$/',
		'label' => '/^[a-zA-Z-\s_]{1,15}$/'									
	);
	
	public $screen_message = '';

	public $field = array();
	
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = PHT_Simple_Post_Types::$options;		
				
	}

	public function validate( $data, $actiontype, $itemtype ) {

		switch( $actiontype ) {
			case 'add' :
				return $this->validate_on_add( $data, $itemtype );
			break;
			case 'delete' :
				return $this->validate_on_remove( $data, $itemtype );
			break;
			case 'edit' :
				return $this->validate_on_edit( $data, $itemtype );
			break;
		}

	}

	private function validate_on_add( $data, $itemtype ) {

		if ( !isset( $data['phtspt_field'] ) ) {
			$this->screen_message = 'error-0';
			return false;			
		}

		if ( !isset( $data['phtspt_field']['key'] ) || !isset( $data['phtspt_field']['name'] ) || !isset( $data['phtspt_field']['singular_name'] ) ) {
			$this->screen_message = 'error-0';
			return false;			
		}
		$data['phtspt_field']['key'] = trim( $data['phtspt_field']['key'] );
		$data['phtspt_field']['name'] = trim( $data['phtspt_field']['name'] );
		$data['phtspt_field']['singular_name'] = trim( $data['phtspt_field']['singular_name'] );
		$this->field = $data['phtspt_field'];

		if ( !preg_match( $this->regex_pattern[ $itemtype ], $this->field['key'] ) ) {
			if ( '' === $this->field['key'] ) {
				$this->screen_message = 'error-5';
			} else {
				$this->screen_message = 'error-' . ( 'post_type' === $itemtype ? 3 : 4 );
			}
			return false;
		}

		$reserved_terms = PHT_Simple_Post_Types_Admin::get_reserved_terms();

		if ( in_array( $this->field['key'], $reserved_terms[ $itemtype ] ) ) {
			$this->screen_message = 'error-' . ( 'post_type' === $itemtype ? 6 : 7 );
			return false;
		}

		if ( !preg_match( $this->regex_pattern['label'], $this->field['name'] ) ) {
			if ( '' === $this->field['name'] ) {
				$this->screen_message = 'error-5';
			} else {
				$this->screen_message = 'error-8';
			}
			return false;
		}

		if ( !preg_match( $this->regex_pattern['label'], $this->field['singular_name'] ) ) {
			if ( '' === $this->field['singular_name'] ) {
				$this->screen_message = 'error-5';
			} else {
				$this->screen_message = 'error-8';
			}
			return false;
		}

		if ( 'taxonomy'  === $itemtype ) {
			
			if ( !$this->validate_object_type() )
			return false;
			
			if ( !$this->validate_hierarchical() )
				return false;			

		} 
		
		$this->screen_message = 'updated-' . ( 'post_type' === $itemtype ? 1 : 2 );
		return true;

	}

	private function validate_on_remove( $data, $itemtype ) {

		if ( !isset( $_GET['deleteid'] ) ) {
			$this->screen_message = 'error-10';
			return false;
		} 
		
		$this->field['key'] =  $_GET['deleteid'];
		
		if ( !array_key_exists( $this->field['key'], $this->options['data'][$itemtype] ) ) {
			$this->screen_message = 'error-10';
			return false;
		}

		$this->screen_message = 'updated-' . ( 'post_type' === $itemtype ? 3 : 4 );
		return true;

	}

	private function validate_on_edit( $data, $itemtype ) {

		if ( 'taxonomy'  === $itemtype ) {

			if ( !isset( $data['phtspt_field'] ) ) {
				$this->screen_message = 'error-0';
				return false;			
			}

			if ( !isset( $data['phtspt_field']['key'] ) ) {
				$this->screen_message = 'error-0';
				return false;			
			}

			$this->field = $data['phtspt_field'];

			if ( !$this->validate_object_type() )
				return false;
			
			if ( !$this->validate_hierarchical() )
				return false;			

			$this->screen_message = 'updated-6';
				return true;

		}

	}

	private function validate_object_type() {

		if ( isset( $this->field['phtspt-object-type'] ) && is_array( $this->field['phtspt-object-type'] ) ) {
			foreach($this->field['phtspt-object-type'] as $object_type ) {
				if ( !array_key_exists( $object_type, $this->options['data']['post_type'] ) ) {
					$this->screen_message = 'error-11';
					return false;
				}

			}
		} else {
			$this->field['phtspt-object-type'] = array();
		}
		return true;

	}

	private function validate_hierarchical() {
		if ( isset( $this->field['phtspt-hierarchical'] ) ) {
			if ( !in_array( $this->field['phtspt-hierarchical'], array( 'yes', 'no') ) ) {
				$this->screen_message = 'error-12';
				return false;
			}
		} else {
			$this->field['phtspt-hierarchical'] = 'yes';
		}
		return true;

	}

}
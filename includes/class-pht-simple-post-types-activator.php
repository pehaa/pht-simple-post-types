<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/pehaa/pht-simple-post-types
 * @since      1.0.0
 *
 * @package    PeHaaThemes_Simple_Post_Types
 * @subpackage PeHaaThemes_Simple_Post_Types/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    PeHaaThemes_Simple_Post_Types
 * @subpackage PeHaaThemes_Simple_Post_Types/includes
 * @author     PeHaa THEMES <info@pehaa.com>
 */
class PeHaaThemes_Simple_Post_Types_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option( PEHAATHEMES_SPT_ACTIVATION, true );
	}

}
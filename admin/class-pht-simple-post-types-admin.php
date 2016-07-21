<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/pehaa/pht-simple-post-types
 * @since      1.0.0
 *
 * @package    PeHaaThemes_Simple_Post_Types
 * @subpackage PeHaaThemes_Simple_Post_Types/admin
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PeHaaThemes_Simple_Post_Types
 * @subpackage PeHaaThemes_Simple_Post_Types/admin
 * @author     PeHaa THEMES <info@pehaa.com>
 */
class PeHaaThemes_Simple_Post_Types_Admin {

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
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	private $plugin_screen_hook_suffix = null;

	private $nonce;

	private $capabilities;

	private $submit = array(
		'add_post_type' => 'pehaathemes_spt_add_post_type',
		'add_taxonomy' => 'pehaathemes_spt_add_taxonomy',
		'edit_taxonomy' => 'edit_taxonomy' );

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string  $plugin_name The name of this plugin.
	 * @param string  $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->manage_page = $this->plugin_name;
		$this->nonce = array(
			'add_post_type' => $this->plugin_name . '_add_post_type',
			'add_taxonomy' => $this->plugin_name . '_add_taxonomy' ,
			'edit_taxonomy' => $this->plugin_name . '_edit_taxonomy' );
		$this->capabilities = apply_filters( $this->plugin_name . '_required_capabilities', 'edit_theme_options' );
		$this->options = PeHaaThemes_Simple_Post_Types::$options;

		$this->load_dependencies();

	}

	private function load_dependencies() {
		require_once 'class-pht-simple-post-types-validation.php';
	}

	public static function get_reserved_terms() {
		$for_post_type = get_post_types();
		array_push( $for_post_type, 'action', 'order', 'theme' );
		$for_taxonomy = get_taxonomies();
		array_push( $for_taxonomy,
			'attachment',
			'attachment_id',
			'author',
			'author_name',
			'calendar',
			'cat',
			'category',
			'category__and',
			'category__in',
			'category__not_in',
			'category_name',
			'comments_per_page',
			'comments_popup',
			'customize_messenger_channel',
			'customized',
			'cpage',
			'day',
			'debug',
			'error',
			'exact',
			'feed',
			'hour',
			'link_category',
			'm',
			'minute',
			'monthnum',
			'more',
			'name',
			'nav_menu',
			'nonce',
			'nopaging',
			'offset',
			'order',
			'orderby',
			'p',
			'page',
			'page_id',
			'paged',
			'pagename',
			'pb',
			'perm',
			'post',
			'post__in',
			'post__not_in',
			'post_format',
			'post_mime_type',
			'post_status',
			'post_tag',
			'post_type',
			'posts',
			'posts_per_archive_page',
			'posts_per_page',
			'preview',
			'robots',
			's',
			'search',
			'second',
			'sentence',
			'showposts',
			'static',
			'subpost',
			'subpost_id',
			'tag',
			'tag__and',
			'tag__in',
			'tag__not_in',
			'tag_id',
			'tag_slug__and',
			'tag_slug__in',
			'taxonomy',
			'tb',
			'term',
			'theme',
			'type',
			'w',
			'withcomments',
			'withoutcomments',
			'year'
		);
		return array(
			'post_type' => $for_post_type,
			'taxonomy' => $for_taxonomy,
		);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( $this->viewing_this_plugin() ) {
			wp_enqueue_style( $this->plugin_name . '-admin-style', plugin_dir_url( __FILE__ ) . 'css/simple-post-types-admin.min.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$notifications = $this->notifications();

		if ( $this->viewing_this_plugin() ) {
			wp_enqueue_script( $this->plugin_name . '-admin-script', plugin_dir_url( __FILE__ ) . 'js/simple-post-types-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-effects-core' ), $this->version, false );
			wp_localize_script(
				'jquery',
				'pehaathemes_spt_data',
				array(
					'reserved_terms' => self::get_reserved_terms(),
					'error_messages' => $notifications['error'],
					'confirmation'=> apply_filters( 'pehaathemes_spt_confirmation_question' , __( 'Are you sure you want to unregister this item?', $this->plugin_name ) ) )
			);
		}

	}

	/**
	 * Check if viewing one of this plugin's admin pages.
	 *
	 * @since   1.0.0
	 *
	 * @return  bool
	 */
	private function viewing_this_plugin() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return false;
		}
			
		$screen = get_current_screen();

		if ( !isset( $screen->id ) ) {
			return false;
		}

		if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) ) {
			return true;
		} 
		
		return false;
		
			
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 */
		$this->plugin_screen_hook_suffix[] = add_menu_page(
			__( 'Simple Post Types & Taxonomies', $this->plugin_name ),
			__( 'PHT SPTypes', $this->plugin_name ),
			'manage_options',
			$this->manage_page,
			array( $this, 'display_plugin_admin_page' ),
			'dashicons-welcome-add-page'
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		if ( ! current_user_can( $this->capabilities ) ) {
			return;
		}

		require_once 'class-pht-simple-post-types-display.php';
		$display = new PeHaaThemes_Simple_Post_Types_Admin_Display( $this->plugin_name, $this->version );
		include_once 'partials/pht-simple-post-types-admin-display.php';

	}

	public function update_options() {

		if ( ! current_user_can( $this->capabilities ) ) {
			return;
		}

		$this->update_options_on_add( 'post_type' );
		$this->update_options_on_add( 'taxonomy' );
		$this->update_options_on_remove( 'post_type' );
		$this->update_options_on_remove( 'taxonomy' );
		$this->update_options_on_edit_taxonomy();
	}

	private function redirect( $msg ) {
		wp_redirect( admin_url( 'admin.php?page=' . $this->manage_page . '&msg=' . $msg ) );
		exit();
	}

	private function update_database() {
		update_option( $this->options['slug'], $this->options['data'] );
	}

	private function update_options_on_add( $itemtype ) {

		if ( isset( $_POST[ $this->submit['add_' . $itemtype] ] ) ) {

			check_admin_referer( $this->nonce['add_' . $itemtype] );

			$validator = new PeHaaThemes_Simple_Post_Types_Validation( $this->plugin_name, $this->version );

			if ( $validator->validate( $_POST, 'add', $itemtype ) ) {

				$this->options['data'][ $itemtype ][ $validator->field['key'] ] = array(
					'name' => $validator->field['name'],
					'singular_name' => $validator->field['singular_name']
				);

				if ( 'taxonomy' === $itemtype ) {
					$this->options['data'][ $itemtype ][ $validator->field['key'] ]['object_types'] = $validator->field['phtspt-object-type'];
					$this->options['data'][ $itemtype ][ $validator->field['key'] ]['hierarchical'] = $validator->field['phtspt-hierarchical'];
				}

				$this->update_database();

			}

			$this->redirect( $validator->screen_message );

		}

	}

	private function update_options_on_edit_taxonomy() {

		if ( isset( $_POST[ $this->submit['edit_taxonomy'] ] ) && isset( $_POST['pehaathemes_spt_field']['key'] ) ) {

			check_admin_referer( $this->nonce['edit_taxonomy'] . '_' .  $_POST['pehaathemes_spt_field']['key'] );

			$validator = new PeHaaThemes_Simple_Post_Types_Validation( $this->plugin_name, $this->version );

			if ( $validator->validate( $_POST, 'edit', 'taxonomy' ) ) {

				$this->options['data']['taxonomy'][ $validator->field['key'] ]['object_types'] = $validator->field['phtspt-object-type'];
				$this->options['data']['taxonomy'][ $validator->field['key'] ]['hierarchical'] = $validator->field['phtspt-hierarchical'];

				$this->update_database();

			}

			$this->redirect( $validator->screen_message );

		}

	}

	private function update_options_on_remove( $itemtype ) {

		if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], $this->plugin_name .'_' . $itemtype . '_delete_nonce' ) ) {

			$validator = new PeHaaThemes_Simple_Post_Types_Validation( $this->plugin_name, $this->version );

			if ( $validator->validate( $_GET, 'delete', $itemtype ) ) {

				unset( $this->options['data'][ $itemtype ][ $validator->field['key'] ] );

				if ( 'post_type' === $itemtype ) {
					foreach ( $this->options['data']['taxonomy'] as $key => $array ) {
						if ( is_array( $array['object_types'] ) && in_array( $validator->field['key'], $array['object_types'] ) ) {
							unset( $this->options['data']['taxonomy'][ $key ]['object_types'][ array_search( $validator->field['key'], $array['object_types'] ) ] );
						}
					}
				}

				$this->update_database();

			}

			$this->redirect( $validator->screen_message );
		}

	}

	/**
	 * Prints the notification
	 *
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function get_notification() {

		if ( isset( $_GET['msg'] ) ) :

			$notifications = $this->notifications();

			preg_match( '/\A(updated|error)-(\d+)\z/', $_GET['msg'], $matches );

			if ( $matches && isset( $notifications[ $matches[1] ][ $matches[2] ] ) ) { ?>
				<div id="message" class="phtspt-message <?php echo $matches[1]; ?>">
					<?php echo $notifications[ $matches[1] ][ $matches[2] ]; ?>
				</div>
				<?php if ( 'updated' === $matches[1] ) {
					flush_rewrite_rules();
				}
			}

		endif;
	}

	private function notifications() {

		return apply_filters( $this->plugin_name . '_notifications', array(
				'updated' => array(
					'1' => __( 'Custom Post Type has been successfully registered.', $this->plugin_name ),
					'2' => __( 'Custom Taxonomy has been successfully registered.', $this->plugin_name ),
					'3' => __( 'Custom Post Type has been successfully unregistered.', $this->plugin_name ),
					'4' => __( 'Custom Taxonomy has been successfully unregistered.', $this->plugin_name ),
					'5' => __( 'Custom Post Type has been successfully modified.', $this->plugin_name ),
					'6' => __( 'Custom Taxonomy has been successfully modified.', $this->plugin_name ),
				),
				'error' => array(
					'0' => __( 'The Custom Post Type/Taxonomy could not have been registered.', $this->plugin_name ),
					'1' => __( 'The Custom Post Type that you are trying to delete does not exist.', $this->plugin_name ),
					'2' => __( 'The Custom Taxonomy that you are trying to delete does not exist.', $this->plugin_name ),
					'3' => __( 'The Custom Post Type slug cannot contain other characters that digits, lowercase letters and the underscores.', $this->plugin_name ),
					'4' => __( 'The Custom Taxonomy slug cannot contain other characters that digits, lowercase letters and the underscores.', $this->plugin_name ),
					'5' => __( 'All required fields must be filled in.', $this->plugin_name ),
					'6' => __( 'The submitted Custom Post Type name already exists or is a reserved WordPress term and cannot be overridden.', $this->plugin_name ),
					'7' => __( 'The submitted Custom Taxonomy name already exists or is a reserved WordPress term and cannot be overridden.', $this->plugin_name ),
					'8' => __( 'The labels cannot contain other characters than letters, spaces, hyphens and underscores (max. length is 15 chars).', $this->plugin_name ),
					'9' => __( 'The Custom Post Type that you are trying to modify does not exist.', $this->plugin_name ),
					'10' => __( 'The Custom Taxonomy that you are trying to delete or modify does not exist.', $this->plugin_name ),
					'11' => __( 'Invalid object type.', $this->plugin_name ),
					'12' => __( 'Invalid type.', $this->plugin_name ),
				)
			) );
	}

}
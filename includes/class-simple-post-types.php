<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/pehaa/pht-simple-post-types
 * @since      1.0.0
 *
 * @package    PHT_Simple_Post_Types
 * @subpackage PHT_Simple_Post_Types/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PHT_Simple_Post_Types
 * @subpackage PHT_Simple_Post_Types/includes
 * @author     PeHaa THEMES <info@pehaa.com>
 */
class PHT_Simple_Post_Types {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PHT_Simple_Post_Types_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $options_slug = 'pht_simple_post_types';

	public static $options;

	public static $post_type_slugs = array();

	public static $taxonomy_slugs = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'pht-simple-post-types';
		$this->version = '1.0.0';

		self::$options = array(
			'slug' => $this->options_slug,
			'data' => array(
				'post_type' => $this->options( 'post_type' ),
				'taxonomy' => $this->options( 'taxonomy' ),
			),
		);

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

		add_action( 'init', array( $this, 'register_custom_taxonomies' ), 0 );
		add_action( 'init', array( $this, 'register_custom_post_types' ) );

		self::$post_type_slugs = array_keys( self::$options['data']['post_type'] );
		self::$taxonomy_slugs = array_keys( self::$options['data']['taxonomy'] );

		add_action( 'init', array( $this, 'conditional_flush_rewrire_rules_after_plugin_activate' ) );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-post-types-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-post-types-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-simple-post-types-admin.php';

		$this->loader = new PHT_Simple_Post_Types_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PHT_Simple_Post_Types_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new PHT_Simple_Post_Types_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new PHT_Simple_Post_Types_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'update_options' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$this->loader->add_filter( 'phtpb_available_post_types', $plugin_admin, 'add_custom_post_types_to_fpb' );


	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PHT_Simple_Post_Types_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	private function options( $itemtype, $default = array() ) {

		$options = get_option( $this->options_slug );
		
		if ( isset( $options[ $itemtype ] ) )
		
			return $options[ $itemtype ];
		
		else return $default;
		
	}

	/**
	 * Register custom post types retrieved from the plugin options array
	 *
	 * @since     1.0.0
	 */

	public function register_custom_post_types() {	
				
		$post_types_array = self::$options['data']['post_type'];

		if ( !is_array( $post_types_array ) ) return;
		
		foreach ( $post_types_array as $key => $array ) {
			
			$this->simple_post_type_register( $key, $array );
			
		}

	}

	private function simple_post_type_register( $key, $array ) {

		$args = $this->post_type_args( $key, $array );

		register_post_type( $key, $args );

	}

	public function register_custom_taxonomies() {	
				
		$taxonomies_array = self::$options['data']['taxonomy'];
		
		if ( !is_array( $taxonomies_array ) ) return;
		
		foreach ( $taxonomies_array as $key => $array ) {
			
			$this->simple_taxonomy_register( $key, $array );
			
		}	

	}

	private function simple_taxonomy_register( $key, $array ) {

		$args = $this->taxonomy_args( $key, $array );

		register_taxonomy( $key, $array['object_types'], $args );

	}

	private function post_type_labels( $key, $array ) {

		$labels = array(
			'name'                => $array['name'],
			'singular_name'       => $array['singular_name'],
			'menu_name'           => $array['name'],
			'name_admin_bar'	  => $array['singular_name'],
			'parent_item_colon'   => sprintf( __('Parent %s:', $this->plugin_name ),  $array['singular_name'] ),
			'all_items'           => sprintf( __( 'All %s', $this->plugin_name ), $array['name'] ),
			'view_item'           => sprintf( __( 'View %s', $this->plugin_name ),  $array['singular_name'] ),
			'add_new_item'        => sprintf( __( 'Add New %s', $this->plugin_name ),  $array['singular_name'] ),
			'add_new'             => __( 'Add New', $this->plugin_name ),
			'edit_item'           => sprintf( __( 'Edit %s', $this->plugin_name ), $array['singular_name'] ),
			'new_item'						=> sprintf( __( 'New %s', $this->plugin_name ),  $array['singular_name'] ),
			'search_items'        => sprintf( __( 'Search %s', $this->plugin_name ),  $array['singular_name'] ),
			'not_found'           => __( 'Not found', $this->plugin_name ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->plugin_name ),
			);

		return apply_filters( $key .'_phtspt_post_type_labels', $labels, $array );

	}

	private function post_type_args( $key, $array ) {

		$args = array(
			'labels' => $this->post_type_labels( $key, $array ),
			'description'         => '',
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => true,
			'menu_position'       => 5,
			'menu_icon'				=> NULL,
			'has_archive'         => true,
			'rewrite' => array( 
				'slug' => $key,
				'with_front' => true
			 ),
			'query_var' => true
		);

		return apply_filters( $key .'_phtspt_post_type_args', $args, $key, $array );
		
	}

	private function taxonomy_labels( $key, $array ) {

		$name = ucfirst( isset( $array['name'] ) ? $array['name'] : 'taxonomies' );
		$singular_name = ucfirst( isset( $array['singular_name'] ) ? $array['singular_name'] : 'taxonomy' );

		$labels = array(
			'name'                => $name,
			'singular_name'       => $singular_name,
			'menu_name'           => $name,
			'parent_item'					=> sprintf( __( 'Parent %s', $this->plugin_name ), $singular_name ),
			'parent_item_colon'   => sprintf( __( 'Parent %s:', $this->plugin_name ), $singular_name ),
			'all_items'           => sprintf( __( 'All %s', $this->plugin_name ), $name ),
			'view_item'           => sprintf( __( 'View %s', $this->plugin_name ), $singular_name ),
			'add_new_item'        => sprintf( __( 'Add New %s', $this->plugin_name ), $singular_name ),
			'add_new'             => __( 'Add New', $this->plugin_name ),
			'edit_item'           => sprintf( __( 'Edit %s', $this->plugin_name ), $singular_name ),
			'update_item'         => sprintf(  __( 'Update %s', $this->plugin_name ), $singular_name ),
			'search_items'        => sprintf( __( 'Search %s', $this->plugin_name ), $singular_name ),
			'not_found'           => __( 'Not found', $this->plugin_name ),
			'not_found_in_trash'  => __( 'Not found in Trash', $this->plugin_name ),
			'new_item_name'              => sprintf( __( 'New %s Name', $this->plugin_name ), $name ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', $this->plugin_name ), $name ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', $this->plugin_name ), $name ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', $this->plugin_name ), $name )
			);

		return apply_filters( $key .'_phtspt_taxonomy_labels', $labels, $array );

	}

	private function taxonomy_args( $key, $array ) {

		$args = array(
				'labels' => $this->taxonomy_labels( $key , $array ),
				'hierarchical' => isset( $array['hierarchical'] ) && 'yes' === $array['hierarchical'] ? true : false,
				'public' => true,
				'show_ui'             => true,
				'show_in_nav_menus'   => true,
				'show_tagcloud' => true,
				'query_var' => true,
				'rewrite' => array( 
					'slug' => $key,
					'with_front' => true,
					'hierarchical' => false,
				 )
			);

		return apply_filters( $key .'_phtspt_taxonomy_args', $args, $key, $array );
		
	}

	public function conditional_flush_rewrire_rules_after_plugin_activate() {
		
		if ( get_option( PHT_SPT_ACTIVATION_DATA_FIELD ) ) {
			update_option( PHT_SPT_ACTIVATION_DATA_FIELD, false );
			flush_rewrite_rules();			
		}
		
	}
	
}
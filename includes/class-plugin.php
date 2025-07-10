<?php
/**
 * Main Plugin Class
 *
 * @package PostGrid
 * @since 1.0.0
 */

namespace PostGrid;

use PostGrid\Core\AssetManager;
use PostGrid\Core\HooksManager;
use PostGrid\Blocks\BlockRegistry;
use PostGrid\Api\RestController;
use PostGrid\Compatibility\LegacySupport;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class Plugin {
	
	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	private static $instance = null;
	
	/**
	 * Asset manager instance
	 *
	 * @var AssetManager
	 */
	private $assets;
	
	/**
	 * Hooks manager instance
	 *
	 * @var HooksManager
	 */
	private $hooks;
	
	/**
	 * Block registry instance
	 *
	 * @var BlockRegistry
	 */
	private $blocks;
	
	/**
	 * REST API controller instance
	 *
	 * @var RestController
	 */
	private $api;
	
	/**
	 * Legacy support instance
	 *
	 * @var LegacySupport
	 */
	private $legacy;
	
	/**
	 * Get plugin instance
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_components();
		$this->init_hooks();
	}
	
	/**
	 * Initialize plugin components
	 */
	private function init_components() {
		$this->assets = new AssetManager();
		$this->hooks = new HooksManager();
		$this->blocks = new BlockRegistry();
		$this->api = new RestController();
		$this->legacy = new LegacySupport();
	}
	
	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Core hooks
		add_action( 'init', array( $this, 'init' ), 5 );
		add_action( 'rest_api_init', array( $this->api, 'register_routes' ) );
		
		// Asset hooks
		add_action( 'enqueue_block_editor_assets', array( $this->assets, 'enqueue_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this->assets, 'enqueue_frontend_assets' ) );
		
		// Allow other plugins to hook in
		do_action( 'postgrid_init', $this );
	}
	
	/**
	 * Initialize plugin
	 */
	public function init() {
		// Load text domain
		load_plugin_textdomain( 'postgrid', false, dirname( plugin_basename( POSTGRID_PLUGIN_FILE ) ) . '/languages' );
		
		// Register blocks
		$this->blocks->register();
		
		// Initialize legacy support
		$this->legacy->init();
		
		// Fire action for extensions
		do_action( 'postgrid_loaded' );
	}
	
	/**
	 * Get component instance
	 *
	 * @param string $component Component name.
	 * @return object|null
	 */
	public function get_component( $component ) {
		if ( property_exists( $this, $component ) ) {
			return $this->$component;
		}
		
		return null;
	}
	
	/**
	 * Get hooks manager
	 *
	 * @return HooksManager
	 */
	public function hooks() {
		return $this->hooks;
	}
	
	/**
	 * Get block registry
	 *
	 * @return BlockRegistry
	 */
	public function blocks() {
		return $this->blocks;
	}
	
	/**
	 * Get API controller
	 *
	 * @return RestController
	 */
	public function api() {
		return $this->api;
	}
}

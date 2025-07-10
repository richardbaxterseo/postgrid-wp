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
 * Main plugin class implementing proper singleton pattern
 */
class Plugin {
	
	/**
	 * Plugin instance
	 *
	 * @var Plugin|null
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
	 * Plugin initialization state
	 *
	 * @var bool
	 */
	private $initialized = false;
	
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
	 * Constructor - private to enforce singleton
	 */
	private function __construct() {
		// Prevent creating multiple instances
		if ( null !== self::$instance ) {
			return self::$instance;
		}
		
		// Initialize on the next tick to ensure WordPress is ready
		add_action( 'init', array( $this, 'initialize' ), 0 );
	}
	
	/**
	 * Prevent cloning
	 */
	private function __clone() {
		_doing_it_wrong( 
			__FUNCTION__, 
			esc_html__( 'PostGrid Plugin class is a singleton and should not be cloned.', 'postgrid' ),
			'0.1.6'
		);
	}
	
	/**
	 * Prevent unserializing
	 */
	public function __wakeup() {
		_doing_it_wrong( 
			__FUNCTION__, 
			esc_html__( 'PostGrid Plugin class is a singleton and should not be unserialized.', 'postgrid' ),
			'0.1.6'
		);
	}
	
	/**
	 * Initialize the plugin
	 */
	public function initialize() {
		// Prevent double initialization
		if ( $this->initialized ) {
			return;
		}
		
		$this->initialized = true;
		
		// Load text domain early
		$this->load_textdomain();
		
		// Initialize components with error handling
		$this->init_components();
		
		// Only proceed if components initialized successfully
		if ( $this->components_loaded() ) {
			$this->init_hooks();
			
			// Fire action for extensions
			do_action( 'postgrid_loaded', $this );
		}
	}
	
	/**
	 * Load plugin text domain
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 
			'postgrid', 
			false, 
			dirname( plugin_basename( POSTGRID_PLUGIN_FILE ) ) . '/languages' 
		);
	}
	
	/**
	 * Initialize plugin components
	 */
	private function init_components() {
		try {
			// Initialize core components
			$this->assets = new AssetManager();
			$this->hooks = new HooksManager();
			$this->blocks = new BlockRegistry();
			$this->api = new RestController();
			$this->legacy = new LegacySupport();
			
		} catch ( \Exception $e ) {
			// Log the error
			error_log( 'PostGrid: Failed to initialize components - ' . $e->getMessage() );
			
			// Show admin notice
			add_action( 'admin_notices', array( $this, 'show_initialization_error' ), 10, 1 );
			
			// Set components to null to indicate failure
			$this->assets = null;
			$this->hooks = null;
			$this->blocks = null;
			$this->api = null;
			$this->legacy = null;
		}
	}
	
	/**
	 * Check if all components loaded successfully
	 *
	 * @return bool
	 */
	private function components_loaded() {
		return ( 
			$this->assets instanceof AssetManager &&
			$this->hooks instanceof HooksManager &&
			$this->blocks instanceof BlockRegistry &&
			$this->api instanceof RestController &&
			$this->legacy instanceof LegacySupport
		);
	}
	
	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Core hooks
		add_action( 'init', array( $this, 'on_init' ), 5 );
		add_action( 'rest_api_init', array( $this->api, 'register_routes' ) );
		
		// Asset hooks
		add_action( 'enqueue_block_editor_assets', array( $this->assets, 'enqueue_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this->assets, 'enqueue_frontend_assets' ) );
		
		// Admin hooks
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
		
		// Allow other plugins to hook in
		do_action( 'postgrid_init', $this );
	}
	
	/**
	 * WordPress init hook callback
	 */
	public function on_init() {
		// Register blocks
		if ( $this->blocks ) {
			$this->blocks->register();
		}
		
		// Initialize legacy support
		if ( $this->legacy ) {
			$this->legacy->init();
		}
		
		// Register post type support
		$this->register_post_type_support();
		
		// Fire action for extensions
		do_action( 'postgrid_ready' );
	}
	
	/**
	 * Register post type support
	 */
	private function register_post_type_support() {
		$supported_types = get_option( 'postgrid_supported_post_types', array( 'post' ) );
		
		foreach ( $supported_types as $post_type ) {
			add_post_type_support( $post_type, 'postgrid' );
		}
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'PostGrid Settings', 'postgrid' ),
			__( 'PostGrid', 'postgrid' ),
			'manage_options',
			'postgrid-settings',
			array( $this, 'render_settings_page' )
		);
	}
	
	/**
	 * Register plugin settings
	 */
	public function register_settings() {
		register_setting( 'postgrid_settings', 'postgrid_cache_expiration' );
		register_setting( 'postgrid_settings', 'postgrid_enable_rest_api' );
		register_setting( 'postgrid_settings', 'postgrid_supported_post_types' );
		register_setting( 'postgrid_settings', 'postgrid_rate_limit' );
	}
	
	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'postgrid_settings' );
				do_settings_sections( 'postgrid_settings' );
				?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="postgrid_cache_expiration">
								<?php esc_html_e( 'Cache Expiration', 'postgrid' ); ?>
							</label>
						</th>
						<td>
							<input type="number" 
								id="postgrid_cache_expiration" 
								name="postgrid_cache_expiration" 
								value="<?php echo esc_attr( get_option( 'postgrid_cache_expiration', 300 ) ); ?>" 
								min="0" 
								step="60" />
							<p class="description">
								<?php esc_html_e( 'Cache expiration time in seconds.', 'postgrid' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Show initialization error
	 */
	public function show_initialization_error() {
		?>
		<div class="notice notice-error is-dismissible">
			<p>
				<strong><?php esc_html_e( 'PostGrid Error:', 'postgrid' ); ?></strong>
				<?php esc_html_e( 'Failed to initialize plugin components. Please check the error log for details.', 'postgrid' ); ?>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Get component instance
	 *
	 * @param string $component Component name.
	 * @return object|null
	 */
	public function get_component( $component ) {
		if ( property_exists( $this, $component ) && $this->$component !== null ) {
			return $this->$component;
		}
		
		return null;
	}
	
	/**
	 * Get hooks manager
	 *
	 * @return HooksManager|null
	 */
	public function hooks() {
		return $this->hooks;
	}
	
	/**
	 * Get block registry
	 *
	 * @return BlockRegistry|null
	 */
	public function blocks() {
		return $this->blocks;
	}
	
	/**
	 * Get API controller
	 *
	 * @return RestController|null
	 */
	public function api() {
		return $this->api;
	}
	
	/**
	 * Get asset manager
	 *
	 * @return AssetManager|null
	 */
	public function assets() {
		return $this->assets;
	}
	
	/**
	 * Get legacy support
	 *
	 * @return LegacySupport|null
	 */
	public function legacy() {
		return $this->legacy;
	}
	
	/**
	 * Check if plugin is initialized
	 *
	 * @return bool
	 */
	public function is_initialized() {
		return $this->initialized && $this->components_loaded();
	}
}

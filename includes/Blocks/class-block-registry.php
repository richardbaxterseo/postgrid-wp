<?php
/**
 * Block Registry
 *
 * @package PostGrid
 * @since 1.0.0
 */

namespace PostGrid\Blocks;

use PostGrid\Core\HooksManager;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles block registration
 */
class BlockRegistry {
	
	/**
	 * Registered blocks
	 *
	 * @var array
	 */
	private $blocks = array();
	
	/**
	 * Block renderer instance
	 *
	 * @var BlockRenderer
	 */
	private $renderer;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->renderer = new BlockRenderer();
	}
	
	/**
	 * Register blocks
	 */
	public function register() {
		// Register main PostGrid block
		$this->register_postgrid_block();
		
		// Allow additional blocks to be registered
		do_action( 'postgrid_register_blocks', $this );
	}
	
	/**
	 * Register PostGrid block
	 */
	private function register_postgrid_block() {
		$block_json_path = POSTGRID_PLUGIN_DIR . 'block.json';
		
		if ( ! file_exists( $block_json_path ) ) {
			error_log( 'PostGrid: block.json not found at ' . $block_json_path );
			return;
		}
		
		// Get block metadata
		$metadata = wp_json_file_decode( $block_json_path, array( 'associative' => true ) );
		
		// Allow filtering block attributes
		$metadata['attributes'] = apply_filters( 'postgrid_block_attributes', $metadata['attributes'] );
		
		// Register block type
		$block_type = register_block_type( $block_json_path, array(
			'render_callback' => array( $this->renderer, 'render' ),
			'attributes' => $metadata['attributes'],
		) );
		
		if ( $block_type ) {
			$this->blocks['postgrid/postgrid'] = $block_type;
			
			// Log successful registration
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PostGrid: Successfully registered block postgrid/postgrid' );
			}
		}
	}
	
	/**
	 * Register a custom block
	 *
	 * @param string $name Block name.
	 * @param array  $args Block arguments.
	 * @return WP_Block_Type|false
	 */
	public function register_block( $name, $args ) {
		// Ensure render callback uses our renderer
		if ( ! isset( $args['render_callback'] ) ) {
			$args['render_callback'] = array( $this->renderer, 'render' );
		}
		
		$block_type = register_block_type( $name, $args );
		
		if ( $block_type ) {
			$this->blocks[ $name ] = $block_type;
		}
		
		return $block_type;
	}
	
	/**
	 * Get registered blocks
	 *
	 * @return array
	 */
	public function get_blocks() {
		return $this->blocks;
	}
	
	/**
	 * Check if block is registered
	 *
	 * @param string $name Block name.
	 * @return bool
	 */
	public function is_registered( $name ) {
		return isset( $this->blocks[ $name ] );
	}
	
	/**
	 * Get block renderer
	 *
	 * @return BlockRenderer
	 */
	public function get_renderer() {
		return $this->renderer;
	}
}

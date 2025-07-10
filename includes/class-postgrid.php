<?php
namespace PostGrid;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main PostGrid class
 */
class PostGrid {
	
	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Register block type from block.json
		$block_json_path = POSTGRID_PLUGIN_DIR . 'block.json';
		
		// Ensure the block is registered with proper script handling
		$result = register_block_type( $block_json_path, array(
			'render_callback' => array( $this, 'render_block' ),
		) );
		
		// If registration failed, log error
		if ( ! $result ) {
			error_log( 'PostGrid: Failed to register block type from ' . $block_json_path );
		} else {
			error_log( 'PostGrid: Successfully registered block type postgrid/postgrid' );
		}
		
		// Register REST API endpoint
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		
		// Ensure frontend styles are loaded
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
		
		// Ensure editor assets are loaded properly
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ), 5 );
		
		// Add support for caxton/posts-grid blocks
		add_action( 'init', array( $this, 'register_caxton_compatibility' ), 20 );
	}
	
	/**
	 * Register Caxton compatibility
	 */
	public function register_caxton_compatibility() {
		// Check if caxton/posts-grid is being used
		if ( ! \WP_Block_Type_Registry::get_instance()->is_registered( 'caxton/posts-grid' ) ) {
			// Register caxton/posts-grid as an alias to postgrid/postgrid
			register_block_type( 'caxton/posts-grid', array(
				'render_callback' => array( $this, 'render_caxton_block' ),
				'attributes' => array(
					'postsPerPage' => array(
						'type' => 'number',
						'default' => 6
					),
					'orderBy' => array(
						'type' => 'string',
						'default' => 'date'
					),
					'order' => array(
						'type' => 'string',
						'default' => 'desc'
					),
					'selectedCategory' => array(
						'type' => 'number',
						'default' => 0
					),
					'columns' => array(
						'type' => 'number',
						'default' => 3
					),
					'showDate' => array(
						'type' => 'boolean',
						'default' => true
					),
					'showExcerpt' => array(
						'type' => 'boolean',
						'default' => true
					)
				),
			) );
		}
	}
	
	/**
	 * Render Caxton block (wrapper for render_block)
	 */
	public function render_caxton_block( $attributes ) {
		return $this->render_block( $attributes );
	}
	
	/**
	 * Enqueue editor assets
	 * This is a fallback in case block.json doesn't load the scripts properly
	 */
	public function enqueue_editor_assets() {
		// Only enqueue if not already loaded by block.json
		if ( ! wp_script_is( 'postgrid-postgrid-editor-script', 'enqueued' ) ) {
			$script_dir = file_exists( POSTGRID_PLUGIN_DIR . 'build/index.js' ) ? 'build' : 'src';
			
			// Register and enqueue editor script with proper dependencies
			wp_enqueue_script(
				'postgrid-editor-fallback',
				POSTGRID_PLUGIN_URL . $script_dir . '/index.js',
				array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-i18n', 'wp-server-side-render' ),
				POSTGRID_VERSION,
				true
			);
		}
	}
	
	/**
	 * Enqueue frontend styles
	 */
	public function enqueue_frontend_styles() {
		// Check for both postgrid and caxton blocks
		if ( has_block( 'postgrid/postgrid' ) || has_block( 'caxton/posts-grid' ) ) {
			$style_dir = file_exists( POSTGRID_PLUGIN_DIR . 'build/style-index.css' ) ? 'build' : 'src';
			$style_file = $style_dir === 'build' ? 'style-index.css' : 'style.css';
			
			wp_enqueue_style(
				'postgrid-frontend-styles',
				POSTGRID_PLUGIN_URL . $style_dir . '/' . $style_file,
				array(),
				POSTGRID_VERSION
			);
		}
	}
	
	/**
	 * Register REST API routes
	 */
	public function register_rest_routes() {
		register_rest_route( 'postgrid/v1', '/posts', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_posts_endpoint' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'per_page' => array(
					'type'    => 'integer',
					'default' => 6,
				),
				'orderby' => array(
					'type'    => 'string',
					'default' => 'date',
				),
				'order' => array(
					'type'    => 'string',
					'default' => 'desc',
				),
				'category' => array(
					'type'    => 'integer',
					'default' => 0,
				),
			),
		) );
	}
	
	/**
	 * REST API endpoint callback
	 */
	public function get_posts_endpoint( $request ) {
		$args = array(
			'posts_per_page' => $request->get_param( 'per_page' ),
			'orderby'        => $request->get_param( 'orderby' ),
			'order'          => $request->get_param( 'order' ),
			'post_status'    => 'publish',
		);
		
		if ( $request->get_param( 'category' ) > 0 ) {
			$args['cat'] = absint( $request->get_param( 'category' ) );
		}
		
		$posts = get_posts( $args );
		$data = array();
		
		foreach ( $posts as $post ) {
			$data[] = array(
				'id'      => $post->ID,
				'title'   => get_the_title( $post ),
				'excerpt' => get_the_excerpt( $post ),
				'link'    => get_permalink( $post ),
				'date'    => get_the_date( '', $post ),
			);
		}
		
		return rest_ensure_response( $data );
	}
	
	/**
	 * Render the block
	 */
	public function render_block( $attributes ) {
		// Ensure styles are loaded for dynamic blocks
		$this->ensure_styles_loaded();
		
		$args = array(
			'posts_per_page' => $attributes['postsPerPage'] ?? 6,
			'orderby'        => $attributes['orderBy'] ?? 'date',
			'order'          => $attributes['order'] ?? 'desc',
			'post_status'    => 'publish',
		);
		
		if ( ! empty( $attributes['selectedCategory'] ) ) {
			$args['cat'] = absint( $attributes['selectedCategory'] );
		}
		
		$posts = get_posts( $args );
		
		if ( empty( $posts ) ) {
			return '<p>' . esc_html__( 'No posts found.', 'postgrid' ) . '</p>';
		}
		
		$columns = $attributes['columns'] ?? 3;
		$show_date = $attributes['showDate'] ?? true;
		$show_excerpt = $attributes['showExcerpt'] ?? true;
		
		$output = '<div class="wp-block-postgrid columns-' . esc_attr( $columns ) . '">';
		
		foreach ( $posts as $post ) {
			$output .= '<article class="wp-block-postgrid__item">';
			$output .= '<h3 class="wp-block-postgrid__title">';
			$output .= '<a href="' . esc_url( get_permalink( $post ) ) . '">';
			$output .= esc_html( get_the_title( $post ) );
			$output .= '</a></h3>';
			
			if ( $show_date ) {
				$output .= '<time class="wp-block-postgrid__date">';
				$output .= esc_html( get_the_date( '', $post ) );
				$output .= '</time>';
			}
			
			if ( $show_excerpt ) {
				$output .= '<div class="wp-block-postgrid__excerpt">';
				$output .= wp_kses_post( get_the_excerpt( $post ) );
				$output .= '</div>';
			}
			
			$output .= '</article>';
		}
		
		$output .= '</div>';
		
		return $output;
	}
	
	/**
	 * Ensure styles are loaded for dynamic block rendering
	 */
	private function ensure_styles_loaded() {
		if ( ! wp_style_is( 'postgrid-frontend-styles', 'enqueued' ) ) {
			$style_dir = file_exists( POSTGRID_PLUGIN_DIR . 'build/style-index.css' ) ? 'build' : 'src';
			$style_file = $style_dir === 'build' ? 'style-index.css' : 'style.css';
			
			wp_enqueue_style(
				'postgrid-frontend-styles',
				POSTGRID_PLUGIN_URL . $style_dir . '/' . $style_file,
				array(),
				POSTGRID_VERSION
			);
		}
	}
}

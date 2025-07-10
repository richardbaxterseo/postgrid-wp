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
		// Register block
		register_block_type( POSTGRID_PLUGIN_DIR . 'block.json', array(
			'render_callback' => array( $this, 'render_block' ),
		) );
		
		// Register REST API endpoint
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
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
}

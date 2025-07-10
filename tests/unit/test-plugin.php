<?php
/**
 * Class Test_PostGrid_Plugin
 *
 * @package PostGrid
 */

/**
 * Test main plugin functionality
 */
class Test_PostGrid_Plugin extends WP_UnitTestCase {
	
	/**
	 * Test that plugin instance is created
	 */
	public function test_plugin_instance() {
		$this->assertInstanceOf( 'PostGrid\Plugin', postgrid() );
	}
	
	/**
	 * Test that hooks are registered
	 */
	public function test_hooks_registered() {
		$this->assertEquals( 10, has_action( 'init', array( postgrid(), 'init' ) ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', array( postgrid()->api(), 'register_routes' ) ) );
	}
	
	/**
	 * Test block registration
	 */
	public function test_block_registration() {
		do_action( 'init' );
		
		$this->assertTrue( WP_Block_Type_Registry::get_instance()->is_registered( 'postgrid/postgrid' ) );
	}
	
	/**
	 * Test cache manager
	 */
	public function test_cache_manager() {
		$cache = new PostGrid\Core\CacheManager();
		
		// Test set and get
		$cache->set( 'test_key', 'test_value', 60 );
		$this->assertEquals( 'test_value', $cache->get( 'test_key' ) );
		
		// Test delete
		$cache->delete( 'test_key' );
		$this->assertFalse( $cache->get( 'test_key' ) );
	}
	
	/**
	 * Test query filters
	 */
	public function test_query_filters() {
		$args = array(
			'posts_per_page' => 10,
			'post_type' => 'post',
		);
		
		// Add a test filter
		add_filter( 'postgrid_query_args', function( $args ) {
			$args['posts_per_page'] = 5;
			return $args;
		} );
		
		$filtered_args = PostGrid\Core\HooksManager::apply_query_filters( $args, array() );
		
		$this->assertEquals( 5, $filtered_args['posts_per_page'] );
	}
	
	/**
	 * Test supported post types filter
	 */
	public function test_supported_post_types() {
		// Default should only include 'post'
		$types = PostGrid\Core\HooksManager::get_supported_post_types();
		$this->assertEquals( array( 'post' ), $types );
		
		// Test filter
		add_filter( 'postgrid_supported_post_types', function( $types ) {
			$types[] = 'page';
			return $types;
		} );
		
		$types = PostGrid\Core\HooksManager::get_supported_post_types();
		$this->assertContains( 'page', $types );
	}
	
	/**
	 * Test legacy support
	 */
	public function test_legacy_support() {
		$legacy = new PostGrid\Compatibility\LegacySupport();
		
		// Test shortcode registration
		$legacy->register_legacy_shortcodes();
		
		$this->assertTrue( shortcode_exists( 'caxton/posts' ) );
		$this->assertTrue( shortcode_exists( 'postgrid' ) );
	}
	
	/**
	 * Test REST API endpoints
	 */
	public function test_rest_endpoints() {
		do_action( 'rest_api_init' );
		
		$routes = rest_get_server()->get_routes();
		
		$this->assertArrayHasKey( '/postgrid/v1/posts', $routes );
		$this->assertArrayHasKey( '/postgrid/v1/categories', $routes );
		$this->assertArrayHasKey( '/postgrid/v1/post-types', $routes );
	}
	
	/**
	 * Test block rendering
	 */
	public function test_block_rendering() {
		// Create test posts
		$post_ids = $this->factory->post->create_many( 3 );
		
		$renderer = new PostGrid\Blocks\BlockRenderer();
		
		$attributes = array(
			'postsPerPage' => 3,
			'columns' => 3,
			'showDate' => true,
			'showExcerpt' => true,
		);
		
		$output = $renderer->render( $attributes );
		
		// Check that output contains expected elements
		$this->assertStringContainsString( 'wp-block-postgrid', $output );
		$this->assertStringContainsString( 'columns-3', $output );
		$this->assertStringContainsString( 'wp-block-postgrid__item', $output );
	}
}

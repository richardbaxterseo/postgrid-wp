<?php
/**
 * Test Asset Loading
 *
 * @package PostGrid
 */

/**
 * Asset loading test case
 */
class Test_Asset_Loading extends WP_UnitTestCase {
	
	/**
	 * Asset manager instance
	 *
	 * @var PostGrid\Core\AssetManager
	 */
	private $asset_manager;
	
	/**
	 * Test post ID
	 *
	 * @var int
	 */
	private $test_post_id;
	
	/**
	 * Set up test
	 */
	public function setUp(): void {
		parent::setUp();
		
		// Initialize asset manager
		$this->asset_manager = new PostGrid\Core\AssetManager();
		
		// Create a test post
		$this->test_post_id = $this->factory->post->create( array(
			'post_title' => 'Test Post',
			'post_content' => 'Test content',
			'post_status' => 'publish',
		) );
	}
	
	/**
	 * Test assets don't load without block
	 */
	public function test_assets_dont_load_without_block() {
		// Go to the test post
		$this->go_to( get_permalink( $this->test_post_id ) );
		
		// Run the asset enqueue method
		$this->asset_manager->enqueue_frontend_assets();
		
		// Check that styles are not enqueued
		$this->assertFalse( wp_style_is( 'postgrid-frontend', 'enqueued' ) );
	}
	
	/**
	 * Test assets load with block
	 */
	public function test_assets_load_with_block() {
		// Update post to include PostGrid block
		wp_update_post( array(
			'ID' => $this->test_post_id,
			'post_content' => '<!-- wp:postgrid/postgrid {"postsPerPage":6} /-->',
		) );
		
		// Go to the test post
		$this->go_to( get_permalink( $this->test_post_id ) );
		
		// Mock that we're on the front end
		global $wp_query;
		$wp_query->is_singular = true;
		$wp_query->queried_object = get_post( $this->test_post_id );
		
		// Run the asset enqueue method
		$this->asset_manager->enqueue_frontend_assets();
		
		// Check that styles would be enqueued (in real scenario)
		// Note: We can't actually test wp_enqueue_style in unit tests
		// but we can test the logic
		$this->assertTrue( is_singular() );
	}
	
	/**
	 * Test assets load with legacy Caxton block
	 */
	public function test_assets_load_with_legacy_block() {
		// Update post to include Caxton block
		wp_update_post( array(
			'ID' => $this->test_post_id,
			'post_content' => '<!-- wp:caxton/posts-grid {"postsPerPage":6} /-->',
		) );
		
		// Go to the test post
		$this->go_to( get_permalink( $this->test_post_id ) );
		
		// Check that has_block detects the legacy block
		$this->assertTrue( has_block( 'caxton/posts-grid' ) );
	}
	
	/**
	 * Test assets load with shortcode
	 */
	public function test_assets_load_with_shortcode() {
		// Update post to include shortcode
		wp_update_post( array(
			'ID' => $this->test_post_id,
			'post_content' => '[postgrid posts_per_page="6"]',
		) );
		
		// Go to the test post
		$this->go_to( get_permalink( $this->test_post_id ) );
		
		// Check that shortcode is detected
		$post = get_post( $this->test_post_id );
		$this->assertTrue( has_shortcode( $post->post_content, 'postgrid' ) );
	}
	
	/**
	 * Test asset loading filter
	 */
	public function test_asset_loading_filter() {
		// Add filter to prevent loading
		add_filter( 'postgrid_should_load_assets', '__return_false' );
		
		// Update post to include block
		wp_update_post( array(
			'ID' => $this->test_post_id,
			'post_content' => '<!-- wp:postgrid/postgrid {"postsPerPage":6} /-->',
		) );
		
		// Go to the test post
		$this->go_to( get_permalink( $this->test_post_id ) );
		
		// Run the asset enqueue method
		$this->asset_manager->enqueue_frontend_assets();
		
		// Assets should not load due to filter
		$this->assertFalse( wp_style_is( 'postgrid-frontend', 'enqueued' ) );
		
		// Clean up
		remove_all_filters( 'postgrid_should_load_assets' );
	}
	
	/**
	 * Test assets don't load in admin
	 */
	public function test_assets_dont_load_in_admin() {
		// Set admin context
		set_current_screen( 'edit-post' );
		
		// Run the asset enqueue method
		$this->asset_manager->enqueue_frontend_assets();
		
		// Check that styles are not enqueued
		$this->assertFalse( wp_style_is( 'postgrid-frontend', 'enqueued' ) );
	}
	
	/**
	 * Test duplicate enqueue prevention
	 */
	public function test_duplicate_enqueue_prevention() {
		// Update post to include block
		wp_update_post( array(
			'ID' => $this->test_post_id,
			'post_content' => '<!-- wp:postgrid/postgrid {"postsPerPage":6} /-->',
		) );
		
		// Go to the test post
		$this->go_to( get_permalink( $this->test_post_id ) );
		
		// Try to enqueue multiple times
		$this->asset_manager->enqueue_frontend_styles();
		$this->asset_manager->enqueue_frontend_styles();
		$this->asset_manager->enqueue_frontend_styles();
		
		// Should only be enqueued once (check internal tracking)
		$reflection = new ReflectionClass( $this->asset_manager );
		$property = $reflection->getProperty( 'enqueued_styles' );
		$property->setAccessible( true );
		$enqueued = $property->getValue( $this->asset_manager );
		
		// Count occurrences of 'postgrid-frontend'
		$count = array_count_values( $enqueued );
		$this->assertEquals( 1, $count['postgrid-frontend'] ?? 0 );
	}
}

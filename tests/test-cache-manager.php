<?php
/**
 * Test Cache Manager
 *
 * @package PostGrid
 */

/**
 * Cache manager test case
 */
class Test_Cache_Manager extends WP_UnitTestCase {
	
	/**
	 * Cache manager instance
	 *
	 * @var PostGrid\Core\CacheManager
	 */
	private $cache;
	
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
		
		// Initialize cache manager
		$this->cache = new PostGrid\Core\CacheManager();
		
		// Create test post
		$this->test_post_id = $this->factory->post->create( array(
			'post_title' => 'Test Post',
			'post_status' => 'publish',
		) );
	}
	
	/**
	 * Tear down test
	 */
	public function tearDown(): void {
		parent::tearDown();
		
		// Flush cache
		$this->cache->flush();
	}
	
	/**
	 * Test cache get and set
	 */
	public function test_cache_get_and_set() {
		$key = 'postgrid_test_key';
		$data = array( 'foo' => 'bar', 'count' => 42 );
		
		// Set cache
		$result = $this->cache->set( $key, $data, 300 );
		$this->assertTrue( $result );
		
		// Get cache
		$cached = $this->cache->get( $key );
		$this->assertEquals( $data, $cached );
	}
	
	/**
	 * Test cache expiration
	 */
	public function test_cache_expiration() {
		$key = 'postgrid_test_expiry';
		$data = 'test data';
		
		// Set cache with 1 second expiration
		$this->cache->set( $key, $data, 1 );
		
		// Should exist immediately
		$this->assertNotFalse( $this->cache->get( $key ) );
		
		// Wait for expiration
		sleep( 2 );
		
		// Should be expired
		$this->assertFalse( $this->cache->get( $key ) );
	}
	
	/**
	 * Test cache delete
	 */
	public function test_cache_delete() {
		$key = 'postgrid_test_delete';
		$data = 'delete me';
		
		// Set cache
		$this->cache->set( $key, $data );
		
		// Verify it exists
		$this->assertEquals( $data, $this->cache->get( $key ) );
		
		// Delete it
		$result = $this->cache->delete( $key );
		$this->assertTrue( $result );
		
		// Verify it's gone
		$this->assertFalse( $this->cache->get( $key ) );
	}
	
	/**
	 * Test cache flush
	 */
	public function test_cache_flush() {
		// Set multiple cache entries
		$this->cache->set( 'postgrid_test_1', 'data1' );
		$this->cache->set( 'postgrid_test_2', 'data2' );
		$this->cache->set( 'postgrid_test_3', 'data3' );
		
		// Verify they exist
		$this->assertNotFalse( $this->cache->get( 'postgrid_test_1' ) );
		$this->assertNotFalse( $this->cache->get( 'postgrid_test_2' ) );
		$this->assertNotFalse( $this->cache->get( 'postgrid_test_3' ) );
		
		// Flush cache
		$result = $this->cache->flush();
		$this->assertTrue( $result );
		
		// Verify they're gone
		$this->assertFalse( $this->cache->get( 'postgrid_test_1' ) );
		$this->assertFalse( $this->cache->get( 'postgrid_test_2' ) );
		$this->assertFalse( $this->cache->get( 'postgrid_test_3' ) );
	}
	
	/**
	 * Test cache clear on post update
	 */
	public function test_cache_clear_on_post_update() {
		// Set some cache
		$key = 'postgrid_test_post_update';
		$this->cache->set( $key, 'cached data' );
		
		// Verify it exists
		$this->assertNotFalse( $this->cache->get( $key ) );
		
		// Update post
		wp_update_post( array(
			'ID' => $this->test_post_id,
			'post_title' => 'Updated Title',
		) );
		
		// Trigger the save_post action
		$this->cache->clear_on_post_update( $this->test_post_id );
		
		// Cache should be cleared
		$this->assertFalse( $this->cache->get( $key ) );
	}
	
	/**
	 * Test cache not cleared for draft posts
	 */
	public function test_cache_not_cleared_for_drafts() {
		// Create draft post
		$draft_id = $this->factory->post->create( array(
			'post_status' => 'draft',
		) );
		
		// Set some cache
		$key = 'postgrid_test_draft';
		$this->cache->set( $key, 'cached data' );
		
		// Update draft
		$this->cache->clear_on_post_update( $draft_id );
		
		// Cache should still exist
		$this->assertNotFalse( $this->cache->get( $key ) );
	}
	
	/**
	 * Test object cache integration
	 */
	public function test_object_cache_integration() {
		// Skip if object cache not available
		if ( ! wp_using_ext_object_cache() ) {
			$this->markTestSkipped( 'External object cache not available' );
		}
		
		$key = 'postgrid_test_object_cache';
		$data = array( 'object' => 'cache' );
		
		// Set cache
		$this->cache->set( $key, $data );
		
		// Should be in object cache
		$cached = wp_cache_get( $key, 'postgrid' );
		$this->assertEquals( $data, $cached );
	}
	
	/**
	 * Test transient fallback
	 */
	public function test_transient_fallback() {
		$key = 'postgrid_test_transient';
		$data = 'transient data';
		
		// Set cache
		$this->cache->set( $key, $data );
		
		// Clear object cache
		wp_cache_flush();
		
		// Should still get from transient
		$cached = $this->cache->get( $key );
		$this->assertEquals( $data, $cached );
	}
}

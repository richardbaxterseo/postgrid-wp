<?php
/**
 * Test Rate Limiting
 *
 * @package PostGrid
 */

/**
 * Rate limiting test case
 */
class Test_Rate_Limiting extends WP_UnitTestCase {
	
	/**
	 * REST controller instance
	 *
	 * @var PostGrid\Api\RestController
	 */
	private $controller;
	
	/**
	 * Test IP address
	 *
	 * @var string
	 */
	private $test_ip = '192.168.1.100';
	
	/**
	 * Set up test
	 */
	public function setUp(): void {
		parent::setUp();
		
		// Initialize controller
		$this->controller = new PostGrid\Api\RestController();
		
		// Clear any existing rate limit transients
		$this->clear_rate_limit_transients();
		
		// Mock the IP address
		$_SERVER['REMOTE_ADDR'] = $this->test_ip;
	}
	
	/**
	 * Tear down test
	 */
	public function tearDown(): void {
		parent::tearDown();
		
		// Clear transients
		$this->clear_rate_limit_transients();
		
		// Reset server vars
		unset( $_SERVER['REMOTE_ADDR'] );
	}
	
	/**
	 * Clear rate limit transients
	 */
	private function clear_rate_limit_transients() {
		delete_transient( 'postgrid_rate_' . $this->test_ip );
	}
	
	/**
	 * Test rate limiting is applied
	 */
	public function test_rate_limiting_is_applied() {
		// Set a low rate limit for testing
		update_option( 'postgrid_rate_limit', 5 );
		
		// Make 5 requests (should all succeed)
		for ( $i = 0; $i < 5; $i++ ) {
			$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
			$response = $this->controller->get_posts( $request );
			
			$this->assertNotWPError( $response, 'Request ' . ( $i + 1 ) . ' should succeed' );
		}
		
		// Make 6th request (should be rate limited)
		$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
		$response = $this->controller->get_posts( $request );
		
		$this->assertWPError( $response, 'Request 6 should be rate limited' );
		$this->assertEquals( 'rate_limit_exceeded', $response->get_error_code() );
	}
	
	/**
	 * Test logged in users bypass rate limiting
	 */
	public function test_logged_in_users_bypass_rate_limit() {
		// Set a low rate limit
		update_option( 'postgrid_rate_limit', 1 );
		
		// Create and log in a user
		$user_id = $this->factory->user->create();
		wp_set_current_user( $user_id );
		
		// Make multiple requests
		for ( $i = 0; $i < 5; $i++ ) {
			$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
			$response = $this->controller->get_posts( $request );
			
			$this->assertNotWPError( $response, 'Logged in user request ' . ( $i + 1 ) . ' should succeed' );
		}
		
		// Log out
		wp_set_current_user( 0 );
	}
	
	/**
	 * Test rate limit filter
	 */
	public function test_rate_limit_filter() {
		// Set option to 10
		update_option( 'postgrid_rate_limit', 10 );
		
		// Add filter to override to 3
		add_filter( 'postgrid_api_rate_limit', function() {
			return 3;
		} );
		
		// Make 3 requests (should succeed)
		for ( $i = 0; $i < 3; $i++ ) {
			$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
			$response = $this->controller->get_posts( $request );
			
			$this->assertNotWPError( $response );
		}
		
		// 4th request should fail
		$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
		$response = $this->controller->get_posts( $request );
		
		$this->assertWPError( $response );
		
		// Clean up filter
		remove_all_filters( 'postgrid_api_rate_limit' );
	}
	
	/**
	 * Test rate limit window resets
	 */
	public function test_rate_limit_window_resets() {
		// Set rate limit
		update_option( 'postgrid_rate_limit', 2 );
		
		// Make 2 requests
		for ( $i = 0; $i < 2; $i++ ) {
			$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
			$response = $this->controller->get_posts( $request );
			$this->assertNotWPError( $response );
		}
		
		// 3rd should fail
		$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
		$response = $this->controller->get_posts( $request );
		$this->assertWPError( $response );
		
		// Clear transient (simulating time passing)
		$this->clear_rate_limit_transients();
		
		// Should work again
		$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
		$response = $this->controller->get_posts( $request );
		$this->assertNotWPError( $response );
	}
	
	/**
	 * Test error response has correct status code
	 */
	public function test_rate_limit_error_status() {
		// Set rate limit to 1
		update_option( 'postgrid_rate_limit', 1 );
		
		// First request succeeds
		$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
		$response = $this->controller->get_posts( $request );
		$this->assertNotWPError( $response );
		
		// Second request should fail with 429 status
		$request = new WP_REST_Request( 'GET', '/postgrid/v1/posts' );
		$response = $this->controller->get_posts( $request );
		
		$this->assertWPError( $response );
		$error_data = $response->get_error_data();
		$this->assertEquals( 429, $error_data['status'] );
	}
}

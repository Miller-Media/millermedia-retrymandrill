<?php
/**
 * Testing Class
 *
 * To set up testing for your wordpress plugin:
 *
 * @see: http://wp-cli.org/docs/plugin-unit-tests/
 *
 * @package retry-emails-with-mandrill
 */
if ( ! class_exists( 'WP_UnitTestCase' ) )
{
	die( 'Access denied.' );
}

/**
 * Example plugin tests
 */
class MillerMediaRetryMandrillPluginTest extends WP_UnitTestCase 
{
	/**
	 * Test that the plugin is a mwp application framework plugin
	 */
	public function test_plugin_class() 
	{
		$plugin = \MillerMedia\RetryMandrill\Plugin::instance();
		
		// Check that the plugin is a subclass of MWP\Framework\Plugin 
		$this->assertTrue( $plugin instanceof \MWP\Framework\Plugin );
	}
}

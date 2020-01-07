<?php
/**
 * Plugin Class File
 *
 * @vendor: Miller Media
 * @package: retry-emails-with-mandrill
 * @author: 
 * @link: 
 * @since: January 7, 2020
 */
namespace MillerMedia\RetryMandrill;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

/**
 * Plugin Class
 */
class Plugin extends \MWP\Framework\Plugin
{
	/**
	 * Instance Cache - Required
	 * @var	self
	 */
	protected static $_instance;
	
	/**
	 * @var string		Plugin Name
	 */
	public $name = 'retry-emails-with-mandrill';
	
	/**
	 * Main Stylesheet
	 *
	 * @MWP\WordPress\Stylesheet
	 */
	public $mainStyle = 'assets/css/style.css';
	
	/**
	 * Main Javascript Controller
	 *
	 * @MWP\WordPress\Script( deps={"mwp"} )
	 */
	public $mainScript = 'assets/js/main.js';
	
	/**
	 * Enqueue scripts and stylesheets
	 * 
	 * @MWP\WordPress\Action( for="wp_enqueue_scripts" )
	 *
	 * @return	void
	 */
	public function enqueueScripts()
	{
		$this->useStyle( $this->mainStyle );
		$this->useScript( $this->mainScript );
	}
	
}
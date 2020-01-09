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

use MWP\Framework\Task;

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
	 * Attach our own mandrill failed email handler
	 *
	 * @MWP\WordPress\Action( for="init" )
	 */
	public function wpInit() 
	{
		// If mandrill plugin is active, replace the native mail action handler to send email to our queue
		if ( class_exists( 'wpMandrill' ) && \wpMandrill::isConfigured() ) {
			remove_action( 'wp_mail_native', array( 'wpMandrill', 'wp_mail_native' ), 10, 5 );
			add_action( 'wp_mail_native', array( $this, 'queueMandrillEmail' ), 10, 5 );
		}
	}
	
	/**
	 * Queue failed mandrill emails
	 *
	 * @param 	$to				string				The address to send to
	 * @param	$subject		string				The email subject
	 * @param	$message		string				The email message
	 * @param	$headers 		array 				Email headers
	 * @param	$attachments 	array 				Email attachments
	 * @return	void
	 */
	public function queueMandrillEmail( $to, $subject, $message, $headers, $attachments )
	{
		Task::queueTask([
			'action' => 'send_queued_mandrill_email',
			'next_start' => time() + ( 60 * 5 ),
		], [
			'to' => $to,
			'subject' => $subject,
			'message' => $message,
			'headers' => $headers,
			'attachments' => $attachments,
			'retried' => 0,
		]);
	}

	/**
	 * Send Queued Mandrill Email
	 *
	 * @MWP\WordPress\Action( for="send_queued_mandrill_email" )
	 *
	 * @param 	Task			$task				The queued task
	 * @return	void
	 */
	public function sendQueuedMandrillEmail( $task ) 
	{
		if ( ! class_exists( 'wpMandrill' ) ) {
			$task->log('Plugin: "Send Email With Mandrill" not active. Aborting.');
			return $task->abort();
		}

		$to = $task->getData('to');
		$subject = $task->getData('subject');
		$message = $task->getData('message');
		$headers = $task->getData('headers');
		$attachments = $task->getData('attachments');

		try {
			$response = \wpMandrill::mail( $to, $subject, $message, $headers, $attachments );
			\wpMandrill::evaluate_response( $response );
			$task->log('Mail sent successfully.');
			$task->setData('mandrill_response', $response);
			return $task->complete();
		}
		catch( \Exception $e ) {
			$task->log('Failed: ' . $e->getMessage());
			
			// Give up after 5 retries
			if ( $task->getData('retried') >= 5 ) {
				$task->log('Stopped after ' . $task->getData('retried') . ' tries.');
				$task->setData('retried', 0);
				return $task->abort();
			}

			$task->setData('retried', $task->getData('retried') + 1);
			$task->next_start = time() + ( 60 * ( 5 ** $task->getData('retried') ) );
			return;
		}
	}
	
}
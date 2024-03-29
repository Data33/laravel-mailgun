<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
 */

namespace Data33\LaravelMailgun\Contracts;


interface TransporterContract {
	public function __construct($apiKey);

	/**
	 * @param string $mailgunUrl The URL to the Mailgun Messages API for your domain
	 * @param mixed $content The content to send
	 * @param \Closure $callback Closure to manipulate the Message object
	 * @param \Closure $requestCallback Closure to manipulate the Request object
	 * @return bool
	 */
	public function send($mailgunUrl, $content, \Closure $callback, \Closure $requestCallback = null);

	/**
	 * Returns an array of all mailgun message ids associated with this message
	 * @return array
	 */
	public function getMessageIds();
}
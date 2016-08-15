<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
 */

namespace Data33\LaravelMailgun\Contracts;


interface TransporterContract {
	public function __construct($apiKey);

	/**
	 * @param string $domain The domain to send from
	 * @param mixed $content The content to send
	 * @param \Closure $callback Closure to manipulate the Message object
	 * @return bool
	 */
	public function send($domain, $content, \Closure $callback);
}
<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
 */

namespace Data33\LaravelMailgun\Traits;


trait APIKeyTrait {
	private $apiKey;

	/**
	 * Contains some logic to help set the api key
	 * ("key-"-part sometimes don't follow a copy-paste from mailgun)
	 *
	 * @param string $apiKey
	 * @throws \Exception
	 */
	public function __construct($apiKey = ''){

		if (strlen($apiKey) === 32){
			$apiKey = 'key-' . $apiKey;
		}

		if (!substr($apiKey, 0, 4) === 'key-' || strlen($apiKey) != 36){
			throw new \Exception('Invalid API key supplied');
		}

		$this->apiKey = $apiKey;
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: Mattias
 * Date: 2016-08-14
 * Time: 17:03
 */

namespace Data33\LaravelMailgun\Traits;


trait APIKeyTrait {
	private $apiKey;

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
<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
 */

namespace Data33\LaravelMailgun\Transporters;

use anlutro\cURL\cURL;
use Data33\LaravelMailgun\Contracts\TransporterContract;
use Data33\LaravelMailgun\Message;
use Data33\LaravelMailgun\Traits\APIKeyTrait;
use Data33\LaravelMailgun\Traits\MessageIdTrait;

class AnlutroCurlTransporter implements TransporterContract{
	use APIKeyTrait, MessageIdTrait;

	/**
	 * @param string $domain The domain to send from
	 * @param mixed $content The content to send
	 * @param \Closure $callback Closure to manipulate the Message object
	 * @param \Closure $requestCallback Closure to manipulate the Request object
	 * @return bool
	 */
	public function send($domain, $content, \Closure $callback, \Closure $requestCallback = null){

		$curl = new cURL;

		$msg = new Message();

		$callback($msg, $curl);

		$msg->setMessage($content);

		$messages = $msg->getData();

		$allSent = true;

		foreach($messages as $postFields){
			$request = $curl->newRawRequest('post', sprintf('https://api.mailgun.net/v3/%s/messages', $domain), $postFields)
				->auth('api', $this->apiKey);
				
			if ($requestCallback !== null) {
				$requestCallback($request);
			}

			$response = $request->send();

			if (isset($response->statusCode, $response->body) && $response->statusCode == 200){
				$json = json_decode($response->body);

				if (!$json || !isset($json->message) || $json->message !== "Queued. Thank you.")
					$allSent = false;

				$this->addMessageId($json->id);
			}
			else{
				$allSent = false;
			}

			return $allSent;
		}
	}
}

<?php
namespace Data33\LaravelMailgun\Transporters;

use anlutro\cURL\cURL;
use Data33\LaravelMailgun\Contracts\TransporterContract;
use Data33\LaravelMailgun\Message;
use Data33\LaravelMailgun\Traits\APIKeyTrait;

class AnlutroCurlTransporter implements TransporterContract{
	use APIKeyTrait;

	public function send($domain, $content, \Closure $callback){

		$curl = new cURL;

		$msg = new Message();

		$callback($msg);

		$msg->setMessage($content);

		$messages = $msg->getData();

		$allSent = true;

		foreach($messages as $postFields){
			$request = $curl->newRawRequest('post', sprintf('https://api.mailgun.net/v3/%s/messages', $domain), $postFields)
				->auth('api', $this->apiKey);

			$response = $request->send();

			if (isset($response->statusCode, $response->body) && $response->statusCode == 200){
				$json = json_decode($response->body);

				if (!$json || !isset($json->message) || $json->message !== "Queued. Thank you.")
					$allSent = false;
			}
			else{
				$allSent = false;
			}

			return $allSent;
		}
	}
}

<?php
/**
 * Created by PhpStorm.
 * User: Mattias
 * Date: 2016-08-14
 * Time: 11:13
 */

namespace Data33\LaravelMailgun;


class Message {
	private $to = [];
	private $cc = [];
	private $bcc = [];
	private $attachments = [];
	private $recipientVariables = [];
	private $from = '';
	private $subject = '';
	private $body = ['text' => '', 'html' => ''];

	public function __construct(){
	}

	public function setSubject($subject){
		if (is_string($subject) && mb_strlen($subject) > 0)
			$this->subject = $subject;

		return $this;
	}

	public function setFromAddress($email, $variables){
		$this->from = $this->parseRecipient($email, $variables);

		return $this;
	}

	public function addToRecipient($email, $variables) {
		$this->setRecipientVariables($email, $variables);
		$this->to[] = $this->parseRecipient($email, $variables);

		return $this;
	}

	public function addCcRecipient($email, $variables){
		$this->setRecipientVariables($email, $variables);
		$this->cc[] = $this->parseRecipient($email, $variables);

		return $this;
	}

	public function addBccRecipient($email, $variables){
		$this->setRecipientVariables($email, $variables);
		$this->bcc[] = $this->parseRecipient($email, $variables);

		return $this;
	}

	public function setMessage($message){
		if (is_array($message)){
			if (isset($message['text'])){
				$this->body['text'] = strip_tags($message['text']);
			}

			if (isset($message['html'])){
				$this->body['html'] = $message['html'];
			}

			if (isset($message[0])){
				$this->body['html'] = $message[0];
			}

			if (isset($message[1])){
				$this->body['text'] = strip_tags($message[1]);
			}
		}
		else{
			$this->body['text'] = strip_tags($message);
			$this->body['html'] = $message;
		}

		return $this;
	}

	public function addAttachment($filePath, $outputName = null){
		if (!file_exists($filePath)){
			throw new Exception('The attached file does not exist!');
		}

		if (is_null($outputName)){
			$outputName = basename($filePath);
		}

		$this->attachments[] = ['path' => $filePath, 'name' => $outputName];

		return $this;
	}

	public function getData(){
		$messages = [];

		if (count($this->to) > 0 || count($this->cc) > 0)
		$messages[] = $this->buildMessageData($this->to, $this->cc);

		//Ugly fix for mailgun not accepting mails without to
		foreach($this->bcc as $bcc){
			$messages[] = $this->buildMessageData([$bcc], []);
		}

		return $messages;
	}

	private function buildMessageData($toRecipients, $ccRecipients){
		$postFields = [
			'from' => '',
			'subject' => '',
			'html' => '',
			'text' => ''
		];

		if (!empty($this->from)){
			$postFields['from'] = $this->from;
		}

		foreach($toRecipients as $index => $to){
			$postFields[sprintf('to[%d]', $index)] = $to;
		}

		foreach($ccRecipients as $index => $to){
			$postFields[sprintf('cc[%d]', $index)] = $to;
		}

		$postFields['subject'] = $this->subject;

		$postFields['html'] = $this->body['html'];
		$postFields['text'] = $this->body['text'];

		foreach($this->attachments as $index => $file){
			$postFields[sprintf('attachment[%d]', $index)] = curl_file_create($file['path'], '', $file['name']);
		}


		return $postFields;
	}

	private function parseRecipient($email, $variables){
		if (is_string($variables)){
			return trim(sprintf("%s <%s>", $variables, $email));
		}
		else if (is_array($variables)){
			if (isset($variables['first'], $variables['last'])){
				return trim(sprintf("%s %s <%s>", $variables['first'], $variables['last'], $email));
			}
			else if (isset($variables['name']) || isset($variables['first'])){
				$name = isset($variables['name']) ? $variables['name'] : $variables['first'];

				return trim(sprintf("%s <%s>", $name, $email));
			}
		}

		return $email;
	}

	private function setRecipientVariables($email, $variables){
		if (!is_array($variables))  return;
		if (!isset($this->recipientVariables[$email])){
			$this->recipientVariables[$email] = [];
		}

		$this->recipientVariables[$email] = array_merge($this->recipientVariables[$email], $variables);
	}
}
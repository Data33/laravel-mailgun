<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
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

	/**
	 * Sets a subject for the message
	 *
	 * @param string $subject The subject of the message
	 *
	 * @return Message
	 */
	public function setSubject($subject){
		if (is_string($subject) && mb_strlen($subject) > 0)
			$this->subject = $subject;

		return $this;
	}

	/**
	 * Sets the from-address for the message
	 *
	 * @param string $email The email address to use as sender
	 * @param string $name The name of the sender
	 *
	 * @return Message
	 *
	 */
	public function setFromAddress($email, $name){
		$this->from = $this->parseRecipient($email, $name);

		return $this;
	}

	/**
	 * Adds a to-recipient to the message
	 * If an array with recipient variables is supplied name can be fetched from the array if
	 * any of the keys "name, first or last" are used.
	 *
	 * @param string $email The email address of the recipient
	 * @param mixed $variables Either supply a string as a name for the recipient or an array with recipient variables
	 * @return Message
	 */
	public function addToRecipient($email, $variables) {
		$this->setRecipientVariables($email, $variables);
		$this->to[] = $this->parseRecipient($email, $variables);

		return $this;
	}

	/**
	 * Adds a cc-recipient to the message
	 * If an array with recipient variables is supplied name can be fetched from the array if
	 * any of the keys "name, first or last" are used.
	 *
	 * @param string $email The email address of the recipient
	 * @param mixed $variables Either supply a string as a name for the recipient or an array with recipient variables
	 * @return Message
	 */
	public function addCcRecipient($email, $variables){
		$this->setRecipientVariables($email, $variables);
		$this->cc[] = $this->parseRecipient($email, $variables);

		return $this;
	}

	/**
	 * Adds a bcc-recipient to the message
	 * If an array with recipient variables is supplied name can be fetched from the array if
	 * any of the keys "name, first or last" are used.
	 *
	 * @param string $email The email address of the recipient
	 * @param mixed $variables Either supply a string as a name for the recipient or an array with recipient variables
	 * @return Message
	 */
	public function addBccRecipient($email, $variables){
		$this->setRecipientVariables($email, $variables);
		$this->bcc[] = $this->parseRecipient($email, $variables);

		return $this;
	}

	/**
	 * Sets the message body
	 *
	 * @param mixed $message Either supply a string that will be used as both html and text, or an array of strings to specify them
	 * @return Message
	 */
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

	/**
	 * Adds an attachment to the message
	 *
	 * @param string $filePath Full path to the file you want to attach
	 * @param string $outputName The filename that recipients will see
	 * @return Message
	 * @throws Exception
	 */
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


	/**
	 * Fetches the message data that needs to be transported
	 * Should only be used by Transporters
	 *
	 * @return array
	 */
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

	/**
	 * Builds the actual postFields to be sent by the Transporter
	 *
	 * @param array $toRecipients
	 * @param array $ccRecipients
	 * @return array
	 */
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

	/**
	 * Parses email and recipient variables into valid recipient string
	 *
	 *
	 * @param string $email The email address to parse
	 * @param mixed $variables The recipient variables to parse
	 * @return string
	 */
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

	/**
	 * Handles the storage of recipient variables internally
	 *
	 * @param string $email
	 * @param mixed $variables
	 */
	private function setRecipientVariables($email, $variables){
		if (!is_array($variables))  return;
		if (!isset($this->recipientVariables[$email])){
			$this->recipientVariables[$email] = [];
		}

		$this->recipientVariables[$email] = array_merge($this->recipientVariables[$email], $variables);
	}
}
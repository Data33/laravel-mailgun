<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
 */

namespace Data33\LaravelMailgun\Traits;


trait MessageIdTrait {
	private $messageIds = [];

	/**
	 * Returns an array of all mailgun message ids associated with this message
	 * @return array
	 */
	public function getMessageIds() {
		return $this->messageIds;
	}

	/**
	 * Appends to the internal array of message ids received from mailgun
	 * @param string $messageId The message id received from mailgun
	 * @return array
	 */
	private function addMessageId($messageId) {
		$this->messageIds[] = $messageId;
	}
}
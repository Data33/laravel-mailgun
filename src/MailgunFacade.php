<?php
/**
 * Created by PhpStorm.
 * User: Mattias
 * Date: 2016-08-05
 * Time: 14:22
 */

namespace Data33\LaravelMailgun;

use Illuminate\Support\Facades\Facade;

class MailgunFacade extends Facade {
	protected static function getFacadeAccessor()
	{
		return 'mailgun';
	}
}

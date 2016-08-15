<?php

namespace Data33\LaravelMailgun\Contracts;


interface TransporterContract {
	public function __construct($apiKey);

	public function send($domain, $content, \Closure $callback);
}
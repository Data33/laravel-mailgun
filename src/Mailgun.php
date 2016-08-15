<?php
/**
 * @author Mattias Ottosson <datae33@gmail.com>
 * @link https://github.com/data33
 */

namespace Data33\LaravelMailgun;


use Data33\LaravelMailgun\Contracts\TransporterContract;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory;

class Mailgun {
	/**
	 * @var Factory
	 */
	private $viewFactory;

	/**
	 * @var TransporterContract
	 */
	private $transport;

	/**
	 *  Injects laravel viewFactory and Transporter of choice
	 */
	public function __construct(){
		$this->viewFactory = app()->make(Factory::class);

		$this->transport = app()->make(TransporterContract::class);
	}


	/**
	 * Sends emails through injected Transporter
	 *
	 * @param string $domain The domain to send email from
	 * @param mixed $view
	 * @param mixed $viewData
	 * @param \Closure $callback Closure to manipulate the Message object
	 * @return bool
	 */
	public function send($domain, $view, $viewData, \Closure $callback){
		return $this->transport->send($domain, $this->parseView($view, $viewData), $callback);
	}

	/**
	 *  Handles which views that need to be parsed
	 *
	 * @param mixed $view
	 * @param mixed $viewData
	 * @return array
	 * @throws \Exception
	 */
	private function parseView($view, $viewData){
		$text = '';
		$html = '';

		if (is_string($view)){
			$html = $this->renderView($view, $viewData);
		}
		else if (is_array($view)){
			if (isset($view['html'])){
				$html = $this->renderView($view['html'], $viewData);
			}

			if (isset($view['text'])){
				$text = $this->renderView($view['text'], $viewData);
			}
		}

		return ['html' => $html, 'text' => $text];
	}

	/**
	 * Parses the view and returns the contents
	 *
	 * @param string $view
	 * @param array $viewData
	 * @return string
	 * @throws \Exception
	 */
	private function renderView($view, $viewData)
	{
		if (!$this->viewFactory->exists($view)){
			throw new \Exception(sprintf('Mailgun: The supplied view [%s] does not exist', $view));
		}

		return $this->viewFactory->make($view, $viewData)->render();
	}
}
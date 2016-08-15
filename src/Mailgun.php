<?php
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

	public function __construct(){
		$this->viewFactory = app()->make(Factory::class);

		$this->transport = app()->make(TransporterContract::class);
	}

	public function send($domain, $view, $viewData, \Closure $callback){
		return $this->transport->send($domain, $this->parseView($view, $viewData), $callback);
	}

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
	 * @param array  $viewData
	 *
	 * @return string
	 */
	private function renderView($view, $viewData)
	{
		if (!$this->viewFactory->exists($view)){
			throw new \Exception(sprintf('Mailgun: The supplied view [%s] does not exist', $view));
		}

		return $this->viewFactory->make($view, $viewData)->render();
	}
}
<?php

namespace Data33\LaravelMailgun\Providers;

use Data33\LaravelMailgun\Mailgun;
use Illuminate\Support\ServiceProvider;
use Data33\LaravelMailgun\Contracts\TransporterContract;
use Data33\LaravelMailgun\Transporters\AnlutroCurlTransporter;

class MailgunServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../config/mailgun.php' => config_path('mailgun.php')
		], 'config');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		app()->bind(TransporterContract::class, function(){
			return new AnlutroCurlTransporter(config('mailgun.apikey', ''));
		});

		app()->bind('mailgun', function(){
			return new Mailgun();
		});
	}
}

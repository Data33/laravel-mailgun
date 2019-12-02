Data33/Laravel-Mailgun
=======

A package for sending emails using the Mailgun HTTP API.
One of the main advantages is that you can send emails from any domains connected to your Mailgun API key.

## Installation ##

Open your `composer.json` file and add the following to the `require` key:

	"data33/laravel-mailgun": "^1"

---
	
After adding the key, run composer update from the command line to install the package 

```bash
composer update
```

## Configuration ##
Before you can start using the package we need to set some configurations.
To do so you must first publish the config file, you can do this with the following `artisan` command. 

```bash
php artisan vendor:publish --provider="Data33\LaravelMailgun\Providers\MailgunServiceProvider" --tag="config"
```
After the config file has been published you can find it at: `config/mailgun.php`

In it you must specify your Mailgun API key.

## Usage with Laravel ##

```php
Mailgun::send('YOUR-DOMAIN', 'view', ['viewVariable' => 'value'], function(\Data33\LaravelMailgun\Message $msg){
	$msg->setFromAddress('sender@YOUR-DOMAIN', 'Sender Name')
		->addToRecipient('RECIPIENT-EMAIL', 'Recipient Name')
		->setSubject('Test subject');
});
```

## Usage without Laravel ##

The easiest way to use this package without Laravel is to directly instantiate a `Transporter` of your choice.
For example:

```php
$mg = new Data33\LaravelMailgun\Transporters\AnlutroCurlTransporter('YOUR-MAILGUN-API-KEY');

$result = $mg->send('YOUR-DOMAIN', ['html' => '<b>Test</b>', 'text' => 'Test'], function(\Data33\LaravelMailgun\Message $msg){
	$msg->setFromAddress('sender@YOUR-DOMAIN', 'Sender Name')
		->addToRecipient('RECIPIENT-EMAIL', 'Recipient Name')
		->setSubject('Test subject');
});
```

## Todo ##

* Implement more Transporters (raw curl, for example)
* Actually pass along recipient variables
* Refine documentation

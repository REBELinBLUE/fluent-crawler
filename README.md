# Fluent Web Crawler
[![StyleCI](https://styleci.io/repos/85713671/shield?style=flat-square&branch=master)](https://styleci.io/repos/85713671)
[![Build Status](https://img.shields.io/travis/REBELinBLUE/fluent-crawler/master.svg?style=flat-square)](https://travis-ci.org/REBELinBLUE/fluent-crawler)
[![Code Climate](https://img.shields.io/codeclimate/github/REBELinBLUE/fluent-crawler.svg?style=flat-square)](https://codeclimate.com/github/REBELinBLUE/fluent-crawler)
[![Code Coverage](https://img.shields.io/codecov/c/github/REBELinBLUE/fluent-crawler/master.svg?style=flat-square)](https://codecov.io/gh/REBELinBLUE/fluent-crawler)

A web scraping library for PHP with a nice fluent interface.

A fork of [laravel/browser-kit-testing](https://github.com/laravel/browser-kit-testing), repurposed to use with real HTTP requests.

## Requirements

PHP 7.1+ and Goutte 3.1+

## Installation

The recommended way to install the library is through [Composer](http://getcomposer.org).

Add ``rebelinblue/fluent-web-crawler`` as a require dependency in your ``composer.json`` file:

```bash
composer require fluent-web-crawler
```

## Usage

Create an instance of the Crawler

```php
use REBELinBLUE\Crawler;

$crawler = new Crawler();
```

Visit a URL

```php
$crawler->visit('http://www.example.com');
```

Interact with the response 

```php
$crawler->type('username', 'admin')
        ->type('password', 'password')
        ->press('Login');
        
// This can also be written as the following

$crawler->submitForm('Login', [
    'username' => 'admin',
    'password' => 'password',
]);

```

or assert the response is as expected
```php
if ($crawler->dontSeeText('Hello World')) {
    throw new \Exception('The page does not contain the expected text');
}
```

For a full list of the available actions see [api.md](api.md).

If you wish to customize the instance of Goutte which is used (or more likely, the instance of Guzzle), you can
inject your own instance when constructing the class. For example, you may want to increase Guzzle's timeout

```php
use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
    
$goutteClient = new GoutteClient();
$guzzleClient = new GuzzleClient([
    'timeout' => 60,
]);
$goutteClient->setClient($guzzleClient);

$crawler = new Crawler($goutteClient);
```

## Further Reading
---------------------

Fluent Crawler is a wrapper around the following PHP libraries.

* [Goutte](https://github.com/FriendsOfPHP/Goutte) web scraper.
* Symfony [BrowserKit](https://symfony.com/components/BrowserKit), [CssSelector](https://symfony.com/doc/current/components/css_selector.html) and [DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html).
* [Guzzle](http://docs.guzzlephp.org) HTTP client.

# API

The web crawler has a fluent interface. It is designed to behave like a browser, but it is also possible to make
HTTP requests directly, for example if you know the fields in a form you can make a `POST` request without having
to load it first.

## Requests

The primary method of making requests is with the `visit` method, this is as if a user has typed an address
into their browser. From there on you then interact with the page to click link & submit forms to visit new pages.
```php
$crawler->visit('http://example.com');
```

### Making raw requests
Of course, it is not always desirable to load a page just so you can go to another, so you are able to make
raw HTTP requests
```php
$crawler->get($url, $headers);

$crawler->post($url, $parameters, $headers);

$crawler->put($url, $parameters, $headers);

$crawler->patch($url, $parameters, $headers);

$crawler->delete($url, $parameters, $headers);
```

The `$parameters` is an array of key/values for data to send, i.e. form fields.
The `$headers` is an array of key/values for additional headers to send.

An example of making a `POST` request

```php
$parameters = ['name' => 'admin'];
$headers    = ['If-Unmodified-Since' => 'Thu, 25 May 2017 18:45:31 GMT'];

$crawler->post('http://www.example.com/users/1', $parameters, $headers);
```

## Interacting with Pages

The primary purpose of the library is to behave like a web
 
## Interacting with Responses

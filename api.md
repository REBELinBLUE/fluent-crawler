# API

The web crawler has a fluent interface. It is designed to behave like a browser, but it is also possible to make
HTTP requests directly, for example if you know the fields in a form you can make a `POST` request without having
to load it first.

- [Making requests](#making-requests)
- [Interacting with Pages](#interacting-with-pages)
- [Checking for desired content](#checking-for-desired-content)
- [Interacting with responses](#interacting-with-responses)
- [Checking for desired responses](#checking-for-desired-responses)

## Making requests

The primary method of making requests is with the `visit` method, this is as if a user has typed an address
into their browser. From there on you then interact with the page to click link & submit forms to visit new pages.
```php
$crawler->visit(string $url);
```

**Making raw requests**

Of course, it is not always desirable to load a page just so you can go to another, so you are able to make
raw HTTP requests
```php
$crawler->get(string $url, array $headers = []);

$crawler->post(string $url, array $parameters = [], array $headers = []);

$crawler->put(string $url, array $parameters = [], array $headers = []);

$crawler->patch(string $url, array $parameters = [], array $headers = []);

$crawler->delete(string $url, array $parameters = [], array $headers = []);
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

Once you have made a request you are then able to interact with the resulting page.

Unless otherwise stated, the following methods will throw a `\InvalidArgumentException` if the desired
element is not found on the current page.

**Click a link**
```php
$crawler->click(string $linkText);
```
The `$linkText` can be the text of the link, or the `name` or `id` if of the tag. Clicking a link results in navigating
to a new page.

**Submit a form**
```php
$crawler->submitForm(string $buttonText, array $inputs);

// Alternatively, if there is only 1 form the $buttonText can be omitted

$crawler->submitForm(array $inputs);
```
The `$buttonText` can be the text of the submit button, or the `name` or `id` if of the tag. Submitting a form results
in navigating to a new to a new page. The `$inputs` parameters is a key/value pair of the values, so, for example.

```php
$crawler->submitForm('Login', [
    'username'    => 'admin',
    'password'    => 'password',
    'remember_me' => true,
]);
```

**Populating a form**
Along with populating and submitting the form in one call you are able to interact with each field separately.

```php
// Type into an input or textarea
$crawler->type(string $value, string $element);

// Select a checkbox
$crawler->check(string $element);

// Clear a checkbox
$crawler->uncheck(string $element);

// Select an option from a radio button or select field
$crawler->select(string $value, string $element);

// Press a submit button
$crawler->press(string $buttonText);
```

So the previous example can be rewritten as the following

```php
$crawler->type('username', 'admin')
        ->type('password', 'password')
        ->check('remember_me')
        ->press('Login');
```

TODO: Add within and filter

## Checking for desired content

## Interacting with responses

## Checking for desired responses

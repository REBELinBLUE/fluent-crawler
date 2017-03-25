# API

The web crawler has a fluent interface. It is designed to behave like a browser, but it is also possible to make
HTTP requests directly, for example if you know the fields in a form you can make a `POST` request without having
to load it first.

- [Making requests](#making-requests)
- [Interacting with Pages](#interacting-with-pages)
- [Checking for desired content](#checking-for-desired-content)
- [Filtering content](#filtering-content)
- [Interacting with responses](#interacting-with-responses)
- [Checking for desired responses](#checking-for-desired-responses)

## Making requests

The primary method of making requests is with the `visit` method, this is as if a user has typed an address
into their browser. From there on you then interact with the page to click link & submit forms to visit new pages.
```php
$crawler->visit(string $url): self;
```

**Making raw requests**
Of course, it is not always desirable to load a page just so you can go to another, so you are able to make
raw HTTP requests
```php
$crawler->get(string $url, array $headers = []): self;

$crawler->post(string $url, array $parameters = [], array $headers = []): self;

$crawler->put(string $url, array $parameters = [], array $headers = []): self;

$crawler->patch(string $url, array $parameters = [], array $headers = []): self;

$crawler->delete(string $url, array $parameters = [], array $headers = []): self;
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
$crawler->click(string $linkText): self;
```
The `$linkText` can be the text of the link, or the `name` or `id` if of the tag. Clicking a link results in navigating
to a new page.

**Submit a form**
```php
$crawler->submitForm(string $buttonText, array $inputs): self;

// Alternatively, if there is only 1 form the $buttonText can be omitted

$crawler->submitForm(array $inputs): self;
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
$crawler->type(string $value, string $name): self;

// Select a checkbox
$crawler->check(string $name): self;

// Clear a checkbox
$crawler->uncheck(string $name): self;

// Select an option from a radio button or select field
$crawler->select(string $value, string $name): self;

// Press a submit button
$crawler->press(string $buttonText): self;
```

So the previous example can be rewritten as the following

```php
$crawler->type('username', 'admin')
        ->type('password', 'password')
        ->check('remember_me')
        ->press('Login');
```

## Checking for desired content

There are several method to allow you to check whether the page has expected content.
 
```php
$crawler->see(string $text): bool;
```
Returns a boolean value indicating whether the supplied string is in the source of the page

```php
$crawler->seeText(string $text): bool;
```
Returns a boolean value indicating whether the supplied string is in the text of the page

```php
$crawler->seeElement(string $selector, array $attributes = []): bool;
```
Returns a boolean value indicating whether the supplied selector is in the source of the page. The `$attributes` array
can be a key/value pair of attributes/values which must also exist on the element, or just an array of attributes.
For example

```php
// Matches <div id="container">
$crawler->seeElement('div#container');

// Matches <div id="container" style="...">, where the value can be anything
$crawler->seeElement('div#container', ['style']);

// Matches <div id="container" class="heading">
$crawler->seeElement('div#container', ['class' => 'heading']);
```

```php
$crawler->seeInElement(string $element, string $text): bool;
```
Returns a boolean value indicating whether the supplied string appears within the HTML of an element.

```php
$crawler->seeLink(string $text, ?string $url = null): bool;
```
Returns a boolean value indicating whether a link with the supplied body appears wihin the HTML of the page. If the 
optional `$url` is supplied the href must almost match.
For example

```php
// Matches <a href="...">Click me</a>, when the href can be any value
$crawler->seeLink('Click me');

// Matches <a href="http://example.com">Click me</a>
$crawler->seeLink('Click me', 'http://example.com');
```

**Checking the value of forms**

N.B. The following methods _do not_ return values based on a form which has been filled in using the previous methods, 
they are only meant for indicating whether the original source of the page has the supplied values.

```php
$crawler->seeInField(string $selector, string $value): bool;
```
Returns a boolean indicating whether the input field has the expected value. 

```php
$crawler->seeIsSelected(string $selector, string $value): bool;
```
Returns a boolean indicating whether the select or radio box as the expected value.

```php
$crawler->seeIsChecked(string $selector): bool;
```
Returns a boolean indicating whether a checkbox is checked. 

**Inverting the checks**

Although you can of course use PHP's logical not operator `!` to invert the check, each method also has a counterpart 
with `dont` prefixed, for example `dontSeeText`, `dontSeeElement` and `dontSeeInField`.

## Filtering content

You can also narrow down the area of the page that a method applies to using the following method

```php
$crawler->within(string $name, \Closure $callback): self;
```

For example

```php
// Indicates whether the <div id="container"> contains the text "Hello World"
$hasText = false;
$crawler->within('div#container', function () use (&$hasText, $crawler) {
    $hasText = $crawler->seeText('Hello World');
});
```

It is also possible to filter the content and interact with it using the following method

```php
$crawler->filter(string $name, \Closure $callback): self;
```
The callback will be passed an instance of Symfony's DomCrawler. For example, using the following HTML

```html
<ul id="container">
    <li>Foo</li>
    <li>Bar</li>
    <li>Baz</li>
    <li>Qux</li>
</ul>
```

You could get an array of all the values in the list with the following

```php
// Filters the content to <ul id="container"> and passes to the supplied closure
$text = [];
$crawler->filter('ul#container', function (DomCrawler $dom) use (&$text) {
    $text = $dom->filter('li')->each(function (DomCrawler $node) {
        return $node->text();
    });
});
```

Read the documentation of the [Symfony DomCrawler](https://symfony.com/doc/current/components/dom_crawler.html)
component for more information about what you can do with this method.

## Interacting with responses

If desired you can interact with the response headers with 2 methods

```php
// Returns a key/value pair of all headers
$crawler->getHeaders(): array

// Returns a specific named header
$crawler->getHeader(string $headerName): ?string
```

You can also interact with the cookies with the following methods

```php
// Returns a key/value pair of all cookies
$crawler->getCookies(): array

// Returns a specific named cookie
$crawler->getCookie(string $cookieName): ?string
```

You can also get the the raw `BrowserKit` response and the `Goutte` client

```php
// Get the response object from the previous request
$crawler->getResponse(): Response;

// Get the Goutte client. This allows you to interact with the history etc.
$crawler->getClient(): Client;
```

## Checking for desired responses

As with the page, you can also test that the response contains expected values.

You can check the status code

```php
$crawler->isStatusCode(int $statusCode): bool;
```
As `200` is the most common status code there is also a helper method to specifically test for this

```php
$crawler->isOk(): bool;
```

Header and cookie values can be checked

```php
$crawler->hasHeader(string $headerName, ?string $value = null): bool;

$crawler->hasCookie(string $cookieName, ?string $value = null): bool;
```

For example

```php
// Checks the E-Tag header exists
$crawler->hasHeader('E-Tag');

// Checks that the Content-Type header exists and has a value of text/html
$crawler->hasHeader('Content-Type', 'text/html');

// Checks that there is a cookie called foo
$crawler->hasCookie('foo');

// Checks that there is a cookie called name foo with a value of bar
$crawler->hasCookie('foo', 'bar');
```

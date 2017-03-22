<?php

namespace REBELinBLUE\Crawler;

use Closure;
use Goutte\Client;
use InvalidArgumentException;
use REBELinBLUE\Crawler\Constraints\HasElement;
use REBELinBLUE\Crawler\Constraints\HasSource;
use REBELinBLUE\Crawler\Constraints\HasText;
use REBELinBLUE\Crawler\Constraints\PageConstraint;
use REBELinBLUE\Crawler\Constraints\ReversePageConstraint;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\DomCrawler\Form;

/**
 * A fluent interface to Symfony Crawler using Goutte.
 *
 * @todo: Add the ability to get an element
 */
class Crawler
{
    /** @var DomCrawler[] */
    protected $subCrawlers = [];

    /** @var array  */
    protected $inputs = [];

    /** @var DomCrawler */
    protected $crawler;

    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     * @return Crawler
     */
    public function visit(string $uri): self
    {
        return $this->makeRequest('GET', $uri);
    }

    /**
     * Submit a form on the page with the given input.
     *
     * @param string|array $buttonText
     * @param array|null   $inputs
     *
     * @return Crawler
     */
    public function submitForm($buttonText, ?array $inputs = [])
    {
        return $this->makeRequestUsingForm($this->fillForm($buttonText, $inputs));
    }

    /**
     * Click a link with the given body, name, or ID attribute.
     *
     * @param  string                   $name
     * @throws InvalidArgumentException
     * @return $this
     */
    public function click(string $name)
    {
        $link = $this->crawler()->selectLink($name);

        if (!count($link)) {
            $link = $this->filterByNameOrId($name, 'a');

            if (!count($link)) {
                throw new InvalidArgumentException(
                    "Could not find a link with a body, name, or ID attribute of [{$name}]."
                );
            }
        }

        $this->visit($link->link()->getUri());

        return $this;
    }

    /**
     * Fill an input field with the given text.
     *
     * @param  string  $text
     * @param  string  $element
     * @return Crawler
     */
    public function type(string $text, string $element): self
    {
        return $this->storeInput($element, $text);
    }

    /**
     * Check a checkbox on the page.
     *
     * @param  string  $element
     * @return Crawler
     */
    public function check(string $element): self
    {
        return $this->storeInput($element, true);
    }

    /**
     * Uncheck a checkbox on the page.
     *
     * @param  string  $element
     * @return Crawler
     */
    public function uncheck(string $element): self
    {
        return $this->storeInput($element, false);
    }

    /**
     * Select an option from a drop-down.
     *
     * @param  string  $option
     * @param  string  $element
     * @return Crawler
     */
    public function select(string $option, string $element): self
    {
        return $this->storeInput($element, $option);
    }

    /**
     * Submit a form using the button with the given text value.
     *
     * @param  string  $buttonText
     * @return Crawler
     */
    public function press(string $buttonText): self
    {
        return $this->submitForm($buttonText, $this->inputs);
    }

    /**
     * Narrow the test content to a specific area of the page.
     *
     * @param  string  $element
     * @param  Closure $callback
     * @return $this
     */
    public function within(string $element, Closure $callback)
    {
        $this->subCrawlers[] = $this->crawler()->filter($element);

        $callback();

        array_pop($this->subCrawlers);

        return $this;
    }

    /**
     * Check that a given string is seen on the current page.
     *
     * @param  string $text
     * @param  bool   $negate
     * @return bool
     */
    public function see(string $text, bool $negate = false): bool
    {
        return $this->assertInPage(new HasSource($text), $negate);
    }

    /**
     * Check that a given string is not seen on the current page.
     *
     * @param  string $text
     * @return bool
     */
    public function dontSee(string $text): bool
    {
        return $this->assertInPage(new HasSource($text), true);
    }

    /**
     * Check that a given string is seen in the current text.
     *
     * @param  string $text
     * @param  bool   $negate
     * @return bool
     */
    public function seeText(string $text, bool $negate = false): bool
    {
        return $this->assertInPage(new HasText($text), $negate);
    }

    /**
     * Check that a given string is not seen in the current text.
     *
     * @param  string $text
     * @return bool
     */
    public function dontSeeText(string $text): bool
    {
        return $this->assertInPage(new HasText($text), true);
    }

    /**
     * Check that an element is present on the page.
     *
     * @param  string $selector
     * @param  array  $attributes
     * @param  bool   $negate
     * @return bool
     */
    public function seeElement(string $selector, array $attributes = [], bool $negate = false): bool
    {
        return $this->assertInPage(new HasElement($selector, $attributes), $negate);
    }

    /**
     * Check that an element is not present on the page.
     *
     * @param  string $selector
     * @param  array  $attributes
     * @return bool
     */
    public function dontSeeElement(string $selector, array $attributes = []): bool
    {
        return $this->assertInPage(new HasElement($selector, $attributes), true);
    }

    /**
     * Get's the HTTP client instance.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get's the response object for the last request.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->client->getResponse();
    }

    protected function makeRequest(string $method, string $uri, array $parameters = []): self
    {
        $this->resetPageContext();

        $this->crawler = $this->client->request($method, $uri, $parameters);

        $this->clearInputs();

        return $this;
    }

    protected function makeRequestUsingForm(Form $form): self
    {
        return $this->makeRequest(
            $form->getMethod(),
            $form->getUri(),
            $this->extractParametersFromForm($form)
        );
    }

    /**
     * @param  string|array $buttonText
     * @param  array|null   $inputs
     * @return $this
     */
    protected function fillForm($buttonText, ?array $inputs = [])
    {
        if (!is_string($buttonText)) {
            $inputs     = $buttonText;
            $buttonText = null;
        }

        return $this->getForm($buttonText)->setValues($inputs);
    }

    protected function getForm(?string $buttonText = null): Form
    {
        try {
            if ($buttonText) {
                return $this->crawler()->selectButton($buttonText)->form();
            }

            return $this->crawler()->filter('form')->form();
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                'Could not find a form that has submit button' . ($buttonText ? "[{$buttonText}]" : '')
            );
        }
    }

    protected function extractParametersFromForm(Form $form): array
    {
        parse_str(http_build_query($form->getValues()), $parameters);

        return $parameters;
    }

    protected function storeInput(string $element, string $text): self
    {
        $this->assertFilterProducesResults($element);

        $element = str_replace(['#', '[]'], '', $element);

        $this->inputs[$element] = $text;

        return $this;
    }

    protected function clearInputs(): self
    {
        $this->inputs = [];

        return $this;
    }

    protected function assertFilterProducesResults(string $filter)
    {
        $crawler = $this->filterByNameOrId($filter);

        if (!count($crawler)) {
            throw new InvalidArgumentException("Nothing matched the filter [{$filter}] CSS query provided");
        }
    }

    protected function filterByNameOrId(string $name, string $elements = '*'): DomCrawler
    {
        $name       = str_replace('#', '', $name);
        $identifier = str_replace(['[', ']'], ['\\[', '\\]'], $name);
        $elements   = is_array($elements) ? $elements : [$elements];

        array_walk($elements, function (&$element) use ($name, $identifier) {
            $element = "{$element}#{$identifier}, {$element}[name='{$name}']";
        });

        return $this->crawler()->filter(implode(', ', $elements));
    }

    protected function resetPageContext()
    {
        $this->crawler     = null;
        $this->subCrawlers = [];
    }

    protected function crawler()
    {
        if (!empty($this->subCrawlers)) {
            return end($this->subCrawlers);
        }

        return $this->crawler;
    }

    protected function assertInPage(PageConstraint $constraint, bool $reverse = false): bool
    {
        if ($reverse) {
            $constraint = new ReversePageConstraint($constraint);
        }

        return $constraint->matches($this->crawler() ?: $this->client->getResponse()->getContent());
    }

//    /**
//     * Assert that a given string is seen inside an element.
//     *
//     * @param  string  $element
//     * @param  string  $text
//     * @param  bool  $negate
//     * @return $this
//     */
//    public function seeInElement($element, $text, $negate = false)
//    {
//        return $this->assertInPage(new HasInElement($element, $text), $negate);
//    }
//
//    /**
//     * Assert that a given string is not seen inside an element.
//     *
//     * @param  string  $element
//     * @param  string  $text
//     * @return $this
//     */
//    public function dontSeeInElement($element, $text)
//    {
//        return $this->assertInPage(new HasInElement($element, $text), true);
//    }
//
//    /**
//     * Assert that a given link is seen on the page.
//     *
//     * @param  string $text
//     * @param  string|null $url
//     * @param  bool  $negate
//     * @return $this
//     */
//    public function seeLink($text, $url = null, $negate = false)
//    {
//        return $this->assertInPage(new HasLink($text, $url), $negate);
//    }
//
//    /**
//     * Assert that a given link is not seen on the page.
//     *
//     * @param  string  $text
//     * @param  string|null  $url
//     * @return $this
//     */
//    public function dontSeeLink($text, $url = null)
//    {
//        return $this->assertInPage(new HasLink($text, $url), true);
//    }
//
//    /**
//     * Assert that an input field contains the given value.
//     *
//     * @param  string  $selector
//     * @param  string  $expected
//     * @param  bool  $negate
//     * @return $this
//     */
//    public function seeInField($selector, $expected, $negate = false)
//    {
//        return $this->assertInPage(new HasValue($selector, $expected), $negate);
//    }
//
//    /**
//     * Assert that an input field does not contain the given value.
//     *
//     * @param  string  $selector
//     * @param  string  $value
//     * @return $this
//     */
//    public function dontSeeInField($selector, $value)
//    {
//        return $this->assertInPage(new HasValue($selector, $value), true);
//    }
//
//    /**
//     * Assert that the expected value is selected.
//     *
//     * @param  string  $selector
//     * @param  string  $value
//     * @param  bool  $negate
//     * @return $this
//     */
//    public function seeIsSelected($selector, $value, $negate = false)
//    {
//        return $this->assertInPage(new IsSelected($selector, $value), $negate);
//    }
//
//    /**
//     * Assert that the given value is not selected.
//     *
//     * @param  string  $selector
//     * @param  string  $value
//     * @return $this
//     */
//    public function dontSeeIsSelected($selector, $value)
//    {
//        return $this->assertInPage(new IsSelected($selector, $value), true);
//    }
//
//    /**
//     * Assert that the given checkbox is selected.
//     *
//     * @param  string  $selector
//     * @param  bool  $negate
//     * @return $this
//     */
//    public function seeIsChecked($selector, $negate = false)
//    {
//        return $this->assertInPage(new IsChecked($selector), $negate);
//    }
//
//    /**
//     * Assert that the given checkbox is not selected.
//     *
//     * @param  string  $selector
//     * @return $this
//     */
//    public function dontSeeIsChecked($selector)
//    {
//        return $this->assertInPage(new IsChecked($selector), true);
//    }
}

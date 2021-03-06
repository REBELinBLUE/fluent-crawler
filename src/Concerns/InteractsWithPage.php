<?php

namespace REBELinBLUE\Crawler\Concerns;

use Closure;
use InvalidArgumentException;
use REBELinBLUE\Crawler\Crawler;

trait InteractsWithPage
{
    /**
     * Narrow the test content to a specific area of the page.
     *
     * @param  string  $selector
     * @param  Closure $callback
     * @return Crawler
     */
    public function within(string $selector, Closure $callback): Crawler
    {
        $this->subCrawlers[] = $this->crawler()->filter($selector);

        $callback();

        array_pop($this->subCrawlers);

        return $this;
    }

    /**
     * Filter the content to a specific area of the page and pass to the callback.
     *
     * @param  string  $selector
     * @param  Closure $callback
     * @return Crawler
     */
    public function filter(string $selector, Closure $callback): Crawler
    {
        $crawler = $this->crawler()->filter($selector);

        $callback($crawler);

        return $this;
    }

    /**
     * Filter the content to a specific area of the page, pass to the callback and return the result.
     *
     * @param  string  $selector
     * @param  Closure $callback
     * @return mixed
     */
    public function extract(string $selector, Closure $callback)
    {
        $crawler = $this->crawler()->filter($selector);

        return $callback($crawler);
    }

    /**
     * Submit a form on the page with the given input.
     *
     * @param  string|array $buttonText
     * @param  array|null   $inputs
     * @return Crawler
     */
    public function submitForm($buttonText, ?array $inputs = []): Crawler
    {
        return $this->makeRequestUsingForm($this->fillForm($buttonText, $inputs));
    }

    /**
     * Click a link with the given body, name, or ID attribute.
     *
     * @param  string                   $name
     * @throws InvalidArgumentException
     * @return Crawler
     */
    public function click(string $linkText): Crawler
    {
        $link = $this->crawler()->selectLink($linkText);

        if (!count($link)) {
            $link = $this->filterByNameOrId($linkText, 'a');

            if (!count($link)) {
                throw new InvalidArgumentException(
                    "Could not find a link with a body, name, or ID attribute of [{$linkText}]."
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
     * @param  string  $name
     * @return Crawler
     */
    public function type(string $text, string $name): Crawler
    {
        return $this->storeInput($name, $text);
    }

    /**
     * Check a checkbox on the page.
     *
     * @param  string  $name
     * @return Crawler
     */
    public function check(string $name): Crawler
    {
        return $this->storeInput($name, true);
    }

    /**
     * Uncheck a checkbox on the page.
     *
     * @param  string  $name
     * @return Crawler
     */
    public function uncheck(string $name): Crawler
    {
        return $this->storeInput($name, false);
    }

    /**
     * Select an option from a drop-down.
     *
     * @param  string  $option
     * @param  string  $name
     * @return Crawler
     */
    public function select(string $option, string $name): Crawler
    {
        return $this->storeInput($name, $option);
    }

    /**
     * Submit a form using the button with the given text value.
     *
     * @param  string  $buttonText
     * @return Crawler
     */
    public function press(string $buttonText): Crawler
    {
        return $this->submitForm($buttonText, $this->inputs);
    }
}

<?php

namespace REBELinBLUE\Crawler\Concerns;

use Closure;
use InvalidArgumentException;

trait InteractsWithPage
{
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
}

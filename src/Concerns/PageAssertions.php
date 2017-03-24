<?php

namespace REBELinBLUE\Crawler\Concerns;

use REBELinBLUE\Crawler\Constraints\HasElement;
use REBELinBLUE\Crawler\Constraints\HasInElement;
use REBELinBLUE\Crawler\Constraints\HasLink;
use REBELinBLUE\Crawler\Constraints\HasSource;
use REBELinBLUE\Crawler\Constraints\HasText;
use REBELinBLUE\Crawler\Constraints\HasValue;
use REBELinBLUE\Crawler\Constraints\IsChecked;
use REBELinBLUE\Crawler\Constraints\IsSelected;
use REBELinBLUE\Crawler\Constraints\PageConstraint;
use REBELinBLUE\Crawler\Constraints\ReversePageConstraint;

trait PageAssertions
{
    /**
     * Check that a given string is seen on the current page.
     *
     * @param  string $text
     * @return bool
     */
    public function see(string $text): bool
    {
        return $this->assertInPage(new HasSource($text));
    }

    /**
     * Check that a given string is not seen on the current page.
     *
     * @param  string $text
     * @return bool
     */
    public function dontSee(string $text): bool
    {
        return $this->assertNotInPage(new HasSource($text));
    }

    /**
     * Check that a given string is seen in the current text.
     *
     * @param  string $text
     * @return bool
     */
    public function seeText(string $text): bool
    {
        return $this->assertInPage(new HasText($text));
    }

    /**
     * Check that a given string is not seen in the current text.
     *
     * @param  string $text
     * @return bool
     */
    public function dontSeeText(string $text): bool
    {
        return $this->assertNotInPage(new HasText($text));
    }

    /**
     * Check that an element is present on the page.
     *
     * @param  string $selector
     * @param  array  $attributes
     * @return bool
     */
    public function seeElement(string $selector, array $attributes = []): bool
    {
        return $this->assertInPage(new HasElement($selector, $attributes));
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
        return $this->assertNotInPage(new HasElement($selector, $attributes));
    }

    /**
     * Checks that a given string is seen inside an element.
     *
     * @param  string $element
     * @param  string $text
     * @return bool
     */
    public function seeInElement(string $element, string $text): bool
    {
        return $this->assertInPage(new HasInElement($element, $text));
    }

    /**
     * Checks that a given string is not seen inside an element.
     *
     * @param $element
     * @param $text
     * @return bool
     */
    public function dontSeeInElement($element, $text): bool
    {
        return $this->assertNotInPage(new HasInElement($element, $text));
    }

    /**
     * Check that a given link is seen on the page.
     *
     * @param  string      $text
     * @param  null|string $url
     * @return bool
     */
    public function seeLink(string $text, ?string $url = null)
    {
        return $this->assertInPage(new HasLink($text, $url));
    }

    /**
     * Check that a given link is not seen on the page.
     *
     * @param  string      $text
     * @param  string|null $url
     * @return bool
     */
    public function dontSeeLink(string $text, string $url = null)
    {
        return $this->assertNotInPage(new HasLink($text, $url));
    }

    /**
     * Check that an input field contains the given value.
     *
     * @param  string $selector
     * @param  string $expected
     * @return mixed
     */
    public function seeInField(string $selector, string $expected)
    {
        return $this->assertInPage(new HasValue($selector, $expected));
    }

    /**
     * Check that an input field does not contain the given value.
     *
     * @param  string $selector
     * @param  string $value
     * @return mixed
     */
    public function dontSeeInField(string $selector, string $value)
    {
        return $this->assertNotInPage(new HasValue($selector, $value));
    }

    /**
     * Checks that the expected value is selected.
     *
     * @param  string $selector
     * @param  string $value
     * @return bool
     */
    public function seeIsSelected(string $selector, string $value): bool
    {
        return $this->assertInPage(new IsSelected($selector, $value));
    }

    /**
     * Check that the given value is not selected.
     *
     * @param  string $selector
     * @param  string $value
     * @return bool
     */
    public function dontSeeIsSelected(string $selector, string $value): bool
    {
        return $this->assertNotInPage(new IsSelected($selector, $value));
    }

    /**
     * Check that the given checkbox is selected.
     *
     * @param  string $selector
     * @return bool
     */
    public function seeIsChecked(string $selector): bool
    {
        return $this->assertInPage(new IsChecked($selector));
    }

    /**
     * Check that the given checkbox is not selected.
     *
     * @param $selector
     * @return bool
     */
    public function dontSeeIsChecked($selector): bool
    {
        return $this->assertNotInPage(new IsChecked($selector));
    }

    protected function assertInPage(PageConstraint $constraint): bool
    {
        return $constraint->matches($this->crawler() ?: $this->getResponse()->getContent());
    }

    protected function assertNotInPage(PageConstraint $constraint): bool
    {
        return $this->assertInPage(new ReversePageConstraint($constraint));
    }
}

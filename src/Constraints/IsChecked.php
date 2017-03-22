<?php

namespace REBELinBLUE\Crawler\Constraints;

class IsChecked extends FormFieldConstraint
{
    public function __construct($selector)
    {
        $this->selector = $selector;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $crawler = $this->crawler($crawler);

        return !is_null($this->field($crawler)->attr('checked'));
    }

    protected function validElements(): array
    {
        return ['input[type="checkbox"]'];
    }
}

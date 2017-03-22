<?php

namespace REBELinBLUE\Crawler\Constraints;

use Symfony\Component\DomCrawler\Crawler;

class HasValue extends FormFieldConstraint
{
    /**
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $crawler = $this->crawler($crawler);

        return $this->getInputOrTextAreaValue($crawler) === $this->value;
    }

    public function getInputOrTextAreaValue(Crawler $crawler): string
    {
        $field = $this->field($crawler);

        return $field->nodeName() === 'input' ? $field->attr('value') : $field->text();
    }

    protected function validElements(): array
    {
        return ['input', 'textarea'];
    }
}

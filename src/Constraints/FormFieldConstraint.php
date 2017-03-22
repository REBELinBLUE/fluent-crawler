<?php

namespace REBELinBLUE\Crawler\Constraints;

use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

abstract class FormFieldConstraint extends PageConstraint
{
    protected $selector;

    protected $value;

    public function __construct(string $selector, $value)
    {
        $this->selector = $selector;
        $this->value    = (string) $value;
    }

    abstract protected function validElements(): array;

    protected function field(Crawler $crawler): Crawler
    {
        $field = $crawler->filter(implode(', ', $this->getElements()));

        if ($field->count() > 0) {
            return $field;
        }

        throw new InvalidArgumentException(sprintf(
            'There is no %s with the name or ID [%s]',
            implode(',', $this->validElements()),
            $this->selector
        ));
    }

    protected function getElements(): array
    {
        $name       = str_replace('#', '', $this->selector);
        $identifier = str_replace(['[', ']'], ['\\[', '\\]'], $name);

        $elements = $this->validElements();
        array_walk($elements, function (&$element) use ($name, $identifier) {
            $element = "{$element}#{$identifier}, {$element}[name='{$name}']";
        });

        return $elements;
    }
}

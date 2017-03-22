<?php

namespace REBELinBLUE\Crawler\Constraints;

use Symfony\Component\DomCrawler\Crawler;

class HasElement extends PageConstraint
{
    protected $selector;

    protected $attributes;

    public function __construct(string $selector, array $attributes = [])
    {
        $this->selector   = $selector;
        $this->attributes = $attributes;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $elements = $this->crawler($crawler)->filter($this->selector);

        if (!$elements->count()) {
            return false;
        }

        if (empty($this->attributes)) {
            return true;
        }

        $elements = $elements->reduce(function ($element) {
            return $this->hasAttributes($element);
        });

        return $elements->count() > 0;
    }

    protected function hasAttributes(Crawler $element): bool
    {
        foreach ($this->attributes as $name => $value) {
            if (is_numeric($name)) {
                if (is_null($element->attr($value))) {
                    return false;
                }
            } elseif ($element->attr($name) !== $value) {
                return false;
            }
        }

        return true;
    }
}

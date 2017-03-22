<?php

namespace REBELinBLUE\Crawler\Constraints;

use Symfony\Component\DomCrawler\Crawler;

class HasInElement extends PageConstraint
{
    protected $element;

    protected $text;

    public function __construct(string $element, string $text)
    {
        $this->text    = $text;
        $this->element = $element;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $elements = $this->crawler($crawler)->filter($this->element);

        $pattern = $this->getEscapedPattern($this->text);

        foreach ($elements as $element) {
            $element = new Crawler($element);

            if (preg_match("/$pattern/i", $element->html())) {
                return true;
            }
        }

        return false;
    }
}

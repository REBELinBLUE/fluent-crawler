<?php

namespace REBELinBLUE\Crawler\Constraints;

class HasText extends PageConstraint
{
    protected $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $pattern = $this->getEscapedPattern($this->text);

        return preg_match("/{$pattern}/i", $this->text($crawler));
    }
}

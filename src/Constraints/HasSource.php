<?php

namespace REBELinBLUE\Crawler\Constraints;

class HasSource extends PageConstraint
{
    protected $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $pattern = $this->getEscapedPattern($this->source);

        return preg_match("/{$pattern}/i", $this->html($crawler));
    }
}

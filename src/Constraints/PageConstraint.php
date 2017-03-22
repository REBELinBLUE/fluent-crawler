<?php

namespace REBELinBLUE\Crawler\Constraints;

use Symfony\Component\DomCrawler\Crawler;

abstract class PageConstraint
{
    abstract public function matches($crawler): bool;

    protected function html($crawler): string
    {
        return is_object($crawler) ? $crawler->html() : $crawler;
    }

    protected function text($crawler): string
    {
        return is_object($crawler) ? $crawler->text() : strip_tags($crawler);
    }

    protected function crawler($crawler): Crawler
    {
        return is_object($crawler) ? $crawler : new Crawler($crawler);
    }

    protected function getEscapedPattern(string $text): string
    {
        $rawPattern     = preg_quote($text, '/');
        $escapedPattern = preg_quote($this->convertToEntities($text), '/');

        return $rawPattern === $escapedPattern ? $rawPattern : "({$rawPattern}|{$escapedPattern})";
    }

    protected function convertToEntities(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }
}

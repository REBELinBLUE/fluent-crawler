<?php

namespace REBELinBLUE\Crawler\Constraints;

class HasLink extends PageConstraint
{
    /** @var string */
    protected $text;

    /** @var string|null */
    protected $url;

    public function __construct(string $text, ?string $url = null)
    {
        $this->url  = $url;
        $this->text = $text;
    }

    /**
     * Check if the link is found in the given crawler.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $links = $this->crawler($crawler)->selectLink($this->text);

        if ($links->count() === 0) {
            return false;
        }

        // If the URL is null we assume the developer only wants to find a link
        // with the given text regardless of the URL. So if we find the link
        // we will return true. Otherwise, we will look for the given URL.
        if (is_null($this->url)) {
            return true;
        }

        foreach ($links as $link) {
            $linkHref = $link->getAttribute('href');

            if ($linkHref === $this->url) {
                return true;
            }
        }

        return false;
    }
}

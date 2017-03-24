<?php

namespace REBELinBLUE\Crawler\Concerns;

use Symfony\Component\BrowserKit\Response;

trait InteractsWithResponse
{
    /**
     * Get all headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->getResponse()->getHeaders();
    }

    /**
     * Get the value of a header.
     *
     * @param  string      $headerName
     * @return null|string
     */
    public function getHeader(string $headerName): ?string
    {
        return $this->getResponse()->getHeader($headerName, true);
    }

    /**
     * Get the response object instance for the most recent request.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->getClient()->getResponse();
    }

    /**
     * Get all cookies.
     *
     * @return array
     */
    public function getCookies(): array
    {
        /** @var \Goutte\Client $client */
        $client  = $this->getClient();
        $uri     = $client->getRequest()->getUri();

        return $client->getCookieJar()->allValues($uri);
    }

    /**
     * Get the value of a cookie.
     *
     * @param  string      $cookieName
     * @return null|string
     */
    public function getCookie(string $cookieName): ?string
    {
        $cookies = $this->getCookies();

        if (isset($cookies[$cookieName])) {
            return $cookies[$cookieName];
        }

        return null;
    }
}

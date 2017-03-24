<?php

namespace REBELinBLUE\Crawler\Concerns;

trait ResponseAssertions
{
    /**
     * Check that the client response has an OK status code.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->isStatusCode(200);
    }

    /**
     * Check that the client response has a given code.
     *
     * @param  int  $code
     * @return bool
     */
    public function isStatusCode(int $code): bool
    {
        return $this->getClient()->getResponse()->getStatus() === $code;
    }

    /**
     * Checks that the response contains the given header and equals the optional value.
     *
     * @param  string $headerName
     * @param  mixed  $value
     * @return bool
     */
    public function hasHeader(string $headerName, ?string $value = null): bool
    {
        /** @var \Goutte\Client $client */
        $client = $this->getClient();

        $header = $client->getInternalResponse()->getHeader($headerName, true);

        if ($header) {
            return is_null($value) ? true : ($header === $value);
        }

        return false;
    }

    /**
     * Check that the response contains the given cookie and equals the optional value.
     *
     * @param  string $cookieName
     * @param  mixed  $value
     * @return bool
     */
    public function hasCookie(string $cookieName, ?string $value = null): bool
    {
        /** @var \Goutte\Client $client */
        $client = $this->getClient();

        $uri = $client->getRequest()->getUri();

        $cookies = $client->getCookieJar()->allValues($uri);

        if (isset($cookies[$cookieName])) {
            return is_null($value) ? true : ($cookies[$cookieName] === $value);
        }

        return false;
    }
}

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
     * @param  int  $statusCode
     * @return bool
     */
    public function isStatusCode(int $statusCode): bool
    {
        return $this->getResponse()->getStatus() === $statusCode;
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
        $header = $this->getHeader($headerName);

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
        $cookie = $this->getCookie($cookieName);

        if ($cookie) {
            return is_null($value) ? true : ($cookie === $value);
        }

        return false;
    }
}

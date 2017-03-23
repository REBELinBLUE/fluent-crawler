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
        return $this->seeStatusCode(200);
    }

    /**
     * Check that the client response has a given code.
     *
     * @param  int  $code
     * @return bool
     */
    public function isStatusCode(int $code): bool
    {
        return $this->getResponse()->getStatusCode() === $code;
    }

    /**
     * Checks that the response contains the given header and equals the optional value.
     *
     * @param  string $headerName
     * @param  mixed  $value
     * @return bool
     */
    protected function hasHeader(string $headerName, ?string $value = null): bool
    {
        //        $headers = $this->response->headers;
//        $this->assertTrue($headers->has($headerName), "Header [{$headerName}] not present on response.");
//        if (! is_null($value)) {
//            $this->assertEquals(
//                $headers->get($headerName),
//                $value,
//                "Header [{$headerName}] was found, but value [{$headers->get($headerName)}]
//                  "does not match [{$value}]."
//            );
//        }

        return false;
    }

    /**
     * Check that the response contains the given cookie and equals the optional value.
     *
     * @param  string $cookieName
     * @param  mixed  $value
     * @return $this
     */
    protected function seeCookie(string $cookieName, ?string $value = null): bool
    {
        //        $headers = $this->response->headers;
//        $exist = false;
//        foreach ($headers->getCookies() as $cookie) {
//            if ($cookie->getName() === $cookieName) {
//                $exist = true;
//                break;
//            }
//        }
//        $this->assertTrue($exist, "Cookie [{$cookieName}] not present on response.");
//        if (! $exist || is_null($value)) {
//            return $this;
//        }
//        $cookieValue = $cookie->getValue();
//        $actual = $cookieValue;
//        $this->assertEquals(
//            $actual,
//            $value,
//            "Cookie [{$cookieName}] was found, but value [{$actual}] does not match [{$value}]."
//        );
//        return $this;
        return false;
    }
}

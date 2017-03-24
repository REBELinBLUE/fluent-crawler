<?php

namespace REBELinBLUE\Crawler\Concerns;

use REBELinBLUE\Crawler\Crawler;

trait MakesHttpRequests
{
    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     * @return Crawler
     */
    public function visit(string $uri): Crawler
    {
        return $this->get($uri);
    }

    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     * @param  array   $headers
     * @return Crawler
     */
    public function get(string $uri, array $headers = []): Crawler
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->makeRequest('GET', $uri, [], $server);
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param  string  $uri
     * @param  array   $parameters
     * @param  array   $headers
     * @return Crawler
     */
    public function post(string $uri, array $parameters = [], array $headers = []): Crawler
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->makeRequest('POST', $uri, $parameters, $server);
    }

    /**
     * Visit the given URI with a PUT request.
     *
     * @param  string  $uri
     * @param  array   $parameters
     * @param  array   $headers
     * @return Crawler
     */
    public function put(string $uri, array $parameters = [], array $headers = []): Crawler
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->makeRequest('PUT', $uri, $parameters, $server);
    }

    /**
     * Visit the given URI with a PATCH request.
     *
     * @param  string  $uri
     * @param  array   $parameters
     * @param  array   $headers
     * @return Crawler
     */
    public function patch(string $uri, array $parameters = [], array $headers = []): Crawler
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->makeRequest('PATCH', $uri, $parameters, $server);
    }

    /**
     * Visit the given URI with a DELETE request.
     *
     * @param  string  $uri
     * @param  array   $parameters
     * @param  array   $headers
     * @return Crawler
     */
    public function delete(string $uri, array $parameters = [], array $headers = []): Crawler
    {
        $server = $this->transformHeadersToServerVars($headers);

        return $this->makeRequest('DELETE', $uri, $parameters, $server);
    }

    protected function makeRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?string $body = null
    ): Crawler {
        $this->resetPageContext();

        $this->crawler = $this->getClient()->request($method, $uri, $parameters, [], $headers, $body);

        $this->clearInputs();

        return $this;
    }

    protected function transformHeadersToServerVars(array $headers): array
    {
        $server = [];
        $prefix = 'HTTP_';

        foreach ($headers as $name => $value) {
            $name = strtr(strtoupper($name), '-', '_');

            if (!substr($name, 0, 5) !== $prefix && $name !== 'CONTENT_TYPE') {
                $name = $prefix . $name;
            }

            $server[$name] = $value;
        }

        return $server;
    }
}

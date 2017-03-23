<?php

namespace REBELinBLUE\Crawler\Concerns;

use REBELinBLUE\Crawler\Crawler;

trait MakesHttpRequests
{
    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     * @param  array   $headers
     * @return Crawler
     */
    public function get(string $uri, array $headers = []): self
    {
        return $this;
    }

    /**
     * Visit the given URI with a POST request.
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return $this
     */
    public function post(string $uri, array $data = [], array $headers = []): self
    {
        return $this;
    }

    /**
     * Visit the given URI with a PUT request.
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return $this
     */
    public function put(string $uri, array $data = [], array $headers = []): self
    {
        return $this;
    }

    /**
     * Visit the given URI with a PATCH request.
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return $this
     */
    public function patch(string $uri, array $data = [], array $headers = []): self
    {
        return $this;
    }

    /**
     * Visit the given URI with a DELETE request.
     *
     * @param  string $uri
     * @param  array  $data
     * @param  array  $headers
     * @return $this
     */
    public function delete(string $uri, array $data = [], array $headers = []): self
    {
        return $this;
    }
}

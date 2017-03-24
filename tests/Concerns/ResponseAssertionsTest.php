<?php

namespace REBELinBLUE\Crawler\Tests\Concerns;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use REBELinBLUE\Crawler\Tests\CrawlerTestAssertions;

class ResponseAssertionsTest extends CrawlerTestAssertions
{
    public function test_it_sees_an_ok_status()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'), 200);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertTrue($crawler->isOk());
    }

    public function test_it_sees_a_value_status()
    {
        // Arrange
        $expected = 201;
        $this->mockResponse($this->getFile('welcome.html'), $expected);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertTrue($crawler->isStatusCode($expected));
    }

    public function test_it_has_header()
    {
        // Arrange
        $headers  = ['E-Tag' => 'an-etag-hash'];
        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertTrue($crawler->hasHeader('E-Tag'));
    }

    public function test_it_doesnt_have_header()
    {
        // Arrange
        $headers  = ['E-Tag' => 'an-etag-hash'];
        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertFalse($crawler->hasHeader('Cache-Control'));
    }

    public function test_it_has_header_with_value()
    {
        // Arrange
        $headers  = ['E-Tag' => 'an-etag-hash'];
        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertTrue($crawler->hasHeader('E-Tag', 'an-etag-hash'));
    }

    public function test_it_doesnt_have_header_with_value()
    {
        // Arrange
        $headers  = ['E-Tag' => 'an-etag-hash'];
        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertFalse($crawler->hasHeader('E-Tag', 'another-hash'));
        $this->assertFalse($crawler->hasHeader('Cache-Control', 'an-etag-hash'));
    }

    public function test_it_has_cookie()
    {
        // Arrange
        $headers = [
            'Set-Cookie' => 'foo=bar; Path=/; Expires=Fri, 15 Jan 2021 22:00:00 GMT; Secure; HttpOnly',
        ];

        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertTrue($crawler->hasCookie('foo'));
    }

    public function test_it_has_cookie_with_value()
    {
        // Arrange
        $headers = [
            'Set-Cookie' => 'foo=bar; Path=/; Expires=Fri, 15 Jan 2021 22:00:00 GMT; Secure; HttpOnly',
        ];

        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertTrue($crawler->hasCookie('foo', 'bar'));
    }

    public function test_it_doesnt_have_cookie()
    {
        // Arrange
        $headers = [
            'Set-Cookie' => 'foo=bar; Path=/; Expires=Fri, 15 Jan 2021 22:00:00 GMT; Secure; HttpOnly',
        ];

        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertFalse($crawler->hasCookie('baz'));
    }

    public function test_it_doesnt_have_cookie_with_value()
    {
        // Arrange
        $headers = [
            'Set-Cookie' => 'foo=bar; Path=/; Expires=Fri, 15 Jan 2021 22:00:00 GMT; Secure; HttpOnly',
        ];

        $response = new GuzzleResponse(200, $headers, $this->getFile('welcome.html'));
        $this->mockResponses($response);

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $this->assertFalse($crawler->hasCookie('baz', 'bar'));
        $this->assertFalse($crawler->hasCookie('foo', 'qux'));
    }
}

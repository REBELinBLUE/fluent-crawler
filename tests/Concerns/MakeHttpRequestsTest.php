<?php

namespace REBELinBLUE\Crawler\Tests\Concerns;

use REBELinBLUE\Crawler\Tests\CrawlerTestAssertions;
use Symfony\Component\BrowserKit\Request;

class MakeHttpRequestsTest extends CrawlerTestAssertions
{
    /** @test */
    public function it_can_visit_url()
    {
        // Arrange
        $expected = $this->mockPageResponse();

        // Act
        $crawler = $this->crawler->visit('http://www.example.com');

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    /** @test */
    public function it_can_make_get_requests()
    {
        // Arrange
        $expected = $this->mockPageResponse();
        $headers  = $this->expectedHeaders();

        // Act
        $crawler = $this->crawler->get('http://www.example.com', $headers);
        $request = $crawler->getClient()->getHistory()->current();

        // Assert
        $this->assertResponseMatches($crawler, $expected);
        $this->assertRequestMatches($request, [], 'GET');
    }

    /** @test */
    public function it_can_make_post_requests()
    {
        // Arrange
        $expected      = $this->mockPageResponse();
        $headers       = $this->expectedHeaders();
        $parameters    = ['foo' => 'bar'];
        $content       = 'baz=qux';

        // Act
        $crawler = $this->crawler->post('http://www.example.com', $parameters, $headers, $content);
        $request = $crawler->getClient()->getHistory()->current();

        // Assert
        $this->assertResponseMatches($crawler, $expected);
        $this->assertRequestMatches($request, $parameters, 'POST', $content);
    }

    /** @test */
    public function it_can_make_put_requests()
    {
        // Arrange
        $expected      = $this->mockPageResponse();
        $headers       = $this->expectedHeaders();
        $parameters    = ['foo' => 'bar'];
        $content       = 'baz=qux';

        // Act
        $crawler = $this->crawler->put('http://www.example.com', $parameters, $headers, $content);
        $request = $crawler->getClient()->getHistory()->current();

        // Assert
        $this->assertResponseMatches($crawler, $expected);
        $this->assertRequestMatches($request, $parameters, 'PUT', $content);
    }

    /** @test */
    public function it_can_make_patch_requests()
    {
        // Arrange
        $expected      = $this->mockPageResponse();
        $headers       = $this->expectedHeaders();
        $parameters    = ['foo' => 'bar'];
        $content       = 'baz=qux';

        // Act
        $crawler = $this->crawler->patch('http://www.example.com', $parameters, $headers, $content);
        $request = $crawler->getClient()->getHistory()->current();

        // Assert
        $this->assertResponseMatches($crawler, $expected);
        $this->assertRequestMatches($request, $parameters, 'PATCH', $content);
    }

    /** @test */
    public function it_can_make_delete_requests()
    {
        // Arrange
        $expected      = $this->mockPageResponse();
        $headers       = $this->expectedHeaders();
        $parameters    = ['foo' => 'bar'];
        $content       = 'baz=qux';

        // Act
        $crawler = $this->crawler->delete('http://www.example.com', $parameters, $headers, $content);
        $request = $crawler->getClient()->getHistory()->current();

        // Assert
        $this->assertResponseMatches($crawler, $expected);
        $this->assertRequestMatches($request, $parameters, 'DELETE', $content);
    }

    private function mockPageResponse()
    {
        $expected = $this->getFile('welcome.html');

        $this->mockResponse($expected);

        return $expected;
    }

    private function assertRequestMatches(Request $request, array $parameters, string $method, string $content = null)
    {
        $server = $request->getServer();

        $this->assertSame('application/json', $server['CONTENT_TYPE']);
        $this->assertSame('a-response-etag-hash', $server['HTTP_IF_MATCH']);

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($parameters, $request->getParameters());
        $this->assertSame($content, $request->getContent());
    }

    private function expectedHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'If-Match'     => 'a-response-etag-hash',
        ];
    }
}

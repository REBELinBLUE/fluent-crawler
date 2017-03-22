<?php

namespace REBELinBLUE\Crawler\Tests;

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit_Framework_TestCase;
use REBELinBLUE\Crawler\Crawler;
use Symfony\Component\BrowserKit\Response;

class CrawlerTest extends PHPUnit_Framework_TestCase
{
    /** @var GoutteClient $client */
    private $client;

    /** @var Crawler $crawler */
    private $crawler;

    /** @var array */
    private $history = [];

    /** @var MockHandler */
    private $mock;

    public function setUp()
    {
        $this->client  = new GoutteClient();
        $this->crawler = new Crawler($this->client);
    }

    public function test_it_can_visit_url()
    {
        $expected = '<html><body><p>Hi</p></body></html>';

        // Arrange
        $this->mockResponse($expected);

        // Act
        $crawler = $this->crawler->visit('http://www.example.com');

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

//    public function test_visit_throws_exception_on_error()
//    {
//
//    }

    private function assertResponseMatches(Crawler $crawler, string $expected)
    {
        $this->assertSame($this->crawler, $crawler);
        $this->assertInstanceOf(Response::class, $crawler->getResponse());
        $this->assertSame($expected, $crawler->getResponse()->getContent());
    }

    private function mockResponse(string $body, int $status = 200)
    {
        $responses = [new GuzzleResponse($status, [], $body)];
        $this->client->setClient($this->getGuzzle($responses));
    }

    private function getGuzzle(array $responses = []): GuzzleClient
    {
        if (empty($responses)) {
            $responses = [new GuzzleResponse(200, [], '<html><body><p>Hi</p></body></html>')];
        }

        $this->mock    = new MockHandler($responses);
        $handlerStack  = HandlerStack::create($this->mock);
        $this->history = [];

        $handlerStack->push(Middleware::history($this->history));
        $guzzle = new GuzzleClient([
            'redirect.disable' => true,
            'base_uri'         => '',
            'handler'          => $handlerStack,
        ]);

        return $guzzle;
    }
}

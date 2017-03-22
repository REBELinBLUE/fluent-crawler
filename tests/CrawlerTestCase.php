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

abstract class CrawlerTestCase extends PHPUnit_Framework_TestCase
{
    /** @var GoutteClient $client */
    protected $client;

    /** @var Crawler $crawler */
    protected $crawler;

    /** @var array */
    protected $history = [];

    /** @var MockHandler */
    protected $mock;

    public function setUp()
    {
        $this->client  = new GoutteClient();
        $this->crawler = new Crawler($this->client);
    }

    protected function assertResponseMatches(Crawler $crawler, string $expected)
    {
        $this->assertSame($this->crawler, $crawler);
        $this->assertInstanceOf(Response::class, $crawler->getResponse());
        $this->assertSame($expected, $crawler->getResponse()->getContent());
    }

    protected function mockResponse(string $body, int $status = 200)
    {
        $responses = [new GuzzleResponse($status, [], $body)];
        $this->mockResponses($responses);
    }

    /**
     * @param GuzzleResponse[] $responses
     */
    protected function mockResponses(array $responses)
    {
        $this->client->setClient($this->getGuzzle($responses));
    }

    protected function getGuzzle(array $responses = []): GuzzleClient
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

<?php

namespace REBELinBLUE\Crawler\Tests;

use Goutte\Client;
use REBELinBLUE\Crawler\Crawler;

class CrawlerTest extends CrawlerTestAssertions
{
    public function test_it_creates_new_client_when_not_provided()
    {
        // Arrange
        $crawler = new Crawler();
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $client = $crawler->getClient();

        // Assert
        $this->assertInstanceOf(Client::class, $client);
        $this->assertNotSame($this->client, $client);
        $this->assertNotSame(
            $client,
            $client->getClient(),
            'Subsequent call to getClient() unexpectedly created a new client'
        );
    }

    public function test_it_returns_correct_client()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $this->crawler->visit('http://www.example.com');

        // Assert
        $this->assertSame($this->client, $this->crawler->getClient());
    }
}

<?php

namespace REBELinBLUE\Crawler\Tests;

class CrawlerTest extends CrawlerTestAssertions
{
    public function test_it_returns_correct_client()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $this->crawler->visit('http://www.example.com');

        // Assert
        $this->assertSame($this->client, $this->crawler->getClient());
    }

    public function test_it_can_visit_url()
    {
        // Arrange
        $expected = $this->getFile('welcome.html');
        $this->mockResponse($expected);

        // Act
        $crawler = $this->crawler->visit('http://www.example.com');

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }
}

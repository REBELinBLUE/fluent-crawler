<?php

namespace REBELinBLUE\Crawler\Tests\Concerns;

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
}

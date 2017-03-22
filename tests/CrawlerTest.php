<?php

namespace REBELinBLUE\Crawler\Tests;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use InvalidArgumentException;

class CrawlerTest extends CrawlerTestAssertions
{
    public function test_it_can_visit_url()
    {
        $expected = $this->getFile('welcome.html');

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

    public function test_it_can_submit_form_with_button_label()
    {
        $expected = $this->getFile('done.html');

        // Arrange
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('form.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler
                        ->visit('http://example.com/form')
                        ->submitForm('Login', ['name' => 'Joe Bloggs']);

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_can_submit_form_without_button_label()
    {
        $expected = $this->getFile('done.html');

        // Arrange
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('form.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler
                        ->visit('http://example.com/form')
                        ->submitForm(['name' => 'value']);

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_throws_an_invalid_argument_exception_when_form_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('welcome.html')),
        ]);

        // Act
        $this->crawler->visit('http://example.com')->submitForm(['name' => 'Joe Bloggs']);
    }

    public function test_it_throws_an_invalid_argument_exception_when_named_form_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('form.html')),
        ]);

        // Act
        $this->crawler->visit('http://example.com')->submitForm('Submit', ['name' => 'Joe Bloggs']);
    }

    public function test_it_fills_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->type('Joe Bloggs', 'name');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    public function test_it_throws_an_invalid_argument_exception_when_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->type('Joe Bloggs', 'forename');
    }
}

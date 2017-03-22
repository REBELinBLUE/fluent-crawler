<?php

namespace REBELinBLUE\Crawler\Tests;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

class CrawlerTest extends CrawlerTestCase
{
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

    public function test_it_can_submit_form_with_button_label()
    {
        $expected = '<p>Login done!</p>';

        // Arrange
        $this->mockResponses([
            new GuzzleResponse(200, [], '<body><form id="my-form"><input type="text" name="name" /><input type="submit" value="Login" /></form>'),
            new GuzzleResponse(201, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler
                        ->visit('http://example.com/form')
                        ->submitForm('Login', ['name' => 'value']);

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

//    public function test_it_can_submit_form_without_button_label()
//    {
//        $expected = '<p>Login done!</p>';
//
//        // Arrange
//        $this->mockResponses([
//            new GuzzleResponse(200, [], '<body><form id="my-form"><input type="text" name="name" /><input type="submit" value="Login" /></form>'),
//            new GuzzleResponse(201, [], $expected),
//        ]);
//
//        // Act
//        $crawler = $this->crawler
//            ->visit('http://example.com/form')
//            ->submitForm(false, ['name' => 'value']);
//
//        // Assert
//        $this->assertResponseMatches($crawler, $expected);
//    }
}

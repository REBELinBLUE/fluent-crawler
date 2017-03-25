<?php

namespace REBELinBLUE\Crawler\Tests\Concerns;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use InvalidArgumentException;
use REBELinBLUE\Crawler\Tests\CrawlerTestAssertions;
use Symfony\Component\DomCrawler\Crawler;

class InteractsWithPageTest extends CrawlerTestAssertions
{
    public function test_it_can_submit_form_with_button_value()
    {
        // Arrange
        $expected = $this->getFile('done.html');
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('form.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler
            ->visit('http://example.com/form')
            ->submitForm('Login', [
                'name'       => 'Joe Bloggs',
                'confirm'    => true,
                'newsletter' => false,
                'country'    => 'uk',
            ]);

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_can_submit_form_without_button_value()
    {
        // Arrange
        $expected = $this->getFile('done.html');
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('form.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler
            ->visit('http://example.com/form')
            ->submitForm([
                'name'       => 'Joe Bloggs',
                'confirm'    => true,
                'newsletter' => false,
                'country'    => 'uk',
            ]);

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_throws_an_invalid_argument_exception_when_form_is_missing()
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

    public function test_it_throws_an_invalid_argument_exception_when_named_form_is_missing()
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

    public function test_it_types_into_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->type('Joe Bloggs', 'name');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    public function test_it_throws_an_invalid_argument_exception_when_typable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->type('Joe Bloggs', 'forename');
    }

    public function test_it_checks_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->check('confirm');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    public function test_it_throws_an_invalid_argument_exception_when_checkable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->check('accept');
    }

    public function test_it_unchecks_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->uncheck('newsletter');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    public function test_it_throws_an_invalid_argument_exception_when_uncheckable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->uncheck('accept');
    }

    public function test_it_selects_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->select('uk', 'country');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    public function test_it_throws_an_invalid_argument_exception_when_selectable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->select('mr', 'title');
    }

    public function test_it_presses_a_button()
    {
        // Arrange
        $expected = $this->getFile('done.html');
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('form.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler->visit('http://example.com')->press('Login');

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_throws_an_invalid_argument_exception_when_pressing_a_missing_button()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->press('Submit');
    }

    public function test_it_can_click_link_with_text()
    {
        // Arrange
        $expected = $this->getFile('done.html');
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('link.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler->visit('http://example.com')->click('Click here');

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_can_click_a_link_with_selector()
    {
        // Arrange
        $expected = $this->getFile('done.html');
        $this->mockResponses([
            new GuzzleResponse(200, [], $this->getFile('link.html')),
            new GuzzleResponse(200, [], $expected),
        ]);

        // Act
        $crawler = $this->crawler->visit('http://example.com')->click('continue');

        // Assert
        $this->assertResponseMatches($crawler, $expected);
    }

    public function test_it_throws_an_invalid_argument_exception_when_clicking_a_missing_link()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('link.html'));

        // Act
        $this->crawler->visit('http://example.com')->click('Log out');
    }

    public function test_it_acts_on_element_within_element()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->within('#container', function () {
            $this->crawler->type('Joe Bloggs', 'name');
        });

        // Assert
        $this->assertIsCrawler($crawler);
    }

    public function test_it_throws_an_invalid_argument_exception_when_acting_within_a_missing_element()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $this->crawler->visit('http://example.com')->within('#myForm', function () {
            $this->crawler->type('Joe Bloggs', 'name');
        });
    }

    public function test_it_can_filter()
    {
        // Arrange
        $this->mockResponse($this->getFile('list.html'));

        // Act
        $values   = [];
        $callback = function (Crawler $element) use (&$values) {
            $this->assertInstanceOf(Crawler::class, $element);
            $values = $element->filter('li')->each(function (Crawler $node) {
                return trim($node->text());
            });
        };

        $crawler = $this->crawler->visit('http://example.com')->filter('ul#container', $callback);

        // Assert
        $this->assertIsCrawler($crawler);
        $this->assertSame(['Foo', 'Bar', 'Baz', 'Qux'], $values);
    }
}

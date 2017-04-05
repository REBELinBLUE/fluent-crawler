<?php

namespace REBELinBLUE\Crawler\Tests\Concerns;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use InvalidArgumentException;
use REBELinBLUE\Crawler\Tests\CrawlerTestAssertions;
use Symfony\Component\DomCrawler\Crawler;

class InteractsWithPageTest extends CrawlerTestAssertions
{
    /** @test */
    public function it_can_submit_form_with_button_value()
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

    /** @test */
    public function it_can_submit_form_without_button_value()
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

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_form_is_missing()
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

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_named_form_is_missing()
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

    /** @test */
    public function it_types_into_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->type('Joe Bloggs', 'name');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_typable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->type('Joe Bloggs', 'forename');
    }

    /** @test */
    public function it_checks_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->check('confirm');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_checkable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->check('accept');
    }

    /** @test */
    public function it_unchecks_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->uncheck('newsletter');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_uncheckable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->uncheck('accept');
    }

    /** @test */
    public function it_selects_a_form_field()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com')->select('uk', 'country');

        // Assert
        $this->assertIsCrawler($crawler);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_selectable_form_field_is_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->select('mr', 'title');
    }

    /** @test */
    public function it_presses_a_button()
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

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_pressing_a_missing_button()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->press('Submit');
    }

    /** @test */
    public function it_can_click_link_with_text()
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

    /** @test */
    public function it_can_click_a_link_with_selector()
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

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_clicking_a_missing_link()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('link.html'));

        // Act
        $this->crawler->visit('http://example.com')->click('Log out');
    }

    /** @test */
    public function it_acts_on_element_within_element()
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

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_acting_within_a_missing_element()
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

    /** @test */
    public function it_can_filter()
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

    /** @test */
    public function it_can_extract()
    {
        // Arrange
        $this->mockResponse($this->getFile('list.html'));

        // Act
        $actual = $this->crawler->visit('http://example.com')
                                ->extract('ul#container', function (Crawler $element) {
                                    $this->assertInstanceOf(Crawler::class, $element);

                                    return $element->filter('li')->each(function (Crawler $node) {
                                        return trim($node->text());
                                    });
                                });

        // Assert
        $this->assertSame(['Foo', 'Bar', 'Baz', 'Qux'], $actual);
    }
}

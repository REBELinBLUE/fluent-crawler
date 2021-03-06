<?php

namespace REBELinBLUE\Crawler\Tests\Concerns;

use InvalidArgumentException;
use REBELinBLUE\Crawler\Tests\CrawlerTestAssertions;

class PageAssertionsTest extends CrawlerTestAssertions
{
    /** @test */
    public function it_sees_string_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = '<h1>Hello world!</h1>';
        $this->assertTrue($crawler->see($expected));
        $this->assertFalse($crawler->dontSee($expected));
    }

    /** @test */
    public function it_does_not_see_string_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = '<p>Foo bar</p>';
        $this->assertTrue($crawler->dontSee($notExpected));
        $this->assertFalse($crawler->see($notExpected));
    }

    /** @test */
    public function it_sees_text_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Hello world!';
        $this->assertTrue($crawler->seeText($expected));
        $this->assertFalse($crawler->dontSeeText($expected));
    }

    /** @test */
    public function it_does_not_see_text_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = 'Foo bar';
        $this->assertTrue($crawler->dontSeeText($notExpected));
        $this->assertFalse($crawler->seeText($notExpected));
    }

    /** @test */
    public function it_sees_element_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = '#container';
        $this->assertTrue($crawler->seeElement($expected));
        $this->assertFalse($crawler->dontSeeElement($expected));
    }

    /** @test */
    public function it_sees_element_with_attribute_value_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $attributes = ['align' => 'center'];
        $selector   = 'p';
        $this->assertTrue($crawler->seeElement($selector, $attributes));
        $this->assertFalse($crawler->dontSeeElement($selector, $attributes));
    }

    /** @test */
    public function it_sees_element_with_attribute_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $attributes = ['align'];
        $selector   = 'p';
        $this->assertTrue($crawler->seeElement($selector, $attributes));
        $this->assertFalse($crawler->dontSeeElement($selector, $attributes));
    }

    /** @test */
    public function it_does_not_see_element_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = '#banner';
        $this->assertTrue($crawler->dontSeeElement($notExpected));
        $this->assertFalse($crawler->seeElement($notExpected));
    }

    /** @test */
    public function it_does_not_see_element_with_attribute_value_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $attributes = ['align' => 'right'];
        $selector   = 'p';
        $this->assertTrue($crawler->dontSeeElement($selector, $attributes));
        $this->assertFalse($crawler->seeElement($selector, $attributes));
    }

    /** @test */
    public function it_does_not_see_element_with_attribute_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $attributes = ['style'];
        $selector   = 'p';
        $this->assertTrue($crawler->dontSeeElement($selector, $attributes));
        $this->assertFalse($crawler->seeElement($selector, $attributes));
    }

    /** @test */
    public function it_sees_string_in_element()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Enter your name';
        $selector = '#container > p';
        $this->assertTrue($crawler->seeInElement($selector, $expected));
        $this->assertFalse($crawler->dontSeeInElement($selector, $expected));
    }

    /** @test */
    public function it_does_not_see_string_in_element()
    {
        // Arrange
        $this->mockResponse($this->getFile('welcome.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = 'Hello world!';
        $selector    = '#container > p';
        $this->assertTrue($crawler->dontSeeInElement($selector, $notExpected));
        $this->assertFalse($crawler->seeInElement($selector, $notExpected));
    }

    /** @test */
    public function it_sees_link_with_text_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('link.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Click here';
        $this->assertTrue($crawler->seeLink($expected));
        $this->assertFalse($crawler->dontSeeLink($expected));
    }

    /** @test */
    public function it_does_not_see_link_with_text_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('link.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = 'Go away';
        $this->assertTrue($crawler->dontSeeLink($notExpected));
        $this->assertFalse($crawler->seeLink($notExpected));
    }

    /** @test */
    public function it_sees_link_with_url_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('link.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'done.html';
        $text     = 'Click here';
        $this->assertTrue($crawler->seeLink($text, $expected));
        $this->assertFalse($crawler->dontSeeLink($text, $expected));
    }

    /** @test */
    public function it_does_not_sees_link_with_url_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('link.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = 'login.html';
        $text        = 'Click here';
        $this->assertTrue($crawler->dontSeeLink($text, $notExpected));
        $this->assertFalse($crawler->seeLink($text, $notExpected));
    }

    /** @test */
    public function it_sees_field_with_value_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Bob Smith';
        $field    = 'name';
        $this->assertTrue($crawler->seeInField($field, $expected));
        $this->assertFalse($crawler->dontSeeInField($field, $expected));
    }

    /** @test */
    public function it_does_not_see_field_with_value_in_page()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = 'John Smith';
        $field       = 'name';
        $this->assertTrue($crawler->dontSeeInField($field, $notExpected));
        $this->assertFalse($crawler->seeInField($field, $notExpected));
    }

    /** @test */
    public function it_sees_option_with_value_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'fr';
        $selector = 'country';
        $this->assertTrue($crawler->seeIsSelected($selector, $expected));
        $this->assertFalse($crawler->dontSeeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_sees_option_with_text_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Spring';
        $selector = 'season';
        $this->assertTrue($crawler->seeIsSelected($selector, $expected));
        $this->assertFalse($crawler->dontSeeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_sees_grouped_option_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Egg';
        $selector = 'food';
        $this->assertTrue($crawler->seeIsSelected($selector, $expected));
        $this->assertFalse($crawler->dontSeeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_does_not_see_option_with_value_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $notExpected = 'uk';
        $selector    = 'country';
        $this->assertTrue($crawler->dontSeeIsSelected($selector, $notExpected));
        $this->assertFalse($crawler->seeIsSelected($selector, $notExpected));
    }

    /** @test */
    public function it_does_not_see_option_with_text_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Winter';
        $selector = 'season';
        $this->assertTrue($crawler->dontSeeIsSelected($selector, $expected));
        $this->assertFalse($crawler->seeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_does_not_see_grouped_option_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'Onions';
        $selector = 'food';
        $this->assertTrue($crawler->dontSeeIsSelected($selector, $expected));
        $this->assertFalse($crawler->seeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_sees_checkbox_is_checked()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'newsletter';
        $this->assertTrue($crawler->seeIsChecked($expected));
        $this->assertFalse($crawler->dontSeeIsChecked($expected));
    }

    /** @test */
    public function it_does_not_see_checkbox_is_checked()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'confirm';
        $this->assertTrue($crawler->dontSeeIsChecked($expected));
        $this->assertFalse($crawler->seeIsChecked($expected));
    }

    /** @test */
    public function it_sees_radiobox_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'm';
        $selector = 'sex';
        $this->assertTrue($crawler->seeIsSelected($selector, $expected));
        $this->assertFalse($crawler->dontSeeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_does_not_see_radiobox_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'f';
        $selector = 'sex';
        $this->assertTrue($crawler->dontSeeIsSelected($selector, $expected));
        $this->assertFalse($crawler->seeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_does_not_see_radiobox_is_selected_when_none_is_selected()
    {
        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $crawler = $this->crawler->visit('http://example.com');

        // Assert
        $expected = 'red';
        $selector = 'colour';
        $this->assertTrue($crawler->dontSeeIsSelected($selector, $expected));
        $this->assertFalse($crawler->seeIsSelected($selector, $expected));
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_when_field_missing()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $this->mockResponse($this->getFile('form.html'));

        // Act
        $this->crawler->visit('http://example.com')->seeInField('surname', 'Smith');
    }
}

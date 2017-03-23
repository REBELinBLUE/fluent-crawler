<?php

namespace REBELinBLUE\Crawler;

use Goutte\Client;
use InvalidArgumentException;
use REBELinBLUE\Crawler\Concerns\InteractsWithPage;
use REBELinBLUE\Crawler\Concerns\MakesHttpRequests;
use REBELinBLUE\Crawler\Concerns\PageAssertions;
use REBELinBLUE\Crawler\Concerns\ResponseAssertions;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\DomCrawler\Form;

/**
 * A fluent interface to Symfony Crawler using Goutte.
 *
 * @todo: Add the ability to get an element
 */
class Crawler
{
    use PageAssertions, InteractsWithPage,
        ResponseAssertions, MakesHttpRequests;

    /** @var DomCrawler $crawler */
    protected $crawler;

    /** @var DomCrawler[] $subCrawlers */
    protected $subCrawlers = [];

    /** @var array $inputs  */
    protected $inputs = [];

    /** @var Client $client */
    private $client;

    /**
     * Constructor can take an instance of \Goutte\Client,
     * otherwise one will be initialized when needed.
     */
    public function __construct(?Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * Visit the given URI with a GET request.
     *
     * @param  string  $uri
     * @return Crawler
     */
    public function visit(string $uri): self
    {
        return $this->makeRequest('GET', $uri);
    }

    /**
     * Get the HTTP client instance.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }

    protected function makeRequest(string $method, string $uri, array $parameters = []): self
    {
        $this->resetPageContext();

        $this->crawler = $this->getClient()->request($method, $uri, $parameters);

        $this->clearInputs();

        return $this;
    }

    protected function makeRequestUsingForm(Form $form): self
    {
        return $this->makeRequest(
            $form->getMethod(),
            $form->getUri(),
            $this->extractParametersFromForm($form)
        );
    }

    /**
     * @param  string|array $buttonText
     * @param  array|null   $inputs
     * @return $this
     */
    protected function fillForm($buttonText, ?array $inputs = [])
    {
        if (!is_string($buttonText)) {
            $inputs     = $buttonText;
            $buttonText = null;
        }

        return $this->getForm($buttonText)->setValues($inputs);
    }

    protected function getForm(?string $buttonText = null): Form
    {
        try {
            if ($buttonText) {
                return $this->crawler()->selectButton($buttonText)->form();
            }

            return $this->crawler()->filter('form')->form();
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                'Could not find a form that has submit button' . ($buttonText ? "[{$buttonText}]" : '')
            );
        }
    }

    protected function extractParametersFromForm(Form $form): array
    {
        parse_str(http_build_query($form->getValues()), $parameters);

        return $parameters;
    }

    protected function storeInput(string $element, string $text): self
    {
        $this->assertFilterProducesResults($element);

        $element = str_replace(['#', '[]'], '', $element);

        $this->inputs[$element] = $text;

        return $this;
    }

    protected function clearInputs(): self
    {
        $this->inputs = [];

        return $this;
    }

    protected function assertFilterProducesResults(string $filter)
    {
        $crawler = $this->filterByNameOrId($filter);

        if (!count($crawler)) {
            throw new InvalidArgumentException("Nothing matched the filter [{$filter}] CSS query provided");
        }
    }

    protected function filterByNameOrId(string $name, string $elements = '*'): DomCrawler
    {
        $name       = str_replace('#', '', $name);
        $identifier = str_replace(['[', ']'], ['\\[', '\\]'], $name);
        $elements   = is_array($elements) ? $elements : [$elements];

        array_walk($elements, function (&$element) use ($name, $identifier) {
            $element = "{$element}#{$identifier}, {$element}[name='{$name}']";
        });

        return $this->crawler()->filter(implode(', ', $elements));
    }

    protected function resetPageContext()
    {
        $this->crawler     = null;
        $this->subCrawlers = [];
    }

    protected function crawler()
    {
        if (!empty($this->subCrawlers)) {
            return end($this->subCrawlers);
        }

        return $this->crawler;
    }
}

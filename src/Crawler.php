<?php

namespace REBELinBLUE\Crawler;

use Closure;
use Goutte\Client;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Symfony\Component\DomCrawler\Form;

/**
 * A fluent interface to Symfony Crawler using Goutte.
 */
class Crawler
{
    /** @var DomCrawler[] */
    protected $subCrawlers = [];

    /** @var array  */
    protected $inputs = [];

    /** @var DomCrawler */
    private $crawler;

    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function visit(string $uri): self
    {
        return $this->makeRequest('GET', $uri);
    }

    /**
     * @param string|array $buttonText
     * @param array|null   $inputs
     *
     * @return Crawler
     */
    public function submitForm($buttonText, ?array $inputs = [])
    {
        return $this->makeRequestUsingForm($this->fillForm($buttonText, $inputs));
    }

    public function click(string $name)
    {
        $link = $this->crawler()->selectLink($name);

        if (!count($link)) {
            $link = $this->filterByNameOrId($name, 'a');

            if (!count($link)) {
                throw new InvalidArgumentException(
                    "Could not find a link with a body, name, or ID attribute of [{$name}]."
                );
            }
        }

        $this->visit($link->link()->getUri());

        return $this;
    }

    public function type(string $text, string $element): self
    {
        return $this->storeInput($element, $text);
    }

    public function check(string $element): self
    {
        return $this->storeInput($element, true);
    }

    public function uncheck(string $element): self
    {
        return $this->storeInput($element, false);
    }

    public function select(string $option, string $element): self
    {
        return $this->storeInput($element, $option);
    }

    public function press(string $buttonText): self
    {
        return $this->submitForm($buttonText, $this->inputs);
    }

    public function getResponse(): Response
    {
        return $this->client->getResponse();
    }

    public function within($element, Closure $callback)
    {
        $this->subCrawlers[] = $this->crawler()->filter($element);

        $callback();

        array_pop($this->subCrawlers);

        return $this;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    protected function makeRequest(string $method, string $uri, array $parameters = []): self
    {
        $this->resetPageContext();

        $this->crawler = $this->client->request($method, $uri, $parameters);

        $this->clearInputs(); //->followRedirects();

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

//
//    public function followRedirects(): self
//    {
//        while ($this->response->isRedirect()) {
//            $this->makeRequest('GET', $this->response->getTargetUrl());
//        }
//
//        return $this;
//    }

    protected function crawler()
    {
        if (!empty($this->subCrawlers)) {
            return end($this->subCrawlers);
        }

        return $this->crawler;
    }
}

<?php

namespace REBELinBLUE\Crawler;

use Closure;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;
use Goutte\Client;
use Symfony\Component\DomCrawler\Form;

class Crawler
{
    /** @var DomCrawler */
    private $crawler;

    /** @var DomCrawler[] */
    protected $subCrawlers = [];

    /** @var array  */
    protected $inputs = [];

    /** @var Client */
    private $client;

    /** @var Response */
    private $response;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function visit(string $uri): self
    {
        return $this->makeRequest('GET', $uri);
    }

    public function submitForm(string $buttonText, array $inputs = [])
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
        return $this->response;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    protected function makeRequest(string $method, string $uri, array $parameters = []): self
    {
        $this->resetPageContext();

        $this->crawler = $this->client->request($method, $uri, $parameters);
        $this->response = $this->client->getResponse();

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

    protected function fillForm(string $buttonText, $inputs = [])
    {
        if (!is_string($buttonText)) {
            $inputs = $buttonText;
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
            throw new InvalidArgumentException("Could not find a form that has submit button [{$buttonText}].");
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
            throw new InvalidArgumentException(
                "Nothing matched the filter [{$filter}] CSS query provided for ..." // [{$this->currentUri}]."
            );
        }
    }

    protected function filterByNameOrId(string $name, string $elements = '*'): DomCrawler
    {
        $name      = str_replace('#', '', $name);
        $identifer = str_replace(['[', ']'], ['\\[', '\\]'], $name);
        $elements  = is_array($elements) ? $elements : [$elements];

        array_walk($elements, function (&$element) use ($name, $identifer) {
            $element = "{$element}#{$identifer}, {$element}[name='{$name}']";
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

    public function within($element, Closure $callback)
    {
        $this->subCrawlers[] = $this->crawler()->filter($element);

        $callback();

        array_pop($this->subCrawlers);
        return $this;
    }
}

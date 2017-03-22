<?php

namespace REBELinBLUE\Crawler\Constraints;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class IsSelected extends FormFieldConstraint
{
    /**
     * Determine if the select or radio element is selected.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler|string $crawler
     * @return bool
     */
    public function matches($crawler): bool
    {
        $crawler = $this->crawler($crawler);

        return in_array($this->value, $this->getSelectedValue($crawler), true);
    }

    public function getSelectedValue(Crawler $crawler): array
    {
        $field = $this->field($crawler);

        return $field->nodeName() === 'select'
            ? $this->getSelectedValueFromSelect($field)
            : [$this->getCheckedValueFromRadioGroup($field)];
    }

    protected function validElements(): array
    {
        return ['select', 'input[type="radio"]'];
    }

    protected function getSelectedValueFromSelect(Crawler $select): array
    {
        $selected = [];

        foreach ($select->children() as $option) {
            if ($option->nodeName === 'optgroup') {
                foreach ($option->childNodes as $child) {
                    if ($child->hasAttribute('selected')) {
                        $selected[] = $this->getOptionValue($child);
                    }
                }
            } elseif ($option->hasAttribute('selected')) {
                $selected[] = $this->getOptionValue($option);
            }
        }

        return $selected;
    }

    protected function getOptionValue(DOMElement $option): string
    {
        if ($option->hasAttribute('value')) {
            return $option->getAttribute('value');
        }

        return $option->textContent;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler $radioGroup
     * @return string|null
     */
    protected function getCheckedValueFromRadioGroup(Crawler $radioGroup): ?string
    {
        foreach ($radioGroup as $radio) {
            if ($radio->hasAttribute('checked')) {
                return $radio->getAttribute('value');
            }
        }
    }
}

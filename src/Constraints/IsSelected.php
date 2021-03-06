<?php

namespace REBELinBLUE\Crawler\Constraints;

use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class IsSelected extends FormFieldConstraint
{
    /**
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
        return [
            'select',
            'input[type="radio"]',
        ];
    }

    protected function getSelectedValueFromSelect(Crawler $select): array
    {
        $selected = [];

        foreach ($select->children() as $option) {
            if ($option->nodeName === 'optgroup') {
                foreach ($option->childNodes as $child) {
                    if ($child instanceof DOMElement && $child->hasAttribute('selected')) {
                        $selected[] = $this->getOptionValue($child);
                    }
                }
            } elseif ($option instanceof DOMElement && $option->hasAttribute('selected')) {
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

    protected function getCheckedValueFromRadioGroup(Crawler $radioGroup): ?string
    {
        foreach ($radioGroup as $radio) {
            if ($radio instanceof DOMElement && $radio->hasAttribute('checked')) {
                return $radio->getAttribute('value');
            }
        }

        return null;
    }
}

<?php

namespace REBELinBLUE\Crawler\Constraints;

class ReversePageConstraint extends PageConstraint
{
    /** @var PageConstraint */
    protected $pageConstraint;

    public function __construct(PageConstraint $pageConstraint)
    {
        $this->pageConstraint = $pageConstraint;
    }

    public function matches($crawler): bool
    {
        return !$this->pageConstraint->matches($crawler);
    }
}

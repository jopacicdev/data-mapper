<?php

namespace G4\DataMapper\Engine\MySQL;

use G4\DataMapper\Common\SelectionFactoryInterface;
use G4\DataMapper\Common\SelectionIdentityInterface;
use G4\DataMapper\Common\Selection\Comparison;
use G4\DataMapper\Engine\MySQL\MySQLComparisonFormatter;
use G4\DataMapper\Common\Selection\Sort;

class MySQLSelectionFactory implements SelectionFactoryInterface
{

    /**
     * @var SelectionIdentityInterface
     */
    private $identity;

    /**
     * @param SelectionIdentityInterface $identity
     */
    public function __construct(SelectionIdentityInterface $identity)
    {
        $this->identity = $identity;
    }

    public function fields()
    {

    }

    public function group()
    {
        return $this->identity->getGrouping();
    }

    public function sort()
    {
        $rawSorting = $this->identity->getSorting();

        if (empty($rawSorting)) {
            return [];
        }

        $sorting = [];

        foreach ($rawSorting as $oneSort) {
            if ($oneSort instanceof Sort) {
                $sorting[] = $oneSort->getSort($this->makeSortingFormatter());
            }
        }
        return $sorting;
    }

    public function where()
    {
        if ($this->identity->isVoid()) {
            return '1';
        }

        $comparisons = [];

        foreach ($this->identity->getComparisons() as $oneComparison) {
            if ($oneComparison instanceof Comparison) {
                $comparisons[] = $oneComparison->getComparison($this->makeComparisonFormatter());
            }
        }
        return join(' AND ', $comparisons);
    }

    public function limit()
    {
        return (int) $this->identity->getLimit();
    }

    public function makeComparisonFormatter()
    {
        return new MySQLComparisonFormatter();
    }

    public function makeSortingFormatter()
    {
        return new MySQLSortingFormatter();
    }

    public function offset()
    {
        return (int) $this->identity->getOffset();
    }
}
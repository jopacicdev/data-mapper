<?php

namespace G4\DataMapper\Engine\Solr\Operators;

use G4\DataMapper\Common\SingleValue;
use G4\DataMapper\Common\QueryConnector;
use G4\DataMapper\Common\QueryOperatorInterface;

class GreaterThanOrEqualOperator implements QueryOperatorInterface
{
    private $name;

    private $value;

    public function __construct($name, SingleValue $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function format()
    {
        return $this->name
            . QueryConnector::COLON
            . QueryConnector::SQUARE_BRACKET_OPEN
            . $this->value
            . QueryConnector::EMPTY_SPACE
            . QueryConnector::CONNECTOR_TO
            . QueryConnector::EMPTY_SPACE
            . QueryConnector::WILDCARD
            . QueryConnector::SQUARE_BRACKET_CLOSE;
    }
}

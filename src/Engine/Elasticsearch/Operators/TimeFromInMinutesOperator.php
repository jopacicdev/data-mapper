<?php

namespace G4\DataMapper\Engine\Elasticsearch\Operators;

use G4\DataMapper\Common\QueryOperatorInterface;
use G4\DataMapper\Common\SingleValue;
use G4\DataMapper\Common\QueryConnector;

class TimeFromInMinutesOperator implements QueryOperatorInterface
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
        $value = preg_replace('~^\-~', '', $this->value);

        return [
            QueryConnector::RANGE => [
                $this->name => [
                    QueryConnector::GREATER_THAN => strtotime("-{$value} minute", time()),
                    QueryConnector::FORMAT       => QueryConnector::EPOCH_SECOND,
                ]
            ]
        ];
    }
}

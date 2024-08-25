<?php

namespace QueryBuilder\Clauses;

use QueryBuilder\Helper;
use Stringable;

class SqlCondition implements Stringable
{
    public function __construct(
        private string $column,
        private mixed $condition,
        private mixed $value
    ) {
    }

    public function __toString(): string
    {
        return match ($this->condition) {
            '=', '>', '<', '>=', '<=', '<>', 'LIKE', 'NOT LIKE' => sprintf(
                '%s%s%s',
                $this->column,
                $this->condition,
                Helper::quote($this->value)
            ),
            'BETWEEN', 'NOT BETWEEN' => sprintf(
                '%s %s %s AND %s',
                $this->column,
                $this->condition,
                ...array_map([Helper::class, 'quote'], $this->value)
            ),
            'IN', 'NOT IN' => sprintf(
                '%s %s %s',
                $this->column,
                $this->condition,
                Helper::parenthesize(implode(', ', array_map([Helper::class, 'quote'], $this->value)))
            ),
            default => '',
        };
    }
}

<?php

namespace QueryBuilder\Clauses;

use QueryBuilder\Helper;
use Stringable;

class Insert implements Stringable
{
    private string $sql = "INSERT INTO {table} {columns} VALUES {values}";

    public function __construct(
        private string $table,
        private array $data,
        private bool $isMulti = false,
    ) {
        $this->sql = str_replace('{table}', $this->table, $this->sql);
    }

    /**
     * ON in MYSQL: https://dev.mysql.com/doc/refman/8.4/en/insert-on-duplicate.html
     * ON in PostgresSQL: https://www.postgresql.org/docs/current/sql-insert.html
     * @return void
     */
    public function onInsert(string $on): void
    {
        $this->sql .= ' ' . $on;
    }


    private function setColumnsAndValuesToSql(): void
    {
        $columns = $this->getColumns();

        if ($this->isMulti) {
            $values = $this->makeMultiValues($this->data);
        } else {
            $values = array_values($this->data);
            $values = implode(', ', array_map([Helper::class, 'quote'], $values));
            $values = Helper::parenthesize($values);
        }

        $this->sql = str_replace(['{columns}', '{values}'], [$columns, $values], $this->sql);
    }

    public function __toString(): string
    {
        $this->setColumnsAndValuesToSql();

        return $this->sql;
    }


    private function makeMultiValues(array $values): string
    {
        $valuesString = '';

        foreach ($values as $value) {
            if (is_array($value)) {
                $value = implode(', ', array_map([Helper::class, 'quote'], $value));
                $valuesString .= Helper::parenthesize($value) . ', ';
            } else {
                throw new \Exception('Not correct data provided! The $values must be 2d array');
            }
        }

        return rtrim($valuesString, ', ');
    }

    private function getColumns(): string
    {
        if (count($this->data) === 0) {
            throw new \Exception('Not data provided!');
        }

        if ($this->isMulti) {
            $columns = array_keys($this->data[0]);
        } else {
            $columns = array_keys($this->data);
        }

        return Helper::parenthesize(implode(', ', $columns));
    }

}

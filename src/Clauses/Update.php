<?php

namespace QueryBuilder\Clauses;

use Exception;
use QueryBuilder\Helper;
use Stringable;

class Update implements Stringable
{
    private string $sql = 'UPDATE {table} SET {sets}';

    private string $where = '';

    public function __construct(
        private string $table,
        private string|array $sets = ''
    ) {
        $this->sql = str_replace('{table}', $this->table, $this->sql);
    }

    public function where(SqlCondition $where, string $logicOperator = null): void
    {
        if (empty($this->where)) {
            $this->where .= ' WHERE ' . $where;
            return;
        }

        if ($logicOperator === null) {
            throw new Exception("Logical operator not provided! (AND, OR, XOR)");
        }

        $this->where .= " $logicOperator " . $where;
    }

    public function __toString(): string
    {
        $this->setSetsToSql();

        return $this->sql . $this->where;
    }

    private function setSetsToSql(): void
    {
        $sets = '';
        foreach ($this->sets as $key => $value) {
            $sets .= "$key=" . Helper::quote($value) . ', ';
        }

        $this->sql = str_replace('{sets}', rtrim($sets, ', '), $this->sql);
    }
}

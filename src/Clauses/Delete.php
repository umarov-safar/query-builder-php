<?php

namespace QueryBuilder\Clauses;

use Exception;
use QueryBuilder\Helper;
use Stringable;

class Delete implements Stringable
{
    private string $sql = 'DELETE FROM {table}';

    private string $where = '';

    private string $limit = '';

    public function __construct(
        private string $table
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

    public function limit(int $limit): void
    {
        $this->limit .= ' LIMIT ' . $limit;
    }

    public function __toString(): string
    {
        return $this->sql . $this->where . $this->limit;
    }

}

<?php

namespace QueryBuilder\Clauses;

use Exception;
use Stringable;

class Select implements Stringable
{
    private bool $isDistinct = false;

    private string $sql = 'SELECT {distinct} {all} FROM {table}';

    private string $joins = '';

    private string $where = '';

    private string $groupBy = '';

    private string $limit = '';

    private string $having = '';

    private string $offset = '';

    private string $orderBy = '';

    public function __construct(
        private string $table,
        private string|array $columns = '*'
    ) {
        $this->sql = str_replace('{table}', $this->table, $this->sql);
    }

    public function distinct(): void
    {
        $this->isDistinct = true;
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

    public function having(SqlCondition $condition, string $logicOperator = null): void
    {
        if (empty($this->having)) {
            $this->having .= ' HAVING ' . $condition;
            return;
        }

        if ($logicOperator === null) {
            throw new Exception("Logical operator not provided! (AND, OR, XOR)");
        }

        $this->having .= " $logicOperator " . $condition;
    }

    public function groupBy(string|array $columns): void
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        $this->groupBy = " GROUP BY $columns";
    }

    public function limit(int $limit): void
    {
        $this->limit = ' LIMIT ' . $limit;
    }

    public function offset(int $offset): void
    {
        $this->offset = ' OFFSET ' . $offset;
    }

    public function orderBy(string|array $columns, string $sort = 'ASC'): void
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        $this->orderBy = ' ORDER BY ' . $columns . " $sort ";
    }

    public function join(string $table, string $onRaw, string $type = 'INNER')
    {
        $this->joins .= sprintf(' %s JOIN %s ON %s ', $type, $table, $onRaw);
    }

    public function __toString(): string
    {
        $this->setColumnsToSql();
        $this->setDistinct();

        return $this->sql .
            $this->joins .
            $this->where .
            $this->groupBy .
            $this->having .
            $this->orderBy .
            $this->limit .
            $this->offset;
    }

    private function setColumnsToSql(): void
    {
        if (is_array($this->columns)) {
            $this->columns = implode(', ', $this->columns);
        }

        $this->sql = str_replace('{all}', $this->columns, $this->sql);
    }

    private function setDistinct(): void
    {
        if ($this->isDistinct) {
            $this->sql = str_replace('{distinct}', 'DISTINCT', $this->sql);
            return;
        }
        $this->sql = str_replace('{distinct}', '', $this->sql);
    }
}

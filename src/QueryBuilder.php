<?php

namespace QueryBuilder;

use PDO;
use PDOStatement;
use QueryBuilder\Clauses\Delete;
use QueryBuilder\Clauses\Insert;
use QueryBuilder\Clauses\Select;
use QueryBuilder\Clauses\SqlCondition;
use QueryBuilder\Clauses\Update;

class QueryBuilder implements SQLDMLInterface
{
    use ConnectDatabase;

    private string $table;

    private array $config;

    private Insert|Delete|Select|Update $clause;

    private PDO $db;

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->connectDatabase();
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function insert(array $data): self
    {
        $this->clause = new Insert($this->table, $data, false);

        return $this;
    }

    public function insertMulti(array $data): self
    {
        $this->clause = new Insert($this->table, $data, true);

        return $this;
    }

    public function onInsert(string $sqlRaw): self
    {
        $this->clause->onInsert($sqlRaw);

        return $this;
    }


    public function select(string|array $columns = '*'): self
    {
        $this->clause = new Select($this->table, $columns);

        return $this;
    }

    public function distinct(): self
    {
        $this->clause->distinct();

        return $this;
    }

    public function where(string $column, string $condition, mixed $value): self
    {
        $this->clause->where(new SqlCondition($column, $condition, $value), 'AND');

        return $this;
    }

    public function orWhere(string $column, string $condition, mixed $value): self
    {
        $this->clause->where(new SqlCondition($column, $condition, $value), 'OR');

        return $this;
    }

    public function like(string $column, mixed $value): self
    {
        $this->clause->where(new SqlCondition($column, 'LIKE', $value), 'AND');

        return $this;
    }

    public function notLike(string $column, mixed $value): self
    {
        $this->clause->where(new SqlCondition($column, 'NOT LIKE', $value), 'AND');

        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->clause->where(new SqlCondition($column, 'IN', $values), 'AND');

        return $this;
    }

    public function orWhereIn(string $column, array $values): self
    {
        $this->clause->where(new SqlCondition($column, 'IN', $values), 'OR');

        return $this;
    }

    public function whereNotIn(string $column, array $values): self
    {
        $this->clause->where(new SqlCondition($column, 'NOT IN', $values), 'AND');

        return $this;
    }

    public function orWhereNotIn(string $column, array $values): self
    {
        $this->clause->where(new SqlCondition($column, 'NOT IN', $values), 'OR');

        return $this;
    }

    public function whereBetween(string $column, mixed $val, mixed $val2): self
    {
        $this->clause->where(new SqlCondition($column, 'BETWEEN', [$val, $val2]), 'AND');

        return $this;
    }

    public function orWhereBetween(string $column, mixed $val, mixed $val2): self
    {
        $this->clause->where(new SqlCondition($column, 'BETWEEN', [$val, $val2]), 'OR');

        return $this;
    }

    public function whereNotBetween(string $column, mixed $val, mixed $val2): self
    {
        $this->clause->where(new SqlCondition($column, 'NOT BETWEEN', [$val, $val2]), 'AND');

        return $this;
    }

    public function orWhereNotBetween(string $column, mixed $val, mixed $val2): self
    {
        $this->clause->where(new SqlCondition($column, 'NOT BETWEEN', [$val, $val2]), 'OR');

        return $this;
    }

    public function groupBy(string|array $columns): self
    {
        $this->clause->groupBy($columns);

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->clause->limit($limit);

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->clause->offset($offset);

        return $this;
    }

    public function orderBy(string|array $columns, string $sort = 'ASC'): self
    {
        $this->clause->orderBy($columns, $sort);

        return $this;
    }

    public function having(string $column, string $condition, mixed $value): self
    {
        $this->clause->having(new SqlCondition($column, $condition, $value), 'AND');

        return $this;
    }

    public function orHaving(string $column, string $condition, mixed $value): self
    {
        $this->clause->having(new SqlCondition($column, $condition, $value), 'OR');

        return $this;
    }

    public function join(string $table, string $onRaw, string $type = 'INNER'): self
    {
        $this->clause->join($table, $onRaw, $type);

        return $this;
    }

    public function update(string|array $sets): self
    {
        $this->clause = new Update($this->table, $sets);

        return $this;
    }

    public function delete(): self
    {
        $this->clause = new Delete($this->table);

        return $this;
    }

    public function toSql(): string
    {
        return (string)$this->clause;
    }

    public function getNewInstance(): self
    {
        return new self($this->config);
    }

    /**
     * @return false|PDOStatement
     */
    public function execute(): false|PDOStatement
    {
        return $this->db->query($this->toSql());
    }
}

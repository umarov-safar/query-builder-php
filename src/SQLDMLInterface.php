<?php

namespace QueryBuilder;

interface SQLDMLInterface
{
    public function insert(array $data);

    public function select(string|array $columns): self;

    public function update(string|array $sets): self;

    public function delete(): self;
}

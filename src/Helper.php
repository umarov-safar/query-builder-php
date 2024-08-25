<?php

namespace QueryBuilder;

class Helper
{
    public static function quote(mixed $value): string
    {
        if (is_float($value) || is_int($value) || is_bool($value) || is_null($value)) {
            return $value;
        }
        return "'$value'";
    }

    public static function parenthesize(string $value): string
    {
        return '(' . $value . ')';
    }
}

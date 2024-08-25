## Примеры использования.
```php
<?php

include __DIR__ . '/vendor/autoload.php';

$newLine = php_sapi_name() === 'cli' ? PHP_EOL : '<br />';

$queryBuilder = new QueryBuilder\QueryBuilder([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'database',
    'user' => 'root',
    'password' => 'password'
]);

$queryBuilder->table('users')
    ->insert([
        'name' => 'Anton',
        'age' => 22,
        'address' => 'Tomsk kievskay'
    ]);
echo $queryBuilder->toSql();
echo $newLine;

$queryBuilder = $queryBuilder->getNewInstance();
$queryBuilder->table('users')->insertMulti([
    ['name' => 'Anton', 'age' => 22, 'address' => 'Tomsk kievskay'],
    ['name' => 'Vanya', 'age' => 42, 'address' => 'Tomsk kievskay'],
    ['name' => 'Tanya', 'age' => 35, 'address' => 'Tomsk kievskay']
])->onInsert('ON DUPLICATE KEY UPDATE id+1');
echo $queryBuilder->toSql();
echo $newLine;


/**
 * Что бы написать таокой запрос
 * SELECT department, sum(salary) as salary FROM employee GROUP BY department HAVING SUM(salary) >= 50000;
 */
$queryBuilder = $queryBuilder->getNewInstance();
$queryBuilder->table('employee')
    ->select('department, SUM(salary) as salary')
    ->groupBy('department')
    ->having('SUM(salary)', '>=', 5000);
echo $queryBuilder->toSql();
echo $newLine;

/**
 * Что бы написать таокой запрос
 * SELECT DISTINCT * FROM Customer ORDER BY age DESC LIMIT 100;
 */
$queryBuilder = $queryBuilder->getNewInstance();
$queryBuilder->table('customer')
    ->select()
    ->distinct()
    ->orderBy('age', 'DESC')
    ->limit(100);
echo $queryBuilder->toSql();
echo $newLine;


/**
 * Что бы написать такой запрос
 * SELECT
 *  d.department_id,
 *  d.department_name,
 *  d.country,
 *  AVG(s.salary) AS avg_salary
 * FROM
 *  departments d
 *  INNER JOIN employees e ON d.department_id = e.department_id
 *  INNER JOIN salaries s ON e.employee_id = s.employee_id
 * WHERE d.department_id BETWEEN 1000 AND 2000 OR d.country='Russia'
 * GROUP BY
 *  d.department_id, d.department_name
 * HAVING avg_salary > 10000
 *
 */
$queryBuilder = $queryBuilder->getNewInstance();
$queryBuilder->table('departments d')
    ->select(['d.department_id', 'd.department_name', 'd.country', 'AVG(s.salary) AS avg_salary'])
    ->join('employees e', 'd.department_id = e.department_id')
    ->join('salaries s', 'e.employee_id = s.employee_id')
    ->whereBetween('d.department_id', 1000, 2000)
    ->orWhere('d.country', '=', 'Russia')
    ->groupBy(['d.department_id', 'd.department_name'])
    ->having('avg_salary', '>', 10000);
echo $queryBuilder->toSql();
echo $newLine;


/**
 * Что бы написать такой запрос
 * DELETE FROM users WHERE id IN(1, 2, 3, 4, 5, 6, 7)
 */
$queryBuilder = $queryBuilder->getNewInstance();
$queryBuilder->table('users')
    ->delete()
    ->whereIn('id', [1, 2, 3, 4, 5, 6, 7]);
echo $queryBuilder->toSql();
echo $newLine;



/**
 * Что бы написать такой запрос
 * UPDATE users SET city='Moscow' WHERE country='Russia' AND city NOT IN ('Moscow', 'SanPeterburg', 'Sochi', 'Tomsk')
 */
$queryBuilder = $queryBuilder->getNewInstance()
    ->table('users')
    ->update(['city' => 'Moscow'])
    ->where('country', '=', 'Russia')
    ->whereNotIn('city', ['Moscow', 'SanPeterburg', 'Sochi', 'Tomsk']);
echo $queryBuilder->toSql();
echo $newLine;

$queryBuilder->execute();


```
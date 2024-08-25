<?php

namespace QueryBuilder;

use PDO;
use PDOException;

trait ConnectDatabase
{
    private function connectDatabase(): void
    {
        $host = $this->config['host'];
        $driver = $this->config['driver']; //mysql,pgsql
        $db_name = $this->config['database'];
        $user = $this->config['user'];
        $port = $this->config['port'];
        $password = $this->config['password'];
        $charset = isset($this->config['charset']) ? 'charset=' . $this->config['charset'] : '';

        $dsn = "$driver:host=$host;port=$port;dbname=$db_name;$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->db = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
}

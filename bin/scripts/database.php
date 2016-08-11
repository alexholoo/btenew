<?php

const EOL = PHP_EOL;

// Create a connection with PDO options
$db = new \Phalcon\Db\Adapter\Pdo\Mysql(
    array(
        "host"     => "localhost",
        "username" => "root",
        "password" => "",
        "dbname"   => "bte",
        "options"  => array(
#           PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES \'UTF8\'",
#           PDO::ATTR_CASE               => PDO::CASE_LOWER
        )
    )
);

function genInsertSql($table, $columns, $data)
{
    $columns = '`' . implode('`, `', $columns) . '`';

    $query = "INSERT INTO `$table` ($columns) VALUES\n";

    $values = array();

    foreach($data as $row) {
        foreach($row as &$val) {
            $val = addslashes($val);
        }
        $values[] = "('" . implode("', '", $row). "')";
    }

    return $query . implode(",\n", $values) . ';';
}



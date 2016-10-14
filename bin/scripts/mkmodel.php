<?php

require 'database.php';

if (!isset($argv[1])) {
    echo 'Please specify a table name.', EOL;
    exit;
}

$table = $argv[1];

$sql = "DESC $table";

try {
    $result = $db->query($sql);

    $columns = [];

    while ($column = $result->fetch(Phalcon\Db::FETCH_ASSOC)) {
        $columns[] = $column;
    }

    createModel($table, $columns, $argv);

} catch (Exception $e) {
    echo $e->getMessage(), EOL;
}

function createModel($table, $columns, $argv)
{
    codeln('<?php');
    codeln('');

    codeln('namespace App\Models;');
    codeln('');

    codeln('use Phalcon\Mvc\Model;');
    codeln('');

    codeln("class $table extends Model");
    codeln('{');

    $maxLen = getMaxLen($columns);
    foreach ($columns as $column) {
        $padding = str_repeat(' ', $maxLen - strlen($column['Field']));
        codeln('public $'. $column['Field']. ';' . $padding. ' // '. $column['Type']);
    }
    codeln('');

    codeln('public function getSource()');
    codeln('{');
    codeln("return '$table';");
    codeln('}');

    codeln('public function initialize()');
    codeln('{');
    codeln('$this->setSource("'.$table.'");');
    codeln('// $this->belongsTo("id", "Brands", "id");');
    codeln('// $this->hasMany("id", "Cars", "brand_id");');
    codeln('}');

    codeln('public function columnMap()');
    codeln('{');
    codeln('// Keys are the real names in the table and');
    codeln('// the values their names in the application');
    codeln('');

    codeln('return array(');
    foreach ($columns as $column) {
        $padding = str_repeat(' ', $maxLen - strlen($column['Field']));
        codeln("    '". $column['Field']. "'". $padding. ' => '. "'". $column['Field']. "',");
    }
    codeln(');');
    codeln('}');

    codeln('// public function beforeSave()');
    codeln('// public function afterFetch()');
    codeln('// public function afterSave()');
    codeln('// public function beforeDelete()');
    codeln('// public function afterDelete()');

    if (isset($argv[2]) && $argv[2] == 'gs') {
        getterSetter($columns);
    }

    codeln('}');
}

function getterSetter($columns)
{
    foreach ($columns as $column) {
        $name   = $column['Field'];
        $ucname = ucfirst($name);
        $lcname = lcfirst($name);

        codeln('public function get'. $ucname .'()');
        codeln('{');
        codeln('return $this->'.$lcname.';');
        codeln('}');

        codeln('public function set'. $ucname .'($'.$lcname .')');
        codeln('{');
        codeln('$this->'.$lcname.' = $'. $lcname.';');
        codeln('}');
    }
}

function codeln($line)
{
    static $indentLevel = 0;

    if ($line == '}') {
        $indentLevel -= 1;
    }

    echo str_repeat(' ', $indentLevel*4);
    echo $line, EOL;

    if ($line == '}') {
        echo EOL;
    }

    if ($line == '{') {
        $indentLevel += 1;
    }
}

function getMaxLen($columns)
{
    $maxLen = 0;
    foreach ($columns as $column) {
        $maxLen = max($maxLen, strlen($column['Field']));
    }
    return $maxLen;
}

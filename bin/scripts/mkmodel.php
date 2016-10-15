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
   #codeln('use Phalcon\Mvc\Model\Validator\InclusionIn;');
   #codeln('use Phalcon\Mvc\Model\Validator\Uniqueness;');
    codeln('use Phalcon\Validation;');
    codeln('use Phalcon\Validation\Validator\Uniqueness;');
    codeln('use Phalcon\Validation\Validator\InclusionIn;');
    codeln('');

    codeln("class $table extends Model");
    codeln('{');

        properties($table, $columns);
        getSource($table, $columns);
        initialize($table, $columns);
        columnMap($table, $columns);

        for ($i=2; $i<count($argv); $i++) {
            if ($argv[$i] == 'vl') {
                validation($table, $columns);
            }

            if ($argv[$i] == 'gs') {
                getterSetter($table, $columns);
            }

            if ($argv[$i] == 'ev') {
                modelEvents($table, $columns);
            }
        }

    codeln('}');
}

function properties($table, $columns)
{
    $maxLen = getMaxLen($columns);
    foreach ($columns as $column) {
        $padding = str_repeat(' ', $maxLen - strlen($column['Field']));
        codeln('public $'. $column['Field']. ';' . $padding. ' // '. $column['Type']);
    }
    codeln('');
}

function getSource($table, $columns)
{
    codeln('public function getSource()');
    codeln('{');
    codeln("return '$table';");
    codeln('}');
}

function initialize($table, $columns)
{
    codeln('public function initialize()');
    codeln('{');
   #codeln('$this->setSource("'.$table.'");');
    codeln('# $this->hasOne("this_id", "That\\\\Model", "that_id");');
    codeln('# $this->hasMany("this_id", "That\\\\Model", "that_id");');
    codeln('# $this->belongsTo("this_id", "That\\\\Model", "that_id");');
    codeln('# $this->hasManyToMany(...);');
    codeln('}');
}

function columnMap($table, $columns)
{
    codeln('public function columnMap()');
    codeln('{');
    codeln('// Keys are the real names in the table and');
    codeln('// the values their names in the application');
    codeln('');

    $maxLen = getMaxLen($columns);

    codeln('return array(');
    foreach ($columns as $column) {
        $padding = str_repeat(' ', $maxLen - strlen($column['Field']));
        codeln("    '". $column['Field']. "'". $padding. ' => '. "'". $column['Field']. "',");
    }
    codeln(');');
    codeln('}');
}

function validation($table, $columns)
{
    codeln('public function validation()');
    codeln('{');
    codeln('$validator = new Validation();');
    codeln('');

    codeln('$validator->add("type",');
    codeln('    new InclusionIn([');
    codeln('        "domain" => [');
    codeln('            "Mechanical",');
    codeln('            "Virtual",');
    codeln('        ]');
    codeln('    ])');
    codeln(');');
    codeln('');

    codeln('$validator->add("name",');
    codeln('    new Uniqueness([');
    codeln('        "message" => "The robot name must be unique",');
    codeln('    ])');
    codeln(');');
    codeln('');

    codeln('return $this->validate($validator);');
    codeln('}');
}

function modelEvents($table, $columns)
{
    codeln('# public function beforeValidation()');
    codeln('# public function beforeValidationOnCreate()');
    codeln('# public function beforeValidationOnUpdate()');
    codeln('# public function onValidationFails()');
    codeln('# public function afterValidationOnCreate()');
    codeln('# public function afterValidationOnUpdate()');
    codeln('# public function afterValidation()');
    codeln('# public function beforeSave()');
    codeln('# public function beforeUpdate()');
    codeln('# public function beforeCreate()');
    codeln('# public function afterUpdate()');
    codeln('# public function afterCreate()');
    codeln('# public function afterSave()');
}

function getterSetter($table, $columns)
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

    if ($line == '') {
        echo EOL;
        return;
    }

    if ($line == '}') {
        $indentLevel = max($indentLevel-1, 0);
    }

    echo str_repeat(' ', $indentLevel*4), $line, EOL;

    if ($line == '}' && $indentLevel > 0) {
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

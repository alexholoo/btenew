<?php

array_shift($argv);

$entityName = array_shift($argv);

if (empty($entityName)) {
    exit("Incorrect number of arguments\n");
}

echo "<?php\n\n";

echo "namespace Domain\\Entity;\n\n";

echo "use Domain\\Entity\\AbstractEntity;\n\n";

echo "/**\n";
echo " * class $entityName\n";
echo " */\n";
echo "class $entityName extends AbstractEntity\n";
echo "{\n";

foreach ($argv as $property) {
    echo "\t/**\n";
    echo "\t * @var string\n";
    echo "\t */\n";
    echo "\tprotected \$$property;\n\n";
}

echo "\n";

foreach ($argv as $property) {
    $prop = ucfirst($property);

    echo "\t/**\n";
    echo "\t * @return string\n";
    echo "\t */\n";
    echo "\tpublic function get$prop()\n";
    echo "\t{\n";
    echo "\t\treturn \$this->$property;\n";
    echo "\t}\n\n";

    echo "\t/**\n";
    echo "\t * @param string \$$property\n";
    echo "\t * @return \$this\n";
    echo "\t */\n";
    echo "\tpublic function set$prop(\$$property)\n";
    echo "\t{\n";
    echo "\t\t\$this->$property = \$$property;\n";
    echo "\t\treturn \$this;\n";
    echo "\t}\n\n";
}
echo "}";

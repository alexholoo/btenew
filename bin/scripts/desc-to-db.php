<?php

include 'simple_html_dom.php';

const EOL = "\n";  // unix, PHP_EOL is os-specific

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

$files = glob('item-desc/html/*.html');

foreach ($files as $file) {
    $path = pathinfo($file);
    $asin = $path['filename'];

    $sql = "SELECT * FROM amazon_asin_desc WHERE asin='$asin'";
    $result = $db->fetchOne($sql);

    if (!$result) {
        echo $asin, EOL;

        $html = file_get_contents($file);
        if (!$html) { // empty file
            continue;
        }

        $dom = str_get_html($html);

        $feature = getFeature($dom);
        $desc = getDescription($dom);

        $sql = "INSERT INTO amazon_asin_desc (asin, feature, `desc`) VALUES ('$asin', '$feature', '$desc')";
        $db->execute($sql);
    }
}

function getFeature($dom)
{
    $list = $dom->find('#feature-bullets li');

    $desc = [];
    foreach ($list as $el) {
        if ($el->getAttribute('id') == 'replacementPartsFitmentBullet') {
            continue;
        }
        $el = $el->find('span.a-list-item')[0];
        $desc[] = "<li>" .trim($el->text()). "</li>";
    }

#   shuffle($desc);

    $result = implode("\n", $desc);

    return addslashes("<ul>\n$result\n</ul>\n");
}

function getDescription($dom)
{
    $div = $dom->find('#productDescription')[0];

    $el = $div->find('.disclaim')[0];
    $disclaim = str_replace(["\n", '  '], '', trim($el->text()));

    $el = $div->find('p')[0];
    $desc = trim($el->text());

    $result = "<div>$disclaim</div>\n<p>$desc</p>\n";

    return addslashes($result);
}

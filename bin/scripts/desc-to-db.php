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

$files = glob('e:/BTE/amazon/item-desc/html/*.html');

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

        $title = addslashes(getTitle($dom));
        $feature = addslashes(getFeature($dom));
        $desc = addslashes(getDescription($dom));

        $sql = "INSERT INTO amazon_asin_desc (asin, title, feature, description) ".
               "VALUES ('$asin', '$title', '$feature', '$desc')";
        $db->execute($sql);
    }
}

function getTitle($dom)
{
    $span = $dom->find('#productTitle');
    if (!$span) {
        return '';
    }

    $el = $span[0];

    return trim($el->text());
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

    return "<ul>\n$result\n</ul>\n";
}

function getDescription($dom)
{
    $result = '';

    $div = $dom->find('#productDescription');
    if (!$div) {
        return $result;
    }

    $div = $div[0];

    $el = $div->find('.disclaim');
    if ($el) {
        $el = $el[0];
        $disclaim = str_replace(["\n", '  '], '', trim($el->innertext()));
        $result .= "<div>$disclaim</div>\n";
    }

    $el = $div->find('p');
    if ($el) {
        $el = $el[0];
        $desc = trim($el->innertext());
        $result .= "<p>$desc</p>\n";
    }

    if (strlen($result) > 1024) {
        $result = strip_tags($result, '<p><div><strong><h4><h5><span><table><tbody><tr><td><br><ul><li><ol>');
    }

    return $result;
}

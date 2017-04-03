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

    echo $asin, EOL;

    $html = file_get_contents($file);
    if (!$html) { // empty file
        continue;
    }

    $dom = str_get_html($html);

    $title = addslashes(getTitle($dom));
    $feature = addslashes(getFeature($dom));
    $desc = addslashes(getDescription($dom));
    $imgurl = trim(addslashes(getImageUrl($dom)));

    $sql = "SELECT * FROM amazon_asin_desc WHERE asin='$asin'";
    $result = $db->fetchOne($sql);

    if (!$result) {
        $sql = "INSERT INTO amazon_asin_desc (asin, title, feature, description, imageurl) ".
               "VALUES ('$asin', '$title', '$feature', '$desc', '$imgurl')";
        $db->execute($sql);
    } else {
        $sql = "UPDATE amazon_asin_desc SET ".
                   "title='$title', ".
                   "feature='$feature', ".
                   "description='$desc', ".
                   "imageurl='$imgurl' ".
               "WHERE asin='$asin'";
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

function getImageUrl($dom)
{
    $el = $dom->find('#imgTagWrapperId img');
   #$el = $dom->find('#landingImage');

    if ($el) {
       #$el = $el[0];
        $el = current($el);

        $imgurl = $el->getAttribute('data-old-hires');
        return urldecode($imgurl);

        // we can get more images
        $imgurl = $el->getAttribute('src');
        if (substr(trim($imgurl), 0, 23) == 'data:image/jpeg;base64,') {
            $imgdat = base64_decode(substr($imgurl, 23));
            file_put_contents("$asin.jpg", $imgdat);
        }

        // even more
        $json = $el->getAttribute('data-a-dynamic-image');
    }

    return '';
}

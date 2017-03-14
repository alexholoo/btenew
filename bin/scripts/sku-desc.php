<?php

include 'simple_html_dom.php';

$file = 'e:/BTE/amazon/reports/amazon_us_listings.txt';

$fp = fopen($file, 'r');

$title = fgetcsv($fp, 0, "\t");

while (($values = fgetcsv($fp, 0, "\t"))) {
    $fields = array_combine($title, $values);

    $asin   = $fields['asin1'];
    $sku    = str_replace('/', '_', $fields['seller-sku']);

    $fhtml  = "item-desc/html/$asin.html";
    $fsku   = "item-desc/sku-desc/$sku.html";
    $fasin  = "item-desc/asin-desc/$asin.html";

    if (file_exists($fhtml) && filesize($fhtml) > 0) {
        if (!file_exists($fsku)) {
            echo "$asin, $sku", PHP_EOL;
            fetchDesc($fhtml, $fsku, $fasin);
        }
    }
}

fclose($fp);

function fetchDesc($in, $fsku, $fasin)
{
    $html = file_get_contents($in);

    $dom = str_get_html($html);
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

    file_put_contents($fsku, "<ul>\n$result\n</ul>\n");
    file_put_contents($fasin, "<ul>\n$result\n</ul>\n");
}

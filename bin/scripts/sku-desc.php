<?php

include 'simple_html_dom.php';

$file = 'e:/BTE/amazon/reports/amazon_us_listings.txt';

$fp = fopen($file, 'r');

$title = fgetcsv($fp, 0, "\t");

while (($values = fgetcsv($fp, 0, "\t"))) {
    $fields = array_combine($title, $values);

    $asin   = $fields['asin1'];
    $sku    = str_replace('/', '_', $fields['seller-sku']);
    $in     = "item-desc/html/$asin.html";
    $out    = "item-desc/$sku.html";

    if (file_exists($in) && filesize($in) > 0) {
        if (!file_exists($out)) {
            echo "$asin, $sku", PHP_EOL;
            fetchDesc($in, $out);
        }
    }
}

fclose($fp);

function fetchDesc($in, $out)
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
    file_put_contents($out, "<ul>\n$result\n</ul>\n");
}

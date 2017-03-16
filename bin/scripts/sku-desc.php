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

            $html = file_get_contents($fhtml);

            $dom = str_get_html($html);

            $feature = getFeature($dom);
            $desc = getDescription($dom);

            file_put_contents($fsku, $desc . $feature);
            file_put_contents($fasin, $desc . $feature);
        }
    }
}

fclose($fp);

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
        $disclaim = str_replace(["\n", '  '], '', trim($el->text()));
        $result .= "<div>$disclaim</div>\n";
    }

    $el = $div->find('p');
    if ($el) {
        $el = $el[0];
        $desc = trim($el->text());
        $result .= "<p>$desc</p>\n";
    }

    return $result;
}

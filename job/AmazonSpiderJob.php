<?php

include 'classes/Job.php';
include 'classes/simple_html_dom.php';

class AmazonSpiderJob extends Job
{
    protected $proxyList;
    protected $headerList;
    protected $userAgentList;

    public function run($argv = [])
    {
        date_default_timezone_set("America/Toronto");

        $asinList = $this->getAsinList();

        $failed = 0;

        foreach ($asinList as $asin) {
            $fname = "E:/BTE/amazon/item-desc/html/$asin.html";
            if (file_exists($fname)) continue;

            echo $asin, PHP_EOL;

            $url = "https://www.amazon.com/dp/$asin";

            // TODO: use php curl to download the page
            exec("wget $url -O $fname");

            if (filesize($fname) < 10000) { // 10K
                echo chr(7);

                // TODO: change proxy/header/userAgent
                // changeWhoAmI

                if (++$failed == 3) break;
            } else {
                $failed = 0;

                // The file might be gzipped, need to gunzip first.
                // $this->processFile($fname, $asin);
            }

            if (date('H') < 11 || date('H') >= 18) {
                sleep(30); // not in office hours
            } else {
                sleep(rand(90, 150)); // office hours
            }
        }
    }

    protected function getAsinList()
    {
        $fname = "E:/BTE/amazon/item-desc/asin-list";
        if (file_exists($fname)) {
            return file($fname, FILE_IGNORE_NEW_LINES);
        }

        $skuList = $this->getMasterSkuList();
        $listing = $this->getAmazonListing();

        $fp = fopen($fname, 'w');

        $asinList = [];
        foreach ($skuList as $sku) {
            if (isset($listing[$sku])) {
                $asin = $listing[$sku];
                $file = "E:/BTE/amazon/item-desc/html/$asin.html";
                if (!file_exists($file)) {
                    $asinList[$asin] = 1;
                    fputs($fp, "$asin\n");
                }
            }
        }

        fclose($fp);

        return array_keys($asinList);
    }

    protected function getAmazonListing()
    {
        $file = 'e:/BTE/amazon/reports/amazon_us_listings.txt';

        $fp = fopen($file, 'r');
        $title = fgetcsv($fp, 0, "\t");

        $listing = [];

        while (($values = fgetcsv($fp, 0, "\t"))) {
            $fields = array_combine($title, $values);

            $asin = $fields['asin1'];
            $sku  = $fields['seller-sku'];

            $listing[$sku] = $asin;
        }

        fclose($fp);

        return $listing;
    }

    protected function getMasterSkuList()
    {
        $file = 'w:/data/master_sku_list.csv';

        $fp = fopen($file, 'r');
        $title = fgetcsv($fp);
        $title[1] = 'PN';

        $skuList = [];

        while (($values = fgetcsv($fp))) {
            $fields = array_combine($title, $values);
            $sku = trim(str_replace('overstock', '', $fields['PN']));
            $skuList[] = $sku;
        }

        fclose($fp);

        return $skuList;
    }

    protected function processFile($fname, $asin)
    {
        $html = file_get_contents($fname);

        if (!$html) { // empty file
            return;
        }

        $dom = str_get_html($html);

        $title   = addslashes($this->getTitle($dom));
        $feature = addslashes($this->getFeature($dom));
        $desc    = addslashes($this->getDescription($dom));
        $imgurl  = addslashes($this->getImageUrl($dom));

        $sql = "INSERT INTO amazon_asin_desc (asin, title, feature, description, imgurl) ".
               "VALUES ('$asin', '$title', '$feature', '$desc', '$imgurl')";

        $this->db->execute($sql);
    }

    protected function getTitle($dom)
    {
        $span = $dom->find('#productTitle');
        if (!$span) {
            return '';
        }

        $el = $span[0];

        return trim($el->text());
    }

    protected function getFeature($dom)
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

        # shuffle($desc);

        $result = implode("\n", $desc);

        return "<ul>\n$result\n</ul>\n";
    }

    protected function getDescription($dom)
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

    protected function getImageUrl($dom)
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
}

include __DIR__ . '/../public/init.php';

$job = new AmazonSpiderJob();
$job->run($argv);

<?php

include __DIR__ . '/../public/init.php';
include 'classes/simple_html_dom.php';

class AmazonSpiderJob extends Job
{
    protected $failed;

    protected $proxy;
    protected $proxyAuth;

    protected $cookieFile = 'cookies.txt';

    protected $headers;
    protected $userAgent;
    protected $referer;
    protected $encoding;

    public function run($argv = [])
    {
        date_default_timezone_set("America/Toronto");

        $asinList = $this->getAsinList();

        $this->failed = 0;

        $this->changeWhoAmI();

        foreach ($asinList as $asin) {
            $fname = "E:/BTE/amazon/item-desc/html/$asin.html";
            if (file_exists($fname)) continue;

            echo $asin, PHP_EOL;

            $url = "https://www.amazon.com/dp/$asin";
            // exec("wget $url -O $fname");
            $this->downloadPage($url, $fname);

            if (filesize($fname) < 10000) { // 10K
                echo chr(7);

                $this->changeWhoAmI();

                if (++$this->failed == 3) break;
            } else {
                $this->failed = 0;

                // The file might be gzipped, need to gunzip first.
                // $this->processFile($fname, $asin);
            }

            if (date('H') < 11 || date('H') >= 18) {
                sleep(30); // not in office hours
            } else {
               #sleep(rand(90, 150)); // office hours
                sleep(5);
            }
        }
    }

    protected function downloadPage($url, $fname)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       #curl_setopt($ch, CURLOPT_TIMEOUT, 30);
       #curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
       #curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

       #curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
       #curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
       #curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

       #curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
       #curl_setopt($ch, CURLOPT_ENCODING, $this->encoding);

        $output = curl_exec($ch);

        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        $errno = curl_errno($ch);

        // print_r($info);
        /*Array
        (
            [http_code] => 200
            [header_size] => 1199
            [request_size] => 308
            [size_download] => 184052
            [speed_download] => 76465
        )*/

        curl_close($ch);

        file_put_contents($fname, $output);
    }

    protected function changeWhoAmI()
    {
        $proxies = [
            'http://138.197.23.220:8080',
            'http://207.141.59.221:8080',
            'http://35.185.42.72:80',
            'http://96.239.193.243:8080',
            'http://164.58.168.2:8080',
            'http://35.185.12.179:80',
            'http://207.99.118.74:8080',
            'http://47.52.33.214:3128',
            'http://35.167.66.19:3128',
            'http://45.55.209.249:3128',
            'http://104.131.30.42:8080',
            'http://35.185.199.138:80',
            'http://35.185.207.5:80',
            'http://47.52.33.136:3128',
            'http://45.79.219.25:3128',
            'http://104.196.248.56:80',
            'http://198.23.161.126:3128',
            'http://192.129.198.21:9001',
            'http://74.84.131.34:80',
            'http://94.78.199.45:8081',
            'http://110.50.84.162:8080',
        ];

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.5',
        ];

        $referers = [
            '',
            'http://cn.dealmoon.com',
            'http://www.amazon.com/',
            'http://www.baidu.com/',
            'http://www.google.com/',
            '',
        ];

        $userAgents = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:32.0) Gecko/20100101 Firefox/32.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/534.55.3 (KHTML, like Gecko) Version/5.1.3 Safari/534.53.10',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; da-dk) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; de-at) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
            'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36',
            'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36',
            'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
            'Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11',
            'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20130401 Firefox/31.0',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
            'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0',
            'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
            'Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux i586; rv:31.0) Gecko/20100101 Firefox/31.0',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:11.0) Gecko/20100101 Firefox/11.0',
            'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko',
            'Mozilla/5.0 (compatible; MSIE 10.0; Macintosh; Intel Mac OS X 10_7_3; Trident/6.0)',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/4.0; InfoPath.2; SV1; .NET CLR 2.0.50727; WOW64)',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/5.0)',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 7.0; InfoPath.3; .NET CLR 3.1.40767; Trident/6.0; en-IN)',
            'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0',
        ];

        $this->proxy = $proxies[array_rand($proxies)];
        $this->headers = $headers;
        $this->userAgent = $userAgents[array_rand($userAgents)];
        $this->referer = $referers[array_rand($referers)];
        $this->encoding = 'en-US';

       #print_r($this->proxy); echo PHP_EOL;
       #print_r($this->headers); echo PHP_EOL;
       #print_r($this->userAgent); echo PHP_EOL;
       #print_r($this->referer); echo PHP_EOL;
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

$job = new AmazonSpiderJob();
$job->run($argv);

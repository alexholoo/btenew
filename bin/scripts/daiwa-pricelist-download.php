<?php

$username = "roy@btecanada.com";
$password = "4800618";

$cookiejar = "./daiwa-cookie.txt";
$useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7";

// delete old cookiejar
if (file_exists($cookiejar)) {
    unlink($cookiejar);
}

//
// step 1: go to homepage to get sesskey in login form
//
$url="http://daiwa.net/"; 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$html = curl_exec($ch);

preg_match('/name="sesscheck" value="([a-z0-9]*)"/', $html, $matches);
$sesskey = $matches[1];
echo "Session Key: $sesskey\n";

$postinfo = sprintf("username=$username&password=$password&ref=loginstatus&sesscheck=%s", $sesskey);

//
// step 2: send login request to the website
//
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
$html = curl_exec($ch);

//
// step 3: download the pricelist we want
//
$url="http://daiwa.net/advsearch/?download=xls&request=advsearch"; 
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$data = curl_exec($ch);
curl_close($ch);
file_put_contents('e:/daiwa-pricelist.csv', $data);

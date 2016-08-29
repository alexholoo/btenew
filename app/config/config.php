<?php

use Phalcon\Config;
use Phalcon\Logger;

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'bte'
    ],
    'application' => [
        'controllersDir' => APP_DIR . '/controllers/',
        'modelsDir' => APP_DIR . '/models/',
        'formsDir' => APP_DIR . '/forms/',
        'viewsDir' => APP_DIR . '/views/',
        'libraryDir' => APP_DIR . '/library/',
        'pluginsDir' => APP_DIR . '/plugins/',
        'cacheDir' => APP_DIR . '/cache/',
        'baseUri' => '/',
        'publicUrl' => 'http://btelocal',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D'
    ],
    'bte' => [
        'name' => 'BTE Computer Inc',
        'contact' => 'Roy Zhang',
        'phone' => '',
        'email' => 'roy@btecanada.com',
        'address' => 'Unit 5, 270 Esna Park Dr',
        'zipcode' => 'L3R 1H3',
        'city' => 'Markham',
        'province' => 'ON',
        'country' => 'Canada',
    ],
    'amazon' => [
        'AWSAccessKeyId' => '',
        'AWSSecretKey' => ''
    ],
    'logger' => [
        'path'     => APP_DIR . '/logs/',
        'format'   => '%date% [%type%] %message%',
        'date'     => 'Y-m-d H:i:s',
        'logLevel' => Logger::DEBUG,
        'filename' => 'application.log',
    ],

    'xmlapi' => include 'xmlapi.php',
]);

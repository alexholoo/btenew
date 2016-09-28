<?php

// these keys can be obtained by registering at http://developer.ebay.com

return [

    'bte' => [
        'devID' => '355b1e91-0fed-4944-9ff7-8960d9584bd6',
        'appID' => 'BTECompu-7d59-44bc-9f9f-e030bc1be7b2',
        'certID' => '3ac0f576-4c74-4d15-85a1-f5faccb23998',
        // set the Server to use (Sandbox or Production)
        'serverUrl' => 'https://api.ebay.com/ws/api.dll',
        // the token representing the eBay user to assign the call with
        'userToken' => 'AgAAAA**AQAAAA**aAAAAA**UA+1Vg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wJl4alCZCLowWdj6x9nY+seQ**YSkDAA**AAMAAA**o0+0YTORcUvxWXVA6emkybreQXAlv0AHoAuhchILUmps8Et8TsjaGz9R72FLzy4mbkgntL0icaeMYWOJgrT4mJxGKAhAwO/ggAuLzZMxs8MDDpomI15nuI97G+NRIvZtI448kbNpHfGGKMC8eyU0BKGqrtCPPwFXQk+YMxCwpqepavKRMz6W/neR+6/g7nX8sZOyEijReGJjz3yFkwviNFmA0ITLpCgqi3lO0JfhRW6+wzF2DnJ5hqgwGxzwlOmb8LnlMtHqPzkp+wb69cWpOHv++XAJmW3Cq502/2fbAbeWP34DsAABarudz+AWYtT8ngX120FHRsdUQks+loxnDI6moTaghoJmr7/PQxRDPlUUlBafdFBugmwn/U7Bp70y0SCNjPn05UjgNMDUuXq9ffjMclnOIQEmmWBlyOSYbNe/vm9Zs02EYXVt2QJEJRM8e70jk394095OK5G7VMTasRs7UxqsLSfdGD6YREX5poIevq4l0GKI7rFwFtxE5bMH7XBbKwc5+OBOI5wdsO6N87VhyaQx/r/hCf6P8JmdQ9NKvvzdSCFzDyfmzB1Lxkoi86hL++ikj63m/MH7YXz/HiRLWWoJUktGrzJYv3k6seT4CKvKwB5aNhJ02RzwsYr0g629NNuSvlhZukt2miXu7GutskCn2S+2AyT+Z/ZQaaaG9EyZ7Npe+fAbffO6hsr6KX9QGnGPVlFz/eFMEoJZePCcekaP1kd0wcLrJxBNMmMqBZN7lPxuxqFIbUpqA9zQ',
    ],

    'odo' => [
        'devID' => '355b1e91-0fed-4944-9ff7-8960d9584bd6',
        'appID' => 'BTECompu-7d59-44bc-9f9f-e030bc1be7b2',
        'certID' => '3ac0f576-4c74-4d15-85a1-f5faccb23998',
        // set the Server to use (Sandbox or Production)
        'serverUrl' => 'https://api.ebay.com/ws/api.dll',
        // the token representing the eBay user to assign the call with
        'userToken' => 'AgAAAA**AQAAAA**aAAAAA**z7npVg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFmYeoDZCGqAudj6x9nY+seQ**YSkDAA**AAMAAA**2iXcGEAjAs74fb8oKltBRritLBhlx603mo2i0i8rBGbbrB/u+8gR2EnKdtl+9DtQJHhz42wxuzAA6SsGifEetX9wJrZYX8jTm2+J1MlDqBMF/Rp+h1EBQP3W6Kg2l41aLup7CxBofihwWv36MHjKw/rdV7xNchJ6dQkwLLyB89L0MyVqCbTa5MQkfQ3kbi2RX6yo9LJObVyFs8EUrIfecszF/BSyh1l5b+5wtq6Cq1aN3gO7uLRiliyWpTVGS+0AM6hRewnJBl8YeyIETUK8+EP38ejkbNdrtUuQG8qBJs8W/xfgyzyroHNDruftCtBKWcWP1fR4+hy40YDDiJOR19/1ft62FSPWJEhysReeL9DhRgEqZPA7ij33NMsAi+PefN/ByKljSRUPGpjkgfPyuTp6gvluZyN6hiUbfC3EBftFD3QSnQGu/lw1o/L8zKbRclASSQX7gf4m5gizXjSPBbHJYUnlzjbzNro7qER8l13vhTD36bkiBx3W2XupvpByblwS1d46zdWn0W+HrBi3x+Hqzz79lYyLLBDTmBhnCF9eP2S4Bz1y3Ube4msan36nxCte+GEfivqsJk9RSPEHAFhuXXGb6A/57o+DXvnjrkZl534XrxjLWit8EPbw0i3E5bnA7wkzsA0GChAeYEV8JCSvoctG9OgRxb5iMQbiL4HkndN9DLeHyeO2mvaRMbxShLpa2ldplHlOCzM7gRUiKXMZL3VQbNXKhhsBFKSbzZOQPEictgO4DqnRWkLkz0C1',
    ],

    // sandbox (test) environment
    'sandbox' => [
        'devID' => 'xxxxxxxx',
        'appID' => 'xxxxxxxxx';
        'certID' => 'xxxxxxxxxxxxxx',
        // set the Server to use (Sandbox or Production)
        'serverUrl' => 'https://api.sandbox.ebay.com/ws/api.dll',
        // the token representing the eBay user to assign the call with
        // this token is a long string - don't insert new lines - different from prod token
        'userToken' => '*************',
    ],
];

<?php

use \Output\Output;
use \User\User;
use \Authentication\Authentication;



/**
*
* Get getDowJones Value from b3 or investing plataform
*
**/

$app->get('/getDowJones', function ($request, $response, array $args) {
  $Output = new Output();

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://br.investing.com/common/modules/js_instrument_chart/api/data.php?pair_id=169&pair_id_for_news=169&chart_type=area&pair_interval=86400&candle_count=120&events=yes&volume_series=yes');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

  $headers = array();
  $headers[] = 'Connection: keep-alive';
  $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
  $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36';
  $headers[] = 'X-Requested-With: XMLHttpRequest';
  $headers[] = 'Sec-Fetch-Site: same-origin';
  $headers[] = 'Sec-Fetch-Mode: cors';
  $headers[] = 'Sec-Fetch-Dest: empty';
  $headers[] = 'Referer: https://br.investing.com/indices/us-30';
  $headers[] = 'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7';
  $headers[] = 'Cookie: PHPSESSID=0ln4ko0lk5ffh1e7i162kcoseh; geoC=BR; prebid_session=0; adBlockerNewUserDomains=1592681232; StickySession=id.25756790998.343.br.investing.com; _ga=GA1.2.906389404.1592681233; _gid=GA1.2.1391671557.1592681234; adbBLk=30; G_ENABLED_IDPS=google; r_p_s_n=1; SideBlockUser=a%3A2%3A%7Bs%3A10%3A%22stack_size%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Bi%3A8%3B%7Ds%3A6%3A%22stacks%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Ba%3A1%3A%7Bi%3A0%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22169%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A14%3A%22%2Findices%2Fus-30%22%3B%7D%7D%7D%7D; gtmFired=OK; prebid_page=0; _gat=1; _gat_allSitesTracker=1; nyxDorf=Mzc3Y2M8YiA%2FaD05NWE3K2Q0ZDcwMGJ%2BZmY%3D';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  curl_close($ch);

  return $Output->response($response, 200, json_decode($result));
});





/**
*
* Get getSP500 Value from b3 or investing plataform
*
**/

$app->get('/getSP500', function ($request, $response, array $args) {
  $Output = new Output();

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://br.investing.com/common/modules/js_instrument_chart/api/data.php?pair_id=166&pair_id_for_news=166&chart_type=area&pair_interval=86400&candle_count=120&events=yes&volume_series=yes');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

  $headers = array();
  $headers[] = 'Connection: keep-alive';
  $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
  $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36';
  $headers[] = 'X-Requested-With: XMLHttpRequest';
  $headers[] = 'Sec-Fetch-Site: same-origin';
  $headers[] = 'Sec-Fetch-Mode: cors';
  $headers[] = 'Sec-Fetch-Dest: empty';
  $headers[] = 'Referer: https://br.investing.com/indices/us-spx-500';
  $headers[] = 'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7';
  $headers[] = 'Cookie: PHPSESSID=0ln4ko0lk5ffh1e7i162kcoseh; geoC=BR; prebid_session=0; adBlockerNewUserDomains=1592681232; StickySession=id.25756790998.343.br.investing.com; _ga=GA1.2.906389404.1592681233; _gid=GA1.2.1391671557.1592681234; adbBLk=30; G_ENABLED_IDPS=google; r_p_s_n=1; gtmFired=OK; _gat=1; _gat_allSitesTracker=1; SideBlockUser=a%3A2%3A%7Bs%3A10%3A%22stack_size%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Bi%3A8%3B%7Ds%3A6%3A%22stacks%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Ba%3A2%3A%7Bi%3A0%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22169%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A14%3A%22%2Findices%2Fus-30%22%3B%7Di%3A1%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22166%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Findices%2Fus-spx-500%22%3B%7D%7D%7D%7D; prebid_page=0; nyxDorf=Z2NiNmU6ZSczZGpuZzNifj5uZTYyMmV5NDQ%3D';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  curl_close($ch);

  return $Output->response($response, 200, json_decode($result));
});





/**
*
* Get getBitcoin Value from b3 or investing plataform
*
**/

$app->get('/getBitcoin', function ($request, $response, array $args) {
  $Output = new Output();

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://br.investing.com/common/modules/js_instrument_chart/api/data.php?pair_id=1024807&pair_id_for_news=1024807&chart_type=area&pair_interval=900&candle_count=120&events=yes&volume_series=yes');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

  $headers = array();
  $headers[] = 'Connection: keep-alive';
  $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
  $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36';
  $headers[] = 'X-Requested-With: XMLHttpRequest';
  $headers[] = 'Sec-Fetch-Site: same-origin';
  $headers[] = 'Sec-Fetch-Mode: cors';
  $headers[] = 'Sec-Fetch-Dest: empty';
  $headers[] = 'Referer: https://br.investing.com/crypto/bitcoin/btc-brl';
  $headers[] = 'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7';
  $headers[] = 'Cookie: PHPSESSID=0ln4ko0lk5ffh1e7i162kcoseh; geoC=BR; prebid_session=0; adBlockerNewUserDomains=1592681232; StickySession=id.25756790998.343.br.investing.com; _ga=GA1.2.906389404.1592681233; _gid=GA1.2.1391671557.1592681234; adbBLk=30; G_ENABLED_IDPS=google; r_p_s_n=1; gtmFired=OK; prebid_page=0; _gat_allSitesTracker=1; SideBlockUser=a%3A2%3A%7Bs%3A10%3A%22stack_size%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Bi%3A8%3B%7Ds%3A6%3A%22stacks%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Ba%3A6%3A%7Bi%3A0%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22169%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A14%3A%22%2Findices%2Fus-30%22%3B%7Di%3A1%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22166%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Findices%2Fus-spx-500%22%3B%7Di%3A2%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A4%3A%222103%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A32%3A%22D%C3%B3lar+Americano+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Fcurrencies%2Fusd-brl%22%3B%7Di%3A3%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A4%3A%221617%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A20%3A%22Euro+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Fcurrencies%2Feur-brl%22%3B%7Di%3A4%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A6%3A%22945629%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A24%3A%22Bitcoin+D%C3%B3lar+Americano%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A23%3A%22%2Fcrypto%2Fbitcoin%2Fbtc-usd%22%3B%7Di%3A5%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A7%3A%221024807%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A23%3A%22Bitcoin+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A23%3A%22%2Fcrypto%2Fbitcoin%2Fbtc-brl%22%3B%7D%7D%7D%7D; nyxDorf=MTU1YWQ7YSM2YWpuNGA4JGMzNmVjYzomPT0%3D; _gat=1';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  curl_close($ch);

  return $Output->response($response, 200, json_decode($result));
});





/**
*
* Get getDolar Value from b3 or investing plataform
*
**/

$app->get('/getDolar', function ($request, $response, array $args) {
  $Output = new Output();

   $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://br.investing.com/common/modules/js_instrument_chart/api/data.php?pair_id=2103&pair_id_for_news=2103&chart_type=area&pair_interval=900&candle_count=120&events=yes&volume_series=yes');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

  $headers = array();
  $headers[] = 'Connection: keep-alive';
  $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
  $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36';
  $headers[] = 'X-Requested-With: XMLHttpRequest';
  $headers[] = 'Sec-Fetch-Site: same-origin';
  $headers[] = 'Sec-Fetch-Mode: cors';
  $headers[] = 'Sec-Fetch-Dest: empty';
  $headers[] = 'Referer: https://br.investing.com/currencies/usd-brl';
  $headers[] = 'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7';
  $headers[] = 'Cookie: PHPSESSID=0ln4ko0lk5ffh1e7i162kcoseh; geoC=BR; prebid_session=0; adBlockerNewUserDomains=1592681232; StickySession=id.25756790998.343.br.investing.com; _ga=GA1.2.906389404.1592681233; _gid=GA1.2.1391671557.1592681234; adbBLk=30; G_ENABLED_IDPS=google; r_p_s_n=1; gtmFired=OK; prebid_page=0; SideBlockUser=a%3A2%3A%7Bs%3A10%3A%22stack_size%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Bi%3A8%3B%7Ds%3A6%3A%22stacks%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Ba%3A6%3A%7Bi%3A0%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22169%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A14%3A%22%2Findices%2Fus-30%22%3B%7Di%3A1%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22166%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Findices%2Fus-spx-500%22%3B%7Di%3A2%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A4%3A%222103%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A32%3A%22D%C3%B3lar+Americano+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Fcurrencies%2Fusd-brl%22%3B%7Di%3A3%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A4%3A%221617%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A20%3A%22Euro+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Fcurrencies%2Feur-brl%22%3B%7Di%3A4%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A6%3A%22945629%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A24%3A%22Bitcoin+D%C3%B3lar+Americano%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A23%3A%22%2Fcrypto%2Fbitcoin%2Fbtc-usd%22%3B%7Di%3A5%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A7%3A%221024807%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A23%3A%22Bitcoin+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A23%3A%22%2Fcrypto%2Fbitcoin%2Fbtc-brl%22%3B%7D%7D%7D%7D; nyxDorf=NjJjN2Q7MHI%2BaWxoZjJhfTZmNWYxMTMvYWE%3D; _gat=1; _gat_allSitesTracker=1';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  curl_close($ch);

  return $Output->response($response, 200, json_decode($result));
});





/**
*
* Get getEuro Value from b3 or investing plataform
*
**/

$app->get('/getEuro', function ($request, $response, array $args) {
  $Output = new Output();

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://br.investing.com/common/modules/js_instrument_chart/api/data.php?pair_id=1617&pair_id_for_news=1617&chart_type=area&pair_interval=900&candle_count=120&events=yes&volume_series=yes');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

  $headers = array();
  $headers[] = 'Connection: keep-alive';
  $headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
  $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36';
  $headers[] = 'X-Requested-With: XMLHttpRequest';
  $headers[] = 'Sec-Fetch-Site: same-origin';
  $headers[] = 'Sec-Fetch-Mode: cors';
  $headers[] = 'Sec-Fetch-Dest: empty';
  $headers[] = 'Referer: https://br.investing.com/currencies/eur-brl';
  $headers[] = 'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7';
  $headers[] = 'Cookie: PHPSESSID=0ln4ko0lk5ffh1e7i162kcoseh; geoC=BR; prebid_session=0; adBlockerNewUserDomains=1592681232; StickySession=id.25756790998.343.br.investing.com; _ga=GA1.2.906389404.1592681233; _gid=GA1.2.1391671557.1592681234; adbBLk=30; G_ENABLED_IDPS=google; r_p_s_n=1; gtmFired=OK; prebid_page=0; SideBlockUser=a%3A2%3A%7Bs%3A10%3A%22stack_size%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Bi%3A8%3B%7Ds%3A6%3A%22stacks%22%3Ba%3A1%3A%7Bs%3A11%3A%22last_quotes%22%3Ba%3A6%3A%7Bi%3A0%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22169%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A14%3A%22%2Findices%2Fus-30%22%3B%7Di%3A1%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A3%3A%22166%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A0%3A%22%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Findices%2Fus-spx-500%22%3B%7Di%3A2%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A4%3A%222103%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A32%3A%22D%C3%B3lar+Americano+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Fcurrencies%2Fusd-brl%22%3B%7Di%3A3%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A4%3A%221617%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A20%3A%22Euro+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A19%3A%22%2Fcurrencies%2Feur-brl%22%3B%7Di%3A4%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A6%3A%22945629%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A24%3A%22Bitcoin+D%C3%B3lar+Americano%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A23%3A%22%2Fcrypto%2Fbitcoin%2Fbtc-usd%22%3B%7Di%3A5%3Ba%3A3%3A%7Bs%3A7%3A%22pair_ID%22%3Bs%3A7%3A%221024807%22%3Bs%3A10%3A%22pair_title%22%3Bs%3A23%3A%22Bitcoin+Real+Brasileiro%22%3Bs%3A9%3A%22pair_link%22%3Bs%3A23%3A%22%2Fcrypto%2Fbitcoin%2Fbtc-brl%22%3B%7D%7D%7D%7D; _gat_allSitesTracker=1; _gat=1; nyxDorf=NzNmMmE%2BZCZmMTk9ZzMyLmU1MWI0NGJ%2BZ2c%3D';
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $result = curl_exec($ch);

  curl_close($ch);

  return $Output->response($response, 200, json_decode($result));
});





<?php

// Set your token
$s4aToken = '0123456789abcdef0123456789abcdef';
$s4aHost = 'https://api.seo4ajax.com/';

function x_forwarded_for() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function parse_request_headers() {
    $requestHeadersArray = array();
    $requestHeaders = '';
    foreach($_SERVER as $key => $value) {
        if(substr($key, 0, 5) == 'HTTP_') {
            $requestHeadersArray[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
        }
    }

    foreach($requestHeadersArray as $key => $value) {
        if($key != "Host" && $key != "Cookie" && $key != "X-Forwarded-For" && $key != "Connection") {
            $requestHeaders .= $key . ":" . $value . "\r\n";
        }
    }

    $requestHeaders .= 'X-Forwarded-For: '. x_forwarded_for() . '\r\n';
    $requestHeaders .= 'Connection: close\r\n';
    return $requestHeaders;
}


$opts = array(
  'http'=>array(
    'method'=>'GET',
    'header'=> parse_request_headers(),
    'follow_location' => false,
    'ignore_errors' => true
  )
);
$context = stream_context_create($opts);

$url = $s4aHost . $s4aToken . $_SERVER['REQUEST_URI'];
$result = file_get_contents($url, false, $context);

if ($result !== FALSE) {
    foreach($http_response_header as $headerLine) {
      header($headerLine);
    }
    echo $result;
}
else
{
    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
    header($protocol . ' 500 Internal Server Error');
}

?>
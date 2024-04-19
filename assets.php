<?php
require_once 'autoload.php';
//AMember

header('Access-Control-Allow-Origin: *');

//Proxy
$method = getRequestMethod();
$path = getRequestPath();
$headers = getForwardHeaders();
$account = getHelium10Account($db);

$requestURI = '/'.trim(explode('?', $path)[0], '/');
//Blocking

//Remove _assets from Path 
$path = str_replace('/_assets', '', $path);

$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36';
if(isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'content-type: '.$_SERVER['CONTENT_TYPE'];
}

if($account) {

    $cookies = getHttpCookies();

    $curl = new \Curl\Curl('https://assets.helium10.com'.$path);

    if($method == 'GET') {

        $curl->setOpts([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $curl->setCookieString($cookies);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();

        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://assets.helium10.com'], [$assetsDomain], $responseHeaders['location']));
            die();
        }

        http_response_code($curl->getHttpStatusCode());

        $responseHeaders->offsetUnset('status-line');
        $responseHeaders->offsetUnset('date');
        $responseHeaders->offsetUnset('set-cookie');
        $responseHeaders->offsetUnset('location');
        $responseHeaders->offsetUnset('server');
        $responseHeaders->offsetUnset('expires');
        $responseHeaders->offsetUnset('cache-control');
        $responseHeaders->offsetUnset('pragma');
        $responseHeaders->offsetUnset('vary');
        $responseHeaders->offsetUnset('etag');
        $responseHeaders->offsetUnset('last-modified');
        $responseHeaders->offsetUnset('accept-ranges');
        $responseHeaders->offsetUnset('content-length');

        foreach($responseHeaders as $name => $value) {
            header($name.': '.$value);
        }

        foreach($curl->getResponseCookies() as $c_name => $c_value) {
            setcookie($c_name, $c_value, 0, '/');
        }

        $response = $curl->getRawResponse(); //Do blocking here

        $response = str_replace([
            'https://members.helium10.com',
            'https://cdn.helium10.com',
            'https://assets.helium10.com',
        ], [
            $mainDomain,
            $cdnDomain,
            $assetsDomain
        ], $response);

        //Remove GTag
        $response = str_replace([
            'googletagmanager.com/ns.html',
            'googletagmanager.com/gtm.js'
        ], [
            'googletagmanager.com/ns2.html',
            'googletagmanager.com/gtm2.js'
        ], $response);

        die($response);

    } else if($method == 'POST') {

        /*
        if(@preg_match('/application\/x-www-form-urlencoded/', $_SERVER['CONTENT_TYPE'])) {
            $post = http_build_query($_POST);
        } else {
            $post = file_get_contents('php://input');
        }
        */
        $post = file_get_contents('php://input');
        $headers[] = 'content-length: '.strlen($post);

        $curl->setOpts([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
        ]);
        $curl->setCookieString($cookies);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();

        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://assets.helium10.com'], [$assetsDomain], $responseHeaders['location']));
            die();
        }

        http_response_code($curl->getHttpStatusCode());

        $responseHeaders->offsetUnset('status-line');
        $responseHeaders->offsetUnset('date');
        $responseHeaders->offsetUnset('set-cookie');
        $responseHeaders->offsetUnset('location');
        $responseHeaders->offsetUnset('server');
        $responseHeaders->offsetUnset('expires');
        $responseHeaders->offsetUnset('cache-control');
        $responseHeaders->offsetUnset('pragma');
        $responseHeaders->offsetUnset('vary');
        $responseHeaders->offsetUnset('etag');
        $responseHeaders->offsetUnset('last-modified');
        $responseHeaders->offsetUnset('accept-ranges');
        $responseHeaders->offsetUnset('content-length');

        foreach($responseHeaders as $name => $value) {
            header($name.': '.$value);
        }

        foreach($curl->getResponseCookies() as $c_name => $c_value) {
            setcookie($c_name, $c_value, 0, '/');
        }

        $response = $curl->getRawResponse(); //Do blocking here

        $response = str_replace([
            'https://members.helium10.com',
            'https://cdn.helium10.com',
            'https://assets.helium10.com',
        ], [
            $mainDomain,
            $cdnDomain,
            $assetsDomain
        ], $response);

        //Remove GTag
        $response = str_replace([
            'googletagmanager.com/ns.html',
            'googletagmanager.com/gtm.js'
        ], [
            'googletagmanager.com/ns2.html',
            'googletagmanager.com/gtm2.js'
        ], $response);

        die($response);

    } else {

        echo '<script>alert(\'There was an issue while making the request!\');</script>';

    }

} else {

    echo '<html><head><title>Updating Accounts, PLease Wait...</title></head><body><h1>Updating Accounts, Please wait...</h1><script>setTimeout(_ => window.location.href = \'/\', 5000)</script></body></html>';

}
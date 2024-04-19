<?php
require_once 'autoload.php';
//AMember

header('Access-Control-Allow-Origin: *');

//Proxy
$method = getRequestMethod();
$path = getRequestPath();
$headers = getForwardHeaders();
$account = getKeepaAccount($db);

$requestURI = '/'.trim(explode('?', $path)[0], '/');
//Blocking

//Remove _cdn from Path 
$path = str_replace('/_dyn', '', $path);

$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36';

if(isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'content-type: '.$_SERVER['CONTENT_TYPE'];
}

if($account) {

    $curl = new \Curl\Curl('https://dyn.keepa.com'.$path);
    
    
    if($method == 'HEAD'){
        
       http_response_code(200);
    }

    if($method == 'GET') {

        $curl->setOpts([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();

        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://dyn.keepa.com'], [$cdnDomain], $responseHeaders['location']));
            die();
        }

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


        $response = $curl->getRawResponse(); //Do blocking here
        
        $response = str_replace([
            'https://keepa.com',
            'cdn.keepa.com',
            'dyn.keepa.com',
            'keepa.com'
            
        ], [
            $mainDomain,
            $cdnDomain,
            'keepa.amztoolz.com/_dyn',
            'keepa.amztoolz.com',
            
        ], $response);
        
        
        die($response);

    } else if($method == 'POST') {

     
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
        
        $curl->exec();
        $responseHeaders = $curl->getResponseHeaders();

        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://dyn.keepa.com'], [$cdnDomain], $responseHeaders['location']));
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

        $response = $curl->getRawResponse(); //Do blocking here
        
       $response = str_replace([
            'https://keepa.com',
            'cdn.keepa.com',
            'dyn.keepa.com',
            'keepa.com'
            
        ], [
            $mainDomain,
            $cdnDomain,
            'keepa.amztoolz.com/_dyn',
            'keepa.amztoolz.com',
            
        ], $response);
        
        
        die($response);

    } else {

        echo '<script>alert(\'There was an issue while making the request!\');</script>';

    }

} else {

    echo '<html><head><title>Updating Accounts, PLease Wait...</title></head><body><h1>Updating Accounts, Please wait...</h1><script>setTimeout(_ => window.location.href = \'/\', 5000)</script></body></html>';

}
<?php
require_once 'autoload.php';
//AMember



//Proxy
$method = getRequestMethod();
$path = getRequestPath();
$headers = getForwardHeaders();
$account = getKeepaAccount($db);

$requestURI = '/'.trim(explode('?', $path)[0], '/');
//Blocking

//Remove _cdn from Path 
$path = str_replace('/_cdn', '', $path);

$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36';

if(isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'content-type: '.$_SERVER['CONTENT_TYPE'];
}

if($account) {

    $curl = new \Curl\Curl('https://cdn.keepa.com'.$path);

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
            header('Location: '.str_replace(['https://cdn.keepa.com'], [$cdnDomain], $responseHeaders['location']));
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
        
        
        $response = str_replace_all([
            'https://keepa.com',
            'cdn.keepa.com',
            'push.keepa.com',
            
            
        ], [
            $mainDomain,
            $cdnDomain,
            'keeepa.amztoolz.com/_dyn',
            
        ], $response);
        
        
        $re = '/wss:\/\/"\+([A-Z]|[a-z])*\[([A-Z]|[a-z]|[0-9])*\]\+"\/apps\/cloud/m';
        
        $response = preg_replace($re, 'ws://keeepa.amztoolz.com/_dyn/apps/cloud', $response);
        
        
        $reg = '/storage.token=\sa/m';
        
        $token = $account['token'];
        
        $response = preg_replace($reg, 'storage.token="'.$token.'"', $response);

        
        // $response = str_replace([
        //     'wss://"+server[0]+"/apps/cloud/',
        //     'wss://"+I[L]+"/apps/cloud',
        //     'wss://"+L[I]+"/apps/cloud',
        //     'wss://"+J[L]+"/apps/cloud',
        //     'wss://"+L[M]+"/apps/cloud',
        //     'wss://"+R[T]+"/apps/cloud',
        //     'wss://"+Q[T]+"/apps/cloud',
        //     'storage.token= a'
        // ], [
        //     'wss://wss.seovpn.net:8015/apps/cloud/',
        //     'wss://wss.seovpn.net:8015/apps/cloud/',
        //     'wss://wss.seovpn.net:8015/apps/cloud',
        //     'wss://wss.seovpn.net:8015/apps/cloud',
        //     'wss://wss.seovpn.net:8015/apps/cloud',
        //     'wss://wss.seovpn.net:8015/apps/cloud',
        //     'wss://wss.seovpn.net:8015/apps/cloud',
        //     'storage.token="9ao65ev9531g7v8q5i3rf9o5l1o94fqqtc3tkekb99ne9tce9b3rplse0uc0gb7a"'
        // ], $response);
         
    //   $response = str_replace([
    //         'dyn-2.keeepa.amztoolz.com',
    //         'wss://"+K[J]+"/apps/cloud/?app=keepaWebsite&version=1.6',
    //         'wss://"+server[0]+"/apps/cloud/?app=keepaWebsite&version=1.6'
            
    //     ], [

    //       'dyn.keeepa.amztoolz.com',
    //       'wss://wss.seovpn.net:8015/apps/cloud/?app=keepaWebsite&version=1.6',
    //       'wss://wss.seovpn.net:8015/apps/cloud/?app=keepaWebsite&version=1.6'
           
           
    //     ], $response);
        
        
        
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
            header('Location: '.str_replace(['https://cdn.keepa.com'], [$cdnDomain], $responseHeaders['location']));
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
            'push.keepa.com',
            
            
        ], [
            $mainDomain,
            $cdnDomain,
            'keeepa.amztoolz.com/_dyn',
            
        ], $response);
       
        
        die($response);

    } else {

        echo '<script>alert(\'There was an issue while making the request!\');</script>';

    }

} else {

    echo '<html><head><title>Updating Accounts, PLease Wait...</title></head><body><h1>Updating Accounts, Please wait...</h1><script>setTimeout(_ => window.location.href = \'/\', 5000)</script></body></html>';

}
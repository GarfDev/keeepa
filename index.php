<?php
require_once 'autoload.php';
require_once 'amember.php';
//AMember



if($loginstatus == '1' && $accesscheck != '1'){

  echo '<html><head><title>You dont have Access</title></head><body><center><h3><b>Hey '.$username.', You dont have access to use this tool. Please purchase it from your membership area.If you are not automatically redirected then <a href="'.$memberurl.'">Click Here</a></b></h3></center><script>setTimeout(_ => window.location.href = "'.$memberurl.'", 10000)</script></body></html>';
        exit(200);
}

if ($loginstatus != '1'){

      echo '<html><head><title>You Are Not Logged In</title></head><body><center><h3><b>Session Expired! Login Again. Wait you will be redirected. If you are not automatically redirected then <a href="'.$loginurl.'">Click Here</a></b></h3></center><script>setTimeout(_ => window.location.href =  "'.$loginurl.'", 3000)</script></body></html>';
      exit(200);
}


//Proxy
$method = getRequestMethod();
$path = getRequestPath();
$headers = getForwardHeaders();
$account = getKeepaAccount($db);
$account_id = $account['account_id'];


$proxy = $db->query('SELECT * FROM proxy WHERE status = 1 ORDER BY timestamp ASC')->fetch(PDO::FETCH_ASSOC);
$proxy_set = $proxy['username'].':'.$proxy['password'].'@'.$proxy['ip'].':'.$proxy['port'];



$requestURI = '/'.trim(explode('?', $path)[0], '/');

$blocked_url = $db->query('SELECT * FROM helium_url_blocking')->fetchall(PDO::FETCH_ASSOC);
$blocked_urls = [];
foreach($blocked_url as $x_url) {
    $blocked_urls[] = $x_url['url'];
}

if (in_array($requestURI, $blocked_urls, TRUE)) {
    header('Location: /?accountId='.$account_id);
    exit;
}



//Blocking

$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.97 Safari/537.36';

if(isset($_SERVER['CONTENT_TYPE'])) {
    $headers[] = 'content-type: '.$_SERVER['CONTENT_TYPE'];
}



  $blockingScript = <<<SCRIPT
   
	
	<style>
	#panelUserMenu{display:none !important;}
	#panelUser{display:none !important;}
	#GCBTab1 > section:nth-child(2) > div{display:none !important;}
	#settingsPage > div > section:nth-child(2) > div{display:none !important;}
	</style>

SCRIPT;

function returnResponse($response){

    global $cdnDomain;
    global $mainDomain;

    $response = str_replace([
            'https://keepa.com',
            'cdn.keepa.com',
            'dyn.keepa.com',
            'graph.keepa.com',
            'keepa.com',
            'Keepa.com'
            
        ], [
            $mainDomain,
            $cdnDomain,
            'keeepa.amztoolz.com/_dyn',
            'keeepa.amztoolz.com/_graph',
            'keeepa.amztoolz.com',
            'keeepa.amztoolz.com'
            
        ], $response);

    return $response;
}

$cookies = $account['cookies'];


if($account) {

    $curl = new \Curl\Curl('https://keepa.com'.$path);

    if($method == 'GET') {

        $curl->setOpts([
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_PROXY => $proxy_set,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $curl->setCookieString($cookies);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();
        
        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://keepa.com'], [$mainDomain], $responseHeaders['location']));
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
        $responseHeaders->offsetUnset('content-security-policy');

        foreach($responseHeaders as $name => $value) {
            header($name.': '.$value);
        }

        $response = $curl->getRawResponse(); //Do blocking here

        $response = returnResponse($response);
        
        
         if(strpos($response, '</head')) {
                $response = str_replace('</head>', $blockingScript."</head>", $response);
            }
            
            
             $response = str_replace([
            '<script crossorigin="anonymous" charset="UTF-8" type="text/javascript" src="//keeepa.amztoolz.com/_cdn/20230208/keepa.js"></script>',
            
        ], [
            '<script crossorigin="anonymous" charset="UTF-8" type="text/javascript" src="//keeepa.amztoolz.com/_cdn/20230208/keepa.js"></script><script>setInterval(() => {
    localStorage.removeItem("token")
    localforage.removeItem("token")
}, 100)</script>'
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
            CURLOPT_PROXY => $proxy_set,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
        ]);
        $curl->setCookieString($cookies);
        $curl->exec();

        $responseHeaders = $curl->getResponseHeaders();
       
        if(isset($responseHeaders['location'])) {
            header('Location: '.str_replace(['https://keepa.com'], [$mainDomain], $responseHeaders['location']));
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
        $responseHeaders->offsetUnset('content-security-policy');

        foreach($responseHeaders as $name => $value) {
            header($name.': '.$value);
        }


        $response = $curl->getRawResponse(); //Do blocking here

        $response = returnResponse($response);
        
        
        if(strpos($response, '</head')) {
            $response = str_replace('</head>', $blockingScript."</head>", $response);
        }

        die($response);

    } else {

        echo '<script>alert(\'There was an issue while making the request!\');</script>';

    }

} else {

    echo '<html><head><title>Updating Accounts, PLease Wait...</title></head><body><h1>Updating Accounts, Please wait...</h1><script>setTimeout(_ => window.location.href = \'/\', 5000)</script></body></html>';

}
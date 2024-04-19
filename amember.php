<?php
require_once 'functions.php';

$cookies = getHttpCookies();

//make a curl request with cookies and get the access data

            $post = 'product_ids=3,4,11,20';

            $curl = new \Curl\Curl('https://amztoolz.com/api_auth/api_remote.php');
            $curl->setOpts([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [
                    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
                    'cookie: '.$cookies
                ],
    
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
            ]);
            $curl->exec();
            $response = json_decode($curl->getResponse());
            

            if(isset($response->access)){
                
            $accesscheck = $response->access;
            
            $username = $response->username;
                    
            $loginstatus = $response->loginstatus;
                    
            $loginurl = $response->loginurl;
                    
            $memberurl = $response->memberurl;
            
            } else {

            $accesscheck = 0;
                
            $username = '';
                    
            $loginstatus = false;
                    
            $loginurl = $response->loginurl;
                    
            $memberurl = $response->memberurl;
                
            }
            

?>
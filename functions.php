<?php
require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'];
}

function getRequestPath() {
    return $_SERVER['REQUEST_URI'];
}

function getPostFormData() {
    return http_build_query($_POST);
}

function getForwardHeaders() {
    if(function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        $headers = [];
        foreach($_SERVER as $name => $value) {
            if(substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    $headers = new \Curl\CaseInsensitiveArray($headers);
    $headers->offsetUnset('host');
    $headers->offsetUnset('user-agent');
    $headers->offsetUnset('cookie');
    $headers->offsetUnset('accept-encoding');
    $headers->offsetUnset('origin');
    $headers->offsetUnset('referer');
    $headers->offsetUnset('expect');
    $headers->offsetUnset('content-length');
    $headers->offsetUnset('X-Real-Ip');
    $headers->offsetUnset('X-Forwarded-Server');
    $headers->offsetUnset('X-Forwarded-Proto');
    $headers->offsetUnset('X-Forwarded-Port');
    $headers->offsetUnset('X-Forwarded-Host');
    $headers->offsetUnset('Content-Type');
    $x = [];
    foreach($headers as $name => $value) {
        $x[] = $name.': '.$value;
    }
    return $x;
}

function getKeepaAccount(\Medoo\Medoo $db) {
    return $db->query('SELECT * FROM `keepa_accounts` WHERE `login` = 1 AND `status` = 1 ORDER BY `timestamp` ASC')->fetch();
}

function getHttpCookies() {
    if(!empty($_COOKIE)) {
        $a = [];
        foreach($_COOKIE as $name => $value) {
            if(is_array($value)) {
                foreach($value as $key => $xValue) {
                    $a[] = $name.'['.$key.']='.$xValue;
                }
            } else {
                $a[] = $name.'='.$value;
            }
        }
        return implode('; ', $a);
    } else {
        return '';
    }
}
<?php

$db_config = [
    'database_type' => 'mysql',
    'database_name' => 'amztoolz_toolsdb',
    'server' => 'localhost',
    'username' => 'amztoolz_user66',
    'password' => 'cDVknqnVgSy)',
    //Extra Config
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
    'option' => [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
];


$mainDomain = 'https://keeepa.amztoolz.com';
$cdnDomain = 'keeepa.amztoolz.com/_cdn';
$socketDomain = 'https://keeepa.amztoolz.com/_dyn';
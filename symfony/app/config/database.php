<?php
$dbal = array(
    'driver'   => 'pdo_pgsql',
    'host'     => 'localhost',
    'dbname'   => 'portico',
    'user'     => 'portico',
    'password' => 'portico',
    'port'     => '5432',
    'charset'  => 'UTF8'
);

$dbFilePath = dirname(__FILE__).'/../../../dbconfig.php';
if(file_exists($dbFilePath)){
    $dbConfig = include_once($dbFilePath);
    $dbal['driver']   = 'pdo_pgsql';
    $dbal['host']     = $dbConfig['default']['db_host'];
    $dbal['dbname']   = $dbConfig['default']['db_name'];
    $dbal['user']     = $dbConfig['default']['db_user'];
    $dbal['password'] = $dbConfig['default']['db_pass'];
    $dbal['port']     = !empty($dbConfig['default']['db_port']) ? $dbConfig['default']['db_port'] : $dbal['port'];
    $dbal['charset']  = 'UTF8';
} 

$container->loadFromExtension('doctrine', array(
    'dbal' => $dbal,
    'orm' => array(
    	'auto_generate_proxy_classes' => '%kernel.debug%',
    	'naming_strategy' => 'doctrine.orm.naming_strategy.underscore',
    	'auto_mapping' => true
    )
));

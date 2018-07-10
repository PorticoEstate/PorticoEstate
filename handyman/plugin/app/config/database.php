<?php
	/**
	 * This file retrieve the database information form BK Bygg and uses the connection strings from there
	 */
	$dbal = array(
		'driver'   =>	'pdo_pgsql',
		'host'     =>	'localhost',
		'dbname'   =>	'',
		'user'     =>	'',
		'password' =>	'',
		'port'     =>	'5432',
		'charset'  =>	'UTF8'
	);

	$db_file_path = dirname(__FILE__).'/../../../../dbconfig.php';
	if(file_exists($db_file_path)){
		$db_config = include_once($db_file_path);
		$dbal['driver']   = 'pdo_pgsql';
		$dbal['host']     = $db_config['default']['db_host'];
		$dbal['dbname']   = $db_config['default']['db_name'];
		$dbal['user']     = $db_config['default']['db_user'];
		$dbal['password'] = $db_config['default']['db_pass'];
		$dbal['port']     = !empty($db_config['default']['db_port']) ? $db_config['default']['db_port'] : $dbal['port'];
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

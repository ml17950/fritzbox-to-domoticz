<?php
	include_once('lib/class.fritzbox.php');
	include_once('lib/class.domoticz.php');

	if (!is_file('conf/config.ini'))
		die('conf/config.ini not found!');

	$config = parse_ini_file('conf/config.ini', true);

	if (empty($config['fritzbox']['hostaddr']) || empty($config['fritzbox']['username']) || empty($config['fritzbox']['password']))
		die('fritzbox config missing or empty');

	if (empty($config['domoticz']['hostaddr']))
		die('domoticz config missing or empty');

	$fritzbox = new ClassFritzBox($config['fritzbox']['hostaddr'], $config['fritzbox']['password'], $config['fritzbox']['username']);
	$domoticz = new ClassDomoticz($config['domoticz']['hostaddr']);



	print_r($config);
	// echo highlight_string(print_r($config, true));
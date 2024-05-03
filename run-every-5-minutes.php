<?php
	include_once(__DIR__.'/lib/class.fritzbox.php');
	include_once(__DIR__.'/lib/class.domoticz.php');

	if (!is_file(__DIR__.'/conf/config.ini'))
		die('conf/config.ini not found!');

	$config = parse_ini_file(__DIR__.'/conf/config.ini', true);

	if (empty($config['fritzbox']['hostaddr']) || empty($config['fritzbox']['username']) || empty($config['fritzbox']['password']))
		die('fritzbox config missing or empty');

	if (empty($config['domoticz']['hostaddr']))
		die('domoticz config missing or empty');

	// print_r($config);

	$fritzbox = new ClassFritzBox($config['fritzbox']['hostaddr'], $config['fritzbox']['password'], $config['fritzbox']['username']);
	$domoticz = new ClassDomoticz($config['domoticz']['hostaddr']);

	// ############################################################
	// read data from fritzbox
	// ############################################################

	if (!$fritzbox->doLogin())
		die('FritzBox login failed');

	$power = $fritzbox->getEnergyValues();

	print_r($power);

	// ############################################################
	// transfer data to domoticz
	// ############################################################

	if ($power['current_power'] > 0) {
		$domoticz->setValue($config['domoticz']['current_energy_id'], 0, $power['current_power']);

		$power_data = $power['current_power'].';'.$power['today_power'];
		$domoticz->setValue($config['domoticz']['today_energy_id'], 0, $power_data);
	}

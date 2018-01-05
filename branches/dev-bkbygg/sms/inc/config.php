<?php
	$web_title = 'phpgroupware';
	$email_footer = 'Mobile Portal System';

	$location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');
	$config = CreateObject('admin.soconfig', $location_id);

	$GLOBALS['phpgw_info']['sms_config'] = $config->config_data;
	$reserved_codes = array("PV", "BC", "GET", "PUT", "INFO", "SAVE", "DEL", "LIST",
		"RETR", "POP3", "SMTP", "BROWSE", "NEW", "SET", "POLL", "VOTE", "REGISTER", "REG",
		"DO", "USE", "EXECUTE", "EXEC", "RUN", "ACK");
	$GLOBALS['phpgw_info']['sms_config']['reserved_codes'] = $reserved_codes;

//_debug_array($GLOBALS['phpgw_info']['sms_config']);

	//$GLOBALS['phpgw_info']['sms_config']['web_title'] = $config->config_data['web_title'];
	//$GLOBALS['phpgw_info']['sms_config']['email_service'] = $config->config_data['email_service'];
	//$GLOBALS['phpgw_info']['sms_config']['email_footer'] = $config->config_data['email_footer'];
	//$GLOBALS['phpgw_info']['sms_config']['datetime_now'] = $datetime_now;
	//$GLOBALS['phpgw_info']['sms_config']['gateway_module'] = $config->config_data['common']['gateway_module'];
	//$GLOBALS['phpgw_info']['sms_config']['gateway_number'] = $config->config_data['common']['gateway_number'];
	//$GLOBALS['phpgw_info']['sms_config']['email_footer'] = $config->config_data['email_footer'];
	//$GLOBALS['phpgw_info']['sms_config']['gnokii_cfg'] = $config->config_data['gnokii_cfg'];



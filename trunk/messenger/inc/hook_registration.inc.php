<?php
	// Note: This is temp sample data, I will make a config option for it soon.

	$message['to']      = $account_lid;
	$message['subject'] = lang('Welcome');
	$message['content'] = $this->config['messenger_welcome_message'];

	$so = createobject('messenger.somessenger');
	$so->send_message($message,True);

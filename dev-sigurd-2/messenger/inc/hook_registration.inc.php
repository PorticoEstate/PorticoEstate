<?php
	// Note: This is temp sample data, I will make a config option for it soon.

	global $reg_info;

	$message['to']      = $reg_info['lid'];
	$message['subject'] = 'Welcome!';
	$message['content'] = '

Thanks for signing up for your new account, blah blah blah blah

';

	$so = createobject('messenger.somessenger');
	$so->send_message($message,True);

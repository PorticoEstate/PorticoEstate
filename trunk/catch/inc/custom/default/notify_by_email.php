<?php

	if(!isset($config_data['notify_email']) || $config_data['notify_email'])
	{
		throw new Exception('notify_accounting_by_email: missing "notify_email" in config for this catch schema');
	}

	$to_array = array
	(
		$GLOBALS['phpgw_info']['user']['preferences']['property']['email'],
		$config_data['notify_email']
	);
					

	if (!is_object($GLOBALS['phpgw']->send))
	{
		$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
	}

	$_to = implode(';',$to_array);

	$from_name = 'noreply';
	$from_email = "{$from_name}<noreply@resight.no>";
	$cc = '';
	$bcc ='';
	$subject = 'Resultat av import';
	$body = 'Se vedlegg';
	$jasper_id = isset($config_data['jasper_id']) && $config_data['jasper_id'] ? $config_data['jasper_id'] : 0;
	
	if(!$jasper_id)
	{
		throw new Exception('notify_accounting_by_email: missing "jasper_id" in config for this catch schema');
	}
			
	$jasper_parameters = '';
	$_parameters = array();

	$_parameters[] =  "id|{$id}";
	$jasper_parameters = '"' . implode(';', $_parameters) . '"';

	unset($_parameters);

	$output_type 		= 'PDF';
	$values_jasper		= execMethod('property.bojasper.read_single', $jasper_id);
	$report_source		= "{$GLOBALS['phpgw_info']['server']['files_dir']}/property/jasper/{$jasper_id}/{$values_jasper['file_name']}";
	$jasper_wrapper		= CreateObject('phpgwapi.jasper_wrapper');

	//_debug_array($jasper_parameters);
	//_debug_array($output_type);
	//_debug_array($report_source);die();

	try
	{
		$report = $jasper_wrapper->execute($jasper_parameters, $output_type, $report_source, true);
	}
	catch(Exception $e)
	{
		$error = $e->getMessage();
		echo "<H1>{$error}</H1>";
	}

	$jasper_fname = tempnam($GLOBALS['phpgw_info']['server']['temp_dir'], 'PDF_') . '.pdf';
	file_put_contents($jasper_fname, $report['content'], LOCK_EX);

	$attachments = array();

	$attachments[] = array
	(
		'file' => $jasper_fname,
		'name' => $report['filename'],
		'type' => $report['mime']
	);

	$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($body), '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments , true);
	unlink($jasper_fname);

<?php
	$validator = CreateObject('phpgwapi.EmailAddressValidator');

	if(isset($config_data['notify_email']) && $config_data['notify_email'])
	{
		$to_array = array();
		$_to_array = explode(',', $config_data['notify_email']);

		if (isset($config_data['notify_rule']) && $config_data['notify_rule'])
		{
			$notify_rule = explode(',', $config_data['notify_rule']);
			foreach($notify_rule as $_rule )
			{
				$__rule = explode('=&gt;', $_rule);
				$___rule = explode(';', trim($__rule[1]));
				if($__rule)
				{
					$_condition = explode('=', $__rule[0]);
					if($_condition)
					{
						$this->db->query("SELECT * FROM $target_table WHERE id = {$id} AND " . trim($_condition[0]) . "='" . trim($_condition[1]) . "'",__LINE__,__FILE__);
						if($this->db->next_record())
						{
							foreach($___rule as $____rule)
							{
								if(isset( $_to_array[($____rule-1)]))
								{
									$to_array[] =  $_to_array[($____rule-1)];
								}
							}
						}
					}
				}
			}
		}
		else
		{
			$to_array = $_to_array;
		}

		$to_array = array_unique($to_array);

		//_debug_array($to_array);

		$socommon		= CreateObject('property.socommon');
		$prefs = $socommon->create_preferences('property',$user_id);

		if ($validator->check_email_address($prefs['email']))
		{
			$account_name = $GLOBALS['phpgw']->accounts->id2name($user_id);
			// avoid problems with the delimiter in the send class
			if(strpos($account_name,','))
			{
				$_account_name = explode(',', $account_name);
				$account_name = ltrim($_account_name[1]) . ' ' . $_account_name[0];
			}
			$from_email = "{$account_name}<{$prefs['email']}>";

			$to_array[] = $from_email;
		}

		if (!is_object($GLOBALS['phpgw']->send))
		{
			$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
		}

		$_to = implode(';',$to_array);

		$from_name = 'noreply';
		$from_email = isset($from_email) && $from_email ? $from_email : "{$from_name}<sigurd.nes@bergen.kommune.no>";
		$cc = '';
		$bcc ='';
		$subject = "{$schema_text}::{$id}";

		// Include something in subject
		if(isset($config_data['email_include_in_subject']) && $config_data['email_include_in_subject'])
		{
			$params = explode('=&gt;', $config_data['email_include_in_subject']);
			$_metadata = $this->db->metadata($target_table);
			if ( isset( $_metadata[$params[1]] ) )
			{
				$this->db->query("SELECT {$params[1]} FROM $target_table WHERE id = {$id}",__LINE__,__FILE__);
				if($this->db->next_record())
				{
					$subject .= "::{$params[0]} " . $this->db->f($params[1]);
				}
			}
			unset($_metadata);
		}

		unset($_link_to_item);

		if(isset($config_data['email_message']) && $config_data['email_message'])
		{
			$body = str_replace(array('[', ']'), array('<', '>'), $config_data['email_message']);
		}
		else
		{
			$body ="<H2>Det er registrert ny post i {$schema_text}</H2>";
		}

		$jasper_id = isset($config_data['jasper_id']) && $config_data['jasper_id'] ? $config_data['jasper_id'] : 0;

		$attachments = array();

		if(!$jasper_id)
		{
			$this->receipt['error'][]=array('msg'=>lang('notify_by_email: missing "jasper_id" in config for catch %1 schema', $schema_text));
		}
		else
		{
			$jasper_parameters = '';
			$_parameters = array();

			$_parameters[] =  "ID|{$id}";
			$jasper_parameters = '"' . implode(';', $_parameters) . '"';

			unset($_parameters);

			$output_type 		= 'PDF';
			$values_jasper		= execMethod('property.bojasper.read_single', $jasper_id);
			$report_source		= "{$GLOBALS['phpgw_info']['server']['files_dir']}/property/jasper/{$jasper_id}/{$values_jasper['file_name']}";
			$jasper_wrapper		= CreateObject('phpgwapi.jasper_wrapper');

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

			$attachments[] = array
			(
				'file' => $jasper_fname,
				'name' => $report['filename'],
				'type' => $report['mime']
			);

			if($attachments)
			{
				$body .= "</br>Se vedlegg";
			}
		}

		if ($_to && $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($body), '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments , true))
		{
			$this->receipt['message'][]=array('msg'=> "email notification sent to: {$_to}");
		}
		if( isset($jasper_fname) && is_file($jasper_fname) )
		{
			unlink($jasper_fname);
		}
	}

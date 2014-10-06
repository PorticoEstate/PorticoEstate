<?php
	$validator = CreateObject('phpgwapi.EmailAddressValidator');

	if(isset($config_data['notify_email']) && $config_data['notify_email'])
	{
		$to_array	 = array();
		$_to_array	 = explode(',', $config_data['notify_email']);

		if(isset($config_data['notify_rule']) && $config_data['notify_rule'])
		{
			$notify_rule = explode(',', $config_data['notify_rule']);
			foreach($notify_rule as $_rule)
			{
				$__rule	 = explode('=&gt;', $_rule);
				$___rule = explode(';', trim($__rule[1]));
				if($__rule)
				{
					$_condition = explode('=', $__rule[0]);
					if($_condition)
					{
						$this->db->query("SELECT * FROM $target_table WHERE id = {$id} AND " . trim($_condition[0]) . "='" . trim($_condition[1]) . "'", __LINE__, __FILE__);
						if($this->db->next_record())
						{
							foreach($___rule as $____rule)
							{
								if(isset($_to_array[($____rule - 1)]))
								{
									$to_array[] = $_to_array[($____rule - 1)];
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

		$socommon	 = CreateObject('property.socommon');
		$prefs		 = $socommon->create_preferences('property', $user_id);

		if($validator->check_email_address($prefs['email']))
		{
			$account_name = $GLOBALS['phpgw']->accounts->id2name($user_id);
			// avoid problems with the delimiter in the send class
			if(strpos($account_name, ','))
			{
				$_account_name	 = explode(',', $account_name);
				$account_name	 = ltrim($_account_name[1]) . ' ' . $_account_name[0];
			}
			$from_email = "{$account_name}<{$prefs['email']}>";

			$to_array[] = $from_email;
		}

		if(!is_object($GLOBALS['phpgw']->send))
		{
			$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
		}

		$_to = implode(';', $to_array);

		$from_name	 = 'noreply';
		$from_email	 = isset($from_email) && $from_email ? $from_email : "{$from_name}<sigurd.nes@bergen.kommune.no>";
		$cc			 = '';
		$bcc		 = '';
		$subject	 = "{$schema_text}::{$id}";

		// Include something in subject
		if(isset($config_data['email_include_in_subject']) && $config_data['email_include_in_subject'])
		{
			$params		 = explode('=&gt;', $config_data['email_include_in_subject']);
			$_metadata	 = $this->db->metadata($target_table);
			if(isset($_metadata[$params[1]]))
			{
				$this->db->query("SELECT {$params[1]} FROM $target_table WHERE id = {$id}", __LINE__, __FILE__);
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
			$body = "<H2>Det er registrert ny post i {$schema_text}</H2>";
		}

		$attachments = array();

		require_once PHPGW_SERVER_ROOT . "/catch/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/pdf_18_1.php";

		$pdf = new pdf_18_1();

		try
		{
			$report = $pdf->get_document($id);
		}
		catch(Exception $e)
		{
			$error = $e->getMessage();
			echo "<H1>{$error}</H1>";
		}


		$report_fname = tempnam($GLOBALS['phpgw_info']['server']['temp_dir'], 'PDF_') . '.pdf';
		file_put_contents($report_fname, $report, LOCK_EX);

		$attachments[] = array
		(
			'file' => $report_fname,
			'name' => "NLSH_melding_om_utflytting_{$id}.pdf",
			'type' => 'application/pdf'
		);

		if($attachments)
		{
			$body .= "</br>Rapport vedlagt";
		}

		$this->db->query("SELECT kontraktsnummer, leie_opphore_fra_dato FROM $target_table WHERE id = {$id}", __LINE__, __FILE__);
		$this->db->next_record();
		$_kontraktsnummer	 = $this->db->f('kontraktsnummer');
		$_utflyttingsdato	 = $this->db->f('leie_opphore_fra_dato');
		if($_utflyttingsdato)
		{
			$this->db->query("SELECT id, num, utflyttingsdato FROM fm_catch_3_1 WHERE kontraktsnummer = '{$_kontraktsnummer}'", __LINE__, __FILE__);
			if($this->db->next_record())
			{
				$_num_3_1				 = $this->db->f('num');
				$_id_3_1				 = $this->db->f('id');
				$_old_utflyttingsdato	 = $this->db->f('utflyttingsdato');

				$this->db->query("UPDATE fm_catch_3_1 SET utflyttingsdato = '{$_utflyttingsdato}' WHERE id = '{$_id_3_1}'", __LINE__, __FILE__);

				$body .= "</br></br>Utflyttingsdato oppdatert fra {$_old_utflyttingsdato} til {$_utflyttingsdato} for inneflyttemelding {$_num_3_1}";
			}
			else
			{
				$body .= "</br></br>Fant ikke inneflyttemelding for kontraktsnummer {$_kontraktsnummer}";
			}
		}
		else
		{
			$body .= "</br>Utflyttingsdato ikke angitt";
		}

		if($_to && $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($body), '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments, true))
		{
			$this->receipt['message'][] = array('msg' => "email notification sent to: {$_to}");
		}
		if(isset($report_fname) && is_file($report_fname))
		{
			unlink($report_fname);
		}
	}

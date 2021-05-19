<?php
	/*
	 * This file will only work for the implementation of EBE/BM
	 */

	/**
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	if (!class_exists("controller_alert_head_of_school"))
	{

		class controller_alert_head_of_school
		{
			protected $account, $db, $message_title;

			function __construct($message_title = '')
			{
				$this->account	= (int)$GLOBALS['phpgw_info']['user']['account_id'];
				$this->db = & $GLOBALS['phpgw']->db;
				$this->message_title = $message_title;
			}


			public function send_alert( $check_list )
			{
				$head_of_school = $this->get_head_of_school($check_list->get_location_code());
				$rc = false;

				if(!empty($head_of_school['email']))
				{

					$enforce_ssl = $GLOBALS['phpgw_info']['server']['enforce_ssl'];
					$GLOBALS['phpgw_info']['server']['enforce_ssl'] = true;

					$report_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.get_report','check_list_id' => $check_list->get_id()), false, true, true);

					$GLOBALS['phpgw_info']['server']['enforce_ssl'] = $enforce_ssl;

					$html = <<<HTML
						<br/>
						<p>Leder: {$head_of_school['unit_leader']}</p>
						<a href="$report_link">Rapport fra kontrollen</a>
						<br/>
						<br/>
						<p>Om denne eposten er på ville veier, eller ikke gir mening<br/> - rapporter til Sigurd.Nes@Bergen.kommune.no</p>

HTML;

					$from_name	 = $GLOBALS['phpgw_info']['user']['fullname'];
					$from_email	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['email'];
					$cc = $from_email;

					$to_array = array('Brunet, Luis Andres Jorn <Luis.Brunet@bergen.kommune.no>', 'Sigurd Nes <Sigurd.Nes@bergen.kommune.no>');
//					$to_array[] = $head_of_school['email'];
					$to = implode(',', $to_array);
					
					$send = CreateObject('phpgwapi.send');
					try
					{
						$subject = "Kontroll gjennomført: {$head_of_school['company_name']} / {$this->message_title}";
						$rc = $send->msg('email', $to, $subject, $html, '', $cc='', $bcc='',$from_email, $from_name,'html');
					}
					catch (Exception $e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');

					}
				}
				return $rc;
			}

			public function get_head_of_school($location_code)
			{
				$location_arr = explode('-', $location_code);

				if(count($location_arr) > 1)
				{
					$location_filter = $location_code;
				}
				else
				{
					$location_filter = "{$location_code}-01";
				}

				$sql = "SELECT rental_contract.date_end, org_enhet_id, rental_party.* FROM rental_party
				 JOIN rental_contract_party ON rental_party.id = rental_contract_party.party_id
				 JOIN rental_contract ON rental_contract_party.contract_id = rental_contract.id
				 JOIN rental_contract_composite ON rental_contract_composite.contract_id = rental_contract.id
				 JOIN rental_unit ON rental_contract_composite.composite_id = rental_unit.composite_id
				 WHERE location_code = '{$location_filter}'
				 AND rental_contract.date_start < extract(epoch from now())
				 AND (rental_contract.date_end > extract(epoch from now()) OR rental_contract.date_end IS NULL)
				 AND org_enhet_id IS NOT NULL";

				 $this->db->query($sql, __LINE__, __FILE__);
				 $this->db->next_record();
				 $company_name = $this->db->f('company_name', true);

				 $org_unit_id = $this->db->f('org_enhet_id');
				 $unit_leader = $this->db->f('unit_leader');
				 $email = $this->db->f('email');

				 //overide
				 if($org_unit_id)
				 {
					phpgw::import_class('rental.bofellesdata');
					$bofelles = rental_bofellesdata::get_instance();
					$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);

					if($org_unit_with_leader['ORG_EMAIL'])
					{
						$email =  $org_unit_with_leader['ORG_EMAIL'];
					}
					else if ( $org_unit_with_leader['LEADER_EMAIL'])
					{
						$email =  $org_unit_with_leader['LEADER_EMAIL'];
					}

					if($org_unit_with_leader['LEADER_FULLNAME'])
					{
						$unit_leader = $org_unit_with_leader['LEADER_FULLNAME'];
					}

					if($org_unit_with_leader['ORG_UNIT_NAME'])
					{
						$company_name = $org_unit_with_leader['ORG_UNIT_NAME'];
					}
				 }
				 
				 return array(
					 'company_name'	 => $company_name,
					 'unit_leader'	 => $unit_leader,
					 'email'		 => $email
					);
			}
		}
	}

	if($check_list->get_status() == controller_check_list::STATUS_DONE)
	{

		$message_title = $this->so_control->get_single($check_list->get_control_id())->get_title();

		$alert_head_of_school = new controller_alert_head_of_school($message_title);

		try
		{
			$alert_head_of_school->send_alert($check_list);
		}
		catch (Exception $exc)
		{
			phpgwapi_cache::message_set($exc->getMessage(), 'error');
		}
	}
	


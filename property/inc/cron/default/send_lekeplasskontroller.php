<?php

/**
 * phpGroupWare - property: a Facilities Management System.
 *
 * @author Sigurd Nes <sigurdne@online.no>
 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
 * This file is part of phpGroupWare.
 *
 * phpGroupWare is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * phpGroupWare is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phpGroupWare; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
 * @package property
 * @subpackage cron
 * @version $Id$
 */
/**
 * Description
 * example cron : /usr/bin/php -q /var/www/html/src/modules/property/inc/cron/cron.php default send_lekeplasskontroller
 * @package property
 */
include_class('property', 'cron_parent', 'inc/cron/');

class send_lekeplasskontroller extends property_cron_parent
{

	var $b_accounts = array();
	var $join;
	var $username, $password, $uicheck_list, $socheck_list, $send, $config, $recipient, $date_from;

	public function __construct()
	{
		parent::__construct();

		$this->function_name = get_class($this);
		$this->sub_location	 = lang('controller');
		$this->function_msg	 = 'send lekeplasskontroller til postmottak';
		$this->join			 = $this->db->join;
		$this->uicheck_list	 = CreateObject('controller.uicheck_list');
		$this->socheck_list = CreateObject('controller.socheck_list');
		$this->config		 = createObject('phpgwapi.config', 'controller')->read();
		$this->recipient =	$this->config['report_email'];
		$this->date_from =	(int)strtotime('2024-08-30'); //strtotime(date('Y-m-d', strtotime('-1 month')));
		$this->send			 = CreateObject('phpgwapi.send');
	}

	function execute()
	{
		$start = time();

		set_time_limit(0);


		$checlists = $this->get_checlists();
		$this->process_checklist($checlists);


		$msg						 = 'Tidsbruk: ' . (time() - $start) . ' sekunder';
		$this->cron_log($msg);
		echo "$msg\n";
		$this->receipt['message'][]	 = array('msg' => $msg);
	}

	function cron_log($receipt = '')
	{

		$insert_values = array(
			$this->cron,
			date($this->db->datetime_format()),
			$this->function_name,
			$receipt
		);

		$insert_values = $this->db->validate_insert($insert_values);

		$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
			. "VALUES ($insert_values)";
		$this->db->query($sql, __LINE__, __FILE__);
	}

	function get_checlists()
	{
		$completed_date = $this->date_from;
		$sql = "SELECT id, to_char(to_timestamp(completed_date),'YYYY-MM-DD') as ferdig_dato FROM controller_check_list 
		WHERE control_id = 9 AND completed_date IS NOT NULL AND (num_open_cases IS NOT NULL AND num_open_cases > 0)
		AND completed_date > {$completed_date}
		AND dispatched IS NULL
		ORDER by completed_date ASC";

		$this->db->query($sql, __LINE__, __FILE__);

		$checlists = array();
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->next_record();

		while ($this->db->next_record())
		{
			$checlists[] = (int)$this->db->f('id');
		}

		return $checlists;
	}


	function process_checklist($checlists)
	{
		if (!$this->recipient)
		{
			phpgwapi_cache::message_set("Missing recipient email address", 'error');
			return false;
		}
		foreach ($checlists as $check_list_id)
		{
			if ($this->send_report($check_list_id))
			{
				$now = time();
				$sql = "UPDATE controller_check_list SET dispatched = {$now} WHERE id = $check_list_id";
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}
	}

	function get_org_unit($location_code)
	{
		$location_arr = explode('-', $location_code);

		if (count($location_arr) > 1)
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
				 AND org_enhet_id IS NOT NULL";

		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->next_record();

		$company_name = $this->db->f('company_name', true);
		$org_unit_id = $this->db->f('org_enhet_id');
		return $company_name;
	}


	function send_report($check_list_id)
	{
		$check_list = $this->socheck_list->get_single($check_list_id);
		$company_name = $this->get_org_unit($check_list->get_location_code());

		if (!$company_name)
		{
			return false;
		}

		if ($this->debug)
		{
			_debug_array("Sending report for {$company_name} for checklist {$check_list_id} to $this->recipient");
			return false;
		}

		$report_file_path = $this->uicheck_list->get_report($check_list_id, true);

		if (!is_file($report_file_path))
		{
			return false;
		}

		$extension = pathinfo($report_file_path, PATHINFO_EXTENSION);
		$date = date("Y-m-d");
		$archive_file_name = "checklist_{$check_list_id}_{$date}.{$extension}";
		$file_name = "checklist_{$check_list_id}_{$date}.zip";
		//compress the file with widows-compatible zip
		$zip = new ZipArchive();
		$zip->open($report_file_path . '.zip', ZipArchive::CREATE);
		$zip->addFile($report_file_path, $archive_file_name);
		$zip->close();
		$report_file_path = $report_file_path . '.zip';


		$attachments = array();
		if ($report_file_path)
		{
			$attachments[] = array(
				'file'	 => $report_file_path,
				'name'	 => $file_name,
				'type'	 => 'application/zip',
			);
		}


		$component = $this->get_component((int)$check_list->get_location_id(), (int)$check_list->get_component_id());
		$to			 = 	!empty($component['postmottak']) ? $component['postmottak'] : $this->recipient;
		$saksnr_bk360	 = 	!empty($component['saksnr_bk360']) ? $component['saksnr_bk360'] : '';

		if ($saksnr_bk360)
		{
			$subject	 = 'Avviksrapport::' . $company_name . '::Saksnummer ' . $saksnr_bk360;
			$body		 = <<<HTML
		<p>Vedlegget hører til sak {$saksnr_bk360}.</p>
		<p>Det jobbes med en integrasjon for direkte levering, men inntil videre kommer rapporten som et vedlegg til denne eposten.</p>
		<p>Link til sak i BK360: <a href="https://bk360.adm.bgo/locator/Common/Search/Everything?searchstring={$saksnr_bk360}">{$saksnr_bk360}</a></p>
		<p>Med vennlig hilsen</p>
		<p>Bymiljøetaten</p>
HTML;
		}
		else
		{
			$subject	 = 'Avviksrapport::' . $company_name;
			$body		 = 'Sjå vedlegg';
		}

		$from_email	 = isset($this->config['email_sender']) && $this->config['email_sender'] ? $this->config['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
		$from_name	 = 'Bymiljøetaten';

		$bcc = 'Sigurd.Nes@Bergen.kommune.no';
		$error = false;
		try
		{
			$rcpt = $this->send->msg('email', $to, $subject, $body, '', $cc = '', $bcc, $from_email, $from_name, 'html', '', $attachments);
			if (!$rcpt)
			{
				$error = true;
			}
		}
		catch (Exception $e)
		{
			$error = true;
		}

		//clean up
		foreach ($attachments as $attachment)
		{
			unlink($attachment['file']);
			unlink($attachment['file'] . '.zip');
		}

		if (!$error)
		{
			return $check_list_id;
		}
		else
		{
			return false;
		}
	}

	function get_component($location_id, $component_id)
	{
		$sql = "SELECT json_representation FROM fm_bim_item WHERE location_id = $location_id AND id = $component_id";
		$this->db->query($sql, __LINE__, __FILE__);
		$this->db->next_record();
		return json_decode($this->db->f('json_representation'), true);
	}
}

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
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default bilagsinfo_BK
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class bilagsinfo_BK extends property_cron_parent
	{
		var $b_accounts = array();

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'Hent bilagsinformasjon fra Agresso';
			$this->db = & $GLOBALS['phpgw']->db;
			$this->join = & $this->db->join;
		}

		function execute()
		{
			set_time_limit(9000);

			require_once PHPGW_SERVER_ROOT . '/property/inc/soap_client/agresso/autoload.php';

			$sql = "SELECT fm_b_account.id AS b_account_id FROM fm_b_account";// WHERE active = 1";

			$this->db->query($sql, __LINE__, __FILE__);

			$b_accounts = array();
			while ($this->db->next_record())
			{
				$b_accounts[] = $this->db->f('b_account_id');
			}

			$this->b_accounts = $b_accounts;

			$sql = "SELECT external_voucher_id AS bilagsnr"
				. " FROM fm_ecobilagoverf"
				. " WHERE overftid > '20170101'"
				. " AND external_voucher_id IS NOT NULL"
				. " AND external_updated IS NULL";

			$this->db->query($sql, __LINE__, __FILE__);

			$bilagserie = array();
			while ($this->db->next_record())
			{
				$bilagserie[] = $this->db->f('bilagsnr');
			}

			foreach ($bilagserie as $bilagsnr)
			{
				$bilag = $this->get_bilag($bilagsnr);
				$this->update_bilag($bilag, $bilagsnr);
			}

			$messages = array();
			foreach ($messages as $message)
			{
				$this->receipt['message'][] = array('msg' => $message);
			}

		}

		function update_bilag($bilag, $bilagsnr)
		{
			$value_set = array
			(
				'periode' => $bilag[0]['period'],
	//			'pmwrkord_code' => $bilag[0]['order_id'],
				'mvakode' => 0,
				'netto_belop' => 0,
				'external_updated'	=> 1
			);
			$tax_code = 0;
			$netto_belop = 0;

			phpgwapi_cache::system_clear('property', "budget_order_{$bilag[0]['order_id']}");

			foreach ($bilag as $line)
			{
				if ($line['account'] == 2327010)
				{
	//				$value_set['belop'] = $line['amount'] * -1;
				}
				if (in_array($line['account'], $this->b_accounts))
				{
					$value_set['netto_belop'] += $line['amount'];
					$value_set['mvakode'] = $line['tax_code'];
				}
			}

			$value_set = $this->db->validate_update($value_set);
			$this->db->query("UPDATE fm_ecobilagoverf SET {$value_set} WHERE external_voucher_id = '{$bilagsnr}'", __LINE__, __FILE__);


			echo $value_set. PHP_EOL;

		}

		function get_bilag($bilagsnr)
		{
			$debug = false;

			$username = 'WEBSER';
			$password = 'wser10';
			$client = 'BY';
			$TemplateId = '11176'; //Spørring bilag_Portico ordrer

			$service = new \QueryEngineV201101(array('trace' => 1));
			$Credentials = new \WSCredentials();
			$Credentials->setUsername($username);
			$Credentials->setPassword($password);
			$Credentials->setClient($client);


			// Get the default settings for a template (templateId)
			$searchProp = $service->GetSearchCriteria(new \GetSearchCriteria($TemplateId, true, $Credentials));
			$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[0]->setFromValue($bilagsnr)->setToValue($bilagsnr);
			$searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList()->getSearchCriteriaProperties()[2]->setFromValue('201701')->setToValue('201812');
			if($debug)
			{
				_debug_array($searchProp);

				echo "====== REQUEST HEADERS =====" . PHP_EOL;
				print_r($service->__getLastRequestHeaders());
				echo "========= REQUEST ==========" . PHP_EOL;
				print_r($service->__getLastRequest());
				echo "========= RESPONSE =========" . PHP_EOL;
			}

			// Create the InputForTemplateResult class and set values
			$input = new InputForTemplateResult($TemplateId);
			$options = $service->GetTemplateResultOptions(new \GetTemplateResultOptions($Credentials));
			$options->RemoveHiddenColumns = true;
			$options->ShowDescriptions = true;
			$options->Aggregated = false;
			$options->OverrideAggregation= false;
			$options->CalculateFormulas= false;
			$options->FormatAlternativeBreakColumns= false;
			$options->FirstRecord= false;
			$options->LastRecord= false;

			if($debug)
			{
				echo "====== REQUEST HEADERS =====" . PHP_EOL;
				print_r($service->__getLastRequestHeaders());
				echo "========= REQUEST ==========" . PHP_EOL;
				print_r($service->__getLastRequest());
				echo "========= RESPONSE =========" . PHP_EOL;
			}

			$input->setTemplateResultOptions($options);
			// Get new values to SearchCriteria (if that’s what you want to do
			$input->setSearchCriteriaPropertiesList($searchProp->getGetSearchCriteriaResult()->getSearchCriteriaPropertiesList());
			//Retrieve result
			if($debug)
			{
				echo "====== REQUEST HEADERS =====" . PHP_EOL;
				print_r($service->__getLastRequestHeaders());
				echo "========= REQUEST ==========" . PHP_EOL;
				print_r($service->__getLastRequest());
				echo "========= RESPONSE =========" . PHP_EOL;
			}

			$result = $service->GetTemplateResultAsDataSet(new \GetTemplateResultAsDataSet($input, $Credentials));
			if($debug)
			{
				echo "====== REQUEST HEADERS =====" . PHP_EOL;
				print_r($service->__getLastRequestHeaders());
				echo "========= REQUEST ==========" . PHP_EOL;
				print_r($service->__getLastRequest());
				echo "========= RESPONSE =========" . PHP_EOL;
			}

			$data = $result->getGetTemplateResultAsDataSetResult()->getTemplateResult()->getAny();

			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('utf-8');
			$xmlparse->setDecodesUTF8Automaticly(false);
			$var_result = $xmlparse->parse($data);

			if($var_result)
			{
				$ret = $var_result['Agresso'][0]['AgressoQE'];
			}
			else
			{
				$ret = array();
			}

			return $ret;

		}

	}


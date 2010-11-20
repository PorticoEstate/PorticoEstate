<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2010 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'property'
	);

	include_once('../header.inc.php');


	if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
	{
		$organize = new koble_bygg_gatetabell();
		$organize->pre_run();
	}
	else
	{
		echo 'go away';
	}


	class koble_bygg_gatetabell
	{
		/* In Admin->Property->Async servises:
		*  Name: property.custom_functions.index
		*  Data: function=koble_bygg_gatetabell,dir=C:/path/to/gatetabell
		*/

		var	$function_name = 'koble_bygg_gatetabell';

		function koble_bygg_gatetabell()
		{
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->db2			= clone($this->db);
		}

		function pre_run()
		{

			$confirm	= get_var('confirm',array('POST'));
			$execute	= get_var('execute',array('GET'));

			if(!$execute)
			{
				$dry_run=True;
			}

			if ($confirm)
			{
				$this->execute($dry_run,$cron);
			}
			else
			{
				$this->confirm($execute=False);
			}
		}

		function confirm($execute='')
		{
			$link_data = array
			(
				'execute'		=> $execute,
			);

			if(!$execute)
			{
				$lang_confirm_msg 	= 'Gå videre for å se hva som blir lagt til';
			}
			else
			{
				$lang_confirm_msg 	= lang('kjør på');	
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));


			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('koble_bygg_gatetabell.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('koble_bygg_gatetabell.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'Legg til manglende gatenavn fra gatetabell',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= 'Legg til manglende gatenavn';
			$function_msg	= '';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($dry_run='',$cron='')
		{

			$street_list = $this->get_streets();

			$execute = false;
			if($dry_run)
			{
				_debug_array($street_list);
				$execute = true;
			}
			else
			{
				$this->update_location($street_list);
			}
			$this->confirm($execute);


		}

		function get_streets()
		{
			$this->db->query("SELECT id,descr as street_name from fm_streetaddress",__LINE__,__FILE__);
			$location = array();
			while ($this->db->next_record())
			{
				$street_name = $this->db->db_addslashes($this->db->f('street_name',true));

				$this->db2->query("SELECT location_code, adresse from fm_location2 WHERE adresse ILIKE '{$street_name}%'",__LINE__,__FILE__);
				while($this->db2->next_record())
				{
					$old_address = $this->db2->f('adresse',true);
					
					$address_info = explode(' ', $old_address);
					$a = count($address_info);
					
					if(!ctype_digit(substr($address_info[$a-1],0,1)) || strrpos($address_info[$a-1], '/')  || strrpos($address_info[$a-1], '(') ) // ikkje gatenummer eller ikke entydig eller parantes...
					{
						continue;
					}

					$street_number = $address_info[$a-1];

					$location[] = array
					(
						'location_code' 	=> $this->db2->f('location_code'),
						'old_address'		=> $old_address,
						'street_id'			=> $this->db->f('id'),
						'street_name'		=> $street_name,
						'street_number'		=> $street_number,
					);
				}
			}

			return $location;
		}

		function update_location($street_list)
		{
			$i = 1;
			$this->db->transaction_begin();
			foreach($street_list as $entry)
			{
				$this->db->query("UPDATE fm_location2 set street_id ='{$entry['street_id']}', street_number = '{$entry['street_number']}' WHERE location_code = '{$entry['location_code']}' AND street_id IS NULL",__LINE__,__FILE__);
				if( $this->db->affected_rows() )
				{
					$this->receipt['message'][]=array('msg'=> "#{$i}: Gateadresse {$entry['street_name']} {$entry['street_number']} oppdatert  for {$entry['location_code']}");
					$i++;
				}
			}
			$this->db->transaction_commit();
		}
	}

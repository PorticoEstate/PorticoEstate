<?php
	/**
	 * phpGroupWare custom fields
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @author Dave Hall dave.hall at skwashd.com
	 * @copyright Copyright (C) 2003-2006 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * Import the parent class
	 */
	phpgw::import_class('phpgwapi.custom_fields');

	/**
	 * Custom Fields
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class property_custom_fields extends phpgwapi_custom_fields
	{

		/**
		 * Constructor
		 *
		 * @param string $appname the name of the module using the custom fields
		 *
		 * @return void
		 */
		public function __construct($appname = null)
		{
			parent::__construct($appname);
			$this->_db2 = clone($this->_db);
			$this->contacts = CreateObject('phpgwapi.contacts');
		}

		/**
		 * Prepare custom attributes for ui
		 * 
		 * @param array $values    values and definitions of custom attributes
		 * @param ????  $appname   ????
		 * @param ????  $location  ????
		 * @param ????  $view_only ????
		 *
		 * @return array values and definitions of custom attributes prepared for ui
		 */
		public function prepare($values, $appname, $location, $view_only='')
		{
			$cache_custom_lookup = array();

			$vendor			= CreateObject('property.sogeneric');
			$vendor->get_location_info('vendor',false);

			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$input_type_array = array
				(
					'R'		=> 'radio',
					'CH'	=> 'checkbox',
					'LB'	=> 'listbox'
				);

			$m = 0;
			$i = 0;
			foreach ($values['attributes'] as &$attributes)
			{
				$attributes['datatype_text']	= $this->translate_datatype($attributes['datatype']);
				$attributes['help_url']			= $attributes['helpmsg'] ? $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'manual.uimanual.attrib_help', 'appname'=> $appname, 'location'=> $location, 'id' => $attributes['id'])): '';

				if(!$view_only && $attributes['history'])
				{
					$GLOBALS['phpgw']->jqcal->add_listener("values_attribute_{$i}_date");
				}

				if($attributes['datatype'] == 'D' || $attributes['datatype'] == 'DT' || $attributes['datatype'] == 'date' || $attributes['datatype'] == 'timestamp')
				{
					if(!$view_only)
					{
						$GLOBALS['phpgw']->jqcal->add_listener('values_attribute_' . $i);
						$attributes['lang_datetitle']	= lang('Select date');
					}


					if($attributes['datatype'] == 'D')
					{
							$clear_functions[$m]['name']	= "clear_{$attributes['name']}()";
							$confirm_msg = lang('delete') . '?';
							$clear_functions[$m]['action']	= <<<JS
							if(confirm("{$confirm_msg}"))
							{
								var attribute_{$i}_date = document.getElementById('values_attribute_{$i}');
								attribute_{$i}_date.value = '';
							}
JS;
							$m++;
					}
					else if($attributes['datatype'] == 'DT')
					{
							$clear_functions[$m]['name']	= "clear_{$attributes['name']}()";
							$confirm_msg = lang('delete') . '?';
							$clear_functions[$m]['action']	= <<<JS
							if(confirm("{$confirm_msg}"))
							{
								var attribute_{$i}_date = document.getElementById('values_attribute_{$i}');
								var attribute_{$i}_hour = document.getElementById('values_attribute_{$i}_hour');
								var attribute_{$i}_min = document.getElementById('values_attribute_{$i}_min');
								attribute_{$i}_date.value = '';
								attribute_{$i}_hour.value = '';
								attribute_{$i}_min.value = '';
							}
JS;
							$m++;
					}

					if(isset($attributes['value']) && $attributes['value'])
					{
						if($attributes['datatype'] == 'DT')
						{
							$timestamp= strtotime($attributes['value']);
							$attributes['value'] = array();
							$attributes['value']['date'] = $GLOBALS['phpgw']->common->show_date($timestamp,$dateformat);
							$attributes['value']['hour'] = date('H', $timestamp + phpgwapi_datetime::user_timezone());
							$attributes['value']['min'] = date('i', $timestamp + phpgwapi_datetime::user_timezone());

						}
						else
						{
							$timestamp_date= mktime(0,0,0,date('m',strtotime($attributes['value'])),date('d',strtotime($attributes['value'])),date('y',strtotime($attributes['value'])));
							$attributes['value']		= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
						}
					}
				}
				else if($attributes['datatype'] == 'AB')
				{
					if($attributes['value'])
					{
						$contact_data					= $this->contacts->read_single_entry($attributes['value'],array('fn','tel_work','email'));
						$attributes['contact_name']		= $contact_data[0]['fn'];
						$attributes['contact_email']	= $contact_data[0]['email'];
						$attributes['contact_tel']		= $contact_data[0]['tel_work'];
					}

					$insert_record_values[]			= $attributes['name'];
					$lookup_link					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column'=> $attributes['name']));

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';

					$clear_functions[$m]['name']	= "clear_{$attributes['name']}()";
					$confirm_msg = lang('delete') . '?';
					$clear_functions[$m]['action']	= <<<JS
					if(confirm("{$confirm_msg}"))
					{
						document.getElementsByName('{$attributes['name']}')[0].value = '';
						document.getElementsByName('{$attributes['name']}_name')[0].value = '';
					}
JS;
					$m++;
				}
				else if($attributes['datatype'] == 'ABO')
				{
					if($attributes['value'])
					{
						$contact_data				= $this->contacts->get_principal_organizations_data($attributes['value']);
						$attributes['org_name']		= $contact_data[0]['org_name'];

						$comms = $this->contacts->get_comm_contact_data($attributes['value'], $fields_comms='', $simple=false);

						$comm_data = array();
						if(is_array($comms))
						{
							foreach($comms as $key => $value)
							{
								$comm_data[$value['comm_contact_id']][$value['comm_description']] = $value['comm_data'];
							}
						}

						if ( count($comm_data) )
						{
							$attributes['org_email'] = isset($comm_data[$attributes['value']]['work email']) ? $comm_data[$attributes['value']]['work email'] : '';
							$attributes['org_tel'] = isset($comm_data[$attributes['value']]['work phone']) ?  $comm_data[$attributes['value']]['work phone'] : '';
						}
					}

					$insert_record_values[]			= $attributes['name'];
					$lookup_link					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.organisation', 'column'=> $attributes['name']));

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($attributes['datatype'] == 'VENDOR')
				{
					if($attributes['value'])
					{
						$vendor_data	= $vendor->read_single(array('id' => $attributes['value']),array('attributes' => array(0 => array('column_name' => 'org_name'))));

						for ($n=0;$n<count($vendor_data['attributes']);$n++)
						{
							if($vendor_data['attributes'][$n]['column_name'] == 'org_name')
							{
								$attributes['vendor_name']= $vendor_data['attributes'][$n]['value'];
								$n = count($vendor_data['attributes']);
							}
						}
					}

					$insert_record_values[]			= $attributes['name'];
					$lookup_link					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor', 'column'=> $attributes['name']));

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($attributes['datatype'] == 'custom1') // select
				{
					$attributes['choice'] = array();

					if($attributes['get_list_function'])
					{
						$_compare_key = $this->_get_compare_key($attributes['get_list_function'], $attributes['get_list_function_input']);
						if(isset($cache_custom_lookup[$_compare_key]))
						{
							$attributes['choice'] = $cache_custom_lookup[$_compare_key];
						}
						else
						{
							$attributes['choice'] = execMethod($attributes['get_list_function'], $attributes['get_list_function_input']);
							$cache_custom_lookup[$_compare_key] = $attributes['choice'];
						}
					}

					foreach ($attributes['choice'] as &$_choice)
					{
						$_choice['selected'] = $_choice['id'] == $attributes['value'] ? 1 : 0;
					}
				}
				else if($attributes['datatype'] == 'custom2') //lookup
				{
					if($attributes['value'] && $attributes['get_single_function'])
					{
						if(!$attributes['get_single_function_input'])
						{
							$attributes['get_single_function_input'] = $attributes['value'];
						}
						$attributes['custom_name'] = execMethod($attributes['get_single_function'], $attributes['get_single_function_input']);
					}

					$insert_record_values[]			= $attributes['name'];
					$lookup_link					= $GLOBALS['phpgw']->link('/index.php',array(
						'menuaction'			=> 'property.uilookup.custom',
						'column'				=> $attributes['name'],
						'get_list_function'		=> $attributes['get_list_function'],
						'get_list_function_input'	=> urlencode(serialize($attributes['get_list_function_input']))
					));

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($attributes['datatype'] == 'custom3') //autocomplete
				{
					if($attributes['value'] && $attributes['get_single_function'])
					{
						if(!$attributes['get_single_function_input'])
						{
							$attributes['get_single_function_input'] = $attributes['value'];
						}
						$attributes['custom_name'] = execMethod($attributes['get_single_function'], $attributes['get_single_function_input']);
					}

					$insert_record_values[]			= $attributes['name'];

					$_append_url = '';
					if(isset($attributes['get_list_function_input']) && is_array($attributes['get_list_function_input']))
					{
						$_append_url = '&' . http_build_query($attributes['get_list_function_input']);
					}

					$_autocomplete = <<<JS

					YAHOO.util.Event.addListener(window, "load", function()
					{
						var oArgs = {menuaction:'{$attributes['get_list_function']}'};
						var strURL = phpGWLink('index.php', oArgs, true);
						strURL += '{$_append_url}';

					    YAHOO.portico.autocompleteHelper(strURL, 
                               '{$attributes['name']}_name', '{$attributes['name']}_id', '{$attributes['name']}_container');

					});
JS;
					if(!$view_only)
					{
						$GLOBALS['phpgw']->js->add_code('', $_autocomplete);
					}

				}
				else if($attributes['datatype'] == 'user')
				{
					if($attributes['value'])
					{
						$attributes['user_name']= $GLOBALS['phpgw']->accounts->id2name($attributes['value']);
					}

					$insert_record_values[]			= $attributes['name'];
					$lookup_link					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $this->_appname.'.uilookup.phpgw_user', 'column'=> $attributes['name']));

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($attributes['datatype'] == 'R' || $attributes['datatype'] == 'CH' || $attributes['datatype'] == 'LB')
				{
					$input_type=$input_type_array[$attributes['datatype']];

					if($attributes['datatype'] == 'CH')
					{
//						$attributes['value'] = unserialize($attributes['value']);
						$attributes['value'] = explode(',', trim($attributes['value'], ','));

						if (isset($attributes['choice']) AND is_array($attributes['choice']))
						{
							foreach($attributes['choice'] as &$choice)
							{
								$choice['input_type'] = $input_type;
								if(isset($attributes['value']) && is_array($attributes['value']))
								{
									foreach ($attributes['value'] as &$selected)
									{
										if($selected == $choice['id'])
										{
											$choice['checked'] = 'checked';
										}
									}
								}
							}
						}
					}
					else
					{
						for ($j=0;$j<count($attributes['choice']);$j++)
						{
							$attributes['choice'][$j]['input_type'] = $input_type;
							if($attributes['choice'][$j]['id'] == $attributes['value'])
							{
								$attributes['choice'][$j]['checked'] = 'checked';
							}
						}
					}
				}
				else if($attributes['datatype'] == 'event')
				{
					// If the record is not saved - issue a warning
					if(isset($values['id']) || $values['id'])
					{
						$attributes['item_id'] = $values['id'];
					}
					else if(isset($values['location_code']) || $values['location_code'])
					{
						$attributes['item_id'] = execMethod('property.solocation.get_item_id', $values['location_code']);
					}
					else
					{
						$attributes['warning']			= lang('Warning: the record has to be saved in order to plan an event');
					}

					if(isset($attributes['value']) && $attributes['value'])
					{
						$event = execMethod('property.soevent.read_single', $attributes['value']);
						$attributes['descr']			= $event['descr'];
						$attributes['enabled']			= $event['enabled'] ? lang('yes') : lang('no');
						$attributes['lang_enabled']		= lang('enabled');

						$id = "property{$location}::{$values['id']}::{$attributes['id']}";
						$job = execMethod('phpgwapi.asyncservice.read', $id);

						$attributes['next']				= $GLOBALS['phpgw']->common->show_date($job[$id]['next'],$dateformat);
						$attributes['lang_next_run']	= lang('next run');
						unset($event);
						unset($id);
						unset($job);
					}

					$insert_record_values[]			= $attributes['name'];

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';

					$lookup_functions[$m]['action'] = "var oArgs = {menuaction:'{$this->_appname}.uievent.edit',"
						."location:'{$location}',"
						."attrib_id:'{$attributes['id']}'";
					$lookup_functions[$m]['action'] .=	isset($attributes['item_id']) && $attributes['item_id'] ? ",item_id:{$attributes['item_id']}" : '';
					$lookup_functions[$m]['action'] .=	isset($attributes['value']) && $attributes['value'] ? ",id:{$attributes['value']}" : '';
					$lookup_functions[$m]['action'] .= "};\n";
					$lookup_functions[$m]['action'] .= "if(document.form.{$attributes['name']}.value)\n";
					$lookup_functions[$m]['action'] .= "{\n";
					$lookup_functions[$m]['action'] .= "oArgs['id'] = document.form.{$attributes['name']}.value;";
					$lookup_functions[$m]['action'] .= "}\n";
					$lookup_functions[$m]['action'] .= "var strURL = phpGWLink('index.php', oArgs);\n";
					$lookup_functions[$m]['action']	.= 'Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=500,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if (isset($entity['attributes'][$i]) && $entity['attributes'][$i]['datatype']!='I' && $entity['attributes'][$i]['value'])
				{
					$entity['attributes'][$i]['value'] = stripslashes($entity['attributes'][$i]['value']);
				}

				$attributes['datatype_text']	= $this->translate_datatype($attributes['datatype']);
				$attributes['counter']			= $i;
				$i++;
			}

			if(isset($lookup_functions) && is_array($lookup_functions))
			{ 
				foreach ( $lookup_functions as $lookup_function)
				{
					$values['lookup_functions'] .= 'function ' . $lookup_function['name'] ."\r\n";
					$values['lookup_functions'] .= '{'."\r\n";
					$values['lookup_functions'] .= $lookup_function['action'] ."\r\n";
					$values['lookup_functions'] .= '}'."\r\n";
				}
			}

			if(isset($clear_functions) && $clear_functions)
			{ 
				foreach ($clear_functions as $clear_function)
				{
					$values['lookup_functions'] .= 'function ' . $clear_function['name'] ."\r\n";
					$values['lookup_functions'] .= '{'."\r\n";
					$values['lookup_functions'] .= $clear_function['action'] ."\r\n";
					$values['lookup_functions'] .= '}'."\r\n";
				}
			}
//_debug_array($values);die();
			if(isset($lookup_functions) && $lookup_functions)
			{
				$GLOBALS['phpgw']->session->appsession('insert_record_values' . $location,$appname,$insert_record_values);
			}

			return $values;
		}


		protected function _get_compare_key($get_list_function, $get_list_function_input)
		{
			$_compare_key = '';
			$_compare_key .= $get_list_function;
			if($get_list_function_input)
			{
				if (is_array($get_list_function_input))
				{
					foreach($get_list_function_input as $_key => $_value)
					{
						$_compare_key .= $_key;
						$_compare_key .= $_value;
					}
				}
				else
				{
						$_compare_key .= $get_list_function_input;
				}
			}
			return md5($_compare_key);
		}


		function prepare_for_db($table, $values_attribute, $id = 0)
		{
			$id = (int)$id;
			$data = array();
			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['disabled'])
					{
						continue;
					}

					switch ($entry['datatype'])
					{
						case 'C':
						case 'T':
						case 'V':
						case 'link':
						case 'email':
						case 'link':
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? $this->_db2->db_addslashes(phpgw::clean_value($entry['value'], 'string')) : '';
							$entry['value'] = $this->_db2->db_addslashes($entry['value']); // in case of history entries
							break;
						case 'LB':
						case 'R':
						case 'AB':
						case 'ABO':
						case 'AB':
						case 'ABO':
						case 'VENDOR':
						case 'event':
						case 'I':
						case 'custom3':
						case 'user':
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? phpgw::clean_value($entry['value'], 'int') : '';
							break;
						case 'bolean':
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? phpgw::clean_value($entry['value'], 'bool') : '';
							break;
						case 'N':
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? phpgw::clean_value($entry['value'], 'float') : '';
							break;
						case 'CH':
							$_value = ',' . implode(',', phpgw::clean_value($entry['value'])) . ',';
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? $_value : '';
						break;
						case 'D':
							$ts = phpgwapi_datetime::date_to_timestamp($entry['value']) - phpgwapi_datetime::user_timezone();
							$_value = date($this->_dateformat, $ts);
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? $_value : '';
							break;
						case 'DT':
							$date_array	= phpgwapi_datetime::date_array($attrib['value']['date']);
							$ts = mktime ((int)$attrib['value']['hour'], (int)$attrib['value']['min'], 0, $date_array['month'], $date_array['day'], $date_array['year']) - phpgwapi_datetime::user_timezone();
							$_value = date($this->_datetimeformat, $ts);
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? $_value : '';
							break;
						case 'pwd':
							if($entry['value'] && $entry['value2'])
							{
								if($entry['value'] || $entry['value2'])
								{
									if($entry['value'] == $entry['value2'])
									{
										$data['value_set'][$entry['name']]	= md5($entry['value']);
									}
									else
									{
										throw new Exception(lang('Passwords do not match!'));
									}
								}
							}
							else
							{
								$data['value_set'][$entry['name']]	= isset($entry['value'])?$entry['value']:'';
							}

							break;

						default:
							$data['value_set'][$entry['name']]	= isset($entry['value']) && $entry['value'] ? $this->_db2->db_addslashes(phpgw::clean_value($entry['value'], 'string')) : '';
					}


					if($entry['history'] == 1)
					{
						if($id)
						{
							$this->_db2->query("SELECT {$entry['name']} FROM $table WHERE id = {$id}",__LINE__,__FILE__);
							$this->_db2->next_record();
							$old_value = $this->_db2->f($entry['name']);
							if($entry['value'] != $old_value)
							{
								$data['history_set'][$entry['attrib_id']] = array
								('
									value'	=> $entry['value'],
									'date'	=> phpgwapi_datetime::date_to_timestamp($entry['date'])
								);
							}
						}
						else
						{
							$data['history_set'][$entry['attrib_id']] = $entry['value'];
						}
					}
				}
			}
			return $data;
		}

		public function get_translated_value($data, $location_id)
		{
			static $cache_lb = array();
			static $cache_contact = array();
			static $cache_vendor = array();
			static $cache_ch = array();
			static $cache_account = array();
			static $cache_custom_lookup = array();

			if(!$data['value'])
			{
				return $data['value'];
			}

			$ret = '';

			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";

			switch($data['datatype'])
			{
				case 'R':
				case 'LB':
					if($data['attrib_id'])
					{
						if(!isset($cache_lb[$location_id][$data['attrib_id']][$data['value']]) || $ret !=$cache_lb[$location_id][$data['attrib_id']][$data['value']])
						{
							$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .(int)$data['attrib_id']. "  AND id=" . (int)$data['value'];
							$this->_db2->query($sql);
							$this->_db2->next_record();
							$ret =  $this->_db2->f('value');
							$cache_lb[$location_id][$data['attrib_id']][$data['value']] = $ret;
						}
					}
					break;
				case 'AB':
					if(!isset($cache_contact[$data['value']]) || $ret!= $cache_contact[$data['value']])
					{
						$contact_data	= $this->contacts->read_single_entry($data['value'],array('fn'));
						$ret =  $contact_data[0]['fn'];
						$cache_contact[$data['value']] = $ret;
					}
					break;
				case 'ABO':
					if(!isset($cache_contact[$data['value']]) || $ret!= $cache_contact[$data['value']])
					{
						$contact_data	= $this->contacts->get_principal_organizations_data($data['value']);
						$ret = $contact_data[0]['org_name'];
						$cache_contact[$data['value']] = $ret;
					}
					break;
				case 'VENDOR':
					if(!isset($cache_vendor[$data['value']]) || $ret!= $cache_vendor[$data['value']])
					{
						$sql="SELECT org_name FROM fm_vendor where id=" . (int)$data['value'];
						$this->_db2->query($sql);
						$this->_db2->next_record();
						$ret =  $this->_db2->f('org_name',true);
						$cache_vendor[$data['value']] = $ret;
					}
					break;
				case 'CH':
					if($data['attrib_id'])
					{
						$ret = '';
						if(!isset($cache_ch[$location_id][$data['attrib_id']][$data['value']]) || $ret !=$cache_ch[$location_id][$data['attrib_id']][$data['value']])
						{
							$ch = explode(',', trim($data['value'], ','));
							if (isset($ch) AND is_array($ch))
							{
								for ($k=0;$k<count($ch);$k++)
								{
									$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id= " . (int)$data['attrib_id'] . ' AND id = ' . (int)$ch[$k];
									$this->_db2->query($sql);
									while ($this->_db2->next_record())
									{
									$ch_value[]=$this->_db2->f('value');
									}
								}
								$ret =  @implode(",", $ch_value);
								unset($ch_value);
							}
							$cache_ch[$location_id][$data['attrib_id']][$data['value']] = $ret;
						}
					}
					break;
				case 'D':
					$ret =  $GLOBALS['phpgw']->common->show_date(strtotime($data['value']), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					break;
				case 'DT':
					$ret =  $GLOBALS['phpgw']->common->show_date(strtotime($data['value']));
					break;
				case 'timestamp':
		//			$ret =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$data['value']);
					$ret =  $GLOBALS['phpgw']->common->show_date($data['value']);
					break;
				case 'link':
					$ret =  phpgw::safe_redirect($data['value']);
					break;
				case 'user':
					if(!isset($cache_vendor[$data['value']]) || $ret!= $cache_vendor[$data['value']])
					{
						$ret =   $GLOBALS['phpgw']->accounts->get($data['value'])->__toString();
						$cache_vendor[$data['value']] = $ret;
					}
					break;
				case 'pwd':
					$ret =   lang('yes');
					break;
				case 'custom1':

					$ret = '';
					if($data['value'] && $data['get_single_function'])
					{
						if(!$data['get_single_function_input'])
						{
							$data['get_single_function_input'] = $data['value'];
						}
						$_compare_key = $this->_get_compare_key($data['get_single_function'], $data['get_single_function_input']);
						if(!isset($cache_custom_lookup[$_compare_key]) || $ret != $cache_custom_lookup[$_compare_key])
						{
							$ret = execMethod($data['get_single_function'], $data['get_single_function_input']);
							$cache_custom_lookup[$_compare_key] = $ret;
						}
					}
					else if($data['value'] && $data['get_list_function'])
					{
						$_compare_key = $this->_get_compare_key($data['get_list_function'], $data['get_list_function_input']);
						if(!isset($cache_custom_lookup[$_compare_key]) || $_list != $cache_custom_lookup[$_compare_key])
						{
							$_list = execMethod($data['get_list_function'], $data['get_list_function_input']);
							$cache_custom_lookup[$_compare_key] = $_list;
						}

						if(isset($_list) && is_array($_list))
						{
							foreach ($_list as $_key => $_entry)
							{
								if ($_entry['id'] == $data['value'])
								{
									$ret = $_entry['name'];
									break;
								}
							}
						}
					}

					break;
				case 'custom2':
				case 'custom3':
					if($data['value'] && $data['get_single_function'])
					{
						if(!$data['get_single_function_input'])
						{
							$data['get_single_function_input'] = $data['value'];
						}
						$_compare_key = $this->_get_compare_key($data['get_single_function'], $data['get_single_function_input']);
						if(!isset($cache_custom_lookup[$_compare_key]) || $ret != $cache_custom_lookup[$_compare_key])
						{
							$ret = execMethod($data['get_single_function'], $data['get_single_function_input']);
							$cache_custom_lookup[$_compare_key] = $ret;
						}
					}

					break;
				default:
					if(is_array($data['value']))
					{
						$ret =  $data['value'];
					}
					else
					{
						$ret =  stripslashes($data['value']);
					}
			}
			return $ret;
		}

		function translate_value($values, $location_id, $location_count = 0)
		{
//			_debug_array($values);die();
			$location = array();
			$ret = array();
			$j=0;
			foreach ($values as $row)
			{
				foreach ($row as $field => $data)
				{
					if($field == 'location_code')
					{
						$location = explode('-',$data['value']);
					}

					$ret[$j][$field] = $this->get_translated_value($data, $location_id);

					if($location)
					{
						$_location_count = $location_count;
						if(!$_location_count)
						{
							$_location_count = count($location);
						}
						for ($m=0;$m < $_location_count ; $m++)
						{
							$ret[$j]['loc' . ($m+1)] = $location[$m];
							$ret[$j]['query_location']['loc' . ($m+1)]=implode('-', array_slice($location, 0, ($m + 1)));
						}
						$_location_count = 0;
					}
				}
				$j++;
			}
			return $ret;
		}
	}

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
	 * @version $Id: class.custom_fields.inc.php 1114 2008-06-02 18:15:22Z sigurd $
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
		 *
		 * @internal this is a UI related method - WTF was it doing in an API logic class?
		 * this is property specific code and so has been moved there!
		 * this code needs some serious attention
		 */
		public function prepare($values, $appname, $location, $view_only='')
		{
			$contacts		= CreateObject('phpgwapi.contacts');
			$vendor 		= CreateObject('property.soactor');
			$vendor->role	= 'vendor';
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
				if($attributes['datatype'] == 'D')
				{
					if(!$view_only)
					{
						if ( !isset($GLOBALS['phpgw']->jscal) || !is_object($GLOBALS['phpgw']->jscal) )
						{
							$GLOBALS['phpgw']->jscal = createObject('phpgwapi.jscalendar');
						}

						$GLOBALS['phpgw']->jscal->add_listener('values_attribute_' . $i);
						$attributes['img_cal']			= $GLOBALS['phpgw']->common->image('phpgwapi','cal');
						$attributes['lang_datetitle']	= lang('Select date');
					}

					if(isset($attributes['value']) && $attributes['value'])
					{
						$timestamp_date= mktime(0,0,0,date('m',strtotime($attributes['value'])),date('d',strtotime($attributes['value'])),date('y',strtotime($attributes['value'])));
						$attributes['value']		= $GLOBALS['phpgw']->common->show_date($timestamp_date,$dateformat);
					}
				}
				else if($attributes['datatype'] == 'AB')
				{
					if($attributes['value'])
					{
						$contact_data				= $contacts->read_single_entry($attributes['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$attributes['contact_name']	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}

					$insert_record_values[]			= $attributes['name'];
					$lookup_link					= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.addressbook', 'column'=> $attributes['name']));

					$lookup_functions[$m]['name']	= 'lookup_'. $attributes['name'] .'()';
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($attributes['datatype'] == 'VENDOR')
				{
					if($attributes['value'])
					{
						$vendor_data	= $vendor->read_single($attributes['value'],array('attributes' => array(0 => array('column_name' => 'org_name'))));

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
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
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
					$lookup_functions[$m]['action']	= 'Window1=window.open('."'" . $lookup_link ."'" .',"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");';
					$m++;
				}
				else if($attributes['datatype'] == 'R' || $attributes['datatype'] == 'CH' || $attributes['datatype'] == 'LB')
				{
					$input_type=$input_type_array[$attributes['datatype']];

					if($attributes['datatype'] == 'CH')
					{
						$attributes['value'] = unserialize($attributes['value']);

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
				for ($j=0;$j<count($lookup_functions);$j++)
				{
					$values['lookup_functions'] .= 'function ' . $lookup_functions[$j]['name'] ."\r\n";
					$values['lookup_functions'] .= '{'."\r\n";
					$values['lookup_functions'] .= $lookup_functions[$j]['action'] ."\r\n";
					$values['lookup_functions'] .= '}'."\r\n";
				}
			}

			if(isset($lookup_functions) && $lookup_functions)
			{
				$GLOBALS['phpgw']->session->appsession('insert_record_values' . $location,$appname,$insert_record_values);
			}

			return $values;
		}

		function prepare_for_db($table, $values_attribute, $id = 0)
		{	
			$id = (int)$id;
			$data = array();
			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR')
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->_db->db_addslashes($entry['value']);
						}

						if($entry['datatype'] == 'pwd' && $entry['value'] && $entry['value2'])
						{
							if($entry['value'] || $entry['value2'])
							{
								if($entry['value'] == $entry['value2'])
								{
									$data['value_set'][$entry['name']]	= md5($entry['value']);
								}
								else
								{
									$data['receipt']['error'][]=array('msg'=>lang('Passwords do not match!'));
								}
							}
						}
						else
						{
							$data['value_set'][$entry['name']]	= isset($entry['value'])?$entry['value']:'';
						}
					}

					if($entry['history'] == 1)
					{
						if($id)
						{
							$this->_db->query("SELECT {$entry['name']} FROM $table WHERE id = {$id}",__LINE__,__FILE__);
							$this->_db->next_record();
							$old_value = $this->_db->f($entry['name']);
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

		function translate_value($values, $location_id)
		{
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$attribute_filter = " location_id = {$location_id}";
			$contacts = CreateObject('phpgwapi.contacts');

			$j=0;
			foreach ($values as $row)
			{
				foreach ($row as $field => $data)
				{
//					$ret[$j][$field] = $this->custom->translate_value($entry, $location_id);

					if(($data['datatype']=='R' || $data['datatype']=='LB') && $data['value'])
					{
						$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$data['attrib_id']. "  AND id=" . $data['value'];
						$this->_db->query($sql);
						$this->_db->next_record();
						$ret[$j][$field] =  $this->_db->f('value');
					}
					else if($data['datatype']=='AB' && $data['value'])
					{
						$contact_data	= $contacts->read_single_entry($data['value'],array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$ret[$j][$field] =  $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}
					else if($data['datatype']=='VENDOR' && $data['value'])
					{
						$sql="SELECT org_name FROM fm_vendor where id={$data['value']}";
						$this->_db->query($sql);
						$this->_db->next_record();
						$ret[$j][$field] =  $this->_db->f('org_name');
					}
					else if($data['datatype']=='CH' && $data['value'])
					{
						$ch= unserialize($data['value']);
						if (isset($ch) AND is_array($ch))
						{
							for ($k=0;$k<count($ch);$k++)
							{
								$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id= {$data['attrib_id']} AND id=" . $ch[$k];
								$this->_db->query($sql);
								while ($this->_db->next_record())
								{
									$ch_value[]=$this->_db->f('value');
								}
							}
							$ret[$j][$field] =  @implode(",", $ch_value);
							unset($ch_value);
						}
					}
					else if($data['datatype']=='D' && $data['value'])
					{
						$ret[$j][$field] =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($data['value']));
					}
					else if($data['datatype']=='timestamp' && $data['value'])
					{
						$ret[$j][$field] =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$data['value']);
					}
					else if($data['datatype']=='link' && $data['value'])
					{
						$ret[$j][$field] =  phpgw::safe_redirect($data['value']);
					}
					else if($data['datatype']=='user_id' && $data['value'])
					{
						$ret[$j][$field] =   $GLOBALS['phpgw']->accounts->get($data['value'])->__toString();
					}
					else
					{
						$ret[$j][$field] =  $data['value'];
					}
				}
				$j++;
			}
			return $ret;
		}

	}

<?php
	/**
	* Contact Management Shared Routines
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Miles Lott <milosch@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) Copyright (C) 2001,2002 Joseph Engo, Miles Lott, Bettina Gille
	* @copyright Portions Copyright (C) 2001-2004 Free Software Foundation http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage contacts
	* @version $Id$
	*/

	/**
	* Contact Management Shared Routines
	*
	* @package phpgwapi
	* @subpackage contacts
	*/
	class contacts extends contacts_
	{
		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function split_stock_and_extras($fields)
		{
			while (list($field,$value) = @each($fields))
			{
				/* Depending on how the array was built, this is needed. */
				if (gettype($value) == 'integer')
				{
					$value = $field;
				}
				if ($this->stock_contact_fields[$field])
				{
					$stock_fields[$field]     = $value;
					$stock_fieldnames[$field] = $this->stock_contact_fields[$field];
				}
				else
				{
					$extra_fields[$field] = $value;
				}
			}
			return array($stock_fields,$stock_fieldnames,$extra_fields);
		}

		function loop_addslashes($fields)
		{
			$absf = $this->stock_contact_fields;
			while ($t = each($absf))
			{
				$ta[] = $this->db->db_addslashes($fields[$t[0]]);
			}
			reset($absf);
			return $ta;
		}

		/* This will take an array or integer */
// 		function delete($id)
// 		{
// 			if (gettype($id) == 'array')
// 			{
// 				while (list($null,$t_id) = each($id))
// 				{
// 					$this->delete_($t_id);
// 				}
// 			}
// 			else
// 			{
// 				$this->delete_($id);
// 			}
// 		}

		function asc_sort($a,$b)
		{
			echo "<br>A:'".$a."' B:'".$b;
			if($a[1]==$b[1]) return 0;
			return ($a[1]>$b[1])?1:-1;
		}

		function desc_sort($a,$b)
		{
			echo "<br>A:'".$a."' B:'".$b;
			if($a[1]==$b[1]) return 0;
			return ($a[1]<$b[1])?1:-1;
		}

		/**
		* Compare two strings by alphabetical order
		*
		* @param string $s1
		* @param string $s2
		* @return integer 1 if $s1 comes after $s2 alphabetically, 0 if not.
		*/
		function comesafter($s1, $s2)
		{
			/*
			We don't want to overstep the bounds of one of the strings and segfault,
			so let's see which one is shorter.
			*/
			$order = 1;

			if ( (strlen($s1) == 0) )
			{
				return 0;
			}

			if ( (strlen($s2) == 0) )
			{
				return 1;
			}

			if (strlen ($s1) > strlen ($s2))
			{
				$temp = $s1;
				$s1 = $s2;
				$s2 = $temp;
				$order = 0;
			}

			for ($index = 0; $index < strlen ($s1); $index++)
			{
				/* $s1 comes after $s2 */
				if (strtolower($s1[$index]) > strtolower($s2[$index])) { return ($order); }

				/* $s1 comes before $s2 */
				if (strtolower($s1[$index]) < strtolower($s2[$index])) { return (1 - $order); }
			}
				/* Special case in which $s1 is a substring of $s2 */

			return ($order);
		}

		/**
		* Sort a multi-dimensional array by a second-degree index.
		*
		* For instance, the 0th index of the Ith member of both the group and user arrays
		* is a string identifier. In the case of a user array this is the username;
		* with the group array it is the group name.
		* @param array $sortarray
		* @param integer|string $index
		* @return array
		*/
		function asortbyindex ($sortarray, $index)
		{
			$lastindex = count($sortarray) - 2;
			for ($subindex = 0; $subindex < $lastindex; $subindex++)
			{
				$lastiteration = $lastindex - $subindex;
				for ($iteration = 0; $iteration < $lastiteration; $iteration++)
				{
					if ($this->comesafter($sortarray[$iteration][$index], $sortarray[$iteration + 1][$index]))
					{
						$temp = $sortarray[$iteration];
						$sortarray[$iteration] = $sortarray[$iteration + 1];
						$sortarray[$iteration + 1] = $temp;
					}
				}
			}
			return ($sortarray);
		}

		function arsortbyindex ($sortarray, $index)
		{
			$lastindex = count($sortarray) - 1;
			for ($subindex = $lastindex; $subindex > 0; $subindex--)
			{
				$lastiteration = $lastindex - $subindex;
				for ($iteration = $lastiteration; $iteration > 0; $iteration--)
				{
					if ($this->comesafter($sortarray[$iteration][$index], $sortarray[$iteration - 1][$index]))
					{
						$temp = $sortarray[$iteration];
						$sortarray[$iteration] = $sortarray[$iteration - 1];
						$sortarray[$iteration - 1] = $temp;
					}
				}
			}
			return ($sortarray);
		}

		/**
		* Eliminate fields that are not found within $filterfields from LDAP fields
		*
		* @param array $ldap_fields Array with LDAP fields to filter
		* @param array $filterfields LDAP fields to filter
		* @param boolean $DEBUG true to switch debug mode on, defaults to false
		* @return array New LDAP field array incuding only fields from $filterfields
		* @deprecated
		*/
		function filter_ldap($ldap_fields,$filterfields,$DEBUG=0)
		{
			$match = 0;
			if($DEBUG) { echo '<br>'; }
			for($i=0;$i<count($ldap_fields);$i++)
			{
				$yes = True;

				if ($ldap_fields[$i]['uidnumber'][0])
				{
					reset($filterfields);
					while (list($col,$filt) = each($filterfields))
					{
						if ($col == 'phpgwcontactcatid')
						{
							$colarray = explode(',',$ldap_fields[$i][$col][0]);
							if ($colarray[1])
							{
								while(list($key,$val) = each ($colarray))
								{
									if($DEBUG) { echo '&nbsp;&nbsp;Testing "'.$col.'" for "'.$val.'"'; }
									if ($val == $filt)
									{
										if($DEBUG) { echo ', and number '.$ldap_fields[$i]['uidnumber'][0].' matched.'.'&nbsp;&nbsp;'; }
										$yes &= True;
										$match++;
										break;
									}
								}
							}
							else
							{
								if($DEBUG) { echo '&nbsp;&nbsp;Testing "'.$col.'" for "'.$filt.'"'; }
								if ($ldap_fields[$i][$col][0] == $filt)
								{
									if($DEBUG) { echo ', and number '.$ldap_fields[$i]['uidnumber'][0].' matched.'.'&nbsp;&nbsp;'; }
									$yes &= True;
									$match++;
								}
								else
								{
									if($DEBUG) { echo ', but number '.$ldap_fields[$i]['uidnumber'][0].' did not match.'.'&nbsp;&nbsp;'; }
									$yes &= False;
									$match--;
								}
							}
						}
						else
						{
							if($DEBUG) { echo '&nbsp;&nbsp;Testing "'.$col.'" for "'.$filt.'"'; }
							if ($ldap_fields[$i][$col][0] == $filt)
							{
								if($DEBUG) { echo ', and number '.$ldap_fields[$i]['uidnumber'][0].' matched.'.'&nbsp;&nbsp;'; }
								$yes &= True;
								$match++;
							}
							else
							{
								if($DEBUG) { echo ', but number '.$ldap_fields[$i]['uidnumber'][0].' did not match.'.'&nbsp;&nbsp;'; }
								$yes &= False;
								$match--;
							}
						}
					}

					if ($yes)
					{
						if($DEBUG) { echo $ldap_fields[$i]['uidnumber'][0].' matched all!'.'<br>'; }
						$new_ldap[] = $ldap_fields[$i];
					}
					else
					{
						if($DEBUG) { echo $ldap_fields[$i]['uidnumber'][0].' did not match all.'.'<br>'; }
					}
				}
			}
			if($DEBUG)
			{
				if($match)
				{
					echo '<br>'.$match.' total matches.'."\n";
				}
				else
				{
					echo '<br>No matches :('."\n";
				}
			}
			$this->total_records = count($new_ldap);

			return $new_ldap;
		}

		function formatted_address($id, $business = True, $afont = '', $asize = '2')
		{
			$t = createObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('addressbook'));
			$s = createObject('phpgwapi.sbox');

			$fields = array
			(
				'n_given'				=> 'n_given',
				'n_family'				=> 'n_family',
				'title'					=> 'title',
				'org_name'				=> 'org_name',
				'org_unit'				=> 'org_unit',
				'adr_one_street'		=> 'adr_one_street',
				'adr_one_locality'		=> 'adr_one_locality',
				'adr_one_postalcode'	=> 'adr_one_postalcode',
				'adr_one_region'		=> 'adr_one_region',
				'adr_one_countryname'	=> 'adr_one_countryname',
				'adr_two_street'		=> 'adr_two_street',
				'adr_two_locality'		=> 'adr_two_locality',
				'adr_two_postalcode'	=> 'adr_two_postalcode',
				'adr_two_region'		=> 'adr_two_region',
				'adr_two_countryname'	=> 'adr_two_countryname'
			);

			list($address) = $this->read_single_entry($id,$fields);
			foreach($address as $k => $val)
			{
				$address[$k] = $GLOBALS['phpgw']->strip_html($val);
			}

			if ($address['title'])
			{
				$title = $address['title'] . '&nbsp;';
			}

			if ($business)
			{
				if ($address['org_name'])
				{
					$company = $address['org_name'];
				}
				else
				{
					$company = $title . $address['n_given'] . '&nbsp;' . $address['n_family'];
				}

				$street  = $address['adr_one_street'];
				$city    = $address['adr_one_locality'];
				$zip     = $address['adr_one_postalcode'];
				$state   = $address['adr_one_region'];
				$country = $address['adr_one_countryname'];
			}
			else
			{
				$company = $title . $address['n_given'] . '&nbsp;' . $address['n_family'];
				$street  = $address['adr_two_street'];
				$city    = $address['adr_two_locality'];
				$zip     = $address['adr_two_postalcode'];
				$state   = $address['adr_two_region'];
				$country = $address['adr_two_countryname'];
			}

			if (! $country)
			{
				$country = $GLOBALS['phpgw_info']['user']['preferences']['common']['country'];
			}

			if (file_exists(PHPGW_SERVER_ROOT . SEP . 'addressbook' . SEP . 'templates' . SEP .'default' . SEP . 'format_' . strtolower($country) . '.tpl'))
			{
				$a = $t->set_file(array('address_format' => 'format_' . strtolower($country) . '.tpl'));
			}
			else
			{
				$a = $t->set_file(array('address_format' => 'format_us.tpl'));
			}

			if (!$afont)
			{
				$afont = $GLOBALS['phpgw_info']['theme']['font'];
			}

			$a .= $t->set_var('font',$afont);
			$a .= $t->set_var('fontsize',$asize);
			$a .= $t->set_var('company',$company);
			$a .= $t->set_var('department',$address['org_unit']);
			$a .= $t->set_var('street',$street);
			$a .= $t->set_var('city',$city);
			$a .= $t->set_var('zip',$zip);
			$a .= $t->set_var('state',$state);

			if ($country != $GLOBALS['phpgw_info']['user']['preferences']['common']['country'])
			{
				$countryname = $s->get_full_name($country);
				$a .= $t->set_var('country',lang($countryname));
			}

			$a .= $t->fp('out','address_format');
			return $a;
		}

		function formatted_address_full($id, $business = True, $afont = '', $asize = '2')
		{
			$t = createObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('addressbook'));
			$s = createObject('phpgwapi.sbox');

			$fields = array
			(
				'n_given'				=> 'n_given',
				'n_family'				=> 'n_family',
				'title'					=> 'title',
				'org_name'				=> 'org_name',
				'org_unit'				=> 'org_unit',
				'adr_one_street'		=> 'adr_one_street',
				'adr_one_locality'		=> 'adr_one_locality',
				'adr_one_postalcode'	=> 'adr_one_postalcode',
				'adr_one_region'		=> 'adr_one_region',
				'tel_work'				=> 'tel_work',
				'tel_fax'				=> 'tel_fax',
				'email'					=> 'email',
				'url'					=> 'url',
				'adr_one_countryname'	=> 'adr_one_countryname',
				'adr_two_street'		=> 'adr_two_street',
				'adr_two_locality'		=> 'adr_two_locality',
				'adr_two_postalcode'	=> 'adr_two_postalcode',
				'adr_two_region'		=> 'adr_two_region',
				'adr_two_countryname'	=> 'adr_two_countryname',
				'tel_home'				=> 'tel_home',
				'email_home'			=> 'email_home'
			);

			list($address) = $this->read_single_entry($id,$fields);
			foreach($address as $k => $val)
			{
				$address[$k] = $GLOBALS['phpgw']->strip_html($val);
			}

			if ($address['title'])
			{
				$title = $address['title'] . '&nbsp;';
			}

			if ($business)
			{
				if ($address['org_name'])
				{
					$company = $address['org_name'];
				}
				else
				{
					$company = $title . $address['n_given'] . '&nbsp;' . $address['n_family'];
				}

				$street		= $address['adr_one_street'];
				$city		= $address['adr_one_locality'];
				$zip		= $address['adr_one_postalcode'];
				$state		= $address['adr_one_region'];
				$country	= $address['adr_one_countryname'];
				$tel		= $address['tel_work'];
				$email		= $address['email'];
			}
			else
			{
				$company	= $title . $address['n_given'] . '&nbsp;' . $address['n_family'];
				$street		= $address['adr_two_street'];
				$city		= $address['adr_two_locality'];
				$zip		= $address['adr_two_postalcode'];
				$state		= $address['adr_two_region'];
				$country	= $address['adr_two_countryname'];
				$tel		= $address['tel_home'];
				$email		= $address['email_home'];
			}

			if (! $country)
			{
				$country = $GLOBALS['phpgw_info']['user']['preferences']['common']['country'];
			}

			if (file_exists(PHPGW_SERVER_ROOT . SEP . 'addressbook' . SEP . 'templates' . SEP .'default' . SEP . 'full_format_' . strtolower($country) . '.tpl'))
			{
				$a = $t->set_file(array('address_format' => 'full_format_' . strtolower($country) . '.tpl'));
			}
			else
			{
				$a = $t->set_file(array('address_format' => 'full_format_us.tpl'));
			}

			if (!$afont)
			{
				$afont = $GLOBALS['phpgw_info']['theme']['font'];
			}

			$a .= $t->set_var('font',$afont);
			$a .= $t->set_var('fontsize',$asize);
			$a .= $t->set_var('lang_url',lang('url'));
			$a .= $t->set_var('lang_email',lang('email'));
			$a .= $t->set_var('lang_fax',lang('fax number'));
			$a .= $t->set_var('lang_fon',lang('phone number'));
			$a .= $t->set_var('company',$company);
			$a .= $t->set_var('department',$address['org_unit']);
			$a .= $t->set_var('street',$street);
			$a .= $t->set_var('city',$city);
			$a .= $t->set_var('zip',$zip);
			$a .= $t->set_var('state',$state);
			$a .= $t->set_var('email',$email);
			$a .= $t->set_var('tel',$tel);
			$a .= $t->set_var('fax',$address['tel_fax']);
			$a .= $t->set_var('url',$address['url']);

			if ($country != $GLOBALS['phpgw_info']['user']['preferences']['common']['country'])
			{
				$countryname = $s->get_full_name($country);
				$a .= $t->set_var('country',lang($countryname));
			}

			$a .= $t->fp('out','address_format');
			return $a;
		}

		function formatted_address_line($id, $business = True, $afont = '', $asize = '2')
		{
			$t = createObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('addressbook'));
			$s = createObject('phpgwapi.sbox');

			$fields = array
			(
				'n_given'				=> 'n_given',
				'n_family'				=> 'n_family',
				'title'					=> 'title',
				'org_name'				=> 'org_name',
				'adr_one_street'		=> 'adr_one_street',
				'adr_one_locality'		=> 'adr_one_locality',
				'adr_one_postalcode'	=> 'adr_one_postalcode',
				'adr_one_region'		=> 'adr_one_region',
				'adr_one_countryname'	=> 'adr_one_countryname',
				'adr_two_street'		=> 'adr_two_street',
				'adr_two_locality'		=> 'adr_two_locality',
				'adr_two_postalcode'	=> 'adr_two_postalcode',
				'adr_two_region'		=> 'adr_two_region',
				'adr_two_countryname'	=> 'adr_two_countryname'
			);

			list($address) = $this->read_single_entry($id,$fields);
			foreach($address as $k => $val)
			{
				$address[$k] = $GLOBALS['phpgw']->strip_html($val);
			}

			if ($address['title'])
			{
				$title = $address['title'] . '&nbsp;';
			}

			if ($business)
			{
				if ($address['org_name'])
				{
					$company = $address['org_name'];
				}
				else
				{
					$company = $title . $address['n_given'] . '&nbsp;' . $address['n_family'];
				}

				$street  = $address['adr_one_street'];
				$city    = $address['adr_one_locality'];
				$zip     = $address['adr_one_postalcode'];
				$state   = $address['adr_one_region'];
				$country = $address['adr_one_countryname'];
			}
			else
			{
				$company = $title . $address['n_given'] . '&nbsp;' . $address['n_family'];
				$street  = $address['adr_two_street'];
				$city    = $address['adr_two_locality'];
				$zip     = $address['adr_two_postalcode'];
				$state   = $address['adr_two_region'];
				$country = $address['adr_two_countryname'];
			}

			if (! $country)
			{
				$country = $GLOBALS['phpgw_info']['user']['preferences']['common']['country'];
			}

			if (file_exists(PHPGW_SERVER_ROOT . SEP . 'addressbook' . SEP . 'templates' . SEP .'default' . SEP . 'line_format_' . strtolower($country) . '.tpl'))
			{
				$a = $t->set_file(array('address_format' => 'line_format_' . strtolower($country) . '.tpl'));
			}
			else
			{
				$a = $t->set_file(array('address_format' => 'line_format_us.tpl'));
			}

			if (!$afont)
			{
				$afont = $GLOBALS['phpgw_info']['theme']['font'];
			}

			$a .= $t->set_var('font',$afont);
			$a .= $t->set_var('fontsize',$asize);
			$a .= $t->set_var('company',$company);
			$a .= $t->set_var('street',$street);
			$a .= $t->set_var('city',$city);
			$a .= $t->set_var('zip',$zip);
			$a .= $t->set_var('state',$state);

			if ($country != $GLOBALS['phpgw_info']['user']['preferences']['common']['country'])
			{
				$countryname = $s->get_full_name($country);
				$a .= $t->set_var('country','&nbsp;&nbsp;' . lang($countryname));
			}

			$a .= $t->fp('out','address_format');
			return $a;
		}
	}

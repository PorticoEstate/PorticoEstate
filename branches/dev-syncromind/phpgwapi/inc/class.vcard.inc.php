<?php
	/**
	* Parse vcards->contacts class fields, and vice-versa
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 2001 Miles Lott
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage network
	* @version $Id$
	*/

	/**
	* Parse vcards->contacts class fields, and vice-versa
	* 
	* @package phpgwapi
	* @subpackage network
	*/
	class vcard
	{
		var $import = array(
			'n'        => 'n',
			'sound'    => 'sound',
			'bday'     => 'bday',
			'note'     => 'note',
			'tz'       => 'tz',
			'geo'      => 'geo',
			'url'      => 'url',
			'pubkey'   => 'pubkey',
			'org'      => 'org',
			'title'    => 'title',
			'adr'      => 'adr',
			'label'    => 'label',
			'tel'      => 'tel',
			'email'    => 'email',
			'org'      => 'org'
		);

		var $export = array(
			'full_name'           => 'FN',
			'first_name'          => 'N;GIVEN',
			'last_name'           => 'N;FAMILY',
			'middle_name'         => 'N;MIDDLE',
			'prefix'              => 'N;PREFIX',
			'suffix'              => 'N;SUFFIX',
			'sound'               => 'SOUND',
			'birthday'            => 'BDAY',
			'note'                => 'NOTE',
			'tz'                  => 'TZ',
			'geo'                 => 'GEO',
			'url'                 => 'URL',
			'pubkey'              => 'PUBKEY',
			'org_name'            => 'ORG;NAME',
			'org_unit'            => 'ORG;UNIT',
			'title'               => 'TITLE',

			'adr_one_type'        => 'ADR;TYPE;WORK',
			'adr_two_type'        => 'ADR;TYPE;HOME',
			'tel_prefer'          => 'TEL;PREFER',
			'email_type'          => 'EMAIL;TYPE;WORK',
			'email_home_type'     => 'EMAIL;TYPE;HOME',

			'adr_one_street'      => 'ADR;WORK;STREET',
			'adr_one_ext'         => 'ADR;WORK;EXT',
			'adr_one_locality'    => 'ADR;WORK;LOCALITY', 
			'adr_one_region'      => 'ADR;WORK;REGION', 
			'adr_one_postalcode'  => 'ADR;WORK;POSTALCODE',
			'adr_one_countryname' => 'ADR;WORK;COUNTRYNAME',
			'label'               => 'LABEL',

			'adr_two_street'      => 'ADR;HOME;STREET',
			'adr_two_ext'         => 'ADR;HOME;EXT',
			'adr_two_locality'    => 'ADR;HOME;LOCALITY',
			'adr_two_region'      => 'ADR;HOME;REGION',
			'adr_two_postalcode'  => 'ADR;HOME;POSTALCODE',
			'adr_two_countryname' => 'ADR;HOME;COUNTRYNAME',

			'tel_work'            => 'TEL;WORK;VOICE',
			'tel_home'            => 'TEL;HOME;VOICE',
			'tel_voice'           => 'TEL;VOICE',
			'tel_work_fax'        => 'TEL;WORK;FAX',
			'tel_home_fax'        => 'TEL;HOME;FAX',
			'tel_fax'             => 'TEL;FAX',
			'tel_msg'             => 'TEL;MSG',
			'tel_cell'            => 'TEL;CELL',
			'tel_pager'           => 'TEL;PAGER',
			'tel_bbs'             => 'TEL;BBS',
			'tel_modem'           => 'TEL;MODEM',
			'tel_car'             => 'TEL;CAR',
			'tel_isdn'            => 'TEL;ISDN',
			'tel_video'           => 'TEL;VIDEO',
			'email'               => 'EMAIL;WORK',
			'email_home'          => 'EMAIL;HOME'
		);

		var $names = array(
			'last_name'  => 'last_name',
			'first_name' => 'first_name',
			'middle_name' => 'middle_name',
			'prefix' => 'prefix',
			'suffix' => 'suffix'
		);

		var $address = array(
			'POSTOFFICEBOX' => '',
			'EXT'           => '',
			'STREET'        => '',
			'LOCALITY'      => '',
			'REGION'        => '',
			'POSTALCODE'    => '',
			'COUNTRYNAME'   => ''
		);

		var $vCard_Version;
		
		function vcard()
		{
			$this->vCard_Version = '2.1';	
			/* _debug_array($this); */
		}

		/*
			This now takes the upload filename
			and parses using the class string and array processors.
			The return is a contacts class entry, ready for add.
		*/
		function in_file($filename='')
		{
			if (!$filename)
			{
				return array();
			}

			$buffer = array();
			
			$fp = fopen($filename,'r');
			while ($data = fgets($fp,8000))
			{
				$data = trim($data);
				while (substr($data,-1) == '=')
				{
					// '=' at end-of-line --> line to be continued with next line
					$data = substr($data,0,-1) . trim(fgets($fp,8000));
				}
				$buffer += $this->parse_vcard_line($data);
			}
			fclose($fp);

			$entry = $this->in($buffer);

			/* _debug_array($entry);exit; */

			return $entry;
		}

		/*
			This is here to match the old in() function, now called _parse_in().
			It is called now also by in_file() above.
			It takes a pre-parsed file using the methods in in_file(), returns a clean contacts class array.
		*/
		function in($buffer)
		{
			$buffer = $this->_parse_in($buffer);
			return $buffer;			
		}

		/*
			Pass this an associative array of fieldnames and values
			returns a clean array based on contacts class std fields
			This array can then be passed via $GLOBALS['phpgw']->contacts->add($ownerid,$buffer)
		*/
		function _parse_in($buffer)
		{
			/* Following is a lot of pain and little magic */
			while ( list($name,$value) = @each($buffer) )
			{
				$field  = split(';',$name);

				while (list($key,$val) = each($field))
				{
					$field[$key] = strtoupper($val);
				}

				$field[0] = str_replace("A\.",'',$field[0]);
				$field[0] = str_replace("B\.",'',$field[0]);
				$field[0] = str_replace("C\.",'',$field[0]);
				$field[0] = str_replace("D\.",'',$field[0]);
				$values = split(';',$value);
				switch ($field[0])
				{
				case 'N':
					reset($this->names);
					$j=0;
					while(list($myname,$myval) = each($this->names) )
					{
						//$namel = 'per_' . $myname;
						$namel = $myname;
						if (isset($values[$j]))
						{
							$entry[$namel] = $values[$j];
						}
						else
						{
							$entry[$namel] = '';
						}
						$j++;
					}
					break;
				case 'FN':
					$fn = split(" ", $values[0], 3);
					switch (count($fn))
					{
						case 1:
							// check if last name was always set, if true don't override it!
							if ((isset($entry['last_name']) == false) || ($entry['last_name'] == ''))
								$entry['last_name'] = $fn[0];
						break;
						case 2:
							// check if first or last name was always set, if true don't override it!
							if ((isset($entry['first_name']) == false) || ($entry['first_name'] == ''))
								$entry['first_name'] = $fn[0];
							if ((isset($entry['last_name']) == false) || ($entry['last_name'] == ''))
								$entry['last_name'] = $fn[1];
						break;
						case 3:
							// check if first, middle or last name was always set, if true don't override it!
							if ((isset($entry['first_name']) == false) || ($entry['first_name'] == ''))
								$entry['first_name'] = $fn[0];
							if ((isset($entry['middle_name']) == false) || ($entry['middle_name'] == ''))
								$entry['middle_name'] = $fn[1];
							if ((isset($entry['last_name']) == false) || ($entry['last_name'] == ''))
								$entry['last_name'] = $fn[2];
						break;
					}
					break;
				case 'TITLE':
					$entry['title'] = $values[0];
					break;
				case 'TZ':
					//$entry['tz'] = $values[0];
					break;
				case 'GEO':
					//$entry['geo'] = $values[0];
					break;
				case 'URL':
					//$entry['url'] = $values[0];
					$entry['comm_media']['website'] = $values[0];
					break;
				case 'NOTE':
					//$entry['note'] = str_replace('=0D=0A',"\n",$values[0]);
					$entry['notes']['type'] = 'vcard';
					$entry['notes']['note'] = str_replace('=0D=0A',"\n",$values[0]);
					break;
				case 'KEY':
					$entry['key'] = str_replace('=0D=0A',"\n",$values[0]);
					break;
				case 'LABEL':
					$entry['label'] = str_replace('=0D=0A',"\n",$values[0]);
					break;
				case 'BDAY': #1969-12-31
					// use ISO 8601

					// for Outlook vCards: bday without '-' separator -> convert to extended ISO 8601
					if (substr($values[0], 4, 1) != '-')
					{
						$year  = substr($values[0], 0, 4);
						$month = substr($values[0], 4, 2);
						$day   = substr($values[0], 6, 2);
						$values[0] = $year.'-'.$month.'-'.$day;
					}

					$entry['birthday'] = $values[0];
					break;
				case 'ORG':	// RB 2001/05/07 added for Lotus Organizer: ORG:Company;Department
					$entry['preferred_org'] = $values[0];
					$entry['department'] = $values[1];
					break;
				case 'ADR':
					$field[1] = str_replace("TYPE=",'',$field[1]);
					switch ($field[1])
					{
					case 'INTL':
						$location_level = 'int';
						switch ($field[2])
						{
						case 'WORK':
							$location_type = 'work';
							break;
						case 'HOME':
							$location_type = 'home';
							break;
						default:
							break;
						}
						break;
					case 'DOM':
						$location_level = 'dom';
						switch ($field[2])
						{
						case 'WORK':
							$location_type = 'work';
							break;
						case 'HOME':
							$location_type = 'home';
							break;
						default:
							break;
						}
						break;
					case 'PARCEL':
						$location_level = 'parcel';
						switch ($field[2])
						{
						case 'WORK':
							$location_type = 'work';
							break;
						case 'HOME':
							$location_type = 'home';
							break;
						default:
							break;
						}
						break;
					case 'POSTAL':
						$location_level = 'postal';
						switch ($field[2])
						{
						case 'WORK':
							$location_type = 'work';
							break;
						case 'HOME':
							$location_type = 'home';
							break;
						default:
							break;
						}
						break;
					case 'WORK':
						$location_type = 'work';
						break;
					case 'HOME':
						$location_type = 'home';
						break;
					default:
						$location_type = 'work';
						break;
					}
					$loc = $location_level?$location_level.'_'.$location_type:$location_type;
					// remember the correct order of address fields!
					// 'POSTOFFICEBOX', 'EXT', 'STREET', 'LOCALITY', 'REGION', 'POSTALCODE', 'COUNTRYNAME'
					$entry['locations'][$loc]['type'] = $location_type;
					$entry['locations'][$loc]['add1'] = $values[2];
					$entry['locations'][$loc]['add2'] = $values[1];
					$entry['locations'][$loc]['city'] = $values[3];
					$entry['locations'][$loc]['state'] = $values[4];
					$entry['locations'][$loc]['postal_code'] = $values[5];
					$entry['locations'][$loc]['country'] = $values[6];
					break;
				case 'TEL':
					// RB 2001/05/07 added for Lotus Organizer ueses TEL;{WORK|HOME};{VOICE|FAX}[;PREF]
					if ($field[2] == 'FAX' && ($field[1] == 'WORK' || $field[1] == 'HOME'))
					{
						/* TODO This is PHP4 only */
						array_shift($field);	// --> ignore the WORK or HOME if FAX follows, HOME;FAX and HOME;TEL are maped later
					}
					switch ($field[1])
					{
					case 'WORK':
						// RB don't overwrite TEL;WORK;VOICE (main nr.) with TEL;WORK, TEL;WORK --> tel_isdn
						//$entry[$buffer['tel_work'] ? 'tel_isdn' : 'tel_work'] = $values[0];
						if (isset($entry['comm_media']['work phone']) == false)
							$entry['comm_media']['work phone'] = $values[0];
						else
							$entry['comm_media']['work phone 2'] = $values[0];
						break;
					case 'HOME':
						// RB don't overwrite TEL;HOME;VOICE (main nr.) with TEL;HOME, TEL;HOME --> ophone
						//$entry[$buffer['tel_home'] ? 'ophone' : 'tel_home' ] = $values[0];
						if (isset($entry['comm_media']['home phone']) == false)
							$entry['comm_media']['home phone'] = $values[0];
						else
							$entry['comm_media']['home phone 2'] = $values[0];
						break;
					case 'VOICE':
						if (isset($entry['comm_media']['voice phone']) == false)
							$entry['comm_media']['voice phone'] = $values[0];
						break;
					case 'FAX':
						switch ($field[0])
						{
						case 'WORK':
							if(!$entry['comm_media']['work fax'])
							{
								$entry['comm_media']['work fax'] = $values[0];
							}
							else
							{
								$entry['comm_media']['other work fax'] = $values[0];
							}
							break;
						case 'HOME':
							$entry['comm_media']['home fax'] = $values[0];
							break;
						default:
							$entry['comm_media']['work fax'] = $values[0];
						}
						break;
					case 'MSG':
						$entry['comm_media']['msg phone'] = $values[0];
						break;
					case 'CELL':
						$entry['comm_media']['mobile (cell) phone'] = $values[0];
						break;
					case 'PAGER':
						$entry['comm_media']['pager'] = $values[0];
						break;
					case 'BBS':
						$entry['comm_media']['bbs'] = $values[0];
						break;
					case 'MODEM':
						$entry['comm_media']['modem'] = $values[0];
						break;
					case 'CAR':
						$entry['comm_media']['car phone'] = $values[0];
						break;
					case 'ISDN':
						$entry['comm_media']['isdn'] = $values[0];
						break;
					case 'VIDEO':
						$entry['comm_media']['video'] = $values[0];
						break;
					case 'PREF':
						//echo $field[2].' is preferred';
						if ($field[2])
						{
							//$buffer['tel_prefer'] .= strtolower($field[2]) . ';';
							$entry['comm_media_preferred'] .= strtolower($field[2]) . ';';
						}
						break;
					default:
						break;
					}
					if ($field[2] == 'PREF')
					{
						$entry['comm_media_preferred'] .= strtolower($field[1]) . ';';
					}
					break;
				case 'EMAIL':
					switch ($field[1])
					{
					case 'WORK':
						if (isset($entry['comm_media']['work email']) == false)
							$entry['comm_media']['work email'] = $values[0];
						break;
					case 'HOME':
						if (isset($entry['comm_media']['home email']) == false)
							$entry['comm_media']['home email'] = $values[0];
						break;
					default:
						if($buffer['email'])
						{
							if (isset($entry['comm_media']['work email']) == false)
								$entry['comm_media']['work email'] = $values[2];
							elseif (isset($entry['comm_media']['home email']) == false) // workaround insert a second email into home_email if this isn't in used
								$entry['comm_media']['home email'] = $values[2];
						}
						elseif (!$buffer['email'])
						{
							if (isset($entry['comm_media']['work email']) == false)
								$entry['comm_media']['work email'] = $values[0];
							elseif (isset($entry['comm_media']['home email']) == false) // workaround insert a second email into home_email if this isn't in used
								$entry['comm_media']['home email'] = $values[0];
						}
						break;
					}
					break;
				default:
					break;
				}
			}

			if (count($street = split("\r*\n",$buffer['adr_one_street'],3)) > 1)
			{
				$entry['adr_one_street'] = $street[0];			// RB 2001/05/08 added for Lotus Organizer to split multiline adresses
				$entry['address2']		  = $street[1];
				$entry['address3']		  = $street[2];
			}
			return $entry;
		}

		// Takes an array of contacts class fields/values, turns it into a vcard string:
		//
		// for ($i=0;$i<count($buffer);$i++) {
		//     $vcards .= $this->vcard->out($buffer[$i]);
		// }
		//
		function out($buffer)
		{
			$entry   = '';
			$header  = 'BEGIN:VCARD' . "\r\n";
			$header .= 'VERSION:2.1' . "\r\n";
			$header .= 'X-PHPGROUPWARE-FILE-AS:phpGroupWare.org' . "\r\n";
			
			$workaddr = $hoeaddr = $this->address;

			reset($this->export);
			while ( list($name,$value) = each($this->export) )
			{
				if (!empty($buffer[$value]))
				{
					$mult = explode(';',$value);
					if (!$mult[1])
					{ // Normal
						if (strstr($buffer[$value],"\r\n") || strstr($buffer[$value],"\n") || (strtoupper($mult[0])=='FN'))
						{
							$buffer[$value] = $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
							$entry .= $value . ';ENCODING=QUOTED-PRINTABLE:' . $buffer[$value]."\r\n";
						}
						else
						{
							$entry .= $value . ':' . $buffer[$value] . "\r\n";
						}
					}
					else
					{
						switch ($mult[0])
						{
							case 'N':
								switch ($mult[1])
								{
									case 'PREFIX':
										$prefix    = ';' . $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
									case 'GIVEN':
										$firstname = ';' . $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
									case 'MIDDLE':
										$middle    = ';' . $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
									case 'FAMILY':
										$lastname  =       $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
									case 'SUFFIX':
										$suffix    = ';' . $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
								}
								break;
							case 'ORG':
								switch ($mult[1])
								{
									case 'NAME':
										$org_name = $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
									case 'UNIT':
										$org_unit = ';' . $this->vCard_encode($buffer[$value], 'QUOTED-PRINTABLE', false);
										break;
								}
								break;
							case 'ADR':
								switch ($mult[1])
								{
									case 'TYPE':
										if(isset($typei[$mult[2]]))
										{
											$typei[$mult[2]] .= ';';	
										}
										
										$types = explode(';',$buffer[$value]);
										if ($types[1])
										{
											$typei[$mult[2]] .= strtoupper($types[0][1]);
											for ($i=1; $i<count($types); $i++)
											{
													$typei[$mult[2]] .= ',' . strtoupper($types[$i][1]);
											}
										}
										elseif ($types[0])
										{
											$typei[$mult[2]] .= strtoupper($types[0]);
										}
										else
										{
											$typei[$mult[2]] .= strtoupper($buffer[$value]);
										}
										//echo "TYPE=".$typei[$mult[2]];
										break;
									case 'WORK':
										$workaddr[$mult[2]] = $buffer[$value];
										$workattr = $mult[0] . ';TYPE=' . $typei[$mult[1]];
										break;
									case 'HOME':
										$homeaddr[$mult[2]] = $buffer[$value];
										$homeattr = $mult[0] . ';TYPE=' . $typei[$mult[1]];
										break;
									default:
										break;
								}
								break;
							case 'TEL':
								switch($mult[1])
								{
									case 'PREFER':
										$prefer = explode(';',$buffer[$value]);
										if ($prefer[1])
										{
											while ($pref = strtoupper(each($prefer)))
											{
												$prefi[$pref] = ';PREF';
											}
											//echo 'PREF1';
										}
										elseif ($prefer[0])
										{
											$prefi[strtoupper($prefer[0])] = ';PREF';
											//echo 'PREF='.strtoupper($prefer[0]);
										}
										elseif ($buffer[$value])
										{
											$prefi[$buffer[$value]] = ';PREF';
											//echo 'PREF3';
										}
										break;
									case 'WORK':
										// Wow, this is fun!
										$entry .= 'A.' . $mult[0] . ';' . $mult[1] . $prefi[$mult[1]] . ';' . $mult[2] . ':' . $buffer[$value] . "\r\n";
										break;
									case 'HOME':
										$entry .= 'B.' . $mult[0] . ';' . $mult[1] . $prefi[$mult[1]] . ';' . $mult[2] . ':' . $buffer[$value] . "\r\n";
										break;
									default:
//										echo $mult[0] . ';' . $mult[1] . $prefi[$mult[1]] . ':' . $buffer[$value] . "\r\n";
//										echo '<br>';
										$entry .= $mult[0] . ';' . $mult[1] . $prefi[$mult[1]] . ':' . $buffer[$value] . "\r\n";
										break;
								}
								break;
							case 'EMAIL':
								switch($mult[1])
								{
									case 'TYPE':
										if ($mult[2] == 'WORK') { $emailtype  = ';' . $buffer[$value]; }
										if ($mult[2] == 'HOME') { $hemailtype = ';' . $buffer[$value]; }
										break;
									case 'WORK':
										$newval = 'A.'.$value;
										$entry .= $newval . $emailtype . ':' . $buffer[$value] . "\r\n";
										break;
									case 'HOME':
										$newval = 'B.' . $value;
										$entry .= $newval . $hemailtype . ':' . $buffer[$value] . "\r\n";
										break;
									default:
										break;
								}
								break;
							default:
								break;
						} //end switch ($mult[0])
					} //end else
				} //end if (!empty)
			} //end while

			$entries .= $header;

			if(!$middle)
			{
				$middle = ';';
			}

			if(!$prefix)
			{
				$prefix = ';';
			}

			$n = $lastname . $firstname . $middle . $prefix . $suffix;
			$entries .= 'N;ENCODING=QUOTED-PRINTABLE:' . $n . "\r\n";
			$entries .= $entry;

			if (!$buffer['FN'])
			{
				if ($lastname || $firstname)
				{
					$fn = substr($firstname,1) . ' ' . $lastname;
					$entries .= 'FN;ENCODING=QUOTED-PRINTABLE:' . $fn . "\r\n";
				}
			}
			if ($org_name || $org_unit)
			{
				$entries .= 'ORG;ENCODING=QUOTED-PRINTABLE:' . $org . "\r\n";
			}

			$workattr = str_replace('ADR;','',$workattr);
			// remember the correct order of address fields!
			$workaddr['POSTOFFICEBOX'] = $this->vCard_encode($workaddr['POSTOFFICEBOX'], 'QUOTED-PRINTABLE', false);
			$workaddr['EXT']           = $this->vCard_encode($workaddr['EXT'], 'QUOTED-PRINTABLE', false);
			$workaddr['STREET']        = $this->vCard_encode($workaddr['STREET'], 'QUOTED-PRINTABLE', false);
			$workaddr['LOCALITY']      = $this->vCard_encode($workaddr['LOCALITY'], 'QUOTED-PRINTABLE', false);
			$workaddr['REGION']        = $this->vCard_encode($workaddr['REGION'], 'QUOTED-PRINTABLE', false);
			$workaddr['POSTALCODE']    = $this->vCard_encode($workaddr['POSTALCODE'], 'QUOTED-PRINTABLE', false);
			$workaddr['COUNTRYNAME']   = $this->vCard_encode($workaddr['COUNTRYNAME'], 'QUOTED-PRINTABLE', false);
			$workaddr = $workaddr['POSTOFFICEBOX'].';'.$workaddr['EXT'].';'.$workaddr['STREET'].';'.$workaddr['LOCALITY'].';'.$workaddr['REGION'].';'.$workaddr['POSTALCODE'].';'.$workaddr['COUNTRYNAME'];
			$work = 'A.ADR;' . $workattr . ';ENCODING=QUOTED-PRINTABLE:' . $workaddr . "\r\n";
			$wlabel = 'LABEL;TYPE=WORK;;ENCODING=QUOTED-PRINTABLE:' . $wlabel . "\r\n";

			$homeattr = str_replace('ADR;','',$homeattr);
			// remember the correct order of address fields!
			$homeaddr['POSTOFFICEBOX'] = $this->vCard_encode($homeaddr['POSTOFFICEBOX'], 'QUOTED-PRINTABLE', false);
			$homeaddr['EXT']           = $this->vCard_encode($homeaddr['EXT'], 'QUOTED-PRINTABLE', false);
			$homeaddr['STREET']        = $this->vCard_encode($homeaddr['STREET'], 'QUOTED-PRINTABLE', false);
			$homeaddr['LOCALITY']      = $this->vCard_encode($homeaddr['LOCALITY'], 'QUOTED-PRINTABLE', false);
			$homeaddr['REGION']        = $this->vCard_encode($homeaddr['REGION'], 'QUOTED-PRINTABLE', false);
			$homeaddr['POSTALCODE']    = $this->vCard_encode($homeaddr['POSTALCODE'], 'QUOTED-PRINTABLE', false);
			$homeaddr['COUNTRYNAME']   = $this->vCard_encode($homeaddr['COUNTRYNAME'], 'QUOTED-PRINTABLE', false);
			$homeaddr = $homeaddr['POSTOFFICEBOX'].';'.$homeaddr['EXT'].';'.$homeaddr['STREET'].';'.$homeaddr['LOCALITY'].';'.$homeaddr['REGION'].';'.$homeaddr['POSTALCODE'].';'.$homeaddr['COUNTRYNAME'];
			$home = 'B.ADR;' . $homeattr . ';ENCODING=QUOTED-PRINTABLE:' . $homeaddr . "\r\n";
			$hlabel = 'LABEL;TYPE=HOME;;ENCODING=QUOTED-PRINTABLE:' . $hlabel . "\r\n";

			$entries = str_replace('PUBKEY','KEY',$entries);
			$entries .= $work . $home . $wlabel . $hlabel . 'END:VCARD' . "\r\n";
			$entries .= "\r\n";

			$buffer = $entries;
			return $buffer;
		} //end function


		function parse_vcard_line($line)
		{
			$parsed_line = array();
			list($name,$value) = explode(':', $line, 2); // explode limit to allow ':' in values
			if ($name && $value)
			{
				if ($name == 'VERSION')
				{
					$this->vCard_Version = $value;
				}

				if (strstr($name, ';ENCODING=QUOTED-PRINTABLE'))
				{
					$name  = str_replace(';ENCODING=QUOTED-PRINTABLE', '', $name);
					$value = quoted_printable_decode($value);
				}

				if (strstr($name, ';CHARSET='))
				{
					$pos_start = strpos($name, ';CHARSET=');
					$name      = str_replace(';CHARSET=', '', $name);
					$pos_end   = strpos($name, ';', $pos_start);
					if($pos_end === false)
					{
						$pos_end = strlen($name);
					}
					$len = $pos_end - $pos_start;
					if($len > 0)
					{
						$charset = substr($name, $pos_start, $len);
						$name    = substr($name, 0, $pos_start).substr($name, $pos_end);
						// convert all applicable characters from $charset to HTML entities
						$value   = htmlentities($value, ENT_NOQUOTES, $charset);
						// convert all applicable characters from HTML entities to ISO
						$value   = html_entity_decode($value, ENT_QUOTES, 'ISO-8859-1');
					}
				}
				else
				{
					$value = stripslashes($this->vCard_decode($value));
				}

				if (strstr($name, ';LANGUAGE='))
				{
					$pos_start = strpos($name, ';LANGUAGE=');
					$name      = str_replace(';LANGUAGE=', '', $name);
					$pos_end   = strpos($name, ';', $pos_start);
					if($pos_end === false)
					{
						$pos_end = strlen($name);
					}
					$len = $pos_end - $pos_start;
					if($len > 0)
					{
						//$lang = substr($name, $pos_start, $len); // no use
						$name = substr($name, 0, $pos_start).substr($name, $pos_end);
					}
				}

				if (strstr($name, ';TYPE='))
				{
					$pos_start = strpos($name, ';TYPE=');
					$name = str_replace(';TYPE=', ';', $name);
					$pos_end   = strpos($name, ';', $pos_start+1); // next ;
					if($pos_end === false)
					{ // no ; found -> use full string length
						$pos_end = strlen($name);
					}
					$len = $pos_end - $pos_start;
					if($len > 0)
					{
						$temp = substr($name, $pos_start, $len);
						$temp = str_replace(',', ';', $temp);
						$name = substr($name, 0, $pos_start) . $temp . substr($name, $pos_end);
					}
				}

				if (strstr($name, ';X-'))
				{
					$pos_start = strpos($name, ';X-');
					$pos_end   = strpos($name, ';', $pos_start+1); // next ;
					if($pos_end === false)
					{ // no ; found -> use full string length
						$pos_end = strlen($name);
					}
					$len = $pos_end - $pos_start;
					if($len > 0)
					{
						$name = substr_replace($name, '', $pos_start, $len);
					}
				}

				reset($this->import);
				while ( list($fname,$fvalue) = each($this->import) )
				{
					if ( strstr(strtolower($name), $this->import[$fname]) )
					{
						$value = trim($value);
						//$value = str_replace('=0D=0A','\n',$value); // use quoted_printable_decode above
						$parsed_line += array($name => $value);
					}
				}
			}
			return $parsed_line;
		}

		function vCard_decode($str)
		{
			switch($this->vCard_Version)
			{
				case '2.1':
				break;
				case '3.0':
					$str = utf8_decode($str);
				break;
				default:
				break;
			}
			return $str;
		}
		

		function vCard_encode($str, $transfer_encoding=false, $charset=false)
		{
			// 1. charset encoding
			switch(strtoupper($charset))
			{
				case 'UTF-8':
					$str = utf8_encode($str);
				break;
				default:
				break;
			}
			
			// 2. tranfer encoding
			switch(strtoupper($transfer_encoding))
			{
				case 'QUOTED-PRINTABLE':
					$str = str_replace("%","=", rawurlencode($str));
				break;
				case 'BASE64':
					$str = base64_encode($str);
				break;
				default:
				break;
			}

			return $str;
		}
				
	} //end class
?>

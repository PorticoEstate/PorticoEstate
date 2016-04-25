<?php
  /**************************************************************************\
  * phpGroupWare - Addressbook: CSV - Import                                 *
  * http://www.phpgroupware.org                                              *
  * Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'addressbook',
		'noheader'   => True,
		'enable_contacts_class' => True,
	);
	include('../header.inc.php');

	$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Import CSV-File into Addressbook');
	$GLOBALS['phpgw']->common->phpgw_header();

	$GLOBALS['phpgw']->contacts = createobject('phpgwapi.contacts');

	$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
	$GLOBALS['phpgw']->template->set_file(array('import' => 'csv_import.tpl'));
	$GLOBALS['phpgw']->template->set_block('import','filename','filenamehandle');
	$GLOBALS['phpgw']->template->set_block('import','fheader','fheaderhandle');
	$GLOBALS['phpgw']->template->set_block('import','fields','fieldshandle');
	$GLOBALS['phpgw']->template->set_block('import','ffooter','ffooterhandle');
	$GLOBALS['phpgw']->template->set_block('import','imported','importedhandle');

	$csvfile  = $_FILES['csvfile']['tmp_name'];

	if($_POST['action'] == 'download' && (!$_POST['fieldsep'] || !$csvfile || !($fp=fopen($csvfile,'rb'))))
	{
		$_POST['action'] = '';
	}
	if($_POST['action'] == 'import')
	{
		$csvfile = $GLOBALS['phpgw']->session->appsession('import_data', 'addressbook');
		if(!file_exists($csvfile))
		{
			$_POST['action'] = '';
		}
	}

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/addressbook/csv_import.php'));

	$PSep = '||'; // Pattern-Separator, separats the pattern-replacement-pairs in trans
	$ASep = '|>'; // Assignment-Separator, separats pattern and replacesment
	$VPre = '|#'; // Value-Prefix, is expanded to \ for ereg_replace
	$CPre = '|['; $CPreReg = '\|\['; // |{csv-fieldname} is expanded to the value of the csv-field
	$CPos = ']';  $CPosReg = '\]';	// if used together with @ (replacement is eval-ed) value gets autom. quoted

	function dump_array($arr)
	{
		while(is_array($arr) && (list($key,$val) = each($arr)))
		{
			$ret .= ($ret ? ',' : '(') . "'$key' => '$val'\n";
		}
		return $ret.')';
	}

	function index($value,$arr)
	{
		while(is_array($arr) && (list($key,$val) = each($arr)))
		{
			if($value == $val)
			{
				return $key;
			}
		}
		return False;
	}


	$cat2id = array();


	switch($_POST['action'])
	{
		case '':	// Start, ask Filename
			$GLOBALS['phpgw']->template->set_var('lang_csvfile',lang('CSV-Filename'));
			$GLOBALS['phpgw']->template->set_var('lang_fieldsep',lang('Fieldseparator'));
			$GLOBALS['phpgw']->template->set_var('fieldsep',$_POST['fieldsep'] ? $_POST['fieldsep'] : ',');
			$GLOBALS['phpgw']->template->set_var('submit',lang('Download'));
			$GLOBALS['phpgw']->template->set_var('csvfile',$csvfile);
			$GLOBALS['phpgw']->template->set_var('enctype','ENCTYPE="multipart/form-data"');
			$hiddenvars .= '<input type="hidden" name="action" value="download">'."\n";

			$GLOBALS['phpgw']->template->parse('filenamehandle','filename');
			break;

		case 'download':
			$GLOBALS['phpgw']->preferences->read();
			$defaults = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['cvs_import'];
			if(!is_array($defaults))
			{
				$defaults = array();
			}
			$GLOBALS['phpgw']->template->set_var('lang_csv_fieldname',lang('CSV-Fieldname'));
			$GLOBALS['phpgw']->template->set_var('lang_addr_fieldname',lang('Addressbook-Fieldname'));
			$GLOBALS['phpgw']->template->set_var('lang_translation',lang("Translation").' <a href="#help">'.lang('help').'</a>');
			$GLOBALS['phpgw']->template->set_var('submit',lang('Import'));
			$GLOBALS['phpgw']->template->set_var('lang_debug',lang('Test Import (show importable records <u>only</u> in browser)'));
			$GLOBALS['phpgw']->template->parse('fheaderhandle','fheader');
			$hiddenvars .= '<input type="hidden" name="action" value="import">'."\n"
				. '<input type="hidden" name="fieldsep" value="'.$_POST['fieldsep']."\">\n";


			$field_names = $GLOBALS['phpgw']->contacts->import_fields;
			$loc_names = $field_names['locations'];
			unset($field_names['locations']);
			$comm_name = linearize_query($GLOBALS['phpgw']->contacts->get_contact_comm_descr(),'comm_description');
			$location_types = linearize_query($GLOBALS['phpgw']->contacts->get_contact_addr_type(), 'addr_description');
			
			$loc_names = set_location_type($location_types, $loc_names);

			$addr_names = $field_names + $comm_name + $loc_names;

			$config = CreateObject('phpgwapi.config','addressbook');
			$config->read();
			while(list($name,$descr) = @each($config->config_data['custom_fields']))
			{
				$addr_names[$name] = $descr;
			}
			unset($config);

			foreach($addr_names as $field => $name)
			{
				if($dn = display_name($field))
				{
					$addr_names[$field] = $dn;
				}
			}
			$addr_name_options = "<option value=\"\">none\n";
			foreach($addr_names as $field => $name)
			{
				$addr_name_options .= "<option value=\"$field\">".$GLOBALS['phpgw']->strip_html($name)."\n";
			}
			$csv_fields = fgetcsv($fp,8000,$_POST['fieldsep']);

			foreach($csv_fields as $csv_idx => $csv_field)
			{
				$GLOBALS['phpgw']->template->set_var('csv_field',$csv_field);
				$GLOBALS['phpgw']->template->set_var('csv_idx',$csv_idx);
				if($def = $defaults[$csv_field])
				{
					list($addr,$_POST['trans']) = explode($PSep,$def,2);
					$GLOBALS['phpgw']->template->set_var('trans',$_POST['trans']);
					$GLOBALS['phpgw']->template->set_var('addr_fields',str_replace('="'.$addr.'">','="'.$addr.'" selected>',$addr_name_options));
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('trans','');
					$GLOBALS['phpgw']->template->set_var('addr_fields',$addr_name_options);
				}
				$GLOBALS['phpgw']->template->parse('fieldshandle','fields',True); 
			}

			$GLOBALS['phpgw']->template->set_var('csv_field','Categories');
			$GLOBALS['phpgw']->template->set_var('csv_idx','cat_id');
			$GLOBALS['phpgw']->template->set_var('trans','');
			$GLOBALS['phpgw']->template->set_var('addr_fields',get_categories());
			$GLOBALS['phpgw']->template->parse('fieldshandle','fields',True); 

			$GLOBALS['phpgw']->template->set_var('csv_field','Access');
			$GLOBALS['phpgw']->template->set_var('csv_idx','access');
			$GLOBALS['phpgw']->template->set_var('trans','');
			$GLOBALS['phpgw']->template->set_var('addr_fields',get_access());
			$GLOBALS['phpgw']->template->parse('fieldshandle','fields',True); 

			$GLOBALS['phpgw']->template->set_var('csv_field','Owner');
			$GLOBALS['phpgw']->template->set_var('csv_idx','owner');
			$GLOBALS['phpgw']->template->set_var('trans','');
			$GLOBALS['phpgw']->template->set_var('addr_fields',get_owner());
			$GLOBALS['phpgw']->template->parse('fieldshandle','fields',True); 

			$GLOBALS['phpgw']->template->set_var('lang_start',lang('Startrecord'));
			$GLOBALS['phpgw']->template->set_var('start',$_POST['start']);
			$GLOBALS['phpgw']->template->set_var('lang_max',lang('Number of records to read (<=200)'));
			$GLOBALS['phpgw']->template->set_var('max',200);
			$GLOBALS['phpgw']->template->parse('ffooterhandle','ffooter'); 
			fclose($fp);
			$old = $csvfile; $csvfile = $GLOBALS['phpgw_info']['server']['temp_dir'].'/addrbook_import_'.$GLOBALS['phpgw_info']['user']['account_id'].'_'.basename($csvfile);
			rename($old,$csvfile);
			$GLOBALS['phpgw']->session->appsession('import_data', 'addressbook', $csvfile);
			//$hiddenvars .= '<input type="hidden" name="csvfile" value="'.$csvfile.'">';
			$mktime_lotus = "${PSep}0?([0-9]+)[ .:-]+0?([0-9]*)[ .:-]+0?([0-9]*)[ .:-]+0?([0-9]*)[ .:-]+0?([0-9]*)[ .:-]+0?([0-9]*).*$ASep@mktime(${VPre}4,${VPre}5,${VPre}6,${VPre}2,${VPre}3,${VPre}1)";
			$help_on_trans = "<a name=\"help\"></a><b>How to use Translation's</b><p>".
				"Translations enable you to change / adapt the content of each CSV field for your needs. <br />".
				"General syntax is: <b>pattern1 ${ASep} replacement1 ${PSep} ... ${PSep} patternN ${ASep} replacementN</b><br />".
				"If the pattern-part of a pair is ommited it will match everything ('^.*$'), which is only ".
				"usefull for the last pair, as they are worked from left to right.<p>".
				"First example: <b>1${ASep}private${PSep}public</b><br />".
				"This will translate a '1' in the CSV field to 'privat' and everything else to 'public'.<p>".
				"Patterns as well as the replacement can be regular expressions (the replacement is done via ereg_replace). ".
				"If, after all replacements, the value starts with an '@' the whole value is eval()'ed, so you ".
				"may use all php, phpgw plus your own functions. This is quiet powerfull, but <u>circumvents all ACL</u>.<p>".
				"Example using regular expressions and '@'-eval(): <br /><b>$mktime_lotus</b><br />".
				"It will read a date of the form '2001-05-20 08:00:00.00000000000000000' (and many more, see the regular expr.). ".
				"The&nbsp;[&nbsp;.:-]-separated fields are read and assigned in different order to @mktime(). Please note to use ".
				"${VPre} insted of a backslash (I couldn't get backslash through all the involved templates and forms.) ".
				"plus the field-number of the pattern.<p>".
				"In addintion to the fields assign by the pattern of the reg.exp. you can use all other CSV-fields, with the ".
				"syntax <b>${CPre}CSV-FIELDNAME$CPos</b>. Here is an example: <br />".
				"<b>.+$ASep${CPre}Company$CPos: ${CPre}NFamily$CPos, ${CPre}NGiven$CPos$PSep${CPre}NFamily$CPos, ${CPre}NGiven$CPos</b><br />".
				"It is used on the CSV-field 'Company' and constructs a something like <i>Company: FamilyName, GivenName</i> or ".
				"<i>FamilyName, GivenName</i> if 'Company' is empty.<p>".
				"You can use the 'No CSV #'-fields to assign csv-values to more than on field, the following example uses the ".
				"csv-field 'Note' (which gots already assingned to the description) and construct a short subject: ".
				"<b>@substr(${CPre}Note$CPos,0,60).' ...'</b><p>".
				"Their is one important user-function for the Info Log:<br />".
				"<b>@addr_id(${CPre}NFamily$CPos,${CPre}NGiven$CPos,${CPre}Company$CPos)</b> ".
				"searches the addressbook for an address and returns the id if it founds an exact match of at least ".
				"<i>NFamily</i> AND (<i>NGiven</i> OR <i>Company</i>). This is necessary to link your imported InfoLog-entrys ".
				"with the addressbook.<br />".
				"<b>@cat_id(Cat1,...,CatN)</b> returns a (','-separated) list with the cat_id's. If a category isn't found, it ".
				"will be automaticaly added.<p>".
				"I hope that helped to understand the features, if not <a href='mailto:RalfBecker@outdoor-training.de'>ask</a>.";

			$GLOBALS['phpgw']->template->set_var('help_on_trans',lang($help_on_trans));	// I don't think anyone will translate this
			break;
		case 'import':
			@set_time_limit(0);
			$fp=fopen($csvfile,'rb');
			$csv_fields = fgetcsv($fp,8000,$_POST['fieldsep']);

			$cat_id = $_POST['addr_fields']['cat_id'];
			$access = $_POST['addr_fields']['access'];
			$owner = $_POST['addr_fields']['owner'];
			unset($_POST['addr_fields']['cat_id']);
			unset($_POST['addr_fields']['access']);
			unset($_POST['addr_fields']['owner']);
			$addr_fields = array_diff($_POST['addr_fields'],array(''));	// throw away empty / not assigned entrys
			$all_fields = $GLOBALS['phpgw']->contacts->import_fields;
			$location_fields = array_keys($all_fields['locations']);
			unset($all_fields['locations']);
			$comm_fields = linearize_query($GLOBALS['phpgw']->contacts->get_contact_comm_descr(),'comm_description');

			$defaults = array();
			foreach($addr_fields as $csv_idx => $addr)
			{	// convert $_POST['trans'][$csv_idx] into array of pattern => value
				$defaults[$csv_fields[$csv_idx]] = $addr;
				if($_POST['trans'][$csv_idx])
				{
					$defaults[$csv_fields[$csv_idx]] .= $PSep.$_POST['trans'][$csv_idx];
				}
			}

			$GLOBALS['phpgw']->preferences->read();
			$GLOBALS['phpgw']->preferences->add('addressbook','cvs_import',$defaults);
			$GLOBALS['phpgw']->preferences->save_repository(True);

			$log = "<table border=1>\n\t<tr><td>#</td>\n";

			foreach($addr_fields as $csv_idx => $addr)
			{	
				$pat_reps = explode($PSep,stripslashes($_POST['trans'][$csv_idx]));
				$replaces = ''; $values = '';
				if($pat_reps[0] != '')
				{
					foreach($pat_reps as $k => $pat_rep)
					{
						list($pattern,$replace) = explode($ASep,$pat_rep,2);
						if($replace == '')
						{
							$replace = $pattern; $pattern = '^.*$';
						}
						$values[$pattern] = $replace;	// replace two with only one, added by the form
						$replaces .= ($replaces != '' ? $PSep : '') . $pattern . $ASep . $replace;
					}
					$_POST['trans'][$csv_idx] = $values;
				}
				else
				{
					unset( $_POST['trans'][$csv_idx] );
				}
				$log .= "\t\t<td><b>$addr</b></td>\n";
			}
			$start = $_POST['start'] < 1 ? 1 : $_POST['start'];

			for($i = 1; $i < $start && fgetcsv($fp,8000,$_POST['fieldsep']); ++$i); 	// overread lines before our start-record

			for($anz = 0; $anz < $_POST['max'] && ($fields = fgetcsv($fp,8000,$_POST['fieldsep'])); ++$anz)
			{
				$log .= "\t</tr><tr><td>".($start+$anz)."</td>\n";

				reset($addr_fields); $values = array();
				while(list($csv_idx,$addr) = each($addr_fields))
				{
					//echo "<p>$csv: $addr".($_POST['trans'][$csv] ? ': '.$_POST['trans'][$csv] : '')."</p>";
					$val = $fields[$csv_idx];
					if(isset($_POST['trans'][$csv_idx]))
					{
						$trans_csv = $_POST['trans'][$csv_idx];
						while(list($pattern,$replace) = each($trans_csv))
						{
							if(preg_match("/$pattern/",$val))
							{
								// echo "<p>csv_idx='$csv_idx',info='$addr',trans_csv=".dump_array($trans_csv).",ereg_replace('$pattern','$replace','$val') = ";
								$val = preg_replace("/$pattern/",str_replace($VPre,'\\',$replace),(string) $val);
								// echo "'$val'</p>";

								$reg = $CPreReg.'([a-zA-Z_0-9]+)'.$CPosReg;
								while(preg_match("/$reg/",$val,$vars))
								{	// expand all CSV fields
									$val = str_replace($CPre . $vars[1] . $CPos, $val[0] == '@' ? "'"
										. addslashes($fields[index($vars[1], $csv_fields)])
										. "'" : $fields[index($vars[1], $csv_fields)], $val);
								}
								if($val[0] == '@')
								{
									if (!$GLOBALS['phpgw_info']['user']['apps']['admin'])
									{
										echo lang('@-eval() is only availible to admins!!!');
									}
									else
									{
										// removing the $ to close security hole of showing vars, which contain eg. passwords
										$val = 'return '.substr(str_replace('$','',$val),1).';';
										// echo "<p>eval('$val')=";
										$val = eval($val);
										// echo "'$val'</p>";
									}
								}
								if($pattern[0] != '@' || $val)
								{
									break; 
								}
							}
						}
					}
					//$values[$addr] = $val;
					if(in_array($addr, $comm_fields))
					{
						$fields_to_add['comm_media'][$addr] = $val;
					}
					elseif(in_array(substr($addr,strpos($addr, '_') +1), $location_fields))
					{
						$type = substr($addr,0, strpos($addr, '_') );
						$data = substr($addr,strpos($addr, '_') +1);
						$fields_to_add['locations'][$type]['type'] = $type;
						$fields_to_add['locations'][$type][$data] = $val;
					}
					else
					{
						$fields_to_add[$addr] = $val;
					}

					$log .= "\t\t<td>$val</td>\n";
				}
				$fields_to_add['categories'] = array($cat_id);
				$fields_to_add['access'] = $access;
				$fields_to_add['owner'] = $owner;
				if(!$_POST['debug'])
				{
					$GLOBALS['phpgw']->contacts->contact_import($fields_to_add);
					// echo "<p>adding: ".dump_array($values)."</p>\n";
				}
			}
			$log .= "\t</tr>\n</table>\n";

			$GLOBALS['phpgw']->template->set_var('anz_imported',$_POST['debug'] ? lang('%1 records read (not yet imported, you may go %2back%3 and uncheck Test Import)',
				$anz,'<a href="javascript:history.back()">','</a>') :
				lang('%1 records imported',$anz));
			$GLOBALS['phpgw']->template->set_var('log',$log);
			$GLOBALS['phpgw']->template->parse('importedhandle','imported');
			break;
	}

	$GLOBALS['phpgw']->template->set_var('hiddenvars',$hiddenvars);
	$GLOBALS['phpgw']->template->pfp('out','import',True);
	$GLOBALS['phpgw']->common->phpgw_footer();

	function linearize_query($qresult,$key)
	{
		//print_r($qresult);
		reset($qresult);
		for($i=0;$i < count($qresult);$i++)
		{
			$ret[$qresult[$i][$key]]=$qresult[$i][$key];
		}
		return $ret;
	}

	function set_location_type($location_types=array(), $location_fields=array())
	{
		foreach($location_types as $type)
		{
			foreach($location_fields as $key => $field)
			{
				$locations[$type . '_' . $key] = $type . ' ' . $field;
			}
		}
		return $locations;
	}

	function get_categories()
	{
		$cat = CreateObject('phpgwapi.categories');
		$cats_link = $cat->formated_list('select','all',$cat_id,True);
		return $cats_link;
	}

	function get_access()
	{
		$access = '<option>public</option>';
		$access .= '<option>private</option>';
		return $access;
	}

	function get_owner()
	{
		$account_list = $GLOBALS['phpgw']->accounts->get_list('accounts');
		if (is_array($account_list))
		{
			foreach($account_list as $entry)
			{
				$user_list .= '<option value="' . $entry['account_id'] . '"'
					. $selected . '>'
					. $GLOBALS['phpgw']->common->display_fullname(
						$entry['account_lid'],
						$entry['account_firstname'],
						$entry['account_lastname'])
					. '</option>'."\n";
			}
		}
		return $user_list;
	}

?>

<?php
  /**************************************************************************\
  * phpGroupWare - Addressbook                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org> and                      *
  * Miles Lott <miloschphpgroupware.org>                                     *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	phpgw::import_class('phpgwapi.uicommon');

	class uivcard extends phpgwapi_uicommon
	{
		var $contacts;
		var $browser;
		var $vcard;
		var $bo;

		var $public_functions = array(
			'in'  => True,
			'out' => True
		);

	 	var $extrafields = array(
			'ophone'   => 'ophone',
			'address2' => 'address2',
			'address3' => 'address3'
		);

		function __construct()
		{
			parent::__construct();

			$this->contacts = CreateObject('phpgwapi.contacts');
			$this->browser  = CreateObject('phpgwapi.browser');
			$this->vcard    = CreateObject('phpgwapi.vcard');
			$this->bo = CreateObject('addressbook.boaddressbook',True);
			self::set_active_menu("{$this->currentapp}::uivcard");
		}

		function in()
		{
			$tabs = array();
			$tabs['import'] = array('label' => lang('VCard in'), 'link' => '#import');

			$data = array(
				'form_action' => self::link(array('menuaction' => "{$this->currentapp}.boaddressbook.add_vcard")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uiaddressbook_persons.index",)),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab' => 0
			);

			self::render_template_xsl(array('vcard'), array('in' => $data));
		}

		function out()
		{
			$ab_id   = phpgw::get_var('ab_id');
			$nolname = phpgw::get_var('nolname');
			$nofname = phpgw::get_var('nofname');

			if($nolname || $nofname)
			{
				$GLOBALS['phpgw']->common->phpgw_header();
				echo parse_navbar();
			}

			if(!$ab_id)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uiaddressbook_persons.index'));
			}

			if(!$this->contacts->check_edit($ab_id))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'addressbook.uiaddressbook_persons.index'));
			}

			// First, make sure they have permission to this entry
			$fieldlist = $this->contacts->person_complete_data($ab_id);
			$type_work = $this->contacts->search_location_type('work');
			$type_home = $this->contacts->search_location_type('home');
			/*
			$fields['full_name']            = $fieldlist['full_name'];
      bug: $fieldlist['full_name'] contains two spaces between first and last name, when middle name is empty
      workaround: calculate the fullname like shown below
			*/
			$fields['first_name']           = $fieldlist['first_name'];
			$fields['last_name']            = $fieldlist['last_name'];
			$fields['middle_name']          = $fieldlist['middle_name'];

			$fields['full_name']            = $fields['first_name'] . ' ';
			$fields['full_name']           .= ($fields['middle_name'] != '') ? $fields['middle_name'] . ' ' : '';
			$fields['full_name']           .= $fields['last_name'];

			$fields['prefix']               = $fieldlist['prefix'];
			$fields['suffix']               = $fieldlist['suffix'];
			$fields['sound']                = $fieldlist['sound'];
			$fields['birthday']             = $fieldlist['birthday'];
			//$fields['note']               = $fieldlist[''];
			//$fields['tz']                 = $fieldlist['locations'][$type_work][''];
			//$fields['geo']                = $fieldlist[''];
			$fields['pubkey']               = $fieldlist['pubkey'];
			$fields['org_name']             = $fieldlist['org_name'];
			$fields['org_unit']             = $fieldlist['department'];
			$fields['title']                = $fieldlist['title'];
			$fields['adr_one_type']         = 'WORK';
			$fields['adr_two_type']         = 'HOME';
			//$fields['tel_prefer']         = $fieldlist[''];
			$fields['email_type']           = 'INTERNET';
			$fields['email_home_type']      = 'INTERNET';

			// locations contains a list of loc_id and its date
			if (isset($fieldlist['locations']) && is_array($fieldlist['locations']))
			{
				// locations[loc_id][type] is work or home
				// loc_id is not  interested here, but the type is important!
				//while ( list($loc_id, $loc_data) = each($fieldlist['locations']) )
                                foreach($fieldlist['locations'] as $loc_id => $loc_data)
				{
					$loc_type_id = $this->contacts->search_location_type($loc_data['type']);
					switch($loc_type_id)
					{
						case $type_work:
							$adr = 'adr_one_';
						break;
						case $type_home:
							$adr = 'adr_two_';
						break;
						default:
							continue 2;
						break;
					}
					$fields[$adr.'street']       = $loc_data['add1'];
					$fields[$adr.'ext']          = $loc_data['add2'];
					$fields[$adr.'locality']     = $loc_data['city'];
					$fields[$adr.'region']       = $loc_data['state'];
					$fields[$adr.'postalcode'] 	 = $loc_data['postal_code'];
					$fields[$adr.'countryname']	 = $loc_data['country'];
				}
			}

			$fields['tel_work']             = $fieldlist['comm_media']['work phone'];
			$fields['tel_home']             = $fieldlist['comm_media']['home phone'];
			$fields['tel_voice']            = $fieldlist['comm_media']['voice phone'];
			$fields['tel_work_fax']         = $fieldlist['comm_media']['work fax'];
			$fields['tel_home_fax']         = $fieldlist['comm_media']['home fax'];
			$fields['tel_msg']              = $fieldlist['comm_media']['msg phone'];
			$fields['tel_cell']             = $fieldlist['comm_media']['mobile (cell) phone'];
			$fields['tel_pager']            = $fieldlist['comm_media']['pager'];
			$fields['tel_bbs']              = $fieldlist['comm_media']['bbs'];
			$fields['tel_modem']            = $fieldlist['comm_media']['modem'];
			$fields['tel_car']              = $fieldlist['comm_media']['car phone'];
			$fields['tel_isdn']             = $fieldlist['comm_media']['isdn'];
			$fields['tel_video']            = $fieldlist['comm_media']['video'];
			$fields['email']                = $fieldlist['comm_media']['work email'];
			$fields['email_home']           = $fieldlist['comm_media']['home email'];
			$fields['url']                  = $fieldlist['comm_media']['website'];

			$email = $fields['email'];
			$emailtype = $fields['email_type'];
			if (!$emailtype)
			{
				$fields['email_type'] = 'INTERNET';
			}
			$hemail       = $fields['email_home'];
			$hemailtype   = $fields['email_home_type'];
			if (!$hemailtype)
			{
				$fields['email_home_type'] = 'INTERNET';
			}
			$firstname    = $fields['first_name'];
			$lastname     = $fields['last_name'];

			if(!$nolname && !$nofname)
			{
				/* First name and last must be in the vcard. */
				if($lastname == '')
				{
					/* Run away here. */
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'addressbook.uivcard.out', 'nolname' => 1, 'ab_id' => $ab_id));
				}
				if($firstname == '')
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction' => 'addressbook.uivcard.out', 'nofname' => 1, 'ab_id' =>$ab_id));
				}

				if ($email)
				{
					$fn =  explode('@',$email);
					$filename = sprintf("%s.vcf", $fn[0]);
				}
				elseif ($hemail)
				{
					$fn =  explode('@',$hemail);
					$filename = sprintf("%s.vcf", $fn[0]);
				}
				else
				{
					$fn = strtolower($firstname);
					$filename = sprintf("%s.vcf", $fn);
				}

				// set translation variable
				$myexport = $this->vcard->export;
				// check that each $fields exists in the export array and
				// set a new array to equal the translation and original value
				//while( list($name,$value) = each($fields) )
                                foreach($fields as $name => $value)
				{
					if ($myexport[$name] && ($value != "") )
					{
						//echo '<br />'.$name."=".$fields[$name]."\n";
						$buffer[$myexport[$name]] = $value;
					}
				}

				// create a vcard from this translated array
				$entry = $this->vcard->out($buffer);

				// print it using browser class for headers
				// filename, mimetype, no length, default nocache True
				$this->browser->content_header($filename,'text/x-vcard');
				echo $entry;
				sleep(1);
				exit;
				//$GLOBALS['phpgw']->common->exit;
			} /* !nolname && !nofname */

			if($nofname)
			{
				echo '<br /><br /><center>';
				echo lang("This person's first name was not in the address book.") .'<br />';
				echo lang('Vcards require a first name entry.') . '<br /><br />';
				echo '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook_persons.index')) . '">' . lang('OK') . '</a>';
				echo '</center>';
			}

			if($nolname)
			{
				echo '<br /><br /><center>';
				echo lang("This person's last name was not in the address book.") . '<br />';
				echo lang('Vcards require a last name entry.') . '<br /><br />';
				echo '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook_persons.index')) . '">' . lang('OK') . '</a>';
				echo '</center>';
			}

			if($nolname || $nofname)
			{
				//$GLOBALS['phpgw']->common->phpgw_footer();
			}
		}
	}

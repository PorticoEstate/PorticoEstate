<?php
/**************************************************************************\
* phpGroupWare - fax                                                       *
* http://www.phpgroupware.org                                              *
* This application written by:                                             *
*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
*                             PROSA <http://www.prosa.it>                  *
* -------------------------------------------------------------------------*
* Funding for this program was provided by http://www.seeweb.com           *
* -------------------------------------------------------------------------*
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

 /* $Id$ */

	class manager
	{
        var $public_functions = array
		  (
		   'preferences'	=>	True,
		   'write_prefs'	=>	True,
		   'globalsettings'	=>	True,
		   'admin_update'	=>	True,
		   'compose'	=>	True,
		   'sendfax'	=>	True,
		   'cover_preview'	=>	True,
		   'show'	=>	True
		   );
         
        function randomname($length) 
		{
			srand(date('s'));
            $possible_characters = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $string = '';
            while (strlen($string) < $length) 
			{
                $string  .= substr($possible_characters, rand()%(strlen($possible_characters)), 1);
			}
            return($string);
		}
         
        
        function stripslashes_array($arr = array()) 
		{
            $rs = array();
            while (list($key, $val) = each($arr)) 
			{
                if (is_array($arr[$key])) 
				{$rs[$key] = stripslashes_array($arr[$key]);}
                else 
				{$rs[$key] = htmlspecialchars(stripslashes($val), ENT_QUOTES);}
			}
            return $rs;
		}
         
        function unhtmlentities ($string) 
		{
			$trans_tbl = get_html_translation_table (HTML_ENTITIES);
            $trans_tbl = array_flip ($trans_tbl);
            $ret = strtr ($string, $trans_tbl);
            return preg_replace('/\&\#([0-9]+)\;/me', "chr('\\1')", $ret);
		}
         
        #to save all POSTed data (used by cover_preview and when send fail)
		function get_post_params()
		{
			$result = array();
			
			foreach ($_POST as $idx => $value)
			{
				if (is_array($value))
				{ 
					foreach ($value as $attr => $datum) 
					{
						$result[$idx.'['.$attr.']'] = $datum ;
					}
				}
				else 
				{ $result[$idx] = $value;}
			}
			return $result;	
		}
		
		
        function compose() 
		{
            if ($_POST) 
			{ $_POST = $this->stripslashes_array($_POST); }
			
            # uploaded file
            if (is_uploaded_file($_FILES['userfile']['tmp_name'])) 
			{ 
                $filename = $_FILES['userfile']['name'];
                $filename_real = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/phpgw_fax_{$GLOBALS['phpgw']->accounts->data['account_lid']}_" . $this->randomname(8) . "_{$filename}";
				
				# avoid to add wrong files
				if (filesize($_FILES['userfile']['tmp_name']) != 0)
				{
					copy($_FILES['userfile']['tmp_name'], $filename_real);
					$_POST['filename'] = $filename;
					$_POST['filename_real'] = $filename_real;
					$upld_err = '' ;
				}
				else
				{ 
					$upld_err = lang('upld_err');
				}
				
				
			}
             
            # file from storage
            if (isset($_POST['real_path'])) 
			{
                $_POST['filename'] = $_POST['name'];
                $_POST['filename_real'] = $_POST['real_path'];
			}
			
            # debug code:
#            print "<pre>";
#
#            print "</pre>";
             
            if (isset($_POST['action1'])) 
			{
                $this->sendfax($_POST);
                return;
			}
             
             
            if (isset($_POST['cat_id']) or isset($_POST['filter'])) 
			{
                $GLOBALS['phpgw']->common->phpgw_header();
                echo parse_navbar();
				
                $cat_id = $_POST['cat_id'];
                $filter = $_POST['filter'];
				
				switch ($cat_id)
				{ 
				 case 'none':
					$none = 'selected' ;
					break;
				 case 'all':
					$all = 'selected';
					break;
				}
				
				switch ($filter)
				{ case 'show_all':
					$show_all = 'selected';
					break;
				 case 'private':
					$private = 'selected';
					break;
				 case 'personal':
					$personal = 'selected';
					break;
				}
				
			}
            else
			{
                $cat_id = 'none';
                $no_data = True;
			}
             
            $faxnumber = $_POST['faxnumber'];
            $recipient = $_POST['recipient'];
            $location = $_POST['location'];
            $company = $_POST['company'];
            $faxtext = $_POST['faxtext'];
            $regarding = $_POST['regarding'];
            $comments = $_POST['comments'];
            $filename = $_POST['filename'];
            $filename_real = $_POST['filename_real'];
			
			if ($_POST['query'] != '')
			{ 
				$cat_id = 'none' ;
				$none = 'selected';
				$all = '';
				$query = $_POST['query'];

			}
			else
			{ $query = ''; }
			
			# keep selected data
            if (!($cat_id  != $_POST['old_cat'] or $query  != $_POST['old_query'] or $filter  != $_POST['old_filter'])) 
			{
                # items to select
                $was_reload = True;
                $to_use = Array();
                foreach ($_POST as $data_key => $data_value) 
				{
                    if ($data_value == 'on' and substr($data_key,0,5)=='item_') 
					{ $to_use[] = substr($data_key, 5); }
				}
			}
			
			
            $url_view = $GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.compose');
            $url_send = $GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.sendfax');
			$preview_url = $GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.cover_preview');
			$show_url = $GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.show');
			
			# WARNING! phpgwebhosting vs filemanager
			# WARNING! if it's not available the file_chooser patch
			$file_chooser = $GLOBALS['phpgw']->link('/index.php','menuaction=phpwebhosting.file_chooser.navigate&targetaction=fax.manager.compose&fc_type=L');

            $cat = CreateObject('phpgwapi.categories');
            $cat->categories($account_id, 'addressbook');
			
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('send_fax' => 'sendfax.tpl'));
            $tpl->set_block('send_fax', 'header', 'write_header');
            $tpl->set_block('send_fax', 'faxdata_body', 'write_faxdata_body');
            $tpl->set_block('send_fax', 'add_cover', 'write_add_cover');
            $tpl->set_block('send_fax', 'faxdata_cover_header', 'write_faxdata_cover_header');
            $tpl->set_block('send_fax', 'faxdata_cover_row', 'write_faxdata_cover_row');
            $tpl->set_block('send_fax', 'faxdata_cover_footer', 'write_faxdata_cover_footer');
            $tpl->set_block('send_fax', 'faxdata_add', 'write_faxdata_add');
            $tpl->set_block('send_fax', 'faxdata_search', 'write_faxdata_search');
			$tpl->set_block('send_fax', 'faxdata_searchonly', 'write_faxdata_searchonly');
            $tpl->set_block('send_fax', 'attachment_header', 'write_attachment_header');
            $tpl->set_block('send_fax', 'attachment_row', 'write_attachment_row');
            $tpl->set_block('send_fax', 'attachment_footer', 'write_attachment_footer');
            $tpl->set_block('send_fax', 'no_data', 'write_no_data');
			$tpl->set_block('send_fax', 'show_preview', 'write_show_preview');
            $tpl->set_block('send_fax', 'categories_header', 'write_categories_header');
            $tpl->set_block('send_fax', 'categories_footer', 'write_categories_footer');
            $tpl->set_block('send_fax', 'contacts_header', 'write_contacts_header');
            $tpl->set_block('send_fax', 'contacts_body', 'write_contacts_body');
            $tpl->set_block('send_fax', 'contacts_footer', 'write_contacts_footer');
            $tpl->set_block('send_fax', 'submit_button', 'write_submit_button');
			
            $contacts = CreateObject('addressbook.boaddressbook', TRUE);
            $common = CreateObject('phpgwapi.common');

			$img_path = $common->get_image_path();
            $user_login = $GLOBALS['phpgw']->accounts->data['account_lid'];
            $user_name = $GLOBALS['phpgw']->accounts->data['fullname'];
            $account_id = $GLOBALS['phpgw']->accounts->data['account_id'];
			
            #if first time, clean data
            if (!$_POST)
			{$tpl->set_var('filename_data',rawurlencode(serialize(array())));}
			
            $data = array
			  (
			   'account_id'	=>	$account_id,
			   'user_name'	=>	$user_name,
               'user_login'	=>	$user_login,
			   'url_view'	=>	$url_view,
			   'all'	=>	$all,
			   'none'	=>	$none,
			   'show_all'	=>	$show_all,
			   'private'	=>	$private,
			   'personal'	=>	$personal,
			   'faxnumber'	=>	$faxnumber,
			   'recipient'	=>	$recipient,
			   'company'	=>	$company,
			   'location'	=>	$location,
			   'regarding'	=>	$regarding,
			   'comments'	=>	$comments,
			   'old_query'	=>	$query,
			   'old_cat'	=>	$cat_id,
			   'old_filter'	=>	$filter,
			   'submit_url'	=>	$url_send,
			   'show_url'	=>	$show_url,
			   'filename'	=>	$filename,
			   'filename_real'	=>	$filename_real,
			   'fc_url'	=>	$file_chooser,
			   'preview_url'	=>	$preview_url,
			   'img_dn'	=>	$img_path.'/down.png',
			   'img_up'	=>	$img_path.'/up.png',
			   'l_faxnumber'	=>	lang('faxnumber'),
			   'l_recipient'	=>	lang('recipient'),
			   'l_company'	=>	lang('company'),
			   'l_location'	=>	lang('flocation'),
			   'l_regarding'	=>	lang('regarding'),
			   'l_comment'	=>	lang('fcomment'),
			   'l_mnotify'	=>	lang('mnotify'),
			   'l_ucover'	=>	lang('ucover'),
			   'l_cover'	=>	lang('cover'),
			   'l_all_cat'	=>	lang('all_cat'),
			   'l_all_add'	=>	lang('all_add'),
			   'l_private'	=>	lang('private'),
			   'l_mine'	=>	lang('mine'),
			   'l_change'	=>	lang('change'),
			   'l_new_query'	=>	lang('nquery'),
			   'l_search'	=>	lang('search'),
			   'l_contact'	=>	lang('contact'),
			   'l_city'	=>	lang('city'),
			   'l_no'	=>	lang('no'),
			   'l_yes'	=>	lang('yes'),
			   'l_send'	=>	lang('fsend'),
			   'l_choose'	=>	lang('choose'),
			   'l_text'	=>	lang('text'),
			   'l_file'	=>	lang('file'),
			   'l_storage'	=>	lang('storage'),
			   'l_cat'	=>	lang('cat'),
			   'l_filter'	=>	lang('filter'),
			   'fax'	=>	lang('fax'),
			   'l_addfile'	=>	lang('addfile'),
			   'l_addtext'	=>	lang('addtext'),
			   'l_delete'	=>	lang('delete'),
			   'l_u'	=>	lang('u'),
			   'l_d'	=>	lang('d'),
			   'l_show'	=>	lang('fshow'),
			   'l_preview'	=>	lang('preview'),
			   'l_fake'	=>	lang('fake'),
			   'l_att_descr'	=>	lang('att_descr'),
			   'l_upld_err'	=>	$upld_err ,
			   'bg_color'	=> $GLOBALS['phpgw_info']['theme']['bg_color'],
			   'bg_text'	=> $GLOBALS['phpgw_info']['theme']['bg_text'],
			   'th_bg'	=> $GLOBALS['phpgw_info']['theme']['th_bg'],
			   'th_text'	=> $GLOBALS['phpgw_info']['theme']['th_text'],
			   'table_bg_color'	=> $GLOBALS['phpgw_info']['theme']['table_bg'],
			   #almost all themes have the same bg colours here :-(
			   'bg01'	=> $GLOBALS['phpgw_info']['theme']['bg02'],
			   'bg02'	=> $GLOBALS['phpgw_info']['theme']['bg03'],
			   'bg03'	=> $GLOBALS['phpgw_info']['theme']['bg04']			   
			   );

            $tpl->set_var($data);
             
            $tpl->pparse('write_header', 'header', TRUE);
            $tpl->pparse('write_categories_header', 'categories_header');
			
            print $cat->formated_list('select', 'all', $cat_id, TRUE);
             
            $tpl->pparse('write_categories_footer', 'categories_footer');
             
            if ($cat_id  != 'none' or isset($query) ) 
			{
                if ($cat_id == 'all' or $query)
				{
                    $tpl->set_var('type', lang('finclude'));
                    $tpl->set_var('operand', 'include');
				}
                else 
				{
                    $tpl->set_var('type', lang('fexclude'));
                    $tpl->set_var('operand', 'exclude');
				}
                 
                $fields = array
				  (
				   'id'	=>	'id',
				   'fn'	=>	'fn',
				   'tel_fax'	=>	'tel_fax',
				   'org_name'	=>	'org_name',
				   'adr_one_locality'	=>	'adr_one_locality'
				   );
                 
                $qfilter = array('fields' => $fields);
                $qfilter['order'] = 'fn,org_name'; # more?
				
                if ($cat_id == 'none')
				{ $qfilter['filter'] = 'cat_id=-1';}
				
                if ($cat_id == 'all' or $query)
				{ $qfilter['filter'] = ''; } 
                else 
				{ $qfilter['filter'] = 'cat_id='.$cat_id; }
				
                switch ($filter) 
				{
                    case 'show_all';
                    break;
					
                    case 'private';
                    $qfilter['filter']  .= ',access=private';
                    break;
                       
                    case 'personal';
                    $qfilter['filter']  .= ',owner='.$account_id;
                    break;
                       
				}
				
                if (isset($query))
				{ $qfilter['query'] = $query; }
				
                $contacts_data = $contacts->read_entries($qfilter);
                 
                if (!$contacts_data)
				{
                    $no_data = TRUE;
					
                    if (($query  != '' and $cat_id == 'none') or $cat_id  != 'none')
					{
                        $tpl->set_var('l_sorry', lang('sorry'));
                        $tpl->pparse('write_no_data', 'no_data');
					}
				}
				else
				{
                    $no_data = FALSE;
                    $tpl->set_var('td_color', $GLOBALS['phpgw_info']['theme']['th_bg']);
                    $tpl->pparse('write_contacts_header', 'contacts_header');
                    $tpl->set_var('contacts', rawurlencode(serialize($contacts_data)));
					
                    $alternate = $GLOBALS['phpgw']->nextmatch = CreateObject('phpgwapi.nextmatchs');
					
                    for($i = 0; $i < count($contacts_data); $i++)
					{
                        if ($to_use)
						{
                            if (in_array($i, $to_use))
							{ $tpl->set_var('it_sel', 'checked');}
                            else
							{ $tpl->set_var('it_sel', ''); }
						}
                        else
						{ $tpl->set_var('it_sel', ''); }
						
                        $bgcolor = $alternate->alternate_row_color($bgcolor);
						
                        $tpl->set_var(array
									  (
									   'fn'	=>	$contacts_data[$i]['fn'],
									   'org_name'	=>	$contacts_data[$i]['org_name'],
									   'adr_one_locality'	=>	$contacts_data[$i]['adr_one_locality'],
									   'id'	=>	$i,
									   'tel_fax'	=>	$contacts_data[$i]['tel_fax'],
									   'bgcolor'	=>	$bgcolor
									   ));
						
                        $tpl->pparse('write_contacts_body', 'contacts_body');
					}
					
                    $tpl->pparse('write_contacts_footer', 'contacts_footer');
				}
			}
			
			
            if ($cat_id == 'none' and $no_data)
			{$tpl->pparse('write_faxdata_search', 'faxdata_search'); }
			else
			{$tpl->pparse('write_faxdata_searchonly', 'faxdata_searchonly'); }
            
			if ($no_data)
			{ $tpl->pparse('write_faxdata_add', 'faxdata_add'); }
			
            $this->db = $GLOBALS['phpgw']->db;
			
            # userprefs
            if (!$_POST['cover'])
			{
                $query = 'SELECT prefs FROM phpgw_fax_prefs WHERE faxuser="'.$user_login.'"';
                $this->db->query($query, __LINE__, __LINE__);
                $this->db->next_record();
                $u_prefs = unserialize($this->db->f('prefs'));
                $_POST['cover'] = $u_prefs['cover'];
                if ($u_prefs['notify'] == 'N')
				{ $_POST['notify'] = 'N'; } 
                else 
				{ $_POST['notify'] = 'Y'; }
			}
			
            if ($_POST['notify'] == 'N')
			{
                $tpl->set_var('mnot_check_no', 'selected');
                $tpl->set_var('mnot_check_yes', '');
			}
            else 
			{
                $tpl->set_var('mnot_check_yes', 'selected');
                $tpl->set_var('mnot_check_no', '');
			}
			
            $tpl->pparse('write_faxdata_body', 'faxdata_body', TRUE);
			
            if ($_POST['addcover'] == 'N')
			{
                $tpl->set_var('cover_sel_yes', '');
                $tpl->set_var('cover_sel_no', 'SELECTED');
                $tpl->pparse('write_add_cover', 'add_cover', TRUE);
			}
            else
			{
                $tpl->set_var('cover_sel_yes', 'SELECTED');
                $tpl->set_var('cover_sel_no', '');
                $tpl->pparse('write_add_cover', 'add_cover', TRUE);
                $tpl->pparse('write_faxdata_cover_header', 'faxdata_cover_header', TRUE);
				
                # cover list				
                $query = 'SELECT global_settings FROM phpgw_fax_admin';
                $this->db->query($query, __LINE__, __FILE__);
                $this->db->next_record();
                $gs = unserialize($this->db->f('global_settings'));
                $cp = $gs['cover_path'];
				
                #ToDO: add smart filter; in prefs section too.
				#ToDO: add warning message if not set
                exec('ls  '.$gs['cover_path'], $ls);
                $i = 0;
                foreach ($ls as $cover_file)
				{
                    $tpl->set_var('cover_path', "{$cp}/{$cover_file}");
                    $tpl->set_var('cover_name', $cover_file);
					
                    if ($_POST['cover'] == "{$cp}/{$cover_file}"
						|| ($_POST['cover'] == '' && $i == 0))
					{
                        $i = 1;
                        $tpl->set_var('sel', 'selected');
					}
                    else
					{ $tpl->set_var('sel', ''); }
                    $tpl->pparse('write_faxdata_cover_row', 'faxdata_cover_row');
				}
				$tpl->pparse('write_faxdata_cover_footer', 'faxdata_cover_footer', TRUE);
			}
			
			
            # faxtext
            if (!$faxtext)
			{ $tpl->set_var('l_msg', lang('msg')); }
            else
			{ $tpl->set_var('l_msg', $faxtext); }
			
            
            #ToDO: filemanager|phpwebhosting present? 
			#if ($GLOBALS['phpgw_info']['apps']['phpwebhosting']['enabled'] or $GLOBALS['phpgw_info']['apps']['filemanager']['enabled'])
			#  { $tpl->pparse('write_faxdata_file_chooser', 'faxdata_file_chooser', TRUE); }
			
            $attach_array = unserialize(urldecode(stripslashes($_POST['filename_data'])));
            $update_attach_data = False ; 
			
			if ($_POST['filename'] and $_POST['filename_real'])
			{
				if ($filename and $filename_real)
				{
					$attach_array[]=Array($filename, $filename_real);
					$update_attach_data = TRUE ;
				}
				
			}
			
            # add text
            if (isset($_POST['add_text']))
			{
                $rand_name = $this->randomname(12);
                $tmp_dir = $GLOBALS['phpgw_info']['server']['temp_dir'];
				
                $filename_real = "{$tmp_dir}/phpgw_fax_{$user_login}_{$rand_name}.txt";
                
                #ToDo: catch errors!
                $fle = fopen($filename_real, 'w+');
                fwrite($fle, $this->unhtmlentities($_POST['faxtext']."\n"));
                fclose($fle);
                $tm = localtime();
                $filename = 'txt_'.$tm[2].$tm[1].$tm[0];
                $attach_array[]=Array($filename, $filename_real);
                $update_attach_data = TRUE ;
                $tpl->set_var('l_msg', lang('msg'));
			}
			
            #modify order 
            $len = count($attach_array);
            $to_move = $_POST['selected_file'];

			if ($to_move == 'FAKE')
			{ $fake = true;}
			else
			{ $fake = false;}
			
            # ToDO: do it better 
            if ($to_move and !$fake)
			{
                for($i = 0; $i < $len; $i++)
				{
                    if (in_array($to_move,$attach_array[$i]))
					{
						$index = $i;
                        break;
					}
				}
			}
			
			if (!$fake)
			{
				switch ($_POST['attach_options'])
				{
					case 'file_up';   
					{               
						if ($index != 0)
						{
							$tmp = $attach_array[$i-1];
							$attach_array[$i-1] = $attach_array[$i];
							$attach_array[$i] = $tmp;
							$update_attach_data = True;
						}
						break; 
					}
					
					case 'file_down';
					{               
						if ($index != $len-1)
						{
							$tmp = $attach_array[$i+1];
							$attach_array[$i+1] = $attach_array[$i];
							$attach_array[$i] = $tmp;
							$update_attach_data = True;
						}
						break ;
					}
					
					
					case 'file_del';
					{
						for ($ii=$i;$ii<=$len-1;$ii++)
						{$attach_array[$ii]=$attach_array[$ii+1];}
						unset($attach_array[$len-1]);  
						$update_attach_data = True;
						break;
					}
					
					case 'file_show';
					{ # check for errors!
						$to_show = $_POST['selected_file'];
						
						# avoid pdf->pdf conversion
						exec('file -bik '.$to_show,$ou);
						
						if (substr($ou[0],0,15)!='application/pdf')
						{ 
							exec ('cp '.$to_show.' '.$to_show.'.txt');
							exec ('convert -antialias -colorspace gray '.$to_show.'.txt '.$to_show.'.pdf',$ou);           
							$tpl->set_var('show_link',$to_show.'.pdf');
						}
						else
						{ $tpl->set_var('show_link',$to_show) ;}
						
						$tpl->set_var('show_win_name',' ');
						$tpl->pparse('write_show_preview','show_preview');
						#update_attach_data = True ;
						break ;

					}
					
				}
			}
			
            $tpl->pparse('write_attachment_header','attachment_header');
			
            if ($update_attach_data==TRUE)
			{
				$tpl->set_var(array
							  (
							   'filename_data'	=>	rawurlencode(serialize($attach_array)),
							   'filename'	=>	'',
							   'filename_real'	=>	'',
							   ));
			}
            else
			{$tpl->set_var('filename_data',$_POST['filename_data']);}
            
            
            if ($attach_array)
			{
				foreach($attach_array as $file_entry)
				{
                    $tpl->set_var('fn_n', $file_entry[0]);
                    $tpl->set_var('fn_fp',$file_entry[1]);
                    $tpl->pparse('write_attachment_row','attachment_row');
				}
			}
			
			
            $tpl->pparse('write_attachment_footer','attachment_footer');
            $tpl->pparse('write_sumbit_button', 'submit_button', TRUE);
			
		}
		
		
        function sendfax($data) 
		{
            $contacts_data = unserialize(urldecode(stripslashes($data['contacts'])));
            $attach_array = unserialize(urldecode(stripslashes($_POST['filename_data'])));
			
            #print "<pre>";
			#print_r($contacts_data);
			#print "</pre>";
            $GLOBALS['phpgw']->common->phpgw_header();
            echo parse_navbar();
			
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('send_fax' => 'sendfax.tpl'));
            $tpl->set_block('send_fax', 'err_message', 'write_err_message');
			
            $tpl->set_var('msg_ok', lang('msgok'));
            $tpl->set_block('send_fax', 'fax_sent', 'write_fax_sent');
			$tpl->set_block('send_fax', 'hidden_data', 'write_hidden_data');
			$tpl->set_block('send_fax', 'hidden_footer', 'write_hidden_footer');
			$tpl->set_block('send_fax', 'hidden_header', 'write_hidden_header');
			
			$comments = $data['comments'];
            $recipient = $data['recipient'];
            $faxnumber = $data['faxnumber'];
            $user_login = $data['user_login'];
            $regarding = $data['regarding'];
            $company = $data['company'];
            $location = $data['location'];
            $operand = $data['operand'];
			$back_url = $GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.compose');

            if (!$attach_array)
			{
				$tpl->set_var('l_goback',lang('goback'));				
                $tpl->set_var('errmessage', lang('err2'));
				$tpl->set_var('back_url',$back_url);
				$tpl->pparse('write_hidden_header','hidden_header');
				foreach ($data as $idx => $value)
				{
					if ($idx != 'action1')
					{
					$tpl->set_var('hidden_name',$idx);
					$tpl->set_var('hidden_value',$value);
					$tpl->pparse('write_hidden_data','hidden_data',TRUE);
					}
					
				}
                $tpl->pparse('write_err_message', 'err_message', TRUE);
				$tpl->pparse('write_hidden_footer','hidden_footer');
                return;
			}
		
			
            $this->db = $GLOBALS['phpgw']->db;
            $query = 'SELECT global_settings FROM phpgw_fax_admin';
            $this->db->query($query, __LINE__, __FILE__);
            $this->db->next_record();
            $gs = unserialize($this->db->f('global_settings'));
			
            #ToDo: warning message if domain not set
            $domain = $gs['domain'];
            if ($domain{0}!='@')
			{$domain = "@".$domain;}
			
            if ($data['addcover'] == 'Y') 
			{ $cover = "-C '".$data['cover']."'";} 
            else 
			{ $cover = '-n '; }
			
            if ($data['notify'] == 'Y')
			{ $notify = ' -D'; }
			
            # get selected items only
            $to_use = array();
            foreach ($data as $data_key => $data_value)
			{
                if ($data_value == 'on')
				{ $to_use[] = substr($data_key, 5);}
			}
			
            # include selected
            if ($operand == 'include')
			{
                $tmp = array();
                foreach ($to_use as $use)
				{ $tmp[] = $contacts_data[$use];}
                $final_data = $tmp;
			}
			
            # exclude selected
            if ($operand == 'exclude')
			{
                foreach ($to_use as $use)
				{ unset($contacts_data[$use]); }
                $final_data = $contacts_data;
			}
			
            if (!$final_data and !$faxnumber)
			{
				$tpl->set_var('back_url',$back_url);
                $tpl->set_var('errmessage', lang('err1'));
				$tpl->set_var('l_goback',lang('goback'));
				$tpl->pparse('write_hidden_header','hidden_header');
				foreach ($data as $idx => $value)
				{
					if ($idx != 'action1')
					{
						$tpl->set_var('hidden_name',$idx);
						$tpl->set_var('hidden_value',$value);
						$tpl->pparse('write_hidden_data','hidden_data',TRUE);
					}
				}
                $tpl->pparse('write_err_message', 'err_message', TRUE);
				$tpl->pparse('write_hidden_footer','hidden_footer');
                return;
			}
			
            if ($faxnumber)
			{
                $final_data = array
				  (
				   array
				   (
					'fn'	=>	$recipient,
					'tel_fax'	 =>	$faxnumber,
					'regarding'	=>	$regarding,
                    'comments'	=>	$comments,
                    'org_name'	=>	$company,
                    'adr_one_locality'	=>	$location
					)
				   );
			}
             
            $fax_filename = '';
            foreach ($attach_array as $attach_file)
			{$fax_filename  .= $attach_file[1].' ';}
			
			print "<h3><center>please: see note in fax/inc/class.manager.inc.php!</center></h3>";
            foreach ($final_data as $dest) 
			{
                print '<br><br>'.$dest['fn']." ".$dest['tel_fax']." ".$dest['company']. " ".$dest['adr_one_locality'].'<br>';
				
                $faxcmd = "sendfax -c '".$comments."' ".$cover.$notify." -f '".$user_login.$domain."' -i 'phpgroupware' -r '".$regarding."' -x '". $dest['org_name']."' -y '".$dest['adr_one_locality']."'  -d '".$dest['tel_fax']."' ".$fax_filename;

/*			   ____  _____    _    ____  __  __ _____ _ 
			  |  _ \| ____|  / \  |  _ \|  \/  | ____| |
			  | |_) |  _|   / _ \ | | | | |\/| |  _| | |
			  |  _ <| |___ / ___ \| |_| | |  | | |___|_|
			  |_| \_\_____/_/   \_\____/|_|  |_|_____(_)
*/
				
                # IF YOU WANT TO SEND THE FAXES FOR REAL UNCOMMENT THE
				# FOLLOWING LINE!
				#exec($faxcmd, $out);
				
				# IF YOU WANT TO REMOVE THE DEBUG OUTPUT COMMENT THE
				# FOLLOWING LINE!
                print $faxcmd;
				
			}
			
            $url = $GLOBALS['phpgw']->link('/fax/index.php');
            $tpl->set_var('submit_url', $url);
            $tpl->pparse('write_fax_sent', 'fax_sent', TRUE);
			
		}
		
		function show()
		{
			print "perche' sono arrivato qui?";
			$GLOBALS['phpgw']->common->phpgw_header();
            echo parse_navbar();
			#print '<pre>';
			#print_r ($_POST);
			#print '</pre>';
			
			$dl_pic = $GLOBALS['phpgw']->common->image('fax','pdf');
			# check for errors!
			
			$tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('send_fax' => 'sendfax.tpl'));
            $tpl->set_block('send_fax', 'show_preview', 'write_show_preview');           
			
			$to_show = $_POST['selected_file'];
			
			$tpl->set_var('dl_pic',$dl_pic);
			$tpl->set_var('name',$to_show);
			$tpl->set_var('l_goback',lang('goback'));
			
			# avoid pdf->pdf conversion; then a real pdf may have colours
			exec('file -bik '.$to_show,$ou);
			
			if (substr($ou[0],0,15)!='application/pdf')
			{ 
				exec ('cp '.$to_show.' '.$to_show.'.txt');
				exec ('convert -antialias -colorspace gray '.$to_show.'.txt '.$to_show.'.pdf',$ou);           
				$tpl->set_var('show_link',$to_show.'.pdf');
			}
			else
			{ $tpl->set_var('show_link',$to_show) ;}
			
			$tpl->set_var('show_win_name',' ');
			$tpl->pparse('write_show_preview','show_preview');
			#update_attach_data = True ;
		}
		
        function preferences()
		{
			
            # Update Preferences
            if (isset($_POST['action1']))
			{
                $this->write_prefs($_POST);
                return;
			}
			
            $user_login = $GLOBALS['phpgw']->accounts->data['account_lid'];
            $this->db = $GLOBALS['phpgw']->db;
			
            $GLOBALS['phpgw']->common->phpgw_header();
            echo parse_navbar();
			
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('preferences' => 'preferences.tpl'));
            $tpl->set_block('preferences', 'notify', 'write_notify');
            $tpl->set_block('preferences', 'cover_header', 'write_cover_header');
            $tpl->set_block('preferences', 'cover_row', 'write_cover_row');
            $tpl->set_block('preferences', 'cover_footer', 'write_cover_footer');
			
            $query = 'SELECT global_settings FROM phpgw_fax_admin';
            $this->db->query($query, __LINE__, __FILE__);
            $this->db->next_record();
            $gs = unserialize($this->db->f('global_settings'));
            $cp = $gs['cover_path'];
			
            $query = "SELECT prefs FROM phpgw_fax_prefs WHERE faxuser='".$user_login."'";
			
            $this->db->query($query, __LINE__, __FILE__);
            $this->db->next_record();
			
            $url = $GLOBALS['phpgw']->link('/index.php', 'menuaction=fax.manager.preferences');
			
            $tpl->set_var(array
						  (
						   'submit_url'	=>	$url,
						   'user_login'	=>	$user_login,
						   'l_mnotify'	=>	lang('mnotify'),
						   'l_no'	=>	lang('no'),
						   'l_yes'	=>	lang('yes'),
						   'l_cover'	=>	lang('cover')
						   ));
			
            $prefs = $this->db->f('prefs');
            if (!$prefs) 
			{
                # default values
                $prefs = array('cover' => '', 'notify' => 'Y');
			}
			else 
			{ $prefs = unserialize($prefs); }
			
            if ($_POST['cover'])
			{ $prefs['cover'] = $_POST['cover']; }
			
			if ($_POST['notify'])
			{ $prefs['notify'] = $_POST['notify'];}
			
			$prefs['notify'] == 'N' ? $tpl->set_var('def_no' , 'CHECKED') : $tpl->set_var('def_yes', 'CHECKED');
			
            $tpl->pparse('write_notify', 'notify');
            
			
            exec('ls  '.$gs['cover_path'], $ls);
			
			
            $tpl->pparse('write_cover_header', 'cover_header');
			
            $i = 0;
            foreach ($ls as $cover_file)
			{
                $tpl->set_var('cover_path', "{$cp}/{$cover_file}");
                $tpl->set_var('cover_name', $cover_file);
                if ($prefs['cover'] == "{$cp}/{$cover_file}" 
					|| (!$prefs['cover'] && $i == 0)) 
				{
                    $i = 1;
                    $prefs['cover'] = "{$cp}/{$cover_file}";
                    $tpl->set_var('sel', 'selected');
				}
                else 
				{ $tpl->set_var('sel', ''); }
                $tpl->pparse('write_cover_row', 'cover_row');
			}
			
            $user_name = $GLOBALS['phpgw']->accounts->data['fullname'];
            $tmp_dir = $GLOBALS['phpgw_info']['server']['temp_dir'];
			
            $c_faxnum = '000000';
            $recipient = 'phpGroupWare';
            $comments = '... ... ...';
			$regarding = 'PROSA - free software -';
			$company = 'http://www.prosa.it';
			
			#ToDo: use the cover_preview func?
			
            $command = "faxcover -C \"{$prefs['cover']}\" -f \"{$user_name}\" -n \"{$c_faxnum}\" -t \"{$recipient}\" -c \"{$comments}\" -r \"{$regarding}\" -x \"{$company}\" > {$tmp_dir}/{$user_login}_COVER.ps";
			
			exec ($command,$ou);
            
            #ToDo: check for conver/faxcover commands
            #ToDo: check acl
            #Memo: original pics: 596x842 
			
            $command = "convert -size 596x842 {$tmp_dir}/{$user_login}_COVER.ps {$tmp_dir}/{$user_login}_COVER.jpg";
            exec ($command, $ou);
			$tpl->set_var('img_src', "{$tmp_dir}/{$user_login}_COVER.jpg");
			
            $tpl->pparse('write_cover_footer', 'cover_footer');
			
		}
		
		
        function write_prefs($data)
		{
            $GLOBALS['phpgw']->common->phpgw_header();
            echo parse_navbar();
			
            $this->db = $GLOBALS['phpgw']->db;
            $user = $data['user_login'];
            $cover = $data['cover'];
            $notify = $data['notify'];
			
            $prefs = array
			  (
			   'cover'	=>	$cover,
			   'notify'	=>	$notify
			   );
			
            $test = "SELECT faxuser FROM phpgw_fax_prefs WHERE faxuser='".$user."'";
            $this->db->query($test, __LINE__, __FILE__);
            if ($this->db->next_record())
			{
                # found
                $query = "UPDATE phpgw_fax_prefs SET prefs='".serialize($prefs). "' WHERE faxuser='".$user."'";
			}
            else 
			{
                # not found
                $query = "INSERT INTO phpgw_fax_prefs (faxuser,prefs) VALUES ('".$user."', '".serialize($prefs)."')";
			}
			
            $this->db->query($query, __LINE__, __FILE__);
			
            $url = $GLOBALS['phpgw']->link('/preferences/index.php');
                         
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('preferences' => 'preferences.tpl'));
            $tpl->set_block('preferences', 'updated', 'write_updated');
            $tpl->set_var(array
						  (
						   'submit_url'	=>	$url,
						   'message'	=>	lang('admin_up')
						   ));
            $tpl->pparse('write_updated', 'updated', TRUE);
		}
		
	    function cover_preview()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			$post_data = $this->get_post_params();
			print "<pre>";
#			print_r ($_POST);
			print "</pre>";
			$faxnumber = $_POST['faxnumber'];
            $recipient = $_POST['recipient'];
            $location = $_POST['location'];
            $company = $_POST['company'];
            $faxtext = $_POST['faxtext'];
            $regarding = $_POST['regarding'];
            $comments = $_POST['comments'];
			$cover_file = $_POST['cover'];
			$back_url = $GLOBALS['phpgw']->link('/index.php','menuaction=fax.manager.compose');
			if ($faxnumber == '')
			{ $c_faxnum = '000 000 000 ';}
			else
			{ $c_faxnum = $faxnumber;}
			
			if (isset($_POST['contacts']))
			{ 
				$recipient=lang('multiple');
				$company=lang('multiple');
				$location=lang('multiple');
				$c_faxnum=lang('multiple');
			}
				
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('cover_preview' => 'cover_preview.tpl'));
            $tpl->set_block('cover_preview', 'image', 'write_image');
			$tpl->set_block('cover_preview', 'form_header', 'write_form_header');
			$tpl->set_block('cover_preview', 'form_footer', 'write_form_footer');
			$tpl->set_block('cover_preview', 'hidden_input', 'write_hidden_input');
			
			$user_login = $GLOBALS['phpgw']->accounts->data['account_lid'];
            $user_name = $GLOBALS['phpgw']->accounts->data['fullname'];
			#$account_id = $GLOBALS['phpgw']->accounts->data['account_id'];

			$tmp_dir = $GLOBALS['phpgw_info']['server']['temp_dir'];
			
			$command = "faxcover -C \"{$cp}/{$cover_file}\" -f \"{$user_name}\" -n \"{$c_faxnum}\" -t \"{$recipient}\" -c \"{$comments}\" -r \"{$regarding}\" -x \"{$company}\" > {$tmp_dir}/{$user_login}_COVER.ps";
			#print $command;
			exec ($command, $ou);
			
			#ToDO: facover & convert programs
			#ToDO: check the rights over the files
			#Memo: Original pic size: 596x842
			
			$command = "convert -size 596x842 {$tmp_dir}/{$user_login}_COVER.ps  {$tmp_dir}/{$user_login}_COVER.jpg";
			exec ($command, $ou);
			
			$tpl->set_var('img_src', "{$tmp_dir}/{$user_login}_COVER.jpg}");
			$tpl->set_var('back_url',$back_url);
			$tpl->set_var('l_goback',lang('goback'));
			$tpl->pparse('write_form_header','form_header');
			foreach ($post_data as $idx => $value)
			{   if ($idx != 'query')
			  {
				  $tpl->set_var('hidden_name',$idx);
				  $tpl->set_var('hidden_value',$value);
				  $tpl->pparse('write_hidden_input','hidden_input',TRUE);
			  }
			}
			$tpl->pparse('write_image','image',TRUE);
			$tpl->pparse('write_form_footer','form_footer');
		}
		
        function globalsettings()
		{
            $this->db = $GLOBALS['phpgw']->db;
            $url = $GLOBALS['phpgw']->link('/index.php', 'menuaction=fax.manager.admin_update');
			
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('admin' => 'admin.tpl'));
            $tpl->set_block('admin', 'set_paths', 'write_set_paths');
			
            $query = 'SELECT global_settings FROM phpgw_fax_admin';
            $this->db->query($query, __LINE__, __FILE__);
            $this->db->next_record();
            $gs = unserialize($this->db->f('global_settings'));
			
            $GLOBALS['phpgw']->common->phpgw_header();
            echo parse_navbar();
			
            #ToDO: add max fax/day ? or similar?
            $tpl->set_var(array
						  (
						   'submit_url'	=>	$url,
						   'cover_path'	=>	$gs['cover_path'],
						   'domain'	=>	$gs['domain'],
						   'l_message'	=>	lang('admin_up'),
						   'l_cover_pref'	=>	lang('cover_pref'),
						   'l_update'	=>	lang('update'),
						   'l_domain'	=>	lang('domain')
						   ));
             
            $tpl->pparse('write_set_paths', 'set_paths', TRUE);
		}
		
        function admin_update()
		{
            $url = $GLOBALS['phpgw']->link('/admin/index.php');
			
            $GLOBALS['phpgw']->common->phpgw_header();
            echo parse_navbar();
			
            $this->db = $GLOBALS['phpgw']->db;
			
            #Warning! Erasing all data
            $query = 'DELETE FROM phpgw_fax_admin';
			
            $this->db->query($query, __LINE__, __FILE__);
			
            $gs = serialize(array
							(
							 'cover_path'	=>	$_POST['cover_path'],
							 'domain'	=>	$_POST['domain']
							 ));
            $query = "INSERT into phpgw_fax_admin (global_settings) VALUES ('".$gs."')";
            $this->db->query($query, __LINE__, __FILE__);
			
            $tpl = CreateObject('phpgwapi.Template', PHPGW_APP_TPL);
            $tpl->set_file(array('admin' => 'admin.tpl'));
            $tpl->set_block('admin', 'updated', 'write_updated');
			
            $tpl->set_var(array
						  (
						   'submit_url'	=>	$url,
						   'admin_up'	=>	lang('admin_up')
						   ));
			
            $tpl->pparse('write_updated', 'updated', TRUE);
			
		}
    }
# hylafax stuff!
#     false 0 startjob pop 
# ing  `when  '' (e.g. `when done'').  Note that `when
#  requeued'' implies `when done''.  (Equivalent  to  the
#  -D, -R, and -N options.)
#xferfaxstats xferfaxlog  /var/spool/hylafax/etc/xferfaxlog 
#ToDO: check attachment with blank spaces!
?>
	      



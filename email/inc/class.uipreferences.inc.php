<?php
	/**
	* EMail - Preferences
	*
	* @author Mark Cushman <mark@cushman.net>
	* @author Angelo (Angles) Puglisi <angles@aminvestments.com>
	* @copyright Copyright (C) xxxx Mark Cushman
	* @copyright Copyright (C) 2001-2002 Angelo Tony Puglisi (Angles)
	* @copyright Copyright (C) 2003-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal Based on AngleMail http://www.anglemail.org/
	* @internal Based on Aeromail http://the.cushman.net/
	*/


	/**
	* Preferences
	*
	* @package email
	*/
	class email_uipreferences
	{
		var $public_functions = array
		(
			'preferences'		=> true,
			'ex_accounts_edit'	=> true,
			'ex_accounts_list'	=> true,
			'preferences_default_acct_zero' => true
		);

		var $bo;
		var $tpl;
		var $nextmatchs;
		var $theme;
		var $prefs;
		var $debug = 0;
		//var $debug = 3;


		function __construct()
		{
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->bo = CreateObject('email.bopreferences');
			$temp_prefs = $GLOBALS['phpgw']->preferences->create_email_preferences();
			$this->prefs = $temp_prefs['email'];
		}

		/**
		 * create 2 columns TR's (TableRows) from preference data as standardized in email
		*
		 * bopreferences class vars ->std_prefs[]  and ->cust_prefs[], various HTML widgets supported
		 * @param $feed_prefs : array : preference data as standardized in email bopreferences class
		 * vars ->std_prefs[]  and ->cust_prefs[]
		 * @return : string : HTML data accumulated for parsed prefernce widget TR's
		 *  email bopreferences class vars ->std_prefs[]  and ->cust_prefs[], as filled by
		 * email bopreferences->init_available_prefs(), represent a standardized preferences schema,
		 * this function generates TR's from that data, using elements "id", "widget", "other_props",
		 * "lang_blurb", and "values" from that array structure. This function uses that data to fill
		 * a template block that contatains the requested widget and the appropriate data.
		 * Available HTML widgets are:
		 * 	* textarea
		 * 	* textbox
		 * 	* passwordbox
		 * 	* combobox
		 * 	* checkbox
		 * If prefs data "other_props" contains "hidden", as with password data, then the actual
		 * preference value is not shown and the "text blurb" is appended with "(hidden)".
		 * Array can contain any number of preference "records", all generated TR's are cumulative.
		 * @author	Angles
		 * @access	Private
		 */
		function create_prefs_block($feed_prefs='')
		{
			if ($this->debug > 0 ) { echo 'email.uipreferences.create_prefs_block: ENTERING, $this->bo->account_group: ['.$this->bo->account_group.']; $this->bo->acctnum: ['.$this->bo->acctnum.']<br />'; }
			$return_block = '';
			if(!$feed_prefs)
			{
				$feed_prefs = array();
			}
			if (count($feed_prefs) == 0)
			{
				if ($this->debug > 0 ) { echo 'email.uipreferences.create_prefs_block: LEAVING early, $feed_prefs param was empty<br />'; }
				return $return_block;
			}

			// initialial backcolor, will be alternated between row_on and row_off
			$back_color = $this->theme['row_off'];
			$back_color_class = 'row_off';

			// what existing user preferences data do we use to retrieve what the user has already saved for a particular pref
			if (($this->bo->account_group == 'extra_accounts')
			&& (isset($this->bo->acctnum)))
			{
				// the existing prefs are for en ectra email account
				if ($this->debug > 1) { echo 'email.uipreferences.create_prefs_block: ('.$this->bo->account_group.') get user prefs from DB by calling $GLOBALS[phpgw]->preferences->create_email_preferences(\'\', '.$this->bo->acctnum.')<br />'; }
				//by calling this function with a specific acctnum, we get back fully procecessed prefs data from the DB
				// for the that acctnum
				$temp_prefs = $GLOBALS['phpgw']->preferences->create_email_preferences('', $this->bo->acctnum);
				$actual_user_prefs = $temp_prefs['email'];
			}
			else
			{
				// default email account, top level data
				if ($this->debug > 1) { echo 'email.uipreferences.create_prefs_block: ('.$this->bo->account_group.') for default account, top level prefs already processed<br />'; }
				$actual_user_prefs = $this->prefs;
			}
			if ($this->debug > 2) { echo 'email.uipreferences.create_prefs_block: $this->bo->account_group: ['.$this->bo->account_group.'] ; $this->bo->acctnum: ['.$this->bo->acctnum.'] ; $actual_user_prefs dump:<pre>'; print_r($actual_user_prefs); echo '</pre>'; }

			$c_prefs = count($feed_prefs);
			// ---  Prefs Loops  ---
			for($i=0;$i<$c_prefs;$i++)
			{
				$this_item = $feed_prefs[$i];
				if ($this->debug > 2) { echo '** loop ['.$i.'] **: email.uipreferences.create_prefs_block: $this_item = $feed_prefs['.$i.'] = [<code>'.serialize($this_item).'</code>] ; $this_item DUMP <pre>'; print_r($this_item); echo '</pre>'; }

				// ---- do not show logic  ----
				// do we show this for "default" account and/or "extra_accounts"
				if (($this->bo->account_group == 'default')
				&& (!stristr($this_item['accts_usage'] , 'default')))
				{
					// we are not supposed to show this item for the default account, skip this pref item
					// continue is used within looping structures to skip the rest of the current loop
					// iteration and continue execution at the beginning of the next iteration
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: skip showing this item because it is not applicable to the default account<br />'; }
					continue;
				}
				elseif (($this->bo->account_group == 'extra_accounts')
				&& (!stristr($this_item['accts_usage'] , 'extra_accounts')))
				{
					// we are not supposed to show this item for extra accounts, skip this pref item
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: skip showing this item because it is not applicable to the extra accounts<br />'; }
					continue;
				}
				elseif (strstr($this_item['type'] , 'INACTIVE'))
				{
					// this item has been depreciated or otherwise no longer is being used
					// we are not supposed to show this item, skip this pref item
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: skip showing this item because "INACTIVE" is in $this_item[type] : ['.$this_item['type'].']<br />'; }
					continue;
				}

				// ----  ok to show this, continue...  ----
				if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block:  ... this item passed skip test, so it should be displayed ...<br />'; }
				// ROW BACK COLOR
				//$back_color = $this->nextmatchs->alternate_row_color($back_color);
				$back_color = (($i + 1)/2 == floor(($i + 1)/2)) ? $this->theme['row_off'] : $this->theme['row_on'];
				$back_color_class = (($i + 1)/2 == floor(($i + 1)/2)) ? 'row_off' : 'row_on';

				$var = array
				(
					'back_color'	=> $back_color,
					'back_color_class'	=> $back_color_class,
					'lang_blurb'	=> $this_item['lang_blurb'],
					'extra_text'	=> ''
				);
				$this->tpl->set_var($var);

				// this will be the HTTP_POST_VARS[*key*] key value, the "id" for the submitted pref item
				if ($this->bo->account_group == 'default')
				{
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: html post array $key for this item is $this_item[id]: '.$this_item['id'].'<br />'; }
					$this->tpl->set_var('pref_id', $this_item['id']);
				}
				else
				{
					// modify the HTTP_POST_VARS[*key*] key in the html form so it contains info about thich acctnum it applies to
					// we do this only for Extra Accounts, prefix the ""id" with the acctnum
					// so the submitted prefs are then array based, wit the acctnum being the top level array item
					// and the pref item "id"'s being child elements of that acctnum
					$html_pref_id = $this->bo->acctnum.'['.$this_item['id'].']';
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: html post array $key for this item is $html_pref_id: '.$html_pref_id.'<br />'; }
					$this->tpl->set_var('pref_id', $html_pref_id);
				}

				// we don't want to show a hidden value
				if ( !isset($this_item['write_props']) || !stristr($this_item['write_props'], 'hidden'))
				{
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: obtain $this_item_value, because "hidden" is not in $this_item[write_props]<br />'; }
					// "user strings" may have quotes and stuff that need to be encoded b4 we display it
					if ($this_item['type'] == 'user_string')
					{
						if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: $this_item[type] == "user string" , before htmlspecialchars_encode: [<code>'.$actual_user_prefs[$this_item['id']].'</code>]<br />'; }
						$this_item_value = $GLOBALS['phpgw']->msg->htmlspecialchars_encode($actual_user_prefs[$this_item['id']]);
						if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: $this_item[type] == "user string" , after htmlspecialchars_encode: [<code>'.$this_item_value.'</code>]<br />'; }
					}
					else
					{
						if ( !isset($actual_user_prefs[$this_item['id']]) )
						{
							 $actual_user_prefs[$this_item['id']] = '';
						}
						$this_item_value = $actual_user_prefs[$this_item['id']];
						if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: $this_item[type] NOT a "user string" , so NO htmlspecialchars_encode required: $this_item_value: [<code>'.$this_item_value.'</code>]<br />'; }
					}
				}
				else
				{
					// if the data is hidden (ex. a password), we do not show the value (obviously)
					if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: HIDDEN $this_item_value should be empty string, this "hidden" is in $this_item[write_props]<br />'; }
					$this_item_value = '';
					// tell user we are hiding the value (that's whay the box is empty)
					$prev_lang_blurb = $this->tpl->get_var('lang_blurb');
					$this->tpl->set_var('lang_blurb', $prev_lang_blurb.'&nbsp('.lang('hidden').')');
				}
				if ($this->debug > 1) { echo ' * email.uipreferences.create_prefs_block: after processing, $this_item_value: [<code>'.serialize($this_item_value).'</code>] ; $this_item DUMP <pre>'; print_r($this_item); echo '</pre>'; }


				// ** possible widget are: **
				// textarea
				// textbox
				// passwordbox
				// combobox
				// checkbox
				if ($this_item['widget'] == 'textarea')
				{
					//$this_item_value = $actual_user_prefs[$this_item['id']];
					$this->tpl->set_var('pref_value', $this_item_value);
					$this->tpl->parse('V_tr_textarea','B_tr_textarea');
					$done_widget = $this->tpl->get_var('V_tr_textarea');
				}
				elseif ($this_item['widget'] == 'textbox')
				{
					$this->tpl->set_var('pref_value', $this_item_value);
					$this->tpl->parse('V_tr_textbox','B_tr_textbox');
					$done_widget = $this->tpl->get_var('V_tr_textbox');
				}
				elseif ($this_item['widget'] == 'passwordbox')
				{
					// this_item_value should have been set to blank above
					// if $this_item['write_props'] contains the word "hidden"
					$this->tpl->set_var('pref_value', $this_item_value);
					$this->tpl->parse('V_tr_passwordbox','B_tr_passwordbox');
					$done_widget = $this->tpl->get_var('V_tr_passwordbox');
				}
				elseif ($this_item['widget'] == 'combobox')
				{
					// set up combobox available options as KEYS array with empty VALUES
					//reset($this_item['values']);
					$combo_availables = Array();
					$x = 0;
					//while ( list ($key,$prop) = each ($this_item['values']))
					if (is_array($this_item['values']))
					{
						foreach($this_item['values'] as $key => $prop)
						{
							$combo_availables[$key]	= '';
							$x++;
						}
					}
					// fill the pref item in $combo_availables[this_item_value] to " selected"
					$combo_available[$actual_user_prefs[$this_item['id']]] = ' selected';
					// make the combobox HTML tags string
					$combobox_html = '';
					//reset($this_item['values']);
					$x = 0;
					//while ( list ($key,$prop) = each ($this_item['values']))
					if (is_array($this_item['values']))
					{
						foreach($this_item['values'] as $key => $prop)
						{
							if ( !isset($combo_available[$key]) || !$combo_available[$key])
							{
								$combo_available[$key] = '';
							}
							$combobox_html .=
								'<option value="'.$key.'"'.$combo_available[$key].'>'.$prop.'</option>' ."\r\n";
							$x++;
						}
					}
					$this_item_value = $combobox_html;
					$this->tpl->set_var('pref_value', $this_item_value);
					$this->tpl->parse('V_tr_combobox','B_tr_combobox');
					$done_widget = $this->tpl->get_var('V_tr_combobox');
				}
				elseif ($this_item['widget'] == 'checkbox')
				{
					if (isset($actual_user_prefs[$this_item['id']]) && $actual_user_prefs[$this_item['id']])
					{
						$this_item_value = 'checked';
					}
					else
					{
						$this_item_value = '';
					}
					$this->tpl->set_var('pref_value', $this_item_value);
					$this->tpl->parse('V_tr_checkbox','B_tr_checkbox');
					$done_widget = $this->tpl->get_var('V_tr_checkbox');
				}
				else
				{
					//$this->pref_errors .= 'call for unsupported widget:'.$this_item['widget'].'<br />';
					$this->tpl->set_var('back_color', $back_color);
					$this->tpl->set_var('back_color_class', $back_color_class);
					$this->tpl->set_var('section_title', 'call for unsupported widget:'.$this_item['widget']);
					$this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
					$done_widget = $this->tpl->get_var('V_tr_sec_title');
				}
				// add long help if requested
				if ((isset($GLOBALS['phpgw']->msg->ref_GET['show_help']))
				&& ($GLOBALS['phpgw']->msg->ref_GET['show_help']))
				{
					$this->tpl->set_var('long_desc', $this_item['long_desc']);
					$done_widget .= $this->tpl->parse('V_tr_long_desc','B_tr_long_desc');
				}
				// for each loop, add the finished widget row to the return_block variable
				$return_block .= $done_widget;
			}
			if ($this->debug > 0 ) { echo 'email.uipreferences.create_prefs_block: LEAVING, returning $return_block if widgets<br />'; }
			return $return_block;
		}

		/**
		 * call this function to display the typical UI html page for email preferences
		*
		 *  should obtain the desired account number the user wants to see prefs for, if possible
		 * @author	Angles
		 * @access	Public
		 */
		function preferences()
		{
			// If we ar given an acct number, then display prefs for that account, else show prefs for default account zero
			if
			(
				((isset($GLOBALS['phpgw']->msg->ref_POST['ex_acctnum']))
				&& ((string)$GLOBALS['phpgw']->msg->ref_POST['ex_acctnum'] != '')
				&& ((string)$GLOBALS['phpgw']->msg->ref_POST['ex_acctnum'] != '0'))
			||
				((isset($GLOBALS['phpgw']->msg->ref_GET['ex_acctnum']))
				&& ((string)$GLOBALS['phpgw']->msg->ref_GET['ex_acctnum'] != '')
				&& ((string)$GLOBALS['phpgw']->msg->ref_GET['ex_acctnum'] != '0'))
			)
			{
				// we are dealing with a non-default account here
				$this->ex_accounts_edit();
			}
			else
			{
				// we are dealing with the default account, account number 0, this is default fallback behavior
				$this->preferences_default_acct_zero();
			}
		}

		/**
		 * call this function to display the UI html page for email preferences for the Default Account
		*
		 *  This is ONLY FOR THE DEFAULT ACCOUNT for 2 reasons
		 * 1) the defaut account has slightly different prefs then the extra account, and
		 * 2) author too lazy to combine this function with ex_accounts_edit() like it should be
		 * @author	Angles, skeeter
		 * @access	Public
		 */
		function preferences_default_acct_zero()
		{
			// this tells "create_prefs_block" that we are dealing with the default email account
			if ($this->debug > 0) { echo 'email.uipreferences.preferences: ENTERING, this function *should* only be called for the default email account prefs submission<br />'; }
			if ($this->debug > 1) { echo 'email.uipreferences.preferences: about to set $this->bo->account_group<br />'; }
			$this->bo->account_group = 'default';
			if ($this->debug > 1) { echo 'email.uipreferences.preferences: just set $this->bo->account_group to ['.$this->bo->account_group.']<br />'; }

			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				// we point to the global template for this version of phpgw templatings
				$this->tpl =& $GLOBALS['phpgw']->template;
				//$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			}
			else
			{
				// we use a PRIVATE template object for 0.9.14 conpat and during xslt porting
				$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			}

			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header(true);
				$this->tpl->set_root(PHPGW_APP_TPL);
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}

			$this->tpl->set_file(
				Array(
					'T_prefs_ui_out'	=> 'class_prefs_ui.tpl',
					'T_pref_blocks'		=> 'class_prefs_blocks.tpl'
				)
			);
			$this->tpl->set_block('T_pref_blocks','B_tr_blank','V_tr_blank');
			$this->tpl->set_block('T_pref_blocks','B_tr_sec_title','V_tr_sec_title');
			$this->tpl->set_block('T_pref_blocks','B_tr_long_desc','V_tr_long_desc');
			$this->tpl->set_block('T_pref_blocks','B_tr_textarea','V_tr_textarea');
			$this->tpl->set_block('T_pref_blocks','B_tr_textbox','V_tr_textbox');
			$this->tpl->set_block('T_pref_blocks','B_tr_passwordbox','V_tr_passwordbox');
			$this->tpl->set_block('T_pref_blocks','B_tr_combobox','V_tr_combobox');
			$this->tpl->set_block('T_pref_blocks','B_tr_checkbox','V_tr_checkbox');
			$this->tpl->set_block('T_pref_blocks','B_submit_btn_only','V_submit_btn_only');
			$this->tpl->set_block('T_pref_blocks','B_submit_and_cancel_btns','V_submit_and_cancel_btns');

			$var = Array(
				'pref_errors'		=> '',
				'page_title'		=> lang('E-Mail preferences'),
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction'	=> 'email.bopreferences.preferences'
					)
				),
				'th_bg'			=> $this->theme['th_bg'],
				'left_col_width'	=> '50%',
				'right_col_width'	=> '50%',
				'checked_flag'		=> 'True',
				'btn_submit_name'	=> $this->bo->submit_token,
				'btn_submit_value'	=> lang('submit'),
				// added a cancel button
				'btn_cancel_name'	=> 'cancel',
				'btn_cancel_value'	=> lang('cancel'),
				'btn_cancel_url'	=> $GLOBALS['phpgw']->link('/preferences/index.php',array())
			);
			$this->tpl->set_var($var);

			// this will fill the $this->bo->std_prefs[] and cust_prefs[]  "schema" arrays
			if ($this->debug > 1) { echo 'email.uipreferences.preferences: calling $this->bo->init_available_prefs() to init $this->bo->std_prefs[] and cust_prefs[]  "schema" arrays<br />'; }
			$this->bo->init_available_prefs();

			if ($this->debug > 3) { echo 'email.uipreferences.preferences: initiated schema dump:'; $this->bo->debug_dump_prefs(); }

			// initialize a local var to hold the cumulative main block data
			$prefs_ui_rows = '';

			// ---  Standars Prefs  ---
			// section title for standars prefs
			$this->tpl->set_var('section_title', lang('Standard E-Mail preferences'));
			//This checks if we are all ready displaying the help and gives us the option of hiding it.
			if ((isset($GLOBALS['phpgw']->msg->ref_GET['show_help']))
				&& ($GLOBALS['phpgw']->msg->ref_GET['show_help']))
			{
			// link to display verbose help text
			$show_help_lnk = $GLOBALS['phpgw']->msg->href_maketag(
					$GLOBALS['phpgw']->link('/index.php',
						Array('menuaction'	=> 'email.uipreferences.preferences',
							)
					),
					lang('Hide Help'));
			} else {
			// link to hide verbose help text
			$show_help_lnk = $GLOBALS['phpgw']->msg->href_maketag(
					$GLOBALS['phpgw']->link('/index.php',
						Array('menuaction'	=> 'email.uipreferences.preferences',
							'show_help'		=> '1')
					),
					lang('Show Help'));
			}
			$this->tpl->set_var('show_help_lnk', $show_help_lnk);
			// parse the block, and put into a local variable
			$done_widget = $this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
			// add the finished widget row to the main block variable
			$prefs_ui_rows .= $done_widget;
			// generate Std Prefs HTML Block
			if ($this->debug > 1) { echo 'email.uipreferences.preferences: about to generate the html for standard email prefs block<br />'; }
			$prefs_ui_rows .= $this->create_prefs_block($this->bo->std_prefs);

			// blank row
			$this->tpl->set_var('back_color', $this->theme['bg_color']);
			$done_widget = $this->tpl->parse('V_tr_blank','B_tr_blank');
			$prefs_ui_rows .= $done_widget;

			// ---  Custom Prefs  ---
			$this->tpl->set_var('section_title', lang('Custom E-Mail preferences'));
			$done_widget = $this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
			$prefs_ui_rows .= $done_widget;
			// generate Custom Prefs HTML Block
			if ($this->debug > 1) { echo 'email.uipreferences.preferences: about to generate the html for custom email prefs block<br />'; }
			$prefs_ui_rows .= $this->create_prefs_block($this->bo->cust_prefs);

			// blank row
			$this->tpl->set_var('back_color', $this->theme['bg_color']);
			$done_widget = $this->tpl->parse('V_tr_blank','B_tr_blank');
			$prefs_ui_rows .= $done_widget;

			// ---  Commit HTML Prefs rows to Main Template
			// put all widget rows data into the template var
			$this->tpl->set_var('prefs_ui_rows', $prefs_ui_rows);

			// Submit Button only
			//$submit_btn_row = $this->tpl->parse('V_submit_btn_only','B_submit_btn_only');
			//$this->tpl->set_var('submit_btn_row', $submit_btn_row);
			// Submit Button and Cancel button
			$submit_btn_row = $this->tpl->parse('V_submit_and_cancel_btns','B_submit_and_cancel_btns');
			$this->tpl->set_var('submit_btn_row', $submit_btn_row);

			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());

			// output the template
			if ($this->debug > 0) { echo 'email.uipreferences.preferences: LEAVING, about to output the template<br />'; }
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$this->tpl->pfp('out','T_prefs_ui_out');
			}
			else
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('E-Mail preferences');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('E-Mail preferences');
				$data['email_page'] = $this->tpl->parse('out','T_prefs_ui_out');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
		}

		/**
		 * call this function to display the typical UI html page Extra Email Accounts Preferences
		*
		 * @author	Angles, skeeter
		 * @access	Public
		 */
		function ex_accounts_edit()
		{
			// this tells "create_prefs_block" that we are dealing with the extra email accounts
			if ($this->debug > 0) { echo 'email.uipreferences.ex_accounts_edit: ENTERING, this function *should* only be called for the EXTRA email account prefs submission<br />'; }
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: about to set $this->bo->account_group<br />'; }
			$this->bo->account_group = 'extra_accounts';
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: just set $this->bo->account_group to ['.$this->bo->account_group.']<br />'; }

			// obtain the acctnum for the extra email account we are dealing with here
			$acctnum = $this->bo->obtain_ex_acctnum();
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: $this->bo->obtain_ex_acctnum() returns ['.serialize($acctnum).']<br />'; }
			$this->bo->acctnum = $acctnum;
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: we just set $this->bo->acctnum to ['.serialize($this->bo->acctnum).']<br />'; }

			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				// we point to the global template for this version of phpgw templatings
				$this->tpl =& $GLOBALS['phpgw']->template;
				//$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			}
			else
			{
				// we use a PRIVATE template object for 0.9.14 conpat and during xslt porting
				$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			}

			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header(true);
				$this->tpl->set_root(PHPGW_APP_TPL);
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}

			$this->tpl->set_file(
				Array(
					'T_prefs_ui_out'	=> 'class_prefs_ui.tpl',
					'T_pref_blocks'		=> 'class_prefs_blocks.tpl'
				)
			);
			$this->tpl->set_block('T_pref_blocks','B_tr_blank','V_tr_blank');
			$this->tpl->set_block('T_pref_blocks','B_tr_sec_title','V_tr_sec_title');
			$this->tpl->set_block('T_pref_blocks','B_tr_long_desc','V_tr_long_desc');
			$this->tpl->set_block('T_pref_blocks','B_tr_textarea','V_tr_textarea');
			$this->tpl->set_block('T_pref_blocks','B_tr_textbox','V_tr_textbox');
			$this->tpl->set_block('T_pref_blocks','B_tr_passwordbox','V_tr_passwordbox');
			$this->tpl->set_block('T_pref_blocks','B_tr_combobox','V_tr_combobox');
			$this->tpl->set_block('T_pref_blocks','B_tr_checkbox','V_tr_checkbox');
			$this->tpl->set_block('T_pref_blocks','B_submit_btn_only','V_submit_btn_only');
			$this->tpl->set_block('T_pref_blocks','B_submit_and_cancel_btns','V_submit_and_cancel_btns');

			$var = Array(
				'pref_errors'		=> '',
				'page_title'		=> lang('E-Mail Extra Accounts'),
				'form_action'		=> $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction'	=> 'email.bopreferences.ex_accounts_edit'
					)
				),
				'th_bg'			=> $this->theme['th_bg'],
				'left_col_width'	=> '50%',
				'right_col_width'	=> '50%',
				'checked_flag'		=> 'True',
				'ex_acctnum_varname'	=> 'ex_acctnum',
				'ex_acctnum_value'	=> $this->bo->acctnum,
				// this says we are submitting extra acount pref data
				'btn_submit_name'	=> $this->bo->submit_token_extra_accounts,
				'btn_submit_value'	=> lang('submit'),
				'btn_cancel_name'	=> 'cancel',
				'btn_cancel_value'	=> lang('cancel'),
				'btn_cancel_url'	=> $GLOBALS['phpgw']->link('/index.php',
					Array(
						'menuaction'	=> 'email.uipreferences.ex_accounts_list'
					)
				)
			);
			$this->tpl->set_var($var);

			// this will fill the $this->bo->std_prefs[] and cust_prefs[]  "schema" arrays
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: calling $this->bo->init_available_prefs() to init $this->bo->std_prefs[] and cust_prefs[]  "schema" arrays<br />'; }
			$this->bo->init_available_prefs();

			if ($this->debug > 3) { echo 'email.uipreferences.ex_accounts_edit: initiated schema dump:'; $this->bo->debug_dump_prefs(); }

			// initialize a local var to hold the cumulative main block data
			$prefs_ui_rows = '';

			// ---  Extra Account Pref Items  ---
			// section title
			$this->tpl->set_var('section_title', '*** '.lang('E-Mail Extra Account').' *** '.lang('Number').' '.$this->bo->acctnum);
			//This checks if we are all ready displaying the help and gives us the option of hiding it.
			// https://brick.earthlink.net/mail/index.php?menuaction=email.uipreferences.ex_accounts_edit&ex_acctnum=2
			if ((isset($GLOBALS['phpgw']->msg->ref_GET['show_help']))
				&& ($GLOBALS['phpgw']->msg->ref_GET['show_help']))
			{
				// link to display verbose help text
				$show_help_lnk = $GLOBALS['phpgw']->msg->href_maketag(
					$GLOBALS['phpgw']->link('/index.php',
						Array('menuaction'	=> 'email.uipreferences.preferences',
							'ex_acctnum'	=> $this->bo->acctnum)
					),
					lang('Hide Help'));
			} else {
				// link to hide verbose help text
				$show_help_lnk = $GLOBALS['phpgw']->msg->href_maketag(
					$GLOBALS['phpgw']->link('/index.php',
						Array('menuaction'	=> 'email.uipreferences.preferences',
							'ex_acctnum'	=> $this->bo->acctnum,
							'show_help'		=> '1')
					),
					lang('Show Help'));
			}
			$this->tpl->set_var('show_help_lnk', $show_help_lnk);
			// parse the block, and put into a local variable
			$done_widget = $this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
			// add the finished widget row to the main block variable
			$prefs_ui_rows .= $done_widget;

			// instructions: fill in everything you need
			$this->tpl->set_var('section_title', lang('Please fill in everything you need'));
			// parse the block,
			$done_widget = $this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
			// get the parsed data and put into a local variable
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: about to generate the html for standard email prefs block<br />'; }
			// add the finished widget row to the main block variable
			$prefs_ui_rows .= $done_widget;

			// generate Std Prefs HTML Block
			$prefs_ui_rows .= $this->create_prefs_block($this->bo->std_prefs);

			// ---  Custom Prefs  ---
			$this->tpl->set_var('section_title', lang('Custom E-Mail Settings').' &#040;'.lang('required').'&#041;');
			$done_widget = $this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
			$prefs_ui_rows .= $done_widget;
			// ---  Custom Prefs INSTRUCTIONS ---
			$this->tpl->set_var('section_title', lang('fill in as much as you can'));
			$done_widget = $this->tpl->parse('V_tr_sec_title','B_tr_sec_title');
			$prefs_ui_rows .= $done_widget;
			// generate Custom Prefs HTML Block
			if ($this->debug > 1) { echo 'email.uipreferences.ex_accounts_edit: about to generate the html for custom email prefs block<br />'; }
			$prefs_ui_rows .= $this->create_prefs_block($this->bo->cust_prefs);

			// blank row
			$this->tpl->set_var('back_color', $this->theme['bg_color']);
			$done_widget = $this->tpl->parse('V_tr_blank','B_tr_blank');
			$prefs_ui_rows .= $done_widget;

			// ---  Commit HTML Prefs rows to Main Template
			// put all widget rows data into the template var
			$this->tpl->set_var('prefs_ui_rows', $prefs_ui_rows);

			// Submit Button and Cancel button
			$submit_btn_row = $this->tpl->parse('V_submit_and_cancel_btns','B_submit_and_cancel_btns');
			$this->tpl->set_var('submit_btn_row', $submit_btn_row);

			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());

			// output the template
			if ($this->debug > 0) { echo 'email.uipreferences.ex_accounts_edit: LEAVING, about to output the template<br />'; }
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$this->tpl->pfp('out','T_prefs_ui_out');
			}
			else
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('E-Mail Extra Accounts');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('E-Mail Extra Accounts');
				$data['email_page'] = $this->tpl->parse('out','T_prefs_ui_out');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				$GLOBALS['phpgw']->xslttpl->pp();
			}
		}


		function ex_accounts_list()
		{
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				// we point to the global template for this version of phpgw templatings
				$this->tpl =& $GLOBALS['phpgw']->template;
				//$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			}
			else
			{
				// we use a PRIVATE template object for 0.9.14 conpat and during xslt porting
				$this->tpl = CreateObject('phpgwapi.template',PHPGW_APP_TPL);
			}

			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				unset($GLOBALS['phpgw_info']['flags']['noheader']);
				unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
				$GLOBALS['phpgw_info']['flags']['noappheader'] = True;
				$GLOBALS['phpgw_info']['flags']['noappfooter'] = True;
				$GLOBALS['phpgw']->common->phpgw_header(true);
				$this->tpl->set_root(PHPGW_APP_TPL);
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('app_data'));
			}

			$this->tpl->set_file(
				Array(
					'T_prefs_ex_accounts'	=> 'class_prefs_ex_accounts.tpl'
				)
			);
			$this->tpl->set_block('T_prefs_ex_accounts','B_accts_list','V_accts_list');

			$var = Array(
				'pref_errors'		=> '',
				'font'				=> $this->theme['font'],
				'tr_titles_color'	=> $this->theme['th_bg'],
				'tr_titles_class'	=> 'th',
				'page_title'		=> lang('E-Mail Extra Accounts List'),
				'account_name_header' => lang('Account Name'),
				'lang_status'		=> lang('Status'),
				'lang_go_there'		=> lang('Read Mail'),
				'lang_edit'			=> lang('Edit'),
				'lang_delete'		=> lang('Delete')
			);
			$this->tpl->set_var($var);

			$acctount_list = array();
			$acctount_list = $this->bo->ex_accounts_list();

			// here's what we get back
			//$acctount_list[$X]['acctnum']
			//$acctount_list[$X]['status']
			//$acctount_list[$X]['display_string']
			//$acctount_list[$X]['go_there_url']
			//$acctount_list[$X]['go_there_href']
			//$acctount_list[$X]['edit_url']
			//$acctount_list[$X]['edit_href']
			//$acctount_list[$X]['delete_url']
			//$acctount_list[$X]['delete_href']

			if ($this->debug) { echo 'email: uipreferences.ex_accounts_list: $acctount_list dump<pre>'; print_r($acctount_list); echo '</pre>'; }

			$tr_color = $this->theme['row_off'];
			$loops = count($acctount_list);
			if ($loops == 0)
			{
				$nothing = '&nbsp;';
				//$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
				$tr_color = $this->theme['row_on'];
				$tr_color_class = 'row_on';
				$this->tpl->set_var('tr_color',$tr_color);
				$this->tpl->set_var('tr_color_class',$tr_color_class);
				$this->tpl->set_var('indentity',$nothing);
				$this->tpl->set_var('status',$nothing);
				$this->tpl->set_var('go_there_href',$nothing);
				$this->tpl->set_var('edit_href',$nothing);
				$this->tpl->set_var('delete_href',$nothing);
				$this->tpl->parse('V_accts_list','B_accts_list');
			}
			else
			{
				for($i=0; $i < $loops; $i++)
				{
					//$tr_color = $this->nextmatchs->alternate_row_color($tr_color);
					$tr_color = (($i + 1)/2 == floor(($i + 1)/2)) ? $this->theme['row_off'] : $this->theme['row_on'];
					$tr_color_class = (($i + 1)/2 == floor(($i + 1)/2)) ? 'row_off' : 'row_on';
					$this->tpl->set_var('tr_color',$tr_color);
					$this->tpl->set_var('tr_color_class',$tr_color_class);
					$this->tpl->set_var('indentity',$acctount_list[$i]['display_string']);
					$this->tpl->set_var('status',$acctount_list[$i]['status']);
					$this->tpl->set_var('go_there_href',$acctount_list[$i]['go_there_href']);
					$this->tpl->set_var('edit_href',$acctount_list[$i]['edit_href']);
					$this->tpl->set_var('delete_href',$acctount_list[$i]['delete_href']);
					$this->tpl->parse('V_accts_list','B_accts_list', True);
				}
			}
			$add_new_acct_url = $GLOBALS['phpgw']->link(
									'/index.php',array(
									 'menuaction'=>'email.uipreferences.ex_accounts_edit',
									'ex_acctnum'=>$this->bo->add_new_account_token));
			$add_new_acct_href = '<a href="'.$add_new_acct_url.'">'.lang('New Account').'</a>';
			$this->tpl->set_var('add_new_acct_href',$add_new_acct_href);

			$done_url = $GLOBALS['phpgw']->link(
									'/preferences/index.php');
			$done_href = '<a href="'.$done_url.'">'.lang('Done').'</a>';
			$this->tpl->set_var('done_href',$done_href);

			// new way to handle debug data, if there is debug data, this will put it in the template source data vars
			$this->tpl->set_var('debugdata', $GLOBALS['phpgw']->msg->dbug->notice_pagedone());

			// output the template
			if ($GLOBALS['phpgw']->msg->phpgw_before_xslt)
			{
				$this->tpl->set_unknowns('comment');
				//$this->tpl->set_unknowns('remove');
				$this->tpl->pfp('out','T_prefs_ex_accounts');
			}
			else
			{
				$this->tpl->set_unknowns('remove');
				$data = array();
				//$data['appname'] = lang('E-Mail');
				//$data['function_msg'] = lang('E-Mail Extra Accounts List');
				$GLOBALS['phpgw_info']['flags']['email']['app_header'] = lang('E-Mail') . ': ' . lang('E-Mail Extra Accounts List');
				//$data['email_page'] = $this->tpl->parse('out','T_prefs_ex_accounts');
				$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('generic_out' => $data));
				//$GLOBALS['phpgw']->xslttpl->pp();
			}
		}
	}

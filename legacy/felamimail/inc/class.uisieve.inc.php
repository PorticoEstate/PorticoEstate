<?php
	/***************************************************************************\
	* phpGroupWare - FeLaMiMail                                                 *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.phpgroupware.org                                               *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; either version 2 of the License, or (at your    *
	* option) any later version.                                                *
	\***************************************************************************/
	/* $Id$ */

	class uisieve
	{

		var $public_functions = array
		(
			'mainScreen'		=> 'True',
			'editScript'		=> 'True',
			'deleteScript'		=> 'True',
			'activateScript'	=> 'True',
			'saveScript'		=> 'True'
		);

		function uisieve()
		{
			
			// get posted variables
/*			$this->urlMailbox	= urldecode($GLOBALS['HTTP_GET_VARS']['mailbox']);
			
			$this->bofelamimail	= CreateObject('felamimail.bofelamimail',$this->urlMailbox);
			$this->bofilter		= CreateObject('felamimail.bofilter');
			$this->sessionData	= $this->bofelamimail->sessionData;
*/
			$this->t 		= CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
			$this->t->set_unknowns('remove');
			$config = CreateObject('phpgwapi.config','felamimail');
			$config->read_repository();
			$felamimailConfig = $config->config_data;
			unset($config);
			                                                                        
			$sieveHost		= $felamimailConfig["sieveServer"];
			$sievePort		= $felamimailConfig["sievePort"];
			$username		= $GLOBALS['phpgw_info']['user']['userid'];
			$password		= $GLOBALS['phpgw_info']['user']['passwd'];
			$this->sieve		= CreateObject('felamimail.sieve',$sieveHost, $sievePort, $username, $password);
			if($this->sieve->sieve_login())
			{
				#print "looks good<br>";
			}
			
			$this->rowColor[0] = $GLOBALS['phpgw_info']["theme"]["bg01"];
			$this->rowColor[1] = $GLOBALS['phpgw_info']["theme"]["bg02"];

		}
		
		function activateScript()
		{
			$scriptName = $GLOBALS['HTTP_GET_VARS']['script'];
			if(!empty($scriptName))
			{
				if($this->sieve->sieve_setactivescript($scriptName))
				{
					#print "Successfully changed active script!<br>";
				}
				else
				{
					#print "Unable to change active script!<br>";
					/* we could display the full output here */
				}
			}
                    
			$this->mainScreen();
		}
		
		function display_app_header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function deleteScript()
		{
			$scriptName = $GLOBALS['HTTP_GET_VARS']['script'];
			if(!empty($scriptName))
			{
				if($this->sieve->sieve_deletescript($scriptName))
				{
					# alles ok!
				}
			}
			
			$this->mainScreen();
		}
		
		function editScript()
		{
			$scriptName = $GLOBALS['HTTP_GET_VARS']['script'];
			if(!empty($scriptName))
			{
				if($this->sieve->sieve_getscript($scriptName))
				{
					#print "Successfully changed active script!<br>";
					if(is_array($this->sieve->response))
					{
						#print_r($this->sieve->response);
						reset($this->sieve->response);
						while(list($key,$value)=each($this->sieve->response))
						{
							$filter .= $value;
						}
						$this->scriptToEdit 	= $scriptName;
						$this->scriptContent 	= $filter;
					}
				}
				else
				{
					#print "Unable to change active script!<br>";
					/* we could display the full output here */
				}
			}
                    
			$this->mainScreen();
		}

		function mainScreen()
		{
			// display the header
			$this->display_app_header();
			
			// initialize the template
			$this->t->set_file(array("filterForm" => "sieveForm.tpl"));
			$this->t->set_block('filterForm','header');
			$this->t->set_block('filterForm','scriptrow');
			
			// translate most of the parts
			$this->translate();
			
			$this->sieve->sieve_listscripts();
			if(is_array($this->sieve->response))
			{
				$i = 1;
				while(list($key,$value)=each($this->sieve->response))
				{
					$this->t->set_var("scriptnumber",$i);
					$this->t->set_var("scriptname",$value);

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.deleteScript',
						'script'	=> $value
					);
					$this->t->set_var('link_deleteScript',$GLOBALS['phpgw']->link('/index.php',$linkData));
					
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.editScript',
						'script'	=> $value
					);
					$this->t->set_var('link_editScript',$GLOBALS['phpgw']->link('/index.php',$linkData));

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.activateScript',
						'script'	=> $value
					);
					$this->t->set_var('link_activateScript',$GLOBALS['phpgw']->link('/index.php',$linkData));

					if($this->sieve->response["ACTIVE"]==$value)
					{
						$this->t->set_var('active','*');
					}
					else
					{
						$this->t->set_var('active','');
					}
					                
					$this->t->parse('scriptrows','scriptrow',true);
					$i++;
				}
			}
			else
			{
				$this->t->set_var("scriptrows",'');
			}
			if(isset($this->scriptToEdit))
			{
				$this->t->set_var("editScriptName",$this->scriptToEdit);
				$this->t->set_var("scriptContent",$this->scriptContent);
			}
			else
			{
				$this->t->set_var("editScriptName",'');
				$this->t->set_var("scriptContent",'');
			}
	                $linkData = array
	                (
	                        'menuaction'    => 'felamimail.uisieve.saveScript'
	                );
			$this->t->set_var('formAction',$GLOBALS['phpgw']->link('/index.php',$linkData));
	                $linkData = array
	                (
	                        'menuaction'    => 'felamimail.uisieve.mainScreen'
	                );
			$this->t->set_var('link_newScript',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$this->t->pparse("out","header");
			
			$this->sieve->sieve_logout();
		}
		
		function saveScript()
		{
			$scriptName = $GLOBALS[HTTP_POST_VARS]['scriptName'];
			$scriptContent = $GLOBALS[HTTP_POST_VARS]['scriptContent'];
			if(isset($scriptName) and isset($scriptContent))
			{
				if($this->sieve->sieve_sendscript($scriptName, stripslashes($scriptContent)))
				{
					#print "Successfully loaded script onto server. (Remember to set it active!)<br>";
				}
				else
				{
/*					print "Unable to load script to server.  See server response below:<br><blockquote><font color=#aa0000>";
					if(is_array($sieve->error_raw))
					foreach($sieve->error_raw as $error_raw)
						print $error_raw."<br>";
					else
						print $sieve->error_raw."<br>";
						print "</font></blockquote>";
						$textarea=stripslashes($script);
						$textname=$scriptname;
						$titleline="Try editing the script again! <a href=$PHP_SELF>Create new script</a>";*/
				}
			}
			$this->mainScreen();
		}

		function translate()
		{
			$this->t->set_var("lang_message_list",lang('Message List'));
			$this->t->set_var("lang_from",lang('from'));
			$this->t->set_var("lang_to",lang('to'));
			$this->t->set_var("lang_edit",lang('edit'));
			$this->t->set_var("lang_delete",lang('delete'));
			$this->t->set_var("lang_subject",lang('subject'));
			$this->t->set_var("lang_filter_active",lang('filter active'));
			$this->t->set_var("lang_filter_name",lang('filter name'));
			$this->t->set_var("lang_new_filter",lang('new filter'));
			$this->t->set_var("lang_no_filter",lang('no filter'));
			$this->t->set_var("lang_activate",lang('activate'));

			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}
}
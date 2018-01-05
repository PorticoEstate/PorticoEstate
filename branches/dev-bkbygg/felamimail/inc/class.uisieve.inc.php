<?php
	/***************************************************************************\
	* eGroupWare - FeLaMiMail                                                   *
	* http://www.linux-at-work.de                                               *
	* http://www.phpgw.de                                                       *
	* http://www.egroupware.org                                                 *
	* Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	* -------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it   *
	* under the terms of the GNU General Public License as published by the     *
	* Free Software Foundation; version 2 of the License.                       *
	\***************************************************************************/
	/* $Id$ */

	class uisieve
	{

		var $public_functions = array
		(
			'activateScript'	=> True,
			'addScript'		=> True,
			'deactivateScript'	=> True,
			'decreaseFilter'	=> True,
			'deleteScript'		=> True,
			'editRule'		=> True,
			'editScript'		=> True,
			'editVacation'		=> True,
			'increaseFilter'	=> True,
			'listScripts'		=> True,
			'listRules'		=> True,
			'updateRules'		=> True,
			'updateVacation'	=> True,
			'saveVacation'		=> True,
			'selectFolder'		=> True,
			'editEmailNotification'           => True, // Added email notifications
		);
		
		/**
		 * Flag if we can do a timed vaction message, requires Cyrus Admin User/Pw to enable/disable via async service
		 *
		 * @var boolean
		 */
		var $timed_vacation;
		//var $scriptName = 'felamimail';
		
		/**
		 * @var bosieve
		 */
		var $bosieve;
		
		var $errorStack;

		function __construct()
		{
			if(empty($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['sieveScriptName'])) {
				$GLOBALS['phpgw']->preferences->add('felamimail','sieveScriptName','felamimail', 'forced');
				$GLOBALS['phpgw']->preferences->save_repository();
			}
			$this->scriptName = (!empty($GLOBALS['phpgw_info']['user']['preferences']['felamimail']['sieveScriptName'])) ? $GLOBALS['phpgw_info']['user']['preferences']['felamimail']['sieveScriptName'] : 'felamimail' ;

			$this->displayCharset	= 'utf-8';

			$this->t 		=& CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
 			$this->botranslation	=& CreateObject('phpgwapi.translation');

			$this->bopreferences    =& CreateObject('felamimail.bopreferences');
			$this->mailPreferences  = $this->bopreferences->getPreferences();
			
			$config 		=& CreateObject('phpgwapi.config','felamimail');
			$config->read();
			$this->felamimailConfig	= $config->config_data;
			unset($config);
			
			$this->restoreSessionData();

			$icServer = $this->mailPreferences->getIncomingServer(0);
			
			if(is_a($icServer,'defaultimap') && $icServer->enableSieve) {
				$this->bosieve		=& CreateObject('felamimail.bosieve',$icServer);
				$this->timed_vacation = is_a($icServer,'cyrusimap') && $icServer->enableCyrusAdmin && 
					$icServer->adminUsername && $icServer->adminPassword;
			} else {
				die('Sieve not activated');
			}
			
			$this->rowColor[0] = $GLOBALS['phpgw_info']["theme"]["bg01"];
			$this->rowColor[1] = $GLOBALS['phpgw_info']["theme"]["bg02"];
		}
		
		function addScript() {
			if($scriptName = $_POST['newScriptName']) {
				#$script	=& CreateObject('felamimail.Script',$scriptName);
				#$script->updateScript($this->sieve);
				$this->bosieve->installScript($scriptName, '');

				// always activate and then edit the first script added
				#if ($this->sieve->listscripts() && count($this->sieve->scriptlist) == 1)
				#{
				#	if ($this->sieve->activatescript($scriptName))
				#	{
				#		$GLOBALS['phpgw']->redirect_link('/index.php',array(
				#			'menuaction' => 'felamimail.uisieve.editScript',
				#			'scriptname' => $scriptName,
				#		));
				#	}
				#}
			}
			
			$this->listScripts();
		}

#		function activateScript()
#		{
#			$scriptName = $_GET['scriptname'];
#			if(!empty($scriptName))
#			{
#				if($this->bosieve->setActive($scriptName))
#				{
#					#print "Successfully changed active script!<br>";
#				}
#				else
#				{
#					#print "Unable to change active script!<br>";
#					/* we could display the full output here */
#				}
#			}
#										
#			$this->listScripts();
#		}
		
		function buildRule($rule) 
		{
			$andor = ' '. lang('and') .' ';
			$started = 0;
			if ($rule['anyof']) $andor = ' '. lang('or') .' ';
			$complete = lang('IF').' ';
			if ($rule['unconditional']) $complete = "[Unconditional] ";
			
			if ($rule['from']) 
			{
				$match = $this->setMatchType($rule['from'],$rule['regexp']);
				$complete .= "'From:' " . $match . " '" . $rule['from'] . "'";
				$started = 1;
			}
			if ($rule['to']) 
			{
				if ($started) $complete .= $andor;
				$match = $this->setMatchType($rule['to'],$rule['regexp']);
				$complete .= "'To:' " . $match . " '" . $rule['to'] . "'";
				$started = 1;
			}
			if ($rule['subject']) 
			{
				if ($started) $complete .= $andor;
				$match = $this->setMatchType($rule['subject'],$rule['regexp']);
				$complete .= "'Subject:' " . $match . " '" . $rule['subject'] . "'";
				$started = 1;
			}
			if ($rule['field'] && $rule['field_val']) 
			{
				if ($started) $complete .= $andor;
				$match = $this->setMatchType($rule['field_val'],$rule['regexp']);
				$complete .= "'" . $rule['field'] . "' " . $match . " '" . $rule['field_val'] . "'";
				$started = 1;
			}
			if ($rule['size']) 
			{
				$xthan = " less than '";
				if ($rule['gthan']) $xthan = " greater than '";
				if ($started) $complete .= $andor;
				$complete .= "message " . $xthan . $rule['size'] . "KB'";
				$started = 1;
			}
			if (!$rule['unconditional']) $complete .= ' '.lang('THEN').' ';
			if (preg_match("/folder/i",$rule['action']))
				$complete .= lang('file into')." '" . $rule['action_arg'] . "';";
			if (preg_match("/reject/i",$rule['action']))
				$complete .= lang('reject with')." '" . $rule['action_arg'] . "'.";
			if (preg_match("/address/i",$rule['action']))
				$complete .= lang('forward to').' ' . $rule['action_arg'] .'.';
			if (preg_match("/discard/i",$rule['action']))
				$complete .= lang('discard').'.';
			if ($rule['continue']) $complete .= " [Continue]";
			if ($rule['keep']) $complete .= " [Keep a copy]";

			return $complete;
		}
		
		function buildVacationString($_vacation)
		{
#			global $script;
#			$vacation = $script->vacation;
			$vacation_str = '';
			if (!is_array($_vacation))
			{ 
				return @htmlspecialchars($vacation_str, ENT_QUOTES, 'utf-8'); 
			}
			
			$vacation_str .= lang('Respond');
			if (is_array($_vacation['addresses']) && $_vacation['addresses'][0])
			{
				$vacation_str .= ' ' . lang('to mail sent to') . ' ';
				$first = true;
				foreach ($_vacation['addresses'] as $addr)
				{
					if (!$first) $vacation_str .= ', ';
					$vacation_str .= $addr;
					$first = false;
				}
			}
			if (!empty($_vacation['days']))
			{
				$vacation_str .= ' ' . lang("every %1 days",$_vacation['days']);
			}
			$vacation_str .= ' ' . lang('with message "%1"',$_vacation['text']);
			return @htmlspecialchars($vacation_str, ENT_QUOTES, 'utf-8');
		}
		
		function checkRule($_vacation)
		{
			$this->errorStack = array();
			
			if (!$_vacation['text'])
			{
				$this->errorStack['text'] = lang('Please supply the message to send with auto-responses'.'!	');
			}

			if (!$_vacation['days'])
			{
				$this->errorStack['days'] = lang('Please select the number of days to wait between responses'.'!');
			}
			
			if(is_array($_vacation['addresses']))
			{
				$regexp="/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
				foreach ($_vacation['addresses'] as $addr)
				{
					if (!preg_match($regexp,$addr)) 
					{
						$this->errorStack['addresses'] = lang('One address is not valid'.'!');
					}
				}
			}
			else
			{
				$this->errorStack['addresses'] = lang('Please select a address'.'!');
			}
			
			if ($_vacation['status'] == 'by_date')
			{
				if (!$_vacation['start_date'] || !$_vacation['end_date'])
				{
					$this->errorStack['status'] = lang('Activating by date requires a start- AND end-date!');
				}
				elseif($_vacation['start_date'] > $_vacation['end_date'])
				{
					$this->errorStack['status'] = lang('Vacation start-date must be BEFORE the end-date!');
				}
			}
			
			if ($_vacation['forwards'])
			{
				foreach(preg_split('/, ?/',$_vacation['forwards']) as $addr)
				{
					if (!preg_match($regexp,$addr)) 
					{
						$this->errorStack['forwards'] = lang('One address is not valid'.'!');
					}					
				}
			}

			if(count($this->errorStack) == 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

#		// RalfBecker: that does obviously nothing
#		function deactivateScript()
#		{
#			$scriptName = get_var('scriptname',array('GET'));
#			if(!empty($scriptName))
#			{
#				#if($this->sieve->activatescript($scriptName))
#				#{
#				#	#print "Successfully changed active script!<br>";
#				#}
#				#else
#				#{
#				#	#print "Unable to change active script!<br>";
#				#	/* we could display the full output here */
#				#}
#			}
#										
#			$this->listScripts();
#		}
		
		function decreaseFilter()
		{
			$this->getRules();	/* ADDED BY GHORTH */
			$ruleID = get_var('ruleID',array('GET'));
			if ($this->rules[$ruleID] && $this->rules[$ruleID+1]) 
			{
				$tmp = $this->rules[$ruleID+1];
				$this->rules[$ruleID+1] = $this->rules[$ruleID];
				$this->rules[$ruleID] = $tmp;
			}
			
			$this->updateScript();
			
			$this->saveSessionData();
			
			$this->listRules();
		}

		function display_app_header() {
			if(preg_match('/^(vacation|filter)$/',get_var('editmode',array('GET'))))
				$editMode	= get_var('editmode',array('GET'));
			else
				$editMode	= 'filter';

			$GLOBALS['phpgw']->js->validate_file('tabs','tabs');
			$GLOBALS['phpgw']->js->validate_file('jscode','editProfile','felamimail');
			$GLOBALS['phpgw']->js->validate_file('jscode','listSieveRules','felamimail');
			$GLOBALS['phpgw']->js->set_onload("javascript:initAll('$editMode');");
			if($_GET['menuaction'] == 'felamimail.uisieve.editRule') {
				$GLOBALS['phpgw']->js->set_onunload('opener.fm_sieve_cancelReload();');
			}
			$GLOBALS['phpgw_info']['flags']['include_xajax'] = True;
			$GLOBALS['phpgw']->common->egw_header();

			switch($_GET['menuaction']) {
				case 'felamimail.uisieve.editRule':
					break;
				default:
					echo parse_navbar();
					break;
			}
		}
		
		function displayRule($_ruleID, $_ruleData) {
			// display the header
			$this->display_app_header();

			// initialize the template
			$this->t->set_file(array("filterForm" => "sieveEditForm.tpl"));
			$this->t->set_block('filterForm','main');

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uisieve.editRule',
				'ruleID'	=> $_ruleID
			);
			$this->t->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$linkData = array
			(
				'menuaction'	=> 'felamimail.uisieve.selectFolder',
			);
			$this->t->set_var('folder_select_url',$GLOBALS['phpgw']->link('/index.php',$linkData));

			
			if(is_array($_ruleData))
			{
				if($_ruleData['continue']) 
					$this->t->set_var('continue_checked','checked');
				if($_ruleData['keep']) 
					$this->t->set_var('keep_checked','checked');
				if($_ruleData['regexp']) 
					$this->t->set_var('regexp_checked','checked');
				$this->t->set_var('anyof_selected'.intval($_ruleData['anyof']),'selected');
				$this->t->set_var('value_from',htmlspecialchars($_ruleData['from'], ENT_QUOTES, 'utf-8'));
				$this->t->set_var('value_to',htmlspecialchars($_ruleData['to'], ENT_QUOTES, 'utf-8'));
				$this->t->set_var('value_subject',htmlspecialchars($_ruleData['subject'], ENT_QUOTES, 'utf-8'));
				$this->t->set_var('gthan_selected'.intval($_ruleData['gthan']),'selected');
				$this->t->set_var('value_size',$_ruleData['size']);
				$this->t->set_var('value_field',htmlspecialchars($_ruleData['field'], ENT_QUOTES, 'utf-8'));
				$this->t->set_var('value_field_val',htmlspecialchars($_ruleData['field_val'], ENT_QUOTES, 'utf-8'));
				$this->t->set_var('checked_action_'.$_ruleData['action'],'checked');
				$this->t->set_var('value_'.$_ruleData['action'],$_ruleData['action_arg']);
				if($_ruleData['action'] == 'folder')
				{
					$this->t->set_var('folderName',$_ruleData['action_arg']);
				}
			}
			$this->t->set_var('value_ruleID',$_ruleID);
			
			#$bofelamimail		=& CreateObject('felamimail.bofelamimail',$this->displayCharset);
			#$uiwidgets		=& CreateObject('felamimail.uiwidgets');
			#$connectionStatus	= $bofelamimail->openConnection();
			#$folders = $bofelamimail->getFolderObjects(false);
			
			#foreach($folders as $folderName => $folderDisplayName)
			#{
			#	$this->t->set_var('folderName',$folderName);
			#	$this->t->set_var('folderDisplayName',$folderDisplayName);
			#	$this->t->parse("folder_rows", 'folder', true); 
			#}
			
			// translate most of the parts
			$this->translate();
			$this->t->pfp("out","main");
		}
		
		function editRule()
		{
			$this->getRules();	/* ADDED BY GHORTH */

			$ruleType = get_var('ruletype',array('GET'));
			
			if(isset($_POST['anyof'])) {
				if(get_var('priority',array('POST')) != 'unset') {
					$newRule['priority']	= get_var('priority',array('POST'));
				}
				$ruleID 		= get_var('ruleID',array('POST'));
				if($ruleID == 'unset')
					$ruleID = count($this->rules);
				$newRule['priority']	= $ruleID*2+1;
				$newRule['status']	= 'ENABLED';
				$newRule['from']	= get_var('from',array('POST'));
				$newRule['to']		= get_var('to',array('POST'));
				$newRule['subject']	= get_var('subject',array('POST'));
				//$newRule[flg]		= get_var('???',array('POST'));
				$newRule['field']	= get_var('field',array('POST'));
				$newRule['field_val']	= get_var('field_val',array('POST'));
				$newRule['size']	= intval(get_var('size',array('POST')));
				$newRule['continue']	= get_var('continue',array('POST'));
				$newRule['gthan']	= intval(get_var('gthan',array('POST')));
				$newRule['anyof']	= intval(get_var('anyof',array('POST')));
				$newRule['keep']	= get_var('keep',array('POST'));
				$newRule['regexp']	= get_var('regexp',array('POST'));
				$newRule['unconditional'] = '0';		// what's this???
				
				$newRule[flg] = 0 ;
				if( $newRule['continue'] ) { $newRule[flg] += 1; }
				if( $newRule['gthan'] )    { $newRule[flg] += 2; }
				if( $newRule['anyof'] )    { $newRule[flg] += 4; }
				if( $newRule['keep'] )     { $newRule[flg] += 8; }
				if( $newRule['regexp'] )   { $newRule[flg] += 128; }
				
				switch(get_var('action',array('POST')))
				{
					case 'reject':
						$newRule['action']	= 'reject';
						$newRule['action_arg']	= get_var('reject',array('POST'));
						break;
						
					case 'folder':
						$newRule['action']	= 'folder';
						$newRule['action_arg']	= get_var('folder',array('POST'));
						#$newRule['action_arg']	= $GLOBALS['phpgw']->translation->convert($newRule['action_arg'], $this->charset, 'UTF7-IMAP');
						break;

					case 'address':
						$newRule['action']	= 'address';
						$newRule['action_arg']	= get_var('address',array('POST'));
						break;

					case 'discard':
						$newRule[action]	= 'discard';
						break;
				}
				if($newRule['action']) {

					$this->rules[$ruleID] = $newRule;

					$this->bosieve->setRules($this->scriptName, $this->rules);
					
					$this->saveSessionData();
				}
				
				if(isset($_POST['save'])) {
					print "<script type=\"text/javascript\">window.close();</script>";
				} else {
					$this->displayRule($ruleID, $newRule);
				}
			}
			else
			{
				if(isset($_GET['ruleID']))
				{
					$ruleID = get_var('ruleID',Array('GET'));
					$ruleData = $this->rules[$ruleID];
					$this->displayRule($ruleID, $ruleData);
				}
				else
				{
					$this->displayRule('unset', false);
				}
			#LK	$this->sieve->disconnet();
			}
		}
		
		function editVacation() {
			$preferences = ExecMethod('felamimail.bopreferences.getPreferences');
			
			$uiwidgets	=& CreateObject('felamimail.uiwidgets',PHPGW_APP_TPL);
			$boemailadmin	= new emailadmin_bo();

			if ($this->timed_vacation)
			{
				include_once(PHPGW_API_INC.'/class.jscalendar.inc.php');
				$jscal = new jscalendar();
			}
			if($this->bosieve->getScript($this->scriptName)) {
				if(PEAR::isError($error = $this->bosieve->retrieveRules($this->scriptName)) ) {
					$rules		= array();
					$vacation	= array();
				} else {
					$rules		= $this->bosieve->getRules($this->scriptName);
					$vacation	= $this->bosieve->getVacation($this->scriptName);
				}
			} else {
				// something went wrong
			}

			if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				// store text as default
				if (isset($_POST['set_as_default']))
				{
					$config =& new config('felamimail');
					$config->save_value('default_vacation_text',$_POST['vacation_text'],'felamimail');
				}
				$this->t->set_var('set_as_default','<input type="submit" name="set_as_default" value="'.htmlspecialchars(lang('Set as default')).'" />');
			}
			if(isset($_POST["vacationStatus"])) {
				$newVacation['text']		= get_var('vacation_text',array('POST'));
				$newVacation['text']		= $this->botranslation->convert($newVacation['text'],$this->displayCharset,'UTF-8');
				$newVacation['days']		= get_var('days',array('POST'));
				$newVacation['addresses']	= get_var('vacationAddresses',array('POST'));
				$newVacation['status']		= get_var('vacationStatus',array('POST'));
				$newVacation['forwards']    = get_var('vacation_forwards',array('POST'));
				if (!in_array($newVacation['status'],array('on','off','by_date'))) $newVacation['status'] = 'off';
				if ($this->timed_vacation) {
					$date = $jscal->input2date($_POST['start_date']); 
					if ($date['raw']) $newVacation['start_date'] = $date['raw']-12*3600;
					$date = $jscal->input2date($_POST['end_date']); 
					if ($date['raw']) $newVacation['end_date'] = $date['raw']-12*3600;
				}
				if(isset($_POST['save']) || isset($_POST['apply'])) {
					if($this->checkRule($newVacation)) { 
						if (!$this->bosieve->setVacation($this->scriptName, $newVacation)) {
							print "vacation update failed<br>";
							#print $script->errstr."<br>";
						}
					}
					else
					{
						$this->t->set_var('validation_errors',implode('<br />',$this->errorStack));
					}
				}
				$vacation = $newVacation;
				
				if(isset($_POST['save']) || isset($_POST['cancel'])) {
					$GLOBALS['phpgw']->redirect_link('/felamimail/index.php');
				}
			}

			$this->saveSessionData();

			// display the header
			$this->display_app_header();
			
			// initialize the template
			$this->t->set_file(array("filterForm" => "sieveForm.tpl"));
			$this->t->set_block('filterForm','vacation');

			// translate most of the parts
			$this->translate();
			
			// vacation status
			if($vacation['status'] == 'on') {
				$this->t->set_var('checked_active', 'checked');
			} elseif($vacation['status'] == 'off') {
				$this->t->set_var('checked_disabled', 'checked');
			}
				
			// vacation text
			if (empty($vacation['text'])) {
				$config =& new config('felamimail');
				$config = $config->read();
				$vacation['text'] = $config['default_vacation_text'];
			}
			$this->t->set_var('vacation_text',$this->botranslation->convert($vacation['text'],'UTF-8'));

			//vacation days
			if(empty($vacation)) {
				$this->t->set_var('selected_7', 'selected="selected"');
				// ToDO set default
				
			} else {
				$this->t->set_var('selected_'.$vacation['days'], 'selected="selected"');
			}
			$this->t->set_var('vacation_forwards',htmlspecialchars($vacation['forwards']));

			// vacation addresses
			if(is_array($vacation['addresses'])) {
				foreach($vacation['addresses'] as $address) {
					$selectedAddresses[$address] = $address;
				}
				asort($selectedAddresses);
			}
			

			$allIdentities = $preferences->getIdentity();
			foreach($allIdentities as $key => $singleIdentity) {
				if(empty($vacation) && $singleIdentity->default === true) {
					$selectedAddresses[$singleIdentity->emailAddress] = $singleIdentity->emailAddress;
				}
				$predefinedAddresses[$singleIdentity->emailAddress] = $singleIdentity->emailAddress;
			}
			asort($predefinedAddresses);

			$this->t->set_var('multiSelectBox',$uiwidgets->multiSelectBox(
					$selectedAddresses,
					$predefinedAddresses,
					'vacationAddresses',
					'400px'
				)
			);

			$linkData = array (
				'menuaction'	=> 'felamimail.uisieve.editVacation',
			);
			
			$this->t->set_var('vacation_action_url',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			if ($this->timed_vacation)
			{
				$this->t->set_var('by_date','<input type="radio" name="vacationStatus" value="by_date" id="status_by_date" '.
					($vacation['status']=='by_date'?' checked':'').' /> <label for="status_by_date">'.lang('by date').'</label>: '.
					$jscal->input('start_date',$vacation['start_date']).' - '.$jscal->input('end_date',$vacation['end_date']));
				$this->t->set_var('lang_help_start_end_replacement','<br />'.lang('You can use %1 for the above start-date and %2 for the end-date.','$$start$$','$$end$$'));
			}
			$this->t->pfp('out','vacation');
			
		#LK	$this->bosieve->disconnect();
		}

		function editEmailNotification() {
			$preferences = ExecMethod('felamimail.bopreferences.getPreferences');

			$uiwidgets  =& CreateObject('felamimail.uiwidgets',PHPGW_APP_TPL);
			$boemailadmin = new emailadmin_bo();

			if($this->bosieve->getScript($this->scriptName)) {
				if(PEAR::isError($error = $this->bosieve->retrieveRules($this->scriptName)) ) {
					$rules    = array();
					$emailNotification = array();
				} else {
					$rules    = $this->bosieve->getRules($this->scriptName);
					$emailNotification = $this->bosieve->getEmailNotification($this->scriptName);
				}
			} else {
				// something went wrong
			}


			// perform actions
			if (isset($_POST["emailNotificationStatus"])) {
				if (isset($_POST['save']) || isset($_POST['apply'])) {
					//$newEmailNotification['text']          = $this->botranslation->convert($newVacation['text'],$this->displayCharset,'UTF-8');
					$newEmailNotification['status']          = get_var('emailNotificationStatus',array('POST')) == 'disabled' ? 'off' : 'on';
					$newEmailNotification['externalEmail']   = get_var('emailNotificationExternalEmail',array('POST'));
					$newEmailNotification['displaySubject']   = get_var('emailNotificationDisplaySubject',array('POST'));
					if (!$this->bosieve->setEmailNotification($this->scriptName, $newEmailNotification)) {
						print lang("email notification update failed")."<br />";
						print $script->errstr."<br />";
					}
					$emailNotification = $newEmailNotification;
				}
				if (isset($_POST['save']) || isset($_POST['cancel'])) {
					$GLOBALS['phpgw']->redirect_link('/felamimail/index.php');
				}
			}

			$this->saveSessionData();

			// display the header
			$this->display_app_header();

			// initialize the template
			$this->t->set_file(array("filterForm" => "sieveForm.tpl"));
			$this->t->set_block('filterForm','email_notification');

			// translate most of the parts
			$this->translate();
			$this->t->set_var("lang_yes",lang('yes'));
			$this->t->set_var("lang_no",lang('no'));

			// email notification status
			if ($emailNotification['status'] == 'on') $this->t->set_var('checked_active', ' checked');
			else $this->t->set_var('checked_disabled', ' checked');

			// email notification display subject
			if ($emailNotification['displaySubject'] == '1') $this->t->set_var('checked_yes', ' checked');
			else $this->t->set_var('checked_no', ' checked');

			// email notification external email
			$this->t->set_var('external_email', $emailNotification['externalEmail']);

			$this->t->set_var('email_notification_action_url',$GLOBALS['phpgw']->link('/index.php','menuaction=felamimail.uisieve.editEmailNotification'));
			$this->t->pfp('out','email_notification');
		}

		function increaseFilter()
		{
			$this->getRules();	/* ADDED BY GHORTH */
			$ruleID = get_var('ruleID',array('GET'));
			if ($this->rules[$ruleID] && $this->rules[$ruleID-1]) 
			{
				$tmp = $this->rules[$ruleID-1];
				$this->rules[$ruleID-1] = $this->rules[$ruleID];
				$this->rules[$ruleID] = $tmp;
			}
			
			$this->updateScript();
			
			$this->saveSessionData();
			
			$this->listRules();
		}
		
		function listRules() 
		{
			$preferences = ExecMethod('felamimail.bopreferences.getPreferences');
			
			$uiwidgets	=& CreateObject('felamimail.uiwidgets', PHPGW_APP_TPL);
			$boemailadmin	= new emailadmin_bo();

			$this->getRules();	/* ADDED BY GHORTH */

			$this->saveSessionData();

			// display the header
			$this->display_app_header();
			
			// initialize the template
			$this->t->set_file(array('filterForm' => 'listRules.tpl'));
			$this->t->set_block('filterForm','header');
			$this->t->set_block('filterForm','filterrow');
			
			// translate most of the parts
			$this->translate();
			
			#if(!empty($this->scriptToEdit))
			#{
				$listOfImages = array(
					'up',
					'down'
				);
				foreach ($listOfImages as $image)
				{
					$this->t->set_var('url_'.$image,$GLOBALS['phpgw']->common->image('felamimail',$image));
				}
			
				$linkData = array
				(
					'menuaction'	=> 'felamimail.uisieve.editRule',
					'ruletype'	=> 'filter'
				);
				$this->t->set_var('url_add_rule',$GLOBALS['phpgw']->link('/index.php',$linkData));

				$linkData = array
				(
					'menuaction'	=> 'felamimail.uisieve.editRule',
					'ruletype'	=> 'vacation'
				);
				$this->t->set_var('url_add_vacation_rule',$GLOBALS['phpgw']->link('/index.php',$linkData));

				foreach ($this->rules as $ruleID => $rule)
				{
					$this->t->set_var('filter_status',lang($rule[status]));
					if($rule[status] == 'ENABLED')
					{
						$this->t->set_var('ruleCSS','sieveRowActive');
					}
					else
					{
						$this->t->set_var('ruleCSS','sieveRowInActive');
					}
					
					$this->t->set_var('filter_text',htmlspecialchars($this->buildRule($rule),ENT_QUOTES,'utf-8'));
					$this->t->set_var('ruleID',$ruleID);

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.editRule',
						'ruleID'	=> $ruleID,
					);
					$this->t->set_var('url_edit_rule',$GLOBALS['phpgw']->link('/index.php',$linkData));

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.increaseFilter',
						'ruleID'	=> $ruleID,
					);
					$this->t->set_var('url_increase',$GLOBALS['phpgw']->link('/index.php',$linkData));

					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.decreaseFilter',
						'ruleID'	=> $ruleID,
					);
					$this->t->set_var('url_decrease',$GLOBALS['phpgw']->link('/index.php',$linkData));
					
					$linkData = array
					(
						'menuaction'	=> 'felamimail.uisieve.updateRules',
					);
					$this->t->set_var('action_rulelist',$GLOBALS['phpgw']->link('/index.php',$linkData));

					$this->t->parse('filterrows','filterrow',true);
				}

			#}
			
			$linkData = array (
				'menuaction'    => 'felamimail.uisieve.saveScript'
			);
			$this->t->set_var('formAction',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$linkData = array (
				'menuaction'    => 'felamimail.uisieve.listRules'
			);
			$this->t->set_var('refreshURL',$GLOBALS['phpgw']->link('/index.php',$linkData));
			
			$linkData = array
			(
				'menuaction'	=> 'felamimail.uisieve.listScripts',
				'scriptname'	=> $scriptName
			);
			$this->t->set_var('url_back',$GLOBALS['phpgw']->link('/index.php',$linkData));

			$this->t->pfp("out","header");

		#LK	$this->bosieve->disconnect();
		}

		function restoreSessionData()
		{
			$sessionData = $GLOBALS['phpgw']->session->appsession('sieve_session_data');
			
			$this->rules		= $sessionData['sieve_rules'];
			$this->scriptToEdit	= $sessionData['sieve_scriptToEdit'];
		}
		
		function selectFolder()
		{
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXCommon');
			$GLOBALS['phpgw']->js->validate_file('dhtmlxtree','js/dhtmlXTree');                        
			$GLOBALS['phpgw']->js->validate_file('jscode','editSieveRule','felamimail');
			$GLOBALS['phpgw']->common->egw_header();

			$bofelamimail		=& CreateObject('felamimail.bofelamimail',$this->displayCharset);
			$uiwidgets		=& CreateObject('felamimail.uiwidgets');
			$connectionStatus	= $bofelamimail->openConnection();

			$folderObjects = $bofelamimail->getFolderObjects(false);
			$folderTree = $uiwidgets->createHTMLFolder
			(
				$folderObjects,
				'INBOX',
				0,
				lang('IMAP Server'),
				$mailPreferences['username'].'@'.$mailPreferences['imapServerAddress'],
				'divFolderTree',
				false,
				true
			);
			print '<div id="divFolderTree" style="overflow:auto; width:375px; height:474px; margin-bottom: 0px;padding-left: 0px; padding-top:0px; z-index:100; border : 1px solid Silver;"></div>';
			print $folderTree;
		}

		function setMatchType (&$matchstr, $regex = false)
		{
			$match = lang('contains');
			if (preg_match("/\s*!/", $matchstr))
				$match = lang('does not contain');
			if (preg_match("/\*|\?/", $matchstr))
			{
				$match = lang('matches');
				if (preg_match("/\s*!/", $matchstr))
					$match = lang('does not match');
			}
			if ($regex)
			{
				$match = lang('matches regexp');
				if (preg_match("/\s*!/", $matchstr))
					$match = lang('does not match regexp');
			}
			$matchstr = preg_replace("/^\s*!/","",$matchstr);
			
			return $match;
		}
		
		function saveScript() 
		{
			$scriptName 	= $_POST['scriptName'];
			$scriptContent	= $_POST['scriptContent'];
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

		function saveSessionData() 
		{
			$sessionData['sieve_rules']		= $this->rules;
			$sessionData['sieve_scriptToEdit']	= $this->scriptToEdit;
			
			$GLOBALS['phpgw']->session->appsession('sieve_session_data','',$sessionData);
		}
		
		function translate()
		{
			$this->t->set_var("lang_message_list",lang('Message List'));
			$this->t->set_var("lang_from",lang('from'));
			$this->t->set_var("lang_to",lang('to'));
			$this->t->set_var("lang_save",lang('save'));
			$this->t->set_var("lang_apply",lang('apply'));
			$this->t->set_var("lang_cancel",lang('cancel'));
			$this->t->set_var("lang_active",lang('active'));
			$this->t->set_var('lang_disabled',lang('disabled'));
			$this->t->set_var('lang_status',lang('status'));
			$this->t->set_var("lang_edit",lang('edit'));
			$this->t->set_var("lang_delete",lang('delete'));
			$this->t->set_var("lang_enable",lang('enable'));
			$this->t->set_var("lang_rule",lang('rule'));
			$this->t->set_var("lang_disable",lang('disable'));
			$this->t->set_var("lang_action",lang('action'));
			$this->t->set_var("lang_condition",lang('condition'));
			$this->t->set_var("lang_subject",lang('subject'));
			$this->t->set_var("lang_filter_active",lang('filter active'));
			$this->t->set_var("lang_filter_name",lang('filter name'));
			$this->t->set_var("lang_new_filter",lang('new filter'));
			$this->t->set_var("lang_no_filter",lang('no filter'));
			$this->t->set_var("lang_add_rule",lang('add rule'));
			$this->t->set_var("lang_add_script",lang('add script'));
			$this->t->set_var("lang_back",lang('back'));
			$this->t->set_var("lang_days",lang('days'));
			$this->t->set_var("lang_save_changes",lang('save changes'));
			$this->t->set_var("lang_extended",lang('extended'));
			$this->t->set_var("lang_edit_vacation_settings",lang('edit vacation settings'));
			$this->t->set_var("lang_every",lang('every'));
			$this->t->set_var('lang_respond_to_mail_sent_to',lang('respond to mail sent to'));
			$this->t->set_var('lang_filter_rules',lang('filter rules'));
			$this->t->set_var('lang_vacation_notice',lang('vacation notice'));
			$this->t->set_var("lang_with_message",lang('with message'));
			$this->t->set_var("lang_script_name",lang('script name'));
			$this->t->set_var("lang_script_status",lang('script status'));
			$this->t->set_var("lang_delete_script",lang('delete script'));
			$this->t->set_var("lang_check_message_against_next_rule_also",lang('check message against next rule also'));
			$this->t->set_var("lang_keep_a_copy_of_the_message_in_your_inbox",lang('keep a copy of the message in your inbox'));
			$this->t->set_var("lang_use_regular_expressions",lang('use regular expressions'));
			$this->t->set_var("lang_match",lang('match'));
			$this->t->set_var("lang_all_of",lang('all of'));
			$this->t->set_var("lang_any_of",lang('any of'));
			$this->t->set_var("lang_if_from_contains",lang('if from contains'));
			$this->t->set_var("lang_if_to_contains",lang('if to contains'));
			$this->t->set_var("lang_if_subject_contains",lang('if subject contains'));
			$this->t->set_var("lang_if_message_size",lang('if message size'));
			$this->t->set_var("lang_less_than",lang('less than'));
			$this->t->set_var("lang_greater_than",lang('greater than'));
			$this->t->set_var("lang_kilobytes",lang('kilobytes'));
			$this->t->set_var("lang_if_mail_header",lang('if mail header'));
			$this->t->set_var("lang_file_into",lang('file into'));
			$this->t->set_var("lang_forward_to_address",lang('forward to address'));
			$this->t->set_var("lang_send_reject_message",lang('send a reject message'));
			$this->t->set_var("lang_discard_message",lang('discard message'));
			$this->t->set_var("lang_select_folder",lang('select folder'));
			$this->t->set_var("lang_vacation_forwards",lang('Forward messages to').'<br />'.lang('(separate multiple addresses by comma)'));

			$this->t->set_var("bg01",$GLOBALS['phpgw_info']["theme"]["bg01"]);
			$this->t->set_var("bg02",$GLOBALS['phpgw_info']["theme"]["bg02"]);
			$this->t->set_var("bg03",$GLOBALS['phpgw_info']["theme"]["bg03"]);
		}
		
		function updateRules()
		{
			$this->getRules();	/* ADDED BY GHORTH */
			$action 	= get_var('rulelist_action',array('POST'));
			$ruleIDs	= get_var('ruleID',array('POST'));
			$scriptName 	= get_var('scriptname',array('GET'));
			
			switch($action)
			{
				case 'enable':
					if(is_array($ruleIDs))
					{
						foreach($ruleIDs as $ruleID)
						{
							$this->rules[$ruleID][status] = 'ENABLED';
						}
					}
					break;
					
				case 'disable':
					if(is_array($ruleIDs))
					{
						foreach($ruleIDs as $ruleID)
						{
							$this->rules[$ruleID][status] = 'DISABLED';
						}
					}
					break;
					
				case 'delete':
					if(is_array($ruleIDs))
					{
						foreach($ruleIDs as $ruleID)
						{
							unset($this->rules[$ruleID]);
						}
					}
					$this->rules = array_values($this->rules);
					break;
			}  
			
			$this->updateScript();
			
			$this->saveSessionData();
			
			$this->listRules();
		}

		function updateScript()
		{
			if (!$this->bosieve->setRules($this->scriptToEdit, $this->rules)) {
				print "update failed<br>";exit;
		#LK		print $script->errstr."<br>";
			}
		}
		
		/* ADDED BY GHORTH */
		function getRules()
		{
			if($script = $this->bosieve->getScript($this->scriptName)) {
				$this->scriptToEdit 	= $this->scriptName;
				if(PEAR::isError($error = $this->bosieve->retrieveRules($this->scriptName)) ) {
					$this->rules	= array();
					$this->vacation	= array();
				} else {
					$this->rules	= $this->bosieve->getRules($this->scriptName);
					$this->vacation	= $this->bosieve->getVacation($this->scriptName);
				}
			} else {
				// something went wrong
			}
		}
	}

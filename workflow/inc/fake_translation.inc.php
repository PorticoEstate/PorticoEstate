<?php

/* $Id: fake_translation.inc.php 21387 2006-04-17 07:33:31Z omgs $ */

/**
 * Fake Class to store all translation not detected by the translation tool
 *
 * @package workflow
 * @author regis.leroy@glconseil.com
 * @license GPL
 */

class fake_translation
{
	//public functions
	var $public_functions = array();

	function fake_translation()
	{
	}
	
	/**
	* If you have something which must be translated and which have no lang() call in the php files,
	* i.e.: a lang_something in a template or a lang call on a variable,
	* you can write it here for detection by the translation tool
	*/
	function nothing()
	{
                //user instances
                $nothing = lang('more options?');
                $nothing = lang('Add instances in exception');
                $nothing = lang('Add completed instances');
                $nothing = lang('Add aborted instances');
                $nothing = lang('Remove active instances');
                $nothing = lang('Instances selection');
                $nothing = lang('Activities selection');
                $nothing = lang('Actions');
                $nothing = lang('Add advanced actions');
                $nothing = lang('Reload filter');
                $nothing = lang('filter instance by id');
                $nothing = lang('warning this filter override all others filters');
                $nothing = lang('Release access to this activity');
                $nothing = lang('Assign me this activity');
                $nothing = lang('Exception this instance');
                $nothing = lang('Resume this exception instance');
                $nothing = lang('Abort this instance');
                $nothing = lang('Execute this activity');
                $nothing = lang('View this instance');
                
                //Workitems and Monitors
                $nothing = lang('Instance History');
                $nothing = lang('View Workitem');
                $nothing = lang('Monitor workitems');
                $nothing = lang('List of monitors');
                $nothing = lang('cleanup actions');
                $nothing = lang('remove all instances for this process');
                
                //sidebox
                $nothing = lang('Monitors');
                $nothing = lang('Workflow Preferences');
                $nothing = lang('Default config values');
                
                //Basics
                $nothing = lang('process:');
                $nothing = lang('activity:');
                $nothing = lang('process version:');
                $nothing = lang('instance:');
                $nothing = lang('started:');
                $nothing = lang('date:');
                $nothing = lang('user:');
                $nothing = lang('active:');
                $nothing = lang('valid:');
                $nothing = lang('search:');
                $nothing = lang('interactive:');
                $nothing = lang('type:');
                $nothing = lang('Act. status:');
                $nothing = lang('status:');
                $nothing = lang('routing:');

	}

}
?>
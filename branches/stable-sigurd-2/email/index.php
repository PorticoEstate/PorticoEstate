<?php
	/**
	* EMail
	*
	* @author Mark C3ushman <mark@cushman.net>
	* @author Angles <angles@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) xxxx Mark Cushman
	* @copyright Copyright (C) xxxx Angles
	* @copyright Copyright (C) 2003-2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id: index.php 17706 2006-12-17 11:21:02Z sigurdne $
	* @internal Based on Aeromail http://the.cushman.net/
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'email',
		'noheader'		=> true,
		'nofooter'		=> true,
		'nonavbar'		=> true,
		'noappheader'	=> true,
		'noappfooter'	=> true
	);
	
	/**
	* Include phpgroupware header
	*/
	include('../header.inc.php');
	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'email.uiindex.index'));


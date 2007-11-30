<?php
	/**************************************************************************\
	* phpGroupWare - Web Content Manager                                       *
	* http://www.phpgroupware.org                                              *
	* -------------------------------------------------                        *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/

	/* $Id: class.ui.inc.php 15965 2005-05-15 02:16:27Z skwashd $ */

	class ui
	{
		var $t;
		

		function ui()
		{
			$themesel = $GLOBALS['sitemgr_info']['themesel'];
			$templateroot = $GLOBALS['sitemgr_info']['site_dir'] . SEP . 'templates' . SEP . $themesel;
			$this->t = new Template3($templateroot);
		}

		function displayPageByName($page_name)
		{
			global $objbo;
			global $page;
			$objbo->loadPage($GLOBALS['Common_BO']->pages->so->PageToID($page_name));
			$this->generatePage();
		}

		function displayPage($page_id)
		{
			global $objbo;
			$objbo->loadPage($page_id);
			$this->generatePage();
		}

		function displayIndex()
		{
			global $objbo;
			$objbo->loadIndex();
			$this->generatePage();
		}

		function displayTOC($categoryid=false)
		{
			global $objbo;
			$objbo->loadTOC($categoryid);
			$this->generatePage();
		}

		function generatePage()
		{
			echo $this->t->parse();
		}

	}
?>

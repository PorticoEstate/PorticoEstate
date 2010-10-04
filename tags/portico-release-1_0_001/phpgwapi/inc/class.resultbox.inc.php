<?php
	/**
	* Creates result boxes using templates
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	CreateObject('phpgwapi.portalbox');

	/**
	* Creates result boxes using templates
	* 
	* @package phpgwapi
	* @subpackage gui
	*/
	class resultbox extends portalbox
	{
		/* 
		 Set up the Object. You will notice, we have not reserved memory
		 space for variables. In this circumstance it is not necessary.
		*/
		//constructor 
		function resultbox($title='', $primary='', $secondary='', $tertiary='')
		{
			$this->portalbox($title, $primary, $secondary, $tertiary);
			$this->setvar('outerwidth',400);
			$this->setvar('innerwidth',400);
		}

		/*
		 This is the only method within the class. Quite simply, as you can see
		 it draws the table(s), placing the required data in the appropriate place.
		*/
		function draw()
		{
			echo '<table border="'.$this->getvar('outerborderwidth')
				. '" cellpadding="0" cellspacing="0" width="' . $this->getvar('outerwidth')
				. '" bordercolor="' . $this->getvar('outerbordercolor')
				. '" bgcolor="' . $this->getvar('titlebgcolor') . '">';
			echo '<tr><td align="center">'.$this->getvar("title") . '</td></tr>';
			echo '<tr><td>';
			echo '<table border="0" cellpadding="0" cellspacing="0" width="'.$this->getvar('innerwidth')
				. '" bgcolor="' . $this->getvar('innerbgcolor') . '">';
			for ($x = 0; $x < count($this->data); $x++)
			{
				echo '<tr>';
				echo '<td width="50%">' . $this->data[$x][0] . '</td>';
				echo '<td width="50%">' . $this->data[$x][1] . '</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</td></tr>';
			echo '</table>';
		}
	}

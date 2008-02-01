<?php
	/**
	* Link box generator - Creates listboxes using templates
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000,2001 Mark Peters
	* @copyright Portions Copyright (C) 2002-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	CreateObject('phpgwapi.portalbox');

	/**
	* Link box generator - Creates listboxes using templates
	* 
	* @package phpgwapi
	* @subpackage gui
	*/
	class listbox extends portalbox
	{
		/*
		 Set up the Object. You will notice, we have not reserved
		 memory space for variables. In this circumstance it is not necessary.
		 */

		/*
		 This is the constructor for the listbox. The only thing this does
		 is to call the constructor of the parent class. Why? Well, whilst
		 PHP manages a certain part of OO, one of the bits it falls down on
		 (at the moment) is constructors within sub-classes. So, to
		 be sure that the sub-class is instantiated with the constructor of
		 the parent class, I simply call the parent constructor. Of course,
		 if I then wanted to override any of the values, I could easily do so.
		*/
		function listbox($param)
		{
			$this->setvar('classname','listbox');
			$this->setvar('outerwidth',300);
			$this->setvar('innerwidth',300);
			$this->setvar('width',300);

			if ( !is_array($param) )
			{
				$param = array();
			}

			foreach ( $param as $key => $value )
			{
				if($key != 'title' && $key != 'primary' && $key != 'secondary' && $key != 'tertiary')
				{
//echo 'Setting '.$key.':'.$value."<br>\n";
					$this->setvar($key, $value);
				}
			}
			$this->portalbox($param['title'], $param['primary'], $param['secondary'], $param['tertiary']);
			$this->start_template();
		}

		/*
		 This is the only method within the class. Quite simply, as you can see
		 it draws the table(s), placing the required data in the appropriate place.
		*/
		function draw($extra_data='')
		{
			if(count($this->data))
			{
				$this->p->parse('row','portal_listbox_header',True);

				for ($x = 0; $x < count($this->data); $x++)
				{
					$var = Array(
						'text'	=> $this->data[$x]['text'],
						'link'	=> $this->data[$x]['link']
					);
					$this->p->set_var($var);
					$this->p->parse('row','portal_listbox_link',True);
				}
				$this->p->parse('row','portal_listbox_footer',True);
			}
			$this->set_internal($extra_data);
			return $this->draw_box();
		}
	}

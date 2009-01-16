<?php
	/**
	* Class for creating select boxes for addresse, projects, array items, ...
	* @author Ralf Becker <RalfBecker@outdoor-training.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	/**
	* Include parent class
	* @see sbox
	*/
	phpgw::import_class('phpgwapi.sbox');

	/**
	* Class for creating select boxes for addresse, projects, array items, ...
	* 
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_sbox2 extends phpgwapi_sbox
	{
		public function __construct()
		{
			trigger_error('phpgwapi_sbox2 is no longer used, please port your code to phpgw_sbox', E_USER_NOTICE);
			parent::__construct();
		}
	}

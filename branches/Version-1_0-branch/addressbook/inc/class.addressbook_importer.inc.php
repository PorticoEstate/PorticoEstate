<?php
  /**************************************************************************\
  * phpGroupWare - uicategorize_contacts                                     *
  * http://www.phpgroupware.org                                              *
  * This program is part of the GNU project, see http://www.gnu.org/         *
  *                                                                          *
  * Copyright 2003 Free Software Foundation, Inc.                            *
  *                                                                          *
  * Originally Written by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * Current Maintained by Jonathan Alberto Rivera Gomez - jarg at co.com.mx  *
  * --------------------------------------------                             *
  * Development of this application was funded by http://www.sogrp.com       *
  * --------------------------------------------                             *
  *  This program is Free Software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	class addressbook_importer
	{
		var $record;
		
		function __construct()
		{
		}
		
		function person($element, $value, $args='')
		{
			$this->record[$element] = $value;
		}
		
		function location($element, $value, $args='')
		{
			$this->record['locations'][$args]['type'] = $args;
			$this->record['locations'][$args][$element] = $value;
		}
		
		function comms($element, $value, $args='')
		{
			$this->record['comm_media'][$element] = $value;
		}
		
		function notes($element, $value, $args='')
		{
			$this->record['notes'][$args]['type'] = $args;
			$this->record['notes'][$args][$element] = $value;
		}
	}

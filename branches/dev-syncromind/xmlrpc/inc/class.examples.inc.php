<?php
	/**
	* Notes
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	/**
	* examples business object class
	*
	* @package XMLRPC
	*/
	class xmlrpc_examples
	{
		var $public_functions = array
		(
			'findstate'			=> true
		);

		var $soap_functions = array(
			'findstate' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			)
		);

		public $xmlrpc_methods = array
		(
			array
			(
				'name'       => 'findstate',
				'decription' => 'When passed an integer between 1 and 51 returns the name of a US state, where the integer is the index of that state name in an alphabetic order.'
			)
		);

		function __construct()
		{
		}

		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'read' => array(
							'function'  => 'findstate',
							'signature' => array(array(xmlrpcInt,xmlrpcString)),
							'docstring' => 'When passed an integer between 1 and 51 returns the name of a US state, where the integer is the index of that state name in an alphabetic order.'
						),
				);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}


		function findstate($snv)
		{
			$stateNames = array(
			"Alabama", "Alaska", "Arizona", "Arkansas", "California",
			"Colorado", "Columbia", "Connecticut", "Delaware", "Florida",
			"Georgia", "Hawaii", "Idaho", "Illinois", "Indiana", "Iowa", "Kansas",
			"Kentucky", "Louisiana", "Maine", "Maryland", "Massachusetts", "Michigan",
			"Minnesota", "Mississippi", "Missouri", "Montana", "Nebraska", "Nevada",
			"New Hampshire", "New Jersey", "New Mexico", "New York", "North Carolina",
			"North Dakota", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island",
			"South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Vermont",
			"Virginia", "Washington", "West Virginia", "Wisconsin", "Wyoming"
			);

			$err="";
	
			// look it up in our array (zero-based)

			if (isset($stateNames[$snv-1]))
			{
				$sname=$stateNames[$snv-1];
			}
			else
			{
				// not, there so complain
				$err="I don't have a state for the index '" . $snv . "'";
			}

			// if we generated an error, create an error return response
			if ($err)
			{
				return $err;
			}
			else
			{
				// otherwise, we create the right response
				// with the state name
				return $sname;
			}
		}
	}

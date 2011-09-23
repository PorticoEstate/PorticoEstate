<?php
	/**
	 * phpGroupWare portico template class
	 *
	 * @author Jan Åge Johnsen <janaage@hikt.no>
	 * @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category gui
	 * @version $Id$
	 */

	/**
	 * Allows you to store and retrive session variables under through php or
	 * remote http JSON calls.
	 *
	 * @package phpgwapi
	 * @subpackage gui
	 * @category gui
	 */

	class phpgwapi_template_portico
	{
		public $public_functions = array
		(
			'store'			=> true,
			'retrive'		=> true
		);

		/******************************************************************************
		 * Stores JSON data through httpd request
		 *
		 * Returns HTTP header error code on failure or JSON data on success
		 *
		 * @param	location	request parameter (string) deciding where data should be stored
		 * @param	data		request (string) JSON payload
		 * @return	mixed		HTTP header error code on failure, JSON decoded payload on success
		 * @access	public
		 */
		public function store()
		{
			$location = phpgw::get_var('location');
			$data = phpgw::get_var('data', 'raw');

			if( $location == '' )
			{
				header('HTTP/1.0 406 Not Acceptable');
				return 'Missing location parameter';
			}

			$json = json_decode($data, true);

			if( $json == null )
			{
				header('HTTP/1.0 406 Not Acceptable');
				return 'Invalid JSON data parameter';
			}

			$GLOBALS['phpgw']->session->appsession("template_portico_$location", 'phpgwapi', $json);
			return $json;
		}

		/******************************************************************************
		 * Retrives stored data stored in session though httpd request
		 *
		 * Returns HTTP header error code on failure or stored data on success

		 * @param	location	request parameter (string) deciding which data to return
		 * @return	mixed		HTTP header error code on failure, stored data on success
		 * @access	public
		 */
		public function retrieve()
		{
			$location = phpgw::get_var('location');

			if( $location == '' )
			{
				header('HTTP/1.0 406 Not Acceptable');
				return 'Missing location parameter';
			}

			$data = self::retrieve_local($location);

			if ( $data == null )
			{
				header('HTTP/1.0 404 Not Found');
				return 'No data found on that location';
			}

			return $data;
		}

		/******************************************************************************
		 * Retrives and returns stored session variable from given location
		 *
		 * Returns string containing data on success, nothing on failure
		 *
		 * usage: retrive_local(string $location)
		 *
		 * @param	$location	string identifying location for data
		 * @return	string		stored data on success, noting on failure
		 * @access	public
		 */
		public static function retrieve_local($location)
		{
			return (array) $GLOBALS['phpgw']->session->appsession("template_portico_{$location}", 'phpgwapi');
		}

		/******************************************************************************
		 * Stores session variable on given location
		 *
		 * Returns string containing data on success, nothing on failure
 		 *
		 * usage: store_local(string $location, data)
		 *
		 * @param   $location	string identifying location for data
		 * @param	$data		payload to store
		 * @return	string		stored data on success, noting on failure
		 * @access	public
		 */
		public static function store_local($location, $data)
		{
			$GLOBALS['phpgw']->session->appsession("template_portico_$location", 'phpgwapi', $data);
		}
	}

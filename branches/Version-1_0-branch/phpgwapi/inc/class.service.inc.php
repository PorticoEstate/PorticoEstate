<?php
	/**
	* Services abstraction class
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 2001 Miles Lott
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Services abstraction class
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	abstract class service
	{
		var $provider = '';
		var $svc      = '';
		var $type     = '';
		var $function_map = array();

		function exec($service)
		{
			if(is_array($service))
			{
				$data     = $service[2];
				$function = $service[1];
				$service  = $service[0];
			}
			switch ($service)
			{
				case 'schedule':
				case 'contacts':
				case 'notes':
				case 'todo':
					$child = createObject('phpgwapi.service_' . $service);
					break;
				default:
					$child = createObject($service);
			}
			if($function)
			{
				return $child->$function($data);
			}
		}

		function list_methods()
		{
			return $this->function_map;
		}
	}
?>

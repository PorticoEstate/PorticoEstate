<?php
	/**
	* Services abstraction class for schedule
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 2001 Miles Lott
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Services abstraction class for schedule
	* 
	* @package phpgwapi
	* @subpackage application
	* @ignore
	*/
	class service_contacts extends service
	{
		function service_contacts()
		{
			$this->provider = $GLOBALS['phpgw_info']['schedule_service'] ? $GLOBALS['phpgw_info']['schedule_service'] : 'calendar';
			$this->svc = $this->provider . '.bo' . $this->provider;
			$type = $this->type ? $this->type : 'xmlrpc';
			$this->function_map = ExecMethod($this->svc . '.list_methods',$type);
		}

		function read($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['read_entry']['function'],$data);
		}

		function read_list($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['store_to_cache']['function'],$data);
		}

		function save($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['update']['function'],$data);
		}

		function add($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['update']['function'],$data);
		}

		function delete($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['delete_entry']['function'],$data);
		}

		function export($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['export_event']['function'],$data);
		}
	}
?>

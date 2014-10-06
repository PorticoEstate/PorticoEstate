<?php
	/**
	* Services abstraction class for contacts
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 2001 Miles Lott
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Services abstraction class for contacts
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class service_contacts extends service
	{
		function service_contacts()
		{
			$this->provider = $GLOBALS['phpgw_info']['contact_service'] ? $GLOBALS['phpgw_info']['contact_service'] : 'addressbook';
			$this->svc = $this->provider . '.bo' . $this->provider;
			$type = $this->type ? $this->type : 'xmlrpc';
			$this->function_map = ExecMethod($this->svc . '.list_methods',$type);
		}

		function read($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['read']['function'],$data);
		}

		function read_list($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['read_list']['function'],$data);
		}

		function save($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['save']['function'],$data);
		}

		function add($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['add']['function'],$data);
		}

		function delete($data)
		{
			return ExecMethod($this->svc . '.' . $this->function_map['delete']['function'],$data);
		}
	}
?>

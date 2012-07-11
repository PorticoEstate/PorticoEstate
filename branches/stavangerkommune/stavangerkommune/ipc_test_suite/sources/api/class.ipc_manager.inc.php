<?php
	/**
	* Manager of the IPC Layer
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	/**
	* Abstract IPC Application class for the IPC Layer
	*/
	include_class('ipc_');

	/**
	* Manager of the IPC Layer
	* @package phpgwapi
	* @subpackage communication
	*/
	class ipc_manager
	{
		/**
		 * @var array $_ipcObjectList  contains the created ipc application objects
		 * @access private
		 */
		var $_ipcObjectList;
	
	
		/**
		 * @var array $xmlrpc_methods  contains information for xmlrpc methods
		 * @access public
		 */
	  var $xmlrpc_methods;
	  
		/**
		 * constructor
		 */
		function ipc_manager()
		{
			$this->_ipcObjectList = array();
			$this->xmlrpc_methods = array();
			$this->xmlrpc_methods[] = array(
				'name'        => 'execIPC',
				'description' => 'Execute an ipc application methode'
			);
		}


		/**
		 * Get the ipc application object
		 *
		 * @access  public
		 * @param   string  $appName  name of the application
		 * @return  object            ipc object of application 
		 */
		function &getIPC($appName)
		{
			// check if app is available amd the acl run app right
	  	if ($this->_checkIPCApp($appName) == false)
	      return false;
	
			// create ipc class name
			$className = $this->_createIPCAppClassName($appName);
			
			// check if ipc app object exists
			if (isset($this->_ipcObjectList[$className]) && is_object($this->_ipcObjectList[$className]))
			{ // return the existing ipc app object
				return $this->_ipcObjectList[$className];
			}
			else
			{
				$obj =& CreateObject($className);
				if (is_object($obj) == true)
				{ // save and return the created ipc app object
					$this->_ipcObjectList[$className] =& $obj;
					return $this->_ipcObjectList[$className];
				}
				else
				{
				  return false;
				}
			}
		}


	  /**
	   * Destroy the ipc application object.
	   *
	   * @access  public
	   * @param   string   $appName  name of application
	   * @return  boolean            true when object was destroyed, otherwise false
	   */
	  function destroyIPC($appName)
	  {
			// create ipc class name
			$className = $this->_createIPCAppClassName($appName);
			
			// check if ipc app object exists
			if (isset($this->_ipcObjectList[$className]) == true)
			{ // destroy the ipc app object
				unset($this->_ipcObjectList[$className]);
				return true;
			}
			else
			{
			  return false;
			}
		}


	  /**
	   * Executes a ipc method.
	   *
	   * @access  public
	   * @param   string  $ipcAppMethod        name of the application and mathod to execute as '<appName>.<methodName>'
	   * @param   array   $ipcAppMethodParams  array with parameters for passing to the called method
	   * @return  mixed                        result of execution
	   */
		function execIPC($ipcAppMethod, $ipcAppMethodParams=null)
		{
			list($ipcApp, $ipcMethod) = explode('.', $ipcAppMethod);
			$ipc =& $this->getIPC($ipcApp);
			
			if (is_object($ipc) == false)
				return false;
			
			if (method_exists($ipc, $ipcMethod) == false)
				return false;
	
			$ipcParams = '';
			for($i=0; $i<count($ipcAppMethodParams); $i++)
			{
				if ($i>0)
					$ipcParams .= ', ';
				if(is_string($ipcAppMethodParams[$i]) == true)
					$ipcParams .= '\''.$ipcAppMethodParams[$i].'\'';
				else {
					if (is_array($ipcAppMethodParams[$i])) {
						$arrayconstructor = 'array(';
						$firstentry = true;
						foreach($ipcAppMethodParams[$i] as $key => $value) {
							if ($firstentry) 
								$firstentry = false;
							else
								$arrayconstructor .= ', ';
							$arrayconstructor .= "'$key' => '$value'";
						}
						$ipcParams .= $arrayconstructor . ')';
					}
					else
						$ipcParams .= $ipcAppMethodParams[$i];
				}
			}
	
			$ipc_cmd = '$ret = $ipc->'.$ipcMethod.'('.$ipcParams.');';
      error_log("$ipc_cmd");
			eval($ipc_cmd);
			return $ret;
		}

		/**
		 * Check if application is available and the acl run application right for the current user.
		 *
		 * @access  private
		 * @param   string   $appName  name of application
		 * @return  boolean            true if application is available and user has acl run right, otherwise false
		 */
		function _checkIPCApp($appName)
		{
			// 1: check if app is available
			if (isset($GLOBALS['phpgw']->applications->data[$appName]) == false)
			{
				return false;
			}
	
			// 2: check the acl run app right
			if ($GLOBALS['phpgw']->acl->check('run', 1, $appName) == false)
			{
				return false;
			}
	
			return true;
		}


		/**
		 * Create the name of the ipc application class for the passed application name.
		 *
		 * @access  private
		 * @param   string   $appName  name of application
		 * @return  string             name of tne ipc application class
		 */
		function _createIPCAppClassName($appName)
		{
			return $appName.'.ipc_'.$appName;
		}

	}
?>
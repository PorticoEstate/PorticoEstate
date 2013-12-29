<?php
/**
 * Has a few functions, but primary role is to load the phpgwapi
 * @author Dan Kuykendall <seek3r@phpgroupware.org>
 * @author Joseph Engo <jengo@phpgroupware.org>
 * @author Dave Hall skwashd phpgroupware.org
 * @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
 * @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
 * @package phpgwapi
 * @subpackage utilities
 * @version $Id$
 */

	/**
	* Require the phpgw class
	* @internal the phpgw class is a special case
	*/
	require_once PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/class.phpgw.inc.php';

	/**
	* Include object factory base class once
	*/
	phpgw::import_class('phpgwapi.object_factory');

	/*
	 * Direct functions which are not part of the API classes because they are required to be available at the lowest level.
	 *
	 */

	/**
	 * Allows for array and direct function params as well as sanatization.
	 *
	 * This function is used to validate param data as well as offer flexible function usage.
	 *
	 * @example
	 * <code>
	 *		function somefunc()
	 *		{
	 *			$expected_args[0] = Array('name'=>'fname','default'=>'joe', 'type'=>'string');
	 *			$expected_args[1] = Array('name'=>'mname','default'=>'hick', 'type'=>'string');
	 *			$expected_args[2] = Array('name'=>'lname','default'=>'bob', 'type'=>'string');
	 *			$recieved_args = func_get_args();
	 *			$args = safe_args($expected_args, $recieved_args,__LINE__,__FILE__);
	 *			echo 'Full name: '.$args['fname'].' '.$args['fname'].' '.$args['lname'].'<br>';
	 *			//default result would be:
	 *			// Full name: joe hick bob<br>
	 *		}
	 *	</code>
	 *
	 *	Using this it is possible to use the function in any of the following ways
	 *	somefunc('jack','city','brown');
	 *	or
	 *	somefunc(array('fname'=>'jack','mname'=>'city','lname'=>'brown'));
	 *	or
	 *	somefunc(array('lname'=>'brown','fname'=>'jack','mname'=>'city'));
	 *
	 *	For the last one, when using named params in an array you dont have to follow any order
	 *	All three would result in - Full name: jack city brown<br>
	 *
	 *	When you use this method of handling params you can secure your functions as well offer
	 *	flexibility needed for both normal use and web services use.
	 *	If you have params that are required just set the default as ##REQUIRED##
	 *	Users of your functions can also use ##DEFAULT## to use your default value for a param
	 *	when using the standard format like this:
	 *	somefunc('jack','##DEFAULT##','brown');
	 *	This would result in - Full name: jack hick brown<br>
	 *	Its using the default value for the second param.
	 *	Of course if you have the second param as a required field it will fail to work.
	 */
	function safe_args($expected, $recieved, $line='??', $file='??')
	{
		/* This array will contain all the required fields */
		$required = Array();

		/* This array will contain all types for sanatization checking */
		/* only used when an array is passed as the first arg          */
		$types = Array();

		/* start by looping thru the expected list and set params with */
		/* the default values                                          */
		$num = count($expected);
		for ($i = 0; $i < $num; $i++)
		{
			$args[$expected[$i]['name']] = $expected[$i]['default'];
			if ($expected[$i]['default'] === '##REQUIRED##')
			{
				$required[$expected[$i]['name']] = True;
			}
			$types[$expected[$i]['name']] = $expected[$i]['type'];
		}

		/* Make sure they passed at least one param */
		if(count($recieved) != 0)
		{
			/* if used as standard function we loop thru and set by position */
			if(!is_array($recieved[0]))
			{
				for ($i = 0; $i < $num; $i++)
				{
					if(isset($recieved[$i]) && $recieved[$i] !== '##DEFAULT##')
					{
						if(sanitize($recieved[$i],$expected[$i]['type']))
						{
							$args[$expected[$i]['name']] = $recieved[$i];
							unset($required[$expected[$i]['name']]);
						}
						else
						{
							echo 'Fatal Error: Invalid paramater type for '.$expected[$i]['name'].' on line '.$line.' of '.$file.'<br>';
							exit;
						}
					}
				}
			}
			/* if used as standard function we loop thru and set by position */
			else
			{
				for ($i = 0; $i < $num; $i++)
				{
					$types[$expected[$i]['name']] = $expected[$i]['type'];
				}
				while(list($key,$val) = each($recieved[0]))
				{
					if($val !== '##DEFAULT##')
					{
						if(sanitize($val,$types[$key]) == True)
						{
							$args[$key] = $val;
							unset($required[$key]);
						}
						else
						{
							echo 'Fatal Error: Invalid paramater type for '.$key.' on line '.$line.' of '.$file.'<br>';
							exit;
						}
					}
				}
			}
		}
		if(count($required) != 0)
		{
			while (list($key) = each($required))
			{
				echo 'Fatal Error: Missing required paramater '.$key.' on line '.$line.' of '.$file.'<br>';
			}
			exit;
		}
		return $args;
	}

	/**
	 * retrieve a value from either a POST, GET, COOKIE, SERVER or from a class variable.
	 *
	 * @author skeeter
	 * This function is used to retrieve a value from a user defined order of methods.
	 * $this->id = get_var('id',array('POST','GET','COOKIE','GLOBAL','DEFAULT'));
	 * @param $variable name
	 * @param $method ordered array of methods to search for supplied variable
	 * @param $default_value (optional)
	 */
	function get_var($variable, $method='any', $default_value='')
	{
		$methods = print_r($method, true);
		trigger_error("get_var(var = $variable, method = $methods, default = $default_value) has been replaced by phpgw::get_var(var, data_type, method, default), please update your code", E_USER_NOTICE);
		//echo '<pre>' . print_r(debug_backtrace(), true) . '</pre>'; // uncomment me to assist with debugging :)

		$var = null;
		if ( is_array($method) )
		{
			foreach ( $method as $req_type )
			{
				if ( in_array($req_type, array('GET', 'POST', 'COOKIE', 'SESSION', 'REQUEST')) )
				{
					$var = phpgw::get_var($variable, 'string', $req_type, $default_value);
					if ( $var )
					{
						return $var;
					}
				}
			}
			return null;
		}
		if ( $method == 'any' || $method == 'GLOBAL' )
		{
			$method == 'REQUEST';
		}
		return $var = phpgw::get_var($variable, 'string', $method, $default_value);
	}

	/**
	 * This will include an application class once and guarantee that it is loaded only once.  Similar to CreateObject, but does not instantiate the class.
	 *
	 * @example include_class('projects', 'ui_base');
	 * @param $module name of module
	 * @param $class_name name of class
	 * @param $include_path path to the module class, default is 'inc/', use this parameter i.e. if the class is located in a subdirectory like 'inc/base_classes/'
	 * @return boolean true if class is included, else false (false means class could not included)
	 */
	function include_class($module, $class_name, $includes_path = 'inc/')
	{
		if ( is_file(PHPGW_INCLUDE_ROOT . "/{$module}/{$includes_path}class.{$class_name}.inc.php") )
		{
			return require_once(PHPGW_INCLUDE_ROOT . "/{$module}/{$includes_path}class.{$class_name}.inc.php");
		}
		//trigger_error(lang('Unable to locate file: %1', "{$module}/{$includes_path}class.{$class_name}.inc.php"), E_USER_ERROR);
		return false;
	}

	/**
	 * delegate the object creation into the module.
	 *
	 * @author Dirk Schaller
	 * @author Phillip Kamps
	 * This function is used to create an instance of a class. Its delegates the creation process into the called module and its factory class. If a module has no factory class, then its use the old CreateObject method. The old CreateObject method is moved into the base object factory class.
	 * $GLOBALS['phpgw']->acl = createObject('phpgwapi.acl');
	 * @param $classname name of class
	 * @param $p1-$p16 class parameters (all optional)
	 */
	function createObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
	{

		list($appname, $classname) = explode('.', $class, 2);

		$of_classname = "of{$appname}";

		// include module object factory class
		if ( !include_class($appname, $of_classname) )
		{
			// fail to load module object factory -> use old CreateObject in base class
			$of_classname = 'phpgwapi_object_factory';
		}
		else
		{
			$of_classname = "{$appname}_{$of_classname}";
		}

		// because $of_classname::CreateObject() is not allowed, we use call_user_func
		return call_user_func("{$of_classname}::createObject", $class, $p1, $p2, $p3, $p4, $p5,
								$p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16);
	}

	/**
	 * Execute a function, and load a class and include the class file if not done so already.
	 *
	 * @author seek3r
	 * This function is used to create an instance of a class, and if the class file has not been included it will do so.
	 * @param $method to execute
	 * @param $functionparams function param should be an array
	 * @param $loglevel developers choice of logging level
	 * @param $classparams params to be sent to the contructor
	 * ExecObject('phpgwapi.acl.read');
	 */
	function ExecMethod($method, $functionparams = '_UNDEF_', $loglevel = 3, $classparams = '_UNDEF_')
	{
		/* Need to make sure this is working against a single dimensional object */
		$partscount = count(explode('.',$method)) - 1;
		if ($partscount == 2)
		{
			list($appname,$classname,$functionname) = explode(".", $method);
			$unique_class = "{$appname}_{$classname}";
			if ( !isset($GLOBALS['phpgw_classes'][$unique_class]) || !is_object($GLOBALS['phpgw_classes'][$unique_class]) )
			{
				if ($classparams != '_UNDEF_' && ($classparams || $classparams != 'True'))
				{
					$GLOBALS['phpgw_classes'][$unique_class] = createObject("{$appname}.{$classname}", $classparams);
				}
				else
				{
					$GLOBALS['phpgw_classes'][$unique_class] = createObject("{$appname}.{$classname}");
				}
			}

			if ( (is_array($functionparams) || is_object($functionparams) || $functionparams != '_UNDEF_')
				&& ($functionparams || $functionparams != 'True'))
			{
				return $GLOBALS['phpgw_classes'][$unique_class]->$functionname($functionparams);
			}
			else
			{
				return $GLOBALS['phpgw_classes'][$unique_class]->$functionname();
			}
		}
		/* if the $method includes a parent class (multi-dimensional) then we have to work from it */
		elseif ($partscount >= 3)
		{
			$GLOBALS['methodparts'] = explode(".", $method);
			$classpartnum = $partscount - 1;
			$appname = $GLOBALS['methodparts'][0];
			$classname = $GLOBALS['methodparts'][$classpartnum];
			$functionname = $GLOBALS['methodparts'][$partscount];
			/* Now I clear these out of the array so that I can do a proper */
			/* loop and build the $parentobject */
			unset ($GLOBALS['methodparts'][0]);
			unset ($GLOBALS['methodparts'][$classpartnum]);
			unset ($GLOBALS['methodparts'][$partscount]);
			reset ($GLOBALS['methodparts']);
			$firstparent = 'True';
			foreach ( $GLOBALS['methodparts'] as $key => $val )
			{
				if ($firstparent == 'True')
				{
					$parentobject = '$GLOBALS["'.$val.'"]';
					$firstparent = False;
				}
				else
				{
					$parentobject .= '->'.$val;
				}
			}
			unset($GLOBALS['methodparts']);

			if ( !isset($$parentobject->$classname)
				|| !is_object($$parentobject->$classname) )
			{
				if ($classparams != '_UNDEF_' && ($classparams || $classparams != 'True'))
				{
					$$parentobject->$classname = createObject($appname.'.'.$classname, $classparams);
				}
				else
				{
					$$parentobject = new stdClass();
					$$parentobject->$classname = createObject($appname.'.'.$classname);
				}
			}

			if ($functionparams != '_UNDEF_' && ($functionparams || $functionparams != 'True'))
			{
				return $$parentobject->$classname->$functionname($functionparams);
			}
			else
			{
				return $returnval = $$parentobject->$classname->$functionname();
			}
		}
		else
		{
			return 'error in parts';
		}
	}

	/**
	 * Return a properly formatted account_id.
	 *
	 * @author skeeter
	 * This function will return a properly formatted account_id. This can take either a name or an account_id as paramters. If a name is provided it will return the associated id.
	 * $account_id = get_account_id($accountid);
	 * @param $account_id either a name or an id
	 * @param $default_id either a name or an id
	 */
	function get_account_id($account_id = '', $default_id = null)
	{
		if ( gettype($account_id) == 'integer' && $account_id <> 0 )
		{
			return $account_id;
		}
		else if ( !$account_id )
		{
			if ( $default_id == null )
			{
				return isset($GLOBALS['phpgw_info']['user']['account_id']) ? $GLOBALS['phpgw_info']['user']['account_id'] : 0;
			}
			elseif (is_string($default_id))
			{
				return $GLOBALS['phpgw']->accounts->name2id($default_id);
			}
			return (int)$default_id;
		}
		else if (is_string($account_id))
		{
			if ( $GLOBALS['phpgw']->accounts->exists((int) $account_id) )
			{
				return (int) $account_id;
			}
			else
			{
				return $GLOBALS['phpgw']->accounts->name2id($account_id);
			}
		}
	}

	/**
	 * gets the file system seperator depending on OS
	 *
	 * @internal this isn't really needed as php can do the translation for us
	 * @return file system separator
	 */
	function filesystem_separator()
	{
		return DIRECTORY_SEPARATOR;
	}

	/**
	* Dump the contents of an array
	*
	* @param array $array the array to dump
	* @param bool $print echo out? returned as string if false
	* @return string the structure of the array
	*/
	function _debug_array($array,$print=True)
	{
		if( phpgw::get_var('phpgw_return_as') == 'json' )
		{
			$bt = debug_backtrace();
			if($array && !is_array($array))
			{
				$array = array($array);
			}
			
			$data = array
			(
				'info' => array
							(
								'file' => "Called from file: {$bt[0]['file']}",
								'line' => "line: {$bt[0]['line']}"
							), 
				'data' => $array
			);
			unset($bt);
			phpgwapi_cache::session_set($GLOBALS['phpgw_info']['flags']['currentapp'], "id_debug", $data);
			return;
		}

		$dump = '<pre>' . print_r($array, true) . '</pre>';
		if(!$print)
		{
			return $dump;
		}
		echo $dump;
		return '';
	}

	/**
	* Prepare a dump of the contents of an array for json
	*
	* @param array $array the array to dump
	* @return void
	*/
	function _debug_json($incoming_data)
	{
	/*  // FIXME - need some kind of unique comparison of a signature of incoming data to avoid repost of the same data set
		$preloaded_data = phpgwapi_cache::session_get($GLOBALS['phpgw_info']['flags']['currentapp'], "id_debug");
		if($preloaded_data)
		{
			if(is_array($preloaded_data))
			{
				if(is_array($incoming_data))
				{
					$data = array_merge($preloaded_data, $incoming_data);
				}
				else
				{
					$data = array_merge($preloaded_data, array($incoming_data));
				}
			}
			else
			{
				if(is_array($incoming_data))
				{
					$data = array_merge(array($preloaded_data), $incoming_data);
				}
				else
				{
					$data = array_merge(array($preloaded_data), array($incoming_data));
				}
			
			}
		}
		else
		{
			$data = $incoming_data;
		}
	*/	
		$data = $incoming_data;
		phpgwapi_cache::session_set($GLOBALS['phpgw_info']['flags']['currentapp'], "id_debug", $data);
	}

	/**
	* phpgw version checking, is param 1 < param 2 in phpgw versionspeak?
	* @param string $a phpgw version number to check if less than $b
	* @param string $b phpgw version number to check $a against
	* @return bool true if $a < $b
	*/
	function alessthanb($a,$b,$DEBUG=False)
	{
		$num = array('1st','2nd','3rd','4th');

		if ($DEBUG)
		{
			echo'<br>Input values: '
				. 'A="'.$a.'", B="'.$b.'"';
		}
		$newa = preg_replace('/pre/','.',$a);
		$newb = preg_replace('/pre/','.',$b);
		$testa = explode('.',$newa);
		if(@$testa[1] == '')
		{
			$testa[1] = 0;
		}
		if(@$testa[3] == '')
		{
			$testa[3] = 0;
		}
		$testb = explode('.',$newb);
		if(@$testb[1] == '')
		{
			$testb[1] = 0;
		}
		if(@$testb[3] == '')
		{
			$testb[3] = 0;
		}
		$less = 0;

		for ($i=0;$i<count($testa);$i++)
		{
			if ($DEBUG) { echo'<br>Checking if '. intval($testa[$i]) . ' is less than ' . intval($testb[$i]) . ' ...'; }
			if (intval($testa[$i]) < intval($testb[$i]))
			{
				if ($DEBUG) { echo ' yes.'; }
				$less++;
				if ($i<3)
				{
					/* Ensure that this is definitely smaller */
					if ($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely less than B."; }
					$less = 5;
					break;
				}
			}
			elseif(intval($testa[$i]) > intval($testb[$i]))
			{
				if ($DEBUG) { echo ' no.'; }
				$less--;
				if ($i<2)
				{
					/* Ensure that this is definitely greater */
					if ($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely greater than B."; }
					$less = -5;
					break;
				}
			}
			else
			{
				if ($DEBUG) { echo ' no, they are equal.'; }
				$less = 0;
			}
		}
		if ($DEBUG) { echo '<br>Check value is: "'.$less.'"'; }
		if ($less>0)
		{
			if ($DEBUG) { echo '<br>A is less than B'; }
			return True;
		}
		elseif($less<0)
		{
			if ($DEBUG) { echo '<br>A is greater than B'; }
			return False;
		}
		else
		{
			if ($DEBUG) { echo '<br>A is equal to B'; }
			return False;
		}
	}

	/**
	 * phpgw version checking, is param 1 > param 2 in phpgw versionspeak?
	 *
	 * @param	 * $a	 * phpgw version number to check if more than $b
	 * @param	 * $b	 * phpgw version number to check $a against
	 * #return	 * True if $a < $b
	 */
	function amorethanb($a,$b,$DEBUG=False)
	{
		$num = array('1st','2nd','3rd','4th');

		if ($DEBUG)
		{
			echo'<br>Input values: '
				. 'A="'.$a.'", B="'.$b.'"';
		}
		$newa = preg_replace('/pre/','.',$a);
		$newb = preg_replace('/pre/','.',$b);
		$testa = explode('.',$newa);
		if($testa[3] == '')
		{
			$testa[3] = 0;
		}
		$testb = explode('.',$newb);
		if($testb[3] == '')
		{
			$testb[3] = 0;
		}
		$less = 0;

		for ($i=0;$i<count($testa);$i++)
		{
			if ($DEBUG) { echo'<br>Checking if '. intval($testa[$i]) . ' is more than ' . intval($testb[$i]) . ' ...'; }
			if (intval($testa[$i]) > intval($testb[$i]))
			{
				if ($DEBUG) { echo ' yes.'; }
				$less++;
				if ($i<3)
				{
					/* Ensure that this is definitely greater */
					if ($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely greater than B."; }
					$less = 5;
					break;
				}
			}
			elseif(intval($testa[$i]) < intval($testb[$i]))
			{
				if ($DEBUG) { echo ' no.'; }
				$less--;
				if ($i<2)
				{
					/* Ensure that this is definitely smaller */
					if ($DEBUG) { echo"  This is the $num[$i] octet, so A is definitely less than B."; }
					$less = -5;
					break;
				}
			}
			else
			{
				if ($DEBUG) { echo ' no, they are equal.'; }
				$less = 0;
			}
		}
		if ($DEBUG) { echo '<br>Check value is: "'.$less.'"'; }
		if ($less>0)
		{
			if ($DEBUG) { echo '<br>A is greater than B'; }
			return True;
		}
		elseif($less<0)
		{
			if ($DEBUG) { echo '<br>A is less than B'; }
			return False;
		}
		else
		{
			if ($DEBUG) { echo '<br>A is equal to B'; }
			return False;
		}
	}

	/**
	 * prepend a prefix to an array of table names
	 *
	 * @author Adam Hull (aka fixe) - No copyright claim
	 * @param	$prefix	the string to be prepended
	 * @param	$tables	and array of tables to have the prefix prepended to
	 * @return array of table names with the prefix prepended
	 */
	function prepend_tables_prefix($prefix,$tables)
	{
		foreach($tables as $key => $value)
		{
			$tables[$key] = $prefix.$value;
		}
		return $tables;
	}

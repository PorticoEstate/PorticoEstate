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
 * @version $Id: common_functions.inc.php,v 1.27 2006/12/27 10:12:17 skwashd Exp $
 */

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
	 * Validate data.
	 *
	 * @author seek3r
	 * This function is used to validate input data. 
	 * sanitize('number',$somestring);
	 */

	/*
	   $GLOBALS['phpgw_info']['server']['sanitize_types']['number'] = Array('type' => 'preg_match', 'string' => '/^[0-9]+$/i');
	 */

	function sanitize($string,$type)
	{
		switch ($type)
		{
			case 'bool':
				if ($string == 1 || $string == 0)
				{
					return True;
				}
				break;
			case 'isprint':
				$length = strlen($string);
				$position = 0;
				while ($length > $position)
				{
					$char = substr($string, $position, 1);
					if ($char < ' ' || $char > '~')
					{
						return False;
					}
					$position = $position + 1;
				}
				return True;
				break;
			case 'alpha':
				if (preg_match("/^[a-z]+$/i", $string))
				{
					return True;
				}
				break;
			case 'number':
				if (preg_match("/^[0-9]+$/i", $string))
				{
					return True;
				}
				break;
			case 'alphanumeric':
				if (preg_match("/^[a-z0-9 -._]+$/i", $string))
				{
					return True;
				}
				break;
			case 'string':
				if (preg_match("/^[a-z]+$/i", $string))
				{
					return True;
				}
				break;
			case 'ip':
				if (eregi("^[0-9]{1,3}(\.[0-9]{1,3}){3}$",$string))
				{
					$octets = split('\.',$string);
					for ($i=0; $i != count($octets); $i++)
					{
						if ($octets[$i] < 0 || $octets[$i] > 255)
						{
							return False;
						}
					}
					return True;
				}
				return False;
				break;
			case 'file':
				if (preg_match("/^[a-z0-9_]+\.+[a-z]+$/i", $string))
				{
					return True;
				}
				break;
			case 'email':
				if (eregi("^([[:alnum:]_%+=.-]+)@([[:alnum:]_.-]+)\.([a-z]{2,3}|[0-9]{1,3})$",$string))
				{
					return True;
				}
				break;
			case 'password':
				$password_length = strlen($string);
				$password_numbers = Array('0','1','2','3','4','5','6','7','8','9');
				$password_special_chars = Array(' ','~','`','!','@','#','$','%','^','&','*','(',')','_','+','-','=','{','}','|','[',']',"\\",':','"',';',"'",'<','>','?',',','.','/');

				if(@isset($GLOBALS['phpgw_info']['server']['pass_min_length']) && is_int($GLOBALS['phpgw_info']['server']['pass_min_length']) && $GLOBALS['phpgw_info']['server']['pass_min_length'] > 1)
				{
					$min_length = $GLOBALS['phpgw_info']['server']['pass_min_length'];
				}
				else
				{
					$min_length = 1;
				}

				if(@isset($GLOBALS['phpgw_info']['server']['pass_require_non_alpha']) && $GLOBALS['phpgw_info']['server']['pass_require_non_alpha'] == True)
				{
					$pass_verify_non_alpha = False;
				}
				else
				{
					$pass_verify_non_alpha = True;
				}

				if(@isset($GLOBALS['phpgw_info']['server']['pass_require_numbers']) && $GLOBALS['phpgw_info']['server']['pass_require_numbers'] == True)
				{
					$pass_verify_num = False;
				}
				else
				{
					$pass_verify_num = True;
				}

				if(@isset($GLOBALS['phpgw_info']['server']['pass_require_special_char']) && $GLOBALS['phpgw_info']['server']['pass_require_special_char'] == True)
				{
					$pass_verify_special_char = False;
				}
				else
				{
					$pass_verify_special_char = True;
				}

				if ($password_length >= $min_length)
				{
					for ($i=0; $i != $password_length; $i++)
					{
						$cur_test_string = substr($string, $i, 1);
						if (in_array($cur_test_string, $password_numbers) || in_array($cur_test_string, $password_special_chars))
						{
							$pass_verify_non_alpha = True;
							if (in_array($cur_test_string, $password_numbers))
							{
								$pass_verify_num = True;
							}
							elseif (in_array($cur_test_string, $password_special_chars))
							{
								$pass_verify_special_char = True;
							}
						}
					}

					if ($pass_verify_num == False)
					{
						$GLOBALS['phpgw_info']['flags']['msgbox_data']['Password requires at least one non-alpha character']=False;
					}

					if ($pass_verify_num == False)
					{
						$GLOBALS['phpgw_info']['flags']['msgbox_data']['Password requires at least one numeric character']=False;
					}

					if ($pass_verify_special_char == False)
					{
						$GLOBALS['phpgw_info']['flags']['msgbox_data']['Password requires at least one special character (non-letter and non-number)']=False;
					}

					if ($pass_verify_num == True && $pass_verify_special_char == True)
					{
						return True;
					}
					return False;
				}
				$GLOBALS['phpgw_info']['flags']['msgbox_data']['Password must be at least '.$min_length.' characters']=False;
				return False;
				break;
			case 'any':
				return True;
				break;
			default :
				if (isset($GLOBALS['phpgw_info']['server']['sanitize_types'][$type]['type']))
				{
					if ($GLOBALS['phpgw_info']['server']['sanitize_types'][$type]['type']($GLOBALS['phpgw_info']['server']['sanitize_types'][$type]['string'], $string))
					{
						return True;
					}
				}
				return False;
		}
	}

	function reg_var($varname, $method = 'any', $valuetype = 'alphanumeric',$default_value='',$register=True)
	{
		if($method == 'any')
		{
			$method = Array('POST','GET','COOKIE','SERVER','GLOBAL','DEFAULT');
		}
		elseif(!is_array($method))
		{
			$method = Array($method);
		}
		$cnt = count($method);
		for($i=0;$i<$cnt;$i++)
		{
			switch(strtoupper($method[$i]))
			{
				case 'DEFAULT':
					if($default_value)
					{
						$value = $default_value;
						$i = $cnt+1; /* Found what we were looking for, now we end the loop */
					}
					break;
				case 'GLOBAL':
					if(@isset($GLOBALS[$varname]))
					{
						$value = $GLOBALS[$varname];
						$i = $cnt+1;
					}
					break;
				case 'POST':
				case 'GET':
				case 'COOKIE':
				case 'SERVER':
					$meth = '_'.strtoupper($method[$i]);
					if(@isset($GLOBALS[$meth][$varname]))
					{
						$value = $GLOBALS[$meth][$varname];
						$i = $cnt+1;
					}
					break;
				default:
					if(@isset($GLOBALS[strtoupper($method[$i])][$varname]))
					{
						$value = $GLOBALS[strtoupper($method[$i])][$varname];
						$i = $cnt+1;
					}
			}
		}

		if (!@isset($value))
		{
			$value = $default_value;
		}

		if (!@is_array($value))
		{
			if ($value == '')
			{
				$result = $value;
			}
			else
			{
				if (sanitize($value,$valuetype) == 1)
				{
					$result = $value;
				}
				else
				{
					$result = $default_value;
				}
			}
		}
		else
		{
			reset($value);
			while(list($k, $v) = each($value))
			{
				if ($v == '')
				{
					$result[$k] = $v;
				}
				else
				{
					if (is_array($valuetype))
					{
						$vt = $valuetype[$k];
					}
					else
					{
						$vt = $valuetype;
					}

					if (sanitize($v,$vt) == 1)
					{
						$result[$k] = $v;
					}
					else
					{
						if (is_array($default_value))
						{
							$result[$k] = $default_value[$k];
						}
						else
						{
							$result[$k] = $default_value;
						}
					}
				}
			}
		}
		if($register)
		{
			$GLOBALS['phpgw_info'][$GLOBALS['phpgw_info']['flags']['currentapp']][$varname] = $result;
		}
		return $result;
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
	function get_var($variable,$method='any',$default_value='')
	{
		return reg_var($variable,$method,'any',$default_value,False);
	}

	/**
	 * This will include an application class once and guarantee that it is loaded only once.  Similar to CreateObject, but does not instantiate the class.
	 *
	 * @example include_class('projects.ui_base');
	 * @param $appName name of application
	 * @param $className name of class
	 * @param $classPath path to the application class, default is 'inc/', use this parameter i.e. if the class is located in a subdirectory like 'inc/base_classes/'
	 * @return boolean true if class is included, else false (false means class could not included)
	 */
	function include_class($appName, $className, $classPath='inc/')
	{
		if ( is_file(PHPGW_INCLUDE_ROOT . "/{$appName}/{$classPath}class.{$className}.inc.php") )
		{
			return include_once(PHPGW_INCLUDE_ROOT . "/{$appName}/{$classPath}class.{$className}.inc.php");
		}
		return false;
	}

	/**
	* include object factory base class once		
	*/
	include_once(PHPGW_SERVER_ROOT.'/phpgwapi/inc/class.object_factory.inc.php');

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
	function CreateObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
	{

		list($appname,$classname) = explode('.', $class, 2);

		$of_classname = 'of'.$appname;

		// include module object factory class
		if (!include_class($appname, $of_classname))
		{
			// fail to load module object factory -> use old CreateObject in base class
			$of_class = new object_factory();
		}
		else
		{
			$of_class = new $of_classname;
		}

		// because $of_classname::CreateObject() is not allowed, we use call_user_func
		return $of_class->createObject($class, $p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16);
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
			if ( !isset($GLOBALS[$classname]) || !is_object($GLOBALS[$classname]) )
			{
				if ($classparams != '_UNDEF_' && ($classparams || $classparams != 'True'))
				{
					$GLOBALS[$classname] = createObject($appname.'.'.$classname, $classparams);
				}
				else
				{
					$GLOBALS[$classname] = createObject($appname.'.'.$classname);
				}
			}

			if ( (is_array($functionparams) || is_object($functionparams) || $functionparams != '_UNDEF_') 
				&& ($functionparams || $functionparams != 'True'))
			{
				return $GLOBALS[$classname]->$functionname($functionparams);
			}
			else
			{
				return $GLOBALS[$classname]->$functionname();
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
			while (list ($key, $val) = each ($GLOBALS['methodparts']))
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

			if (! @is_object($$parentobject->$classname) )
			{
				if ($classparams != '_UNDEF_' && ($classparams || $classparams != 'True'))
				{
					$$parentobject->$classname = createObject($appname.'.'.$classname, $classparams);
				}
				else
				{
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
	function get_account_id($account_id = '', $default_id = '')
	{
		if ( gettype($account_id) == 'integer' && $account_id <> 0 )
		{
			return $account_id;
		}
		elseif ( !$account_id )
		{
			if ($default_id == '')
			{
				return isset($GLOBALS['phpgw_info']['user']['account_id']) ? $GLOBALS['phpgw_info']['user']['account_id'] : 0;
			}
			elseif (is_string($default_id))
			{
				return $GLOBALS['phpgw']->accounts->name2id($default_id);
			}
			return (int)$default_id;
		}
		elseif (is_string($account_id))
		{
			if($GLOBALS['phpgw']->accounts->exists(intval($account_id)) == True)
			{
				return intval($account_id);
			}
			else
			{
				return $GLOBALS['phpgw']->accounts->name2id($account_id);
			}
		}
	}

	/**
	 * sets the file system seperator depending on OS
	 *
	 * @return file system separator
	 */
	function filesystem_separator()
	{
		if (PHP_OS == 'Windows' || PHP_OS == 'OS/2')
		{
			return '\\';
		}
		else
		{
			return '/';
		}
	}

	/* Just a wrapper to my new print_r() function I added to the php3 support file.  Seek3r */
	function _debug_array($array,$print=True)
	{
		$four = False;
		if(@floor(phpversion()) >= 4)
		{
			$four = True;
		}
		if($four)
		{
			if(!$print)
			{
				ob_start();
			}
			echo '<pre>';
			print_r($array);
			echo '</pre>';
			if(!$print)
			{
				$v = ob_get_contents();
				ob_end_clean();
				return $v;
			}
		}
		else
		{
			return print_r($array,False,$print);
		}
	}

	/*
	   @function alessthanb
	   @abstract phpgw version checking, is param 1 < param 2 in phpgw versionspeak?
	   @param	$a	phpgw version number to check if less than $b
	   @param	$b	phpgw version number to check $a against
	#return	True if $a < $b
	 */
	function alessthanb($a,$b,$DEBUG=False)
	{
		$num = array('1st','2nd','3rd','4th');

		if ($DEBUG)
		{
			echo'<br>Input values: '
				. 'A="'.$a.'", B="'.$b.'"';
		}
		$newa = ereg_replace('pre','.',$a);
		$newb = ereg_replace('pre','.',$b);
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
		$newa = ereg_replace('pre','.',$a);
		$newb = ereg_replace('pre','.',$b);
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

	/**
	 * For users with an old php version (<4.3.0)
	 */
	if(!function_exists('html_entity_decode'))
	{
		/**
		 * Convert all HTML entities to their applicable characters.
		 *
		 * @param   string   $str          string to convert
		 * @param   string   $quote_style  optional quote_style parameter lets you define what will be done with 'single' and "double" quotes. It takes on one of three constants with the default being ENT_COMPAT
		 * @param   string   $charset      optional charset string to convert (no supported yet!)
		 * @return  string                 converted string
		 */
		function html_entity_decode($str, $quote_style=ENT_COMPAT, $charset='ISO-8859-1')
		{
			$ents = get_html_translation_table(HTML_ENTITIES, $quote_style);
			$rpl_ents = array_flip($ents);
			return (strtr($str, $rpl_ents));
		}
	}
?>

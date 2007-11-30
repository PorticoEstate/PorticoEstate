<?php
	/**
	* Object Factory
	*
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.object_factory.inc.php 17771 2006-12-27 02:49:50Z skwashd $
	*/

	/**
	* Object factory
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class object_factory
	{
		function object_factory()
		{
			//die ('not allowed');
		}
		
		
		/**
		  * Load a class and include the class file if not done so already.
		 *
		  * @author mdean
		  * @author milosch
		  * @author (thanks to jengo and ralf)
		  * This function is used to create an instance of a class, and if the class file has not been included it will do so. 
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
			if ( $class != 'phpgwapi.log' && $class != 'phpgwapi.error' && $class != 'phpgwapi.errorlog')
			{
				phpgw_handle_error(PHPGW_E_DEBUG, 'This class was run: ' . $class, __LINE__, __FILE__);
			}
	
			list($appname,$classname) = explode('.', $class);
			$is_included = include_class($appname, $classname);
			if($is_included)
			{
				if ( class_exists("{$appname}_{$classname}") )
				{
					$classname = "{$appname}_{$classname}";
				}

				if ($p1 === '_UNDEF_')
				{
					$obj = new $classname;
				}
				else
				{
					$code = '$obj = new ' . $classname . '(';
					for ( $i = 1; $i < 17; ++$i )
					{
						$arg = "p$i";
						if ( $$arg === '_UNDEF_' )
						{
							break;
						}
						else
						{
							$code .= "\$$arg,";
						}
					}
					$code = substr($code,0,-1) . ');';
					eval($code);
				}
				return $obj;
			}
			else
			{
				trigger_error("Can not createObject($class), file does not exist", E_USER_ERROR);
			}
		}
	
		/**
		  * Convert the class string into an array.
		 *
		  * @author Dirk Schaller <dschaller@probusiness.de>
		  * This function is used to convert the first parameter of CreateObject method ('app.class') into an array (array('app'=>app, 'class'=>class)).
		  * $ci = createObject('phpgwapi.acl');
		  * @param $class 'app.class' string
		  * @return array with key 'app' and key 'class'.
		 */
		function getClassInfo($class)
		{
			list($app,$class) = explode('.', $class, 2);
			return array('app' => $app, 'class' => $class);
		}	
		
	}
	

?>

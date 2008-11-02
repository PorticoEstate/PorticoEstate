<?php
	/**
	 * Object Factory
	 *
	 * @author Dirk Schaller <dschaller@probusiness.de>
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author mdean
	 * @author milosch
	 * @author (thanks to jengo and ralf)
	 * @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Object factory
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	*/
	class phpgwapi_object_factory
	{
		/**
		 * Constructor - prevents instanstiation
		 *
		 * @return null
		 */
		public function __construct()
		{
			trigger_error('Do no instanstiate ' . __CLASS__);
		}

		/**
		 * Instantiate a class
		 *
		 * @param string $class name of class
		 * @param mixed  $p1    paramater for constructor of class (optional)
		 * @param mixed  $p2    paramater for constructor of class (optional)
		 * @param mixed  $p3    paramater for constructor of class (optional)
		 * @param mixed  $p4    paramater for constructor of class (optional)
		 * @param mixed  $p5    paramater for constructor of class (optional)
		 * @param mixed  $p6    paramater for constructor of class (optional)
		 * @param mixed  $p7    paramater for constructor of class (optional)
		 * @param mixed  $p8    paramater for constructor of class (optional)
		 * @param mixed  $p9    paramater for constructor of class (optional)
		 * @param mixed  $p10   paramater for constructor of class (optional)
		 * @param mixed  $p11   paramater for constructor of class (optional)
		 * @param mixed  $p12   paramater for constructor of class (optional)
		 * @param mixed  $p13   paramater for constructor of class (optional)
		 * @param mixed  $p14   paramater for constructor of class (optional)
		 * @param mixed  $p15   paramater for constructor of class (optional)
		 * @param mixed  $p16   paramater for constructor of class (optional)
		 *
		 * @return object the instantiated class
		 */
		public static function createObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
		{
			list($appname, $class) = explode('.', $class, 2);
			$is_included = include_class($appname, $class);
			if ( $is_included )
			{
				if ( class_exists("{$appname}_{$class}") )
				{
					$class = "{$appname}_{$class}";
				}

				if ($p1 === '_UNDEF_')
				{
					$obj = new $class;
				}
				else
				{
					$code = "\$obj = new {$class}(";
					for ( $i = 1; $i < 17; ++$i )
					{
						$arg = "p$i";
						if ( $$arg === '_UNDEF_' )
						{
							break;
						}
						else
						{
							$code .= "\$$arg, ";
						}
					}
					$code = substr($code, 0, -2) . ');';
					eval($code);
				}
				return $obj;
			}
			else
			{
				trigger_error("Can not createObject({$class}), file does not exist", E_USER_ERROR);
			}
		}

		/**
		 * Convert the class string into an array.
		 *
		 * @param string $class 'app.class' string
		 *
		 * @return array class as an array - format array('app' => appname, 'class' => classname)
		 */
		public static function get_class_info($class)
		{
			list($app, $class) = explode('.', $class, 2);
			return array('app' => $app, 'class' => $class);
		}
	}

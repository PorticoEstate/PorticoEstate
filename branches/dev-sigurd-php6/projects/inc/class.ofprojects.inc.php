<?php
	/**
	 * Object Factory
	 *
	 * @author Dirk Schaller <dschaller@probusiness.de>
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU Lesser General Public License v2 or later
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Object factory
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	*/
	class ofprojects extends object_factory
	{

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

			$ci = parent::get_class_info($class);
			switch($ci['class'])
			{
				case 'checker':
					return ofprojects::create_checker_object();

				default:
					return parent::CreateObject($class, $p1, $p2, $p3, $p4, $p5, $p6,
							$p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16);
			}
		}

		/**
		 * Instanstiate new project checker object
		 *
		 * @return object new project checker object - null if not found
		 */
		public static function create_checker_object()
		{
			// get customer version setting
			$soconfig	= CreateObject('projects.soconfig');
			$siteconfig	= $soconfig->get_site_config();
			if ( isset($siteconfig['customer_version_id']) )
			{
				$customer_version_id = $siteconfig['customer_version_id'];
			}
			else
			{
				$customer_version_id = 'standard';
			}

			$loaded = false;
			if ( $customer_version_id
				&& $customer_version_id != 'standard' )
			{
				// use customer version class
				$checkerClassName = "checker_{$customer_version_id}";
				if ( include_class('projects', $checkerClassName) )
				{
					$loaded = true;
				}
			}

			if ( !$loaded )
			{
				$checkerClassName = 'checker';
				if ( !include_class('projects', $checkerClassName) )
				{
					return null;
				}
			}
			return new $checkerClassName();
		}
	}

<?php
	/**
	* Project Manager
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	*/

	class ofprojects extends object_factory
	{

		/*!
		 @function CreateObject
		 @abstract Load a class and include the class file if not done so already.
		 @author mdean
		 @author milosch
		 @author (thanks to jengo and ralf)
		 @discussion This function is used to create an instance of a class, and if the class file has not been included it will do so. 
		 @syntax CreateObject('app.class', 'constructor_params');
		 @example $phpgw->acl = CreateObject('phpgwapi.acl');
		 @param $classname name of class
		 @param $p1-$p16 class parameters (all optional)
		*/
		function CreateObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
		{

			$ci = parent::getClassInfo($class);
			switch($ci['class'])
			{
				case 'checker':
					return ofprojects::CreateCheckerObject();
				break;
				default:
					return parent::CreateObject($class,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16);
			}
		}
		
		function CreateCheckerObject($params = array())
		{
			// get customer version setting
			$soconfig		= CreateObject('projects.soconfig');
			$siteconfig	= $soconfig->get_site_config();
			if(isset($siteconfig['customer_version_id']))
			{
				$customer_version_id = $siteconfig['customer_version_id'];
			}
			else
			{
				$customer_version_id = 'standard';
			}

			$loaded = false;
			if($customer_version_id && $customer_version_id != 'standard')
			{ // use customer version class
				$checkerClassName = 'checker_'.$customer_version_id;
				if(include_class('projects', $checkerClassName))
				{
					$loaded = true;
				}
			}
			
			if(!$loaded)
			{
				$checkerClassName = 'checker';
				if(!include_class('projects', $checkerClassName))
				{
					return null;
				}
			}

			return new $checkerClassName();
		}
		
	}
?>

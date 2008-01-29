<?php

require_once(PHPGW_INCLUDE_ROOT . SEP . 'sitemgr' . SEP . 'inc' . SEP . 'class.module.inc.php');

	class Modules_BO
	{
		var $so;

		function Modules_BO()
		{
			//all sitemgr BOs should be instantiated via a globalized Common_BO object,
			$this->so = CreateObject('sitemgr.Modules_SO', true);
		}

		function getmoduleid($modulename)
		{
			return $this->so->getmoduleid($modulename);
		}

		function getmodule($module_id)
		{
			return $this->so->getmodule($module_id);
		}

		function savemoduleproperties($module_id,$element,$contentarea,$cat_id)
		{
			$module = $this->getmodule($module_id);
			$moduleobject = $this->createmodule($module['module_name']);
			if ($moduleobject->validate_properties($element))
			{
				$this->so->savemoduleproperties($module_id,$element,$contentarea,$cat_id);
			}
		}

		function deletemoduleproperties($module_id,$contentarea,$cat_id)
		{
			$this->so->deletemoduleproperties($module_id,$contentarea,$cat_id);
		}


		//this is identical to CreateObect in phpgwapi/functions.inc.php, but looks into sitemgr/modules instead of appname/inc
		function createmodule($modulename, $p1 = '_UNDEF_', $p2 = '_UNDEF_', $p3 = '_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',
			$p12='_UNDEF_',$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
		{

		global $phpgw_info, $phpgw;

		//if (is_object(@$GLOBALS['phpgw']->log) && $class != 'phpgwapi.error' && $class != 'phpgwapi.errorlog')
		//{
			//$GLOBALS['phpgw']->log->write(array('text'=>'D-Debug, dbg: %1','p1'=>'This class was run: '.$class,'file'=>__FILE__,'line'=>__LINE__));
		//}

		/* error_reporting(0); */
		//list($appname,$classname) = explode(".", $class);
		
		$classname = 'module_' . $modulename;

		if (!isset($GLOBALS['phpgw_info']['flags']['included_classes'][$classname]) ||
			!$GLOBALS['phpgw_info']['flags']['included_classes'][$classname])
		{
			if(@file_exists(PHPGW_INCLUDE_ROOT.'/sitemgr/modules/class.'.$classname.'.inc.php'))
			{
				include(PHPGW_INCLUDE_ROOT.'/sitemgr/modules/class.'.$classname.'.inc.php');
				$GLOBALS['phpgw_info']['flags']['included_classes'][$classname] = True;
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['included_classes'][$classname] = False;
			}
		}
		if($GLOBALS['phpgw_info']['flags']['included_classes'][$classname])
		{
			if ($p1 == '_UNDEF_' && $p1 != 1)
			{
				eval('$obj = new ' . $classname . ';');
			}
			else
			{
				$input = array($p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16);
				$i = 1;
				$code = '$obj = new ' . $classname . '(';
				while (list($x,$test) = each($input))
				{
					if (($test == '_UNDEF_' && $test != 1 ) || $i == 17)
					{
						break;
					}
					else
					{
						$code .= '$p' . $i . ',';
					}
					$i++;
				}
				$code = substr($code,0,-1) . ');';
				eval($code);
			}
			/* error_reporting(E_ERROR | E_WARNING | E_PARSE); */
			return $obj;
		}
	}


		function getallmodules()
		{
			return $this->so->getallmodules();
		}

		function findmodules()
		{
			$incdir = PHPGW_SERVER_ROOT . SEP . 'sitemgr' . SEP . 'modules';
			if (is_dir($incdir))
			{
				$d = dir($incdir);
				while ($entry = $d->read())
				{
					if (preg_match ("/class\.module_(.*)\.inc\.php$/", $entry, $module)) 
					{
						$modulename = $module[1];
						$moduleobject = $this->createmodule($modulename);
						if ($moduleobject)
						{
							$this->so->registermodule($modulename,$moduleobject->description);
						}
					}
				}
				$d->close();
			}
		}

		function savemodulepermissions($contentarea,$cat_id,$modules)
		{
			$this->so->savemodulepermissions($contentarea,$cat_id,$modules);
		}

		//this function looks for a configured value for the combination contentareara,cat_id
		function getpermittedmodules($contentarea,$cat_id)
		{
			return $this->so->getpermittedmodules($contentarea,$cat_id);
		}

		//this function looks for a module's configured propertiese for the combination contentareara,cat_id
		//if module_id is 0 the fourth argument should provide modulename
		function getmoduleproperties($module_id,$contentarea,$cat_id,$modulename=False)
		{
			return $this->so->getmoduleproperties($module_id,$contentarea,$cat_id,$modulename);
		}

		//this function calculates the permitted modules by asking first for a value contentarea/cat_id
		//if it does not find one, climbing up the category hierarchy until the site wide value for the same contentarea
		//and if it still does not find a value, looking for __PAGE__/cat_id, and again climbing up until the master list
		function getcascadingmodulepermissions($contentarea,$cat_id)
		{
			$cat_ancestorlist = ($cat_id !=  CURRENT_SITE_ID) ? $GLOBALS['Common_BO']->cats->getCategoryancestorids($cat_id) : array();
			$cat_ancestorlist[] = CURRENT_SITE_ID;

			$cat_ancestorlist_temp = $cat_ancestorlist;

			do
			{
				$cat_id = array_shift($cat_ancestorlist_temp);

				while($cat_id !== NULL)
				{
					$permitted = $this->so->getpermittedmodules($contentarea,$cat_id);
					if ($permitted)
					{
						return $permitted;
					}
					$cat_id = array_shift($cat_ancestorlist_temp);
				}
				$contentarea = ($contentarea != "__PAGE__") ? "__PAGE__" : False;
				$cat_ancestorlist_temp = $cat_ancestorlist;
			} while($contentarea);
			return array();
		}

		//this function calculates the properties by climbing up the hierarchy tree in the same way as 
		//getcascadingmodulepermissions does
		function getcascadingmoduleproperties($module_id,$contentarea,$cat_id,$modulename=False)
		{
			$cat_ancestorlist = ($cat_id !=  CURRENT_SITE_ID) ? $GLOBALS['Common_BO']->cats->getCategoryancestorids($cat_id) : array();
			$cat_ancestorlist[] = CURRENT_SITE_ID;

			$cat_ancestorlist_temp = $cat_ancestorlist;

			do
			{
				$cat_id = array_shift($cat_ancestorlist_temp);

				while($cat_id !== NULL)
				{
					$properties = $this->so->getmoduleproperties($module_id,$contentarea,$cat_id,$modulename);
					//we have to check for type identity since properties can be NULL in case of unchecked checkbox
					if ($properties !== false)
					{
						return $properties;
					}
					$cat_id = array_shift($cat_ancestorlist_temp);
				}
				$contentarea = ($contentarea != "__PAGE__") ? "__PAGE__" : False;
				$cat_ancestorlist_temp = $cat_ancestorlist;
			} while($contentarea);
		}
	}

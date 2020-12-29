<?php

	/**
	 * Custom object factory
	 *
	 * @package phpgroupware
	 * @subpackage booking
	 */
	class booking_ofbooking extends phpgwapi_object_factory
	{

		/**
		 * Instantiate a class
		 *
		 * @return object the instantiated class
		 */
		public static function createObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
		{
			static $cache = array();


			$object_args = func_get_args();
			$class_identifier = array_shift($object_args);

			list($appname, $class) = explode('.', $class_identifier, 2);
			if (preg_match('/^sfValidator/', $class) > 0)
			{
				require_once(dirname(__FILE__) . '/vendor/symfony/validator/bootstrap.php');
				if (!isset($cache[$class]))
				{
					$cache[$class] = new ReflectionClass($class);
				}
				while ($arg = array_pop($object_args))
				{
					if ($arg !== '_UNDEF_')
					{
						$object_args[] = $arg;
						break;
					}
				}
				return count($object_args) > 0 ? $cache[$class]->newInstanceArgs($object_args) : $cache[$class]->newInstance();
			}

			array_unshift($object_args, $class_identifier);
			return call_user_func_array(array('phpgwapi_object_factory', 'createObject'), $object_args);
		}
	}
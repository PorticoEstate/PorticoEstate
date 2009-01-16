<?php
	/**
	* SQL Generator for Oracle
	* @author Yoshihiro Kamimura <your@itheart.com>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	*/

	/**
	* SQL Generator for Oracle
	*
	* @package phpgwapi
	* @subpackage database
	* @ignore
	*/
	class phpgwapi_sql extends phpgwapi_sql_
	{
		public static function sql_()
		{
		}

		public static function concat($elements)
		{
			$str =  implode(', ', $elements);
			return ($str) ? 'concat('.$str.')' : '';

		}

		public static function concat_null($elements)
		{
			$str =  implode(', ', self::safe_null($elements));
			return ($str) ? 'concat('.$str.')' : '';
		}
	}

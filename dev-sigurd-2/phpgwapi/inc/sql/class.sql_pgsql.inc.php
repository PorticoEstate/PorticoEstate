<?php
	/**
	* SQL Generator for PostrgreSQL
	* @author Edgar Antonio Luna Diaz <eald@co.com.mx>
	* @author Alejadro Borges
	* @author Jonathan Alberto Rivera Gomez
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id$
	* @internal Development of this application was funded by http://www.sogrp.com
	* @link http://www.sogrp.com/
	*/

	/**
	* SQL Generator for PostrgreSQL
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
			$str = implode(' || ', $elements);
			return ($str)? '('.$str.')' : '';
		}

		public static function concat_null($elements)
		{
			$str = implode(' || ', self::safe_null($elements));
			return ($str)? '('.$str.')' : '';
		}
		public static function has($field, $value)
		{
			return phpgwapi_sql_criteria::upper($field).' ILIKE '."'%$value%'";
		}
		public static function begin_with($field, $value)
		{
			return phpgwapi_sql_criteria::upper($field).' ILIKE '."'$value%'";
		}
		public static function end_with($field, $value)
		{
			return phpgwapi_sql_criteria::upper($field).' ILIKE '."'%$value'";
		}
	}

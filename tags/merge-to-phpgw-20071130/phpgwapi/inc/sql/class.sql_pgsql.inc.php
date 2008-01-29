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
	* @version $Id: class.sql_pgsql.inc.php 18006 2007-03-01 07:52:26Z sigurdne $
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
	class sql extends sql_
	{
		function sql_()
		{
		}

		function concat($elements)
		{
			$str = implode(' || ', $elements);
			return ($str)? '('.$str.')' : '';
		}

		function concat_null($elements)
		{
			$str = implode(' || ', sql::safe_null($elements));
			return ($str)? '('.$str.')' : '';
		}
		function has($field, $value)
		{
			return sql_criteria::upper($field).' ILIKE '."'%$value%'";
		}
		function begin_with($field, $value)
		{
			return sql_criteria::upper($field).' ILIKE '."'$value%'";
		}
		function end_with($field, $value)
		{
			return sql_criteria::upper($field).' ILIKE '."'%$value'";
		}
	}
?>

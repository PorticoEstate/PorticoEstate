<?php
	/**
	* SQL Generator for MS SQL Server
	* @author Edgar Antonio Luna Diaz <eald@co.com.mx>
	* @author Alejadro Borges
	* @author Jonathan Alberto Rivera Gomez
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id: class.sql_mssql.inc.php 17763 2006-12-26 11:37:44Z skwashd $
	* @internal Development of this application was funded by http://www.sogrp.com
	* @link http://www.sogrp.com/
	*/

	/**
	* SQL Generator for MS SQL Server
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
			$str = implode(' + ', $elements);
			return ($str)? '('.$str.')' : '';
		}

		function concat_null($elements)
		{
			$str = implode(' + ', sql::safe_null($elements));
			return ($str)? '('.$str.')' : '';
		}
	}
?>

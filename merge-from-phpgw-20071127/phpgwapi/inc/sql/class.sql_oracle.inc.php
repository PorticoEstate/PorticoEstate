<?php
	/**
	* SQL Generator for Oracle
	* @author Yoshihiro Kamimura <your@itheart.com>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage database
	* @version $Id: class.sql_oracle.inc.php 17763 2006-12-26 11:37:44Z skwashd $
	*/

	/**
	* SQL Generator for Oracle
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
			$str =  implode(', ', $elements);
			return ($str) ? 'concat('.$str.')' : '';

		}

		function concat_null($elements)
		{
			$str =  implode(', ', sql::safe_null($elements));
			return ($str) ? 'concat('.$str.')' : '';
		}
	}
?>

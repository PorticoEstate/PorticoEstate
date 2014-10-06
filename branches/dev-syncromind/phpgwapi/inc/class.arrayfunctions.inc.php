<?php
	/**
	* Arrayfunctions
	* @author Lars Kneschke <lkneschke@phpgw.de>
	* @copyright Copyright (C) 2002,2003 Lars Kneschke
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id$
	*/

	/**
	* Arrayfunctions
	*
	* @package phpgwapi
	* @subpackage utilities
	*/
	class arrayfunctions
	{
		function arrayfunctions($vars='')
		{
		}

		/**
		* Sort a multi-dimensional array according to a list of fields.
		*
		* @param array $a Array to sort
		* @param array $fl Field list in order of sort importance
		* @param string $_sort 'ASC'ending or 'DESC'ending sort order
		* @return array|boolean Sorted array or false
		*/
		function arfsort( $a, $fl, $_sort='ASC' )
		{
			$GLOBALS['__ARFSORT_LIST__'] = $fl;
			
			$this->sort=$_sort;

			if (is_array($a))
			{
				usort( $a, array($this,'arfsort_func') );
				return $a;
			}
			return False;
		}

		/**
		* Comparision function for arfsort()
		*
		* Uses $GLOBALS['__ARFSORT_LIST__'] and $sort
		* @param array $a Array one for comparision
		* @param array $b Array two for comparision
		* @return integer Returns < 0 if $a is less than $b; > 0 if $a is greater than $b, and 0 if they are equal.
		* @access private
		* @see arfsort()
		*/
		function arfsort_func( $a, $b )
		{
			foreach( $GLOBALS['__ARFSORT_LIST__'] as $f )
			{
				if($this->sort == 'ASC')
				{
					$strc = strcmp( $a[$f], $b[$f] );
				}
				else
				{
					$strc = strcmp( $b[$f], $a[$f] );
				}
				if ( $strc != 0 )
				{
					return $strc;
				}
			}
			return 0;
		}

	}
?>

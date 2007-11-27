<?php
/*  See the README file that came with this library for more
 *  information, and read the inline documentation.
 *
 *  Anil Madhavapeddy, <anil@recoil.org>
 *  $Horde: chora/lib/CVSLib/Rev.php,v 1.3 2001/01/26 20:37:45 chuck Exp $
 */

	/**
	* CVSLib revisions class.
	*
	* @author  Anil Madhavapeddy <anil@recoil.org>
	* @version $Revision: 10073 $
	* @since   Chora 0.1
	* @package chora
	*/
	class CVSLib_Rev
	{
		/*
		* Validation function to ensure that a revision number is
		* of the right form
		* @param val Value to check.
		* @return boolean true if it is a revision number
		*/
		function valid($val)
		{
			return $val && preg_match('/^[\d\.]+$/',$val);
		}

		/*
		* Given a revision number, remove a given number
		* of portions from it.  For example, if we remove
		* 2 portions of 1.2.3.4, we are left with 1.2
		* @param val input revision
		* @param amount number of portions to strip
		* @return stripped revision number
		*/
		function strip($val, $amount)
		{
			if(!$this->valid($val))
			{
				return False;
			}
			$revs = explode('.',$val);
			while ($amount--) array_pop($revs);
			return implode('.',$revs);
		}

		/*
		* The size of a revision number is the number
		* of portions it has.   For example, 1,2.3.4
		* is of size 4.
		* @param input revision number to determine size of
		* @param size of revision number
		*/
		function sizeof($val)
		{
			if(!$this->valid($val))
			{
				return False;
			}
			return sizeof(explode('.',$val));
		}

		/*
		* Given a valid revision number, this will 
		* return the revision number from which it 
		* branched.  If it cannot be determined, then
		* false is returned.
		* @param input revision number
		* @return branch point revision, or false
		*/
		function branchPoint($val)
		{
			/* Check if we have a valid revision number */
			if(!$this->valid($val))
			{
				return False;
			}

			/* If its on the trunk, or is an odd size, ret false */
			if($this->sizeof($val) < 3 || ($this->sizeof($val)%2))
			{
				return False;
			}

			/* Strip off two revision portions, and return it */
			return $this->strip($val, 2); 
		}

		/**
		* Compare two input numbers, and return an integer
		* with the sign of their difference.
		* @param a The first input number
		* @param b The second input number
		* @return -1,0,1 depending on the sign of their difference
		*/
		function numrel($a, $b)
		{
			if($a < $b)
			{
				return -1;
			}
			elseif($a == $b)
			{
				return 0;
			}
			else
			{
				return 1;
			}
		}

		/**
		* Given two CVS revision numbers, this figures out which
		* one is greater than the other by stepping along the
		* decimal points until a difference is found, at which
		* point a sign comparison of the two is returned.
		*
		* @param rev1 Period delimited revision number
		* @param rev2 Second period delimited revision number
		* @see numrel
		* @return 1 if the first is greater, -1 if the second if greater,
		*         and 0 if they are equal
		*/
		function cmp ($rev1, $rev2)
		{
			$r1 = explode('.', $rev1);
			$r2 = explode('.', $rev2);
			while (($a = array_shift($r1)) && ($b = array_shift($r2)))
			{
				if($a != $b)
				{
					return $this->numrel($a, $b);
				}
			}

			if($r1)
			{ 
				return 1; 
			}
			elseif($r2)
			{
				return -1; 
			}
			else
			{
				return 0;
			}
		}

		/*
		* Given a revision number of the form x.y.0.z, this remaps it
		* into the appropriate branch number, which is x.y.z
		* @param $rev Even-digit revision number of a branch
		* @return Odd-digit Branch number
		*/
		function toBranch($rev)
		{
			/* Check if we have a valid revision number */
			if(!$this->valid($rev))
			{
				return False;
			}

			$parts = explode('.',$rev);
			$last = array_splice($parts, -2);
			$parts[] = $last[1];
			return implode('.',$parts);
		}
	}
?>

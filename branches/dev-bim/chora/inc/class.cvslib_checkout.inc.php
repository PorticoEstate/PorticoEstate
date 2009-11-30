<?php 
	/*  See the README file that came with this library for more
	 *  information, and read the inline documentation.
	 *
	 *  Anil Madhavapeddy, <anil@recoil.org>
	 *  $Horde: chora/lib/CVSLib/Checkout.php,v 1.7 2001/03/18 20:18:48 avsm Exp $
	 */

	/**
	 * CVSLib checkout class.
	 *
	 * @author  Anil Madhavapeddy <anil@recoil.org>
	 * @version $Revision: 10073 $
	 * @since   Chora 0.1
	 * @package chora
	 */
	class CVSLib_Checkout
	{
		/**
		* Static function which returns a file pointing to the head of the requested
		* revision of an RCS file.
		* @param CVS CVSLib object of the desired CVS repository
		* @param fullname Fully qualified pathname of the desired RCS file to checkout
		* @param rev RCS revision number to check out
		* @return Either a CVSLib_Error object, or a stream pointer to the head of the checkout
		*/

		function get($CVS, $fullname, $rev)
		{
			$_rev = CreateObject('chora.cvslib_rev');
			if(!$_rev->valid($rev))
			{
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_INTERNAL_ERROR,
					'Invalid revision number'
				);
			}

			if(!($RCS = popen($CVS->conf['paths']['co']." -p$rev '$fullname' 2>&1", 'r')))
			{
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_INTERNAL_ERROR,
					"Couldn't perform checkout of the requested file"
				);
			}

			/* First line from co should be of the form :
			* /path/to/filename,v  -->  standard out
			* and we check that this is the case and error otherwise
			*/

			$co = fgets($RCS, 1024);
			if(!preg_match('/^([\S ]+),v\s+-->\s+st(andar)?d ?out(put)?\s*$/', $co, $regs) || $regs[1] != $fullname)
			{
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_INTERNAL_ERROR,
					"Unexpected output from CVS Checkout: $co"
				);
			}

			/*
			* Next line from co is of the form:
			* revision 1.2.3
			* TODO: compare this to $rev for consistency, atm we just
			*       discard the value to move input pointer along - avsm
			*/
			$co = fgets($RCS, 1024);

			return $RCS;
		}
	}

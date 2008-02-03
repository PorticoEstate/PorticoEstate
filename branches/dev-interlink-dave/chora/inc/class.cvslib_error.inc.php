<?php 
	/*  See the README file that came with this library for more
	 *  information, and read the inline documentation.
	 *
	 *  Anil Madhavapeddy, <anil@recoil.org>
	 *  $Horde: chora/lib/CVSLib/Error.php,v 1.3 2001/01/26 20:37:45 chuck Exp $
	 */

	/**
	 * CVSLib error class.
	 *
	 * @author  Anil Madhavapeddy <anil@recoil.org>
	 * @version $Revision: 10069 $
	 * @since   Chora 0.1
	 * @package chora
	 */
	class CVSLib_Error
	{
		var $header, $body;
		/**
		* Create a CVS Error object to indicate something has
		* gone wrong.
		*
		* @param $h The error number of what happened
		* @param $b Brief textual description of the error
		*/
		function CVSLib_Error($h, $b)
		{
			$this->header=$h;
			$this->body=$b;
		}

		/**
		* Return what class this is for identification purposes
		* @return CVSLIB_ERROR constant
		*/
		function id()
		{
			return CVSLIB_ERROR;
		}

		/**
		* Retrieve the error in a format suitable for outputting
		* in a HTTP Header
		*
		* @return HTTP Error String
		*/
		function error_header()
		{
			switch ($this->header)
			{
				case CVSLIB_INTERNAL_ERROR:
					$err = "505 Internal Server Error";
					break;
				case CVSLIB_NOT_FOUND:
					$err = "404 Page Not Found";
					break;
				case CVSLIB_PERMISSION_DENIED:
					$err = "403 Forbidden";
					break;
				default:
					$err = "501 Not Implemented";
					break;
			}
			return $err;
		}

		/**
		* Retrieve the body of the error for either output or
		* further logging
		*
		* @return Brief description of the error that occurred
		*/
		function error_body()
		{
			return $this->body;
		}
	}

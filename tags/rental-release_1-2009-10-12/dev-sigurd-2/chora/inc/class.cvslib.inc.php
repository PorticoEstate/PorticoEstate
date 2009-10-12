<?php 
  /*  See the README file that came with this library for more
   *  information, and read the inline documentation.
   *
   *  Copyright 2000, 2001 Anil Madhavapeddy, <anil@recoil.org>
   *  $Horde: chora/lib/CVSLib.php,v 1.67 2001/03/18 03:09:00 avsm Exp $
   */

	/*
	* These are used to construct the CVSLib_Error objects
	* to identify problem classes
	*/
	define('CVSLIB_INTERNAL_ERROR',1);
	define('CVSLIB_NOT_FOUND',2);
	define('CVSLIB_PERMISSION_DENIED',3);

	/*
	 * Every class in this package has an id() function 
	 * which returns what it is, to allow the calling function
	 * to figure out whether it is an error or a data return type
	 */
	define('CVSLIB_ERROR', 4);
	define('CVSLIB_CHECKOUT', 5);
	define('CVSLIB_REPOSITORY',6);
	define('CVSLIB_DIRECTORY',7);
	define('CVSLIB_FILE',8);
	define('CVSLIB_ANNOTATE',9);

	define('CVSLIB_LOG_INIT',10);
	define('CVSLIB_LOG_FILENAME',11);
	define('CVSLIB_LOG_REVISION',12);
	define('CVSLIB_LOG_INFO',13);
	define('CVSLIB_LOG_DATE',14);
	define('CVSLIB_LOG_BRANCHES',15);
	define('CVSLIB_LOG_DONE',16);

	define('CVSLIB_LOG_FULL',0);
	define('CVSLIB_LOG_QUICK',1);

	define('CVSLIB_ATTIC_HIDE',0);
	define('CVSLIB_ATTIC_SHOW',1);

	/*
	 * Sorting options 
	 */
	define('CVSLIB_SORT_NONE',0);        // don't sort
	define('CVSLIB_SORT_AGE',1);         // sort by age
	define('CVSLIB_SORT_NAME',2);        // sort by filename
	define('CVSLIB_SORT_REV',3);         // sort by revision number
	define('CVSLIB_SORT_AUTHOR',4);      // sort by author name

	define('CVSLIB_SORT_ASCENDING',0);   // ascending order
	define('CVSLIB_SORT_DESCENDING',1);  // descending order

	/*
	 * Diff options
	 */
	define('CVSLIB_DIFF_CONTEXT', 0);
	define('CVSLIB_DIFF_UNIFIED', 1);
	define('CVSLIB_DIFF_COLUMN',  2);
	define('CVSLIB_DIFF_ED',      3);

	define('CVSLIB_DIFF_HEADER', 'header');
	define('CVSLIB_DIFF_ADD', 'add');
	define('CVSLIB_DIFF_EMPTY', 'empty');
	define('CVSLIB_DIFF_DUMP', 'dump');
	define('CVSLIB_DIFF_REMOVE', 'remove');
	define('CVSLIB_DIFF_CHANGE', 'change');

	/* Report all errors from PHP */
	//error_reporting(E_ALL);

	/**
	 * CVSLib base class.
	 *
	 * @author  Anil Madhavapeddy <anil@recoil.org>
	 * @version $Revision: 10077 $
	 * @since   Chora 0.1
	 * @package chora
	 */
	class CVSLib
	{
		var $conf, $mime;

		function CVSLib($initConf,$initMime)
		{
			$this->conf = $initConf;
			$this->mime = $initMime;
		}

		/**
		* Return what class this is for identification purposes
		* @return CVSLIB_REPOSITORY constant
		*/
		function id()
		{
			return CVSLIB_REPOSITORY;
		}

		/**
		* Return the CVSROOT for this repository, with no trailing /
		* @return CVSROOT for this repository
		*/
		function cvsRoot()
		{
			return $this->conf['paths']['cvsRoot'];
		}

		/**
		* Attempt to figure out the MIME type for a file based on
		* its extension.  First of all the value is searched for in
		* the mimeTypes hash, and then in the mime.types file if
		* it was defined in the configuration.
		*
		* @param fullname The fully qualified name of the file
		* @return The MIME-type of the file argument
		*/
		function getMimeType($fullname)
		{
			$suffix = preg_replace('/^.*\.([^.]*)$/', '\1', $fullname);
			$mimetype = isset($this->mime['types'][$suffix]) ? $this->mime['types'][$suffix] : '';

			if (!$mimetype && file_exists($this->mime['mimeTypes']))
			{
				$MIMETYPES = file($this->mime['mimeTypes']);
				while (list(,$line) = each($MIMETYPES))
				{
					if (preg_match('/^\s*(\S+\/\S+).*\b$suffix\b/', $line, $regs))
					{
						return $regs[1];
					}
				}
			}

			if (!preg_match('/\S\/\S/', $mimetype))
			{
				$mimetype=$this->mime['default'];
			}
			return $mimetype;
		}

		function queryDir($where)
		{
			$dir = CreateObject('chora.cvslib_directory',$this, $where);
			return $dir;
		}

		/*
		* Parse the 'cvsusers' file, if present in the CVSROOT, and return a 
		* hash containing the requisite information, keyed on the username, and
		* with the 'desc','name', and 'mail' values inside.
		* @return false if the file is not present, otherwise a hash with the data
		*/
		function parseCVSUsers()
		{
			$users = array();

			/* Try to locate the cvsusers file, and test to see if it is there */
			$cvsfile = $this->conf['paths']['cvsusers'];

			if (!@is_file($cvsfile) || !($fl=fopen($cvsfile,'r')))
			{
				return false;
			}

			/* Discard the first line, since it'll be the header info */
			fgets($fl, 4096);

			/* Parse the rest of the lines into a hash, keyed on username */
			while ($line = fgets($fl, 4096))
			{
				if (preg_match('/^\s*$/',$line))
				{
					continue;
				}
				if (!preg_match('/^(\w+)\s+(.+)\s+(\w+@[\w\.]+)\s+(.*)$/', $line, $regs))
				{
					continue;
				}
				$users[$regs[1]]['name'] = trim($regs[2]);
				$users[$regs[1]]['mail'] = trim($regs[3]);
				$users[$regs[1]]['desc'] = trim($regs[4]);
			}
			return $users;
		}
	}
?>

<?php
/*  See the README file that came with this library for more
 *  information, and read the inline documentation.
 *
 *  Anil Madhavapeddy, <anil@recoil.org>
 *  $Horde: chora/lib/CVSLib/Directory.php,v 1.11 2001/03/07 05:07:43 chuck Exp $
 */

	/**
	* CVSLib directory class.
	*
	* @author  Anil Madhavapeddy <anil@recoil.org>
	* @version $Revision: 11846 $
	* @since   Chora 0.1
	* @package chora
	*/
	class cvslib_directory
	{
		var $dirName, $rep, $files, $atticFiles, $mergedFiles, $dirs, $parent, $moduleName;

		/**
		* Create a CVS Directory object to store information
		* about the files in a single directory in the repository
		*
		* @param rp CVSLIB_Repository object this directory is part of
		* @param dn Path to the directory
		* @param pn Optional parent CVSLib_Directory to this one
		*/
		function CVSLib_Directory($rp, $dn, $pn='')
		{
			$this->parent  = $pn;
			$this->rep     = $rp;
			$this->moduleName = $dn;
			$this->dirName = $rp->cvsRoot() . "/$dn";
			$this->files   = array();
			$this->dirs    = array(); 
		}

		/**
		* Return what class this is for identification purposes
		* @return CVSLIB_DIRECTORY constant
		*/
		function id()
		{
			return CVSLIB_DIRECTORY;
		}

		/**
		* Return fully qualified pathname to this directory
		* with no trailing /
		* @return Pathname of this directory
		*/
		function queryDir()
		{
			return $this->dirName;
		}

		function queryDirList()
		{
			reset ($this->dirs);
			return $this->dirs;
		}

		function queryFileList($flags = CVSLIB_ATTIC_HIDE)
		{
			if($flags == CVSLIB_ATTIC_SHOW && isset($this->mergedFiles))
			{
				return $this->mergedFiles;
			}
			else
			{
				return $this->files;
			}
		}

		/**
		* Tell the object to open and browse its current directory, and
		* retrieve a list of all the objects in there.  It then populates
		* the file/directory stack and makes it available for retrieval.
		* @return CVSLib_Error object on an error, 1 on success. 
		*/
		function browseDir($flags = CVSLIB_LOG_QUICK, $attic = CVSLIB_ATTIC_HIDE)
		{
			/* Make sure we are trying to list a directory */
			if(!@is_dir($this->dirName))
			{
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_NOT_FOUND,
					'Unable to find directory'
				);
			}

			/* Open the directory for reading its contents */
			if(!($DIR = @opendir($this->dirName)))
			{
				$errmsg = (!empty($php_errormsg)) ? $php_errormsg : 'Permission Denied';
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_PERMISSION_DENIED,
					"$this->dirName: $errmsg"
				);
			}

			/* Maintain two arrays - one of all the files, and the other of dirs */
			$fileList = array();

			while(($name = readdir($DIR)) !== false)
			{
				/* Drop the special files, we know they exist */
				if($name == '.' || $name == '..' || $name == 'Attic')
				{
					continue;
				}
				/* Check to see if we have a directory */
				elseif(@is_dir($this->queryDir()."/$name"))
				{
					array_push($this->dirs, $name);
				}
				/* Check to see if we have a repository file */
				elseif(preg_match('/,v$/',$name))
				{
					array_push($fileList, preg_replace('/,v$/','',$name));
				}
				/* Otherwise we have an illegal file in our repository, just ignore it */
			}

			/* Close the filehandle; we've now got a list of dirs and files */
			closedir($DIR);

			@reset($fileList);
			while(list(,$fname) = @each($fileList))
			{
				/* Spawn a new file object to represent this file */
				$fl = CreateObject(
					'chora.cvslib_file',
					$this->rep,
					$this->queryDir() . "/$fname",
					$flags
				);
				$retVal = $fl->getBrowseInfo();

				/* Check for an error; if there is one, then return it */
				if(is_object($retVal) && $retVal->id() == CVSLIB_ERROR)
				{
					return $fl;
				}
				/* Push the created file object on the directory file stack */
				elseif($fl->id() == CVSLIB_FILE)
				{
					array_push($this->files, $fl);
				}
				else
				{
					/* TODO- We should never reach here - throw a consistency
					* CVSLib_Error in the future - avsm */
				}
			}

			/* If we want to merge the attic, add it in here */
			if($attic = CVSLIB_ATTIC_SHOW)
			{
				$atticDir = CreateObject(
					'chora.cvslib_directory',
					$this->rep,
					$this->moduleName . '/Attic',
					$this
				);
				if($atticDir->browseDir($flags, CVSLIB_ATTIC_HIDE) == 1)
				{
					$this->atticFiles = $atticDir->queryFileList();
					$this->mergedFiles = array_merge($this->files, $this->atticFiles);
				}
			}
			return 1;
		}

		/**
		* Sort the contents of the directory in a given fashion and order
		* @param how Of the form CVSLIB_SORT_* where * can be: 
		*       NONE, NAME, AGE, REV for sorting by name, age or revision
		* @param dir Of the form CVSLIB_SORT_* where * can be:
		*       ASCENDING, DESCENDING for the order of the sort
		*/
		function applySort($how=CVSLIB_SORT_NONE, $dir=CVSLIB_SORT_ASCENDING)
		{
			// assume by name for the moment
			sort($this->dirs);
			reset($this->dirs);
			$this->doFileSort($this->files, $how, $dir);
			reset($this->files);
			if(isset($this->atticFiles))
			{
				$this->doFileSort($this->atticFiles, $how, $dir);
				reset($this->atticFiles);
			}
			if(isset($this->mergedFiles))
			{
				$this->doFileSort($this->mergedFiles, $how, $dir);
				reset($this->mergedFiles);
			}
			if($dir == CVSLIB_SORT_DESCENDING)
			{
				$this->dirs=array_reverse($this->dirs);
				$this->files=array_reverse($this->files);
			}
		}

		function doFileSort(&$fileList, $how = CVSLIB_SORT_NONE, $dir=CVSLIB_SORT_ASCENDING)
		{
			switch($how)
			{
				case CVSLIB_SORT_NONE:
					break;
				case CVSLIB_SORT_AGE:
					usort($fileList, array($this, "fileAgeSort"));
					break;
				case CVSLIB_SORT_NAME:
					usort($fileList, array($this, "fileNameSort"));
					break;
				case CVSLIB_SORT_AUTHOR:
					usort($fileList, array($this, "fileAuthorSort"));
					break;
				case CVSLIB_SORT_REV:
					usort($fileList, array($this, "fileRevSort"));
					break;
				default:
					break;
			}
		}

		/**
		* Sort function for ascending age
		*/
		function fileAgeSort($a, $b)
		{
			$aa = $a->queryLastLog();
			$bb = $b->queryLastLog();
			if($aa->queryDate() == $bb->queryDate())
			{
				return 0;
			}
			else
			{
				return ($aa->queryDate() < $bb->queryDate())?1:-1;
			}
		}

		/**
		* Sort function by author name
		*/
		function fileAuthorSort($a, $b)
		{
			$aa = $a->queryLastLog();
			$bb = $b->queryLastLog();
			if($aa->queryAuthor() == $bb->queryAuthor())
			{
				return 0;
			}
			else
			{
				return ($aa->queryAuthor() > $bb->queryAuthor())?1:-1;
			}
		}

		/**
		* Sort function for ascending file-name
		*/
		function fileNameSort($a, $b)
		{
			if($a->name == $b->name)
			{
				return 0;
			}
			else
			{
				return ($a->name < $b->name)?-1:1;
			}
		}

		/**
		* Sort function for ascending revision
		*/
		function fileRevSort($a, $b)
		{
			$rev = CreateObject('chora.cvslib_rev');
			return $rev->cmp(
				$a->queryHead(),
				$b->queryHead()
			);
		}
	}
?>

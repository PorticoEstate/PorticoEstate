<?php
/*  See the README file that came with this library for more
 *  information, and read the inline documentation.
 *
 *  Anil Madhavapeddy, <anil@recoil.org>
 *  $Horde: chora/lib/CVSLib/File.php,v 1.13 2001/03/07 05:07:44 chuck Exp $
 */

	/**
	 * CVSLib file class.
	 *
	 * @author  Anil Madhavapeddy <anil@recoil.org>
	 * @version $Revision: 10073 $
	 * @since   Chora 0.1
	 * @package chora
	 */
	class cvslib_file
	{
		var $rep, $dir, $name, $logs, $revs, $head, $flags, $symrev, $revsym, $branches;

		/**
		* Create a repository file object, and give
		* it information about what its parent directory
		* and repository objects are
		* @param rp The CVSLIB_Repository object this is part of
		* @param fl Full path to this file
		*/
		function cvslib_file($rp, $fl, $flags= CVSLIB_LOG_FULL)
		{
			$fl .= ',v';
			$this->name = basename($fl);
			$this->dir = dirname($fl);
			$this->rep = $rp;
			$this->logs = array();
			$this->flags = $flags;
			$this->revs = array();
			$this->branches = array();
		}

		/**
		* Return what class this is for identification purposes
		* @return CVSLIB_FILE constant
		*/
		function id()
		{
			return CVSLIB_FILE;
		}

		/**
		* If this file is present in an Attic directory, this indicates it
		* @return true if file is in the Attic, and false otherwise
		*/
		function isAtticFile()
		{
			return substr($this->dir,-5) == 'Attic';
		}

		/**
		* Returns the name of the current file as in the repository
		* @return Filename (without the path)
		*/
		function queryRepositoryName()
		{
			return $this->name;
		}

		/**
		* Returns name of the current file without the repository
		* extensions (usually ,v)
		* @return Filename without repository extension
		*/

		function queryName()
		{
			return preg_replace('/,v$/', '', $this->name);
		}

		/**
		* Return the last revision of the current file
		* on the HEAD branch
		* @return Last revision of the current file
		*/
		function queryRevision()
		{
			return $this->revs[0];
		}

		/** Return the HEAD revision number for this file
		* @return HEAD revision number
		*/
		function queryHead()
		{
			return $this->head;
		}

		/*
		* Return the last CVSLib_Log object in the file
		* @return CVSLib_Log of the last entry in the file
		*/
		function queryLastLog()
		{
			return $this->logs[$this->revs[0]];
		}

		/* Sort the list of CVSLib_Log objects that this file contains
		* @param how CVSLIB_SORT_REV (sort by revision), 
		*            CVSLIB_SORT_NAME (sort by author name),
		*            CVSLIB_SORT_AGE (sort by commit date)
		*/ 
		function applySort($how = CVSLIB_SORT_REV)
		{
			switch ($how)
			{
				case CVSLIB_SORT_REV:
					$func = 'Revision'; 
					break;
				case CVSLIB_SORT_NAME:
					$func = 'Name';
					break;
				case CVSLIB_SORT_AGE:
					$func = 'Age';
					break;
				default:
					$func = 'Revision';
			}
			uasort($this->logs,array($this,"sortBy$func"));
			return true;
		} 

		/* The sortBy*() functions are internally used by applySort
		*/
		function sortByRevision($a, $b)
		{
			return CVSLib_Rev::cmp($b->rev,$a->rev);
		}

		function sortByAge($a, $b)
		{
			if ($a->date == $b->date)
			{
				return 0;
			}
			return ($a->date < $b->date)?1:-1;
		}

		function sortByName($a , $b)
		{
			if ($a->author == $b->author)
			{
				return 0;
			}
			return ($a->author < $b->author)?-1:1;
		}

		/**
		* Populate the object with information about the revisions
		* logs and dates of the file
		*
		* @return CVSLib_Error object on error, or true on success
		*/
		function getBrowseInfo()
		{
			/* Check that we are actually in the filesystem */
			if(!is_file($this->queryFullPath()))
			{
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_NOT_FOUND,
					'File Not Found'
				);
			}

			/* Call the RCS rlog command to retrieve the file information */
			$flag = ($this->flags == CVSLIB_LOG_QUICK) ? ' -r ' : ' ';
			$cmd = $this->rep->conf['paths']['rlog'] . $flag . escapeShellCmd($this->queryFullPath());

			/* Try and execute it, and retrieve the output into a variable */
			if(!($pstream = popen($cmd, 'r')))
			{
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_INTERNAL_ERROR,
					'Failed to spawn rlog to retrieve file log information'
				);
			}
			$accum = array();
			$symrev = array();
			$revsym = array();
			$state = CVSLIB_LOG_INIT;
			while($line = fgets($pstream, 4096))
			{
				switch($state)
				{
					case CVSLIB_LOG_INIT:
						if(preg_match("/^head: (.*)$/",$line,$head))
						{
							$this->head = $head[1];
						}
						elseif(preg_match("/^branch:/",$line))
						{
							$state=CVSLIB_LOG_REVISION;
						}
						break;
					case CVSLIB_LOG_REVISION:
						if(preg_match("/^----------/",$line))
						{
							$state = CVSLIB_LOG_INFO;
							$this->symrev = $symrev;
							$this->revsym = $revsym;
						}
						elseif(preg_match("/^\s+([^:]+):\s+([\d\.]+)/", $line ,$regs))
						{
							/* Check to see if this is a branch */
							if(preg_match('/^(\d+(\.\d+)+)\.0\.(\d+)$/',$regs[2]))
							{
								$branchRev = ExecMethod('chora.cvslib_rev.toBranch',$regs[2]);
								if(!isset($this->branches[$branchRev]))
								{
									$this->branches[$branchRev] = $regs[1];
								}
							}
							else
							{
								$symrev[$regs[1]] = $regs[2];
								if(empty($revsym[$regs[2]]))
								{
									$revsym[$regs[2]]=array();
								}
								array_push($revsym[$regs[2]],$regs[1]);
							}
						}
						break;
					case CVSLIB_LOG_INFO:
						if(!preg_match("/^----------------------------|^==============================/", $line))
						{
							array_push($accum, $line);
						}
						elseif(sizeof($accum) > 0)
						{
							// spawn a new cvslib_log object and add it to the logs hash
							$log = CreateObject('chora.cvslib_log', $this->rep, $this);
							$err = $log->processLog($accum);
							// TODO: error checks - avsm
							$this->logs[$log->queryRevision()] = $log;
							array_push($this->revs, $log->queryRevision());
							$accum = array();
						}
						break;
				}
			}
			return true;
		}

		/**
		* Return a text description of how long its been since the
		* file has been last modified.
		*
		* @param date Number of seconds since epoch we wish to display
		* @param long If true, display a more verbose date
		* @return String with the human-readable date
		*/
		function readableTime($date, $long = false)
		{
			$secs = time() - $date;
			$i = 0;
			$desc = array(
				1        => 'second',
				60       => 'minute',
				3600     => 'hour',
				86400    => 'day',
				604800   => 'week',
				2628000  => 'month',
				31536000 => 'year'
			);

			if($secs < 2)
			{
				return lang('very little time');
			}

			while(list($k,) = each($desc))
			{
				$breaks[] = $k;
			}
			sort($breaks);

			while($i < count($breaks) && $secs >= (2 * $breaks[$i]))
			{
				$i++;
			}
			$i--;
			$break = $breaks[$i];

			$val = intval($secs / $break);
			$retval = $val . ' ' . $desc[$break] . ($val>1 ? 's' : '');
			if($long && $i > 0)
			{
				$rest = $secs % $break;
				$break = $breaks[--$i];
				$rest = intval($rest/$break);
				if($rest > 0)
				{
					$resttime = $rest . ' ' . $desc[$break] . ($rest > 1 ? 's' : '');
					$retval .= ", $resttime";
				}
			}

			return $retval;
		}

		/**
		* Return the fully qualified filename of this object
		* @return Fully qualified filename of this object
		*/
		function queryFullPath()
		{
			return $this->dir . '/' . $this->name;
		}

		/**
		* Return the name of this file relative to its CVSROOT
		* @return Pathname relative to CVSROOT
		*/
		function queryModulePath()
		{
			return preg_replace('|^'. $this->rep->cvsRoot() . '/?(.*),v$|', '\1', $this->queryFullPath());
		}
	}
?>

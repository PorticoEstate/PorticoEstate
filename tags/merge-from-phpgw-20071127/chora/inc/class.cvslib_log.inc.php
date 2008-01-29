<?php 
/*  See the README file that came with this library for more
 *  information, and read the inline documentation.
 *
 *  Anil Madhavapeddy, <anil@recoil.org>
 *  $Horde: chora/lib/CVSLib/Log.php,v 1.6 2001/01/26 20:37:45 chuck Exp $
 */

	/**
	* CVSLib log class.
	*
	* @author  Anil Madhavapeddy <anil@recoil.org>
	* @version $Revision: 10077 $
	* @since   Chora 0.1
	* @package chora
	*/
	class CVSLib_Log
	{
		var $rep, $file, $tags, $rev, $date, $log, $author, $state, $lines, $branch;

		function CVSLib_Log($rp, $fl)
		{
			$this->rep = $rp;
			$this->file = $fl;
		}

		function processLog($raw)
		{
			/* Initialise a simple state machine to parse the output of rlog */
			$state = CVSLIB_LOG_INIT;
			while(!empty($raw) && $state != CVSLIB_LOG_DONE)
			{
				switch($state)
				{
					/* Found filename, now looking for the revision number */
					case CVSLIB_LOG_INIT:
						$line = array_shift($raw);
						if(preg_match("/revision (.+)$/", $line, $parts))
						{
							$this->rev = $parts[1];
							$state = CVSLIB_LOG_DATE;
						}
						break;
					/* Found revision and filename, now looking for date */
					case CVSLIB_LOG_DATE:
						$line = array_shift($raw);
						if(preg_match("|^date:\s+(\d+)/(\d+)/(\d+)\s+(\d+):(\d+):(\d+);\s+author:\s+(\S+);\s+state:\s+(\S+);(\s+lines:\s+([0-9\s+-]+))?|", $line, $parts))
						{
							$this->date = mktime($parts[4], $parts[5], $parts[6], $parts[2], $parts[3], $parts[1]);
							$this->author = $parts[7];
							$this->state = $parts[8];
							$this->lines = isset($parts[10])?$parts[10]:'';
							$state = CVSLIB_LOG_BRANCHES;
						}
						break;
					/* Look for a branch point here - format is 'branches:  x.x.x;' */
					case CVSLIB_LOG_BRANCHES:
						// TODO: This only handles one branch per rev at the moment!  Need to setup
						// a test repository to find out the format of rlog output if more than one
						// branch point exists at a given revision number - avsm

						/* If we find a branch tag, process and pop it, otherwise leave input
						stream untouched */

						if(!empty($raw) && preg_match("/^branches:\s+([0-9\.]+);/",$raw[0],$br))
						{
							$this->branch = $br[1]; 
							array_shift($raw);
						}
						else
						{
							$this->branch = '';
						}
						$state = CVSLIB_LOG_DONE;
						break;
					default:
				}
			}

			/* Assume the rest of the lines are the log message */
			$this->log = implode("\n",$raw);
			$this->tags = @$this->file->revsym[$this->rev];
			if(empty($this->tags))
			{
				$this->tags = array();
			}
		}

		function queryDate()
		{
			return $this->date;
		}

		function queryRevision()
		{
			return $this->rev;
		}

		function queryAuthor()
		{
			return $this->author;
		}

		function queryLog()
		{
			return $this->log;
		}

		function queryChangedLines()
		{
			return isset($this->lines)?($this->lines):'';
		}

		/*
		* Return the logical revision before this one.  Normally, this
		* will be the revision minus one, but in the case of a new branch,
		* we strip off the last two decimal places to return the original
		* branch point.
		*
		* @return revision number, or false if none could be determined
		*/
		function queryPreviousRevision()
		{
			$parts = explode('.',$this->rev);
			$last = sizeof($parts)-1;
			if(--$parts[$last] > 0)
			{
				return implode('.',$parts);
			}
			else
			{
				array_pop($parts);
				array_pop($parts);
				if(sizeof($parts) > 0)
				{
					return implode('.',$parts);
				}
				else
				{
					return false;
				}
			} 
		}

		/*
		* Given a branch revision number, this function remaps it
		* accordingly, and performs a lookup on the file object to
		* return the symbolic name(s) of that branch in the tree.
		*
		* @return array of symbols, or false if none were found
		*/
		function querySymbolicBranches()
		{
			// TODO: simplify this logic :-) - avsm
			if(!empty($this->branch))
			{
				$parts = explode('.',$this->branch);
				$last = array_pop($parts);
				$parts[] = '0';
				$parts[] = $last;
				$rev = implode('.',$parts);
				if(isset($this->file->branches[$this->branch]))
				{
					return $this->file->branches[$this->branch];
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
?>

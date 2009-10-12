<?php 
	/*  See the README file that came with this library for more
	 *  information, and read the inline documentation.
	 *
	 *  Anil Madhavapeddy, <anil@recoil.org>
	 *  $Horde: chora/lib/CVSLib/Annotate.php,v 1.3 2001/01/26 20:37:45 chuck Exp $
	 */

	/**
	 * CVSLib annotate class.
	 *
	 * @author  Anil Madhavapeddy <anil@recoil.org>
	 * @version $Revision: 10073 $
	 * @since   Chora 0.2
	 * @package chora
	 */
	class cvslib_annotate
	{
		var $file, $CVS, $tmpfile;

		function cvslib_annotate($rep, $file)
		{
			$this->CVS = $rep;
			$this->file = $file;
		}
 
		function doAnnotate($rev)
		{
			/* Make sure that the file values for this object is valid */
			if(!is_object($this->file) || $this->file->id() != CVSLIB_FILE)
			{
				return false;
			}

			/* Make sure that the cvsrep parameter is valid */
			if(!is_object($this->CVS) || $this->CVS->id() != CVSLIB_REPOSITORY)
			{
				return false;
			}

			if(!CVSLib_Rev::valid($rev))
			{
				return false;
			}

			$where = $this->file->queryModulePath();
			$cvsroot = $this->CVS->cvsRoot();

			$this->tmpfile = tempnam('/tmp','choraannotate');
			$pipe = popen("/usr/bin/cvs -n -l server > ".$this->tmpfile,'w');

			$out = array();
			$out[] = "Root $cvsroot";
			$out[] = 'Valid-responses ok error Valid-requests Checked-in Updated Merged Removed M E';
			$out[] = 'UseUnchanged';
			$out[] = 'Argument -r';
			$out[] = "Argument $rev";
			$out[] = "Argument $where";
			$dirs = explode('/', dirname($where));
			while(sizeof($dirs))
			{
				$out[] = 'Directory ' . implode('/', $dirs);
				$out[] = "$cvsroot/" . implode('/', $dirs);
				array_pop($dirs);
			}
			$out[] = 'Directory .';
			$out[] = "$cvsroot";
			$out[] = 'annotate';

			@reset($out);
			while(list(,$line) = @each($out))
			{
				fwrite($pipe, "$line\n");
			}

			pclose($pipe);

			if(!($fl = fopen($this->tmpfile, 'rb')))
			{
				$this->_clean();
				exit;
			}

			$lines = array();
			$line = fgets($fl, 4096);
			if(!preg_match("|^E\s+Annotations for $where|", $line))
			{
				$this->_clean();
				return CreateObject(
					'chora.cvslib_error',
					CVSLIB_INTERNAL_ERROR,
					"Unable to annotate; server said: $line"
				);
			}

			while($line = fgets($fl, 4096))
			{
				if(preg_match('/^M\s+([\d\.]+)\s+\((\w+)\s+(\d+-\w+-\d+)\):.(.*)$/',$line, $regs))
				{
					$entry = array();
					$entry['rev'] = $regs[1];
					$entry['author'] = $regs[2];
					$entry['date'] = $regs[3];
					$entry['line'] = $regs[4];
					$lines[] = $entry;
				}
			}

			fclose($fl);
			$this->_clean();
			return $lines;
		}

		/**
		* Return what class this is for identification purposes
		* @return CVSLIB_ANNOTATE constant
		*/
		function id()
		{
			return CVSLIB_ANNOTATE;
		}

		/**
		* Private function to clean-up the temporary file
		*/
		function _clean()
		{
			if(isset($this->tmpfile))
			{
				@unlink($this->tmpfile);
			}
		}
	}

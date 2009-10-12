<?php
  /**************************************************************************\
  * phpGroupWare                                                             *
  * http://www.phpgroupware.org                                              *
  * The file written by Miles Lott <milosch@phpgroupware.org>                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

  /*
   * Portions of this code:
   * Copyright 1999, 2000, 2001 Anil Madhavapeddy <anil@recoil.org>
   * Copyright 1999, 2000, 2001 Charles Hagenbuch <chuck@horde.org>
   *
   * See the enclosed file COPYING for license information (GPL).  If you
   * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
   */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'enable_config_class' => True
	);
	include('../header.inc.php');
	include('./config/conf.php');

	if (@is_dir($fullname))
	{
		/* checkError is the error trapping function */
		checkError($dir = $CVS->queryDir($where));

		$atticFlags = $acts['sa'] ? CVSLIB_ATTIC_SHOW : CVSLIB_ATTIC_HIDE;

		checkError($dir->browseDir(CVSLIB_LOG_QUICK, $atticFlags));
		$dir->applySort($acts['sbt'],$acts['ord']);
		checkError($dirList = $dir->queryDirList());
		checkError($fileList = $dir->queryFileList($atticFlags));

		/* Decide what title to display */
		if ($where == '')
		{
			$title = $conf['options']['introTitle'];
		}
		else
		{
			$title = lang('CVS Directory of') . ' ' . $rt . $where;
		}

		if ($acts['sa'])
		{
			$extraLink='<a href="' . $GLOBALS['phpgw']->link('/chora/cvs.php','sa=0&where=' . $where) . '">Hide Deleted Files</a>';
		}
		else
		{
			$extraLink='<a href="'.$GLOBALS['phpgw']->link('/chora/cvs.php','sa=1&where=' . $where).'">Show Deleted Files</a>';
		}

		include($conf['paths']['templates'].'/page_header.tpl');

		$thisarr = array('age','rev','name','author');
		while(list(,$u) = @each($thisarr))
		{
			$umap = array(
				'age'    => CVSLIB_SORT_AGE,
				'rev'    => CVSLIB_SORT_REV,
				'name'   => CVSLIB_SORT_NAME,
				'author' => CVSLIB_SORT_AUTHOR
			);
			$arg = array('sbt'=>$umap[$u]);
			if ($acts['sbt'] == $umap[$u])
			{
				$arg['ord'] = !$acts['ord']; 
			}
			$url[$u] = url('cvs',$where, $arg);
		}
 
		/* Print out the directory header */
		$printAllCols = sizeof($fileList);
		include($conf['paths']['templates'].'/dir_header.tpl');

		/* Unless we're at the top, display the 'back' bar */
		if ($where != '')
		{
			$url = $GLOBALS['phpgw']->link('/chora/cvs.php','rt='.$rt.'&where=' . preg_replace('|[^/]+$|', '', $where));
			include($conf['paths']['templates'].'/dir_back.tpl');
		}

		/* Display all the directories first */
		$dirrow = 0;
		while (list($null,$currDir) = each($dirList))
		{
			$dirrow = (++$dirrow % 2);
			$url = $GLOBALS['phpgw']->link('/chora/cvs.php','rt='.$rt.'&where=' . $where . '/' . $currDir);
			include($conf['paths']['templates'].'/dir_directory.tpl');
		}

		/* Display all of the files in this directory */
		while (list($null,$currFile) = each($fileList))
		{
			$dirrow = (++$dirrow % 2);
			$lg = $currFile->queryLastLog();
			$name = $currFile->queryName();
			$aid = $lg->queryAuthor();
			$author = showAuthorName($aid);
			$head = $currFile->queryHead();
			$date = $lg->queryDate();
			$log  = $lg->queryLog();
			$attic = $currFile->isAtticFile();
			$fileName = $where.($attic?'/Attic':'')."/$name";
			$url = $GLOBALS['phpgw']->link('/chora/cvs.php','rt='.$rt.'&where=' . $fileName);
			$readableDate = CVSLib_File::readableTime($date);
			if ($log)
			{
				$shortLog = str_replace("\n"," - ",
				trim(substr($log, 0, $conf['options']['shortLogLength']-2)));
				if (strlen($log) > 80)
				{
					$shortLog .= "...";
				}
			}
			include($conf['paths']['templates'].'/dir_file.tpl');
		}
		/* Display the options control panel at the bottom */
		$formwhere = $scriptName.'/'.$where;

		include($conf['paths']['templates'].'/dir_footer.tpl');
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
	elseif (@is_file($fullname . ',v'))
	{
		$fl = CreateObject('chora.cvslib_file', $CVS, $fullname, CVSLIB_LOG_FULL);
		checkError($fl->getBrowseInfo());

		$fl->applySort(CVSLIB_SORT_AGE);

		$title = lang('CVS Log for') . ' ' . $rt . $where;

		$upwhere = preg_replace('|[^/]+$|', '', $where);
		$isBranch = isset($onb) && isset($fl->branches[$onb])?($fl->branches[$onb]):'';
		$extraLink = '<a href="' . $GLOBALS['phpgw']->link('/chora/history.php','rt='.$rt.'&where=' . $where) . '">Switch to Branch View</a>';

		include($conf['paths']['templates'].'/page_header.tpl');
		include($conf['paths']['templates'].'/diff_header.tpl');

		$mimeType = $CVS->getMimeType($fullname);
		$defaultTextPlain = ($mimeType == 'text/plain');

		while(list(,$lg)=each($fl->logs))
		{
			$rev = $lg->rev;
			/* Are we sticking only to one branch ? */
			if (isset($onb) && CVSLib_Rev::valid($onb))
			{
				/* If so, if we are on the branch itself, let it through */
				if (substr($rev,0,strlen($onb)) != $onb)
				{
					/* We are not on the branch, see if we are on a trunk
					* branch below the branch */
					$baseRev = CVSLib_Rev::strip($onb, 1);

					/* Check we are at the same level of branching or less */
					if (substr_count($rev,'.') <= substr_count($baseRev,'.'))
					{
						/* If we are at the same level, and the revision is
						* less, then let the revision through, since it was
						* committed before the branch actually took place
						*/
						if (CVSLib_Rev::cmp($rev,$baseRev) > 0)
						{
							continue;
						}
					}
					else
					{
						continue;
					}
				}
			}

			$textURL= $GLOBALS['phpgw']->link('/chora/checkout.php','r=' . $rev .'&rt='.$rt. '&where=' . $where) ;
			$commitDate = gmdate('jS F Y, g:ia', $lg->date);
			$readableDate = CVSLib_File::readableTime($lg->date, true);

			$aid = $lg->queryAuthor();
			$author = showAuthorName($aid, true);

			if (!empty($lg->tags))
			{
				$commitTags = implode(', ',$lg->tags);
			}
			else
			{
				$commitTags = '';
			}

			if (is_array($lg->querySymbolicBranches()))
			{
				$branchPoints = implode(',',$lg->querySymbolicBranches());
			}
			else
			{
				$branchPoints = '';
			}

			if ($prevRevision = $lg->queryPreviousRevision())
			{
				$changedLines = $lg->lines;
				$diffURL = $GLOBALS['phpgw']->link('/chora/diff.php','rt='.$rt.'&where=' . $where . '&r1=' . $prevRevision . '&r2=' . $rev . '&f=h');
				$uniDiffURL = $GLOBALS['phpgw']->link('/chora/diff.php','rt='.$rt.'&where=' . $where . '&r1=' . $prevRevision . '&r2=' . $rev . '&f=u');
			}

			$logMessage = htmlify($lg->log);
			include($conf['paths']['templates'].'/diff_rev.tpl');
		}

		$first = end($fl->logs);
		$diffValueLeft  = $first->rev;
		$diffValueRight = $fl->queryRevision();

		$sel = '';
		while (list($sm,$rv) = each($fl->symrev))
		{
			$sel .= '<option value="'.$rv.'">'.$sm.'</option>';
		}

		$selAllBranches = '';
		while (list($num,$sym) = each($fl->branches))
		{
			$selAllBranches .= '<option value="'.$num.'">'.$sym;
		}

		include($conf['paths']['templates'].'/diff_request.tpl');

		$GLOBALS['phpgw']->common->phpgw_footer();
	}
	else
	{
		fatal('404 Not Found', "$where: no such file or directory");
	}
?>

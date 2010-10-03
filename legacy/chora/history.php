<?php
/*
 * $Horde: chora/history.php,v 1.7 2001/02/27 07:06:00 avsm Exp $
 *
 * Copyright 2000, 2001 Anil Madhavapeddy <anil@recoil.org>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'enable_nextmatchs_class' => True,
		'enable_config_class' => True
	);
	include('../header.inc.php');
	include('./config/conf.php');

	$_rev = CreateObject('chora.cvslib_rev');

	/* Spawn the file object */
	$fl = CreateObject('chora.cvslib_file', $CVS, $CVS->cvsRoot() . '/' . $where);
	checkError($fl->getBrowseInfo());

	/* $trunk contains an array of trunk revisions */
	$trunk = array();

	/* $branches is a hash with the branch revision as the 
	 * key, and value being an array of revs on that branch */
	$branches = array();

	/* Populate $col with a list of all the branch points */
	@reset($fl->branches);
	while(list($rev,$sym) = @each($fl->branches))
	{
		$branches[$rev] = array();       
	}

	/* For every revision, figure out if it is a trunk
	 * revision, or instead associated with a branch.
	 * If trunk, add it to the $trunk array.
	 * Otherwise, add it to an array in $branches[$branch]
	 */

	@reset($fl->logs);
	while(list(,$log) = @each($fl->logs))
	{
		$rev = $log->queryRevision();
		$baseRev = $_rev->strip($rev, 1);
		$branchFound = false;
		@reset($fl->branches);
		while(list($branch,$name) = @each($fl->branches))
		{
			if($branch == $baseRev)
			{
				array_unshift($branches[$branch], $rev);
				$branchFound = true;
			}
		}
		/* If its not a branch, then add it to the trunk */
		/* TODO: this silently drops vendor branches atm! - avsm */
		if (!$branchFound && $_rev->sizeof($rev) == 2)
		{
			array_unshift($trunk, $rev);
		}
	}

	@reset($branches);
	while(list($col,$rows) = @each($branches))
	{
		/* If this branch has no actual commits on it, then it's a 
		* stub branch, and we can remove it for this view */
		if (!sizeof($rows))
		{
			unset($branches[$col]);
		}
	}

	$colset = array('#ccdeff','#eeccff','#ffeecc','#eeffcc','#ccffdd','#dcdba0');
	$colStack = array();
	$branchColours = array();
	@reset($branches);
	while(list($brrev,$brcont) = @each($branches))
	{
		if(!sizeof($colStack))
		{
			$colStack = $colset;
		}
		$branchColours[$brrev] = array_shift($colset);
	}

	/* This takes a row and a column, and recursively iterates through
	 * any sub-revisions or branches from the value that was already in 
	 * the grid at the co-ordinates that it was called with.
	 *
	 * Calling this function on every revision of the trunk is enough
	 * to render out the whole tree. */

	function populateGrid($row, $col)
	{
		global $grid, $branches;

		/* Figure out the starting revision this function uses */
		$rev = $grid[$row][$col];

		/* For every branch that is known, try to see if it forks here */
		$brkeys = array_keys($branches);

		/* NOTE: do not optimise to use foreach() or each() here, as
		* that really screws up the $branches pointer array due to the
		* recursion, and parallel branches fail - avsm */

		for($a=0; $a<sizeof($brkeys); $a++)
		{
			$brrev = $brkeys[$a];
			$brcont = $branches[$brrev]; 
			/* Check to see if current point matches a branch point */
			if(!strcmp($rev, $GLOBALS['_rev']->strip($brrev,1)))
			{
				/* If it does, figure out how many rows we have to add */
				$numRows = sizeof($brcont);
				/* Check rows in columns to the right, until one is free */
				$insCol = $col+1;
				while(1)
				{
					/* Look in the current column for a set value */
					$inc = False;
					for($i=$row; $i <=($row + $numRows); $i++)
					{
						if(isset($grid[$i][$insCol]))
						{
							$inc = true;
						}
					}
					/* If a set value was found, shift to the right and 
					* try again.  Otherwise, break out of the loop */

					if ($inc)
					{
						if (!isset($grid[$row][$insCol]))
						{
							$grid[$row][$insCol] = ':'.$brcont[0];
						}
						$insCol++; 
					}
					else
					{
						break;
					}
				}

				/* Put a fork marker in the top of the branch */
				$grid[$row][$insCol] = $brrev;

				/* Populate the grid with the branch values at this point */
				for ($i=0; $i < $numRows; $i++)
				{
					$grid[1+$i+$row][$insCol] = $brcont[$i];
				}
				/* For each value just set, check for sub-branches,
				* - but in reverse (VERY IMPORTANT!) */
				for ($i=$numRows-1; $i >= 0 ; $i--)
				{
					populateGrid(1+$i+$row, $insCol);
				}
			}
		}
	}

	/* Start row at the bottom trunk revision.  Since branches always
	 * go down, there can never be one above 1.1, and so this is a
	 * safe location to start.  We will then work our way up,
	 * recursively populating the grid with branch revisions
	 */
	for($row = sizeof($trunk)-1; $row >= 0; $row--)
	{
		$grid[$row][0] = $trunk[$row];
		populateGrid($row, 0);
	}

	/* Sort the grid array into row order, and determine the maximum column
	 * size that we need to render out in HTML */

	ksort($grid);
	$maxCol = 0;
	@reset($grid);
	while(list(,$cols) = @each($grid))
	{
		krsort($cols);
		list($val) = each($cols);
		$maxCol = max($val, $maxCol);
	}

	$title = "CVS Branching View for $where";
	$extraLink = '<a href="' . $GLOBALS['phpgw']->link('/chora/cvs.php','rt='.$rt.'&where=' . $where) . '">Switch to Log View</a>';
	include $conf['paths']['templates'].'/page_header.tpl';
	include $conf['paths']['templates'].'/history_header.tpl';

	@reset($grid);
	while(list(,$row) = @each($grid))
	{
		include $conf['paths']['templates'].'/history_row_start.tpl';

		/* Start traversing the grid of rows and columns */
		for ($i=0; $i<= $maxCol; $i++)
		{
			/* If this column has nothing in it, include a blank cell */
			if (!isset($row[$i]))
			{
				$bg='';
				include $conf['paths']['templates'].'/history_blank.tpl';
				continue;
			}

			/* Otherwise, this cell has content; determine what it is */
			$rev = $row[$i];

			if($_rev->valid($rev) && ($_rev->sizeof($rev) % 2))
			{
				/* This is a branch point, so put the info out */
				$bg = isset($branchColours[$rev])?$branchColours[$rev]:'white';
				$symname = $fl->branches[$rev];
				include $conf['paths']['templates'].'/history_branch_cell.tpl';
			}
			elseif(preg_match('|^:|',$rev))
			{
				/* This is a continuation cell, so render it with the branch colour */
				$bgbr = $_rev->strip(preg_replace('|^\:|','',$rev),1);
				$bg = isset($branchColours[$bgbr])?$branchColours[$bgbr]:'white';
				include $conf['paths']['templates'].'/history_blank.tpl';
			}
			elseif($_rev->valid($rev))
			{
				/* This cell contains a revision, so render it */
				$bgbr = $_rev->strip($rev,1);
				$bg = isset($branchColours[$bgbr])?$branchColours[$bgbr]:'white';
				$log = $fl->logs[$rev]; 
				$author = showAuthorName($log->queryAuthor());
				$date = gmdate('jS M Y',$log->queryDate());
				$lines = $log->queryChangedLines();
				include $conf['paths']['templates'].'/history_rev.tpl';
			}
			else
			{
				/* Exhausted other possibilities, just show a blank cell */
				include $conf['paths']['templates'].'/history_blank.tpl';
			}
		}
		include $conf['paths']['templates'].'/history_row_end.tpl';
	}

	include $conf['paths']['templates'].'/history_footer.tpl';
	$GLOBALS['phpgw']->common->phpgw_footer();
?>

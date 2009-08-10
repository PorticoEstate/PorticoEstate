<?php

/*
 * $Horde: chora/diff.php,v 1.25 2001/03/01 01:26:29 avsm Exp $
 *
 * Copyright 2000, 2001 Anil Madhavapeddy <anil@recoil.org>
 *
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

/**
  * Convert a line of text to be HTML-displayable
  * @param text The line of text to convert
  * @return The HTML-compliant converted text.  It always returns at least
  *         a non-breakable space, if the return would otherwise be empty.
  */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'noheader'   => False,
		'enable_config_class' => True
	);
	if($_GET['f'] == 'u')
	{
		$GLOBALS['phpgw_info']['flags']['noheader'] = True;
	}
	include('../header.inc.php');
	include('./config/conf.php');

	function htmlspaces($text='')
	{
		$text = htmlspecialchars($text);
		$text = str_replace("\t", '        ', $text);
		$text = str_replace('  ', ' &nbsp;', $text);
		return empty($text)?'&nbsp;':$text;
	}

	/* Spawn the repository and file objects */
	$fl = CreateObject(
		'chora.cvslib_file',
		$CVS,
		$CVS->cvsRoot() . '/' . $where
	);
	checkError($fl->getBrowseInfo());

	/* Initialize the GET variables properly */
	if (!isset($type))
	{
		$type = CVSLIB_DIFF_UNIFIED;
	}

	/* If r1 is zero, then we want to use the value specified
	 * in the text field tr1 */
	if ($r1==0 && isset($tr1))
	{
		$r1 = $tr1;
	}

	/* Similar to the above, for r2 */
	if ($r2==0 && isset($tr2))
	{
		$r2 = $tr2;
	}

	/* If no type has been specified, then default to unified */
	if (!isset($f))
	{
		$f = 'h';
	}

	/* Figure out what type of diff has been requested */
	switch ($f)
	{
		case 'u':
			$type = CVSLIB_DIFF_UNIFIED;
			break;
		case 'h':
			$type = CVSLIB_DIFF_UNIFIED;
			break;
		case 's':
			$type = CVSLIB_DIFF_COLUMN;
			break;
		case 'c':
			$type = CVSLIB_DIFF_CONTEXT;
			break;
		case 'e':
			$type = CVSLIB_DIFF_ED;
			break;
		default:
			$type = CVSLIB_DIFF_UNIFIED;
			break;
	}

	/* Ensure that we have valid revision numbers */
	$_rev = CreateObject('chora.cvslib_rev');
	if (!$_rev->valid($r1) || !$_rev->valid($r2))
	{
		checkError(CreateObject('chora.cvslib_error',CVSLIB_NOT_FOUND, 'Malformed Query'));
	}
	/* All is ok, proceed with the diff */
	elseif ($f != 'h')
	{
		/* A plain-text diff */
		header("Content-Type: text/plain\n\n");
		$diffa = CreateObject('chora.cvslib_diff');
		echo implode("\n",$diffa->get($CVS, $fl, $r1, $r2, $type));
	}
	else
	{
		/* Human-Readable diff */
		/* Output standard header information for the page */
		$filename = preg_replace('/^.*\//', '', $where);
		$pathname = preg_replace('/[^\/]*$/', '', $where);
		$title = "Diff for $where between version $r1 and $r2";

		include($conf['paths']['templates'].'/page_header.tpl');
		include($conf['paths']['templates'].'/hr_diff_header.tpl');

		/* Retrieve the tree of changes from CVSLib */
		$diffa = CreateObject('chora.cvslib_diff');
		$lns = $diffa->humanReadable(
			$diffa->get(
				$CVS, $fl, $r1, $r2, CVSLIB_DIFF_UNIFIED
			)
		);
		/* TODO: check for errors here (CVSLib_Error returned) - avsm */
		/* Is the diff empty? */
		if (!sizeof($lns))
		{
			include($conf['paths']['templates'].'/hr_diff_nochange.tpl');
		}
		else
		{
			/* Iterate through every header block of changes */
			@reset($lns);
			while(list(,$header) = @each($lns))
			{
				$lefthead = htmlspaces(@$header['oldline']);
				$righthead = htmlspaces(@$header['newline']);
				$headfunc = htmlspaces(@$header['function']);
				include($conf['paths']['templates'].'/hr_diff_row.tpl');

				/* Each header block consists of a number of changes (add, remove, change) */
				@reset($header['contents']);
				while(list(,$change) = @each($header['contents']))
				{
					switch ($change['type'])
					{
						case CVSLIB_DIFF_ADD:
							@reset($change['lines']);
							while(list(,$line) = @each($change['lines']))
							{
								$line = htmlspaces($line);
								include($conf['paths']['templates'].'/hr_diff_add.tpl');
							}
							break;
						case CVSLIB_DIFF_REMOVE:
							@reset($change['lines']);
							while(list(,$line) = @each($change['lines']))
							{
								$line = htmlspaces($line);
								include($conf['paths']['templates'].'/hr_diff_remove.tpl');
							}
							break;
						case CVSLIB_DIFF_EMPTY:
							$line = htmlspaces($change['line']);
							include($conf['paths']['templates'].'/hr_diff_empty.tpl');
							break;
						case CVSLIB_DIFF_CHANGE:
							/* Pop the old/new stacks one by one, until both are empty */
							while (sizeof($change['old']) || sizeof($change['new']))
							{
								if ($left = array_shift($change['old']))
								{
									$left = htmlspaces($left);
								}
								if ($right = array_shift($change['new']))
								{
									$right = htmlspaces($right);
								}
								include($conf['paths']['templates'].'/hr_diff_change.tpl');
							}
							break;
					}
				}
			}
		}
		// print legend
		include($conf['paths']['templates'].'/hr_diff_footer.tpl');
		$GLOBALS['phpgw']->common->phpgw_footer();
	}
?>

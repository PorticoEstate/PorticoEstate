<?php
/*
 * $Horde: chora/annotate.php,v 1.5 2001/02/27 07:05:59 avsm Exp $
 *
 * Copyright 2000, 2001 Anil Madhavapeddy <anil@recoil.org>
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

	$_rev = CreateObject('chora.cvslib_rev');

	/* Spawn the file object */
	$fl = CreateObject('chora.cvslib_file', $CVS, $CVS->cvsRoot() . '/' . $where);

	checkError($fl->getBrowseInfo());

	if(!isset($rev))
	{
		$rev='1.1';
	}

	if(!$_rev->valid($rev))
	{
		//fatal('404 Not Found',"Revision $rev not found");
		echo "Revision $rev not found";
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->exit();
	}

	$ann = CreateObject('chora.cvslib_annotate', $CVS, $fl);
	checkError($lines = $ann->doAnnotate($rev));

	$title = "CVS Annotation of $where for version $rev";
	include($conf['paths']['templates'].'/page_header.tpl');
	include($conf['paths']['templates'].'/annotate_header.tpl');

	@reset($lines);
	while(list(,$line) = @each($lines))
	{
		$author = showAuthorName($line['author']);
		$rev = $line['rev'];
		$line = htmlspecialchars($line['line']);
		include($conf['paths']['templates'].'/annotate_line.tpl');
	}

	include($conf['paths']['templates'].'/annotate_footer.tpl');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>

<?php

	/* Chora CSS information
	* This file is parsed by css.php, and used to produce a stylesheet.
	* $Horde: chora/config/html.php.dist,v 1.8 2001/02/01 01:18:22 avsm Exp $
	*/

	$css['body']['font-family'] = $GLOBALS['phpgw_info']['theme']['font'];
	$css['body']['font-size'] = '11pt';
	$css['body']['background-color'] = $GLOBALS['phpgw_info']['theme']['bg_color'];
	$css['body']['color'] = $GLOBALS['phpgw_info']['theme']['bg_text'];

	$css['a']['color'] = $GLOBALS['phpgw_info']['theme']['link'];
	$css['a']['font-family'] = 'Geneva,Arial,Helvetica,sans-serif';
	$css['a']['font-size'] = '11pt';
	$css['a']['text-decoration'] = 'none';

	$css['a:hover']['text-decoration'] = 'underline';
	$css['a:hover']['color'] = $GLOBALS['phpgw_info']['theme']['alink'];
	$css['a.footer:hover']['color'] = $GLOBALS['phpgw_info']['theme']['hovlink'];

	$css['.title']['font-size'] = '14pt';
	$css['.title']['font-weight'] = 'bold';

	$css['.outline-lite']['background-color'] = $GLOBALS['phpgw_info']['theme']['bg01'];
	$css['.outline-menu']['background-color'] = $GLOBALS['phpgw_info']['theme']['bg02'];

	$css['.menu']['color'] = '#222222';
	$css['.menu']['background-color'] = '#dfdfdf';
	$css['.menu']['font-family'] = $GLOBALS['phpgw_info']['theme']['font'];

	$css['.menuhead']['color'] = $GLOBALS['phpgw_info']['theme']['table_text'];
	$css['.menuhead']['background-color'] = $GLOBALS['phpgw_info']['theme']['table_bg'];
	$css['.menuhead']['font-family'] = $GLOBALS['phpgw_info']['theme']['font'];
	$css['.menuhead']['font-size'] = '14pt';

	$css['a.menuhead']['color'] = 'white';
	$css['a.menuhead']['font-family'] = 'Geneva,Arial,Helvetica,sans-serif';
	$css['a.menuhead']['font-size'] = '11pt';
	$css['a.menuhead:hover']['color'] = '#ddddff';

	$css['td']['font-size'] = '11pt';
	$css['td']['font-family'] = $GLOBALS['phpgw_info']['theme']['font'];

	$css['th']['font-size'] = '11pt';
	$css['th']['font-family'] = $GLOBALS['phpgw_info']['theme']['font'];

	$css['.header']['background-color'] = '#dedeee';
	$css['.header']['color'] = 'black';
	$css['.header-sel']['background-color'] = '#bbcbff';
	$css['.info']['background-color'] = '#d0d0d0';

	$css['.item0']['background-color'] = $GLOBALS['phpgw_info']['theme']['row_on'];
	$css['.item1']['background-color'] = $GLOBALS['phpgw_info']['theme']['row_off'];
	$css['.attic']['background-color'] = '#eedddd';
	$css['.attic']['font-style'] = 'italic';

	$css['.footer']['color'] = '#aaaaaa';
	$css['.footer']['font-style'] = 'italic';

	$css['.diff-back']['background-color'] = '#ffffff';
	$css['.diff-header']['background-color'] = '#cfcfd9';
	$css['.diff-log']['background-color'] = '#eeeeee';
	$css['.diff-request']['background-color'] = '#eeeeee';

	$css['.annotate-back']['background-color'] = '#ffffff';
	$css['.annotate-header']['background-color'] = '#dddddd';
	$css['.annotate-author']['background-color'] = '#eeeeff';
	$css['.annotate-rev']['background-color'] = '#ffeeee';

	$css['.hr-diff-back']['background-color'] = '#ffffff';
	$css['.hr-diff-add']['background-color'] = '#ccccff';
	$css['.hr-diff-change']['background-color'] = '#99ff99';
	$css['.hr-diff-nochange']['background-color'] = '#99cc99';
	$css['.hr-diff-remove']['background-color'] = '#ff9999';
	$css['.hr-diff-grey']['background-color'] = '#cccccc';
	$css['.hr-diff-context']['background-color'] = '#eeeeee';
	$css['.hr-diff-linenum']['background-color'] = '#99cccc';

	$css['.history']['background-color'] = '#f2f3ff';
	$css['.history-branch']['background-color'] = '#cceebb';
?>

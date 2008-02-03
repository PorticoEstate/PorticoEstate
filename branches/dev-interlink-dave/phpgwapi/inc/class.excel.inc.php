<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgwapi
	* @subpackage utilities
 	* @version $Id$
	*
	* Example
	* <code>
	*
	*	$GLOBALS['phpgw_info']['flags'] = array
	* 	(
	*		'noheader'	=> true,
	*		'nofooter'	=> true,
	*		'xslt_app'	=> false
	*	);
	*
 	*	$filename= $GLOBALS['phpgw_info']['user']['account_lid'].'.xls';
	*
	*	$workbook	= createObject('phpgwapi.excel',"-");
	*	$browser = createObject('phpgwapi.browser');
	*	$browser->content_header($filename,'application/vnd.ms-excel');
	*
	*	$worksheet1 =& $workbook->add_worksheet('First One');
	*	// have a look at phpgwapi/inc/excel/test.php on input
	*	$workbook->close();
	* </code>
	*/

	
	/**
	* @see worksheet
	*/
	require_once(PHPGW_API_INC . '/excel/Worksheet.php');

	/**
	* @see workbook
	*/
	require_once(PHPGW_API_INC . '/excel/Workbook.php');
?>

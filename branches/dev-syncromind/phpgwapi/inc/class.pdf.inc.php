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
	*	$GLOBALS['phpgw_info']['flags'] = array
	* 	(
	*		'noheader'	=> true,
	*		'nofooter'	=> true,
	*		'xslt_app'	=> false
	*	);
	*
	*	$pdf	= createObject('phpgwapi.pdf');
	*
	*	set_time_limit(1800); //allows for generation of complex documents
	*	$pdf -> ezSetMargins(50,70,50,50);
	*	$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
	*	
	*	//have a look at the function tender in /property/inc/class.uiwo_hour.inc.php for usage.
	*	
	*	$document= $pdf->ezOutput();
	*	$pdf->print_pdf($document,'document_name');
	* </code>
	*/

	/**
	* Document me!
	*
	* @package phpgwapi
	* @subpackage utilities
	*/
	class pdf___
	{
 
		public function __construct()
		{
			$this->tempPath = $GLOBALS['phpgw_info']['server']['temp_dir'];
		}
		/**
		* Output a pdf
		*
		* @param string $document the pdf document as a string
		* @param string $document_name the name to save the document as
		*/
		function print_pdf($document = '', $document_name = 'document')
		{	
			$browser = createObject('phpgwapi.browser');

			$size = strlen($document);
			$browser->content_header($document_name .'.pdf','application/x-pdf', $size);
			echo $document;
		}
	}

	/**
	* Include the pdf class
	* @see pdf_
	*/
	require_once PHPGW_API_INC . '/pdf/extensions/CezTableImage.php';
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
	class pdf__
	{
 
		/**
		* Output a pdf
		*
		* @param string $document the pdf document as a string
		* @param string $document_name the name to save the document as
		*/
		function print_pdf($document = '', $document_name = 'document')
		{	
			$browser = createObject('phpgwapi.browser');

//			if($browser->BROWSER_AGENT != 'IE')
			if(true)
			{
				$size = strlen($document);
				$browser->content_header($document_name .'.pdf','application/x-pdf', $size);
				echo $document;
			}
			else
			{
 				//save the file
				$dir = PHPGW_API_INC  . '/pdf/pdf_files';
 				if ( !is_dir($dir) )
 				{
 					die(lang('Directory for temporary pdf-files is missing - pleace notify the Administrator'));
 				}

 				if ( !is_writeable($dir) )
 				{
  					die(lang('Directory for temporary pdf-files is not writeable by the webserver - pleace notify the Administrator'));
 				}

 				$fname = tempnam($dir, 'PDF_') . '.pdf';
				file_put_contents($fname, $document, LOCK_EX);

  				//TODO consider using phpgw::redirect_link() ?
				$fname = 'phpgwapi/inc/pdf/pdf_files/'. basename($fname);
 				echo <<<HTML
		<html>
			<head>
				<script language="javascript">
				<!--
					function go_now()
					{
						window.location.href = "{$fname}";
					}
				//-->
				</script>
			</head>
			<body onload="go_now()";>
				<a href="$fname">click here</a> if you are not re-directed.
			</body>
		</html>

HTML;

				$this->_clear_cache($dir);
			}
		}

		/**
		 * Remove files that are older than a day
		 */
		protected function _clear_cache($dir)
		{
			$min_ctime = time() - (60*60);
			$dir = new DirectoryIterator($dir);
			foreach ( $dir as $fileinfo )
			{
				if ( preg_match('/^PDF_/', $fileinfo->getFilename())
					&& $fileinfo->getCTime() < $min_ctime )
				{
					unlink($fileinfo->getPathname());
				}
			}
		}
	}

	/**
	* Include the pdf class
	* @see pdf_
	*/
	require_once PHPGW_API_INC . '/pdf/class.pdf.php';

	/**
	* Include the ezpdf class
	* @see @pdf
	*/
	require_once PHPGW_API_INC . '/pdf/class.ezpdf.php';


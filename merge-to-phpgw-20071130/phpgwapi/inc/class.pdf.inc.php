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
 	* @version $Id: class.pdf.inc.php 17066 2006-09-03 11:14:42Z skwashd $
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
		function print_pdf($document = '',$document_name = 'document')
		{	
			$browser = createObject('phpgwapi.browser');

			if($browser->BROWSER_AGENT != 'IE')
			{
				$size=strlen($document);
				$browser->content_header($document_name .'.pdf','application/pdf',$size);
				echo $document;
			}
			else
			{
 				$dir = PHPGW_API_INC  . SEP . 'pdf' . SEP . 'pdf_files';
  
 				//save the file
 				if (!file_exists($dir))
 				{
 					die('Directory for temporary pdf-files is missing - pleace notify the Administrator');
 				}

 				$fname = tempnam($dir.SEP,'PDF_').'.pdf';
 
 				if(!$fp = @fopen($fname,'wb'))
 				{
  					die('Directory for temporary pdf-files is not writeable to the webserver - pleace notify the Administrator');				
 				}
 				
				fwrite($fp,$document);
				fclose($fp);

  				//TODO consider using phpgw::redirect_link() ?
				$fname = 'phpgwapi/inc/pdf/pdf_files/'. basename($fname);
 				echo '<html>
 				<head>
 				<SCRIPT LANGUAGE="JavaScript"><!-- 
 				function go_now ()   { window.location.href = "'.$fname.'"; }
 				//--></SCRIPT>
 				</head>
	 			<body onLoad="go_now()"; >
 				<a href="'.$fname.'">click here</a> if you are not re-directed.
 				</body>
 				</html>
 				';

 				// also have a look through the directory, and remove the files that are older than a week
 				if ($d = @opendir($dir))
 				{
 					while (($file = readdir($d)) !== false)
 					{
			 			if (substr($file,0,4)=="PDF_")
			 			{
 							// then check to see if this one is too old
 							$ftime = filemtime($dir.SEP.$file);
 							if (time()-$ftime > 3600*1) // one hour
 							{
 								unlink($dir.SEP.$file);
 							}
 						}
 					}  
 					closedir($d);
 				}
 			}
		}
	}

	/**
	* Include the pdf class
	* @see pdf_
	*/
	include (PHPGW_API_INC . '/pdf/class.pdf.php');

	/**
	* Include the ezpdf class
	* @see @pdf
	*/
	include (PHPGW_API_INC . '/pdf/class.ezpdf.php');
?>

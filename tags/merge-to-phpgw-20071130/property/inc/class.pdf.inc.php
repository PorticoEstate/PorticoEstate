<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package Cpdf
 	* @version $Id: class.pdf.inc.php,v 1.10 2007/01/26 14:53:46 sigurdne Exp $
	*/

	class property_pdf__
	{
		function print_pdf($document = '',$document_name = 'document')
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			
			$browser = CreateObject('phpgwapi.browser');

			if($browser->BROWSER_AGENT != 'IE')
			{
				$size=strlen($document);
				$browser->content_header($document_name .'.pdf','application/pdf',$size);
				echo $document;
			}
			else
			{
 				$dir = PHPGW_APP_INC  . '/..';
 				$dir = $dir . SEP . 'pdf_files';
  
 				//save the file
 				if (!file_exists($dir))
 				{
 					die('Directory for temporary pdf-files is missing - pleace notify the Administrator');
 				}

 				$fname = tempnam($dir.SEP,'PDF_').'.pdf';
 
 				$fp = fopen($fname,'w');
				fwrite($fp,$document);
				fclose($fp);

 				if (!file_exists($fname))
 				{
 					die('Directory for temporary pdf-files is not writeable to the webserver - pleace notify the Administrator');
 				}

  				$fname = $this->currentapp . '/pdf_files/'. basename($fname);
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
 							$ftime = filemtime($dir.'/'.$file);
 							if (time()-$ftime > 3600*24)
 							{
 								unlink($dir.'/'.$file);
 							}
 						}
 					}  
 					closedir($d);
 				}
 			}
		}
	}
	
	include (PHPGW_APP_INC . '/pdf/class.pdf.php');
	include (PHPGW_APP_INC . '/pdf/class.ezpdf.php');
?>

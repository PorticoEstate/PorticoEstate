<?php
	/**
	* phpGroupWare - Manual
	*
	* @author Piotr Maliński <riklaunim@gmail.com>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package manual
	* @subpackage odt2xhtml
 	* @version $Id: class.odt2xhtml.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * odt2xhtml converts odt to xhtml
	 * @package odt2xhtml
	 */


	class odt2xhtml
	{
		public function oo_unzip($file, $path = false)
		{
			if(!function_exists('zip_open'))
			{
				throw new Exception('NO ZIP FUNCTIONS DETECTED. Do you have the PECL ZIP extensions loaded?');
			}
			if(!is_file($file))
			{
				throw new Exception('Can\'t find file: '.$file);
			}
			if($zip = zip_open($file))
			{
				while ($zip_entry = zip_read($zip))
				{
					$filename = zip_entry_name($zip_entry);
					if(zip_entry_name($zip_entry) == 'content.xml' and zip_entry_open($zip, $zip_entry, "r"))
					{
						$content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						zip_entry_close($zip_entry);
					}
					if(ereg('Pictures/', $filename) and !ereg('Object', $filename)  and zip_entry_open($zip, $zip_entry, "r"))
					{
						$img[$filename] = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						zip_entry_close($zip_entry);
					}
				}
				if(isset($content))
				{
					if(isset($img) && is_array($img))
					{
						if(!is_dir($path.'Pictures'))
						{
							mkdir($path.'Pictures');
						}
						foreach($img as $key => $val)
						{
							file_put_contents($path.$key, $val);
						}
					}
					return $content;
				}
			}
		}

		public function oo_convert($xml)
		{
			$xls = new DOMDocument;
			$xls->load(PHPGW_SERVER_ROOT . '/manual/templates/base/odt2html.xsl');
		//	$xls->load(PHPGW_SERVER_ROOT . '/manual/templates/base/odt2xhtml.xsl');
		//	$xls->load('xslt/export/xhtml/ooo2xhtml.xsl');
		//	$xls->load('template.xsl');
			$xslt = new XSLTProcessor;
			$xslt->importStylesheet($xls);
		
			$x = preg_replace('#<draw:image xlink:href="Pictures/([a-z .A-Z_0-9]*)" (.*?)/>#es', "ODT2XHTML::makeImage('\\1')", $xml);
		
			$xml = new DOMDocument;
			$xml->loadXML($x);
			return $xslt->transformToXML($xml);
		}

		public function makeImage($img)
		{
			return '&lt;img src="Pictures/'.$img.'" border="0" /&gt;';
		}
	}
?>

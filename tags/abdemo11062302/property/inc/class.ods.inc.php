<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage property
	 * @category utilities
 	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Document me!
	*
	* @package property
	* @subpackage utilities
	*/
	if ( !isset($GLOBALS['phpgw_info']['server']['temp_dir'])  
			|| !is_dir($GLOBALS['phpgw_info']['server']['temp_dir']) )
	{
		if ( substr(PHP_OS, 3) == 'WIN' )
		{
			$GLOBALS['phpgw_info']['server']['temp_dir'] = 'c:/temp';
		}
		else
		{
			$GLOBALS['phpgw_info']['server']['temp_dir'] = '/tmp';
		}
	}


	/**
	* Include the ods class
	* @see ods
	*/
	require_once PHPGW_INCLUDE_ROOT . '/property/inc/ods/ods.php';


	class property_ods extends ods
	{
		/**
		* Constructor
		*/
		public function __construct()
		{

		}

		function parseOds($file) 
		{
			$tmp = $GLOBALS['phpgw_info']['server']['temp_dir'];
			copy($file,$tmp.'/'.basename($file));
			$path = $tmp.'/'.basename($file);
			$uid = uniqid();
			mkdir($tmp.'/'.$uid);
			shell_exec('unzip '.escapeshellarg($path).' -d '.escapeshellarg($tmp.'/'.$uid));
			$this->parse(file_get_contents($tmp.'/'.$uid.'/content.xml'));
			return $this;
		}

		function saveOds($obj,$file)
		{
			$tmp = $GLOBALS['phpgw_info']['server']['temp_dir'];
			$uid = uniqid();
			mkdir($tmp.'/'.$uid);
			file_put_contents($tmp.'/'.$uid.'/content.xml',$obj->array2ods());
			file_put_contents($tmp.'/'.$uid.'/mimetype','application/vnd.oasis.opendocument.spreadsheet');
			file_put_contents($tmp.'/'.$uid.'/meta.xml',$obj->getMeta('es-ES'));
			file_put_contents($tmp.'/'.$uid.'/styles.xml',$obj->getStyle());
			file_put_contents($tmp.'/'.$uid.'/settings.xml',$obj->getSettings());
			mkdir($tmp.'/'.$uid.'/META-INF/');
			mkdir($tmp.'/'.$uid.'/Configurations2/');
			mkdir($tmp.'/'.$uid.'/Configurations2/acceleator/');
			mkdir($tmp.'/'.$uid.'/Configurations2/images/');
			mkdir($tmp.'/'.$uid.'/Configurations2/popupmenu/');
			mkdir($tmp.'/'.$uid.'/Configurations2/statusbar/');
			mkdir($tmp.'/'.$uid.'/Configurations2/floater/');
			mkdir($tmp.'/'.$uid.'/Configurations2/menubar/');
			mkdir($tmp.'/'.$uid.'/Configurations2/progressbar/');
			mkdir($tmp.'/'.$uid.'/Configurations2/toolbar/');
			file_put_contents($tmp.'/'.$uid.'/META-INF/manifest.xml',$obj->getManifest());
			shell_exec('cd '.$tmp.'/'.$uid.';zip -r '.escapeshellarg($file).' ./');
			$this->advancedrmdir("{$tmp}/{$uid}");
		}

		function newOds()
		{
			$content = '<?xml version="1.0" encoding="UTF-8"?>
				<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0"><office:scripts/><office:font-face-decls><style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:automatic-styles><style:style style:name="co1" style:family="table-column"><style:table-column-properties fo:break-before="auto" style:column-width="2.267cm"/></style:style><style:style style:name="ro1" style:family="table-row"><style:table-row-properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true"/></style:style><style:style style:name="ta1" style:family="table" style:master-page-name="Default"><style:table-properties table:display="true" style:writing-mode="lr-tb"/></style:style></office:automatic-styles><office:body><office:spreadsheet><table:table table:name="Hoja1" table:style-name="ta1" table:print="false"><office:forms form:automatic-focus="false" form:apply-design-mode="false"/><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table><table:table table:name="Hoja2" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table><table:table table:name="Hoja3" table:style-name="ta1" table:print="false"><table:table-column table:style-name="co1" table:default-cell-style-name="Default"/><table:table-row table:style-name="ro1"><table:table-cell/></table:table-row></table:table></office:spreadsheet></office:body></office:document-content>';
			$this->parse($content);	
			return $this;
		}

		/**
		 * Removes a directory recursively
		 * This code is borrowed from kisgabo94 at freemail dot hu
		 * @link http://no.php.net/manual/en/function.rmdir.php#84377
		 *
		 * @param string $path path to directory to delete
		 *
		 * @return bool true on success
		 */

		function advancedrmdir($path)
		{
			$origipath = $path;
			$handler = opendir($path);
			while (true)
			{
				$item = readdir($handler);
				if ($item == "." or $item == "..")
				{
					continue;
				}
				else if (gettype($item) == "boolean")
				{
					closedir($handler);
					if (!@rmdir($path))
					{
						return false;
					}
					if ($path == $origipath)
					{
						break;
					}
					$path = substr($path, 0, strrpos($path, "/"));
					$handler = opendir($path);
				}
				else if (is_dir($path."/".$item))
				{
					closedir($handler);
					$path = $path."/".$item;
					$handler = opendir($path);
				}
				else
				{
					unlink($path."/".$item);
				}
			}
			return true;
		}
	}

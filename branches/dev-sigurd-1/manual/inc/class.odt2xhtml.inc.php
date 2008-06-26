<?php
/***
 * class PHP odt2xhtml
 * Copyright (C) 2006  Stephane HUC
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * Contact information:
 *   Stephane HUC
 *   <devs@stephane-huc.net>
 *
 ***/

/******************************************************************************/
/***   Dont touch at the rest !                                             ***/
/******************************************************************************/

class odt2xhtml
{
	public $dir=array();
	public $file=array();
	
	private $attribute='';
	private $body='';
	private $code='';
	private $content='';
	private $debug='';
	private $ext=array();
	private $id='';
	private $meta='';
	private $pattern='';
	private $position=array();
	private $style='';
	private $title='';
	private $version=''; /*** php version ***/

	/***
	    publics functions
	***/
	
	public function __construct($file)
	{
		$this->version = substr(VERSION, 0, 1);	// obtain 1rst string php version
		
		$this->dir['odf'] = $GLOBALS['phpgw_info']['server']['temp_dir'];	// dir root
		
		$this->file['full_path'] = $file;
		$file = basename($file);
		$this->file['name'] = substr($file, 0, -4);		// name file without extension
		$this->file['ext'] = substr($file, -3, 3);		// just extension
		
		$this->dir['odf_tmp'] = $this->dir['odf'].'/tmp';	// tmp dir 
		$this->dir['html'] = $this->dir['odf'].'/html';		// tmp dir html
		$this->dir['img'] = $this->dir['html'].'/img';		// tmp dir img
		
		/*** define dir for attribute img src ***/
		if(defined('IMG_SRC')) $this->dir['img_src'] = IMG_SRC.'img';
		else $this->dir['img_src'] = 'img';
		
		$this->file['dir_tmp'] = $this->dir['odf_tmp'].'/php'.$this->version.'_'.$this->file['name'].'_'.$this->file['ext'];
		$this->file['html_tmp'] = $this->file['dir_tmp'].'_tmp.html';	// file html tmp
		$this->file['xml_tmp'] = $this->file['dir_tmp'].'.xml';	// file xml tmp
		
		$this->dir['img_php'] = $this->dir['img'].'/php'.$this->version;
		$this->dir['img_php_ext'] = $this->dir['img_php'].'/'.$this->file['ext'];
		$this->dir['img_OOo'] = $this->dir['img_php_ext'].'/'.$this->file['name'];
		$this->dir['pictures'] = $this->file['dir_tmp'].'/Pictures';
		
		$this->debug = 0;
	}
	
	public function __destruct() 
	{
	}
	
	/*** Converter ODT TO XHTML ***/
	public function convert2xhtml()
	{
		/*** verify if file's extension is really odf format ****/
		$this->_valid_ext();
		
		/*** making all directories ***/
		$this->_mk_all_dir();
		
		/*** unzip odf file ***/
		$this->_unzip_odf();
		
		/*** choose better file xsl ***/
		$this->_choose_valid_xsl();
		
		/***  make new file xml with ODF files xml ***/
		$this->_make_new_xml_with_odf_files();
		
		/*** move file img to directory img ***/
		$this->_mv_img();
		
		/*** Create temporary file html by xslt processor ***/
		$this->_xslt_convert_odf();
		
		/*** Create real file html ***/
		$this->_create_file_html();		

	}
	
	/*** delete directory temporary ***/
	public function delete_tmp($path='')
	{	
	//	ini_set('memory_limit', '24M');
	//	ini_set('max_execution_time', 0);
		if(empty($path)) $path = $this->dir['odf_tmp'];
		
		chmod($path, 0777);
		$dir_tmp = opendir($path);
		
		/*** tant que la condition est juste en type alors on lit ***/
		while(false !== ($file_tmp = readdir($dir_tmp)))
		{
			if($file_tmp != '.' && $file_tmp != '..')
			{
				$path_tmp = $path.'/'.$file_tmp;
				
				if(!empty($path_tmp) && is_file($path_tmp)) 
				{
					unlink($path_tmp);
				}
				else 
				{
					$this->delete_tmp($path_tmp);
				}
			}
		}
		
		closedir($dir_tmp);
		rmdir($path);
		
		if(!empty($this->debug)) echo 'Directory '.$path.' is deleted: OK!<br/>';
		
	//	ini_set('memory_limit', MEM);
	//	ini_set('max_execution_time', MAX_TIME);
	}
	
	/*** try to obtain elements when file html is created ***/
	public function get_elements_html() {
		$this->subject = file_get_contents($this->dir['html'].'/'.$this->file['name'].'.html');
	
		$this->pattern['meta'] = '`<meta (.*?) />`es';
		$this->pattern['css'] = '`<style type="text/css">(.*?)</style>`es';
		$this->pattern['body'] = '`<body>(.*?)</body>`es';
		$this->pattern['title'] = '`<h1 class="(.*?)">(.*?)</h1>`es';
		
		foreach($this->pattern as $k => $v) {
			preg_match_all($v, $this->subject, $this->match); 
			$this->elements[$k] = $this->match;
		}
		
		return $this->elements;
	}
	
	public function display_elements_html($info,$x) {
		$this->code='';
		if($x != '0' && $x != '1') die('You can\'t use other digit, than 0 or 1, in the method display_elements_html() !');
		
		if(!empty($this->elements[$info]) && is_array($this->elements[$info])) {
			foreach($this->elements[$info][$x] as $v) {
				$this->code .= $v."\n";
			}
			echo $this->code;
		}
		else die('The searched element <strong>'.$info.'</strong> in the method display_elements_html() is empty or not exists ... try-it with \'css\',\'body\',\'meta\', or \'title\' !');
	}
	
	/***
	    privates functions
	***/
	
	/*** transform values cm in px ***/
	function _analyze_attribute($attribute) {
		
		if(ereg('cm',$attribute)) {
			if(ereg(' ',$attribute)) $xploz = explode(' ',$attribute);
			
			if(!empty($xploz) && is_array($xploz)) {
				foreach($xploz as $k => $v) {
					if(ereg('cm$',$v)) {
						$v = round(floatval($v)*28.6264);
						$xploz[$k] = $v.'px';
					}

					for($i=0;$i<count($xploz);$i++) {
						if($i==0) $this->attribute = $xploz[$i];
						else $this->attribute .= ' '.$xploz[$i];
					}
				}
			}
			else {
				$this->attribute = round(floatval($attribute)*28.6264);
				$this->attribute = $this->attribute.'px';
			}
			unset($xploz);
		}
		else $this->attribute = $attribute;

		$this->attribute = '="'.$this->attribute.'"';
		
		return $this->attribute;
	}
	
	/*** choose better file xsl segun file's extension ***/
	function _choose_valid_xsl() {
		switch($this->file['ext']) {
			case 'odt' :
			case 'ott' :
				$this->file['xsl'] = PHPGW_APP_TPL . '/odt2xhtml.xsl';
			break;
			case 'sxw' :
			case 'stw' :
				$this->file['xsl'] = PHPGW_APP_TPL.'/sxw2xhtml.xsl';
			break;
		}
		
		if(!empty($this->debug)) { echo 'Choosing valid XSL: ok!<br/>'; }
	}
	
	/*** create ultimate file html ***/
	function _create_file_html() {
		/*** modify title in html flux ***/
		$this->file['tmp'] = $this->_replace_content('title');
		
		/*** Create real file html ***/
		$this->_write_file($this->dir['html'].'/'.$this->file['name'].'.html','w',$this->file['tmp']);
		
		if(!empty($this->debug)) { echo 'Creating File HTML: OK!<br/>'; }
	}
	
	/*** make image code html ***/
	private function _make_image($name) 
	{
		$this->code = 'xlink:href="'.$this->dir['img_src'].'/php'.$this->version.'/'.$this->file['ext'].'/'.$this->file['name'].'/'.$name.'"';
		return $this->code;
	}
	
	/*** make new file xml with ODF files xml ***/
	private function _make_new_xml_with_odf_files() 
	{		
		/*** make file xml ***/
		$this->content = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		
		switch($this->file['ext']) {
			case 'odt' :
			case 'ott' :
				$this->content .= $this->_replace_content('open_element_xml4odt');
			break;
			case 'sxw' :
			case 'stt' :
				$this->content .= $this->_replace_content('open_element_xml4sxw');
			break;
		}
		
		$this->_write_file($this->file['dir_tmp'].'.xml','w',$this->content);
		
		/*** add meta.xml in file xml ***/
		$this->file['xml'] = $this->file['dir_tmp'].'/meta.xml';
		
		if(file_exists($this->file['xml']) && is_readable($this->file['xml'])) 
		{
			$this->_read_xml($this->file['xml']);
			
			/*** modify the content ***/
			$this->content = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$this->content);
			
			switch($this->file['ext']) {
				case 'sxw' :
				case 'stw' :
					$this->content = str_replace('<!DOCTYPE office:document-meta PUBLIC "-//OpenOffice.org//DTD OfficeDocument 1.0//EN" "office.dtd">','',$this->content);
				break;
			}
			
			/*** build new file xml segun the modified content ***/
			$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
			
			
			if(!empty($this->debug)) { echo 'Making File XML - Part Meta : ok!<br/>'; }
		}
		else die('Problem with file: '.$this->file['xml']);
		
		/*** add styles.xml in file xml modified ***/
		$this->file['xml'] = $this->file['dir_tmp'].'/styles.xml';
		
		if(file_exists($this->file['xml']) && is_readable($this->file['xml'])) 
		{
			$this->_read_xml($this->file['xml']);
			
			/*** modify the content ***/
			$this->content = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$this->content);
			
			switch($this->file['ext']) {
				case 'sxw' :
				case 'stw' :
					$this->content = str_replace('<!DOCTYPE office:document-styles PUBLIC "-//OpenOffice.org//DTD OfficeDocument 1.0//EN" "office.dtd">','',$this->content);
				break;
			}
			
			# analyze attribute to transform style's value cm in px
			$this->content = $this->_replace_content('analyze_attribute');
			
			if(preg_match_all('/<style:header>(.*)<\/style:header>/Us',$this->content,$this->match)) 
				$this->header = str_replace('style:header','text:header',$this->match[0][0]);
			if(preg_match_all('/<style:footer>(.*)<\/style:footer>/Us',$this->content,$this->match))
				$this->footer = str_replace('style:footer','text:footer',$this->match[0][0]);
			
			/*** build new file xml segun the modified content ***/
			$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
			
			
			if(!empty($this->debug)) { echo 'Making File XML - Part Styles: ok!<br/>'; }
		}
		else die('Problem with file: '.$this->file['xml']);
		
		/*** add content.xml in file xml modified ***/
		$this->file['xml'] = $this->file['dir_tmp'].'/content.xml';
		
		if(file_exists($this->file['xml']) && is_readable($this->file['xml'])) 
		{
			$this->_read_xml($this->file['xml']);
			
			/*** modify the content ***/
			$this->content = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$this->content);
			
			switch($this->file['ext']) {
				case 'odt' :
				case 'ott' :
					# add header page
					if(!empty($this->header))	$this->content = str_replace('<office:text>','<office:text>'.$this->header,$this->content); 
					# modify src img
					$this->content = $this->_replace_content('img_odt');
					# add footer page
					if(!empty($this->footer))	$this->content = str_replace('</office:text>',$this->footer.'</office:text>',$this->content); 
				break;
				case 'sxw' :
				case 'stw' :
					$this->content = str_replace('<!DOCTYPE office:document-content PUBLIC "-//OpenOffice.org//DTD OfficeDocument 1.0//EN" "office.dtd">','',$this->content);
					# add header page
					if(!empty($this->header))	$this->content = str_replace('<office:body>','<office:body>'.$this->header,$this->content); 
					# modify src img
					$this->content = $this->_replace_content('img_sxw');
					# add footer page
					if(!empty($this->footer))	$this->content = str_replace('</office:body>',$this->footer.'</office:body>',$this->content); 
				break;
			}

			# analyze attribute to transform style's value cm in px
			$this->content = $this->_replace_content('analyze_attribute');
			# search text in position indice or exposant to transform it correctly
			$this->_rewrite_position();
			
			/*** build new file xml segun the modified content ***/
			$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
			
			if(!empty($this->debug)) { echo 'Making File XML - Part Content: ok!<br/>'; }
		}
		else die('Problem with file: '.$this->file['xml']);
		
		/*** modify the content ***/
		$this->content = "\n".'</office:document>'."\n";
		
		/*** terminate file xml ***/
		$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
		
		
		if(!empty($this->debug)) { echo 'Making File XML - Terminated: ok!<br/>'; }
	}

	private function _mk_all_dir() {
		umask(0000);
		
		/*** making temporary dir ***/
		if(!file_exists($this->dir['odf_tmp']) && !is_dir($this->dir['odf_tmp'])) {
			mkdir($this->dir['odf_tmp'], 0777);
			if(!empty($this->debug)) { echo 'Making Dir TMP '.$this->dir['odf_tmp'].': OK!<br/>'; }
		}
		
		/*** making directory to receive file img ***/
		if(!file_exists($this->dir['img']) and !is_dir($this->dir['img']))
		{
			mkdir($this->dir['img'], 0777);
			if(!empty($this->debug)) { echo 'Making Dir IMG '.$this->dir['img'].': OK!<br/>'; }
		}
			
		if(!file_exists($this->dir['img_php']) and !is_dir($this->dir['img_php']))
		{
			mkdir($this->dir['img_php'], 0777);
			if(!empty($this->debug)) { echo 'Making Dir '.$this->dir['img_php'].': OK!<br/>'; }
		}
			
		if(!file_exists($this->dir['img_php_ext']) and !is_dir($this->dir['img_php_ext']))
		{
			mkdir($this->dir['img_php_ext'], 0777);
			if(!empty($this->debug)) { echo 'Making Dir '.$this->dir['img_php_ext'].': OK!<br/>'; }
		}
		
		if(!file_exists($this->dir['img_OOo']) and !is_dir($this->dir['img_OOo']))
		{
			mkdir($this->dir['img_OOo'], 0777);
			if(!empty($this->debug)) { echo 'Making Dir '.$this->dir['img_OOo'].': OK!<br/>'; }
		}
		
		/*** making dir to receive file html ***/
		if(!file_exists($this->dir['html']) && !is_dir($this->dir['html'])) 
		{
			mkdir($this->dir['html'], 0777);
			if(!empty($this->debug)) { echo 'Making Dir HTML: OK!<br/>'; }
		}
		
		if(!empty($this->debug)) { echo 'All dir are making: OK!<br/>'; }
	}

	/*** modify title code html ***/
	private function _modify_title()
	{
		$this->title = "<head>\n\t<title>&quot;".$this->file['name'].'.'.$this->file['ext'].'&quot; :: converted by Odt2Xhtml in PHP5</title>';
		return $this->title;
	}
	
	/*** make directory image and moving images ***/
	private function _mv_img()
	{		
		/*** move img ***/
		if(file_exists($this->dir['pictures']) && $this->handle = opendir($this->dir['pictures']))
		{			
			while( false !== $this->file['img'] = readdir($this->handle) )
			{
				if(is_file($this->dir['pictures'].'/'.$this->file['img']))
				{					
					/*** move img at temp directory to img directory ***/
					if(rename($this->dir['pictures'].'/'.$this->file['img'], $this->dir['img_OOo'].'/'.$this->file['img'])) {
						chmod($this->dir['img_OOo'].'/'.$this->file['img'], 0644);
						
						if(!empty($this->debug)) { echo 'Moving IMG '.$this->file['img'].': ok!<br/>'; }
					}
					else die('Image '.$this->file['img'].' can\'t be moved.');
				}
			}
			closedir($this->handle);
		}
		//else die('Directory can\'t be opened: '.$this->dir['img']['tmp']);
	}
	
	/*** read file xml ***/
	private function _read_xml($xml) {
		$this->handle = fopen($xml, 'r');
		$this->content = fread($this->handle, filesize($this->file['xml']));
		fclose($this->handle);
	}
	
	/*** replace content ***/
	private function _replace_content($info) {
		switch($info) {
			case 'analyze_attribute' :
				$this->exec=1;
				$this->search = '/="(.*?)"/es';
				$this->replace = "odt2xhtml::_analyze_attribute('$1')";
				$this->subject = $this->content;
			break;
			case 'img_odt' :
				$this->exec=1;
				$this->search = '#xlink:href="Pictures/([.a-zA-Z_0-9]*)"#es';
				$this->replace = "odt2xhtml::_make_image('$1')";
				$this->subject = $this->content;
			break;
			case 'img_sxw' :
				$this->exec=1;
				$this->search = '!xlink:href="\#Pictures/([.a-zA-Z_0-9]*)"!es';
				$this->replace = "odt2xhtml::_make_image('$1')";
				$this->subject = $this->content;
			break;
			case 'open_element_xml4odt' :
				$this->buffer = '<office:document xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0">';
			break;
			case 'open_element_xml4sxw' :
				$this->buffer = '<office:document xmlns:office="http://openoffice.org/2000/office">';
			break;
			case 'title' :
				$this->exec=1;
				$this->search = '/<head>/es';
				$this->replace = "odt2xhtml::_modify_title()";
				$this->subject = $this->file['tmp'];
			break;
		}
		
		if(!empty($this->exec)) $this->buffer = preg_replace($this->search,$this->replace,$this->subject);
		
		return $this->buffer;
		
		unset($this->buffer,$this->exec);
	}
	
	/*** search text in position indice or exposant and transform it ***/
	private function _rewrite_position() 
	{
		# search styles text-position
		switch($this->file['ext']) {
			case 'odt' :
			case 'ott' :
				if(preg_match_all('`<style:style style:name="T([0-9]+)" style:family="text"><style:text-properties style:text-position="(.*?)"/></style:style>`es',$this->content,$this->match)) {
					$this->_make_position($this->match);
				}
			break;
			case 'sxw' :
			case 'stw' :
				if(preg_match_all('`<style:style style:name="T([0-9]+)" style:family="text"><style:properties style:text-position="(.*?)"/></style:style>`es',$this->content,$this->match)) {
					$this->_make_position($this->match);
				}
			break;
		}
		unset($this->match);
		# search text relative to style text-position
		if(!empty($this->position) && preg_match_all('`<text:span text:style-name="T([0-9]+)">(.*?)</text:span>`es',$this->content,$this->match)) {
			
			foreach($this->match[1] as $k => $v) {
				if(in_array($v,$this->position['name'])) {
					foreach($this->position['name'] as $k2 => $v2) {
						if($v2 == $v) {
							$this->position['search'][$k2] = '<text:span text:style-name="T'.$this->position['name'][$k2].'">'.$this->match[2][$k].'</text:span>';
							$this->position['replace'][$k2] = '<text:'.$this->position['string'][$k2].' text:style-name="T'.$this->position['name'][$k2].'">'.$this->match[2][$k].'</text:'.$this->position['string'][$k2].'>';
						}
					}
				}
			}
		}
		unset($this->match);
		# replace search text position par replace text position
		if(!empty($this->position['search']) && is_array($this->position['search'])) {
			foreach($this->position['search'] as $k => $v) {
				$this->content = str_replace($v, $this->position['replace'][$k], $this->content);
			}
		}
		unset($this->position);
	}
	
	/*** Unzip file ODT ***/
	private function _unzip_odf() 
	{
		$archive = CreateObject('phpgwapi.pclzip', $this->file['full_path']);
		
		if($archive->extract(PCLZIP_OPT_PATH, $this->file['dir_tmp']) == 0) 
		{
			die('Error: '.$archive->errorInfo(true));
		}
		
		if(!empty($this->debug)) { echo 'Unzip ok!<br/>'; }
		
	}
	
	/*** Verify if extension is really odt ***/
	private function _valid_ext() 
	{
		$this->ext['valid'] = array('odt', 'ott', 'stw', 'sxw');
		
		if(!in_array($this->file['ext'], $this->ext['valid'])) {
			die('No valid extension ! The script stop it ! Your extension could be odt, ott or sxw, stw...');
		}
		
		if(!empty($this->debug)) { echo 'Valid extension!<br/>'; }
	}
	
	/*** write file ***/
	private function _write_file($filename,$mode,$resource) {
		$this->handle = fopen($filename,$mode);
		fwrite($this->handle,$resource);
		fclose($this->handle);
	}
	
	/*** PHP Convert XML ***/
	private function _xslt_convert_odf() 
	{
		$xls = new DOMDocument();
		$xls->load($this->file['xsl']);

		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xls);

		$xml = new DOMDocument();
		$xml->load($this->file['xml_tmp']);

		$this->file['tmp'] = html_entity_decode($xslt->transformToXML($xml));
		if(!empty($this->debug)) { echo 'Convert ODF to Temporary File HTML: ok!<br/>'; }
	}
	
	/***
		Protected functions
	***/
	
	/*** this function is run by method _rewrite_position() ***/
	protected function _make_position($match) {
		if(!empty($match) && is_array($match)) {
			foreach($match[1] as $k => $v) {
				$this->position['name'][$k] = $v;
				$this->position['string'][$k] = substr($match[2][$k], 0, 3);
			}
		}
	}
}
?>

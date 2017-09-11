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
	private $html=array();
	private $meta='';
	private $mssg=array();
	private $path='';
	private $pattern='';
	private $position=array();
	private $style='';
	private $title='';
	private $version=''; /*** php version ***/

	/***
	    publics functions
	***/
	
	public function __construct($root, $frontend, $file)
	{
		$this->message = new display_message();
		$this->version = substr(ODT2XHTML_PHPVERSION, 0, 1);	// obtain 1rst string php version
		
		if(ODT2XHTML_PHPCLI==TRUE) {
			$this->dir['odf_in'] = $root;
			$this->dir['odf_out'] = $frontend;
		}
		else $this->dir['odf_out'] = $root.$frontend;	// dir root
		
		$this->strrpos['.'] = strrpos($file, ".");	// search last occurence of "."
		$this->file['name'] = substr($file, 0, $this->strrpos['.']);		// name file without extension
		$this->file['ext'] = str_replace($this->file['name'].'.', '', $file);		// just extension

		$_fname = basename($this->file['name']);
		$this->file['html'] = "{$_fname}.html";//$this->file['name'].'.html';
		$this->file['css'] = "{$_fname}.css";//$this->file['name'].'.css'; 
		
		$this->dir['odf_tmp'] = $this->dir['odf_out'].'tmp';	// tmp dir 
		$this->dir['html'] = $this->dir['odf_out'].'odt2xhtml';		// dir html
		$this->dir['img'] = $this->dir['html'].'/img';		// dir img
		
		/*** define dir for attribute img src ***/
		if(defined('IMG_SRC')) $this->dir['img_src'] = IMG_SRC.'img';
		else $this->dir['img_src'] = 'img';
		
//		$this->file['dir_tmp'] = $this->dir['odf_tmp'].'/php'.$this->version.'_'.$this->file['name'].'_'.$this->file['ext'];
		$this->file['dir_tmp'] = $this->dir['odf_tmp'].'/php'.$this->version.'_'.$_fname.'_'.$this->file['ext'];
		$this->file['html_tmp'] = $this->file['dir_tmp'].'_tmp.html';	// file html tmp
		$this->file['xml_tmp'] = $this->file['dir_tmp'].'.xml';	// file xml tmp
		
		$this->dir['img_php'] = $this->dir['img'].'/php'.$this->version;
		$this->dir['img_php_ext'] = $this->dir['img_php'].'/'.$this->file['ext'];
//		$this->dir['img_OOo'] = $this->dir['img_php_ext'] . $this->file['name'];
		$this->dir['img_OOo'] = $this->dir['img_php_ext'].'/'.$_fname;
		$this->dir['pictures'] = $this->file['dir_tmp'].'/Pictures';
		
		if(ODT2XHTML_DEBUG==TRUE)
		{
			_debug_array($this->dir);
			_debug_array($this->file);
		}
		
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
		
		/***  make new file xml with ODF files xml ***/
		$this->_make_new_xml_with_odf_files();
		
		/*** move file img to directory img ***/
		$this->_mv_img();
				
		/*** choose better file xsl ***/
		$this->_choose_valid_xsl();

		/*** Create temporary file html by xslt processor ***/
		$this->_xslt_convert_odf();
		
		/*** Create real file html ***/
		$this->_create_files();	

		/*** Move icone ***/
		$this->_mv_icon();	

	}
	
	/*** delete directory temporary ***/
	public function delete_tmp($path='')
	{	
		ini_set('memory_limit', '24M');
		ini_set('max_execution_time', 0);
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
		
		if(ODT2XHTML_DEBUG==TRUE) echo $this->message->display('ok','dir_deleted',$path);
		
		ini_set('memory_limit', ODT2XHTML_MEM);
		ini_set('max_execution_time', ODT2XHTML_MAX_TIME);
	}
	
	/*** try to obtain elements when file html is created ***/
	public function get_elements_html() {
		$this->subject = file_get_contents($this->dir['html'].'/'.$this->file['html']);

		$this->pattern['meta'] = '`<meta (.*?) />`es';
		if(ODT2XHTML_FILE_CSS==FALSE) $this->pattern['css'] = '`<style type="text/css">(.*?)</style>`es';
		$this->pattern['body'] = '`<body>(.*?)</body>`es';
		$this->pattern['title'] = '`<h1 class="(.*?)">(.*?)</h1>`es';
		
		foreach($this->pattern as $k => $v) {
			preg_match_all($v, $this->subject, $this->match); 
			$this->elements[$k] = $this->match;
		}
		
		if(ODT2XHTML_FILE_CSS==TRUE) {
			$this->buffer = file_get_contents($this->dir['html'].'/'.$this->file['css']);
			$this->elements['css'][0][0] = '<style type="text/css">'.$this->buffer.'</style>';
			$this->elements['css'][1][0] = $this->buffer;
			unset($this->buffer);
		}
		
		return $this->elements;
	}
	
	public function display_elements_html($info,$x) {
		$this->code='';
		if($x != '0' && $x != '1') die($this->message->display('ko','bad_digit'));
		
		if(!empty($this->elements[$info]) && is_array($this->elements[$info])) {
			foreach($this->elements[$info][$x] as $v) {
				$this->code .= $v."\n";
			}
			echo $this->code;
		}
		else die($this->message->display('ko','searched_element',$info));
	}
	
	/***
	    privates functions
	***/
	
	/*** transform values cm in px ***/
	private function _analyze_attribute($attribute) {
		
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
				$this->file['xsl'] = ODT2XHTML_XSL_ROOT.'/odt2xhtml.xsl';
			break;
			case 'sxw' :
			case 'stw' :
				$this->file['xsl'] = ODT2XHTML_XSL_ROOT.'/sxw2xhtml.xsl';
			break;
		}
		
		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','valid_xsl'); }
	}
	
	/*** create file css ***/
	function _create_file_css() { 
		if(preg_match_all('/<style type="text\/css">(.*)<\/style>/es',$this->file['tmp'],$this->match))	{
			$this->buffer = trim($this->match[1][0]); 
			$this->_write_file($this->dir['html'].'/'.$this->file['css'],'w',$this->buffer);
			unset($this->buffer);
		}
		$this->file['tmp'] = $this->_replace_content('link_css');

		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','creating_file_css'); }
	}

	/*** create ultimate files ***/
	function _create_files() {
		
		/*** modify title in html flux ***/
		$this->file['tmp'] = $this->_replace_content('title');
		
		/*** manage to create file css ***/
		if(ODT2XHTML_FILE_CSS==TRUE) $this->_create_file_css();

		/*** Create real file html ***/
		$this->_write_file($this->dir['html'].'/'.$this->file['html'],'w',$this->file['tmp']);
		
		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','creating_file_html'); }
	}
	
	
	//FIXME - make this public accessible through the browser
	/*** make image code html ***/
	private function _make_image($name) 
	{
	//	$this->code = 'xlink:href="'.$this->dir['img_src'].'/php'.$this->version.'/'.$this->file['ext'].'/'.$this->file['name'].'/'.$name.'"';
		$this->code = 'xlink:href="'.$this->dir['img_src'].'/php'.$this->version.'/'.$this->file['ext'].'/'.$name.'"';
	//	$this->code = 'xlink:href="'.$this->dir['img_OOo'].'/'.$name.'"';

		return $this->code;
	}
	
	/*** make new file xml with ODT2XHTML files xml ***/
	private function _make_new_xml_with_odf_files() 
	{
		$this->doctype = '<!DOCTYPE office:document-meta PUBLIC "-//OpenOffice.org//DTD OfficeDocument 1.0//EN" "office.dtd">';
		$this->xml_version = '<?xml version="1.0" encoding="UTF-8"?>';
		/*** make file xml ***/
		$this->content = $this->xml_version."\n";
		
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
			$this->content = str_replace($this->xml_version,'',$this->content);
			
			switch($this->file['ext']) {
				case 'sxw' :
				case 'stw' :
					$this->content = str_replace($this->doctype,'',$this->content);
				break;
			}
			
			/*** build new file xml segun the modified content ***/
			$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
			
			
			if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_meta'); }
		}
		else die($this->message->display('ko','pb_file',$this->file['xml']));
		
		/*** add styles.xml in file xml modified ***/
		$this->file['xml'] = $this->file['dir_tmp'].'/styles.xml';
		
		if(file_exists($this->file['xml']) && is_readable($this->file['xml'])) 
		{
			$this->_read_xml($this->file['xml']);
			
			/*** modify the content ***/
			$this->content = str_replace($this->xml_version,'',$this->content);
			
			switch($this->file['ext']) {
				case 'sxw' :
				case 'stw' :
					$this->content = str_replace($this->doctype,'',$this->content);
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
			
			
			if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_style'); }
		}
		else die($this->message->display('ko','pb_file',$this->file['xml']));
		
		/*** add content.xml in file xml modified ***/
		$this->file['xml'] = $this->file['dir_tmp'].'/content.xml';
		
		if(file_exists($this->file['xml']) && is_readable($this->file['xml'])) 
		{
			$this->_read_xml($this->file['xml']);
			
			/*** modify the content ***/
			$this->content = str_replace($this->xml_version,'',$this->content);

			/*** try to recuperate text:h level 1 to include in element html title : *** EXPERIMENTAL *** ***/
			if(preg_match_all('/<text:h text:style-name="(.*)" text:outline-level="1">(.*?)<\/text:h>/',$this->content,$this->match))
				$this->html['title'] = strip_tags($this->match[2][0]);
			
			/*** add header and footer page ***/
			switch($this->file['ext']) {
				case 'odt' :
				case 'ott' :
					# add header page
					if(!empty($this->header)) $this->content = $this->_replace_content('header');
					# modify src img
					$this->content = $this->_replace_content('img_odt');
					# add footer page
					if(!empty($this->footer)) $this->content = str_replace('</office:text>',$this->footer.'</office:text>',$this->content); 
				break;
				case 'sxw' :
				case 'stw' :
					$this->content = str_replace($this->doctype,'',$this->content);
					# add header page
					if(!empty($this->header)) $this->content = $this->_replace_content('header');
					# modify src img
					$this->content = $this->_replace_content('img_sxw');
					# add footer page
					if(!empty($this->footer)) $this->content = str_replace('</office:body>',$this->footer.'</office:body>',$this->content); 
				break;
			}
			
			# rebuild text:reference-mark-* in text:reference-mark syntax xml correct : manage element html abbr 
			$this->content = $this->_replace_content('reference_mark');

			# analyze attribute to transform style's value cm in px
			$this->content = $this->_replace_content('analyze_attribute');
			
			# search text in position indice or exposant to transform it correctly
			$this->_rewrite_position();
			
			/*** build new file xml segun the modified content ***/
			$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
			
			if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_content'); }
		}
		else die($this->message->display('ko','pb_file',$this->file['xml']));
		
		/*** modify the content ***/
		$this->content = "\n".'</office:document>'."\n";
		
		/*** terminate file xml ***/
		$this->_write_file($this->file['dir_tmp'].'.xml','a',$this->content);
		
		
		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','file_xml'); }
	}

	private function _mk_all_dir() {
		umask(0000);
		
		/*** making temporary dir ***/
		if(!file_exists($this->dir['odf_tmp']) && !is_dir($this->dir['odf_tmp'])) {
			if(!mkdir($this->dir['odf_tmp'], 0777, true)) die($this->message->display('ko','making_dir_tmp',$this->dir['odf_tmp']));
			else { 
				if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_dir_tmp',$this->dir['odf_tmp']); } 
			}
		}
		
		/*** making directory to receive file img ***/
		if(!file_exists($this->dir['img']) and !is_dir($this->dir['img']))
		{
			if(!mkdir($this->dir['img'], 0777, true)) die($this->message->display('ko','making_dir_img',$this->dir['img']));
			else {
				if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_dir_img',$this->dir['img']); }
			}
		}
			
		if(!file_exists($this->dir['img_php']) and !is_dir($this->dir['img_php']))
		{
			if(!mkdir($this->dir['img_php'], 0777, true)) die($this->message->display('ko','making_dir_img_php',$this->dir['img_php']));
			else {
				if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_dir_img_php',$this->dir['img_php']); }
			}
		}
			
		if(!file_exists($this->dir['img_php_ext']) and !is_dir($this->dir['img_php_ext']))
		{
			if(!mkdir($this->dir['img_php_ext'], 0777, true)) die($this->message->display('ko','making_dir_imp_php_ext',$this->dir['img_php_ext']));
			else {
				if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_dir_img_php_ext',$this->dir['img_php_ext']); }
			}
		}
		
		if(!file_exists($this->dir['img_OOo']) and !is_dir($this->dir['img_OOo']))
		{
			if(!mkdir($this->dir['img_OOo'], 0777, true)) die($this->message->display('ko','making_dir_img_ooo',$this->dir['img_OOo']));
			else {
				if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_dir_img_ooo',$this->dir['img_OOo']); }
			}
		}
		
		/*** making dir to receive file html ***/
		if(!file_exists($this->dir['html']) && !is_dir($this->dir['html'])) 
		{
			if(!mkdir($this->dir['html'], 0777, true)) die($this->message->display('ko','making_dir_html',$this->dir['html']));
			else {
				if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_dir_html',$this->dir['html']); }
			}
		}
		
		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','making_all_dir'); }
	}

	/*** modify appel css ***/
	private function _modify_css() 
	{
		$this->link_css = '<link rel="stylesheet" href="'.$this->file['css'].'" type="text/css" media="screen" title="Default" />';
		return $this->link_css;
	}

	/*** modify title code html ***/
	private function _modify_title()
	{
		$this->title = "<head>\n\t<title>&quot;";
		if(ODT2XHTML_TITLE=='element_title' && !empty($this->html['title'])) $this->title .= $this->html['title'];
		else $this->title .= $this->file['name'].'.'.$this->file['ext'];
		$this->title .= '&quot;';
		$this->title .= ODT2XHTML_PUB;
		$this->title .= '</title>';
		return $this->title;
	}

	/*** move icon ***/
	private function _mv_icon() 
	{
		$this->icon = array ('favicon.ico','icone.png');

		foreach($this->icon as $v) {
			if(!copy(ODT2XHTML_ROOT.'/'.$v,$this->dir['img'].'/'.$v)) echo $this->message->display('ko','icon_copy',$v);
			else if(ODT2XHTML_DEBUG==TRUE) echo $this->message->display('ok','icon_copy',$v);
		}
	}
	
	//FIXME - make images public accessible through the browser - see also _make_image()
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
						
						if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','moving_img',$this->file['img']); }
					}
					else die($this->message->display('ko','moving_img',$this->file['img']));
				}
			}
			closedir($this->handle);
		}
	}
	
	/*** replace content ***/
	private function _replace_content($info) {
		$callback = false;
		switch($info) {
			case 'analyze_attribute' :
				$this->exec=1;
				$this->search = '/="(.*?)"/s';
				$this->replace = "odt2xhtml::_analyze_attribute('$1')";
				$this->subject = $this->content;
				$callback = true;
			break;
			case 'header' :
				$this->exec=1;
				$this->search = '!<office:forms(.*?)/>!';
				$this->replace = '<office:forms$1/>'.$this->header;
				$this->subject = $this->content;
			break;
			case 'img_odt' :
				$this->exec=1;
				$this->search = '#xlink:href="Pictures/([.a-zA-Z_0-9]*)"#s';
				$this->replace = "odt2xhtml::_make_image('$1')";
				$this->subject = $this->content;
				$callback = true;
			break;
			case 'img_sxw' :
				$this->exec=1;
				$this->search = '!xlink:href="\#Pictures/([.a-zA-Z_0-9]*)"!s';
				$this->replace = "odt2xhtml::_make_image('$1')";
				$this->subject = $this->content;
				$callback = true;
			break;
			case 'link_css' :
				$this->exec=1;
				$this->search = '/<style type="text\/css">(.*)<\/style>/s';
				$this->replace = "odt2xhtml::_modify_css()";
				$this->subject = $this->file['tmp'];
				$callback = true;
			break;
			case 'open_element_xml4odt' :
				$this->buffer = '<office:document xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0">';
			break;
			case 'open_element_xml4sxw' :
				$this->buffer = '<office:document xmlns:office="http://openoffice.org/2000/office">';
			break;
			case 'reference_mark' :
				$this->exec=1;
				$this->search = '/<text:reference-mark-start text:name="(.*)"\/>(.*)<text:reference-mark-end text:name="(.*)"\/>/SU';
				$this->replace = '<text:reference-mark text:name="$1">$2</text:reference-mark>';
				$this->subject = $this->content;
			break;
			case 'title' :
				$this->exec=1;
				$this->search = '/<head>/s';
				$this->replace = "odt2xhtml::_modify_title";
				$this->subject = $this->file['tmp'];
				$callback = true;
			break;
		}
		
		if(!empty($this->exec))
		{
			if($callback)
			{
				$this->buffer = preg_replace_callback($this->search,$this->replace,$this->subject);
			}
			else
			{
				$this->buffer = preg_replace($this->search,$this->replace,$this->subject);
				
			}
		}
		
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
		if(ODT2XHTML_PHPCLI==TRUE)
		{
			$_file = $this->dir['odf_in'].'/'.$this->file['name'].'.'.$this->file['ext'];
		}
		else
		{
			$_file = "{$this->file['name']}.{$this->file['ext']}";
		}

		$archive = CreateObject('phpgwapi.pclzip', $_file);
		if($archive->extract(PCLZIP_OPT_PATH, $this->file['dir_tmp']) == 0) 
		{
			die($this->message->display('ko','zip_extract').$archive->errorInfo(true));
		}
		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','unzip'); }
	}
	
	/*** Verify if extension is really odt ***/
	private function _valid_ext() 
	{
		$this->ext['valid'] = array('odt', 'ott', 'stw', 'sxw');
		
		if(!in_array($this->file['ext'], $this->ext['valid'])) {
			die($this->message->display('ko','extension'));
		}
		
		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','extension'); }
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

	/*** read file xml ***/
	protected function _read_xml($xml) {
		$this->handle = fopen($xml, 'r');
		$this->content = fread($this->handle, filesize($this->file['xml']));
		fclose($this->handle);
	}

	/*** write file ***/
	protected function _write_file($filename,$mode,$resource) {
		$this->handle = fopen($filename,$mode);
		fwrite($this->handle,$resource);
		fclose($this->handle);
	}
	
	/*** PHP Convert XML ***/
	protected function _xslt_convert_odf() 
	{
		$xls = new DOMDocument();
		$xls->load($this->file['xsl']);
		
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xls);

		$xml = new DOMDocument();
		$xml->load($this->file['xml_tmp']);
		
		$this->file['tmp'] = html_entity_decode($xslt->transformToXML($xml));

		if(ODT2XHTML_DEBUG==TRUE) { echo $this->message->display('ok','convert_odf'); }
	}

}
<?php
/***
 * class PHP display_message : to display message in class odt2xhtml
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

class display_message {
	
	private $ko;
	private $ok;
	private $mssg;

	public function __construct() {
		$this->ko = array (
			'bad_digit'	=> "You can't use other digit, than 0 or 1, in the method display_elements_html() !",
			'extension'	=> 'No valid extension ! The script stop it ! Your extension could be odt, ott or sxw, stw...',
			'icon_copy'	=> 'Error on copying icone ',
			'making_dir_html'	=> 'Making Dir HTML: ',	// $this->dir['html']
			'making_dir_img'	=> 'Making Dir IMG: ',	// $this->dir['img']
			'making_dir_img_ooo'	=> 'Making Dir: ',	// $this->dir['img_OOo']
			'making_dir_img_php'	=> 'Making Dir: ',	// $this->dir['img_php']
			'making_dir_img_php_ext'	=> 'Making Dir: ',	// $this->dir['img_php_ext']
			'making_dir_tmp'	=> 'Making Dir TMP: ',	// $this->dir['odf_tmp']
			'moving_img'	=> "Image can't be moved!",
			'pb_file'	=> 'Problem with file: ',
			'searched_element'	=> "The searched element in the method display_elements_html() is empty or not exists ... try-it with 'css','body','meta', or 'title' !",
			'zip_disabled'	=> "The support ZIP isn't active on your PHP!",
			'zip_extract'	=> 'Error on extracting archive: ',
			'zip_open'	=> 'Error on opening archive: ',
		);
		
		$this->ok = array (
			'convert_odf'	=> 'Convert ODF to Temporary File HTML: ',
			'creating_file_css'	=> 'Creating File CSS: ',
			'creating_file_html'	=> 'Creating File HTML: ',
			'dir_deleted'	=> 'Directory is deleted: ',
			'extension'	=> 'Extension : ', // $this->file['ext']
			'file_xml'	=> 'Making File XML - Terminated: ',
			'icon_copy'	=> 'Icon copied successfully ',
			'making_all_dir'	=> 'All dir are making: ',
			'making_content'	=> 'Making File XML - Part Content: ',
			'making_dir_html'	=> 'Making Dir HTML: ',
			'making_dir_img'	=> 'Making Dir IMG: ',	// $this->dir['img']
			'making_dir_img_ooo'	=> 'Making Dir: ',	// $this->dir['img_OOo']
			'making_dir_img_php'	=> 'Making Dir: ',	// $this->dir['img_php']
			'making_dir_img_php_ext'	=> 'Making Dir: ',	// $this->dir['img_php_ext']
			'making_dir_tmp'	=> 'Making Dir TMP: ',	// $this->dir['odf_tmp']
			'making_meta'	=> 'Making File XML - Part Meta: ',
			'making_style'	=> 'Making File XML - Part Styles: ',
			'moving_img'	=> 'Moving IMG: ',
			'unzip'	=> 'Unzip: ',
			'valid_xsl'	=> 'Choosing valid XSL: ',
		);

	}

	public function __destruct() {
		unset($this->mssg);
	}
	
	/***
		$value 
		$index
		$var : is variable to obtain in method
	***/
	public function display($value,$index,$var='') {
		switch($value) {
			case 'ko' :
				$this->mssg = '<p style="color:red;">';
				if(!empty($var)) $this->mssg .= $this->ko[$index].' (<strong>'.$var.'</strong>)';
				else $this->mssg .= $this->ko[$index];
				$this->mssg .= ' <strong style="color:red;">KO</strong>!</p>';
			break;
			case 'ok' :
				$this->mssg = '<p>';
				if(!empty($var)) $this->mssg .= $this->ok[$index].' (<strong>'.$var.'</strong>)';
				else $this->mssg .= $this->ok[$index];
				$this->mssg .= ' <strong style="color:green;">OK</strong>!</p>';
			break;
		}
		
		if(ODT2XHTML_PHPCLI == TRUE) return strip_tags($this->mssg)."\n";
		else return $this->mssg."\n";
	}
}
?>

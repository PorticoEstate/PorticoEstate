<?php 

class module_google extends Module
{
	function module_google()
	{
		$this->arguments = array();
		$this->properties = array();
		$this->title = lang('Google');
		$this->description = lang('Interface to Google website');
	}

	function get_content(&$arguments,$properties)
	{
		$content = '<form action="http://www.google.com/search" name=f>';
		$content .= '<img src="images/Google_25wht.gif" border="0" align="middle" hspace="0" vspace="0"><br>';
		$content .= '<center><input type=hidden name=hl value=en>';
		$content .= '<input type=hidden name=ie value="ISO-8859-1">';
		$content .= '<input maxLength=256 size=15 name=q value=""><br>';
		$content .= '<input type=submit value="' . lang('Google Search') . '" name=btnG></center>';
		$content .= '</form>';
		return $content;
	}
}

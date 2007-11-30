<?php 

class module_login extends Module
{
	function module_login()
	{
		$this->arguments = array(
			'registration' => array(
				'type' => 'checkbox',
				'label' => lang('Display link to autoregistration below login form?')
			)
		);
		$this->properties = array();
		$this->title = lang('Login');
		$this->description = lang('This module displays a login form');
	}

	function get_content(&$arguments,$properties)
	{
		$content = '<form name="login" action="'.phpgw_link('/login.php').'" method="post">';
		$content .= '<input type="hidden" name="passwd_type" value="text">';
		$content .= '<input type="hidden" name="phpgw_forward" value="/sitemgr/">';
		$content .= '<center><font class="content">' . lang('Login Name') .'<br>';
		$content .= '<input type="text" name="login" size="8" value=""><br>';
		$content .= lang('Password') . '<br>';
		$content .= '<input name="passwd" size="8" type="password"><br>';
		$content .= '<input type="submit" value="' . lang('Login') .'" name="submitit">';
		$content .= '</font></center></form>';
		if ($arguments['registration'])
		{
			$content .= '<center><font class="content">' . lang("Don't have an account?") .'  ';
			$content .= '<a href="'.phpgw_link('/registration/index.php').'">';
			$content .= lang('Register for one now.') . '</a></font></center>';
		}
		return $content;
	}
}

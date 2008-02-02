<?php
  /**************************************************************************\
  * phpGroupWare - skel                                                      *
  * http://www.phpgroupware.org                                              *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
	/* $Id$ */

	class uicvs
	{
		var $cvs;

		var $public_functions = array(
			'index' => True
		);

		function uicvs()
		{
			$this->cvs = CreateObject('chora.cvs');
			$this->template = $GLOBALS['phpgw']->template;
		}

		function index()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$newval = get_var('newval',Array('POST')); 
			$command = $newval['command'];
			if(!$command)
			{
				$command = 'log';
			}

			echo '<form method="post" action="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'cvs.uicvs.index')) . '">';
			echo $this->formatted_list($command,$this->cvs->commands,'command');

			echo '</form>';
			//$this->cvs->connect('log',array('module' => 'addressbook', 'fname' => 'index.php', 'version' => ''));
			/*
			$this->cvs->connect('log',array(
				'module'   => 'notes',
				'fname'    => 'index.php',
				'version'  => '1.28',
				'conflict' => '',
				'options'  => '',
				'tag'      => '', // T or D
				'args'     => '-bh' // -b only default branch, -N don't show tags, -h Only print header, -l local(no recursion), -R name of RCS file only
			));
			*/
			$this->cvs->connect($command,array('module' => 'todo'));
			//$this->cvs->connect('co',array('module' => array('todo','notes')));
			$this->cvs->disconnect();

			echo '<br>Request:';
			_debug_array($this->cvs->request);
			echo '<br>Response:';
			_debug_array($this->cvs->response);
			//_debug_array($this->cvs->debug);
			//_debug_array($this->cvs->files['fname']);
			//_debug_array($this->cvs->files['modtime']);
			//_debug_array($this->cvs->files['mode']);
		//	_debug_array($this->cvs->files['size']);
		//	_debug_array($this->cvs->files['extra']);
		//	_debug_array($this->cvs->files['mt']);

		//	$dir = $this->cvs->fs->ls('/',array(RELATIVE_USER));
		//	_debug_array($dir);
			$arr = $this->cvs->fs->ls('/',array(RELATIVE_USER));
			_debug_array($arr);
			$GLOBALS['phpgw']->common->phpgw_footer();
		}

		/* Return a selectbox */
		function formatted_list($id=0,$list,$name='',$java=True)
		{
			if (!$name)
			{
				return False;
			}

			if ($java)
			{
				$jselect = ' onChange="this.form.submit();"';
			}

			$select  = "\n" .'<select name="newval[' . $name . ']"' . $jselect . ">\n";
			$select .= '<option value="0">' . lang('Please Select') . '</option>'."\n";
			while (list($val,$x) = each($list))
			{
				$select .= '<option value="' . $val . '"';
				if ($val == $id)
				{
					$select .= ' selected';
				}
				$select .= '>' . $val . '</option>'."\n";
			}

			$select .= '</select>'."\n";
			$select .= '<noscript><input type="submit" name="style_select" value="{lang_select}"></noscript>' . "\n";

			return $select;
		}
	}
?>

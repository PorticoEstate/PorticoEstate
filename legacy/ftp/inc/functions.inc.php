<?php
	/**************************************************************************\
	* phpGroupWare - Ftp Module                                                *
	* http://www.phpgroupware.org                                              *
	* Written by Scott Moser <smoser@brickies.net>                             *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	//NOTE: This is a hack to fix the register_globals problems with this app in 16
	//TODO: Fix it properly
	foreach($_GET as $name => $value)
	{
		$$name = $value;
	}
	
	foreach($_POST as $name => $value)
	{
		$$name = $value;
	}
	
	function createLink($string,$params='')
	{
		return $GLOBALS['phpgw']->link($string,$params);
	}

	function getConnectionInfo() 
	{
		return $GLOBALS['phpgw']->session->appsession('default', '');
	}

	function phpftp_connect($host,$user,$pass) 
	{
		// echo "connecting to $host with $user and $pass\n";
		$ftp = ftp_connect($host);
		if ( $ftp ) 
		{
			if ( ftp_login($ftp,$user,$pass) ) 
			{
				return $ftp;
			}
		}
	}

	function renameForm($session,$filename,$directory) 
	{
		$rename_form_begin= '<form action="' . createLink($GLOBALS['target']) . '" method="post">'."\n"
			. '<input type="hidden" name="action" value="rename">'."\n"
			. '<input type="hidden" name="olddir" value="'.$directory.'">'."\n"
			. '<input type="hidden" name="newdir" value="'.$directory.'">'."\n"
			. '<input type="hidden" name="filename" value="'.$filename.'">'."\n";
		$rename_form_end = '</form>'."\n";
		$rename_form_from=  $filename;
		$rename_form_to='<input type="text" name="newfilename" size="20" value="">';
		$rename_form_submit='<input type="submit" name="confirm" value="' . lang('rename') . '">'."\n";
		$rename_form_cancel='<input type="submit" name="cancel" value="' . lang('cancel') . '">'."\n";

		$GLOBALS['phpgw']->template->set_var(array(
			'rename_form_begin' => $rename_form_begin,
			'rename_form_end'  => $rename_form_end,
			'rename_form_from' => $rename_form_from,
			'rename_form_to' => $rename_form_to,
			'rename_form_submit' => $rename_form_submit,
			'rename_form_cancel' => $rename_form_cancel,
			'lang_rename_from' => lang('rename from'), 
			'lang_rename_to' => lang('rename to')
		));

		$GLOBALS['phpgw']->template->set_var('lang_message',lang('Rename file'));

		$GLOBALS['phpgw']->template->parse('out','rename',true);
		// $template->p('renameform');
		$GLOBALS['phpgw']->template->set_var('return',$GLOBALS['phpgw']->template->get('out'));
		return $GLOBALS['phpgw']->template->get('return');
	}

	function confirmDeleteForm($session,$filename,$directory,$type ='') 
	{
		$delete_form_begin= '<form action="' . createLink($GLOBALS['target']) . '" method="post">'."\n"
			. '<input type="hidden" name="action" value="delete">'."\n"
			. '<input type="hidden" name="olddir" value="'.$directory.'">'."\n"
			. '<input type="hidden" name="newdir" value="'.$directory.'">'."\n"
			. '<input type="hidden" name="file" value="'.$filename.'">'."\n";
		$delete_form_end = '</form>'."\n";
		$delete_form_question = lang ('Are you sure you want to delete %1 ?', $filename);
		$delete_form_from= $directory . '/' . $filename;
		$delete_form_to='<input type="text" name="newname" size=20" value="">';
		$delete_form_confirm='<input type="submit" name="confirm" value="' . lang('delete') . '">'."\n";
		$delete_form_cancel='<input type="submit" name="cancel" value="' . lang('cancel') . '">'."\n";

		$GLOBALS['phpgw']->template->set_var(array(
			'delete_form_begin' => $delete_form_begin,
			'delete_form_end'  => $delete_form_end,
			'delete_form_question' => $delete_form_question,
			'delete_form_confirm' => $delete_form_confirm,
			'delete_form_cancel' => $delete_form_cancel
		));

		$GLOBALS['phpgw']->template->parse('out','confirm_delete',true);
		$GLOBALS['phpgw']->template->set_var('return',$GLOBALS['phpgw']->template->get('out'));
		return $GLOBALS['phpgw']->template->get('return');
	}

	function newLogin($dfhost,$dfuser,$dfpass) 
	{
		$login_form_begin= '<form action="'.createLink($GLOBALS['target']).'" method="post">'."\n".'<input type="hidden" name="action" value="login">'."\n";
		$login_form_end='</form>'."\n";
		$login_form_username='<input type="text" name="username" value="'.$dfuser.'">';
		$login_form_password='<input type="password" name="password" value="'.$dfpass.'">';
		$login_form_ftpserver='<input type="text" name="ftpserver" value="'.$dfhost.'">';
		$login_form_submit='<input type="submit" name="submit" value="'.lang('connect').'">'."\n";
		$login_form_end="</form>";

		$GLOBALS['phpgw']->template->set_var(array(
			'login_form_begin' => $login_form_begin,
			'login_form_end' => $login_form_end,
			'login_form_username' => $login_form_username,
			'login_form_password' => $login_form_password,
			'login_form_ftpserver' => $login_form_ftpserver,
			'login_form_submit' => $login_form_submit,
			'lang_username' => lang('username'),
			'lang_password' => lang('password'),
			'langserver' => lang('Ftp Server')
		));
		$GLOBALS['phpgw']->template->set_var('lang_login',lang('Log into FTP server'));
		$GLOBALS['phpgw']->template->set_var('lang_ftpserver',lang('FTP hostname'));

		$GLOBALS['phpgw']->template->parse('loginform','login',false);
		$GLOBALS['phpgw']->template->p('loginform');
		return;
	}

	function phpftp_get( $ftp, $tempdir, $dir, $file )
	{
		srand((double)microtime()*1000000);
		$randval = rand();
		$tmpfile=$tempdir.'/'.$file.".".$randval;
		ftp_chdir($ftp,$dir);
		$remotefile=$dir . '/' . $file;
		if ( ! ftp_get( $ftp, $tmpfile, $remotefile, FTP_BINARY ) )
		{
			echo 'tmpfile="' . $tmpfile . '",file="' . $remotefile . '"<br>' . "\n";
			ftp_quit( $ftp );
			echo macro_get_Link('newlogin','Start over?');
			$retval=0;
		}
		else
		{
			ftp_quit( $ftp );
			$b = CreateObject('phpgwapi.browser');
			if ($GLOBALS['phpgw_info']['server']['ftp_use_mime'])
			{
				$mime = getMimeType($file);
				$b->content_header($file,$mime);
			}
			else
			{
				$b->content_header($file);
			}
			//header( "Content-Type: application/octet-stream" );
			//header( "Content-Disposition: attachment; filename=" . $file );
			readfile( $tmpfile );
			$retval=1;
		}
		@unlink( $tmpfile );
		return $retval;
	}

	function getMimeType($file)
	{
		$file=basename($file);
		$mimefile = PHPGW_APP_ROOT . '/mime.types';
		$fp=fopen($mimefile,"r");
		$contents = explode("\n",fread ($fp, filesize($mimefile)));
		fclose($fp);

		$parts=explode(".",$file);
		$ext=$parts[(sizeof($parts)-1)];

		for($i=0;$i<sizeof($contents);$i++)
		{
			if (! ereg("^#",$contents[$i]))
			{
				$line=split("[[:space:]]+", $contents[$i]);
				if (sizeof($line) >= 2)
				{
					for($j=1;$j<sizeof($line);$j++)
					{
						if ($line[$j] == $ext)
						{
							$mimetype=$line[0];
							return $mimetype;
						}
					}
				}
			}
		}
		return 'text/plain';
	}

	function phpftp_view( $ftp, $tempdir, $dir, $file ) 
	{
		srand((double)microtime()*1000000);
		$randval = rand();
		$tmpfile="$tempdir/" . $file . "." . $randval;
		ftp_chdir($ftp,$dir);
		$remotefile=$dir . "/" . $file;
		if ( ! ftp_get( $ftp, $tmpfile, $remotefile, FTP_BINARY ) ) 
		{
			echo "tmpfile=\"$tmpfile\",file=\"$remotefile\"<BR>\n";
			macro_get_Link('newlogin','Start over?');
			$retval=0;
		}
		else 
		{
			$content_type=getMimeType($remotefile);
			header('Content-Type: '.$content_type);
			readfile( $tmpfile );
			$retval=1;
		}
		@unlink( $tmpfile );
		return $retval;
	}

	function updateSession($string='')
	{
		return $GLOBALS['phpgw']->session->appsession('default', '', $string);
	}

	function analysedir($dirline)
	{
		$dirinfo = array();
		if (ereg("([-dl])[rwxst-]{9}",substr($dirline,0,10)))
		{
			$GLOBALS['systyp'] = 'UNIX';
		}

		if (substr($dirline,0,5) == 'total')
		{
			$dirinfo[0] = -1;
		}
		elseif($GLOBALS['systyp'] == 'Windows_NT')
		{
			if (ereg("[-0-9]+ *[0-9:]+[PA]?M? +<DIR> {10}(.*)",$dirline,$regs))
			{
				$dirinfo[0] = 1;
				$dirinfo[1] = 0;
				$dirinfo[2] = $regs[1];
			}
			elseif(ereg("[-0-9]+ *[0-9:]+[PA]?M? +([0-9]+) (.*)",$dirline,$regs))
			{
				$dirinfo[0] = 0;
				$dirinfo[1] = $regs[1];
				$dirinfo[2] = $regs[2];
			}
		}
		elseif($GLOBALS['systyp'] == 'UNIX')
		{
			//echo "$dirline <br />";
			$ta = explode(' ',$dirline);
			
			if(count($ta))
			{
				foreach($ta as $p)
				{
					if(trim($p) != '')//need to let 0 thru
					{
						$a[] = $p;
					}
				}
				$fileinfo['permissions'] = $a[0];
				$fileinfo['owner']       = $a[2];
				$fileinfo['group']       = $a[3];
				$fileinfo['size']        = $a[4];
				$fileinfo['date']        = $a[5] . '-' . $a[6] . '-' . $a[7];
				$fileinfo['name']        = $a[8];
			}
			else //if something goes wrong
			{
				$fileinfo['name'] = $ta[0];
			}
			//echo '<pre>'; print_r($fileinfo); echo '</pre>';
		}
    
		if ( isset($dirinfo[2]) && ($dirinfo[2] == '.' || $dirinfo[2] == '..') )
		{
			$dirinfo[0] = 0;
		}
        
		return $fileinfo;
	}

	function phpftp_getList($ftp,$dir,$start)
	{
		$GLOBALS['real_systyp'] = ftp_systype($ftp);

		ftp_chdir($ftp,$dir);
		$dirlist = ftp_rawlist($ftp,'');
		for ($i=$start; $i<($start+$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']); $i++)
		{
			if ($i < count($dirlist))
			{
				$dirinfo[] = analysedir($dirlist[$i]);
			}
		}
		return $dirinfo;
	}

	function macro_get_Link($action,$string) 
	{
		// globals everything it needs but the string to link
		global $olddir, $newdir, $file;
		
		$retval = '<a href="'
			. $GLOBALS['phpgw']->link($GLOBALS['target'],
					array(
						'olddir'	=> $olddir,
						'action'	=> $action,
						'file'		=> $file,
						'newdir'	=> $newdir
					)
				)
			. '">';
		$retval .= $string;
		$retval .= '</a>';
		return $retval;
	}

	function phpftp_delete($file,$confirm)
	{
	}

	function phpftp_rename($origfile,$newfile,$confirm) 
	{
	}
?>

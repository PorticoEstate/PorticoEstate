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

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'              => 'ftp',
		'enable_nextmatchs_class' => True
	);

	if ($_GET['action'] == 'get' || $_GET['action'] == 'view')
	{
		$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
		$GLOBALS['phpgw_info']['flags']['noheader'] = true;
	}
	include('../header.inc.php');

	$action = get_var('action', array('POST', 'GET'));
	$newdir = get_var('newdir', array('POST', 'GET'));
	$olddir = @urldecode(get_var('olddir', array('POST', 'GET')));
	$start = intval(get_var('start', array('GET')));

	$default_login  = $GLOBALS['phpgw_info']['user']['account_lid'];
	$default_pass   = $GLOBALS['phpgw']->session->appsession('password','phpgwapi');
	$default_server = isset($GLOBALS['phpgw_info']['server']['default_ftp_server']) ? $GLOBALS['phpgw_info']['server']['default_ftp_server'] : '';

	$sessionUpdated=false;

	$bgclass = array('row_on', 'row_off');
	$tempdir    = $GLOBALS['phpgw_info']['server']['temp_dir'];

	$GLOBALS['target']='/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/index.php';

	$GLOBALS['phpgw']->template->set_file(array(
		'main_' => 'main.tpl',
		'login' => 'login.tpl',
		'rename' => 'rename.tpl',
		'confirm_delete' => 'confirm_delete.tpl',
		'bad_connect' => 'bad_connection.tpl'
	));
	$GLOBALS['phpgw']->template->set_var(array(
		'bgclass' => $bgclass[0]
	));

	$GLOBALS['phpgw']->template->set_block('main_','main');
	$GLOBALS['phpgw']->template->set_block('main_','row');

	$GLOBALS['phpgw']->template->set_var('module_name',lang('Ftp Client'));

	$tried_default = false;
	if (!$action || $action=='login')
	{
		// if theres no action, try to login to default host with user and pass
		if ($action=='login') 
		{
			// username, ftpserver and password should have been passed in via POST
			$connInfo['username']  = $_POST['username'];
			$connInfo['password']  = $_POST['password'];
			$connInfo['ftpserver'] = $_POST['ftpserver'];
		}
		else
		{
			// try to default with session id and passwd
			if (!($connInfo=getConnectionInfo())) 
			{
				$connInfo['username']  = $default_login;
				$connInfo['password']  = $default_pass;
				$connInfo['ftpserver'] = $default_server;

				$tried_default=true;
			}
		}
		updateSession($connInfo);
		$sessionUpdated=true;
	} 

	if ($action != 'newlogin') 
	{
		if ( !count($connInfo) )
		{
			$connInfo=getConnectionInfo();
		}
		$ftp=@phpftp_connect($connInfo['ftpserver'],$connInfo['username'],$connInfo['password']);
		if ($ftp)
		{
			$homedir=ftp_pwd($ftp);
			$retval=ftp_pasv($ftp,1);
			if ($action == 'delete' || $action == 'rmdir') 
			{
				if ($_POST['confirm']) 
				{
					if ($action=='delete') 
					{
						$retval=@ftp_delete($ftp,$olddir . '/' . $file);
					}
					else 
					{
						$retval=@ftp_rmdir($ftp,$olddir . '/' . $file);
					}
					if ($retval) 
					{
						$GLOBALS['phpgw']->template->set_var("misc_data",lang('Successfully deleted %1',"$olddir/$file"), true);
					}
					else
					{
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to delete %1', "$olddir/$file"), true);
					}
				} else if (!$_POST['cancel']) 
				{
					$GLOBALS['phpgw']->template->set_var('misc_data',confirmDeleteForm($session,$file,$olddir),true);
				}
			}

			if ($action == 'rename')
			{
				if ($confirm) 
				{
					if (ftp_rename($ftp,$olddir . '/' . $filename, $olddir . '/' . $newfilename)) 
					{
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('renamed %1 to %2',
							"$filename", "$newfilename"), true);
					} 
					else 
					{
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to rename %1 to %2',
							"$filename", "$newfilename"), true);
					}
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('misc_data', renameForm($session,$file,$olddir), true);
				}
			}
			if ($action == 'get') 
			{
				phpftp_get($ftp,$tempdir,$olddir,$file);
				$GLOBALS['phpgw']->common->phpgw_exit();
			} 
			if ($action == 'view') 
			{
				phpftp_view($ftp,$tempdir,$olddir,$file);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			if ($action == 'upload') 
			{
				$newfile=$olddir . '/' . $uploadfile_name;
				if (ftp_put($ftp,$newfile, $uploadfile, FTP_BINARY)) 
				{
					$GLOBALS['phpgw']->template->set_var('misc_data',lang('Successfully uploaded %1',$newfile), true);
				}
				else 
				{
					$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to upload %1',$newfile), true);
				}
				unlink($uploadfile);
			}
			if ($action == 'mkdir')
			{
				if ($newdirname!='')
				{
					if (ftp_mkdir($ftp,$olddir . '/' . $newdirname)) 
					{
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('Successfully created directory %1',
							"$olddir/$newdirname"), true);
					}
					else 
					{
						$GLOBALS['phpgw']->template->set_var('misc_data',lang('failed to create directory %1',
							"$olddir/$newdirname"), true);
					}
				}
				else 
				{
					$GLOBALS['phpgw']->template->set_var('misc_data',lang('Attempt to create a directory with empty name'),true);
				}
			}

			// heres where most of the work takes place
			if ($action == 'cwd')
			{
				if ($olddir == $newdir)
				{
					ftp_chdir($ftp,$newdir);
				}
				else
				{
					ftp_chdir($ftp,$olddir . $newdir . '/');
					if($oldir == '/')
					{
						$olddir = $newdir;
					}
					elseif(! ($file && $newdir) )
					{
						$olddir = $newdir = '';
					}
					{
						$olddir = $olddir . $newdir . '/';
					}
				}
			}
			elseif ($action == 'up')
			{
				ftp_chdir($ftp,$connInfo['cwd']);
				ftp_cdup($ftp);
			}
			elseif ($action == '' && $connInfo['cwd'] != '')
			{
				// this must have come back from another module, try to 
				// get into the old directory
				ftp_chdir($ftp,$connInfo['cwd']);
			}
			elseif ($olddir)
			{
				ftp_chdir($ftp,$olddir);
			}

			if (! $olddir)
			{
				$olddir = ftp_pwd($ftp);
			}
			$cwd = ftp_pwd($ftp);
			$connInfo['cwd'] = $cwd;

			// set up the upload form
			$ul_form_open='<form name="upload" action="'.createLink($GLOBALS['target'])
				. '" enctype="multipart/form-data" method="post">'."\n"
				. '<input type="hidden" name="olddir" value="'.$cwd.'">'."\n"
				. '<input type="hidden" name="action" value="upload">'."\n";
			$ul_select='<input type="file" name="uploadfile" size="30">'."\n" ;
			$ul_submit='<input type="submit" name="upload" value="Upload">'."\n";
			$ul_form_close='</form>'."\n";

			// set up the create directory
			$crdir_form_open='<form name="mkdir" action="'.createLink($GLOBALS['target']).'" method="post" >'."\n"
				. "\t".'<input type="hidden" name="olddir" value="'.$cwd.'">'."\n"
				. "\t".'<input type="hidden" name="action" value="mkdir">'."\n";

			$crdir_form_close='</form>'."\n";
			$crdir_textfield="\t".'<input type="text" size="30" name="newdirname" value="">'."\n";
			$crdir_submit="\t".'<input type="submit" name="submit" value="Create New Dir">'."\n";
			$ftp_location='ftp://' . $connInfo['username'] . '@' . $connInfo['ftpserver'] . $cwd;

			$newdir=''; $temp=$olddir; $olddir=$homedir; 
			$home_link= macro_get_Link('cwd','<img border="0" src="'.$GLOBALS['phpgw']->common->image('ftp','home.gif').'">') . "\n";
			$olddir=$temp;

			// set up all the global variables for the template
			$GLOBALS['phpgw']->template->set_var(array(
				'ftp_location' => $ftp_location,
				'relogin_link'=> macro_get_Link('newlogin',lang('logout/relogin')),
				'home_link' => $home_link,
				'ul_select' => $ul_select, 
				'ul_submit' => $ul_submit,
				'ul_form_open' => $ul_form_open,
				'ul_form_close' => $ul_form_close,
				'crdir_form_open' => $crdir_form_open,
				'crdir_form_close' => $crdir_form_close,
				'crdir_textfield' => $crdir_textfield,
				'crdir_submit' => $crdir_submit
			));

			$total = count(ftp_rawlist($ftp,''));
			$GLOBALS['phpgw']->template->set_var('nextmatchs_left',$GLOBALS['phpgw']->nextmatchs->left('/ftp/index.php',$start,$total));
			$GLOBALS['phpgw']->template->set_var('nextmatchs_right',$GLOBALS['phpgw']->nextmatchs->right('/ftp/index.php',$start,$total));

			$contents = phpftp_getList($ftp,'.',$start);

			$GLOBALS['phpgw']->template->set_var('lang_name',lang('Name'));
			$GLOBALS['phpgw']->template->set_var('lang_owner',lang('Owner'));
			$GLOBALS['phpgw']->template->set_var('lang_group',lang('Group'));
			$GLOBALS['phpgw']->template->set_var('lang_permissions',lang('Permissions'));
			$GLOBALS['phpgw']->template->set_var('lang_size',lang('Size'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
			$GLOBALS['phpgw']->template->set_var('lang_rename',lang('Rename'));


			$newdir = $olddir;
			$GLOBALS['phpgw']->template->set_var('name',macro_get_link('up','..'));
			$GLOBALS['phpgw']->template->set_var('del_link','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('rename_link','&nbsp;');
			$GLOBALS['phpgw']->template->set_var('owner','');
			$GLOBALS['phpgw']->template->set_var('group','');
			$GLOBALS['phpgw']->template->set_var('permissions','');
			$GLOBALS['phpgw']->template->fp('rowlist_dir','row',True);

			if(is_array($contents))
			{
				$i = 1; //this is done as the first line is already set higher up
				foreach($contents as $null => $fileinfo)
				{
					//echo '<pre>'; print_r($fileinfo); echo '</pre>';
					$newdir = $fileinfo['name'];
					$GLOBALS['phpgw']->template->set_var('owner',$fileinfo['owner']);
					$GLOBALS['phpgw']->template->set_var('group',$fileinfo['group']);
					$GLOBALS['phpgw']->template->set_var('permissions',$fileinfo['permissions']);

					if ($fileinfo['size'] < 1024)
					{
						$fileinfo['size'] = $fileinfo['size'] . ' b';
					}
					elseif ($fileinfo['size'] < 999999)
					{
						$fileinfo['size'] = round(10*($fileinfo['size']/1024))/10 .' k';
					}
					else
					{
						//  round to W.XYZ megs by rounding WX.YZ
						$fileinfo['size'] = round($fileinfo['size']/(1024*100));
						// then bring it back one digit and add the MB string
						$fileinfo['size'] = ($fileinfo['size']/10) .' MB';
					}
					if (substr($fileinfo['permissions'],0,1) == 'd')
					{
						$file = $fileinfo['name'];
						$GLOBALS['phpgw']->template->set_var('name',macro_get_link('cwd',$fileinfo['name']));
						$GLOBALS['phpgw']->template->set_var('del_link',macro_get_link('rmdir',lang('Delete')));
						$GLOBALS['phpgw']->template->set_var('size','- ' . lang('dir') . ' -');
					}
					else
					{
						$file = $fileinfo['name'];
						$GLOBALS['phpgw']->template->set_var('del_link',macro_get_link('delete',lang('Delete')));
						$GLOBALS['phpgw']->template->set_var('name',macro_get_link('get',$fileinfo['name']));
						$GLOBALS['phpgw']->template->set_var('size',$fileinfo['size']);
					}
					$GLOBALS['phpgw']->template->set_var('rename_link',macro_get_link('rename',lang('Rename')));
					$GLOBALS['phpgw']->template->set_var('bgclass', $bgclass[($i % 2)]);
					$GLOBALS['phpgw']->template->fp('rowlist_dir','row',True);
					$i++;
				}
			}
			ftp_quit($ftp);
			$GLOBALS['phpgw']->template->pfp('out','main');
		} 
		else 
		{
			updateSession();
			$sessionUpdated=true;
			if (!$tried_default) 
			{
				$pass = '&lt;&lt;EMPTY&gt;&gt;';
				if ( !empty($connInfo['password']) )
				{
					$pass = '***PASSWORD***';
				}
				$GLOBALS['phpgw']->template->set_var('error_message', lang('Failed to connect to %1 with user %2 and password %3', 
					$connInfo['ftpserver'], $connInfo['username'], $pass), true);
				$GLOBALS['phpgw']->template->parse('out','bad_connect',false);
				$GLOBALS['phpgw']->template->p('out');
			}
			newLogin($connInfo['ftpserver'],$connInfo['username'],'');
		}
	}
	else 
	{
		// set the login and such to ""
		updateSession('');
		$sessionUpdated=true;
		// $GLOBALS['phpgw']->modsession(
		newLogin($default_server,$default_login,'');
	}
	if (!$sessionUpdated && $action=='cwd') 
	{
		// echo "updating session with new cwd<BR>\n";
		updateSession($connInfo);
	}

	$GLOBALS['phpgw']->common->phpgw_footer();
?>

#!{php_path} -q
<?php
	/*******************************************************************\
	* phpGroupWare - Backup                                             *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* Administration Tool for data backup                               *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright (C) 2001,2002 Bettina Gille                             *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id$ */

	function get_archives($dir)
	{
		if (is_dir($dir))
		{
			$basedir = opendir($dir);

			while ($files = readdir($basedir))
			{
				if (($files != '.') && ($files != '..'))
				{
					$archive[] = array
					(
						'file'	=> $files,
						'bdate'	=> filemtime($dir . '/' . $files)
					);
				}
			}
			closedir($basedir);
			return $archive;
		}
		else
		{
			return False;
		}
	}

	function get_date()
	{
		$bdate		= time();
		$month		= date('m',$bdate);
		$day		= date('d',$bdate);
		$year		= date('Y',$bdate);

		$bdateout	=  $month . '_' . $day . '_' . $year;
		return $bdateout;
	}

	function get_rdate($versions, $bintval)
	{	
		$dd = date('d');
		$dm = date('m');

		switch($bintval)
		{
			case 'daily':	$dd = $dd - $versions; break;
			case 'weekly':	$dd = $dd - ( 7 * $versions ); break;
			case 'monthly':	$dm = $dm - $versions; break;
		}
		$rdate = mktime(1,0,0,$dm,$dd,date('Y'));
		return $rdate;
	}

	function check_datedue($dir)
	{
		$versions = {versions};

		$archive = get_archives($dir);

		$bintval = '{bintval}';

		$rdate = get_rdate($versions,$bintval);

		while (list($null,$rfiles) = each($archive))
		{
			if ($rfiles['bdate'] <= $rdate)
			{
				unlink($dir . '/' . $rfiles['file']);
				echo 'removed ' . $dir . '/' . $rfiles['file'] . "\n";
			}
		}
	}

	$basedir	= '{basedir}';

//	check_datedue($basedir);

	$bsql		= '{bsql}';
	$bldap		= '{bldap}';
	$bemail		= '{bemail}';

	$bzip2		= '{bzip2_path}';

	$bcomp		= '{bcomp}';

	switch ($bcomp)
	{
		case 'tgz':		$end = 'tar.gz'; break;
		case 'tar.bz2':	$end = 'tar'; break;
		case 'zip':		$end = 'zip'; break;
	}

	switch ($bcomp)
	{
		case 'tgz':		$command = '{tar_path} -czf '; break;
		case 'tar.bz2':	$command = '{tar_path} -cf '; break;
		case 'zip':		$command = '{zip_path} -rq9 '; break;
	}

	if ($bsql)
	{
		switch($bsql)
		{
			case 'mysql':	$database = '{mysql_dir}'; break;
			case 'pgsql':	$database = '{pgsql_dir}'; break;
		}

		chdir($database);
		$out	= $basedir . '/' . get_date() . '_phpGWBackup_{bsql}.' . $end;
		$in		= ' {db_name}';

		system("$command" . $out . $in);

		if ($bcomp == 'tar.bz2')
		{
			$end = '.bz2';
			system("$bzip2 -z " . $out . ' 2>&1 > /dev/null'); 
			$out = $out . $end;
		}
		$output[]	= $out;
		$input[]	= substr($out,strlen($basedir)+1);
	}

	if ($bldap == 'yes')
	{
		chdir('{ldap_dir}');
		$out	= $basedir . '/' . get_date() . '_phpGWBackup_ldap.' . $end;
		$in		= ' {ldap_in}';

		system("$command" . $out . $in);

		if ($bcomp == 'tar.bz2')
		{
			$end = '.bz2';
			system("$bzip2 -z " . $out . ' 2>&1 > /dev/null'); 
			$out = $out . $end;
		}
		$output[]	= $out;
		$input[]	= substr($out,strlen($basedir)+1);
	}

	if ($bemail == 'yes')
	{
<!-- BEGIN script_ba -->
		if (is_dir('/home/{lid}') == True)
		{
			chdir('/home/{lid}');
			$out	= $basedir . '/' . get_date() . '_phpGWBackup_email_{lid}.' . $end;
			$in		= ' {maildir}';
			system("$command" . $out . $in . ' 2>&1 > /dev/null');

			if ($bcomp == 'tar.bz2')
			{
				$end = '.bz2';
				system("$bzip2 -z " . $out);
				$out = $out . $end;
			}
			$output[]	= $out;
			$input[]	= substr($out,strlen($basedir)+1);
		}
<!-- END script_ba -->
	}

	check_datedue($basedir);

// ----------------------- move to remote host --------------------------------

	$lsave		= '{lsave}';
	$lpath		= '{lpath}';
	$lwebsave	= '{lwebsave}';

	$rsave		= '{rsave}';
	$rapp		= '{rapp}';
	$rip		= '{rip}';
	$rpath		= '{rpath}';
	$ruser		= '{ruser}';
	$rpwd		= '{rpwd}';

	if ($rsave == 'yes')
	{
		if ($rapp == 'ftp')
		{
			$con = ftp_connect("$rip");

			$login_result = ftp_login($con, "$ruser", "$rpwd");

			if (!$con || !$login_result)
			{
				echo 'Connection to remote ftp-server failed !' . "\n";
				exit;
			}

			$rem = ftp_chdir($con, "$rpath");

			for ($i=0;$i<count($output);$i++)
			{
				$put = ftp_put($con, "$input[$i]", "$output[$i]", FTP_BINARY);

				if ($put)
				{
					echo 'ftp backuptransfer ' . $input[$i] . ': success !' . "\n";
				}
				else
				{
					echo 'ftp backuptransfer ' . $input[$i] . ': failed !' . "\n";
					exit;
				}
			}
			ftp_quit($con);
		}

		if ($rapp == 'nfs')
		{
			$nfsdir = '/mnt';
			system("mount -t nfs $rip:$rpath $nfsdir 2>&1 > /dev/null");

		//	check_datedue($nfsdir);

			for ($i=0;$i<count($output);$i++)
			{
				system("cp " . $output[$i] . ' ' . $nfsdir . '/ 2>&1 > /dev/null');
				echo 'transfer of ' . $output[$i] . ' through nfs: success !' . "\n";
			}
			check_datedue($nfsdir);
			system("umount " . $nfsdir);
		}

// this works now too...

		if ($rapp == 'smbmount')
		{
			$smbdir = '/mnt';

			$rip = '//' . $rip;

			system("mount.smbfs $rip$rpath $smbdir -o username=$ruser,password=$rpwd,rw 2>&1 > /dev/null");

		//	check_datedue($smbdir);

			for ($i=0;$i<count($output);$i++)
			{
				system("cp " . $output[$i] . ' ' . $smbdir . '/ 2>&1 > /dev/null');
				echo 'transfer of ' . $output[$i] . ' through smbmount: success !' . "\n";
			}
			check_datedue($smbdir);
			system("smbumount " . $smbdir);
		}
	}

	if ($lsave == 'yes')
	{
		if ($lwebsave == 'yes')
		{
			$command = 'cp';
		}
		else
		{
			$command = 'mv';
		}

		if ($lpath != '')
		{
		//	check_datedue($lpath);

			for ($i=0;$i<count($output);$i++)
			{
				system("$command " . $output[$i] . ' ' . $lpath . '/ 2>&1 > /dev/null'); 
			}
			check_datedue($lpath);
		}
	}
	else
	{
		$command = 'rm';
		for ($i=0;$i<count($output);$i++)
		{
			system("$command " . $output[$i] . ' 2>&1 > /dev/null');
		}
	}

?>

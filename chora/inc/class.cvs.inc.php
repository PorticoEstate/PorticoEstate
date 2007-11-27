<?php
  /**************************************************************************\
  * phpGroupWare - chora Remote CVS Class                                    *
  * http://www.phpgroupware.org                                              *
  * This file written by Miles Lott <milosch@phpgroupware.org>               *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: class.cvs.inc.php 10090 2002-04-28 20:36:01Z milosch $ */

/*
Mod-time 13 Mar 2001 17:18:22 -0000
MT +updated
MT text U
MT fname addressbook/templates/default/vcardin.tpl
MT newline
MT -updated
Created addressbook/templates/default/
/cvsroot/phpgroupware/addressbook/templates/default/vcardin.tpl
/vcardin.tpl/1.2///
u=rw,g=rw,o=rw
323
*/

	class cvs
	{
		/* phpgw network object */
		var $network;

		/* phpgw vfs object */
		var $fs;

		/* Client request string */
		var $request;

		/* Server response string */
		var $response;

		/* debug data gets dumped here */
		var $debug;

		/* Server, port, cvsroot info */
		var $server  = 'subversions.gnu.org';
		var $cvsroot = '/cvsroot/phpgroupware';
		var $connected = False;
		var $port = 2401;
		var $files;

		/* CVS user info and mode */
		var $user = 'anoncvs';
		var $pass = '';
		var $mode = 'pserver';

		/* Valid responses we accept from the server */
		var $vr = 'Valid-responses ok error Valid-requests Checked-in New-entry Checksum Copy-file Updated Created Update-existing Merged Patched Rcs-diff Mode Mod-time Removed Remove-entry Set-static-directory Clear-static-directory Set-sticky Clear-sticky Template Set-checkin-prog Set-update-prog Notified Module-expansion Wrapper-rcsOption M Mbinary E F MT';

		/* pserver method password hash */
		var $scrambled = array(
			'0' => 111, 'P' => 125, 'p' =>  58,
			'!' => 120, '1' =>  52, 'A' =>  57, 'Q' =>  55,  'a' => 121, 'q' => 113,
			'"' =>  53, '2' =>  75, 'B' =>  83, 'R' =>  54,  'b' => 117, 'r' =>  32,
			'3' => 119, 'C' =>  43, 'S' =>  66, 'c' => 104,  's' =>  90,
			'4' =>  49, 'D' =>  46, 'T' => 124, 'd' => 101,  't' =>  44,
			'%' => 109, '5' =>  34, 'E' => 102, 'U' => 126,  'e' => 100, 'u' =>  98,
			'&' =>  72, '6' =>  82, 'F' =>  40, 'V' =>  59,  'f' =>  69, 'v' =>  60,
			"'" => 108, '7' =>  81, 'G' =>  89, 'W' =>  47,  'g' =>  73, 'w' =>  51,
			'(' =>  70, '8' =>  95, 'H' =>  38, 'X' =>  92,  'h' =>  99, 'x' =>  33,
			')' =>  64, '9' =>  65, 'I' => 103, 'Y' =>  71,  'i' =>  63, 'y' =>  97,
			'*' =>  76, ':' => 112, 'J' =>  45, 'Z' => 115,  'j' =>  94, 'z' =>  62,
			'+' =>  67, ';' =>  86, 'K' =>  50, 'k' =>  93,
			',' => 116, '<' => 118, 'L' =>  42, 'l' =>  39,
			'-' =>  74, '=' => 110, 'M' => 123, 'm' =>  37,
			'.' =>  68, '>' => 122, 'N' =>  91, 'n' =>  61,
			'/' =>  87, '?' => 105, 'O' =>  35, '_' =>  56,  'o' =>  48
		);

		var $commands = array(
			'valid_requests' => True,
			'version' => True,
			'noop'    => True,
			'co'      => True,
			'log'     => True
		);

		function cvs()
		{
			$this->network = CreateObject('phpgwapi.network');
			$this->fs      = CreateObject('phpgwapi.vfs');
			if (!$this->fs->file_exists('cvs',array(RELATIVE_USER)))
			{
				$this->fs->mkdir('cvs',array(RELATIVE_USER));
			}
		}

		function connect($command='login',$param='')
		{
			$this->connected = $this->network->open_port($this->server,$this->port);
			if($command && $param)
			{
				$this->command($command,$param);
			}
		}

		function command($command='',$param='')
		{
			/* _debug_array($param); */
			if($this->connected && $command && $param)
			{
				if(gettype($param) != 'array')
				{
					break;
				}
				$command = ereg_replace('-','_',$command);
				eval('$this->$command($param);');
			}
		}

		function disconnect()
		{
			$this->network->close_port();
			$this->connected = False;
		}

		function login($verify=False)
		{
			if($this->mode)
			{
				switch ($this->mode)
				{
					case 'pserver':
						$this->_auth_pserver($verify);
						break;
					default:
						break;
				}
			}
			return False;
		}

		/* Used below in each cvs command to auth the connection */
		function _start_request($root=True)
		{
			$auth = '_auth_' . $this->mode;
			return $this->$auth($root);
		}

		/* Build an auth string for pserver mode */
		function _auth_pserver($root=True,$verify=False)
		{
			$type = 'AUTH';
			if($verify)
			{
				$type = 'VERIFICATION';
			}
			$str .= 'BEGIN ' . $type . ' REQUEST' . "\n";
			$str .= $this->cvsroot . "\n";
			$str .= $this->user . "\n";
			$str .= $this->_pserver_scramble($this->pass) . "\n";
			$str .= 'END ' . $type . ' REQUEST' . "\n";
			$str .= $this->vr . "\n";
			if($root)
			{
				$str .= 'Root ' . $this->cvsroot . "\n";
			}
			$this->request = $str;
			return $str;
		}

		function _auth_server($root=True,$verify=False)
		{
			system("$ssh -l $this->user $this->server",$array);
			echo $array;
		}

		/* translate pserver password using class array */
		function _pserver_scramble($pass='')
		{
			$end = strlen($pass);
			for ($i=0;$i<$end;$i++)
			{
				$tmp = substr($pass,$i,1);
				$out .= chr($this->scrambled[$tmp]);
			}
			return 'A' . $out;
		}

		function _send($bs=False)
		{
			if($bs)
			{
				$this->network->bs_write_port($this->request,strlen($this->request));
			}
			else
			{
				$this->network->write_port($this->request);
			}
			$this->debug .= $this->request;
		}

		function _read($nook=False)
		{
			$newfile = 0;
			while($line = $this->network->read_port())
			{
				$i++;
				$x = trim($line);
				$ss = intval($x);
				/* echo $line; */
				if(!preg_match("/\AI LOVE YOU/", $x) && !preg_match("/\Aok/",$x))
				{
					$this->response[] = $line;
				}

				if (preg_match("/\AMod-time/", $x))
				{
					$file['modtime'][$newfile] = strtotime(ereg_replace('Mod-time ','',$x));
					$file['date'][$newfile] = ereg_replace('Mod-time ','',$x);
					$newfile++;
				}
				elseif(preg_match("/\Au=/",$x) || preg_match("/g=/",$x) || preg_match("/o=/",$x))
				{
					$attr  = '';
					$attrs = split(',',$x);
					while(list($k,$v) = each($attrs))
					{
						if(preg_match("/u=/",$v))
						{
							$tmp['u'] = $this->_mkchown(ereg_replace('u=','',$v));
						}
						if(preg_match("/g=/",$v))
						{
							$tmp['g'] = $this->_mkchown(ereg_replace('g=','',$v));
						}
						if(preg_match("/o=/",$v))
						{
							$tmp['o'] = $this->_mkchown(ereg_replace('o=','',$v));
						}
					}
					$attr .= $tmp['u'] ? $tmp['u'] : '0';
					$attr .= $tmp['g'] ? $tmp['g'] : '0';
					$attr .= $tmp['o'] ? $tmp['o'] : '0';
					$file['mode'][$newfile] = $attr;
				}
				elseif(preg_match("/\AMT fname/", $x))
				{
					$file['fname'][$newfile] = ereg_replace('MT fname ','',$x);
				}
				elseif(preg_match("/\AClear-sticky/",$x) || preg_match("/\AClear-static/",$x))
				{
					$file['extra'][$newfile] = $x;
				}
				elseif($ss && ($ss == $x))
				{
					$file['size'][$newfile] = intval($x);
				}
				else
				{
					$file['data'][$newfile] .= $line;
				}
				if(preg_match("/\AMT/",$x))
				{
					$file['mt'][$newfile] .= $line;
				}

				if($nook)
				{
					$nook = False;
					next;
				}
				if($x == 'ok')
				{
					break;
				}
				$this->files = $file;
				/* if($i>=2) break; */
			}
		}

		function _mkchown($val)
		{
			$mode = 0;
			if(ereg('x',$val))
			{
				$mode++;
			}
			if(ereg('w',$val))
			{
				$mode = $mode + 2;
			}
			if(ereg('r',$val))
			{
				$mode = $mode + 4;
			}
			return $mode;
		}

		/* And now.... the cvs commands */
		function valid_requests()
		{
			$this->_start_request(False);
			$this->request .= "valid-requests\n";

			$this->_send();
			$this->_read();
		}

		function version()
		{
			$this->_start_request(False);
			$this->request .= "version\n";
			$this->_send();
			$this->_read();
		}

		function noop()
		{
			$this->_start_request(False);
			$this->request .= "noop\n";
			$this->_send();
			$this->_read();
		}

		function status()
		{
			$this->_start_request(True);
			$this->request .= "status\n";
			$this->_send();
			$this->_read();
		}

		function co($param)
		{
			$module = $param['module'];
			if(gettype($module) != 'array')
			{
				$tmpmod[] = $module;
				$module   = $tmpmod;
			}
			$args   = $param['args'] ? $param['args'] : '-N';
			$gzip   = $param['gzip'] ? 'Gzip-stream ' . $param['gzip'] . "\n" : '';

			$this->_start_request(True);
			$this->request .= "UseUnchanged\n";

			@reset($module);
			while(list($x,$mod) = @each($module))
			{
				$this->request .= "Argument $mod\n";
			}

			$this->request .= "Directory .\n";
			$this->request .= "$this->cvsroot\n";
			$this->request .= "expand-modules\n";
			$this->response = '';
			$this->_send(True);
			$this->_read(True);
			while(list($x,$tmp) = @each($this->response))
			{
				if(ereg('Module-expansion ',$tmp))
				{
					$tmp = trim(ereg_replace("Module-expansion ",'',$tmp));
					$tmpmod[] = $tmp;
				}
			}
			if($tmpmod)
			{
				$module = $tmpmod;
			}

			$this->request .= $gzip;
			$this->request  = "Argument $args\n";

			@reset($module);
			while(list($x,$mod) = @each($module))
			{
				$this->request .= "Argument $mod\n";
			}

			$this->request .= "Directory .\n";
			$this->request .= "$this->cvsroot\n";
			$this->request .= "co\n";
			$this->_send(True);
			$this->_read();
		}

		function update($param)
		{
		}

		function commit($param)
		{
		}

		function log($param)
		{
			$module   = $param['module'];
			$fname    = $param['fname'];
			$version  = $param['version'];
			$conflict = $param['conflict'];
			$options  = $param['options'];
			$tag      = $param['tag'];
			$date     = $param['date'];
			$args     = $param['args'];
			$Entry    = '/' . $fname . '/' . $version . '/' . $options . '/' . $tag  . '/' . $date;

			$this->_start_request(True);
			$this->request .= "UseUnchanged\n";
			if($args)
			{
				$this->request .= "Argument $args\n";
			}
			$this->request .= "Directory .\n";
			$this->request .= "$this->cvsroot/$module\n";
			if($fname)
			{
				$this->request .= "Entry $Entry\n";
				$this->request .= "Unchanged $fname\n";
				$this->request .= "Argument $fname\n";
			}
			$this->request .= "log\n";
			$this->_send();
			$this->_read(True);
		}
	}
?>

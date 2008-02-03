<?php
  /**************************************************************************\
  * phpGroupWare API - Auth from Mail server                                 *
  * This file written by Dan Kuykendall <seek3r@phpgroupware.org>            *
  * Authentication based on mail server                                      *
  * Copyright (C) 2000, 2001 Mike Bell <mike@mikebell.org>                   *
  * -------------------------------------------------------------------------*
  * This library is part of the phpGroupWare API                             *
  * http://www.phpgroupware.org/api                                          * 
  * ------------------------------------------------------------------------ *
  * This library is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU Lesser General Public License as published by *
  * the Free Software Foundation; either version 2.1 of the License,         *
  * or any later version.                                                    *
  * This library is distributed in the hope that it will be useful, but      *
  * WITHOUT ANY WARRANTY; without even the implied warranty of               *
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
  * See the GNU Lesser General Public License for more details.              *
  * You should have received a copy of the GNU Lesser General Public License *
  * along with this library; if not, write to the Free Software Foundation,  *
  * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
  \**************************************************************************/

  /* $Id$ */

	class vmailmgr
	{
		var $domain;
		var $domainpass;
		var $vm_tcphost;
		var $vm_tcphost_port;

		function vmailmgr()
		{
			$config = CreateObject('phpgwapi.config', 'vmailmgr');
			$config->read_repository();
			$this->domain = $config->config_data['vmailmgr_domain'];
			$this->domainpass = $config->config_data['vmailmgr_domainpass'];
			$this->vm_tcphost = $config->config_data['vmailmgr_tcphost'];
			$this->vm_tcphost_port = $config->config_data['vmailmgr_tcphost_port'];
		}

		function vm_daemon_raw($arg)
		{
			// om/23feb01 - dual vmailmgrd support : unix & tcp socket

			if ($this->vm_tcphost)
			{
				// TCP SOCKET - default port = 322.
 				if (!$this->vm_tcphost_port)
				{
					$this->vm_tcphost_port = "322";
				}
       	$vmailsock = fsockopen ($vm_tcphost, $this->vm_tcphost_port, $errno, $errstr, 10);
       	if (!$vmailsock)
				{
					die("Failed to open tcp socket, is daemon running on host '".$this->vm_tcphost.":".$this->vm_tcphost_port."'? <br>\nError: $errno - $errstr");
				}
			}
			else
			{
				// UNIX SOCKET
	      $vmailfile = "/tmp/.vmailmgrd";
				if (file_exists("/etc/vmailmgr/socket-file"))
				{
					$socketfile = (file ("/etc/vmailmgr/socket-file"));
				}
				$socketfile = trim($socketfile[0]);
				if ($socketfile != "")
				{
					$vmailfile = $socketfile;
				}
				$vmailsock = fsockopen ($vmailfile, 0, $errno, $errstr, 4);
				if (!$vmailsock)
				{
					die("Failed to open unix socket file at '$vmailfile', is daemon running? <br>\n Error: $errno - $errstr");
				}
			}

			/* Parse $arg, which should be an array of arguments to pass to the
      daemon, into a glob consisting of each argument proceeded by a
      two-byte representation of its length. */
			for ($x=0; $x < sizeof($arg); $x++)
			{
				$commandlength = strlen($arg[$x]);
				$high=(($commandlength & (0xFF << 8)) >> 8);
				$low=($commandlength & 0xFF);
				$command .= sprintf("%c%c%s", $high, $low, $arg[$x]);
			}


			/* Create the header, which consists of another two byte length
      representation, the number of arguments being passed, and the
      command string created above. */

      $args=$x-1;
      $commandlength=strlen($command);
      $high=(($commandlength & (255 << 8)) >> 8);
      $low=($commandlength & 255);
      $commandstr = sprintf("\002%c%c%c", $high, $low+1, $args).$command;

			/* Pass it all to the daemon */
      fputs($vmailsock, $commandstr);
			/* Get the response */

			$value = ord( fgetc ($vmailsock));
			$length = (ord(fgetc($vmailsock)) << 8) + ord(fgetc($vmailsock));

			if ($length == 0)
			{
				while (!feof($vmailsock))
				{
					$out.=fread($vmailsock, 65535);
				}
				fclose($vmailsock);
				return $out;
			}

			$message = fread ($vmailsock, $length);

			/*	Close the socket	*/
			fclose($vmailsock);

			return array($value, $message);
		}

		/* vadduser, takes domain name, domainpass, username, userpassword, and an
   	array of forwarding desinations, returns an array consisting of an
   	integer exit code and message. */

		function vadduser($username, $userpass)
		{
			global $quota_data;
			if ($this->domain=="")
			{
				return array(1, "Empty domain");
			}
			if ($this->domainpass=="")
			{
				return array(1, "Empty domain password");
			}
			if ($username=="")
			{
				return array(1, "Empty username");
			}
			if ($userpass=="")
			{
				return array(1, "No user password supplied");
			}
			$command=array("adduser2", $this->domain, $username, $this->domainpass, $userpass, $username);
			return $this->vm_daemon_raw($command);
		}

		/* vdeluser, takes domain name, password, and username, returns an array
   	consisting of an integer exit code and message. */

		function vdeluser($username)
		{
			if ($this->domain=="")
			{
				return array(1, "Empty domain");
			}
			if ($this->domainpass=="")
			{
				return array(1, "Empty domain password");
			}
			if ($username=="")
			{
				return array(1, "Empty username");
			}
			$command=array("deluser", $this->domain, $username, $this->domainpass);
			return $this->vm_daemon_raw($command);
		}

		/* vchpass, takes domain name, password, username and a new password,
   	returns an array consisting of an integer exit code and message. Scripts
   	allowing users to change their own passwords should check the password
   	was entered correctly by having the user enter it twice and checking
   	these are equal*/

		function vchpass($username, $newpass)
		{
			if ($this->domain=="")
			{
				return array(1, "Empty domain");
			}
			if ($this->domainpass=="")
			{
				return array(1, "Empty domain password");
			}
			if ($username=="")
			{
				return array(1, "Empty username");
			}
			if ($newpass=="")
			{
				return array(1, "Empty new password");
			}
			$command=array("chattr", $this->domain, $username, $this->domainpass, "1", $newpass);
			return $this->vm_daemon_raw($command);
		}
	}
?>

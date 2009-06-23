<?php
	/**
	* Globally Denied Account Name Functions
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage utility
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */
	class phpgwapi_globally_denied
	{
		/**
		* @var array $user_list list of banned user account names
		*/
		private static $user_list = array
		(
			'adm'			=> true,
			'alias'			=> true,
			'amanda'		=> true,
			'apache'		=> true,
			'avahi'			=> true,
			'backup'		=> true,
			'backup'		=> true,
			'beagleindex'	=> true,
			'bin'			=> true,
			'cupsys'		=> true,
			'cvs'			=> true,
			'cyrus'			=> true,
			'daemon'		=> true,
			'dhcp'			=> true,
			'dnsmasq'		=> true,
			'fetchmail'		=> true,
			'ftp'			=> true,
			'games'			=> true,
			'gdm'			=> true,
			'gnats'			=> true,
			'gopher'		=> true,
			'haldaemon'		=> true,
			'hal'			=> true,
			'halt'			=> true,
			'hplip'			=> true,
			'ident'			=> true,
			'irc'			=> true,
			'klog'			=> true,
			'ldap'			=> true,
			'list'			=> true,
			'lp'			=> true,
			'mailnull'		=> true,
			'mail'			=> true,
			'messagebus'	=> true,
			'mysql'			=> true,
			'named'			=> true,
			'news'			=> true,
			'nobody'		=> true,
			'nscd'			=> true,
			'operator'		=> true,
			'oracle'		=> true,
			'pgsql'			=> true,
			'postfix'		=> true,
			'postgres'		=> true,
			'proxy'			=> true,
			'pvm'			=> true,
			'qmaild'		=> true,
			'qmaillog'		=> true,
			'qmaill'		=> true,
			'qmailp'		=> true,
			'qmailq'		=> true,
			'qmailr'		=> true,
			'qmails'		=> true,
			'root'			=> true,
			'rpc'			=> true,
			'rpcuser'		=> true,
			'sabayon-admin'	=> true,
			'saned'			=> true,
			'shutdown'		=> true,
			'squid'			=> true,
			'sshd'			=> true,
			'sweep'			=> true,
			'sync'			=> true,
			'syslog'		=> true,
			'sys'			=> true,
			'uucp'			=> true,
			'web'			=> true,
			'www-data'		=> true,
			'xfs'			=> true
		);

		/**
		* @var array $group_list array list of banned user group names
		*/
		private static $group_list = array
		(
			'admin'			=> true,
			'adm'			=> true,
			'audio'			=> true,
			'avahi'			=> true,
			'backup'		=> true,
			'bin'			=> true,
			'cdrom'			=> true,
			'console'		=> true,
			'crontab'		=> true,
			'cvs'			=> true,
			'daemon'		=> true,
			'dba'			=> true,
			'dhcp'			=> true,
			'dialout'		=> true,
			'dip'			=> true,
			'dirmngr'		=> true,
			'disk'			=> true,
			'dnstools'		=> true,
			'fax'			=> true,
			'floppy'		=> true,
			'ftp'			=> true,
			'games'			=> true,
			'gdm'			=> true,
			'gnats'			=> true,
			'haldaemon'		=> true,
			'hal'			=> true,
			'irc'			=> true,
			'klog'			=> true,
			'kmem'			=> true,
			'ldap'			=> true,
			'list'			=> true,
			'lpadmin'		=> true,
			'lp'			=> true,
			'lp'			=> true,
			'mail'			=> true,
			'man'			=> true,
			'messagebus'	=> true,
			'mysql'			=> true,
			'named'			=> true,
			'news'			=> true,
			'nobody'		=> true,
			'nofiles'		=> true,
			'nogroup'		=> true,
			'oinstall'		=> true,
			'operator'		=> true,
			'oracle'		=> true,
			'plugdev'		=> true,
			'popusers'		=> true,
			'postdrop'		=> true,
			'postfix'		=> true,
			'postgres'		=> true,
			'pppusers'		=> true,
			'proxy'			=> true,
			'qmail'			=> true,
			'root'			=> true,
			'sabayon-admin'	=> true,
			'saned'			=> true,
			'sasl'			=> true,
			'scanner'		=> true,
			'shadow'		=> true,
			'slipusers'		=> true,
			'slocate'		=> true,
			'src'			=> true,
			'ssh'			=> true,
			'ssl-cert'		=> true,
			'staff'			=> true,
			'sudo'			=> true,
			'sweep'			=> true,
			'syslog'		=> true,
			'sys'			=> true,
			'tape'			=> true,
			'tty'			=> true,
			'users'			=> true,
			'utmp'			=> true,
			'uucp'			=> true,
			'video'			=> true,
			'voice'			=> true,
			'web'			=> true,
			'wheel'			=> true,
			'www-data'		=> true,
			'xfs'			=> true,
		);

		/**
		 * Check if the username is banned
		 *
		 * @param string $login the account login to lookup
		 * @return bool is the login banned?
		 */
		public static function user($login)
		{
			return !$login || isset(self::$user_list[$login]);
		}

		/**
		 * Check if the groupname is banned
		 *
		 * @param string $group the group name to lookup
		 * @return bool is the group banned?
		 */
		public static function group($group)
		{
			return !$group || isset(self::$group_list[$group]);
		}

	}

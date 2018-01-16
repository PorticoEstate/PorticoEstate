<?php
	/**
	* Addressbook - remote
	*
	* @author Joseph Engo <jengo@mail.com>
	* @copyright Copyright (C) 2000-2002,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package addressbook
	* @version $Id$
	*/


	/**
	* remote
	*
	* @package addressbook
	*/
	class remote
	{
		var $servers = array(
			'BigFoot' => array(
				'host'    => 'ldap.bigfoot.com',
				'basedn'  => '',
				'search'  => 'cn',
				'attrs'   => 'mail,cn,o,surname,givenname',
				'enabled' => True
			),
		);
		var $serverid = '';

		var $ldap = 0;

		function __construct($serverid='BigFoot')
		{
			$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_addressbook_servers",__LINE__,__FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				if ($GLOBALS['phpgw']->db->f('name'))
				{
					$this->servers[$GLOBALS['phpgw']->db->f('name')] = array(
						'host'    => $GLOBALS['phpgw']->db->f('host'),
						'basedn'  => $GLOBALS['phpgw']->db->f('basedn'),
						'search'  => $GLOBALS['phpgw']->db->f('search'),
						'attrs'   => $GLOBALS['phpgw']->db->f('attrs'),
						'enabled' => $GLOBALS['phpgw']->db->f('enabled')
					);
				}
			}
			$this->serverid = $serverid;
			$this->ldap = $this->_connect($this->serverid);
			//$this->search();
		}

		function _connect($serverid='BigFoot')
		{
			if (!$ds = ldap_connect($this->servers[$serverid]['host']))
			{
				printf("<b>Error: Can't connect to LDAP server %s!</b><br>",$this->servers[$serverid]['host']);
				return False;
			}
			@ldap_bind($ds);

			return $ds;
		}

		function search($query='')
		{
			if(!$query)
			{
				return;
			}

			if(isset($this->servers[$this->serverid]['attrs']))
			{
				$attrs = explode(',',$this->servers[$this->serverid]['attrs']);
				$found = ldap_search($this->ldap,$this->servers[$this->serverid]['basedn'],$this->servers[$this->serverid]['search'] . '=*' . $query . '*',$attrs);
			}
			else
			{
				$found = ldap_search($this->ldap,$this->servers[$this->serverid]['basedn'],$this->servers[$this->serverid]['search'] . '=*' . $query . '*');
			}

			$ldap_fields = @ldap_get_entries($this->ldap, $found);

			$out = $this->clean($ldap_fields);
			$out = $this->convert($out);

			return $out;
		}

		function clean($value)
		{
			if(!is_int($value) && ($value != 'count'))
			{
				if(is_array($value))
				{
					//while(list($x,$y) = @each($value))
                                        foreach($value as $x => $y)
					{
						/* Fill a new output array, but do not include things like array( 0 => mail) */
						if(isset($this->servers[$this->serverid]['attrs']) &&
							!@in_array($y,explode(',',$this->servers[$this->serverid]['attrs'])))
						{
							$out[$x] = $this->clean($y);
						}
					}
					unset($out['count']);
					return $out;
				}
				else
				{
					return $value;
				}
			}
		}

		function convert($in='')
		{
			if(is_array($in))
			{
				//while(list($key,$value) = each($in))
                                foreach($in as $key => $value)
				{
					$out[] = array(
						'fn'       => $value['cn'][0],
						'n_family' => $value['sn'][0] ? $value['sn'][0] : $value['surname'][0],
						'email'    => $value['mail'][0],
						'owner'    => $GLOBALS['phpgw_info']['user']['account_id']
					);
				}
				return $out;
			}
			else
			{
				return $in;
			}
		}
	}
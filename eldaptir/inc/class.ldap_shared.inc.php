<?php
  /**************************************************************************\
  * phpGroupWare - eLDAPtir - LDAP Administration                            *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * Provide common vars/functions for schema-aware LDAP Administration       *
  * ------------------------------------------------------------------------ *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  
  /* $Id: class.ldap_shared.inc.php 8324 2001-11-19 15:50:05Z milosch $ */

	class ldap
	{
		var $ldap;          // connection
		var $ldap_schema;

		var $host;
		var $base;          // basedn derived from phpgw ldap_context config or configured server
		var $other;         // All but the current basedn
		var $entries;       // all entries in a list/search
		var $entry;         // all attrs for a single entry
		var $total_entries; // count of entries in a list/search
		var $total_attrs;   // count of attrs for a single entry
		var $searchline;    // ???
		var $objkey;        // key for creation of new entries, get parsed into dn
		var $DEBUG = 0;

		function ldap($serverinfo='')
		{
			$this->ldap_schema = new ldap_schema;

			if($serverinfo)
			{
				$this->host = $serverinfo['host'];
				$this->base = $serverinfo['basedn'];
				$rootdn     = $serverinfo['rootdn'];
				$rootpw     = $serverinfo['rootpw'];
				$this->ldap = $GLOBALS['phpgw']->common->ldapConnect($this->host,$rootdn,$rootpw);
				if($this->DEBUG) { echo '<br>Host: '.$this->host; }
				if($this->DEBUG) { echo '<br>Base: '.$this->base; }
			}
			else
			{
				$this->host = $GLOBALS['phpgw_info']["server"]["ldap_host"];
				$this->ldap = $GLOBALS['phpgw']->common->ldapConnect();
				$ldap_context = $GLOBALS['phpgw_info']["server"]["ldap_context"];
				//$ldap_context = 'ou=People, ou=accounts, o=kermit.org';
				// Drop the first element, usually ou=People, leaving the basedn
				$parsebase = explode(',',$ldap_context);
				while (list($key,$piece) = each($parsebase))
				{
					if ($key)//don't take the first one ($key=0)
					{
						$pieces[] = trim($piece);
					}
				}
				$this->base = implode(',',$pieces);
			}
			if($this->DEBUG) { echo"<br>Using basedn: '".$this->base."'"; }
		}

		/*
		@abstract	Retrieve LDAP server schema, if available
		*/
		function schema($start=0,$offset=0,$base='',$filter='',$fields='',$andor='',$sort='ASC',$order='',$query='')
		{
			$base       = 'cn=Subschema';
			$thisfilter = 'objectclass=*';
			//$filter = 'objectclass=subschema';
			$attrs      = array(
				'objectClasses',
				'attributeTypes',
				'matchingRules',
				'ldapSyntaxes'
			);

			$sr = @ldap_read($this->ldap,$base,$thisfilter,$attrs);
			if($sr)
			{
				// Might be openldap 2.X
				$entries = ldap_get_entries($this->ldap, $sr);
			}
			else
			{
				// Or in this case, perhaps iPlanet
				$base ='cn=schema';
				$sr = @ldap_read($this->ldap,$base,$thisfilter,$attrs);
				$entries = ldap_get_entries($this->ldap, $sr);
			}

			$this->total_entries = $this->entries['count'];

			// Use shared sorting routines, based on sort and order
			if ($order)
			{
				if ($sort == "ASC") {
					$ldap_fields = $this->asortbyindex($this->entries, $order);
				}
				else
				{
					$ldap_fields = $this->arsortbyindex($this->entries, $order);
				}
				$this->entries = $ldap_fields;
			}

			// This logic allows you to limit rows, or not.
			// The export feature, for example, does not limit rows.
			// This way, it can retrieve all rows at once.
			if ($start && $offset)
			{
				$limit = $start + $offset;;
			}
			elseif ($start && !$offset)
			{
				$limit = $start;
			}
			elseif(!$start && !$offset)
			{
				$limit = $this->total_records;
			}
			else
			{ #(!$start && $offset) {
				$start = 0;
				$limit = $offset;
			}
			//echo '('.$start.','.$limit.')';

			@reset($this->entries);
			$j=0; $newentry = array();
			if ($limit)
			{
				for ($i=$start;$i<$limit;$i++) {
					if ($this->entries[$i])
					{
						while (list($f_name,$f_value) = each($this->entries[$i])) {
							//echo '<br>'.$f_name.' '.$f_value[0];
							$newentry[$j][$f_name] = $f_value;
						}
					$j++;
					}
				}
				$this->entries = $newentry;
			}
			return $entries;
		}

		function get_ou($string,$x=0)
		{
			if($this->DEBUG) { echo "<br>Inbound dn: ".$string; }
			$ldap_context = explode(',',$string);
			if ($x)
			{
				while (list($key,$piece) = each($ldap_context))
				{
					if (!$key)
					{
						$ou = $piece;
					}
				}
			}
			else
			{
				$ou = $ldap_context[0];
			}
			//$ou = substr($ou,0,-1);
			//$ou = ereg_replace($this->base,'',$ou);
			//echo $ou; exit;
			return $ou;
		}

		function get_searchline($searchstring)
		{
			if (($searchstring=="*") || ($searchstring==""))
			{
				$this->searchline = "cn=*";
			}
			else
			{
				$this->searchline = sprintf("cn=*%s*",$searchstring);
			}
			return $this->searchline;
		}

		function makefilter($thisfilter, $andor='AND',$lquery='')
		{
			if($this->DEBUG) { echo "<br>Inbound query: ".$lquery; }
			if($this->DEBUG) { echo"<br>Inbound filter: ".$thisfilter; }
			$filterarray = explode(',',$thisfilter);
			$total = count($filterarray);

			if ($thisfilter)
			{
				if ($total == 1)
				{
					if($this->DEBUG) { echo"<br>Filter has one element"; }
					$filter = $thisfilter;
				}
				else
				{
					if($this->DEBUG) { echo"<br>Filter has $total elements"; }
					if ($andor == 'AND')
					{
						$filter = '(&';
					}
					else
					{
						$filter = '(|';
					}

					for($i=0;$i<$total;$i++)
					{
						$filter .= '('.$filterarray[$i].')';
					}
					$filter .= ')';
				}
			}
			else
			{
				$filter = '(|(ou=*)(nismapname=*))';
				if($this->DEBUG) { echo"<br>Outbound filter: ".$filter; }
			}
			if($lquery)
			{
				$filter = "(|(cn=*$lquery*)(uid=*$lquery*)(uidnumber=*$lquery*)(gidnumber=*$lquery*)(description=*$lquery*)(sn=*$lquery*)(givenname=*$lquery*))";
			}
			if($this->DEBUG) { echo '<br>Querying: '.$filter; }
			return $filter;
		}

		// Read a single dn
		function read($dn)
		{
			// First cleanup the spaces, but only the ones in the entry's dn
			$thisdn = $this->despace($dn);
			if($this->DEBUG) { echo '<br>Reading: "' . $thisdn . '"'; }

			$sr = ldap_read($this->ldap,$thisdn,'objectclass=*');
			$this->entry = ldap_get_entries($this->ldap,$sr);
			return $this->entry;
		}

		// Accepts search params and field list
		// Sets total_entries
		// Returns $entries array
		function search($start=0,$offset=0,$base='',$filter='',$fields='',$andor='',$sort='ASC',$order='',$query='')
		{
			if($query)
			{
				$thisfilter=$this->makefilter($filter,'OR',$query);
			}
			else
			{
				$thisfilter = $this->makefilter($filter,$andor);
			}

			if ($fields)
			{
				if($base)
				{
					$sr = @ldap_search($this->ldap,$base,$thisfilter,$fields);
				}
				else
				{
					$sr = @ldap_search($this->ldap,$this->base,$thisfilter,$fields);
				}
			}
			else
			{
				if($base)
				{
					$sr = @ldap_search($this->ldap,$base,$thisfilter);
				}
				else
				{
					$sr = @ldap_search($this->ldap,$this->base,$thisfilter);
				}
			}
			$this->entries = @ldap_get_entries($this->ldap, $sr);
			$this->total_entries = $this->entries['count'];

			// Use shared sorting routines, based on sort and order
			if ($order)
			{
				if ($sort == "ASC") {
					$ldap_fields = $this->asortbyindex($this->entries, $order);
				}
				else
				{
					$ldap_fields = $this->arsortbyindex($this->entries, $order);
				}
				$this->entries = $ldap_fields;
			}

			// This logic allows you to limit rows, or not.
			// The export feature, for example, does not limit rows.
			// This way, it can retrieve all rows at once.
			if ($start && $offset) {
				$limit = $start + $offset;;
			} elseif ($start && !$offset) {
				$limit = $start;
			} elseif(!$start && !$offset) {
				$limit = $this->total_records;
			} else { #(!$start && $offset) {
				$start = 0;
				$limit = $offset;
			}
			//echo '('.$start.','.$limit.')';

			@reset($this->entries);
			$j=0; $newentry = array();
			if ($limit)
			{
				for ($i=$start;$i<$limit;$i++) {
					if ($this->entries[$i])
					{
						while (list($f_name,$f_value) = each($this->entries[$i])) {
							//echo '<br>'.$f_name.' '.$f_value[0];
							$newentry[$j][$f_name] = $f_value;
						}
					$j++;
					}
				}
				$this->entries = $newentry;
			}
			return $this->entries;
		}

		// Accepts search params and field list
		// Sets total_entries
		// Returns total_entries
		function count($base="",$filter,$andor='',$query='')
		{
			if($query)
			{
				$thisfilter=$this->makefilter($filter,'OR',$query);
			}
			else
			{
				$thisfilter = $this->makefilter($filter,$andor);
			}
			if($base)
			{
				$sr   = @ldap_search($this->ldap,$base,$thisfilter);
			}
			else
			{
				$sr   = @ldap_search($this->ldap,$this->base,$thisfilter);
			}

			$info = ldap_get_entries($this->ldap, $sr);
			$this->total_entries = $info['count'];
			//echo $this->total_entries;
			return $this->total_entries;
		}

		// This WILL be the magic for verifying schema prior to adding
		//  or updating entries.
		// It might also be used in batch mode to verify a list of entries
		//  already in LDAP, e.g. if the ldbm was created offline.
		function schemacheck($type='People',$nis='')
		{
		}

		
		// Create a shell array for a new entry, based on specs
		// laid out above for the type of entry we are adding.
		function create($type='People',$nis='')
		{
			if ($nis)
			{
				$object = $this->nismapnames[$nis][0];
				$this->objkey = $this->nismapnames[$type][1];
			}
			else
			{
				$object = $this->orgunits[$type][0];
				$this->objkey = $this->orgunits[$type][1];
			}
			$occount=0;
			while(list($key,$oc) = each($this->$object))
			{
				$loc = strtolower($oc);
				$this->entry[0]['objectclass'][$occount] = $loc;
				//echo $oc;exit;
				if($this->DEBUG) { echo "\n".'<br>Adding objectclass[0]['.$loc.']['.$occount.']'."\n"; }
				if($loc != 'top')
				{
					while(list($attr,$req) = each($this->$loc))
					{
						$lattr = strtolower($attr);
						if($this->DEBUG) { echo '<br>Adding attr: '.$lattr.":"; }
						if ($req)
						{
							if($this->DEBUG) { echo ' required'."\n"; }
							$this->entry[0][$lattr][0] = '';
						}
						else
						{
							if($this->DEBUG) { echo ' optional'."\n"; }
							$this->entry[0][$lattr][0] = '';
						}
					}
				}
				$occount++;
			}
			return $this->entry;
		}

		function add($dn = '')
		{
			if ($dn && $this->entry)
			{
				// call for schemacheck here
				if (ldap_add($this->ldap, $dn, $this->entry))
				{
					$cd = 28;
				}
				else
				{
					$cd = 99; // Come out with a code for this
				}
				return $cd;
			}
			else
			{
				return 99;
			}
		}

		function update($dn = '')
		{
			if ($dn && $this->entry)
			{
				// call for schemacheck here
				if (ldap_modify($this->ldap, $dn, $this->entry))
				{
					$cd = 28;
				}
				else
				{
					$cd = 99; // Come out with a code for this
				}
				return $cd;
			}
			else
			{
				return 99;
			}
		}

		function delete($entries)
		{
			if(count($entries) > 1)
			{
				for ($i=0; $i < $this->total_entries; $i++)
				{
					#print "<br> delete".$allValues[$i]["dn"];
					if ($this->delete($entries[$i]["dn"]))
					{
						$cd = 28;
					}
					else
					{
						#print ldap_error($ldap);
						$cd = 99;
					}
				} 
			}
			else
			{
				if(ldap_delete($this->ldap,$entries["dn"]))
				{
					$cd = 28;
				}
				else
				{
					$cd = 99;
				}
			}
			return $cd;
		}

		function exists($entry)
		{
			$filter = "(|(uid=$loginid))";

			$sr = @ldap_search($GLOBALS['ldap'],$this->base,$filter,array("uid"));
			$total = ldap_get_entries($ldap, $sr);

			// Odd, but it works
			if (count($total) == 2)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function close()
		{
			@ldap_close($this->ldap);
		}

		// remove count and keys, leaving only attrs
		function clean($value)
		{
			if((gettype($value) != 'integer') && ($value != 'count'))
			{
				return $value;
			}
		}

		// remove spaces from the dn
		function despace($dn)
		{
			$parsedn = explode(',',$dn);
			while (list($key,$piece) = each($parsedn))
			{
				$piece     = trim($piece);
				if (!strstr(strtolower($dn),'nslielement')) // special case, they like spaces here
				{
					$piece = ereg_replace(' ','+',$piece);
				}
				$pieces[] = $piece;
			}
			$thisdn = implode(',',$pieces);
			//echo $thisdn;
			return $thisdn;
		}

		//
		// Take form checkbox input parsed into an array
		// add objectclasses and empty attrs where needed
		// This still violates schemacheck :(
		//
		function form_addobj($dn,$addto="")
		{
			if($addto)
			{
				$tmpdata = $this->read($dn);
				$i = count($tmpdata[0]['objectclass']) - 1; // less the count var
				$newattrs['objectclass'] = $tmpdata[0]['objectclass'];

				while (list($key,$addoc) = each($addto))
				{
					if($addoc)
					{
						$lcaddoc = strtolower($addoc);
						$newattrs['objectclass'][$i] = $addoc;
						//echo '#'.$lcaddoc.'#';
						while(list($attr,$req) = @each($this->$lcaddoc))
						{
							if (!$tmpdata[0][$attr])
							{
								// This is broken, most of the time
								// Stuff some value there so ldap doesn't complain of null
								$newattrs[$attr] = 1;
								//echo '<br> Adding: '.$attr;
							}
						}
						$i++;
					}
				}
				ldap_modify($this->ldap,$dn,$newattrs);
				/*
				@reset($newattrs);
				while (list($attr) = each($newattrs))
				{
					echo '<br> Attr: '.$attr.' Value: '.$newattrs[$attr];
				}
				$GLOBALS['phpgw']->common->phpgw_footer();
				exit;
				*/
			}
		}

		/**
		** comesafter ($s1, $s2)
		**
		** Returns 1 if $s1 comes after $s2 alphabetically, 0 if not.
		**/
		function comesafter ($s1, $s2)
		{
			/**
			** We don't want to overstep the bounds of one of the strings and segfault,
			** so let's see which one is shorter.
			**/
			$order = 1;

			if ( (strlen($s1) == 0) )
			{
				return 0;
			}

			if ( (strlen($s2) == 0) )
			{
				return 1;
			}

			if (strlen ($s1) > strlen ($s2))
			{
				$temp = $s1;
				$s1 = $s2;
				$s2 = $temp;
				$order = 0;
			}

			for ($index = 0; $index < strlen ($s1); $index++)
			{
				/* $s1 comes after $s2 */
				if (strtolower($s1[$index]) > strtolower($s2[$index])) { return ($order); }

				/* $s1 comes before $s2 */
				if (strtolower($s1[$index]) < strtolower($s2[$index])) { return (1 - $order); }
			}
				/* Special case in which $s1 is a substring of $s2 */

			return ($order);
		}

		/*
		* asortbyindex ($sortarray, $index)
		*
		* Sort a multi-dimensional array by a second-degree index. For instance, the 0th index
		* of the Ith member of both the group and user arrays is a string identifier. In the
		* case of a user array this is the username; with the group array it is the group name.
		* asortby
		*/
		function asortbyindex ($sortarray, $index)
		{
			$lastindex = count($sortarray) - 2;
			for ($subindex = 0; $subindex < $lastindex; $subindex++)
			{
				$lastiteration = $lastindex - $subindex;
				for ($iteration = 0; $iteration < $lastiteration; $iteration++)
				{
					$nextchar = 0;
					if ($this->comesafter($sortarray[$iteration][$index], $sortarray[$iteration + 1][$index]))
					{
						$temp = $sortarray[$iteration];
						$sortarray[$iteration] = $sortarray[$iteration + 1];
						$sortarray[$iteration + 1] = $temp;
					}
				}
			}
			return ($sortarray);
		}

		function arsortbyindex ($sortarray, $index)
		{
			$lastindex = count($sortarray) - 1;
			for ($subindex = $lastindex; $subindex > 0; $subindex--)
			{
				$lastiteration = $lastindex - $subindex;
				for ($iteration = $lastiteration; $iteration > 0; $iteration--)
				{
					$nextchar = 0;
					if ($this->comesafter($sortarray[$iteration][$index], $sortarray[$iteration - 1][$index]))
					{
						$temp = $sortarray[$iteration];
						$sortarray[$iteration] = $sortarray[$iteration - 1];
						$sortarray[$iteration - 1] = $temp;
					}
				}
			}
			return ($sortarray);
		}
	}
?>

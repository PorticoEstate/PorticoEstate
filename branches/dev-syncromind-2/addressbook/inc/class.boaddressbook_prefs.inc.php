<?php
  /**************************************************************************\
  * phpGroupWare API - Commononly used functions                             *
  * This file written by Alex Borges <alex@co.com.mx>                        *
  * Business Logic for addressbook preferences                               *
  * Copyright (C) 2003 Alex Borges                                           *
  * -------------------------------------------------------------------------*
  * This library is part of the phpGroupWare Addressbook app                 *
  * http://www.phpgroupware.org/                                             * 
  * ------------------------------------------------------------------------ *
  * This library is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU  General Public License as published by       *
  * the Free Software Foundation.                                            *
  * This library is distributed in the hope that it will be useful, but      *
  * WITHOUT ANY WARRANTY; without even the implied warranty of               *
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
  * See the GNU General Public License for more details.                     *
  * You should have received a copy of the GNU  General Public License       *
  * along with this library; if not, write to the Free Software Foundation,  *
  * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
  \**************************************************************************/
class boaddressbook_prefs
{
	var $preferences;
	var $person_columns;
	var $org_columns;
	var $default_category='__NONE__';
	var $person_columns_forced=false;
	var $org_columns_forced=false;
	var $default_category_forced=false;
	var $public=array('read_preferences' => True);
	function __construct()
	{
		$this->preferences=CreateObject('phpgwapi.preferences');
	}
	
	function save_preferences($type='')  
	{
		if(is_array($this->person_columns))
		{
			if(count($this->person_columns) >=1 || 
			   (
			    isset($this->person_columns['comm_types'])
			    &&(count($this->person_columns['comm_types'])>0)))
			{
				//print "<br /><B>count(Person )columns >= 1".print_r($this->person_columns)."</B><br />";
				$this->preferences->add('addressbook', 'person_columns', $this->person_columns, $type);
			}
			else
			{

				//print "<br /><B>Deleting person_columns $type</B><br />";
				$this->preferences->delete('addressbook',"person_columns",$type);
				$this->remove_from_forced("person_columns");
			}
		}
		else
		{
			$this->preferences->delete('addressbook','person_columns',$type);
			$this->remove_from_forced("person_columns");
		}

		if(count($this->org_columns) >=1 || 
			   (
			    isset($this->org_columns['comm_types'])
			    &&(count($this->org_columns['comm_types'])>0)))
		{
			if(count($this->org_columns) >=1)
			{

				$this->preferences->add('addressbook', 'org_columns', $this->org_columns, $type);
			}
			else
			{
				$this->preferences->delete('addressbook','org_columns',$type);

				//print "<br /><B>Deleting person_columns $type</B><br />";
				$this->remove_from_forced("org_columns");
				$this->org_columns_forced=false;
			}
		}
		else
		{
			$this->preferences->delete('addressbook','org_columns',$type);
			$this->remove_from_forced("org_columns");
			$this->org_columns_forced=false;
		}

		if($this->default_category!='__NONE__')
		{
			/*	print "<B>DSASDADSADSADAS</B>";
				print $this->default_category; */

			$this->preferences->add('addressbook','default_category',$this->default_category,$type);
		}
		else
		{
				$this->preferences->delete('addressbook','default_category',$type);
				$this->remove_from_forced('default_category');
		}
		$this->preferences->save_repository(true, $type);

	}
	function read_preferences($type='') 
	{
		$this->preferences->read();
		$temp=$this->preferences->data['addressbook'];
		$this->person_columns = $temp['person_columns'];
		$this->person_columns_forced = $this->is_forced_value('person_columns');
		//Check that we dont reflect types that arent asked of us in 'type'
		if(!$this->person_columns_forced && $type=='forced')
		{
			
			unset($this->person_columns);
		}

		$this->org_columns = $temp['org_columns'];
		$this->org_columns_forced=$this->is_forced_value('org_columns');
		if(!$this->org_columns_forced && $type=='forced')
		{

			//print "<br /><B>GRABLING</B><br />";
			unset($this->org_columns);

		}
		$this->default_category=$temp['default_category'];
		$this->default_category_forced=$this->is_forced_value('default_category');

		if($this->default_category_forced && $type!='forced')
		{
			$this->default_category='__NONE__';
		}
	}
	function remove_from_forced($preference_name)
	{
		if(!empty($this->preferences->forced['addressbook'][$preference_name]))
		{
			unset($this->preferences->forced['addressbook'][$preference_name]);
			
		}
	}
	function is_forced_value($preference_name)
	{
		if ($this->preferences->forced['addressbook'][$preference_name])
		{
			return True;
		}
		else
		{
			return False;
		}
	}

}

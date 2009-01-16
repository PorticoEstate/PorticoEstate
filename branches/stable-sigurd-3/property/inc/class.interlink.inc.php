<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id: class.interlink.inc.php 732 2008-02-10 16:21:14Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * interlink - handles information of relations of items across locations.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */

	class property_interlink
	{
		/**
		* @var object $_db Database connection
		*/
		protected $_db;

		/**
		* Constructor
		*
		*/

		function __construct()
		{
//			$this->_account			=& $GLOBALS['phpgw_info']['user']['account_id'];
			$this->_db 				=& $GLOBALS['phpgw']->db;
			$this->boadmin_entity	= CreateObject('property.boadmin_entity');

//			$this->_like 			=& $this->db->like;
//			$this->_join 			=& $this->db->join;
//			$this->_left_join		=& $this->db->left_join;
		}

		/**
		* Get relation of the interlink
		*
		* @param string  $appname  the application name for the location
		* @param string  $location the location name
		* @param integer $id       id of the referenced item
		* @param integer $role     role of the referenced item ('origin' or 'target')
		*
		* @return array interlink data
		*/

		public function get_relation($appname, $location, $id, $role = 'origin')
		{
			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$id				= (int) $id;

			switch( $role )
			{
				case 'target':
					$sql = "SELECT location2_id as linkend_location, location2_item_id as linkend_id FROM phpgw_interlink WHERE location1_id = {$location_id} AND location1_item_id = {$id} ORDER by location1_id DESC";
					break;
				default:
					$sql = "SELECT location1_id as linkend_location, location1_item_id as linkend_id FROM phpgw_interlink WHERE location2_id = {$location_id} AND location2_item_id = {$id} ORDER by location2_id DESC";
			}

			$this->_db->query($sql,__LINE__,__FILE__);
			$relation = array();

			$last_type = false;
			$i=-1;
			while ($this->_db->next_record())
			{
				if($last_type != $this->_db->f('linkend_location'))
				{
					$i++;
				}
				$relation[$i]['linkend_location']	= $this->_db->f('linkend_location');
				$relation[$i]['data'][] = array( 'id' => $this->_db->f('linkend_id'));

				$last_type = $this->_db->f('linkend_location');
			}

			foreach ($relation as &$entry)
			{
				$linkend_location = $GLOBALS['phpgw']->locations->get_name($entry['linkend_location']);
				$entry['location'] = $linkend_location['location'];

				$entry['descr']= $this->get_location_name($linkend_location['location']);

				foreach ($entry['data'] as &$data)
				{
					$data['link'] = $this->get_relation_link($linkend_location, $data['id']);
				}
			}
			return $relation;
		}

		/**
		* Get specific target
		*
		* @param string  $appname  the application name for the location
		* @param string  $location1 the location name of origin
		* @param string  $location1 the location name of target
		* @param integer $id       id of the referenced item
		* @param integer $role     role of the referenced item ('origin' or 'target')
		*
		* @return array targets
		*/

		public function get_specific_relation($appname, $location1, $location2, $id, $role = 'origin')
		{
			$location1_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location1);
			$location2_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location2);
			$id = (int) $id;
			$targets = array();

			switch( $role )
			{
				case 'target':
					$sql = "SELECT location2_item_id as item_id FROM phpgw_interlink WHERE location1_id = {$location1_id} AND location2_id = {$location2_id} AND location1_item_id = {$id}";
					break;
				default:
					$sql = "SELECT location1_item_id as item_id FROM phpgw_interlink WHERE location1_id = {$location1_id} AND location2_id = {$location2_id} AND location2_item_id = {$id}";
			}

			$this->_db->query($sql,__LINE__,__FILE__);
			while ($this->_db->next_record())
			{
				$targets[]= $this->_db->f('item_id');
			}
			return $targets;
		}

		
		/**
		* Get location name
		*
		* @param array   $linkend_location the location
		* @param integer $id			   the id of the referenced item
		*
		* @return string the linkt to the the related item
		*/

		public function get_location_name($location)
		{
			if(substr($location,1,6)=='entity')
			{
				$type		= explode(".",$location);
				$entity_id	= $type[2];
				$cat_id		= $type[3];

				$entity_category = $this->boadmin_entity->read_single_category($entity_id,$cat_id);
				return $entity_category['name'];
			}
			else
			{
				return lang($location);
			}
		}
		/**
		* Get relation of the interlink
		*
		* @param array   $linkend_location the location
		* @param integer $id			   the id of the referenced item
		*
		* @return string the linkt to the the related item
		*/

		public function get_relation_link($linkend_location, $id)
		{
			$link = array();
			$type = $linkend_location['location'];
			if($type == '.ticket')
			{
				$link = array('menuaction' => 'property.uitts.view', 'id' => $id);
			}
			else if($type == '.project.workorder')
			{
				$link = array('menuaction' => 'property.uiworkorder.view', 'id' => $id);
			}
			else if($type == '.project.request')
			{
				$link = array('menuaction' => 'property.uirequest.view', 'id' => $id);
			}
			else if($type == '.project')
			{
				$link = array('menuaction' => 'property.uiproject.view', 'id' => $id);
			}
			else if( substr($type, 1, 6) == 'entity' )
			{
				$type		= explode('.',$type);
				$entity_id	= $type[2];
				$cat_id		= $type[3];
				$link =	array
				(
					'menuaction'	=> 'property.uientity.view',
					'entity_id'		=> $entity_id,
					'cat_id'		=> $cat_id,
					'id'			=> $id
				);
			}
			return $GLOBALS['phpgw']->link('/index.php',$link);	
		}

		/**
		* Get entry date of the related item
		*
		* @param string  $appname  		  the application name for the location
		* @param string  $origin_location the location name of the origin
		* @param string  $target_location the location name of the target
		* @param integer $id			  id of the referenced item (parent)
		* @param integer $entity_id		  id of the entity type if the type is a entity
		* @param integer $cat_id		  id of the entity_category type if the type is a entity
		*
		* @return array date_info and link to related items
		*/

		public function get_child_date($appname, $origin_location, $target_location, $id, $entity_id = '', $cat_id = '')
		{
			$dateformat 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if($cat_id)
			{
				$soadmin_entity	= CreateObject('property.soadmin_entity');
			}

			$location1_id	= $GLOBALS['phpgw']->locations->get_id($appname, $origin_location);
			$location2_id	= $GLOBALS['phpgw']->locations->get_id($appname, $target_location);

			$sql = "SELECT entry_date, location2_item_id FROM phpgw_interlink WHERE location1_item_id = {$id} AND location1_id = {$location1_id} AND location2_id = {$location2_id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			$date_info = array();
			while ($this->_db->next_record())
			{
				$date_info[] = array
				(
					'entry_date'	=> $GLOBALS['phpgw']->common->show_date($this->_db->f('entry_date'),$dateformat),
					'target_id'		=> $this->_db->f('location2_item_id')
				);
			}

			foreach ( $date_info as &$entry )
			{
				$entry['link']=$this->get_relation_link(array('location'=>$target_location), $entry['target_id']);
				if($cat_id)
				{
					$entry['descr']=$soadmin_entity->read_category_name($entity_id,$cat_id);
				}
				else
				{
					$entry['descr']=lang($target_location);
				}
			}
			return array('date_info' => $date_info);
		}

		/**
		* Add link to item
		*
		* @param array  $data	link data
		* @param object $db		db-object - used to keep the operation within the callers transaction
		*
		* @return bool true on success, false otherwise
		*/

		public function add($data, $db = '')
		{
			if(!$db)
			{
				$db = $this->_db;
			}
			$location1_id		= $data['location1_id'];
			$location1_item_id	= $data['location1_item_id'];
			$location2_id		= $data['location2_id'];
			$location2_item_id	= $data['location2_item_id'];
			$account_id			= $data['account_id'];
			$entry_date			= time();
			$is_private			= isset($data['is_private']) && $data['is_private'] ? $data['is_private'] : -1;
			$start_date			= isset($data['start_date']) && $data['start_date'] ? $data['start_date'] : -1;
			$end_date			= isset($data['end_date']) && $data['end_date'] ? $data['end_date'] : -1;
			
			$db->query('INSERT INTO phpgw_interlink (location1_id,location1_item_id,location2_id,location2_item_id,account_id,entry_date,is_private,start_date,end_date) '
				. "VALUES ({$location1_id},{$location1_item_id},{$location2_id},{$location2_item_id},{$account_id},{$entry_date},{$is_private},{$start_date},{$end_date})",__LINE__,__FILE__);
			
		}

		/**
		* Delete link at origin
		*
		* @param string  $appname   the application name for the location
		* @param string  $location1 the location name of origin
		* @param string  $location1 the location name of target
		* @param integer $id        id of the referenced item
		* @param object $db			db-object - used to keep the operation within the callers transaction
		*
		* @return array interlink data
		*/

		public function delete_at_origin($appname, $location1, $location2, $id, $db = '')
		{
			if(!$db)
			{
				$db = $this->_db;
			}

			$location1_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location1);
			$location2_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location2);
			$id				= (int) $id;

			$sql = "DELETE FROM phpgw_interlink WHERE location1_id = {$location1_id} AND location2_id = {$location2_id} AND location1_item_id = {$id}";

			$db->query($sql,__LINE__,__FILE__);
		}

		/**
		* Delete all relations based on a given start point (location1 and item1)
		*
		* @param string  $appname   the application name for the location
		* @param string  $location  the location name of target
		* @param integer $id        id of the referenced item
		* @param object $db			db-object - used to keep the operation within the callers transaction
		*
		* @return array interlink data
		*/

		public function delete_at_target($appname, $location, $id, $db = '')
		{
			if(!$db)
			{
				$db = $this->_db;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$id 		 = (int) $id;

			$sql		 = "DELETE FROM phpgw_interlink WHERE location1_id = {$location_id} AND location1_item_id = {$id}";

			$db->query($sql,__LINE__,__FILE__);
		}

		/**
		* Delete all relations based on a given end point (location2 and item2)
		*
		* @param string  $appname   the application name for the location
		* @param string  $location  the location name of target
		* @param integer $id        id of the referenced item
		* @param object $db			db-object - used to keep the operation within the callers transaction
		*
		* @return array interlink data
		*/

		public function delete_from_target($appname, $location, $id, $db = '')
		{
			if(!$db)
			{
				$db = $this->_db;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			$id 		 = (int) $id;

			$sql		 = "DELETE FROM phpgw_interlink WHERE location2_id = {$location_id} AND location2_item_id = {$id}";

			$db->query($sql,__LINE__,__FILE__);
		}


	}

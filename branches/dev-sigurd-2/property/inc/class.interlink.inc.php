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
	   the Free Software Foundation, either version 3 of the License, or
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

			switch( $role )
			{
				case 'target':
					$sql = "SELECT location1_id as linkend_location, location1_item_id as linkend_id FROM phpgw_interlink WHERE location2_id = {$location_id} AND location2_item_id = {$id} ORDER by location2_id DESC";
					break;
				default:
					$sql = "SELECT location2_id as linkend_location, location2_item_id as linkend_id FROM phpgw_interlink WHERE location1_id = {$location_id} AND location1_item_id = {$id} ORDER by location1_id DESC";
			}

			$last_type = false;
			$i=-1;
			while ($this->_db->next_record())
			{
				if($last_type != $this->_db->f('linkend_location'))
				{
					$i++;
				}
				$relation[$i]['linkend_location']	= $this->_db->f('linkend_location');
				$relation[$i]['id']					= $this->_db->f('linkend_id');

				$last_type = $this->_db->f('linkend_location');
			}

			foreach ($relation as &$entry)
			{
				$linkend_location = $GLOBALS['phpgw']->locations->get_name($entry['linkend_location']);
				$entry['type'] = $linkend_location['name'];
				$entry['link'] = $this->_get_relation_link($linkend_location, $entry['id']);
			}
		}
		
		/**
		* Get relation of the interlink
		*
		* @param array   $linkend_location the location
		* @param integer $id			   the id of the referenced item
		*
		* @return array the extravars part of the relation link
		*/

		protected function _get_relation_link($linkend_location, $id)
		{
			$link = array();
			$type = $linkend_location['name'];
			if($type == '.tts')
			{
				$link = array('menuaction' => 'property.uitts.view', 'id' => $id);
			}
			else if($type == 'request')
			{
				$link = array('menuaction' => 'property.uirequest.view', 'id' => $id);
			}
			else if($type == 'project')
			{
				$link = array('menuaction' => 'property.uiproject.view', 'id' => $id);
			}
			else if( substr($type, 0, 6) == 'entity' )
			{
				$type		= explode('.',$type);
				$entity_id	= $type[1];
				$cat_id		= $type[2];
				$link =	array
				(
					'menuaction'	=> 'property.uientity.view',
					'entity_id'		=> $entity_id,
					'cat_id'		=> $cat_id,
					'id'			=> $id
				);
			}
			return $link;	
		}
	}

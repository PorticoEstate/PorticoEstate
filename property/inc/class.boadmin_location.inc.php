<?php

	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage admin
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_boadmin_location
	{

		public $start;
		public $query;
		public $sort;
		public $order;
		public $allrows;

		/**
		 * @var object $custom reference to custom fields object
		 */
		protected $custom;
		var $public_functions = array
		(
			'read'			 => true,
			'read_single'	 => true,
			'save'			 => true,
			'delete'		 => true,
			'check_perms'	 => true
		);

		function property_boadmin_location( $session = false )
		{
			$this->so		 = CreateObject( 'property.soadmin_location' );
			$this->bocommon	 = CreateObject( 'property.bocommon' );
			$this->custom	 = createObject( 'property.custom_fields' );

			if ( $session )
			{
				//		$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	 = phpgw::get_var( 'start', 'int', 'REQUEST', 0 );
			$query	 = phpgw::get_var( 'query' );
			$sort	 = phpgw::get_var( 'sort' );
			$order	 = phpgw::get_var( 'order' );
			$allrows = phpgw::get_var( 'allrows', 'bool' );

			$this->start	 = $start ? $start : 0;
			$this->query	 = isset( $query ) ? $query : $this->query;
			$this->sort		 = isset( $sort ) && $sort ? $sort : '';
			$this->order	 = isset( $order ) && $order ? $order : '';
			$this->allrows	 = isset( $allrows ) && $allrows ? $allrows : '';
		}

		function save_sessiondata( $data )
		{
			if ( $this->use_session )
			{
				$GLOBALS['phpgw']->session->appsession( 'session_data', 'standard_e', $data );
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession( 'session_data', 'standard_e' );

			$this->start = $data['start'];
			$this->query = $data['query'];
			$this->sort	 = $data['sort'];
			$this->order = $data['order'];
		}

		function read()
		{
			$standard = $this->so->read( array('start'	 => $this->start, 'query'	 => $this->query, 'sort'	 => $this->sort, 'order'	 => $this->order) );

			$this->total_records = $this->so->total_records;


			return $standard;
		}

		function read_config()
		{
			$standard = $this->so->read_config( array('start' => $this->start, 'query' => $this->query, 'sort' => $this->sort, 'order' => $this->order) );

			$this->total_records = $this->so->total_records;

			return $standard;
		}

		function read_config_single( $column_name )
		{
			return $this->so->read_config_single( $column_name );
		}

		function read_single( $id )
		{
			return $this->so->read_single( $id );
		}

		function save( $standard )
		{
			if ( isset( $standard['id'] ) && $standard['id'] )
			{
				$receipt = $this->so->edit( $standard );
			}
			else
			{
				$receipt = $this->so->add( $standard );
			}
			return $receipt;
		}

		function delete( $type_id, $id, $attrib = '', $group_id )
		{
			if ( $id && !$attrib )
			{
				$receipt = $this->so->delete( $id );
			}
			else if ( $type_id && $id && $attrib )
			{
				$ok		 = 0;
				$receipt = array();

				if ( $this->custom->delete( 'property', ".location.{$type_id}", $id,
								"fm_location{$type_id}_history", true ) )
				{
					$ok++;
				}
				if ( $this->custom->delete( 'property', ".location.{$type_id}", $id,
								"fm_location{$type_id}" ) )
				{
					$ok++;
				}
				if ( $ok == 2 )
				{
					$receipt['message'][] = array('msg' => lang( 'attibute has been deleted' ));
				}
				else
				{
					$receipt['error'][] = array('msg' => lang( 'something went wrong' ));
				}
			}
			else if ( $type_id && $group_id )
			{
				if ( $this->custom->delete_group( 'property', ".location.{$type_id}",
									  $group_id ) )
				{
					$receipt['message'][] = array('msg' => lang( 'attibute group %1 has been deleted',
												  $group_id ));
				}
				else
				{
					$receipt['error'][] = array('msg' => lang( 'something went wrong' ));
				}
			}

			return $receipt;
		}

		function get_attrib_group_list( $type_id, $selected )
		{
			$location	 = ".location.{$type_id}";
			$group_list	 = $this->read_attrib_group( $location, true );

			foreach ( $group_list as &$group )
			{
				if ( $group['id'] == $selected )
				{
					$group['selected'] = true;
				}
			}
			return $group_list;
		}

		function read_attrib_group( $location, $allrows = '' )
		{
			if ( $allrows )
			{
				$this->allrows = $allrows;
			}

			$attrib				 = $this->custom->find_group( 'property', $location, $this->start,
											$this->query, $this->sort, $this->order, $this->allrows );
			$this->total_records = $this->custom->total_records;

			return $attrib;
		}

		function read_single_attrib_group( $location, $id )
		{
			return $this->custom->get_group( 'property', $location, $id, true );
		}

		function resort_attrib_group( $location, $id, $resort )
		{
			$this->custom->resort_group( $id, $resort, 'property', $location );
		}

		public function save_attrib_group( $group, $action = '' )
		{
			$group['appname'] = 'property';

			if ( $action == 'edit' && $group['id'] )
			{
				if ( $this->custom->edit_group( $group ) )
				{
					return array
					(
						'msg' => array('msg' => lang( 'group has been updated' ))
					);
				}

				return array('error' => lang( 'Unable to update group' ));
			}
			else
			{
				$id = $this->custom->add_group( $group );
				if ( $id <= 0 )
				{
					return array('error' => lang( 'Unable to add group' ));
				}
				else if ( $id == -1 )
				{
					return array
					(
						'id'	 => 0,
						'error'	 => array
						(
							array('msg' => lang( 'group already exists, please choose another name' )),
							array('msg' => lang( 'Attribute group has NOT been saved' ))
						)
					);
				}

				return array
				(
					'id'	 => $id,
					'msg'	 => array('msg' => lang( 'group has been created' ))
				);
			}
		}

		function read_attrib( $type_id, $allrows = '' )
		{
			if ( $allrows || phpgw::get_var( 'allrows' ) == 1 )
			{
				$this->allrows = true;
			}

			$attrib = $this->custom->find( 'property', '.location.' . $type_id,
								  $this->start, $this->query, $this->sort, $this->order, $this->allrows );

			$this->total_records = $this->custom->total_records;

			return $attrib;
		}

		function read_single_attrib( $type_id, $id )
		{
			return $this->custom->get( 'property', ".location.{$type_id}", $id, true );
		}

		function resort_attrib( $data = array() )
		{
			$resort	 = isset( $data['resort'] ) ? $data['resort'] : 'up';
			$type_id = isset( $data['type_id'] ) ? $data['type_id'] : '';
			$id		 = (isset( $data['id'] ) ? $data['id'] : '');

			if ( !$type_id || !$id )
			{
				return;
			}

			$this->custom->resort( $id, $resort, 'property', '.location.' . $type_id );
		}

		public function save_attrib( $attrib, $action = '' )
		{
			$attrib['appname']	 = 'property';
			$attrib['location']	 = '.location.' . $attrib['type_id'];
			$primary_table		 = 'fm_location' . $attrib['type_id'];
			$history_table		 = $primary_table . '_history';

			if ( $action == 'edit' && $attrib['id'] )
			{
				if ( $this->custom->edit( $attrib, $history_table, true ) )
				{
					$this->custom->edit( $attrib, $primary_table );
					return array
					(
						'msg' => array('msg' => lang( 'Field has been updated' ))
					);
				}

				return array('error' => lang( 'Unable to update field' ));
			}
			else
			{
				$id = $this->custom->add( $attrib, $primary_table );
				$this->custom->add( $attrib, $history_table, true );
				if ( $id <= 0 )
				{
					return array('error' => lang( 'Unable to add field' ));
				}
				else if ( $id == -1 )
				{
					return array
					(
						'id'	 => 0,
						'error'	 => array
						(
							array('msg' => lang( 'field already exists, please choose another name' )),
							array('msg' => lang( 'Attribute has NOT been saved' ))
						)
					);
				}

				return array
				(
					'id'	 => $id,
					'msg'	 => array('msg' => lang( 'Custom field has been created' ))
				);
			}
		}

		function save_config( $values = '', $column_name = '' )
		{
			return $this->so->save_config( $values, $column_name );
		}

		function select_location_type( $selected = '' )
		{
			$location_types = $this->so->select_location_type();
			return $this->bocommon->select_list( $selected, $location_types );
		}

		function select_nullable( $selected = '' )
		{
			$nullable[0]['id']	 = 'true';
			$nullable[0]['name'] = lang( 'true' );
			$nullable[1]['id']	 = 'false';
			$nullable[1]['name'] = lang( 'false' );

			return $this->bocommon->select_list( $selected, $nullable );
		}

		function get_list_info( $type_id = '', $selected = '' )
		{
			if ( $type_id )
			{
				$location_types = $this->so->select_location_type();

				for ( $i = 0; $i < ($type_id); $i++ )
				{
					$location[$i] = $location_types[$i];
					unset( $location[$i]['list_info'] );
					if ( isset( $selected[($i + 1)] ) && $selected[($i + 1)] )
					{
						$location[$i]['selected'] = 'selected';
					}
				}
				return $location;
			}
		}

	}
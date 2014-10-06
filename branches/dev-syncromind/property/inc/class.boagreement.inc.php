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
	 * @subpackage agreement
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_boagreement
	{

		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $role;
		var $member_id;

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

		function property_boagreement( $session = false )
		{
			$this->so		 = CreateObject( 'property.soagreement' );
			$this->bocommon	 = CreateObject( 'property.bocommon' );
			$this->custom	 = createObject( 'property.custom_fields' );

			if ( $session )
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start		 = phpgw::get_var( 'start', 'int', 'REQUEST', 0 );
			$query		 = phpgw::get_var( 'query' );
			$sort		 = phpgw::get_var( 'sort' );
			$order		 = phpgw::get_var( 'order' );
			$filter		 = phpgw::get_var( 'filter', 'int' );
			$cat_id		 = phpgw::get_var( 'cat_id', 'int' );
			$vendor_id	 = phpgw::get_var( 'vendor_id', 'int' );
			$allrows	 = phpgw::get_var( 'allrows', 'bool' );
			$role		 = phpgw::get_var( 'role' );
			$member_id	 = phpgw::get_var( 'member_id', 'int' );
			$status_id	 = phpgw::get_var( 'status_id' );

			$this->role		 = $role;
			$this->so->role	 = $role;

			$this->status_id = isset( $_REQUEST['status_id'] ) ? $status_id : $this->status_id;
			$this->start	 = isset( $_REQUEST['start'] ) ? $start : $this->start;
			$this->order	 = isset( $_REQUEST['order'] ) ? $order : $this->order;
			$this->sort		 = isset( $_REQUEST['sort'] ) ? $sort : $this->sort;
			$this->query	 = isset( $_REQUEST['query'] ) ? $query : $this->query;
			$this->vendor_id = isset( $_REQUEST['vendor_id'] ) ? $vendor_id : $this->vendor_id;
			$this->member_id = isset( $_REQUEST['member_id'] ) ? $member_id : $this->member_id;
			$this->cat_id	 = isset( $_REQUEST['cat_id'] ) ? $cat_id : $this->cat_id;

			if ( !empty( $filter ) )
			{
				$this->filter = $filter;
			}

			if ( $allrows )
			{
				$this->allrows = $allrows;
			}
		}

		function save_sessiondata( $data )
		{
			if ( $this->use_session )
			{
				$GLOBALS['phpgw']->session->appsession( 'session_data', 'agreement', $data );
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession( 'session_data', 'agreement' );

//			_debug_array($data);die();

			$this->start	 = $data['start'];
			$this->query	 = $data['query'];
			$this->filter	 = $data['filter'];
			$this->sort		 = $data['sort'];
			$this->order	 = $data['order'];
			$this->cat_id	 = $data['cat_id'];
			$this->vendor_id = $data['vendor_id'];
			$this->member_id = $data['member_id'];
			$this->status_id = $data['status_id'];
		}

		function check_perms( $has, $needed )
		{
			return (!!($has & $needed) == true);
		}

		function select_vendor_list( $format = '', $selected = '' )
		{
			switch ( $format )
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file( array('select_vendor') );
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file( array('filter_vendor') );
					break;
			}

			$input_list	 = $this->so->select_vendor_list();
			$vendor_list = $this->bocommon->select_list( $selected, $input_list );

			return $vendor_list;
		}

		function read()
		{
			$agreements = $this->so->read( array('start' => $this->start, 'query' => $this->query, 'sort' => $this->sort, 'order' => $this->order,
				'filter' => $this->filter, 'cat_id' => $this->cat_id, 'allrows' => $this->allrows, 'member_id' => $this->member_id,
				'vendor_id' => $this->vendor_id, 'status_id' => $this->status_id) );
			$this->total_records = $this->so->total_records;

			$this->uicols = $this->so->uicols;

			foreach ( $agreements as &$agreement )
			{
				if ( $agreement['start_date'] )
				{
					$agreement['start_date'] = $GLOBALS['phpgw']->common->show_date( $agreement['start_date'],
																	  $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] );
				}
				if ( $agreement['termination_date'] )
				{
					$agreement['termination_date'] = $GLOBALS['phpgw']->common->show_date( $agreement['termination_date'],
																			$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] );
				}

				if ( $agreement['end_date'] )
				{
					$agreement['end_date'] = $GLOBALS['phpgw']->common->show_date( $agreement['end_date'],
																	$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] );
				}
			}
			return $agreements;
		}

		function read_details( $id )
		{
			$list = $this->so->read_details( array('start' => $this->start, 'query'	 => $this->query, 'sort' => $this->sort, 'order' => $this->order,
				'filter' => $this->filter, 'cat_id' => $this->cat_id, 'allrows' => $this->allrows, 'member_id' => $this->member_id,
				'agreement_id' => $id) );
			$this->total_records = $this->so->total_records;

			$this->uicols = $this->so->uicols;

			return $list;
		}

		function read_prizing( $data )
		{
			$list				 = $this->so->read_prizing( $data );
			$this->total_records = $this->so->total_records;

			$this->uicols = $this->so->uicols;

			for ( $i = 0; $i < count( $list ); $i++ )
			{
				$list[$i]['index_date'] = $GLOBALS['phpgw']->common->show_date( $list[$i]['index_date'],
																	$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] );
			}

			return $list;
		}

		function read_event( $data )
		{
			$boalarm			 = CreateObject( 'property.boalarm' );
			$event				 = $this->so->read_single( $data['agreement_id'] );
			$event['alarm_date'] = $event['termination_date'];
			$event['alarm']		 = $boalarm->read_alarms( $type				 = 'agreement',
											  $data['agreement_id'] );
			return $event;
		}

		function read_single( $data )
		{
			$values['attributes'] = $this->custom->find( 'property', '.agreement', 0, '',
												'ASC', 'attrib_sort', true, true );

			if ( isset( $data['agreement_id'] ) && $data['agreement_id'] )
			{
				$values = $this->so->read_single( $data['agreement_id'], $values );
			}

			$values = $this->custom->prepare( $values, 'property', '.agreement' );

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if ( isset( $values['entry_date'] ) && $values['entry_date'] )
			{
				$values['entry_date'] = $GLOBALS['phpgw']->common->show_date( $values['entry_date'],
																  $dateformat );
			}

			$values['start_date']	 = $GLOBALS['phpgw']->common->show_date( $values['start_date'],
																  $dateformat );
			$values['end_date']		 = $GLOBALS['phpgw']->common->show_date( $values['end_date'],
																 $dateformat );
			if ( isset( $values['termination_date'] ) && $values['termination_date'] )
			{
				$values['termination_date'] = $GLOBALS['phpgw']->common->show_date( $values['termination_date'],
																		$dateformat );
			}

			$vfs				 = CreateObject( 'phpgwapi.vfs' );
			$vfs->override_acl	 = 1;

			$values['files'] = $vfs->ls( array(
				'string'	 => "/property/agreement/{$data['agreement_id']}",
				'relatives'	 => array(RELATIVE_NONE)) );

			$vfs->override_acl = 0;

			if ( !isset( $values['files'][0]['file_id'] ) || !$values['files'][0]['file_id'] )
			{
				unset( $values['files'] );
			}

			return $values;
		}

		function read_single_item( $data )
		{
			$values['attributes'] = $this->custom->find( 'property', '.agreement.detail',
												0, '', 'ASC', 'attrib_sort', true, true );
			if ( isset( $data['agreement_id'] ) && $data['agreement_id'] && isset( $data['id'] ) && $data['id'] )
			{
				$values	 = $this->so->read_single_item( $data, $values );
			}
			$values	 = $this->custom->prepare( $values, 'property', '.agreement.detail' );
			return $values;
		}

		/**
		 * Arrange attributes within groups
		 *
		 * @param string  $location    the name of the location of the attribute
		 * @param array   $attributes  the array of the attributes to be grouped
		 *
		 * @return array the grouped attributes
		 */
		public function get_attribute_groups( $location, $attributes = array() )
		{
			return $this->custom->get_attribute_groups( 'property', $location,
											   $attributes );
		}

		function save( $values, $values_attribute = '', $action = '' )
		{
			$values['start_date']		 = $this->bocommon->date_to_timestamp( $values['start_date'] );
			$values['end_date']			 = $this->bocommon->date_to_timestamp( $values['end_date'] );
			$values['termination_date']	 = $this->bocommon->date_to_timestamp( $values['termination_date'] );

			if ( is_array( $values_attribute ) )
			{
				$values_attribute = $this->custom->convert_attribute_save( $values_attribute );
			}

			if ( $action == 'edit' )
			//			if ($values['agreement_id'])
			{
				if ( $values['agreement_id'] != 0 )
				{
					$receipt = $this->so->edit( $values, $values_attribute );
				}
			}
			else
			{
				$receipt = $this->so->add( $values, $values_attribute );
			}
			return $receipt;
		}

		function save_item( $values, $values_attribute = '' )
		{
			//_debug_array($values);
			$values['m_cost']		 = str_replace( ",", ".", $values['m_cost'] );
			$values['w_cost']		 = str_replace( ",", ".", $values['w_cost'] );
			$values['total_cost']	 = $values['m_cost'] + $values['w_cost'];

			if ( $values['index_count'] > 0 )
			{
				if ( $values['id'] != 0 )
				{
					$receipt = $this->so->edit_item( $values );
				}
			}
			else
			{
				$receipt = $this->so->add_item( $values );
			}
			return $receipt;
		}

		function update( $values )
		{
			$values['date'] = $this->bocommon->date_to_timestamp( $values['date'] );

			return $this->so->update( $values );
		}

		function delete_last_index( $agreement_id, $id )
		{
			$this->so->delete_last_index( $agreement_id, $id );
		}

		function delete_item( $agreement_id, $activity_id )
		{
			$this->so->delete_item( $agreement_id, $activity_id );
		}

		function delete( $agreement_id = '' )
		{
			$this->so->delete( $agreement_id );
		}

		function column_list( $selected = '', $allrows = '' )
		{
			if ( !$selected )
			{
				$selected = isset( $GLOBALS['phpgw_info']['user']['preferences']['property']["agreement_columns"] ) ? $GLOBALS['phpgw_info']['user']['preferences']['property']["agreement_columns"] : '';
			}

			$filter = array('list'	 => ''); // translates to "list IS NULL"
			$columns = $this->custom->find( 'property', '.agreement', 0, '', '', '',
								   true, false, $filter );

			$column_list = $this->bocommon->select_multi_list( $selected, $columns );

			return $column_list;
		}

		function request_next_id()
		{
			return $this->so->request_next_id();
		}

		function get_agreement_group_list( $selected = '' )
		{
			$agreement_groups = $this->so->get_agreement_group_list();
			return $this->bocommon->select_list( $selected, $agreement_groups );
		}

		function read_group_activity( $group_id = '', $agreement_id = '' )
		{
			$activity_list	 = $this->so->read_group_activity( $group_id, $agreement_id );
			$this->uicols	 = $this->so->uicols;
			return $activity_list;
		}

		function add_activity( $values, $agreement_id )
		{
			return $this->so->add_activity( $values, $agreement_id );
		}

		function select_status_list( $format = '', $selected = '' )
		{
			switch ( $format )
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file( array('status_select') );
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file( array('status_filter') );
					break;
			}

			$status_entries = $this->so->select_status_list();

			return $this->bocommon->select_list( $selected, $status_entries );
		}

		function get_activity_descr( $id )
		{
			return $this->so->get_activity_descr( $id );
		}

	}
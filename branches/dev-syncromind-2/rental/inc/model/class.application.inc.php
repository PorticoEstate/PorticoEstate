<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package rental
	 * @subpackage application
	 * @version $Id: $
	 */
	include_class('rental', 'model', 'inc/model/');

	class rental_application extends rental_model
	{

		const STATUS_REGISTERED = 1;
		const STATUS_PENDING = 2;
		const STATUS_REJECTED = 3;
		const STATUS_APPROVED = 4;

		protected $status;
		protected $ecodimb;
		protected $district_id;
		protected $composite_type;
		protected $date_start;
		protected $date_end;
		protected $cleaning;
		protected $payment_method;
		protected $title;
		protected $description;
		protected $firstname;
		protected $lastname;
		protected $company_name;
		protected $department;
		protected $address1;
		protected $address2;
		protected $postal_code;
		protected $place;
		protected $account_number;
		protected $phone;
		protected $email;
		protected $unit_leader;
		protected $comment;
		protected $assign_date_start;
		protected $assign_date_end;

		protected $type;
		protected $type_id;
		protected $party_id;
		protected $identifier;

		public function __construct( int $id = null )
		{
			parent::__construct((int)$id);
		}
		public function set_ecodimb( $ecodimb )
		{
			$this->ecodimb = $ecodimb;
		}

		public function get_ecodimb()
		{
			return $this->ecodimb;
		}

		public function set_district_id( $district_id )
		{
			$this->district_id = $district_id;
		}

		public function get_district_id()
		{
			return $this->district_id;
		}
		public function set_composite_type( $composite_type )
		{
			$this->composite_type = $composite_type;
		}

		public function get_composite_type()
		{
			return $this->composite_type;
		}
		public function set_date_start( $start_date )
		{
			$this->date_start = $start_date;
		}

		public function get_date_start()
		{
			return $this->start_date;
		}
		public function set_date_end( $date_end )
		{
			$this->date_end = $date_end;
		}
		public function get_date_end()
		{
			return $this->date_end;
		}

		public function set_cleaning( $cleaning )
		{
			$this->cleaning = $cleaning;
		}

		public function get_cleaning()
		{
			return (bool)$this->cleaning;
		}

		public function set_payment_method( $payment_method )
		{
			$this->payment_method = $payment_method;
		}

		public function get_payment_method()
		{
			return $this->payment_method;
		}

		public function set_title( $title )
		{
			$this->title = $title;
		}

		public function get_title()
		{
			return $this->title;
		}

		public function set_identifier( $identifier )
		{
			$this->identifier = $identifier;
		}

		public function get_identifier()
		{
			return $this->identifier;
		}

		public function set_description( $description )
		{
			$this->description = $description;
		}

		public function get_description()
		{
			return $this->description;
		}

		public function set_firstname( $firstname )
		{
			$this->firstname = $firstname;
		}

		public function get_firstname()
		{
			return $this->firstname;
		}

		public function set_lastname( $lastname )
		{
			$this->lastname = $lastname;
		}

		public function get_lastname()
		{
			return $this->lastname;
		}


		public function set_company_name( $company_name )
		{
			$this->company_name = $company_name;
		}

		public function get_company_name()
		{
			return $this->company_name;
		}
		public function set_department( $department )
		{
			$this->department = $department;
		}

		public function get_department()
		{
			return $this->department;
		}

		public function set_address1( $address1 )
		{
			$this->address1 = $address1;
		}

		public function get_address1()
		{
			return $this->address1;
		}

		public function set_address2( $address2 )
		{
			$this->address2 = $address2;
		}

		public function get_address2()
		{
			return $this->address2;
		}

		public function set_postal_code( $postal_code )
		{
			$this->postal_code = $postal_code;
		}

		public function get_postal_code()
		{
			return $this->postal_code;
		}

		public function set_place( $place )
		{
			$this->place = $place;
		}

		public function get_place()
		{
			return $this->place;
		}

		public function set_account_number( $account_number )
		{
			$this->account_number = $account_number;
		}

		public function get_account_number()
		{
			return $this->account_number;
		}

		public function set_phone( $phone )
		{
			$this->phone = $phone;
		}

		public function get_phone()
		{
			return $this->phone;
		}

		public function set_email( $email )
		{
			$this->email = $email;
		}

		public function get_email()
		{
			return $this->email;
		}

		public function set_unit_leader( $unit_leader )
		{
			$this->unit_leader = $unit_leader;
		}

		public function get_unit_leader()
		{
			return $this->unit_leader;
		}

		public function set_comment( $comment )
		{
			$this->comment = $comment;
		}

		public function get_comment()
		{
			return $this->comment;
		}

		public function set_assign_date_start( $assign_date_start )
		{
			$this->assign_date_start = $assign_date_start;
		}

		public function get_assign_date_start()
		{
			return $this->assign_date_start;
		}
		public function set_assign_date_end( $assign_date_end )
		{
			$this->assign_date_end = $assign_date_end;
		}

		public function get_assign_date_end()
		{
			return $this->assign_date_end;
		}
		public function set_status( $status )
		{
			$this->status = $status;
		}

		public function get_status()
		{
			return $this->status;
		}
		public function set_type( $type )
		{
			$this->type = $type;
		}

		public function get_type()
		{
			return $this->type;
		}

		public function set_type_id( $type_id )
		{
			$this->type_id = $type_id;
		}

		public function get_type_id()
		{
			return $this->type_id;
		}

		public function set_party_id( $party_id )
		{
			$this->party_id = $party_id;
		}

		public function get_party_id()
		{
			return $this->party_id;
		}

		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'description' => $this->get_description(),
				'name' => $this->get_name(),
				'type' => lang($this->get_type())
			);
		}
	}
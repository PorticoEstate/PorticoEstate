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

		protected $ecodimb;
		protected $title;
		protected $description;
		protected $name;
		protected $type;
		protected $type_id;
		protected $contract_id;
		protected $party_id;

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

		public function set_title( $title )
		{
			$this->title = $title;
		}

		public function get_title()
		{
			return $this->title;
		}

		public function set_description( $description )
		{
			$this->description = $description;
		}

		public function get_description()
		{
			return $this->description;
		}

		public function set_name( $name )
		{
			$this->name = $name;
		}

		public function get_name()
		{
			return $this->name;
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

		public function set_contract_id( $contract_id )
		{
			$this->contract_id = $contract_id;
		}

		public function get_contract_id()
		{
			return $this->contract_id;
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
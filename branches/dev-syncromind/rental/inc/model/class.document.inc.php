<?php
	include_class('rental', 'model', 'inc/model/');

	class rental_document extends rental_model
	{

		protected $title;
		protected $description;
		protected $name;
		protected $type;
		protected $type_id;
		protected $contract_id;
		protected $party_id;

		public function __construct( int $id = null )
		{
			$doc_id = intval($id);
			parent::__construct($doc_id);
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
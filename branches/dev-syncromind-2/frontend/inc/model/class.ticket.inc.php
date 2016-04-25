<?php

	class frontend_ticket
	{

		private $id, $title, $date, $user, $messages, $location_description;

		public function get_location_description()
		{
			return $this->location_description;
		}

		public function set_location_description( $location_description )
		{
			$this->location_description = $location_description;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id( $id )
		{
			$this->id = $id;
		}

		public function get_title()
		{
			return $this->title;
		}

		public function set_title( $title )
		{
			$this->title = $title;
		}

		public function get_date()
		{
			return $this->date;
		}

		public function set_date( $date )
		{
			$this->date = $date;
		}

		public function get_user()
		{
			return $this->user;
		}

		public function set_user( $user )
		{
			$this->user = $user;
		}

		public function get_messages()
		{
			return $this->messages;
		}

		public function set_messages( $messages )
		{
			$this->messages = $messages;
		}

		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'user' => $this->get_user(),
				'date' => $this->get_date()
			);
		}
	}
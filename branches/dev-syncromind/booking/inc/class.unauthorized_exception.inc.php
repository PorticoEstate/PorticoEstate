<?php

	class booking_unauthorized_exception extends Exception
	{

		protected
			$operation;

		// 	$field;

		public function __construct( $operation, $message = null, $code = 0 )
		{
			parent::__construct($message, $code);
			$this->operation = $operation;
		}

		public function get_operation()
		{
			return $this->operation;
		}
	}
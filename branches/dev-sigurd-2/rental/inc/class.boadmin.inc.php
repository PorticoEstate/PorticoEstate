<?php
	phpgw::import_class('rental.bocommon');
	
	class rental_boadmin extends rental_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('rental.bocomposite');
		}
	}
?>
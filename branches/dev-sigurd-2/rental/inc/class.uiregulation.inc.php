<?php
phpgw::import_class('rental.uicommon');

class rental_uiregulation extends rental_uicommon {
	public $public_functions = array
	(
		'index'	=>	true
	);
	
	public function index()
	{
		$this->render('regulation.php');
	}
	
	public function query()
	{
		return "";
	}
}
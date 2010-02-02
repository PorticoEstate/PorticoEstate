<?php
phpgw::import_class('rental.uicommon');

class rental_uiregulation extends rental_uicommon {
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('rental::contracts::regulation');
	}
	
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
?>
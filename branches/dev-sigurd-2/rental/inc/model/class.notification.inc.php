<?php
phpgw::import_class('rental.bocommon');
include_class('rental', 'model', 'inc/model/');

class rental_notification extends rental_model
{
	public static $so;
	
	protected $id;
	protected $user_id;
	protected $contract_id;
	protected $date;
	protected $message;
	protected $dismissed;
	
	public function __construct(int $id = null, int $user_id = null, int $contract_id = null, string $date = null, string $message = null, $dismissed = false)
	{
		$this->id = (int)$id;
		$this->user_id = (int)$user_id;
		$this->contract_id = (int)$contract_id;
		$this->date = $date;
		$this->message = $message;
		$this->dismissed = (boolean)$dismissed;
	}
	
	public function get_id()
	{
		return $this->id;
	}
	
	public function set_id(int $id)
	{
		$this->id = (int)$id;
	}
	
	public function get_user_id()
	{
		return $this->user_id;
	}
	
	public function get_contract_id()
	{
		return $this->contract_id;
	}
	
	public function get_date()
	{
		return $this->date;
	}
	
	public function get_message()
	{
		return $this->message;
	}
	
	public function is_dismissed()
	{
		return $this->dismissed;
	}

	/**
	 * Convert this object to a hash representation
	 * 
	 * @see rental/inc/model/rental_model#serialize()
	 */
	public function serialize()
	{
		$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		return array(
			'id' => $this->get_id(),
			'user_id' => $this->get_user_id(),
			'contract_id' => $this->get_contract_id(),
			'message' => $this->get_message(),
			'date' => date($date_format, $this->get_date()),
			'dismissed' => $this->is_dismissed()
		);
	}
		
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_so()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.sonotification');
		}
		
		return self::$so;
	}
	
	public static function get_all($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$so = self::get_so();
		return $so->get_notification_array($start, $results, $sort, $dir, $query, $search_option, $filters);
	}
	
}
?>

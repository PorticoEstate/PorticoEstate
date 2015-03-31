<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class('booking.sodocument');

	class booking_bodocument_view extends booking_bocommon
	{
		
		public function __construct() {
			$this->so = CreateObject('booking.sodocument_view');
		}
		
		public function read_regulations() {
			$params = $this->build_default_read_params();
			!isset($params['filters']) AND $params['filters'] = array();
			$params['filters']['category'] = array(booking_sodocument::CATEGORY_REGULATION,
                booking_sodocument::CATEGORY_HMS_DOCUMENT,
                booking_sodocument::CATEGORY_PRICE_LIST);
			$where_filter = array();
			if ($owner_filters = phpgw::get_var('owner', 'string')) {
				foreach($owner_filters as $filter) {
					list($owner_type, $owner_id) = explode('::', $filter);
					$owner_type = $this->so->marshal_field_value('type', $owner_type);
					$owner_id = $this->so->marshal_field_value('owner_id', $owner_id);
					$where_filter[] = "(%%table%%.type=$owner_type AND %%table%%.owner_id = $owner_id)";
				}
			}
			$params['filters']['where'] = array('('.join($where_filter, ' OR ').')');
			return $this->so->read($params);
		}
	}
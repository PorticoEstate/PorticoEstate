<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soactivity extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_activity', 
				array(
					'id'	=>		array('type' => 'int'),
					'parent_id'	=>	array('type' => 'int', 'required' => false),
					'name'	=>		array('type' => 'string',	'query' => true, 'required' => true),
					'description'	=>	array('type' => 'string', 'query' => true)
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);

			if($entity['id'] && $entity['parent_id'] == $entity['id'])
			{
				$errors['parent_id'] = 'Invalid parent activity';
			}
			return $errors;
		}

	}

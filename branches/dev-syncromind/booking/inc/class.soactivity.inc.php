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
					'description'	=>	array('type' => 'string', 'query' => true),
					'active' => array('type' => 'int', 'required' => true)
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function validate($entity)
		{
			$errors = parent::validate($entity);
			# Detect and prevent loop creation
			$node_id = $entity['parent_id'];
			while($entity['id'] && $node_id)
			{
				if($node_id == $entity['id'])
				{
					$errors['parent_id'] = lang('Invalid parent activity');
					break;
				}
				$next = $this->read_single($node_id);
				$node_id = $next['parent_id'];
			}
			return $errors;
		}

	}

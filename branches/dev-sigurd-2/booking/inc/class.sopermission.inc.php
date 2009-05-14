<?php
	phpgw::import_class('booking.socommon');
	
	abstract class booking_sopermission extends booking_socommon
	{
		const ROLE_ADMIN = 'admin';
		const ROLE_CASE_OFFICER = 'case_officer';
		const ROLE_BASIC = 'basic';
		
		protected 
			$default_roles = array(
			   self::ROLE_ADMIN,
			   self::ROLE_CASE_OFFICER,
			   self::ROLE_BASIC,
			);
		
		protected $object_type = null;
		
		function __construct()
		{
			$this->object_type = substr(get_class($this), 21);
			
			parent::__construct(sprintf('bb_permission_%s', $this->get_object_type()), 
				array(
					'id'			=> array('type' => 'int'),
					'subject_id'	=> array('type' => 'int', 'required' => true),
					'object_id'		=> array('type' => 'int', 'required' => true),
					'role'			=> array('type' => 'string', 'required' => true, 'query' => true),
					'object_name'	=> array(
						'type' => 'string',
						'query' => true,
						'join' => array(
							'table' => sprintf('bb_%s', $this->get_object_type()),
							'fkey' => 'object_id',
							'key' => 'id',
							'column' => 'name'
						)
					),
					'subject_name'	=> array(
						'type' => 'string',
						'query' => true,
						'join' => array(
							'table' => 'phpgw_accounts',
							'fkey' => 'subject_id',
							'key' => 'account_id',
							'column' => 'account_lid'
						)
					)
				)
			);
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
		}
		
		public function get_roles()
		{
			return $this->default_roles;
		}
		
		public function get_object_type()
		{
			return $this->object_type;
		}
		
		public function read_object($object_id)
		{
			$object_so = CreateObject(sprintf('booking.so%s', $this->get_object_type()));
			return $object_so->read_single($object_id);
		}
	}

<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.sopermission');

	class booking_sopermission_root extends booking_socommon
	{

		protected
			$default_roles = array(
			booking_sopermission::ROLE_MANAGER,
			booking_sopermission::ROLE_CASE_OFFICER,
		);

		function __construct()
		{
			parent::__construct('bb_permission_root', array(
				'id' => array('type' => 'int'),
				'subject_id' => array('type' => 'int', 'required' => true),
				'role' => array('type' => 'string', 'required' => true, 'query' => true),
				'subject_name' => array(
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

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		public function get_roles()
		{
			return $this->default_roles;
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			if (!$this->validate_uniqueness($entity, 'subject_id', 'role'))
			{
				$errors['global'] = lang('Permission already exists');
			}
		}
	}
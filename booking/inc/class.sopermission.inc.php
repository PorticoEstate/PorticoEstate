<?php
	phpgw::import_class('booking.socommon');

	abstract class booking_sopermission extends booking_socommon
	{

		const ROLE_DEFAULT = 'default';
		const ROLE_ADMIN = 'admin';
		const ROLE_MANAGER = 'manager';
		const ROLE_CASE_OFFICER = 'case_officer';

		protected
			$default_roles = array(
			self::ROLE_MANAGER,
			self::ROLE_CASE_OFFICER,
		);
		protected $object_type = null;

		function __construct()
		{
			$this->object_type = substr(get_class($this), 21);

			$table_def = array(
				'id' => array('type' => 'int'),
				'subject_id' => array('type' => 'int', 'required' => true),
				'object_id' => array('type' => 'int', 'required' => true),
				'object_type' => array('type' => 'string'),
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
			);

			if (is_array($object_relations = $this->build_object_relations()))
			{
				$table_def = array_merge($table_def, $object_relations);
			}

			parent::__construct('bb_permission', $table_def);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		/**
		 * Builds relations to other tables for the storage object's table def.
		 * Override where necessary to customize relations.
		 *
		 * @return array
		 */
		protected function build_object_relations()
		{
			return array(
				'object_name' => array(
					'type' => 'string',
					'query' => true,
					'join' => array(
						'table' => sprintf('bb_%s', $this->get_object_type()),
						'fkey' => 'object_id',
						'key' => 'id',
						'column' => 'name'
					)
				)
			);
		}

		function get_user_list()
		{
			$object_type = $this->get_object_type();
			$sql	 = "SELECT DISTINCT account_id, account_lastname,  account_firstname FROM bb_permission "
				. "JOIN phpgw_accounts ON bb_permission.subject_id = phpgw_accounts.account_id WHERE object_type = '{$object_type}'";
			$this->db->query($sql);
			$users	 = array();
			while ($this->db->next_record())
			{
				$users[] = array(
					'id' => $this->db->f('account_id'),
					'name' => $this->db->f('account_lastname', true) . ', ' . $this->db->f('account_firstname', true),
				);
			}

			return $users;
		}

		function copy_permissions($from_id, $to_id)
		{
			$object_type = $this->get_object_type();

			$this->db->query("DELETE FROM bb_permission WHERE object_type = '{$object_type}' AND bb_permission.object_id = " . (int) $to_id);

			$sql = "SELECT * FROM bb_permission WHERE object_type = '{$object_type}' AND object_id = " . (int) $from_id;
			$this->db->query($sql, __LINE__, __FILE__);

			$valueset = array();
		 
			while($this->db->next_record())
			{
				$valueset[]	 = array(
					1	 => array
						(
						'value'	 => (int) $this->db->f('subject_id'),
						'type'	 => PDO::PARAM_INT
					),
					2	 => array
						(
						'value'	 => (int) $to_id,
						'type'	 => PDO::PARAM_INT
						),
					3	 => array
						(
						'value'	 =>$this->db->f('object_type'),
						'type'	 => PDO::PARAM_STR
					),
					4	 => array
						(
						'value'	 =>$this->db->f('role'),
						'type'	 => PDO::PARAM_STR
					),

				);
			}

			$sql_insert = 'INSERT INTO bb_permission (subject_id, object_id, object_type, role) VALUES (?, ?, ?, ?)';

			if ($valueset)
			{
				$this->db->insert($sql_insert, $valueset, __LINE__, __FILE__);
			}

		}

		function read( $params )
		{
			$params['filters']['object_type'] = $this->get_object_type();
			return parent::read($params);
		}

		function add( $entry )
		{
			$entry['object_type'] = $this->get_object_type();
			return parent::add($entry);
		}

		function update( $entry )
		{
			$entry['object_type'] = $this->get_object_type();
			return parent::update($entry);
		}

		public function get_roles()
		{
			return $this->default_roles;
		}

		public function get_object_type()
		{
			return $this->object_type;
		}

		public function read_object( $object_id )
		{
			$object_so = CreateObject(sprintf('booking.so%s', $this->get_object_type()));
			return $object_so->read_single($object_id);
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{
			if (!$this->validate_uniqueness($entity, 'subject_id', 'role', 'object_type', 'object_id'))
			{
				$errors['global'] = lang('Permission already exists');
			}
		}
	}
<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class('booking.sopermission');
	phpgw::import_class('booking.unauthorized_exception');
	phpgw::import_class('booking.account_helper');

	abstract class booking_bocommon_authorized extends booking_bocommon
	{

		protected
			$sopermission,
			$collection_roles,
			$subject_roles = array(),
			$subject_global_roles;
		protected $defaultObjectPermissions = array(
			booking_sopermission::ROLE_DEFAULT =>
			array(
				'read' => true,
			),
		);
		protected $defaultCollectionPermissions = array(
			booking_sopermission::ROLE_DEFAULT =>
			array(
				'read' => true,
			),
		);
		protected $allow_all_permissions;
		static protected $auth_enabled = true;

		function __construct()
		{
			parent::__construct();
			$this->sopermission = $this->create_permission_storage();
		}

		static public function disable_authorization()
		{
			self::$auth_enabled = false;
		}

		static public function enable_authorization()
		{
			self::$auth_enabled = true;
		}

		static public function authorization_enabled()
		{
			return self::$auth_enabled;
		}

		protected function create_permission_storage()
		{
			return CreateObject($this->get_permission_object_type());
		}

		public function get_permission_object_type()
		{
			$current_type = substr(get_class($this), 10);
			return sprintf('booking.sopermission_%s', $current_type);
		}

		public function get_columns()
		{
			return $this->so->get_columns();
		}

		protected function current_app()
		{
			return $GLOBALS['phpgw_info']['flags']['currentapp'];
		}

		protected function current_account_id()
		{
			return booking_account_helper::current_account_id();
		}

		protected function current_account_memberships()
		{
			return booking_account_helper::current_account_memberships();
		}

		protected function current_account_member_of_admins()
		{
			return booking_account_helper::current_account_member_of_admins();
		}

		/**
		 * Providing no id to this method results in that the permissions
		 * for the collection is retrieved. If the object_id is specified,
		 * then the roles for the specific object is retrieved
		 *
		 * @param $for_object mixed (optional)
		 *
		 * @return array with roles
		 */
		protected function get_subject_roles( $for_object = null, $initial_roles = array() )
		{
			$default_role = array('role' => booking_sopermission::ROLE_DEFAULT);

			if (is_null($for_object))
			{
				if (!$this->collection_roles)
				{
					$roles = $this->sopermission->read(array('filters' => array('subject_id' => $this->current_account_id())));
					$roles = $initial_roles + ($roles['total_records'] > 0 ? $roles['results'] : array());
					$roles[] = $default_role;
					$this->collection_roles = $roles;
				}

				if (is_array($parent_roles = $this->include_subject_parent_roles()))
				{
					$this->collection_roles['_parent_roles'] = $parent_roles;
				}

				return $this->collection_roles;
			}
			else
			{

				if (is_array($for_object))
				{
					$key = isset($for_object['id']) ? $for_object['id'] : -1;
				}
				else
				{
					//for_object is understood to be the id of the object if not null and not array
					$key = $for_object;
					$for_object = parent::read_single($key);
				}

				if (!is_array($for_object))
				{
					throw new LogicException('Unable to find object for permissions');
				}

				if (!isset($this->subject_roles[$key]))
				{
					if (isset($for_object['id']))
					{
						//An id = Edit or read existing object, a candidate for subject object roles.
						$roles = $this->sopermission->read(array('filters' => array('object_id' => $for_object['id'],
								'subject_id' => $this->current_account_id())));
						$roles = $initial_roles + ($roles['total_records'] > 0 ? $roles['results'] : array());
					}
					else
					{
						//No id = Create operation: no possible roles for this subject and object as
						//the object does not exist at this stage. But since there could be parent roles 
						//affecting authorization for this operation, we continue on without defining any
						//subject roles here.
						$roles = $initial_roles + array();
					}

					$roles[] = $default_role;

					$this->subject_roles[$key] = $roles;
				}

				//Parent roles must be retrieved every time since the object's parent may have changed
				//since the last time we read the object. No need to worry performancewise here since 
				//the parent(s) will also have cached their permissions (so long as you retrieve parent
				//permissions using this method too).
				if (is_array($parent_roles = $this->include_subject_parent_roles($for_object)))
				{
					$this->subject_roles[$key]['_parent_roles'] = $parent_roles;
				}
				elseif (isset($this->subject_roles[$key]['_parent_roles']))
				{
					unset($this->subject_roles[$key]['_parent_roles']);
				}

				return $this->subject_roles[$key];
			}
		}

		/**
		 * If $for_object is provided then return only the parent roles for that specific object.
		 * If $for_object == null then provide all distinct roles from all parent objects.
		 *
		 * @param array $for_object (optional)
		 */
		protected function include_subject_parent_roles( array $for_object = null )
		{

		}

		protected function get_subject_global_roles()
		{
			if (is_null($this->subject_global_roles))
			{
				$result = CreateObject('booking.sopermission_root')->read(array('filters' => array(
						'subject_id' => $this->current_account_id())));
				$this->subject_global_roles = $result['total_records'] > 0 ? $result['results'] : array();
			}

			return $this->subject_global_roles;
		}

		// public function get_role_allowing($operation, $for_object = null)
		// {
		// 	$role_permissions = $this->get_role_permissions($operation, $for_object);
		// 	return $this->find_role_allowing_recursive($operation, $role_permissions)
		// }
		// 
		// protected function find_role_allowing_recursive($operation, $role_permissions)
		// {
		// 	
		// }
		// 
		// protected function find_role_allowing($operation, $role_permissions)
		// {
		// 	foreach($role_permissions as $role)
		// 	{
		// 		if $role['role'];
		// 		
		// 	}
		// }

		public function get_role_permissions( $for_object = null )
		{
			if ($this->current_account_member_of_admins())
			{
				return array('admin' => $this->allow_all_permissions());
			}

			return is_null($for_object) ? $this->collection_role_permissions() : $this->object_role_permissions($for_object);
		}

		public function object_role_permissions( $forObject )
		{
			return $this->get_object_role_permissions($forObject, $this->defaultObjectPermissions);
		}

		public function collection_role_permissions()
		{
			return $this->get_collection_role_permissions($this->defaultCollectionPermissions);
		}

		public function allow_all_permissions( $for_operation = null )
		{
			if (!$this->allow_all_permissions)
			{
				$this->allow_all_permissions = array(
					'read' => true,
					'create' => true,
					'delete' => true,
					'write' => array_fill_keys($this->get_columns(), true),
				);
			}

			if (is_null($for_operation))
				return $this->allow_all_permissions;

			return isset($this->allow_all_permissions[$for_operation]) ? $this->allow_all_permissions[$for_operation] : false;
		}

		// public function auth_role_has_access($role, $operation, array $object)
		// {
		// 	if ($this->auth_enabled) {
		// 		$role_permissions = $this->object_role_permissions($object);
		// 		return isset($role_permissions[$role]) && isset($role_permissions[$role][$operation]) && $role_permissions[$role][$operation] === true;
		// 	}
		// 	
		// 	return true;
		// }

		protected abstract function get_object_role_permissions( $forObject, $defaultPermissions );

		protected abstract function get_collection_role_permissions( $defaultPermissions );

		protected function compute_operation_grant_for_role( &$role, $operation, &$permissions )
		{
			$role_name = $role['role'];
			if (isset($permissions[$role_name]) && isset($permissions[$role_name][$operation]) && false != ($grant = $permissions[$role_name][$operation]))
			{
				return ($operation == 'write' && $grant === true) ? $this->allow_all_permissions('write') : $grant;
			}

			return false;
		}

		function compute_operation_grant_for_role_collection( &$role_collection, $operation, &$permissions )
		{
			$grants = array();
			foreach ($role_collection as $object_roles)
			{
				foreach ($object_roles as $role)
				{
					if (false != ($grant = $this->compute_operation_grant_for_role($role, $operation, $permissions)))
					{
						$grants[] = $grant;
					}
					else
					{
						unset($grants);
						return false;
					}
				}
			}

			return $this->coalesce_grants($grants);
		}

		protected function coalesce_grants( array $grants )
		{
			$coalesced_grant = count($grants) > 0 ? array_shift($grants) : false;
			foreach ($grants as $grant)
			{
				if (is_array($grant))
				{
					if (is_array($coalesced_grant))
					{
						$coalesced_grant = array_merge($coalesced_grant, $grant);
						if (count($remove_keys = array_diff_key($coalesced_grant, $grant)) > 0)
						{
							foreach ($remove_keys as $key)
								unset($coalesced_grant[$key]);
						}
					}
					else
					{
						$coalesced_grant = $grant;
					}
				}
			}

			if (is_array($coalesced_grant) && count($coalesced_grant) == 0)
			{
				return false;
			}

			return $coalesced_grant;
		}

		protected function check_authorization( $roles, $permissions, $operation, $object = null, $options = array() )
		{
			$options = array_merge(
				array('namespace' => ''), $options
			);

			$ns = $options['namespace'];

			if (strlen(trim($ns)) > 0)
			{
				$permissions = isset($permissions[$ns]) ? $permissions[$ns] : array();
			}

			$all_permissions = $this->allow_all_permissions();

			foreach ($roles as $role)
			{
				if (false != ($grant = $this->compute_operation_grant_for_role($role, $operation, $permissions)))
				{
					return $grant;
				}
			}

			return false;
		}

		public function check_authorization_recursive( $roles, $permissions, $operation, $object = null, $options = array() )
		{
			$parent_roles = null;

			if (isset($roles['_parent_roles']))
			{
				$parent_roles = $roles['_parent_roles'];
				unset($roles['_parent_roles']);
			}

			if (false != $permission = $this->check_authorization($roles, $permissions, $operation, $object, $options))
			{
				return $permission;
			}

			if (is_array($parent_roles))
			{
				if (!isset($permissions['parent_role_permissions']) || !is_array($parent_role_permissions = $permissions['parent_role_permissions']))
				{
					throw new LogicException('Missing parent role permissions definition');
				}

				foreach ($parent_roles as $key => $roles)
				{
					if (!isset($parent_role_permissions[$key]))
					{
						throw new LogicException(sprintf('Missing parent role permissions for "%s"', $key));
					}

					$current_parent_role_permissions = $parent_role_permissions[$key];

					$type = isset($current_parent_role_permissions['type']) ? $current_parent_role_permissions['type'] : 'one';

					if ($type == 'one')
					{
						if (false != $permission = $this->check_authorization_recursive($roles, $current_parent_role_permissions, $operation, $object, $options))
						{
							return $permission;
						}
					}
					elseif ($type == 'many')
					{
						//$roles is a collection of roles for many related objects
						$permissions = array();
						foreach ($roles as $object_roles)
						{
							if (false != $permission = $this->check_authorization_recursive($object_roles, $current_parent_role_permissions, $operation, $object, $options))
							{
								$permissions[] = $permission;
							}
							else
							{
								unset($permissions);
								return false;
							}
						}

						return $this->coalesce_grants($permissions);
					}
					else
					{
						return false;
					}
				}
			}

			return false;
		}

		/**
		 * Providing no id to this method results in that authorization
		 * is performed for the collection. If the 'object' is provided,
		 * then authorization is performed for that object.
		 *
		 * @param $operation
		 * @param $object (optional)
		 *
		 * @return boolean true if authorized
		 * @throws booking_unauthorized_exception if not authorized
		 */
		protected function authorize( $operation, $object = null )
		{
			if ($this->current_account_member_of_admins() || !self::authorization_enabled())
			{
				$all_permissions = $this->allow_all_permissions();

				if (!isset($all_permissions[$operation]))
				{
					throw new LogicException('Unsupported operation');
				}

				return $all_permissions[$operation];
			}

			$role_permissions = $this->get_role_permissions($object);

			$object_id = null;

			if (!is_null($object))
			{
				$object_id = is_array($object) ? $object['id'] : $object;
				$object = is_array($object) ? $object : parent::read_single($object_id);
			}

			if (false != $permission = $this->check_authorization($this->get_subject_global_roles(), $role_permissions, $operation, $object, array(
				'namespace' => 'global')))
			{
				return $permission;
			}

			if (false != $permission = $this->check_authorization_recursive($this->get_subject_roles($object), $role_permissions, $operation, $object))
			{
				return $permission;
			}

			throw new booking_unauthorized_exception($operation, sprintf('Operation \'%s\' was denied on %s %s', $operation, get_class($this), is_null($object) ? 'collection' : 'object'));
		}

		protected function _compute_permissions( array $entity, $roles, $role_permissions, $initial_grants = array() )
		{
			$all_permissions = $this->allow_all_permissions();

			$grants = $initial_grants;

			$parent_roles = null;

			if (isset($roles['_parent_roles']))
			{
				$parent_roles = $roles['_parent_roles'];
				unset($roles['_parent_roles']);
			}

			foreach ($roles as $role)
			{
				$role_name = $role['role'];
				if (isset($role_permissions[$role_name]) && false != $current_role_permissions = $role_permissions[$role_name])
				{
					$current_role_permissions['write'] === true AND $current_role_permissions['write'] = $all_permissions['write'];
					$grants = array_merge($grants, $current_role_permissions);
				}
			}

			if (is_array($parent_roles))
			{
				if (!isset($role_permissions['parent_role_permissions']) || !is_array($parent_role_permissions = $role_permissions['parent_role_permissions']))
				{
					throw new LogicException('Missing parent role permissions definition');
				}

				foreach ($parent_roles as $key => $roles)
				{
					if (!isset($parent_role_permissions[$key]))
					{
						throw new LogicException(sprintf('Missing parent role permissions for "%s"', $key));
					}

					$current_parent_role_permissions = $parent_role_permissions[$key];

					$type = isset($current_parent_role_permissions['type']) ? $current_parent_role_permissions['type'] : 'one';

					if ($type == 'one')
					{
						$grants = array_merge($this->_compute_permissions($entity, $roles, $current_parent_role_permissions), $grants);
					}
					elseif ($type == 'many')
					{
						//$roles is a collection of roles for many related objects
						$collected_grants = array();
						foreach ($roles as $object_roles)
						{
							$collected_grants[] = $this->_compute_permissions($entity, $object_roles, $current_parent_role_permissions);
						}

						return $this->coalesce_grants($permissions);
					}
				}
			}

			return $grants;
		}

		public function get_permissions( array $entity )
		{
			if ($this->current_account_member_of_admins())
			{
				return $this->allow_all_permissions();
			}

			$grants = array();
			$object_role_permissions = $this->object_role_permissions($entity);

			$grants = $this->_compute_permissions($entity, $this->get_subject_roles($entity), $object_role_permissions, $grants);

			if (isset($object_role_permissions['global']))
			{
				$grants = $this->_compute_permissions($entity, $this->get_subject_global_roles(), $object_role_permissions, $grants);
			}

			return $grants;
		}

		public function add_permission_data( array $entity )
		{
			$entity['permission'] = $this->get_permissions($entity);
			return $entity;
		}

		public function is_authorized( $operation, $object = null )
		{
			try
			{
				$this->authorize($operation, $object);
				return true;
			}
			catch (booking_unauthorized_exception $e)
			{
				return false;
			}
		}

		public function authorize_read( $object = null )
		{
			$this->authorize('read', $object);
			return $object;
		}

		public function allow_read( $object = null )
		{
			try
			{
				$this->authorize_read($object);
				return true;
			}
			catch (booking_unauthorized_exception $e)
			{
				
			}

			return false;
		}

		public function authorize_create( $object = null )
		{
			$this->authorize('create', $object);
			return $object;
		}

		public function allow_create( $object = null )
		{
			try
			{
				$this->authorize_create($object);
				return true;
			}
			catch (booking_unauthorized_exception $e)
			{
				
			}

			return false;
		}

		/**
		 * @param mixed $object Either an array or the id of the entity to be deleted
		 */
		public function authorize_delete( $object = null )
		{
			if (is_null($object))
			{
				$this->authorize('delete');
				return;
			}

			$object_id = (is_array($object) && isset($object['id'])) ? $object['id'] : $object;

			if (!$object_id)
			{
				throw new InvalidArgumentException('Cannot authorize operation \'delete\' unless an object id is provided');
			}

			if (!is_array($object))
			{
				$object = parent::read_single($object_id);
			}

			$this->authorize('delete', $object);
			return $object;
		}

		/**
		 * @param mixed $object Either an array or the id of the entity to be deleted
		 */
		public function allow_delete( $object = null )
		{
			try
			{
				$this->authorize_delete($object);
				return true;
			}
			catch (booking_unauthorized_exception $e)
			{
				
			}

			return false;
		}

		/**
		 * @param mixed $object Either an array of the entity or the id of the entity to be written to
		 */
		public function authorize_write( $object = null )
		{
			$object_id = $object;

			if (is_array($object))
			{
				$object_id = isset($object['id']) ? $object['id'] : null;

				if (!$object_id)
				{
					throw new InvalidArgumentException('Cannot authorize operation \'write\' unless an object id is provided');
				}

				$persisted_object = parent::read_single($object_id);
				$allowed_fields = $this->authorize('write', $persisted_object);

				if (is_array($object))
				{
					$transient_object = $object;
					$this->authorize('write', $transient_object);
				}
				else
				{
					$transient_object = $persisted_object;
				}

				//$allowed_fields is an array that contains the names of the
				//fields that the role gave us permission to write to.
				if (is_array($transient_object))
				{
					$allowed_object = array();
					if (is_array($allowed_fields))
					{
						foreach ($this->get_columns() as $field)
						{
							if (isset($allowed_fields[$field]))
							{
								$allowed_object[$field] = $transient_object[$field];
							}
							elseif (isset($persisted_object[$field]))
							{
								$allowed_object[$field] = $persisted_object[$field];
							}
						}
					}

					return $allowed_object;
				}

				return $persisted_object; //No change allowed, so return the already persisted object
			}
			else
			{
				$this->authorize('write', $object);
			}
		}

		/**
		 * @param mixed $object Either an array or the id of the entity to be deleted
		 */
		public function allow_write( $object = null )
		{
			try
			{
				$this->authorize_write($object);
				return true;
			}
			catch (booking_unauthorized_exception $e)
			{
				
			}

			return false;
		}

		function add( $entity )
		{
			$allowed_entity = $this->authorize_create($entity);
			return parent::add($allowed_entity);
		}

		function update( $entity )
		{
			$allowed_entity = $this->authorize_write($entity);
			return parent::update($allowed_entity);
		}

		function delete( $id )
		{
			$this->authorize_delete($id);
			return parent::delete($id);
		}

		function set_active( $id, $active )
		{
			$object = $this->authorize_write(array('id' => $id, 'active' => $active));
			return parent::set_active($object['id'], $object['active']);
		}

		function read()
		{
			$this->authorize_read();
			return parent::read();
		}

		function read_single( $id )
		{
			$entity = parent::read_single($id);
			if($entity)
			{
				$this->authorize_read($entity);
				return $this->add_permission_data($entity);
			}
			else
			{
				return array();
			}
		}
	}
<?php
	phpgw::import_class('booking.socommon');

	class booking_sodelegate extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_delegate', array(
				'id' => array('type' => 'int'),
				'active' => array('type' => 'int', 'required' => true),
				'organization_id' => array('type' => 'int', 'required' => true),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'email' => array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array(
						'invalid' => '%field% is invalid'))),
				'ssn' => array('type' => 'string', 'required' => true),
				'phone' => array('type' => 'string', 'query' => true, 'required' => true),
				'organization_name' => array('type' => 'string',
					'query' => true,
					'join' => array(
						'table' => 'bb_organization',
						'fkey' => 'organization_id',
						'key' => 'id',
						'column' => 'name'
					)),
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_metainfo( $id )
		{
			$this->db->limit_query("SELECT bb_delegate.first_name || ' ' || last_name AS name"
				. " bb_organization.name AS organization, bb_organization.district,"
				. " bb_organization.city, bb_delegate.description"
				. " FROM bb_delegate, bb_organization AS bb_organization"
				. " WHERE bb_delegate.organization_id=bb_organization.id AND bb_delegate.id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', false),
				'organization' => $this->db->f('organization', false),
				'district' => $this->db->f('district', false),
				'city' => $this->db->f('city', false),
				'description' => $this->db->f('description', false));
		}

		protected function preValidate( &$entity )
		{
			$id = (int) $entity['id'];
			if (empty($entity['ssn']))
			{
				$this->db->query("SELECT ssn FROM bb_delegate WHERE id = {$id}", __LINE__, __FILE__);
				$this->db->next_record();
				$entity['ssn'] = $this->db->f('ssn');
			}
		}


		protected function doValidate( $entity, booking_errorstack $errors )
		{

			$ssn= $entity['ssn'];

			if ( !preg_match('/^{(.*)}(.*)$/', $ssn, $m) || count($m) != 3  ) //full string, algorhythm, hash
			{
				//raw ssn
				try
				{
					$sf_validator = createObject('booking.sfValidatorNorwegianSSN', array(), array(
					'invalid' => '%field% is invalid'));
					$sf_validator->setOption('required', false);
					$sf_validator->clean($ssn);
				}
				catch (sfValidatorError $e)
				{
					$errors['ssn'] = lang(strtr($e->getMessage(), array('%field%' => 'ssn')));
				}

				return;
			}
			$algo = $m[1];
			$hash = $m[2];

			if($algo != 'SHA1')
			{
				$errors['ssn'] = lang('the ssn hash is not correct');
			}
		}

	}
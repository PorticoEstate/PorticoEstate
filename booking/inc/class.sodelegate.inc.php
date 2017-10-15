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

				$hash = sha1($entity['ssn']);
				$ssn =  '{SHA1}' . base64_encode($hash);

				$cnt = 0;
				if(empty($entity['id']))
				{
					$sql = "SELECT count(id) AS cnt FROM bb_delegate WHERE ssn = '{$ssn}' AND organization_id = " . (int) $entity['organization_id'];
				}
				else
				{
					$id = (int) $entity['id'];
					$sql = "SELECT count(id) AS cnt FROM bb_delegate WHERE id != {$id} AND ssn = '{$ssn}' AND organization_id = " . (int) $entity['organization_id'];
				}
				$this->db->query($sql, __LINE__, __FILE__);
				$this->db->next_record();
				$cnt = $this->db->f('cnt');
				if($cnt > 0)
				{
					$errors['ssn'] = lang('duplicate ssn');
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
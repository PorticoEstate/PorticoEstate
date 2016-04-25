<?php
	/*
	 * This file will only work for the implementation of NLSH
	 */

	/**
	 * Intended for custom validation of tickets prior to commit.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	class ticket_NLSH_pre_commit_validate extends property_botts
	{

		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Do your magic
		 * @param integer $id
		 * @param array $data
		 * @param array $values_attribute
		 */
		function validate( $id = 0, &$data, $values_attribute = array() )
		{
			//		_debug_array($data);
			//		$data['assignedto'] = 11;
			//		return 'Validate';
		}
	}
	$ticket_NLSH_pre_commit_validate = new ticket_NLSH_pre_commit_validate();
	if ($_error = $ticket_NLSH_pre_commit_validate->validate($id, $data, $values_attribute))
	{
		return $receipt['error'][] = array('msg' => $_error);
	}

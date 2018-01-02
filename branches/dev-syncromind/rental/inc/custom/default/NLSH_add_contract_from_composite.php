<?php
	/*
	 * This file will only work for the implementation of NLSH
	 */

	/**
	 * Intended for custom configuration on contracts.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	class rental_NLSH_add_contract_from_composite
	{

		function __construct()
		{

		}

		/**
		 * Do your magic
		 * @param array $data
		 */
		function validate( &$data )
		{
			$contract = $data['contract'];
			$location_arr = explode('-', $data['location_code']);
			$loc1 = $location_arr[0];
			$loc2 = $location_arr[1];

			if ($loc1 > 8006 && $loc1 < 8100)
			{
				$responsibility_id = 8018;
			}
			else if($loc1 == 8534)
			{
				$responsibility_id = 4036;
			}
			else if ($loc1 > 8499 && $loc1 < 8600)
			{
				if ($loc2 == 18)
				{
					$responsibility_id = 4036;
				}
				else
				{
					$responsibility_id = 4034;
				}
			}
			else if ($loc1 > 8599 && $loc1 < 8700)
			{
				$responsibility_id = 3015;
			}

			$contract->set_responsibility_id($responsibility_id);
			return;
		}
	}
	$process = new rental_NLSH_add_contract_from_composite($data);
	if ($_error = $process->validate($data))
	{
		return $receipt['error'][] = array('msg' => $_error);
	}

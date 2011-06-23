<?php
	/**
	* phpGroupWare - sms: A SMS Gateway
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage sms
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	$status_code = array
	(
		1 => 'utført',
		2 => 'ikke_tilgang',
		3 => 'i_arbeid',
	);

	$param 		= explode(' ' , $command_param);

	if (ctype_digit($param[0]) && ctype_digit($param[1]))
	{
		$workorder_id 	= $param[0];
		$status 	= $status_code[$param[1]];

		$this->db->query("SELECT status FROM fm_workorder where id='{$workorder_id}'",__LINE__,__FILE__);
		if($this->db->next_record())
		{
			$this->db->query("UPDATE fm_workorder set status = '{$status}' WHERE id='{$workorder_id}'" ,__LINE__,__FILE__);
			$historylog	= CreateObject('property.historylog','workorder');
	// temporary - fix this
			$historylog->account = 6;
			$historylog->add('S',$workorder_id,$status . ': endret av: ' . $sms_sender);
			$command_output = 'success';
		}
	}

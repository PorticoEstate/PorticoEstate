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

	$param 		= explode(' ' , $command_param);
	$receipt ='';

	include(PHPGW_SERVER_ROOT . "/sms/bin/{$GLOBALS['phpgw_info']['user']['domain']}/config_" . strtoupper(basename($command_code)));
	if(isset($filter) && $filter)
	{
		$sql = "SELECT id from fm_s_agreement_detail $filter";
		$this->db->query($sql,__LINE__,__FILE__);

		if($this->db->next_record() && $agreement_id && $attrib_id)
		{
			$id = $this->db->f('id');
			$value = $receipt . $sms_datetime . ' (' . $sms_sender . ')';

			$this->db->query("UPDATE fm_s_agreement_detail set $target_field = '$value' WHERE agreement_id = $agreement_id AND  id=$id" ,__LINE__,__FILE__);
			$historylog	= CreateObject('property.historylog','s_agreement');
			$historylog->account = 6;
			$historylog->add('SO',$agreement_id ,$receipt . $sms_sender,False, $attrib_id,strtotime($sms_datetime),$id);
			$command_output = 'success';
			$this->account = 6;
			$this->websend2pv('Admin',$sms_sender,'Takk for det! - Melding er mottatt','text','0');
		}
	}



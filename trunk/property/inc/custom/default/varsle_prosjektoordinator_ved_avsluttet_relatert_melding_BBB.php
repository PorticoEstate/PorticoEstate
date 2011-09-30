<?php
	$db = & $GLOBALS['phpgw']->db;

	if(in_array('status', $this->fields_updated))
	{
		$_status = (int) trim($data['status'],'C');
		$db->query("SELECT * from fm_tts_status WHERE id = {$_status}",__LINE__,__FILE__);
		$this->db->next_record();
		if($db->f('closed'))
		{
			_debug_array($this->fields_updated);				
		}
	}
	


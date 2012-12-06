<?php
	if($ticket['cat_id'] == 4)
	{
		$db = $this->bocommon->new_db();

		if(!$id)
		{
			$id = $receipt['id'];
		}

		$db->query("SELECT * FROM fm_tts_tickets WHERE id='$id'",__LINE__,__FILE__);
		$db->next_record();
		if($db->f('finnish_date2'))
		{
			$finnish_date = $db->f('finnish_date2');
		}
		else
		{
			$finnish_date = $db->f('finnish_date');
		}

		$location_code = $db->f('location_code');

		if($finnish_date >0)
		{
			$finnish_date = date($db->date_format(),$finnish_date);
			$db->query("UPDATE fm_location4 set finnish_date = '$finnish_date' WHERE location_code='$location_code'",__LINE__,__FILE__);
		}
	}

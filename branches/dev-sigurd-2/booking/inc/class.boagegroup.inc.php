<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boagegroup extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soagegroup');
		}
		
		function fetch_age_groups()
		{
			return $this->so->read(array('filters'=>array('active'=>'1')));
		}

		// Extract agegroup info from _POST into $data
		public function extract_form_data($data)
		{
			foreach($_POST['male'] as $group_id => $num)
			{
				$found = false;
				foreach($data['agegroups'] as &$group)
				{
					if($group['agegroup_id'] == $group_id)
					{
						$group['male'] = $num;
						$found = true;
						break;
					}
				}
				if(!$found)
				{
					$data['agegroups'][] = array('agegroup_id' => $group_id, 'male' => $num, 'female' => 0);
				}
			}
			foreach($_POST['female'] as $group_id => $num)
			{
				$found = false;
				foreach($data['agegroups'] as &$group)
				{
					if($group['agegroup_id'] == $group_id)
					{
						$group['female'] = $num;
						$found = true;
						break;
					}
				}
				if(!$found)
				{
					$data['agegroups'][] = array('agegroup_id' => $group_id, 'female' => $num, 'male' => 0);
				}
			}
		}


	}

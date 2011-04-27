<?php
	phpgw::import_class('activitycalendar.soorganization');
	phpgw::import_class('activitycalendar.sogroup');
	phpgw::import_class('activitycalendar.soarena');
	phpgw::import_class('activitycalendar.socontactperson');
	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_activity extends activitycalendar_model
	{
		public static $so;
		
		protected $id;
		protected $organization_id;
		protected $group_id;
		protected $district;
		protected $category;
		protected $description;
		protected $arena;
		protected $date_start;
		protected $date_end;
		protected $create_date;
		protected $last_change_date;
		protected $contact_person_1;
		protected $contact_person_2;
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }
		
		public function set_organization_id($organization_id)
		{
			$this->organization_id = $organization_id;
		}
		
		public function get_organization_id() { return $this->organization_id; }

		public function set_group_id($group_id)
		{
			$this->group_id = $group_id;
		}
		
		public function get_group_id() { return $this->group_id; }
		
		public function set_district($district)
		{
			$this->district = $district;
		}
		
		public function get_district() { return $this->district; }
		
		public function set_category($category)
		{
			$this->category = $category;
		}
		
		public function get_category() { return $this->category; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_state($state)
		{
			$this->state = $state;
		}
		
		public function get_state() { return $this->state; }
		
		public function set_arena($arena)
		{
			$this->arena = $arena;
		}
		
		public function get_arena() { return $this->arena; }
		
		public function set_date_start($date_start)
		{
			$this->date_start = $date_start;
		}
		
		public function get_date_start() { return $this->date_start; }
		
		public function set_date_end($date_end)
		{
			$this->date_end = $date_end;
		}
		
		public function get_date_end() { return $this->date_end; }
		
		public function set_create_date($create_date)
		{
			$this->create_date = $create_date;
		}
		
		public function get_create_date() { return $this->create_date; }
		
		public function get_last_change_date() { return $this->last_change_date; }
		
		public function set_last_change_date($last_change_date)
		{
			$this->last_change_date = $last_change_date;
		}
		
		public function set_contact_person_1($contact_person_1)
		{
			$this->contact_person_1 = $contact_person_1;
		}
		
		public function get_contact_person_1() { return $this->contact_person_1; }
		
		public function set_contact_person_2($contact_person_2)
		{
			$this->contact_person_2 = $contact_person_2;
		}
		
		public function get_contact_person_2() { return $this->contact_person_2; }
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('activitycalendar.soactivity');
			}
			
			return self::$so;
		}
		
		public function serialize()
		{
			/*if(isset($this->organization_id) && $this->get_organization_id() > 0)
			{
				$contact_1 = activitycalendar_socontactperson::get_instance()->get_org_contact_name($this->get_contact_person_1());
				$contact_2 = activitycalendar_socontactperson::get_instance()->get_org_contact_name($this->get_contact_person_2());
			}
			else if(isset($this->group_id) && $this->get_group_id() > 0)
			{
				$contact_1 = activitycalendar_socontactperson::get_instance()->get_group_contact_name($this->get_contact_person_1());
				$contact_2 = activitycalendar_socontactperson::get_instance()->get_group_contact_name($this->get_contact_person_2());
			}
			else
			{*/
				$contact_1 = "";
				$contact_2 = "";
			//}
			return array(
				'id' => $this->get_id(),
				'organization_id' => activitycalendar_soorganization::get_instance()->get_organization_name($this->get_organization_id()),
				'group_id' => activitycalendar_sogroup::get_instance()->get_group_name($this->get_group_id()),
				'district' => $this->get_district(),
				'category' => $this->get_category(),
				'description' => $this->get_description(),
				'state' => $this->get_state(),
				'arena' => activitycalendar_soarena::get_instance()->get_arena_name($this->get_arena()),
				'date_start' => $this->get_date_start(),
				'date_end' => $this->get_date_end(),
				'contact_person_1' => $contact_1,
				'contact_person_2' => $contact_2
			);
		}
	}
?>
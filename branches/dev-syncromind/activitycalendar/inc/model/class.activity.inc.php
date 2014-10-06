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
		protected $title;
		protected $organization_id;
		protected $group_id;
		protected $district;
		protected $office;
		protected $category;
		protected $state;
		protected $target;
		protected $description;
		protected $arena;
		protected $time;
		protected $create_date;
		protected $last_change_date;
		protected $contact_person_1;
		protected $contact_person_2;
		protected $contact_person_2_address;
		protected $contact_person_2_zip;
		protected $special_adaptation;
		protected $secret;
		protected $internal_arena;
		protected $frontend;
		protected $new_org;
		protected $new_group;

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

		public function set_title($title)
		{
			$this->title = $title;
		}

		public function get_title() { return $this->title; }

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

		public function set_office($office)
		{
			$this->office = $office;
		}

		public function get_office() { return $this->office; }

		public function set_target($target)
		{
			$this->target = $target;
		}

		public function get_target() { return $this->target; }

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

		public function set_time($time)
		{
			$this->time = $time;
		}

		public function get_time() { return $this->time; }

/*		public function set_date_end($date_end)
		{
			$this->date_end = $date_end;
		}

		public function get_date_end() { return $this->date_end; }*/

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

		public function set_contact_persons($persons)
		{
			$count=0;
			foreach($persons as $person)
			{
				if($count == 0)
				{
					$this->set_contact_person_1($persons[0]);
				}
				else
				{
					$this->set_contact_person_2($persons[1]);
				}
				$count++;
			}
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

		public function set_contact_person_2_address($contact_person_2_address)
		{
			$this->contact_person_2_address = $contact_person_2_address;
		}

		public function get_contact_person_2_address() { return $this->contact_person_2_address; }

		public function set_contact_person_2_zip($contact_person_2_zip)
		{
			$this->contact_person_2_zip = $contact_person_2_zip;
		}

		public function get_contact_person_2_zip() { return $this->contact_person_2_zip; }

		public function set_special_adaptation($special_adaptation)
		{
			$this->special_adaptation = $special_adaptation;
		}

		public function get_special_adaptation() { return $this->special_adaptation; }

		public function set_secret($secret)
		{
			$this->secret = $secret;
		}

		public function get_secret() { return $this->secret; }

		public function set_internal_arena($internal_arena)
		{
			$this->internal_arena = $internal_arena;
		}

		public function get_internal_arena() { return $this->internal_arena; }

		public function set_frontend(bool $frontend)
		{
			$this->frontend = (bool)$frontend;
		}

		public function get_frontend() { return $this->frontend; }

		public function set_new_org(bool $new_org)
		{
			$this->new_org = (bool)$new_org;
		}

		public function get_new_org() { return $this->new_org; }

		public function set_new_group(bool $new_group)
		{
			$this->new_group = (bool)$new_group;
		}

		public function get_new_group() { return $this->new_group; }


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
		    $so_org = activitycalendar_soorganization::get_instance();
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(isset($this->group_id) && $this->get_group_id() > 0)
			{
				if($this->get_new_group())
				{
					$group_name = activitycalendar_sogroup::get_instance()->get_group_name_local($this->get_group_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_local_contact_persons($this->get_group_id(), true));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_group_contact_name_local($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
					    $contact_2 = activitycalendar_socontactperson::get_instance()->get_group_contact_name_local($this->get_contact_person_2()->get_id());
					}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_sogroup::get_instance()->get_description_local($this->get_group_id());
				}
				else
				{
					$group_name = activitycalendar_sogroup::get_instance()->get_group_name($this->get_group_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($this->get_group_id(), true));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_group_contact_name($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
					    $contact_2 = activitycalendar_socontactperson::get_instance()->get_group_contact_name($this->get_contact_person_2()->get_id());
					}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_sogroup::get_instance()->get_description($this->get_group_id());
				}
				$o_id = $this->get_organization_id();
				if($this->get_new_org())
				{
				    $org_name = $so_org->get_organization_name_local($o_id);
				}
				else
				{
				    $org_name = $so_org->get_organization_name($o_id);
				}

			}
			else if(isset($this->organization_id) && $this->get_organization_id() > 0)
			{
				if($this->get_new_org())
				{
					$org_name = activitycalendar_soorganization::get_instance()->get_organization_name_local($this->get_organization_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_local_contact_persons($this->get_organization_id()));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_org_contact_name_local($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
					    $contact_2 = activitycalendar_socontactperson::get_instance()->get_org_contact_name_local($this->get_contact_person_2()->get_id());
					}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_soorganization::get_instance()->get_description_local($this->get_organization_id());
				}
				else
				{
					$org_name = activitycalendar_soorganization::get_instance()->get_organization_name($this->get_organization_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($this->get_organization_id()));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_org_contact_name($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
    					$contact_2 = activitycalendar_socontactperson::get_instance()->get_org_contact_name($this->get_contact_person_2()->get_id());
	    			}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_soorganization::get_instance()->get_description($this->get_organization_id());
				}
			}
			else
			{
				$contact_1 = "";
				$contact_2 = "";
			}

			if($this->get_internal_arena() && $this->get_internal_arena() > 0)
			{
				$arena_name = activitycalendar_soarena::get_instance()->get_building_name($this->get_internal_arena());
			}
			else
			{
				$arena_name = activitycalendar_soarena::get_instance()->get_arena_name($this->get_arena());
			}

			$activity_district = $this->get_so()->get_district_name($this->get_district());

			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'organization_id' => $org_name,
				'group_id' => $group_name,
				'district' => $activity_district,
				'office' => activitycalendar_soactivity::get_instance()->get_office_name($this->get_office()),
				'category' => $this->get_so()->get_category_name($this->get_category()),
				'state' => lang('state_'.$this->get_state()),
				'description' => $desc,
				'arena' => $arena_name,
				'time' => $this->get_time(),
				'contact_person_1' => $contact_1,
				'contact_person_2' => $contact_2,
				'special_adaptation' => $this->get_special_adaptation(),
				'last_change_date' => $this->get_last_change_date()!=NULL?date($date_format, $this->get_last_change_date()):'',
				'frontend' => $this->get_frontend()
			);
		}

	    public function serialize_for_export()
		{
		    $so_org = activitycalendar_soorganization::get_instance();
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(isset($this->group_id) && $this->get_group_id() > 0)
			{
				if($this->get_new_group())
				{
					$group_name = activitycalendar_sogroup::get_instance()->get_group_name_local($this->get_group_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_local_contact_persons($this->get_group_id(), true));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_group_contact_name_local($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
					    $contact_2 = activitycalendar_socontactperson::get_instance()->get_group_contact_name_local($this->get_contact_person_2()->get_id());
					}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_sogroup::get_instance()->get_description_local($this->get_group_id());
				}
				else
				{
					$group_name = activitycalendar_sogroup::get_instance()->get_group_name($this->get_group_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($this->get_group_id(), true));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_group_contact_name($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
					    $contact_2 = activitycalendar_socontactperson::get_instance()->get_group_contact_name($this->get_contact_person_2()->get_id());
					}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_sogroup::get_instance()->get_description($this->get_group_id());
				}
				$o_id = $this->get_organization_id();
				if($this->get_new_org())
				{
				    $org_name = $so_org->get_organization_name_local($o_id);
						$org_homepage = $so_org->get_organization_homepage_local($o_id);
				}
				else
				{
				    $org_name = $so_org->get_organization_name($o_id);
						$org_homepage = $so_org->get_organization_homepage($o_id);
				}

			}
			else if(isset($this->organization_id) && $this->get_organization_id() > 0)
			{
				if($this->get_new_org())
				{
					$org_name = activitycalendar_soorganization::get_instance()->get_organization_name_local($this->get_organization_id());
					$org_homepage = $so_org->get_organization_homepage_local($this->get_organization_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_local_contact_persons($this->get_organization_id()));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_org_contact_name_local($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
					    $contact_2 = activitycalendar_socontactperson::get_instance()->get_org_contact_name_local($this->get_contact_person_2()->get_id());
					}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_soorganization::get_instance()->get_description_local($this->get_organization_id());
				}
				else
				{
					$org_name = activitycalendar_soorganization::get_instance()->get_organization_name($this->get_organization_id());
					$org_homepage = $so_org->get_organization_homepage($this->get_organization_id());
					$this->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($this->get_organization_id()));
					$contact_1 = activitycalendar_socontactperson::get_instance()->get_org_contact_name($this->get_contact_person_1()->get_id());
					if($this->get_contact_person_2())
					{
    					$contact_2 = activitycalendar_socontactperson::get_instance()->get_org_contact_name($this->get_contact_person_2()->get_id());
	    			}
					else
					{
					    $contact_2  = null;
					}
					$desc = activitycalendar_soorganization::get_instance()->get_description($this->get_organization_id());
				}
			}
			else
			{
				$contact_1 = "";
				$contact_2 = "";
			}

			if($this->get_internal_arena() && $this->get_internal_arena() > 0)
			{
				$arena_name = activitycalendar_soarena::get_instance()->get_building_name($this->get_internal_arena());
			}
			else
			{
				$arena_name = activitycalendar_soarena::get_instance()->get_arena_name($this->get_arena());
			}

			$activity_district = $this->get_so()->get_district_name($this->get_district());

			$contact_person_1_name = $this->get_contact_person_1()?$this->get_contact_person_1()->get_name():'';
            $contact_person_1_phone = $this->get_contact_person_1()?$this->get_contact_person_1()->get_phone():'';
            $contact_person_1_mail = $this->get_contact_person_1()?$this->get_contact_person_1()->get_email():'';
            $contact_person_2_name = $this->get_contact_person_2()?$this->get_contact_person_2()->get_name():'';
            $contact_person_2_phone = $this->get_contact_person_2()?$this->get_contact_person_2()->get_phone():'';
            $contact_person_2_mail = $this->get_contact_person_2()?$this->get_contact_person_2()->get_email():'';

			return array(
				'id' => $this->get_id(),
				'title' => $this->get_title(),
				'organization_id' => $org_name,
				'organization_homepage' => $org_homepage,
				'group_id' => $group_name,
				'district' => $activity_district,
				'office' => activitycalendar_soactivity::get_instance()->get_office_name($this->get_office()),
				'category' => $this->get_so()->get_category_name($this->get_category()),
				'state' => lang('state_'.$this->get_state()),
				'description' => $desc,
				'arena' => $arena_name,
				'time' => $this->get_time(),
				'contact_person_1_name' => $contact_person_1_name,
                'contact_person_1_phone' => $contact_person_1_phone,
                'contact_person_1_mail' => $contact_person_1_mail,
    			'contact_person_2_name' => $contact_person_2_name,
                'contact_person_2_phone' => $contact_person_2_phone,
                'contact_person_2_mail' => $contact_person_2_mail,
				'special_adaptation' => $this->get_special_adaptation(),
				'last_change_date' => $this->get_last_change_date()!=NULL?date($date_format, $this->get_last_change_date()):'',
				'frontend' => $this->get_frontend()
			);
		}
	}
?>
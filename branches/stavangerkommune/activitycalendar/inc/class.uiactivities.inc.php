<?php
phpgw::import_class('activitycalendar.uicommon');
phpgw::import_class('activitycalendar.soactivity');
phpgw::import_class('activitycalendar.soarena');
phpgw::import_class('activitycalendar.soorganization');
phpgw::import_class('activitycalendar.sogroup');

include_class('activitycalendar', 'activity', 'inc/model/');

class activitycalendar_uiactivities extends activitycalendar_uicommon
{
    protected $so_org;
    protected $so_group;
    protected $so_contact;
    protected $so_activity;
    protected $so_arena;

    public $public_functions = array
    (
        'index'                     => true,
        'index_json'                => true,
        'query'                     => true,
        'view'                      => true,
        'add'                       => true,
        'edit'                      => true,
        'download'                  => true,
        'send_mail'                 => true,
        'get_organization_groups'   => true,
        'create_groups'             => true,
				'remove_old_activities'			=> true
    );

    public function __construct()
    {
        parent::__construct();
        $this->bo_org = CreateObject('booking.boorganization');
        $this->bo_group = CreateObject('booking.bogroup');
        $this->so_org = activitycalendar_soorganization::get_instance();
        $this->so_group = activitycalendar_sogroup::get_instance();
        $this->so_contact = activitycalendar_socontactperson::get_instance();
        $this->so_activity = activitycalendar_soactivity::get_instance();
        $this->so_arena = activitycalendar_soarena::get_instance();
        self::set_active_menu('activitycalendar::activities');
        $config	= CreateObject('phpgwapi.config','activitycalendar');
        $config->read();
    }

    /**
     * Public method. Forwards the user to edit mode.
     */
    public function add()
    {
        $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.edit'));
    }

    public function index()
    {
        //$message = phpgw::get_var('message');
        $this->render('activity_list.php');

    }

    /**
     * Displays info about one single billing job.
     */
    public function view()
    {
        $errorMsgs = array();
        $infoMsgs = array();

        $activity = $this->so_activity->get_single((int)phpgw::get_var('id'));
        $cancel_link = self::link(array('menuaction' => 'activitycalendar.uiactivities.index'));
        $saved_OK = phpgw::get_var('saved_ok');
        if($saved_OK)
        {
                $message = lang('activity_saved_form');
        }

        if($activity == null) // Not found
        {
                $errorMsgs[] = lang('Could not find specified activity.');
        }

        if(isset($_POST['edit_activity'])) // The user has pressed the save button
        {
                $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.edit','id' => phpgw::get_var('id')));
        }

        $data = array
        (
            'activity' => $activity,
            'cancel_link' => $cancel_link,
            'message' => $message,
            'errorMsgs' => $errorMsgs,
            'infoMsgs' => $infoMsgs
        );
        $this->render('activity.php', $data);
    }

    public function edit()
    {
        $GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
        // Get the contract part id
        $activity_id = (int)phpgw::get_var('id');
        $cancel_link = self::link(array('menuaction' => 'activitycalendar.uiactivities.index'));
        $categories = $this->so_activity->get_categories();
        $targets = $this->so_activity->get_targets();
        $offices = $this->so_activity->select_district_list();
        $districts = $this->so_activity->get_districts();
        $buildings = $this->so_arena->get_buildings();
        // Retrieve the activity object or create a new one
        if(isset($activity_id) && $activity_id > 0)
        {
            $activity = $this->so_activity->get_single($activity_id);
        }
        else
        {
            $activity = new activitycalendar_activity();
        }
        $new_group = $activity->get_new_group();
        $g_id = phpgw::get_var('group_id');
        $o_id = phpgw::get_var('organization_id');
        if($new_group)
        {
            $persons = $this->so_group->get_contacts_local($activity->get_group_id());
            $desc = $this->so_group->get_description_local($activity->get_group_id());
        }
        else if(isset($g_id) && $g_id > 0)
        {
            $persons = $this->so_group->get_contacts($g_id);
            $desc = $this->so_group->get_description($g_id);
        }
        else if(isset($o_id) && $o_id > 0)
        {
            $persons = $this->so_org->get_contacts($o_id);
            $desc = $this->so_org->get_description($o_id);
        }

        if(strlen($desc) > 254)
        {
            $desc = substr($desc,0,254);
        }
        $arenas = $this->so_arena->get(null, null, 'arena.arena_name', true, null, null, null);
        if($activity->get_new_org())
        {
            $org_name = $this->so_org->get_organization_name_local($activity->get_organization_id());
        }
        else
        {
            $organizations = $this->so_org->get(null, null, 'org.name', true, null, null, null);
        }
        if($new_group)
        {
            $group_array = $this->so_group->get(null, null, null, null, null, null, array('group_id' => $activity->get_group_id(), 'new_groups' => 'true'));
            //var_dump($group_array);
        if(count($group_array) > 0){
            $keys = array_keys($group_array);
            $local_group = $group_array[$keys[0]];
            //$group_name = $local_group->get_name();
        }
        }
        else
        {
            $groups = $this->so_group->get(null, null, null, null, null, null, null);
        }

        if(isset($_POST['save_activity'])) // The user has pressed the save button
        {
            if(isset($activity)) // If an activity object is created
            {
                $old_state = $activity->get_state();
                $new_state = phpgw::get_var('state');
                // ... set all parameters
                $activity->set_title(phpgw::get_var('title'));
                $activity->set_organization_id(phpgw::get_var('organization_id'));
                $activity->set_group_id(phpgw::get_var('group_id'));
                $internal_arena = phpgw::get_var('internal_arena_id');
                if(isset($internal_arena) && $internal_arena > 0)
                {
                    $activity->set_arena(0);
                    $activity->set_internal_arena($internal_arena);
                }
                else
                {
                    $activity->set_arena(phpgw::get_var('arena_id'));
                    $activity->set_internal_arena(0);
                }
                $district_array = phpgw::get_var('district');
                $activity->set_district(implode(",", $district_array));
                $activity->set_office(phpgw::get_var('office'));
                $activity->set_state($new_state);
                $activity->set_category(phpgw::get_var('category'));
                $target_array = phpgw::get_var('target');
                $activity->set_target(implode(",", $target_array));
                $activity->set_description($desc);
                $activity->set_time(phpgw::get_var('time'));
                $activity->set_contact_persons($persons);
                $activity->set_contact_person_2_address(phpgw::get_var('contact_person_2_address'));
                $activity->set_contact_person_2_zip(phpgw::get_var('contact_person_2_zip'));
                $activity->set_special_adaptation(phpgw::get_var('special_adaptation'));

                $target_ok = false;
                $district_ok = false;
								if($new_state != 5)
								{
									$target_ok = true;
									$district_ok = true;
								}
								else
								{
									if($activity->get_target() && $activity->get_target() != '')
									{
											$target_ok = true;
									}
									if($activity->get_district() && $activity->get_district() != '')
									{
											$district_ok = true;
									}
								}

                if($target_ok && $district_ok)
                {
                    if($this->so_activity->store($activity)) // ... and then try to store the object
                    {
                        if($new_group)
                        {
                            //transfer group to booking
                            $group_array = $this->so_group->get(null, null, null, null, null, null, array('group_id' => $activity->get_group_id(), 'new_groups' => 'true'));
                            if(count($group_array) > 0){
                                $keys = array_keys($group_array);
                                $group = $group_array[$keys[0]];
                            }

                            $group_info = array();
                            $group_info['name'] = $group->get_name(); //new
                            $group_info['organization_id'] = $activity->get_organization_id();
                            $group_info['description'] = $group->get_description();

                            $contacts = $this->so_contact->get_local_contact_persons($group->get_id(), true);
                            $contact_1 = $contacts[0];

                            $new_group_id = $this->so_group->transfer_group($group_info);
                            if($new_group_id)
                            {
                                //update activity with new org id
                                //add contact persons to booking
                                $contact1 = array();
                                $contact1['name'] = $contact_1->get_name();
                                $contact1['phone'] = $contact_1->get_phone();
                                $contact1['mail'] = $contact_1->get_email();
                                $contact1['group_id'] = $new_group_id;
                                $this->so_activity->add_contact_person_group($contact1);

                                $message = lang('messages_saved_form');

                                //get organization_id for the group:
                                $group_org_id = $this->so_group->get_orgid_from_group($new_group_id);

                                //get affected activities and update with new org id
                                $update_activities = $this->so_activity->get_activities_for_update($group->get_id(), true);

                                foreach($update_activities as $act_id)
                                {
                                    $act = $this->so_activity->get_single($act_id);
                                    $act->set_organization_id($group_org_id);
                                    $act->set_group_id($new_group_id);
                                    $act->set_new_org(false);
                                    $act->set_new_group(false);
                                    $this->so_activity->store($act);
                                }

                                //set local group as stored
                                $group->set_change_type('added');
                                $group->set_transferred(true);

                                $this->so_group->update_local($group);
                                $message = lang('messages_saved_form');
                                //var_dump($new_group_id);
                                $contact_persons = $this->so_contact->get_booking_contact_persons($new_group_id, true);
                                //var_dump(2);
                                $cp1 = $contact_persons[0];
                            }
                        }
                        $message = lang('messages_saved_form');
                    }
                    else
                    {
                        $error = lang('messages_form_error');
                    }

										if(isset($activity_id) && $activity_id > 0)
										{
												$activity = $this->so_activity->get_single($activity_id);
										}

                    if($old_state != $new_state && ($new_state == 3 || $new_state == 5))
                    {
                        $kontor = $this->so_activity->get_office_name($activity->get_office());
                        $subject = lang('mail_subject_update');
                        $body = lang('mail_body_state_' . $new_state, $activity->get_title(), $kontor);

                        if($activity->get_group_id() && $activity->get_group_id() > 0)
                        {
                            $activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_group_id(), true));
                            activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_1(),$subject,$body);
                        }
                        else if ($activity->get_organization_id() && $activity->get_organization_id() > 0)
                        {
                            $activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_organization_id()));
                            activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_1(),$subject,$body);
                        }
                    }
                    $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.view', 'id' => $activity->get_id(), 'saved_ok' => 'yes'));
                }
                else
                {
                    if(!$target_ok)
                    {
                        $error .= "<br/>" . lang('target_not_selected');
                    }
                    if(!$district_ok)
                    {
                        $error .= "<br/>" . lang('district_not_selected');
                    }
                    return $this->render('activity.php', array
                            (
                                'activity' 	=> $activity,
                                'organizations' => $organizations,
                                'org_name' => $org_name,
                                'groups' => $groups,
                                'local_group' => $local_group,
                                'arenas' => $arenas,
                                'buildings' => $buildings,
                                'categories' => $categories,
                                'targets' => $targets,
                                'districts' => $districts,
                                'offices' => $offices,
                                'editable' => true,
                                'cancel_link' => $cancel_link,
                                'message' => isset($message) ? $message : phpgw::get_var('message'),
                                'error' => isset($error) ? $error : phpgw::get_var('error')
                            )
                    );
                }
            }
        }
        $editable = true;
        if($activity->get_new_org())
        {
            $error = lang('org_not_transferred');
            $editable = false;
        }
        //var_dump($local_group);

        return $this->render('activity.php', array
                (
                    'activity' 	=> $activity,
                    'organizations' => $organizations,
                    'org_name' => $org_name,
                    'groups' => $groups,
                    'local_group' => $local_group,
                    'arenas' => $arenas,
                    'buildings' => $buildings,
                    'categories' => $categories,
                    'targets' => $targets,
                    'districts' => $districts,
                    'offices' => $offices,
                    'editable' => $editable,
                    'cancel_link' => $cancel_link,
                    'message' => isset($message) ? $message : phpgw::get_var('message'),
                    'error' => isset($error) ? $error : phpgw::get_var('error')
                )
        );
    }

    public function query()
    {
        if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
        {
            $user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
        }
        else {
            $user_rows_per_page = 10;
        }
        // YUI variables for paging and sorting
        $start_index	= phpgw::get_var('startIndex', 'int');
        $num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
        $sort_field		= phpgw::get_var('sort');
        $sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
        // Form variables
        $search_for 	= phpgw::get_var('query');
        $search_type	= phpgw::get_var('search_option');
        // Create an empty result set
        $result_objects = array();
        $result_count = 0;
        //Retrieve the type of query and perform type specific logic
        $query_type = phpgw::get_var('type');

        $exp_param 	= phpgw::get_var('export');
        $export = false;
        if(isset($exp_param)){
            $export=true;
            $num_of_objects = null;
        }

        $email_param = phpgw::get_var('email');
        $email = false;
        if(isset($email_param)){
            $email=true;
        }

        $uid = $GLOBALS['phpgw_info']['user']['account_id'];

        switch($query_type)
        {
            case 'new_activities':
                $filters = array('new_activities' => 'yes', 'activity_state' => phpgw::get_var('activity_state'), 'activity_category' => phpgw::get_var('activity_category'), 'activity_district' => phpgw::get_var('activity_district'), 'user_id' => $uid, 'updated_date_hidden' => phpgw::get_var('date_change_hidden'));
                $result_objects = activitycalendar_soactivity::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
                $object_count = activitycalendar_soactivity::get_instance()->get_count($search_for, $search_type, $filters);
                break;
            case 'all_activities':
            default:
                $filters = array('activity_state' => phpgw::get_var('activity_state'), 'activity_category' => phpgw::get_var('activity_category'), 'activity_district' => phpgw::get_var('activity_district'), 'user_id' => $uid, 'updated_date_hidden' => phpgw::get_var('date_change_hidden'));
                $result_objects = activitycalendar_soactivity::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
                $object_count = activitycalendar_soactivity::get_instance()->get_count($search_for, $search_type, $filters);
                break;
        }

        //Create an empty row set
        $rows = array();
        $mail_rows = array();
        foreach($result_objects as $result) {
//          var_dump($result);
            if(isset($result))
            {
                            // ... add a serialized result
                if($export)
                {
                    $rows[] = $result->serialize_for_export();
                }
                else
                {
                    $rows[] = $result->serialize();
                }
                $mail_rows[] = $result;
            }
        }

        // ... add result data
        $result_data = array('results' => $rows, 'total_records' => $object_count);

        if(!$export && !$email){
            //Add action column to each row in result table
            array_walk($result_data['results'], array($this, 'add_actions'), array($query_type));
        }
        if($email)
        {
            //var_dump($mail_rows);
            $this->send_email_to_selection($mail_rows);
        }
        else
        {
            return $this->yui_results($result_data, 'total_records', 'results');
        }
    }

    /**
     * Add action links and labels for the context menu of the list items
     *
     * @param $value pointer to
     * @param $key ?
     * @param $params [composite_id, type of query, editable]
     */
    public function add_actions(&$value, $key, $params)
    {
        //Defining new columns
        $value['ajax'] = array();
        $value['actions'] = array();
        $value['labels'] = array();

        $query_type = $params[0];

        switch($query_type)
        {
            case 'all_activities':
                $value['ajax'][] = false;
                $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.edit', 'id' => $value['id'])));
                $value['labels'][] = lang('edit');
                $value['ajax'][] = false;
                $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.view', 'id' => $value['id'])));
                $value['labels'][] = lang('show');
                $value['ajax'][] = true;
                $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.send_mail', 'activity_id' => $value['id'],'message_type' => 'update')));
                $value['labels'][] = lang('send_mail');
                break;

            case 'new_activities':
                $value['ajax'][] = false;
                $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.edit', 'id' => $value['id'])));
                $value['labels'][] = lang('edit');
                $value['ajax'][] = false;
                $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.view', 'id' => $value['id'])));
                $value['labels'][] = lang('show');
                $value['ajax'][] = true;
                $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.send_mail', 'activity_id' => $value['id'],'message_type' => 'update')));
                $value['labels'][] = lang('send_mail');
                break;
        }
    }

    function send_email_to_selection($activities)
    {
        $c = createobject('phpgwapi.config','activitycalendarfrontend');
        $c->read();
        $config = $c->config_data;

        $mailBaseURL = $c->config_data['mailBaseURL'];
    	foreach($activities as $activity)
    	{
            //$activity = activitycalendar_soactivity::get_instance()->get_single($activity_id);
            $subject = lang('mail_subject_update');
            //$link_text = "<a href='http://www.bergen.kommune.no/aktivby/registreringsskjema/ny/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
            //$link_text = "<a href='{$mailBaseURL}?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
            $link_text = "<a href='http://www.bergen.kommune.no/aktivitetsoversikt/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
            $office_name = activitycalendar_soactivity::get_instance()->get_office_name($activity->get_office());
            /*
            $uid = $GLOBALS['phpgw_info']['user']['account_id'];
            $user_office_id =  activitycalendar_soactivity::get_instance()->get_office_from_user($uid);
            $office_footer = activitycalendar_soactivity::get_instance()->get_office_description($user_office_id);
             */
            $office_id = $activity->get_office();
            if($office_id == 1)
                $office_id_new = 2;
            else if($office_id == 2)
                $office_id_new = 1;
            else
                $office_id_new = (int)$office_id;
            $office_footer = activitycalendar_soactivity::get_instance()->get_office_description($office_id_new);
            if($activity->get_state() == 2)
            {
                $body = lang('mail_body_update_frontend', $activity->get_title(), $link_text, $office_footer, $office_name);
            }
            else
            {
                $body = lang('mail_body_update', $activity->get_title(), $link_text, $office_footer, $office_name);
            }

            //var_dump($subject);
            //var_dump($body);
            //var_dump($activity->get_organization_id() . " ; " . $activity->get_group_id());

            if($activity->get_group_id() && $activity->get_group_id() > 0)
            {
                $activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_group_id(), true));
/*	    		if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
                        activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_2(), $subject, $body);
                else*/
                if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
                    activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_1(), $subject, $body);
            }
            else if($activity->get_organization_id() && $activity->get_organization_id() > 0)
            {
                $activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_organization_id()));
/*	    		if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
                        activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_2(), $subject, $body);
                else*/
                if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
                    activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_1(), $subject, $body);
            }
        }

    	//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.index', 'message' => 'E-post sendt'));

    }

    public function send_mail()
    {
        $c = createobject('phpgwapi.config','activitycalendarfrontend');
        $c->read();
        $config = $c->config_data;

        $mailBaseURL = $c->config_data['mailBaseURL'];
    	$activity_id = (int)phpgw::get_var('activity_id');
    	$activity = activitycalendar_soactivity::get_instance()->get_single($activity_id);

    	$message_type = phpgw::get_var('message_type');
    	if($message_type)
    	{
            //$subject = lang('mail_subject_update', $avtivity->get_id() . '-' . $activity->get_title(), $activity->get_link());
            $subject = lang('mail_subject_update');
            //$link_text = "http://www.bergen.kommune.no/aktivby/registreringsskjema/ny/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}";
            //$link_text = "{$mailBaseURL}?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}";
            //$link_text = "http://www.bergen.kommune.no/aktivitetsoversikt/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}";
            $link_text = "<a href='http://www.bergen.kommune.no/aktivitetsoversikt/?menuaction=activitycalendarfrontend.uiactivity.edit&amp;id={$activity->get_id()}&amp;secret={$activity->get_secret()}'>Rediger opplysninger for {$activity->get_title()}</a>";
            $office_name = activitycalendar_soactivity::get_instance()->get_office_name($activity->get_office());
            $office_id = $activity->get_office();
            if($office_id == 1)
                $office_id_new = 2;
            else if($office_id == 2)
                $office_id_new = 1;
            else
                $office_id_new = (int)$office_id;
            $office_footer = activitycalendar_soactivity::get_instance()->get_office_description($office_id_new);
            $body = lang('mail_body_update', $activity->get_title(), $link_text, $office_footer, $office_name);
    	}
    	else
    	{
            $subject = "dette er en test";
            $body = "testmelding fra Aktivitetsoversikt";
    	}

//    	var_dump($subject);
//    	var_dump($body);
//    	var_dump($activity->get_organization_id() . " ; " . $activity->get_group_id());

    	if($activity->get_group_id() && $activity->get_group_id() > 0)
    	{
            //$contact_person2 = activitycalendar_socontactperson::get_instance()->get_group_contact2($activity>get_group_id());
            $activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_group_id(), true));
/*    		if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
                    activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_2(), $subject, $body);
            else */
            if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
                activitycalendar_uiactivities::send_mailnotification_to_group($activity->get_contact_person_1(), $subject, $body);
    	}
    	else if($activity->get_organization_id() && $activity->get_organization_id() > 0)
    	{
            //$contact_person2 = activitycalendar_socontactperson::get_instance()->get_oup_contact2($activity>get_group_id());
            $activity->set_contact_persons(activitycalendar_socontactperson::get_instance()->get_booking_contact_persons($activity->get_organization_id()));
/*    		if($activity->get_contact_person_2() && $activity->get_contact_person_2()->get_email())
                            activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_2(), $subject, $body);
            else */
            if($activity->get_contact_person_1() && $activity->get_contact_person_1()->get_email())
                activitycalendar_uiactivities::send_mailnotification_to_organization($activity->get_contact_person_1(), $subject, $body);
    	}

    	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.index', 'message' => 'E-post sendt'));

    }

    function send_mailnotification_to_organization($contact_person, $subject, $body)
    {

        //var_dump($contact_person_id . ',' . $subject . ',' . $body);
        if (!is_object($GLOBALS['phpgw']->send))
        {
            $GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
        }

        $config	= CreateObject('phpgwapi.config','booking');
        $config->read();
        $from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
        //$from = "erik.holm-larsen@bouvet.no";

        if (strlen(trim($body)) == 0)
        {
            return false;
        }

        $mailtoAddress = activitycalendar_socontactperson::get_instance()->get_mailaddress_for_org_contact($contact_person->get_id());
        //$mailtoAddress = "erik.holm-larsen@bouvet.no";

        //var_dump($mailtoAddress);
        //var_dump($mailtoAddress.';'.$from.';'.$subject);
        if (strlen($mailtoAddress) > 0)
        {
            try
            {
                //var_dump('inne i try');
//				var_dump('inne i try - org;');
                $GLOBALS['phpgw']->send->msg('email', $mailtoAddress, $subject, $body, '', '', '', $from, '', 'html');
            }
            catch (phpmailerException $e)
            {
                if($e)
                {
                    $GLOBALS['phpgw']->log->error(array(
                            'text'	=> 'uiactivities::send_mailnotification_to_organization() : error when trying to execute %1. Error: %2',
                            'p1'	=> 'send()',
                            'p2'	=> $e->getMessage(),
                            'line'	=> __LINE__,
                            'file'	=> __FILE__
                    ));
                }
            }
        }
    }

    function send_mailnotification_to_group($contact_person, $subject, $body)
    {
        if (!is_object($GLOBALS['phpgw']->send))
        {
            $GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
        }

        $config	= CreateObject('phpgwapi.config','booking');
        $config->read();
        $from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
        //$from = "tester@bouvet.no";

        if (strlen(trim($body)) == 0)
        {
            return false;
        }

        $mailtoAddress = activitycalendar_socontactperson::get_instance()->get_mailaddress_for_group_contact($contact_person->get_id());
        //$mailtoaddress = "erik.holm-larsen@bouvet.no";
        //var_dump($mailtoAddress.';'.$from.';'.$subject);
        if (strlen($mailtoAddress) > 0)
        {
            try
            {
//		var_dump('inne i try - group;');
//		$send->msg('email', $mailtoAddress, $subject, $body, '', '', '', $from, '', 'html');
                $GLOBALS['phpgw']->send->msg('email', $mailtoAddress, $subject, $body, '', '', '', $from, '', 'html');
            }
            catch (phpmailerException $e)
            {
                if($e)
                {
                    $GLOBALS['phpgw']->log->error(array(
                            'text'	=> 'uiactivities::send_mailnotification_to_group() : error when trying to execute %1. Error: %2',
                            'p1'	=> 'send()',
                            'p2'	=> $e->getMessage(),
                            'line'	=> __LINE__,
                            'file'	=> __FILE__
                    ));
                }
            }
        }
    }

    public function get_organization_groups()
    {
        $GLOBALS['phpgw_info']['flags']['noheader'] = true;
        $GLOBALS['phpgw_info']['flags']['nofooter'] = true;
        $GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

        $org_id = phpgw::get_var('orgid');
        $group_id = phpgw::get_var('groupid');
        $returnHTML = "<option value='0'>Ingen gruppe valgt</option>";
        if($org_id)
        {
            $groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, array('org_id' => $org_id));
            foreach ($groups as $group)
            {
                if(isset($group))
                {
                    //$res_g = $group->serialize();
                    $selected = "";
                    if($group_id && $group_id > 0)
                    {
                        $gr_id = (int)$group_id;
                        if($gr_id == (int)$group->get_id())
                        {
                            $selected_group = " selected='selected'";
                        }
                        else
                        {
                            $selected_group = "";
                        }
                    }
                    $group_html[] = "<option value='" . $group->get_id() . "'". $selected_group . ">" . $group->get_name() . "</option>";
                }
            }
            $html = implode(' ' , $group_html);
            $returnHTML = $returnHTML . ' ' . $html;
        }


        return $returnHTML;
    }

    public function create_groups()
    {
        $activities = $this->so_activity->get_activities_without_groups();

        foreach ($activities as $a)
        {
            $group_info = array();
            $title_new = $a['title'];
            if(strlen($title_new) > 50)
            {
                $title_new = substr($title_new,0,49);
            }
            $group_info['name'] = $title_new;
            $group_info['organization_id'] =  $a['organization'];
            $group_info['description'] = $a['description'];

            //add new group
            $new_group_id = $this->so_group->add_new_group_from_activity($group_info);
            var_dump("lagt til gruppen " . $group_info['name'] . " med id " . $new_group_id . "<br/>");
            $this->so_activity->update_activity_group($a['id'], $new_group_id);
            $cp = $this->so_contact->get_booking_contact_persons($a['organization']);
            foreach ($cp as $c)
            {
                $c->set_group_id($new_group_id);
                $contact_id = $this->so_contact->add_new_group_contact($c);
                var_dump("Lagt til kontaktperson " . $c->get_name() . " på gruppe " . $group_info['name'] . "<br/>");
                //_debug_array($c);
            }
        }
    }

		public function remove_old_activities()
		{
			$activity_id = phpgw::get_var('act_id');
			if($activity_id && $activity_id > 0)
			{
				$activity_del = $this->so_activity->get_single($activity_id);
				$this->so_activity->remove_old_activities($activity_id);

				echo "<h2>Aktiviteten '" . $activity_del->get_title() . "' ble slettet fra systemet.</h2>";
				echo "<a href='#' onclick='history.go(-1);'>Tilbake til forrige side</a>";

			}
			else
			{
				echo "<h2>Du må fylle ut en aktivitets-id!</h2>";
				echo "<a href='#' onclick='history.go(-1);'>Tilbake til forrige side</a>";
			}
		}
}
<?php
phpgw::import_class('booking.uievent');
phpgw::import_class('booking.uiapplication');

class bookingfrontend_uievent extends booking_uievent
{

    public $public_functions = array
    (
        'info' => true,
        'info_json' => true,
        'report_numbers' => true,
        'cancel' => true,
        'edit' => true,
        'show' => true
    );

    protected $application_ui, $customer_name;
    var $booking_bo, $resource_bo, $building_bo, $assoc_bo;

    public function __construct()
    {
        parent::__construct();
        $this->resource_bo = CreateObject('booking.boresource');
        $this->building_bo = CreateObject('booking.bobuilding');
        $this->organization_bo = CreateObject('booking.boorganization');
        $this->booking_bo = CreateObject('booking.bobooking');
        $this->application_ui = new booking_uiapplication();
    }

    private function _is_event_owner($event, $bouser, $skip_redirect = false)
    {
        $external_login_info = $bouser->validate_ssn_login(array('menuaction' => 'bookingfrontend.uievent.cancel',
            'id' => $event['id'],
            'resource_ids' => $event['resource_ids']), $skip_redirect);


        if (!empty($event['application_id'])) {
            $check_for_owner = $this->application_ui->bo->read_single($event['application_id']);
        } else {
            $check_for_owner = $event;
        }

        $event_owner_person = !empty($external_login_info['ssn']) && $check_for_owner['customer_ssn'] == $external_login_info['ssn'] ? true : false;
        $event_owner_organization = $bouser->is_organization_admin($check_for_owner['customer_organization_id']);

        if (!empty($external_login_info['ssn'])) {
            $user_id = CreateObject('booking.souser')->get_user_id($external_login_info['ssn']);
            if ($user_id) {
                $customer = CreateObject('booking.bouser')->read_single($user_id);
                $this->customer_name = $customer['name'];
            }
        }

        return ($event_owner_person || $event_owner_organization);
    }

    public function edit()
    {
        $id = phpgw::get_var('id', 'int');
        if (!$id) {
            phpgw::no_access('booking', lang('missing id'));
        }
        $from_org = phpgw::get_var('from_org', 'boolean', 'REQUEST', false);
        $event = $this->bo->read_single($id);
        $building_info = $this->bo->so->get_building_info($id);
        $event['building_id'] = $building_info['id'];
        $event['building_name'] = $building_info['name'];
        $bouser = CreateObject('bookingfrontend.bouser');
        $config = CreateObject('phpgwapi.config', 'booking');
        $config->read();
        $errors = array();
        $customer = array();
        array_set_default($event, 'resource_ids', phpgw::get_var('resource_ids'));

        if ($event['customer_identifier_type']) {
            $customer['customer_identifier_type'] = $event['customer_identifier_type'];
            $customer['customer_ssn'] = $event['customer_ssn'];
            $customer['customer_organization_number'] = $event['customer_organization_number'];
            $customer['customer_internal'] = $event['customer_internal'];
            $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
            $customer['customer_organization_id'] = $orginfo['id'];
            $customer['customer_organization_name'] = $orginfo['name'];
        } else {
            $customer['customer_organization_name'] = $event['customer_organization_name'];
            $customer['customer_organization_id'] = $event['customer_organization_id'];
            $organization = $this->organization_bo->read_single($event['customer_organization_id']);
            $customer['customer_identifier_type'] = 'organization_number';
            $customer['customer_ssn'] = $organization['customer_internal'];
            $customer['customer_organization_number'] = $organization['organization_number'];
            $customer['customer_internal'] = $organization['customer_internal'];
        }
        if ($config->config_data['split_pool'] == 'yes') {
            $split = 1;
        } else {
            $split = 0;
        }
        $resources = $event['resources'];
        $activity = $this->organization_bo->so->get_resource_activity($resources);
        $mailadresses = $this->building_users($event['building_id'], $split, $activity);

        if (!$this->_is_event_owner($event, $bouser)) {
            $date = substr($event['from_'], 0, 10);
            self::redirect(array('menuaction' => 'bookingfrontend.uibuilding.show',
                'id' => $event['building_id'], 'date' => $date));
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $currres = $event['resources'];

            $_POST['org_from'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['org_from']));
            $_POST['org_to'] = date("Y-m-d H:i:s", phpgwapi_datetime::date_to_timestamp($_POST['org_to']));

            $new_date['from_'] = substr($_POST['org_from'], 0, 11) . $_POST['from_'] . ":00";
            $new_date['to_'] = substr($_POST['org_to'], 0, 11) . $_POST['to_'] . ":00";

            array_set_default($_POST, 'resources', array());


            $free_up_time = false;
            $orgdate = array();
            foreach ($event['dates'] as $odate) {
                if (substr($odate['from_'], 0, 10) == substr($event['from_'], 0, 10)) {
                    $orgdate['from'] = $odate['from_'];
                    $orgdate['to'] = $odate['to_'];
                }
            }

            $test = $this->bo->read_single($event['id']);

            $no_time_changes = false;

            if ($test['from_'] <= $new_date['from_'] && $test['to_'] >= $new_date['to_']) {
                if ($new_date['from_'] > $new_date['to_']) {
                    $errors['start_time'] = lang('Cannot change start time');
                } else {
                    if ($test['from_'] == $new_date['from_'] && $test['to_'] == $new_date['to_']) {
                        $no_time_changes = true;
                    }

                    $event['from_'] = $new_date['from_'];
                    $event['to_'] = $new_date['to_'];
                    $_POST['from_'] = $new_date['from_'];
                    $_POST['to_'] = $new_date['to_'];
                }
            } else {
                if ($test['from_'] <= $new_date['from_']) {
                    if ($new_date['from_'] > $new_date['to_']) {
                        $errors['start_time'] = lang('Cannot change start time');
                    } else {
                        $event['from_'] = $new_date['from_'];
                        $_POST['from_'] = $new_date['from_'];
                    }
                } else {
                    $event['from_'] = $new_date['from_'];
                    $_POST['from_'] = $new_date['from_'];
                }

                if ($test['to_'] >= $new_date['to_']) {
                    if ($new_date['to_'] < $new_date['from_']) {
                        $errors['end_time'] = lang('Cannot change end time');
                    } else {
                        $event['to_'] = $new_date['to_'];
                        $_POST['to_'] = $new_date['to_'];
                    }

                } else {
                    $event['to_'] = $new_date['to_'];
                    $_POST['to_'] = $new_date['to_'];
                }
            }

            if (
                ($test['from_'] < $new_date['from_'] && $test['to_'] > $new_date['to_'])
                || ($test['from_'] == $new_date['from_'] && $test['to_'] > $new_date['to_'])
                || ($test['from_'] < $new_date['from_'] && $test['to_'] == $new_date['to_'])
            ) {
                $free_up_time = true;
            }


            list($event, $errors) = $this->extract_and_validate($event);

            if ($event['customer_organization_number']) {
                $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
                $event['customer_organization_id'] = $orginfo['id'];
                $event['customer_organization_name'] = $orginfo['name'];
            }

            if (!$errors && !$no_time_changes) {

                if (!is_null($event['application_id']) && $event['application_id'] != '' && !$errors['start_time'] && !$errors['end_time']) {
                    $comment = lang('event') . " #: " . $event['id'] . " " . lang("User has made a request to alter time on existing booking") . ' ' . $new_date['from_'] . ' - ' . $new_date['to_'];

                    $change_status = $free_up_time ? false : 'PENDING';
                    $this->application_ui->add_comment_to_application($event['application_id'], $comment, $change_status, $this->customer_name);
                    phpgwapi_cache::message_set(lang('Request for changed time') . '</br>' . lang('Follow status'));
                }
            }

            if ($test['equipment'] != $event['equipment']) {
                if (!is_null($event['application_id']) && $event['application_id'] != '') {

                    $comment = "ID: " . $event['id'] . " " . lang("User has changed field for equipment") . ' ' . $event['equipment'];

                    $this->application_ui->add_comment_to_application($event['application_id'], $comment, false, $this->customer_name);
                    phpgwapi_cache::message_set(lang('Request for equipment has been sent') . '</br>' . lang('Follow status'));

                }
            }


            if (sizeof($currres) != sizeof($_POST['resources'])) {
                $errors['resource_number'] = lang("You can't change resources to the event, for that contact administrator");
            }

            if (!$errors['event'] and !$errors['resource_number'] and !$errors['organization_number'] and !$errors['invoice_data'] && !$errors['contact_name'] && !$errors['out_of_range'] && !$errors['start_time'] && !$errors['end_time']) {

                if ($event['from_'] > $test['from_'] || $event['to_'] < $test['to_']) {

                    $this->bo->send_notification(true, $event, $mailadresses, $orgdate);
                }


                if (!$no_time_changes) {
                    $message = '';
                    $this->bo->send_admin_notification(true, $event, $message, $orgdate);
                }

                if ($free_up_time) {
                    $this->bo->update($event);
                }

                $date = substr($event['from_'], 0, 10);

                if ($from_org && $event['customer_organization_id'] !== null) {
                    self::redirect(array('menuaction' => 'bookingfrontend.uiorganization.show',
                        'id' => $event['customer_organization_id'], 'date' => $date));
                } else {
                    self::redirect(array('menuaction' => 'bookingfrontend.uibuilding.show',
                        'id' => $event['building_id'], 'date' => $date));
                }
            }
        }

        if ($errors['allocation'] && is_array($errors['allocation'])) {
            $errors['allocation'] = lang('Overlaps with existing allocation %1. Remember to send a notification', " #" . implode(', #', $errors['allocation'][0]));
        }
        if ($errors['booking'] && is_array($errors['booking'])) {
            $errors['booking'] = lang('Overlaps with existing booking %1. Remember to send a notification', " #" . implode(', #', $errors['booking'][0]));
        }

        $this->flash_form_errors($errors);
        if ($customer['customer_identifier_type']) {
            $event['customer_identifier_type'] = $customer['customer_identifier_type'];
            $event['customer_ssn'] = $customer['customer_ssn'];
            $event['customer_organization_number'] = $customer['customer_organization_number'];
            $event['customer_internal'] = $customer['customer_internal'];
        }

        $date = substr($event['from_'], 0, 10);
        self::add_javascript('bookingfrontend', 'base', 'event.js');
        $event['resources_json'] = json_encode(array_map('intval', $event['resources']));
        $event['resource_ids_json'] = json_encode(array_map('intval', $event['resource_ids']));
        $event['audiences_json'] = json_encode(array_map('intval', $event['audience']));
        $event['agegroups_json'] = json_encode($event['agegroups']);

        if ($from_org && $event['customer_organization_id'] !== null) {
            $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
                'id' => $event['customer_organization_id'], 'date' => $date));
        } else {
            $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
                'id' => $event['building_id'], 'date' => $date));
        }

        $activity_path = $this->activity_bo->get_path($event['activity_id']);
        $top_level_activity = $activity_path ? $activity_path[0]['id'] : .1;

        $activities = $this->activity_bo->fetch_activities();
        $activities = $activities['results'];
        $comments = $this->bo->so->get_ordered_comments($id);
        $agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
        $agegroups = $agegroups['results'];
        $audience = $this->audience_bo->fetch_target_audience($top_level_activity);
        $audience = $audience['results'];
        $this->install_customer_identifier_ui($event);
        $this->add_template_helpers();

        $event['from_'] = pretty_timestamp($event['from_']);
        $event['to_'] = pretty_timestamp($event['to_']);
        $event['from_2'] = date("H:i", phpgwapi_datetime::date_to_timestamp($event['from_']));
        $event['to_2'] = date("H:i", phpgwapi_datetime::date_to_timestamp($event['to_']));

        $GLOBALS['phpgw']->jqcal2->add_listener('from_', 'time');
        $GLOBALS['phpgw']->jqcal2->add_listener('to_', 'time');
        phpgwapi_jquery::load_widget('datepicker');
        phpgwapi_jquery::load_widget('timepicker');

        phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
            'file'), 'event_form');

        self::render_template_xsl('event_edit', array('event' => $event, 'activities' => $activities,
            'agegroups' => $agegroups, 'audience' => $audience, 'comments' => $comments, 'config' => $config->config_data));
    }

    public function cancel()
    {
        $id = phpgw::get_var('id', 'int');
        if (!$id) {
            phpgw::no_access('booking', lang('missing id'));
        }

        $config = CreateObject('phpgwapi.config', 'booking');
        $config->read();

        $event = $this->bo->read_single($id);

        if (!$event) {
            // Redirect to the front page
            self::redirect(array());
        }

        $from_org = phpgw::get_var('from_org', 'boolean', 'REQUEST', false);
        $bouser = CreateObject('bookingfrontend.bouser');
        $errors = array();

        date_default_timezone_set("Europe/Oslo");
        $currdate = new DateTime(phpgw::get_var('date'));
        $cdate = $currdate->format('Y-m-d H:m:s');
        if ($config->config_data['user_can_delete_events'] != 'yes') {
            phpgwapi_cache::message_set('user can not delete events', 'error');

            $can_delete_events = 0;
        } else {
            $can_delete_events = 1;
        }
        if ($event['customer_organization_number']) {
            $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
            $event['customer_organization_id'] = $orginfo['id'];
            $event['customer_organization_name'] = $orginfo['name'];
        }

        if ($config->config_data['split_pool'] == 'yes') {
            $split = 1;
        } else {
            $split = 0;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if ($cdate < $event['to_']) {
                if ($this->_is_event_owner($event, $bouser)) {
                    $resources = $event['resources'];
                    $activity = $this->organization_bo->so->get_resource_activity($resources);
                    $mailadresses = $this->building_users($event['building_id'], $split, $activity);
                    $extra_mailadresses = $this->resource_users($resources);
                    $mailadresses = array_merge($mailadresses, $extra_mailadresses);

                    $this->bo->send_notification(false, $event, $mailadresses);
                    $this->bo->send_admin_notification(false, $event, $_POST['message']);

                    if ($can_delete_events) {
                        $this->bo->so->delete_event($event['id']);
                    } else {
                        $event['active'] = 0;
                        $this->bo->update($event);
                    }

                    if (!empty($event['application_id'])) {
                        $this->assoc_bo = new booking_boapplication_association();
                        $associations = $this->assoc_bo->so->read(array('filters' => array('application_id' => $event['application_id']),
                            'sort' => 'from_', 'dir' => 'asc', 'results' => 'all'));
                        $changeStatus = 'REJECTED';
                        foreach ($associations['results'] as $association) {
                            if ($association['id'] != $event['id'] && $association['active'] == 1) {
                                $changeStatus = null;
                            }
                        }

                        foreach ($event['dates'] as $odate) {
                        }

                        $comment = lang('event') . " #: " . $event['id'] . " " . lang("User has made a request to cancel event") . ' ' . $odate['from_'] . ' - ' . $odate['to_'];
                        $this->application_ui->add_comment_to_application($event['application_id'], $comment, $changeStatus, $this->customer_name);
                    }

                    $date = substr($event['from_'], 0, 10);

                    if ($from_org && $event['customer_organization_id'] !== null) {
                        self::redirect(array('menuaction' => 'bookingfrontend.uiorganization.show',
                            'id' => $event['customer_organization_id'], 'date' => $date));
                    } else {
                        self::redirect(array('menuaction' => 'bookingfrontend.uibuilding.show',
                            'id' => $event['building_id'], 'date' => $date));
                    }
                } else {
                    $errors['not_admin'] = lang("You can't cancel events");
                }
            } else {
                $errors['started'] = lang("You can't cancel event that has started, for help contacts site admin");
            }
        }
        $this->flash_form_errors($errors);
        $date = substr($event['from_'], 0, 10);

        $event['resources_json'] = json_encode(array_map('intval', $event['resources']));

        if ($from_org && $event['customer_organization_id'] !== null) {
            $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
                'id' => $event['customer_organization_id'], 'date' => $date));
        } else {
            $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
                'id' => $event['building_id'], 'date' => $date));
        }

        $activities = $this->activity_bo->fetch_activities();
        $activities = $activities['results'];
        $this->install_customer_identifier_ui($event);
        $this->add_template_helpers();

        $event['from_'] = pretty_timestamp($event['from_']);
        $event['to_'] = pretty_timestamp($event['to_']);

        self::rich_text_editor('field_message');

        self::render_template_xsl('event_delete', array('event' => $event, 'activities' => $activities,
            'can_delete_events' => $can_delete_events));
    }

    public function building_users($building_id, $type = false, $activities = array())
    {
        $contacts = array();
        $organizations = $this->organization_bo->find_building_users($building_id, $type, $activities);
        foreach ($organizations['results'] as $key => $org) {
            if (!empty($org['email']) && strstr($org['email'], '@')) {
                if (!in_array($org['email'], $contacts)) {
                    $contacts[] = $org['email'];
                }
            }
            if (!empty($org['contacts'][0]['email']) && strstr($org['contacts'][0]['email'], '@')) {
                if (!in_array($org['contacts'][0]['email'], $contacts)) {
                    $contacts[] = $org['contacts'][0]['email'];
                }
            }
            if (!empty($org['contacts'][1]['email']) && strstr($org['contacts'][1]['email'], '@')) {
                if (!in_array($org['contacts'][1]['email'], $contacts)) {
                    $contacts[] = $org['contacts'][1]['email'];
                }
            }
            $grp_con = $this->booking_bo->so->get_group_contacts_of_organization($org['id']);
            foreach ($grp_con as $grp) {
                if (!empty($grp['email']) && !in_array($grp['email'], $contacts) && strstr($grp['email'], '@')) {
                    $contacts[] = $grp['email'];
                }
            }
        }
        return array_unique($contacts);
    }

    public function resource_users($resources)
    {
        $contacts = array();
        $orgs = "";
        foreach ($resources as $res) {
            $cres = $this->resource_bo->read_single($res);
            if ($cres['organizations_ids'] != '') {
                $orgs .= $cres['organizations_ids'] . ',';
            }
        }

        $orgs = explode(",", rtrim($orgs, ","));

        $organizations = $this->organization_bo->so->read(array('filters' => array('id' => $orgs),
            'sort' => 'name'));
        foreach ($organizations['results'] as $key => $org) {
            if ($org['email'] != '' && strstr($org['email'], '@')) {
                if (!in_array($org['email'], $contacts)) {
                    $contacts[] = $org['email'];
                }
            }
            if ($org['contacts'][0]['email'] != '' && strstr($org['contacts'][0]['email'], '@')) {
                if (!in_array($org['contacts'][0]['email'], $contacts)) {
                    $contacts[] = $org['contacts'][0]['email'];
                }
            }
            if ($org['contacts'][1]['email'] != '' && strstr($org['contacts'][1]['email'], '@')) {
                if (!in_array($org['contacts'][1]['email'], $contacts)) {
                    $contacts[] = $org['contacts'][1]['email'];
                }
            }
            $grp_con = $this->booking_bo->so->get_group_contacts_of_organization($org['id']);
            foreach ($grp_con as $grp) {
                if (!in_array($grp['email'], $contacts) && strstr($grp['email'], '@')) {
                    $contacts[] = $grp['email'];
                }
            }
        }
        return $contacts;
    }

    public function info()
    {
        $id = phpgw::get_var('id', 'int');
        if (!$id) {
            phpgw::no_access('booking', lang('missing id'));
        }

        $config = CreateObject('phpgwapi.config', 'booking')->read();
        if ($config['user_can_delete_events'] != 'yes') {
            $user_can_delete_events = 0;
        } else {
            $user_can_delete_events = 1;
        }
        $event = $this->bo->read_single($id);
        $from_org = phpgw::get_var('from_org', 'boolean', 'REQUEST', false);
        unset($event['comments']);
        $resources = $this->resource_bo->so->read(array('filters' => array('id' => $event['resources']),
            'sort' => 'name'));
        if ($event['customer_organization_number'] != '') {
            $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
            if ($orginfo != array()) {
                $event['customer_organization_id'] = $orginfo['id'];
                $event['customer_organization_name'] = $orginfo['name'];
                $orginfo['link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
                    'id' => $orginfo['id']));
            }
        } else {
            $orginfo = array();
        }
        //echo $event['name'];
        $event['resources'] = $resources['results'];
        $res_names = array();
        $res_ids = array();
        foreach ($event['resources'] as $res) {
            $res_names[] = $res['name'];
            $res_ids[] = $res['id'];
        }
        $event['resource_info'] = join(', ', $res_names);
        $event['resource_ids'] = $res_ids;
        $event['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
            'id' => $event['building_id']));
        $interval = (new DateTime($event['from_']))->diff(new DateTime($event['to_']));
        $when = "";
        if ($interval->days > 0) {
            $when = pretty_timestamp($event['from_']) . ' - ' . pretty_timestamp($event['to_']);
        } else {
            $end = new DateTime($event['to_']);
            $when = pretty_timestamp($event['from_']) . ' - ' . $end->format('H:i');
        }
        $event['when'] = $when;
        $bouser = CreateObject('bookingfrontend.bouser');
        if ($this->_is_event_owner($event, $bouser, true)) {
            if ($event['from_'] > Date('Y-m-d H:i:s')) {
                $event['edit_link'] = self::link(array('menuaction' => 'bookingfrontend.uievent.edit',
                    'id' => $event['id'],
                    'resource_ids' => $event['resource_ids'],
                    'from_org' => $from_org));

                $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uievent.cancel',
                    'id' => $event['id'],
                    'resource_ids' => $event['resource_ids'],
                    'from_org' => $from_org));
            }

            if ($event['application_id'] != null) {
                $event['copy_link'] = self::link(array('menuaction' => 'bookingfrontend.uiapplication.add',
                    'application_id' => $event['application_id']));
            }
        }

        $event['show_link'] = self::link(array('menuaction' => 'bookingfrontend.uievent.show',
            'id' => $event['id']));
        $resource_participant_limit_gross = $this->resource_bo->so->get_participant_limit($event['resources'], true);

        if (!empty($resource_participant_limit_gross['results'][0]['quantity'])) {
            $resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
        }

        $event['ical_link'] = self::link(array('menuaction' => 'bookingfrontend.uiparticipant.ical', 'reservation_type' => 'event', 'reservation_id' => $event['id']));

        if (!$event['participant_limit']) {
            $event['participant_limit'] = $resource_participant_limit ? $resource_participant_limit : (int)$config['participant_limit'];
        }

        $event['participant_limit'] = $event['participant_limit'] ? $event['participant_limit'] : (int)$config['participant_limit'];

        self::render_template_xsl('event_info', array('event' => $event, 'orginfo' => $orginfo,
            'user_can_delete_events' => $user_can_delete_events));
        $GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
    }

    public function info_json()
    {

        $ids = phpgw::get_var('ids', 'string');
        if ($ids) {
            $ids = explode(',', $ids);
        } elseif (!$ids || !is_array($ids)) {
            $ids = array(phpgw::get_var('id'));
        }
        if (empty($ids)) {
            phpgw::no_access('booking', lang('missing id'));
        }

        $config = CreateObject('phpgwapi.config', 'booking')->read();
        $user_can_delete_events = $config['user_can_delete_events'] === 'yes' ? 1 : 0;

        $from_org = phpgw::get_var('from_org', 'boolean', 'REQUEST', false);


        $events_info = [];
        foreach ($ids as $id) {
            $event = $this->bo->read_single($id);
            if (!$event) {
                continue; // Skip if the event is not found
            }

            if ($event['is_public'] == 0) {
                $event = $this->redactSensitiveInformation($event);
            }

            $event['info_resource_info'] = $this->calculate_resource_info($event['resources']);
            $event['info_org'] = $this->info_get_org_info($event['customer_organization_number']);
            $event['info_when'] = $this->info_format_event_time($event['from_'], $event['to_']);
            $event['info_participant_limit'] = $this->info_calculate_participant_limit($event, $config);
            $event['resource_ids'] = isset($event['resources']) ? array_map('intval', $event['resources']) : [];

            $event['info_edit_link'] = $this->info_determine_edit_link($event, $from_org);
            $event['info_cancel_link'] = $this->info_determine_cancel_link($event, $user_can_delete_events, $from_org);


            $event['info_ical_link'] = self::link([
                'menuaction' => 'bookingfrontend.uiparticipant.ical',
                'reservation_type' => 'event',
                'reservation_id' => $event['id']
            ]);
            $event['info_show_link'] = self::link([
                'menuaction' => 'bookingfrontend.uievent.show',
                'id' => $event['id']
            ]);

            $events_info[$id] = $event;
        }

        return ['events' => $events_info, 'info_user_can_delete_events' => $user_can_delete_events];
    }


    function redactSensitiveInformation($event)
    {
        $bouser = CreateObject('bookingfrontend.bouser');
        if ($bouser->is_logged_in() && $this->_is_event_owner($event, $bouser)) {
            return $event;
        }
        $redactedEvent = $event;
        $fieldsToRedact = array('organizer', 'homepage', 'description', 'contact_name',
            'contact_email', 'contact_phone', 'customer_organization_name',
            'customer_identifier_type', 'customer_ssn', 'customer_organization_number', 'secret', 'name');
        $redactedEvent['comments'] = array();
        foreach ($fieldsToRedact as $field) {
            if (isset($redactedEvent[$field])) {
                $redactedEvent[$field] = 'PRIVATE';
            }
        }

        return $redactedEvent;
    }

    private function info_get_org_info($customer_organization_number)
    {
        if ($customer_organization_number != '') {
            $orginfo = $this->bo->so->get_org($customer_organization_number);
            if (!empty($orginfo)) {
                $orginfo['link'] = self::link([
                    'menuaction' => 'bookingfrontend.uiorganization.show',
                    'id' => $orginfo['id']
                ]);
                return [
                    'customer_organization_id' => $orginfo['id'],
                    'customer_organization_name' => $orginfo['name'],
                    'org_link' => $orginfo['link']
                ];
            }
        }
        return []; // Return an empty array if no organization info is found
    }


    private function calculate_resource_info($resourceIds)
    {
        $resources = $this->resource_bo->so->read([
            'filters' => ['id' => $resourceIds],
            'sort' => 'name'
        ]);
        $resNames = array_map(function ($res) {
            return $res['name'];
        }, $resources['results']);

        return join(', ', $resNames);
    }

    private function info_format_event_time($from, $to)
    {
        $interval = (new DateTime($from))->diff(new DateTime($to));
        $when = "";
        if ($interval->days > 0) {
            $when = pretty_timestamp($from) . ' - ' . pretty_timestamp($to);
        } else {
            $end = new DateTime($to);
            $when = pretty_timestamp($from) . ' - ' . $end->format('H:i');
        }
        return $when;
    }

    private function info_calculate_participant_limit($event, $config)
    {
        $resource_participant_limit_gross = $this->resource_bo->so->get_participant_limit($event['resources'], true);
        $resource_participant_limit = !empty($resource_participant_limit_gross['results'][0]['quantity']) ? $resource_participant_limit_gross['results'][0]['quantity'] : 0;
        return !$event['participant_limit'] ? ($resource_participant_limit ?: (int)$config['participant_limit']) : $event['participant_limit'];
    }

    private function info_determine_edit_link($event, $from_org)
    {
        $bouser = CreateObject('bookingfrontend.bouser');

        if ($bouser->is_logged_in() && $this->_is_event_owner($event, $bouser) && $event['from_'] > Date('Y-m-d H:i:s')) {
            return self::link([
                'menuaction' => 'bookingfrontend.uievent.edit',
                'id' => $event['id'],
                'resource_ids' => $event['resource_ids'],
                'from_org' => $from_org
            ]);
        }
        return null;
    }

    private function info_determine_cancel_link($event, $user_can_delete_events, $from_org)
    {
        $bouser = CreateObject('bookingfrontend.bouser');

        if ($bouser->is_logged_in() && $this->_is_event_owner($event, $bouser) && $event['from_'] > Date('Y-m-d H:i:s') && $user_can_delete_events) {
            return self::link([
                'menuaction' => 'bookingfrontend.uievent.cancel',
                'id' => $event['id'],
                'resource_ids' => $event['resource_ids'],
                'from_org' => $from_org
            ]);
        }
        return null;
    }


    public function show()
    {
        $id = phpgw::get_var('id', 'int');
        if (!$id) {
            phpgw::no_access('booking', lang('missing id'));
        }

        $event = $this->bo->read_single($id);
        unset($event['comments']);
        $resources = $this->resource_bo->so->read(array('filters' => array('id' => $event['resources']),
            'sort' => 'name'));
        if ($event['customer_organization_number'] != '') {
            $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
            if ($orginfo != array()) {
                $event['customer_organization_id'] = $orginfo['id'];
                $event['customer_organization_name'] = $orginfo['name'];
                $orginfo['link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show',
                    'id' => $orginfo['id']));
            }
        } else {
            $orginfo = array();
        }
        //echo $event['name'];
        $event['resources'] = $resources['results'];
        $res_names = array();
        foreach ($event['resources'] as $res) {
            $res_names[] = $res['name'];
        }
        $event['resource_info'] = join(', ', $res_names);
        $event['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show',
            'id' => $event['building_id']));
        $interval = (new DateTime($event['from_']))->diff(new DateTime($event['to_']));
        $when = "";
        if ($interval->days > 0) {
            $when = pretty_timestamp($event['from_']) . ' - ' . pretty_timestamp($event['to_']);
        } else {
            $end = new DateTime($event['to_']);
            $when = pretty_timestamp($event['from_']) . ' - ' . $end->format('H:i');
        }
        $event['when'] = $when;

        $number_of_participants = createObject('booking.boparticipant')->get_number_of_participants('event', $event['id']);

        $event['number_of_participants'] = $number_of_participants;

        $config = CreateObject('phpgwapi.config', 'booking')->read();
        $external_site_address = !empty($config['external_site_address']) ? $config['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

        $participant_registration_link = $external_site_address
            . "/bookingfrontend/?menuaction=bookingfrontend.uiparticipant.add"
            . "&reservation_type=event"
            . "&reservation_id={$event['id']}";

        $event['participant_registration_link'] = $participant_registration_link;
        $event['participanttext'] = !empty($config['participanttext']) ? $config['participanttext'] : '';

        $resource_participant_limit_gross = $this->resource_bo->so->get_participant_limit($event['resources'], true);

        if (!empty($resource_participant_limit_gross['results'][0]['quantity'])) {
            $resource_participant_limit = $resource_participant_limit_gross['results'][0]['quantity'];
        }

        if (!$event['participant_limit']) {
            $event['participant_limit'] = $resource_participant_limit ? $resource_participant_limit : (int)$config['participant_limit'];
        }

        $event['participant_limit'] = $event['participant_limit'] ? $event['participant_limit'] : (int)$config['participant_limit'];

        phpgw::import_class('phpgwapi.phpqrcode');
        $code_text = $participant_registration_link;
        $filename = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . md5($code_text) . '.png';
        QRcode::png($code_text, $filename);
        $event['encoded_qr'] = 'data:image/png;base64,' . base64_encode(file_get_contents($filename));
//			_debug_array($event);

        $get_participants_link = $GLOBALS['phpgw']->link('/index.php', array(
            'menuaction' => 'booking.uiparticipant.index',
            'filter_reservation_id' => $event['id'],
            'filter_reservation_type' => 'event',
        ));

        $event['get_participants_link'] = $get_participants_link;

        $datatable_def = array();
        if (CreateObject('bookingfrontend.bouser')->is_logged_in()) {
            $datatable_def[] = array
            (
                'container' => 'datatable-container_0',
                'requestUrl' => json_encode(self::link(array(
                    'menuaction' => 'bookingfrontend.uiparticipant.index',
                    'filter_reservation_id' => $event['id'],
                    'filter_reservation_type' => 'event',
                    'phpgw_return_as' => 'json'))),
                'ColumnDefs' => array(
                    array(
                        'key' => 'phone',
                        'label' => lang('participants'),
                        'sortable' => true,
                    ),
                    array(
                        'key' => 'quantity',
                        'label' => lang('quantity'),
                        'sortable' => true,
                    )
                ),
                'data' => json_encode(array()),
                'config' => array(
                    array('disableFilter' => true),
                    array('disablePagination' => true)
                )
            );
        }

        self::render_template_xsl(
            array(
                'event',
                'datatable_inline'
            ),
            array(
                'event' => $event,
                'orginfo' => $orginfo,
                'datatable_def' => $datatable_def
            )
        );
    }

    public function report_numbers()
    {
        $id = phpgw::get_var('id', 'int');
        if (!$id) {
            phpgw::no_access('booking', lang('missing id'));
        }
        $step = 1;
        $event = $this->bo->read_single($id);

        $activity_path = $this->activity_bo->get_path($event['activity_id']);
        $top_level_activity = $activity_path ? $activity_path[0]['id'] : -1;

        $agegroups = $this->agegroup_bo->fetch_age_groups($top_level_activity);
        $agegroups = $agegroups['results'];

        $building_info = $this->bo->so->get_building_info($id);
        $building = $this->building_bo->read_single($building_info['id']);

        $interval = (new DateTime($event['from_']))->diff(new DateTime($event['to_']));
        $when = "";

        if ($interval->days > 0) {
            $when = pretty_timestamp($event['from_']) . ' - ' . pretty_timestamp($event['to_']);
        } else {
            $end = new DateTime($event['to_']);
            $when = pretty_timestamp($event['from_']) . ' - ' . $end->format('H:i');
        }

        $event['when'] = $when;

        if ($event['secret'] != phpgw::get_var('secret', 'string')) {
            $step = -1; // indicates that an error message should be displayed in the template
            self::render_template_xsl('report_numbers', array('event_object' => $event, 'agegroups' => $agegroups,
                'building' => $building, 'step' => $step));
            return false;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //reformatting the post variable to fit the booking object
            $temp_agegroup = array();
            $sexes = array('male', 'female');
            foreach ($sexes as $sex) {
                $i = 0;
                foreach (phpgw::get_var($sex) as $agegroup_id => $value) {
                    $temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
                    $temp_agegroup[$i][$sex] = $value;
                    $i++;
                }
            }

            $event['agegroups'] = $temp_agegroup;
            $event['reminder'] = 2; // status set to delivered
            $errors = $this->bo->validate($event);
            if (!$errors) {
                $receipt = $this->bo->update($event);
                $step++;
            }
        }
        self::render_template_xsl('report_numbers', array('event_object' => $event, 'agegroups' => $agegroups,
            'building' => $building, 'step' => $step));
    }
}

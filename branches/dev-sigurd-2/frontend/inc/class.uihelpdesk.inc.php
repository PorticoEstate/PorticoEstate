<?php

    /**
     * Frontend : a simplified tool for end users.
     *
     * @author Sigurd Nes <sigurdne@online.no>
     * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
     * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
     * @package Frontend
     * @version $Id$
     */

    /*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
    */

    phpgw::import_class('frontend.uifrontend');

    /**
     * Helpdesk
     *
     * @package Frontend
     */

    class frontend_uihelpdesk extends frontend_uifrontend
    {

        public $public_functions = array
            (
            'index'     	=> true,
            'add_ticket'	=> true,
            'view'	=> true,
        );

        public function __construct()
        {
            parent::__construct();
            $this->location_code = phpgw::get_var('location_code');
        }

        public function index()
        {

            $bo	= CreateObject('property.botts',true);

            $dry_run = false;
            $second_display = phpgw::get_var('second_display', 'bool');

            $datatable = array();

            if( phpgw::get_var('phpgw_return_as') != 'json' )
            {


                $datatable['config']['allow_allrows'] = true;

                $datatable['config']['base_java_url'] = "menuaction:'frontend.uihelpdesk.index',"
                    ."second_display:1,"
                    ."sort: '{$this->sort}',"
                    ."order: '{$this->order}',"
                    ."cat_id:'{$this->cat_id}',"
                    ."status_id: '{$this->status_id}',"
                    ."user_id: '{$this->user_id}',"
                    ."query: '{$this->query}',"
                    ."district_id: '{$this->district_id}',"
                    ."start_date: '{$start_date}',"
                    ."end_date: '{$end_date}',"
                    ."allrows:'{$this->allrows}'";


                $dry_run = true;
            }

            $ticket_list = array();
            if(!$dry_run)
            {
                $bo->location_code = $this->location_code;
                $ticket_list = $bo->read();
            }

            $uicols = array();
            $i = 0;
            $uicols['name'][$i++] = 'id';
            $uicols['name'][$i++] = 'subject';
            $uicols['name'][$i++] = 'entry_date';
            $uicols['name'][$i++] = 'status';

            $count_uicols_name = count($uicols['name']);

            if(is_array($ticket_list))
            {
                $status['X'] = array
                    (
                    'bgcolor'			=> '#5EFB6E',
                    'status'			=> lang('closed'),
                    'text_edit_status'	=> isset($bo->config->config_data['tts_lang_open']) && $bo->config->config_data['tts_lang_open'] ? $bo->config->config_data['tts_lang_open'] : lang('Open'),
                    'new_status' 		=> 'O'
                );

                $custom_status	= $bo->get_custom_status();

                foreach($custom_status as $custom)
                {
                    $status["C{$custom['id']}"] = array
                        (
                        'bgcolor'			=> $custom['color'] ? $custom['color'] : '',
                        'status'			=> $custom['name'],
                        'text_edit_status'	=> lang('close'),
                        'new_status'		=> 'X'
                    );
                }

                $j = 0;
                foreach($ticket_list as $ticket)
                {
                    for ($k = 0 ; $k < $count_uicols_name ; $k++)
                    {
                        if($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='O')
                        {
                            $datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
                            $datatable['rows']['row'][$j]['column'][$k]['value'] 	= isset($bo->config->config_data['tts_lang_open']) && $bo->config->config_data['tts_lang_open'] ? $bo->config->config_data['tts_lang_open'] : lang('Open');
                        }
                        else if($uicols['name'][$k] == 'status' && $ticket[$uicols['name'][$k]]=='C')
                        {
                            $datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
                            $datatable['rows']['row'][$j]['column'][$k]['value'] 	= lang('Closed');
                        }
                        else if($uicols['name'][$k] == 'status' && array_key_exists($ticket[$uicols['name'][$k]],$status))
                        {
                            $datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
                            $datatable['rows']['row'][$j]['column'][$k]['value'] 	= $status[$ticket[$uicols['name'][$k]]]['status'];
                        }
                        else
                        {
                            $datatable['rows']['row'][$j]['column'][$k]['name']		= $uicols['name'][$k];
                            $datatable['rows']['row'][$j]['column'][$k]['value']	= $ticket[$uicols['name'][$k]];
                        }
                    }

                    $j++;
                }
            }

            $parameters = array
                (
                'parameter' => array
                (
                    array
                    (
                        'name'		=> 'id',
                        'source'	=> 'id'
                    ),
                )
            );

            $datatable['rowactions']['action'][] = array(
                'my_name' 			=> 'view',
                'statustext' 	=> lang('view the ticket'),
                'text'			=> lang('view'),
                'action'		=> $GLOBALS['phpgw']->link('/index.php',array
                (
                'menuaction'	=> 'frontend.uihelpdesk.view'
                )),
                'parameters'	=> $parameters
            );

            unset($parameters);
            for ($i = 0 ; $i < $count_uicols_name ; $i++)
            {
                $datatable['headers']['header'][$i]['formatter'] 		= !isset($uicols['formatter'][$i]) || $uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i];
                $datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
                $datatable['headers']['header'][$i]['text'] 			= lang($uicols['name'][$i]);
                $datatable['headers']['header'][$i]['visible'] 			= true;
                $datatable['headers']['header'][$i]['sortable']			= false;
                if($uicols['name'][$i]=='id' || $uicols['name'][$i]=='user' || $uicols['name'][$i]=='entry_date')
                {
                    $datatable['headers']['header'][$i]['sortable']		= true;
                    $datatable['headers']['header'][$i]['sort_field']   = $uicols['name'][$i];
                }
                if($uicols['name'][$i]=='id')
                {
                    $datatable['headers']['header'][$i]['visible'] 		= false;
                }
            }

            //path for property.js
            $datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

            // Pagination and sort values
            $datatable['pagination']['records_start'] 	= (int)$bo->start;
            $datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
            if($dry_run)
            {
                $datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
            }
            else
            {
                $datatable['pagination']['records_returned']= count($ticket_list);
            }
            $datatable['pagination']['records_total'] 	= $bo->total_records;

            $datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column

            $appname		= lang('helpdesk');
            $function_msg	= lang('list ticket');

            if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
            {
                $datatable['sorting']['order'] 			= 'entry_date'; // name key Column in myColumnDef
                $datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
            }
            else
            {
                $datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
                $datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
            }

//-- BEGIN----------------------------- JSON CODE ------------------------------
            //values for Pagination
            $json = array(
                'recordsReturned' 	=> $datatable['pagination']['records_returned'],
                'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
                'startIndex' 		=> $datatable['pagination']['records_start'],
                'sort'				=> $datatable['sorting']['order'],
                'dir'				=> $datatable['sorting']['sort'],
                'records'			=> array()
            );

            // values for datatable
            if(is_array($datatable['rows']['row']))
            {
                foreach( $datatable['rows']['row'] as $row )
                {
                    $json_row = array();
                    foreach( $row['column'] as $column)
                    {
                        $json_row[$column['name']] = $column['value'];
                    }
                    $json['records'][] = $json_row;
                }
            }

            // right in datatable
            if(is_array($datatable['rowactions']['action']))
            {
                $json['rights'] = $datatable['rowactions']['action'];
            }

            if( phpgw::get_var('phpgw_return_as') == 'json' )
            {
                return $json;
            }


            $datatable['json_data'] = json_encode($json);
//-------------------- JSON CODE ----------------------

// Prepare template variables and process XSLT

            if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
            {
                $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
            }

            phpgwapi_yui::load_widget('dragdrop');
            phpgwapi_yui::load_widget('datatable');
            phpgwapi_yui::load_widget('menu');
            phpgwapi_yui::load_widget('connection');
            phpgwapi_yui::load_widget('loader');
            phpgwapi_yui::load_widget('paginator');

            // Prepare CSS Style
            $GLOBALS['phpgw']->css->validate_file('datatable');
            $GLOBALS['phpgw']->css->validate_file('property');
            $GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
            $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
            $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
            $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

            $GLOBALS['phpgw_info']['flags']['app_header'] = lang('frontend') . ' - ' . $appname . ': ' . $function_msg;

            $GLOBALS['phpgw']->js->validate_file( 'yahoo', 'helpdesk.list' , 'frontend' );

            $data = array(
            	'header' 		=>	$this->header_state,
                'tabs'			=> 	$this->tabs,
                'helpdesk' 		=> array('datatable' => $datatable)
  
                //'lightbox_name'	=> 	lang('add ticket')
            );
            
            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'helpdesk', 'datatable'));
            $GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('app_data' => $data));
			//print_r( $GLOBALS['phpgw']->xslttpl->get_vars());
        }

        private function cmp($a, $b)
        {
            $timea = explode('/', $a['date']);
            $timeb = explode('/', $b['date']);
            $year_and_maybe_time_a = explode(' - ', $timea[2]);
            $year_and_maybe_time_b = explode(' - ', $timeb[2]);
            $time_of_day_a = explode(':', $year_and_maybe_time_a[1]);
            $time_of_day_b = explode(':', $year_and_maybe_time_b[1]);

            $timestamp_a = mktime($time_of_day_a[0], $time_of_day_a[1], 0, $timea[1], $timea[0], $year_and_maybe_time_a[0]);
            $timestamp_b = mktime($time_of_day_b[0], $time_of_day_b[1], 0, $timeb[1], $timeb[0], $year_and_maybe_time_b[0]);

            if($timestamp_a > $timestamp_b)
            {
                return 1;
            }

            return -1;
        }


        public function view()
        {
            $bo	= CreateObject('property.botts',true);
            $ticketid = phpgw::get_var('id');
            $ticket = $bo->read_single($ticketid);

            $notes = $bo->read_additional_notes($ticketid);
            $history = $bo->read_record_history($ticketid);

            $tickethistory = array();

            foreach($notes as $note)
            {
                if(empty($note['value_publish']) || $note['value_publish'])
                {
                    $tickethistory[] = array(
                        'date' => $note['value_date'],
                        'user' => $note['value_user'],
                        'note' => $note['value_note']
                    );
                }
            }


            foreach($history as $story)
            {
                $tickethistory[] = array(
                    'date' => $story['value_date'],
                    'user' => $story['value_user'],
                    'action'=> $story['value_action'],
                    'new_value' => $story['value_new_value'],
                    'old_value' => $story['value_old_value']
                );
            }

            usort($tickethistory, array($this, "cmp"));


            $i=0;
            foreach($tickethistory as $foo)
            {
                $tickethistory2['record'.$i] = $foo;
                $i++;
            }

            $data = array(
                'tabs'			=> $this->tabs,
                'ticket'        => $ticket,
                'tickethistory'	=> $tickethistory2
            );

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'ticketview'));
            $GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('ticketinfo' => $data));
        }


        public function add_ticket()
        {
            $bo	= CreateObject('property.botts',true);

            $values         = phpgw::get_var('values');
            $missingfields  = false;
            $msglog         = array();

            if (isset($values['save']))
            {
                foreach($values as $key => $value)
                {
                    if(empty($value) && $key != 'file')
                    {
                        $missingfields = true;
                    }
                }

                if(!$missingfields)
                {


                    $ticket = new frontend_ticket();
                    $ticket->set_date(time());
                    $ticket->set_title($values('title'));
                    $ticket->set_location_description($values('locationdesc'));
                    $ticket->set_messages(array($values('title')));
                }
                else
                {
                    $msglog['error'][] = array('msg'=>lang('Missing field(s)'));
                }
            }

            $data = array(
                'msgbox_data'       => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
                'from_name'         => $GLOBALS['phpgw_info']['user']['fullname'],
                'from_address'      => $GLOBALS['phpgw_info']['user']['preferences']['property']['email'],
                'form_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uihelpdesk.add_ticket')),
                'support_address'	=> $GLOBALS['phpgw_info']['server']['support_address'],
            );

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend','helpdesk'));
            $GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add_ticket' => $data));
        }

    }

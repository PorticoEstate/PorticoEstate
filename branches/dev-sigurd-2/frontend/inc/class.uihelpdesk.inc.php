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
            'download'      => true,
            'add_ticket'	=> true,
        );

        public function __construct()
        {
            parent::__construct();
        }

        public function query()
        {
            $location_code	= phpgw::get_var('location_code');

            $tickets = frontend_boproperty::get_tickets($location_code);


            //Serialize the contracts found
            $rows = array();
            foreach ($tickets as $result)
            {
                if(isset($result))
                {
                    $rows[] = $result->serialize();
                }
            }

            //Add context menu columns (actions and labels)
            array_walk($rows, array($this, 'add_actions'), array($type));

            //Build a YUI result from the data
            $result_data = array('results' => $rows, 'total_records' => count($rows));

            return $this->yui_results($result_data, 'total_records', 'results');
        }

        /**
         * Add data for context menu
         *
         * @param $value pointer to
         * @param $key ?
         * @param $params [type of query, editable]
         */
        public function add_actions(&$value, $key, $params)
        {
            $value['ajax'] = array();
            $value['actions'] = array();
            $value['labels'] = array();

            $type = $params[0];

            $value['ajax'][] = false;
            $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uihelpdesk.view', 'id' => $value['id'])));
            $value['labels'][] = lang('view');
        }

        public function index()
        {
            $location_code	= phpgw::get_var('location_code');

            
            $datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
                (
                'menuaction'		=> 'frontend.uihelpdesk.index',
                'location_code'				=> $this->location_code
                )
            );

            $datatable['config']['allow_allrows'] = true;

            $datatable['config']['base_java_url'] = "menuaction:'frontend.uihelpdesk.query',"
                ."second_display:1,"
                ."sort: '{$this->sort}',"
                ."location_code: '{$this->location_code}',"
                ."order: '{$this->order}',"
                ."cat_id:'{$this->cat_id}',"
                ."status_id: '{$this->status_id}',"
                ."user_id: '{$this->user_id}',"
                ."query: '{$this->query}',"
                ."district_id: '{$this->district_id}',"
                ."start_date: '{$start_date}',"
                ."end_date: '{$end_date}',"
                ."allrows:'{$this->allrows}'";

            $uicols = array(
                array('id', '', false, false),
                array('title','title', true, false),
                array('date','date', true, false),
                array('user','user', true, false)
            );

            for ($i = 0 ; $i < count($uicols) ; $i++)
			{
					$datatable['headers']['header'][$i]['name'] 		= $uicols[$i][0];
					$datatable['headers']['header'][$i]['text'] 		= lang($uicols[$i][1]);
					$datatable['headers']['header'][$i]['visible'] 		= $uicols[$i][2];
					$datatable['headers']['header'][$i]['sortable']		= $uicols[$i][3];
			}


			$data = array
			(
				'tabs'			=> $this->tabs,
				'datatable' 	=> $datatable
			);

            //print_r($mekk);

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend','helpdesk', 'datatable'));
            $GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('helpdesk' => $data));

        }

        public function helpdesk_download()
        {
            echo 'implement me';
        }
        public function add_ticket()
        {

            $GLOBALS['phpgw_info']['flags']['noframework'] =  true;

            $values	= phpgw::get_var('values');

            $receipt = array();
            if (isset($values['save']))
            {
                if($GLOBALS['phpgw']->session->is_repost())
                {
                    $receipt['error'][]=array('msg'=>lang('repost'));
              ø  }

                if(!isset($values['address']) || !$values['address'])
                {
                    $receipt['error'][]=array('msg'=>lang('Missing address'));
                }

                if(!isset($values['details']) || !$values['details'])
                {
                    $receipt['error'][]=array('msg'=>lang('Please give som details'));
                }

                $attachments = array();

                if(isset($_FILES['file']['name']) && $_FILES['file']['name'])
                {
                    $file_name	= str_replace(' ','_',$_FILES['file']['name']);
                    $mime_magic = createObject('phpgwapi.mime_magic');
                    $mime       = $mime_magic->filename2mime($file_name);

                    $attachments[] = array
                        (
                        'file' => $_FILES['file']['tmp_name'],
                        'name' => $file_name,
                        'type' => $mime
                    );
                }

                if (!$receipt['error'])
                {
                    if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'] )
                    {
                        if (!is_object($GLOBALS['phpgw']->send))
                        {
                            $GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
                        }

                        $from = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$values['from_address']}>";

                        $receive_notification = true;
                        $rcpt = $GLOBALS['phpgw']->send->msg('email', $values['address'],'Support',
                            stripslashes(nl2br($values['details'])), '', '', '',
                            $from , $GLOBALS['phpgw_info']['user']['fullname'],
                            'html', '', $attachments , $receive_notification);

                        if($rcpt)
                        {
                            $receipt['message'][]=array('msg'=>lang('message sent'));
                        }
                    }
                    else
                    {
                        $receipt['error'][]=array('msg'=>lang('SMTP server is not set! (admin section)'));
                    }
                }
            }

            $data = array
                (
                'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
                'from_name'		=> $GLOBALS['phpgw_info']['user']['fullname'],
                'from_address'	=> $GLOBALS['phpgw_info']['user']['preferences']['property']['email'],
                'form_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uihelpdesk.add_ticket')),
                'support_address'	=> $GLOBALS['phpgw_info']['server']['support_address'],
            );

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend','helpdesk'));
            $GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('add_ticket' => $data));
        }

        public function download()
        {
            echo 'implement me';
            die();
        }
    }

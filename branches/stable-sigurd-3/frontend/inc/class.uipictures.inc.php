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
	 * Pictures
	 *
	 * @package Frontend
	 */

    class frontend_uipictures extends frontend_uifrontend
    {

        public $public_functions = array
        (
            'index'     	=> true,
        );

		public function __construct()
		{
			parent::__construct();
		}

		public function index()
		{
			/*$allusers = $GLOBALS['phpgw']->accounts->get_list('accounts', -1);
			$content = array();
			foreach ($allusers as $user)
			{
				$content[] = array
				(
					'id'	=> $user->id,
					'name'	=> $user->__toString(), 
				);
			}


       		$myColumnDefs[0] = array
       		(
       			'name'		=> "0",
       			'values'	=>	json_encode(array(	array('key' => 'id','label'=> lang('id') ,'sortable'=>true,'resizeable'=>true,'hidden'=>false),
       												array('key' => 'name',	'label'=> lang('name'),	'sortable'=>true,'resizeable'=>true),
		       				       					array('key' => 'select','label'=> lang('select'), 'sortable'=>false,'resizeable'=>false,'formatter'=>'myFormatterCheck','width'=>30)))
			);	

			$datavalues[0] = array
			(
					'name'					=> "0",
					'values' 				=> json_encode($content),
					'total_records'			=> 0,
					'permission'   			=> "''",
					'is_paginator'			=> 1,
					'footer'				=> 1
			);


			$data = array
			(
				'header' 			=>$this->header_state,
				'td_count'			=> 2,
				'base_java_url'		=> "{menuaction:'frontend.ui_demo_tabs.first'}",
				'property_js'		=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'			=> $datavalues,
				'myColumnDefs'		=> $myColumnDefs,
				'tabs'				=> $this->tabs,
			);

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('paginator');

		  	$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'demo_tabs.second', 'frontend' );

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'demo'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('demo_3' => $data));*/
			$data = array
			(
				'header' =>$this->header_state,
				'tabs' => $this->tabs,
				'pictures'      => lang('not_implemented')
			);
			
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
        	$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','pictures'));

		}
    }

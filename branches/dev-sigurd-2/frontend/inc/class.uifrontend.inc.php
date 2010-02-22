<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uifrontend.inc.php 4859 2010-02-18 23:09:16Z sigurd $
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


	/**
	 * Frontend
	 *
	 * @package Frontend
	 */

	class frontend_uifrontend
	{

		public $public_functions = array(
			'index'				=> true,
			'drawings'			=> true,
			'pictures'			=> true,
			'maintenance'		=> true,
			'refurbishment'		=> true,
			'services'			=> true,
			'contract'			=> true,
			'helpdesk'			=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$noframework = phpgw::get_var('noframework', 'bool');
			$GLOBALS['phpgw_info']['flags']['noframework'] = $noframework;

			$locations = $GLOBALS['phpgw']->locations->get_locations();

			unset($locations['.']);
			unset($locations['admin']);

			$tabs = array();
			foreach ($locations as $location => $name)
			{
				if ( $GLOBALS['phpgw']->acl->check($location, PHPGW_ACL_READ, 'frontend') )
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('frontend', $location);
					$tabs[$location_id] = array(
						'label' => lang($name),
						'link'  => $GLOBALS['phpgw']->link('/',array('menuaction' => "frontend.uifrontend.{$name}", 'type'=>$location_id, 'noframework' => $noframework))
					);
				}			
			}
			
			$selected = phpgw::get_var('type', 'int', 'REQUEST', 0);
			$this->tabs = $GLOBALS['phpgw']->common->create_tabs($tabs, $selected);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "frontend::{$selected}";
		}


		static function render_template($tpl, $section_data)
		{
            $GLOBALS['phpgw']->xslttpl->add_file($tpl);
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $section_data);
		}


		/**
		 * TODO
		 */
		public function index()
		{
			$data = array
			(
				'tabs'		=> $this->tabs
			);
			self::render_template(array('frontend'), array('demo_section_within_template' => $data));

		}

		public function drawings()
		{
			$receipt = array();

			$receipt['error'][]=array('msg'=>'Eksempel på feilmelding');
			$receipt['message'][]=array('msg'=>'Eksempel på gladmelding');

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'tabs'			=> $this->tabs,
				'date_start'	=> $GLOBALS['phpgw']->yuical->add_listener('date_start', $date_start),
				'date_end'		=> $GLOBALS['phpgw']->yuical->add_listener('date_end', $date_end),
			);
			self::render_template(array('frontend'), array('demo_2' => $data));
		}

		public function pictures()
		{
			$allusers = $GLOBALS['phpgw']->accounts->get_list('accounts', -1);
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

			self::render_template(array('frontend'), array('inline_table' => $data));
		}

		public function maintenance()
		{
			$this->index();
		}

		public function refurbishment()
		{
			$this->index();
		}

		public function services()
		{
			$this->index();
		}

		public function contract()
		{
			$this->index();
		}

		public function helpdesk()
		{
			$this->index();
		}
	}

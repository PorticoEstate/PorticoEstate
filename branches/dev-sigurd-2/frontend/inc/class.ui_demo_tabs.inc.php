<?php

	phpgw::import_class('frontend.uicommon');

	class frontend_ui_demo_tabs
	{

		public $public_functions = array(
			'index'		=> true,
			'first'		=> true,
			'second'	=> true,
			'third'		=> true
		);

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$noframework = phpgw::get_var('noframework', 'bool');
			$GLOBALS['phpgw_info']['flags']['noframework'] = $noframework;
			$tabs = array();
			$tabs[] = array(
				'label' => lang('tab # %1', 1),
				'link'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.ui_demo_tabs.first', 'type'=>1, 'noframework' => $noframework))
			);
			$tabs[] = array(
				'label' => lang('tab # %1', 2),
				'link'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.ui_demo_tabs.second', 'type'=>'2', 'noframework' => $noframework))
			);
			$tabs[] = array(
				'label' => lang('tab # %1', 3),
				'link'  => $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.ui_demo_tabs.third', 'type'=>'3', 'noframework' => $noframework))
			);

			$type = phpgw::get_var('type', 'int', 'REQUEST', 0);
			switch($type)
			{
				case '1':
					$selected = 0;
					break;
				case '2':
					$selected = 1;
					break;
				case '3':
					$selected = 2;
					break;
				default:
					$selected = 0;
			}
			$this->tabs = $GLOBALS['phpgw']->common->create_tabs($tabs, $selected);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'frontend::demo_tab';
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
			self::render_template(array('tabs_demo'), array('demo_section_within_template' => $data));

		}

		public function first()
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
			self::render_template(array('tabs_demo'), array('demo_2' => $data));
		}

		public function second()
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

			self::render_template(array('tabs_demo'), array('inline_table' => $data));
		}

		public function third()
		{
			$this->index();
		}

	}

<?php
	phpgw::import_class('booking.uicommon');

	class booking_uicontactperson extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'          =>  true,
			'edit'          => true,
		);

		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocontactperson');
			self::set_active_menu('booking::contacts');
			$this->module = "booking";
		}
        public function index()
        {
            if(phpgw::get_var('phpgw_return_as') == 'json') {
                return $this->index_json();
            }

			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New contact'),
								'href' => self::link(array('menuaction' => 'booking.uicontactperson.edit'))
							),
							array(
								'type' => 'text',
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uicontactperson.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('First name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'surname',
							'label' => lang('Surname'),
						),
						array(
							'key' => 'organization',
							'label' => lang('Organization')
						),
						array(
							'key' => 'phone',
							'label' => lang('Phone')
						),
						array(
							'key' => 'mail',
							'label' => lang('Email')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			self::render_template('datatable', $data);
        }
        public function index_json()
        {   
			if ($id = intval(phpgw::get_var('id', 'GET'))) {
				$person = $this->bo->read_single($id);
				return $this->yui_results(array("totalResultsAvailable" => 1, "results" => $person));
			}

			$persons = $this->bo->read();
			array_walk($persons["results"], array($this, "_add_links"), "booking.uicontactperson.show");
			return $this->yui_results($persons);
        }
		public function show()
		{
			$person = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$person['contactpersons_link'] = self::link(array('menuaction' => 'booking.uicontactperson.index'));
			$person['edit_link'] = self::link(array('menuaction' => 'booking.uicontactperson.edit', 'id' => $person['id']));

			$data = array(
				'group'	=>	$group
			);
			self::render_template('contactperson', array('person' => $person, ));
		}
		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			if ($id) {
				$person = $this->bo->read_single($id);
				$person['id'] = $id;
				$person['contactpersons_link'] = self::link(array('menuaction' => 'booking.uicontactperson.index'));
				$person['edit_link'] = self::link(array('menuaction' => 'booking.uicontactperson.edit', 'id' => $person['id']));
			} else {
				$person = array();
			}

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$person = array_merge($person, extract_values($_POST, array('ssn', 'name', 'homepage', 'phone', 'email', 'description',)));
				$errors = $this->bo->validate($person);
				if(!$errors)
				{
					if ($id) {
						$receipt = $this->bo->update($person);
					} else {
						$receipt = $this->bo->add($person);
					}
					$this->redirect(array('menuaction' => $this->module . '.uicontactperson.show', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);

			self::add_stylesheet('phpgwapi/js/yahoo/assets/skins/sam/skin.css');
			self::add_javascript('yahoo', 'yahoo/yahoo-dom-event', 'yahoo-dom-event.js');
			self::add_javascript('yahoo', 'yahoo/element', 'element-min.js');
			self::add_javascript('yahoo', 'yahoo/dom', 'dom-min.js');
			self::add_javascript('yahoo', 'yahoo/container', 'container_core-min.js');
			self::add_javascript('yahoo', 'yahoo/editor', 'simpleeditor-min.js');

			self::add_template_file("contactperson_fields");
			self::render_template('contactperson_edit', array('person' => $person,));
		}

    }


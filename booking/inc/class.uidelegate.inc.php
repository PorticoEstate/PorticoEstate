<?php
	phpgw::import_class('booking.uicommon');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uidelegate extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'show' => true,
			'edit' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bodelegate');
			$this->activity_bo = CreateObject('booking.boactivity');
			self::set_active_menu('booking::organizations::delegates');

			$this->module = "booking";
		}

		public function link_to_parent_params( $action = 'show', $params = array() )
		{
			return array_merge(array('menuaction' => sprintf($this->module . '.ui%s.%s', $this->get_current_parent_type(), $action),
				'id' => $this->get_parent_id()), $params);
		}

		public function link_to_parent( $action = 'show', $params = array() )
		{
			return $this->link($this->link_to_parent_params($action, $params));
		}

		public function get_current_parent_type()
		{
			if (!$this->is_inline())
			{
				return null;
			}
			$parts = explode('_', key($a = $this->get_inline_params()));
			return $parts[1];
		}

		public function get_parent_id()
		{
			$inlineParams = $this->get_inline_params();
			return $inlineParams['filter_organization_id'];
		}

		public function get_parent_if_inline()
		{
			if (!$this->is_inline())
				return null;
			return CreateObject('booking.bo' . $this->get_current_parent_type())->read_single($this->get_parent_id());
		}

		public function redirect_to_parent_if_inline()
		{
			if ($this->is_inline())
			{
				$this->redirect($this->link_to_parent_params());
			}

			return false;
		}

		public function link_to( $action, $params = array() )
		{
			return $this->link($this->link_to_params($action, $params));
		}

		public function link_to_params( $action, $params = array() )
		{
			if (isset($params['ui']))
			{
				$ui = $params['ui'];
				unset($params['ui']);
			}
			else
			{
				$ui = 'delegate';
				$this->apply_inline_params($params);
			}

			$action = sprintf($this->module . '.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}

		public function apply_inline_params( &$params )
		{
			if ($this->is_inline())
			{
				$params['filter_organization_id'] = intval(phpgw::get_var('filter_organization_id'));
			}
			return $params;
		}

		public function get_inline_params()
		{
			return array('filter_organization_id' => phpgw::get_var('filter_organization_id', 'int', 'REQUEST'));
		}

		public function is_inline()
		{
			return false != phpgw::get_var('filter_organization_id', 'int', 'REQUEST');
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$lang_delegate = lang('delegate');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$lang_delegate}";

			$data = array(
				'datatable_name'	=> $lang_delegate,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => $this->module . '.uidelegate.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'organization_name',
							'label' => lang('Organization')
						),
						array(
							'key' => 'name',
							'label' => lang('delegate'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'phone',
							'label' => lang('Phone'),
						),
						array(
							'key' => 'email',
							'label' => lang('Email'),
						),
						array(
							'key' => 'active',
							'label' => lang('Active'),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			$data['datatable']['actions'][] = array();
			$data['datatable']['new_item'] = self::link(array('menuaction' => $this->module . '.uidelegate.edit'));

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$delegates = $this->bo->read();

			$lang_yes = lang('yes');
			$lang_no = lang('no');

			array_walk($delegates["results"], array($this, "_add_links"), $this->module . ".uidelegate.show");
			foreach ($delegates["results"] as &$delegate)
			{
				$delegate['active'] = $delegate['active'] == 1 ? $lang_yes : $lang_no;
			}
			$results = $this->jquery_results($delegates);

			if (is_array($parent_entity = $this->get_parent_if_inline()))
			{
				if ($this->bo->allow_create(array($this->get_current_parent_type() . '_id' => $parent_entity['id'])))
				{
					$results['Actions']['add'] = array('text' => lang('Add Group'), 'href' => $this->link_to('edit'));
				}
			}

			return $results;
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');


			if ($id)
			{
				$delegate = $this->bo->read_single($id);
				$delegate['id'] = $id;
				$delegate['organization_link'] = $this->link_to('show', array('ui' => 'organization',
					'id' => $delegate['organization_id']));

				$delegate['cancel_link'] = $this->link_to('show', array('id' => $id));

				if ($this->is_inline())
				{
					$delegate['cancel_link'] = $this->link_to_parent();
				}
			}
			else
			{
				$delegate = array();
				$delegate['cancel_link'] = $this->link_to('index', array('ui' => 'organization'));

				$organization_id = phpgw::get_var('organization_id', 'int');
				if($organization_id)
				{
					$delegate['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show',
						'id' => $organization_id));
					$delegate['cancel_link'] = $delegate['organization_link'];
					$delegate['organization_id'] = $organization_id;
					$organization = CreateObject('booking.boorganization')->read_single($organization_id);
					$delegate['organization_name'] = $organization['name'];
				}

				if ($this->is_inline())
				{
					$delegate['organization_link'] = $this->link_to_parent();
					$delegate['cancel_link'] = $this->link_to_parent();
					$this->apply_inline_params($delegate);
				}
			}

			$delegate['organizations_link'] = $this->link_to('index', array('ui' => 'organization'));

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$delegate = array_merge($delegate, extract_values($_POST, array(
					'name' => 'string',
					'ssn' => 'string',
					'email' => 'string',
					'phone' => 'string',
					'organization_id' => 'string',
					'organization_name' => 'string',
					'active' => 'int',
				)));
				if (!isset($delegate["active"]))
				{
					$delegate['active'] = '1';
				}

				$errors = $this->bo->validate($delegate);
				if (strlen($_POST['name']) > 50)
				{
					$errors['name'] = lang('Lengt of name is to long, max 50 characters long');
				}
				if (strlen($_POST['shortname']) > 11)
				{
					$errors['shortname'] = lang('Lengt of shortname is to long, max 11 characters long');
				}
				if (!$errors)
				{
					if (empty($delegate['ssn']))
					{
						$_delegate = $this->bo->read_single($id);
						$delegate['ssn'] = $_delegate['ssn'];
					}
					else
					{
						$hash = sha1($delegate['ssn']);
						$delegate['ssn'] =  '{SHA1}' . base64_encode($hash);
					}

					if ($id)
					{
						$receipt = $this->bo->update($delegate);
					}
					else
					{
						$receipt = $this->bo->add($delegate);
					}

					$this->redirect_to_parent_if_inline();
					$this->redirect($this->link_to_params('show', array('id' => $receipt['id'])));
				}
			}
			$this->flash_form_errors($errors);

			if (is_array($parent_entity = $this->get_parent_if_inline()))
			{
				$delegate[$this->get_current_parent_type() . '_id'] = $parent_entity['id'];
				$delegate[$this->get_current_parent_type() . '_name'] = $parent_entity['name'];
			}

			phpgwapi_jquery::load_widget('autocomplete');
			self::rich_text_editor('field_description');

			$tabs = array();
			$tab_text = ($id) ? 'Delegate Edit' : 'Delegate New';
			if (id)
			{
				$tabs['generic'] = array('label' => lang($tab_text), 'link' => '#delegate_edit');
			}
			$active_tab = 'generic';
			$delegate['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$delegate['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			$delegate['ssn'] = '';//secret

			self::render_template_xsl('delegate_edit', array('delegate' => $delegate, 'module' => $this->module));
		}

		public function show()
		{
			$delegate = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$delegate['organizations_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.index'));
			$delegate['organization_link'] = self::link(array('menuaction' => $this->module . '.uiorganization.show',
					'id' => $delegate['organization_id']));
			$delegate['edit_link'] = self::link(array('menuaction' => $this->module . '.uidelegate.edit',
					'id' => $delegate['id']));
			$delegate['cancel_link'] = self::link(array('menuaction' => $this->module . '.uidelegate.index'));

			$data = array(
				'delegate' => $delegate
			);
			$loggedin = (int)true; // FIXME: Some sort of authentication!
			$edit_self_link = self::link(array('menuaction' => 'bookingfrontend.uidelegate.edit',
					'id' => $delegate['id']));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('delegate'), 'link' => '#delegate');
			$active_tab = 'generic';

			$delegate['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('delegate', array('delegate' => $delegate, 'loggedin' => $loggedin,
				'edit_self_link' => $edit_self_link));
		}
	}
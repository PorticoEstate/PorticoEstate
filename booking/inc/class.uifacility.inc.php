<?php
	phpgw::import_class('booking.uicommon');

	class booking_uifacility extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'add' => true,
			'edit' => true,
		);


		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bofacility');
			self::set_active_menu('booking::applications::facility');
			$this->fields = array('name', 'active');
		}


		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uifacility.index', 'phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 0),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'active',
							'label' => lang('Active')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);

			if ($this->bo->allow_create())
			{
				$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uifacility.add'));
			}
			$data['datatable']['actions'][] = array();

			self::render_template_xsl('datatable_jquery', $data);
		}


		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');
			if ($order)
			{
				$sort = $columns[$order[0]['column']]['data'];
				$dir = $order[0]['dir'];
			}
			else
			{
				$sort = 'name';
				$dir = 'asc';
			}

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $sort,
				'dir' => $dir,
			);

			$facilities = $this->bo->populate_grid_data($params);
			array_walk($facilities['results'], array($this, '_add_links'), 'booking.uifacility.edit');

			return $this->jquery_results($facilities);
		}


		public function add()
		{
			$facility = array();
			$errors = array();
			$tabs = array();
			$tabs['generic'] = array('label' => lang('New facility'), 'link' => '#facility_add');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$facility = extract_values($_POST, $this->fields);
				$errors = $this->bo->validate($facility);
				if (!$errors)
				{
					try
					{
						$receipt = $this->bo->add($facility);
						$this->redirect(array('menuaction' => 'booking.uifacility.index'));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}

			$this->flash_form_errors($errors);
			$facility['cancel_link'] = self::link(array('menuaction' => 'booking.uifacility.index'));
			$facility['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$facility['validator'] = phpgwapi_jquery::formvalidator_generate(array());

			self::render_template_xsl('facility_new', array('facility' => $facility));
		}


		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			$facility = $this->bo->read_single($id);
			$errors = array();
			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit facility'), 'link' => '#facility_edit');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$facility = array_merge($facility, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($facility);
				if (!$errors)
				{
					try
					{
						$receipt = $this->bo->update($facility);
						$this->redirect(array('menuaction' => 'booking.uifacility.index'));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$this->flash_form_errors($errors);
			$facility['cancel_link'] = self::link(array('menuaction' => 'booking.uifacility.index'));
			$facility['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$facility['validator'] = phpgwapi_jquery::formvalidator_generate(array());

			self::render_template_xsl('facility_edit', array('facility' => $facility));
		}

	}

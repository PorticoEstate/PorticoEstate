<?php
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('bim.bobimitem');
	phpgw::import_class('bim.sobimitem');
	/*
	 * This class serves as the 'Controller' or 'Container' in a dependancy injection context
	 */

	interface uibimitem
	{

		public function showItems();

		public function showBimItem();
	}

	class bim_uibimitem extends phpgwapi_uicommon_jquery implements uibimitem
	{

		private $db;

		public function __construct()
		{
			parent::__construct();
			$this->bocommon = CreateObject('property.bocommon');

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->db = & $GLOBALS['phpgw']->db;
		}

		public $public_functions = array
			(
			'showItems' => true,
			'showBimItem' => true
		);

		function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array
				(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'filter' => $this->filter,
				'allrows' => phpgw::get_var('length', 'int') == -1,
				'status_id' => phpgw::get_var('status_id')
			);


			$modelId = phpgw::get_var("modelId");
			if(empty($modelId))
			{
				$bimItems = array();
			}
			else
			{
				$sobimitem = new sobimitem_impl($this->db);
				$sobimitem->setModelId($modelId);
				$bobimitem = new bobimitem_impl();
				$bobimitem->setSobimitem($sobimitem);
				$items = $bobimitem->fetchItemsByModelId();
				$bimItems = array();
				$count = count(($items));
				foreach($items as $bimItem)
				{
					/* @var $bimItem BimItem */
					array_push($bimItems, $bimItem->transformObjectToArray());
				}
				/*
				  $data = array
				  (
				  'someData' => "data",
				  'modelId' => $modelId,
				  'count' => $count,
				  'bimItems' => array("item" => $bimItems)
				  );
				 */
			}
			$results['results'] = $bimItems;
			$results['total_records'] = count(($items));
			$results['start'] = $params['start'];
			$results['sort'] = 'databaseId';
			$results['dir'] = $params['sort'] ? $params['sort'] : 'ASC';
			$results['draw'] = $draw;

			return $this->jquery_results($results);
		}

		public function showItems()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'datatable_name' => lang('Objects'),
				'js_lang' => js_lang('edit', 'add'),
				'form' => array(
				/* 		'toolbar' => array(
				  'item' => array(
				  array(
				  'type'	 => 'link',
				  'value'	 => lang('new'),
				  'href'	 => self::link(array('menuaction' => 'bim.uibim.upload')),
				  'class'	 => 'new_item'
				  ),
				  )
				  ), */
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'bim.uibimitem.showItems', 'modelId' => phpgw::get_var("modelId"),
						'phpgw_return_as' => 'json')),
					'ungroup_buttons' => true,
					'allrows' => true,
					'field' => array(
						array(
							'key' => 'databaseId',
							'label' => lang('Database id'),
							'sortable' => true,
						//	'formatter' => 'formatLinkPending'
						),
						array(
							'key' => 'guid',
							'label' => lang('guid'),
							'sortable' => true,
						//	'formatter' => 'formatLinkPending'
						),
						array(
							'key' => 'type',
							'label' => lang('type'),
							'sortable' => true
						)
					)
				)
			);


			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'modelGuid',
						'source' => 'guid'
					),
				)
			);


			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'bim.uibimitem.showBimItem'
				)),
				'parameters' => json_encode($parameters)
			);

			self::render_template_xsl(array('datatable_jquery'), $data);
		}

		public function showBimItem()
		{
			$modelGuid = phpgw::get_var("modelGuid");
			if(empty($modelGuid))
			{
				echo "No guid!";
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('bim_showSingleItem'));
				$sobimitem = new sobimitem_impl($this->db);
				/* @var $bimItem BimItem */
				$bimItem = $sobimitem->getBimItem($modelGuid);
				$GLOBALS['phpgw']->xslttpl->set_xml_data($bimItem->getXml());
				$this->setupBimCss();
			}
		}

		private function testTemplate()
		{
			phpgw::import_class('phpgwapi.template_portico');
			$app = $GLOBALS['phpgw_info']['flags']['currentapp'];

			$GLOBALS['phpgw']->template->set_root("C:\\vBoxShare\\html\\dev-bim2\\bim\\templates\\portico");
			$GLOBALS['phpgw']->template->set_unknowns('remove');
			$GLOBALS['phpgw']->template->set_file('test', 'test.tpl');
			$tpl_vars = array
				(
				'test2' => "myTest"
			);

			$GLOBALS['phpgw']->template->set_var($tpl_vars);
			$GLOBALS['phpgw']->template->pfp('out', 'test');
			unset($tpl_vars);
		}

		private function setupBimCss()
		{
			if(!isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			$GLOBALS['phpgw']->css->add_external_file('bim/templates/base/css/bim.css');
		}
	}
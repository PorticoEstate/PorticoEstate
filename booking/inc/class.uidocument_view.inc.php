<?php
	phpgw::import_class('booking.uicommon');

	class booking_uidocument_view extends booking_uicommon
	{

		protected
			$module;
		public
			$public_functions = array(
			'query' => true,
			'regulations' => true,
			'download' => true,
		);

		public function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('booking.bodocument_view');
			$this->url_prefix = 'booking.uidocument_view';
			$this->module = 'booking';
		}

		public function link_to( $action, $params = array() )
		{
			return $this->link($this->link_to_params($action, $params));
		}

		public function redirect_to( $action, $params = array() )
		{
			return $this->redirect($this->link_to_params($action, $params));
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
				$ui = 'document_view';
			}

			$action = sprintf($this->module . '.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}

		public function download()
		{
			if ($id = phpgw::get_var('id', 'string'))
			{
				$document = $this->bo->read_single(urldecode($id));
				self::send_file($document['filename'], array('filename' => $document['name']));
			}
		}

		public function regulations()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('booking', 'base', 'datatable.js');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
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
					'source' => $this->link_to('regulations', array('phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'formatter' => 'JqueryPortico.formatLink',
						),
						array(
							'key' => 'link',
							'hidden' => true
						),
					)
				)
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public static function sort_by_params( $a, $b )
		{
			static $dir, $key;
			if (!isset($dir))
			{
				!($dir = phpgw::get_var('dir', 'string', null)) AND $dir = 'asc';
				!($sort = phpgw::get_var('sort', 'string', null)) AND $sort = 'name';
			}

			$retVal = strcmp($a[$sort], $b[$sort]);
			return ($dir == 'desc' ? -$retVal : $retVal);
		}

		public function query()
		{
			$documents = $this->bo->read_regulations();

			foreach ($documents['results'] as &$document)
			{
				$document['link'] = $this->link_to('download', array('id' => $document['id']));
				$document['name'] = isset($document['description']) && strlen(trim($document['description'])) > 0 ?
					$document['description'] : $document['name'];
			}

			//Resort because the sorting order from the database may have been screwed up above
			//when choosing between name and description of document
			usort($documents['results'], array(self, 'sort_by_params'));

			return $this->jquery_results($documents);
		}
	}
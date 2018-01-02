<?php
	phpgw::import_class('frontend.uicommon');

	class frontend_uimessages extends frontend_uicommon
	{

		public $public_functions = array
			(
			'index' => true
		);

		public function __construct()
		{
			$extra_tabs = array();
			$extra_tabs[0] = array(
				'label' => lang('messages'),
				'link' => $GLOBALS['phpgw']->link('/', array('menuaction' => "frontend.uimessages.index",
					'noframework' => $noframework))
			);
			phpgwapi_cache::session_set('frontend', 'extra_tabs', $extra_tabs);
			phpgwapi_cache::session_set('frontend', 'tab', 0);
			parent::__construct();
		}

		public function index()
		{
			$form_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uimessages.index'));

			$message_id = phpgw::get_var('message_id', 'int', 'REQUEST');
			$bomessenger = CreateObject('messenger.bomessenger');

			if (isset($message_id))
			{
				//en enkelt melding
				$message_id = phpgw::get_var('message_id', 'int', 'REQUEST', 0);
				$message = $bomessenger->read_message($message_id);
			}

			// Liste over meldinger
			$params = array
				(
				'start' => $start,
				'order' => 'message_date',
				'sort' => 'DESC'
			);
			$messages = $bomessenger->read_inbox($params);

			$data = array(
				'header' => $this->header_state,
				'section' => array(
					'form_action' => $form_action,
					'message' => $messages,
					'view' => $message,
					'tabs' => $this->tabs,
					'tabs_content' => $this->tabs_content,
					'tab_selected' => $this->tab_selected
				),
			);

			self::render_template_xsl(array('messages', 'datatable_inline', 'frontend'), $data);
		}

		public function query()
		{

		}
	}
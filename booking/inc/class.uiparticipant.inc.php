<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiparticipant extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'show' => true,
			'edit' => true,
			'delete' => true,
			'toggle_show_inactive' => true,
		);
		protected $module, $account;

		public function __construct()
		{
			parent::__construct();
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo = CreateObject('booking.boparticipant');
//			self::set_active_menu('booking::organizations::participants');

			$this->module = "booking";
			$this->display_name = lang('participant');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$this->display_name}";
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
			return $inlineParams['filter_reservation_id'];
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
				$ui = 'participant';
				$this->apply_inline_params($params);
			}

			$action = sprintf($this->module . '.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}

		public function apply_inline_params( &$params )
		{
			if ($this->is_inline())
			{
				$params['filter_reservation_id'] = intval(phpgw::get_var('filter_reservation_id'));
			}
			return $params;
		}

		public function get_inline_params()
		{
			return array('filter_reservation_id' => phpgw::get_var('filter_reservation_id', 'int', 'REQUEST'));
		}

		public function is_inline()
		{
			return false != phpgw::get_var('filter_reservation_id', 'int', 'REQUEST');
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
		}

		public function query()
		{
			$participants = $this->bo->read();

			$lang_yes = lang('yes');
			$lang_no = lang('no');

			array_walk($participants["results"], array($this, "_add_links"), $this->module . ".uiparticipant.show");
			foreach ($participants["results"] as &$participant)
			{
				$participant['active'] = $participant['active'] == 1 ? $lang_yes : $lang_no;
			}
			$results = $this->jquery_results($participants);

			return $results;
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');

		}

		public function show()
		{
		}

		public function delete()
		{
			$id = phpgw::get_var('id', 'int');
			if( $this->bo->delete($id) )
			{
				return lang('participant %1 has been deleted', $id);
			}
			else
			{
				return lang('delete failed');
			}

		}
	}
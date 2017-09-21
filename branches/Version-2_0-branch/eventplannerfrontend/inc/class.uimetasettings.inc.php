<?php
/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this metasettings was funded by http://www.bergen.kommune.no/
	 * @package eventplanner
	 * @subpackage metasettings
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('eventplannerfrontend', 'metasettings', 'inc/model/');

	class eventplannerfrontend_uimetasettings extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'index' => true,
		);

		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('metasettings');
			$this->fields = eventplanner_metasettings::get_fields();
			$this->permissions = eventplanner_metasettings::get_instance()->get_permission_array();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::metasettings");
		}

		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			$appname = phpgw::get_var('appname');
			$appname = $appname ? $appname : $this->currentapp;
			$config = CreateObject('phpgwapi.config', $appname);
			$config->read();

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$metasettings = new eventplanner_metasettings();
				$this->populate($metasettings);

				foreach ($this->fields as $field	=> $field_info)
				{
					if(($field_info['action'] & PHPGW_ACL_ADD) ||  ($field_info['action'] & PHPGW_ACL_EDIT))
					{
						$value = $metasettings->$field;
						if (strlen(trim($value)) > 0)
						{
							$config->value($field, $value);
						}
						else
						{
							unset($config->config_data[$field]);
						}

					}
				}

				$config->save_repository();
			}

			$tabs = array();
			$tabs['meta'] = array('label' => lang('metadata settings'), 'link' => '#meta');
			$active_tab = 'meta';

			$meta['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::rich_text_editor('field_frontpage_text');
			self::rich_text_editor('application_condition');
			self::rich_text_editor('user_agreement_text_1');
			self::rich_text_editor('user_agreement_text_2');

			self::render_template_xsl('metasettings', array('config_data' => $config->config_data,
				'meta' => $meta));
		}


		public function populate(&$object)
		{
			foreach ($this->fields as $field	=> $field_info)
			{
				if(($field_info['action'] & PHPGW_ACL_ADD) ||  ($field_info['action'] & PHPGW_ACL_EDIT))
				{
						$object->set_field( $field, phpgw::get_var($field, $field_info['type'] ) );
				}
			}
		}
	}
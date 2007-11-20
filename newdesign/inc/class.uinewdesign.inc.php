<?php
	/**
	* phpGroupWare - DEMO: a demo aplication.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage demo
 	* @version $Id: class.uidemo.inc.php,v 1.7 2006/12/27 11:04:41 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package demo
	 */

	class newdesign_uinewdesign
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'  => True,
			'grid'  => True,
			'project'   => True,
			'edit'   => True,
			'delete' => True,
			'no_access'=> true
		);

		function newdesign_uinewdesign()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->menu				= CreateObject($this->currentapp.'.menu');
			$this->menu->sub		='newdesign';
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location 	= '.demo_location';
			/*
			$this->cats				= CreateObject('phpgwapi.categories');
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject($this->currentapp.'.bodemo',true);
			$this->menu				= CreateObject($this->currentapp.'.menu');
			$this->menu->sub		='demo';
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location 	= '.demo_location';
			$this->acl_read 			= $this->acl->check($this->acl_location,PHPGW_ACL_READ);
			$this->acl_add 				= $this->acl->check($this->acl_location,PHPGW_ACL_ADD);
			$this->acl_edit 			= $this->acl->check($this->acl_location,PHPGW_ACL_EDIT);
			$this->acl_delete 			= $this->acl->check($this->acl_location,PHPGW_ACL_DELETE);

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
			$this->cat_id			= $this->bo->cat_id;
			$this->filter			= $this->bo->filter;
			*/
		}
		function index() {
			$output = "html";

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->add_external_file('newdesign/js/yahoo/yui/build/assets/skins/sam/calendar.css');
			$GLOBALS['phpgw']->css->add_external_file('newdesign/js/yahoo/yui/build/assets/skins/sam/tabview.css');

			//function validate_file($package, $file, $app='phpgwapi')
			if ( !isset($GLOBALS['phpgw']->js) || !is_object($GLOBALS['phpgw']->js) )
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'yahoo-dom-event', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'calendar-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'utilities', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tabview-min', $this->currentapp );

			$GLOBALS['phpgw']->js->validate_file( 'newdesign', 'form', $this->currentapp );


			$data = array
			(
				'form' => array
				(
					'title' => 'Add Contact',
					'action' => "testaction",
					'fieldset' => array
					(
						array(
							'title' => 'Basic',
							'field' => array
							(
								array
								(
									'title' => lang('Firstname'),
									'accesskey' => 'F',
									'tooltip' => 'Please enter your name',
									'required' => true
									/*'error' => 'This field can not be empty!'	*/
								),
								array
								(
									'title' => lang('Lastname'),
									'accesskey' => 'L',
									'name' => 'lastname',
									'tooltip' => 'Here you should input the tooltip'
								),
								array
								(
									'title' => lang('Username'),
									'accesskey' => 'U',
									'name' => 'username',
									'required' => true
								),
								array
								(
									'title' => lang('Password'),
									'accesskey' => 'P',
									'name' => 'password',
									'type' => 'password',
									'maxlength' => 8,
									'required' => true
								)
							)
						),
						array
						(
							'title' => 'Advanced',
							'field' => array
							(
								array
								(
									'title' => 'Birthday',
									'value' => '12/12/2007',
									'tooltip' => 'Enter your birthday',
									'type' => 'date',
									'required' => 'true'
								),
								array
								(
									'title' => 'Password',
									'password' => 'Password',
									'type' => 'password'
								),
								array
								(
									'title' => 'Readonly',
									'tooltip' => 'You can only read this one',
									'readonly' => true,
									'value' => 'This is readonly',
									'error' => 'This is readonly'
								),
								array
								(
									'title' => 'disabled',
									'disabled' => true,
									'value' => 'disabled'
								),
								array
								(
									'title' => 'Spam?',
									'type' => 'checkbox',
									'tooltip' => 'Do you want spam?',
									'value' => 'checked'
								),
								array
								(
									'title' => 'Textarea',
									'type' => 'textarea'
								)
							)
						),
						array
						(
							'title' => 'Last one',
							'field' => array
							(
								array
								(
									'title' => lang('Another one')
								)
							)
						)
					)
				)
			);

			$this->menu->sub = $output;
			$links = $this->menu->links();

			$GLOBALS['phpgw']->xslttpl->add_file(array('common', 'form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $data);
			//$GLOBALS['phpgw']->xslttpl->set_xml("<test></test>");
		}

		function grid()
		{
			if(!is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->add_external_file($GLOBALS['phpgw_info']['server']['webserver_url'] . '/newdesign/js/yahoo/yui/build/datatable/assets/datatable-core.css');
			$GLOBALS['phpgw']->css->add_external_file($GLOBALS['phpgw_info']['server']['webserver_url'] . '/newdesign/js/yahoo/yui/build/assets/skins/sam/datatable.css');

			//function validate_file($package, $file, $app='phpgwapi')
			if(!isset($GLOBALS['phpgw']->js) || !is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'yahoo-dom-event', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'element-beta-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'datasource-beta-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'connection-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'dragdrop-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'calendar-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'datatable-beta-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'newdesign', 'grid', $this->currentapp );
			//$GLOBALS['phpgw']->js->set_onload( 'init_grid();' );

			$this->bocommon			= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db->query("SELECT fm_location2.location_code,fm_location2.loc1,fm_location2.loc2,fm_location1.loc1_name,fm_location2.loc2_name ,fm_location2.status,fm_location2.remark,fm_location2.rental_area FROM ((( fm_location2 JOIN fm_location1 ON (fm_location2.loc1 = fm_location1.loc1)) JOIN fm_owner ON ( fm_location1.owner_id=fm_owner.id)) JOIN fm_part_of_town ON ( fm_location1.part_of_town_id=fm_part_of_town.part_of_town_id)) WHERE (fm_location2.category !=99 OR fm_location2.category IS NULL) LIMIT 10");

			$datatable = array();
			$i=0;
			while ($this->db->next_record()) {
				foreach ($this->db->resultSet->fields as $key => $value) {
					if(is_string($key)) {
						if($i==0) {
							$datatable['grid']['column_defs']['column'][] = array
							(
								'key' => $key,
								'label' => $key,
								'formater' => 'text',
								'sortable' => true
							);
						}
						$datatable['grid']['rows'][$i]['data'][] = $value;
					}
				}
				$i++;
			}
			/*
			$datatable['grid']['column_defs']['column'][0]['label'] = "Property";
			$datatable['grid']['column_defs']['column'][2]['label'] = "Location name";
			*/
			$GLOBALS['phpgw']->xslttpl->add_file(array('common', 'grid'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $datatable);
		}
		function project() {
			$output = "html";

			if(!is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->add_external_file($GLOBALS['phpgw_info']['server']['webserver_url'] . '/newdesign/js/yahoo/yui/build/assets/skins/sam/calendar.css');
			$GLOBALS['phpgw']->css->add_external_file($GLOBALS['phpgw_info']['server']['webserver_url'] . '/newdesign/js/yahoo/yui/build/assets/skins/sam/tabview.css');

			//function validate_file($package, $file, $app='phpgwapi')
			if(!isset($GLOBALS['phpgw']->js) || !is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'yahoo-dom-event', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'calendar-min', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'utilities', $this->currentapp );
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'tabview-min', $this->currentapp );

			$GLOBALS['phpgw']->js->validate_file( 'newdesign', 'form', $this->currentapp );

			$data = array
			(
				'form' => array
				(
					'title' => 'Add Project',
					'action' => "testaction",
					'tabbed' => true,
					'fieldset' => array
					(
						array(
							'title' => lang('General'),
							'field' => array
							(
								array
								(
									'title' => lang('Name'),
									'required' => true
								),
								array
								(
									'title' => lang('Description'),
									'type' => 'textarea',
									'cols' => 60
								),
								array
								(
									'title' => lang('Category'),
									//'type' => 'select',
									'required' => true,
									'datasource' => array
									(
									)
								),
								array
								(
									'title' => lang('Status'),
									//'type' => 'select',
									'required' => true,
									'datasource' => array
									(
									)
								)
							)
						),
						array
						(
							'title' => lang('Location'),
							'field' => array
							(
								array
								(
									'title' => lang('Contact phone')
								),
								array
								(
									'title' => lang('Power meter')
								)
							)
						),
						array
						(
							'title' => lang('Time and budget'),
							'field' => array
							(
								array
								(
									'title' => lang('Project start date'),
									'type' => 'date'
								),
								array
								(
									'title' => lang('Project end date'),
									'type' => 'date'
								),
								array
								(
									'title' => lang('Vendor'),
									'required' => true
								),

								array
								(
									'title' => lang('Budget account'),
									'required' => true
								),
								array
								(
									'title' => lang('Budget')
								),
								array
								(
									'title' => lang('Reserve')
								),
								array
								(
									'title' => lang('Sum'),
									'readonly' => true
								)
							)
						),
						array
						(
							'title' => lang('Coordintaion')
						),
						array
						(
							'title' => lang('Extra'),
							'field' => array
							(
								'title' => lang('Remark'),
								'type' => 'textarea',
								'cols' => 60
							)
						),
						array
						(
							'title' => lang('History')
						)
					)
				)
			);

			$this->menu->sub = $output;
			$links = $this->menu->links();

			$GLOBALS['phpgw']->xslttpl->add_file(array('common', 'form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $data);

		}
	}

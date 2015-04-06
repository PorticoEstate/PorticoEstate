<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage budget
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');
	
	class property_uibudget extends phpgwapi_uicommon_jquery
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
			(
				'index'			=> true,
				'query'			=> true,
				'basis'			=> true,
				'query_basis'	=> true,
				'obligations'	=> true,
				'get_filters_dependent' => true,
				'view'			=> true,
				'edit'			=> true,
				'add'			=> true,
				'edit_basis'	=> true,
				'add_basis'		=> true,
				'download'		=> true,
				'delete'		=> true,
				'delete_basis'	=> true
			);
		
		function __construct()
		{
			parent::__construct();
			
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::budget';

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.bobudget',true);
			$this->bocommon		= & $this->bo->bocommon;
			$this->cats			= & $this->bo->cats;

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->dimb_id		= $this->bo->dimb_id;
			$this->allrows		= $this->bo->allrows;
			$this->district_id	= $this->bo->district_id;
			$this->year			= $this->bo->year;
			$this->month		= $this->bo->month;
			$this->grouping		= $this->bo->grouping;
			$this->revision		= $this->bo->revision;
			$this->details		= $this->bo->details;
			$this->direction	= $this->bo->direction;

			$this->acl 			= & $GLOBALS['phpgw']->acl;

		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'			=> $this->start,
					'query'			=> $this->query,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'filter'		=> $this->filter,
					'cat_id'		=> $this->cat_id,
					'dimb_id'		=> $this->dimb_id,
					'allrows'		=> $this->allrows,
					'direction'		=> $this->direction,
					'month'			=> $this->month
				);
			$this->bo->save_sessiondata($data);
		}

		private function _get_filters($selected = 0)
		{
			$link = self::link(array(
					'menuaction' => 'property.uibudget.get_filters_dependent',
					'phpgw_return_as' => 'json'
					));

			$code = '
				var link = "'.$link.'";
				var data = {"year": $(this).val()};
				clearFilterParam("revision");
				clearFilterParam("grouping");
				
				execute_ajax(link,
					function(result){
						var $el_revision = $("#revision");
						$el_revision.empty();
						$.each(result.revision, function(key, value) {
						  $el_revision.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
						var $el_grouping = $("#grouping");
						$el_grouping.empty();
						$.each(result.grouping, function(key, value) {
						  $el_grouping.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
					}, data, "GET", "json"
				);
				';
			
			$values_combo_box[0]  = $this->bo->get_year_filter_list($this->year);
			array_unshift ($values_combo_box[0], array('id'=>'','name'=>lang('no year')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'year',
								'extra' => $code,
								'text'  => lang('year'),
								'list'  => $values_combo_box[0]
							);

			$values_combo_box[1]  = $this->bo->get_revision_filter_list($this->revision);
			array_unshift ($values_combo_box[1], array ('id'=>'','name'=>lang('no revision')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'revision',
								'extra' => '',
								'text'  => lang('revision'),
								'list'  => $values_combo_box[1]
							);
			
			$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
			array_unshift ($values_combo_box[2], array('id'=>'','name'=>lang('no district')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'district_id',
								'extra' => '',
								'text'  => lang('district'),
								'list'  => $values_combo_box[2]
							);


			$values_combo_box[3] =  $this->bo->get_grouping_filter_list($this->grouping);
			array_unshift ($values_combo_box[3], array('id'=>'','name'=>lang('no grouping')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'grouping',
								'extra' => '',
								'text'  => lang('grouping'),
								'list'  => $values_combo_box[3]
							);

			$cat_filter =  $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True,'link_data' => $link_data));
			foreach($cat_filter['cat_list'] as $_cat)
			{
				$values_combo_box[4][] = array
				(
					'id' => $_cat['cat_id'],
					'name' => $_cat['name'],
					'selected' => $_cat['selected'] ? 1 : 0
				);
			}
			array_unshift ($values_combo_box[4], array('id'=>'','name'=>lang('no category')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'cat_id',
								'extra' => '',
								'text'  => lang('Category'),
								'list'  => $values_combo_box[4]
							);
			
			$values_combo_box[5]  = $this->bocommon->select_category_list(array('type'=>'dimb'));
			foreach($values_combo_box[5] as & $_dimb)
			{
				$_dimb['name'] = "{$_dimb['id']}-{$_dimb['name']}";
			}
			array_unshift ($values_combo_box[5], array('id'=>'','name'=>lang('no dimb')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'dimb_id',
								'extra' => '',
								'text'  => lang('dimb'),
								'list'  => $values_combo_box[5]
							);
			
			return $combos;
		}
		
		private function _get_filters_basis($selected = 0)
		{
			$basis = true;
			
			$link = self::link(array(
					'menuaction' => 'property.uibudget.get_filters_dependent',
					'phpgw_return_as' => 'json'
					));

			$code = '
				var link = "'.$link.'";
				var data = {"year": $(this).val(), "basis":1};
				clearFilterParam("revision");
				clearFilterParam("grouping");
				
				execute_ajax(link,
					function(result){
						var $el_revision = $("#revision");
						$el_revision.empty();
						$.each(result.revision, function(key, value) {
						  $el_revision.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
						var $el_grouping = $("#grouping");
						$el_grouping.empty();
						$.each(result.grouping, function(key, value) {
						  $el_grouping.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
					}, data, "GET", "json"
				);
				';
			
			$values_combo_box[0]  = $this->bo->get_year_filter_list($this->year, $basis);
			array_unshift ($values_combo_box[0], array('id'=>'','name'=>lang('no year')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'year',
								'extra' => $code,
								'text'  => lang('year'),
								'list'  => $values_combo_box[0]
							);

			$values_combo_box[1]  = $this->bo->get_revision_filter_list($this->revision, $basis);
			array_unshift ($values_combo_box[1], array ('id'=>'','name'=>lang('no revision')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'revision',
								'extra' => '',
								'text'  => lang('revision'),
								'list'  => $values_combo_box[1]
							);
			
			$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
			array_unshift ($values_combo_box[2], array('id'=>'','name'=>lang('no district')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'district_id',
								'extra' => '',
								'text'  => lang('district'),
								'list'  => $values_combo_box[2]
							);


			$values_combo_box[3] =  $this->bo->get_grouping_filter_list($this->grouping, $basis);
			array_unshift ($values_combo_box[3], array('id'=>'','name'=>lang('no grouping')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'grouping',
								'extra' => '',
								'text'  => lang('grouping'),
								'list'  => $values_combo_box[3]
							);
			
			
			$values_combo_box[4]  = $this->bocommon->select_category_list(array('type'=>'dimb'));
			array_unshift ($values_combo_box[4], array('id'=>'','name'=>lang('no dimb')));
			$combos[] = array
							(
								'type'  => 'filter',
								'name'  => 'dimb_id',
								'extra' => '',
								'text'  => lang('dimb'),
								'list'  => $values_combo_box[4]
							);
			
			return $combos;
		}
		
		
		function get_filters_dependent()
		{
			$basis = phpgw::get_var('draw', 'bool');
			
			$revision  = $this->bo->get_revision_filter_list($this->revision, $basis);
			array_unshift ($revision, array ('id'=>'','name'=>lang('no revision')));
			
			$grouping  = $this->bo->get_grouping_filter_list($this->grouping, $basis);
			array_unshift ($grouping, array ('id'=>'','name'=>lang('no grouping')));
			
			return $result = array('revision'=>$revision, 'grouping'=>$grouping);
		}
		
		function index()
		{
			$acl_location	= '.budget';
			$acl_read 		= $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

            if( phpgw::get_var('phpgw_return_as') == 'json' )
            {
				return $this->query();
            }
			
			$acl_add		= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit		= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete 	= $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::budget';

            $data   = array(
                'datatable_name'    => lang('list budget'),
                'form'  => array(
                               'toolbar'    => array(
                                   'item'   => array()
								)
                            ),
                'datatable' =>  array(
                    'source'    => self::link(array(
							'menuaction'		=> 'property.uibudget.index',
							'phpgw_return_as'   => 'json'
                    )),
					'download'	=> self::link(array(
							'menuaction'	=> 'property.uibudget.download',
							'export'		=> true,
							'allrows'		=> true
					)),
                    'allrows'		=> true,
                    'editor_action' => '',
                    'field'			=>  array()
                )
            );
			
            $filters = $this->_get_Filters();
			krsort($filters);
            foreach($filters as $filter){
                array_unshift($data['form']['toolbar']['item'], $filter);
            }
			
			if($acl_add)
			{
				$data['form']['toolbar']['item'][] = array
					(
						'type'   => 'link',
						'value'  => lang('new'),
						'href'   => self::link(array(
							'menuaction'	=> 'property.uibudget.add'
						)),
						'class'  => 'new_item'
					);
			}

			$uicols = array (
				array('hidden'=>true,'key'=>'budget_id','label'=>'dummy','sortable'=>false),
				array('hidden'=>false,'key'=>'year','label'=>lang('year'),'className'=>'center','sortable'=>false),
				array('hidden'=>false,'key'=>'revision','label'=>lang('revision'),'className'=>'center','sortable'=>false),
				array('hidden'=>false,'key'=>'b_account_id','label'=>lang('budget account'),'className'=>'center','sortable'=>false),
				array('hidden'=>false,'key'=>'b_account_name','label'=>lang('name'),'sortable'=>false),
				array('hidden'=>false,'key'=>'grouping','label'=>lang('grouping'),'className'=>'right','sortable'=>true),
				array('hidden'=>false,'key'=>'district_id','label'=>lang('district'),'className'=>'right','sortable'=>true),
				array('hidden'=>false,'key'=>'ecodimb','label'=>lang('dimb'),'className'=>'right', 'sortable'=>true),
				array('hidden'=>false,'key'=>'category','label'=>lang('category'),'sortable'=>false),
				array('hidden'=>false,'key'=>'budget_cost','label'=>lang('budget cost'),'className'=>'right','sortable'=>true,'formatter'=>'JqueryPortico.FormatterAmount0'),
			);

            foreach ($uicols as $col) 
			{
                array_push($data['datatable']['field'], $col);
            }

			$parameters = array('parameter' => array(array('name'=> 'budget_id', 'source'=> 'budget_id')));
			
			$data['datatable']['actions'][] = array(
				'my_name'		=> 'edit',
				'text' 			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uibudget.edit')),
				'parameters'	=> json_encode($parameters)
			);

			$data['datatable']['actions'][] = array(
				'my_name'		=> 'delete',
				'text' 			=> lang('delete'),
				'confirm_msg'	=> lang('do you really want to delete this entry'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uibudget.delete')),
				'parameters'	=> json_encode($parameters)
			);

			/*if($acl_add)
			{
				$data['datatable']['actions'][] = array(
					'my_name'	=> 'add',
					'text' 		=> lang('add'),
					'action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit'))
				);
			}*/
			unset($parameters);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			phpgwapi_jquery::load_widget('numberformat');
			
			self::add_javascript('property', 'portico', 'budget.index.js');
			self::render_template_xsl('datatable_jquery',$data);
		}

        public function query_basis()
        {
            $search			= phpgw::get_var('search');
			$order			= phpgw::get_var('order');
			$draw			= phpgw::get_var('draw', 'int');
			$columns		= phpgw::get_var('columns');
			
            $params = array(
                'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1
            );
			
			$values = $this->bo->read_basis($params);	

            if( phpgw::get_var('export','bool'))
            {
                return $values;
            }
            
            $result_data = array('results'  => $values);
            $result_data['total_records'] = $this->bo->total_records;
            $result_data['draw'] = $draw;
			$result_data['sum_budget'] = number_format($this->bo->sum_budget_cost, 0, ',', ' ');
            
            return $this->jquery_results($result_data);
		}		
		
		
		function basis()
		{
			$acl_location	= '.budget';
			$acl_read 		= $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

            if( phpgw::get_var('phpgw_return_as') == 'json' )
            {
				return $this->query_basis();
            }
			
			$acl_add		= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit		= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete 	= $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			/*$revision_list	= $this->bo->get_revision_filter_list($this->revision,$basis=true); // reset year
			$this->year		= $this->bo->year;
			$this->revision = $this->bo->revision;*/
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::basis';

            $data   = array(
                'datatable_name'    => lang('list budget'),
                'form'  => array(
                               'toolbar'    => array(
                                   'item'   => array()
								)
                            ),
                'datatable' =>  array(
                    'source'    => self::link(array(
							'menuaction'		=> 'property.uibudget.basis',
							'phpgw_return_as'   => 'json'
                    )),
					'download'	=> self::link(array(
							'menuaction'	=> 'property.uibudget.download',
							'export'		=> true,
							'allrows'		=> true
					)),
                    'allrows'		=> true,
                    'editor_action' => '',
                    'field'			=>  array()
                )
            );
			
            $filters = $this->_get_Filters_basis();
			krsort($filters);
            foreach($filters as $filter){
                array_unshift($data['form']['toolbar']['item'], $filter);
            }

			if($acl_add)
			{
				$data['form']['toolbar']['item'][] = array
					(
						'type'   => 'link',
						'value'  => lang('new'),
						'href'   => self::link(array(
							'menuaction'	=> 'property.uibudget.add_basis'
						)),
						'class'  => 'new_item'
					);
			}

			$uicols = array (
				array('hidden'=>true,'key'=>'budget_id','label'=>'dummy','sortable'=>false),
				array('hidden'=>false,'key'=>'year','label'=>lang('year'),'className'=>'center','sortable'=>false),
				array('hidden'=>false,'key'=>'revision','label'=>lang('revision'),'className'=>'center','sortable'=>false),
				array('hidden'=>false,'key'=>'grouping','label'=>lang('grouping'),'className'=>'right','sortable'=>true),
				array('hidden'=>false,'key'=>'district_id','label'=>lang('district_id'),'className'=>'right','sortable'=>true),
				array('hidden'=>false,'key'=>'ecodimb','label'=>lang('dimb'),'className'=>'right','sortable'=>true),
				array('hidden'=>false,'key'=>'category','label'=>lang('category'),'className'=>'right','sortable'=>false),
				array('hidden'=>false,'key'=>'budget_cost','label'=>lang('budget_cost'),'className'=>'right','sortable'=>true,'formatter'=>'JqueryPortico.FormatterAmount0')
			);

            foreach ($uicols as $col) 
			{
                array_push($data['datatable']['field'], $col);
            }

			//$datatable['rowactions']['action'] = array();

			$parameters = array('parameter' => array(array('name'=> 'budget_id', 'source'=> 'budget_id')));

			$data['datatable']['actions'][] = array(
				'my_name'		=> 'edit',
				'text' 			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uibudget.edit_basis')),
				'parameters'	=> json_encode($parameters)
			);

			$data['datatable']['actions'][] = array(
				'my_name'		=> 'delete',
				'text' 			=> lang('delete'),
				'confirm_msg'	=> lang('do you really want to delete this entry'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'property.uibudget.delete_basis')),
				'parameters'	=> json_encode($parameters)
			);

			/*if($acl_add)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name'		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit_basis'))
				);
			}*/
			
			unset($parameters);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			phpgwapi_jquery::load_widget('numberformat');
			
			self::render_template_xsl('datatable_jquery',$data);
		}


        public function query()
        {
            $search			= phpgw::get_var('search');
			$order			= phpgw::get_var('order');
			$draw			= phpgw::get_var('draw', 'int');
			$columns		= phpgw::get_var('columns');
			
            $params = array(
                'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1
            );
			
			$values = $this->bo->read($params);	

            if( phpgw::get_var('export','bool'))
            {
                return $values;
            }
            
            $result_data = array('results'  => $values);
            $result_data['total_records'] = $this->bo->total_records;
            $result_data['draw'] = $draw;
			$result_data['sum_budget'] = number_format($this->bo->sum_budget_cost, 0, ',', ' ');
            
            return $this->jquery_results($result_data);
		}	
		
		
		function obligations()
		{
			//$this->allrows = 1;
			$acl_location	= '.budget.obligations';
			$acl_read 	= $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add 	= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 	= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete 	= $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::obligations';

			$datatable = array();
			$values_combo_box = array();
			$dry_run = false;
			$this->save_sessiondata();
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uibudget.obligations',
						'cat_id'		=> $this->cat_id,
						'filter'		=> $this->filter,
						'query'			=> $this->query,
						'district_id'	=> $this->district_id,
						'grouping'		=> $this->grouping,
						'year'			=> $this->year,
						'month'			=> $this->month,
						'details'		=> $this->details,
						'allrows'		=> $this->allrows,
						'dimb_id'		=> $this->dimb_id,
						'direction'		=> $this->direction
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uibudget.obligations',"
					."cat_id: '{$this->cat_id}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}',"
					."district_id:'{$this->district_id}',"
					."grouping:'{$this->grouping}',"
					."year:'{$this->year}',"
					."month:'{$this->month}',"
					."details:'{$this->details}',"
					."dimb_id:'{$this->dimb_id}',"
					."direction:'{$this->direction}',"
					."allrows:'{$this->allrows}',"
					."download:'obligations'";

				$values_combo_box[0]  = $this->bo->get_year_filter_list($this->year,$basis=false);
				$default_value = array ('id'=>'','name'=>lang('no year'));
				array_unshift ($values_combo_box[0],$default_value);



				for ($i=1;$i< 13 ;$i++)
				{
					$values_combo_box[1][] = array ('id'=> $i,'name'=> sprintf("%02s",$i));
				}

				array_unshift ($values_combo_box[1], array ('id'=>'','name'=>lang('month')));

				$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[2],$default_value);

//_debug_array($values_combo_box[2]);

				$values_combo_box[3] =  $this->bo->get_b_group_list($this->grouping);
				$default_value = array ('id'=>'','name'=>lang('no grouping'));
				array_unshift ($values_combo_box[3],$default_value);

				$cat_filter =  $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True,'link_data' => $link_data));
				foreach($cat_filter['cat_list'] as $_cat)
				{
					$values_combo_box[4][] = array
					(
						'id' => $_cat['cat_id'],
						'name' => $_cat['name'],
						'selected' => $_cat['selected'] ? 1 : 0
					);
				}

				array_unshift ($values_combo_box[4],array ('id'=>'', 'name'=>lang('no category')));


				$values_combo_box[5]  = $this->bocommon->select_category_list(array('type'=>'org_unit'));
				array_unshift ($values_combo_box[5], array ('id'=>'','name'=>lang('department')));

				$values_combo_box[6]  = $this->bocommon->select_category_list(array('type'=>'dimb'));
				foreach($values_combo_box[6] as & $_dimb)
				{
					$_dimb['name'] = "{$_dimb['id']}-{$_dimb['name']}";
				}
				$default_value = array ('id'=>'','name'=>lang('no dimb'));
				array_unshift ($values_combo_box[6],$default_value);


				$values_combo_box[7]  = array
				(
					array
					(
						'id' => 'expenses',
						'name'	=> lang('expenses'),
						'selected'	=> $this->direction == 'expenses' ? 1 : 0
					),
					array
					(
						'id' => 'income',
						'name'	=> lang('income'),
						'selected'	=> $this->direction == 'income' ? 1 : 0
					)
				);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uibudget.obligations',
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	YEAR
									'id'		=> 'btn_year',
									'name'		=> 'year',
									'value'		=> lang('year'),
									'type'		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	YEAR
									'id'		=> 'btn_month',
									'name'		=> 'month',
									'value'		=> lang('month'),
									'type'		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	DISTRICT
									'id' 		=> 'btn_district_id',
									'name' 		=> 'district_id',
									'value'		=> lang('district_id'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	GROUPING
									'id' 		=> 'btn_grouping',
									'name' 		=> 'grouping',
									'value'		=> lang('grouping'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 4
								),
								array
								(
									'id' => 'sel_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[4],
									'onchange'=> 'onChangeSelect("cat_id");',
									'tab_index' => 5
								),
								array
								( 
									'id' => 'sel_org_unit_id',
									'name' => 'org_unit_id',
									'value'	=> lang('department'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[5],
									'onchange'=> 'onChangeSelect("org_unit_id");',
									'tab_index' => 6
								),
								array
								( 
									'id' => 'sel_dimb_id',
									'name' => 'dimb_id',
									'value'	=> lang('dimb'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[6],
									'onchange'=> 'onChangeSelect("dimb_id");',
									'tab_index' => 7
								),
								array
								(
									'id' => 'sel_direction',
									'name' => 'direction',
									'value'	=> lang('direction'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[7],
									'onchange'=> 'onChangeSelect("direction");',
									'tab_index' => 8
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 11
								),
								array
								( //boton     SEARCH
									'id' 		=> 'btn_search',
									'name' 		=> 'search',
									'value'    	=> lang('search'),
									'type' 		=> 'button',
									'tab_index' => 10
								),
								array
								( // TEXT IMPUT
									'name'     	=> 'query',
									'id'     	=> 'txt_query',
									'value'    	=> $this->query,
									'type' 		=> 'text',
									'size'    	=> 28,
									'onkeypress'=> 'return pulsar(event)',
									'tab_index' => 9
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								),								array
								( //div values  combo_box_3
									'id' => 'values_combo_box_3',
									'value'	=> $this->bocommon->select2String($values_combo_box[3])
								)
							)
						)
					)
				);

				$dry_run = true;
			}

			$uicols = array (

				array(
					'col_name'=>'grouping',		'visible'=>false,	'label'=>'',				'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'b_account',		'visible'=>true,	'label'=>lang('grouping'),	'className'=>'centerClasss',	'sortable'=>true,	'sort_field'=>'b_account',	'formatter'=>'myformatLinkPGW'),
//				array(
//					'col_name'=>'district_id',	'visible'=>true,	'label'=>lang('district_id'),'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'ecodimb',		'visible'=>true,	'label'=>lang('dimb'),	'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'hits_ex',		'visible'=>false,	'label'=>'',				'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'hits',			'visible'=>true,	'label'=>lang('hits'),		'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'budget_cost_ex',	'visible'=>false,	'label'=>'',				'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'budget_cost',	'visible'=>true,	'label'=>lang('budget'),	'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'obligation_ex',	'visible'=>false,	'label'=>''					,'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'obligation',		'visible'=>true,	'label'=>lang('sum orders'),'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>'myFormatLink_Count'),
				array(
					'col_name'=>'link_obligation','visible'=>false,	'label'=>'',				'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'actual_cost_ex',	'visible'=>false,	'label'=>'',				'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'actual_cost_period',	'visible'=>true,	'label'=>lang('paid') . ' ' . lang('period'),		'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'actual_cost',	'visible'=>true,	'label'=>lang('paid'),		'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>'myFormatLink_Count'),
				array(
					'col_name'=>'link_actual_cost','visible'=>false,	'label'=>'',				'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'diff_ex',		'visible'=>false,	'label'=>'',				'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'diff',			'visible'=>true,	'label'=>lang('difference'),'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'percent',			'visible'=>true,	'label'=>lang('percent'),'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>'')
				);


			//FIXME
			if($dry_run)
			{
				$location_list = array();

			}
		//	else
			{
				$location_list = $this->bo->read_obligations();
			}

			//_debug_array($location_list);

			$entry = $content = array();
			$j = 0;
			//cramirez: add this code because  "mktime" functions fire an error
			if($this->year == "")
			{
				$today = getdate();
				$this->year = $today['year'];
			}

			if (isset($location_list) && is_array($location_list))
			{
				$details = $this->details ? false : true;

				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,1,1,$this->year),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$end_date	= $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,12,31,$this->year),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				$sum_obligation = $sum_hits = $sum_budget_cost = $sum_actual_cost = 0;
				foreach($location_list as $entry)
				{
					$content[] = array
						(
							'grouping'			=> $entry['grouping'],
							'b_account'			=> $entry['b_account'],
							'district_id'		=> $entry['district_id'],
							'ecodimb'			=> $entry['ecodimb'],
							'hits_ex'			=> $entry['hits'],
							'hits'				=> number_format($entry['hits'], 0, ',', ' '),
							'budget_cost_ex'	=> $entry['budget_cost'],
							'budget_cost'		=> number_format($entry['budget_cost'], 0, ',', ' '),
							'obligation_ex'		=> $entry['obligation'],
							'obligation'		=> number_format($entry['obligation'], 0, ',', ' '),
							'link_obligation'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index', 'filter'=>'all', 'paid'=>1, 'district_id'=> $entry['district_id'], 'b_group'=> $entry['grouping'], 'b_account' =>$entry['b_account'], 'start_date'=> $start_date, 'end_date'=> $end_date, 'ecodimb' => $entry['ecodimb'], 'status_id' => 'all', 'obligation' => true)),
							'actual_cost_ex'	=> $entry['actual_cost'],
							'actual_cost_period'=> number_format($entry['actual_cost_period'], 0, ',', ' '),
							'actual_cost'		=> number_format($entry['actual_cost'], 0, ',', ' '),
							'link_actual_cost'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.consume', 'district_id'=> $entry['district_id'], 'b_account_class'=> $entry['grouping'], 'b_account' =>$entry['b_account'],  'start_date'=> $start_date, 'end_date'=> $end_date, 'ecodimb' => $entry['ecodimb'], 'submit_search'=>true)),
							'diff_ex'			=> $entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'],
							'diff'				=> number_format($entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'], 0, ',', ' '),
							'percent'			=> $entry['percent']
						);
				}

			}

			$j=0;
			if (isset($content) && is_array($content))
			{
				foreach($content as $budget)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols[$i]['col_name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']		= $budget[$uicols[$i]['col_name']];
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['name']			= $uicols[$i]['col_name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['label'];
				$datatable['headers']['header'][$i]['visible'] 		= $uicols[$i]['visible'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['formatter']	= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
			}

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($location_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[1]['col_name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'sum_budget'		=> $this->bo->sum_budget_cost,
					'sum_obligation'	=> $this->bo->sum_obligation_cost,
					'sum_actual'		=> $this->bo->sum_actual_cost,
					'sum_actual_period'	=> $this->bo->sum_actual_cost_period,
					'sum_diff'			=> $this->bo->sum_budget_cost - $this->bo->sum_actual_cost - $this->bo->sum_obligation_cost,
					'sum_hits'			=> $this->bo->sum_hits
				);

			// values for datatable
			$json_row = array();
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					foreach( $row['column'] as $column)
					{
						$json_row[$column['name']] = $column['value'];
					}
					$json['records'][] = $json_row;
				}
			}
			// right in datatable
			$json ['rights'] = $datatable['rowactions']['action'];

			//				$json ['sum_hits'] 			= number_format($sum_hits, 0, ',', ' ');
			//				$json ['sum_budget_cost']	= number_format($sum_budget_cost, 0, ',', ' ');
			//				$json ['sum_obligation']	= number_format($sum_obligation, 0, ',', ' ');
			//				$json ['sum_actual_cost']	= number_format($sum_actual_cost, 0, ',', ' ');
			//				$json ['sum_diff'] 			= number_format($sum_diff, 0, ',', ' ');

			//_debug_array($json);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list obligations');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'budget.obligations', 'property' );
		}

		public function add()
		{
			$this->edit();
		}
		
		function edit()
		{
			$acl_location	= '.budget';
			$acl_add 	= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 	= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $acl_location));
			}

			$budget_id	= phpgw::get_var('budget_id', 'int');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget'));

			$receipt = array();
			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
				$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');
				$values['ecodimb']			= phpgw::get_var('ecodimb');

				if(!$values['b_account_id'] > 0)
				{
					$values['b_account_id']='';
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if(!$values['district_id'] && !$budget_id > 0)
				{
		//			$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['budget_cost'])
				{
//					$receipt['error'][]=array('msg'=>lang('Please enter a budget cost !'));
				}

				if(!isset($receipt['error']) || !$receipt['error'])
				{
					$values['budget_id']	= $budget_id;
					$receipt = $this->bo->save($values);
					$budget_id = $receipt['budget_id'];

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','budget_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.index'));
					}
				}
				else
				{
					$year_selected = $values['year'];
					$district_id = $values['district_id'];
					$revision = $values['revision'];

					$values['year'] ='';
					$values['district_id'] = '';
					$values['revision'] = '';
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.index'));
			}


			if ($budget_id)
			{
				$values = $this->bo->read_single($budget_id);
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uibudget.edit',
					'budget_id'	=> $budget_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'		=> $values['b_account_id'],
				'b_account_name'	=> isset($values['b_account_name'])?$values['b_account_name']:'',
				'type'			=> isset($values['b_account_id']) && $values['b_account_id'] > 0 ?'view':'form'));

			$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array(
				'ecodimb'			=> $values['ecodimb'],
				'ecodimb_descr'		=> $values['ecodimb_descr']));

			$data = array
				(
					'ecodimb_data'					=>	$ecodimb_data,
					'lang_category'					=> lang('category'),
					'lang_no_cat'					=> lang('Select category'),
					'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
					'b_account_data'				=> $b_account_data,
					'value_b_account'				=> $values['b_account_id'],
					'lang_revision'					=> lang('revision'),
					'lang_revision_statustext'		=> lang('Select revision'),
					'revision_list'					=> $this->bo->get_revision_list($values['revision']),

					'lang_year'						=> lang('year'),
					'lang_year_statustext'			=> lang('Budget year'),
					'year'							=> $this->bocommon->select_list($values['year']?$values['year']:date('Y'),$this->bo->get_year_list()),

					'lang_district'					=> lang('District'),
					'lang_no_district'				=> lang('no district'),
					'lang_district_statustext'		=> lang('Select the district'),
					'select_district_name'			=> 'values[district_id]',
					'district_list'					=> $this->bocommon->select_district_list('select',$values['district_id']),

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_budget_id'				=> lang('ID'),
					'value_budget_id'				=> $budget_id,
					'lang_budget_cost'				=> lang('budget cost'),
					'lang_remark'					=> lang('remark'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'value_remark'					=> $values['remark'],
					'value_budget_cost'				=> $values['budget_cost'],
					'lang_name_statustext'			=> lang('Enter a name for the query'),
					'lang_remark_statustext'		=> lang('Enter a remark'),
					'lang_apply_statustext'			=> lang('Apply the values'),
					'lang_cancel_statustext'		=> lang('Leave the budget untouched and return to the list'),
					'lang_save_statustext'			=> lang('Save the budget and return to the list'),


				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id?lang('edit budget'):lang('add budget'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

		}

		
		public function add_basis()
		{
			$this->edit_basis();
		}
		
		function edit_basis()
		{

			$acl_location	= '.budget';
			$acl_add 	= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 	= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $acl_location));
			}

			$budget_id	= phpgw::get_var('budget_id', 'int');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget'));

			if ((isset($values['save']) && $values['save'])|| (isset($values['apply']) && $values['apply']))
			{
				$values['ecodimb']	= phpgw::get_var('ecodimb');

				if(!$values['b_group'] && !$budget_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget group !'));
				}


				if(!$values['district_id'] && !$budget_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['budget_cost'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a budget cost !'));
				}

				if(!$receipt['error'])
				{
					$values['budget_id']	= $budget_id;
					$receipt = $this->bo->save_basis($values);
					$budget_id = $receipt['budget_id'];

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','budget_basis_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.basis'));
					}
				}
				else
				{
					$year_selected = $values['year'];
					$district_id = $values['district_id'];
					$revision = $values['revision'];
					$b_group = $values['b_group'];

					unset ($values['year']);
					unset ($values['district_id']);
					unset ($values['revision']);
					unset ($values['b_group']);
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.basis'));
			}

			if ($budget_id)
			{
				$values = $this->bo->read_single_basis($budget_id);
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uibudget.edit_basis',
					'budget_id'	=> $budget_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$year[0]['id'] = date(Y);
			$year[1]['id'] = date(Y) +1;
			$year[2]['id'] = date(Y) +2;
			$year[3]['id'] = date(Y) +3;

			$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array(
				'ecodimb'			=> $values['ecodimb'],
				'ecodimb_descr'		=> $values['ecodimb_descr']));


			$data = array
				(
					'ecodimb_data'						=>	$ecodimb_data,
					'lang_category'						=> lang('category'),
					'lang_no_cat'						=> lang('Select category'),
					'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
					'lang_distribute'					=> lang('distribute'),
					'lang_distribute_year'				=> lang('distribute year'),
					'lang_distribute_year_statustext'	=> lang('of years'),
					'distribute_year_list'				=> $this->bo->get_distribute_year_list($values['distribute_year']),

					'lang_revision'						=> lang('revision'),
					'lang_revision_statustext'			=> lang('Select revision'),
					'revision_list'						=> $this->bo->get_revision_list($revision),

					'lang_b_group'						=> lang('budget group'),
					'lang_b_group_statustext'			=> lang('Select budget group'),
					'b_group_list'						=> $this->bo->get_b_group_list($b_group),

					'lang_year'							=> lang('year'),
					'lang_year_statustext'				=> lang('Budget year'),
					'year'								=> $this->bocommon->select_list($year_selected,$year),

					'lang_district'						=> lang('District'),
					'lang_no_district'					=> lang('no district'),
					'lang_district_statustext'			=> lang('Select the district'),
					'select_district_name'				=> 'values[district_id]',
					'district_list'						=> $this->bocommon->select_district_list('select',$district_id),

					'value_year'						=> $values['year'],
					'value_district_id'					=> $values['district_id'],
					'value_b_group'						=> $values['b_group'],
					'value_revision'					=> $values['revision'],

					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_budget_id'					=> lang('ID'),
					'value_budget_id'					=> $budget_id,
					'value_distribute_id'				=> $budget_id?$budget_id:'new',
					'lang_budget_cost'					=> lang('budget cost'),
					'lang_remark'						=> lang('remark'),
					'lang_save'							=> lang('save'),
					'lang_cancel'						=> lang('cancel'),
					'lang_apply'						=> lang('apply'),
					'value_remark'						=> $values['remark'],
					'value_budget_cost'					=> $values['budget_cost'],
					'lang_name_statustext'				=> lang('Enter a name for the query'),
					'lang_remark_statustext'			=> lang('Enter a remark'),
					'lang_apply_statustext'				=> lang('Apply the values'),
					'lang_cancel_statustext'			=> lang('Leave the budget untouched and return to the list'),
					'lang_save_statustext'				=> lang('Save the budget and return to the list'),
				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id?lang('edit budget'):lang('add budget'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_basis' => $data));

		}
		function delete()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int');
			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($budget_id);
				return "budget_id ".$budget_id." ".lang("has been deleted");
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uibudget.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete', 'budget_id'=> $budget_id)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname		= lang('budget');
			$function_msg		= lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}

		function delete_basis()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int');
			//JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete_basis($budget_id);
				return "budget_id ".$budget_id." ".lang("has been deleted");
			}



			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uibudget.basis'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_basis($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete_basis', 'budget_id'=> $budget_id)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname	= lang('budget');
			$function_msg	= lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}
		function view()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int', 'GET');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget','nextmatchs'));

			$list= $this->bo->read_budget($budget_id);
			$uicols	= $this->bo->uicols;

			//_debug_array($uicols);

			$j=0;
			if (isSet($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$content[$j]['row'][$i]['value'] = $entry[$uicols[$i]['name']];
					}

					$j++;
				}
			}

			for ($i=0;$i<count($uicols);$i++)
			{
				$table_header[$i]['header'] 	= $uicols[$i]['descr'];
				$table_header[$i]['width'] 	= '15%';
				$table_header[$i]['align'] 	= 'left';
			}

			//_debug_array($content);


			$budget_name = $this->bo->read_budget_name($budget_id);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . $budget_name;

			$link_data = array
				(
					'menuaction'	=> 'property.uibudget.view',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'budget_id'	=>$budget_id,
					'filter'	=>$this->filter,
					'query'		=>$this->query
				);


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_download = array
				(
					'menuaction'	=> 'property.uibudget.download',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'filter'	=>$this->filter,
					'query'		=>$this->query,
					'budget_id'	=>$budget_id,
					'allrows'	=> $this->allrows
				);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
				(
					'lang_download'					=> 'download',
					'link_download'					=> $GLOBALS['phpgw']->link('/index.php',$link_download),
					'lang_download_help'			=> lang('Download table to your browser'),

					'allow_allrows'					=> true,
					'allrows'						=> $this->allrows,
					'start_record'					=> $this->start,
					'record_limit'					=> $record_limit,
					'num_records'					=> count($list),
					'all_records'					=> $this->bo->total_records,
					'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
					'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
					'lang_searchbutton_statustext'	=> lang('Submit the search string'),
					'query'							=> $this->query,
					'lang_search'					=> lang('search'),
					'table_header'					=> $table_header,
					'values'						=> $content,

					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.index')),
					'lang_done'						=> lang('done'),
				);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		function download()
		{
			switch (phpgw::get_var('download'))
			{
				case 'basis':
					$list= $this->bo->read_basis();
					$names = array
					(
						'year',
						'revision',
						'grouping',
						'district_id',
						'ecodimb',
						'category',
						'budget_cost'
					);
					$descr = array
					(
						lang('year'),
						lang('revision'),
						lang('grouping'),
						lang('district_id'),
						lang('dimb'),
						lang('category'),
						lang('budget')
					);
					break;
				case 'budget':
					$list= $this->bo->read();
					$names = array
					(
						'year',
						'revision',
						'b_account_id',
						'b_account_name',
						'grouping',
						'district_id',
						'ecodimb',
						'category',
						'budget_cost'
						);
					$descr = array
					(
						lang('year'),
						lang('revision'),
						lang('budget account'),
						lang('name'),
						lang('grouping'),
						lang('district_id'),
						lang('dimb'),
						lang('category'),
						lang('budget')
					);
					break;
				case 'obligations':

					$gross_list= $this->bo->read_obligations();
					$sum_obligation = $sum_hits = $sum_budget_cost = $sum_actual_cost = 0;
					$list = array();
					foreach($gross_list as $entry)
					{
						$list[] = array
						(
							'grouping'			=> $entry['grouping'],
							'b_account'			=> $entry['b_account'],
							'district_id'		=> $entry['district_id'],
							'ecodimb'			=> $entry['ecodimb'],
							'hits'				=> $entry['hits'],
							'budget_cost'		=> $entry['budget_cost'],
							'obligation'		=> $entry['obligation'],
							'actual_cost_period'=> $entry['actual_cost_period'],
							'actual_cost'		=> $entry['actual_cost'],
							'diff'				=> ($entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation']),
						);
					}
					$names = array
					(
						'grouping',
						'b_account',
						'district_id',
						'ecodimb',
						'hits',
						'budget_cost',
						'obligation',
						'actual_cost_period',
						'actual_cost',
						'diff'
					);
					$descr = array
					(
						lang('grouping'),
						lang('budget account'),
						lang('district_id'),
						lang('dimb'),
						lang('hits'),
						lang('budget'),
						lang('sum orders'),
						lang('paid') . ' ' . lang('period'),
						lang('paid'),
						lang('difference')
					);
					break;
				default:
					return;
			}

			if($list)
			{
				$this->bocommon->download($list,$names,$descr);
			}
		}
	}


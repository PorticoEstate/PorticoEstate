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
   * @version $Id$
  */

  phpgw::import_class('phpgwapi.yui');

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
      'location'	=> true,
      'index'		=> true,
      'grid'		=> true,
      'project'	=> true,
      'edit'		=> true,
      'delete'	=> true,
      'no_access'	=> true,
      'datatable' => true,
      'datatable_json' => true,
      'property'	=> true
    );

	function location()
	{
		$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/datatable/assets/datatable-core.css');
      	$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/datatable.css');

      	$GLOBALS['phpgw']->js->validate_file( 'newdesign', 'location', $this->currentapp );

      	phpgwapi_yui::load_widget('element');
      	phpgwapi_yui::load_widget('connection');
      	phpgwapi_yui::load_widget('dragdrop');
      	phpgwapi_yui::load_widget('calendar');
		phpgwapi_yui::load_widget('datatable');

      	$type_id 		= phpgw::get_var('type_id', 'int', 'REQUEST', 1);
      	$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'int', 'REQUEST', false);
      	$lookup 		= false;
      	$this->allrows 	= false;

      	$this->bo 		= CreateObject('property.bolocation',True);
      	$location_list 	= $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>$this->allrows));
		$uicols 		= $this->bo->uicols;

		$output = phpgw::get_var('phpgw_return_as', 'string') ? phpgw::get_var('phpgw_return_as', 'string') : 'html';

		if($output == "html")
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'newdesign::location::location_loc_' . $type_id;

			if($lookup_tenant)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "_${lookup_tenant}";
			}

			//var_dump($uicols);
			$data = array();
			$columnDefs = array();
			for($i=0;$i<count($uicols['name']);$i++)
			{
				//$columnDefs['column'][$i]['input_type'] = $uicols['input_type'][$i];
				$columnDefs['column'][$i]['name'] = $uicols['name'][$i];
				$columnDefs['column'][$i]['descr'] = $uicols['descr'][$i];
				//$columnDefs['column'][$i]['statustext'] = $uicols['statustext'][$i];
				/* 'exchange', 'align', 'datatype' */
			}

			$data['locationDataTable']['datasrouce_url'] = $GLOBALS['phpgw']->link('/index.php', array(
				'menuaction'		=> 'newdesign.uinewdesign.location',
				'type_id'			=> $type_id,
				'phpgw_return_as'	=> 'json',
				'lookup_tenant'		=> $lookup_tenant
			));

			$data['locationDataTable']['type_id'] = $type_id;
			$data['locationDataTable']['columns'] = $columnDefs;

			// Get filter data for Category, District, Part of town and owner
			$this->socommon 		= CreateObject('property.socommon', true);
			$this->so 				= CreateObject('property.solocation');

			$data['filter'] = array();
			$i=0;
			//$this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' =>'location','type_id' =>$type_id,'order'=>'descr')),

			$data['filter'][$i]['id'] = 'category';
			$data['filter'][$i]['name'] = 'cat_id';
			$data['filter'][$i]['title'] = 'Category';
			$data['filter'][$i]['descr'] = lang('Select the category the location belongs to. To do not use a category select NO CATEGORY');
			$data['filter'][$i]['selected'] = $this->bo->cat_id;
			//$data['filter'][$i]['options'][] =

			$socategory = CreateObject('property.socategory');
			$categories = $socategory->select_category_list( array(
				'type'=> 'location',
				'type_id' => 1
			));
			$empty[] = array( 'id'	=>	'', "name"	=> lang('no category') );
			$data['filter'][$i]['options'] = array_merge($empty, $categories);
			unset($empty);

			$i++;
			$data['filter'][$i]['id'] = 'district';
			$data['filter'][$i]['name'] = 'district_id';
			$data['filter'][$i]['title'] = 'District';
			$data['filter'][$i]['descr'] = lang('Select the district the selection belongs to. To do not use a district select NO DISTRICT');
			$data['filter'][$i]['selected'] = $this->bo->district_id;
			$empty[] = array( 'id'	=>	'', "name"	=> lang('no district') );
			$data['filter'][$i]['options'] = array_merge($empty, $this->socommon->select_district_list());
			unset($empty);

			$i++;
			$data['filter'][$i]['id'] = 'part_of_town';
			$data['filter'][$i]['name'] = 'part_of_town_id';
			$data['filter'][$i]['title'] = 'Part of town';
			$data['filter'][$i]['descr'] = lang('Select the part of town the selection belongs to. To do not use a part of town select NO PART OF TOWN');
			$data['filter'][$i]['selected'] = $this->bo->part_of_town_id;
			$empty[] = array( 'id'	=>	'', "name"	=> lang('no part of town') );
			$data['filter'][$i]['options'] = array_merge($empty, $this->socommon->select_part_of_town($district_id));
			unset($empty);

			$i++;
			$data['filter'][$i]['id'] = 'owner';
			$data['filter'][$i]['name'] = 'filter';
			$data['filter'][$i]['title'] = 'Owner';
			$data['filter'][$i]['descr'] = lang('Select the owner type. To show all entries select SHOW ALL');
			$data['filter'][$i]['selected'] = $this->bo->filter;
			$empty[] = array( 'id'	=>	'', "name"	=> lang('Show all') );
			$data['filter'][$i]['options'] = array_merge($empty, $this->so->get_owner_type_list());
			unset($empty);

			$GLOBALS['phpgw']->xslttpl->add_file(array('location'));
      		$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $data);
      		//var_dump ($GLOBALS['phpgw']->xslttpl->xml_parse());
		}
		else
		{
			//recordsReturned should come from bo
			//TODO: totalRecords should be int no mather what...
			$data = array(
                'recordsReturned'	=>	count($location_list),
                'totalRecords'		=>	(int)$this->bo->total_records,
                'startIndex'		=> 	(int)$this->bo->start,
                'sort'				=> 	$this->bo->order,
      			'sort_dir'			=> 	$this->bo->sort,
				'query'				=>  $this->bo->query,
				'part_of_town_id'	=>	$this->bo->part_of_town_id,
				'cat_id'			=>	$this->bo->cat_id,
				'district_id'		=>	$this->bo->district_id,
				'filter'			=>	$this->bo->filter,
                'records'			=> 	$location_list
      		);
			return $data;
		}
	}

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
	function property()
	{
		$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'newdesign::property';

		if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
      	{
        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
      	}

      	$data=array();

      	$loc1 = phpgw::get_var('loc1', 'string');

      	$cmd = phpgw::get_var('cmd', 'string') ? phpgw::get_var('cmd', 'string') : 'view';

      	if(!$loc1 && $cmd != 'new')
      	{
      		$data['error'] = "No location supplied";
      	}
		else {
			$this->bocommon = CreateObject('property.bocommon');
      		$this->db = $this->bocommon->new_db();

			$query = "SELECT * FROM fm_location1 WHERE loc1 = '" . $loc1 . "'";

			$this->db->query( $query );

      		if( !$this->db->next_record() )
      		{
				$data['error'] = "Location: " . $loc1 . " not found";

      		}
      		else
      		{
      			$record = array();
      			foreach ($this->db->resultSet->fields as $key => $value) {
          			if(is_string($key)) {
               			$record[$key] = $value;
          			}
        		}
        		var_dump($record);
        		/*
        		 	property
					name
					category
					part of town
					owner
					status
					remark
					mva
					kostra_id
					rental area
        		 */
        		$data['cmd']['form']=array
        		(
              		'field' => array
              		(
                		array
                		(
							'title' => 'Property',
                			'value' => $record['location_code'],
							'tooltip' => 'Please enter property code',
							'required' => true
                  			/*'error' => 'This field can not be empty!'	*/
                		),
                		array
                		(
                  			'title' => 'Name',
                			'value' => $record['loc1_name'],
                  			'name' => 'lastname',
                  			'required' => true
                  			//'tooltip' => 'Here you should input the tooltip'
                		),
                		array
                		(
                  			'title' => 'Category',
                			'value' => $record['category'],
                  			'name' => 'lastname',
                  			'required' => true
                  			//'tooltip' => 'Here you should input the tooltip'
                		),
                		array
                		(
                  			'title' => 'Part of town',
                  			'name' => 'lastname',
                			'required' => true
                  			//'tooltip' => 'Here you should input the tooltip'
                		),


                	)
                );
      		}
		}



      	$output = "html";

      	$this->menu->sub = $output;
      	$links = $this->menu->links();

      	$GLOBALS['phpgw']->xslttpl->add_file(array('property'));
      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $data);
		var_dump ($GLOBALS['phpgw']->xslttpl->xml_parse());
	}
    function datatable()
    {
      $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'newdesign::datatable';
      if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
      {
        $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
      }

      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/datatable/assets/datatable-core.css');
      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/datatable.css');

      $GLOBALS['phpgw']->js->validate_file( 'newdesign', 'datatable', $this->currentapp );

      phpgwapi_yui::load_widget('element');
      phpgwapi_yui::load_widget('connection');
      phpgwapi_yui::load_widget('dragdrop');
      phpgwapi_yui::load_widget('calendar');
      phpgwapi_yui::load_widget('datatable');

      $output = "html";
      $data=array();
      $this->menu->sub = $output;
      $links = $this->menu->links();

      $GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
      $GLOBALS['phpgw']->xslttpl->set_var('phpgw', $data);
    }

    function datatable_json()
    {
      $sort_dir = phpgw::get_var('sort_dir', 'string') ? phpgw::get_var('sort_dir', 'string') : 'asc';
      $sort = phpgw::get_var('sort', 'string') ? phpgw::get_var('sort', 'string') : 'loc1';
      $start_offset	= phpgw::get_var('start_offset', 'int') ? phpgw::get_var('start_offset', 'int') : 0;
	  $limit_records = phpgw::get_var('limit_records', 'int') ? phpgw::get_var('limit_records', 'int') : 30;

      $this->bocommon = CreateObject('property.bocommon');
      $this->db = $this->bocommon->new_db();

      $this->db->query( "SELECT count(loc1) as total_records FROM fm_location1");
      if( $this->db->next_record() )
      {
        $total_records = (int)$this->db->resultSet->fields['total_records']-1;
      }
      else
      {
      	$total_records = 0;
      }

      $query = "SELECT loc1, loc1_name, fm_owner.org_name as owner_name, fm_location1.remark as remark,
       			fm_part_of_town.name as town_name, fm_location1_category.descr as category_descr, user_id, status
				FROM fm_location1
				JOIN fm_owner ON fm_location1.owner_id=fm_owner.id
				JOIN fm_part_of_town ON fm_location1.part_of_town_id=fm_part_of_town.part_of_town_id
				JOIN fm_location1_category ON fm_location1.category=fm_location1_category.id
				ORDER BY $sort $sort_dir";

      $this->db->limit_query($query, $start_offset, '100', 'class.uinewdesign.inc.php', $limit_records);

      $records = array();
      while ($this->db->next_record()) {
        $record=array();
        foreach ($this->db->resultSet->fields as $key => $value) {
          if(is_string($key)) {
               $record[$key] = $value;
          }
        }
        $records[] = $record;
      }

      $data = array(
                'recordsReturned'	=> $this->db->num_rows(),
                'totalRecords'		=> $total_records,
                'startIndex'		=> $start_offset,
                'sort'				=> $sort,
      			'sort_dir'			=> $sort_dir,
                'records'			=> $records
      );

      return $data;
    }
/**
    * TODO document me
    */
    function index()
    {
      $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'newdesign::form';
      $output = "html";

      if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
      {
        $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
      }

	  $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/autocomplete.css');
      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/calendar.css');
      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/tabview.css');

      phpgwapi_yui::load_widget('calendar');
      phpgwapi_yui::load_widget('tabview');
      phpgwapi_yui::load_widget('autocomplete');

      $GLOBALS['phpgw']->js->validate_file( 'newdesign', 'form', $this->currentapp );


      $data = array
      (
        'form' => array
        (
          'title' => 'New Property',
          'action' => "testaction",
          'fieldset' => array
          (
            array(
              'field' => array
              (
                array
                (
                  'title' => 'Property',
                  'accesskey' => 'P',
                  'tooltip' => 'Please enter property code',
                  'required' => true
                  /*'error' => 'This field can not be empty!'*/
                ),
                array
                (
                  'title' => lang('Name'),
                  'accesskey' => 'N',
                  'name' => 'lastname',
                  'tooltip' => 'Please enter property name'
                ),
                array
                (
                  'title' => lang('Category'),
                  'accesskey' => 'C',
                  'name' => 'username',
                  'required' => true
                ),
                array
                (
                  'title' => 'Part of town',
                  'accesskey' => 'a',
                  'name' => 'password',
                  'type' => 'password',
                  'maxlength' => 8,
                  'required' => true
                ),
                array
                (
                  'title' => 'Owner',
                  'accesskey' => 'O',
                  'required' => true
                ),
                array
                (
                	'title' => 'Remark',
                	'accesskey' => 'R',
                	'type' => 'textarea'
                )
              )
            )
            /*
            array
            (
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
			*/
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
      $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'newdesign::grid';
      if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
      {
        $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
      }

      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/datatable/assets/datatable-core.css');
      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/datatable.css');

      phpgwapi_yui::load_widget('element');
      phpgwapi_yui::load_widget('connection');
      phpgwapi_yui::load_widget('dragdrop');
      phpgwapi_yui::load_widget('calendar');
      phpgwapi_yui::load_widget('datatable');

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
      $GLOBALS['phpgw']->xslttpl->add_file(array('common', 'grid'));
      $GLOBALS['phpgw']->xslttpl->set_var('phpgw', $datatable);
    }
    function project()
    {
      $GLOBALS['phpgw_info']['flags']['menu_selection'] = 'newdesign::project';
      $output = "html";

      if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
      {
        $GLOBALS['phpgw']->css = createObject('phpgwapi.css');
      }

      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/calendar.css');
      $GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/build/assets/skins/sam/tabview.css');

      phpgwapi_yui::load_widget('calendar');
      phpgwapi_yui::load_widget('tabview');
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

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
   * @version $Id: class.uinewdesign.inc.php 1433 2008-07-16 12:02:46Z janaage@hikt.no $
  */

  phpgw::import_class('phpgwapi.yui');

  /**
   * Description
   * @package demo
   */

  class equipo_uiequipo
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
      'property'	=> true,
      'gab' => true
    );

        function gab() {

    	$datatable = array();
    	$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array(
			'menuaction'	=> 'equipo.uiequipo.gab'
		));

		// Definition of headers
		$datatable['headers']['header'] = array(
			array(
				'name'		=> 'gab_id',
				'visible'	=> false
			),
			array(
				'name'		=> 'gaards_nr',
				'text'		=> lang('GardsNr'),
				'format'	=> 'number',
				'sortcolumn'=> 'gab_id'
			),
			array(
				'name'		=> 'bruks_nr',
				'text'		=> lang('BruksNr'),
				'sortable'	=> false,
				'format'	=> 'number'
			),
			array(
				'name'		=> 'feste_nr',
				'text'		=> lang('FesteNr'),
				'sortable'	=> false,
				'format'	=> 'number'
			),
			array(
				'name'		=> 'seksjons_nr',
				'text'		=> lang('SeksjonsNr'),
				'sortable'	=> false,
				'format'	=> 'number'
			),
			array(
				'name'		=> 'owner',
				'text'		=> lang('Eier'),
				'sortable'	=> false,
				'format'	=> 'bool'
			),
			array(
				'name'		=> 'location_code',
				'text'		=> lang('Lokalisering'),
				'format'	=> 'number'
			),
			array(
				'name'		=> 'address',
				'text'		=> lang('Adresse'),
				'sortable'	=> false
			)
		);

		// Get rowdata based on query variables
		$reset_query 		= phpgw::get_var('reset_query', 'bool');

		if( !$reset_query )
		{
			$address 			= phpgw::get_var('address');
			$check_payments 	= phpgw::get_var('check_payments', 'bool');
			$location_code 		= phpgw::get_var('location_code');
			$gaards_nr 			= phpgw::get_var('gaards_nr', 'int');
			$bruksnr 			= phpgw::get_var('bruksnr', 'int');
			$feste_nr 			= phpgw::get_var('feste_nr', 'int');
			$seksjons_nr 		= phpgw::get_var('seksjons_nr', 'int');
		}

		//Actions / filters
		$datatable['actions']['form'] = array(
			array(
				'action'	=> $GLOBALS['phpgw']->link('/index.php',
					array(
						'menuaction' => 'property.uilocation.edit',
						'type_id' => '1'
					)
				),
				'fields'	=> array(
					'field' => array(
						array(
							'type' => 'submit',
							'value' => lang('New')
						)
					)
				)
			),

			array(
				'fields'	=> array(
					'field' => array(
						array(
							'id'	=> 'someid',
							'name'	=> 'check_payments',
							'text'	=> lang('Check payments'),
							'type'	=> 'checkbox',
							'value'	=> "1",
							'checked' => $check_payments
						),
						array(
							'name' 	=> 'address',
							'text'	=> lang('Address'),
							'value'	=> $address
						),
						array(
							'name' 	=> 'location_code',
							'text'	=> lang('Property ID'),
							'value'	=> $location_code,
							'size'	=> 4
						),
						array(
							'name' 	=> 'gaards_nr',
							'text'	=> lang('Gaards NR'),
							'value'	=> $gaards_nr,
							'size'	=> 4
						),
						array(
							'name' 	=> 'bruksnr',
							'text'	=> lang('Bruks NR'),
							'value'	=> $bruksnr,
							'size'	=> 4
						),
						array(
							'name' 	=> 'feste_nr',
							'text'	=> lang('Feste NR'),
							'value'	=> $feste_nr,
							'size'	=> 4
						),
						array(
							'name' 	=> 'seksjons_nr',
							'text'	=> lang('Seksjons NR'),
							'value'	=> $seksjons_nr,
							'size'	=> 4
						),
						array(
							'name'	=> 'submit',
							'value'	=> lang('Search'),
							'type'	=> 'submit'
						),
						array(
							'name'	=> 'reset_query',
							'value'	=> lang('Clear'),
							'type'	=> 'submit'
						)
					)
				)
			)
		);

		$this->bo = CreateObject('property.bogab',True);
		$gab_rows = $this->bo->read( $location_code, $gaards_nr, $bruksnr, $feste_nr, $seksjons_nr, $address, $check_payments);

		// Format bo rows for xml output
		for($i=0; $i < count($gab_rows); $i++)
		{
			$row = $gab_rows[$i];
			foreach($gab_rows[$i] as $key => $value)
			{
				if($key == 'gab_id')
				{
					$datatable['rows']['row'][$i]['column'][] = array(
						'name' 	=> 'gaards_nr',
						'value'	=> substr( $value, 4, 5 )
					);
					$datatable['rows']['row'][$i]['column'][] = array(
						'name' 	=> 'bruks_nr',
						'value'	=> substr( $value, 9, 4 )
					);
					$datatable['rows']['row'][$i]['column'][] = array(
						'name' 	=> 'feste_nr',
						'value'	=> substr( $value, 13, 4 )
					);
					$datatable['rows']['row'][$i]['column'][] = array(
						'name' 	=> 'seksjons_nr',
						'value'	=> substr( $value, 17, 3 )
					);
				}
				else if($key == 'owner')
				{
					$value = lang($value);
				}

				$datatable['rows']['row'][$i]['column'][] = array(
					'name' 	=> $key,
					'value' => $value
				);
			}
		}

		// Row actions
		$datatable['rowactions']['action'][] = array(
			'text' 			=> lang('Vis'),
			'action'		=> $GLOBALS['phpgw']->link('/index.php',
				array( 'menuaction' => 'property.uigab.list_detail' )
			),
			'parameters'	=> array
			(
				'parameter' => array(
					array(
						'name'	=> 'gab_id'
					)
				)
			)
		);

		$datatable['rowactions']['action'][] = array(
			'text' 			=> lang('Map'),
			'action'		=> phpgw::safe_redirect('http://www.bergenskart.no/bergen/index.jsp?maptype=Eiendomskart'),
			'parameters'	=> array(
				'parameter' => array(
					array(
						'name'		=> 'gnr',
						'source'	=> 'gaards_nr'
					),
					array(
						'name'		=> 'bnr',
						'source'	=> 'bruks_nr',
					),
					array(
						'name'		=> 'fnr',
						'source'	=> 'feste_nr',
					)
				)
			)
		);

		$datatable['rowactions']['action'][] = array(
			'text' 			=> lang('Gab'),
			'action'		=> phpgw::safe_redirect('http://geodat01/gl_webgab/webgab.aspx?type=eiendom'),
			'parameters'	=> array
			(
				'parameter' => array(
					array(
						'name'	=> 'Gnr',
						'source'=> 'gaards_nr'
					),
					array(
						'name'		=> 'Bnr',
						'source'	=> 'bruks_nr'
					),
					array(
						'name'		=> 'Fnr',
						'source'	=> 'feste_nr'
					),
					array(
						'name'		=> 'Snr',
						'source'	=> 'seksjons_nr'
					)
				)
			)
		);

		// Pagination and sort values
		$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
		$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		$datatable['pagination']['records_returned']= count($gab_rows);
		$datatable['pagination']['records_total'] 	= $this->bo->total_records;

		$datatable['pagination']['lang']['first'] 	= lang('First');
		$datatable['pagination']['lang']['next'] 	= lang('Next');
		$datatable['pagination']['lang']['previous']= lang('Previous');
		$datatable['pagination']['lang']['last'] 	= lang('Last');
		$datatable['pagination']['lang']['overview']= lang('Records $1 - $2 of $3');

		$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
		$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC


    	if( phpgw::get_var('phpgw_return_as') == 'json' ) {
    		$json = array(
    			'recordsReturned' 	=> $datatable['pagination']['records_returned'],
    			'totalRecords' 		=> $datatable['pagination']['records_total'],
    			'recordStartIndex' 	=> $datatable['pagination']['records_start'],
    			'sortKey'			=> $datatable['sorting']['order'],
    			'sortDir'			=> $datatable['sorting']['sort'],
    			'records'			=> array()
    		);

    		foreach( $datatable['rows']['row'] as $row )
    		{
    			$json_row = array();
    			foreach( $row['column'] as $column)
    			{
    				$json_row[$column['name']] = $column['value'];
    			}
    			$json['records'][] = $json_row;
    		}
    		return $json;
		}

		// Prepare template variables and process XSLT
		$template_vars = array();
		$template_vars['datatable'] = $datatable;

		$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

      	if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
      	{
        	$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
      	}

	  	$GLOBALS['phpgw']->css->validate_file('datatable');
		$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');

		phpgwapi_yui::load_widget('dragdrop');
	  	phpgwapi_yui::load_widget('datatable');

      	//phpgwapi_yui::load_widget('tabview');

	  	// Uncomment the following line to enable experimental YUI Datagrid version
	  	//$GLOBALS['phpgw']->js->validate_file( 'newdesign', 'gabnr', $this->currentapp );

      	//echo "<pre>";
      	//echo $GLOBALS['phpgw']->xslttpl->xml_parse();
    }


    function equipo_uiequipo()
    {
      $GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
      $this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
      $this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
      $this->menu				= CreateObject($this->currentapp.'.menu');
      $this->menu->sub		='equipo';
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

  }

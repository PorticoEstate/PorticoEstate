<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.uicontract_documents.inc.php 11377 2013-10-18 08:25:54Z sigurdne $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

    phpgw::import_class('frontend.uifrontend');
    phpgw::import_class('rental.uicontract');
    phpgw::import_class('rental.socontract');
    include_class('rental', 'document', 'inc/model/');

	/**
	 * Drawings
	 *
	 * @package Frontend
	 */

    class frontend_uicontract_documents extends frontend_uifrontend
    {

        public $public_functions = array
        (
            'index'     	=> true,
        );

		public function __construct()
		{
		    $this->contract_state_identifier_doc = "contract_state_in";
		    $this->contracts_per_location_identifier_doc = "contracts_in_per_location";
		    $this->form_url_doc = "index.php?menuaction=frontend.uicontract_documents.index";
			phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.document.contracts'));
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
//			$this->location_code = '1102-01';
		}

		public function index()
		{
		    $contractdata = array();	// This is the main container for all contract data sent to XSLT template stuff
		    $msglog = array();			// Array of errors and other notifications displayed to us
		    
    		$filter = phpgw::get_var('contract_filter');
    		// The user wants to change the contract status filter
    		if(isset($filter)) 
    		{
    				$this->contract_filter_doc = $filter;
    				phpgwapi_cache::session_set('frontend', 'contract_filter_doc', $filter);				
    
    				// ... if the user changes filter that may cause the
    				if($filter == 'active' || $filter == 'not_active')
    				{
    					$change_contract = true;
    				}	
    		}
    		else
    		{
    			$filter = phpgwapi_cache::session_get('frontend', 'contract_filter_doc');
    			$this->contract_filter_doc = isset($filter) ? $filter : 'active';
    		}
    		
			// If the user wants to view another contract connected to this location
    		// Request parameter: the user wants to view details about anther contract
    		// The current state of the contract view of this user's session
    		$this->contract_state_doc = phpgwapi_cache::session_get('frontend', $this->contract_state_identifier_doc);
    		$new_contract = phpgw::get_var('contract_id');
    		$contracts_per_location = phpgwapi_cache::session_get('frontend', $this->contracts_per_location_identifier_doc);
    		$contracts_for_selection = array();
    		$number_of_valid_contracts = 0;
    		foreach($contracts_per_location[$this->header_state['selected_location']] as $contract)
    		{
    			if(	($this->contract_filter_doc == 'active' && $contract->is_active()) ||
    				($this->contract_filter_doc == 'not_active' && !$contract->is_active()) ||
    				$this->contract_filter_doc == 'all'
    			)
    			{
    				$number_of_valid_contracts += 1;
    				//Only select necessary fields
    				$contracts_for_selection[] = array(
    					'id' 				=> $contract->get_id(),
    					'old_contract_id' 	=> $contract->get_old_contract_id(),
    					'contract_status' 	=> $contract->get_contract_status()
    					
    				);

    				if($change_contract || $new_contract == $contract->get_id() || !isset($this->contract_state_doc['contract']))
    				{
    					$this->contract_state_doc['selected'] = $contract->get_id();
    					$this->contract_state_doc['contract'] = $contract;
    					//$this->contract = rental_socontract::get_instance()->get_single($new_contract);
    					phpgwapi_cache::session_set('frontend', $this->contract_state_identifier_doc , $this->contract_state_doc);
    					$change_contract = false;
    				}
    			}			
    		}
    		
    		if($number_of_valid_contracts == 0)
    		{
    			$this->contract_state_doc['selected'] = '';
    			$this->contract_state_doc['contract'] = null;
    		}
		    
			$config	= CreateObject('phpgwapi.config','frontend');
			$config->read();
			//$doc_types = isset($config->config_data['document_frontend_cat']) && $config->config_data['document_frontend_cat'] ? $config->config_data['document_frontend_cat'] : array();	
			$doc_types = array('type' => 1);

			$allrows = true;
			$sodocument	= CreateObject('rental.sodocument');
            $filters = array('contract_id' => $this->contract_state_doc['selected'], 'document_type' => 1);
			$document_list = array();
			$total_records = 0;
			if( $this->location_code )
			{
				foreach ($doc_types as $doc_type)
				{
					if($doc_type)
					{
						$document_list = array_merge($document_list, $sodocument->get($start_index, $num_of_objects, 'id', true, $search_for, $search_type, $filters));
					}

					$total_records = $total_records + $sodocument->get_count($search_for, $search_type, $filters);;
				}
			}
			
			//----------------------------------------------datatable settings--------

			$valid_types = isset($config->config_data['document_valid_types']) && $config->config_data['document_valid_types'] ? str_replace ( ',' , '|' , $config->config_data['document_valid_types'] ) : '';

			$content = array();
			if($valid_types)
			{
				foreach($document_list as $entry)
				{
					if ( !preg_match("/({$valid_types})$/i", $entry->get_name()) )
					{
						continue;
					}

					$content[] = array
					(
						'document_id'			=> $entry->get_id(),
						'document_name'			=> $entry->get_name(),
						'link'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'rental.uidocument.view', 'id' => $entry->get_id())),
						'title'					=> $entry->get_title(),
					    'description'			=> $entry->get_description(),
						'doc_type'				=> lang($entry->get_type()),
					);	
				}
			}
			$datavalues[0] = array
			(
				'name'					=> "0",
				'values' 				=> json_encode($content),
				'total_records'			=> count($content),
				'edit_action'			=> isset($entry) && $entry->get_id() ? json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'rental.uidocument.view', 'id' => $entry->get_id()))):'' ,
				'is_paginator'			=> 1,
				'footer'				=> 0
			);


			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode(array(	array('key' => 'document_name','label'=>lang('filename'),'sortable'=>true,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
													array('key' => 'document_id','label'=>lang('filename'),'sortable'=>false,'hidden' => true),
													array('key' => 'title','label'=>lang('name'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'description','label'=>lang('description'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'doc_type','label'=>'Type','sortable'=>true,'resizeable'=>true)
													))
			);

			//----------------------------------------------datatable settings--------


			$datatable = array
			(
				'property_js'			=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'				=> $datavalues,
				'myColumnDefs'			=> $myColumnDefs
			);

			$data = array
			(
				'header'				=> $this->header_state,
				'tabs'					=> $this->tabs,
    			'contract_data' => 	array (
	    			'select' => $contracts_for_selection, 
		    		'selected_contract' =>  $this->contract_state_doc['selected'], 
			    	'contract'	=> isset($this->contract_state_doc['contract']) ? $this->contract_state_doc['contract']->serialize() : array(),
	    			'contract_filter' => $this->contract_filter_doc,
		    		'form_url' => $this->form_url_doc
			    ),
				'documents'				=> array('datatable' => $datatable)
			);
			
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
        	$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','document'));
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'drawing.list', 'frontend' );

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('paginator');

			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
		}
    }

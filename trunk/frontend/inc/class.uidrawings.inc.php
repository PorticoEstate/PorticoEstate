<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
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

	/**
	 * Drawings
	 *
	 * @package Frontend
	 */

    class frontend_uidrawings extends frontend_uifrontend
    {

        public $public_functions = array
        (
            'index'     	=> true,
        );

		public function __construct()
		{
			phpgwapi_cache::session_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.drawings'));
			parent::__construct();
			$this->location_code = $this->header_state['selected_location'];
		}

		public function index()
		{
			$doc_type = 17;
			$allrows = true;
			$sodocument	= CreateObject('property.sodocument');

			$document_list = $sodocument->read_at_location(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
				'filter' => $this->filter,'location_code' => $this->location_code,'doc_type' => $doc_type, 'allrows' => $allrows));
			$this->total_records = $this->so->total_records;
_debug_array($document_list);
_debug_array($this->location_code);

			//----------------------------------------------datatable settings--------

			$content = array();
			foreach($invoices as $entry)
			{
				$content[] = array
				(
					'document_id'			=> $entry['document_id'],
					'document_name'			=> $entry['document_name'],
					'link'					=> $entry['link'],
					'title'					=> $entry['title'],
					'doc_type'				=> $entry['doc_type'],
					'document_date'			=> $GLOBALS['phpgw']->common->show_date($entry['document_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
				);	
			}

			$datavalues[0] = array
			(
				'name'					=> "0",
				'values' 				=> json_encode($content),
				'total_records'			=> count($content),
				'edit_action'			=> json_encode($GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uidocument.view_file'))),
				'is_paginator'			=> 1,
				'footer'				=> 0
			);


			$myColumnDefs[0] = array
			(
				'name'		=> "0",
				'values'	=>	json_encode(array(	array('key' => 'document_name','label'=>lang('name'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'document_id','label'=>lang('id'),'sortable'=>false,'resizeable'=>true,'formatter'=>'YAHOO.widget.DataTable.formatLink'),
													array('key' => 'title','label'=>lang('title'),'sortable'=>false,'resizeable'=>true),
													array('key' => 'doc_type','label'=>lang('type'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'document_date','label'=>lang('date'),'sortable'=>true,'resizeable'=>true)
													))
			);

			//----------------------------------------------datatable settings--------




			$data = array
			(
				'property_js'			=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'				=> $datavalues,
				'myColumnDefs'			=> $myColumnDefs,
				'header'				=>$this->header_state,
				'tabs'					=> $this->tabs,
				'drawings'				=> lang('not_implemented')
			);
			
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
        	$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','drawings'));
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'project.edit', 'frontend' );

		}
    }

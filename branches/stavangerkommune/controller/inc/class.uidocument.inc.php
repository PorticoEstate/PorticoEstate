<?php
    /**
    * phpGroupWare - controller: a part of a Facilities Management System.
    *
    * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
    * @author Torstein Vadla <torstein.vadla@bouvet.no>
    * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
    * @internal Development of this application was funded by http://www.bergen.kommune.no/
    * @package property
    * @subpackage controller
    * @version $Id$
    */	

    phpgw::import_class('phpgwapi.uicommon');
    phpgw::import_class('controller.sodocument');
    phpgw::import_class('controller.soprocedure');
    include_class('controller', 'document', 'inc/model/');

    class controller_uidocument extends phpgwapi_uicommon
    {
        private $so;
        private $so_procedure;

	    private $read;
	    private $add;
	    private $edit;
	    private $delete;


        public $public_functions = array
        (
				'query'						=> true,
				'add'							=> true,
				'view'						=> true,
				'delete'					=> true,
				'show'						=> true,
				'document_types'	=> true
        );

        public function __construct()
        {
            parent::__construct();
            $this->so = controller_sodocument::get_instance();
            $this->so_procedure = controller_soprocedure::get_instance();
			$this->read    = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller');//1 
			$this->add     = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller');//2 
			$this->edit    = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller');//4 
			$this->delete  = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller');//8 
        }

        public function query()
        {
            // YUI variables for paging and sorting
            $start_index	= phpgw::get_var('startIndex', 'int');
            $num_of_objects	= phpgw::get_var('results', 'int', 'GET', 10);
            $sort_field		= phpgw::get_var('sort');
            $sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
            // Form variables
            $search_for 	= phpgw::get_var('query');
            $search_type	= phpgw::get_var('search_option');
            // Create an empty result set
            $result_objects = array();
            $result_count = 0;

            //Retrieve a contract identifier and load corresponding contract
            $procedure_id = phpgw::get_var('procedure_id');
            if(isset($procedure_id))
            {
                $procedure = $this->so_procedure->get_single($procedure_id);
            }

            $type = phpgw::get_var('type');
            switch($type)
            {
                case 'documents_for_procedure':
                    $filters = array('procedure_id' => $procedure_id, 
                    				'document_type' => phpgw::get_var('document_type'));
                    break;
            }

            $result_objects = $this->so->get($start_index, 
                                            $num_of_objects, 
                                            $sort_field, 
                                            $sort_ascending, 
                                            $search_for, 
                                            $search_type, 
                                            $filters);
            $result_count = $this->so->get_count($search_for, $search_type, $filters);
            
            //Serialize the documents found
            $rows = array();
            foreach ($result_objects as $result)
            {
                if(isset($result))
                {
                    $rows[] = $result->serialize();
                }
            }
			
            $editable = phpgw::get_var('editable') == '1' ? true : false;
			
            //Add context menu columns (actions and labels)
            array_walk($rows, array($this, 'add_actions'), array($type, 
                                                                isset($procedure) ? $procedure->has_permission(PHPGW_ACL_EDIT) : false, 
                                                                $this->type_of_user, 
                                                                $editable));
				
            //Build a YUI result from the data
            $result_data = array('results' => $rows, 'total_records' => $result_count);	
            return $this->yui_results($result_data, 'total_records', 'results');
        }
		
        public function get_document_types()
        {
            $result_objects = $this->so->list_document_types();
            
            $editable = phpgw::get_var('editable') == '1' ? true : false;
            $result_data = array('results' => $result_objects);
            
            //Add context menu columns (actions and labels)
            array_walk(
            		$result_data['results'],
            		array($this, '_add_links'),
            		"controller.uidocument.edit_document_type");
            	
            
            //Build a YUI result from the data	
            return $this->yui_results($result_data, 'total_records', 'results');
		}
		
        /**
         * Add data for context menu
         *
         * @param $value pointer to
         * @param $key ?
         * @param $params [type of query, editable]
         */
        public function add_actions(&$value, $key, $params)
        {
            $value['ajax'] = array();
            $value['actions'] = array();
            $value['labels'] = array();
            
            //view/download
            $value['ajax'][] = false;
            $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uidocument.view', 
            														'id' => $value['id'])));
            $value['labels'][] = lang('view');
            
            $type = $params[0];
            $edit_permission = $params[1];
            $user_is = $params[2];
            $editable = $params[3];
            
            switch($type)
            {
                case 'documents_for_procedure':
                    if($edit_permission && $editable) {
                        $value['ajax'][] = true;
                        $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uidocument.delete', 
                        														'id' => $value['id'])));
                        $value['labels'][] = lang('delete');
                    }
                    break;
                case 'admin':
                    $value['ajax'][] = true;
                    $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'controller.uidocument.edit_document_type', 
                    														'id' => $value['id'])));
                    $value['labels'][] = lang('Edit document type');
                    break;
            }
        }

        /**
         * Public function to add a document.
         * 
         * @param HTTP::procedure_id	the procedure id
         * @return unknown_type
         */
        public function add()
        {	
            // Get target ids
            $procedure_id = intval(phpgw::get_var('procedure_id'));
            
            $data = array();
            // Check permissions if procedure id is set
            if(isset($procedure_id) && $procedure_id > 0)
            {
            	//Load procedure
            	$procedure = $this->so_procedure->get_single($procedure_id);
            }
            
            // If no contract or party is loaded
            if(!isset($procedure))
            {
            	$data['error'] = lang('error_no_procedure');
            	$this->render('permission_denied.php', $data);
            	return;
            }
            
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
				if(!$this->add && !$this->edit)
				{
					phpgwapi_cache::message_set('No access', 'error');
                            $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uidocument.show', 
                            												'procedure_id' => $procedure->get_id(), 
                            												'tab' => 'documents'));
 
				}
                //Create a document object
                $document = new controller_document();
                $document->set_title(phpgw::get_var('document_title'));
                $document->set_name($_FILES["file_path"]["name"]);
                $document->set_type_id(phpgw::get_var('document_type'));
                $desc = phpgw::get_var('document_description','html');
                $desc = str_replace("&nbsp;", " ", $desc);
                $document->set_description($desc);
                $document->set_procedure_id($procedure_id);
                
                //Retrieve the document properties
                $document_properties = $this->get_type_and_id($document);
                
                // Move file from temporary storage to vfs
                $result = $this->so->write_document_to_vfs
                (
                	$document_properties['document_type'], 
                	$_FILES["file_path"]["tmp_name"],
                	$document_properties['id'],
                	$_FILES["file_path"]["name"]
                );
                
                if($result)
                {
                    if($this->so->store($document))
                    {
                        if(isset($procedure))
                        {
                            $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uidocument.show', 
                            												'procedure_id' => $procedure->get_id(), 
                            												'tab' => 'documents'));
                        }
                    }
                    else
                    {
                        // Handle failure on storing document
                        $this->redirect($document, $document_propeties,'','');
                    }
                }
                else
                {
                    //Handle vfs failure to store document
                    $this->redirect($document, $document_propeties,'','');
                }
            }
        }

        /**
         * Public function for viewing/downloading a document.
         * 
         * @param HTTP::id	the document id
         * @return document on success, error message on failure
         */
        public function view()
        {
            $document_id = intval(phpgw::get_var('id'));
            $document = $this->so->get_single($document_id);
            $document_properties = $this->get_type_and_id($document);
            
            header("Content-Disposition: attachment; filename={$document->get_name()}");
            header("Content-Type: $file_type");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            
            echo $this->so->read_document_from_vfs
            (
                $document_properties['document_type'],	
                $document_properties['id'],
                $document->get_name()
            );
        }

        /**
         * Public function for deleting a document. Deletes the document from
         * the database and the virtual file system (vfs).
         * 
         * @param HTTP::id	the document id
         * @return true if successful, false if error, permission denied message on
         * 			not enough privileges
         */
        public function delete()
        {	
            $document_id = intval(phpgw::get_var('id'));
            $document = $this->so->get_single($document_id);
            
            $procedure_id = intval(phpgw::get_var('procedure_id'));
 
  			if(!$this->delete)
			{
				phpgwapi_cache::message_set('No access', 'error');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uidocument.show', 
																	'procedure_id' => $procedure_id,
																	'tab' => 'documents'));
			}
 
            $procedure = $this->so_procedure->get_single($procedure_id);

            $document_properties = $this->get_type_and_id($document);
            
            /*if(!$this->check_permissions($document,$document_properties))
            {
                $this->render('permission_denied.php');
                return;
            }*/
            
            $result = $this->so->delete_document_from_vfs
            (
                $document_properties['document_type'],	
                $document_properties['id'],
                $document->get_name()
            );
            
            if($result)
            {
                $this->so->delete_document($document_id);
                $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uidocument.show', 
                            												'procedure_id' => $procedure->get_id(), 
                            												'tab' => 'documents'));
            } 
            // TODO: communicate error/message to user
            return false;
        }
		
        /**
         * Utitity function for redirecting to correct edit mode (procedure)
         * 
         * @param $document	the target document
         * @param $document_properties	the document properies (name/value array)
         * @param $error	an error message
         * @param $message	a user message
         */
        public function redirect($document, $document_properties, $error, $message)
        {
            if($document_properties['document_type'] == controller_sodocument::$PROCEDURE_DOCUMENTS)
            {
                $GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uiprocedure.edit', 
                													'id' => $document_properties['id'], 
                													'error' => $error, 
                													'message' => $message));		
            }
        }
		
        /**
         * Utiity method for checking the users permission on this document. If the
         * document is bound to a procedure, then the user must have edit privileges
         * on the given procedure. If no procedure, the user must be an executive 
         * officer or an administrator.
         * 
         * @param $document	the document in question
         * @param $document_properties	the document type and object id
         * @return true if correct privileges, false otherwise
         */
        private function check_permissions($document, $document_properties)
        {
            if($document_properties == controller_sodocument::$PROCEDURE_DOCUMENTS)
            {
                $procedure = $this->so_procedure->get_single($document_properties['id']);
                if(!$procedure->has_permission(PHPGW_ACL_EDIT))
                {
                    return false;
                }
            } 
            else
            {
                if(!($this->isExecutiveOfficer() || $this->isAdministrator()))
                {
                    return false;
                }
            }
            return true;
        }

        /**
         * Utility method for finding out whether a document is bound to a
         * procedure.
         * 
         * @param $document	the given document
         * @return name/value array ('document_type','id')
         */
        private function get_type_and_id($document)
        {
            $document_type;
            $id;
            $procedure_id = $document->get_procedure_id();
            if(isset($procedure_id) && $procedure_id > 0)
            {
                $document_type = controller_sodocument::$PROCEDURE_DOCUMENTS;
                $id = $procedure_id;
            }
            return array
            (
        		'document_type' => $document_type,
        		'id' => $id
            );
        }
		
        public function show()
        {
            $GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
            $procedure_id = (int)phpgw::get_var('procedure_id');
            $document_type = phpgw::get_var('type');
            if(isset($_POST['edit_procedure']))
            {
                $GLOBALS['phpgw']->redirect_link('/index.php', array(
            								'menuaction' => 'controller.uiprocedure.edit', 
            								'id' => $procedure_id));
            }
            else
            {
                if(isset($procedure_id) && $procedure_id > 0)
                {
                    $procedure = $this->so_procedure->get_single($procedure_id);
                }
                else
                {
                    $this->render('permission_denied.php',array('error' => lang('invalid_request')));
                    return;
                }
                
                if($this->flash_msgs)
                {
                    $msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->flash_msgs);
                    $msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
                }
                
                $documents = $this->so->get(null,null,null,null,null,null,array(
                							'procedure_id' => $procedure_id, 
                							'type' => $document_type));
                	
                $table_header[] = array('header' => lang('Document title'));
                $table_header[] = array('header' => lang('Document name'));
                $table_header[] = array('header' => lang('Document description'));
                
                foreach($documents as $document)
                {
                	/* hack to fix display of &nbsp; char */
                    $document->set_description(str_replace("&nbsp;", " ",$document->get_description()));
                    $doc_array = $document->toArray();
                    $doc_array['link'] = self::link(array('menuaction' => 'controller.uidocument.view', 
                    									'id' => $doc_array['id']));
                    $doc_array['delete_link'] = self::link(array('menuaction' => 'controller.uidocument.delete', 
                    									'id' => $doc_array['id'], 'procedure_id' => $procedure_id));
                    $table_values[] = array('document' => $doc_array);
                }
                
                $procedure_array = $procedure->toArray();
                
                $tabs = array( array(
                			'label' => lang('Procedure'),
                			'link'  => $GLOBALS['phpgw']->link('/index.php', array(
                								'menuaction' => 'controller.uiprocedure.view', 
                								'id' => $procedure->get_id()))
                
                		), array(
                			'label' => lang('View_documents_for_procedure')
                		));
                
                $data = array
                (
                	'tabs'					=> $GLOBALS['phpgw']->common->create_tabs($tabs, 1),
                	'view'					=> "view_documents_for_procedure",
                	'procedure_id'			=> !empty($procedure) ? $procedure->get_id() : 0,
                	'procedure'				=> $procedure_array,
                	'values'				=> $table_values,
                	'table_header'			=> $table_header,
                );
                
                $this->use_yui_editor(array('document_description'));
                
                $GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . lang('Procedure');
                
                self::render_template_xsl(array('procedure/procedure_tabs', 
                								'common', 
                								'procedure/procedure_documents'), $data);
            }
        }
		
        public function document_types()
        {
            if(phpgw::get_var('phpgw_return_as') == 'json')
            {
                return $this->get_document_types();
                self::render_template_xsl(array('procedure/procedure_tabs', 
                								'common', 
                								'procedure/procedure_documents'), 
                                        $data);
    	    }
        	self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
        	phpgwapi_yui::load_widget('datatable');
        	phpgwapi_yui::load_widget('paginator');
        
        	$data = array(
        		'datatable_name'	=> 'Dokument typer',
        		'form' => array(
        	    ),
        		'datatable' => array(
        			'source' => self::link(array('menuaction' => 'controller.uidocument.document_types', 
        										'phpgw_return_as' => 'json')),
        			'field' => array(
        	            array(
        					'key' => 'id',
        					'label' => lang('ID'),
        					'sortable'	=> true,
        					'formatter' => 'YAHOO.portico.formatLink'
        				),
        				array(
        					'key' => 'title',
        					'label' => lang('Procedure title'),
        					'sortable'	=> false
        				),
        				array(
        					'key' => 'link',
        					'hidden' => true
        				)
        			)
        		),
        	);
    
            self::render_template_xsl('datatable_common', $data);
        }
    }

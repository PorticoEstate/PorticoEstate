<?php
/**************************************************************************
* phpGroupWare - ged
* http://www.phpgroupware.org
* Written by Pascal Vilarem <pascal.vilarem@steria.org>
*
* --------------------------------------------------------------------------
*  This program is free software; you can redistribute it and/or modify it
*  under the terms of the GNU General Public License as published by the
*  Free Software Foundation; either version 2 of the License, or (at your
*  option) any later version
***************************************************************************/

include_once ( 'ged_common_functions.inc.php');

/**
* flow object class
*
* @package ged
*/

class flow_client
{
	var $ged_dm;
	var $t;
	
	/*
	 * OBJECT INFO 
	 */	 
	 		
	function flow_client()
	{
		if ( isset($GLOBALS['ged_ui']))
		{
			// We are called from ged : easy case
			$this->ged_dm=&$GLOBALS['ged_ui']->ged_dm;
			$this->t =&$GLOBALS['ged_ui']->t;
		}
		else
		{
			// called from an other app
			$this->ged_dm=CreateObject('ged.ged_dm', True);
					
			// TODO : replace PHPGW_APP_TPL with the ged app tpl
			$this->t = clone ($GLOBALS['phpgw']->template);
			$this->t->set_root(PHPGW_APP_TPL);
		}
	}

	// wrapper to use new phpgw::get_var if it exists
	// and old get_var otherwise
	function get_var($varname,$method=null,$default=null)
	{
		return ged_get_var($varname,$method, $default);
	}

	function load_template_defaults()
	{
		$this->t->set_var('lang_name', lang('name'));
		$this->t->set_var('lang_type', lang('type'));
		$this->t->set_var('lang_reference', lang('reference'));
		$this->t->set_var('lang_description', lang('description'));
		$this->t->set_var('lang_period', lang('lifetime'));
		$this->t->set_var('lang_version', lang('version'));
	}
	
	function display_app_header()
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
	}

	function get_flow($object)
	{
		// TODO : récupérer le flow de l'objet
		// TODO : si non récupérer celui du type/projet
		// TODO : sinon récupérer celui du type
		// TODO :  sinon renvoyer le type par défaut
		return 1;
	}
	
	function get_status($object)
	{
		$version=$this->ged_dm->get_version_info($object['version_id']);

		return($version['status']);
	}
	
	/*
	 * CONDITIONS 
	 */
	 
	function is_last_version($object)
	{
		// DEBUG
		//_debug_array($object);	
		
		$last_version=$this->ged_dm->get_last_version($object['element_id']);
		
		if ( $last_version['version_id'] == $object['version_id'])
		{
			$result=true;
		}
		else
		{
			$result=false;
		}
		
		return($result);
	}
	 
	/*
	 * SET_STATUS
	 */	
	 
	function set_status($object,$status,$context=null)
	{
		//DEBUG
		//print ( "<br/>\n".$status );
		$this->ged_dm->set_version_status($object['version_id'],$status);
		
		$function_result=Array('status' => 'ok');
		return($function_result);
	}
	
	function set_status_with_review($object,$status,$context=null)
	{
		$function_result=Array('status' => 'processing');
		
		$last_version=$this->ged_dm->get_version_info($object['version_id']);
		$element=$this->ged_dm->get_element_info($last_version['element_id']);
		
		$review_file=$this->get_var('review_file',array('POST'));
		$comment=addslashes($this->get_var('comment', array( 'POST')));

		// Récupérer le type de review file en fonction de la transition
		// Du type de fichier, du projet concerné
		// Et de l'âge du capitaine
		if ( isset($context['custom_fields']['review_file_type']))
		{
			$review_file_type_for_transition=$context['custom_fields']['review_file_type'];
		}
		else
		{
			// no default review_file_type
			$review_file_type_for_transition='';
		}
		// Puis récupérer la référence probable
		$next_reference_for_review_file=$this->ged_dm->get_next_available_reference($review_file_type_for_transition, $element['project_root']);


		if ($review_file==lang($context['action']))
		{
			//DEBUG
			//print ( "hop");
			
			if ( isset($_FILES['file']) && $_FILES['file']['name'] != '')
			{		
				$new_file['file_name']=$_FILES['file']['name'];
				$new_file['file_size']=$_FILES['file']['size'];
				$new_file['file_tmp_name']=$_FILES['file']['tmp_name'];
				$new_file['file_mime_type']=$_FILES['file']['type'];
				
				$new_file['doc_type']=$review_file_type_for_transition;
				$new_file['name']=$this->ged_dm->get_type_desc($new_file['doc_type'])." / ".$element['name'];
				$new_file['description']=$comment;
				$new_file['reference']=$next_reference_for_review_file;
				$new_file['major']=1;
				$new_file['minor']=0;
				$new_file['validity_period']=0;
				
				$new_place=null;
				$new_place=$this->ged_dm->get_type_place($new_file['doc_type'],$element['project_root']);
				
				if ( !isset($new_place))
				{
					$new_place=$element['parent_id'];
				}
				$new_file['parent_id']=$new_place;
									
				$new_id=$this->ged_dm->add_file($new_file);
				
				// Updating ACL table with brand new element
				// So that process can continue without acces denied
				$this->ged_dm->acl[$new_id]['read']=1;

				$new_version=$this->ged_dm->get_last_version($new_id);
				
				$new_relations[0]['linked_version_id']=$last_version['version_id'];
				$new_relations[0]['relation_type']='review';
				
				$this->ged_dm->set_relations($new_version['version_id'],$new_relations);
				
			}
			
			$this->ged_dm->set_version_status($object['version_id'],$status);
			$function_result['comment']=$comment;
			$function_result['status']='ok';
		}
		
		if ( $function_result['status'] != 'ok')
		{				
			//$this->set_template_defaults();
	
			$this->t->set_file(array('review_file_tpl'=>'review_file.tpl'));
			
			$this->t->set_var('probable_reference_value', $next_reference_for_review_file);
			
			$this->t->set_var('probable_reference_label', lang('Probable reference'));
			$this->t->set_var('review_title', lang($context['action']));
			$this->t->set_var('lang_file', 'Review file');
			
			$this->t->set_var('element_id_value', $last_version['element_id']);
			
			$this->t->set_var('file_field', 'file');

			$this->t->set_var('comment_field', 'comment');
			$this->t->set_var('comment_label', lang('comment'));
			$this->t->set_var('comment_value', $comment);
			
			$this->t->set_var('lang_do_transition', lang( $context['action']));
					
			$this->display_app_header();
	
			$this->t->pfp('out', 'review_file_tpl');
		}
		return ($function_result);	
	}
	
	function update($object,$status,$context=null)
	{
		$function_result=Array('status' => 'processing');
		
		$version=$this->ged_dm->get_version_info($object['version_id']);
		$element=$this->ged_dm->get_element_info($version['element_id']);
		
		// actions
		$update_file=$this->get_var('update_file', array('POST', 'GET'));
		$update_version=$this->get_var('update_version', array('POST', 'GET'));
		$go_back=$this->get_var('go_back', array('POST', 'GET'));
		$search=$this->get_var('search', array('POST', 'GET'));
		$do_add_relation=$this->get_var('do_add_relation', array('POST', 'GET'));
		$do_remove_relation=$this->get_var('do_remove_relation', array('POST', 'GET'));
		
		// needed for 'update_file' action
		$new_file_name=$this->get_var('file_name', array('POST', 'GET'));
		$new_file_description=$this->get_var('file_description', array('POST', 'GET'));
		$new_referenceq=$this->get_var('referenceq',array('GET','POST'));
		$new_doc_type=$this->get_var('document_type', array('GET', 'POST'));
		$new_validity_period=$this->get_var('validity_period', array('POST', 'GET'));

		// needed for 'update_version' action
		$new_version_description=$this->get_var('version_description', array('POST', 'GET'));
		$new_major_value=$this->get_var('major', array('POST', 'GET'));
		$new_minor_value=$this->get_var('minor', array('POST', 'GET'));
					
		// TODO : version numbering
		$new_version_type=$this->get_var('version_type', array('POST', 'GET'));
		
		// needed for 'search' action			
		$query=$this->get_var('query', array('POST', 'GET'));

		// needed for relations action
		$new_relations=$this->get_var('relations', array('POST', 'GET'));

		// TODO : real update
		if ($update_file==lang('Update'))
		{
			$new_file['element_id']=$version['element_id'];

			$new_file['name']=$new_file_name;
			$new_file['reference']=$new_referenceq;
			$new_file['doc_type']=$new_doc_type;
			$new_file['description']=$new_file_description;
			$new_file['validity_period']=$new_validity_period;

			$this->ged_dm->update_file($new_file);
			
			$function_result=Array('status' => 'ok');
			$function_result['comment']="updated file info";
		}
    elseif ( $context['custom_fields']['update_mode'] == 'new'  && $update_version==lang('New') )
    {

      $new_version['element_id']=$version['element_id'];
      $new_version['file_name']=$_FILES['version_file']['name'];
      $new_version['file_size']=$_FILES['version_file']['size'];
      $new_version['file_tmp_name']=$_FILES['version_file']['tmp_name'];
      $new_version['file_mime_type']=$_FILES['version_file']['type'];
      $new_version['relations']=$new_relations;
      
      // TODO : version numbering
      $new_version['major']=$new_major_value;
      $new_version['minor']=$new_minor_value;
      	
      $new_version['description']=$new_version_description;
      		
      $version_added=$this->ged_dm->add_version($new_version);
      
      if ($version_added=='OK')
      {
				$function_result=Array('status' => 'ok', 'mute_history' => 'mute');
      }
      else
      {
        print ( $version_added);
      		$function_result=Array('status' => 'error');
      }
    	
    }
		elseif ($context['custom_fields']['update_mode'] == 'update'  && $update_version==lang('Update') )
		{
      $amended_version['element_id']=$version['element_id'];
      $amended_version['file_name']=$_FILES['version_file']['name'];
      $amended_version['file_size']=$_FILES['version_file']['size'];
      $amended_version['file_tmp_name']=$_FILES['version_file']['tmp_name'];
      $amended_version['file_mime_type']=$_FILES['version_file']['type'];
      
      // TODO : version numbering
      $amended_version['major']=$new_major_value;
      $amended_version['minor']=$new_minor_value;
      	      
      if ( is_array($new_relations))
      {
      	$amended_version['relations']=$new_relations;
      }
      else
      	$amended_version['relations']=null;
      
      $amended_version['description']=$new_version_description;
      $amended_version['version_id']=$version['version_id'];
      
      $version_updated=$this->ged_dm->update_version($amended_version);

			if ($version_updated=='OK')
			{
				$function_result=Array('status' => 'ok');
				$function_result['comment']=$new_version_description;									
			}
      else
      {
        print ( "ERROR : ".$version_updated);
      }

		}
		elseif ( $go_back == lang('Go back'))
		{
			$function_result=Array('status' => 'ok');
		}
		else
		{

			$file_name=$element['name'];
			$file_description=$element['description'];
			$validity_period=$element['validity_period'];
			$referenceq=$element['reference'];
			$doc_type=$element['doc_type'];
				      
      $version_status=$version['status'];
      $version_description=$version['description'];
      $version_id=$version['version_id'];
      
      if ( $context['custom_fields']['update_mode'] == 'update' )
      {	        
				// TODO : Guess next version numbers	
        
        $this->t->set_var('update_version_action', lang('Update'));
      }
      elseif ( $context['custom_fields']['update_mode'] == 'new' )
	    {
				// TODO : Guess next version numbers	

        $this->t->set_var('update_version_action', lang('New'));
  	  }
      
      /*
       * relations
       */

      if ( ( $search=="search" || $do_add_relation != '' || $do_remove_relation != '' ) && $query != ''  )
			{
				$search_results=$this->ged_dm->search($query);
			}
			
			if ( is_array($new_relations) || $search=="search" || $do_add_relation != '' || $do_remove_relation != '' )
			{
				// TODO : Enrichir un peu pour afficher plus d'infos'					
				$i=0;
				foreach ( $new_relations as $relation )
				{
					if ( $relation['linked_version_id'] != $do_remove_relation || $do_remove_relation == '')
					{
						// TODO : Ajouter le nom
						$version_relations[$i]=$this->ged_dm->get_version_info($relation['linked_version_id']);
						$version_relations[$i]['linked_version_id']=$relation['linked_version_id'];
						$version_relations[$i]['relation_type']=$relation['relation_type'];
						
						$i++;							
					}
				}
			}
			else
			{
				$version_relations=$this->ged_dm->list_version_relations_out ( $object['version_id'] );
				//_debug_array($version_relations);
			}
			
			if ( $do_add_relation != '')
			{
				$version_relations_next_index=sizeof($version_relations)+1;
				
				$new_version_to_add=$this->ged_dm->get_version_info($do_add_relation);
				
				$version_relations[$version_relations_next_index]['version_id']=$do_add_relation;
				$version_relations[$version_relations_next_index]['linked_version_id']=$do_add_relation;
				$version_relations[$version_relations_next_index]['relation_type']='dependancy';
				$version_relations[$version_relations_next_index]['element_id']=$new_version_to_add['element_id'];
				$version_relations[$version_relations_next_index]['name']=$new_version_to_add['name'];
				$version_relations[$version_relations_next_index]['major']=$new_version_to_add['major'];
				$version_relations[$version_relations_next_index]['minor']=$new_version_to_add['minor'];
				$version_relations[$version_relations_next_index]['status']=$new_version_to_add['status'];
				$version_relations[$version_relations_next_index]['reference']=$new_version_to_add['reference'];
			}

    	
    	$new_relations=null;
    	$nri=0;
    	if ( is_array($version_relations))
    	{
    		foreach ( $version_relations as $version_relation )
    		{
    			//print ($version_relation['status'] );
    			
    			// NIARF
    			if ( array_key_exists('status', $version_relation) )
    			{
    				if ( $version_relation['status']=='obsolete' || $version_relation['status']=='refused' )
    				{
      				// print ( 'new version : '.$version_relation['version_id']."<br/>\n");
      				
      				// TODO : prepare data for future relation creation
      				// TOFIX : when obsolete or refused without current version
      				// TOFIX : there is a problem
      				$the_new_relations=$this->ged_dm->get_current_version($version_relation['element_id']);
      				
      				$new_relations[$nri]['linked_version_id']=$the_new_relations['version_id'];
      				$new_relations[$nri]['reference']=$version_relation['reference'];
      				$new_relations[$nri]['name']=$version_relation['name'];
        			$new_relations[$nri]['major']=$the_new_relations['major'];
      				$new_relations[$nri]['minor']=$the_new_relations['minor'];
      				$new_relations[$nri]['status']=$the_new_relations['status'];
      				
      				// TODO : use real value
      				$new_relations[$nri]['relation_type']='dependancy';
      				
      				$nri++;      					
    				}
    				else
    				{
      				// print ( 'report : '.$version_relation['version_id']."<br/>\n");
      				
      				// TODO : prepare data for future relation creation
      				$new_relations[$nri]['linked_version_id']=$version_relation['version_id'];
      				$new_relations[$nri]['major']=$version_relation['major'];
      				$new_relations[$nri]['minor']=$version_relation['minor'];
      				$new_relations[$nri]['status']=$version_relation['status'];
      				$new_relations[$nri]['reference']=$version_relation['reference'];
      				$new_relations[$nri]['name']=$version_relation['name'];
      				$new_relations[$nri]['relation_type']=$version_relation['relation_type'];
      				
      				$nri++;     					
    				}     				
    			}
    			else
    			{
    				// TODO : prepare data for future relation creation
    				$new_relations[$nri]['linked_version_id']=$version_relation['version_id'];
    				$new_relations[$nri]['major']=$version_relation['major'];
    				$new_relations[$nri]['minor']=$version_relation['minor'];
    				$new_relations[$nri]['status']=$version_relation['status'];
    				$new_relations[$nri]['reference']=$version_relation['reference'];
    				$new_relations[$nri]['name']=$version_relation['name'];
    				$new_relations[$nri]['relation_type']='dependancy';
    				
    				$nri++;     					      				
    			}      			
    		}      		
    	}
      
      /*
       * fin relations
       */
      
		}			
								
		// display form if needed
		if ( $function_result['status'] != 'ok')
		{				

			if ( (int)$new_major_value != 0 )
			{
				$major=$new_major_value;
			}
			else
			{
				$major=$version['major'];
			}
			
			if ( (int)$new_minor_value != 0 )
			{
				$minor=$new_minor_value;
			}
			else
			{
				if ( $context['custom_fields']['update_mode'] == 'update')
				{
					$minor=$version['minor'];
				}
				else
				{
					$minor=$version['minor']+1;
				}
			}
			
			$this->t->set_file(array('update_file_tpl'=>'update_file.tpl'));
			$this->load_template_defaults();
			$this->display_app_header();				
							 
			$this->t->set_var('element_id_value', $object['element_id']);
			$this->t->set_var('search_query', $query);
			
			/*
			 * Generic display data
			 */
			
			$this->t->set_var('reset_file_field', 'reset_file');
			$this->t->set_var('reset_file_action', lang('Undo'));
			$this->t->set_var('update_file_field', 'update_file');
			$this->t->set_var('update_file_action', lang('Update'));
			$this->t->set_var('update_version_field', 'update_version');
			
			$this->t->set_var('reset_version_field', 'reset_version');
			$this->t->set_var('reset_version_action', lang('Undo'));
			
			$this->t->set_var('referenceq_field', 'referenceq');
			$this->t->set_var('period_field', 'validity_period');		
	
			$this->t->set_var('go_back_field', 'go_back');
			$this->t->set_var('go_back_action', lang('Go back'));
	
			$this->t->set_var('element_id_field', 'element_id');
			$this->t->set_var('file_name_field', 'file_name');
			
			$this->t->set_var('major_field', 'major');
			$this->t->set_var('minor_field', 'minor');
			$this->t->set_var('major_value', $major);
			$this->t->set_var('minor_value', $minor);
					
			$this->t->set_var('file_description_field', 'file_description');
			$this->t->set_var('version_description_field', 'version_description');
			$this->t->set_var('version_file_field', 'version_file');
			$this->t->set_var('version_type_field', 'version_type');
			
			$this->t->set_var('add-image', $GLOBALS['phpgw']->common->image('ged', "add-16"));
			$this->t->set_var('remove-image', $GLOBALS['phpgw']->common->image('ged', "remove-16"));
	    
	    /*
	     * file zone
	     */
	     
			$this->t->set_var('file_description_value', $file_description);		
			$this->t->set_var('file_name_value', $file_name);
			
			$this->t->set_block('update_file_tpl', 'power_block', 'power_block_handle');
			// Begin power_block zone
			if ( $this->ged_dm->admin )
			{
	
			$this->t->set_var('new_reference', $referenceq);
	
			$select_types=$this->ged_dm->list_doc_types ();
	
			$select_types_html="<select name=\"document_type\">\n";
			foreach ($select_types as $select_type)
			{
				$selected="";
				if ($select_type['type_id'] == $doc_type )
				{
					$selected=" selected ";
				}
	
				$chrono_flag=$style="";
				if ( $select_type['type_chrono']==1)
				{
					$chrono_flag=" [C]";
					$style="style=\"font-weight: bold;\"";
				}
				$select_types_html.="<option ".$style." value=\"".$select_type['type_id']."\"".$selected.">".lang($select_type['type_desc']).$chrono_flag."</option>\n";
			}
			$select_types_html.="</select>\n";
	
			$this->t->set_var('select_type', $select_types_html);
			$this->t->fp('power_block_handle', 'power_block', True);
			// End power_block zone
			}
			else
			{
				$this->t->set_var( 'power_block_handle', "");
			}
			
			$select_periods=$this->ged_dm->select_periods ();
	
			$select_period_html='<select name="validity_period">\n';
			foreach ($select_periods as $select_period)
			{
				if ($select_period['period']==$validity_period )
				{
					$select_period_html.="<option value=\"".$select_period['period']."\" selected>".lang($select_period['description'])."</option>\n";
				}
				else
				{
					$select_period_html.="<option value=\"".$select_period['period']."\">".lang($select_period['description'])."</option>\n";
				}
			}
			$select_period_html.="</select>\n";
	
			$this->t->set_var('select_period', $select_period_html);
	
	
	    /*
	     * version zone
	     */
	     
	    $this->t->set_var('version_id_field', 'version_id');
			$this->t->set_var('version_id_value', $object['version_id']);
	    $this->t->set_var('version_description_value', $version_description);
	    
			// TODO : versions numbers
	    
	    $this->t->set_block('update_file_tpl', 'relations_list_block', 'relations_list_block_handle');
	    
	    if ( isset($new_relations))
	    {
		    if ( is_array($new_relations))
		    {  	
		    	$nri=0;
		    	foreach ($new_relations as $new_relation)
		    	{
		    		$this->t->set_var('relations_element_reference', $new_relation['reference']);
		    		$this->t->set_var('relations_element_major', $new_relation['major']);
		    		$this->t->set_var('relations_element_minor', $new_relation['minor']);
		    		$this->t->set_var('relations_element_status_image', $GLOBALS['phpgw']->common->image('ged', $new_relation['status']."-16"));
		    		$this->t->set_var('relations_element_name', $new_relation['name']);
		    		
		    		$this->t->set_var('relations_id_field', 'relations['.$nri.'][linked_version_id]');
		    		$this->t->set_var('relations_id_value', $new_relation['linked_version_id']);
		    		
		    		$relations_types=Array('dependancy', 'delivery', 'review', 'notice');
		    		
						$select_relation_stypes_html="<select name=\"".'relations['.$nri.'][relation_type]'."\">\n";
						foreach ($relations_types as $relation_type)
						{
							if ( $relation_type == $new_relation['relation_type'])
							{
								$selected_flag="selected";
							}
							else
							{
								$selected_flag="";
							}
				
							$select_relation_stypes_html.="<option value=\"".$relation_type."\" ".$selected_flag." >".lang($relation_type)."</option>\n";
						}
						$select_relation_stypes_html.="</select>\n";
				
						$this->t->set_var('relations_type', $select_relation_stypes_html);
		
		    		$nri++;
		    		$this->t->fp('relations_list_block_handle', 'relations_list_block', True);   
		    	}
		    	
		    }
	    }
	    
	
			if ( isset($search_results))
			{
				if ( is_array($search_results))
				{
					$this->t->set_block('update_file_tpl', 'search_list_block', 'search_list_block_handle');
						
		    	//$nri=0;
		    	foreach ($search_results as $search_result)
		    	{
		    		$this->t->set_var('element_id', $search_result['element_id']);
		    		$this->t->set_var('version_id', $search_result['version_id']);
		    		$this->t->set_var('name', $search_result['name']);
		    		$this->t->set_var('reference', $search_result['reference']);
		    		$this->t->set_var('version', "v".$search_result['major'].".".$search_result['minor']);
		    		$this->t->set_var('status', $search_result['status']);
		    		
		    		
						$this->t->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $search_result['status']."-16"));
				
						$link_data=null;
						$link_data['menuaction']='ged.ged_ui.browse';
						$link_data['focused_id']=$search_result['element_id'];
						$this->t->set_var('search_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
		    		
		    			
		    		//$nri++;
		    		$this->t->fp('search_list_block_handle', 'search_list_block', True);   
		    	}				
				}
				else
					$this->t->set_block('update_file_tpl', 'search_list_block', 'search_list_block_handle');
			}
			else
				$this->t->set_block('update_file_tpl', 'search_list_block', 'search_list_block_handle');
			 
			/*
			 * display
			 */
			
			$this->t->pfp('out', 'update_file_tpl');
		}

		return ($function_result);	
	}
	
	function set_history($object, $action, $context=null)
	{
		if ( isset($context))
		{
			if (isset($context['comment']))
			{
				$comment=$context['comment'];
			}
			else
			{
				$comment='';
			}
		}
		else
		{
			$comment='';
			
		}
		$this->ged_dm->store_history ($action, $comment, $object['version_id']);
	}
	
	/*
	 * TRIGGERS
	 */
	 
	function apply_transition_to_previous_versions_matching_status($object,$trigger_context=null)
	{
		// DONE : set as parameter
		$transition=$trigger_context['transition'];
		
		$sub_flow=CreateObject('ged.flows');			
		$status_from=$sub_flow->get_transition_status_from($transition);
		
		$versions=$this->ged_dm->get_previous_versions_matching_status($object['version_id'],$status_from);
		
		// CARE : triggered transition should always be 'automatic' ones
		foreach ($versions as $version)
		{
			$loop_object=null;
			$loop_object=$object;
			$loop_object['version_id']=$version['version_id'];

			$do_transition_result=$sub_flow->do_transition($transition, $loop_object);
		}
	}
	
	// Mettre en alerte les dépendances quand on passe obsolète
	function apply_transition_to_linking_versions_with_link_type($object,$trigger_context=null)
	{
		// DONE : set as parameter
		$transition=$trigger_context['transition'];;
		$link_type=$trigger_context['link_type'];

		$sub_flow=CreateObject('ged.flows');			
		$status_from=$sub_flow->get_transition_status_from($transition);
		
		$versions=$this->ged_dm->get_versions_linking_and_matching_criteria($object['version_id'],$status_from, $link_type);

		// CARE : triggered transition should always be 'automatic' ones
		foreach ($versions as $version)
		{
			$loop_element=null;
			$loop_element=$this->ged_dm->get_element_info($version['element_id']);
			$loop_object=array('app' => $object['app'],'project_root' => $loop_element['project_root'], 'doc_type' => $loop_element['doc_type'], 'element_id' => $version['element_id'], 'version_id' => $version['version_id'] );

			$do_transition_result=$sub_flow->do_transition($transition, $loop_object);
		}
		
	}

	// Mettre en alerte les dépendances quand on passe obsolète
	function apply_transition_to_linked_versions_with_link_type($object,$trigger_context=null)
	{
		// DONE : set as parameter
		$transition=$trigger_context['transition'];;
		$link_type=$trigger_context['link_type'];

		$sub_flow=CreateObject('ged.flows');			
		$status_from=$sub_flow->get_transition_status_from($transition);
		
		$versions=$this->ged_dm->get_versions_linked_and_matching_criteria($object['version_id'],$status_from, $link_type);

		// CARE : triggered transition should always be 'automatic' ones
		foreach ($versions as $version)
		{
			$loop_element=null;
			$loop_element=$this->ged_dm->get_element_info($version['element_id']);
			$loop_object=array('app' => $object['app'],'project_root' => $loop_element['project_root'], 'doc_type' => $loop_element['doc_type'], 'element_id' => $version['element_id'], 'version_id' => $version['version_id'] );

			$do_transition_result=$sub_flow->do_transition($transition, $loop_object);
		}
		
	}

}

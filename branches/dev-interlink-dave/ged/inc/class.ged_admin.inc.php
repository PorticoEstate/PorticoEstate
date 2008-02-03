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

class ged_admin
{
	
	var $public_functions=array(
		'types'=>true,
		'places'=>true
	);

	function ged_admin()
	{		
		$this->ged_dm=CreateObject('ged.ged_dm', True);

		/*
		 *  Get sure that the user is a ged admin
		 * else eject the looser
		 */
		if ( ! $this->ged_dm->admin )
		{
			$link_data=null;
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		$this->t = clone ($GLOBALS['phpgw']->template);
		define('GED_TPL', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'ged'));
		$this->t->set_root(GED_TPL);

		if(!@is_object($GLOBALS['phpgw']->css))
		{
			$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
		}
		$GLOBALS['phpgw']->css->validate_file('default','ged');
	}
	
	function display_app_header()
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
	}

	function types()
	{
		$action=get_var('action',array('POST'));
		$doc_types=get_var('doc_types',array('POST'));
		
		if (  $action==lang("add"))
		{
			$this->ged_dm->add_doc_types($doc_types);
		}
		elseif ( $action==lang("update"))
		{
			$this->ged_dm->update_doc_types($doc_types);
		}
		elseif ( $action == lang("delete"))
		{
			$this->ged_dm->delete_doc_types($doc_types);			
		}

		$this->t->set_file(array('types_tpl'=>'types.tpl'));
		
		$doc_types=$this->ged_dm->list_doc_types(false);
		
		$this->t->set_block('types_tpl', 'types_bloc', 'types_bloc_handler');
		$this->t->set_var('types_bloc_handler', '');
		$this->t->set_block('types_bloc', 'action_bloc', 'action_bloc_handler');
		$this->t->set_var('action_bloc_handler', '');
		
		$this->t->set_var('submit_field', "submit");
		$this->t->set_var('submit_value', lang("submit"));
		
		if ( isset($doc_types))
		{
			foreach ( $doc_types as $doc_type )
			{			
				$this->t->set_var('type_id_field', "doc_types[".$doc_type['type_id']."][type_id]");
				$this->t->set_var('type_desc_field', "doc_types[".$doc_type['type_id']."][type_desc]");
				$this->t->set_var('type_ref_field', "doc_types[".$doc_type['type_id']."][type_ref]");
				$this->t->set_var('type_chrono_field', "doc_types[".$doc_type['type_id']."][type_chrono]");
				$this->t->set_var('type_delete_field', "doc_types[".$doc_type['type_id']."][type_delete]");
	
				$this->t->set_var('type_id_value', $doc_type['type_id']);
				$this->t->set_var('type_desc_value', $doc_type['type_desc']);
				$this->t->set_var('type_ref_value', $doc_type['type_ref']);
	
				if ( $doc_type['type_chrono'] == 1)
				{
					$this->t->set_var('type_chrono_checked', 'checked');
				}
				else
				{
					$this->t->set_var('type_chrono_checked', '');				
				}
				
				/*
				 * actions update and delete
				 */
				 
				$this->t->set_var('action_bloc_handler', '');
				
				$this->t->set_var('action_field', 'action');
				$this->t->set_var('action_value', lang('update'));
				$this->t->fp('action_bloc_handler', 'action_bloc', True);
	
				$this->t->set_var('action_field', 'action');
				$this->t->set_var('action_value', lang('delete'));
				$this->t->fp('action_bloc_handler', 'action_bloc', True);
				
				
				$this->t->fp('types_bloc_handler', 'types_bloc', True);
			}
		}
		
		/*
		 * new line to allow admin to add a new doc type
		 */
		 
		$this->t->set_var('type_id_field', "doc_types[new][type_id]");
		$this->t->set_var('type_desc_field', "doc_types[new][type_desc]");
		$this->t->set_var('type_ref_field', "doc_types[new][type_ref]");
		$this->t->set_var('type_chrono_field', "doc_types[new][type_chrono]");
		$this->t->set_var('type_delete_field', "");

		$this->t->set_var('type_id_value', "");
		$this->t->set_var('type_desc_value', "");
		$this->t->set_var('type_ref_value', "");
		$this->t->set_var('type_chrono_checked', '');

		/*
		 * action add
		 */
		
		$this->t->set_var('action_bloc_handler', '');
		
		$this->t->set_var('action_field', 'action');
		$this->t->set_var('action_value', lang('add'));
		$this->t->fp('action_bloc_handler', 'action_bloc', True);


		$this->t->fp('types_bloc_handler', 'types_bloc', True);
		
		$this->display_app_header();
		
		$this->t->pfp('out', 'types_tpl');
	}
	
	function gen_subfolder_select ( $element_id, $field_name, $selected_element_id='', $recursion_level=0)
	{
		if ( isset( $this->cached_gen_subfolders[$element_id]))
		{
			$my_sub_folders=$this->cached_gen_subfolders[$element_id];
		}
		else
		{
			$my_sub_folders=$this->ged_dm->list_sub_folders($element_id);
			$this->cached_gen_subfolder[$element_id]=$my_sub_folders;
		}	

		if ( $recursion_level == 0)
		{
			$select_sub_folders_html="<select name=\"".$field_name."\">\n";
		}
		else
		{
			$select_sub_folders_html="";
		}

		$indent=str_repeat("&nbsp;", $recursion_level*4)."-";
		
		foreach ( $my_sub_folders as $my_sub_folder )
		{
			$selected="";
			if ( $my_sub_folder['element_id'] == $selected_element_id)
			{
				$selected="selected ";
			}
			
			$project_style="";
			if ( $my_sub_folder['element_id'] == $my_sub_folder['project_root'])
			{
				$project_style="style=\"font-weight:bold;\"";
			}
			
			$select_sub_folders_html.="<option ".$project_style." value=\"".$my_sub_folder['element_id']."\" $selected>".$indent.$my_sub_folder['name']."</option>\n";
			
			$select_sub_folders_html.=$this->gen_subfolder_select ( $my_sub_folder['element_id'], $field_name, $selected_element_id, $recursion_level+1);
		}
		
		if ( $recursion_level == 0)
		{
			$select_sub_folders_html.="</select>\n";
		}	
					
		return ( $select_sub_folders_html );		
	}
	
	function gen_unplaced_types_select ( $project_root_id, $field_name )
	{
		$select_unplaced_types_html="<select name=\"".$field_name."\">\n";
		
		$my_unplaced_types=$this->ged_dm->list_unplaced_types($project_root_id);
		
		if ( ! empty($my_unplaced_types))
		{
		foreach ( $my_unplaced_types as $my_unplaced_type )
		{
			$chrono_flag=$style="";
			if ( $my_unplaced_type['type_chrono']==1)
			{
				$chrono_flag=" [C]";
				$style="style=\"font-weight: bold;\"";
			}

			$select_unplaced_types_html.="<option ".$style." value=\"".$my_unplaced_type['type_id']."\" >".$my_unplaced_type['type_desc'].$chrono_flag."</option>\n";
		}
		}
		
		$select_unplaced_types_html.="</select>\n";
		
		return ( $select_unplaced_types_html );
	}
	
	function places()
	{
		//$project_root_id=1;
		$project_root_id=get_var('project_root',array('GET'));
		$action=get_var('action',array('POST'));
		$places=get_var('places',array('POST'));
		
		if (  $action==lang("add"))
		{
			$this->ged_dm->add_places($places);
		}
		elseif ( $action==lang("update"))
		{
			$this->ged_dm->update_places($places);
		}
		elseif ( $action == lang("delete"))
		{
			$this->ged_dm->delete_places($places);			
		}

		$this->t->set_file(array('places_tpl'=>'places.tpl'));
		
		/*
		 * Afficher la liste des projets
		 */
		$this->t->set_block('places_tpl', 'projects_bloc', 'projects_bloc_handler');
		$this->t->set_var('projects_bloc_handler', '');
		
		$projects=$this->ged_dm->list_available_projects();
		
		if ( isset($projects))
		{
			foreach( $projects as $the_project_root => $the_project_name)
			{
				$link_data=null;
				$link_data['menuaction']='ged.ged_admin.places';
				$link_data['project_root']=$the_project_root;
				
				$this->t->set_var('project_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				$this->t->set_var('project_name', $the_project_name);
				 
				$this->t->fp('projects_bloc_handler', 'projects_bloc', True);
			}
		}
		else
		{
			$this->t->set_var('projects_bloc_handler', lang("No project available"));
		}
		
		 
		$this->t->set_block('places_tpl', 'edit_bloc', 'edit_bloc_handler');
		$this->t->set_var('edit_bloc_handler', '');

		if ( is_numeric($project_root_id))
		{
			$types_places=$this->ged_dm->list_types_places($project_root_id);
			
			$this->t->set_block('edit_bloc', 'types_bloc', 'types_bloc_handler');
			$this->t->set_var('types_bloc_handler', '');
			$this->t->set_block('types_bloc', 'action_bloc', 'action_bloc_handler');
			$this->t->set_var('action_bloc_handler', '');
			
			$this->t->set_var('active_project_name', $this->ged_dm->get_project_name($project_root_id));
			
			if ( isset($types_places))
			{
				$i=0;
				foreach ( $types_places as $type_place )
				{			
					$this->t->set_var('type_desc', "<input type=\"hidden\" name=\"places[".$i."][type_id]\" value=\"".$type_place['type_id']."\"/>".$type_place['type_id']);
					$this->t->set_var('select', $this->gen_subfolder_select($project_root_id,"places[".$i."][element_id]", $type_place['element_id']));
					$this->t->set_var('project',"<input type=\"hidden\" name=\"places[".$i."][project_root]\" value=\"".$project_root_id."\"/>");
					
					$this->t->set_var('action_bloc_handler', '');
					
					$this->t->set_var('action_field', 'action');
					$this->t->set_var('action_value', lang('update'));
					$this->t->fp('action_bloc_handler', 'action_bloc', True);
		
					$this->t->set_var('action_field', 'action');
					$this->t->set_var('action_value', lang('delete'));
					$this->t->fp('action_bloc_handler', 'action_bloc', True);
					
					$i++;
					$this->t->fp('types_bloc_handler', 'types_bloc', True);
				}
			}		
			
	
			$this->t->set_var('type_desc', $this->gen_unplaced_types_select ( $project_root_id, "places[new][type_id]" ));
			$this->t->set_var('select', $this->gen_subfolder_select($project_root_id,"places[new][element_id]", 0));
			$this->t->set_var('project',"<input type=\"hidden\" name=\"places[new][project_root]\" value=\"".$project_root_id."\"/>");
			
			/*
			 * action add
			 */
			
			$this->t->set_var('action_bloc_handler', '');
			
			$this->t->set_var('action_field', 'action');
			$this->t->set_var('action_value', lang('add'));
			$this->t->fp('action_bloc_handler', 'action_bloc', True);
	
	
			$this->t->fp('types_bloc_handler', 'types_bloc', True);

			$this->t->fp('edit_bloc_handler', 'edit_bloc', True);
		}
		else
		{
			$this->t->set_var('edit_bloc_handler', lang( "Please choose a project"));
		}
		

		$this->display_app_header();		

		$this->t->pfp('out', 'places_tpl');

	}
	
}
	
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

$GLOBALS['debug']["ged.ged_ui"]=false;

// define zip command fullpath instead of hard coding it
// later it will be possible todefine it using an admin form
define ( "zip_bin", "/usr/bin/zip");

class ged_ui
{
	var $t;
	var $theme;
	var $categories;
	var $ged_dm;
	var $debug=1;
	var $browser;

	var $public_functions=array(
		'browse'=>true, 
		'add_file'=>true, 
		'add_folder'=>true, 
		'update_folder'=>true, 
		'delete_folder'=>true, 
		'download'=>true, 
		'package_download' => true, 
		'view'=>true, 
		'delete_file'=>true, 
		'change_acl'=>true, 
		'search' => true, 
		'stats'=> true, 
		'chrono' => true,
		'flow_do' => true
	);

	var $icons;

	/* TODO document my code in English :P */
	/* Constructor method called when ged_ui object is created */

	function ged_ui()
	{
		//$this->theme = $GLOBALS['phpgw_info']['theme'];
		$this->t = clone ($GLOBALS['phpgw']->template);
		$this->t->set_root(PHPGW_APP_TPL);
		
		// Gestion des groupes et des droits
		//$this->owner=$GLOBALS['phpgw_info']['user']['account_id'];
		//$this->acct=CreateObject('phpgwapi.accounts',$owner);
		//$this->groups=$this->acct->get_list('groups');
		//_debug_array($this->groups);
		//$this->users=$this->acct->get_list('accounts');
		//_debug_array($this->users);
		
		
		$this->ged_dm=CreateObject('ged.ged_dm', True);
		$this->categories=CreateObject('phpgwapi.categories');
		$this->browser=CreateObject('phpgwapi.browser');

		$this->flows=CreateObject('ged.flows');
		
		if(!@is_object($GLOBALS['phpgw']->css))
		{
			$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
		}
		$GLOBALS['phpgw']->css->validate_file('default','ged');
		
		
		//TODO Move this to a function, maybe an api mime class ?
		$this->icons["txt"]="txt";
		$this->icons["doc"]="word";
		$this->icons["rtf"]="document";
		$this->icons["xls"]="excel";
		$this->icons["ppt"]="powerpoint";
		$this->icons["exe"]="binary";
		$this->icons["html"]="html";
		$this->icons["htm"]="html";
		$this->icons["gif"]="image";
		$this->icons["jpg"]="image";
		$this->icons["bmp"]="image";
		$this->icons["png"]="image";
		$this->icons["log"]="log";
		$this->icons["midi"]="midi";
		$this->icons["pdf"]="pdf";
		$this->icons["wav"]="sound";
		$this->icons["mp3"]="sound";
		$this->icons["c"]="source_c";
		$this->icons["cpp"]="source_cpp";
		$this->icons["h"]="source_h";
		$this->icons["java"]="source_java";
		$this->icons["py"]="source_py";
		$this->icons["tar"]="tar";
		$this->icons["gz"]="gz";
		$this->icons["zip"]="gz";
		$this->icons["mpg"]="video";
		$this->icons["avi"]="video";
		$this->icons["tex"]="tex";
		$this->icons["php"]="php";
		$this->icons["wmv"]="wmv_movie";
		$this->icons["default"]="default";

		//_debug_array($GLOBALS);
		//die();
	}

	function debug ($thefunction)
	{
		if ( ! array_key_exists('debug', $GLOBALS))
			$GLOBALS['debug']=Array();
			
		if ( !array_key_exists('all', $GLOBALS['debug']))
			$GLOBALS['debug']['all']=false;
		
		if ( !array_key_exists ("ged.".get_class($this), $GLOBALS['debug']) )
			$GLOBALS['debug']["ged.".get_class($this)]=false;
			
		if ( !array_key_exists ("ged.".get_class($this).".".$thefunction, $GLOBALS['debug']))
			$GLOBALS['debug']["ged.".get_class($this).".".$thefunction]=false;
		
		if ( $GLOBALS['debug']['all'] || $GLOBALS['debug']["ged.".get_class($this)] || $GLOBALS['debug']["ged.".get_class($this).".".$thefunction])
		{
			$GLOBALS['debug']['show']=true;
			return true;
		}
		else
			return false;
	}
	
	function truncate ( $label, $size=50 )
	{
		if ( strlen( $label ) > $size )
		{
			return ( substr($label, 0, $size - 3)."..." );
		}
		else
		{
			return ( $label );
		}
	}
	
	function save_sessiondata()
	{
		//$this->ged_dm->save_sessiondata($data);
	}

	/* a voir plus tard pour la gestion des langues */
	function set_app_langs()
	{
		global $tr_color;

		$this->t->set_var('tr_color', $tr_color);
		$this->t->set_var('font', $this->bo->set_font());
		//$this->t->set_var('font_size', $this->bo->set_font_size());
		$this->t->set_var('name', $GLOBALS['phpgw_info']['user']['fullname']);
	}

	function set_template_defaults()
	{

		$this->t->set_var('parent_id_label', 'parent_id');
		$this->t->set_var('parent_id_field', 'parent_id');
		$this->t->set_var('element_id_field', 'element_id');
		$this->t->set_var('file_field', 'file');
		$this->t->set_var('referenceq_field', 'referenceq');
		$this->t->set_var('description_field', 'description');
		
		$this->t->set_var('lang_file', lang('file'));
		$this->t->set_var('lang_folder', lang('folder'));
		
		$this->t->set_var('description_label', 'description');
		$this->t->set_var('referenceq_label', 'Reference');

		$this->t->set_var('lang_subfolders', lang('Sub folders'));
		$this->t->set_var('lang_owner', lang('Owner'));
		$this->t->set_var('lang_description', lang('Description'));
		$this->t->set_var('lang_type', lang('Type'));
		$this->t->set_var('lang_reference', lang('Reference'));
		$this->t->set_var('lang_name', lang('Name'));
		$this->t->set_var('lang_documents', lang('Documents'));
		$this->t->set_var('lang_Information', lang('Informations'));
		$this->t->set_var('lang_project', lang('Project root'));

		$this->t->set_var('lang_download', lang('Download'));
		$this->t->set_var('lang_view', lang('View'));

		$this->t->set_var('lang_creation_date', lang('Created on'));
		$this->t->set_var('lang_keywords', lang('Keywords'));
		$this->t->set_var('lang_last_maj', lang('Last updated on'));
		$this->t->set_var('lang_current_version', lang('Current version'));
		$this->t->set_var('major_field', 'major');
		$this->t->set_var('minor_field','minor');
		
		$this->t->set_var('project_name_field', 'project_name');
		

		$this->t->set_var('lang_version', lang('Version'));
		$this->t->set_var('lang_version_creation_date', lang('creation date'));
		$this->t->set_var('lang_version_description', lang('description'));
		$this->t->set_var('lang_version_creator', lang('Uploaded by'));
		

		$this->t->set_var('lang_add_file', lang('Add file'));
		$this->t->set_var('lang_add_folder', lang('Add folder'));
		$this->t->set_var('lang_update_file', lang('Update file'));
		$this->t->set_var('lang_update_folder', lang('Update folder'));
		$this->t->set_var('lang_confirm_deletion', lang('Confirm deletion'));

		$this->t->set_var('lang_informations', 'Informations');
		$this->t->set_var('lang_versions', 'Versions');
		$this->t->set_var('lang_file_size', 'File size');
		$this->t->set_var('lang_creator', 'Creator');
		$this->t->set_var('lang_current_version_expiration_date', 'Expiration date');
		$this->t->set_var('lang_current_version_description', 'Description');
		$this->t->set_var('lang_current_version_date', 'Valid since');
		$this->t->set_var('lang_period', lang('Validity'));
		$this->t->set_var('period_field', 'validity_period');
		
		$this->t->set_var('relation_label', lang('Relation'));


		$this->t->set_var('lang_creator', lang('creator'));
		
		$this->t->set_var('lang_update_acl', lang('Update AC'));
		$this->t->set_var('lang_reset_acl', lang('Reset AC'));
		$this->t->set_var('lang_go_back', lang('Go back'));

		$this->t->set_var('action_field', 'default');

		$this->t->set_var('input_default_class', 'text_default');
		$this->t->set_var('input_active_class', 'text_focused');
		$this->t->set_var('textarea_default_class', 'textarea_default');
		$this->t->set_var('textarea_active_class', 'textarea_focused');
		$this->t->set_var('select_default_class', 'select-one_default');
		$this->t->set_var('select_active_class', 'select-one_focused');

		$this->t->set_var('reference_color', 'red');
		
		$this->t->set_var('image_download-48', $GLOBALS['phpgw']->common->image('ged', 'download-48'));
		$this->t->set_var('image_download-32', $GLOBALS['phpgw']->common->image('ged', 'download-32'));
		$this->t->set_var('image_download-16', $GLOBALS['phpgw']->common->image('ged', 'download-16'));
		
		$this->t->set_var('img_view', $GLOBALS['phpgw']->common->image('ged', 'eye'));
		$this->t->set_var('img_edit_version', $GLOBALS['phpgw']->common->image('ged', 'admin_log'));

		$this->t->set_var('lang_up', lang('Up'));
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.browse';
		$link_data['focused_id']=0;
		$top_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
		$this->t->set_var('top_link', "<a href=\"".$top_url."\">".lang('Top')."</a>");
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.search';
		$search_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
		$this->t->set_var('search', "<a href=\"".$search_url."\">".lang('Search')."</a>");

		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.stats';
		$stats_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
		$this->t->set_var('stats', "<a href=\"".$stats_url."\">".lang('Stats')."</a>");
		

	}

	function display_app_header()
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();

		//$this->t->set_file(array('header'=>'header.tpl'));
		//$this->t->set_block('header', 'ged_header');
		//$this->set_app_langs();
		//$this->t->fp('app_header', 'ged_header');

	}

	function gen_select()
	{

	}
	
	// TODO acl here
	function view()
	{
		$version_id=get_var('version_id',array('GET','POST'));

		$version=$this->ged_dm->get_version_info($version_id);
    
		if ($this->browser->is_ie())
		{
			ini_set('zlib.output_compression', 'Off');
			header('Pragma: private');
			header('Cache-control: private, must-revalidate');
			header('Content-type: '.$version['mime_type'], false);
			header('Content-Disposition: inline; filename="'.$version['file_name'].'"', false);
			$download_size=filesize($version['file_full_path']);
			header('Content-Length: '.$download_size, false);
			readfile($version['file_full_path']);	
		}
		else
		{
			header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
			header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT', false); 
			header('Cache-Control: private, must-revalidate');
			header('Content-type: '.$version['mime_type'], false);
			header('Content-Disposition: inline; filename="'.$version['file_name'].'"', false);
			$download_size=filesize($version['file_full_path']);
			header('Content-Length: '.$download_size, false);
			readfile($version['file_full_path']);
		}
    
		exit();
	}
	
	// TODO acl here
	function download()
	{
		$version_id=get_var('version_id',array('GET','POST'));

		$version=$this->ged_dm->get_version_info($version_id);

		if ($this->browser->is_ie())
		{
			ini_set('zlib.output_compression', 'Off');
			header('Pragma: private');
			header('Cache-control: private, must-revalidate');
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment; filename="'.$version['file_name'].'"', false);
			$download_size=filesize($version['file_full_path']);
			header('Content-Length: '.$download_size, false);
			readfile($version['file_full_path']);
		}
		else
		{
			header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
			header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT', false); 
			header('Cache-Control: must-revalidate', false);
			header("Content-type: ".$version['mime_type'], false);
			header('Content-Disposition: attachment; filename="'.$version['file_name'].'"', false);
			$download_size=filesize($version['file_full_path']);
			header('Content-Length: '.$download_size, false);
			readfile($version['file_full_path']);
    		}
    		exit();
    
	}

	// TODO acl here
	function package_download()
	{
		$element_id=get_var('element_id',array('GET','POST'));
		$version_id=get_var('version_id',array('GET','POST'));
		
		if ( $version_id != '' )
			$theversion=$this->ged_dm->get_version_info($version_id);
		else
		{
			$theversion=$this->ged_dm->get_current_or_alert_or_refused_version($element_id);
			$version_id=$theversion['version_id'];
		}
			
		$theversion_sterile_file_name = preg_replace( "/[^\w\.-]+/", "_", $theversion['name'] );
		
		$thetempdir=$GLOBALS['phpgw_info']['server']['temp_dir']."/".$theversion_sterile_file_name;
		
		mkdir($thetempdir, 0700);
		
		$zip_file_name=$theversion_sterile_file_name.'.zip';
		$zip_file_name_full_path=$GLOBALS['phpgw_info']['server']['temp_dir']."/".$zip_file_name;
		
		$list_relations_out=$this->ged_dm->list_version_relations_out($version_id);
		
		if ($list_relations_out !="")
			foreach ($list_relations_out as $list_relation)
			{
				$version=$this->ged_dm->get_version_info($list_relation['version_id']);
				$filename=basename ($version['file_full_path']);
				$filenames[]=$thetempdir."/".$filename;
				copy($version['file_full_path'], $thetempdir."/".$filename);
			}
		
		// zip creation
		$retval=null;
		exec ( "cd ".$GLOBALS['phpgw_info']['server']['temp_dir']."; ".zip_bin." -r ".$theversion_sterile_file_name.".zip ".$theversion_sterile_file_name, $retval);
		

		if ($this->browser->is_ie())
		{
			ini_set('zlib.output_compression', 'Off');
			header('Pragma: private');
			header('Cache-control: private, must-revalidate');
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment; filename="'.$zip_file_name.'"', false);
			$download_size=filesize($zip_file_name_full_path);
			header('Content-Length: '.$download_size, false);
			readfile($zip_file_name_full_path);
		}
		else
		{
			header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
			header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT', false); 
			header('Cache-Control: must-revalidate', false);
			header('Content-type: application/zip', false);
			header('Content-Disposition: attachment; filename="'.$zip_file_name.'"', false);
			$download_size=filesize($zip_file_name_full_path);
			header('Content-Length: '.$download_size, false);
			readfile($zip_file_name_full_path);
    }
    
    foreach ( $filenames as $filename)
    {
    	unlink($filename);
    }
    rmdir ($thetempdir);
    unlink ($zip_file_name_full_path);

		exit();
    
	}

	function draw_tree($focused_id=0, $parent_id=0, $path="", $element_info="", $expand=false)
	{

		if ($path=="")
			$path=$this->ged_dm->get_path($focused_id);

		if ($parent_id=="" || $parent_id==0)
		{
			//on est � la racine
			$parent_id=0;
			$expand=true;
			$element_info['element_id']="0";
			$element_info['name']=lang('Root folder');
			$element_info['type']='folder';
		}

		$elements=$this->ged_dm->list_elements($parent_id);
		$itemcount=count($elements);

		if ($expand==false || $itemcount==0)
		{

			$this->t->set_file(array('zetree'.$parent_id=>'tree_item.tpl'));

			$this->t->set_var('imgback', $GLOBALS['phpgw']->common->image('ged', 'down'));

			if ($element_info['type']=='folder')
			{
				if ($element_info['element_id']==$focused_id)
					$this->t->set_var('imgtype', $GLOBALS['phpgw']->common->image('ged', 'folder_opened'));
				else
					$this->t->set_var('imgtype', $GLOBALS['phpgw']->common->image('ged', 'folder_closed'));
			}
			elseif ($element_info['type']=='file')
			{
				$last_version=$this->ged_dm->get_last_version($element_info['element_id']);

				$extension=$last_version['file_extension'];
				
				if ( isset ( $this->icons[$extension] ) )
				{
				 if ($this->icons[$extension]=='')
						$extension='default';
				}
				else
					$extension='default';
				
				$this->t->set_var('imgtype', $GLOBALS['phpgw']->common->image('ged', $this->icons[$extension]));
			}

			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$element_info['element_id'];
			$this->t->set_var('link', $GLOBALS['phpgw']->link('/index.php', $link_data));

			$this->t->set_var('imgdot', $GLOBALS['phpgw']->common->image('ged', 'right_in'));

			if ($element_info['element_id']==$focused_id)
				$this->t->set_var('label', "<b>".$this->truncate($element_info['name'])."</b>");
			else
				$this->t->set_var('label', $this->truncate($element_info['name']));
			
			$this->t->set_var('title', $element_info['name']);

		}
		else
		{
			//on a forc�ment affaire � un folder avec au moins un element
			$this->t->set_file(array('zetree'.$parent_id=>'tree.tpl'));

			$this->t->set_block('zetree'.$parent_id, 'items_list', 'items_list_handle'.$parent_id);

			for ($i=0; $i < $itemcount; $i ++)
			{
				$current_element=$elements[$i];

				if ($this->ged_dm->is_on_path($current_element, $path))
					$this->t->set_var('itemcontent', $this->draw_tree($focused_id, $current_element['element_id'], "", $current_element, true));
				else
					$this->t->set_var('itemcontent', $this->draw_tree($focused_id, $current_element['element_id'], "", $current_element, false));

				if ($i +1==$itemcount)
				{
					$this->t->set_var('itemimgright', $GLOBALS['phpgw']->common->image('ged', 'right_last'));
					$this->t->set_var('itemimgback', $GLOBALS['phpgw']->common->image('ged', ''));
				}
				else
				{
					$this->t->set_var('itemimgright', $GLOBALS['phpgw']->common->image('ged', 'right'));
					$this->t->set_var('itemimgback', $GLOBALS['phpgw']->common->image('ged', 'down'));
				}

				$this->t->fp('items_list_handle'.$parent_id, 'items_list', True);
			}

			if ($parent_id==0)
				$this->t->set_var('imgdot', $GLOBALS['phpgw']->common->image('ged', 'to_down'));
			else
				$this->t->set_var('imgdot', $GLOBALS['phpgw']->common->image('ged', 'right_in_to_down'));

			$this->t->set_var('imgback', $GLOBALS['phpgw']->common->image('ged', 'down'));

			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$element_info['element_id'];
			$this->t->set_var('link', $GLOBALS['phpgw']->link('/index.php', $link_data));

			$this->t->set_var('imgtype', $GLOBALS['phpgw']->common->image('ged', 'folder_opened'));

			if ($element_info['element_id']==$focused_id)
				$this->t->set_var('label', "<b>".$this->truncate($element_info['name'])."</b>");
			else
				$this->t->set_var('label', $this->truncate($element_info['name']));
				
			$this->t->set_var('title', $element_info['name']);

		}

		$out=$this->t->parse('zetree'.$parent_id, 'zetree'.$parent_id);

		return $out;

	}

	function draw_file_panel($element_id, $version_id)
	{
		if ( $this->debug('draw_file_panel') )
			print ( "draw_file_panel: entering with element_id=".$element_id."<br/>\n");

		$this->t->set_file(array('file_tpl'=>'file.tpl'));

		$element_info=$this->ged_dm->get_element_info($element_id);
		
		$current_version=$this->ged_dm->get_version_info($version_id);
		
		$this->t->set_var('current_version_status_image', $GLOBALS['phpgw']->common->image('ged', $current_version['status']."-48"));

		$list_versions=$this->ged_dm->list_versions($element_id);
		
		$list_relations_out=$this->ged_dm->list_version_relations_out($current_version['version_id']);
		
		$this->t->set_block('file_tpl', 'relations_list', 'relations_list_handle');
		
		$relation_odd_even='odd';

		if ($list_relations_out !="")
		foreach ($list_relations_out as $list_relation)
		{
			if ( $relation_odd_even=='odd' )
				$relation_odd_even='even';
			else
				$relation_odd_even='odd';

			$this->t->set_var('relation_name', $list_relation['name']);
			$this->t->set_var('relation_reference', $list_relation['reference']);
			$this->t->set_var('relation_version', $list_relation['major'].".".$list_relation['minor']);
			$this->t->set_var('relation_status_value', $list_relation['status']);
			$this->t->set_var('relation_status_image', $GLOBALS['phpgw']->common->image('ged', $list_relation['status']."-16"));
			
			$link_data=null;$this->t->set_var('relation_status_oe', 'file_version_'.$list_relation['status'].'_'.$relation_odd_even);
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$list_relation['element_id'];
			$link_data['version_id']=$list_relation['version_id'];
			$this->t->set_var('relation_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
			
			$this->t->set_var('relation_status_oe', 'file_version_'.$list_relation['status'].'_'.$relation_odd_even);
			
			$this->t->fp('relations_list_handle', 'relations_list', True);
		}	
		
		if ($list_relations_out !="")
		{
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.package_download';
			$link_data['element_id']=$current_version['element_id'];
			$link_data['version_id']=$current_version['version_id'];
			$this->t->set_var('relation_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
			
			$this->t->set_var('download_all_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
		}
    
		$list_relations_in=$this->ged_dm->list_version_relations_in($current_version['version_id']);
		
		$this->t->set_block('file_tpl', 'relations_list2', 'relations_list2_handle');
		
		$relation_odd_even='odd';
		
		if ($list_relations_in !="")
		foreach ($list_relations_in as $list_relation)
		{
			if ( $relation_odd_even=='odd' )
				$relation_odd_even='even';
			else
				$relation_odd_even='odd';

			$this->t->set_var('relation_name', $list_relation['name']);
			$this->t->set_var('relation_reference', $list_relation['reference']);
			$this->t->set_var('relation_version', $list_relation['major'].".".$list_relation['minor']);
			$this->t->set_var('relation_status_value', $list_relation['status']);
			$this->t->set_var('relation_status_image', $GLOBALS['phpgw']->common->image('ged', $list_relation['status']."-16"));
			
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$list_relation['element_id'];
			$link_data['version_id']=$list_relation['version_id'];
			$this->t->set_var('relation_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
			
			$this->t->set_var('relation_status_oe', 'file_version_'.$list_relation['status'].'_'.$relation_odd_even);
			
			$this->t->fp('relations_list2_handle', 'relations_list2', True);
		}	
		

		$this->t->set_block('file_tpl', 'versions_list', 'versions_list_handle');

		$versions_numcol=2;
		$file_version_odd_even='odd';

		if ($list_versions !="")
		{
			$list_versions=array_reverse($list_versions, true);
			foreach ($list_versions as $version_info)
			{
				if ( $file_version_odd_even=='odd' )
					$file_version_odd_even='even';
				else
					$file_version_odd_even='odd';
								
				$this->t->set_var('version', $version_info['major'].".".$version_info['minor']);
				$this->t->set_var('version_creation_date', $GLOBALS['phpgw']->common->show_date($version_info['creation_date']));
				$this->t->set_var('version_description', $version_info['description']);
				$this->t->set_var('version_creator', $GLOBALS['phpgw']->common->grab_owner_name($version_info['creator_id']));
				$this->t->set_var('version_status_value', $version_info['status']);
				
				$this->t->set_var('ged_version_class',$version_info['status']);
	
				$mime_type=$version_info['mime_type'];
							
				$extension=$version_info['file_extension'];
	
				if ( isset ( $this->icons[$extension] ) )
				{
				 if ($this->icons[$extension]=='')
						$extension='default';
				}
				else
					$extension='default';
	
				$this->t->set_var('version_img_mimetype', $GLOBALS['phpgw']->common->image('ged', $this->icons[$extension]));
				$this->t->set_var('version_status_image', $GLOBALS['phpgw']->common->image('ged', $version_info['status']."-32"));
	
				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.browse';
				$link_data['focused_id']=$element_id;			
				$link_data['version_id']=$version_info['version_id'];
				$this->t->set_var('show_version_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
	
				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.download';
				$link_data['version_id']=$version_info['version_id'];
				$this->t->set_var('download_file_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				$this->t->set_var('download_file_target', '');
	
				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.view';
				$link_data['version_id']=$version_info['version_id'];
				$this->t->set_var('view_file_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
	
				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.edit_version';
				$link_data['element_id']=$version_info['element_id'];
				$link_data['version_id']=$version_info['version_id'];
				$this->t->set_var('edit_version_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
	
				if ($this->browser->is_ie())
				{
					$this->t->set_var('view_file_target', '_blank');
				}
				else
				{
					$this->t->set_var('view_file_target', '');
				}
				
				$this->t->set_var('file_version_status_oe', 'file_version_'.$version_info['status'].'_'.$file_version_odd_even);
				
	
				$this->t->fp('versions_list_handle', 'versions_list', True);
	
				$versions_numcol ++;
			}
		}

		if ( $this->debug('draw_file_panel') )
			print ( "draw_file_panel: end of version list<br/>\n");
		
		$extension=$current_version['file_extension'];

		if ( isset ( $this->icons[$extension] ) )
		{
		 if ($this->icons[$extension]=='')
				$extension='default';
		}
		else
			$extension='default';

		if ( $element_info['validity_period'] > 0 and isset($current_version['validation_date']))
		{
			$expiration_date=$current_version['validation_date']+$element_info['validity_period'];
			
			if ( $expiration_date < time() )
				$expiration_date='Need acceptation';
			else
				$expiration_date=date("d/m/y", $expiration_date);
		}
		else
		{
			$expiration_date="N/A";
		}
			
			
		$file_array_vars=Array('versions_numcol'=>$versions_numcol, 'owner'=>$GLOBALS['phpgw']->common->grab_owner_name($element_info['owner_id']), 'description'=>$element_info['description'], 'reference'=>$element_info['reference'], 'name'=>$element_info['name'], 'creation_date'=>$GLOBALS['phpgw']->common->show_date($element_info['creation_date']), 'current_version_date'=>$GLOBALS['phpgw']->common->show_date($current_version['creation_date']), 'current_version'=>$current_version['major'].".".$current_version['minor'], 'current_version_description'=>$current_version['description'], 'current_version_creator'=>$GLOBALS['phpgw']->common->grab_owner_name($current_version['creator_id']), 'current_version_file_size'=>$current_version['size'], 'current_version_img_mime_type'=>$GLOBALS['phpgw']->common->image('ged', $this->icons[$extension]), 'current_version_expiration_date'=>$expiration_date, 'current_version_mime_type'=>$current_version['mime_type']);

		$this->t->set_var($file_array_vars);

		$out=$this->t->parse('file_tpl', 'file_tpl');

		return $out;

	}

	function draw_folder_panel($element_id)
	{
		$this->t->set_file(array('folder_tpl'=>'folder.tpl'));

		if ($element_id==0)
		{
			$element_info['reference']=lang('Root folder');
			$element_info['name']=lang('Root folder');
			$element_info['description']=lang('Root folder');
			$element_info['owner_id']=0;
			$element_info['creation_date']=0;
		}
		else
		{
			$element_info=$this->ged_dm->get_element_info($element_id);
		}

		$this->t->set_var('owner', $GLOBALS['phpgw']->common->grab_owner_name($element_info['owner_id']));
		$this->t->set_var('description', $this->truncate($element_info['description'],30));
		$this->t->set_var('reference', $element_info['reference']);
		$this->t->set_var('name', $this->truncate($element_info['name']));
		$this->t->set_var('creation_date', $GLOBALS['phpgw']->common->show_date($element_info['creation_date']));
		
		$sub_folders=null;
		$sub_folders=$this->ged_dm->list_elements($element_id, 'folder');

		$this->t->set_block('folder_tpl', 'subfolders_list', 'subfolders_list_handle');
		$this->t->set_var('subfolders_list_handle', "");

		if (isset($sub_folders))
		{
			$tr_class='';
			foreach ($sub_folders as $subfolder)
			{
				if ( $tr_class=='row_off' )
					$tr_class='row_on';
				else
					$tr_class='row_off';
				
				$this->t->set_var('tr_class', $tr_class);
				
				$this->t->set_var('folder_name', $this->truncate($subfolder['name']));
				$this->t->set_var('folder_reference', $subfolder['reference']);
				$this->t->set_var('folder_description', $subfolder['description']);
				$this->t->set_var('folder_owner', $GLOBALS['phpgw']->common->grab_owner_name($subfolder['owner_id']));

				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.browse';
				$link_data['focused_id']=$subfolder['element_id'];
				$this->t->set_var('folder_link', $GLOBALS['phpgw']->link('/index.php', $link_data));

				$this->t->fp('subfolders_list_handle', 'subfolders_list', True);
			}
		}

		$files=null;
		$files=$this->ged_dm->list_elements($element_id, 'file');
		
		$this->t->set_block('folder_tpl', 'files_list', 'files_list_handle');
		$this->t->set_var('files_list_handle','');

		if (isset($files))
		{
			$tr_class='row_off';
			foreach ($files as $file)
			{
				if ( $tr_class=='row_off' )
					$tr_class='row_on';
				else
					$tr_class='row_off';
				
				$file_version=null;

					$file_version=$this->ged_dm->get_last_version($file['element_id']);
				
				$this->t->set_var('tr_class', $tr_class);

				$this->t->set_var('file_name', $this->truncate($file['name']));
				$this->t->set_var('file_reference', $file['reference']);
				$this->t->set_var('file_description', $file['description']);
				$this->t->set_var('file_version', "v".$file_version['major'].".".$file_version['minor']);
				$this->t->set_var('file_status_image', $GLOBALS['phpgw']->common->image('ged', $file_version['status']."-16"));

				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.browse';
				$link_data['focused_id']=$file['element_id'];
				$this->t->set_var('file_link', $GLOBALS['phpgw']->link('/index.php', $link_data));

				$this->t->fp('files_list_handle', 'files_list', True);
			}
		}

		$out=$this->t->parse('folder_tpl', 'folder_tpl');

		return $out;
	}

	function draw_history_panel ( $element_id)
	{
		$this->t->set_file(array('history_tpl'=>'history.tpl'));

		$history=$this->ged_dm->get_history($element_id);
		
		$this->t->set_block('history_tpl', 'event_list', 'event_list_handle');
		$event_odd_even='odd';
		
		if ( $history )
		{
			$history=array_reverse($history);
			
			foreach ( $history as $event)
			{
				if ( $event_odd_even=='odd' )
					$event_odd_even='even';
				else
					$event_odd_even='odd';
				
				$this->t->set_var('event_status_oe', 'event_'.$event_odd_even);
				
				$this->t->set_var('icon', $GLOBALS['phpgw']->common->image('ged', $event['status']."-16"));
				$this->t->set_var('version', $event['major'].".".$event['minor']);
				$this->t->set_var('action', $event['action']);
				$this->t->set_var('actor', $GLOBALS['phpgw']->common->grab_owner_name($event['account_id']));
				$this->t->set_var('comment', $event['comment']);
				$this->t->set_var('date', $GLOBALS['phpgw']->common->show_date($event['logdate']));
				
				$this->t->fp('event_list_handle', 'event_list', True);
			}
			
			
		}

		$out=$this->t->parse('history_tpl', 'history_tpl');

		return $out;
	}
	
	function draw_url_panel($element_id)
	{
		return "url".$element_id;
	}

	function browse()
	{
		if ( $this->debug('browse') )
			print ( "browse: entering<br>\n");

		$focused_id=(int)get_var('focused_id',array('GET','POST'));
		$focused_version_id=(int)get_var('version_id',array('GET','POST'));

		if ($focused_id=="" || ! $this->ged_dm->can_read($focused_id))
			$focused_id=0;
			
		$this->set_template_defaults();

		$this->t->set_file(array('browse_file_tpl'=>'browse.tpl'));

		$this->t->set_var('tree', $this->draw_tree($focused_id));

		$focused_element=$this->ged_dm->get_element_info($focused_id);
		
		// MEMO Link to go up
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.browse';
		
		if ( $focused_id != 0)
		{
			$link_data['focused_id']=$focused_element['parent_id'];
		}
		
		$up_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
		$this->t->set_var('up_link', "<a href=\"".$up_url."\">".lang('Up')."</a>" );


		if ($focused_id==0)
		{
			$focused_element['type']='folder';
		}

		if ( $this->debug('browse') )
			print ( "browse: focused_element type=".$focused_element['type']."<br>\n");		

		switch ($focused_element['type'])
		{
			case 'file' :

				if ( $this->debug('browse') )
					print ( "browse: case file<br/>\n");
				
				$last_version=$this->ged_dm->get_last_version($focused_id);
				
				if ( empty($last_version['element_id']) )
				{
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$focused_element['parent_id'];
						
					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
				
				if ($focused_version_id != 0)
				{
					$focused_version=$this->ged_dm->get_version_info($focused_version_id);
					
					if ( isset($this->ged_dm->acl[$focused_element['element_id']]['statuses']) && is_array($this->ged_dm->acl[$focused_element['element_id']]['statuses']))
				{
						if ( ! in_array($focused_version['status'], $this->ged_dm->acl[$focused_element['element_id']]['statuses']));
				{
					$focused_version=$last_version;
							$focused_version_id=$focused_version['version_id'];
						}
				}
				
				}
				else
				{
					$focused_version=$last_version;
				}
								
				$this->t->set_var('main_content', $this->draw_file_panel($focused_id, $focused_version['version_id']));
				$this->t->set_var('history_content', $this->draw_history_panel($focused_id));
				$this->t->set_var('add_folder', '');
				$this->t->set_var('lang_add_folder', '');
				$this->t->set_var('add_file', '');
				$this->t->set_var('lang_add_file', '');
				
				$flow_object=array(
					'app' => 'ged',
					'project_root' => (int)$focused_element['project_root'], 
					'doc_type' => $focused_element['doc_type'], 
					'element_id' => (int)$focused_id, 
					'version_id' => (int)$focused_version['version_id']
				);
				$flow_actions=$this->flows->get_available_transitions($flow_object);
				
				$this->t->set_block('browse_file_tpl', 'flow_actions_list', 'flow_actions_list_handle');
				
				foreach ($flow_actions as $flow_action)
				{
					$this->t->set_var('flow_do', $flow_action['action']);
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.flow_do';
					$link_data['element_id']=$focused_id;
					$link_data['version_id']=$focused_version['version_id'];
					$link_data['transition']=$flow_action['transition'];
					$this->t->set_var('flow_do_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
										
					$this->t->fp('flow_actions_list_handle', 'flow_actions_list', True);
				}	
								 				
				if ( $this->ged_dm->can_change_acl($focused_id) )
				{
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.change_acl';
					$link_data['element_id']=$focused_id;
					$update_file_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('change_acl', "<a href=\"".$update_file_url."\">".lang('Change ACL')."</a>");
				}
				
				// TODO : droit specifique de delete ?
				if ( $this->ged_dm->can_delete($focused_id) )
				{
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.delete_file';
					$link_data['element_id']=$focused_id;
					$delete_file_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('delete_file', "<a href=\"".$delete_file_url."\">".lang('Delete file')."</a>");
				}
				
				break;
			case 'folder' :
				$this->t->set_var('main_content', $this->draw_folder_panel($focused_id));
				$this->t->set_var('history_content', '');
				$this->t->set_var('lang_update_file', '');
				$this->t->set_var('update_file', '');

					// TODO if acl write DONE
				if ( $this->ged_dm->can_write($focused_id) )
				{
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.add_file';
					$link_data['parent_id']=$focused_id;
					$add_file_link=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('add_file', "<a href=\"".$add_file_link."\">".lang('Add file')."</a>");
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.add_folder';
					$link_data['parent_id']=$focused_id;
					$add_folder_link=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('add_folder', "<a href=\"".$add_folder_link."\">".lang('Add folder')."</a>" );
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.update_folder';
					$link_data['element_id']=$focused_id;
					$add_folder_link=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('update_folder', "<a href=\"".$add_folder_link."\">".lang('Update folder')."</a>" );

				}
					
				// TODO if acl acl add a change acl link
				if ( $this->ged_dm->can_change_acl($focused_id) )
				{
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.change_acl';
					$link_data['element_id']=$focused_id;
					$update_file_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('change_acl', "<a href=\"".$update_file_url."\">".lang('Change ACL')."</a>");
				}
				
				// TODO : droit specifique de delete ?
				if ( $this->ged_dm->can_delete($focused_id) )
				{
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.delete_folder';
					$link_data['element_id']=$focused_id;
					$delete_folder_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
					$this->t->set_var('delete_folder', "<a href=\"".$delete_folder_url."\">".lang('Delete folder')."</a>");
				}
					
				break;
			case "url" :
				$this->t->set_var('main_content', $this->draw_url_panel($focused_id));
				$this->t->set_var('add_folder', '');
				break;
		}
		
		if ( isset ($focused_element['project_root']) && $focused_element['project_root'] != 0 && $this->ged_dm->can_change_acl($focused_element['project_root']))
		{
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.chrono';
			$link_data['project_root']=$focused_element['project_root'];
			$delete_folder_url=$GLOBALS['phpgw']->link('/index.php', $link_data);
			$this->t->set_var('chrono', "<a href=\"".$delete_folder_url."\">".lang('Display chrono')."</a>");

		}

		$this->display_app_header();
		
		$this->t->pfp('out', 'browse_file_tpl');

	}

	// New status management : at first status=working
	function add_file()
	{
		$parent_id=get_var('parent_id',array('GET','POST'));
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.browse';
		$link_data['focused_id']=$parent_id;

		if ( ! $this->ged_dm->can_write($parent_id) )
		{
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$parent_id;
				
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}
		
		$add_file=get_var('add_file',array('GET','POST'));
		$name=addslashes(get_var('name',array('GET','POST')));
		$referenceq=addslashes(get_var('referenceq',array('GET','POST')));
		$major=addslashes(get_var('major',array('GET','POST')));
		$minor=addslashes(get_var('minor',array('GET','POST')));
		$description=addslashes(get_var('description', array('GET', 'POST')));
		$doc_type=addslashes(get_var('document_type', array('GET', 'POST')));
		$validity_period=get_var('validity_period', array('GET', 'POST'));

		$this->set_template_defaults();

		if ($parent_id=="")
			$parent_id=0;

		if ($add_file==lang('Add file'))
		{

			$new_file['file_name']=$_FILES['file']['name'];
			$new_file['file_size']=$_FILES['file']['size'];
			$new_file['file_tmp_name']=$_FILES['file']['tmp_name'];
			$new_file['file_mime_type']=$_FILES['file']['type'];
			$new_file['parent_id']=$parent_id;
			$new_file['name']=$name;
			$new_file['reference']=$referenceq;
			$new_file['major']=$major;
			$new_file['minor']=$minor;
			$new_file['description']=$description;
			$new_file['doc_type']=$doc_type;
			$new_file['validity_period']=$validity_period;

			$this->ged_dm->add_file($new_file);
			$file_added='done';

			if ($file_added=='done')
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

		}

		$this->t->set_file(array('add_file_tpl'=>'add_file.tpl'));

		$this->set_template_defaults();

		$parent_element=$this->ged_dm->get_element_info($parent_id);

		$select_types=$this->ged_dm->list_doc_types ();

		$select_types_html="<select name=\"document_type\">\n";
		foreach ($select_types as $select_type)
		{
			$chrono_flag=$style="";
			if ( $select_type['type_chrono']==1)
			{
				$chrono_flag=" [C]";
				$style="style=\"font-weight: bold;\"";
			}
			$select_types_html.="<option ".$style." value=\"".$select_type['type_id']."\">".lang($select_type['type_desc']).$chrono_flag."</option>\n";
		}
		$select_types_html.="</select>\n";

		$this->t->set_var('select_type', $select_types_html);
		
		// TODO precalcul de la reference
		// TODO attention : forcer si repertoire chrono
		// TODO reprendre ce qui a ete fait pour le wizard
		if ( ! isset ($reference))
			$reference="";
			
		if ($reference !="")
			$this->t->set_var('new_reference', $reference);
		elseif ( isset($parent_element['reference']))
			$this->t->set_var('new_reference', $parent_element['reference']);
		else
			$this->t->set_var('new_reference', "");

		$this->t->set_var('parent_id_value', $parent_id);
		$this->t->set_var('name_value', $name);
		$this->t->set_var('description_value', $description);
		$this->t->set_var('major_reference', 0);
		$this->t->set_var('minor_reference', 1);
		

		$add_link_data['menuaction']='ged.ged_ui.add_file';
		$this->t->set_var('action_add', $GLOBALS['phpgw']->link('/index.php', $add_link_data));
		
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


		$this->display_app_header();

		$this->t->pfp('out', 'add_file_tpl');

	}

	function delete_file()
	{
		
		$element_id=get_var('element_id', array('GET', 'POST'));
		$delete_file=get_var('delete_file', array('GET', 'POST'));

		// Contr�le des droits	
		if ( ! $this->ged_dm->can_write($element_id) || $element_id==0 )
		{
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$element_id;
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		// Confirmation faite
		if ($delete_file==lang('Confirm deletion') )
		{
			$parent_id=$this->ged_dm->delete_element($element_id);

			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$parent_id;
	
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		//Affichage du formulaire de confirmation
		$element_info=$this->ged_dm->get_element_info($element_id);
		
		$this->set_template_defaults();
		$this->display_app_header();

		$this->t->set_file(array('delete_element_tpl'=>'delete_element.tpl'));

		$this->t->set_var('element_name', $element_info['name']);
		$this->t->set_var('element_type', lang($element_info['type']));
		$this->t->set_var('element_id_value', $element_id);
		$this->t->set_var('confirm_delete_field', 'delete_file');
		$this->t->set_var('confirm_delete_value', lang('Confirm deletion'));

		$this->t->pfp('out', 'delete_element_tpl');
	
	}

	function add_folder()
	{
		$parent_id=get_var('parent_id', array('GET', 'POST'));
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.browse';
		$link_data['focused_id']=$parent_id;

		if ( ! $this->ged_dm->can_write($parent_id) )
		{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		$add_folder=get_var('add_folder', array('GET', 'POST'));
		$name=addslashes(get_var('name', array('GET', 'POST')));
		$description=addslashes(get_var('description', array('GET', 'POST')));
		$referenceq=addslashes(get_var('referenceq', array('GET', 'POST')));
		$project_name=addslashes(get_var('project_name', array('GET', 'POST')));

		$this->set_template_defaults();

		if ($parent_id=="")
		{
			$parent_id=0;
		}

		if ($add_folder==lang('Add folder'))
		{

			$new_folder['parent_id']=$parent_id;
			$new_folder['name']=$name;
			$new_folder['referenceq']=$referenceq;
			$new_folder['description']=$description;
			$new_folder['project_name']=$project_name;

			$this->ged_dm->add_folder($new_folder);

			$folder_added='done';

			if ($folder_added=='done')
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

		}

		$this->t->set_file(array('add_folder_tpl'=>'add_folder.tpl'));

		$this->set_template_defaults();

		$parent_element=$this->ged_dm->get_element_info($parent_id);

		// TODO precalcul de la reference
		// TODO attention : forcer si r�pertoire chrono
		if ( ! isset ($reference))
			$reference="";
			
		if ($reference !="")
			$this->t->set_var('new_reference', $reference);
		elseif ($parent_id != 0 )
			$this->t->set_var('new_reference', $parent_element['reference']."/");
		else
			$this->t->set_var('new_reference', '');

		$this->t->set_var('parent_id_value', $parent_id);
		$this->t->set_var('name_value', $name);
		$this->t->set_var('description_value', $description);
		$this->t->set_var('lang_add_folder', lang('Add folder'));
		
		$this->t->set_block('add_folder_tpl', 'project_block', 'project_block_handle');
		
		if ( $parent_id == 0 || is_null($parent_element['project_root']) || $parent_element['project_root']=='' )
		{
			$this->t->set_var('project_name_value', '');
			$this->t->fp('project_block_handle', 'project_block', True);
		}
		
		
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.add_folder';
		$this->t->set_var('action_add', $GLOBALS['phpgw']->link('/index.php', $link_data));

		$this->display_app_header();

		$this->t->pfp('out', 'add_folder_tpl');

	}

	function update_folder()
	{
		
		$element_id=get_var('element_id', array('GET', 'POST'));
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.browse';
		$link_data['focused_id']=$element_id;

		if ( ! $this->ged_dm->can_write($element_id) )
		{
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		$update_folder=get_var('update_folder', array('GET', 'POST'));
		
		$folder_name=get_var('folder_name', array('GET', 'POST'));
		$folder_description=get_var('folder_description', array('GET', 'POST'));
		$folder_reference=get_var('folder_reference', array('GET', 'POST'));
		$project_name=get_var('project_name', array('GET', 'POST'));

		$this->set_template_defaults();
		
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.update_folder';
  	$this->t->set_var('action_update', $GLOBALS['phpgw']->link('/index.php', $link_data));

		$this->t->set_var('reset_folder_field', 'reset_folder');
		$this->t->set_var('reset_folder_action', lang('Undo'));
		$this->t->set_var('update_folder_field', 'update_folder');
		$this->t->set_var('update_folder_action', lang('Update'));
		
		$this->t->set_var('project_name_field', 'project_name');

		$this->t->set_var('element_id_field', 'element_id');
		$this->t->set_var('folder_name_field', 'folder_name');
		$this->t->set_var('folder_reference_field', 'folder_reference');
		$this->t->set_var('folder_description_field', 'folder_description');

		if ($update_folder==lang('Update'))
		{

			$new_folder['element_id']=$element_id;

			$new_folder['name']=$folder_name;
			$new_folder['description']=$folder_description;
			$new_folder['reference']=$folder_reference;
			$new_folder['project_name']=$project_name;

			$this->ged_dm->update_file($new_folder);

			$folder_updated='done';

			if ($folder_updated=='done')
			{
				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.browse';
				$link_data['focused_id']=$element_id;

				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

		}
		else
		{
			$focused_element=$this->ged_dm->get_element_info($element_id);
			$folder_name=$focused_element['name'];
			$folder_description=$focused_element['description'];
			$folder_reference=$focused_element['reference'];
			$project_name=$focused_element['project_name'];
			$project_root=$focused_element['project_root'];
      
		}

		$this->t->set_file(array('update_folder_tpl'=>'update_folder.tpl'));

		$this->t->set_var('element_id_value', $element_id);
		
		$this->t->set_block('update_folder_tpl', 'project_block', 'project_block_handle');
		
		if ( $project_root == $focused_element['element_id'] || $project_root == null || $project_root == 0)
		{
			$this->t->set_var('project_name_value', $project_name);
			$this->t->fp('project_block_handle', 'project_block', True);
		}
    
    /* folder */
		$this->t->set_var('folder_description_value', $folder_description);
		$this->t->set_var('folder_reference_value', $folder_reference);
		$this->t->set_var('folder_name_value', $folder_name);        

		$this->display_app_header();

		$this->t->pfp('out', 'update_folder_tpl');

	}
	
	// DONE : gerer la confirmation 
	// TODO : Afficher quelques details... nom etc.
	function delete_folder()
	{
		$element_id=get_var('element_id', array('GET', 'POST'));
		$delete_folder=get_var('delete_folder', array('GET', 'POST'));

		// Contr�le des droits	
		if ( ! $this->ged_dm->can_write($element_id) || $element_id==0 )
		{
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$element_id;
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		// Confirmation faite
		if ($delete_folder==lang('Confirm deletion') )
		{
			$parent_id=$this->ged_dm->delete_element($element_id);

			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$parent_id;
	
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}

		//Affichage du formulaire de confirmation
		$element_info=$this->ged_dm->get_element_info($element_id);
		
		$this->set_template_defaults();
		$this->display_app_header();
		

		$this->t->set_file(array('delete_element_tpl'=>'delete_element.tpl'));

		$this->t->set_var('element_name', $element_info['name']);
		$this->t->set_var('element_type', lang($element_info['type']));
		$this->t->set_var('element_id_value', $element_id);
		$this->t->set_var('confirm_delete_field', 'delete_folder');
		$this->t->set_var('confirm_delete_value', lang('Confirm deletion'));

		$this->t->pfp('out', 'delete_element_tpl');

	}
	
	function change_acl ()
	{
		$element_id=get_var('element_id', array('GET', 'POST'));
		$update_acl=get_var('update_acl', array('POST'));
		$statuses=$this->flows->get_app_statuses('ged');
		
		//DEBUG
		//_debug_array($statuses);
		
		$this->set_template_defaults();

		$this->display_app_header();
		
		$this->t->set_file(array('change_acl_tpl'=>'change_acl.tpl'));
		
		//Update if necessary toussa
		if ( $update_acl==lang ( "Update AC" ))
		{
			//DEBUG
			//_debug_array( $_POST );
			$newacl=null;
			$newacl=get_var('newacl', array('POST'));
			
			if ( $newacl['account_id'] !="" && ( $newacl['read']=='on' || $newacl['write']=='on' || $newacl['delete']=='on' || $newacl['changeacl']=='on') )
			{
				$read=null;
				$write=null;
				$delete=null;
				$changeacl=null;
				$recursive=false;
				$the_status_array=array();
				
				if ( isset($newacl['read']) && $newacl['read']=='on' )
					$read=1;

				if ( isset($newacl['statuses']) && is_array($newacl['statuses']) )
				{				
					foreach ( $newacl['statuses'] as $granted_status_label => $granted_status_value )
					{
						if ( $granted_status_value == "on")
						{
							$the_status_array[]=$granted_status_label;
						}
					}
				}
				
				//DEBUG
				//_debug_array($the_status_array);
				
				if ( isset($newacl['write']) && $newacl['write']=='on' )
					$write=1;
				
				if ( isset($newacl['delete']) && $newacl['delete']=='on' )
					$delete=1;

				if ( isset($newacl['changeacl']) && $newacl['changeacl']=='on' )
					$changeacl=1;
				
				if ( isset($newacl['recursive']) && $newacl['recursive']=='on' )
							$recursive=true;
					
				$this->ged_dm->new_acl($element_id, $newacl['account_id'], $read, $the_status_array, $write, $delete, $changeacl, $recursive);
			
			}
			
			$acl=null;
			$acl=get_var('acl', array('POST'));
			
			//DEBUG
			//_debug_array($acl);
		
			if ( ! empty ( $acl ) )
				foreach ( $acl as $acl_id=>$ac )
				{
					$recursive=false;
					
					if ( ! array_key_exists('read', $ac))
						$ac['read']='';
					
					if ( ! array_key_exists('write', $ac))
						$ac['write']='';

					if ( ! array_key_exists('delete', $ac))
						$ac['delete']='';

					if ( ! array_key_exists('changeacl', $ac))
						$ac['changeacl']='';
					
					if ( $ac['read']=='on' || $ac['write']=='on' || $ac['delete']=='on' || $ac['changeacl']=='on' )
					{
						$read=null;
						$write=null;
						$delete=null;
						$changeacl=null;
						$the_status_array=array();
						
						if ( $ac['read']=='on' )
							$read=1;
		
						if ( isset($ac['statuses']) && is_array($ac['statuses']))
						{
							foreach ( $ac['statuses'] as $granted_status_label => $granted_status_value )
							{
								if ( $granted_status_value == "on")
								{
									$the_status_array[]=$granted_status_label;
								}
							}
						}
						
						//DEBUG
						//_debug_array($the_status_array);
						
						if ( $ac['delete']=='on' )
							$delete=1;

						if ( $ac['write']=='on' )
							$write=1;
						
						if ( $ac['changeacl']=='on' )
							$changeacl=1;
						
						if ( isset ($ac['recursive']))	
							if ( $ac['recursive']=='on' )
								$recursive=true;

						$this->ged_dm->set_acl($acl_id, $read, $the_status_array, $write, $delete, $changeacl,$recursive);
					}
					else
					{
						if ( isset($ac['recursive']) && $ac['recursive']=='on' )
							$recursive=true;

						$this->ged_dm->set_acl($acl_id, 'null', '', 'null', 'null', 'null', $recursive);
					}
				
				}
			
		}

			
		$acl=$this->ged_dm->get_element_acl ( $element_id );
		
		//DEBUG
		//_debug_array($acl);
				
		$this->t->set_block('change_acl_tpl', 'statuses_list', 'statuses_list_handle');
		
		if ( ! empty ($statuses))
			foreach ( $statuses as $status )
			{
				$this->t->set_var ( 'status_label', $status);
				$this->t->fp('statuses_list_handle', 'statuses_list', True);
			}		
		
		$this->t->set_block('change_acl_tpl', 'acl_list', 'acl_list_handle');
		
		$this->t->set_block('acl_list', 'acl_list_statuses_list', 'acl_list_statuses_list_handle');
		
		$element_info=$this->ged_dm->get_element_info($element_id);
		
		if ( $element_info['type']=='folder' )
			$element_name=lang("folder")." ".$element_info['name'];
		elseif ($element_info['type']=='file' )
			$element_name=lang("file")." ".$element_info['name'];
		
		$this->t->set_var ( 'element_name', $element_name);
		
		if ( ! empty ($acl))
			foreach ( $acl as $ac )
			{
				$this->t->set_var('acl_list_statuses_list_handle', '');

				$this->t->set_var ( 'account_id', $ac['account_id']);
				$this->t->set_var ( 'acl_id', $ac['acl_id']);
				$this->t->set_var ( 'account', $GLOBALS['phpgw']->common->grab_owner_name($ac['account_id']));
				
				if ( $ac['read']==1)
					$readflag="checked";
				else
					$readflag="";
				
				$this->t->set_var ( 'readflag', $readflag);

			if ( $ac['write']==1)
					$writeflag="checked";
				else
					$writeflag="";

			$this->t->set_var ( 'writeflag', $writeflag);

			if ( $ac['delete']==1)
					$deleteflag="checked";
				else
					$deleteflag="";

			$this->t->set_var ( 'deleteflag', $deleteflag);

				if ( $ac['changeacl']==1)
					$changeaclflag="checked";
				else
					$changeaclflag="";
					
				$this->t->set_var ( 'changeaclflag', $changeaclflag);
			
				if ( ! empty ($statuses))
					foreach ( $statuses as $status )
					{
						//DEBUG
						//_debug_array($ac['statuses']);
						
						if ( in_array($status, $ac['statuses']) )
						{
							$statusflag='checked';
						}
						else
						{
							$statusflag='';
						}
						$this->t->set_var ( 'status', $status);
						$this->t->set_var ( 'statusflag', $statusflag);
						$this->t->fp('acl_list_statuses_list_handle', 'acl_list_statuses_list', True);
					}		
			
				$this->t->fp('acl_list_handle', 'acl_list', True);
			}
			
		$candidates=$this->ged_dm->get_element_acl_candidates ( $element_id );
		
		$this->t->set_block('change_acl_tpl', 'accounts_list', 'accounts_list_handle');
		
		if ( ! empty ($candidates))
		{
			$this->t->set_var ( 'account_id', "");
			$this->t->set_var ( 'account', lang ( "select"));
			$this->t->fp('accounts_list_handle', 'accounts_list', True);
			
			foreach ( $candidates as $candidate )
			{
				$this->t->set_var ( 'account_id', $candidate['account_id']);
				$this->t->set_var ( 'account', $GLOBALS['phpgw']->common->grab_owner_name($candidate['account_id']));

				$this->t->fp('accounts_list_handle', 'accounts_list', True);
			}
		}

		$this->t->set_block('change_acl_tpl', 'new_statuses_list', 'new_statuses_list_handle');

		if ( ! empty ($statuses))
			foreach ( $statuses as $status )
			{
				$this->t->set_var ( 'status', $status);
				$this->t->fp('new_statuses_list_handle', 'new_statuses_list', True);
			}		

		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.browse';
		$link_data['focused_id']=$element_id;

		$url_go_back=$GLOBALS['phpgw']->link('/index.php', $link_data);
		$js_go_back="document.location='".$url_go_back."'";
		$this->t->set_var ( 'js_action_go_back', $js_go_back );
		
		$this->t->pfp('out', 'change_acl_tpl');
	
	}
	
	// Just one workflow method remains
	// (removed all the previous hardcoded flow functions)
	function flow_do()
	{
		$transition=get_var('transition', array('POST', 'GET'));
		$element_id=get_var('element_id', array('POST', 'GET'));
		$version_id=get_var('version_id', array('POST', 'GET'));
		
		$element=$this->ged_dm->get_element_info($element_id);
		$version=$this->ged_dm->get_version_info($version_id);
		
		$flow_object=array(
			'app' => 'ged',
			'project_root' => (int)$element['project_root'], 
			'doc_type' => $element['doc_type'], 
			'element_id' => (int)$element_id, 
			'version_id' => (int)$version_id
		);
		$do_transition_result=$this->flows->do_transition($transition, $flow_object);
		
		if ( $do_transition_result['status'] == 'ok')
		{
			$link_data=null;
			$link_data['menuaction']='ged.ged_ui.browse';
			$link_data['focused_id']=$element_id;
			$link_data['version_id']=$version_id;
		
			$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
		}
		elseif ( isset($do_transition_result['error_message']) )
		{
			print ( $do_transition_result['error_message'] );
		}
	}	
		
	// Search
	function search()
	{
		$search_query=get_var('search_query', array('GET', 'POST'));
		$search=get_var('search', array('GET', 'POST'));

		$this->set_template_defaults();
		$this->display_app_header();
		
		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.search';
		$link_data['kp3']=$GLOBALS['phpgw_info']['user']['kp3'];
		$link_data['sessionid']=$GLOBALS['sessionid'];
		$link_data['click_history']=$_GET['click_history'];
		$search_url=$GLOBALS['phpgw_info']['server']['webserver_url'];
		
		$this->t->set_var('menuaction', $link_data['menuaction']);
		$this->t->set_var('kp3', $link_data['kp3']);
		$this->t->set_var('sessionid', $link_data['sessionid']);
		$this->t->set_var('click_history', $link_data['click_history']);
		$this->t->set_var('action_search', $search_url);
		
		// Search
		$results_query= $this->ged_dm->search($search_query);
		
		$this->t->set_file(array('search_tpl'=>'search.tpl'));
		
		$this->t->set_block('search_tpl', 'search_results_block', 'search_results_block_handle');
		
		// Set block
		if ( $search == lang("Search") && $search_query != '' && is_array($results_query))
		{	
			foreach($results_query as $result_query )
			{
    		$this->t->set_var('element_id', $result_query['element_id']);
    		$this->t->set_var('version_id', $result_query['version_id']);
    		$this->t->set_var('name', $result_query['name']);
    		$this->t->set_var('reference', $result_query['reference']);
    		$this->t->set_var('version', "v".$result_query['major'].".".$result_query['minor']);
    		$this->t->set_var('status', $result_query['status']);
    		$this->t->set_var('description', $result_query['description']);
    		$this->t->set_var('descriptionv', $result_query['descriptionv']);
    		
    		
				$this->t->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $result_query['status']."-16"));
		
				$link_data=null;
				$link_data['menuaction']='ged.ged_ui.browse';
				$link_data['focused_id']=$result_query['element_id'];
				$this->t->set_var('search_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				
				$this->t->fp('search_results_block_handle', 'search_results_block', True);
				
			}
			
			   
		}
		
		// Display result

		$this->t->set_var('search_query_field', "search_query");
		$this->t->set_var('search_query_value', $search_query);
		$this->t->set_var('do_search_command', "search");
		$this->t->set_var('do_search_value', lang("Search"));

		$this->t->pfp('out', 'search_tpl');

	}

	// Statistics
	function stats()
	{
		$jscal = CreateObject('phpgwapi.jscalendar');
		
		if ( isset($_GET['ok']))
		{
			$date_start = $jscal->input2date($_GET['date_start']);
			$datetime_start = mktime(0,0,0,$date_start['month'],$date_start['day'],$date_start['year']);
			$date_end = $jscal->input2date($_GET['date_end']);
			$datetime_end = mktime(0,0,0,$date_end['month'],$date_end['day'],$date_end['year']);
		}
		else
		{
			// DONE : Set default values as start of month -> now
			$datetime_end = time();
			$day=1;
			$month=date ( 'm', $datetime_end);
			$year=date ( 'Y', $datetime_end);
			$datetime_start = mktime(0,0,0,$month,$day,$year);
			
		}

		// Get info
		$myprojects=$this->ged_dm->list_wanted_projects();
		
		$this->set_template_defaults();
		$this->display_app_header();	
		$this->t->set_file(array('stats_tpl'=>'stats.tpl'));

		$this->t->set_block('stats_tpl', 'ged_projects', 'ged_projects_handle');
		$this->t->set_block('ged_projects', 'delivered_block', 'delivered_block_handle');
		$this->t->set_block('ged_projects', 'accepted_block', 'accepted_block_handle');
		$this->t->set_block('ged_projects', 'refused_block', 'refused_block_handle');

		$link_data=null;
		$link_data['menuaction']='ged.ged_ui.stats';
		$link_data['kp3']=$GLOBALS['phpgw_info']['user']['kp3'];
		$link_data['sessionid']=$GLOBALS['sessionid'];
		$link_data['click_history']=$_GET['click_history'];
		$filter_url=$GLOBALS['phpgw_info']['server']['webserver_url'];
		
		$this->t->set_var('menuaction', $link_data['menuaction']);
		$this->t->set_var('kp3', $link_data['kp3']);
		$this->t->set_var('sessionid', $link_data['sessionid']);
		$this->t->set_var('click_history', $link_data['click_history']);
		$this->t->set_var('action_filter', $filter_url);
		
		$this->t->set_var('jscal_start', $jscal->input('date_start', $datetime_start));
		$this->t->set_var('jscal_end', $jscal->input('date_end', $datetime_end));

		foreach ( $myprojects as $my_element_id => $myproject )
		{
		
			$this->t->set_var('project_name', $myproject );			
			$this->t->set_var('refused_block_handle', "");
			$this->t->set_var('delivered_block_handle', "");
			$this->t->set_var('accepted_block_handle', "");
			
			$stats_delivered=null;
			$stats_delivered=$this->ged_dm->get_stats($datetime_start, $datetime_end, 'pending_for_acceptation',$my_element_id);

			$count=0;
			if (isset($stats_delivered))
			{
	
				foreach ( $stats_delivered as $element )
				{
	    		$this->t->set_var('element_id', $element['element_id']);
	    		$this->t->set_var('version_id', $element['version_id']);
	    		$this->t->set_var('name', $element['name']);
	    		$this->t->set_var('reference', $element['reference']);
	    		$this->t->set_var('version', "v".$element['major'].".".$element['minor']);
	    		$this->t->set_var('status', $element['status']);
	    		$this->t->set_var('description', $element['description']);
	    		$this->t->set_var('descriptionv', $element['descriptionv']);
	    		
	    		
					$this->t->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $element['status']."-16"));
			
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$element['element_id'];
					$link_data['version_id']=$element['version_id'];
					$this->t->set_var('search_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
					
					$count++;
					$this->t->fp('delivered_block_handle', 'delivered_block', True);
					
				}
			}

			$this->t->set_var('count_delivered', $count);
			
			$stats_accepted=null;
			$stats_accepted=$this->ged_dm->get_stats($datetime_start, $datetime_end, 'current',$my_element_id);

			$count=0;
			if (isset($stats_accepted))
			{
				foreach ( $stats_accepted as $element )
				{
	    		$this->t->set_var('element_id', $element['element_id']);
	    		$this->t->set_var('version_id', $element['version_id']);
	    		$this->t->set_var('name', $element['name']);
	    		$this->t->set_var('reference', $element['reference']);
	    		$this->t->set_var('version', "v".$element['major'].".".$element['minor']);
	    		$this->t->set_var('status', $element['status']);
	    		$this->t->set_var('description', $element['description']);
	    		$this->t->set_var('descriptionv', $element['descriptionv']);
	    		
	    		
					$this->t->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $element['status']."-16"));
			
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$element['element_id'];
					$link_data['version_id']=$element['version_id'];
					$this->t->set_var('search_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
					
					$count++;
					$this->t->fp('accepted_block_handle', 'accepted_block', True);
					
				}
			} 

			$this->t->set_var('count_accepted', $count);
			
			$stats_refused=null;
			$stats_refused=$this->ged_dm->get_stats($datetime_start, $datetime_end, 'refused',$my_element_id);
			
			$count=0;
			if (isset($stats_refused))
			{
				foreach ( $stats_refused as $element )
				{
	    		$this->t->set_var('element_id', $element['element_id']);
	    		$this->t->set_var('version_id', $element['version_id']);
	    		$this->t->set_var('name', $element['name']);
	    		$this->t->set_var('reference', $element['reference']);
	    		$this->t->set_var('version', "v".$element['major'].".".$element['minor']);
	    		$this->t->set_var('status', $element['status']);
	    		$this->t->set_var('description', $element['description']);
	    		$this->t->set_var('descriptionv', $element['descriptionv']);
	    		
	    		
					$this->t->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $element['status']."-16"));
			
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$element['element_id'];
					$link_data['version_id']=$element['version_id'];
					$this->t->set_var('search_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
					
					$count++;
					$this->t->fp('refused_block_handle', 'refused_block', True);
					
				}
			} 

			$this->t->set_var('count_refused', $count);
			
			$this->t->fp('ged_projects_handle', 'ged_projects', True);
		}	

		//$link_data=null;
		//$link_data['menuaction']='ged.ged_stats.ged_pie_status';
		//$the_graph_link=$GLOBALS['phpgw']->link('/index.php', $link_data);
		//$this->t->set_var('test_graph_link', $the_graph_link);

		$this->t->pfp('out', 'stats_tpl');

	}

	function chrono()
	{
		$project_root=get_var('project_root',array('GET'));
		
		$this->set_template_defaults();
		$this->display_app_header();
		$this->t->set_file(array('chrono_tpl'=>'chrono.tpl'));
		$this->t->set_block('chrono_tpl', 'type_block', 'type_block_handle');
		$this->t->set_block('type_block', 'chrono_block', 'chrono_block_handle');
		
		// DONE : use the project name instead of root id
		$this->t->set_var('lang_chrono_title', lang('Chronos for project')." ".$this->ged_dm->get_project_name($project_root));
		
		$chronos=null;
		$chronos=$this->ged_dm->list_chronos($project_root);
		
		if ( isset($chronos))
		{
			foreach ( $chronos as $type_id => $type_chronos)
			{
				$this->t->set_var('chrono_block_handle', "");
				
				//DONE : Use the detailed label of type 
				$this->t->set_var('doc_type', $type_id);
				$row_class="row_off";
				foreach ( $type_chronos as $chrono)
				{
					// DONE : set up the needed template variables
					$this->t->set_var('name', $chrono['name']);
					$this->t->set_var('date', $GLOBALS['phpgw']->common->show_date($chrono['date']));
					$this->t->set_var('author', $GLOBALS['phpgw']->common->grab_owner_name($chrono['creator_id']));
					$this->t->set_var('description', $chrono['description']);
					$this->t->set_var('version_label', $chrono['version_label']);
					$this->t->set_var('reference', $chrono['reference']);
					$this->t->set_var('no', $chrono['no']);
	
					$this->t->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $chrono['status']."-16"));
			
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$chrono['element_id'];
					$link_data['version_id']=$chrono['version_id'];
					$this->t->set_var('browse_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
	
					
					if ( $row_class=="row_on")
						$row_class="row_off";
					else
						$row_class="row_on";
						
					$this->t->set_var('row_class', $row_class);
					
					$this->t->fp('chrono_block_handle', 'chrono_block', True);
				}
				$this->t->fp('type_block_handle', 'type_block', True);
			}
		}
		$this->t->pfp('out', 'chrono_tpl');
	}
	
}

?>

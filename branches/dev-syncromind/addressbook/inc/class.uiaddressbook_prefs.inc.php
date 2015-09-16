<?php
  /**************************************************************************\
  * phpGroupWare API - Commononly used functions                             *
  * This file written by Alex Borges <alex@co.com.mx>                        *
  * UI for addressbook preferences                                           *
  * Copyright (C) 2003 Free Software Foundation                              *
  * -------------------------------------------------------------------------*
  * This library is part of the phpGroupWare Addressbook app                 *
  * http://www.phpgroupware.org/                                             * 
  * ------------------------------------------------------------------------ *
  * This library is free software; you can redistribute it and/or modify it  *
  * under the terms of the GNU  General Public License as published by       *
  * the Free Software Foundation.                                            *
  * This library is distributed in the hope that it will be useful, but      *
  * WITHOUT ANY WARRANTY; without even the implied warranty of               *
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     *
  * See the GNU Lesser General Public License for more details.              *
  * You should have received a copy of the GNU  General Public License       *
  * along with this library; if not, write to the Free Software Foundation,  *
  * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            *
  \**************************************************************************/
class uiaddressbook_prefs 
{
	var $prefs; 
 	var $template;
	//@param selected_fields 
	//@discussion transient selected fields. Allways holds what the fields
	//that are found as selected in GET/POST vars, NOT the ones in session data
	var $selected_fields;
	//@param cached_selected_fields
	//@discussion Selected fields that are in the session data
	var $cached_selected_fields;
	var $public_functions = array(
					'index' => True,
					'remove_me' => True,
					'css' => true
					);
	/*@param remove_me
	@discussion transient field representing the false field to remove from the
	table's column
	*/
	 var $remove_me;
	/*
	   @param contacts
	   @discussion Should be contacts but for now we are going to make a uiaddressbook
	   because its where the addressbook caches the com-types catalog 
	 */
	var $contacts;
	/*
	    @param org_or_person
	    @discussion A simple flag that determines if we are editing the showable fields for
	    orgs or persons.
	*/
	var $org_or_person='Persons';
	var $map_tpl_to_real=array( 
		 'select_columns_form_name'=> 'on_constructor',
		 'select_columns_form_action' => 'on_constructor',
		 'select_columns_selectbox_name' => 'var_dependant',
		 'select_columns_submit_value' => 'on_constructor',
		 'lang_select_cols' => 'on_constructor', 
		 'lang_abprefs' => 'on_constructor',
		 'B_select_columns_form_options' => 'on_constructor',
		 'select_columns_comtypes_name' => 'var_dependant',
		 'B_select_columns_comtypes_options' => 'on_constructor',
		 'org_preferences_link' => 'on_constructor',
		 'person_preferences_link' => 'on_constructor',
		 'cat_options'  =>  'on_contructor',
		 'adm_pref_type' => 'user',
		 'hider_open' => '',
		 'hider_close' => ''
		 ); 
		//@param fields_show_selectbox
		//@discussion fields to show in the selectbox
		var $fields_show_selectbox;
		var $lang_fields;
		var $bo;
		var $submit;
		function __construct()
		{
			$this->template = $GLOBALS['phpgw']->template;
			if(!$this->session_data_saved())
			{
				$this->map_tpl_to_real['lang_select_cols']=$GLOBALS['phpgw']->lang('Fields to show in address list');
				$this->map_tpl_to_real['lang_abprefs']=$GLOBALS['phpgw']->lang('addressbook preferences');
				$this->map_tpl_to_real['select_columns_submit_value']=$GLOBALS['phpgw']->lang('select fields');
				$this->map_tpl_to_real['submit_save_value']=$GLOBALS['phpgw']->lang('save');
				$this->map_tpl_to_real['submit_cancel_value']=$GLOBALS['phpgw']->lang('cancel');
				$this->map_tpl_to_real['submit_remove_value']=$GLOBALS['phpgw']->lang('remove');
			}
			else
			{
				$this->read_sessiondata();
			}
			$this->contacts = CreateObject('phpgwapi.contacts');
			$this->bo=CreateObject('addressbook.boaddressbook_prefs');
			if($this->is_current_admin())
			{
				$temp=phpgw::get_var('adm_pref_type');
				//print "<br /><B>Admin user var is".$temp."</B><br />";
				//Changed preference type tav
				if(isset($temp))
				{
					//Prefs type change, flush cache
					if($temp != $this->map_tpl_to_real['adm_pref_type'])
					{
						
						//print "<br /><B>Flushing cache</B><br />";
						$this->clear_dinamic_data();
					}
					$this->map_tpl_to_real['adm_pref_type']=$temp;
				}
				$this->build_admin_tabs();
			}
			$this->bo->read_preferences($this->map_tpl_to_real['adm_pref_type']);
	//		print "<br /><B>current tab is".$this->map_tpl_to_real['adm_pref_type']."</B><br />";

			$this->map_tpl_to_real['org_preferences_link']=$GLOBALS['phpgw']->link('/index.php',
					array(
						'menuaction' => 'addressbook.uiaddressbook_prefs.index',
						'org_or_person' => 'Organizations',
						'adm_pref_type' => $this->map_tpl_to_real['adm_pref_type']
					     )
					);

			$this->map_tpl_to_real['person_preferences_link']=$GLOBALS['phpgw']->link('/index.php',
					array(
						'menuaction' => 'addressbook.uiaddressbook_prefs.index',
						'org_or_person' => 'Persons',
						'adm_pref_type' => $this->map_tpl_to_real['adm_pref_type']
					     )
												);

	/*		print "<p><b>Preferences</b></p>";
			print_r($this->bo->person_columns);
			print "<p><b>Preferences</b></p>";
			print_r($this->bo->org_columns); 
			*/

		}
		//@function get_addressbook_cats
		//Gets the <option> tags of the categories of the addressbook, also asks what return selected
		function get_addressbook_cats($selected=0)
		{
			$tmp=CreateObject('phpgwapi.categories');
			$tmp->app_name='addressbook';
			//Forced category, can be  an administrator not the forced
			//preferences tab
			//print "<br /><B>Default category is ".$this->bo->default_category_forced."</B><br />";
		//	print "<br /><B>Tab is ".$this->map_tpl_to_real['adm_pref_type']."</B><br />";
			if($this->bo->default_category_forced &&  $this->map_tpl_to_real['adm_pref_type']!='forced')
			{
					//print "<br /><B>FORCED cat</B><BR";
					$ret="<option value='"
					.$this->bo->default_category.
					"'>".
					$tmp->id2name($this->bo->default_category)
					."</option>";

			}
			else
			{
					
					$ret= $tmp->formated_list('select','all',$selected,True);
					
			}

			if($this->map_tpl_to_real['adm_pref_type']=="forced")	
			{
				$ret="<option value='user_default'>User default</option>".$ret;
			}
			return $ret;
		}

		//@function hashify
		//I dislike single dimmension numeric key arrays, i use this function to convert them to a hash
		function hashify($ar,$exclude)
		{

			if(is_array($ar))
			{
				reset($ar);
				while(list($k,$v)=each($ar))
				{
					if(!is_array($v))
					{
						
						$tmp[$v]=True;
					}
					else
					{
						$name=array_pop($exclude);
						$tmp[$name]=$this->hashify($v,$exclude);
					}
				}
			}
			else
			{
				if(!empty($ar))
				{
					$tmp[$ar]=$ar;
				}
				else
				{
					return $false;
				}
			}
			return $tmp;

		}
		function get_vars()
		{
			$temp=phpgw::get_var('org_or_person');
			if($temp=='Organizations' || $temp == 'Persons')
			{
				$this->org_or_person=$temp;
			}
			$this->map_tpl_to_real['select_columns_form_action']=$GLOBALS['phpgw']->link
						('/index.php',
						array(
							'menuaction' => "addressbook.uiaddressbook_prefs.columns_selected",
							'org_or_person' => $this->org_or_person
							)
						);

			
			$this->map_tpl_to_real['select_columns_selectbox_name']='selected_fields['.$this->org_or_person.'][]';
			$this->map_tpl_to_real['select_columns_comtypes_name']='selected_fields['.$this->org_or_person.'][comm_types][]';
			
			$this->selected_fields=phpgw::get_var('selected_fields');
			/*
			print "<p><b>Selected fields</b></p>";
			print_r($this->selected_fields);*/
			if(is_array($this->selected_fields[$this->org_or_person]))
			{
			/*	print "<p><b>There are ".count($this->selected_fields[$this->org_or_person])." selected</b></p>"; */
				$this->selected_fields[$this->org_or_person]=$this->hashify($this->selected_fields[$this->org_or_person],array('comm_types'));
			}
			//three kinds of submit
			//They have hit the button to add selected fields
			if(phpgw::get_var('select_fields'))
			{
				/* print '<br /><B>selectfields</B><br />'; */
				$temp='select_fields';
			}
			elseif(phpgw::get_var('save'))
			{
				$temp='save';
			}
			elseif(phpgw::get_var('cancel'))
			{
				$temp='cancel';
			}
			else
			{
				$temp=phpgw::get_var('remove_me');
				/*print '<br /><B>remove type'.$temp.'</B><br />';*/
				
				if(isset($this->selected_fields[$this->org_or_person][$temp]) && $this->selected_fields[$this->org_or_person][$temp])
				{
					//if we have found an element to be removed
					//remove it from here
					//This should never happen though
					unset($this->select_fields[$this->org_or_person][$temp]);
				}
					$this->remove_me=$temp;
					$temp='remove';
			}
			

			$this->submit=$temp;
			/*print '<br /><B>selected </B>';
			print_r($this->selected_fields);
			print '<br /><B>Submited</B>';
			print $this->submit; */
			$temp=phpgw::get_var('org_or_person');
			if($temp=='Organizations' || $temp == 'Persons')
			{
				$this->org_or_person=$temp;
			}

			$temp=phpgw::get_var('cat_id');
	//		print $temp;
			if($temp)
			{
				//Admin wants to delete the cat_id from preferences
				if($temp=='user_default' && $this->map_tpl_to_real['adm_pref_type']=='forced')
				{
					$this->bo->default_category_forced=false;
					$this->bo->default_category='__NONE__';
					$this->map_tpl_to_real['cat_options']=$this->get_addressbook_cats();
					$temp=($this->bo->default_category == '' ) ? '__NONE__' : $this->bo->default_category;
				}
				else
				{
					$this->map_tpl_to_real['cat_options']=$this->get_addressbook_cats($temp);
				}
					$this->map_tpl_to_real['cat_id']=$temp;
				//print '<br /><b>Selected catid!'.$temp.'</b><br />';
			}
			else
			{
		///		print '<br /><b>Cached catid</b><br />';
				
					//fetch from preferences
					$this->map_tpl_to_real['cat_id']=$this->bo->default_category;
					if($this->map_tpl_to_real['adm_pref_type']=='forced')
					{
						if($this->bo->default_category!='__NONE__' && $this->bo->default_category_forced)
						{
							$this->map_tpl_to_real['cat_options']=$this->get_addressbook_cats($this->bo->default_category);
						}
						else
							$this->map_tpl_to_real['cat_options']=$this->get_addressbook_cats();
					}
					else
					{
						
						$this->map_tpl_to_real['cat_options']=$this->get_addressbook_cats($this->map_tpl_to_real['cat_id']);
					}
		//			print '<br /><b>Preference catid</b><br />';

			}
		}
		function cache_is_empty()
		{
			if(is_array($this->cached_selected_fields[$this->org_or_person]['comm_types']))
			{
				if(count($this->cached_selected_fields[$this->org_or_person]['comm_types']) < 1)
				{
					if(count ($this->cached_selected_fields[$this->org_or_person]) <= 1)
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			elseif(count ($this->cached_selected_fields[$this->org_or_person]) < 1)
			{
				return true;
			}
				return false;

		}
					
		function fields_to_show() 
		{
			//have uiaddressbook fill up its field => english array, stock_contact_fields 
			$org_person_array['Persons']=$this->contacts->get_person_properties();
			$org_person_array['Organizations']=$this->contacts->get_organization_properties();

			//print "<br /><B>orgpersonarray properties".$orgpersonarray."</B><br />";
			/*print "<br /><b>catalogs</b><br />";*/
			//now go translating each field
			reset($this->contacts->stock_contact_fields);
			//Constructing simple 'showable' fields
			while(list($falsefield,$english)=each($this->contacts->stock_contact_fields))
			{
				//If it in selected_fields, then it has been selected, and it doesnt go into
				//the fields_show_selectbox array
				if(!isset($this->selected_fields[$this->org_or_person][$falsefield]) || !$this->selected_fields[$this->org_or_person][$falsefield])
				{
					if($this->org_or_person=='Persons')
					{
						if(! in_array($falsefield,$org_person_array['Organizations']) )
						{
							$this->fields_show_selectbox[$falsefield]=$GLOBALS['phpgw']->lang($this->contacts->stock_contact_fields[$falsefield]);
						//	print "<br /><br /><b> $falsefield<\b>";
							$this->lang_fields[$falsefield]=$this->fields_show_selectbox[$falsefield];
						}
							unset($org_person_array['Organizations'][$falsefield]);
					}
					elseif($this->org_or_person=='Organizations')
					{
						if(! in_array($falsefield,$org_person_array['Persons'] ))
						{
							$this->fields_show_selectbox[$falsefield]=$GLOBALS['phpgw']->lang($this->contacts->stock_contact_fields[$falsefield]);
							$this->lang_fields[$falsefield]=$this->fields_show_selectbox[$falsefield];
						}
							unset($org_person_array['Persons'][$falsefield]);
						
					}
				}
				else
				{
					//If it in selected_fields, then it has been selected, excluded from the selectbox, and
					//added to the lang_fields array which shows the columns that have been selected
					$this->lang_fields[$falsefield]=$GLOBALS['phpgw']->lang($this->contacts->stock_contact_fields[$falsefield]);

				}
			//Constructing commtype descriptions
			}

			$possible_comtypes=$this->linearize_query($this->contacts->get_contact_comm_descr(),'comm_description');

			if(isset($this->selected_fields[$this->org_or_person]['comm_types']) && is_array($possible_comtypes))
			{
				while(list($k,$v)=each($possible_comtypes))
				{
					if(!$this->selected_fields[$this->org_or_person]['comm_types'][$v])
					{
					//	print "<B><br />Commtypes $this->selected_fields[$this->org_or_person]['comm_types'][$v]</b><br />";
						$this->fields_show_selectbox['comm_types'][$v]=$v;	
					}
					else
					{
					//	print "<B><br />langfields $this->lang_fields[$v]=$v</b><br />";
						$this->lang_fields[$v]=$v;
					}
				}
			}
			/*
				
			print "<br /><b>Columns</b><br />";
			print_r($this->lang_fields);

			print "<br /><b>Columns</b><br />"; */
		}

		function linearize_query($qresult,$key)
		{
	//		print_r($qresult);
			reset($qresult);
			for($i=0;$i < count($qresult);$i++)
			{
					$ret[$qresult[$i][$key]]=$qresult[$i][$key];
			}
			return $ret;
		}
		function get_exact_fields()
		{
			//Selecting fields, let it roll
			/*print '<br /><B>SELECTFIELDS!</B><br />';*/
			if(is_array($this->cached_selected_fields[$this->org_or_person]) && is_array($this->selected_fields[$this->org_or_person]))
			{
				//We have cached selected fields, and someone selected more
				//Need to agregate the selected to the cached
				/*print '<br /><B>Merging Selected Fields</B><br />';
				print_r($this->selected_fields[$this->org_or_person]);
				print '<br /><B>Merging Cached Selected Fields</B><br />';
				print_r($this->cached_selected_fields[$this->org_or_person]);
	*/
				$this->cached_selected_fields[$this->org_or_person]=$this->selected_fields[$this->org_or_person]=array_merge_recursive($this->cached_selected_fields[$this->org_or_person],$this->selected_fields[$this->org_or_person]);

				// print '<br /><B>Merging Result</B><br />';
			//	print_r($this->selected_fields[$this->org_or_person]);
			}
			elseif(is_array($this->selected_fields[$this->org_or_person]))
			{
				$this->cached_selected_fields[$this->org_or_person]=$this->selected_fields[$this->org_or_person];
				//print '<br /><B>Selected awright</B><br />'; 
			}
			elseif(is_array($this->cached_selected_fields[$this->org_or_person]))
			{
				$this->selected_fields[$this->org_or_person]=$this->cached_selected_fields[$this->org_or_person];

				//print '<br /><B>Cached awright</B><br />'; 
			}
			elseif($this->org_or_person == 'Persons' && is_array($this->bo->person_columns))
			{
				//print '<br /><B>Old preferences found</B><br />';
				$this->selected_fields['Persons']=$this->cached_selected_fields['Persons']=$this->bo->person_columns;
			}
			elseif($this->org_or_person == 'Organizations' && is_array($this->bo->org_columns))
			{
				 //print '<br /><B>Old preferences found</B><br />'; 
				$this->selected_fields['Organizations']=$this->cached_selected_fields['Organizations']=$this->bo->org_columns;
			}
			
		}
		function index()
		{
			$this->get_vars();
			/*print '<br /><B>It is a !'.$this->org_or_person.'</B><br />';*/
			switch($this->submit)
			{

				case 'cancel':
					{
						$this->save_sessiondata(true);
						$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
						return;
					}
				case 'save':
					{
						//print_r($this->cached_selected_fields);
						//Need to get it all, we are in a tab, so actual data is
						//combinedly in the cache or in the selected_fields array
						if(is_array($this->selected_fields['Persons']))
						{
							$person_columns=$this->selected_fields['Persons'];
						}
						else
						{
							$person_columns=$this->cached_selected_fields['Persons'];
						}
						if(is_array($this->selected_fields['Organizations']))
						{
							$org_columns=$this->selected_fields['Organizations'];
						}
						else
						{
							$org_columns=$this->cached_selected_fields['Organizations'];
						}

					$this->bo->person_columns=$person_columns;
					$this->bo->org_columns=$org_columns;
				

					//print '<br /><B>Catid'.$this->map_tpl_to_real['cat_id'].'</B><br />';
					$this->bo->default_category=$this->map_tpl_to_real['cat_id'];
					//print "<br /><B>Person Columns";
					//print_r($this->bo->person_columns)."</B><br />";

					$this->bo->save_preferences($this->map_tpl_to_real['adm_pref_type']);
				//	$this->save_sessiondata(true);
				//	print $GLOBALS['phpgw']->redirect_link('/preferences/index.php');
				//	return;
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();
					$this->get_exact_fields();
					break;
				}
			case 'remove':
				{
					//if removing
					
					//print '<br /><B>Removing!</B><br />';
					if($this->cached_selected_fields[$this->org_or_person][$this->remove_me])
					{
						//kill the field to be removed
						//print '<br /><B>Removing!'.$this->remove_me.'</B><br />';
						unset($this->cached_selected_fields[$this->org_or_person][$this->remove_me]);
					}
					elseif($this->cached_selected_fields[$this->org_or_person]['comm_types'][$this->remove_me])
					{
						//print '<br /><B>Removing!'.$this->remove_me.'</B><br />';
						unset($this->cached_selected_fields[$this->org_or_person]['comm_types'][$this->remove_me]);
					}
					//Look if we remove_me is the last field to be removed
					
					if($this->cache_is_empty())
					{
						if($this->cached_selected_fields[$this->org_or_person]['comm_types'][$this->remove_me])
						{
							unset($this->cached_selected_fields[$this->org_or_person]['comm_types'][$this->remove_me]);
						}
						else
						{
							unset($this->cached_selected_fields[$this->org_or_person][$this->remove_me]);
						}
					}	

				}
							
			default:
				{
					$GLOBALS['phpgw']->common->phpgw_header();
					echo parse_navbar();


					
					//Selecting fields, let it roll
					//print '<br /><B>SELECTFIELDS!</B><br />';
					$this->get_exact_fields();
					//print_r($this->selected_fields);

						
				}
		}
		$this->template->set_root(PHPGW_APP_TPL);
		$this->template->set_file(
					array(
						'addressbook_preferences_t' =>'preferences.tpl',
						'selected_rows_t' => 'preferences_bits.tpl',
						'principal_tabs_t' => 'principal_tabs.tpl'
						)
					);
		//first, build the selectbox, select where needed
		$this->show_selectbox();
		//Obviously Not first time,  fields have been selected
		if( (is_array($this->selected_fields[$org_or_person])) || (is_array($this->cached_selected_fields[$this->org_or_person])) && !$this->cache_is_empty())
		{

			//print_r($this->cached_selected_fields);
			$this->show_cols();
		}
		$this->set_static_vars();
		$this->set_tabs();
		$this->template->parse('out','addressbook_preferences_t');
		$this->template->p('out');
		$this->save_sessiondata();
	}
	function set_static_vars()
	{

		$this->template->set_var('lang_abprefs',$this->map_tpl_to_real['lang_abprefs']);
		$this->template->set_var('lang_select_cols',$this->map_tpl_to_real['lang_select_cols']);
		$this->template->set_var('select_columns_submit_value',$this->map_tpl_to_real['select_columns_submit_value']);

		$this->template->set_var('select_columns_selectbox_name',$this->map_tpl_to_real['select_columns_selectbox_name']);
		$this->template->set_var('select_columns_comtypes_name',$this->map_tpl_to_real['select_columns_comtypes_name']);
		$this->template->set_var('submit_cancel_value',$this->map_tpl_to_real['submit_cancel_value']);
		$this->template->set_var('submit_save_value',$this->map_tpl_to_real['submit_save_value']);
		$this->template->set_var('lang_remove_field',$GLOBALS['phpgw']->lang('remove'));
		$this->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
		$this->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);
		//Catergories
		$this->template->set_var('cat_options',$this->map_tpl_to_real['cat_options']);
		if($this->is_current_admin())
		{
			$tabs=$this->map_tpl_to_real['final_admin_tabs'];
		}
		else
		{
			$tabs='';
		}
		$this->template->set_var('admin_tabs',$tabs);
		if($this->current_columns_value_is_forced() && $this->map_tpl_to_real['adm_pref_type'] != 'forced')
		{
			$hider_open="<!--";
			$hider_close="-->";
		}
		$this->template->set_var('hider_open',$hider_open);
		$this->template->set_var('hider_close',$hider_close);
	}
	
	function set_tabs()
	{
		$this->template->set_block('principal_tabs_t','principal_button');
		$this->template->set_block('principal_tabs_t','principal_tab');

		//print '<br /><B>Now it is a '.$this->org_or_person.'</B><br />';
		$this->parse_principal_tabs(
						$this->map_tpl_to_real['person_preferences_link'],
						$this->get_class_css(
								'Persons',
					    			$this->org_or_person
								),
					 	'Persons',
						'Person'
					    );
		$this->parse_principal_tabs(
						$this->map_tpl_to_real['org_preferences_link'],
						$this->get_class_css(
								'Organizations',
					    			$this->org_or_person),		
						'Organizations',
						'Organization'
					    );
		$this->template->set_var('tabs',$this->template->fp('out','principal_tab'));
	
		
	}
	function current_columns_value_is_forced($commtypes=false)
	{
		switch($this->org_or_person)
		{
			case 'Persons':
				{
						return $this->bo->person_columns_forced;
				}
			case 'Organizations':
				{
				
						return $this->bo->org_columns_forced;
				}
		}

	}
	function show_selectbox($org_or_person='')
	{
		if(empty($org_or_person))
		{
			$org_or_person=$this->org_or_person;
		}
		$this->fields_to_show();
		$this->template->set_block('addressbook_preferences_t','B_select_columns_form_options','V_select_columns_form_options');
		$this->template->set_block('addressbook_preferences_t', 'B_select_ctypes_options', 'V_select_ctypes_options');
 
		if ( isset($this->fields_show_selectbox['comm_types']) )
		{
			if ( is_array($this->fields_show_selectbox['comm_types']) 
				&& count($this->fields_show_selectbox['comm_types']) )
			{
				foreach ( $this->fields_show_selectbox['comm_types'] as $k => $description )
				{
					$this->template->set_var('lang_comtype_field', $description);
					$this->template->set_var('commtype_description', $description);
					$this->template->parse('V_select_ctypes_options', 'B_select_ctypes_options', True);
				}
			}
			else
			{
				$this->template->set_var('lang_comtype_field',$GLOBALS['phpgw']->lang('Empty'));
				$this->template->set_var('value', '');
				$this->template->parse('V_select_ctypes_options', 'B_select_ctypes_options', True);
			}
  
			unset($this->fields_show_selectbox['comm_types']);
		}
 			
		if ( count($this->fields_show_selectbox) )
		{
			//print "<br /><B> To Show in Selectbox<br />".print_r($this->fields_show_selectbox, true)."</B><br />";
			foreach ( $this->fields_show_selectbox as $field => $lang )
			{
				$this->template->set_var('lang_contact_field',$lang);
				$this->template->set_var('value',$field);
				$this->template->parse('V_select_columns_form_options','B_select_columns_form_options',True);
			}
		}
		else
		{
			$this->template->set_var('lang_contact_field',$GLOBALS['phpgw']->lang('Empty'));
			$this->template->set_var('value', '');
			$this->template->parse('V_select_columns_form_options','B_select_columns_form_options',True);
		}
	}

	function show_cols($org_or_person='')
	{
		if(empty($org_or_person))
		{
			$org_or_person=$this->org_or_person;
		}
		$this->template->set_block('selected_rows_t','B_selected_rows','V_selected_rows');

		//print "<br /><B>Selected</B><br />";
		//print_r($this->selected_fields[$this->org_or_person]);
		reset($this->selected_fields[$this->org_or_person]);
		while(list($k,$v)=each($this->selected_fields[$this->org_or_person]))
		{
			if(!is_array($v))
			{
				$this->template->set_var('lang_selected_contact_field',$this->lang_fields[$k]);
				if($this->current_columns_value_is_forced() && $this->map_tpl_to_real['adm_pref_type'] != 'forced')
				{
					$removelink='';
				}
				else
				{
					$removelink=$GLOBALS['phpgw']->link
								('/index.php',
					array("menuaction"=>"addressbook.uiaddressbook_prefs.index",
						"remove_me"=>$k,
						"org_or_person" => $this->org_or_person,
						"adm_pref_type" => $this->map_tpl_to_real['adm_pref_type']
												
						)
					);
				}

				$this->template->set_var('remove_me_link',$removelink);
				$this->template->parse('V_selected_rows','B_selected_rows',True);
			}
			else
			{
				reset($v);
				$arrays[]=$v;
			}
				
		}
		if(is_array($arrays))
		{
			if($this->current_columns_value_is_forced() && $this->map_tpl_to_real['adm_pref_type'] != 'forced')
			{

				$removelink='';
			}
			else
			{
				$removelink=true;
			}

			while(list($k,$v)=each($arrays))
			{
				while(list($ok,$ov)=each($v))
				{
					if($removelink)
					{
						$removelink=$GLOBALS['phpgw']->link
							('/index.php',
							 array("menuaction"=>"addressbook.uiaddressbook_prefs.index",
								 "remove_me"=>$ok,
								 "org_or_person" => $this->org_or_person,
								 "adm_pref_type" => $this->map_tpl_to_real['adm_pref_type']

							      )
							);
					}
					

					$this->template->set_var('lang_selected_contact_field',$this->lang_fields[$ok]);
					$this->template->set_var('remove_me_link',$removelink);
					$this->template->parse('V_selected_rows','B_selected_rows',True);
				}
			}
		}

		$this->template->parse('B_selected_rows','V_selected_rows');
	}
	
	function save_sessiondata($clear=false)
	{
		if(!$clear)
		{

			$GLOBALS['phpgw']->session->appsession('session_data','addressbookpref',
					array(
						'selected_fields' => $this->cached_selected_fields,
						'map_tpl_to_real' => $this->map_tpl_to_real,
						'lang_fields' => $this->lang_fields
					     )
					);
		}
		else
		{
		//	print '<br /><B>Clearing Cache </B><br />';
			$GLOBALS['phpgw']->session->appsession('session_data','addressbookpref','');
		}

	}
	function read_sessiondata()
	{

//		print '<br /><B>READING SESSIONDATA!</B><br />';
		$data=$GLOBALS['phpgw']->session->appsession('session_data','addressbookpref');
		$this->cached_selected_fields=$data['selected_fields'];
		$this->map_tpl_to_real=$data['map_tpl_to_real'];
		$this->cat_id=$this->map_tpl_to_real['cat_id'];
		$this->lang_fields=$data['lang_fields'];
		$this->cache_is_empty=$data['cache_is_empty'];

	}
	//Strange, the cached fields need to be really unset, then we clear the cache and we are really clean
	function clear_dinamic_data()
	{
		unset($this->cached_selected_fields);
		unset($this->lang_fields);
		$this->save_sessiondata('true');
	}

	function session_data_saved()
	{
		$data=$GLOBALS['phpgw']->session->appsession('session_data','addressbookpref');
		//print "<br /><B>Actually got sessiondata</B><br />";
		//print_r($data);
		return is_array($data);
	}
	function parse_principal_tabs($action,$class_css,$name,$value)
	{
		$button = array('principal_action' 	=> $action,
				'principal_class_css'	=> $class_css,
				'principal_name'	=> $name,
				'principal_value'	=> $value);
		$this->template->set_var($button);
		$this->template->parse('principal_buttons', 'principal_button',true);

	}
	function get_class_css($tab, $current_tab)
	{
		//print "<br /><B>ITS AN $tab == $current_tab";
		if ($tab == $current_tab)
		{
			return 'button_style_sel';
		}
		else
		{
			return 'button_style';
		}
	
	}

		function css()
		{
			$tmp = 'input[type="submit"].button_style, input[type="button"].button_style {
				color: #555;
				margin-left: 1px;
				margin-right: 1px;
				background-color: #ddd;
				border:1px #888 solid;
			        border-bottom-width: 0px;
				padding: 1px;
				width: 85px;
				}

				input[type="submit"].button_style_sel, input[type="button"].button_style_sel {
				color: #555;
				margin-left: 1px;
				margin-right: 1px;
				/*background-color: #e5e5e5;*/
				border:1px #888 solid;
        			border-bottom-width: 0px;
				padding: 1px;
				width: 85px;
				}

				input[type="submit"]:hover.button_style, input[type="button"]:hover.button_style {
				background-color: #eee;
				color: #36c;
				}

				input[type="submit"]:active.button_style, input[type="button"]:active.button_style {
				background-color: #eee;
				color: #9ac;
				}';
			return $tmp;
		}
	

		function is_current_admin()
		{
//			print "<br /><B> Current is admin? ".
			 	$GLOBALS['phpgw']->acl->check('run',1,'admin')."</B><br />";
			return $GLOBALS['phpgw']->acl->check('run',1,'admin');

		}

		function build_admin_tabs()
		{
			if ($this->is_current_admin())
			{
				$tabs[] = array(
						'label' => lang('Your preferences'),
						'link'  => $GLOBALS['phpgw']->link('/index.php',array(
								"menuaction" => "addressbook.uiaddressbook_prefs.index",
								 "adm_pref_type"=>"user"
							 ))
					       );
				$tabs[] = array(
						'label' => lang('Default preferences'),
						'link'  => $GLOBALS['phpgw']->link('/index.php',array(
								"menuaction" => "addressbook.uiaddressbook_prefs.index",
								 "adm_pref_type"=>"default"
								 ))

					       );
				$tabs[] = array(
						'label' => lang('Forced preferences'),
						'link'  => $GLOBALS['phpgw']->link('/index.php',array(
								"menuaction" => "addressbook.uiaddressbook_prefs.index",
								 "adm_pref_type"=>"forced"
								 ))
					       );

				switch($this->map_tpl_to_real['adm_pref_type'])
				{
					case 'user':    $selected = 0; break;
					case 'default': $selected = 1; break;
					case 'forced':  $selected = 2; break;
				}
				$this->map_tpl_to_real['final_admin_tabs']=$GLOBALS['phpgw']->common->create_tabs($tabs,$selected);
			}
		}

		

}

<?php
	/**
	* EMail - Addressbook
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal WARNING-> The sheer size of this stupid file is what shows why there is a
	* @internal three tiered design in phpgw...sigh.... now im even gonna put some php
	* @internal functions in here.... dont be mad, be critical and show me how to move to
	* @internal a better way.....Alex
	*/

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => True,
		'nonavbar' => True,
		'currentapp' => 'email',
		'enable_nextmatchs_class' => False 
	);
//		Template initialization, moved to blocks outside the main template


	/**
	* Include phpgroupware header
	*/
	include('../header.inc.php');
	
	$GLOBALS['phpgw']->template->set_file(array(
		'addressbook_names_t' => 'addressbook-js.tpl',
		'addressbook_names' => 'addressbook-js.tpl',
		'hidden_emails_t' => 'addressbook-js-bits.tpl',
		'selectboxes_t' => 'addressbook-js-bits.tpl'
		));
	
/* This is a control variable so that we know if we need to take data out of db or just from the cache */

	$cachestate="dirty";
/* We initialize this switch that tells the code if any comes with data in it */
	$boxecoming=False;

/*		This   exists if we clicked on a name to see its data from the database  */
	$searchbox  = $_POST['searchbox'] ? $_POST['searchbox'] : $_GET['searchbox'];
/*	This is the View More checkbox */
	$viewmore = $_POST['viewmore'] ? $_POST['viewmore'] : $_GET['viewmore'];
	
/*	
	The next three are, respectively, the selected To:,cc and bcc  selectboxes. We need them to remember if
	they something was selected on them and we, for example, clicked on a name and, thus, submited the form.
	We need to keep all values in this boxes. This is why the js code autoselects all of the options
	just before submiting. BTW, this should come in post allways but its a good practice to allways try and 
	get from both.
*/
	$toselectbox	= $_REQUEST['toselectbox'];
	$ccselectbox	= $_REQUEST['ccselectbox'];
	$bccselectbox	= $_REQUEST['bccselectbox'];
	$nameselect	= $_REQUEST['nameselect'];
	
/*	Block initialization... u see why i got the previous variables first dont you? */
	$GLOBALS['phpgw']->template->set_block('addressbook_names_t','addressbook_names','list');
	$GLOBALS['phpgw']->template->set_block('hidden_emails_t','B_hidden_emails_list','V_hidden_emails_list');
	if($viewmore)
	{
		
		$GLOBALS['phpgw']->template->set_var('viewmore_checked',"checked=\"checked\"");
		$GLOBALS['phpgw']->template->set_block('hidden_emails_t','B_addressbook_record','V_addressbook_record');
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('viewmore_checked',"");
		$GLOBALS['phpgw']->template->set_var('V_addressbook_record',"");
		
	}
	if($toselectbox)
	{
		$GLOBALS['phpgw']->template->set_block('selectboxes_t','B_toselectbox','V_toselectbox');
		$boxcoming=True;
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('V_toselectbox',"");
	}
	if($ccselectbox)
	{
		$GLOBALS['phpgw']->template->set_block('selectboxes_t','B_ccselectbox','V_ccselectbox');
		$boxcoming=True;
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('V_ccselectbox',"");
	}
	if($bccselectbox)
	{
		$GLOBALS['phpgw']->template->set_block('selectboxes_t','B_bccselectbox','V_bccselectbox');
		$boxcoming=True;
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('V_bccselectbox',"");
	}
	if($nameselect)
	{
		//This means the nameselect box was clicked to find out info for a given user
		//So, no need to requery the database, we have that data here in the appsession cache
		//therefore, we deem it "clean"
		$cachestate="clean";
	}
	
	//We need to remember value and name of a single selected item in any of the destination boxes. Since this is the case, 
	//we hackishly (by javascript, see corresponding code in addressbook-js.tpl) put the selected value into the searchbox 
	if($boxcoming)
	{
		$selected_destination=$searchbox;
		//We are evil, so we dont want this to be remembered
		$searchbox="";
		//BUT, we need to know if we really need to fetch any data from thedatabase, so we flag
		//to the rest of the code that it should use cached values
		$cachestate="clean";
	}
	
	//We do it this way so the cats class gets the right rights, alright? - skwashd Dec-2005
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'addressbook';
 	$c = CreateObject('phpgwapi.categories');
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'email';
	
/*	I honestly dont know where this comes from but its strangely familiar */
	// (angles) the original address book didnot xfer the persson's name AND email address, only their email address
	// eventually I changed it to xfre the persons name ("personal" part) as well as the email address.
	// This is a relic of that development
	$include_personal = True;

/*	We need to get some preferences here so we can set the width the addressbook will have */
	$scrsize=$GLOBALS['phpgw_info']['user']['preferences']['email']['js_addressbook_screensize'];

/*	We can tell the fontsize needed for this screen with this array */
	$fontsizes=array(
			'700' =>'xx-small', //800x600 screens
			'800'  => 'x-small', //1024x768 screens
			'900'  => 'small' //1600x1200 screens
			);

			
	$GLOBALS['phpgw']->template->set_var('optimal_width',$scrsize);
	$GLOBALS['phpgw']->template->set_var('widget_font_size',$fontsizes[$scrsize]);
	
/*	Normal stuff and setting of template vars as they come	*/
	$charset = $GLOBALS['phpgw']->translation->translate('charset');
	$GLOBALS['phpgw']->template->set_var('charset',$charset);
	$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw_info']['site_title']);
	$GLOBALS['phpgw']->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['bg_color']);
	$GLOBALS['phpgw']->template->set_var('lang_addressbook_action',lang('Address book'));
	$GLOBALS['phpgw']->template->set_var('include_link',$GLOBALS['phpgw']->link("/email/inc/selectboxes.js"));
	$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);
	$GLOBALS['phpgw']->template->set_var('main_form_name',"mainform");
	$GLOBALS['phpgw']->template->set_var('lang_search',lang('Search'));
	$GLOBALS['phpgw']->template->set_var('searchbox_value',(($searchbox) ? $searchbox : ""));
	$GLOBALS['phpgw']->template->set_var('lang_select_cats',lang('Select category'));

/*	This vars here are most important, they drive the search */
	$start  = $_POST['start'] ? $_POST['start'] : $_GET['start'];
	$filter  = $_POST['filter'] ? $_POST['filter'] : $_GET['filter'];
	$cat_id  = $_POST['cat_id'] ? $_POST['cat_id'] : $_GET['cat_id'];
	$cat_id = ($cat_id == -2)?"":$cat_id;
	$order  = $_POST['order'] ? $_POST['order'] : $_GET['order'];
	$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook-js.php',"sort=$sort&order=$order&filter=$filter&start=$start&cat_id=$cat_id"));
	$GLOBALS['phpgw']->template->set_var('query',$query);
	$GLOBALS['phpgw']->template->set_var('order',$order);


	if (! $start)
	{
		$start = 0;
	}

	if (!$filter)
	{
		$filter = 'none';
	}
	if (!$cat_id)
	{
		if ($filter == 'none')
		{
			$qfilter  = 'tid=n';
		}
		elseif ($filter == 'private')
		{
			$qfilter  = 'owner='.$GLOBALS['phpgw_info']['user']['account_id'].',access=private';
		}
		elseif($filter == 'user_only')
		{
			$qfilter = 'owner='.$GLOBALS['phpgw_info']['user']['account_id'];
		}
		$cat="";
		
	}
	else
	{
		$cat="cat_id=$cat_id";
		if ($filter == 'none')
		{
			$qfilter  = 'cat_id='.$cat_id;
		}
		elseif ($filter == 'private')
		{
			$qfilter  = 'owner='.$GLOBALS['phpgw_info']['user']['account_id'].'access=private';
		}
		elseif($filter == 'user_only')
		{
			$qfilter = 'owner='.$GLOBALS['phpgw_info']['user']['account_id'];
		}
	}

	$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

	$cols = array (
		'title'	    => 'title',
		'n_given'    => 'n_given',
		'n_family'   => 'n_family',
		'email'      => 'email'
	);

	
	$decent_show['none']="All available entries";
	$decent_show['private']="Private marked entries";
	$decent_show['user_only']="Your entries";
	switch($filter)
	{
		case "none":
		{
			$GLOBALS['phpgw']->template->set_var('global_is_selected',"selected");
			$GLOBALS['phpgw']->template->set_var('mine_is_selected',"");
			$GLOBALS['phpgw']->template->set_var('private_is_selected',"");
			break;
		}
		case "private":
		{
			$GLOBALS['phpgw']->template->set_var('mine_is_selected',"selected");
			$GLOBALS['phpgw']->template->set_var('global_is_selected',"");
			$GLOBALS['phpgw']->template->set_var('private_is_selected',"");
			break;
		}
		case "user_only":
		{
			$GLOBALS['phpgw']->template->set_var('private_is_selected',"selected");
			$GLOBALS['phpgw']->template->set_var('mine_is_selected',"");
			$GLOBALS['phpgw']->template->set_var('global_is_selected',"");
			break;
		}
		default:
		{
			$GLOBALS['phpgw']->template->set_var('private_is_selected',"");
			$GLOBALS['phpgw']->template->set_var('mine_is_selected',"");
			$GLOBALS['phpgw']->template->set_var('global_is_selected',"");
			break;
			
		}
	}

// ------------------- list header variable template-declaration -----------------------
//-----------------DAMN MY CLIENT---HE DOESNT LIKE CATHEGORY LEVEL TO SHOW IN THE CATEGORY BOX---it aint gonna get any faster though
//----------------SO, UNCOMMENT THE NEXT ONE TO OBTAIN THI BEHAVIOUR, WILL MAKE PREFERENCE LATER
//------ Categories almost do not change.....we cache them allways...

$catlist=$GLOBALS['phpgw']->session->appsession('jsbook_catlist','email');
//We dont have none
if(!$catlist)
{
	$catlist=$c->formated_list('select','all',$cat_id,'True');
	
	$GLOBALS['phpgw']->session->appsession('jsbook_catlist','email',$catlist);
}


	$GLOBALS['phpgw']->template->set_var('cats_list',
						ereg_replace( '&nbsp;&lt;' . lang('Global') . '&nbsp;' . lang($c->app_name) . '&gt;',
								"",
								$catlist
								)
						);

//	$GLOBALS['phpgw']->template->set_var('cats_list',$c->formated_list('select','all',$cat_id,'True'));
	$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));
	$GLOBALS['phpgw']->template->set_var('cats_action',
		$GLOBALS['phpgw']->link(
			'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook-js.php',
				"sort=$sort&order=$order&filter=$filter&start=$start&query=$query&cat_id=$cat_id".($viewmore ? "&viewmore=1" :"")
		)
	);
	$GLOBALS['phpgw']->template->set_var('main_form_action',
		$GLOBALS['phpgw']->link(
			'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook-js.php',
				array(
					'sort'		=> $sort,
					'order'		=> $order,
					'filter'	=> $filter,
					'start'		=> $start,
					'query'		=> $query,
					'cat_id'	=> $cat_id
				)
		)
	);
	// --------------------------- end header declaration ----------------------------------
	$largest=0;
	
	if($cachestate=="dirty")
	{
		$entries = $d->read($start,$offset,$cols,$query,$qfilter,$sort,"n_given");
		/*Save it in the cache */
		$GLOBALS['phpgw']->session->appsession('session_data','jsbook_query',$entries);
	}
	else
	{
		$entries = $GLOBALS['phpgw']->session->appsession('session_data','jsbook_query');
	}
	for ($i=0;$i<count($entries);$i++)
	{
		$firstname = $entries[$i]['n_given'];
		if (!$firstname)
		{
			$firstname = '&nbsp;';
		}
		$lastname = $entries[$i]['n_family'];
		if (!$lastname)
		{
			$lastname = '&nbsp;';
		}
		$personal_firstname = '';
		$personal_lastname = '';
		$personal_part = '';
		if ((isset($firstname)) &&
			($firstname != '') &&
			($firstname != '&nbsp;'))
		{
			$personal_firstname = $firstname.' ';
		}
		if ((isset($lastname)) &&
			($lastname != '') &&
			($lastname != '&nbsp;'))
		{
			$personal_lastname = $lastname;
		}
		$personal_part = $personal_firstname.$personal_lastname;
		
		if (($personal_part == '') ||
			($include_personal == False))
		{
			$id     = $entries[$i]['id'];
			$email  = $entries[$i]['email'];
			$hemail = $entries[$i]['email_home'];
		}
		else
		{
			$id = $entries[$i]['id'];
			if ((isset($entries[$i]['email'])) &&
				(trim($entries[$i]['email']) != ''))
			{
				$email  = '&quot;'.$personal_part.'&quot; &lt;'.$entries[$i]['email'].'&gt;';
			}
			else
			{
				$email  = $entries[$i]['email'];
			}
			if ((isset($entries[$i]['email_home'])) &&
			(trim($entries[$i]['email_home']) != ''))
			{
				$hemail = '&quot;'.$personal_part.'&quot; &lt;'.$entries[$i]['email_home'].'&gt;';
			}
			else
			{
				$hemail = $entries[$i]['email_home'];
			}
		}
		
		// --------------------- template declaration for list records --------------------------
		//Remember selected
		if($nameselect)
			{
				if($nameselect==$id)
				{
					$GLOBALS['phpgw']->template->set_var('name_option_selected'," selected");	
				}
				else
				{
					$GLOBALS['phpgw']->template->set_var('name_option_selected',"");
				}
			}
		//Make shure this guy doesnt go into the to,cc or bcc selectboxes
		$tainted=false;
		if(isset($toselectbox))
		{
			$searchresult=array_search($id,$toselectbox);
			if(($searchresult) || ($searchresult===0))
			{
				$GLOBALS['phpgw']->template->set_var('toselvalue',$id);
				$GLOBALS['phpgw']->template->set_var('toselname',$firstname." ".$lastname);
				//Check if this item was selected before
				//print "<br />Selected $selected_destination ID $id <br />";
				if($selected_destination==$id)
				{
				//it was, set it selected
					$GLOBALS['phpgw']->template->set_var('tosel_is_selected',"SELECTED");
				//set nameselect id (we cheat here, it was designed only for the nameselectbox)
				//now nameselect is also a switch to tell the userdata box to show the info
				//for a selected user in any box
					$nameselect=$id;
				}
				else
				{
				//it wasnt, remove the template tag
					$GLOBALS['phpgw']->template->set_var('tosel_is_selected',"");
				}
				$GLOBALS['phpgw']->template->parse('V_toselectbox','B_toselectbox',true);
				$tainted=true;
			}
		}
		if(isset($bccselectbox))
		{
			$searchresult=array_search($id,$bccselectbox);
			if(($searchresult) || ($searchresult===0))
			{
				$GLOBALS['phpgw']->template->set_var('bccselvalue',$id);
				$GLOBALS['phpgw']->template->set_var('bccselname',$firstname." ".$lastname);
				//Check if this item was selected before
				if($selected_destination==$id)
				{
				//it was, set it selected
					$GLOBALS['phpgw']->template->set_var('bccsel_is_selected',"SELECTED");
				//set nameselect id (we cheat here, it was designed only for the nameselectbox)
				//now nameselect is also a switch to tell the userdata box to show the info
				//for a selected user in any box
					$nameselect=$id;
				}
				else
				{
				//it wasnt, remove the template tag
					$GLOBALS['phpgw']->template->set_var('bccsel_is_selected',"");
				}
				$GLOBALS['phpgw']->template->parse('V_bccselectbox','B_bccselectbox',true);
				$tainted=true;
			}
		
		}
		if(isset($ccselectbox))
		{
			$searchresult=array_search($id,$ccselectbox);
			if(($searchresult) || ($searchresult===0))
			{
				$GLOBALS['phpgw']->template->set_var('ccselvalue',$id);
				$GLOBALS['phpgw']->template->set_var('ccselname',$firstname." ".$lastname);
				//Check if this item was selected before
				if($selected_destination==$id)
				{
				//it was, set it selected
					$GLOBALS['phpgw']->template->set_var('ccsel_is_selected',"SELECTED");
				//set nameselect id (we cheat here, it was designed only for the nameselectbox)
				//now nameselect is also a switch to tell the userdata box to show the info
				//for a selected user in any box
					$nameselect=$id;
				}
				else
				{
				//it wasnt, remove the template tag
					$GLOBALS['phpgw']->template->set_var('ccsel_is_selected',"");
				}
				$GLOBALS['phpgw']->template->parse('V_ccselectbox','B_ccselectbox',true);
				$tainted=true;
			}
		}
		//if it went into the other boxes, we dont let him be in the select from box
		if(!$tainted)
		{
			$GLOBALS['phpgw']->template->set_var('name_option_value',$id);
			$GLOBALS['phpgw']->template->set_var('name_option_name',$firstname." ".$lastname);
			$GLOBALS['phpgw']->template->parse('list','addressbook_names',True);
		}
		$GLOBALS['phpgw']->template->set_var('hidden_value',$email);
		$GLOBALS['phpgw']->template->set_var('hidden_name',$id);
		$GLOBALS['phpgw']->template->parse('V_hidden_emails_list','B_hidden_emails_list',True);
		
	}
	// --------------------------- end record declaration ---------------------------
	
	
	if($viewmore && $nameselect)
	{
		$fields = array (
			'title'	    => 'title',
			'n_given'    => 'n_given',
			'n_family'   => 'n_family',
			'org_name' => 'org_name',
			'tel_work' => 'tel_work',
			'cat_id'  => 'cat_id'
			);
		$entry = $d->read("","",$fields,"","id=$nameselect");
		$largest=0;
		if(!$entry[0])
		{
			exit("User Not Found!!");
		}
		while(list($k,$v)=each($entry[0]))
		{
			$actualsize=strlen($v);
			if($actualsize > $largest)
			{
				$largest=$actualsize;
			}
		}
		$completename=$entry[0]["n_given"]." ".$entry[0]["n_family"];
		$largest=($largest > strlen($completename)) ? $largest : strlen($completename);
		$GLOBALS['phpgw']->template->set_var('record_col_num',$largest+10);
		$GLOBALS['phpgw']->template->set_var('record_row_num',count($fields));
		$GLOBALS['phpgw']->template->set_var('space_to_fit',str_pad("", 5, " ", STR_PAD_LEFT));
		$GLOBALS['phpgw']->template->set_var('lang_name',lang("Name"));
		$GLOBALS['phpgw']->template->set_var('name',$completename);
		$GLOBALS['phpgw']->template->set_var('lang_title',lang("Title"));
		$GLOBALS['phpgw']->template->set_var('title',$entry[0]['title']);
		$GLOBALS['phpgw']->template->set_var('lang_phone',lang("Phone"));
		$GLOBALS['phpgw']->template->set_var('phone',$entry[0]['tel_work']);
		$GLOBALS['phpgw']->template->set_var('lang_organization',lang("Organization"));
		$GLOBALS['phpgw']->template->set_var('organization',$entry[0]['org_name']);
		$GLOBALS['phpgw']->template->set_var('',$largest);

		$GLOBALS['phpgw']->template->parse('V_addressbook_record','B_addressbook_record',True);
	}
	$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
	$GLOBALS['phpgw']->template->parse('out','addressbook_names_t',True);
	$GLOBALS['phpgw']->template->p('out');

	$GLOBALS['phpgw']->common->phpgw_exit();
?>

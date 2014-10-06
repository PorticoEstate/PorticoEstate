<?php
	/**
	* SMS - Addressbook
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package sms
	* @version $Id$
	*/

	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => true,
		'nonavbar' => true,
		'currentapp' => 'sms',
		'enable_nextmatchs_class' => true
	);

	/**
	* Include phpgroupware header
	*/
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_file(array(
		'addressbook_list_t' => 'addressbook.tpl',
		'addressbook_list' => 'addressbook.tpl'
	));
	$GLOBALS['phpgw']->template->set_block('addressbook_list_t','addressbook_list','list');

	$d = CreateObject('phpgwapi.contacts');

	//We do it this way so the cats class gets the right rights, alright? - skwashd Dec-2005
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'addressbook';
 	$c = CreateObject('phpgwapi.categories');
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'sms';

	$include_personal = true;

	$charset = $GLOBALS['phpgw']->translation->translate('charset');
	$GLOBALS['phpgw']->template->set_var('charset',$charset);
	$GLOBALS['phpgw']->template->set_var('title',$GLOBALS['phpgw_info']['site_title']);
	$GLOBALS['phpgw']->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['bg_color']);
	$GLOBALS['phpgw']->template->set_var('lang_addressbook_action',lang('Address book'));
	$GLOBALS['phpgw']->template->set_var('font',$GLOBALS['phpgw_info']['theme']['font']);

	$GLOBALS['phpgw']->template->set_var('lang_search',lang('Search'));
	$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php'));
	$GLOBALS['phpgw']->template->set_var('lang_select_cats',lang('Select category'));

	$cat_id = get_var('cat_id',array('get','post'));
	$filter = get_var('filter',array('get','post'));
	$start = get_var('start',array('get','post'));
	$limit = get_var('limit',array('get','post'));
	$query = get_var('query',array('get','post'));
	$sort = get_var('sort',array('get','post'));
	$order = get_var('order',array('get','post'));

	if(!$cat_id)
	{
		$cat_id = $prefs['default_category'];
	}

	switch ($filter)
	{
	case 'user_only':
		$access = PHPGW_CONTACTS_MINE;
		break;
	case 'private':
		$access = PHPGW_CONTACTS_PRIVATE;
		break;
	default:
		$access = PHPGW_CONTACTS_ALL;
	}

	if(!$start)
	{
		$start = 0;
	}

	if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
	   $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
	{
		$limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	}
	else
	{
		$limit = 15;
	}

	if($cat_id && $cat_id!=-1)
	{
		$category_filter = $cat_id;
	}
	else
	{
		$category_filter = PHPGW_CONTACTS_CATEGORIES_ALL;
	}

	$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

	$cols = array (
		'contact_id' => 'contact_id',
		'per_first_name'    => 'per_first_name',
		'per_last_name'   => 'per_last_name',
		'org_name'   => 'org_name',
	);


	//$entries = $d->read($start,$offset,$cols,$query,$qfilter,$sort,$order);
	$criteria = $d->criteria_for_index($account_id, $access, $category_filter, $cols, $query);
	$total_all_persons = $d->get_count_persons($criteria);
	$entries = $d->get_persons($cols, $limit, $start, $order, $sort, '', $criteria);

	if(is_array($entries))
	{
		foreach ($entries as $id)
		{
			$contacts[] = $id['contact_id'];
		}
		$entries_comm = $d->get_comm_contact_data($contacts, array('work email', 'home email','tel_cell'));
	}

//_debug_array($entries_comm);
	//------------------------------------------- nextmatch --------------------------------------------
	$left = $GLOBALS['phpgw']->nextmatchs->left('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',$start,$total_all_persons,"&order=$order&filter=$filter&sort=$sort&query=$query");
	$right = $GLOBALS['phpgw']->nextmatchs->right('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',$start,$total_all_persons,"&order=$order&filter=$filter&sort=$sort&query=$query");
	$GLOBALS['phpgw']->template->set_var('left',$left);
	$GLOBALS['phpgw']->template->set_var('right',$right);

	//$lang_showing = $GLOBALS['phpgw']->nextmatchs->show_hits($total_records,$this->start);
	$lang_showing = lang('%1 - %2 of %3 ',
			     ($d->total_records!=0)?$start+1:$start,
			     $start+$d->total_records,$total_all_persons);
	$GLOBALS['phpgw']->template->set_var('lang_showing', $lang_showing);


	// --------------------------------------- end nextmatch ------------------------------------------

	// ------------------- list header variable template-declaration -----------------------
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('sort_firstname',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'per_first_name',$order,'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',lang('Firstname')));
	$GLOBALS['phpgw']->template->set_var('sort_lastname',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'per_last_name',$order,'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',lang('Lastname')));
	$GLOBALS['phpgw']->template->set_var('sort_company',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'org_name',$order,'/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',lang('Company')));
	$GLOBALS['phpgw']->template->set_var('lang_email',lang('Select work email address'));
	$GLOBALS['phpgw']->template->set_var('lang_hemail',lang('Select home email address'));
	$GLOBALS['phpgw']->template->set_var('cats_list',$c->formated_list('select','all',$cat_id,'true'));
	$GLOBALS['phpgw']->template->set_var('lang_select',lang('Select'));

	//$GLOBALS['phpgw']->template->set_var('cats_action',$GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',"sort=$sort&order=$order&filter=$filter&start=$start&query=$query&cat_id=$cat_id"));
	// thanks to  dave.hall@mbox.com.au for fixing drop down list filtering by categories
	$GLOBALS['phpgw']->template->set_var('cats_action',$GLOBALS['phpgw']->link('/'.$GLOBALS['phpgw_info']['flags']['currentapp'].'/addressbook.php',"sort=$sort&order=$order&filter=$filter&start=$start&query=$query"));

	// --------------------------- end header declaration ----------------------------------
	for ($i=0;$i<count($entries);$i++)
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);
		$firstname = $entries[$i]['per_first_name'];
		if (!$firstname)
		{
			$firstname = '&nbsp;';
		}
		$lastname = $entries[$i]['per_last_name'];
		if (!$lastname)
		{
			$lastname = '&nbsp;';
		}
		// thanks to  dave.hall@mbox.com.au for adding company
		$company = $entries[$i]['org_name'];
		if (!$company)
		{
			$company = '&nbsp;';
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

		$tmp_email  = get_comm_value($entries[$i]['contact_id'], 'work email', $entries_comm);
		$tmp_hemail = get_comm_value($entries[$i]['contact_id'], 'home email', $entries_comm);
		if (($personal_part == '') ||
			($include_personal == false))
		{
			$id     = $entries[$i]['contact_id'];
			$email  = $tmp_email;
			$hemail = $tmp_hemail;
		}
		else
		{
			$id = $entries[$i]['contact_id'];
			if ((isset($tmp_email)) &&
				(trim($tmp_email) != ''))
			{
				$email  = '&quot;'.$personal_part.'&quot; &lt;'.$tmp_email.'&gt;';
			}
			else
			{
				$email  = $tmp_email;
			}
			if ((isset($tmp_hemail)) &&
			(trim($tmp_hemail) != ''))
			{
				$hemail = '&quot;'.$personal_part.'&quot; &lt;'.$tmp_hemail.'&gt;';
			}
			else
			{
				$hemail = $tmp_hemail;
			}
		}

		// --------------------- template declaration for list records --------------------------
		$GLOBALS['phpgw']->template->set_var(array(
			'firstname' => $firstname,
			'lastname'  => $lastname,
			'company'	=> $company
		));

		$GLOBALS['phpgw']->template->set_var('id',$id);
		$GLOBALS['phpgw']->template->set_var('email',$email);
		$GLOBALS['phpgw']->template->set_var('hemail',$hemail);

		$GLOBALS['phpgw']->template->parse('list','addressbook_list',true);
	}
	// --------------------------- end record declaration ---------------------------

	$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
	$GLOBALS['phpgw']->template->parse('out','addressbook_list_t',true);
	$GLOBALS['phpgw']->template->p('out');

	$GLOBALS['phpgw']->common->phpgw_exit();

	function get_comm_value($contact_id, $column, $entries_comm)
	{
		if(!is_array($entries_comm))
		{
			$entries_comm=array();
		}

		foreach($entries_comm as $comms)
		{
			if($contact_id == $comms['comm_contact_id'] && $column == $comms['comm_description'])
			{
				return $comms['comm_data'];
			}
		}
	}


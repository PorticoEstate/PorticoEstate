<?php
	/**
	* Addressbook Chooser
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id: 
	*/

	class pbaddressbook
	{
		var $public_functions=array ('show' => true,
					     'show_contactsframe' => true,
					     'show_contactsdetailframe' => true,
					     'show_repositoryframe' => true,
					     'show_contactsdetailframe'
					     );

		var $template;
		var $enable_contactsframe;
		var $enable_contactsdetailframe;


		/**
		* Popup Window to select accounts or contacts
		* 
		* @package phpgwapi
		* @subpackage uijsaddressbook
		*/
		function pbaddressbook()
		{
			$this->template = CreateObject('phpgwapi.Template');
	//		$this->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('phpgwapi'));
			$this->template->set_root(PHPGW_APP_TPL);
			$this->repositories = $this->get_repositories();
			$this->addRepo =& $this->repository_factory($_REQUEST['repository']);
			if(isset($_REQUEST['category']))
			{
				$this->enable_contactsframe = true;
				$this->addRepo->set_category($_REQUEST['category']);
			}
			if(isset($_REQUEST['id']))
			{
				$this->enable_contactsdetailframe = true;
			}
			if($_REQUEST['prefilter'])
			{
				$this->addRepo->set_filter(array(array('lastname' => $_REQUEST['prefilter'])));
			}
		}

		//@abstract public function merely outputs the parsed main template
		//@function show
		//@abstract public function merely outputs the parsed main template
		function show()
		{
			if(!$_REQUEST['save'])
			{
				$to_visibility        = $_REQUEST['hideto'] ? 'hidden' : 'visible';
				$cc_visibility        = $_REQUEST['hidecc'] ? 'hidden' : 'visible';;
				$bcc_visibility       = $_REQUEST['hidebcc'] ? 'hidden' : 'visible';
				$prefilter_visibility = $_REQUEST['hideprefilter'] ? 'hidden' : 'visible';
				$details_visibility   = $_REQUEST['hidedetails'] ? 'hidden' : 'visible';
				
//				$GLOBALS['phpgw']->common->phpgw_header();
				$this->template->set_file(array('main' => 'pbaddressbook.tpl'));
				$this->template->set_var('link_contactsframe', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.'.get_class($this).'.show_contactsframe')));
				$this->template->set_var('link_repositoryframe', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.'.get_class($this).'.show_repositoryframe')));
				$this->template->set_var('link_contactdetailsframe', $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'projects.'.get_class($this).'.show_contactsdetailframe')));
				$this->template->set_var('l_select_repository', lang('Select Repository'));
				$this->template->set_var('l_select_contact', lang('Select Contacts'));
				$this->template->set_var('l_edit_recipients', lang('Edit recipients'));
				$this->template->set_var('to_visibility', $to_visibility);
				$this->template->set_var('to', lang('to'));
				$this->template->set_var('cc_visibility', $cc_visibility);
				$this->template->set_var('cc', lang('cc'));
				$this->template->set_var('bcc_visibility', $bcc_visibility);
				$this->template->set_var('bcc', lang('bcc'));
				$this->template->set_var('prefilter_visibility', $prefilter_visibility);
				$this->template->set_var('details_visibility', $details_visibility);
				$this->template->parse('out','main',true);
				$this->template->p('out');
			}
			else // get the entries back to parent window
			{
				$this->template->set_var('targettagto', $_REQUEST['targettagto']);

				if(!$_REQUEST['hideto'])
				{
					$list[] = 'to';
				}
				if(!$_REQUEST['hidecc'])
				{
					$list[] = 'cc';
				}
				if(!$_REQUEST['hidebc'])
				{
					$list[] = 'bc';
				}

				$repositories = array();
				for($i=0; $i < count($list); $i++)
				{
					for($j=0; $j < count($_REQUEST[$list[$i]]); $j++)
					{
						$entry = $_REQUEST[$list[$i]][$j];
						$repoType = substr($entry, 0, strpos($entry, '_'));
						$id = substr($entry, (strpos($entry, '_') + 1));
						if(!array_key_exists($repoType, $repositories))
						{
							$repositories[$repoType] = $this->repository_factory($repoType);
						}
						$saveEntries[$list[$i]][] = $this->get_saveEntry($repositories[$repoType], $id);
					}
				}
				$this->parse_result($list, $saveEntries);
			}
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}

		function show_repositoryframe()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			$selected = array();
			if($_REQUEST['repository'])
			{
				$selected[] = $_REQUEST['repository'];
			}
			
			$categories = $this->addRepo->get_categories();

			$this->template->set_var('prefilter', lang('pre filter'));
			$this->template->set_file(array('repositoryframe' => 'pbaddressbook_repository.tpl'));
			$this->template->set_var('SBRepos', $this->parseSelectBox('repository', $this->repositories, '1', $selected, null, null, 'changeRepo(this)'));			
			
			$this->template->set_var('categories', $this->parseSelectBox('categories', $categories, '20', array(), null, 'name', 'changeCategory(this)'));
			$this->template->parse('out','repositoryframe',true);
			$this->template->p('out');
			$GLOBALS['phpgw']->common->phpgw_exit(false);
		}
		
		function show_contactsframe()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			if($this->enable_contactsframe)
			{
				$contacts = $this->addRepo->get_list();
			}

			$this->template->set_file(array('contactsframe' => 'pbaddressbook_contacts.tpl'));
			$this->template->set_var('contacts', $this->parseSelectBox('contacts', $contacts, '15', array(), null, 'fullname', 'show_contactdetails(this)', true));
			$this->template->set_var('repository', $this->addRepo->get_id());
			$this->template->set_var('enable_preview', lang('Enable detail preview'));
			$this->template->set_var('all', lang('all'));
			$this->template->parse('out','contactsframe',true);
			$this->template->p('out');
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}

		function show_contactsdetailframe()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			//$attributes = array('street', 'postalcode', 'city', 'organization', 'department', 'telefone'); 
			if($this->enable_contactsdetailframe)
			{
				$details = $this->addRepo->get_list($_REQUEST['category'],'id='.$_REQUEST['id'], $attributes);
			}
			$this->template->set_file(array('contactsdetailframe' => 'pbaddressbook_contactsdetails.tpl'));
			if($details)
			{
				$this->template->set_var('contactsdetails', $this->parse_contactsDetail($details[0]));
			}
			$this->template->parse('out','contactsdetailframe',true);
			$this->template->p('out');
			$GLOBALS['phpgw']->common->phpgw_exit(False);
		}

		function parse_contactsDetail($values, $mode = 'german')
		{
			$return  = '<table>';
			$return .= '	<tr>';
			$return .= '		<td colspan="2">';
			$return .= $values['department'];
			$return .= '		</td>';
			$return .= '	</tr>';
			$return .= '	<tr>';
			$return .= '		<td colspan="2">';
			$return .= $values['organization'];
			$return .= '		</td>';
			$return .= '	</tr>';
			$return .= '	<tr>';
			$return .= '		<td colspan="2">';
			$return .= $values['street'];
			$return .= '		</td>';
			$return .= '	</tr>';
			$return .= '	<tr>';
			$return .= '		<td>';
			$return .= $values['postalcode'];
			$return .= '		</td>';
			$return .= '		<td>';
			$return .= $values['city'];
			$return .= '		</td>';
			$return .= '	</tr>';
			if($values['telefone'])
			{
				$return .= '	<tr>';
				$return .= '		<td>';
				$return .= 'Tel.';
				$return .= '		</td>';
				$return .= '		<td>';
				$return .= $values['telefone'];
				$return .= '		</td>';
				$return .= '	</tr>';
			}
			$return .= '</table>';
			return $return;
		}

		function parseSelectBox($id, $values, $size = null, $selected = array(), $id_name = null, $value_name = null, $onchange=null, $multiple=false) //invent the wheel, dude
		{
			if($size)
			{
				$html_size = 'size="'.$size.'" ';
			}
			if(!$id_name)
			{
				$id_name = 'id';
			}
			if(!$value_name)
			{
				$value_name = 'value';
			}
			if($onchange)
			{
				$html_onchange = 'onchange="'.$onchange.'"';
			}
			if($multiple)
			{
				$html_multiple = 'multiple ';
			}
			$return = '<select '.$html_size.'name="'.$id.'" '.$html_multiple.$html_onchange.'>';
			for($i=0; $i < count($values); $i++)
			{
				if(in_array($values[$i][$id_name], $selected))
				{
					$html_selected = 'selected ';
				}
				else
				{
					$html_selected = '';
				}
				$return .= '<option value="'.$values[$i][$id_name].'" '.$html_selected.' >'.$values[$i][$value_name].'</option>';
			}
			$return .= '</select>';
			return $return;
		}
		
		function build_recipient($email, $fullname = null)
		{
			return '"'.$fullname.'" <'.$email.'>';
		}	
		
		function repository_factory($key)
		{
			if(!$key)
			{
				$key = $this->repositories[0]['id'];
			}
			switch ($key)
			{
				case 'izn':
				include_once('projects/inc/addressrepositories/class.addRepoLDAP.inc.php');
				$repo =  new addRepoLDAP('ldap://'.'cn=admin,dc=probusiness,dc=de'.'@pbdbserver/ou=people,ou=hannover,dc=probusiness,dc=de');
				$repo->set_id('izn');
				break;
				
				case 'mailalias':
				include_once('projects/inc/addressrepositories/class.addRepoLDAP.inc.php');
				$repo =  new addRepoLDAP('ldap://'.'cn=admin,dc=probusiness,dc=de'.'@nlkhtest:1389/ou=maildrops,dc=nlkhwunstorf,dc=niedersachsen,dc=de');
				$repo->set_id('mailalias');
				break;
				
				case 'phpgwaccount':
				include_once('projects/inc/addressrepositories/class.addRepoPHPAccount.inc.php');
				$repo =  new addRepoPHPAccount('mysql://'.$GLOBALS['phpgw']->accounts->get_id().'@anything');
				$repo->set_id('phpgwaccount');
				break;

				case 'phpgwOrgaContact':
				include_once('projects/inc/addressrepositories/class.addRepoPHPOrgaContact.inc.php');
				$repo =  new addRepoPHPOrgaContact('mysql://'.$GLOBALS['phpgw']->accounts->get_id().'@anything');
				$repo->set_id('phpgwOrgaContact');
				break;

				
				default:
				include_once('projects/inc/addressrepositories/class.addRepoPHPContact.inc.php');
				$repo = new addRepoPHPContact('mysql://'.$GLOBALS['phpgw']->accounts->get_id().'@anything');
				$repo->set_id('phpgwcontact');
			}
			return $repo;
		}
		
		//@function forget_all
		//@access public
		//@abstract This function makes the bo class forget everything in cache
		//@discuss this function is directly called by email module. So I have to
		// keep it :-( 
		function
		forget_all($non_interactive="")
		{
			
			$this->bo=CreateObject("phpgwapi.bojsaddressbook");
			$this->bo->forget_destboxes();
			$this->bo->forget_query();
			//forget our own cache
			$GLOBALS['phpgw']->session->appsession('jsuibook_sbox','phpgwapi',"");
			$GLOBALS['phpgw']->session->appsession('jsuibook_acbox','phpgwapi',"");
			$GLOBALS['phpgw']->session->appsession('jsuibook_catid','phpgwapi','');
			if($non_interactive=="")
			{
				print $this->final_js("","window.parent.all_forgoten();");
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		}
		
		function get_saveEntry(&$repo, $id)
		{
			$attributes = array('fullname', 'email');
			$list = $repo->get_list($_REQUEST['category'],'id='.$id, $attributes);
			return array ('id' => $id,
			              'name' => $this->build_recipient($list[0]['email'], $list[0]['fullname'])
			             );
		}
		
		function get_repositories()
		{
			$repositories[] = array('id'    => 'phpgwcontact',
			                        'value' => 'Kontakte'
			                       );
			return $repositories;
		}
		
		function parse_result($list, $saveEntries)
		{
			for($i = 0; $i < count($list); $i++)
			{
				for($j = 0; $j < count($saveEntries[$list[$i]]); $j++)
				{
					if($saveEntries[$list[$i]][$j]['name'])
					{
						$htmlEntries[$list[$i]][] = $saveEntries[$list[$i]][$j]['name'];
					}
				}
				if(count($htmlEntries[$list[$i]]))
				{
					switch($list[$i])
					{
						case "to":
						$this->template->set_var('to', "opener.document.getElementById('".$_REQUEST['targettagto']."').value = '".implode(',', $htmlEntries[$list[$i]])."';");
						break;
						
						case "cc":
						$this->template->set_var('cc', "opener.document.getElementById('".$_REQUEST['targettagcc']."').value = '".implode(',', $htmlEntries[$list[$i]])."';");
						break;
						
						case "bc":
						$this->template->set_var('bc', "opener.document.getElementById('".$_REQUEST['targettagbc']."').value = '".implode(',', $htmlEntries[$list[$i]])."';");
						break;
					}
				}
			}

			$this->template->set_file(array('result' => 'pbaddressbook_result.tpl'));
			$this->template->parse('out','result',true);
			$this->template->p('out');
			return true;
		}
		
	}
?>

<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
 	* @version $Id: class.menu.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_menu
	{
		var $sub;
		var $query;

		var $public_functions = array
		(
			'links'	=> True,
		);

		function property_menu($sub='')
		{
			$this->sub		= $sub;
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->query	= phpgw::get_var('query');
		}

		function links($page='',$page_2='')
		{
			$currentapp='property';
			$sub = $this->sub;
			if(!$this->query)
			{
				$menu = $GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20));
			}
//_debug_array($page);
			if(!isset($menu) || !$menu)
			{
				$menu = array();  // set to '' as appsession dos'nt return empty array correctly 
				$this->acl 			= CreateObject('phpgwapi.acl');

				$i=0;
				if ($this->acl->check('.location',1))
				{
					if($sub=='location')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url'] 		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.index', 'type_id'=>1));
					$menu['module'][$i]['text'] 		= lang('Location');
					$menu['module'][$i]['statustext'] 	= lang('Location');
					$i++;
				}

				if ($this->acl->check('.ifc',1))
				{
					if($sub=='ifc')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiifc.import'));
					$menu['module'][$i]['text']			=	lang('IFC');
					$menu['module'][$i]['statustext']	=	lang('IFC');
					$i++;
				}

				if ($this->acl->check('.ticket',1))
				{
					if($sub=='ticket')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uitts.index'));
					$menu['module'][$i]['text']			=	lang('Helpdesk');
					$menu['module'][$i]['statustext']	=	lang('Helpdesk');
					$i++;
				}

				if ($this->acl->check('.project',1))
				{
					if($sub=='project')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiproject.index'));
					$menu['module'][$i]['text']			=	lang('Project');
					$menu['module'][$i]['statustext']	=	lang('Project');
					$i++;
				}

				if ($this->acl->check('.invoice',1))
				{
					if($sub=='invoice')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.index'));
					$menu['module'][$i]['text']			=	lang('Invoice');
					$menu['module'][$i]['statustext']	=	lang('Invoice');
					$i++;
				}

				if ($this->acl->check('.budget',1))
				{
					if($sub=='budget')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.index'));
					$menu['module'][$i]['text']			=	lang('Budget');
					$menu['module'][$i]['statustext']	=	lang('Budget');
					$i++;
				}
//--------------------
				if ($this->acl->check('.agreement',1))
				{
					if($sub=='agreement')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
					$menu['module'][$i]['text']			=	lang('Agreement');
					$menu['module'][$i]['statustext']	=	lang('Agreement');
					$i++;
				}
//----------------------

				if ($this->acl->check('.document',1))
				{
					if($sub=='document')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uidocument.index'));
					$menu['module'][$i]['text']			=	lang('Documentation');
					$menu['module'][$i]['statustext']	=	lang('Documentation');
					$i++;
				}

				if ($this->acl->check('.custom',1))
				{
					if($sub=='custom')
					{
						$menu['module'][$i]['this']=True;
					}
					$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uicustom.index'));
					$menu['module'][$i]['text']			=	lang('Custom');
					$menu['module'][$i]['statustext']	=	lang('Custom queries');
				}
	
				$entity			= CreateObject('property.soadmin_entity');
				$entity_list 	= $entity->read(array('allrows'=>True));

				if (isset($entity_list) AND is_array($entity_list))
				{
					foreach($entity_list as $entry)
					{
						if ($this->acl->check('.entity.' . $entry['id'],1))
						{
							$i++;
							if($sub=='entity_' . $entry['id'])
							{
								$menu['module'][$i]['this']=True;
							}
							$menu['module'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uientity.index', 'entity_id'=> $entry['id']));
							$menu['module'][$i]['text']			=	$entry['name'];
							$menu['module'][$i]['statustext']	=	$entry['descr'];
						}
					}
				}

				unset($entity);

				$i = 0;
				if ($this->acl->check('.location',1))
				{
					if ($sub == 'location')
					{
						$menu['menu_title_2']=lang('Location');

						$soadmin_location	= CreateObject('property.soadmin_location');
						$location	= $soadmin_location->select_location_type();
						$query_temp = explode('-',$this->query);
						$query_location = '';
						
						$location_count=count($location);
						for ($j=0; $j<$location_count; $j++)
						{
							if(isset($query_temp[$j]) && $query_temp[$j])
							{
								$query[] = $query_temp[$j];
								$query_location = implode('-',$query);
							}
							if($page=='location'.$location[$j]['id'].'_')
							{
								$menu['sub_menu'][$i]['this']=True;
							}
							$menu['sub_menu'][$i]['url'] = $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.index', 'type_id'=> $location[$j]['id'],'query'=>$query_location));
							$menu['sub_menu'][$i]['text'] = $location[$j]['name'];
							$menu['sub_menu'][$i]['statustext'] = $location[$j]['descr'];
							$i++;
						}

						$config_tenant = $soadmin_location->read_config_single('tenant_id');

						if(substr($page,-2)=='_1')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.index', 'lookup_tenant'=>1, 'type_id'=> $config_tenant,'query'=>$query_location));
						$menu['sub_menu'][$i]['text']			=	lang('Tenant');
						$menu['sub_menu'][$i]['statustext']		=	lang('Tenant');
						$i++;
					
						if($page=='gab')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uigab.index'));
						$menu['sub_menu'][$i]['text']			=	lang('gabnr');
						$menu['sub_menu'][$i]['statustext']		=	lang('gabnr');
						$i++;
					
						if($page=='summary')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uilocation.summary'));
						$menu['sub_menu'][$i]['text']			=	lang('Summary');
						$menu['sub_menu'][$i]['statustext']		=	lang('Summary');
						$i++;
					}
				}

				if ($sub == 'invoice')
				{
					$menu['menu_title_2']=lang('Invoice');

					if($page=='invoice_')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Invoice');
					$menu['sub_menu'][$i]['statustext']		=	lang('Invoice');
					$i++;

					if($page=='invoice_1')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.index', 'paid'=>true));
					$menu['sub_menu'][$i]['text']			=	lang('Paid');
					$menu['sub_menu'][$i]['statustext']		=	lang('Paid');
					$i++;

					if($page=='consume')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.consume'));
					$menu['sub_menu'][$i]['text']			=	lang('consume');
					$menu['sub_menu'][$i]['statustext']		=	lang('consume');
					$i++;

					if($page=='b_account')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uib_account.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Budget account');
					$menu['sub_menu'][$i]['statustext']		=	lang('Budget account');
					$i++;

					if($page=='vendor')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiactor.index', 'role'=> 'vendor'));
					$menu['sub_menu'][$i]['text']			=	lang('Vendor');
					$menu['sub_menu'][$i]['statustext']		=	lang('Vendor');
					$i++;

					if($page=='tenant')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiactor.index', 'role'=> 'tenant'));
					$menu['sub_menu'][$i]['text']			=	lang('Tenant');
					$menu['sub_menu'][$i]['statustext']		=	lang('Tenant');
					$i++;
					
					if ($this->acl->check('.invoice',16))
					{
						if($page=='investment')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvestment.index'));
						$menu['sub_menu'][$i]['text']			=	lang('Investment value');
						$menu['sub_menu'][$i]['statustext']		=	lang('Investment value');
						$i++;

						if($page=='import_inv')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiXport.import'));
						$menu['sub_menu'][$i]['text']			=	lang('Import invoice');
						$menu['sub_menu'][$i]['statustext']		=	lang('Import invoice');
						$i++;

						if($page=='export_inv')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiXport.export'));
						$menu['sub_menu'][$i]['text']			=	lang('Export invoice');
						$menu['sub_menu'][$i]['statustext']		=	lang('Export invoice');
						$i++;
					}

					if ($this->acl->check('.invoice',2))
					{
						if($page=='add_inv')
						{
							$menu['sub_menu'][$i]['this']=True;
						}
						$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiinvoice.add'));
						$menu['sub_menu'][$i]['text']			=	lang('Add');
						$menu['sub_menu'][$i]['statustext']		=	lang('Add invoice');
						$i++;
					}
				}

				if ($sub == 'pricebook' && $this->acl->check('.agreement',16))
				{
					$menu['menu_title_2']=lang('pricebook');

					if($page=='agreement_group')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.agreement_group'));
					$menu['sub_menu'][$i]['text']			=	lang('Agreement group');
					$menu['sub_menu'][$i]['statustext']		=	lang('Agreement group');
					$i++;

					if($page=='activity')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.activity'));
					$menu['sub_menu'][$i]['text']			=	lang('Activities');
					$menu['sub_menu'][$i]['statustext']		=	lang('Activities');
					$i++;

					if($page=='agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Agreement');
					$menu['sub_menu'][$i]['statustext']		=	lang('Agreement');
					$i++;
				}
//--------------
				if ($sub == 'agreement')
				{
					$menu['menu_title_2']=lang('Agreement');
					if($page=='agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Pricebook');
					$menu['sub_menu'][$i]['statustext']		=	lang('Pricebook');
					$i++;

					if($page=='s_agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uis_agreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Service');
					$menu['sub_menu'][$i]['statustext']		=	lang('service agreement');
					$i++;

					if($page=='r_agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uir_agreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Rental');
					$menu['sub_menu'][$i]['statustext']		=	lang('Rental agreement');
					$i++;

					if($page=='alarm')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uialarm.list_alarm'));
					$menu['sub_menu'][$i]['text']			=	lang('alarm');
					$menu['sub_menu'][$i]['statustext']		=	lang('alarm');
					$i++;

					if($this->acl->check('.agreement',16) && $page=='agreement')
					{
						$menu['menu_title_3']=lang('pricebook');

						$j=0;
						if($page_2=='agreement_group')
						{
							$menu['sub_menu_2'][$j]['this']=True;
						}
						$menu['sub_menu_2'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.agreement_group'));
						$menu['sub_menu_2'][$j]['text']			=	lang('Agreement group');
						$menu['sub_menu_2'][$j]['statustext']	=	lang('Agreement group');
						$j++;

						if($page_2=='activity')
						{
							$menu['sub_menu_2'][$j]['this']=True;
						}
						$menu['sub_menu_2'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uipricebook.activity'));
						$menu['sub_menu_2'][$j]['text']			=	lang('Activities');
						$menu['sub_menu_2'][$j]['statustext']	=	lang('Activities');
						$j++;

						if($page_2=='agreement')
						{
							$menu['sub_menu_2'][$j]['this']=True;
						}
						$menu['sub_menu_2'][$j]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiagreement.index'));
						$menu['sub_menu_2'][$j]['text']			=	lang('Agreement');
						$menu['sub_menu_2'][$j]['statustext']	=	lang('Agreement');
						$j++;
					}

				}

//---------------
				if ($sub == 'project')
				{
					$menu['menu_title_2']=lang('Project');

					if($page=='project')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiproject.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Project');
					$menu['sub_menu'][$i]['statustext']		=	lang('Project');
					$i++;

					if($page=='workorder')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiworkorder.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Workorder');
					$menu['sub_menu'][$i]['statustext']		=	lang('Workorder');
					$i++;
					
/*
					if($page=='s_agreement')
					{
						$menu['sub_menu'][$i]['this']=True;
						$menu['menu_title_2'][]=lang('Service');
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uis_agreement.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Service');
					$menu['sub_menu'][$i]['statustext']		=	lang('service agreement');
					$i++;
*/
					if($page=='request')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uirequest.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Request');
					$menu['sub_menu'][$i]['statustext']		=	lang('Request');
					$i++;

					if($page=='template')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uitemplate.index'));
					$menu['sub_menu'][$i]['text']			=	lang('template');
					$menu['sub_menu'][$i]['statustext']		=	lang('Workorder template');
					$i++;

					if($page=='tenant_claim')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uitenant_claim.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Tenant claim');
					$menu['sub_menu'][$i]['statustext']		=	lang('Tenant claim');
					$i++;
				}

				if ($sub == 'adm_loc' && $this->acl->check('.location',16))
				{
					$menu['menu_title_2']=lang('Admin location');
					if($page=='loc_type')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiadmin_location.index'));
					$menu['sub_menu'][$i]['text']			=	lang('Location type');
					$menu['sub_menu'][$i]['statustext']		=	lang('Location type');
					$i++;

					if($page=='loc_config')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiadmin_location.config'));
					$menu['sub_menu'][$i]['text']			=	lang('Config');
					$menu['sub_menu'][$i]['statustext']		=	lang('Location Config');
					$i++;
				}

				if ($sub == 'document')
				{
					$menu['menu_title_2']=lang('documentation');
					if($page=='document_')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uidocument.index'));
					$menu['sub_menu'][$i]['text']			=	lang('location');
					$menu['sub_menu'][$i]['statustext']		=	lang('Documentation for locations');
					$i++;
					
					if (isset($entity_list) AND is_array($entity_list))
					{
						foreach($entity_list as $entry)
						{
							if($entry['documentation'])
							{
								if($page=='document_'.$entry['id'])
								{
									$menu['sub_menu'][$i]['this']=True;
								}
								$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uidocument.index', 'entity_id' => $entry['id']));
								$menu['sub_menu'][$i]['text']			=	$entry['name'];
								$menu['sub_menu'][$i]['statustext']		=	$entry['descr'];
								$i++;
							}
						}
					}

				}
				
				if ($sub == 'budget')
				{
					$menu['menu_title_2']=lang('budget');

					if($page=='budget.basis')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.basis'));
					$menu['sub_menu'][$i]['text']			=	lang('basis');
					$menu['sub_menu'][$i]['statustext']		=	lang('budget per group');
					$i++;

					if($page=='budget')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.index'));
					$menu['sub_menu'][$i]['text']			=	lang('budget');
					$menu['sub_menu'][$i]['statustext']		=	lang('budget');
					$i++;

					if($page=='budget.obligations')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uibudget.obligations'));
					$menu['sub_menu'][$i]['text']			=	lang('obligations');
					$menu['sub_menu'][$i]['statustext']		=	lang('contractual obligations');
					$i++;
				}

				if ($sub == 'ifc')
				{
					$menu['menu_title_2']=lang('ifc');

					if($page=='ifc.import')
					{
						$menu['sub_menu'][$i]['this']=True;
					}
					$menu['sub_menu'][$i]['url']			=	$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> $currentapp.'.uiifc.import'));
					$menu['sub_menu'][$i]['text']			=	lang('import');
					$menu['sub_menu'][$i]['statustext']		=	lang('import ifc xml');
					$i++;

				}

				$GLOBALS['phpgw']->session->appsession('menu',substr(md5($currentapp.$sub . '_' . $page . '_' . $page_2),-20),$menu);
			}

			$GLOBALS['phpgw']->session->appsession('menu_property','sidebox',$menu);
			return $menu;
		}
	}
?>

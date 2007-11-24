<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.soprojects.inc.php,v 1.120 2007/09/05 11:45:29 sigurdne Exp $
	* $Source: /sources/phpgroupware/projects/inc/class.soprojects.inc.php,v $
	*/

	class soprojects
	{
		var $db;
		var $grants;
		var $column_array;

		function soprojects()
		{
			$this->db			= $GLOBALS['phpgw']->db;
			$this->db2			= $this->db;
			$this->grants		= $GLOBALS['phpgw']->acl->get_grants('projects');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->currency 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
			$this->year			= $GLOBALS['phpgw']->common->show_date(time(),'Y');
			$this->member		= $this->get_acl_projects();
			$this->soconfig		= CreateObject('projects.soconfig');
			$this->siteconfig	= $this->soconfig->get_site_config();
			$this->column_array = array();
		}

		function project_filter($type)
		{
			switch ($type)
			{
				case 'subs':			$s = ' and parent != 0'; break;
				case 'mains':			$s = ' and parent = 0'; break;
				default: return False;
            }
			return $s;
		}

		function db2projects($column = false)
		{
			$projects = array();

			$i = 0;
			while ( $this->db->next_record() )
			{
				if($column)
				{
					$projects[$i] = array();
					for($k=0;$k<count($this->column_array);$k++)
					{
						$projects[$i][$this->column_array[$k]] = $this->db->f($this->column_array[$k]);
					}
					$i++;
				}
				else
				{
					$projects[] = array
					(
						'project_id'					=> $this->db->f('project_id'),
						'parent'						=> $this->db->f('parent'),
						'number'						=> $this->db->f('p_number'),
						'access'						=> $this->db->f('access'),
						'cat'							=> $this->db->f('category'),
						'sdate'							=> $this->db->f('start_date'),
						'edate'							=> $this->db->f('end_date'),
						'coordinator'					=> $this->db->f('coordinator'),
						'customer'						=> $this->db->f('customer'),
						'customer_org'					=> $this->db->f('customer_org'),
						'status'						=> $this->db->f('status'),
						'descr'							=> $this->db->f('descr'),
						'title'							=> $this->db->f('title'),
						'budget'						=> $this->db->f('budget'),
						'budget_childs'     			=> $this->db->f('budget_childs'),
						'e_budget'						=> $this->db->f('e_budget'),
						'e_budget_childs'   			=> $this->db->f('e_budget_childs'),
						'ptime'							=> $this->db->f('time_planned'),
						'ptime_childs'      			=> $this->db->f('time_planned_childs'),
						'owner'							=> $this->db->f('owner'),
						'cdate'							=> $this->db->f('date_created'),
						'processor'						=> $this->db->f('processor'),
						'udate'							=> $this->db->f('entry_date'),
						'investment_nr'					=> $this->db->f('investment_nr'),
						'main'							=> $this->db->f('main'),
						'level'							=> $this->db->f('level'),
						'previous'						=> $this->db->f('previous'),
						'customer_nr'					=> $this->db->f('customer_nr'),
						'salesmanager'					=> $this->db->f('salesmanager'),
						'url'							=> $this->db->f('url'),
						'reference'						=> $this->db->f('reference'),
						'result'						=> $this->db->f('result'),
						'test'							=> $this->db->f('test'),
						'quality'						=> $this->db->f('quality'),
						'accounting'					=> $this->db->f('accounting'),
						'project_accounting_factor'		=> $this->db->f('acc_factor'),
						'project_accounting_factor_d'	=> $this->db->f('acc_factor_d'),
						'billable'						=> $this->db->f('billable'),
						'psdate'						=> $this->db->f('psdate'),
						'pedate'						=> $this->db->f('pedate'),
						'priority'						=> $this->db->f('priority'),
						'discount'						=> $this->db->f('discount'),
						'discount_type'					=> $this->db->f('discount_type'),
						'inv_method'					=> $this->db->f('inv_method'),
						'plan_bottom_up'				=> $this->db->f('plan_bottom_up'),
						'direct_work'					=> $this->db->f('direct_work'),
						'level'				      		=> $this->db->f('level'),
						'acc_type'						=> $this->db->f('acc_type')
					);
				}
			}
			return $projects;
		}

		function read_projects( $values )
		{
			$start		= isset( $values['start'] ) ? intval($values['start']) : 0;			$limit		= isset( $values['limit'] ) ? $values['limit'] : true;
			$filter		= isset( $values['filter'] ) && $values['filter'] ? $values['filter'] : 'none';
			$sort		= isset( $values['sort'] ) && $values['sort'] ? $values['sort'] : 'ASC';
			$order		= isset( $values['order'] ) && $values['order'] ? $values['order'] : 'p_number,title,start_date';
			$status		= isset( $values['status'] ) ? $values['status'] : 'active';
			$action		= isset( $values['action'] ) ? $values['action'] : 'mains';

			$cat_id		= isset( $values['cat_id'] ) ? intval($values['cat_id']) : 0;
			$main		= isset( $values['main'] ) ? intval($values['main']) : 0;
			$parent		= isset( $values['parent'] ) ? intval($values['parent']) : 0;
			$project_id = isset( $values['project_id'] ) ? intval($values['project_id']) : 0;
			$column		= isset( $values['column'] ) ? $values['column'] : false;
			$employee	= isset( $values['employee'] ) ? $values['employee'] : '';

			$query		= $this->db->db_addslashes(isset($values['query']) ? $values['query'] : '');

			// TODO: Finn
			$statussort = '';
			if ( $status )
			{
				$statussort = " AND status = '" . $status . "' ";
			}
/*
			else
			{
				$statussort = " AND status != 'archive' ";
			}

			if($order == 'coordinator')
			{
				$order = 'phpgw_accounts.account_lastname';
			}
*/
			$ordermethod = " order by $order $sort";

			if ( $filter == 'none' || $filter == 'noadmin' )
			{
				if ( $filter == 'none' && ($this->soconfig->isprojectadmin('pad') || $this->soconfig->isprojectadmin('pmanager') || $this->soconfig->isprojectadmin('psale')) )
				{
					$filtermethod = " ( access != 'private' OR coordinator = " . $this->account . ' )';
				}
				else
				{
					$filtermethod = ' ( coordinator=' . $this->account;

					if ( is_array($this->grants) )
					{
						$grants = $this->grants;

						while( list($user) = each($grants) )
						{
							$public_user_list[] = $user;
						}

						reset($public_user_list);

						$filtermethod .= " OR (access != 'private' AND coordinator in(" . implode(',', $public_user_list) . '))';
					}

					if ( is_array($this->member) )
					{
						$filtermethod .= " OR (access != 'private' AND project_id in(" . implode(',', $this->member) . '))';
					}

					$filtermethod .= ' )';
				}
			}
			elseif ($filter == 'yours')
			{
				$filtermethod = ' coordinator=' . $this->account;
			}
			elseif ($filter == 'anonym')
			{
				$filtermethod = " access = 'anonym' ";
			}
			elseif ($filter == 'employee')
			{
				$filtermethod = ' employee =' . $employee;
			}
			else
			{
				$filtermethod = ' coordinator=' . $this->account . " AND access='private'";
			}

			if ($cat_id > 0)
			{
				$filtermethod .= ' AND category=' . $cat_id;
			}

			switch( $action )
			{
				case 'all':
				case 'mains':
					$parent_select = ' AND parent=0';
					break;
				case 'subs':
					$parent_select = ' AND (parent=' . $parent . ' AND parent != 0)';
					break;
				case 'mainandsubs':
					$parent_select = ' AND main=' . $main;
					break;
				case 'mainsubsorted':
					$parent_select = ' AND project_id=' . $project_id;
					break;
			}

			// TODO: Finn
			$querymethod = '';
			if ( $query )
			{
				$querymethod = " AND (title like '%$query%' OR p_number like '%$query%' OR descr like '%$query%') ";
			}

			$column_select = ( ( is_string($column) && $column != '' ) ? $column : '*' );
			$this->column_array = explode(',', $column);

			$sql = "SELECT $column_select from phpgw_p_projects WHERE $filtermethod $statussort $querymethod";
			//wenn accounts in db: $sql = "SELECT $column_select from phpgw_p_projects , phpgw_accounts WHERE $filtermethod $statussort $querymethod AND coordinator=account_id";

			if ( $limit && $action == 'mains' )
			{
				$this->db2->query($sql . $parent_select, __LINE__, __FILE__);

				//echo 'query main: ' . $sql . $parent_select;

				$total = $this->db2->num_rows();

				$this->db->limit_query($sql . $parent_select . $ordermethod, $start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $parent_select . $ordermethod, __LINE__, __FILE__);
				$total = $this->db->num_rows();
			}

			$pro = $this->db2projects($column);

			if ($main == 0 && $action != 'mains')
			{
				$num_pro = count($pro);
				for ($i=0;$i < $num_pro;$i++)
				{
					$sub_select = ' AND parent=' . $pro[$i]['project_id'] . ' AND level=' . ($pro[$i]['level']+1);
					$this->db->query($sql . $sub_select . $ordermethod,__LINE__,__FILE__);
					$total += $this->db->num_rows();
					$subpro = $this->db2projects($column);

					$num_subpro = count($subpro);
					if ($num_subpro != 0)
					{
						$newpro = array();
						for ($k = 0; $k <= $i; $k++)
						{
							$newpro[$k] = $pro[$k];
						}
						for ($k = 0; $k < $num_subpro; $k++)
						{
							$newpro[$k+$i+1] = $subpro[$k];
						}
						for ($k = $i+1; $k < $num_pro; $k++)
						{
							$newpro[$k+$num_subpro] = $pro[$k];
						}
						$pro = $newpro;
						$num_pro = count($pro);
					}
				}
			}

			$this->total_records = $total;
			if ($limit && $main == 0 && $action != 'mains')
			{
				$max = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
				$max = $max + $start;

				$k=0;
				for($i=$start;$i<$max;++$i)
				{
					if(is_array($pro[$i]))
					{
						$spro[$k] = $pro[$i];
						++$k;
					}
				}
				if(is_array($spro))
				{
					$pro = $spro;
				}
			}

			//_debug_array($pro);
			return $pro;
		}

		function read_gantt_projects($data)
		{
			$parent_array	= $data['parent_array'];
			$project_id		= intval($data['project_id']);

			$ordermethod = ' order by p_number,title,start_date ASC';

			$main_select = 'project_id=' . $data['project_id'];

			$sql = 'SELECT * from phpgw_p_projects WHERE ';

			$this->db->query($sql . $main_select . $ordermethod,__LINE__,__FILE__);

			$pro = $this->db2projects();

			if (is_array($parent_array))
			{
				$num_pro = count($pro);
				for ($i=0;$i < $num_pro;$i++)
				{
					if(in_array($pro[$i]['project_id'],$parent_array))
					{
						$sub_select = 'parent=' . $pro[$i]['project_id'] . ' AND level=' . ($pro[$i]['level']+1);
						$this->db->query($sql . $sub_select . $ordermethod,__LINE__,__FILE__);
						$subpro = $this->db2projects();

						$num_subpro = count($subpro);
						if ($num_subpro != 0)
						{
							$newpro = array();
							for ($k = 0; $k <= $i; $k++)
							{
								$newpro[$k] = $pro[$k];
							}
							for ($k = 0; $k < $num_subpro; $k++)
							{
								$newpro[$k+$i+1] = $subpro[$k];
							}
							for ($k = $i+1; $k < $num_pro; $k++)
							{
								$newpro[$k+$num_subpro] = $pro[$k];
							}
							$pro = $newpro;
							$num_pro = count($pro);
						}
					}
				}
			}
			return $pro;
		}

		function read_single_project($project_id)
		{
			$this->db->query('SELECT * from phpgw_p_projects WHERE project_id=' . intval($project_id), __LINE__, __FILE__);

			list($project) = $this->db2projects();

			return $project;
		}

		function select_project_list($values)
		{
			$formatted = isset( $values['formatted'] ) ? $values['formatted'] : true;

			$pro = $this->read_projects( array
			(
				'limit'		=> false,
				'status'	=> isset( $values['status'] ) ? $values['status'] : '',
				'action'	=> isset( $values['action'] ) ? $values['action'] : '',
				'main'		=> isset( $values['main'] ) ? $values['main'] : '',
				'filter'	=> isset( $values['filter'] ) ? $values['filter'] : '',
				'column'	=> 'project_id, p_number, level, title',
				'order'		=> 'title'
			));

			if( isset( $values['self'] ) )
			{
				for ( $i=0; $i < count($pro); $i++ )
				{
					if ( $pro[$i]['project_id'] == $values['self'] )
					{
						unset( $pro[$i] );
					}
				}
			}

			if( is_array($pro) && $formatted )
			{
				$s = '';
				foreach( $pro as $p )
				{
					$s .= '<option value="' . $p['project_id'] . '"';
					if ( $p['project_id'] == $values['selected'] )
					{
						$s .= ' selected';
					}
					$s .= '>';

					for ( $j=0;$j<$p['level'];$j++ )
					{
						$s .= '&nbsp;.&nbsp;';
					}

					$s .= $GLOBALS['phpgw']->strip_html( $p['title'] ) . ' [ ' . $GLOBALS['phpgw']->strip_html( $p['number']?$p['number']:$p['p_number'] ) . ' ]';
					$s .= '</option>';
				}
			}
			return $formatted ? $s : $pro;
		}

		function add_project( $values )
		{
			$values['descr']						= $this->db->db_addslashes( $values['descr'] );
			$values['title']						= $this->db->db_addslashes( $values['title'] );
			$values['number']						= $this->db->db_addslashes( $values['number'] );
			$values['investment_nr']				= $this->db->db_addslashes( $values['investment_nr'] );
			$values['customer_nr']					= $this->db->db_addslashes( $values['customer_nr'] );
			$values['result']						= $this->db->db_addslashes( $values['result'] );
			$values['test']							= $this->db->db_addslashes( $values['test'] );
			$values['quality']						= $this->db->db_addslashes( $values['quality'] );
			$values['inv_method']					= $this->db->db_addslashes( $values['inv_method'] );
			$values['url']							= $this->db->db_addslashes( $values['url'] );
			$values['reference']					= $this->db->db_addslashes( $values['reference'] );

			$values['budget']						= $values['budget'] + 0.0;
			$values['budget_childs']				= $values['budget_childs'] + 0.0;
			$values['e_budget']						= $values['e_budget'] + 0.0;
			$values['e_budget_childs']				= $values['e_budget_childs'] + 0.0;
			$values['discount']						= $values['discount'] + 0.0;
			$values['project_accounting_factor']	= $values['project_accounting_factor'] + 0.0;
			$values['project_accounting_factor_d']	= $values['project_accounting_factor_d'] + 0.0;
			$values['parent']						= intval($values['parent']);

			if ($values['parent'] > 0)
			{
				$values['main'] = intval( $this->id2item( array
				(
					'item_id' => $values['parent'],
					'item' => 'main'
				)));
				$values['level'] = intval( $this->id2item( array
				(
					'item_id' => $values['parent'],
					'item' => 'level'
				))+1);
			}

			$table = 'phpgw_p_projects';
			$this->db->lock( $table );

			$this->db->query('INSERT into phpgw_p_projects (owner,access,category,entry_date,start_date,end_date,coordinator,customer,status,'
							. 'descr,title,budget,budget_childs,p_number,parent,time_planned,time_planned_childs,date_created,processor,investment_nr,main,level,previous,'
							. 'customer_nr,url,reference,result,test,quality,accounting,acc_factor,acc_factor_d,billable,inv_method,psdate,pedate,priority,e_budget,e_budget_childs,
							discount,discount_type,plan_bottom_up,customer_org,direct_work,salesmanager,acc_type) VALUES ('
							. $this->account . ",'" . (isset($values['access'])?$values['access']:'public') . "'," . intval($values['cat']) . ',' . time() . ','
							. intval($values['sdate']) . ',' . intval($values['edate']) . ',' . intval($values['coordinator']) . ',' . intval($values['customer']) . ",'"
							. $values['status'] . "','" . $values['descr'] . "','" . $values['title'] . "'," . $values['budget'] . "," . $values['budget_childs'] . ",'" . $values['number'] . "',"
							. $values['parent'] . ',' . intval($values['ptime']) . ',' . intval($values['ptime_childs']) . ',' . time() . ',' . $this->account . ",'" . $values['investment_nr']
							. "'," . intval($values['main']) . ',' . intval($values['level']) . ',' . intval($values['previous']) . ",'"
							. $values['customer_nr'] . "','" . $values['url'] . "','" . $values['reference'] . "','" . $values['result'] . "','"
							. $values['test'] . "','" . $values['quality'] . "','" . $values['accounting'] . "'," . $values['project_accounting_factor']
							. ',' . $values['project_accounting_factor_d'] . ",'". ($values['billable']?'Y':'N') . "','" . $values['inv_method'] . "',"
							. intval($values['psdate']) . ',' . intval($values['pedate']) . ',' . intval($values['priority']) . ',' . $values['e_budget'] . ',' . $values['e_budget_childs'] . ','
							. $values['discount'] . ",'" . $values['discount_type'] . "', '".$values['plan_bottom_up']."', ".intval($values['customer_org']).", '".$values['direct_work']."', '".intval($values['salesmanager'])."', '".$values['acc_type']."')",__LINE__,__FILE__);

			$p_id = $this->db->get_last_insert_id( $table,'project_id' );
			$this->db->unlock();

			if ( $p_id && ( $p_id != 0 ) )
			{
				if ( $values['parent'] == 0 )
				{
					$this->db->query( 'UPDATE phpgw_p_projects SET main=' . $p_id . ' WHERE project_id=' . $p_id,__LINE__,__FILE__ );
				}

				if ( is_array( $values['book_activities'] ) )
				{
					while( $activ=each( $values['book_activities'] ) )
					{
						$this->db->query('insert into phpgw_p_projectactivities (project_id,activity_id,billable) values (' . $p_id . ','
										. $activ[1] . ",'N')",__LINE__,__FILE__);
					}
				}

				if ( is_array( $values['bill_activities'] ) )
				{
					while( $activ = each( $values['bill_activities'] ) )
					{
						$this->db->query('insert into phpgw_p_projectactivities (project_id,activity_id,billable) values (' . $p_id . ',' . $activ[1] . ",'Y')",__LINE__,__FILE__);
					}
				}
				return $p_id;
			}
			return false;
		}

		function subs( $parent, &$subs, &$main )
		{
			if ( !is_array( $main ) )
			{
				$this->db->query('SELECT * from phpgw_p_projects WHERE main=' . $main,__LINE__,__FILE__);
				$main = $this->db2projects();
				//echo "main: "; _debug_array($main);
			}

			reset( $main );

			for ($n = 0; $n < count( $main ); $n++)
			{
				$pro = $main[$n];
				if ( $pro['parent'] == $parent )
				{
					//echo "Adding($pro[project_id])<br>";
					$subs[$pro['project_id']] = $pro;
					$this->subs( $pro['project_id'],$pro,$main );
				}
			}
		}

		function reparent( $values )
		{
			$id = $values['project_id'];
			$parent = $values['parent'];
			$old_parent = $values['old_parent'];
			$main = $old_parent ? intval($this->id2item(array('item_id' => $old_parent))) : $id;
			//echo "<p>reparent: $id/$main: $old_parent --> $parent</p>\n";

			$subs = array();
			$this->subs($id,$subs,$main);
         //echo "<p>subs($id) = "; _debug_array($subs);

			if (isset($subs[$parent]))
			{
				//echo "<p>new parent $parent is sub of $id</p>\n";
				$parent = $subs[$parent];
				$parent['old_parent'] = $parent['parent'];
				$parent['parent'] = intval($values['old_parent']);
				$this->reparent($parent);

				unset($parent['old_parent']);
				unset($parent['main']);

				$this->edit_project($parent);
				$this->reparent($values);
				return;
			}

			$new_main = $parent ? $this->id2item(array('item_id' => $parent)) : $id;
			$new_parent_level = $parent ? $this->id2item(array('item_id' => $parent,'item' => 'level')) : -1;
			$old_parent_level = $old_parent ? $this->id2item(array('item_id' => $old_parent,'item' => 'level')) : -1;
			$level_adj = $old_parent_level - $new_parent_level;
			reset($subs);
         //echo "new_main=$new_main,level_adj = $level_adj<br>";
			while (list($n) = each($subs))
			{
				$subs[$n]['main'] = $new_main;
				$subs[$n]['level'] -= $level_adj;
				//echo "<p>$n: id=".$subs[$n]['project_id']." set main to $new_main, subs[$n] = \n"; _debug_array($subs[$n]);
				$this->edit_project($subs[$n]);
			}
		}

		function edit_project($values)
		{
			$values['project_id'] = intval($values['project_id']);

			if (is_array($values['book_activities']))
			{
				$this->db2->query('delete from phpgw_p_projectactivities where project_id=' . $values['project_id']
								. " and billable='N'",__LINE__,__FILE__);

				while($activ=each($values['book_activities']))
				{
					$this->db->query('insert into phpgw_p_projectactivities (project_id, activity_id, billable) values (' . $values['project_id']
									. ',' . $activ[1] . ",'N')",__LINE__,__FILE__);
				}
			}

			if (is_array($values['bill_activities']))
			{
				$this->db2->query('delete from phpgw_p_projectactivities where project_id=' . $values['project_id']
								. " and billable='Y'",__LINE__,__FILE__);

				while($activ=each($values['bill_activities']))
				{
					$this->db->query('insert into phpgw_p_projectactivities (project_id, activity_id, billable) values (' . $values['project_id']
									. ',' . $activ[1] . ",'Y')",__LINE__,__FILE__);
				}
			}

			$values['descr']						= $this->db->db_addslashes( $values['descr'] );
			$values['title']						= $this->db->db_addslashes( $values['title'] );
			$values['number']						= $this->db->db_addslashes( $values['number'] );
			$values['investment_nr']				= $this->db->db_addslashes( $values['investment_nr'] );
			$values['customer_nr']					= $this->db->db_addslashes( $values['customer_nr'] );
			$values['result']						= $this->db->db_addslashes( $values['result'] );
			$values['test']							= $this->db->db_addslashes( $values['test'] );
			$values['quality']						= $this->db->db_addslashes( $values['quality'] );
			$values['url']							= $this->db->db_addslashes( $values['url'] );
			$values['reference']					= $this->db->db_addslashes( $values['reference'] );
			$values['inv_method']					= $this->db->db_addslashes( $values['inv_method'] );
			$values['parent']						= intval( $values['parent'] );
			$values['edate']						= intval( $values['edate'] );

			$values['budget']						= $values['budget'] + 0.0;
			$values['budget_childs']				= $values['budget_childs'] + 0.0;
			$values['e_budget']						= $values['e_budget'] + 0.0;
			$values['e_budget_childs']  			= $values['e_budget_childs'] + 0.0;
			$values['discount']						= $values['discount'] + 0.0;
			$values['project_accounting_factor']	= $values['project_accounting_factor'] + 0.0;
			$values['project_accounting_factor_d']	= $values['project_accounting_factor_d'] + 0.0;

			if( is_string( $values['billable'] ) )
			{
				if( $values['billable'] == 'N' )
				{
					$values['billable'] = false;
				}
				else
				{
					$values['billable'] = true;
				}
			}

			if( !is_string( $values['acc_type'] ) )
			{
				$values['acc_type'] = 'T';
			}

			if ( isset( $values['old_parent'] ) && $values['old_parent'] != $values['parent'] )
			{
				$this->reparent($values);
			}

			if ( !isset($values['main']) || !isset( $values['level'] ) )
			{
				if ( $values['parent'] > 0 )
				{
					$values['main']		= intval( $this->id2item( array( 'item_id' => $values['parent'],'item' => 'main' ) ) );
					$values['level']	= intval( $this->id2item( array( 'item_id' => $values['parent'],'item' => 'level' ) ) +1 );
				}
				else
				{
					$values['main'] = $values['project_id'];
				}
			}

			$this->db->query("UPDATE phpgw_p_projects set access='" . (isset($values['access'])?$values['access']:'public') . "', category=" . intval($values['cat']) . ", entry_date="
							. time() . ", start_date=" . intval($values['sdate']) . ", end_date=" . $values['edate'] . ", coordinator="
							. intval($values['coordinator']) . ", salesmanager=".intval($values['salesmanager']).", customer=" . intval($values['customer']) . ", status='" . $values['status'] . "', descr='"
							. $values['descr'] . "', title='" . $values['title'] . "', budget=" . $values['budget'] . ", budget_childs=" . $values['budget_childs'] . ", p_number='"
							. $values['number'] . "', time_planned=" . intval($values['ptime']) . ", time_planned_childs=" . intval($values['ptime_childs']) . ', processor=' . $this->account . ", investment_nr='"
							. $values['investment_nr'] . "', inv_method='" . $values['inv_method'] . "', parent=" . $values['parent'] . ', main=' . intval($values['main'])
							. ', level=' . intval($values['level']) . ', previous=' . intval($values['previous']) . ", customer_nr='" . $values['customer_nr']
							. "', url='" . $values['url'] . "', reference='" . $values['reference'] . "', result='" . $values['result'] . "', test='"
							. $values['test'] . "', quality='" . $values['quality'] . "', accounting='" . $values['accounting'] . "', acc_type='".$values['acc_type']."', acc_factor="
							. $values['project_accounting_factor'] . ', acc_factor_d=' . $values['project_accounting_factor_d'] . ",billable='" . ($values['billable']?'Y':'N')
							. "', discount_type='" . $values['discount_type'] . "',psdate=" . intval($values['psdate']) . ', pedate=' . intval($values['pedate']) . ', priority='
							. intval($values['priority']) . ", e_budget=" . $values['e_budget'] . ", e_budget_childs=" . $values['e_budget_childs'] . ", discount=" . $values['discount'] .", plan_bottom_up='" . $values['plan_bottom_up']
							. "', customer_org=".intval($values['customer_org']). ", direct_work='" . $values['direct_work'] . "' where project_id=" . $values['project_id'],__LINE__,__FILE__);

			if ( $values['status'] == 'archive' )
			{
				$this->db->query("Update phpgw_p_projects set status='archive' WHERE parent=" . $values['project_id'],__LINE__,__FILE__);
			}

			if( $values['oldstatus'] && $values['oldstatus'] == 'archive' && $values['status'] != 'archive' )
			{
				$this->db->query("Update phpgw_p_projects set status='" . $values['status'] . "' WHERE parent=" . $values['project_id'],__LINE__,__FILE__);
			}

			$values['old_edate'] = intval( $values['old_edate'] );
			if ( $values['old_edate'] > 0 && $values['edate'] > 0 && $values['old_edate'] != $values['edate'] )
			{
				$this->db->query('SELECT project_id,title,p_number,start_date,end_date from phpgw_p_projects where previous=' . $values['project_id'],__LINE__,__FILE__);

				while( $this->db->next_record() )
				{
					$following[] = array
					(
						'project_id'	=> $this->db->f('project_id'),
						'title'			=> $this->db->f('title'),
						'number'		=> $this->db->f('p_number'),
						'sdate'			=> $this->db->f('start_date'),
						'edate'			=> $this->db->f('end_date')
					);
				};

				//_debug_array($following);

				if ( is_array($following) )
				{
					if ( $this->siteconfig['dateprevious'] == 'yes' )
					{
						$diff = abs( $values['edate']-$values['old_edate'] );

						if ( $values['old_edate'] > $values['edate'] )
						{
							$op = 'sub';
						}
						else
						{
							$op = 'add';
						}
					}
					foreach( $following as $key => $fol )
					{
						if ( $this->siteconfig['dateprevious'] == 'yes' )
						{
							$nsdate = $op=='add' ? $fol['sdate'] + $diff : $fol['sdate'] - $diff;
							$nedate = intval( $fol['edate'] ) > 0 ? ( $op=='add' ? $fol['edate'] + $diff : $fol['edate'] - $diff ) : 0;
							//$npsdate = intval($fol['psdate'])>0?($op=='add'?$fol['psdate']+$diff:$fol['psdate']-$diff):0;
							//$npedate = intval($fol['pedate'])>0?($op=='add'?$fol['pedate']+$diff:$fol['pedate']-$diff):0;

							$this->db->query('UPDATE phpgw_p_projects set start_date=' . intval($nsdate) . ', end_date=' . intval($nedate) . ', entry_date=' . time()
										. ', processor=' . $this->account . ' WHERE project_id=' . $fol['project_id'],__LINE__,__FILE__);

							$following[$key]['nsdate'] = $nsdate;
							$following[$key]['nedate'] = $nedate;
						}
						$this->db->query('SELECT s_id,edate,title from phpgw_p_mstones WHERE project_id=' . intval($fol['project_id']),__LINE__,__FILE__);

						while($this->db->next_record())
						{
							$stones[] = array
							(
								's_id'	=> $this->db->f('s_id'),
								'edate'	=> $this->db->f('edate'),
								'title'	=> $this->db->f('title')
							);
						};
						$following[$key]['mstones'] = $stones;

						if ( $this->siteconfig['dateprevious'] == 'yes' && is_array( $stones ) )
						{
							foreach( $stones as $skey => $stone )
							{
								$snedate = $op=='add' ? $stone['edate'] + $diff : $stone['edate'] - $diff;

								$this->db->query('UPDATE phpgw_p_mstones set edate=' . intval($snedate) . ' WHERE s_id=' . intval($stone['s_id']),__LINE__,__FILE__);
								$stones[$skey]['snedate'] = $snedate;
							}
						}
					}
					return $following;
				}
				return False;
			}
		}

		function return_value( $action,$pro_id,$account_id = 0 )
		{
			$pro_id		= intval($pro_id);
			$account_id	= intval($account_id);

			if ( $action == 'act' )
			{
				$this->db->query('SELECT a_number,descr from phpgw_p_activities where id=' . $pro_id,__LINE__,__FILE__);
				if ($this->db->next_record())
				{
					$bla = $GLOBALS['phpgw']->strip_html($this->db->f('descr')) . ' [' . $GLOBALS['phpgw']->strip_html($this->db->f('a_number')) . ']';
				}
			}
			else if( $action == 'role' )
			{
				$this->db->query('SELECT role_id from phpgw_p_projectmembers where project_id=' . $pro_id . ' and account_id=' . $account_id
								. " and type='role'",__LINE__,__FILE__);
				if( $this->db->next_record() )
				{
					$bla = $this->id2item(array('action' => 'role','item' => 'role_name', 'item_id' => $this->db->f('role_id')));

				}
			}
			else if( $action == 'charge' )
			{
				$this->db->query('SELECT charge_percent from phpgw_p_surcharges where charge_id=' . $pro_id,__LINE__,__FILE__);
				if( $this->db->next_record() )
				{
					$bla = $this->db->f('charge_percent');
				}
			}
			else
			{
				switch ( $action )
				{
					case 'co':
						$column = 'coordinator';
						break;
					case 'main':
						$column = 'main';
						break;
					case 'level':
						$column = 'level';
						break;
					case 'parent':
						$column = 'parent';
						break;
					case 'pro':
						$column = 'p_number,title';
						break;
					case 'edate':
						$column = 'end_date';
						break;
					case 'sdate':
						$column = 'start_date';
						break;
					case 'pedate':
						$column = 'pedate';
						break;
					case 'psdate':
						$column = 'psdate';
						break;
					case 'phours': // fall thru to ptime
					case 'ptime':
						$column = 'time_planned';
						break;
					case 'ptime_childs':
						$column = 'time_planned_childs';
						break;
					case 'invest':
						$column = 'investment_nr';
						break;
					case 'budget':
						$column = 'budget';
						break;
					case 'budget_childs':
						$column = 'budget_childs';
						break;
					case 'e_budget':
						$column = 'e_budget';
						break;
					case 'e_budget_childs':
						$column = 'e_budget_childs';
						break;
					case 'previous':
						$column = 'previous';
						break;
					case 'billable':
						$column = 'billable';
						break;
					case 'plan_bottom_up':
						$column = 'plan_bottom_up';
						break;
					case 'direct_work':
						$column = 'direct_work';
						break;
					case 'title':
						$column = 'title';
						break;
					case 'coordinator':
						$column = 'coordinator';
						break;
					case 'cat':
						$column = 'category';
						break;
				}

				$this->db->query('SELECT ' . $column . ' from phpgw_p_projects where project_id=' . $pro_id,__LINE__,__FILE__);
				if ( $this->db->next_record() )
				{
					switch( $action )
					{
						case 'pro':
							$bla = $GLOBALS['phpgw']->strip_html( $this->db->f('title') ) . ' [' . $GLOBALS['phpgw']->strip_html($this->db->f('p_number')) . ']';
							break;
						case 'phours':
							$bla = $this->db->f('time_planned')/60;
							break;
						default:
							$bla = $GLOBALS['phpgw']->strip_html( $this->db->f($column) );
					}
				}
			}
			return $bla;
		}

		function exists( $params )
		{
			$project_id	= intval($params['project_id']);
			$column_val	= $params['column_val']?$params['column_val']:$project_id;
			$check		= $params['check']?$params['check']:'project_id';

			switch($check)
			{
				case 'number':
					$column = 'p_number';
					$equal  = '=';
					if ( $project_id > 0 )
					{
						$editexists = ' and project_id !=' . $project_id;
					}
					break;
				case 'main_project_number':
					$column     = 'p_number';
					$equal      = ' LIKE ';
					$editexists = ' and parent = 0 and main != ' . $project_id;
					break;
				case 'parent':
					$column = 'parent';
					$equal  = '=';
					break;
				default:
					$column = 'project_id';
					$equal  = '=';
			}
			$this->db->query('SELECT count(*) from phpgw_p_projects where ' . $column . '=' . $column_val . $editexists,__LINE__,__FILE__);
			$this->db->next_record();

			if ( $this->db->f(0) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}


// returns project-,invoice- and delivery-ID

		function add_leading_zero( $num )
		{
/*			if ($id_type == "hex")
			{
				$num = hexdec($num);
				$num++;
				$num = dechex($num);
			}
			else
			{
				$num++;
			} */

			$num++;

			if ( strlen($num) == 4 )
				$return = $num;
			if ( strlen($num) == 3 )
				$return = "0$num";
			if ( strlen($num) == 2 )
				$return = "00$num";
			if ( strlen($num) == 1 )
				$return = "000$num";
			if ( strlen($num) == 0 )
				$return = "0001";

			return strtoupper( $return );
		}

		function create_projectid()
		{
			$prefix = 'P-' . $this->year . '-';

			$this->db->query("select max(p_number) from phpgw_p_projects where p_number like ('$prefix%') and parent=0");
			$this->db->next_record();
			$max = $this->add_leading_zero( substr( $this->db->f(0), -4 ) );

			return $prefix . $max;
		}

		function create_jobid($pro_parent)
		{
			/*$parent_level = $this->id2item(array('project_id' => $pro_parent, 'item' => 'level'));
			switch($parent_level)
			{
				case 0:		$add = ' / '; break;
				default:	$add = ''; break;
			}*/

			$this->db->query('select p_number from phpgw_p_projects where project_id=' . $pro_parent);
			$this->db->next_record();
			$prefix = $this->db->f('p_number') . '/';

			$this->db->query("select max(p_number) from phpgw_p_projects where p_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero( substr( $this->db->f(0), -4 ) );

			return $prefix . $max;
		}

		function create_activityid()
		{
			$prefix = 'A-' . $this->year . '-';

			$this->db->query("select max(a_number) from phpgw_p_activities where a_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero( substr( $this->db->f(0), -4 ) );

			return $prefix . $max;
		}

		function create_deliveryid()
		{
			$prefix = 'D-' . $this->year . '-';
			$this->db->query("select max(d_number) from phpgw_p_delivery where d_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero( substr( $this->db->f(0), -4 ) );

			return $prefix . $max;
		}

		function create_invoiceid()
		{
			$prefix = 'I-' . $this->year . '-';
			$this->db->query("select max(i_number) from phpgw_p_invoice where i_number like ('$prefix%')");
			$this->db->next_record();
			$max = $this->add_leading_zero( substr( $this->db->f(0), -4 ) );

			return $prefix . $max;
		}

		function delete_project( $project_id, $subs = false )
		{
			$project_id = intval( $project_id );

			if($subs)
			{
				$subpro = $this->read_projects(array('column' => 'project_id,level','limit' => false,'action' => 'subs','parent' => $project_id));

				if( is_array( $subpro ) )
				{
					$i = 0;
					foreach( $subpro as $sub )
					{
						$s[$i] = $sub['project_id'];
						++$i;
					}
				}

				$id_list = '';
				if( is_array($s) )
				{
					$sub_acl_delete = ' OR acl_location in(' . implode(',',$s) . ')';
					$id_list = ','.implode(',',$s);
				}
			}

			$this->db->query("DELETE from phpgw_acl where acl_appname='project_members' and acl_rights=7 and (acl_location=" . $project_id . $sub_acl_delete
							. ')',__LINE__,__FILE__);

			if( $subs )
			{
				$this->db->query('DELETE from phpgw_p_projects where project_id in('.$project_id.$id_list.')',__LINE__,__FILE__);
				$this->db->query('DELETE from phpgw_p_hours where project_id in('.$project_id.$id_list.')',__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('DELETE from phpgw_p_projects where project_id=' . $project_id,__LINE__,__FILE__);
				$this->db->query('DELETE from phpgw_p_hours where project_id=' . $project_id,__LINE__,__FILE__);
			}

			$this->db->query('select id from phpgw_p_delivery where project_id=' . $project_id,__LINE__,__FILE__);
			while ( $this->db->next_record() )
			{
				$del[] = array
				(
					'id'	=> $this->db->f('id')
				);
			}

			if ( is_array( $del ) )
			{
				for ( $i=0; $i <= count( $del ); $i++ )
				{
					$this->db->query('Delete from phpgw_p_deliverypos where delivery_id=' . intval($del[$i]['id']),__LINE__,__FILE__);
				}
				$this->db->query('DELETE from phpgw_p_delivery where project_id=' . $project_id,__LINE__,__FILE__);
			}

			$this->db->query('select id from phpgw_p_invoice where project_id=' . $project_id,__LINE__,__FILE__);

			while ( $this->db->next_record() )
			{
				$inv[] = array
				(
					'id'	=> $this->db->f('id')
				);
			}

			if ( is_array( $inv ) )
			{
				for ( $i=0; $i <= count( $inv ); $i++ )
				{
					$this->db->query('Delete from phpgw_p_invoicepos where invoice_id=' . intval($inv[$i]['id']),__LINE__,__FILE__);
				}
				$this->db->query('DELETE from phpgw_p_invoice where project_id=' . $project_id,__LINE__,__FILE__);
			}
		}

		function delete_account_project_data( $account_id )
		{
			$account_id = intval( $account_id );
			if ( $account_id > 0 )
			{
				$this->db->query('delete from phpgw_categories where cat_owner=' . $account_id . " AND cat_appname='projects'",__LINE__,__FILE__);
				$this->db->query('delete from phpgw_p_hours where employee=' . $account_id,__LINE__,__FILE__);
				$this->db->query('select project_id from phpgw_p_projects where coordinator=' . $account_id,__LINE__,__FILE__);

				while ( $this->db->next_record() )
				{
					$drop_list[] = $this->db->f('project_id');
				}

				if ( is_array( $drop_list ) )
				{
					reset( $drop_list );
//					_debug_array($drop_list);
//					exit;

					$subdelete = ' OR parent in (' . implode(',',$drop_list) . ')';

					$this->db->query('DELETE from phpgw_p_projects where project_id in (' . implode(',',$drop_list) . ')'
									. $subdelete,__LINE__,__FILE__);

					$this->db->query('select id from phpgw_p_delivery where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);

					while ( $this->db->next_record() )
					{
						$del[] = array
						(
							'id'	=> $this->db->f('id')
						);
					}

					if ( is_array($del) )
					{
						for ( $i=0; $i <= count( $del ); $i++ )
						{
							$this->db->query('Delete from phpgw_p_deliverypos where delivery_id=' . intval($del[$i]['id']),__LINE__,__FILE__);
						}

						$this->db->query('DELETE from phpgw_p_delivery where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);
					}


					$this->db->query('select id from phpgw_p_invoice where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);

					while ( $this->db->next_record() )
					{
						$inv[] = array
						(
							'id'	=> $this->db->f('id')
						);
					}

					if ( is_array( $inv ) )
					{
						for ( $i=0; $i <= count( $inv ); $i++ )
						{
							$this->db->query('Delete from phpgw_p_invoicepos where invoice_id=' . intval($inv[$i]['id']),__LINE__,__FILE__);
						}

						$this->db->query('DELETE from phpgw_p_invoice where project_id in (' . implode(',',$drop_list) . ')',__LINE__,__FILE__);
					}
				}
			}
		}

		function change_owner( $old, $new )
		{
			$old = intval( $old );
			$new = intval( $new );

			$this->db->query('UPDATE phpgw_p_projects set coordinator=' . $new . ' where coordinator=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_hours set employee=' . $new . ' where employee=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_projectmembers set account_id=' . $new . ' where (account_id=' . $old . " AND type='aa')",__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_invoice set owner=' . $new . ' where owner=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_p_delivery set owner=' . $new . ' where owner=' . $old,__LINE__,__FILE__);
			$this->db->query('UPDATE phpgw_categories set cat_owner=' . $new . ' where cat_owner=' . $old . " AND cat_appname='projects'",__LINE__,__FILE__);
		}


// -------- SUM BUDGET ---------------

		function sum_budget( $values )
		{
			$action		= $values['action'] ? $values['action'] : 'mains';
			$bcolumn	= $values['bcolumn'] ? $values['bcolumn'] : 'budget';
			$project_id	= intval( $values['project_id'] );

			$values['column'] = 'project_id,level';

			$projects = $this->read_projects($values);

			//_debug_array($projects);

			$pro = array();
			for( $i=0; $i < count( $projects ); $i++ )
			{
				$pro[$i] = $projects[$i]['project_id'];
			}

			if( count( $pro ) == 0 )
			{
				$pro[0] = 0;
			}

			$sql = 'SELECT SUM(' . $bcolumn . ') as sumvalue from phpgw_p_projects where project_id in(' . implode(',',$pro) . ')';
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				return $this->db->f('sumvalue');
			}
		}

		function get_planned_value( $option )
		{
			$action		= isset( $option['action']) ? $option['action'] : 'main';
			$project_id	= isset( $option['project_id']) ? $option['project_id'] : 0;
			$parent_id	= isset( $option['parent_id'] ) ? $option['parent_id'] : 0;
			$project_id	= intval( $project_id );
			$parent_id	= intval( $parent_id );

			switch( $action )
			{
				case 'tmain':
				case 'bmain':
					$filter = 'main=' . $parent_id . ' and project_id !=' . $parent_id;
					break;
				case 'tparent':
				case 'ebparent':
				case 'bparent':
					$filter = 'parent=' . $parent_id;
					break;
			}

			switch( $action )
			{
				case 'bmain':
				case 'bparent':
					$column = 'budget';
					break;
				case 'ebparent': $column = 'e_budget';
					break;
				case 'tmain':
				case 'tparent':	$column = 'time_planned';
					break;
			}

			if( $project_id > 0 )
			{
				$editfilter = ' and project_id !=' . $project_id;
			}

			$this->db->query( 'SELECT SUM(' . $column . ') as sumvalue from phpgw_p_projects where (' . $filter . $editfilter . ')',__LINE__,__FILE__ );
			if ( $this->db->next_record() )
			{
				return $this->db->f('sumvalue');
			}
		}

		function item2id( $data = 0 )
		{
			$item_id	= isset( $data['item_id'] ) ? $data['item_id'] : 'event_id';
			$item		= $data['item'];
			$action		= isset( $data['action'] ) ? $data['action'] : 'event';

			switch( $action )
			{
				case 'event':
					$table = 'phpgw_p_events';
					$column = 'event_name';
					break;
			}

			$this->db->query( "SELECT $item_id FROM $table WHERE $column='" . $item . "'",__LINE__,__FILE__ );
			$this->db->next_record();

			if ( $this->db->f($item_id) )
			{
				return $this->db->f(0);
			}
		}

		function id2item( $data )
		{
			if( is_array( $data ) )
			{
				$item_id	= intval( $data['item_id'] );
				$item		= isset( $data['item'] ) ? $data['item'] : 'main';
				$action		= isset( $data['action'] ) ? $data['action'] : 'pro';
			}

			switch( $action )
			{
				case 'role':
					$table = 'phpgw_p_roles';
					$column = 'role_id';
					break;
				case 'event':
					$table = 'phpgw_p_events';
					$column = 'event_id';
					break;
				default:
					$table = 'phpgw_p_projects';
					$column = 'project_id';
					break;
			}

			$this->db->query( "SELECT $item FROM $table WHERE $column=" . $item_id,__LINE__,__FILE__ );
			if ( $this->db->next_record() )
			{
				return $this->db->f(0);
			}
		}

		function get_mstones( $project_id = '' )
		{
			$this->db->query( "SELECT * FROM phpgw_p_mstones WHERE project_id=" . intval($project_id),__LINE__,__FILE__ );

			while( $this->db->next_record() )
			{
				$stones[] = array
				(
					's_id'	=> $this->db->f('s_id'),
					'title'	=> $this->db->f('title'),
					'edate'	=> $this->db->f('edate')
				);
			}
			return $stones;
		}

		function get_single_mstone( $s_id = '' )
		{
			$this->db->query( "SELECT * FROM phpgw_p_mstones WHERE s_id=" . intval($s_id),__LINE__,__FILE__ );

			if( $this->db->next_record() )
			{
				$stone = array
				(
					's_id'	=> $this->db->f('s_id'),
					'title'	=> $this->db->f('title'),
					'edate'	=> $this->db->f('edate')
				);
			}
			return $stone;
		}

		function add_mstone( $values )
		{
			$this->db->query('INSERT into phpgw_p_mstones (project_id,title,edate) VALUES (' . intval($values['project_id']) . ",'"
							. $this->db->db_addslashes($values['title']) . "'," . intval($values['edate']) . ')',__LINE__,__FILE__);
			return $this->db->get_last_insert_id('phpgw_p_mstones','s_id');
		}

		function edit_mstone( $values )
		{
			$this->db->query('UPDATE phpgw_p_mstones set edate=' . intval($values['edate']) . ", title='" . $this->db->db_addslashes($values['title']) . "' "
							. 'WHERE s_id=' . intval($values['s_id']),__LINE__,__FILE__);
		}

		function delete_mstone( $s_id = '' )
		{
			$this->db->query('DELETE from phpgw_p_mstones where s_id=' . intval($s_id),__LINE__,__FILE__);
		}

		function delete_acl( $project_id )
		{
			$this->db->query("DELETE from phpgw_acl where acl_appname='project_members' AND acl_location=" . $project_id
							. ' AND acl_rights=7',__LINE__,__FILE__);
		}

		function get_acl_projects()
		{
			$this->db->query("SELECT acl_location FROM phpgw_acl, phpgw_p_projects WHERE acl_appname='project_members' AND acl_rights=7 AND acl_account="
								. $this->account . " AND acl_location=project_id ORDER BY title", __LINE__, __FILE__);

			// Did the query return any records?
			if ( $this->db->next_record() )
			{
				while( $this->db->next_record() )
				{
					$projects[] = $this->db->f(0);
				}

				return $projects;
			}
			else // No acl_location in the database
			{
				return false;
			}
		}

		function get_employee_projects($account_id = '')
		{
			$this->account = intval($account_id);
			$coord = $this->read_projects(array('filter' => 'yours','action' => 'all','limit' => False,'column' => 'title,p_number,level,project_id',
												'order' => 'main'));

			$pros = $this->get_acl_projects();
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$space = '&nbsp;.&nbsp;';
			for($i=0;$i<count($pros);$i++)
			{
				$level = $spaceset = '';
				$level = $this->return_value('level',$pros[$i]);
				if ($level > 0)
				{
					$spaceset = str_repeat($space,$level);
				}

				$pro[] = array
				(
					'pro_name'	=> $spaceset . $this->return_value('pro',$pros[$i])
				);
			};

			if(is_array($coord))
			{
				foreach($coord as $co)
				{
					if(!is_array($pros) || (is_array($pros) && !in_array($co['project_id'],$pros)))
					{
						$spaceset = '';
						if ($co['level'] > 0)
						{
							$spaceset = str_repeat($space,$co['level']);
						}
						$pro[] = array
						(
							'pro_name'	=> $spaceset . $co['title'] . ' [' . $co['p_number'] . ']'
						);
					}
				}
			}
			return $pro;
		}

		function member($project_id)
		{
			$this->db->query("SELECT acl_account from phpgw_acl where acl_appname = 'project_members' and acl_rights=7 and acl_location="
								. intval($project_id),__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$members[] = $this->db->f(0);
			}

			if (is_array($members) && in_array($this->account,$members))
			{
				return True;
			}
			return False;
		}

		function read_employee_roles($data)
		{
			$project_id = intval($data['project_id']);
			$column		= isset($data['column'])?$data['column']:'*';
			$account_id	= intval($data['account_id']);
			$event_type	= $data['event_type']?$data['event_type']:'';

			//echo 'SOPROJECTS->read->employee_roles: DATA ';
			//_debug_array($data);

			if($account_id > 0)
			{
				$emp_select = ' and account_id=' . $account_id;
			}

			$this->db->query('SELECT * from phpgw_p_projectmembers where project_id=' . $project_id . " and type='role'" . $emp_select,__LINE__,__FILE__);

			while($this->db->next_record())
			{
				if($column != '*')
				{
					$roles = $this->db->f($column);
				}
				else
				{
					$roles[] = array
					(
						'r_id'			=> $this->db->f('id'),
						'account_id'	=> $this->db->f('account_id'),
						'role_id'		=> $this->db->f('role_id'),
						'events'		=> $this->db->f('events')?explode(',',$this->db->f('events')):array()
					);
				}
			}

			if($event_type && $event_type != '')
			{
				//echo 'event_type: ' . $event_type;

				if(is_string($event_type))
				{
					$event_type = explode(',',$event_type);
				}

				for($i=0;$i<=count($event_type);$i++)
				{
					$event_id = $this->item2id(array('item' => $event_type[$i]));

					for ($k=0;$k<=count($roles);$k++)
					{
						if(is_array($roles[$k]['events']) && in_array($event_id,$roles[$k]['events']))
						{
							$eroles[] = array
							(
								'r_id'			=> $roles[$k]['r_id'],
								'account_id'	=> $roles[$k]['account_id'],
								'role_id'		=> $roles[$k]['role_id'],
								'events'		=> array($event_id)
							);
						}
					}
				}
				$roles = is_array($eroles)?$eroles:False;
			}

			//echo 'SOPROJECTS->read_employee_roles: ROLES ';
			//_debug_array($roles);
			return $roles;
		}

		function save_employee_role($values,$edit = False)
		{
			if(!$edit)
			{
				$this->db->query('INSERT into phpgw_p_projectmembers (project_id,account_id,type,role_id,events) values(' . intval($values['project_id']) . ','
							. intval($values['account_id']) . ",'role'," . intval($values['role_id']) . ",'"
							. (is_array($values['events'])?implode(',',$values['events']):'') . "')",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query('UPDATE phpgw_p_projectmembers set role_id=' . intval($values['role_id']) . ",events='" . (is_array($values['events'])?implode(',',$values['events']):'')
								. "' where type='role' and project_id=" . intval($values['project_id']) . ' and account_id=' . intval($values['account_id']) . ' and id='
								. intval($values['r_id']),__LINE__,__FILE__);

			}
		}

		function add_alarm($data = 0)
		{
			$project_id = intval($data['project_id']);
			$action		= isset($data['action'])?$data['action']:'hours';
			$extra		= intval($data['extra']);

			$this->db->query('INSERT into phpgw_p_alarm (project_id,alarm_type,alarm_extra,alarm_send) values(' . $project_id . ",'" . $action . "',"
							. $extra . ',1)',__LINE__,__FILE__);

			return $this->db->get_last_insert_id('phpgw_p_alarm','alarm_id');
		}

		function update_alarm($data)
		{
			$alarm_id	= intval($data['alarm_id']);
			$extra		= intval($data['extra']);
			$send		= isset($data['send'])?$data['send']:'1';

			$this->db->query('UPDATE phpgw_p_alarm set alarm_extra=' . $extra . ", alarm_send='" . $send . "' where alarm_id=" . $alarm_id,__LINE__,__FILE__);
		}

		function drop_alarm($project_id = 0,$action = 'edit')
		{
			$this->db->query('DELETE from phpgw_p_alarm where project_id=' . intval($project_id) . " and alarm_type='" . $action . "'",__LINE__,__FILE__);
		}

		function check_alarm($project_id = 0,$action = 'hours')
		{
			$this->db->query('SELECT * from phpgw_p_alarm where project_id=' . intval($project_id) . " and alarm_send='1' and alarm_type='" . $action . "'",__LINE__,__FILE__);

			if($this->db->next_record())
			{
				return True;
			}
			return False;
		}

		function get_alarm($data)
		{
			$project_id = intval($data['project_id']);
			$action		= isset($data['action'])?$data['action']:'hours';

			$this->db->query('SELECT * from phpgw_p_alarm where project_id=' . intval($project_id) . " and alarm_type='" . $action . "'",__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$alarm = array
				(
					'alarm_id'	=> $this->db->f('alarm_id'),
					'extra'		=> $this->db->f('alarm_extra')
				);
				return $alarm;
			}
			return False;
		}

		function check_employee_alarm($data)
		{
			$employee	= intval($data['employee']);
			$type		= isset($data['type'])?$data['type']:'assignment to role';
			$project_id	= intval($data['project_id']);

			$event_id = $this->soprojects->item2id(array('item' => $type));

			$events = $this->read_employee_roles(array('project_id' => $project_id,'employee' => $employee, 'column' => 'events'));

			if(is_string($events) && $events != '')
			{
				$events = explode(',',$events);
			}

			if(is_array($events) && in_array($event_id,$events))
			{
				return $event_id;
			}
			return False;
		}

		function plan_bottom_up_set_job_setting($main_project_id, $plan_bottom_up)
		{
			$this->db->query("UPDATE phpgw_p_projects SET plan_bottom_up='" . $plan_bottom_up . "' WHERE main=" . $main_project_id, __LINE__,__FILE__);
		}

		function direct_work_set_job_setting($main_project_id, $direct_work)
		{
			$this->db->query("UPDATE phpgw_p_projects SET direct_work='" . $direct_work . "' WHERE main=" . $main_project_id, __LINE__,__FILE__);
		}

		function get_projects_tree($mainProject = null, $fields = array('project_id', 'parent', 'title', 'p_number', 'direct_work', 'end_date', 'customer_org', 'status'), $employee=-1)
		{
			if($employee == -1)
			{
				$employee = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			$projectstree = array();
			//if($this->soconfig->isprojectadmin('pad') || $this->soconfig->isprojectadmin('pmanager'))
			//{
			//	$this->db->query('SELECT '.implode($fields, ',').' FROM phpgw_p_projects '.
			//	                 'WHERE status = "active"');
			//}
			//elseif($this->member)
			if($this->member)
			{
/*
				$this->db->query('SELECT '.implode($fields, ',').' FROM phpgw_p_projects '.
				                 'WHERE (status = "active" OR status = "nonactive") AND ('.
				                 	'coordinator = '.$employee.' OR '.
				                 	'(access != "private" AND project_id IN(' . implode(',',$this->member) . ')))');
*/
				$this->db->query('SELECT * FROM phpgw_p_projects '.
				                 'WHERE ('.
				                 	'coordinator = '.$employee. " OR "
				                 	."(access != 'private' AND project_id IN(" . implode(',',$this->member) . ')))');
			}
			else
			{
				return $projectstree;
			}

			$i = 0;
			while($this->db->next_record())
			{
				$projects[$i]['id']            = $this->db->f('project_id');
				$projects[$i]['parent']        = $this->db->f('parent');
				$projects[$i]['title']         = $this->db->f('title');
				$projects[$i]['direct']        = $this->db->f('direct_work');
				$projects[$i]['pnumber']       = $this->db->f('p_number');
				$projects[$i]['enddate']       = $this->db->f('end_date');
				$projects[$i]['customer_org']  = $this->db->f('customer_org');
				$projects[$i]['status']        = $this->db->f('status');

				if($this->db->f('acc_factor') > 0)
				{
					$projects[$i]['budget_factor'] = $this->db->f('acc_factor');
				}
				else
				{
					$projects[$i]['budget_factor'] = $this->db->f('acc_factor_d') / 8;
				}
				if($this->db->f('time_planned') > 0)
				{
					$projects[$i]['budget'] = $this->db->f('time_planned') * $projects[$i]['budget_factor'] / 60;
				}
				else
				{
					$projects[$i]['budget'] = $this->db->f('budget');
				}
				++$i;
			}

			$this->tmp = $projects;
			for($i = 0; $i < count($projects); ++$i)
			{
				//$presort = $projects[$i]['direct'] == 'Y' ? '1' : '2';
				//echo $presort.'.'.$this->get_tree_index($projects[$i]['id'], $projects[$i]['parent'])."<br>";
				$index = $this->get_tree_index($projects[$i]['id'], $projects[$i]['parent']);
				$indexparts = explode('.', $index);

				if(!$mainProject || array_search((int) $mainProject, $indexparts) !== FALSE)
				{
					//$projectstree[$presort.'.'.$index] = $projects[$i];
					$projectstree['0.'.$index] = $projects[$i];
				}
			}
			ksort($projectstree);
			return $projectstree;
		}

		function get_tree_index($id, $parent)
		{
			if($parent)
			{
				for($i = 0; $i < count($this->tmp); ++$i)
				{
					if($this->tmp[$i]['id'] == $parent)
					{
						$parentparent = $this->tmp[$i]['parent'];
						$i = count($this->tmp);
					}
				}
				$id = $this->get_tree_index($parent, $parentparent).'.'.$id;
			}
			return $id;
		}

		/**
		* Get cost unit numbers for active projects in given month
		*
		* @param integer $month Month for which to get the projects
		* @param integer $year Year for which to get the projects
		* @return array List of active projects
		*/
		function get_active_projects($month,$year)
		{
			$this->db->query('select distinct p_number from phpgw_p_projects,phpgw_p_hours where phpgw_p_projects.project_id = phpgw_p_hours.pro_main and phpgw_p_hours.start_date >= ' . mktime(0,0,0,$month,1,$year) . ' and phpgw_p_hours.start_date <= ' . mktime(23,59,59,$month,cal_days_in_month(CAL_GREGORIAN,$month,$year),$year) . ' order by p_number asc', __LINE__,__FILE__);
			$result = array();
			while ($this->db->next_record())
			{
				$result[] = $this->db->f('p_number');
			}
			return($result);
		}

		/**
		* Get hours for a given project in a specified month
		*
		* @param integer $month Month for which to get the project hours
		* @param integer $year Year for which to get the project hours
		* @param integer $location_id primary key of location for which to get the cost accounting
		* @return array Project hours for different "credit cost centres"
		* divided inot project hours and travel hours. $result[$cost_centre]['project'];
		* $result[$cost_centre]['travel']
		*/
		function get_project_hours($month,$year,$location_id)
		{
			$sql = "select p_number,employee,cost_centre,sum(minutes),sum(t_journey) from phpgw_p_projects join phpgw_p_hours on phpgw_p_projects.project_id = phpgw_p_hours.pro_main join (select distinct account_id,cost_centre from phpgw_p_projectmembers where type='accounting' and location_id = " . $location_id . " and ((sdate = 0) or (sdate <= " . mktime(0,0,0,$month,1,$year) . ")) and ((edate = 0) or (edate >= " . mktime(23,59,59,$month,cal_days_in_month(CAL_GREGORIAN,$month,$year),$year) . "))) as pmembers on phpgw_p_hours.employee = pmembers.account_id where phpgw_p_hours.start_date >= " . mktime(0,0,0,$month,1,$year) . " and  phpgw_p_hours.start_date <= " . mktime(23,59,59,$month,cal_days_in_month(CAL_GREGORIAN,$month,$year),$year) . " group by employee,cost_centre,p_number order by p_number,employee,cost_centre";
			$this->db->query($sql, __LINE__,__FILE__);
			$result = array();
			$i = 0;
			while ($this->db->next_record())
			{
				$result[$i]['p_number'] = $this->db->f('p_number');
				$GLOBALS['phpgw']->accounts->get_account_name($this->db->f('employee'),$lid,$fname,$lname);
				$result[$i]['employee'] = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);
				$result[$i]['cost_centre'] = $this->db->f('cost_centre');
				$result[$i]['minutes'] = $this->db->f('sum(minutes)') / 60;
				$result[$i]['journey'] = $this->db->f('sum(t_journey)') / 60;
				++$i;
			}
			return($result);
		}

		function get_acl_project_members($project_id = false)
		{
			$sql  = 'SELECT * FROM phpgw_acl ';
			$sql .= 'WHERE acl_appname LIKE \'project_members\' ';

			if($project_id)
			{ // project members of a project
				$sql .= 'AND acl_location = '.intval($project_id).' ';
			}

			$sql .= 'ORDER BY acl_account';

			$this->db->query($sql ,__LINE__,__FILE__);

			if ($this->db->num_rows() == 0)
			{
				return false;
			}

			$members = array();
			while ($this->db->next_record())
			{
				$account_id = $this->db->f('acl_account');
				$members[$account_id][] = $this->db->f('acl_location');
			}

			return $members;
		}

	}
?>

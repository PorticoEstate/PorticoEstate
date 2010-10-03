<?php
	/**************************************************************************
	* phpGroupWare - flows
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/


	/**
	* soflows business object class
	*
	* @package flows
	*/
	class soflows
	{
		var $db;
	
		/**
		* soflow class initialization method
	 	*
	 	* @access  public
	 	* @return  nothing
	 	*/
		function soflows()
		{
			$this->db=clone($GLOBALS['phpgw']->db);
		}
		
		/**
		 * STANDARD INFORMATION METHODS
		 */

		/**
		* Returns the app linked with the flow the given transition belongs to
	 	*
	 	* @access  public
	 	* @param   string   $transition    transition
	 	* @return  string                  app name
	 	*/
		function get_transition_app($transition)
		{
			$sql="SELECT flow FROM phpgw_flows_transitions WHERE transition='".$transition."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			if ($this->db->next_record())
			{
				$flow=$this->db->f('flow');
				
				$sql2="SELECT app from phpgw_flows WHERE flow=".$flow;
				
				$this->db->query($sql2, __LINE__, __FILE__);
				
				if ($this->db->next_record())
				{
					$app=$this->db->f('app');
				}
				else
				{
					$app='';
				}
			}
			else
			{
				$app='';
			}
			return ($app);
		}

		/**
		* Returns statuses linked with a given application
	 	*
	 	* @access  public
	 	* @param   string   $app           application
	 	* @return  array                   list of possible statuses
	 	*/
		function get_app_statuses ( $app )
		{
			$sql="SELECT status_id FROM phpgw_flows_statuses WHERE app='".$app."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			$app_statuses=Array();
			
			while ($this->db->next_record())
			{
				$app_statuses[]=$this->db->f('status_id');
			}

			return ($app_statuses);
		}

		/**
		* Returns true if user has the required role for triggering the given transition
	 	*
	 	* @access  public
	 	* @param   string   $transition    transition
	 	* @param   integer  $account       account_id (current uid by default)
	 	* @return  bool                    result
	 	*/
		function check_if_user_has_required_role($transition,$object,$account_id=null)
		{
			if (is_null($account_id))
			{
				$the_account_id=$GLOBALS['phpgw_info']['user']['account_id'];
			}
			else
			{
				$the_account_id=$account_id;
			}
			
			$the_groups=$GLOBALS['phpgw']->acl->get_location_list_for_id('phpgw_group', 1, $the_account_id);
			
			$or="";
			$sql_role_base="( ";
			foreach ( $the_groups as $group )
			{
				$sql_role_base.=$or."phpgw_flows_roles.account_id=".intval($group)." ";
				$or="OR ";
			}
			$sql_role_base.=$or."phpgw_flows_roles.account_id=".$the_account_id." ";
			$sql_role_base.=" ) ";
			
			// looping through object fields 
			// to define progressive context arrays
			// and serialize them for role sql request part
			
			$role_contexts=array();
			$temp_array=array();
			foreach ( $object as $field => $content )
			{
				if ( $field != 'app')
				{
					$temp_array[$field]=$content;
					$role_contexts[]=serialize($temp_array);
				}
			}
			
			$or="";
			$sql_context_base="( ";
			foreach ( $role_contexts as $role_context)
			{
				$sql_context_base.=$or."phpgw_flows_roles.context='".$role_context."' ";
				$or="OR ";
			}
			$sql_context_base.=$or."phpgw_flows_roles.context='' ";
			$sql_context_base.=$or."phpgw_flows_roles.context IS NULL ";
			$sql_context_base.=" ) ";
						
			$sql="SELECT * FROM phpgw_flows_roles WHERE phpgw_flows_roles.transition=".$transition." ";
			$sql.="AND ".$sql_role_base." ";
			$sql.="AND ".$sql_context_base;
			
			// DEBUG
			//print ($sql."<br/>");
			
			$this->db->query($sql, __LINE__, __FILE__);
	
			if ($this->db->next_record())
			{
				$check=true;
			}
			else
			{
				$check=false;
			}
			
			return($check);
		}

		/**
		* Returns true if conditions for triggering transition on given object are filled
	 	*
	 	* @access  public
	 	* @param   string   $transition    transition
	 	* @param   TODO     $context       TODO
	 	* @return  bool                    result
	 	*/
		function check_if_conditions_of_transition_are_filled($transition,$object=null)
		{
			return (true);
		}

		/**
		* Returns available transitions from a given status in a flow
	 	*
	 	* @access  public
	 	* @param   string   $flow          workflow in which we are running
	 	* @param   string   $status        status from which we are starting
	 	* @return  array                   list of possible transitions
	 	*/
		function get_available_transitions($flow,$status)
		{
			$sql="SELECT phpgw_flows_transitions.* FROM  phpgw_flows_transitions WHERE phpgw_flows_transitions.flow=".$flow." ";
			$sql.="AND phpgw_flows_transitions.from_status='".$status."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
	
			$ii=0;
			$transitions=Array();
			
			while ($this->db->next_record())
			{
				$transitions[$ii]['transition']=$this->db->f('transition');
				$transitions[$ii]['action']=$this->db->f('action');
				$ii ++;
			}
	
			$this->db->unlock();
	
			return $transitions;			
		}

		/**
		* Returns true if given transition is in given flow and false otherwise
	 	*
	 	* @access  public
	 	* @param   string   $transition    transition
	 	* @param   integer  $flow          flow
	 	* @return  bool                    result
	 	*/
		function check_if_transition_is_in_flow($transition, $flow)
		{
			$sql="SELECT flow FROM phpgw_flows_transitions WHERE transition='".$transition."' AND flow=".$flow;
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			if ($this->db->next_record())
			{
				$check=true;
			}
			else
			{
				$check=false;
			}
			return($check);
		}
		
		/**
		* Returns from status for given transition
	 	*
	 	* @access  public
	 	* @param   string   $transition    transition
	 	* @return  string                  status
	 	*/
		function get_transition_status_from($transition)
		{
			$sql="SELECT from_status FROM phpgw_flows_transitions WHERE transition='".$transition."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			if ($this->db->next_record())
			{
				$from_status=$this->db->f('from_status');
			}
			else
			{
				$from_status='';
			}
			return ($from_status);
		}

		/**
		* Returns to status for given transition
	 	*
	 	* @access  public
	 	* @param   string   $transition    transition
	 	* @return  string                  status
	 	*/
		function get_next_status($transition)
		{
			$sql="SELECT to_status FROM phpgw_flows_transitions WHERE transition='".$transition."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			if ($this->db->next_record())
			{
				$to_status=$this->db->f('to_status');
			}
			else
			{
				$to_status='';
			}
			return ($to_status);
		}
		
		/**
		* Returns flows linked with a given application
	 	*
	 	* @access  public
	 	* @param   string   $app           application
	 	* @return  array                   list of possible flows
	 	*/
		function get_app_flows ( $app )
		{
			
		}
		
		/**
		* Returns accounts linked with a given (transition, context)
	 	*
	 	* @access  public
	 	* @param   string   $transition_id transition
	 	* @param   TODO     $context       TODO
	 	* @return  array                   list of phpgw accounts
	 	*/
		function get_transition_roles($transition,$object)
		{
			
		}
		
		/**
		* TODO : description 
	 	*
	 	* @access  public
	 	* @param   TODO
	 	* @return  string                   action field (human readable) for transition
	 	*/
		function get_transition_action($transition)
		{
			$sql="SELECT action FROM phpgw_flows_transitions WHERE transition='".$transition."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			if ($this->db->next_record())
			{
				$action=$this->db->f('action');
			}
			else
			{
				$action='';
			}
			return ($action);
		}
		
		/**
		* TODO : description 
	 	*
	 	* @access  public
	 	* @param   TODO
	 	* @return  array                   custom fields for transition
	 	*/
		function get_custom_fields($transition)
		{
			$sql="SELECT field_name, value FROM phpgw_flows_transitions_custom_values WHERE transition='".$transition."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			$custom_fields=Array();
			
			while ($this->db->next_record())
			{
				$custom_fields[$this->db->f('field_name')]=$this->db->f('value');
			}

			return ($custom_fields);
		}
		
		/**
		* TODO : description 
	 	*
	 	* @access  public
	 	* @param   TODO
	 	* @return  string                   client method required to perform status change
	 	*/
		function get_transition_method($transition)
		{
			$sql="SELECT method FROM phpgw_flows_transitions WHERE transition='".$transition."'";
			
			$this->db->query($sql, __LINE__, __FILE__);
			
			if ($this->db->next_record())
			{
				$method=$this->db->f('method');
			}
			else
			{
				$method='set_status';
			}
			return ($method);
		}

		/**
		* TODO : description 
	 	*
	 	* @access  public
	 	* @param   TODO
	 	* @return  array                   list of conditions
	 	*/
		function get_transition_conditions($transition)
		{
			$sql="SELECT condition_id, app, class, method, context FROM phpgw_flows_conditions WHERE transition='".$transition."'";

			$this->db->query($sql, __LINE__, __FILE__);
			
			$conditions=array();
			while ($this->db->next_record())
			{
				$conditions[$this->db->f('condition_id')]['app']=$this->db->f('app');
				$conditions[$this->db->f('condition_id')]['class']=$this->db->f('class');
				$conditions[$this->db->f('condition_id')]['method']=$this->db->f('method');
				$conditions[$this->db->f('condition_id')]['context']=unserialize($this->db->f('context'));				
			}

			return($conditions);
		}
				
		/**
		* TODO : description 
	 	*
	 	* @access  public
	 	* @param   TODO
	 	* @return  array                   list of triggers
	 	*/
		function get_transition_triggers($transition)
		{
			$sql="SELECT trigger_id, app, class, method, context FROM phpgw_flows_triggers WHERE transition='".$transition."'";

			$this->db->query($sql, __LINE__, __FILE__);
			
			$triggers=array();
			while ($this->db->next_record())
			{
				$triggers[$this->db->f('trigger_id')]['app']=$this->db->f('app');
				$triggers[$this->db->f('trigger_id')]['class']=$this->db->f('class');
				$triggers[$this->db->f('trigger_id')]['method']=$this->db->f('method');
				$triggers[$this->db->f('trigger_id')]['context']=unserialize($this->db->f('context'));				
			}

			return($triggers);	
		}
		
		/**
		 * STANDARD TRIGGERS
		 */		
		
		/**
		* Sends an email notification to people involved in next available transitions (called after a transition)
	 	*
	 	* @access  public
	 	* @param   TODO
	 	* @return  TODO                   list of phpgw accounts
	 	*/
		function send_status_notification($flow,$status,$context=null)
		{
			
		}

		function grant_role($transition,$object=null,$account_id=null)
		{
			if ( ! is_null($object))
			{
				if (is_null($account_id))
				{
					$the_account_id=$GLOBALS['phpgw_info']['user']['account_id'];
				}
				else
				{
					$the_account_id=$account_id;
				}
	
				foreach ( $object as $field => $content )
				{
					if ( $field != 'app')
					{
						$temp_array[$field]=$content;
					}
				}
				
				$role_context=serialize($temp_array);
				
				$sql="INSERT INTO phpgw_flows_roles (transition,account_id,context) VALUES ";
				$sql.="('".$transition."',".$the_account_id.",'".$role_context."' )";
				
				$this->db->query($sql, __LINE__, __FILE__);
	
				$result=true;
			}
			else
			{
				$result=false;
			}
			return ($result);
		}
		
		function remove_role($transition,$object=null,$account_id=null)
		{
			if ( ! is_null($object))
			{
				if (is_null($account_id))
				{
					$the_account_id=$GLOBALS['phpgw_info']['user']['account_id'];
				}
				else
				{
					$the_account_id=$account_id;
				}
	
				foreach ( $object as $field => $content )
				{
					if ( $field != 'app')
					{
						$temp_array[$field]=$content;
					}
				}
				
				$role_context=serialize($temp_array);
				
				$sql="DELETE FROM phpgw_flows_roles WHERE ";
				$sql.="transition='".$transition."' AND account_id=".$the_account_id." AND context='".$role_context."'";
				
				$this->db->query($sql, __LINE__, __FILE__);
	
				$result=true;
			}
			else
			{
				$result=false;
			}
			return ($result);
		}

		function remove_all_roles($transition,$object=null)
		{
			if ( ! is_null($object))
			{	
				foreach ( $object as $field => $content )
				{
					if ( $field != 'app')
					{
						$temp_array[$field]=$content;
					}
				}
				
				$role_context=serialize($temp_array);
				
				$sql="DELETE FROM phpgw_flows_roles WHERE ";
				$sql.="transition='".$transition."' AND context='".$role_context."'";
				
				$this->db->query($sql, __LINE__, __FILE__);
	
				$result=true;
			}
			else
			{
				$result=false;
			}
			return ($result);
		}

	}

?>
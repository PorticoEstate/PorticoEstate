<?php
	/**
	* phpGroupWare - ged - Workflow Logic
	*
	* @author Pascal Vilarem <pascal.vilarem@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package ged
	* @category workflow
	* @version $Id$
	*/

	/*
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	/**
	* flow object class
	*
	* @package ged
	* @category workflow
	*/
	class flows
	{
		private $soflows;

		/**
		* flow class initialization method
		*
		* @access  public
		* @return  nothing
		*/
		public function __construct()
		{
			$this->soflows = createObject('ged.soflows', True);
		}


		/**
		* STANDARD INFORMATION METHODS
		*/

		/**
		* Returns available transitions from a given status in a flow
		*
		* @access  public
		* @param   string   $flow          workflow in which we are running
		* @param   string   $status        status from which we are starting
		* @return  array                   list of possible transitions
		*/
		public function get_available_transitions($flow_object,$account_id=null)
		{
			$app=$flow_object['app'];
			$this->app_flow_client=createObject($app.'.flow_client', True);

			// Retrieve flow id for given object
			$flow=$this->app_flow_client->get_flow($flow_object);

			$status=$this->app_flow_client->get_status($flow_object);

			$available_transitions=$this->soflows->get_available_transitions($flow,$status);
			$result_transition=array();

			foreach ($available_transitions as $transition_label => $available_transition)
			{
				$conditions_met=true;

				$conditions=$this->soflows->get_transition_conditions($available_transition['transition']);

				foreach ($conditions as $condition)
				{
					if ($condition['app']==$app )
					{
						$condition_object=&$this->app_flow_client;
					}
					else
					{
						$condition_object=createObject($condition['app'].'.flow_client', True);
					}

					$condition_result=call_user_func(array(&$condition_object, $condition['method']),$flow_object,$condition['context']);

					if ( $condition_result == false)
					{
						$conditions_met=false;
					}
				}

				if ( $conditions_met == true )
				{
					if ( $this->soflows->check_if_user_has_required_role($available_transition['transition'],$flow_object,$account_id))
					{
						$result_transition[$transition_label]=$available_transition;
					}
				}
			}

			return $result_transition;
		}

		/**
		* Returns flows linked with a given application
		*
		* @access  public
		* @param   string   $app           application
		* @return  array                   list of possible flows
		*/
		public function get_app_flows ( $app )
		{
			$app_flows=$this->soflows->get_app_flows ($app);
			return($app_flows);
		}

		/**
		* Returns statuses linked with a given application
		*
		* @access  public
		* @param   string   $app           application
		* @return  array                   list of possible statuses
		*/
		public function get_app_statuses ( $app )
		{
			$app_statuses=$this->soflows->get_app_statuses ($app);
			return($app_statuses);
		}

		/**
		* Returns initial status required for the given transition
		*
		* @access  public
		* @param   string   $transition transition
		* @return  string               initial status for transition
		*/
		public function get_transition_status_from($transition)
		{
			return($this->soflows->get_transition_status_from($transition));
		}


		/**
		* Checks if a given account is entitled to perform
		* a transition on a given object
		*
		* @access  public
		* @param   string   $transition    transition
		* @param   array    $context       set of vars depending on needs
		* @return  array                   list of phpgw accounts
		*/
		public function check_transition_role($transition,$context=null)
		{
			// TODO
		}


		/**
		* STANDARD ACTION METHODS
		*/

		/**
		* Performs a workflow transition on a given object for a given application
		*
		* @access  public
		* @param   string   $transition_id transition
		* @param   array    $context       set of vars depending on needs
		* @return  array                   error data
		*/
		public function do_transition($transition,$object=null,$account_id=null)
		{
			/*
			* Needs to checks conditions,
			* Perform the status changes
			* And launch triggers registered by the application
			*/

			// DEBUG
			//print ( "transition : ". $transition );
			$transition_result=Array('status' => 'processing');

			//Get application linked with the transition
			$app=$this->soflows->get_transition_app($transition);

			if ( $app == '')
			{
				// No app linked == big problem !
				return array
				(
					'status'	=> 'error',
					'error_message'	=> 'no app linked'
				);
			}

			//Create the app flow plugin needed to perform app specific operations
			$this->app_flow_client=createObject($app.'.flow_client', True);

			//Get flow for the object
			$flow=$this->app_flow_client->get_flow($object);

			//Check that requested transition is actually in flow
			$transition_is_in_flow=$this->soflows->check_if_transition_is_in_flow($transition, $flow);

			if ( !$transition_is_in_flow )
			{
				// Transition not in flow => flow changed or ugly bug or illegal user attempt
				return array
				(
					'status'	=> 'error',
					'error_message'	=> 'transition not in flow'
				);
			}

			// Get current object status and initial status required for transition
			$object_status_from=$this->app_flow_client->get_status($object);

			$transition_status_from=$this->soflows->get_transition_status_from($transition);

			// Check that statuses match
			if ( $object_status_from != $transition_status_from )
			{
				// Statuses do not match => someone else just performed a transition or ugly bug or illegal user attempt
				return array
				(
					'status'	=> 'error',
					'error_message'	=> 'status mismatch'
				);
			}

			// DEBUG
			//print ( "<br>\nok available");

			// Check roles
			$user_has_required_role=$this->soflows->check_if_user_has_required_role($transition,$object,$account_id);

			if ( !$user_has_required_role )
			{
				// the user is not entitled to perform the transition => right has just been removed or ugly bug or illegaluser attempt
				return array
				(
					'status'	=> 'error',
					'error_message'	=> 'roles violation'
				);
			}

			// Check conditions
			$conditions_met = true;

			$conditions=$this->soflows->get_transition_conditions($transition);

			foreach ($conditions as $condition)
			{
				if ($condition['app']==$app )
				{
					$condition_object=&$this->app_flow_client;
				}
				else
				{
					$condition_object=createObject($condition['app'].'.flow_client', True);
				}

				// TODO look at alternatives to call_user_func it is very expensive
				$condition_result=call_user_func(array(&$condition_object, $condition['method']),$object,$condition['context']);

				if ( $condition_result == false)
				{
					$conditions_met = false;
				}
			}

			if ( !$conditions_met )
			{
				// Conditions not met => transition is not possible currently.
				return array
				(
					'status'	=> 'error',
					'error_message'	=> 'conditions not filled'
				);
			}

			// get_next_status
			$next_status=$this->soflows->get_next_status($transition);

			if ( !$next_status || $next_status == '' )
			{
				// no next status == bug !
				return array
				(
					'status'	=> 'error',
					'error_message'	=> 'next status unknown'
				);
			}

			//DEBUG
			//print ( "<br>\nnext status: ".$next_status);

			// Time to prepare transition data

			// Check which method of app plugin we'll need to use
			$client_set_next_method=$this->soflows->get_transition_method($transition);
			$action=$this->soflows->get_transition_action($transition);

			// Get custom parameters that could have been defined for this method
			$custom_fields=$this->soflows->get_custom_fields($transition);
			$transition_context=Array('action' => $action, 'custom_fields' =>$custom_fields);

			// Time to actually do the transition
			if ( is_callable(array(&$this->app_flow_client, $client_set_next_method)))
			{
				$method_result=call_user_func(array(&$this->app_flow_client, $client_set_next_method),$object, $next_status,$transition_context);

				if ( $method_result['status'] == 'ok')
				{
					//set_history
					if ( isset($method_result['comment']))
					{
						$comment=$method_result['comment'];
					}
					else
					{
						$comment=$action;
					}

					$do_history=true;
					if ( isset($method_result['mute_history']) && $method_result['mute_history']=='mute')
					{
						$do_history=false;
					}

					if ( $do_history == true )
					{
						$comment_context=Array('comment' => $comment);
						$this->app_flow_client->set_history($object, $action."[".$transition."]", $comment_context);
					}

					// Get triggers that could be declared
					$triggers=$this->soflows->get_transition_triggers($transition);

					// Run said triggers
					foreach ($triggers as $trigger)
					{
						if ($trigger['class']=='flows' )
						{
							$trigger_object=&$this;
						}
						elseif ($trigger['app']==$app )
						{
							$trigger_object=&$this->app_flow_client;
						}
						else
						{
							$trigger_object=createObject($trigger['app'].'.flow_client', True);
						}

						$trigger['context']['account_id']=$account_id;

						$trigger_result=call_user_func(array(&$trigger_object, $trigger['method']),$object,$trigger['context']);

						//TODO : Check triggers results
					}

					$transition_result=$method_result;
				}
				elseif ($method_result['status'] == 'processing' )
				{
					// app plugin transition method needs a dialog with user
					$transition_result=$method_result;
				}
				else
				{
					//Something went wrong
					$transition_result=$method_result;
				}

			}
			elseif (is_callable(array(&$this->app_flow_client, 'set_status' )))
			{
				//ALERT : app transition méthod not found but we could set a default transition method there
				//$flow=$this->app_flow_client->set_status($context['object'], $next_status, $transition_context);
			}
			else
			{
				//ALERT : ugly bug in app plugin : no method found
				$transition_result['status']='error';
				$transition_result['error_message']='unknown flow method';
			}
			// Return the result
			return ($transition_result);
		}


		/**
		* STANDARD TRIGGERS
		*/

		/**
		* Sends an email notification to people involved in next available transitions (called after a transition)
		*
		* @access  public
		* @param   string   $object           object driven by a flow
		* @param   array    $context          TODO
		* @return                             error status
		*/
		public function send_status_notification($object,$status,$context=null)
		{
			// TODO
		}

		/**
		* Grants a specific role on a transition for the given object
		* (Used for lock/unlock transitions)
		*
		* @access  public
		* @param   string   $object           object driven by a flow
		* @param   array    $trigger_context  context data (transition and account_id required )
		* @return                             error status
		*/
		public function grant_role($object,$trigger_context=null)
		{
			$transition=$trigger_context['transition'];
			$account_id=$trigger_context['account_id'];
			return ($this->soflows->grant_role($transition,$object,$account_id));
		}

		/**
		* Removes a specific role on a transition for the given object
		* (Used for lock/unlock transitions)
		*
		* @access  public
		* @param   string   $object           object driven by a flow
		* @param   array    $trigger_context  context data (transition and account_id required )
		* @return                             error status
		*/
		public function remove_role($object,$trigger_context=null)
		{
			$transition=$trigger_context['transition'];
			$account_id=$trigger_context['account_id'];
			return ($this->soflows->remove_role($transition,$object,$account_id));
		}

		/**
		* Removes all roles on a transition for the given object
		* (Used for lock/unlock transitions)
		*
		* @access  public
		* @param   string   $object           object driven by a flow
		* @param   array    $trigger_context  context data (transition and account_id required )
		* @return                             error status
		*/
		public function remove_all_roles($object,$trigger_context=null)
		{
			$transition=$trigger_context['transition'];
			return ($this->soflows->remove_all_roles($transition,$object));
		}

	}

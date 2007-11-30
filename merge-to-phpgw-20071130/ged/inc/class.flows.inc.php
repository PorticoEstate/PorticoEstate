<?php
	/**************************************************************************
	* phpGroupWare - flow
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
	* flow object class
	*
	* @package flows
	*/
	class flows
	{
		var $soflows;
	
		/**
		* flow class initialization method
	 	*
	 	* @access  public
	 	* @return  nothing
	 	*/
		function flows()
		{
			$this->soflows=CreateObject('ged.soflows', True);
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
		function get_available_transitions($flow_object,$account_id=null)
		{
			$app=$flow_object['app'];
			$this->app_flow_client=CreateObject($app.'.flow_client', True);
			
			//récupérer le flow
			$flow=$this->app_flow_client->get_flow($flow_object);
			
			$status=$this->app_flow_client->get_status($flow_object);
			
			$available_transitions=$this->soflows->get_available_transitions($flow,$status);
			$result_transition=array();
						
			foreach ($available_transitions as $transition_label => $available_transition)
			{
				$conditions_of_transition_are_filled=true;
				
				$conditions=$this->soflows->get_transition_conditions($available_transition['transition']);
				
				foreach ($conditions as $condition)
				{
					if ($condition['app']==$app )
					{
						$condition_object=&$this->app_flow_client;
					}
					else
					{
						$condition_object=CreateObject($condition['app'].'.flow_client', True);
					}
																	
					$condition_result=call_user_func(array(&$condition_object, $condition['method']),$flow_object,$condition['context']);
					
					if ( $condition_result == false)
					{
						$conditions_of_transition_are_filled=false;
					}
				}
				
				if ( $conditions_of_transition_are_filled == true )
				{
					if ( $this->soflows->check_if_user_has_required_role($available_transition['transition'],$flow_object,$account_id))
					{
						$result_transition[$transition_label]=$available_transition;
					}
				}
			}
			
			return ($result_transition);
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
		function get_app_statuses ( $app )
		{
			$app_statuses=$this->soflows->get_app_statuses ($app);
			return($app_statuses);
		}
			
		function get_transition_status_from($transition)
		{
			return($this->soflows->get_transition_status_from($transition));
		}


		/**
		* Checks if a given account is entitled to perform
		* a transition on a given object
	 	*
	 	* @access  public
	 	* @param   string   $transition_id transition
	 	* @param   array    $context       set of vars depending on needs
	 	* @return  array                   list of phpgw accounts
	 	*/
		function check_transition_role($transition,$context=null)
		{
			
		}
		

		/**
		 * STANDARD ACTION METHODS
		 */

		/**
		* Performs a workflow transition on a given object of a given application
	 	*
	 	* @access  public
	 	* @param   string   $transition_id transition
	 	* @param   array    $context       set of vars depending on needs
	 	* @return  TOTO                    error message ?
	 	*/
		function do_transition($transition,$object=null,$account_id=null)
		{
			/**
			 * Needs to checks conditions,
			 * Perform the status changes
			 * And launch triggers registered by the application
			 */
		  // DEBUG
			//print ( "hop : ". $transition );
			$transition_result=Array('status' => 'processing');
			
			//récupérer l'application
			$app=$this->soflows->get_transition_app($transition);
			
			if ( $app != '')
			{
				//instancier le plugin de flow
				$this->app_flow_client=CreateObject($app.'.flow_client', True);
				
				//récupérer le flow
				$flow=$this->app_flow_client->get_flow($object);
				
				//contrôler que la transition appratient bien au flow en question
				$transition_is_in_flow=$this->soflows->check_if_transition_is_in_flow($transition, $flow);
				
				if ( $transition_is_in_flow )
				{				
					//récupérer le statut de départ de l'objet
					$object_status_from=$this->app_flow_client->get_status($object);
					
					$transition_status_from=$this->soflows->get_transition_status_from($transition);
					
					//contrôler la transition
					if ($object_status_from == $transition_status_from )
					{
						// DEBUG
						//print ( "<br>\nok available");
						
						//contrôler les rôles
						$user_has_required_role=$this->soflows->check_if_user_has_required_role($transition,$object,$account_id);
						
						if ($user_has_required_role == true)
						{
						
							//contrôler les conditions
							$conditions_of_transition_are_filled=true;
							
							$conditions=$this->soflows->get_transition_conditions($transition);
							
							foreach ($conditions as $condition)
							{
								if ($condition['app']==$app )
								{
									$condition_object=&$this->app_flow_client;
								}
								else
								{
									$condition_object=CreateObject($condition['app'].'.flow_client', True);
								}
																				
								$condition_result=call_user_func(array(&$condition_object, $condition['method']),$object,$condition['context']);
								
								if ( $condition_result == false)
								{
									$conditions_of_transition_are_filled=false;
								}
							}
							
							if ( $conditions_of_transition_are_filled )
							{								
								//effectuer la transition
								
								//get_next_status
								$next_status=$this->soflows->get_next_status($transition);
								
								if ( $next_status != "")
								{
									//DEBUG
									//print ( "<br>\nnext status: ".$next_status);
									
									//vérifier quelle fonction du client utiliser en fonction de la transition
									$client_set_next_method=$this->soflows->get_transition_method($transition);
									
									//set next_status
									$action=$this->soflows->get_transition_action($transition);
									$custom_fields=$this->soflows->get_custom_fields($transition);
									$transition_context=Array('action' => $action, 'custom_fields' =>$custom_fields);
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
											
											//récupérer les triggers
											$triggers=$this->soflows->get_transition_triggers($transition);
										
											//déclencher les triggers
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
													$trigger_object=CreateObject($trigger['app'].'.flow_client', True);
												}
												
												$trigger['context']['account_id']=$account_id;
																								
												$trigger_result=call_user_func(array(&$trigger_object, $trigger['method']),$object,$trigger['context']);
												
												//TODO : contrôler le résultat des triggers
											}
											
											$transition_result=$method_result;
										}
										elseif ($method_result['status'] == 'processing' )
										{
											// la méthode a besoin d'interagir avec l'utilisateur
											$transition_result=$method_result;
										}
										else
										{
											//ça a merdé
											$transition_result=$method_result;
										}
										
									}
									elseif (is_callable(array(&$this->app_flow_client, 'set_status' )))
									{
										//ALERTE : méthode non trouvée mais il existe une méthode par défaut
										//$flow=$this->app_flow_client->set_status($context['object'], $next_status, $transition_context);
									}
									else
									{
										//oula : gros bug dans le client de workflow
										$transition_result['status']='error';
										$transition_result['error_message']='unknown flow method';
									}

								}
								else
								{
									// no next status == bug !
									$transition_result['status']='error';
									$transition_result['error_message']='next status unknown';
								}
							}
							else
							{
								// Conditions non réunies == plutot bug que petit malin
								$transition_result['status']='error';
								$transition_result['error_message']='conditions not filled';
							}
						}
						else
						{
							// the user is not entitled to perform the transition == petit malin ou bug
							$transition_result['status']='error';
							$transition_result['error_message']='roles violation';
						}
					}
					else
					{
						// Les status ne correspondent pas == petit malin ou bug
						$transition_result['status']='error';
						$transition_result['error_message']='status mismatch';
					}
				}
				else
				{
					// transition hors flow == petit malin ou bug
					$transition_result['status']='error';
					$transition_result['error_message']='transition not in flow';
				}
			}
			else
			{
				// Pas d'application associée == gros problème
				$transition_result['status']='error';
				$transition_result['error_message']='no app linked';
			}
			// Return en fonction du résultat
			return ($transition_result);
		}


		/**
		 * STANDARD TRIGGERS
		 */		
		
		/**
		* Sends an email notification to people involved in next available transitions (called after a transition)
	 	*
	 	* @access  public
	 	* @param   string   $flow          workflow in which we are running
	 	* @param   string   $status        current status
	 	* @param   TODO     $context       TODO
	 	* @return  array                   list of phpgw accounts
	 	*/
		function send_status_notification($flow,$status,$context=null)
		{
			
		}
		
		function grant_role($object,$trigger_context=null)
		{
			$transition=$trigger_context['transition'];
			$account_id=$trigger_context['account_id'];			
			return ($this->soflows->grant_role($transition,$object,$account_id));
		}

		function remove_role($object,$trigger_context=null)
		{
			$transition=$trigger_context['transition'];
			$account_id=$trigger_context['account_id'];			
			return ($this->soflows->remove_role($transition,$object,$account_id));
		}
		
		function remove_all_roles($object,$trigger_context=null)
		{
			$transition=$trigger_context['transition'];
			return ($this->soflows->remove_all_roles($transition,$object));
		}
	
	}

?>
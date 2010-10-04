<?php
	/**
	* EMail - JavaScript addressbook
	*
	* @author Alex Borges <alex@sogrp.com>
	* @author Dave Hall <dave.hall@mbox.com.au>
	* @author Gerardo Ramirez <gramirez@grupogonher.com>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package email
	* @version $Id$
	* @internal 
	*/


	/**
	* JavaScript addressbook
	*
	* @package email
	*/	
	 class bojsaddressbook
	{
		//@param $contacts phpgwapi contacts object
		var $contacts;
		//@param destboxes array that has the state of the three possible destination boxes
		//this thing will have the title of the destination boxes and their content
		//this class is session cache enabled. When you solicit the content of 
		//this boxes this class may get it from cache if it thinks its necesary
		//the boxes are named toselectbox,ccselectbox  and bccselectbox. 
		var $destboxes;
		//@param contactquery this is an array that holds the parameters
		//to make a query for  the contacts class. The contact class takes
		//as parameters: integers: start,offset. The cols array that has the fields that should be returned.
		//The query string that returns whatever matches the given string for the fields provided
		//the filter string to match against non contact fields. The sort boolean and the extra sort_by parameter
		//to sort by a given field.
		var $contactquery;
		//@param queryresult its the array with actual user data that we have gotten from contacts
		var $queryresults;
		//@param cachestate Its an internal var to tell us if we should expire the cache
		var $cachestate="dirty";
		var $debug = False;
		//@param result Which has an array of results from querying the contacts backend.
		var $result;
		//@param mass_query_cols Is an array that tells the backends which fields to get from the
		//contacts backend for FULL QUERIES. Full queries are made upon construction of this
		//object. They are different from single queries in that full queries are supposed to get
		//A list of contacts, each having their own fields given by this array.
		var $mass_query_cols = array (
				'title'     => 'title',
				'n_given'    => 'n_given',
				'n_family'   => 'n_family',
				'email'      => 'email',
				'org_name' => 'org_name',
				'email_home'  => 'email_home'
				);
		//@param single_query_cols This array is similar to the one above except this is for
		//SINGLE QUERIES. This queries happen when you call the get_userdata method to get
		//the record of a single entry in the contacts backend
		//To add a field to fetch back from userdata
		//u need to add the field u want here
		//Also, in class.uijsaddressbook there is a translation array that has
		//the names of each field in english. Use it to decide what values to add here
		var $single_query_cols =  array (
				'title'     => 'title',
				'n_given'    => 'n_given',
				'n_family'   => 'n_family',
				'org_name' => 'org_name',
				'tel_work' => 'tel_work',
				'cat_id'  => 'cat_id'
				);
		//@param use_session Not used at the moment
		var $use_session=true;

		//@function bojsaddressbook 
		//@abstract This class's contructor
		//@param contactquery Its a query array in the form explained as:
		//	order: Must be equal to the order in which you want the query...ASC or DESC
		//	categories: Must have the categories string as constrained by the categories class
		//		    ,1,2 or 1,2 are valid strings to say, category number 1 and 2
		//	filter: This is a whole parameter in itself, will explain bellow in the parse_contactquery discussion
		//	query: Freestyle query to match against any fields in a mass query
		//	sort:  The field to sort by.... n_give will sort by name, for example
		//	directory_uid: This field may be empty but, if it has a number in it
		//	we will search in the contacts owned by the user whose uid matches this value
		//@param queryresults UNUSED, might use it for caching state purposes later on
		//@discussion 
		//This function checks its cache. All parameters are optional. If called with no parameters
		//the class will assume all that it needs is in the cache and will get it from there
		//If a contactquery value is supplied, the class will desregard its cache and go fetch the whole
		//query again. 
		//To be truth, its actually quite stupid and we should be using more intelligence to decide
		//if the cache is stale.... for example, caching the result form parse_contactquery and
		//compare it with what results from parsing the incoming contactquery. If its the same,
		//we shouldnt refetch.
		function bojsaddressbook($contactquery="",$queryresults="")
		{
			$this->contactsobject=CreateObject('phpgwapi.contacts');
			//the idea is that here, right here, nowhere else, do we decide what to keep
			//from the cache and what to go and query again
				$data=$this->read_sessiondata();
				if(is_array($contactquery))
				{
					$this->parse_contactquery($contactquery);
					$data['result']=$this->contactsobject->read($this->contactquery['start'],$this->contactquery['offset'],
							$this->mass_query_cols,$this->contactquery['query'],
							$this->contactquery['filter'],
							$this->contactquery['sort'],
							$this->sortby);
					$this->save_sessiondata($data);
				}

				$this->result=$data['result'];

		}
		//@function parse_contactquery 
		//@param contactquery As described in the constructor's param
		//@abstrcat Parses an incoming contactquery into what the contacts backend
		//likes to see in a query
		//@discussion This ignores the start and offset parameters as they are somehow
		//obsolete in this version. Its strange, i know, but maybe we will want to optimize
		//later what we want to fetch from the contacts backend by this parameters so all other
		//functions respect and think that thisone builds the start and offset
		function parse_contactquery($contactquery)
		{
			$notfirsttime=False;
			while(list($k,$v)=each($contactquery))
			{
				switch($k)
				{
					case 'filter':
						{
							switch($v)
							{

								case 'none':
									{
										$this->contactquery['filter']=$this->contactquery['filter'].
											($notfirsttime ? "," :"")."tid=n";
										$notfirsttime=True;
										break;
									}
								case 'user_only':
									{
										$this->contactquery['filter']=$this->contactquery['filter'].
											($notfirsttime ? "," :"").
											'owner='.$GLOBALS['phpgw_info']['user']['account_id'];
										$notfirsttime=True;
										break;
									}
								case 'directory':
									{
										if(!$contactquery['directory_uid'])
										{

											$this->contactquery['filter']=$this->contactquery['filter'].
												($notfirsttime ? "," :"")."tid=p";
										}
										else
										{
											 $this->contactquery['filter']=$this->contactquery['filter'].
											 	      ($notfirsttime ? "," :"")."owner=".
												      $contactquery['directory_uid'];
										}
										$notfirsttime=True;
										break;
									}
								case 'private':
									{
										$this->contactquery['filter']=$this->contactquery['filter'].
											($notfirsttime ? "," :"").'owner='.
											$GLOBALS['phpgw_info']['user']['account_id'].
											',access=private';
										$notfirsttime=True;
										break;
									}
							}
							$notfirsttime=false;
							break;
						}
					case 'categories':
						{
							if($v)
							{
								$this->contactquery['filter']=$this->contactquery['filter'].
										 ($notfirsttime ? "," :"")."cat_id=".$v;
							$notfirsttime=true;
							}
							break;
						}
					case 'query':
						{
							if($v)
							{
								$this->contactquery['query']=$v;
							}
						}
							
				}//end switch
			}//end while
//			print "<br /> built query";
//			print_r($this->contactquery);
		}//end function
		
		//@function forget_query
		//@discussion
		//Causes the class to forget its query cache. This does not forget the destination boxes, only
		//the mass query
		function forget_query()
		{
				$this->save_sessiondata("");
		}
		//@function recordinfo
		//@param addy_id The record's id in the contacts backend
		//@abstract Gets the record info descirbed by the single_query_cols array
		//@discussion This returns an array of field=>value that actually has
		//the whole record for the given id. As u can see, the values it gets back are
		//given by the single_query_cols attribute which u can change to get more data
		function recordinfo($addy_id)
		{
			$entry = $this->contactsobject->read("","",$this->single_query_cols,"","id=$addy_id");
			if(!$entry[0])
			{
				return false;
			}
			return $entry[0];	
		}
		//@function save_destboxes
		//@param destboxes The array of destbox arrays that we want saveed in the cache
		//@discussion This function saves the destboxes into the cache
		function save_destboxes($destboxes)
		{
			
			$this->save_sessiondata($destboxes,"destboxes");
		}
		//@function get_destboxes
		//@abstract Function to get the destination boxes... .this parameter should exlusively be gotten this way
		//@discussion This function sees if we have any destboxes present in the destboxes attribute
		//if we do, it returns that, if we dont, it gets them from cache.
		
		function get_destboxes()
		{
			if(!is_array($this->destboxes) || (count($this->destboxes)<1) )
			{
				$this->destboxes=$this->read_sessiondata("destboxes");
			}
			return $this->destboxes;
		}
		//@function forget_destbox
		//@param destboxname The name of the destbox which serves as key to the destboxes array
		//@abstract Will unset the live destbox corresponding to destboxname
		//@discussion 
		//Note that this function will not forget the destbox from the cache... i thought it
		//a bit unneded for the particular application since i wanted this function to 
		//iterate through the destboxes array and unset them one by one. This means that
		//thisone only operates on real, already in memory (not in cache) destboxes.
		//For the cache to reflect this change, you need to $obj->save_destboxes($this->destboxes)
		//after calling this.
		function  forget_destbox($destboxname)
		{
			$this->get_destboxes();
			if(is_array($this->destboxes[$destboxname]))  
			{
				unset($this->destboxes[$destboxname]); 
				return $this->destboxes;
			}  
			return false;
			
		}
		//@function forget_destboxes
		//@abstract Will forget all the destboxes, then save the changes to the cache
		
		function forget_destboxes()
		{
			if($this->get_destboxes())
			{
				while(list($name,$list)=each($this->destboxes))
				{
					$this->forget_destbox($name);
				}
				$this->set_destboxes($this->destboxes);
			}
		}
		//@function set_destboxes
		//@param aryboxes The new destboxes array
		//@param deleted An array of booleans with keys similar to the destboxes array
		//If a given destination box has a true entry here, it will be removed in the cache
		//@abstract This functions saves in cache the destination boxes values
		//@discussion Note that this function can be mistaken by the save_destboxes function.
		//Different thigs completely. This one takes an array of destboxes. The keys to this array
		// are the destboxes names. Inside each array, there are uid => name pairs. Note the absence
		//of an email field. The incoming aryboxes have NO email field whatsoever.
		//What we do here, is try and find the corresponding email fields either in cache or directly
		//in our mass query cache and set that field correctly to save it in cache
		//This function is redundant, inneficient and dead slow. Not to say complex and unreadable
		//Please change this please please please.
		//Previous disclaimer said, it works now, and will release this way.
		function set_destboxes($aryboxes,$deleted='')
		{
			//print_r($aryboxes);
//			print "<br /> SAVed DESTBOXES <br />";
			//We get our own destboxes from the cache
			$saveddestboxes=$this->get_destboxes();
		//	print_r($saveddestboxes);
			$found=false;
			//We iterate into each box
			while(list($ak,$li)=each($aryboxes))
			{
//				print $ak." ".$li."<br />";
//				print_r($li);
//				print "<br />";
				//We make shure this box has an array in it
				if($aryboxes[$ak])
				{
					//We iterate into the incoming box to search
					//for its values in the cache
					while(list($numary,$ary)=each($aryboxes[$ak]))
					{
//						print "<br /> Iterating aryboxes $numary";	
//						print_r($ary);
						list($id,$name)=each($ary);
						//Look for this record in the cached destboxes
						if(is_array($saveddestboxes[$ak]))
						{
							//Well, we found that we have this destboxed cached so
							//now we will iterate through that
							while(list($numarysave,$arysave)=each($saveddestboxes[$ak]))
							{
								//We will try and get each addressbook key
								//out of the cached destbox
								list($sid,$sname)=each($arysave);
								
//								print "<br /> Iterating destboxes $id -> $name / $sid $sname";	
								//So we can compare it and set the email field in it
								if($id==$sid)
								{
//									print "<br /> found $id in $ak";
//									print "<br /> seting mail to $arysave[email]";
									$ary['email']=$arysave['email'];
									$aryboxes[$ak][$numary]=$ary;
									
									$found=true;
								}

							}
							reset($saveddestboxes[$ak]);
						}
						//couldnt find it in saved destboxes, lookfor ir in result
						//This redundant POSH makes me angry....
						//Now we look into our names cache...im not shure why, if i 
						//try and evade this search when i find it in the cache, 
						//it all goes borken
						//We iterate into the query cache
						while(list($num,$record)=each($this->result))
						{
//							print "<br /> Iterating results $id   ---> $name <br />$record[id]---> $record[email]";
							//Found what we are looking for
							if($id == $record["id"])
							{
								//Set the mail record to what it should be
//								print "<br /> seting mail to $record[email] <br />";
								$ary['email']=($record["email"] ? $record["email"] : $record["home_email"]);
								$aryboxes[$ak][$numary]=$ary;
								$retboxes[$ak][$id]['email']= $ary['email'];
								$retboxes[$ak][$id]['name']= $name;
								
							}
						}
						reset($this->result);
						$found=false;
					}

				}
				elseif(!$deleted[$ak])
				{
//					print "<br />Saving $ak from destination data $deleted[$ak]<br />";
//					print_r($deleted);
					//Delete the destboxes that need deletion
					$aryboxes[$ak]=$saveddestboxes[$ak];
				}
			}
//			print "<br />modified<br />";
//				print_r($aryboxes);
				reset($aryboxes);
			//Save the resulting destboxes
			$this->save_destboxes($aryboxes);
			//We return what we couldnt find in cache so the caller can evaluate
			//if he needs to refresh his info...
			return $retboxes; 
			
		}
		//@function save_sessiondata
		//@param data The data to be saved
		//@param location An extra string to save data in diferent locations
		//@abstract Saves the data into the app session cache
		//@discussion
		//If you pass it no location, it will save into jsbook_data
		//If you do, it will save into jsbook_data_location
		//This is important cause we sometimes only need the destboxes and not
		//the whole queries so we only get what we need
		function save_sessiondata($data,$location="")
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data',"jsbook_data".($location ? '_'.$location :""),$data);
			}
			if($this->debug)
			{
				echo '<br />Saving: ';
				_debug_array($data);
			}
		}
		//@function read_sessiondata
		//@param location
		//@abstract gets data out of the appsesion cache
		//@discussion
		//The location field behaves like the one described in save_sessiondata

		function read_sessiondata($location="")
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','jsbook_data'.($location ? '_'.$location :""));
			if($this->debug)
			{
				echo '<br />Read: ';
				_debug_array($data);
			}
			return $data;
		}
		
	}
?>

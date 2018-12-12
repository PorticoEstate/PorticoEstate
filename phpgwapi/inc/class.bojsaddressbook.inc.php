<?php
	/**
	* Logic for the javascript addressbook
	* @author Alex Borges <alex@sogrp.com>
	* @copyright Copyright (C) 2003-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	*/

	/**
	* Logic for the javascript addressbook
	*
	* This class will handle all data.
	* This means that the state of the abstraction we call the cool addressbook chooser
	* is here.... THE STATE! Nothing webish about this file. This file will no receive ever variables
	* from $_POST or $_GET. That is for the UI class to handle. This class has methods that receive
	* parameters so you  can use it from anyware.
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_bojsaddressbook
	{
		var $soaddressbook;
		/**
		* Private contacts object
		* @var object phpgwapi.contacts object
		* @access private
		*/
		var $contacts;
		/**
		* The state of the three possible destination boxes
		* @var array The state of the three possible destination boxes
		* This thing will have the title of the destination boxes and their content
		* This class is session cache enabled. When you solicit the content of this boxes
		* this class may get it from cache if it thinks its necesary the boxes are named
		* toselectbox,ccselectbox  and bccselectbox.
		* @access private
		*/
		var $destboxes;
		/**
		* Query for the contacts class
		* @var array Holds the parameters
		* The contact class takes as parameters: integers: start,offset.
		* The cols array that has the fields that should be returned.
		* The query string that returns whatever matches the given string for the fields provided
		* the filter string to match against non contact fields. The sort boolean and the extra sort_by parameter
		* to sort by a given field.
		* @access private
		*/
		var $contactquery;
		/**
		* Actual user data
		* @var array Actual user data that we have gotten from contacts
		* @access private
		*/
		var $queryresults;
		/**
		* Cache expire flag
		* @var string Cache expire flag
		* @access private
		*/
		var $cachestate="dirty";
		var $debug = False;
		/**
		* Results from querying the contacts backend
		* @var array Results from querying the contacts backend
		* @access private
		*/
		var $result;
		/**
		* Tells the backends which fields to get from the contacts backend for FULL QUERIES
		* @var array Tells the backends which fields to get from the contacts backend for FULL QUERIES
		* Full queries are made upon construction of this object. They are different from single
		* queries in that full queries are supposed to get a list of contacts, each having their
		* own fields given by this array.
		* @access private
		*/
		var $mass_query_cols = array (
			//'per_title',
			'per_first_name',
			'per_last_name',
			'contact_id'
			//'my_org_id',
			//'org_name'
			);
		/**
		* Tells the backends which fields to get from the contacts backend for SINGLE QUERIES
		* @var array Tells the backends which fields to get from the contacts backend for SINGLE QUERIES
		* This queries happen when you call the get_userdata method to get the record of a single entry
		* in the contacts backend. To add a field to fetch back from userdata you need to add the field
		* you want here.
		* Also, in class.uijsaddressbook there is a translation array that has
		* the names of each field in english. Use it to decide what values to add here
		* @access private
		*/
		var $single_query_cols =  array (
			'per_title',
			'per_first_name',
			'per_last_name' ,
			'per_department',
			'contact_id' ,
			'addr_add1',
			'addr_add2',
			'addr_city',
			'addr_country'

			);
		var $commtypes= array(
			'work email' => 'work email'
			);
		/**
		* Not used at the moment
		* @var boolean Not used at the moment
		* @access private
		*/
		var $use_session=true;
		var $filters;

		/**
		* Constructor
		*
		* @param array $contactquery Contains the following fields:
		* 'order' sort order ASC or DESC
		* 'categories' Categories string for categories class ,1,2 or 1,2 are valid strings for category number 1 and 2
		* 'filter' Explained bellow in the parse_contactquery discussion
		* 'query' Freestyle query to match against any fields in a mass query
		* 'sort' The field to sort by "n_give" will sort by name
		* 'directory_uid' Owner User ID for search or empty
		* @param $queryresults Unused, might use it for caching state purposes later on
		*
		* This function checks its cache. All parameters are optional. If called with no parameters
		* the class will assume all that it needs is in the cache and will get it from there
		* If a contactquery value is supplied, the class will desregard its cache and go fetch the whole
		* query again.
		*
		* @internal To be truth, its actually quite stupid and we should be using more intelligence to decide
		* if the cache is stale.... for example, caching the result form parse_contactquery and
		* compare it with what results from parsing the incoming contactquery. If its the same,
		* we shouldnt refetch.
		*/
		function __construct($contactquery="",$queryresults="")
		{
			$this->contactsobject = createObject('phpgwapi.contacts');
			$this->boaddressbook = createObject('addressbook.boaddressbook');

			//the idea is that here, right here, nowhere else, do we decide what to keep
			//from the cache and what to go and query again
			$data=$this->read_sessiondata();
			if(is_array($contactquery))
			{
				$this->parse_contactquery($contactquery);
				//Searching freely in all fields
				if($contactquery['in'])
				{
					//print '<br /><strong>DIRECTORY QUERY</strong><br />';
					//$data['result']=$this->contactsobject->get_system_contacts($this->mass_query_cols);
					$data['result'] = $this->boaddressbook->get_persons(
 						$this->mass_query_cols,'','','','',array('contact_id' => $this->filters['contact']));
				}
				else
				{

					//print "<br /><strong>".print_r($this->mass_query_cols)." searcvhing".
					//$contactquery['query']."</strong><br />";
					$precriteria = $this->boaddressbook->criteria_contacts(
						$this->filters['access'],
						$this->filters['cat_id'],
						$this->mass_query_cols,
						$contactquery['query'], '');

					$data['result'] = $this->boaddressbook->get_persons(
 						$this->mass_query_cols,'','','','','',
 						$precriteria);
				}

				if(count($data['result']) > 0)
				{
					//print '<br><strong>Fed to comm_contact'.print_r($contacts).'</strong><br />';
					$entries_comm = $this->boaddressbook->get_comm_contact_data(array_keys($data['result']), $this->commtypes);
					$data['result']=$this->merge_emails_to_results($data['result'],$entries_comm);
					//print '<br /><strong>entries com'.var_export($entries_comm).'</strong><br />';
				}

				//print '<strong><br />DATA<BR></strong>';
				//print_r($data);
				$this->save_sessiondata($data);
			}
			$this->result=$data['result'];
		}

		function merge_emails_to_results($result,$entries_comm)
		{
			foreach($result as $key => $data)
				{
					$result[$key]['email'] = $entries_comm[$key]['work email'];
				}
			return $result;
		}

		/**
		* Parses an incoming contactquery into what the contacts backend likes to see in a query
		*
		* @param array $contactquery Contains the following fields:
		* 'order' sort order ASC or DESC
		* 'categories' Categories string for categories class ,1,2 or 1,2 are valid strings for category number 1 and 2
		* 'filter' Explained bellow in the parse_contactquery discussion
		* 'query' Freestyle query to match against any fields in a mass query
		* 'sort' The field to sort by "n_give" will sort by name
		* 'directory_uid' Owner User ID for search or empty
		*
		* This ignores the start and offset parameters as they are somehow obsolete in this version.
		* Its strange, I know, but maybe we will want to optimize later what we want to fetch from
		* the contacts backend by this parameters so all other functions respect and think that this
		* one builds the start and offset.
		*/
		function parse_contactquery($contactquery)
		{
			$notfirsttime=False;
			//while(list($k,$v)=each($contactquery))
			//print '<br /><strong>Contactyquery</strong><br />';
			//print_r($contactquery);
			foreach ($contactquery as $k => $v)
			{
				switch($k)
				{
				case 'filter':
				{
					switch($v)
					{

					case 'none':
					{
						//print_r($this->grants);
						//$this->filters ['owner'] = array_keys($this->grants);
						$this->filters['access']=PHPGW_CONTACTS_ALL;
						break;
					}
					case 'user_only':
					{
						//$this->filters['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
						$this->filters['access']=PHPGW_CONTACTS_MINE;
						break;
					}
					case 'directory':
					{
						$this->filters['access']='addressmaster';
						//$this->filters['OWNER_CONSTANT']=PHPGW_CONTACTS_PRIVATE;
						$notfirsttime=True;
						break;
					}
					case 'private':
					{
						//$this->filters['owner'] = $GLOBALS['phpgw_info']['user']['account_id'];
						//$this->filter['access'] = 'private';
						$this->filters['access']=PHPGW_CONTACTS_PRIVATE;
						break;
					}
					}
					$notfirsttime=false;
					break;
				}
				case 'categories':
				{
					$this->filters['cat_id']=$v;
					break;
				}
				case 'query':
				{
					if($v)
					{
						$this->filters['query'] = $v;
					}
					break;
				}
				case 'in':
				{
					$this->filters['contact'] = explode(',',$v);
					break;
				}

				}//end switch
			}//end while
			//print "<br /> built query";
			//$this->filters['my_preferred']='Y';
			//print_r($this->filters);
		}//end function


		/**
		* Causes the class to forget its query cache.
		* This does not forget the destination boxes, only the mass query
		*/
		function forget_query()
		{
			$this->save_sessiondata("");
		}

		/**
		* Gets the record info described by the single_query_cols array
		*
		* @param integer $addy_id The record's id in the contacts backend
		* @return array Field=>value pair that actually has the whole record for the given id.
		* As you can see, the values it gets back are given by the single_query_cols attribute
		* which you can change to get more data
		*/
		function recordinfo($addy_id)
		{
			//print "<br /><strong>CID".$addy_id."</strong><br />";
			$entry = $this->contactsobject->get_persons(
				$this->single_query_cols,"","","","",array("contact_id" => "$addy_id"));
			if(!$entry[0])
			{
				return false;
			}
			return $entry[0];
		}

		/**
		* This function saves the destboxes into the cache
		*
		* @param array $destboxes Array of destbox arrays that we want save in the cache
		*/
		function save_destboxes($destboxes)
		{

			$this->save_sessiondata($destboxes,"destboxes");
		}

		/**
		* Get the destination boxes
		*
		* @return array|boolean Destboxes or false
		*/
		function get_destboxes()
		{
			if(!is_array($this->destboxes) || (count($this->destboxes)<1) )
			{
				$this->destboxes=$this->read_sessiondata("destboxes");
			}
			if(is_array($this->destboxes))
			{
				return $this->destboxes;
			}
			return false;
		}

		/**
		* Will unset the live destbox corresponding to destboxname
		*
		* @param $destboxname The name of the destbox which serves as key to the destboxes array
		* @return array|boolean Destboxes or false
		*
		* Note that this method will not forget the destbox from the cache.
		* I thought it a bit unneded for the particular application since I wanted this function to
		* iterate through the destboxes array and unset them one by one. This means that
		* this one only operates on real, already in memory (not in cache) destboxes.
		* For the cache to reflect this change, you need to $obj->save_destboxes($this->destboxes)
		* after calling this.
		*/
		function forget_destbox($destboxname)
		{
			$this->get_destboxes();
			if(is_array($this->destboxes[$destboxname]))
			{
				unset($this->destboxes[$destboxname]);
				return $this->destboxes;
			}
			return false;

		}

		/**
		* Will forget all the destboxes, then save the changes to the cache
		*/
		function forget_destboxes()
		{
			if($this->get_destboxes())
			{
				$destboxesnames=array_keys($this->destboxes);
				foreach($destboxesnames as $name)
				{
					$this->forget_destbox($name);
				}
				$this->set_destboxes($this->destboxes);
			}
		}

		/**
		* This functions saves in cache the destination boxes values
		*
		* @param array $aryboxes The new destboxes array
		* @param array $deleted An array of booleans with keys similar to the destboxes array
		* If a given destination box has a true entry here, it will be removed in the cache
		* @return array We return what we couldn't find in cache so the caller can evaluate if he needs to refresh his info
		*
		* Note that this function can be mistaken by the save_destboxes function.
		* Different things completely. This one takes an array of destboxes. The keys to this array
		* are the destboxes names. Inside each array, there are uid => name pairs. Note the absence
		* of an email field. The incoming aryboxes have NO email field whatsoever.
		* What we do here, is try and find the corresponding email fields either in cache or directly
		* in our mass query cache and set that field correctly to save it in cache
		*
		* @internal This function is redundant, inneficient and dead slow. Not to say complex and unreadable
		* Please change this please please please.
		* Previous disclaimer said, it works now, and will release this way.
		*/
		function set_destboxes($aryboxes,$deleted='')
		{
			//print_r($aryboxes);
			//print "<br />SAVed DESTBOXES<br />";
			//We get our own destboxes from the cache
			$saveddestboxes=$this->get_destboxes();
			$GLOBALS['debug_timer_start']=perfgetmicrotime();
			//print_r($saveddestboxes);

			$found=false;
			//We iterate into each box
			//while(list($ak,$li)=each($aryboxes))
			foreach($aryboxes as $ak => $li)
			{
				//print $ak." ".$li."<br />";
				//print_r($li);
				//print "<br />";
				//We make shure this box has an array in it
				if($aryboxes[$ak])
				{
					//We iterate into the incoming box to search
					//for its values in the cache
					//while(list($numary,$ary)=each($aryboxes[$ak]))
					foreach($aryboxes[$ak] as $numary => $ary)
					{
						//print "<br /> Iterating aryboxes $numary";
						//print_r($ary);
						//list($id,$name)=each($ary);
						$id = key($ary);
						$name = current($ary);
						//Look for this record in the cached destboxes
						if(is_array($saveddestboxes[$ak]))
						{
							//Well, we found that we have this destboxed cached so
							//now we will iterate through that
							//while(list($numarysave,$arysave)=each($saveddestboxes[$ak]))
							foreach($saveddestboxes[$ak] as $numarysave => $arysave)
							{
								//We will try and get each addressbook key
								//out of the cached destbox
								//list($sid,$sname)=each($arysave);
								$sid = key($arysave);
								$sname = current($arysave);
								//print "<br /> Iterating destboxes $id -> $name / $sid $sname";
								//So we can compare it and set the email field in it
								if($id==$sid)
								{
									//print "<br /> found $id in $ak";
									//print "<br /> seting mail to $arysave[email]";
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
						if(strpos($id, 'id_')!==false)
						{
							$categoryobject=CreateObject('phpgwapi.categories');
							$categoryobject->app_name = 'addressbook';

							$cat_id = substr($id,3);
							//$cat_name = $categoryobject->id2name($cat_id);
							//$cat_email = "cat:$cat_id:$cat_name@grupogonher.com";
							$cat_email = "id_$cat_id@lists.com";
							$ary['email']=$cat_email;
							$aryboxes[$ak][$numary]=$ary;
							$retboxes[$ak][$id]['email']= $ary['email'];
							$retboxes[$ak][$id]['name']= $name;
						}
						else
						{
							//while(list($num,$record)=each($this->result))
							foreach ($this->result as $num => $record)
							{
								//print "<br> Iterating results $id   ---> $name <br>$record[id]---> $record[email]";
								//Found what we are looking for
								if($id == $record["contact_id"])
								{
									//Set the mail record to what it should be
									//print "<br> seting mail to $record[email] <br>";
									$ary['email']=($record["email"] ? $record["email"] : $record["home_email"]);
									$aryboxes[$ak][$numary]=$ary;
									$retboxes[$ak][$id]['email']= $ary['email'];
									$retboxes[$ak][$id]['name']= $name;
								}
							}
							reset($this->result);
						}
						$found=false;
					}
				}
				elseif(!$deleted[$ak])
				{
					//print "<br />Saving $ak from destination data $deleted[$ak]<br />";
					//print_r($deleted);
					//Delete the destboxes that need deletion
					$aryboxes[$ak]=$saveddestboxes[$ak];
				}
			}
			//print "<br />modified<br />";
			//print_r($aryboxes);
			reset($aryboxes);
			//Save the resulting destboxes
			$GLOBALS['debug_timer_stop']=perfgetmicrotime();
			//print("<br />Set destboxes in ". ($GLOBALS['debug_timer_stop'] - $GLOBALS['debug_timer_start']) . " seconds.");
			$this->save_destboxes($aryboxes);
			//We return what we couldnt find in cache so the caller can evaluate
			//if he needs to refresh his info...
			return $retboxes;
		}

		/**
		* Saves the data into the app session cache
		*
		* @param string $data The data to be saved
		* @param string $location Data locations, when no location is given, it will save into jsbook_data otherwise in jsbook_data_location
		* This is important cause we sometimes only need the destboxes and not the whole queries so we only get what we need
		*/
		function save_sessiondata($data,$location="")
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data',"jsbook_".($location ? '_'.$location :""),$data);
			}
			if($this->debug)
			{
				echo '<br>Saving: ';
				_debug_array($data);
			}
		}

		/**
		* Gets data out of the appsesion cache
		*
		* @param string $location Data locations, when no location is given, it will save into jsbook_data otherwise in jsbook_data_location
		* This is important cause we sometimes only need the destboxes and not the whole queries so we only get what we need
		* @return array Appsession data for jsbook
		*/
		function read_sessiondata($location="")
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','jsbook_'.($location ? '_'.$location :""));
			if($this->debug)
			{
				echo '<br>Read: ';
				_debug_array($data);
			}
			return $data;
		}

		/**
		* Changes a false addressbook field into the correct string in the current language
		*
		* @param string $fieldname Addressbook field name
		*/
		function display_name($fieldname)
		{
			$this->contactsobject->display_name($fieldname);
		}

		function get_persons_by_list($list)
		{
			if(intval($list))
			{
				$criteria = $this->contactsobject->criteria_for_index($GLOBALS['phpgw_info']['user']['account_id'], PHPGW_CONTACTS_ALL, $list);
				$new = phpgwapi_sql_criteria::token_and($criteria, phpgwapi_sql_criteria::_equal('comm_descr', $this->contactsobject->search_comm_descr('work email')));
				$persons = $this->contactsobject->get_persons(array('per_full_name', 'comm_data'), '', '', '', '', '', $new);
				if(!is_array($persons))
				{
					$persons = array();
				}

				foreach($persons as $data)
				{
					$persons_list[] = array('name' => $data['per_full_name'],
								'email'=> $data['comm_data']);
				}
			}
			return $persons_list;
		}
	}

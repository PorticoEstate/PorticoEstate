<?php
	phpgw::import_class('booking.bocommon');
	phpgw::import_class( 'booking.sosearchcount' );
	
	class bookingfrontend_bosearch extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->sobuilding = CreateObject('booking.sobuilding');
			$this->soorganization = CreateObject('booking.soorganization');
			$this->soresource = CreateObject('booking.soresource');
		}

		private function get_free_time($restype, $from, $to, $wclause)
		{

			$db = & $GLOBALS['phpgw']->db;


			foreach( $restype as $key => $value ) {
				$restype[$key] = "'" . $value . "'";
			}
			$restype = implode(",", $restype);


			$sql2 = "SELECT br.id FROM bb_event be, bb_event_resource ber, bb_resource br 
					WHERE  ('$from 14:00:00' 
					BETWEEN  be.from_ AND be.to_ OR '$to 14:00:00' 
					BETWEEN be.from_ AND be.to_ 
					OR ('$from 14:00:00' < be.from_ 
					AND '$to 14:00:00' > be.to_))";

			if ($restype)
				$sql2 .= " AND br.type in (".$restype.") ";
					
			$sql2 .= " AND be.id = ber.event_id AND ber.resource_id = br.id";


			$sql = "SELECT br1.*,bu.name as building_name,bu.district FROM bb_resource br1, bb_building bu
					WHERE br1.id 
					NOT IN ($sql2) AND br1.building_id = bu.id";

			if ($restype)
				$sql  .= " AND br1.type IN (".$restype.") ";

			$sql .= " AND ".$wclause;

			$sql .= " ORDER BY br1.type, br1.name";

			$db->query($sql);
			$result = $db->resultSet;

			$retval = array();
			$retval['total_records'] = count($result);
			$retval['results'] = $result;
			$retval['start'] = 0;
			$retval['sort'] = null;
			$retval['dir'] = 'asc';

			return $retval;
		}
		

		function search($searchterm,$resource)
		{


			$type = phpgw::get_var('type', 'GET');
            $bui_result = $org_result = $res_result = array();

			if($resource['from_']) {
				if ($resource['region'] == 'east') {
					$regions = "('akerhus','oslo','ostfold','vestfold','hedemark','oppland','buskerud','telemark')";
				}
				elseif ($resource['region'] == 'south') {
					$regions = "('vestagder','austagder')";
				}					
				elseif ($resource['region'] == 'west') {
					$regions = "('rogaland','hordaland','sognogfjordane','moreogromsdal')";
				}					
				elseif ($resource['region'] == 'middle') {
					$regions = "('nordtrodelag','sortrondelag')";
				}					
				elseif ($resource['region'] == 'north') {
					$regions = "('finnmark','nordland','troms')";
				} else {
					$regions = '';
				}				
				if( $resource['fylke'] != '') {
					$fylke = $resource['fylke'];
				} else {
					$fylke = '';
				}
				if ($resource['res'] != ''){
					$ressurs = $resource['res'];
					if(in_array($ressurs,array('House','Boat','Location'))) {
						if ($resource['beds']=='one') {
							$sengeplasser = '(br1.bedspaces >= 1 and br1.bedspaces <= 10)';
						} 
						elseif ($resource['beds']=='two') {
							$sengeplasser = 'br1.bedspaces >= 10 AND br1.bedspaces <= 25';
						}
						elseif ($resource['beds']=='three') {
							$sengeplasser = 'br1.bedspaces >= 25 AND br1.bedspaces <= 50';
						}
						elseif ($resource['beds']=='four') {
							$sengeplasser = 'br1.bedspaces >= 50 AND br1.bedspaces <= 100';
						}
						elseif ($resource['beds']=='five') {
							$sengeplasser = 'br1.bedspaces >= 100 AND br1.bedspaces <= 300';
						}
						elseif ($resource['beds']=='six') {
							$sengeplasser = 'br1.bedspaces >= 300';
						}
						$teltplasser = '';
					} elseif(in_array($ressurs,array('Campsite'))) {
						if ($resource['campsite'] != '') {
							$teltplasser = 'br1.campsites > '.$resource['campsite'];
							$sengeplasser = '';
						} else {
							$teltplasser = '';
							$sengeplasser = '';
						}
					} else {
						$teltplasser = '';
						$sengeplasser = '';
					}
				} else {
					$ressurs = '';
					$teltplasser = '';
					$sengeplasser = '';
				}

				$wclause = 'br1.active = 1';
					if($regions != '') {
					$wclause .= " AND bu.district IN ".$regions;			
				} 					
				if($fylke != '') {
					$wclause .= " AND bu.district = '".$fylke."'";						
				} 					
				if($teltplasser != '') {
					$wclause .= ' AND '.$teltplasser;						
				} 					
				if($sengeplasser != '') {
					$wclause .= ' AND '.$sengeplasser;						
				} 					
				if ($resource['res'] != '' ) 
					$restype = array($resource['res']);
				else 
					$restype = array();

				$resources = $this->get_free_time(
					$restype,				
					$resource['from_'],
					$resource['to_'],
					$wclause
				);


                foreach($resources['results'] as &$res)
                {

                    $res['name'] = $res['building_name']. ' / ' . $res['name'];
                    $res['type'] = "resource";
                    $res['schedule'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $res['id']));
                    $res['schedule'] = $res['schedule']."#".$resource['from_'];
                    $res['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uiresource.show', 'id' => $res['id']));
                    $res['img_container'] = "resource-" . $res['id'];
                    $res['img_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uidocument_resource.index_images', 'filter_owner_id' => $res['id'], 'phpgw_return_as' => 'json', 'results' => '3'));
                }


				$final_array = $resources;
				$final_array['total_records_sum']	=	array_sum((array)$resources['total_records']);
			
				// Finally increase search counter
				$counter = new booking_searchcount();
				$counter->increaseTerm( $searchterm );

				return $final_array;

			} else {
            if ((!$type || $type == "building") && in_array($resource['res'],array('House','Location','Campsite',''))) {
		
					
					if ($resource['region'] == 'east') {
						$regions = "('akerhus','oslo','ostfold','vestfold','hedemark','oppland','buskerud','telemark')";
					}
					elseif ($resource['region'] == 'south') {
						$regions = "('vestagder','austagder')";
					}					
					elseif ($resource['region'] == 'west') {
						$regions = "('rogaland','hordaland','sognogfjordane','moreogromsdal')";
					}					
					elseif ($resource['region'] == 'middle') {
						$regions = "('nordtrodelag','sortrondelag')";
					}					
					elseif ($resource['region'] == 'north') {
						$regions = "('finnmark','nordland','troms')";
					} else {
						$regions = '';
					}				
					if( $resource['fylke'] != '') {
						$fylke = $resource['fylke'];
					} else {
						$fylke = '';
					}
					if ($resource['res'] != ''){
						$ressurs = $resource['res'];
						if(in_array($ressurs,array('House','Boat','Location'))) {
							if ($resource['beds']=='one') {
								$sengeplasser = '(bedspaces >= 1 and bedspaces <= 10)';
							} 
							elseif ($resource['beds']=='two') {
								$sengeplasser = 'bedspaces >= 10 AND bedspaces <= 25';
							}
							elseif ($resource['beds']=='three') {
								$sengeplasser = 'bedspaces >= 25 AND bedspaces <= 50';
							}
							elseif ($resource['beds']=='four') {
								$sengeplasser = 'bedspaces >= 50 AND bedspaces <= 100';
							}
							elseif ($resource['beds']=='five') {
								$sengeplasser = 'bedspaces >= 100 AND bedspaces <= 300';
							}
							elseif ($resource['beds']=='six') {
								$sengeplasser = 'bedspaces >= 300';
							}
							$teltplasser = '';
						} elseif(in_array($ressurs,array('Campsite'))) {
							if ($resource['campsite'] != '') {
								$teltplasser = 'campsites > '.$resource['campsite'];
								$sengeplasser = '';
							} else {
								$teltplasser = '';
								$sengeplasser = '';
							}
						} else {
							$teltplasser = '';
							$sengeplasser = '';
						}

					} else {
						$ressurs = '';
						$teltplasser = '';
						$sengeplasser = '';
					}

					$wclause = 'active = 1';

					if($regions != '') {
						$wclause .= " AND district IN ".$regions;			
					} 					
					if($fylke != '') {
						$wclause .= " AND district = '".$fylke."'";						
					} 					
					if($teltplasser != '') {
						$wclause .= ' AND '.$teltplasser;						
					} 					
					if($sengeplasser != '') {
						$wclause .= ' AND '.$sengeplasser;						
					} 					

                $bui_result = $this->sobuilding->read(array("query"=>$searchterm, "filters" => array('where' => $wclause)));

                foreach($bui_result['results'] as &$bui)
                {

                    $bui['type'] = "building";
                    $bui['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $bui['id']));
                    $bui['img_container'] = "building-" . $bui['id'];
                    $bui['img_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uidocument_building.index_images', 'filter_owner_id' => $bui['id'], 'phpgw_return_as' => 'json', 'results' => '3'));
					if ( trim($bui['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($bui['homepage'])) )
					{
						$bui['homepage'] = 'http://'.$bui['homepage'];
					}
                }
            }
#            if (!$type || $type == "organization") {
#                $org_result = $this->soorganization->read(array("query"=>$searchterm, "filters" => array("active" => "1")));
#                foreach($org_result['results'] as &$org)
#                {
#                    $org['type'] = "organization";
#                    $org['description'] = nl2br(strip_tags($org['description']));
#                    $org['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $org['id']));
#					if ( trim($org['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($org['homepage'])) )
#					{
#						$org['homepage'] = 'http://'.$org['homepage'];
#					}
#                }
#            }
	            if(!$type || $type == "resource") {
					
					$filters = array();

					if (($resource['region'] == '' || $resource['region'] == 'all')  && $resource['res'] == '')
					{
						$filters['active'] = "1";
		                $res_result = $this->soresource->read(array("query"=>$searchterm, "filters" => $filters));
					} else {

						if ($resource['region'] == 'east') {
							$regions = "('akerhus','oslo','ostfold','vestfold','hedemark','oppland','buskerud','telemark')";
						}
						elseif ($resource['region'] == 'south') {
							$regions = "('vestagder','austagder')";
						}					
						elseif ($resource['region'] == 'west') {
							$regions = "('rogaland','hordaland','sognogfjordane','moreogromsdal')";
						}					
						elseif ($resource['region'] == 'middle') {
							$regions = "('nordtrodelag','sortrondelag')";
						}					
						elseif ($resource['region'] == 'north') {
							$regions = "('finnmark','nordland','troms')";
						} else {
							$regions = '';
						}				
						if( $resource['fylke'] != '') {
							$fylke = $resource['fylke'];
						} else {
							$fylke = '';
						}
						if ($resource['res'] != ''){
							$ressurs = $resource['res'];
							if(in_array($ressurs,array('House','Boat','Location'))) {
								if ($resource['beds']=='one') {
									$sengeplasser = '(br.bedspaces >= 1 and br.bedspaces <= 10)';
								} 
								elseif ($resource['beds']=='two') {
									$sengeplasser = 'br.bedspaces >= 10 AND br.bedspaces <= 25';
								}
								elseif ($resource['beds']=='three') {
									$sengeplasser = 'br.bedspaces >= 25 AND br.bedspaces <= 50';
								}
								elseif ($resource['beds']=='four') {
									$sengeplasser = 'br.bedspaces >= 50 AND br.bedspaces <= 100';
								}
								elseif ($resource['beds']=='five') {
									$sengeplasser = 'br.bedspaces >= 100 AND br.bedspaces <= 300';
								}
								elseif ($resource['beds']=='six') {
									$sengeplasser = 'br.bedspaces >= 300';
								}
								$teltplasser = '';
							} elseif(in_array($ressurs,array('Campsite'))) {
								if ($resource['campsite'] != '') {
									$teltplasser = 'br.campsites > '.$resource['campsite'];
									$sengeplasser = '';
								} else {
									$teltplasser = '';
									$sengeplasser = '';
								}
							} else {
								$teltplasser = '';
								$sengeplasser = '';
							}

						} else {
							$ressurs = '';
							$teltplasser = '';
							$sengeplasser = '';
						}

						$wclause = '';

						if($regions != '') {
							$wclause .= " AND bb.district IN ".$regions;			
						} 					
						if($fylke != '') {
							$wclause .= " AND bb.district = '".$fylke."'";						
						} 					
						if($ressurs != '') {
							$wclause .= " AND br.type = '".$ressurs."'";						
						} 					
						if($teltplasser != '') {
						$wclause .= ' AND '.$teltplasser;						
						} 					
						if($sengeplasser != '') {
							$wclause .= ' AND '.$sengeplasser;						
						} 					

						$res_result = $this->soresource->getresources($searchterm,$wclause);

					}				

    	            foreach($res_result['results'] as &$res)
    	            {
    	                $res['name'] = $res['building_name']. ' / ' . $res['name'];
    	                $res['type'] = "resource";
    	                $res['link'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uiresource.show', 'id' => $res['id']));
    	                $res['img_container'] = "resource-" . $res['id'];
    	                $res['img_url'] = $GLOBALS['phpgw']->link('/bookingfrontend/', array('menuaction' => 'bookingfrontend.uidocument_resource.index_images', 'filter_owner_id' => $res['id'], 'phpgw_return_as' => 'json', 'results' => '3'));
    	            }
    	        }
				$final_array = array_merge_recursive($bui_result, $org_result, $res_result);
				$final_array['total_records_sum']	=	array_sum((array)$final_array['total_records']);
			
				// Finally increase search counter
				$counter = new booking_searchcount();
				$counter->increaseTerm( $searchterm );
				return $final_array;
			}
		}
	}


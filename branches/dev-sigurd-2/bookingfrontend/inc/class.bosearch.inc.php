<?php
	phpgw::import_class('booking.bocommon');
	
	class bookingfrontend_bosearch extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->sobuilding = CreateObject('booking.sobuilding');
			$this->soorganization = CreateObject('booking.soorganization');
			$this->soresource = CreateObject('booking.soresource');
		}
		
		function search($searchterm)
		{
			
			
			
			
			$bui_result = $this->sobuilding->read(array("query"=>$searchterm));
			foreach($bui_result['results'] as &$bui)
			{
				$bui['type'] = "building";
				$bui['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uibuilding.index', 'id' => $bui['id']));
			}
			$org_result = $this->soorganization->read(array("query"=>$searchterm));
			foreach($org_result['results'] as &$org)
			{
				$org['type'] = "organization";
				$org['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uibuilding.index', 'id' => $org['id']));
			}
			$res_result = $this->soresource->read(array("query"=>$searchterm));
			foreach($res_result['results'] as &$res)
			{
				$res['type'] = "resource";
				$res['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uibuilding.index', 'id' => $res['id']));
			}
			$final_array = array_merge_recursive($bui_result, $org_result, $res_result);
			$final_array['total_records_sum']	=	(	$final_array['total_records'][0] +
														$final_array['total_records'][1] +
														$final_array['total_records'][2]);
			
				#echo("<pre>");
				#print_r($final_array);
				#echo("</pre>");
			
			return $final_array;
			
			
			
		}
	}

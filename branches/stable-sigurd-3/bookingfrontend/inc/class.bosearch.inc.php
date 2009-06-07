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
			$type = phpgw::get_var('type', 'GET');
            $bui_result = $org_result = $res_result = array();

            if (!$type || $type == "building") {
                $bui_result = $this->sobuilding->read(array("query"=>$searchterm));
                foreach($bui_result['results'] as &$bui)
                {
                    $bui['type'] = "building";
                    $bui['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $bui['id']));
                }
            }
            if (!$type || $type == "organization") {
                $org_result = $this->soorganization->read(array("query"=>$searchterm));
                foreach($org_result['results'] as &$org)
                {
                    $org['type'] = "organization";
                    $org['description'] = nl2br(strip_tags($org['description']));
                    $org['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $org['id']));
                }
            }
            if(!$type || $type == "resource") {
                $res_result = $this->soresource->read(array("query"=>$searchterm));
                foreach($res_result['results'] as &$res)
                {
                    $res['type'] = "resource";
                    $res['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'bookingfrontend.uiresource.show', 'id' => $res['id']));
                }
            }
			$final_array = array_merge_recursive($bui_result, $org_result, $res_result);
			$final_array['total_records_sum']	=	array_sum((array)$final_array['total_records']);
			
			return $final_array;
		}
	}


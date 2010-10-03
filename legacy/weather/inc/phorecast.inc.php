<?php
/* phorecast v0.4 - Copyright (c) 2000 by Todd S. Hammer <thammer@rtccom.com>
   October 31, 2000
   
   This include file will grab the zone file specified from the National 
   Weather Service and produce two arrays containing the forecast and the 
   extended forecast. The functions here should be fairly well documented so 
   there shouldn't be too much confusion ;-)

   The zone file is updated either upon expiration of the old one or hourly.

   Licensed under the GPL. See http://www.linux.org for the GPL license.
*/

/******************************************************************************
 * This function will convert the first letter of sentences to upper case.
 * If the data is not a sentence it assumes all words need capitalization.
 *****************************************************************************/
	function phorecast_fix_first($data)
	{
		/**************************************************************************
		* look for periods to see if it's a sentence
		*************************************************************************/
		if(ereg('\.',$data))
		{
			$data = trim($data);
			$sentences = explode('.', $data);
			for($z=0;$z<count($sentences);$z++)
			{
				$sentence = trim($sentences[$z]);
				if(!$sentence == "")
				{
					ereg("^([A-Z]|[a-z]){1}(.*$)",$sentence,$regs);
					$newdata .= strtoupper("$regs[1]") . $regs[2] . ". ";
				}
			}
			return "$newdata";

		}
		/**************************************************************************
		* must be a string of words which all need capitalization
		*************************************************************************/
		else
		{
			$data = trim($data);
			$words = explode(" ", $data);
			for($z=0;$z<count($words);$z++)
			{
				$word = trim($words[$z]);
				ereg("^([A-Z]|[a-z]){1}(.*$)",$word,$regs);
				$newdata .= strtoupper("$regs[1]") . $regs[2] . " ";
			}
			return "$newdata";
		}
	}

	/******************************************************************************
	* this function records an error for the NWS lookup
	*****************************************************************************/
	function phorecast_NWS_Error()
	{
		print "<HTML><BODY BGCOLOR=white><CENTER><H3>\n";
		print "Unable to obtain forecast information from the ";
		print "National Weather Service at this time.<BR>\n";
		print "Please try again later.<BR>\n";
		print "</H3></CENTER></BODY></HTML>";
	}

	/******************************************************************************
	* this function creates the filenames for local and web retrieval of data
	*****************************************************************************/
	function phorecast_file_name($forecast)
	{
		$filename = "";

		if ($forecast != "")
		{
			$string     = strtolower($forecast);
			$state      = substr($string,0,2);
			$file       = $string.'.txt';

			$filename["localfile"] = PHPGW_SERVER_ROOT.'/weather/tmp/'.$file;
			$filename["webfile"]   =
			'http://weather.noaa.gov/pub/data/forecasts/zone/'
			.$state.'/'.$file;
		}

		return $filename;
	}

	/******************************************************************************
	* this function attempts to fetch raw forecast data from the given files
	*****************************************************************************/
	function phorecast_file_data($filename, $force_local=FALSE)
	{
		$get_web    = FALSE;
		$failed_web = FALSE;

		$file_data  = "";

		/**************************************************************************
		* if we have some filenames to work with
		*************************************************************************/
		if (is_array($filename))
		{
			/**********************************************************************
			* if have local forecast file
			*********************************************************************/
			if(file_exists($filename["localfile"]))
			{
				/******************************************************************
				* we check to see how current
				*****************************************************************/
				$mtime           = filemtime($filename["localfile"]);
				$expire_data     = file($filename["localfile"]);
				list($one,$two)  = explode(':',$expire_data["0"]);
				list($exp,$junk) = explode(';;',$two);
				ereg("([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})",
				$exp,$regs);

				/******************************************************************
				* generate a unix timestamp from the file data
				*****************************************************************/
				$expire = mktime($regs[4], $regs[5], '0', $regs[2],
				$regs[3], $regs[1]);

				/******************************************************************
				* if expired, must go to web
				*****************************************************************/
				if($expire <= time()||$mtime < time()-3600)
				{
					/**************************************************************
					* may want to ignore expiration
					*************************************************************/
					if ($force_local == FALSE)
					{
						$get_web = TRUE;
					}
				}
			}
			else
			{
				/******************************************************************
				* no local so must go to web
				*****************************************************************/
				$get_web = TRUE;
			}

			/**********************************************************************
			* if need to get from the web
			*********************************************************************/
			if ($get_web == TRUE)
			{
				/******************************************************************
				* attempt to get it
				*****************************************************************/
				if(!$zone = $GLOBALS['phpgw']->network->gethttpsocketfile($filename["webfile"]))
				{ 
					$failed_web = TRUE;
				}
				else
				{
					/**************************************************************
					* if succeed grok it for errors
					*************************************************************/
					for($i=0;$i<count($zone) && !$failed_web;$i++)
					{
						if (ereg("orbidden", $zone[$i]))
						{
							$failed_web = TRUE;
						}
					}
					reset($zone);

					if (!$failed_web)
					{
						/*********************************************************
						* if succeed, put it in our local file
						********************************************************/
						$fp = fopen($filename["localfile"],"w");
						for($i=0;$i<count($zone);$i++)
						{
							fputs($fp,"$zone[$i]");
						}
						fclose($fp);
						reset($zone);
					}
				}
			}

			/**********************************************************************
			* populate the return array
			*********************************************************************/
			if ($failed_web == FALSE)
			{
				$file_data = file($filename["localfile"]);
			}
		}
		return $file_data;
	}

	/******************************************************************************
	* this function populates the forecast, extended forecast and advisory arrays
	*****************************************************************************/
	function phorecast_forecast($file_data,
	&$shortforecast, &$extforecast, &$advisory)
	{
		$divider = 3; /* default for troublesome forecasts */
		$offby   = 0;

		/**************************************************************************
		* first pass finds forcast lines and marks them in $arr_marks
		*************************************************************************/
		for($i=0; $i<count($file_data);$i++)
		{
			/**********************************************************************
			* some zone reports use '=' on a line by itself in addition to '$$'
			*********************************************************************/
			if(ereg('^=',$file_data[$i]))
			{
				$file_data[$i] = ereg_replace('=','',$file_data[$i]);
			}

			/**********************************************************************
			* look for an advisory or watch
			*********************************************************************/
			if(ereg('(^\.\.\.)',$file_data[$i]) &&
			eregi('(watch|warning|advisory)',$file_data[$i]))
			{
				$advisory = str_replace('.','',$file_data[$i]);
			}

			/**********************************************************************
			* store line numbers beginnging with either '.' or '$$'
			*********************************************************************/
			if(ereg('(^\.([A-Z]|[0-9])|^\\$\\$)',$file_data[$i]))
			{
				$arr_marks[] .= $i;
				if(!ereg('(^\\$\\$)',$file_data[$i]))
				{
					/**************************************************************
					* store the lines except the ending mark
					*************************************************************/
					$arr_forecasts[] = $file_data[$i]; 
				}
			}
		}

		for($i=0;$i<count($arr_forecasts);$i++)
		{
			if(ereg('^\.EXTENDED',$arr_forecasts[$i]))
			{
				$divider = $i;
				$offby   = 1;
			}
		}

		/**************************************************************************
		* now, go back and add the lines in between the days
		*************************************************************************/
		for($i=0;$i<count($arr_marks);$i++)
		{
			$start_line = $arr_marks[$i] + 1;
			$end_line = ($arr_marks[$i + 1] - 1);
			for($n=$start_line; $n<=$end_line; $n++)
			{
				$arr_forecasts[$i] .= $file_data[$n];
			}
		}

		/**************************************************************************
		* at this point we have each forecast in its entirety.
		* generate the Forecast array '$shortforecast'
		*************************************************************************/
		for($i=0;$i<$divider;$i++)
		{
			/**********************************************************************
			* stop at the divider...we only want the forecast
			*********************************************************************/
			list($timeframe,$forecast,$forecast1,$forecast2,$forecast3)
			= explode('...',$arr_forecasts[$i]);

			if($timeframe) { $timeframe = trim($timeframe); }
			if($forecast)  { $forecast  = trim($forecast);  }
			if($forecast1) { $forecast1 = trim($forecast1); }
			if($forecast2) { $forecast2 = trim($forecast2); }
			if($forecast3) { $forecast3 = trim($forecast3); }

			$timeframe = str_replace('.','',$timeframe);

			/**********************************************************************
			* if the snippet doesn't end in a period, we add one to it.
			*********************************************************************/
			if(!$forecast == "" && !ereg('\.$',$forecast))
			{
				$forecast = "$forecast" . ". ";
			}
			if(!$forecast1 == "" && !ereg('\.$',$forecast1))
			{
				$forecast1 = "$forecast1" . ". ";
			}
			if(!$forecast2 == "" && !ereg('\.$',$forecast2))
			{
				$forecast2 = "$forecast2" . ". ";
			}
			if(!$forecast3 == "" && !ereg('\.$',$forecast3))
			{
				$forecast3 = "$forecast3" . ". ";
			}
			$shortforecast["$timeframe"]
			= ("$forecast"."$forecast1"."$forecast2"."$forecast3");
		}

		/**************************************************************************
		* now we generate the extended forecast array '$extforecast'
		*************************************************************************/
		for($i=($divider+$offby);$i<count($arr_forecasts);$i++)
		{
			list($timeframe,$forecast,$forecast1,$forecast2,$forecast3)
			= explode('...',$arr_forecasts[$i]);

			if($timeframe) { $timeframe = trim($timeframe); }
			if($forecast)  { $forecast  = trim($forecast);  }
			if($forecast1) { $forecast1 = trim($forecast1); }
			if($forecast2) { $forecast2 = trim($forecast2); }
			if($forecast3) { $forecast3 = trim($forecast3); }

			$timeframe = str_replace('.','',$timeframe);

			/**********************************************************************
			* if the snippet doesn't end in a period, we add one to it.
			*********************************************************************/
			if(!$forecast == "" && !ereg('\.$',$forecast))
			{
				$forecast = "$forecast" . ". ";
			}
			if(!$forecast1 == "" && !ereg('\.$',$forecast1))
			{
				$forecast1 = "$forecast1" . ". ";
			}
			if(!$forecast2 == "" && !ereg('\.$',$forecast2))
			{
				$forecast2 = "$forecast2" . ". ";
			}
			if(!$forecast3 == "" && !ereg('\.$',$forecast3))
			{
				$forecast3 = "$forecast3" . ". ";
			}
			$extforecast["$timeframe"]
			= ("$forecast"."$forecast1"."$forecast2"."$forecast3");
		}
	}

	/******************************************************************************
	* this function makes the calls to the required phorecast functions
	* to populate the data arrays
	*****************************************************************************/
	function weather_display_phorecast($forecast, &$advisory_c,&$forecast_c, &$extforecast_c)
	{
		/**************************************************************************
		* get the filenames for the forecast data
		*************************************************************************/
		$filename = phorecast_file_name($forecast);

		/**************************************************************************
		* fetch the forecast data
		*************************************************************************/
		$file_data     = phorecast_file_data($filename, FALSE);

		if (is_array($file_data))
		{
			phorecast_forecast($file_data,
				$shortforecast,
				$extforecast,
				$advisory
			);

			/**********************************************************************
			* advisory table
			*********************************************************************/
			if(!empty($advisory))
			{
				$advisory_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
				$advisory_tpl->set_unknowns('remove');
				$advisory_tpl->set_file('advisory', 'table.advisory.tpl');
				$advisory_tpl->set_var(array(
					'advisory_heading' => lang('Advisory'),
					'advisory_body'    => $advisory,
					'th_bg'            => $GLOBALS['phpgw_info']['theme']['th_bg'],
					'th_text'          => $GLOBALS['phpgw_info']['theme']['th_text'],
					'alert_bg'         => 'white',
					'alert_text'       => 'red'
				));
				$advisory_tpl->parse('ADVISORY', 'advisory');
				$advisory_c = $advisory_tpl->get('ADVISORY');
			}

			/**********************************************************************
			* forecast table
			*********************************************************************/
			if (is_array($shortforecast))
			{
				$forecast_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
				$forecast_tpl->set_unknowns('remove');
				$forecast_tpl->set_file(array(
					'forecast'  => 'table.forecast.tpl',
					'list_item' => 'list.forecast.tpl'
				));
				$forecast_tpl->set_var(array(
					'forecast_heading' => lang('Forecast'),
					'th_bg'    => $GLOBALS['phpgw_info']['theme']['th_bg'],
					'th_text'  => $GLOBALS['phpgw_info']['theme']['th_text'],
					'bg_color' => $GLOBALS['phpgw_info']['theme']['bg_color'],
					'bg_text'  => $GLOBALS['phpgw_info']['theme']['bg_text']
				));

				while(list($key,$value) = each($shortforecast))
				{
					$forecast_tpl->set_var(array(
						'forecast_period' => phorecast_fix_first(strtolower($key)),
						'forecast_data'   => phorecast_fix_first(strtolower($value))
					));
					$forecast_tpl->parse(forecast_list, 'list_item', TRUE);
				}
				$forecast_tpl->parse('FORECAST', 'forecast');
				$forecast_c = $forecast_tpl->get('FORECAST');
			}

			/**********************************************************************
			* extended forecast table
			*********************************************************************/
			if (is_array($extforecast))
			{
				$forecast_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
				$forecast_tpl->set_unknowns('remove');
				$forecast_tpl->set_file(array(
					'forecast'  => 'table.forecast.tpl',
					'list_item' => 'list.forecast.tpl'
				));
				$forecast_tpl->set_var(array(
					'forecast_heading' => lang('Extended Forecast'),
					'th_bg'            => $GLOBALS['phpgw_info']['theme']['th_bg'],
					'th_text'          => $GLOBALS['phpgw_info']['theme']['th_text'],
					'bg_color'         => $GLOBALS['phpgw_info']['theme']['bg_color'],
					'bg_text'          => $GLOBALS['phpgw_info']['theme']['bg_text']
				));

				while(list($key,$value) = each($extforecast))
				{
					$forecast_tpl->set_var(array(
						'forecast_period' => phorecast_fix_first(strtolower($key)),
						'forecast_data'   => phorecast_fix_first(strtolower($value))
					));

					$forecast_tpl->parse('forecast_list', 'list_item', TRUE);
				}
				$forecast_tpl->parse('EXTFORECAST', 'forecast');
				$extforecast_c = $forecast_tpl->get('EXTFORECAST');
			}
		}
	}
?>

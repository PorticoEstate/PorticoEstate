<?php	
	/**
	* Project Manager - reportOOo 
	*
	* @author Lars Piepho [lpiepho@probusiness.de]
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id: class.reportOOo.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	* $Source: /sources/phpgroupware/projects/inc/class.reportOOo.inc.php,v $
	*/

	class reportOOo
	{
		var $public_functions = array
		(
			'generate'		=> True,
			'get_projects'	=> True
		);

		var $project_id;
		var $account_id;

		function reportOOo()
		{	
			$this->project_id		= $_REQUEST['project_id'];
			$this->account_id 		= $_REQUEST['account_id'];		
			$this->attached_files	= CreateObject('projects.attached_files');
			$this->doc				= CreateObject('phpgwapi.open_office');

			$this->bohours		= CreateObject('projects.boprojecthours');
			$this->boprojects	= CreateObject('projects.boprojects');
			$this->sohours		= $this->boprojects->sohours;
			$this->contacts		= $this->boprojects->contacts;
			$this->accounts = CreateObject('phpgwapi.accounts');
		}

		function generate($project_id, $sdate, $edate, $hourid, $template, $account_id, $filename)
		{			
			$activities = $this->bohours->get_emp_activities($project_id, $sdate, $edate, $account_id);
			
			for($i=0;$i<count($hourid);$i++)
				{
						$values[$i] = $activities[$hourid[$i]];
				}
			
			//$values = $this->get_projects($project_id, $sdate, $edate, $account_id);
			if($values)
			{				
				switch($template)
				{
					case "Hannover" :
						$doc = 'hannover.sxw';
						break;
					case "Berlin" :
						$doc = 'berlin.sxw';
						break;
					case "Frankfurt" :
						$doc = 'frankfurt.sxw';
						break;
					case "München" :
						$doc = 'muenchen.sxw';
						break;
					case "Hamburg" :
						$doc = 'hamburg.sxw';
						break;
					case "Düsseldorf" :
						$doc = 'duesseldorf.sxw';
						break;
					case "Dresden" :
						$doc = 'berlin.sxw';
						break;
					case "Böblingen" :
						$doc = 'frankfurt.sxw';
						break;
				}
				
				chdir('projects/templates/default');
				$this->doc->loadDocument($doc);
			
				$file = $this->doc->parserFiles["content.xml"];

				$f = fopen($file, "r+b");
				$contents = fread ($f, filesize ($file));

				$lenght = strpos($contents, "<table:table-cell table:style-name=\"Tabelle4.A3\"");
				$rowBegin = $lenght - 17;
    			$part = substr($contents, $rowBegin);
    			$rowEnd = strpos($part, "</table:table-row>") + strlen("</table:table-row>");
    			$rowString = substr($part, 0, $rowEnd);
    			$contentsBegin = substr($contents, 0, $rowBegin);
    			$contentsEnd = substr($part, $rowEnd, strlen($contents));
    			
    			for($i=0;$i<count($values);$i++)
    			{
    				$jobDuration[$i] += $values[$i]['duration'];
    				$sumDuration += $values[$i]['duration'];
    				$sumDrivetime += $values[$i]['drivetime'];
    				$sumDistance += $values[$i]['distance'];
    				if($values[$i]['surcharge'] == '1')
    				{
    					$sur50 = 'x';
    				}
    				elseif($values[$i]['surcharge'] == '2')
    				{
    					$sur50 = '';
    					$sur100 = 'x';
    				}
    				else
    				{
    					$sur50 = '';
    					$sur100 = '';
    				}
    				$row .= '<table:table-row>
    							<table:table-cell table:style-name="Tabelle4.A3" table:value-type="float" table:value="' . ($i+1) . '">
    								<text:p text:style-name="P4">' . ($i+1) . '</text:p>
    							</table:table-cell>
    							<table:table-cell table:style-name="Tabelle4.B3" table:value-type="string">
    								<text:p text:style-name="P6">' . date("d.m.Y", $values[$i]['date']) . '</text:p>
    							</table:table-cell>
    							<table:table-cell table:style-name="Tabelle4.B3" table:value-type="string">
    								<text:p text:style-name="P6">' . date("H:i", $values[$i]['begin']) . '</text:p>
    							</table:table-cell>
    								<table:table-cell table:style-name="Tabelle4.B3" table:value-type="string">
    							<text:p text:style-name="P6">' . date("H:i", $values[$i]['end']) . '</text:p>
    							</table:table-cell>
    							<table:table-cell table:style-name="Tabelle4.B3" table:value-type="string">
    								<text:p text:style-name="P6">' . $this->min2time($values[$i]['duration']) .'</text:p>
    							</table:table-cell>
    								<table:table-cell table:style-name="Tabelle4.A2" table:value-type="string">
    							<text:p text:style-name="P5">[ ' . $sur50 . '<text:s/>] 50% <text:s text:c="2"/>[ ' . $sur100 . '<text:s/>] 100%</text:p>
    								</table:table-cell>
    							<table:table-cell table:style-name="Tabelle4.A2" table:value-type="string">
    								<text:p text:style-name="P6">' . $rate . '</text:p>
    							</table:table-cell>
    							<table:table-cell table:style-name="Tabelle4.H3" table:value-type="time">
    								<text:p text:style-name="P6">' . sprintf("%02d:%02d", floor($values[$i]['drivetime']/60), $values[$i]['drivetime']%60) . '</text:p>
    							</table:table-cell>
    							<table:table-cell table:style-name="Tabelle4.I3" table:value-type="float">
    								<text:p text:style-name="P6">' . str_replace(".",",",$values[$i]['distance']) . '</text:p>
    							</table:table-cell>
    						</table:table-row>';
    				
    				$descRow .= '<table:table-row>
									<table:table-cell table:style-name="Tabelle2.A2" table:value-type="string">
										<text:p text:style-name="P9">' . ($i+1) . '</text:p>
									</table:table-cell>
									<table:table-cell table:style-name="Tabelle2.B1" table:value-type="string">
										<text:p text:style-name="P2">' . htmlspecialchars(utf8_encode($values[$i]['descr'])) . '; ' . htmlspecialchars(utf8_encode($values[$i]['notes'])) . '</text:p>
									</table:table-cell>
									<table:table-cell table:style-name="Tabelle2.B1" table:value-type="string">
										<text:p text:style-name="P9"/>
									</table:table-cell>
									<table:table-cell table:style-name="Tabelle2.D2" table:value-type="string">
										<text:p text:style-name="P9">' . $this->min2time($values[$i]['duration']) . '</text:p>
									</table:table-cell>
								</table:table-row>';
						
					$rows=($i+1);
    			}
				
				$contentsEnd = str_replace('table:formula="SUMME(&lt;Tabelle4.E3&gt;)"' , 'table:formula="SUMME(&lt;Tabelle4.E3:E' . utf8_encode($rows + 2) . '&gt;)"' , $contentsEnd);
				$contentsEnd = str_replace('table:formula="SUMME(&lt;Tabelle4.H3&gt;)"' , 'table:formula="SUMME(&lt;Tabelle4.H3:H' . utf8_encode($rows + 2) . '&gt;)"' , $contentsEnd);
				$contentsEnd = str_replace('table:formula="SUMME(&lt;Tabelle4.I3&gt;)"' , 'table:formula="SUMME(&lt;Tabelle4.I3:I' . utf8_encode($rows + 2) . '&gt;)"' , $contentsEnd);
				$contentsEnd = str_replace('table:formula="SUMME(&lt;Tabelle2.D2&gt;)"' , 'table:formula="SUMME(&lt;Tabelle2.D2:D' . utf8_encode($rows + 1) . '&gt;)"' , $contentsEnd);
				
				$lenght = strpos($contentsEnd, '<table:table-cell table:style-name="Tabelle2.A2" table:value-type="string">');
				$rowBegin = $lenght - 17;
				$part = substr($contentsEnd, $rowBegin);
				$rowEnd = strpos($part, "</table:table-row>") + strlen("</table:table-row>");
				$rowString = substr($part, 0, $rowEnd);
				$contents2Begin = substr($contentsEnd, 0, $rowBegin);
				$contents2End = substr($part, $rowEnd, strlen($contentsEnd));
    			
				$contentsEnd = $contents2Begin . $descRow . $contents2End;				
				$newContents = $contentsBegin . $row . $contentsEnd; 

		
				rewind($f);
				fwrite($f, $newContents);
				fclose($f);

				$project = $this->boprojects->read_single_project($project_id);
				$address = $this->contacts->get_addr_contact_data($project['customer_org']);

   			$vars = array(
    				'KUNDE'		=> $project['customerorgout'],
    				'ANSPRP'	=> $project['customerout'],
    				'STRASSE'	=> $address[0]['addr_add1'],
    				'ORT'		=> $address[0]['addr_postal_code'] . " " . $address[0]['addr_city'],
    				'AUFTRAG'	=> $project['number'],
    				'KDNR'		=> $project['customer_nr'],
    				'CONSULT'	=> $this->accounts->id2name($account_id),
    				'INVEST'	=> $project['investment_nr']
   			);
   			$this->doc->parse($vars);

   			$sstring = date("Ymd", $sdate);
   			$estring = date("Ymd", $edate);

   			//$saveFilename = "TB_" . $this->accounts->id2name($account_id) . "_" . $sstring . "-" . $estring . ".sxw";
   			$saveFilename = $filename . ".sxw";
				$source = stripslashes($GLOBALS['phpgw_info']['server']['temp_dir']). "/$saveFilename";
   			$this->doc->savefile($source);
  			$this->doc->clean();

				$testfile = "projects/" . $project_id . "/" . $saveFilename;
				$exists = $this->attached_files->file_exists (array (
															'string' => $testfile,
															'relatives' => array (RELATIVE_ROOT)));
				$details = array(
								'comment' => $sdate . ';' . $edate,
								'owner_id' => $account_id
								);
				
				$this->attached_files->save_file($project_id, $source, ($exists?$filename . "_01.sxw":$saveFilename), $details);
				unlink($source);

 	 			return True;
			}
			else
			{
				return False;
			}

		}
		
		function get_projects($project_id, $sdate, $edate, $account_id)
		{
			$params = array(
					'project_id' => $project_id,
					'filter' => 'employee',
					'status' => 'all',
					'limit' => false,
					'order' => 'end_date',
					'employee' => $account_id
				);

			$subs = $this->boprojects->get_sub_projects($params);
			$x = 0;
			
			for($i=0;$i<=(count($subs));$i++)
			{
				$values_hours = array(
					'project_id' => $subs[$i]['project_id'],
					'filter' => 'employee',
					'action' => 'all',
					'limit' => false,
					'order' => 'end_date',
					'employee' => $account_id
				);
				$hours[$i] = $this->sohours->read_hours($values_hours);
									
				for($j=0;$j<=(count($hours[$i]));$j++)
				{	
					if(($hours[$i][$j]['sdate'] >= $sdate) && ($hours[$i][$j]['edate'] <= $edate) && ($hours[$i][$j]['billable'] == 'Y'))
					{
						$values[$x] = array(
							'date'		=> date("d.m.Y", $hours[$i][$j]['sdate']),
							'begin'		=> date("H:i", $hours[$i][$j]['sdate']),
							'end'		=> date("H:i", $hours[$i][$j]['edate']),
							'duration'	=> $hours[$i][$j]['minutes'],
							'drivetime'	=> $hours[$i][$j]['t_journey'],
							'distance'	=> $hours[$i][$j]['km_distance'],
							'descr'		=> $hours[$i][$j]['hours_descr'],
							'notes'		=> $hours[$i][$j]['remark'],
							'surcharge' => $hours[$i][$j]['surcharge']);
						$x++;
					}
				}
			
			}

			return $values;

		}
		
		function min2time($string)
		{
			$time = ((int)($string / 60)) . ":" . (($string % 60) == "0" ? "00" : sprintf("%02d",($string % 60)));
			
			return $time;
		}
		
		
	}
?>

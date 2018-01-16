<?php
  /**************************************************************************\
  * phpGroupWare - addressbook                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Joseph Engo <jengo@phpgroupware.org>                          *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class boXport
	{
		var $public_functions = array(
			'import' => True,
			'export' => True
		);

		var $so;
		var $contacts;

		var $start;
		var $limit;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $cat_id;

		var $use_session = False;

		function __construct($session=False)
		{
			$this->contacts = $GLOBALS['phpgw']->contacts;
			$this->so = CreateObject('addressbook.soaddressbook');
			if($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}
			global $start,$limit,$query,$sort,$order,$filter,$cat_id;

			if($start || $start == 0)  { $this->start = $start; }
			if($limit)  { $this->limit  = $limit;  }
			if($query)  { $this->query  = $query;  }
			if($sort)   { $this->sort   = $sort;   }
			if($order)  { $this->order  = $order;  }
			if($filter) { $this->filter = $filter; }
			$this->cat_id = $cat_id;
		}

		function save_sessiondata()
		{
			global $start,$limit,$query,$sort,$order,$filter,$cat_id;

			if ($this->use_session)
			{
				$data = array(
					'start'  => $start,
					'limit'  => $limit,
					'query'  => $query,
					'sort'   => $sort,
					'order'  => $order,
					'filter' => $filter,
					'cat_id' => $cat_id
				);
				if($this->debug) { echo '<br />Save:'; _debug_array($data); }
				$GLOBALS['phpgw']->session->appsession('session_data','addressbook',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','addressbook');
			if($this->debug) { echo '<br />Read:'; _debug_array($data); }

			$this->start  = $data['start'];
			$this->limit  = $data['limit'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
			$this->filter = $data['filter'];
			$this->cat_id = $data['cat_id'];
		}

		function import($tsvfile,$conv_type,$private,$fcat_id)
		{
			include (PHPGW_APP_INC . '/import/' . $conv_type);

			if ($private == '') { $private = 'public'; }
			$row = 0;
			$buffer = array();
			$contacts = new import_conv;

			$buffer = $contacts->import_start_file($buffer);
			$fp = fopen($tsvfile,'r');
			if ($contacts->type == 'csv')
			{
				while ($data = fgetcsv($fp,8000,','))
				{
					$num = count($data);
					$row++;
					if ($row == 1)
					{
						$header = $data;
						/* Changed here to ignore the header, set to our array
						while(list($lhs,$rhs) = each($contacts->import))
						{
							$header[] = $lhs;
						}
						*/
					}
					else
					{
						$buffer = $contacts->import_start_record($buffer);
						for ($c=0; $c<$num; $c++ )
						{
							//Send name/value pairs along with the buffer
							if ($contacts->import[$header[$c]] != '' && $data[$c] != '')
							{
								$buffer = $contacts->import_new_attrib($buffer, $contacts->import[$header[$c]],$data[$c]);
							}
						}
						$buffer = $contacts->import_end_record($buffer,$private);
					}
				}
			}
			elseif ($contacts->type == 'ldif')
			{
				while ($data = fgets($fp,8000))
				{
					$url = "";
					list($name,$value,$extra) = preg_split('/:/', $data);
					if (substr($name,0,2) == 'dn')
					{
						$buffer = $contacts->import_start_record($buffer);
					}
					
					$test = trim($value);
					if ($name && !empty($test) && $extra)
					{
						// Probable url string
						$url = $test;
						$value = $extra;
					}
					elseif ($name && empty($test) && $extra)
					{
						// Probable multiline encoding
						$newval = base64_decode(trim($extra));
						$value = $newval;
						//echo $name.':'.$value;
					}

					if ($name && $value)
					{
						$test = preg_split('/,mail=/',$value);
						if ($test[1])
						{
							$name = "mail";
							$value = $test[1];
						}
						if ($url)
						{
							$name = "homeurl";
							$value = $url. ':' . $value;
						}
						//echo '<br />'.$j.': '.$name.' => '.$value;
						if ($contacts->import[$name] != '' && $value != '')
						{
							$buffer = $contacts->import_new_attrib($buffer, $contacts->import[$name],$value);
						}
					}
					else
					{
						$buffer = $contacts->import_end_record($buffer,$private);
					}
				}
			}
			else
			{
				while ($data = fgets($fp,8000))
				{
					$data = trim($data);
					// RB 2001/05/07 added for Lotus Organizer
					while (substr($data,-1) == '=')
					{
						// '=' at end-of-line --> line to be continued with next line
						$data = substr($data,0,-1) . trim(fgets($fp,8000));
					}
					if (strstr($data, 'BEGIN:VCARD'))
					{
						// added for p800 vcards: problem if vcard starts with "<![CDATA["
						$data = strstr($data, 'BEGIN:VCARD');
					}
					list($name,$value) = explode(':', $data,2); // RB 2001/05/09 to allow ':' in Values (not only in URL's)

					if (strtolower(substr($name,0,5)) == 'begin')
					{
						$buffer = $contacts->import_start_record($buffer);
					}
					elseif (strtolower(substr($name, 0, 3)) == 'end')
					{
						$buffer = $contacts->import_end_record($buffer);
					}
					elseif ($name && $value)
					{
						//reset($contacts->import);
						//while ( list($fname,$fvalue) = each($contacts->import) )
                                                if (is_array($contacts->import))
                                                {
                                                    foreach($contacts->import as $fname => $fvalue)
                                                    {
							if ( strstr(strtolower($name), $contacts->import[$fname]) )
							{
								$buffer = $contacts->import_new_attrib($buffer,$name,$value);
							}
                                                    }
                                                }
					}
				}
			}

			fclose($fp);
			/* Delete the temp file. */
			unlink($tsvfile);

			$buffer = $contacts->import_end_file($buffer,$private,$fcat_id);
			return $buffer;
		}

		function export($conv_type,$cat_id='',$both_types='',$sub_cats='')
		{
			include (PHPGW_APP_INC . '/export/' . $conv_type);
			
			$buffer=array();
			$contacts = new export_conv;
			
			// Note our use of ===.  Simply == would not work as expected
			if( strpos($conv_type, 'OpenOffice') === false )
			{
				// Read in user custom fields, if any
				$customfields = array();
				//while (list($col,$descr) = @each($GLOBALS['phpgw_info']['user']['preferences']['addressbook']))
                                if (is_array($GLOBALS['phpgw_info']['user']['preferences']['addressbook']))
                                {
                                    foreach($GLOBALS['phpgw_info']['user']['preferences']['addressbook'] as $col => $descr)
                                    {
                                        if ( substr($col,0,6) == 'extra_' )
					{
						$field = preg_replace('/extra_/','',$col);
							$field = preg_replace('/ /','_',$field);
						$customfields[$field] = ucfirst($field);
					}
                                    }
                                }

				if (!empty($cat_id))
				{
					$buffer = $contacts->export_start_file($buffer,$cat_id);
				}
				else
				{
					$buffer = $contacts->export_start_file($buffer);
				}

				for ($i=0;$i<count($contacts->ids);$i++)
				{
					$buffer = $contacts->export_start_record($buffer);
					//while( list($name,$value) = each($contacts->currentrecord) )
                                        if (is_array($contacts->currentrecord))
                                        {
                                            foreach($contacts->currentrecord as $name => $value)
                                            {
						$buffer = $contacts->export_new_attrib($buffer,$name,$value);
                                            }
                                        }
					$buffer = $contacts->export_end_record($buffer);
				}

				// Here, buffer becomes a string suitable for printing
				$buffer = $contacts->export_end_file($buffer);

				$tsvfilename = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$tsvfilename}";

			}
			else // this is the openoffice section
			{
				//echo "<pre>here1  $both_types $sub_cats</pre>";
				$tsvfilename = $contacts->do_it($both_types,$sub_cats);
				$buffer = $tsvfilename;
			}
			
			return $buffer;
		}
	}
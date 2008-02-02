<?php
  /**************************************************************************\
  * phpGroupWare - news headlines                                            *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * Based on pheadlines 0.1 19991104 by Dan Steinman <dan@dansteinman.com>   *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	class headlines
	{
		// socket timeout in seconds
		var $current_time;
		var $db;

		var $con       = 0;
		var $display   = '';
		var $base_url  = '';
		var $newsfile  = '';
		var $lastread  = 0;
		var $newstype  = '';
		var $cachetime = 0;
		var $listings  = 0;
		var $error_timeout = False;

		// wired news was messing up, I dunno
		// "wired" => array("Wired&nbsp;News","http://www.wired.com","/news_drop/netcenter/netcenter.rdf/","rdf"),

		function headlines()
		{
			$GLOBALS['phpgw']->network = CreateObject('phpgwapi.network',False);
		}

		// try to get the links for the site
		function getLinks($site)
		{
			if(!$this->readtable($site))
			{
				return $links;
			}

			if($this->isCached())
			{
				$links = $this->getLinksDB();
			}
			else
			{
				$links = $this->getLinksSite();

				if($links)
				{
					$this->saveToDB($links);
				}
				else
				{
					$links = $this->getLinksDB();
					$this->error_timeout = True;
				}
			}
			return $links;
		}

		// do a quick read of the table
		function readtable($site)
		{
			$GLOBALS['phpgw']->db->query("SELECT con,display,base_url,newsfile,lastread,newstype,"
                    . "cachetime,listings FROM phpgw_headlines_sites WHERE con=$site",__LINE__,__FILE__);
			if(!$GLOBALS['phpgw']->db->num_rows())
			{
				return False;
			}
			$GLOBALS['phpgw']->db->next_record();

			$this->con       = $GLOBALS['phpgw']->db->f(0);
			$this->display   = $GLOBALS['phpgw']->db->f(1);
			$this->base_url  = $GLOBALS['phpgw']->db->f(2);
			$this->newsfile  = $GLOBALS['phpgw']->db->f(3);
			$this->lastread  = $GLOBALS['phpgw']->db->f(4);
			$this->newstype  = $GLOBALS['phpgw']->db->f(5);
			$this->cachetime = $GLOBALS['phpgw']->db->f(6);
			$this->listings  = $GLOBALS['phpgw']->db->f(7);

			return True;
		}

		// determines if the headlines were cached less than $cachetime minutes ago
		function isCached()
		{
			$this->current_time = time();
			return (($this->current_time - $this->lastread) < ($this->cachetime * 60));
		}

		// get the links from the database
		function getLinksDB()
		{
			$GLOBALS['phpgw']->db->query("SELECT title, link FROM phpgw_headlines_cached WHERE site = ".$this->con);

			if(!$GLOBALS['phpgw']->db->num_rows())
			{
				$links = $this->getLinksSite();  // try from site again
				if(!$links)
				{
					$display = htmlspecialchars($this->display);
//					die("</table><b>error</b>: unable to get links for <br><a href=\""
//						. "$this->base_url\">$this->display</a>");
					return False;
				}
			}

			while($GLOBALS['phpgw']->db->next_record())
			{
				$links[$GLOBALS['phpgw']->db->f('title')] = $GLOBALS['phpgw']->db->f('link');
			}
			return $links;
		}

		// get a new set of links from the site
		function getLinksSite()
		{
			// get the file that contains the links
			$data = $GLOBALS['phpgw']->network->gethttpsocketfile($this->base_url.$this->newsfile,True);
			if(!$data)
			{
				return False;
			}

			switch($this->newstype)
			{
				case 'rdf':
				case 'fm':
					$simple = True;
					break;
				case 'lt':
					$data = @ereg_replace('<story>','<item>',$data);
					$data = @ereg_replace('</story>','</item>',$data);
					$data = @ereg_replace('<url>','<link>',$data);
					$data = @ereg_replace('</url>','</link>',$data);
					$simple = True;
					break;
				default: 
					$simple = False;
			}

			$rss = CreateObject('headlines.rss',$data,$simple);
			$allItems = $rss->getAllItems();

			$i = 1;
			$links = array();
			while(list($key,$val) = @each($allItems))
			{
				if($i == $this->listings)
				{
					break;
				}
				$i++;
				$links[$key] = $val;
			}

			return $links;
		}

		// get a list of the sites
		function getList($dropall=False)
		{
			@set_time_limit(0);

			// determine the options to properly extract the links
			$startat = '</image>';
			$linkstr = 'link';
			$exclude = '';

			// get the file that contains the links
			//$lines = $GLOBALS['phpgw']->network->gethttpsocketfile("http://blinkylight.com/headlines.rdf");
			$lines = $GLOBALS['phpgw']->network->gethttpsocketfile("http://www.phpgroupware.org/headlines.rdf");
			if(!$lines)
			{
				return False;
			}

			$startnum = 0;

			// determine which line to begin grabbing the links
			for($i=0;$i<count($lines);$i++)
			{
				if(ereg($startat,$lines[$i],$regs))
				{
					$startnum = $i;
					break;
				}
			}

			// extract the links and assemble into array $links
			$links = array();
			for($i=$startnum,$j=0;$i<count($lines);$i++)
			{
				if(ereg("<title>(.*)</title>",$lines[$i],$regs))
				{
					if($regs[1] == $exclude)
					{
						$i+=1;
						break;
					}
					$title[$j] = $regs[1];
					$title[$j] = ereg_replace("&amp;apos;","'",$title[$j]);
				}
				elseif(ereg("<$linkstr>(.*)</$linkstr>",$lines[$i],$regs))
				{
					$links[$j] = $regs[1];
				}
				elseif(ereg("<description>(.*)</description>",$lines[$i],$regs))
				{
					$type[$j] = $regs[1];
					$j++;
				}
			}

			$GLOBALS['phpgw']->db->transaction_begin();
			if($dropall)
			{
				$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_headlines_sites",__LINE__,__FILE__);
			}

			for($i=0;$i<count($title);$i++)
			{
				$server = str_replace('http://','',$links[$i]);
				$file   = strstr($server,'/');
				$server = 'http://' . str_replace($file,'',$server);

				$GLOBALS['phpgw']->db->query("SELECT con,display,base_url,newsfile,newstype "
					. "FROM phpgw_headlines_sites WHERE display='".$title[$i]."' AND "
					. "base_url='$server' AND newsfile='$file'",__LINE__,__FILE__);
				if($GLOBALS['phpgw']->db->num_rows() == 0)
				{
					$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,"
						."newstype,lastread,cachetime,listings) VALUES("
						."'".$title[$i]."','$server','$file','".$type[$i]."',0,60,20)",__LINE__,__FILE__);
					continue;
				}
				$GLOBALS['phpgw']->db->next_record();

				if($GLOBALS['phpgw']->db->f('newstype') <> $type[$i])
				{
					$GLOBALS['phpgw']->db->query("UPDATE phpgw_headlines_sites SET newstype='".$type[$i]."' WHERE con=".$this->db->f('con'),__LINE__,__FILE__);
				}
			}
			$GLOBALS['phpgw']->db->transaction_commit();
		}

		// save the new set of links and update the cache time
		function saveToDB($links)
		{
			$GLOBALS['phpgw']->db->query("DELETE FROM phpgw_headlines_cached WHERE site='" . $this->con . "'",__LINE__,__FILE__);

			// save links
			while(list($title,$link) = @each($links))
			{
				$link  = addslashes($link);
				$title = addslashes($title);
				$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_headlines_cached VALUES("
					.$this->con.",'$title','$link')",__LINE__,__FILE__);
			}

			// save cache time
			$GLOBALS['phpgw']->db->query("UPDATE phpgw_headlines_sites SET lastread = '" . $this->current_time
				. "' WHERE con='" . $this->con . "'",__LINE__,__FILE__);
		}
	}
?>

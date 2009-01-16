<?php
  /*******************************************************************
  * $Id$
  *
  * class.RSS.php3
  * Version: 0.91 (natch!)
  * Author: Joseph Harris (CDI)
  * Copyright (C) 2001, Joseph Harris
  * cdi@thewebmasters.net
  * http://www.thewebmasters.net/
  *
  *******************************************************************
  This program is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published by the
  Free Software Foundation; either version 2 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License along
  with this program; if not, write to the Free Software Foundation, Inc.,
  59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
  *******************************************************************
  *
  * I use a tab stop of (4) in my editor, so this file may look weird
  * if you have your tab stop set differently.
  *
  * This class will completely parse RSS 0.91 compliant data.
  * Reference the 'rss-0.91.dtd' included with this distribution
  * or visit 'http://my.netscape.com/publish/formats/rss-0.91.dtd'
  * or 'http://www.webreference.com/authoring/languages/xml/rss/1/'
  *
  * Requires: PHP4 w/PCRE support
  *
  * Basic usage is extremely simple:
  *
  *      $rss = new RSS ($data);
  *
  *      // The call to 'new' results in the data being parsed.
  *  // Data needs to be raw RSS data already obtained from a file or URL.
  *      // Data needs to be one big string, no pre-processing of the data is needed.
  *
  *      $allItems = $rss->getAllItems();
  *      $itemCount = count($allItems);
  *      for($y=0;$y<$itemCount;$y++) {
  *              print "\nItem [$y] has data\n";
  *              print "[$y]: Title: " . $allItems[$y]['TITLE'];
  *              print "\n[$y]: Link : " . $allItems[$y]['LINK'];
  *              print "\n[$y]: Desc : " . $allItems[$y]['DESCRIPTION'];
  *      }
  *
  */

	class rss
	{
		var $CHANNELS           = array();      // Array, holds individual channel data
		var $CHANNELINFO        = array();      // Array that holds NON-ITEM channel data

		var $COUNT              = 0;            // Number of channels found

		function rss($data='', $simple=0)
		{
			if($simple)
			{
				// Ignore channel information, just grab <items>. Useful for
				// RDF files, rss-0.9-simple and non-compliant RSS

				$temp = array();
				$temp[0][0] = $data;
				$this->COUNT = 1;
				$this->parseItems($temp);
			}
			else
			{
				$this->assignDATA($data);
			}
		}

		/*
		 * void error ( string msg )
		 */
		function error($msg='')
		{
			print "<H3>Error: [$msg]</H3>\n";
			return;
		}

		/*
		 * int getCount ( void )
		 *  returns the number of channels parsed or 0 if none found
		 */
		function getCount()
		{
			return $this->COUNT;
		}

		/*
		 * array getChannel ( int channelID )
		 */
		function getChannel($channelID)
		{
			return $this->CHANNELS[$channelID];
		}

		/*
		 * array getChannelInfo ( int channelID )
		 */
		function getChannelInfo($channelID)
		{
			return $this->CHANNELINFO[$channelID];
		}

		/*
		 * int itemCount ( int channelID )
		 */
		function itemCount($channelID)
		{
			return count($this->CHANNELS[$channelID]['ITEMS']);
		}

		/*
		 * array getItems ( int channelID )
		 */
		function getItems($channelID)
		{
			return $this->CHANNELS[$channelID]['ITEMS'];
		}

		/*
		 * array getAllItems ( void )
		 */
		function getAllItems()
		{
			$count = $this->getCount();
			$ticker=0;
			$allItems = array();

			for ($x=0;$x<$count;$x++)
			{
				$itemCount = $this->itemCount($x);
				$itemData = $this->getItems($x);
				for($y=0;$y<$itemCount;$y++)
				{
					$allItems[$itemData[$y]['TITLE']] = $itemData[$y]['LINK'];
					/*
					$allItems[$ticker]['TITLE'] = $itemData[$y]['TITLE'];
					$allItems[$ticker]['LINK'] = $itemData[$y]['LINK'];
					$allItems[$ticker]['DESCRIPTION'] = $itemData[$y]['DESCRIPTION'];
					*/
					$ticker++;
				}
			}
			return $allItems;
		}

		/*
		 * void assignData ( string data )
		 */
		function assignDATA($data='')
		{
			if(empty($data))
			{
				$this->error('No RSS data submitted');
			}
			else
			{
				$this->parse($data);
			}
			return;
		}

		/*
		 * array parseChannels (string data )
		 */
		function parseChannels($data='')
		{
//			$channelCount = preg_match_all("|<channel>(.*)</channel>|iUs",$data,$channels,PREG_SET_ORDER);
			$channelCount = preg_match_all("|<channel (.*?)>(.*)</channel>|iUs",$data,$channels,PREG_SET_ORDER);

			if(!$channelCount)
			{
				$this->error('No channels in RSS data');
				return;
			}
			else
			{
				$this->COUNT = $channelCount;
			}
			return $channels[0];
		}

		/*
		 * void storeItems ( string itemData, int channelID, int itemID )
		 */
		function storeItems($itemData='',$channelID,$itemID)
		{
			if(preg_match_all("|<title>(.+)</title>|iUs",$itemData,$match,PREG_SET_ORDER))
			{
				$title = $match[0][1];
				$this->CHANNELS[$channelID]['ITEMS'][$itemID]['TITLE'] = "$title";
			}
			else
			{
				$this->CHANNELS[$channelID]['ITEMS'][$itemID]['TITLE'] = "";
			}
			if(preg_match_all("|<link>(.+)</link>|iUs",$itemData,$match,PREG_SET_ORDER))
			{
				$link = $match[0][1];
				$this->CHANNELS[$channelID]['ITEMS'][$itemID]['LINK'] = "$link";
			}
			else
			{
				$this->CHANNELS[$channelID]['ITEMS'][$itemID]['LINK'] = "";
			}
			if(preg_match_all("|<description>(.+)</description>|iUs",$itemData,$match,PREG_SET_ORDER))
			{
				$desc = $match[0][1];
				$this->CHANNELS[$channelID]['ITEMS'][$itemID]['DESCRIPTION'] = "$desc";
			}
			else
			{
				$this->CHANNELS[$channelID]['ITEMS'][$itemID]['DESCRIPTION'] = "";
			}
			return;
		}

		/*
		 * void storeChannelData ( string data, int channelID )
		 */
		function storeChannelData($data='',$channelID)
		{
			$data = str_replace("<channel>",'',$data);
			$data = str_replace("</channel>",'',$data);
			$lines = split("\n",$data);
			while(list($key, $line) = each($lines))
			{
				$line = trim($line);
				if(!empty($line))
				{
					if(preg_match("|<([^>]+)>(.*)</\\1>|U",$line,$matches))
					{
						$tagName = $matches[1];
						$tagVal  = $matches[2];
						$this->CHANNELS[$channelID][$tagName] = $tagVal;
						$this->CHANNELINFO[$channelID][$tagName] = $tagVal;
					}
				}
			}
			return;
		}

		/*
		 * void parseItems ( array channels )
		 */
		function parseItems($channels)
		{
			$channelCount = count($channels);
			if(!$channelCount)
			{
				$this->error("Could not locate any channel data to parse");
				exit;
			}
			for($x=0;$x<$channelCount;$x++)
			{
				$channelData = $channels[$x][0];
				$leftOvers = $channelData;
				$itemCount = preg_match_all("|<item(.*)>(.*)</item>|iUs",$channelData,$items,PREG_SET_ORDER);
				if($itemCount)
				{
					for($y=0;$y<$itemCount;$y++)
					{
						$itemData = $items[$y][0];
						$leftOvers = str_replace("$itemData","",$leftOvers);
						$this->storeItems($itemData,$x,$y);
					}
				}
				$this->storeChannelData($leftOvers,$x);
			}
			return;
		}

		/*
		 * void parse ( string data )
		 */
		function parse($data='')
		{
			$channels = $this->parseChannels($data);
			if(empty($channels))
			{
				return;
			}
			$this->parseItems($channels);
			return;
		}
	}
?>

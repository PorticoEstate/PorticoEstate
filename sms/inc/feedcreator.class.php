<?php
	/*	 * *************************************************************************

	  FeedCreator class v1.4
	  originally (c) Kai Blankenhorn
	  www.bitfolge.de
	  kaib@bitfolge.de
	  v1.3 work by Scott Reynen (scott@randomchaos.com) and Kai Blankenhorn

	  This program is free software; you can redistribute it and/or
	  modify it under the terms of the GNU General Public License
	  as published by the Free Software Foundation; either version 2
	  of the License, or (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details: <http://www.gnu.org/licenses/gpl.txt>

	 * ***************************************************************************


	  Changelog:

	  v1.4	11-11-03
	  optional feed saving and caching
	  improved documentation
	  minor improvements

	  v1.3    10-02-03
	  renamed to FeedCreator, as it not only creates RSS anymore
	  added support for mbox
	  tentative support for echo/necho/atom/pie/???

	  v1.2    07-20-03
	  intelligent auto-truncating of RSS 0.91 attributes
	  don't create some attributes when they're not set
	  documentation improved
	  fixed a real and a possible bug with date conversions
	  code cleanup

	  v1.1    06-29-03
	  added images to feeds
	  now includes most RSS 0.91 attributes
	  added RSS 2.0 feeds

	  v1.0    06-24-03
	  initial release



	 * ************************************************************************* */

	/**
	 * A FeedItem is a part of a FeedCreator feed.
	 *
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 * @version 1.3
	 */
	class FeedItem
	{

		/**
		 * Mandatory attributes of an item.
		 */
		var $title, $description, $link;

		/**
		 * Optional attributes of an item.
		 */
		var $author, $image, $category, $comments, $guid;

		/**
		 * Publishing date of an item. May be in one of the following formats:
		 *
		 * 	RFC 822:
		 * 	"Mon, 20 Jan 03 18:05:41 +0400"
		 * 	"20 Jan 03 18:05:41 +0000"
		 *
		 * 	ISO 8601:
		 * 	"2003-01-20T18:05:41+04:00"
		 *
		 * 	Unix:
		 * 	1043082341
		 */
		var $date;

		// on hold
		// var $source;
	}

	/**
	 * An FeedImage may be added to a FeedCreator feed.
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 * @version 1.3
	 */
	class FeedImage
	{

		/**
		 * Mandatory attributes of an image.
		 */
		var $title, $url, $link;

		/**
		 * Optional attributes of an image.
		 */
		var $width, $height, $description;

	}

	/**
	 * FeedDate is an internal class that stores a date for a feed or feed item.
	 * Usually, you won't need to use this.
	 */
	class FeedDate
	{

		var $unix;

		/**
		 * Creates a new instance of FeedDate representing a given date.
		 * Accepts RFC 822, ISO 8601 date formats as well as unix time stamps.
		 * @param mixed $dateString optional the date this FeedDate will represent. If not specified, the current date and time is used.
		 */
		function FeedDate( $dateString = "" )
		{
			if ($dateString == "")
				$dateString = date("r");

			if (is_integer($dateString))
			{
				$this->unix = $dateString;
				return;
			}
			if (preg_match("~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~", $dateString, $matches))
			{
				$months = Array("Jan" => 1, "Feb" => 2, "Mar" => 3, "Apr" => 4, "May" => 5, "Jun" => 6,
					"Jul" => 7, "Aug" => 8, "Sep" => 9, "Oct" => 10, "Nov" => 11, "Dec" => 12);
				$this->unix = mktime($matches[4], $matches[5], $matches[6], $months[$matches[2]], $matches[1], $matches[3]);
				if (substr($matches[7], 0, 1) == '+' OR substr($matches[7], 0, 1) == '-')
				{
					$tzOffset = (substr($matches[7], 0, 3) * 60 + substr($matches[7], -2)) * 60;
				}
				else
				{
					if (strlen($matches[7]) == 1)
					{
						$oneHour = 3600;
						$ord = ord($matches[7]);
						if ($ord < ord("M"))
						{
							$tzOffset = (ord("A") - $ord - 1) * $oneHour;
						}
						elseif ($ord >= ord("M") AND $matches[7] != "Z")
						{
							$tzOffset = ($ord - ord("M")) * $oneHour;
						}
						elseif ($matches[7] == "Z")
						{
							$tzOffset = 0;
						}
					}
					switch ($matches[7])
					{
						case "UT":
						case "GMT": $tzOffset = 0;
					}
				}
				$this->unix += $tzOffset;
				return;
			}
			if (preg_match("~(\\d{4})-(\\d{2})-(\\d{2})T(\\d{2}):(\\d{2}):(\\d{2})(.*)~", $dateString, $matches))
			{
				$this->unix = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
				if (substr($matches[7], 0, 1) == '+' OR substr($matches[7], 0, 1) == '-')
				{
					$tzOffset = (substr($matches[7], 0, 3) * 60 + substr($matches[7], -2)) * 60;
				}
				else
				{
					if ($matches[7] == "Z")
					{
						$tzOffset = 0;
					}
				}
				$this->unix += $tzOffset;
				return;
			}
			$this->unix = 0;
		}

		/**
		 * Gets the date stored in this FeedDate as an RFC 822 date.
		 *
		 * @return a date in RFC 822 format
		 */
		function rfc822()
		{
			return gmdate("r", $this->unix);
		}

		/**
		 * Gets the date stored in this FeedDate as an ISO 8601 date.
		 *
		 * @return a date in ISO 8601 format
		 */
		function iso8601()
		{
			$date = gmdate("Y-m-d\TH:i:sO", $this->unix);
			$date = substr($date, 0, 22) . ':' . substr($date, -2);
			return $date;
		}

		/**
		 * Gets the date stored in this FeedDate as unix time stamp.
		 *
		 * @return a date as a unix time stamp
		 */
		function unix()
		{
			return $this->unix;
		}
	}

	/**
	 * Feed is the abstract base implementation for concrete
	 * implementations that implement a specific format of syndication.
	 *
	 * @abstract
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 * @version 1.4
	 */
	class FeedCreator
	{

		/**
		 * Mandatory attributes of a feed.
		 */
		var $title, $description, $link;

		/**
		 * Optional attributes of a feed.
		 */
		var $image = null;
		var $SyndicationURL, $image, $language, $copyright, $pubDate, $lastBuildDate, $editor, $webmaster, $category, $docs, $ttl, $rating, $skipHours, $skipDays;

		/**
		 * @access private
		 */
		var $items = Array();

		/**
		 * version information string, do not modify
		 * @access private
		 */
		var $generatorVersion = "FeedCreator 1.4";

		/**
		 * This feed's MIME content type.
		 * @since 1.4
		 * @access private
		 */
		var $contentType = "text/xml";

		/**
		 * Adds an FeedItem to the feed.
		 *
		 * @param object FeedItem $item The FeedItem to add to the feed.
		 * @access public
		 */
		function addItem( $item )
		{
			$this->items[] = $item;
		}

		/**
		 * Truncates a string to a certain length at the most sensible point.
		 * First, if there's a '.' character near the end of the string, the string is truncated after this character.
		 * If there is no '.', the string is truncated after the last ' ' character.
		 * If the string is truncated, " ..." is appended.
		 * If the string is already shorter than $length, it is returned unchanged.
		 *
		 * @static
		 * @param string    string A string to be truncated.
		 * @param int        length the maximum length the string should be truncated to
		 * @return string    the truncated string
		 */
		function iTrunc( $string, $length )
		{
			if (strlen($string) <= $length)
			{
				return $string;
			}

			$pos = strrpos($string, ".");
			if ($pos >= $length - 4)
			{
				$string = substr($string, 0, $length - 4);
				$pos = strrpos($string, ".");
			}
			if ($pos >= $length * 0.4)
			{
				return substr($string, 0, $pos + 1) . " ...";
			}

			$pos = strrpos($string, " ");
			if ($pos >= $length - 4)
			{
				$string = substr($string, 0, $length - 4);
				$pos = strrpos($string, " ");
			}
			if ($pos >= $length * 0.4)
			{
				return substr($string, 0, $pos) . " ...";
			}

			return substr($string, 0, $length - 4) . " ...";
		}

		/**
		 * Builds the feed's text.
		 * @abstract
		 * @return    string    the feed's complete text
		 */
		function createFeed()
		{
			
		}

		/**
		 * Generate a filename for the feed cache file. The result will be $PHP_SELF with the extension changed to .xml.
		 * For example:
		 *
		 * echo $PHP_SELF."\n";
		 * echo FeedCreator::_generateFilename();
		 *
		 * would produce:
		 *
		 * /rss/latestnews.php
		 * latestnews.xml
		 *
		 * @return string the feed cache filename
		 * @since 1.4
		 * @access private
		 */
		function _generateFilename()
		{
			$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
			return substr($fileInfo["basename"], 0, -(strlen($fileInfo["extension"]) + 1)) . ".xml";
		}

		/**
		 * @since 1.4
		 * @access private
		 */
		function _redirect( $filename )
		{
			// attention, heavily-commented-out-area
			// maybe use this in addition to file time checking
			//Header("Expires: ".date("r",time()+$this->_timeout));

			/* no caching at all, doesn't seem to work as good:
			  Header("Cache-Control: no-cache");
			  Header("Pragma: no-cache");
			 */

			// HTTP redirect, some feed readers' simple HTTP implementations don't follow it
			//Header("Location: ".$filename);

			Header("Content-Type: " . $contentType . "; filename=" . basename($filename));
			Header("Content-Disposition: inline; filename=" . basename($filename));
			readfile($filename, "r");
			die();
		}

		/**
		 * Turns on caching and checks if there is a recent version of this feed in the cache.
		 * If there is, an HTTP redirect header is sent.
		 * To effectively use caching, you should create the FeedCreator object and call this method
		 * before anything else, especially before you do the time consuming task to build the feed
		 * (web fetching, for example).
		 * @since 1.4
		 * @param filename	string	optional	the filename where a recent version of the feed is saved. If not specified, the filename is $PHP_SELF with the extension changed to .xml (see _generateFilename()).
		 * @param timeout	int		optional	the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1 hour)
		 */
		function useCached( $filename = "", $timeout = 3600 )
		{
			$this->_timeout = $timeout;
			if ($filename == "")
			{
				$filename = $this->_generateFilename();
			}
			if (file_exists($filename) AND ( time() - filemtime($filename) < $timeout))
			{
				$this->_redirect($filename);
			}
		}

		/**
		 * Saves this feed as a file on the local disk. After the file is saved, an redirect
		 * header may be sent to redirect the user to the newly created file.
		 * @since 1.4
		 *
		 * @param filename	string	optional	the filename where a recent version of the feed is saved. If not specified, the filename is $PHP_SELF with the extension changed to .xml (see _generateFilename()).
		 * @param redirect	boolean	optional	send an HTTP redirect header or not. If true, the user will be automatically redirected to the created file.
		 */
		function saveFeed( $filename = "", $redirect = true )
		{
			if ($filename == "")
			{
				$filename = $this->_generateFilename();
			}
			$feedFile = fopen($filename, "w+");
			if ($feedFile)
			{
				fputs($feedFile, $this->createFeed($version));
				fclose($feedFile);
				if ($redirect)
				{
					$this->_redirect($filename);
				}
			}
			else
			{
				echo "<br /><b>Error creating feed file, please check write permissions.</b><br />";
			}
		}
	}

	/**
	 * RSSCreator10 is a FeedCreator that implements RDF Site Summary (RSS) 1.0.
	 *
	 * @see http://www.purl.org/rss/1.0/
	 * @version 1.3
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 */
	class RSSCreator10 extends FeedCreator
	{

		/**
		 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
		 * The feed will contain all items previously added in the same order.
		 * @return    string    the feed's complete text
		 */
		function createFeed()
		{
			$feed = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
			$feed.= "<rdf:RDF\n";
			$feed.= "    xmlns=\"http://purl.org/rss/1.0/\"\n";
			$feed.= "    xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n";
			$feed.= "    xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
			$feed.= "    <channel rdf:about=\"" . $this->SyndicationURL . "\">\n";
			$feed.= "        <title>" . htmlspecialchars($this->title) . "</title>\n";
			$feed.= "        <description>" . htmlspecialchars($this->description) . "</description>\n";
			$feed.= "        <link>" . $this->link . "</link>\n";
			if ($this->image != null)
			{
				$feed.= "        <image rdf:resource=\"" . $this->image->url . "\" />\n";
				$feed.= "        <image rdf:about=\"" . $this->image->url . "\">\n";
				$feed.= "            <title>" . $this->image->title . "</title>\n";
				$feed.= "            <link>" . $this->image->link . "</link>\n";
				$feed.= "            <url>" . $this->image->url . "</url>\n";
				$feed.= "        </image>\n";
			}
			$now = new FeedDate();
			$feed.= "        <dc:date>" . htmlspecialchars($now->iso8601()) . "</dc:date>\n";
			$feed.= "        <items>\n";
			$feed.= "            <rdf:Seq>\n";

			for ($i = 0; $i < count($this->items); $i++)
			{
				$feed.= "                <rdf:li rdf:resource=\"" . htmlspecialchars($this->items[$i]->link) . "\"/>\n";
			}
			$feed.= "            </rdf:Seq>\n";
			$feed.= "        </items>\n";
			$feed.= "    </channel>\n";

			for ($i = 0; $i < count($this->items); $i++)
			{
				$feed.= "    <item rdf:about=\"" . htmlspecialchars($this->items[$i]->link) . "\">\n";
				//$feed.= "        <dc:type>Posting</dc:type>\n";
				$feed.= "        <dc:format>text/html</dc:format>\n";
				if ($this->items[$i]->date != null)
				{
					$itemDate = new FeedDate($this->items[$i]->date);
					$feed.= "        <dc:date>" . htmlspecialchars($itemDate->iso8601()) . "</dc:date>\n";
				}
				if ($this->items[$i]->source != "")
				{
					$feed.= "        <dc:source>" . htmlspecialchars($this->items[$i]->source) . "</dc:source>\n";
				}
				if ($this->items[$i]->creator != "")
				{
					$feed.= "        <dc:creator>" . htmlspecialchars($this->items[$i]->author) . "</dc:creator>\n";
				}
				$feed.= "        <title>" . htmlspecialchars(strip_tags(strtr($this->items[$i]->title, "\n\r", "  "))) . "</title>\n";
				$feed.= "        <link>" . htmlspecialchars($this->items[$i]->link) . "</link>\n";
				$feed.= "        <description>" . htmlspecialchars($this->items[$i]->description) . "</description>\n";
				$feed.= "    </item>\n";
			}
			$feed.= "</rdf:RDF>\n";
			return $feed;
		}
	}

	/**
	 * RSSCreator091 is a FeedCreator that implements RSS 0.91 Spec, revision 3.
	 *
	 * @see http://my.netscape.com/publish/formats/rss-spec-0.91.html
	 * @version 1.3
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 */
	class RSSCreator091 extends FeedCreator
	{

		/**
		 * Stores this RSS feed's version number.
		 * @access private
		 */
		var $RSSVersion;

		function RSSCreator091()
		{
			$this->_setRSSVersion("0.91");
		}

		/**
		 * Sets this RSS feed's version number.
		 * @access private
		 */
		function _setRSSVersion( $version )
		{
			$this->RSSVersion = $version;
		}

		/**
		 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
		 * The feed will contain all items previously added in the same order.
		 * @return    string    the feed's complete text
		 */
		function createFeed()
		{
			$feed = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
			$feed.= "<rss version=\"" . $this->RSSVersion . "\">\n";
			$feed.= "    <channel>\n";
			$feed.= "        <title>" . FeedCreator::iTrunc(htmlspecialchars($this->title), 100) . "</title>\n";
			$feed.= "        <description>" . FeedCreator::iTrunc(htmlspecialchars($this->description), 500) . "</description>\n";
			$feed.= "        <link>" . $this->link . "</link>\n";
			$now = new FeedDate();
			$feed.= "        <lastBuildDate>" . htmlspecialchars($now->rfc822()) . "</lastBuildDate>\n";
			$feed.= "        <generator>" . $this->generatorVersion . "</generator>\n";

			if ($this->image != null)
			{
				$feed.= "        <image>\n";
				$feed.= "            <url>" . $this->image->url . "</url>\n";
				$feed.= "            <title>" . FeedCreator::iTrunc(htmlspecialchars($this->image->title), 100) . "</title>\n";
				$feed.= "            <link>" . $this->image->link . "</link>\n";
				if ($this->image->width != "")
				{
					$feed.= "            <width>" . $this->image->width . "</width>\n";
				}
				if ($this->image->height != "")
				{
					$feed.= "            <height>" . $this->image->height . "</height>\n";
				}
				if ($this->image->description != "")
				{
					$feed.= "            <description>" . htmlspecialchars($this->image->description) . "</description>\n";
				}
				$feed.= "        </image>\n";
			}
			if ($this->language != "")
			{
				$feed.= "        <language>" . $this->language . "</language>\n";
			}
			if ($this->copyright != "")
			{
				$feed.= "        <copyright>" . FeedCreator::iTrunc(htmlspecialchars($this->copyright), 100) . "</copyright>\n";
			}
			if ($this->editor != "")
			{
				$feed.= "        <managingEditor>" . FeedCreator::iTrunc(htmlspecialchars($this->editor), 100) . "</managingEditor>\n";
			}
			if ($this->webmaster != "")
			{
				$feed.= "        <webmaster>" . FeedCreator::iTrunc(htmlspecialchars($this->webmaster), 100) . "</webmaster>\n";
			}
			if ($this->pubDate != "")
			{
				$pubDate = new FeedDate($this->pubDate);
				$feed.= "        <pubDate>" . htmlspecialchars($pubDate->rfc822()) . "</pubDate>\n";
			}
			if ($this->category != "")
			{
				$feed.= "        <category>" . htmlspecialchars($this->category) . "</category>\n";
			}
			if ($this->docs != "")
			{
				$feed.= "        <docs>" . FeedCreator::iTrunc(htmlspecialchars($this->docs), 500) . "</docs>\n";
			}
			if ($this->ttl != "")
			{
				$feed.= "        <ttl>" . htmlspecialchars($this->ttl) . "</ttl>\n";
			}
			if ($this->rating != "")
			{
				$feed.= "        <rating>" . FeedCreator::iTrunc(htmlspecialchars($this->rating), 500) . "</rating>\n";
			}
			if ($this->skipHours != "")
			{
				$feed.= "        <skipHours>" . htmlspecialchars($this->skipHours) . "</skipHours>\n";
			}
			if ($this->skipDays != "")
			{
				$feed.= "        <skipDays>" . htmlspecialchars($this->skipDays) . "</skipDays>\n";
			}

			for ($i = 0; $i < count($this->items); $i++)
			{
				$feed.= "        <item>\n";
				$feed.= "            <title>" . FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)), 100) . "</title>\n";
				$feed.= "            <link>" . htmlspecialchars($this->items[$i]->link) . "</link>\n";
				$feed.= "            <description>" . htmlspecialchars($this->items[$i]->description) . "</description>\n";
				if ($this->items[$i]->author != "")
				{
					$feed.= "            <author>" . htmlspecialchars($this->items[$i]->author) . "</author>\n";
				}
				/*
				  // on hold
				  if ($this->items[$i]->source!="") {
				  $feed.= "            <source>".htmlspecialchars($this->items[$i]->source)."</source>\n";
				  }
				 */
				if ($this->items[$i]->category != "")
				{
					$feed.= "            <category>" . htmlspecialchars($this->items[$i]->category) . "</category>\n";
				}
				if ($this->items[$i]->comments != "")
				{
					$feed.= "            <comments>" . $this->items[$i]->comments . "</comments>\n";
				}
				if ($this->items[$i]->date != "")
				{
					$itemDate = new FeedDate($this->items[$i]->date);
					$feed.= "            <pubDate>" . htmlspecialchars($itemDate->rfc822()) . "</pubDate>\n";
				}
				if ($this->items[$i]->guid != "")
				{
					$feed.= "            <guid>" . $this->items[$i]->guid . "</guid>\n";
				}
				$feed.= "        </item>\n";
			}
			$feed.= "    </channel>\n";
			$feed.= "</rss>\n";
			return $feed;
		}
	}

	/**
	 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
	 *
	 * @see http://backend.userland.com/rss
	 * @version 1.3
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 */
	class RSSCreator20 extends RSSCreator091
	{

		function RSSCreator20()
		{
			parent::_setRSSVersion("2.0");
		}
	}

	/**
	 * PIECreator01 is a FeedCreator that implements the emerging PIE specification,
	 * as in http://intertwingly.net/wiki/pie/Syntax.
	 *
	 * @version 1.3
	 * @author Scott Reynen <scott@randomchaos.com> and Kai Blankenhorn <kaib@bitfolge.de>
	 */
	class PIECreator01 extends FeedCreator
	{

		/**
		 * Builds the PIE feed's text.
		 * @return    string    the feed's complete text
		 */
		function createFeed()
		{
			$feed = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
			$feed.= "<feed version=\"0.1\" xmlns=\"http://example.com/newformat#\">\n";
			$feed.= "    <title>" . FeedCreator::iTrunc(htmlspecialchars($this->title), 100) . "</title>\n";
			$feed.= "    <subtitle>" . FeedCreator::iTrunc(htmlspecialchars($this->description), 500) . "</subtitle>\n";
			$feed.= "    <link>" . $this->link . "</link>\n";
			for ($i = 0; $i < count($this->items); $i++)
			{
				$feed.= "    <entry>\n";
				$feed.= "        <title>" . FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)), 100) . "</title>\n";
				$feed.= "        <link>" . htmlspecialchars($this->items[$i]->link) . "</link>\n";
				$itemDate = new FeedDate($this->items[$i]->date);
				$feed.= "        <created>" . htmlspecialchars($itemDate->iso8601()) . "</created>\n";
				$feed.= "        <issued>" . htmlspecialchars($itemDate->iso8601()) . "</issued>\n";
				$feed.= "        <modified>" . htmlspecialchars($itemDate->iso8601()) . "</modified>\n";
				$feed.= "        <id>" . $this->items[$i]->guid . "</id>\n";
				if ($this->items[$i]->author != "")
				{
					$feed.= "        <author>\n";
					$feed.= "            <name>" . htmlspecialchars($this->items[$i]->author) . "</name>\n";
					$feed.= "            <weblog>" . $this->link . "</weblog>\n";
					$feed.="        </author>\n";
				}
				$feed.= "        <content type=\"text/html\" xml:lang=\"en-us\">\n";
				$feed.= "            <div xmlns=\"http://www.w3.org/1999/xhtml\">" . $this->items[$i]->description . "</div>\n";
				$feed.= "        </content>\n";
				/*
				  // on hold
				  if ($this->items[$i]->source!="") {
				  $feed.= "            <source>".htmlspecialchars($this->items[$i]->source)."</source>\n";
				  }
				 */
				$feed.= "    </entry>\n";
			}
			$feed.= "</feed>\n";
			return $feed;
		}
	}

	/**
	 * MBOXCreator is a FeedCreator that implements the mbox format
	 * as described in http://www.qmail.org/man/man5/mbox.html
	 *
	 * @version 1.3
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 */
	class MBOXCreator extends FeedCreator
	{

		var $contentType = "text/plain";

		function qp_enc( $input = "", $line_max = 76 )
		{
			$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C',
				'D', 'E', 'F');
			$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
			$eol = "\r\n";
			$escape = "=";
			$output = "";
			while (list(, $line) = each($lines))
			{
				//$line = rtrim($line); // remove trailing white space -> no =20\r\n necessary
				$linlen = strlen($line);
				$newline = "";
				for ($i = 0; $i < $linlen; $i++)
				{
					$c = substr($line, $i, 1);
					$dec = ord($c);
					if (($dec == 32) && ($i == ($linlen - 1)))
					{ // convert space at eol only
						$c = "=20";
					}
					elseif (($dec == 61) || ($dec < 32 ) || ($dec > 126))
					{ // always encode "\t", which is *not* required
						$h2 = floor($dec / 16);
						$h1 = floor($dec % 16);
						$c = $escape . $hex["$h2"] . $hex["$h1"];
					}
					if ((strlen($newline) + strlen($c)) >= $line_max)
					{ // CRLF is not counted
						$output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
						$newline = "";
					}
					$newline .= $c;
				} // end of for
				$output .= $newline . $eol;
			}
			return trim($output);
		}

		/**
		 * Builds the MBOX contents.
		 * @return    string    the feed's complete text
		 */
		function createFeed()
		{
			for ($i = 0; $i < count($this->items); $i++)
			{
				if ($this->items[$i]->author != "")
				{
					$from = $this->items[$i]->author;
				}
				else
				{
					$from = $this->title;
				}
				$itemDate = new FeedDate($this->items[$i]->date);
				$feed.= "From " . strtr(MBOXCreator::qp_enc($from), " ", "_") . " " . date("D M d H:i:s Y", $itemDate->unix()) . "\n";
				$feed.= "Content-Type: text/plain;\n";
				$feed.= "	charset=\"ISO-8859-15\"\n";
				$feed.= "Content-Transfer-Encoding: quoted-printable\n";
				$feed.= "Content-Type: text/plain\n";
				$feed.= "From: \"" . MBOXCreator::qp_enc($from) . "\"\n";
				$feed.= "Date: " . $itemDate->rfc822() . "\n";
				$feed.= "Subject: " . MBOXCreator::qp_enc(FeedCreator::iTrunc($this->items[$i]->title, 100)) . "\n";
				$feed.= "\n";
				$body = chunk_split(MBOXCreator::qp_enc($this->items[$i]->description));
				$feed.= preg_replace("/~\nFrom ([^\n]*)(\n?)~/", "\n>From $1$2\n", $body);
				$feed.= "\n";
				$feed.= "\n";
			}
			return $feed;
		}

		/**
		 * Generate a filename for the feed cache file. Overridden from FeedCreator to prevent XML data types.
		 * @return string the feed cache filename
		 * @since 1.4
		 * @access private
		 */
		function _generateFilename()
		{
			$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
			return substr($fileInfo["basename"], 0, -(strlen($fileInfo["extension"]) + 1)) . ".mbox";
		}
	}

	/**
	 * UniversalFeedCreator lets you choose during runtime which
	 * format to build.
	 *
	 * @version 1.3
	 * @author Kai Blankenhorn <kaib@bitfolge.de>
	 */
	class UniversalFeedCreator extends FeedCreator
	{

		var $_feed;

		function _setFormat( $format )
		{
			switch (strtoupper($format))
			{

				case "2.0":
				// fall through
				case "RSS2.0":
					$this->_feed = new RSSCreator20();
					break;

				case "1.0":
				// fall through
				case "RSS1.0":
					$this->_feed = new RSSCreator10();
					break;

				case "0.91":
				// fall through
				case "RSS0.91":
					$this->_feed = new RSSCreator091();
					break;

				case "PIE0.1":
					$this->_feed = new PIECreator01();
					break;

				case "MBOX":
					$this->_feed = new mboxCreator();
					break;

				default:
					$this->_feed = new RSSCreator091();
					break;
			}

			$vars = get_object_vars($this);
			foreach ($vars as $key => $value)
			{
				if ($key != "feed")
				{
					$this->_feed->{$key} = $this->{$key};
				}
			}
		}

		/**
		 * Creates a syndication feed based on the items previously added.
		 *
		 * @see        FeedCreator::addItem()
		 * @param    string    format    format the feed should comply to. Valid values are:
		 *                "PIE0.1", "mbox", "RSS0.91", "RSS1.0" or "RSS2.0".
		 * @return    string    the contents of the feed.
		 */
		function createFeed( $format = "RSS0.91" )
		{
			$this->_setFormat($format);
			return $this->_feed->createFeed();
		}

		/**
		 * Saves this feed as a file on the local disk. After the file is saved, an HTTP redirect
		 * header may be sent to redirect the use to the newly created file.
		 * @since 1.4
		 *
		 * @param	string	format	format the feed should comply to. Valid values are:
		 * 			"PIE0.1", "mbox", "RSS0.91", "RSS1.0" or "RSS2.0".
		 * @param	string	filename	optional	the filename where a recent version of the feed is saved. If not specified, the filename is $PHP_SELF with the extension changed to .xml (see _generateFilename()).
		 * @param	boolean	forward	optional	send an HTTP redirect header or not. If true, the user will be automatically redirected to the created file.
		 */
		function saveFeed( $format = "RSS0.91", $filename = "", $forward = true )
		{
			$this->_setFormat($format);
			$this->_feed->saveFeed($filename, $forward);
		}
	}
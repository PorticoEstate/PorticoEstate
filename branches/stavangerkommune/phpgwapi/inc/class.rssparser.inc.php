<?php
	/**
	* RSSparser for parsing RDF/RSS XML data
	* @author Jeremey Barrett <j@nwow.org>
	* @copyright Copyright (C) 2000 Jeremey Barrett
	* @copyright Portions Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage communication
	* @version $Id$
	* @link http://nwow.org
	* @internal Version 0.4
	* @internal Width and height tags in image not supported, some other tags not supported
	* @internal This requires PHP's XML routines. You must configure PHP with --with-xml.
	*/

	/**
	* Add element start to $GLOBALS['_rss']
	*
	* @param $parser unused
	* @param string $elem Element type: CHANNEL, IMAGE, ITEM, TITLE, LINK, DESCRIPTION, URL
	* @param $attrs unused
	*/
	function _rssparse_start_elem ($parser, $elem, $attrs)
	{
		switch($elem)
		{
			case 'CHANNEL':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth]    = 'channel';
				$GLOBALS['_rss']->tmptitle[$GLOBALS['_rss']->depth] = '';
				$GLOBALS['_rss']->tmplink[$GLOBALS['_rss']->depth]  = '';
				$GLOBALS['_rss']->tmpdesc[$GLOBALS['_rss']->depth]  = '';
				break;
			case 'IMAGE':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth]    = 'image';
				$GLOBALS['_rss']->tmptitle[$GLOBALS['_rss']->depth] = '';
				$GLOBALS['_rss']->tmplink[$GLOBALS['_rss']->depth]  = '';
				$GLOBALS['_rss']->tmpdesc[$GLOBALS['_rss']->depth]  = '';
				$GLOBALS['_rss']->tmpurl[$GLOBALS['_rss']->depth]   = '';
				break;
			case 'ITEM':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth]    = 'item';
				$GLOBALS['_rss']->tmptitle[$GLOBALS['_rss']->depth] = '';
				$GLOBALS['_rss']->tmplink[$GLOBALS['_rss']->depth]  = '';
				$GLOBALS['_rss']->tmpdesc[$GLOBALS['_rss']->depth]  = '';
				break;
			case 'TITLE':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth] = 'title';
				break;
			case 'LINK':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth] = 'link';
				break;
			case 'DESCRIPTION':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth] = 'desc';
				break;
			case 'URL':
				$GLOBALS['_rss']->depth++;
				$GLOBALS['_rss']->state[$GLOBALS['_rss']->depth] = 'url';
				break;
		}
	}


	/**
	* Add element end to $GLOBALS['_rss']
	*
	* @param $parser unused
	* @param string $elem Element type: CHANNEL, IMAGE, ITEM, TITLE, LINK, DESCRIPTION, URL
	*/
	function _rssparse_end_elem ($parser, $elem)
	{
		switch ($elem)
		{
			case 'CHANNEL':
				$GLOBALS['_rss']->set_channel(
					$GLOBALS['_rss']->tmptitle[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmplink[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmpdesc[$GLOBALS['_rss']->depth]
				);
				$GLOBALS['_rss']->depth--;
				break;
			case 'IMAGE':
				$GLOBALS['_rss']->set_image(
					$GLOBALS['_rss']->tmptitle[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmplink[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmpdesc[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmpurl[$GLOBALS['_rss']->depth]
				);
				$GLOBALS['_rss']->depth--;
				break;
			case 'ITEM':
				$GLOBALS['_rss']->add_item(
					$GLOBALS['_rss']->tmptitle[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmplink[$GLOBALS['_rss']->depth],
					$GLOBALS['_rss']->tmpdesc[$GLOBALS['_rss']->depth]
				);
				$GLOBALS['_rss']->depth--;
				break;
			case 'TITLE':
				$GLOBALS['_rss']->depth--;
				break;
			case 'LINK':
				$GLOBALS['_rss']->depth--;
				break;
			case 'DESCRIPTION':
				$GLOBALS['_rss']->depth--;
				break;
			case 'URL':
				$GLOBALS['_rss']->depth--;
				break;
		}
	}

	/**
	* Add element data to $GLOBALS['_rss']
	*
	* @param $parser unused
	* @param string $data Element data
	*/
	function _rssparse_elem_data ($parser, $data)
	{
		switch ($GLOBALS['_rss']->state[$GLOBALS['_rss']->depth])
		{
			case 'title':
				$GLOBALS['_rss']->tmptitle[($GLOBALS['_rss']->depth - 1)] .= $data;
				break;
			case 'link':
				$GLOBALS['_rss']->tmplink[($GLOBALS['_rss']->depth - 1)] .= $data;
				break;
			case 'desc':
				$GLOBALS['_rss']->tmpdesc[($GLOBALS['_rss']->depth - 1)] .= $data;
				break;
			case 'url':
				$GLOBALS['_rss']->tmpurl[($GLOBALS['_rss']->depth - 1)] .= $data;
				break;
		}
	}


	/**
	* RSSparser for parsing RDF/RSS XML data
	* 
	* @package phpgwapi
	* @subpackage communication
	*/
	class rssparser
	{
		var $title;
		var $link;
		var $desc;
		var $items = array();
		var $nitems;
		var $image = array();
		var $state = array();
		var $tmptitle = array();
		var $tmplink = array();
		var $tmpdesc = array();
		var $tmpurl = array();
		var $depth;

		function rssparser()
		{
			$this->nitems = 0;
			$this->depth  = 0;
		}

		function set_channel($in_title, $in_link, $in_desc)
		{
			$this->title = $in_title;
			$this->link  = $in_link;
			$this->desc  = $in_desc;
		}

		function set_image($in_title, $in_link, $in_desc, $in_url)
		{
			$this->image['title'] = $in_title;
			$this->image['link']  = $in_link;
			$this->image['desc']  = $in_desc;
			$this->image['url']   = $in_url;
		}

		function add_item($in_title, $in_link, $in_desc)
		{
			$this->items[$this->nitems]['title'] = $in_title;
			$this->items[$this->nitems]['link']  = $in_link;
			$this->items[$this->nitems]['desc']  = $in_desc;
			$this->nitems++;
		}

		function parse($fp)
		{
			$xml_parser = xml_parser_create();

			xml_set_element_handler($xml_parser, '_rssparse_start_elem', '_rssparse_end_elem');
			xml_set_character_data_handler($xml_parser, '_rssparse_elem_data');

			while ($data = fread($fp, 4096))
			{
				if (!xml_parse($xml_parser, $data, feof($fp)))
				{
					return 1;
				}
			}

			xml_parser_free($xml_parser);

			return 0;
		}
	}

	function rssparse ($fp)
	{
		$GLOBALS['_rss'] = new rssparser();

		if ($GLOBALS['_rss']->parse($fp))
		{
			return 0;
		}

		return $GLOBALS['_rss'];
	}
?>

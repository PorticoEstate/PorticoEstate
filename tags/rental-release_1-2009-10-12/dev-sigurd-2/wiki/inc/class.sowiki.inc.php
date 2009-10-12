<?php
/**************************************************************************\
* phpGroupWare - Wiki DB-Layer                                             *
* http://www.phpgroupware.org                                              *
* Written by Ralf Becker <RalfBecker@outdoor-training.de>                  *
* originaly based on WikkiTikkiTavi tavi.sf.net and www.axisgroupware.org: *
* former files lib/pagestore.php + lib/page.php                            *
* --------------------------------------------                             *
*  This program is free software; you can redistribute it and/or modify it *
*  under the terms of the GNU General Public License as published by the   *
*  Free Software Foundation; either version 2 of the License, or (at your  *
*  option) any later version.                                              *
\**************************************************************************/

/* $Id$ */

/*!
@class sowiki
@author ralfbecker
*/
class soWikiPage
{
	var $name = '';                       // Name of page.
	var $dbname = '';                     // Name used in DB queries.
	var $text = '';                       // Page's text in wiki markup form.
	var $time = '';                       // Page's modification time.
	var $hostname = '';                   // Hostname of last editor.
	var $username = '';                   // Username of last editor.
	var $comment  = '';                   // Description of last edit.
	var $version = -1;                    // Version number of page.
	var $mutable = 1;                     // Whether page may be edited.
	var $exists = 0;                      // Whether page already exists.
	var $db;                              // Database object.
	var $tblNames;                        // Names of the db-tables

	/*
	@function soWikiPage
	@abstract Constructor of the soWikiPage class
	@syntax soWikiPage(&$pagestore,$name = '')
	@param $store Referenz to a pagestore for phpgw db-object and the db-names
	@param $name Name of page to load
	*/
	function soWikiPage($db,$PgTbl,$name = '')
	{
		$this->db = $db;		// to have an independent result-pointer
		$this->PgTbl = $PgTbl;
		$this->name = $name;
		$this->dbname = str_replace('\\', '\\\\', $name);
		$this->dbname = str_replace('\'', '\\\'', $this->dbname);
	}

	
	/*!
	@function exists 
	@abstract Check whether a page exists.
	@syntax exists()
	@note the name of the page has to be set by the constructor
	@returns true if page exists in database.
	*/
	function exists()
	{
		$this->db->query("SELECT MAX(version) FROM $this->PgTbl WHERE title='$this->dbname'",__LINE__,__FILE__);
		
		return $this->db->next_record() ? $this->db->f(0) : False;
	}

	/*!
	@function read
	@abstract Read in a page's contents.
	@syntax read()
	@note the name of the page has to be set by the constructor
	@returns contents of the page or False.
	*/
	function read()
	{
		$query = "SELECT title,time,author,body,mutable,version,username,comment".
		         " FROM $this->PgTbl WHERE title='$this->dbname' ";
		if($this->version != -1)
		{ 
			$query = $query . "AND version='$this->version'"; 
		}
		else
		{
			$query = $query . "ORDER BY version DESC"; 
		}
		$this->db->query($query,__LINE__,__FILE__);

		if (!$this->db->next_record())
		{
			return False;
		}
		$this->time     = $this->db->f('time');
		$this->hostname = $this->db->f('author');
		$this->exists   = 1;
		$this->version  = $this->db->f('version');
		$this->mutable  = !strncmp($this->db->f('mutable'),'on',2);
		$this->username = $this->db->f('username');
		$this->text     = $this->db->f('body');
		$this->comment  = $this->db->f('comment');

		return $this->text;
	}

	/*!
	@function write
	@abstract Write the a page's contents to the db and sets the supercede-time of the prev. version
	@syntax write()
	@note The caller is responsible for performing locking.
	*/
	function write()
	{
		$this->db->query("INSERT INTO $this->PgTbl".
		                 " (title,version,time,supercede,mutable,username,author,comment,body)".
		                 " VALUES ('$this->dbname',$this->version,".time().','.time().",'".
		                 ($this->mutable ? 'on' : 'off') . "','$this->username','$this->hostname',". 
		                 "'$this->comment','$this->text')",__LINE__,__FILE__);

		if($this->version > 1)	// set supercede-time of prev. version
		{
			$this->db->query("UPDATE $this->PgTbl SET supercede=".time().
			                 " WHERE title='$this->dbname' AND version=".($this->version-1),__LINE__,__FILE__);
		}
	}
}
	
/*!
@class sowiki
@author ralfbecker
@note was former called pageStore
*/
class sowiki	// DB-Layer
{
	var $db;
	var $LkTbl = 'phpgw_wiki_links';
	var $PgTbl = 'phpgw_wiki_pages';
	var $RtTbl = 'phpgw_wiki_rate';
	var $IwTbl = 'phpgw_wiki_interwiki';
	var $SwTbl = 'phpgw_wiki_sisterwiki';
	var $RemTbl= 'phpgw_wiki_remote_pages';
	var $ExpireLen,$Admin;
	var $RatePeriod,$RateView,$RateSearch,$RateEdit;

	/*!
	@function sowiki
	@abstract Constructor of the PageStrore class sowiki
	@syntax sowiki()
	*/
	function sowiki()
	{
		$this->db = $GLOBALS['phpgw']->db;
		
		global $ExpireLen,$Admin;		// this should come from the app-config later
		global $RatePeriod, $RateView, $RateSearch, $RateEdit;
		$this->ExpireLen  = $ExpireLen;
		$this->Admin      = $Admin;
		$this->RatePeriod = $RatePeriod;
		$this->RateView   = $RateView;
		$this->RateSearch = $RateSearch;
		$this->RateEdit   = $RateEdit;
	}

	/*!
	@function page
	@abstract Create a page object.
	@syntax page($name = '')
	@param $name Name of the page
	@returns the page
	*/
	function page($name = '')
	{
		return new soWikiPage($this->db,$this->PgTbl,$name);
	}

	/*!
	@function find
	@abstract Find $text in the database, searches title and body.
	@syntax find($text)
	@param $text Name of the page
	@returns an array of page-titles
	*/
	function find($text)
	{
		$this->db->query("SELECT t1.title,t1.version,MAX(t2.version),t1.body".
		                  " FROM $this->PgTbl AS t1,$this->PgTbl AS t2".
		                  " WHERE t1.title=t2.title ".
		                  " GROUP BY t1.title,t1.version,t1.body".
		                  " HAVING t1.version=MAX(t2.version) AND (t1.body LIKE '%$text%' OR t1.title LIKE '%$text%')",
		                  __LINE__,__FILE__);
		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->f('title');
		}
		return $list;
	}

	/*!
	@function history
	@abstract Retrieve a page's edit history.
	@syntax history($title)
	@param $title title of the page
	@returns an array of the different versions 
	*/
	function history($title)
	{
		$title = $this->db->db_addslashes($title);
		$this->db->query("SELECT time,author,version,username,comment " .
		                 " FROM $this->PgTbl WHERE title='$title' ORDER BY version DESC");
		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;	// that allows num. indexes as well as strings
		}
		return $list;
	}

	/*!
	@function interwiki
	@abstract Look up an interwiki prefix
	@syntax interwiki($name)
	@param $name name-prefix of an interwiki
	@returns the url of False 
	*/
	function interwiki($name)
	{
		$name = $this->db->db_addslashes($name);
		$this->db->query("SELECT url FROM $this->IwTbl WHERE prefix='$name'",__LINE__,__FILE__);
		
		return $this->db->next_record() ? $this->db->f('url') : False;
	}

	/*!
	@function clear_link
	@abstract Clear all the links cached for a particular page.
	@syntax clear_link($page)
	@param $page page-title
	*/
	function clear_link($page)
	{
		$page = $this->db->db_addslashes($page);
		$this->db->query("DELETE FROM $this->LkTbl WHERE page='$page'",__LINE__,__FILE__);
	}

	/*!
	@function clear_interwiki
	@abstract Clear all the interwiki definitions for a particular page.
	@syntax clear_interwiki($page)
	@param $page page-title
	*/
	function clear_interwiki($page)
	{
		$page = $this->db->db_addslashes($page);
		$this->db->query("DELETE FROM $this->IwTbl WHERE where_defined='$page'",__LINE__,__FILE__);
	}

	/*!
	@function clear_sisterwiki
	@abstract Clear all the sisterwiki definitions for a particular page.
	@syntax clear_sisterwiki($page)
	@param $page page-title
	*/
	function clear_sisterwiki($page)
	{
		$page = $this->db->db_addslashes($page);
		$this->db->query("DELETE FROM $this->SwTbl WHERE where_defined='$page'",__LINE__,__FILE__);
	}

	/*!
	@function new_link
	@abstract Add a link for a given page to the link table.
	@syntax new_link($page, $link)
	@param $page
	@param $link
	*/
	function new_link($page, $link)
	
	{
		// Assumption: this will only ever be called with one page per
		//   script invocation.  If this assumption should change, $links should
		//   be made a 2-dimensional array.

		static $links = array();

		$page = $this->db->db_addslashes($page);
		$link = $this->db->db_addslashes($link);

		if(empty($links[$link]))
		{
			$this->db->query("INSERT INTO $this->LkTbl VALUES('$page','$link',1)",__LINE__,__FILE__);
			$links[$link] = 1;
		}
		else
		{
			$links[$link]++;
			$this->db->query("UPDATE $this->LkTbl SET count=" . $links[$link] .
			                 " WHERE page='$page' AND link='$link'",__LINE__,__FILE__);
		}
	}

	/*!
	@function new_interwiki
	@abstract Add an interwiki definition for a particular page.
	@syntax new_interwiki($where_defined, $prefix, $url)
	@param $where_defined
	@param $prefix Prefix of the new interwiki
	@param $url URL of the new interwiki
	*/
	function new_interwiki($where_defined, $prefix, $url)
	{
		$url = str_replace("'", "\\'", $url);
		$url = str_replace("&amp;", "&", $url);

		$where_defined = $this->db->db_addslashes($where_defined);

		$this->db->query("SELECT where_defined FROM $this->IwTbl".
		                 " WHERE prefix='$prefix'",__LINE__,__FILE__);
		
		if($this->db->next_record())
		{
			$this->db->query("UPDATE $this->IwTbl SET where_defined='$where_defined',".
			                 "url='$url' WHERE prefix='$prefix'",__LINE__,__FILE__);
		}
		else
		{
			$this->db->query("INSERT INTO $this->IwTbl (prefix, where_defined, url) " .
							"VALUES('$prefix', '$where_defined', '$url')",__LINE__,__FILE__);
		}
	}

	/*!
	@function new_sisterwiki
	@abstract Add an sisterwiki definition for a particular page.
	@syntax new_sisterwiki($where_defined, $prefix, $url)
	@param $where_defined
	@param $prefix Prefix of the new sisterwiki
	@param $url URL of the new sisterwiki
	*/
	function new_sisterwiki($where_defined, $prefix, $url)
	{
		$url = str_replace("'", "\\'", $url);
		$url = str_replace("&amp;", "&", $url);

		$where_defined = $this->db->db_addslashes($where_defined);

		$this->db->query("SELECT where_defined FROM $this->SwTbl".
		                 " WHERE prefix='$prefix'",__LINE__,__FILE__);
		
		if($this->db->next_record())
		{
			$this->db->query("UPDATE $this->IwTbl SET where_defined='$where_defined',".
			                 "url='$url' WHERE prefix='$prefix'",__LINE__,__FILE__);
		}
		else
		{
			$this->db->query("INSERT INTO $this->SwTbl (prefix, where_defined, url) " .
							"VALUES('$prefix', '$where_defined', '$url')",__LINE__,__FILE__);
		}
	}

	/*!
	@function twinpages
	@abstract Find all twins of a page at sisterwiki sites.
	@syntax twinpages($page)
	@param $page page-title
	@returns a list of array(site,page)
	*/
	function twinpages($page)
	{
		$page = $this->db->db_addslashes($page);

		$list = array();
		$this->db->query("SELECT site, page FROM $this->RemTbl WHERE page LIKE '$page'",__LINE__,__FILE__);
		
		while($this->db->next_record())
		{ 
			$list[] = $this->db->Record;
		}
		return $list;
	}

	/*
	@function lock
	@abstract Lock the database tables.
	@syntax lock()
	*/
	function lock()
	{
		global $PgTbl, $IwTbl, $SwTbl, $LkTbl;

		$this->db->lock(array($this->PgTbl,$this->IwTbl,$this->SwTbl,$this->LkTbl),'write');
	}

	/*
	@function unlock
	@abstract Unlock the database tables.
	@syntax unlock()
	*/
	function unlock()
	{
		$this->db->unlock();
	}

	/*
	@function allpages
	@abstract Retrieve a list of all of the pages in the wiki.
	@syntax allpages()
	@returns array of all pages
	*/
	function allpages()
	{
		$qid = $this->db->query("SELECT t1.time,t1.title,t1.author,t1.username,".
		                        " LENGTH(t1.body) AS length,t1.comment,t1.mutable,t1.version,MAX(t2.version)" .
		                        " FROM $this->PgTbl AS t1, $this->PgTbl AS t2" .
		                        " WHERE t1.title = t2.title".
		                        " GROUP BY t1.title,t1.version,t1.time,t1.author,t1.username,t1.body,t1.comment,t1.mutable" .
		                        " HAVING t1.version = MAX(t2.version)",__LINE__,__FILE__);
		$list = array();
		while($this->db->next_record())
		{
			$page = $this->db->Record;
			$page['mutable'] = $page[6] = !strncmp($page['mutable'],'on',2);
			$list[] = $page;
		}

		return $list;
	}

	/*
	@function newpages
	@abstract Retrieve a list of the new pages in the wiki.
	@syntax newpages()
	@returns array of pages
	*/
	function newpages()
	{
		$this->db->query("SELECT time,title,author,username,LENGTH(body) AS length,comment" .
		                 " FROM $this->PgTbl WHERE version=1",__LINE__,__FILE__);

		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;
		}
		return $list;
	}

	/*
	@function emptypages
	@abstract Retrieve a list of all empty (deleted) pages in the wiki.
	@syntax emptypages()
	@returns array of page-infos
	*/
	function emptypages()
	{
		$this->db->query("SELECT t1.time,t1.title,t1.author,t1.username,0,t1.comment,t1.version,MAX(t2.version) " .
		                 " FROM $this->PgTbl AS t1,$this->PgTbl AS t2" .
		                 " WHERE t1.title=t2.title".
		                 " GROUP BY t1.title,t1.version,t1.time,t1.author,t1.username,t1.comment".
		                 " HAVING t1.version = MAX(t2.version) AND t1.body=''",__LINE__,__FILE__);
		$list = array();
		while($this->db->next_record())
		{
			$list[] = $this->db->Record;
		}
		return $list;
	}

	/*
	@function givenpages
	@abstract Retrieve a list of information about a particular set of pages
	@syntax givenpages()
	@returns array of page-infos
	*/
	function givenpages($names)
	{
		$list = array();
		foreach($names as $page)
		{
			$esc_page = $this->db->db_addslashes($page);
			$this->db->query("SELECT time,title,author,username,LENGTH(body) AS length,comment".
			                 " FROM $this->PgTbl WHERE title='$esc_page'" .
			                 " ORDER BY version DESC",__LINE__,__FILE__);

			if($this->db->next_record())
			{ 
				$list[] = $this->db->Record;
			}
		}
		return $list;
	}

	/*!
	@function maintain
	@abstract Expire old versions of pages.
	@syntax maintain()
	*/
	function maintain()
	{
		$db2 = $this->db;	// we need a new/second result-pointer
		
		$this->db->query("SELECT title,MAX(version) AS version".
		                 " FROM $this->PgTbl GROUP BY title",__LINE__,__FILE__);

		while($this->db->next_record())
		{
			$title = $this->db->db_addslashes($this->db->f('title'));
			$version = $this->db->f('version');
			$db2->query("DELETE FROM $this->PgTbl WHERE title='$title' AND" .
			            " (version < $version OR body='') AND ".
			            intval(time()/86400-$this->ExpireLen).">supercede/86400",__LINE__,__FILE__);
			            //was "TO_DAYS(NOW()) - TO_DAYS(supercede) > $ExpireLen";
		}

		if($this->RatePeriod)
		{
			$this->db->query("DELETE FROM $this->RtTbl WHERE ip NOT LIKE '%.*' AND " .
			                 intval(time()/86400)." > time/86400",__LINE__,__FILE__);
			                 //was "TO_DAYS(NOW()) > TO_DAYS(time)"
		}
	}
	
	
	/*!
	@function rateCheck
	@abstract Perform a lookup on an IP addresses edit-rate.
	@syntax rateCheck($type,$remote_addr)
	@param $type 'view',' search' or 'edit'
	@param $remote_addr eg. $_SERVER['REMOTE_ADDR']
	*/
	function rateCheck($type,$remote_addr)
	{
		if(!$this->RatePeriod)
		{ 
			return;
		}

		$this->db->lock($this->RtTbl,'WRITE');

		// Make sure this IP address hasn't been excluded.

		$fields = explode(".", $remote_addr);
		$this->db->query("SELECT * FROM $this->RtTbl WHERE ip='$fields[0].*'".
		                 " OR ip='$fields[0].$fields[1].*'".
		                 " OR ip='$fields[0].$fields[1].$fields[2].*'",__LINE__,__FILE__);
		
		if ($this->db->next_record())
		{
			die(lang('You have been denied access to this site.') .'<br/><br/>'.
			    lang('Please contact the <a href="mailto:%1">administrator</a> for assistence.',$this->Admin)); 
		}

		// Now check how many more actions we can perform.

		$this->db->query("SELECT time,". //was "TIME_TO_SEC(NOW()) - TIME_TO_SEC(time),"
		                 "viewLimit,searchLimit,editLimit FROM $this->RtTbl " .
		                 "WHERE ip='$remote_addr'",__LINE__,__FILE__);

		if(!$this->db->next_record())
		{ 
			$result = array(-1, $this->RateView, $this->RateSearch, $this->RateEdit); 
		}
		else
		{
			$result[0] = time()-$result[0];
			if ($result[0]  < 0)
			{ 
				$result[0] = $this->RatePeriod; 
			}
			$result[1] = min($result[1] + $result[0] * $this->RateView / $this->RatePeriod,$this->RateView);
			$result[2] = min($result[2] + $result[0] * $this->RateSearch / $this->RatePeriod,$this->RateSearch);
			$result[3] = min($result[3] + $result[0] * $this->RateEdit / $this->RatePeriod,$this->RateEdit);
		}

		switch($type)
		{
			case 'view':	$result[1]--; break;
			case 'search':	$result[2]--; break;
			case 'edit':	$result[3]--; break;
		}
		if($result[1] < 0 || $result[2] < 0 || $result[3] < 0)
		{ 
			die(lang('You have exeeded the number of pages you are allowed to visit in a given period of time.  Please return later.').
			    '<br/><br/>'.lang('Please contact the <a href="mailto:%1">administrator</a> for assistence.',$this->Admin)); 
		}

		// Record this action.

		if($result[0] == -1)
		{
			$this->db->query("INSERT INTO $this->RtTbl VALUES('$remote_addr',".time().	//was "NULL"
			                 ",$result[1],$result[2],$result[3])",__LINE__,__FILE__);
		}
		else
		{
			$this->db->query("UPDATE $this->RtTbl SET viewLimit=$result[1],searchLimit=$result[2],".
			                 " editLimit=$result[3],time=".time().
			                 " WHERE ip='$remote_addr'",__LINE__,__FILE__);
		}
		$this->db->unlock();
	}

	/*!
	@function rateBlockList
	@abstract Return a list of blocked address ranges.
	@syntax rateBlockList()
	*/
	function rateBlockList()
	{
		$list = array();

		if(!$this->RatePeriod)
		{ 
			return $list; 
		}
		$this->db->query("SELECT ip FROM $this->RtTbl",__LINE__,__FILE__);
		
		while($this->db->next_record())
		{
			if(preg_match('/^\\d+\\.(\\d+\\.(\\d+\\.)?)?\\*$/',$this->db->f('ip')))
			{ 
				$list[] = $this->db->f('ip');
			}
		}
		return $list;
	}

	/*!
	@function rateBlockAdd
	@abstract Block an address range.
	@syntax rateBlockAdd($address)
	@param $address ip-addr. or addr-range
	*/
	function rateBlockAdd($address)
	{
		if(preg_match('/^\\d+\\.(\\d+\\.(\\d+\\.)?)?\\*$/', $address))
		{
			$this->db->query("SELECT * FROM $this->RtTbl WHERE ip='$address'",__LINE__,__FILE__);

			if(!$this->db->next_record())
			{
				$this->db->query("INSERT INTO $this->RtTbl (ip,time) VALUES('$address',".time().")",__LINE__,__FILE__);
			}
		}
	}

	/*!
	@function rateBlockRemove
	@abstract Remove an address-range block.
	@syntax rateBlockRemove($address)
	@param $address ip-addr. or addr-range
	*/
	function rateBlockRemove($address)
	{
		$this->db->query("DELETE FROM $this->RtTbl WHERE ip='$address'",__LINE__,__FILE__);
	}
}
?>

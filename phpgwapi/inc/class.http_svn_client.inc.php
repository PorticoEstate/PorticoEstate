<?php
	/**
	* WebDAV_SVN - Provides methods for manipulating an RFC3253 SVN  repository
	* @author Van Cong Ngo <van_cong.ngo@int-evry.fr>
	* @author Benoît Hamet <caeies@phpgroupware.org>
	* @copyright Copyright (C) 2006 Free Software Foundation.
	* @license LGPL
	* @package phpgwapi
	* @subpackage network
	* @version $Id$
	* @internal At the moment much of this is simply a wrapper around the NET_HTTP_Client class, with some other methods for parsing the returned XML etc Ideally this will eventually use groupware's inbuilt HTTP class
	*/

	/**
	* Debug flag for svn client
	*/
	define ('DEBUG_SVN_CLIENT', 0);
	/**
	* Debug flag for svn xml
	*/
  	define ('DEBUG_SVN_XML', 0);

	/**
	* SVNParser
	*
	* @package phpgwapi
	* @subpackage network
	* @access private
	*/
  	class SVNParser
	{
		var $xml = null;
		var $log;
		var $cdata;
		var $logentry;

		function __construct()
		{
			$this->xml = xml_parser_create();
			xml_set_object($this->xml,$this);
			xml_set_character_data_handler($this->xml, 'dataHandler'); 
			xml_set_element_handler($this->xml, 'startHandler', 'endHandler');
		}
 
		function parse($xmlString)
		{
			if (!xml_parse($this->xml,$xmlString)) 
			{
		       		die(sprintf("XML error: %s at line %d",
		           	xml_error_string(xml_get_error_code($this->xml)),
       	       			xml_get_current_line_number($this->xml)));
		      	       	xml_parser_free($this->xml);
	           	}
        		return true;
	        }


		 function startHandler($parser, $name, $attribs)
		 {
			switch($name) 
			{	
				case 'S:LOG-ITEM':
					$this->logentry=array();
					break;
		                case 'D:VERSION-NAME':
	        	        case 'D:COMMENT': 
           	        	case 'D:CREATOR-DISPLAYNAME':
		                case 'S:DATE':
					$this->cdata='';
					break;
			}
		 }


		function dataHandler($parser, $data)
		{
			$this->cdata.=$data;
		}


		function endHandler($parser, $name)
		{
			switch($name)
			{
				case 'D:VERSION-NAME':
					$this->logentry['version']=$this->cdata;   	
					break;
				case 'D:COMMENT': 
					$this->logentry['comment']=$this->cdata;   	
					break;
				case 'D:CREATOR-DISPLAYNAME':
					$this->logentry['creator']=$this->cdata;   	
					break;
				case 'S:DATE':
					$this->logentry['created']=$this->cdata;   	
					break;
				case 'S:LOG-ITEM':
					$this->log[] = $this->logentry;
					break;  
			}	  
		}
	}

	//we inherit functions from the dav_client for most of our jobs
	require_once('class.http_dav_client.inc.php');

	/**
	* http_svn_client : svn client extending the dav_client for extracting specific function
	*
	* @package phpgwapi
	* @subpackage network
	* @access public
	*/
	class http_svn_client extends http_dav_client
	{
		var $svn_processor = NULL;
		
		function http_svn_client()
		{
			parent::http_dav_client();
		}       
		
		/**
		* Report information about URI
		*
		* Svn report of activity between $startvers and $endvers about $uri
		*
		* @param string $uri URI of the resource
		* @param int $startvers the start revision we want
		* @param int $endvers the end revision we want
		* @return string the XML answer from the server.
		* @internal The only supported authentication type is "basic", returned value are the pure xml answer
		* @access private
		*/
		function report($uri,$startvers,$endvers,  $dummy_to_match_parent_class='')
		{
			$svnxml = '<?xml version="1.0" encoding="utf-8" ?>
			<S:log-report xmlns:S="svn:" xmlns:D="DAV">';
			if($startvers)
			{
				$svnxml .= '<S:start-revision>'.$startvers.'</S:start-revision>'; 
			}
			$svnxml .= '<S:end-revision>'.$endvers.'</S:end-revision>';   
			$svnxml .= '<S:path/>';
			$svnxml.='<S:strict-node-history/>';
			$svnxml.='<S:discover-changed-paths/>';
			$svnxml .= '</S:log-report>';

			If (DEBUG_SVN_XML) 
			{
				echo '<B>report: Send</b><pre>'.htmlentities($svnxml).'</pre>';
			}
			$this->http_client->requestBody = $svnxml;
			if( $this->http_client->sendCommand( 'REPORT '.$uri.' HTTP/1.1' ) )
			{
				$this->http_client->processReply();
			}
			If (DEBUG_SVN_XML)
			{
				echo '<b>report: Send</b><pre>'.htmlentities($this->http_client->getBody()).'</pre>';
			}
			return $this->http_client->reply;
		}

		/**
		* getVersions information about version of URI
		*
		*
		* @param string $uri URI of the resource
		* @param int $startvers the start revision we want
		* @param int $endvers the end revision we want
		* @return array containing the XML parsed by the processor
		* @internal The only supported authentication type is "basic"
		* @access public
		*/
		function getVersions($uri,$startvers,$endvers)
		{
			$ret=$this->report($uri,$startvers,$endvers);
			$xml_result=$this->http_client->getBody();			

			if ( !is_object($this->svn_processor))
			{
				$this->svn_processor = new SVNParser();
			}
			$this->svn_processor->parse($xml_result);
			$result_array =& $this->svn_processor->log; 
			return $result_array;
		}

		/**
		* Splitime given by SVN / DAV
		*
		* @param string $time the string representation of the time
		* @return array containing 'date', 'hour', 'minute', 'second'
		* @access private
		*/
		function _splittime($time)
		{
			$result=array();
			$temp=explode('T',$time);
			$result['date']=$temp[0];
			$temp1=explode(':',$temp[1]);
			$result['hour']=$temp1[0]; 
			$result['minute']=$temp1[1];
			$temp2=explode('Z',$temp1[2]);
			$result['second']=$temp2[0];
			return $result;
		}

		/**
		* compare the proximity of two date from SVN
		*
		* @param string $date1 first date
		* @param string $date2 second date
		* @return true if $date1 and $date2 are in the same second
		* @access private
		*/
		function compare_date($date1,$date2)                    
		{
			$result1 = $this->_splittime($date1);                    
			$result2 = $this->_splittime($date2);
			if($result1['date']==$result2['date'] && ($result1['hour']-$result2['hour'])==0 && ($result1['minute']-$result2['minute'])==0)   
			{
				if(abs($result1['second'] - $result2['second'])<0.5)          
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			return false;
		}

		/**
		* collapse of the journal when svn is autoversionned and used by phpGroupWare
		* 
		* @param array $journal result of getVersion.
		* @return array the new journal with entries collapsed if they are in the same second
		* @internal Journal is the return of getversions
		* @access public
		*/
		function collapse($journal)
		{
			$rarray=array();
			$numofentry=0;
			$rjournal=array();
			
			//while(list (, $entry_journal) = each ($journal))
			foreach($journal as $key => $entry_journal)
			{
				++$numofentry;
				$rjournal[]=$entry_journal; 
			}
			@reset($rjournal);
			$temp=$rjournal[0]; 
			for ($i = 1; $i != $numofentry; $i++)
			{
				$entry=$rjournal[$i];
				if($this->compare_date($temp['created'],$entry['created']))
				{	
					$temp=$entry;
					//for the entry final
					if($i==$numofentry-1) $rarray[]=$temp;
				}
				else
				{
					$rarray[]=$temp;
					$temp=$entry;    
				}
			} 
			return $rarray;
		}
    }

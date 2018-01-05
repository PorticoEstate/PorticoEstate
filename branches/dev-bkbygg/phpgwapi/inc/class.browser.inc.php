<?php
	/**
	* Browser detect functions
	* @author Miles Lott <milosch@phpgroupware.org>
	* @copyright Copyright (C) 1999,2000 The SourceForge Crew - http://sourceforge.net
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage network
	* @version $Id$
	* @internal Majority of code borrowed from Sourceforge 2.5
	*/

	/**
	* Browser detect functions
	*
	* @package phpgwapi
	* @subpackage network
	*/
	class phpgwapi_browser
	{
		var $BROWSER_AGENT;
		var $BROWSER_VER;
		var $BROWSER_PLATFORM;
		var $br;
		var $p;
		var $data;

		/**
		* Determine browser, version and platform
		*/
		function __construct()
		{
			$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

			if(preg_match('/MSIE ([0-9].[0-9]{1,2})/',$HTTP_USER_AGENT,$log_version))
			{
				$this->BROWSER_VER = $log_version[1];
				$this->BROWSER_AGENT = 'IE';
			}
			else if(preg_match('/Opera ([0-9].[0-9]{1,2})/',$HTTP_USER_AGENT,$log_version) ||
				preg_match('/Opera\\/([0-9].[0-9]{1,2})/',$HTTP_USER_AGENT,$log_version))
			{
				$this->BROWSER_VER   = $log_version[1];
				$this->BROWSER_AGENT = 'OPERA';
			}
			else if(preg_match('/iCab ([0-9].[0-9a-zA-Z]{1,4})/i',$HTTP_USER_AGENT,$log_version) ||
				preg_match('/iCab\\/([0-9].[0-9a-zA-Z]{1,4})/i',$HTTP_USER_AGENT,$log_version))
			{
				$this->BROWSER_VER   = $log_version[1];
				$this->BROWSER_AGENT = 'iCab';
			} 
			else if(preg_match('/Gecko\\/([0-9]{8})/', $HTTP_USER_AGENT, $log_version))
			{
				$this->BROWSER_VER   = $log_version[1];
				$this->BROWSER_AGENT = 'MOZILLA';
			}
			else if(preg_match('/Konqueror\\/([0-9].[0-9].[0-9]{1,2})/',$HTTP_USER_AGENT,$log_version) ||
				preg_match('/Konqueror\\/([0-9].[0-9]{1,2})/',$HTTP_USER_AGENT,$log_version))
			{
				$this->BROWSER_VER=$log_version[1];
				$this->BROWSER_AGENT='Konqueror';
			}
			else
			{
				$this->BROWSER_VER=0;
				$this->BROWSER_AGENT='OTHER';
			}

			/*
				Determine platform
			*/
			if(strstr($_SERVER['HTTP_USER_AGENT'],'Win'))
			{
				$this->BROWSER_PLATFORM='Win';
			}
			else if(strstr($_SERVER['HTTP_USER_AGENT'],'Mac'))
			{
				$this->BROWSER_PLATFORM='Mac';
			}
			else if(strstr($_SERVER['HTTP_USER_AGENT'],'Linux'))
			{
				$this->BROWSER_PLATFORM='Linux';
			}
			else if(strstr($_SERVER['HTTP_USER_AGENT'],'Unix'))
			{
				$this->BROWSER_PLATFORM='Unix';
			}
			else if(strstr($_SERVER['HTTP_USER_AGENT'],'Beos'))
			{
				$this->BROWSER_PLATFORM='Beos';
			}
			else
			{
				$this->BROWSER_PLATFORM='Other';
			}

			/*
			echo "\n\nAgent: " . $_SERVER['HTTP_USER_AGENT'];
			echo "\nIE: ".browser_is_ie();
			echo "\nMac: ".browser_is_mac();
			echo "\nWindows: ".browser_is_windows();
			echo "\nPlatform: ".browser_get_platform();
			echo "\nVersion: ".browser_get_version();
			echo "\nAgent: ".browser_get_agent();
			*/

			// The br and p functions are supposed to return the correct
			// value for tags that do not need to be closed.  This is
			// per the xhmtl spec, so we need to fix this to include
			// all compliant browsers we know of.
			if($this->BROWSER_AGENT == 'IE')
			{
				$this->br = '<br />';
				$this->p = '<p />';
			}
			else
			{
				$this->br = '<br />';
				$this->p = '<p>';
			}
		}

		function return_array()
		{
			$this->data = array(
				'agent'    => $this->get_agent(),
				'version'  => $this->get_version(),
				'platform' => $this->get_platform()
			);

			return $this->data;
		}

		function get_agent()
		{
			return $this->BROWSER_AGENT;
		}

		function get_version()
		{
			return $this->BROWSER_VER;
		}

		function get_platform()
		{
			return $this->BROWSER_PLATFORM;
		}

		function is_linux()
		{
			if($this->get_platform()=='Linux')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_unix()
		{
			if($this->get_platform()=='Unix')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_beos()
		{
			if($this->get_platform()=='Beos')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_mac()
		{
			if($this->get_platform()=='Mac')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_windows()
		{
			if($this->get_platform()=='Win')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_ie()
		{
			if($this->get_agent()=='IE')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_netscape()
		{
			if($this->get_agent()=='MOZILLA')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function is_opera()
		{
			if($this->get_agent()=='OPERA')
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		* Echo content headers for file downloads
		*
		* @param string $fn Filename
		* @param string $mime Content mime type
		* @param integer $length Content length
		* @param boolean $nocache When set to true output no-cache pragmas
		*/
		function content_header($fn='',$mime='',$length='',$nocache=True)
		{
			// if no mime-type is given or it's the default binary-type, guess it from the extension
			if( !$mime || $mime == 'application/octet-stream')
			{
				$mime_magic = createObject('phpgwapi.mime_magic');
				$mime = $mime_magic->filename2mime($fn);
			}
			if($fn)
			{
				if($this->get_agent() == 'IE')// && $this->BROWSER_VER == '5.5')
				{
					$attachment = '';
				}
				else if( $mime != 'text/plain' )
				{
					$attachment = ' attachment;';
				}
				else
				{
					$attachment = ' inline;';
				}

				// Show this for all
				header('Content-Disposition:'.$attachment.' filename="'.$fn.'"');
				header('Content-Type: '.$mime);

				if($length)
				{
					header('Content-Length: '.$length);
				}

				if($nocache)
				{
					header('Pragma: no-cache');
					header('Pragma: public');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				}
			}
		}
		
		/**
		* Determine whether the browser is wml-capable or not
		*
		* @return bool is wml-capable
		*/
		public static function is_mobile()
		{
			return strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'text/vnd.wap.wml') > 0;
		}
	}

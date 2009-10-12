<?php
	/**
	* PHPClientSniffer - Returns client information based on HTTP_USER_AGENT
	* @author Roger Raymond for PHyX8 studios <roger.raymond@asphyxia.com>
	* @copyright Copyright (C) 2000 PHyX8 studios
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage network
	* @version $Id$
	*
	* @internal BASED ON WORKS AND IDEAS BY:
	* @internal Tim Perdue of PHPBuilder.com
	* @link http://www.phpbuilder.com/columns/tim20000821.php3
	* @internal The Ultimate JavaScript Client Sniffer by Netscape.
	* @link http://developer.netscape.com/docs/examples/javascript/NAME_type.html
	*/

	/**
	* Returns client information based on HTTP_USER_AGENT
	*
	* @package phpgwapi
	* @subpackage network
	*/
	class clientsniffer
	{
		/**
		* The HTTP USER AGENT String
		* @var string HTTP USER AGENT
		*/
		var $UA         =  '';
		/**
		* Browser Name (Netscape, IE, Opera, iCab, Unknown)
		* @var string Browser Name (Netscape, IE, Opera, iCab, Unknown)
		*/
		var $NAME       =  'Unknown';
		/**
		* Browser Full Version
		* @var integer Browser Full Version
		*/
		var $VERSION    =  0;
		/**
		* Browser Major Version 
		* @var integer Browser Major Version
		*/
		var $MAJORVER   =  0;
		/**
		* Browser Minor Version
		* @var integer Browser Minor Version
		*/
		var $MINORVER   =  0;
		/**
		* AOL Browser
		* @var boolean AOL Browser
		*/
		var $AOL        =  false;
		/**
		* WEB TV Browser
		* @var boolean WEB TV Browser
		*/
		var $WEBTV      =  false;
		/**
		* Assumed JavaScript Version Supported by Browser
		* @var float Supported JavaScript Version
		*/
		var $JS         =  0.0;
		/**
		* System Platform (Win16,Win32,Mac,OS2,Unix,Unknown)
		* @var string System Platform (Win16,Win32,Mac,OS2,Unix,Unknown)
		*/
		var $PLATFORM   =  'Unknown';
		/**
		* System OS (Win98,OS2,Mac68k,linux,bsd,...,Unknown)
		* @var string System OS (Win98,OS2,Mac68k,linux,bsd,...,Unknown)
		*/
		var $OS         =  'Unknown';
		/**
		* Remote ip address or 'Unknown'
		* @var string Remote ip address or 'Unknown'
		*/
		var $IP         =  'Unknown';

		/**
		* Constructor
		*/
		function clientsniffer()
		{
			$this->UA = $_SERVER['HTTP_USER_AGENT'];

			// Determine NAME Name and Version      
			if ( eregi( 'MSIE ([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ||
				eregi( 'Microsoft Internet Explorer ([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ) 
			{
				$this->VERSION = $info[1];
				$this->NAME = 'IE';
			} 
			elseif ( eregi( 'Opera ([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ||
				eregi( 'Opera/([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ) 
			{
				$this->VERSION = $info[1];
				$this->NAME = 'Opera';
			}
			elseif ( eregi( 'iCab ([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ||
				eregi( 'iCab/([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ) 
			{
				$this->VERSION = $info[1];
				$this->NAME = 'iCab';
			}
			elseif ( eregi( 'Netscape6/([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ) 
			{
				$this->VERSION = $info[1];
				$this->NAME = 'Netscape';
			}
			elseif ( eregi( 'Mozilla/([0-9].[0-9a-zA-Z]{1,4})',$this->UA,$info) ) 
			{
				$this->VERSION = $info[1];
				$this->NAME = 'Netscape';
			}
			else 
			{
				$this->VERSION = 0;
				$this->NAME = 'Unknown';
			}

			// Determine if AOL or WEBTV
			if( eregi( 'aol',$this->UA,$info))
			{
				$this->AOL = true;
			}
			elseif( eregi( 'webtv',$this->UA,$info))
			{
				$this->WEBTV = true;
			}

			// Determine Major and Minor Version
			if($this->VERSION > 0)
			{
				$pos = strpos($this->VERSION,'.');
				if ($pos > 0)
				{
					$this->MAJORVER = substr($this->VERSION,0,$pos);
					$this->MINORVER = substr($this->VERSION,$pos,strlen($this->VERSION));
				}
				else
				{
					$this->MAJORVER = $this->VERSION; 
				}
			}

			// Determine Platform and OS

			// Check for Windows 16-bit
			if( eregi('Win16',$this->UA)           || 
			eregi('windows 3.1',$this->UA)     || 
			eregi('windows 16-bit',$this->UA)  || 
			eregi('16bit',$this->UA))
			{
				$this->PLATFORM = 'Win16';
				$this->OS = 'Win31';
			}

			// Check for Windows 32-bit     
			if(eregi('Win95',$this->UA) || eregi('windows 95',$this->UA)) 
			{
				$this->PLATFORM = 'Win32'; 
				$this->OS = 'Win95'; 
			}
			elseif(eregi('Win98',$this->UA) || eregi('windows 98',$this->UA)) 
			{
				$this->PLATFORM = 'Win32'; 
				$this->OS = 'Win98'; 
			}
			elseif(eregi('WinNT',$this->UA) || eregi('windows NT',$this->UA)) 
			{
				$this->PLATFORM = 'Win32'; 
				$this->OS = 'WinNT'; 
			}
			else
			{
				$this->PLATFORM = 'Win32'; 
				$this->OS = 'Win9xNT'; 
			}

			// Check for OS/2
			if( eregi('os/2',$this->UA) || eregi('ibm-webexplorer',$this->UA))
			{
				$this->PLATFORM = 'OS2';
				$this->OS = 'OS2';  
			}

			// Check for Mac 68000
			if( eregi('68k',$this->UA) || eregi('68000',$this->UA))
			{
				$this->PLATFORM = 'Mac';
				$this->OS = 'Mac68k';
			}

			//Check for Mac PowerPC
			if( eregi('ppc',$this->UA) || eregi('powerpc',$this->UA))
			{
				$this->PLATFORM = 'Mac';
				$this->OS = 'MacPPC';
			}

			// Check for Unix Flavor

			//SunOS
			if(eregi('sunos',$this->UA)) 
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'sun';
			}
			if(eregi('sunos 4',$this->UA)) 
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'sun4';
			}
			elseif(eregi('sunos 5',$this->UA)) 
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'sun5';
			}
			elseif(eregi('i86',$this->UA)) 
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'suni86';
			}

			// Irix
			if(eregi('irix',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'irix';
			}
			if(eregi('irix 6',$this->UA)) 
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'irix6';
			}
			elseif(eregi('irix 5',$this->UA)) 
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'irix5';
			}

			//HP-UX
			if(eregi('hp-ux',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'hpux';
			}
			if(eregi('hp-ux',$this->UA) && ereg('10.',$this-UA))  
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'hpux10';
			}
			elseif(eregi('hp-ux',$this->UA) && ereg('09.',$this-UA))  
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'hpux9';
			}

			//AIX
			if(eregi('aix',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'aix';
			}
			if(eregi('aix1',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'aix1';
			}
			elseif(eregi('aix2',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'aix2';
			}
			elseif(eregi('aix3',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'aix3';
			}
			elseif(eregi('aix4',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'aix4';
			}

			// Linux
			if(eregi('inux',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'linux';
			}

			//Unixware
			if(eregi('unix_system_v',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'unixware';
			}

			//mpras
			if(eregi('ncr',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'mpras';
			}

			//Reliant
			if(eregi('reliantunix',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'reliant';
			}

			// DEC
			if(eregi('dec',$this->UA)           ||  
			eregi('osfl',$this->UA)          || 
			eregi('alphaserver',$this->UA)   || 
			eregi('ultrix',$this->UA)        || 
			eregi('alphastation',$this->UA))
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'dec';
			}

			// Sinix
			if(eregi('sinix',$this->UA))    
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'sinix';
			}

			// FreeBSD
			if(eregi('freebsd',$this->UA))    
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'freebsd';
			}

			// BSD
			if(eregi('bsd',$this->UA))    
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'bsd';
			}

			// VMS
			if(eregi('vax',$this->UA) || eregi('openvms',$this->UA))    
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'vms';
			}

			// SCO
			if(eregi('sco',$this->UA) || eregi('unix_sv',$this->UA))    
			{
				$this->PLATFORM = 'Unix';
				$this->OS = 'sco';
			}

			// Assume JavaScript Version

			// make the code a bit easier to read
			$ie  = eregi('ie',$this->NAME);
			$ie5 = ( eregi('ie',$this->NAME) && ($this->MAJORVER >= 5) );
			$ie4 = ( eregi('ie',$this->NAME) && ($this->MAJORVER >= 4) );
			$ie3 = ( eregi('ie',$this->NAME) && ($this->MAJORVER >= 3) );

			$nav  = eregi('netscape',$this->NAME);
			$nav5 = ( eregi('netscape',$this->NAME) && ($this->MAJORVER >= 5) );
			$nav4 = ( eregi('netscape',$this->NAME) && ($this->MAJORVER >= 4) );
			$nav3 = ( eregi('netscape',$this->NAME) && ($this->MAJORVER >= 3) );
			$nav2 = ( eregi('netscape',$this->NAME) && ($this->MAJORVER >= 2) );

			$opera = eregi('opera',$this->NAME);

			// do the assumption
			// update as new versions are released

			// Provide upward compatibilty
			if($nav && ($this->MAJORVER > 5))
			{
				$this->JS = 1.4;
			}
			elseif($ie && ($this->MAJORVER > 5))
			{
				$this->JS = 1.3;
			}
			// check existing versions
			elseif($nav5)
			{
				$this->JS = 1.4;
			}
			elseif(($nav4 && ($this->VERSION > 4.05)) || $ie4)
			{
				$this->JS = 1.3;
			}
			elseif(($nav4 && ($this->VERSION <= 4.05)) || $ie4)
			{
				$this->JS = 1.2;
			}
			elseif($nav3 || $opera)
			{
				$this->JS = 1.1;
			}
			elseif(($nav && ($this->MAJORVER >= 2)) || ($ie && ($this->MAJORVER >=3)))
			{
				$this->JS = 1.0;
			}
			//no idea
			else
			{
				$this->JS = 0.0;
			}

			// Grab IP Address
			$this->IP = getenv('REMOTE_ADDR');
		}
	}

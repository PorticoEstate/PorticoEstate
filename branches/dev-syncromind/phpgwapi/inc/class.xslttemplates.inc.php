<?php
	/**
	* XSLT Template class
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Ralf Becker <ralfbecker@outdoor-training.de>
	* @copyright Copyright (C) 2002-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage gui
	* @version $Id$
	*/

	if( !extension_loaded('xsl') )
	{
		die('PHP CONFIGURATION. xslt-extension is not loaded. Please contact the system administrator.');
	}

	phpgw::import_class('phpgwapi.browser');	

	/**
	* Include xml tool
	*/
	phpgw::import_class('phpgwapi.xmlhelper');
//	phpgw::import_class('phpgwapi.xmltool');


	/**
	* XSLT template engine
	*
	* @package phpgwapi
	* @subpackage gui
	*/
	class phpgwapi_xslttemplates
	{
		var $rootdir = '';
		var $prev_rootdir = '';

		/**
		* @var string $output the format of the output
		*/ 
		private $output = 'html';

		/**
		* The xslfiles will be loaded up and merged into $xsldata
		* @var array XSL files to load
		*/
		var $xslfiles = Array();
		var $xsldata = '';

		/**
		* Users can set $vars which will be converted into xmldata before xsl processing.
		* Or they can generate their own XML data and set it directly when they have
		* need for a more robust schema.
		* @var array Variables to convert into xml-data
		*/
		var $vars = array();
		var $xmlvars = array();
		var $xmldata = '';

		/**
		* Constructor
		*
		* @param string $root the root directory
		*/
		function __construct($root = '.')
		{
			//FIXME Print view/mode should be handled by CSS not different markup
			if ( isset($GLOBALS['phpgw_info']['flags']['printview']) && $GLOBALS['phpgw_info']['flags']['printview'] )
			{
				$this->print = true;
			}
			$this->set_root($root);
			if ( phpgwapi_browser::is_mobile() )
			{
				$this->set_output('wml');
			}
		}

		/**
		* Error hanlder
		*
		* @param string $msg the error message
		*/
		function halt($msg)
		{
			throw new Exception($msg);
		}

		 /**
		 * Set the output format
		 *
		 * @internal currently supports html, html5 and wml
		 * @param string $output the desired output format
		 */
		 public function set_output($output)
		{
			$output = strtolower($output);
			switch ( $output )
			{
				case 'wml':
				case 'html':
				case 'html5':
					$this->output = $output;
					break;
				default:
					$this->output = 'html';
			}
		}

		function set_root($rootdir)
		{
			if (!is_dir($rootdir))
			{
				$this->halt('set_root: '.$rootdir.' is not a directory.');
				return false;
			}
			$this->prev_rootdir = $this->rootdir;
			$this->rootdir = $rootdir;
			return true;
		}

		function reset_root()
		{
			$this->rootdir = $this->prev_rootdir;
		}

		function add_file($filename, $rootdir='', $time=1)
		{
			if ( is_array($filename) )
			{
				foreach ( $filename as $file )
				{
					$this->add_file($file, $rootdir);
				}
				return;
			}

			if($rootdir=='')
			{
				$rootdir=$this->rootdir;
			}

			if ( substr($filename, 0, 1) != '/' 
				&& substr($filename, 1, 1) != ':' )
			{
				$new_filename = "{$rootdir}/{$filename}";
			}
			else
			{
				$new_filename = $filename;
			}

//				echo 'Rootdir: '.$rootdir.'<br>'."\n".'Filename: '.$filename.'<br>'."\n".'New Filename: '.$new_filename.'<br>'."\n";
			if (!file_exists($new_filename.'.xsl'))
			{
				switch($time)
				{
					case 2:
						$new_root = PHPGW_SERVER_ROOT . str_replace($GLOBALS['phpgw_info']['server']['template_set'], 'base', substr($rootdir,strlen(PHPGW_SERVER_ROOT)));
						$this->add_file($filename, $new_root, 3);
						return true;
					case 3:
						$new_root = PHPGW_SERVER_ROOT . '/phpgwapi/templates/' . $GLOBALS['phpgw_info']['server']['template_set'];
						$this->add_file($filename, $new_root, 4);
						return true;
					case 4:
						$new_root = PHPGW_SERVER_ROOT . '/phpgwapi/templates/base';
						$this->add_file($filename, $new_root, 5);
						return true;
					case 5:
						$this->add_file($filename, $rootdir, 6);
						return true;
					case 6:
						$this->halt("filename: file $new_filename.xsl does not exist.");
						break;
					default:
						$this->add_file($filename, $rootdir, 2);
						return true;
				}
			}
			else
			{
				$this->xslfiles[$filename] = $new_filename.'.xsl';
			}
		}

		function set_var($name, $value, $append = false)
		{
			if($append)
			{
				if (is_array($value))
				{
					foreach ( $value as $key => $val )
					{

						if (is_array($val) && is_array($this->vars[$name][$key]))
						{
							$this->vars[$name][$key] = array_merge($this->vars[$name][$key],$val);
						}
						else
						{
							$this->vars[$name][$key] .= $val;
						}
					}
				}
			}
			else
			{
				$this->vars[$name] = $value;
			}
		}

		function set_xml($xml, $append = false)
		{
			if(!$append)
			{
				$this->xmlvars = $xml;
			}
			else
			{
				$this->xmlvars .= $xml;
			}
		}

		function set_xml_data($xml)
		{
			$this->xmldata = $xml;
		}

		function get_var($name)
		{
			return $this->vars[$name];
		}

		function get_vars()
		{
			return $this->vars;
		}

		function get_xml()
		{
			return $this->xmlvars;
		}

		/**
		* Parse the xsl-stylesheets
		*
		* @param string $ignored this value is now ignored and is only kept as a transitional hack
		*/
		function xsl_parse()
		{
			if ( is_array($this->xslfiles) && count($this->xslfiles) )
			{
				$this->xsldata = <<<XSLT
<?xml version="1.0" encoding="UTF-8"?>
	<!DOCTYPE xsl:stylesheet [
		<!ENTITY nl "&#10;">
		<!ENTITY nbsp "&#160;">
		]>
	<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
		xmlns:phpgw="http://phpgroupware.org/functions"
		xmlns:func="http://exslt.org/functions"
		extension-element-prefixes="func" 
		exclude-result-prefixes="phpgw"
		>

XSLT;
				switch ( $this->output )
				{
					case 'wml':
						$this->xsldata .= '<xsl:output method = "xml" encoding="utf-8"  doctype-public="-//WAPFORUM//DTD WML 1.3//EN" doctype-system="http://www.wapforum.org/DTD/wml13.dtd" indent="yes" />'."\n";				
						break;

					case 'html5':
						$this->xsldata .= '<xsl:output  method="xml" doctype-system="about:legacy-compat" encoding="UTF-8" indent="yes" />'."\n";				
						break;

					case 'html':
					default:
 						$this->xsldata .= '<xsl:output method="html" version="4.01" encoding="utf-8" indent="yes" omit-xml-declaration="yes" standalone="yes" doctype-system="http://www.w3.org/TR/html4/loose.dtd" doctype-public="-//W3C//DTD HTML 4.01//EN" media-type="text/html"/>' . "\n";
				}
				
				$this->xsldata .= <<<XSLT
		<xsl:template match="/">
			<xsl:apply-templates select="PHPGW" />
		</xsl:template>

XSLT;

				foreach ( $this->xslfiles as $xslfile )
				{
					$this->xsldata .= "\n<!-- XSL File: {$xslfile} -->\n";
					$this->xsldata .= file_get_contents($xslfile);
				}
				$this->xsldata .= '</xsl:stylesheet>'."\n";
			}
			else
			{
				die('Error: No XSL files have been selected');
			}
			return $this->xsldata;
		}

		function xml_parse()
		{
			if(strlen($this->xmldata)== 0)
			{
				$xmldata = $this->vars;
	
				/* auto generate xml based on vars */
				foreach ( $this->xmlvars as $key => $value )
				{
					$xmldata[$key] = $value;
				}
	
			//	$this->xmldata = var2xml('PHPGW', $xmldata);
			//  use simplexml - it's faster.
				$this->xmldata = phpgwapi_xmlhelper::toXML($xmldata, 'PHPGW');
			}

			$debug = false;
		//	$debug = true;			
			if ($debug)
			{
				//$this->xmldata = str_replace("\n",'' ,$this->xmldata);
				$doc = new DOMDocument;
				$doc->preserveWhiteSpace = true;
				$doc->loadXML( $this->xmldata );
				$doc->formatOutput = true;
				$xml = $doc->saveXML();
				unset($doc);

				echo "<textarea cols='200' rows='20'>";
				echo $xml;
				echo "</textarea><br>";
			}
			
			return $this->xmldata;
		}

		function list_lineno($xmldata, $format = false)
		{
			if ($format)
			{
				$doc = new DOMDocument;
				$doc->preserveWhiteSpace = false;
				$doc->loadXML( $xmldata );
				$doc->formatOutput = true;
				$xml = $doc->saveXML();
				unset($doc);
			}
			else
			{
				$xml = $xmldata;
			}

			$lines = explode("\n", $xml);
			unset($xml);
			unset($xmldata);
			echo "<ol class=\"source\">\n";
			foreach ( $lines as $line )
			{
				echo "<li>" . htmlentities($line,ENT_COMPAT,'utf-8') . "</li>\n";
			}
			echo "</ol>\n";
		}

		function parse($parsexsl = true, $parsexml = true)
		{
			$output_header = !(isset($GLOBALS['phpgw_info']['flags']['noframework']) && $GLOBALS['phpgw_info']['flags']['noframework']);
			
			$stripped_htm	= phpgw::get_var('phpgw_return_as') == 'stripped_html';
			
			if ( $this->output != 'wml' && !$stripped_htm)
			{
				$GLOBALS['phpgw']->common->phpgw_header($output_header);
			}

			if($parsexsl)
			{
				$this->xsl_parse();
			}

			if($parsexml)
			{
				$this->xml_parse();
			}

			$xml = new DOMDocument;
			$xml->loadXML($this->xmldata);

			$xsl = new DOMDocument;
			$xsl->loadXML($this->xsldata);

			// Configure the transformer
			$proc = new XSLTProcessor;
			$proc->registerPHPFunctions();
			$proc->importStyleSheet($xsl); // attach the xsl rules

			$html =  trim($proc->transformToXML($xml));

			if (!$html || $html == '<?xml version="1.0"?>')
			{
				$message ='Systemfeil - kontakt adminstrator';

				if(!isset($GLOBALS['phpgw_info']['user']['apps']['admin']) || !$GLOBALS['phpgw_info']['user']['apps']['admin'])
				{
					phpgwapi_cache::message_set($message, 'error');
				}
				else
				{
					$message .= '<br>' . $_SERVER['SERVER_ADDR'] . $_SERVER['SCRIPT_NAME'];
					$message .= isset($_REQUEST['menuaction']) ? "?menuaction={$_REQUEST['menuaction']}" : '';
					echo '<div class="error">';
					echo $message;
					echo '</div>';
					_debug_array(libxml_get_last_error());
					echo "<h2>xml-data</h2>";
					$this->list_lineno($this->xmldata, true);

					echo "<h2>xsl-data</h2>";
					$this->list_lineno($this->xsldata);
				}

				return '';
			}

			switch ( $this->output)
			{
				case 'wml':
				case 'html5':
					$html = preg_replace('/<\?xml version([^>])+>/', '', $html);
					break;
				default:
			}

			$html = preg_replace('/<!DOCTYPE([^>])+>/', '', $html);

			return $html;
		}

		function pparse()
		{
			print $this->parse();
			return false;
		}
		function pp()
		{
			return $this->pparse();
		}
	}

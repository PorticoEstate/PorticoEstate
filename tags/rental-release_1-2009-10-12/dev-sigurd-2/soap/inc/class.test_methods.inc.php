<?php
	/**
	* phpGroupWare Soap Application
	*
	* test_methods class
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @licence http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package soap
	* @version $Id$
	* @author Caeies (caeies@phpgroupware.org) / Original idea from Joseph Engo <jengo@phpgroupware.org> ?
	*/

	/*
	* That's only proof of concept ...
	*/
	class test_methods
	{
		var $client = null;
		var $servers = array();
		var $mparams = array(
			'echoVoid' => '',
			'echoString' => array('inputString' => 'Hello Server !'),
			'echoInteger' => array( 'inputInteger' => 42 ),
			'echoFloat' => array( 'inputFloat' => 42.42 ),
			'echoStringArray' => array('inputStringArray' => array('Hello Server !', 'Hello Client !', 'Hi Skwashd !') ),
			'echoIntegerArray' => array('inputIntegerArray' => array( 42, -4242, 42424242)),
			'echoFloatArray' => array('inputFloatArray' => array( 42.42, 42.4242, 1.345)),
			'echoStruct' => array('inputStruct' => array( 'varString' => 'test_value', 'varInt' => '34', 'varFloat' => '42.42')),
			'echoStructArray' => array('inputStructArray' => array( 'test_key' => array( 'varString' => 'test_value', 'varInt' => '34', 'varFloat' => '42.42'), 'test_key 2' => array( 'varString' => 'test_value', 'varInt' => '34', 'varFloat' => '42.42'), 'item' => array( 'varString' => 'plop', 'varInt' => '1', 'varFloat' => 45.456789 )))
			);
		function test_methods()
		{
			$this->public_functions = array( 'uitest_methods' => True);
			$this->app			= (isset($_REQUEST['app']) && $_REQUEST['app'])?$_REQUEST['app']:$GLOBALS['phpgw_info']['flags']['currentapp'];
			if(!$this->client)
			{
				$this->client = CreateObject('phpgwapi.soapclient','http://www.xmethods.net/interfaces/query.wsdl', true);
			}
			$this->servers = $GLOBALS['phpgw']->session->appsession('server', 'soap');
			if(!is_array($this->servers))
			{
				$this->servers = $this->client->call('getAllServiceSummaries', '', 'http://www.xmethods.net/interfaces/query');
				$GLOBALS['phpgw']->session->appsession('server', 'soap', $this->servers);
			}
			//Allow us to save a soapclient object !
			$this->xmlschema = CreateObject('phpgwapi.soap_XMLSchema');
			$this->wsdl = CreateObject('phpgwapi.wsdl');
		}

		function uitest_methods($content = '')
		{
			$soapClients = array();
			$array_link = array();
			if($this->servers === false || !is_array($this->servers) ||isset($this->servers['faultcode']) )
			{
				_debug_array('wrong server');
				_debug_array($this->servers);
				_debug_array($this->client);
			}
			$nameid = get_var('nameid');
			$function = get_var('function');
			if(empty($nameid))
			{
				foreach($this->servers as $k => $server)
				{
					$link = array('menuaction' => 'soap.test_methods.uitest_methods',
								'nameid' => "id$k");
					$array_link[] = '<p><a href="'.$GLOBALS['phpgw']->link('/index.php', $link).'">'.$server['name'].' '. $server['shortDescription'].' (wsdl : '.$server['wsdlURL'].')</a></p>';
				}
			}
			elseif(empty($function))
			{
				$id = str_replace('id', '', $nameid);
				$server = $this->servers[$id];
				if(!empty($server['wsdlURL']))
				{
					$soapclient = CreateObject('phpgwapi.soapclient', $server['wsdlURL'], true);
					$operations = $soapclient->operations; // well that's not the way to proceed, but don't find another one ...
					if(!is_array($operations) || count($operations) == 0)
					{
						unset($soapclient);
						$soapclient = CreateObject('phpgwapi.soapclient', $server['wsdlURL']);
					}
				}
				else
				{
					$soapclient = CreateObject('phpgwapi.soapclient', $server['wsdlURL']);
					$operations = array();
				}
				$error = $soapclient->GetError();
				if($error)
				{
					_debug_array($error);
					die('Avoid endless wait !');
				}
				if(count($operations) == 0)
				{
					_debug_array('Defaulting to "classical methods"');
					$operations = array(
						'echoVoid' => array(),
						'echoString' => array(),
						'echoInteger' => array(),
						'echoFloat'=> array(),
						'echoStringArray' => array(),
						'echoIntegerArray' => array(),
						'echoFloatArray' => array()
						);
				}
				$operations += array( 'all' => array()); //for all methods
				foreach($operations as $operation => $var)
				{
					$link = array('menuaction' => 'soap.test_methods.uitest_methods',
								'nameid' => "id$id",
								'function' => $operation
								);
					$array_link[] = '<p><a href="'.$GLOBALS['phpgw']->link('/index.php', $link).'">'.$server['name'].'=>'.$operation.'(...) </a></p>';
				}
				
				$GLOBALS['phpgw']->session->appsession('id'.$id, 'soap', $operations);
				$GLOBALS['phpgw']->session->appsession('serv'.$id, 'soap', $soapclient);
			}
			else
			{
				$id = str_replace('id', '', $nameid);
				$server = $this->servers[$id];
				$operations = $GLOBALS['phpgw']->session->appsession('id'.$id, 'soap');
				$soapclient = $GLOBALS['phpgw']->session->appsession('serv'.$id, 'soap');
				if(is_array($operations) && is_object($soapclient))
				{
					unset($operations['all']);
					$function = strtolower($function);
					foreach($operations as $operation => $val)
					{
						if($function == 'all' || $function == $operation)
						{
							if(isset($this->mparams[$operation]))
							{
								$array_link[] = '<h2>'.$server['name'].' => '.$operation.'</h2>';
								$array_link[] = lang('Args passed :');
								$array_link[] = $this->mparams[$operation];
								$array_link[] = lang('Result :');
								$array_link[] = $soapclient->call($operation, $this->mparams[$operation], $server['methodNamespace']);
								$error = $soapclient->getError();
								if(!empty($error))
								{
									$array_link[] = '<p><pre>GET ERROR : '.htmlentities($error).'</pre></p>';
									$array_link[] = $soapclient->GetOperationData($operation);
									$array_link[] = '<code>'.htmlentities($soapclient->response).'</code>';
								}
							}
							else
							{
								$array_link[] = '<p>'.lang('Sorry, Unknow method for %1 %2', $server['name'], $operation).'</p>';
							}
						}
					}
				}
				else
				{
					$array_link[] = '<p>'.lang('Sorry, I do not find any info about the server `%1\'', $server['name']).'</p>';
				}
				
			}
			$GLOBALS['phpgw']->common->phpgw_header(true);
			if(count($array_link))
				foreach($array_link as $link)
				{
					if(is_string($link))
					{
						echo $link;
					}
					else
					{
						_debug_array($link);
					}
				}
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}
?>

<?php
	// Java-bin path
	define('JAVA_BIN', '/usr/bin/java');

	// path to JasperEngine.class
	define('JASPER_BIN', PHPGW_API_INC.'/jasper/bin/');

	# path to the Jasper libraries
	# N.B. should always end with a /
	define ('JASPER_LIBS', PHPGW_API_INC .'/jasper/lib/');

	class phpgwapi_jasper_wrapper
	{

		public function __construct()
		{
			if (stristr(PHP_OS, 'WIN')) 
			{ 
				$sep = ';';// Win 
			}
			else
			{ 
				$sep = ':';// Other
			}

			$java_classpath = "{$sep}.{$sep}";
			foreach (glob(JASPER_LIBS . "*.jar") as $filename) 
			{
				$java_classpath .=  $filename . $sep;
			}
			$this->java_classpath = $java_classpath;

			$_key = $GLOBALS['phpgw_info']['server']['setup_mcrypt_key'];
			$_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
			$crypto = createObject('phpgwapi.crypto',array($_key, $_iv));

			$db_host = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_host']);
			$db_port=  $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_port']);
			$db_name = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_name']);
			$this->db_user = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_user']);
			$this->db_pass = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_pass']);
			$this->connection_string = "";

			if ($GLOBALS['phpgw_info']['server']['db_type'] == "postgres")
			{
				$db_port = $db_port ? $db_port : '5432';
				$this->connection_string = "jdbc:postgresql://{$db_host}:{$db_port}/{$db_name}";
			} 
			elseif ($GLOBALS['phpgw_info']['server']['db_type'] == "mysql") 
			{
				$db_port = $db_port ? $db_port : '3306';
				$this->connection_string = "jdbc:mysql://{$db_host}:{$db_port}/{$db_name}";
			}
		}

		/**
		* create jasper config information used for input for executing the report
		*
		* @param string $report_source full path to the jrxml-report definition file
		*
		* @return array Array with referense to the config-file and report name
		*/

		protected static function _create_jasper_info($report_source)
		{
			$info				= pathinfo($report_source);
			$base_name 			= basename($report_source,'.'.$info['extension']);
			$report_name 		= "report_{$base_name}";

			$memory = xmlwriter_open_memory();
			xmlwriter_start_document($memory,'1.0','UTF-8');
			xmlwriter_start_element ($memory,'JasperConfig'); // <JasperConfig>
				xmlwriter_start_element ($memory,'Reports'); // <Reports>	
					xmlwriter_start_element ($memory,'Report'); // <Report>			
						xmlwriter_write_attribute( $memory, 'name', $report_name);
						xmlwriter_write_attribute( $memory, 'source', $report_source);
					xmlwriter_end_element($memory); // </Report>
				xmlwriter_end_element($memory); // </Reports>
			xmlwriter_end_element($memory); // </JasperConfig>

			$xml = xmlwriter_output_memory($memory,true);

			$jasper_info = array
			(
				'config'		=> $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . uniqid('config_') . "{$base_name}.xml",
				'report_name'	=> $report_name
			);

			$fp = fopen($jasper_info['config'], "wb");
			fwrite($fp,$xml);

			if( !fclose($fp) )
			{
				throw new Exception('jasper_wrapper::create_jasper_config did not write any config file');
			}
			return $jasper_info;
		}

		/**
		* 'parameters' will be in the following format:
		* 'key1|value1;key2|value2;key3|value3' where key1, key2 ... keyX are
		*  unique
		*/

		public function execute($parameters = '', $output_type, $report_source, $return_content = false) 
		{
			if( !$parameters )
			{
				$parameters = '"DUMMY|1"';
			}

			if (!chdir(JASPER_BIN)) 
			{
				throw new Exception('jasper_wrapper::execute ' . lang('Unable to perform chdir'));
			}

			try
			{
				$jasper_info = self::_create_jasper_info($report_source);
			}
			catch(Exception $e)
			{
				throw $e;
				return false;
			}

			$report_name = $jasper_info['report_name'];
			$cmd = sprintf("CLASSPATH=%s %s -D%s JasperEngine -p %s -t %s -n %s -d %s -u %s -P %s %s",
							$this->java_classpath,
							JAVA_BIN,
							'java.awt.headless=true', // To run the environment with a headless implementation (when apache-user can't connect to X11)
							$parameters,
							$output_type,
							$report_name,
							$this->connection_string,
							$this->db_user,
							$this->db_pass,
							$jasper_info['config']);

			exec($cmd, $cmd_output, $retval);
//			echo $cmd . ":retval: " . $retval;
//			_debug_array($parameters);
//			_debug_array( $cmd_output);
//			exit(0);

			if(is_file($jasper_info['config']))
			{
				unlink($jasper_info['config']);
			}

			switch ($retval) 
			{
				case 201:
					$error = lang('Corrupt template');
					break;

				case 202:
					$error = lang('Unable to fill report');
					break;

				case 203:
					$error = lang('Corrupt report object');
					break;

				case 204:
					$error = lang('Unable to export to PDF');
					break;

				case 205:
					$error = lang('Unable to export to CSV');
					break;

				case 206:
					$error = lang('Unable to export to XLS');
					break;

				case 207:
					$error = lang('Unable to parse configuration');
					break;

				case 208:
					$error = lang('Invalid output-type provided');
					break;

				case 209:
					$error = lang('Unable to load the MySQL driver');
					break;

				case 210:
					$error = lang('Unable to load the PostgreSQL driver');
					break;

				case 211:
					$error = lang('Unable to connect to database');
					break;

				case 212:
					$error = lang('Missing report-name');
					break;

				case 213:
					$error = lang('Invalid report-name');
					break;

				case 214:
					$error = lang('Invalid report-name');
					break;

				case 215:
					$error = lang('Missing connection-string');
					break;

				case 216:
					$error = lang('Missing DB-username');
					break;

				case 217:
					$error = lang('Missing DB-password');
					break;

				case 218:
					$error = lang('Unable to export to XHTML');
					break;

				case 219:
					$error = lang('Unable to export to DOCX');
					break;

				case 0:
					$output = join("\n", $cmd_output);
					if ($output_type == 'PDF') 
					{
						$mime= 'application/pdf';
						$filename ="{$report_name}.pdf"; 
					} 
					else if ($output_type == 'CSV') 
					{
						$mime= 'text/csv';
						$filename ="{$report_name}.csv"; 
					} 
					else if ($output_type == 'XLS') 
					{
						$mime= 'application/vnd.ms-excel';
						$filename ="{$report_name}.xls"; 
					} 
					else if ($output_type == 'XHTML') 
					{
						$mime= 'text/html';
						$filename ="{$report_name}.html"; 
					} 
					else if ($output_type == 'DOCX') 
					{
						$mime= 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
						$filename ="{$report_name}.docx"; 
					} 
					else 
					{ // should never arise                                                                                                                                                           
						$mime= 'application/octet-stream';
						$filename ="{$report_name}.dat"; 
					}
			}
			if(isset($error) && $error)
			{
				throw new Exception($error);
			}
			if($return_content)
			{
				return array
				(
					'content'	=> $output,
					'mime'		=> $mime,
					'filename'	=> $filename
				);			
			}
			else
			{
				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($filename,$mime,strlen($output));
				echo $output;
				if($output)
				{
					$GLOBALS['phpgw']->common->phpgw_exit();
				}
				return false;
			}
		}
	}

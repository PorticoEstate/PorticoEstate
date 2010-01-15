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
		# path to the Jasper config file (containing the report-list) and report name
		public $jasper_info = array
		(
			'config'		=> false,
			'report_name'	=> false
		);

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
			$db_name = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_name']);
			$this->db_user = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_user']);
			$this->db_pass = $crypto->decrypt($GLOBALS['phpgw_info']['server']['db_pass']);
			$this->connection_string = "";

			if ($GLOBALS['phpgw_info']['server']['db_type'] == "postgres") 
			{
				$this->connection_string = "jdbc:postgresql://".$db_host.":5432/".$db_name;
			} 
			elseif ($GLOBALS['phpgw_info']['server']['db_type'] == "mysql") 
			{
				$this->connection_string = "jdbc:postgresql://".$db_host.":3306/".$db_name;
			}
		}

		/**
		* create jasper config information used for input for executing the report
		*
		* @param string $report_source full path to the jrxml-report definition file
		*
		* @return array Array with referense to the config-file and report name
		*/

		public function create_jasper_info($report_source)
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
				'config'		=> "{$GLOBALS['phpgw_info']['server']['temp_dir']}/config_{$base_name}.xml",
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

		public function execute($parameters, $output_type, &$err) 
		{
			if (!chdir(JASPER_BIN)) 
			{
				$err['chdir'] = lang('Unable to perform chdir');
				return 102;
			}

			$report_name = $this->jasper_info['report_name'];
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
							$this->jasper_info['config']);

			exec($cmd, $cmd_output, $retval);
		//	echo $cmd . ":retval: " . $retval;
		//	exit(0);

			switch ($retval) 
			{
				case 201:
					$err['corrupt template'] = lang('Corrupt template');
					break;

				case 202:
					$err['fill report'] = lang('Unable to fill report');
					break;

				case 203:
					$err['report object'] = lang('Corrupt report object');
					break;

				case 204:
					$err['pdf'] = lang('Unable to export to PDF');
					break;

				case 205:
					$err['csv'] = lang('Unable to export to CSV');
					break;

				case 206:
					$err['xls'] = lang('Unable to export to XLS');
					break;

				case 207:
					$err['parse'] = lang('Unable to parse configuration');
					break;

				case 208:
					$err['invalid output'] = lang('Invalid output-type provided');
					break;

				case 209:
					$err['mysql driver'] = lang('Unable to load the MySQL driver');
					break;

				case 210:
					$err['psql driver'] = lang('Unable to load the PostgreSQL driver');
					break;

				case 211:
					$err['connect'] = lang('Unable to connect to database');
					break;

				case 212:
					$err['no name'] = lang('Missing report-name');
					break;

				case 213:
					$err['invalid rname'] = lang('Invalid report-name');
					break;

				case 214:
					$err['invalid rname'] = lang('Missing configuration file');
					break;

				case 215:
					$err['missing cs'] = lang('Missing connection-string');
					break;

				case 216:
					$err['missing du'] = lang('Missing DB-username');
					break;

				case 217:
					$err['missing dp'] = lang('Missing DB-password');
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
					else 
					{ // should never arise                                                                                                                                                           
						$mime= 'application/octet-stream';
						$filename ="{$report_name}.dat"; 
					}

				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($filename,$mime,strlen($output));
				echo $output;
			}
		}
	}

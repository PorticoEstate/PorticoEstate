<?php
	/**
	* Timed Asynchron Services for creating cron-job like timed calls of phpGroupWare methods
	* @author Ralf Becker <RalfBecker@outdoor-training.de>
	* @copyright Copyright (C) 2003,2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id$
	*/

	/**
	* Timed Asynchron Services for creating cron-job like timed calls of phpGroupWare methods
	*
	* The class implements a general phpGW service to execute callbacks at a given time.
	* @package phpgwapi
	* @subpackage application
	* @link http://www.phpgroupware.org/wiki/TimedAsyncServices
	*/
	class asyncservice
	{
		var $public_functions = array
		(
			'set_timer' => True,
			'check_run' => True,
			'cancel_timer' => True,
			'read'      => True,
			'install'   => True,
			'installed' => True,
			'last_check_run' => True
		);
		var $php = '';
		var $crontab = '';
		var $db;
		var $db_table = 'phpgw_async';
		var $debug = false;
		protected $Exception_On_Error = false;

		/**
		* Constructor
		*/
		function __construct()
		{
			$this->db =& $GLOBALS['phpgw']->db;
			$domain = isset($GLOBALS['phpgw_info']['user']['domain']) ? $GLOBALS['phpgw_info']['user']['domain'] : 'default';
			$this->cronline = PHPGW_SERVER_ROOT . '/phpgwapi/cron/asyncservices.php '.$domain;
			$this->only_fallback = substr(php_uname(), 0, 7) == "Windows";	// atm cron-jobs dont work on win
			$this->Exception_On_Error =	$GLOBALS['phpgw']->db->Exception_On_Error; // continue on dberror
		}

		/**
		* Calculates the next run of the timer and puts that with the rest of the data in the db for later execution.
		*
		* @param integer|array $times Unix timestamp or array('min','hour','dow','day','month','year') with execution time. Repeated events are possible to schedule by setting the array only partly, eg. array('day' => 1) for first day in each month 0am or array('min' => '* /5', 'hour' => '9-17') for every 5mins in the time from 9am to 5pm.
		* @param string $id Unique id to cancel the request later, if necessary. Should be in a form like eg. '<app><id>X' where id is the internal id of app and X might indicate the action.
		* @param string $method Method to be called via ExecMethod($method,$data). $method has the form '<app>.<class>.<public function>'.
		* @param integer|array $data This data is passed back when the method is called. It might simply be an integer id, but it can also be a complete array.
		* @param integer|boolean $account_id account_id, under which the methode should be called or False for the actual user
		* @return boolean False if $id already exists, otherwise True
		*/
		function set_timer($times,$id,$method,$data,$account_id=False)
		{
			if ( empty($id) || empty($method) || $this->read($id)
				|| !($next = $this->next_run($times)) )
			{
				return false;
			}

			if ($account_id === False)
			{
				$account_id	= isset($GLOBALS['phpgw_info']['user']['account_id']) ? (int)$GLOBALS['phpgw_info']['user']['account_id'] : 0;
			}

			$job = array
			(
				'id'     => $id,
				'next'   => $next,
				'times'  => $times,
				'method' => $method,
				'data'   => $data,
				'account_id' => $account_id
			);
			$this->write($job);

			return true;
		}

		/**
		* Calculates the next execution time for $times
		*
		* @param integer|array $times unix timestamp or array('year'=>$year,'month'=>$month,'dow'=>$dow,'day'=>$day,'hour'=>$hour,'min'=>$min) with execution time. Repeated execution is possible to shedule by setting the array only partly, eg. array('day' => 1) for first day in each month 0am or array('min' => '/5', 'hour' => '9-17') for every 5mins in the time from 9am to 5pm. All not set units before the smallest one set, are taken into account as every possible value, all after as the smallest possible value.
		* @param boolean $debug if True some debug-messages about syntax-errors in $times are echoed
		* @return integer|boolean A unix timestamp of the next execution time or False if no more executions
		*/
		function next_run($times,$debug=False)
		{
			if ($this->debug)
			{
				echo "<p>next_run("; print_r($times); ",'$debug')</p>\n";
				$debug = True;	// enable syntax-error messages too
			}
			$now = time();

			// $times is unix timestamp => if it's not expired return it, else False
			if (!is_array($times))
			{
				$next = intval($times);

				return $next > $now ? $next : False;
			}
			// If an array is given, we have to enumerate the possible times first
			$units = array
			(
				'year'  => 'Y',
				'month' => 'm',
				'day'   => 'd',
				'dow'   => 'w',
				'hour'  => 'H',
				'min'   => 'i'
			);
			$max_unit = array
			(
				'min'   => 59,
				'hour'  => 23,
				'dow'   => 6,
				'day'   => 31,
				'month' => 12,
				'year'  => date('Y')+10	// else */[0-9] would never stop returning numbers
			);
			$min_unit = array
			(
				'min'   => 0,
				'hour'  => 0,
				'dow'   => 0,
				'day'   => 1,
				'month' => 1,
				'year'  => date('Y')
			);

			// get the number of the first and last pattern set in $times,
			// as empty patterns get enumerated before the the last pattern and
			// get set to the minimum after
			$n = $first_set = $last_set = 0;
			foreach($units as $u => $date_pattern)
			{
				++$n;
				if (isset($times[$u]))
				{
					$last_set = $n;

					if (!$first_set)
					{
						$first_set = $n;
					}
				}
			}

			// now we go through all units and enumerate all patterns and not set patterns
			// (as descript above), enumerations are arrays with unit-values as keys
			//
			$n = 0;
			foreach($units as $u => $date_pattern)
			{
				++$n;
				if ($this->debug)
				{
					echo "<p>n=$n, $u: isset(times[$u]=";
					print_r($times[$u]);
					echo ")=".(isset($times[$u])?'True':'False')."</p>\n";
				}
				if (isset($times[$u]))
				{
					$time = explode(',',$times[$u]);

					$times[$u] = array();

					foreach($time as $t)
					{
						if (strstr($t,'-') !== False && strstr($t,'/') === False)
						{
							list($min,$max) = $arr = explode('-',$t);

							if (count($arr) != 2 || !is_numeric($min) || !is_numeric($max) || $min > $max)
							{
								if ($this->debug) echo "<p>Syntax error in $u='$t', allowed is 'min-max', min <= max, min='$min', max='$max'</p>\n";

								return False;
							}
							for ($i = intval($min); $i <= $max; ++$i)
							{
								$times[$u][$i] = True;
							}
						}
						else
						{
							if ($t == '*')
							{
								$t = '*/1';
							}

							list($one,$inc) = $arr = explode('/',$t);

							if (!(is_numeric($one) && count($arr) == 1 ||
								  count($arr) == 2 && is_numeric($inc)))
							{
								if ($this->debug)
								{
									echo "<p>Syntax error in $u='$t', allowed is a number or '{*|range}/inc', inc='$inc'</p>\n";
								}

								return False;
							}
							if (count($arr) == 1)
							{
								$times[$u][intval($one)] = True;
							}
							else
							{
								list($min,$max) = $arr = explode('-',$one);
								if (empty($one) || $one == '*')
								{
									$min = $min_unit[$u];
									$max = $max_unit[$u];
								}
								else if (count($arr) != 2 || $min > $max)
								{
									if ($this->debug)
									{
										echo "<p>Syntax error in $u='$t', allowed is '{*|min-max}/inc', min='$min',max='$max', inc='$inc'</p>\n";
									}
									return False;
								}
								for ($i = $min; $i <= $max; $i += $inc)
								{
									$times[$u][$i] = True;
								}
							}
						}
					}
				}
				else if ($n < $last_set || $u == 'dow')	// before last value set (or dow) => empty gets enumerated
				{
					for ($i = $min_unit[$u]; $i <= $max_unit[$u]; ++$i)
					{
						$times[$u][$i] = True;
					}
				}
				else	// => after last value set => empty is min-value
				{
					$times[$u][$min_unit[$u]] = True;
				}
			}
			if ($this->debug)
			{
				echo "enumerated times=<pre>"; print_r($times); echo "</pre>\n";

			}

			// now we have the times enumerated, lets find the first not expired one
			$found = array();
			while (!isset($found['min']))
			{
				$future = False;

				foreach($units as $u => $date_pattern)
				{
					$unit_now = $u != 'dow' ? intval(date($date_pattern)) :
						intval(date($date_pattern,mktime(12,0,0,$found['month'],$found['day'],$found['year'])));

					if (isset($found[$u]))
					{
						$future = $future || $found[$u] > $unit_now;
						if ($this->debug)
						{
							echo "--> already have a $u = ".$found[$u].", future='$future'<br>\n";
						}
						continue;	// already set
					}
					foreach($times[$u] as $unit_value => $nul)
					{
						switch($u)
						{
							case 'dow':
								$valid = $unit_value == $unit_now;
								break;
							case 'min':
								$valid = $future || $unit_value > $unit_now;
								break;
							default:
								$valid = $future || $unit_value >= $unit_now;
								break;

						}
						if ($valid && ($u != $next || $unit_value > $over))	 // valid and not over
						{
							$found[$u] = $unit_value;
							$future = $future || $unit_value > $unit_now;
							break;
						}
					}
					if (!isset($found[$u]))		// we have to try the next one, if it exists
					{
						$next = array_keys($units);
						if (!isset($next[count($found)-1]))
						{
							if ($this->debug)
							{
								echo "<p>Nothing found, exiting !!!</p>\n";
							}
							return False;
						}
						$next = $next[count($found)-1];
						$over = $found[$next];
						unset($found[$next]);
						if ($this->debug)
						{
							echo "<p>Have to try the next $next, $u's are over for $next=$over !!!</p>\n";
						}
						break;
					}
				}
			}
			if ($this->debug)
			{
				echo "<p>next="; print_r($found); echo "</p>\n";
			}

			return mktime($found['hour'],$found['min'],0,$found['month'],$found['day'],$found['year']);
		}

		/**
		* Cancel a timer
		*
		* @param integer $id ID of timer to cancel
		* @return boolean True if the timer exists and is not expired, otherwise false
		*/
		function cancel_timer($id)
		{
			return $this->delete($id);
		}

		/**
		* Checks when the last check_run was run or set the run-semaphore if $semaphore == True
		*
		* @param bollean $semaphore If false only check, if true try to set/release the semaphore
		* @param boolean $release If $semaphore == True, tells if we should set or release the semaphore
		* @param string $run_by Unknown
		* @return boolean If !$set array('start' => $start,'end' => $end) with timestamps of last check_run start and end,  !$end means check_run is just running. If $set returns True if it was able to get the semaphore, else False
		*/
		function last_check_run($semaphore=False,$release=False,$run_by='')
		{
			//echo "<p>last_check_run(semaphore=".($semaphore?'True':'False').",release=".($release?'True':'False').")</p>\n";
			if ($semaphore)
			{
				$this->db->lock($this->db_table,'write');	// this will block til we get exclusive access to the table

				@set_time_limit(0);		// dont stop for an execution-time-limit
				ignore_user_abort(true);
			}
			if ($exists = $this->read('##last-check-run##'))
			{
				$last_run = current($exists);
			}
			else
			{
				$this->write(
					array(
					'id'		=> '##last-check-run##',
					'account_id'=> 0,
					'next'		=> 0,
					'times'		=> array(),
					'method'	=> 'none',
					'data'		=> array
					(
						'run_by'=> $run_by,
						'start' => time(),
						'end'   => 0
					)
				));

			}
			//echo "last_run (from db)=<pre>"; print_r($last_run); echo "</pre>\n";

			if (!$semaphore)
			{
				return $last_run['data'];
			}
			elseif (!$release && !isset($last_run['data']['end']) && isset($last_run['data']['start']) && $last_run['data']['start'] > time()-600)
			{
				// already one instance running (started not more then 10min ago, else we ignore it)

				$this->db->unlock();	// unlock the table again

				//echo "<p>An other instance is running !!!</p>\n";
				return false;
			}
			// no other instance runs ==> we should run
			//
			if ($release)
			{
				$last_run['data']['end'] = time();
			}
			else
			{
				$last_run = array(
					'id'		=> '##last-check-run##',
					'account_id'=> 0,
					'next'		=> 0,
					'times'		=> array(),
					'method'	=> 'none',
					'data'		=> array
					(
						'run_by'=> $run_by,
						'start' => time(),
						'end'   => 0
					)
				);
			}
			//echo "last_run=<pre>"; print_r($last_run); echo "</pre>\n";
			$this->write($last_run, true);
			$this->db->unlock();
			return true;
		}

		/**
		* Test if there are any jobs ready to run (timer expired) and executes them
		*
		* @param string $run_by Unknown
		* @return integer|boolean Number of jobs or false
		*/
		function check_run($run_by='')
		{
			@set_time_limit(0);		// dont stop for an execution-time-limit
			flush();
			$error = false;

			if (!$this->last_check_run(True,False,$run_by))
			{
				return False;	// cant obtain semaphore
			}
			if ($jobs = $this->read())
			{
				foreach($jobs as $id => $job)
				{
					// checking / setting up phpgw_info/user
					//
					if ($GLOBALS['phpgw_info']['user']['account_id'] != $job['account_id'])
					{
						$domain = $GLOBALS['phpgw_info']['user']['domain'];
						$lang   = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
						unset($GLOBALS['phpgw_info']['user']);

						if ($job['account_id'])
						{
							$GLOBALS['phpgw']->session->set_account_id($job['account_id']);
				//			$GLOBALS['phpgw']->session->account_domain = $domain;
							$GLOBALS['phpgw']->session->read_repositories(False,False);
							$GLOBALS['phpgw_info']['user']  = $GLOBALS['phpgw']->session->get_user();

							if ($lang != $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'])
							{
								$GLOBALS['phpgw']->translation->add_app('common');
							}
						}
						$GLOBALS['phpgw_info']['user']['domain'] = $domain;
						$GLOBALS['phpgw_info']['server']['default_domain'] = $domain;
					}
					list($app) = explode('.',$job['method']);
					$GLOBALS['phpgw']->translation->add_app($app);

					$GLOBALS['phpgw']->db->Exception_On_Error = true;

					if($job['next'] <= time())
					{
						try
						{
							echo 'Start job: ' . date('Y/m/d H:i:s ') . "\n";
							echo "--id: {$job['id']}\n";
							echo "--method: {$job['method']}\n";
							if(isset($job['data']) && $job['data'])
							{
								echo "--data: ";
								print_r($job['data']);
								echo "\n";
								$job['data']['cron'] = true;
							}
							ExecMethod($job['method'],$job['data']);

							echo 'End job: ' . date('Y/m/d H:i:s ') . "\n\n";

						}
						catch (Exception $e)
						{
							if($e)
							{
								$GLOBALS['phpgw']->log->error(array(
									'text'	=> 'asyncservice::check_run() : error when trying to execute %1. Error: %2',
									'p1'	=> $job['method'],
									'p2'	=> $e->getMessage(),
									'line'	=> __LINE__,
									'file'	=> __FILE__
								));

								// Do not throw further - it will stop the loop
								// in case of a manual run
								echo $e->getMessage() . "\n";
								continue;
							}
						}
					}

					$GLOBALS['phpgw']->db->Exception_On_Error = $this->Exception_On_Error;

					if ($job['next'] = $this->next_run($job['times']))
					{
						$updated_jobs = $this->read($id);
						if (isset($updated_jobs[$id]) && isset($updated_jobs[$id]['data']))
						{ // update async data field, it could be changed during ExecMethod()
							$job['data'] = $updated_jobs[$id]['data'];
						}
						// TK 20.11.06 write job to get 'next' and alarm updated
						$job['data']['time'] = $job['next'];
						$this->write($job);
					}
					else	// no further runs
					{
						if($job['next'] <= time())
						{
							$this->delete($job['id']);
						}
					}

				}
			}
			$this->last_check_run(True,True,$run_by);	// release semaphore

			return $jobs ? count($jobs) : False;
		}

		/**
		* Reads all matching db-rows / jobs
		*
		* @param integer $id =0 reads all expired rows / jobs ready to run != 0 reads all rows/jobs matching $id (sql-wildcards '%' and '_' can be used)
		* @return array|boolean Jobs as array or false if no matches
		*/
		function read($id=0)
		{
			$id = $this->db->db_addslashes($id);
			if (strpos($id,'%') !== False || strpos($id,'_') !== False)
			{
				$where = "id LIKE '$id' AND id!='##last-check-run##'";
			}
			elseif (!$id)
			{
				$where = 'next<='.time()." AND id!='##last-check-run##'";
			}
			else
			{
				$where = "id='$id'";
			}
			$this->db->query($sql="SELECT * FROM $this->db_table WHERE $where",__LINE__,__FILE__);

			$jobs = array();
			while ($this->db->next_record())
			{
				$id = $this->db->f('id');

				$jobs[$id] = array(
					'id'     => $id,
					'next'   => $this->db->f('next'),
					'times'  => unserialize($this->db->f('times')),
					'method' => $this->db->f('method'),
					'data'   => unserialize($this->db->f('data')),
					'account_id'   => $this->db->f('account_id')
				);
				//echo "job id='$id'<pre>"; print_r($jobs[$id]); echo "</pre>\n";
			}
			if (!count($jobs))
			{
				return False;
			}
			return $jobs;
		}

		/**
		* Write a job to the db
		*
		* @param array $job DB-row as array
		* @param boolean $exits If True we do an update otherwise we check if update or insert necesary
		*/
		function write($job,$exists = False)
		{
			$job['times'] = (isset($job['times'])?$this->db->db_addslashes(serialize($job['times'])):'');
			$job['data'] = (isset($job['data'])?$this->db->db_addslashes(serialize($job['data'])):'');
			$job['next']  = (isset($job['next'])?intval($job['next']):0);
			$job['account_id']  = (isset($job['account_id'])?intval($job['account_id']):0);
			$job['method']  = (isset($job['method'])?$job['method']:'');
			$job['id']  = (isset($job['id'])?$job['id']:'');

			if ($exists || $this->read($job['id']))
			{
				$this->db->query("UPDATE $this->db_table SET next=$job[next],times='$job[times]',".
					"method='$job[method]',data='$job[data]',account_id=$job[account_id] WHERE id='$job[id]'",__LINE__,__FILE__);
			}
			else
			{
				$this->db->query("INSERT INTO $this->db_table (id,next,times,method,data,account_id) VALUES ".
					"('$job[id]',$job[next],'$job[times]','$job[method]','$job[data]',$job[account_id])",__LINE__,__FILE__);
			}
		}

		/**
		* Delete job with $id
		*
		* @param integer $id Job id
		* @return boolean False when $id not found otherwise True
		*/
		function delete($id)
		{
			$this->db->query("DELETE FROM $this->db_table WHERE id='$id'",__LINE__,__FILE__);

			return $this->db->affected_rows();
		}

		function find_binarys()
		{
			static $run = False;
			if ($run)
			{
				return;
			}
			$run = True;

			if (substr(php_uname(), 0, 7) == "Windows")
			{
				// ToDo: find php-cgi on windows
			}
			else
			{
				$binarys = array(
					'php_local'		=> '/usr/local/bin/php',
					'php'			=> '/usr/bin/php',
					'php5'			=> '/usr/bin/php5',		// this is for debian
					'crontab'		=> '/usr/bin/crontab'
				);
				foreach ($binarys as $name => $path)
				{
					$this->$name = $path;	// a reasonable default for *nix
					if (!is_executable($this->$name))
					{
						if ($fd = popen('/bin/sh -c "which '.$name.'"','r'))
						{
							$this->$name = fgets($fd,256);
							@pclose($fd);
						}
						if ($pos = strpos($this->$name,"\n"))
						{
							$this->$name = substr($this->$name,0,$pos);
						}
					}
					if (!is_executable($this->$name))
					{
						$this->$name = $name;	// hopefully its in the path
					}
					//echo "<p>$name = '".$this->$name."'</p>\n";
				}
				if ($this->php_local[0] == '/')	// we found a homebrewed binary
				{
					$this->php = $this->php_local;
				}
				else if ($this->php5[0] == '/')	// we found a php5 binary
				{
					$this->php = $this->php5;
				}
			}
		}

		/**
		* Test if asyncservices is installed as cron-job
		*
		* @return integer|array|boolean The times asyncservices are run (normaly 'min'=>'* /5') or False if not installed or 0 if crontab not found
		* @internal Not implemented for Windows, always returns 0
		*/
		function installed()
		{
			if ($this->only_fallback) {
				return 0;
			}
			$this->find_binarys();

			if (!is_executable($this->crontab))
			{
				if($this->debug)
				{
					echo "<p>Error: $this->crontab not found !!!</p>";
				}
				return 0;
			}

			if (!is_executable($this->php))
			{
				if($this->debug)
				{
					echo "<p>Error: $this->php not found !!!</p>";
				}
				return 0;
			}

			$times = False;
			$this->other_cronlines = array();
			if (($crontab = popen('/bin/sh -c "'.$this->crontab.' -l" 2>&1','r')) !== False)
			{
				while ($line = fgets($crontab,256))
				{
					if ($this->debug) echo 'line '.++$n.": $line<br>\n";
					$parts = explode(' ',$line,6);

					if ($line[0] == '#' || count($parts) < 6 || ($parts[5][0] != '/' && substr($parts[5],0,3) != 'php'))
					{
						// ignore comments
						if ($line[0] != '#')
						{
							$times['error'] .= $line;
						}
					}
					elseif (strstr($line,$this->cronline) !== False)
					{
						$cron_units = array('min','hour','day','month','dow');
						foreach($cron_units as $n => $u)
						{
							$times[$u] = $parts[$n];
						}
						$times['cronline'] = $line;
					}
					else
					{
						$this->other_cronlines[] = $line;
					}
				}
				@pclose($crontab);
			}
			return $times;
		}

		/**
		* Installs asyncservices as cron-job
		*
		* @param array $times Array with keys 'min','hour','day','month','dow', not set is equal to '*'
		* @return integer|array|boolean The times asyncservices are run or False if they are not installed or 0 if crontab not found
		* @internal Not implemented for Windows, always returns 0
		*/
		function install($times)
		{
			if ($this->only_fallback) {
				return 0;
			}
			$this->installed();	// find other installed cronlines

			if (($crontab = popen('/bin/sh -c "'.$this->crontab.' -" 2>&1','w')) !== False)
			{
				$cron_units = array('min','hour','day','month','dow');
				foreach($cron_units as $cu)
				{
					$cronline .= (isset($times[$cu]) ? $times[$cu] : '*') . ' ';
				}
				$cronline .= $this->php.' -q '.$this->cronline."\n";
				//echo "<p>Installing: '$cronline'</p>\n";
				fwrite($crontab,$cronline);

				foreach ($this->other_cronlines as $cronline)
				{
					fwrite($crontab,$cronline);		// preserv the other lines
				}
				@pclose($crontab);
			}
			return $this->installed();
		}

		/**
		* UnInstalls asyncservices as cron-job
		*
		* @return integer|array|boolean The times asyncservices are run or False if they are not installed or 0 if crontab not found
		* @internal Not implemented for Windows, always returns 0
		*/
		function uninstall()
		{
			if ($this->only_fallback) {
				return 0;
			}
			$this->installed();	// find other installed cronlines

			if(isset($this->other_cronlines) && $this->other_cronlines)
			{
				if (($crontab = popen('/bin/sh -c "'.$this->crontab.' -" 2>&1','w')) !== False)
				{
					foreach ($this->other_cronlines as $cronline)
					{
						fwrite($crontab,$cronline);		// preserv the other lines
					}
					@pclose($crontab);
				}
			}
			else
			{
				system('crontab -r');
			}
			return $this->installed();
		}
	}

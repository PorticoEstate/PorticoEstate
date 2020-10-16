<?php

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'		=> true,
		'nonavbar'		=> true,
		'currentapp'	=> 'property'
	);

	include_once('../header.inc.php');


	$fudforum = new fudforum();
	$messages = $fudforum->get_messages();


	class fudforum
	{
		protected $db;

		function __construct()
		{
			$this->db = $this->get_db();
		}

		public function get_db()
		{
			if($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$fudforum_db = array (
				'db_host' => 'localhost',
				'db_name' => 'fudforum',
				'db_user' => 'root',
				'db_pass' => 'Fmsigg10',
				'db_type' => 'mysqli'
			);

			$host_info = explode(':', $fudforum_db['db_host']);

			$host = $host_info[0];
			$port = isset($host_info[1]) && $host_info[1] ? $host_info[1] : $fudforum_db['db_port'];

			$db           	= createObject('phpgwapi.db_adodb', null, null, true);
			$db->Host     	= $host;
			$db->Port     	= $port;
			$db->Type     	= $fudforum_db['db_type'];
			$db->Database 	= $fudforum_db['db_name'];
			$db->User     	= $fudforum_db['db_user'];
			$db->Password 	= $fudforum_db['db_pass'];
			$db->Halt_On_Error 	= 'yes';

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch(Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$this->db = $db;
			return $db;
		}

		function get_messages()
		{
			$sql = "SELECT fud26_msg.id as msg_id, fud26_msg.thread_id,fud26_msg.subject,"
				. " fud26_msg.foff, fud26_msg.length, fud26_msg.file_id as msg_file_id, fud26_msg.post_stamp, fud26_thread.tdescr,"
				. " fud26_forum.name, location, original_name, fsize"
				. " FROM fud26_msg "
				. " JOIN fud26_thread ON fud26_thread.id = fud26_msg.thread_id"
				. " JOIN fud26_forum ON fud26_forum.id = fud26_thread.forum_id"
				. " LEFT JOIN fud26_attach ON fud26_msg.id = fud26_attach.message_id"
				. " where fud26_msg.file_id is not null";



			$this->db->query($sql, __LINE__, __FILE__);
			$messages = array();
			while($this->db->next_record())
			{


				$messages[] = array(
					'msg_id'	=> $this->db->f('msg_id'),
					'forum'	=> utf8_encode($this->db->f('name',true)),
					'tdescr'	=> utf8_encode($this->db->f('tdescr',true)),
					'subject'	=> utf8_encode($this->db->f('subject', true)),
					'thread_id'	=> $this->db->f('thread_id'),
					'msg_file_id'	=> $this->db->f('msg_file_id'),
					'post_stamp'	=> $this->db->f('post_stamp'),
					'foff'	=> $this->db->f('foff'),
					'length'	=> $this->db->f('length'),
					'file_location'	=> $this->db->f('location'),
					'file_name'	=> utf8_encode($this->db->f('original_name')),
					'fsize'	=> $this->db->f('fsize'),
				);

			}



			$msg_store_dir = '/var/www/FUDforum/messages/';
			foreach ($messages as &$message)
			{
				$message['date'] = date('Ymd', $message['post_stamp']);
				$handle = fopen($msg_store_dir .'msg_'. $message['msg_file_id'], 'rb');
				// Read from file.
				fseek($handle, $message['foff']);
				$message['melding'] = fread($handle, $message['length']);
				fclose($handle);
			}


			unset($message);

			$msg_output_dir = '/opt/møtereferater/';

			$filetypes = array(
				'txt',
				'doc',
				'docx',
				'xsls',
				'jpg',
			);

			foreach ($messages as $message)
			{

				$base_file_name = str_replace(array(' '), array('_'), $message['forum'])
					. '_#_' . $message['date']
					. '_#_' .str_replace(array(' '), array('_'), $message['subject']);

				if($message['melding'])
				{
					$html =<<<HTML
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		{$message['melding']}
	</body>
</html>
HTML;

					$fp = fopen("{$msg_output_dir}/{$base_file_name}.html", 'w');
					fwrite($fp, $html);
					fclose($fp);

				}

				if($message['file_name'] && filesize($message['file_location']))
				{
					$suffix_arr = explode('.', $message['file_name']);
					$suffix = strtolower(end($suffix_arr));

					if(!in_array($suffix, $filetypes))
					{
						$suffix = 'doc';
					}

					$fp = fopen("{$msg_output_dir}/{$base_file_name}.{$suffix}", 'w');

					$handle = fopen($message['file_location'], "rb");
					$contents = fread($handle, filesize($message['file_location']));
					fclose($handle);

					fwrite($fp, $contents );
					fclose($fp);
				}
			}



			_debug_array($messages);die();
			return $messages;
		}
	}

	

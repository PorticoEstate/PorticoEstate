<?php
	/**
	 * phpGroupWare (http://phpgroupware.org/)
	 * SyncML interface
	 *
	 * @author    Johan Gunnarsson <johang@phpgroupware.org>
	 * @copyright Copyright (c) 2007 Free Software Foundation, Inc.
	 * @license   GNU General Public License 3 or later
	 * @package   syncml
	 * @version   $Id$
	 */

	define('METINF', 'xmlns="syncml:metinf"');

	function ec($cond, $name, $cdata, $attrs = '')
	{
		return $cond ? ("<$name" . ($attrs ? ' ' . $attrs : '') .
			($cdata === '' ? '/>' : ">$cdata</$name>")) : '';
	}

	class syncml_response
	{
		var $next_cmdid = 0;
		var $msg_id = 1;

		var $max_size;

		var $is_final = FALSE;
		var $global_status_code;

		var $root_namespace = '';
		var $header = '';
		var $header_cred = '';
		var $commands = array();

		var $status_count = 0;

		function syncml_response()
		{
			$this->root_namespace = 'SYNCML:SYNCML1.1';
		}

		function print_response()
		{
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<SyncML xmlns="', $this->root_namespace, '">';
			echo '<SyncHdr>', $this->header, $this->header_cred, '</SyncHdr>';
			echo '<SyncBody>', implode('', $this->commands);
			echo ($this->is_final ? '<Final/>' : ''), '</SyncBody>';
			echo '</SyncML>';
		}

		function filter($data)
		{
			return $data;
		}

		function status_commands_only()
		{
			return $this->status_count == count($this->commands);
		}

		function set_max_size($size)
		{
			$this->max_size = $size;
		}

		function get_size_left()
		{
			return $this->max_size
				- array_sum(array_map('strlen', $this->commands))
				- strlen($this->header)
				- strlen($this->header_cred)
				- 500;
		}

		/**
		 * Decide weather FINAL element should be appended to body.
		 *
		 * @param bool $final True if append, false if not.
		 */
		function set_final($final)
		{
			$this->is_final = $final;
		}

		function is_final()
		{
			return $this->is_final;
		}

		function set_global_status_code($code)
		{
			$this->global_status_code = $code;
		}

		function has_global_status_code()
		{
			return !is_null($this->global_status_code);
		}

		function get_global_status_code()
		{
			return intval($this->global_status_code);
		}

		function get_next_cmdid()
		{
			return ++$this->next_cmdid;
		}

		function get_cmdid()
		{
			return $this->next_cmdid;
		}

		function add_result($cmdref, $msgref, $srcref, $trgref,
			$type, $items)
		{
			$item_d = array_map(array($this, "get_item"), $items);

			for($i = 0, $c = count($item_d); $i < $c; $i++)
			{
				$item_d[$i] = '<Item>' . $item_d[$i] . '</Item>';
			}

			$this->commands[] = $this->filter(
				'<Results>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<MsgRef>' . $msgref . '</MsgRef>' .
					'<CmdRef>' . $cmdref . '</CmdRef>' .
					ec((bool)$srcref, 'SourceRef', $srcref) .
					ec((bool)$trgref, 'TargetRef', $trgref) .
					'<Meta>' .
						$this->get_meta(array('type' => $type)) .
					'</Meta>' .
					implode('', $item_d) .
				'</Results>'
			);
		}

		function add_alert($code, $item, $supportlargeobj = FALSE)
		{
			if(!$supportlargeobj && isset($item['meta']))
			{
				unset($item['meta']['maxobjsize']);
			}

			$this->commands[] = $this->filter(
				'<Alert>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<Data>' . $code . '</Data>' .
					ec(is_array($item), 'Item',
						$this->get_item($item, $supportlargeobj)) .
				'</Alert>'
			);
		}
		
		function set_syncml_namespace_version($version)
		{
			$this->root_namespace = 'SYNCML:SYNCML' . $version;
		}

		function set_header($verdtd, $verproto, $sessionid, $msgid,
			$trg_lu, $trg_ln, $src_lu, $src_ln)
		{
			$this->header =
				'<VerDTD>' . $verdtd . '</VerDTD>' .
				'<VerProto>' . $verproto . '</VerProto>' .
				'<SessionID>' . $sessionid . '</SessionID>' .
				'<MsgID>' . $msgid . '</MsgID>' .
				'<Target>' .
					'<LocURI>' . $trg_lu . '</LocURI>' .
					ec((bool)$trg_ln, "LocName", $trg_ln) .
				'</Target>' .
				'<Source>' .
					'<LocURI>' . $src_lu . '</LocURI>' .
					ec((bool)$src_ln, "LocName", $src_ln) .
				'</Source>';
		}

		function set_header_cred($format, $type, $data)
		{
			$this->header_cred = 
				'<Cred>' .
					'<Meta>' .
						'<Format xmlns="syncml:metinf">' .
							$format .
						'</Format>' .
						'<Type xmlns="syncml:metinf">' . $type . '</Type>' .
					'</Meta>' .
					'<Data>' . $data . '</Data>' .
				'</Cred>';
		}

		function add_status($cmdref, $msgref, $cmd, $trgref, $srcref, $data,
			$item = NULL)
		{
			$this->status_count++;

			$this->commands[] = $this->filter(
				'<Status>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<CmdRef>' . $cmdref . '</CmdRef>' .
					'<Cmd>' . $cmd . '</Cmd>' .
					'<MsgRef>' . $msgref . '</MsgRef>' .
					ec((bool)$srcref, 'SourceRef', $srcref) .
					ec((bool)$trgref, 'TargetRef', $trgref) .
					'<Data>' . $data . '</Data>' .
					ec(is_array($item), "Item", $this->get_item($item)) .
				'</Status>'
			);
		}

		function get_item($item)
		{
			return
				ec(isset($item['trg_uri']), "Target",
					'<LocURI>' . @$item['trg_uri'] . '</LocURI>' .
					ec(isset($item['trg_name']), "LocName",
						@$item['trg_name'])) .
				ec(isset($item['src_uri']), "Source",
					'<LocURI>' . @$item['src_uri'] . '</LocURI>' .
					ec(isset($item['src_name']), "LocName",
						@$item['src_name'])) .
				ec(isset($item['meta']), 'Meta',
					@$this->get_meta($item['meta'])) .
				ec(isset($item['data']), 'Data',
					/* '<![CDATA[' . */ @$item['data'] /* . ']]>' */) .
				ec(isset($item['moredata']) && $item['moredata'],
					'MoreData', '');
		}

		function get_meta($meta)
		{
			return
				ec(isset($meta['type']), 'Type', @$meta['type'], METINF) .
				ec(
					isset($meta['next']),
					'Anchor',
					'<Next>' . @$meta['next'] . '</Next>' .
						ec(isset($meta['last']), 'Last', @$meta['last']),
					METINF
				) .
				ec(isset($meta['maxobjsize']), 'MaxObjSize',
					@$meta['maxobjsize'], METINF) .
				ec(isset($meta['size']) && $meta['size'], 'Size',
					@$meta['size'], METINF);
		}

		function add_status_with_chal($cmdref, $msgref, $cmd, $trgref,
			$srcref, $data, $type, $nonce = NULL)
		{
			$this->status_count++;

			$this->commands[] = $this->filter(
				'<Status>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<CmdRef>' . $cmdref . '</CmdRef>' .
					'<Cmd>' . $cmd . '</Cmd>' .
					'<MsgRef>' . $msgref . '</MsgRef>' .
					ec($srcref, 'SourceRef', $srcref) .
					ec($trgref, 'TargetRef', $trgref) .
					'<Data>' . $data . '</Data>' .
					'<Chal>' .
						'<Meta>' .
							'<Type xmlns="syncml:metinf">' .
								$type .
							'</Type>' .
							'<Format xmlns="syncml:metinf">b64</Format>' .
							ec($nonce, 'NextNonce', base64_encode($nonce),
								METINF) .
						'</Meta>' .
					'</Chal>' .
				'</Status>'
			);
		}

		function add_status_with_anchor($cmdref, $msgref, $cmd, $trgref,
			$srcref, $data, $next)
		{
			$this->status_count++;

			$this->commands[] = $this->filter(
				'<Status>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<CmdRef>' . $cmdref . '</CmdRef>' .
					'<Cmd>' . $cmd . '</Cmd>' .
					'<MsgRef>' . $msgref . '</MsgRef>' .
					ec($srcref, 'SourceRef', $srcref) .
					ec($trgref, 'TargetRef', $trgref) .
					'<Data>' . $data . '</Data>' .
					'<Item>' .
						'<Data>' .
							'<Anchor xmlns="syncml:metinf">' .
								'<Next>' . $next . '</Next>' .
							'</Anchor>' .
						'</Data>' .
					'</Item>' .
				'</Status>'
			);
		}

		function build_add($meta, $item, $last_chunk)
		{
			return
				'<Add>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<Meta>' . $this->get_meta($meta) . '</Meta>' .
					'<Item>' .
						$this->get_item($item) .
						ec(!$last_chunk, 'MoreData', '') .
					'</Item>' .
				'</Add>';
		}

		function build_delete($item)
		{
			return
				'<Delete>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<Item>' . $this->get_item($item) . '</Item>' .
				'</Delete>';
		}

		function build_replace($meta, $item, $last_chunk)
		{
			return
				'<Replace>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<Meta>' . $this->get_meta($meta) . '</Meta>' .
					'<Item>' .
						$this->get_item($item) .
						ec(!$last_chunk, 'MoreData', '') .
					'</Item>' .
				'</Replace>';
		}

		function add_sync($trg_uri, $src_uri, $commands, $supportnbrofchanges,
			$nbrofchanges)
		{
			$this->commands[] = $this->filter(
				'<Sync>' .
					'<CmdID>' . $this->get_next_cmdid() . '</CmdID>' .
					'<Target>' .
						'<LocURI>' . $trg_uri . '</LocURI>' .
					'</Target>' .
					'<Source>' .
						'<LocURI>' . $src_uri . '</LocURI>' .
					'</Source>' .
					ec($supportnbrofchanges, 'NumberOfChanges',
						$nbrofchanges) .
					implode('', $commands) .
				'</Sync>'
			);
		}
	}
?>

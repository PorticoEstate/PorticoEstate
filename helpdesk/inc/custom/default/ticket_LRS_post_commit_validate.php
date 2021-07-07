<?php
	/*
	 * This file will only work for the implementation of LRS
	 */

	/**
	 * Intended for custom validation of tickets prior to commit.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	if (!class_exists("ticket_LRS_post_commit_validate"))
	{

		class ticket_LRS_post_commit_validate extends helpdesk_botts
		{

			protected
				$db,
				$join,
				$left_join,
				$like,
				$socat_assignment;
			function __construct()
			{
				parent::__construct();
				$this->db = & $GLOBALS['phpgw']->db;
				$this->join = & $this->db->join;
				$this->left_join = & $this->db->left_join;
				$this->like = & $this->db->like;
				$this->socat_assignment = createObject('helpdesk.socat_assignment');
			}

			function alert_and_reopen_origin($id, $origin_id)
			{
				$botts	 = createObject('helpdesk.botts');
				$botts->reset_views($origin_id);
				$ticket	 = $botts->read_single($origin_id);
				$send	 = CreateObject('phpgwapi.send');

				if(!empty($ticket['assignedto']))
				{
					$assignedto = $ticket['assignedto'];
				}
				else if(!empty($ticket['reverse_id']))
				{
					$assignedto = $ticket['reverse_id'];
				}
				else
				{
					$assignedto = $ticket['user_id'];
				}

				$subject = "LRSHD: Sak #{$id} er referert til fra ny sak";

				$link_to_ticket =  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.view',
					'id' => $id), false, true);

				$body = "<p>Sak #{$id}er referert til fra ny sak, ";
				$body .= "<a href ='{$link_to_ticket}'>klikk her for detaljer</a></p>";

				$prefs = $botts->bocommon->create_preferences('common',$assignedto);
				$to = $prefs['email'];
				$from_name = 'Ikke svar';
				$from_address ="Ikke svar<IkkeSvarLRS@Bergen.kommune.no>";

				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					try
					{
						$rc = $send->msg('email', $to, $subject, $body, '', $cc='', $bcc='',$from_address, $from_name,'html');
					}
					catch (Exception $e)
					{
					}
				}

				if ($rc && $to)
				{
					$botts->historylog->add('M', $id, $to);
				}
			}

			/**
			 * Do your magic
			 * @param integer $id
			 * @param array $data
			 * @param array $values_attribute
			 */
			function validate( $id = 0, &$data, $values_attribute = array() )
			{
				if($id && !empty($data['origin_id']))
				{
					$this->alert_and_reopen_origin($id, (int)$data['origin_id']);
				}
			}
		}
	}

	$ticket_LRS_post_commit_validate = new ticket_LRS_post_commit_validate();
	$ticket_LRS_post_commit_validate->validate(empty($id)?null:$id, $data, $values_attribute);
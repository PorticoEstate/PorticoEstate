<?php
	/*	 * *************************************************************************\
	 * eGroupWare - FeLaMiMail                                                   *
	 * http://www.linux-at-work.de                                               *
	 * http://www.phpgw.de                                                       *
	 * http://www.egroupware.org                                                 *
	 * Written by : Lars Kneschke [lkneschke@linux-at-work.de]                   *
	 * -------------------------------------------------                         *
	 * This program is free software; you can redistribute it and/or modify it   *
	 * under the terms of the GNU General Public License as published by the     *
	 * Free Software Foundation; either version 2 of the License, or (at your    *
	 * option) any later version.                                                *
	  \************************************************************************** */

	/* $Id$ */

	phpgw::import_class('phpgwapi.sql_criteria');

	class ajax_contacts
	{

		function __construct()
		{
			$GLOBALS['phpgw']->session->commit_session();
			$this->charset = 'utf-8';
			$this->translation = createObject('felamimail.translation');
		}

		function searchAddress( $_searchString )
		{

			function compare( $a, $b )
			{
				return strcasecmp($a["n_fn"],$b["n_fn"]);
			}

			if (method_exists($GLOBALS['phpgw']->contacts, 'search'))
			{
				// 1.3+
				$contacts = $GLOBALS['phpgw']->contacts->search(array(
					'n_fn' => $_searchString,
					'email' => $_searchString,
					'email_home' => $_searchString,
					), array('n_fn', 'email', 'email_home'), 'n_fn', '', '%', false, 'OR', array(
					0, 20));

				// additionally search the accounts, if the contact storage is not the account storage
				if ($GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap' &&
					$GLOBALS['phpgw_info']['server']['contact_repository'] == 'sql')
				{
					$accounts = $GLOBALS['phpgw']->contacts->search(array(
						'n_fn' => $_searchString,
						'email' => $_searchString,
						'email_home' => $_searchString,
						), array('n_fn', 'email', 'email_home'), 'n_fn', '', '%', false, 'OR', array(
						0, 20), array('owner' => 0));

					if ($contacts && $accounts)
					{
						$contacts = array_merge($contacts, $accounts);
						usort($contacts, 'compare');
					}
					elseif ($accounts)
					{
						$contacts = & $accounts;
					}
					unset($accounts);
				}
			}
			else
			{
				// < 1.3

				$d = CreateObject('phpgwapi.contacts');
				$fields = array('per_first_name', 'per_last_name', 'email', 'email_home');
				$criteria_search[] = phpgwapi_sql_criteria::token_begin('per_first_name', $_searchString);
				$criteria_search[] = phpgwapi_sql_criteria::token_begin('per_last_name', $_searchString);
				$criteria_search[] = phpgwapi_sql_criteria::token_has('email', $_searchString);
				$criteria[] = phpgwapi_sql_criteria::_append_or($criteria_search);
				$criteria[] = $d->criteria_for_index((int)$GLOBALS['phpgw_info']['user']['account_id']);
				$criteria_token = phpgwapi_sql_criteria::_append_and($criteria);
//FIXME	: the sql_builder/sql_criteria has issues.
				$contacts = $d->get_persons($fields, 0, 0, 'per_last_name', 'ASC', '', $criteria_token);
			}

			$response =  new xajaxResponse();

			if (is_array($contacts))
			{
				$innerHTML = '';
				$jsArray = array();
				$i = 0;

				foreach ($contacts as $contact)
				{
					foreach (array($contact['email'], $contact['email_home']) as $email)
					{
						// avoid wrong addresses, if an rfc822 encoded address is in addressbook
						$email = preg_replace("/(^.*<)([a-zA-Z0-9_\-]+@[a-zA-Z0-9_\-\.]+)(.*)/", '$2', $email);
						if (!empty($email) && !isset($jsArray[$email]))
						{
							$i++;
							$str = $this->translation->convert(trim($contact['n_fn'] ? $contact['n_fn'] : $contact['fn']) . ' <' . trim($email) . '>', $this->charset, 'utf-8');
							#$innerHTML .= '<div class="inactiveResultRow" onclick="selectSuggestion('. $i .')">'.
							$innerHTML .= '<div class="inactiveResultRow" onmousedown="keypressed(13,1)" onmouseover="selectSuggestion(' . ($i - 1) . ')">' .
								htmlentities($str, ENT_QUOTES, 'utf-8') . '</div>';
							$jsArray[$email] = addslashes(trim($contact['n_fn'] ? $contact['n_fn'] : $contact['fn']) . ' <' . trim($email) . '>');
						}
						if ($i > 10)
							break; // we check for # of results here, as we might have empty email addresses
					}
				}

				if ($jsArray)
				{
					$response->addAssign('resultBox', 'innerHTML', $innerHTML);
					$response->addScript('results = new Array("' . implode('","', $jsArray) . '");');
					$response->addScript('displayResultBox();');
				}
				//$response->addScript("getResults();");
				//$response->addScript("selectSuggestion(-1);");
			}
			else
			{
				$response->addAssign('resultBox', 'className', 'resultBoxHidden');
			}

			return $response->getXML();
		}
	}
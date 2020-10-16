<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');

	$link_text = '<p>Hei.</p>

<p>Du har fått tildelt en sak vedr lønnsopplysninger for person nevnt i tabellen under.</p>

<p>[[Vennligst åpne saken for se hva du kan bidra med.]] NB! Ved å klikke på denne lenken erkjenner du samtidig at<br />
du er personalleder eller lokal saksbehandler lønn og refusjon for den nevnte personen.</p>

<p>Dersom du ikke har en av disse relasjonene til vedkommende,<br />
<a href="https://portico.srv.bergenkom.no/portico/index.php?menuaction=helpdesk.uitts.add&cat_id=371&domain=lrs&origin_id=__ID__">så  vennligst klikk her og opplys oss gjerne om hvem du mener er rett person.</a></p>
';


	//message when closed;
	$link_to_ticket = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uitts.view',
		'id'		 => $id), false, true);

	preg_match_all("/\[[^\]\]]*\]\]/", $link_text, $matches);

	if (empty($matches[0][0]))
	{
		$body = "<a href ='{$link_to_ticket}'>{$link_text}</a>\n";
	}
	else
	{
		$replace			 = trim($matches[0][0], '[]');
		$link_to_ticket_text = "<a href ='{$link_to_ticket}'>{$replace}</a>";
		$body				 = str_ireplace($matches[0][0], $link_to_ticket_text, $link_text);
	}

	echo $body;

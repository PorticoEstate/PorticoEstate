<?php
	/**
	* Administration - User manual
	*
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package admin
	* @subpackage manual
	* @version $Id$
	*/

	$phpgw_flags = Array(
		'currentapp'	=> 'manual',
		'admin_header'	=> True,
	);
	$phpgw_info['flags'] = $phpgw_flags;
	
	/**
	* Include phpgroupware header
	*/
	include('../../../header.inc.php');
	$appname = 'admin';
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border=0> 
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>&nbsp;&nbsp;&nbsp;&nbsp;
Diese Funktion ist normalerweise nur f&uuml;r den Administrator zug�nglich. Administration aller Programme, Benutzer und Benutzergruppen und Sitzungs/Logging Kontrolle.
<ul>
<li><b>Benutzerkonten-Verwaltung:</b><br/>
<i>Benutzerkonten:</i><br/>
Erstellen, Edittieren und l&ouml;schen von Benutzerkonten. Setzen von Gruppenmidgliedschaft und der Zugriff auf Programme.<br/
><i>Benutzergruppen:</i><br/>
    Erstellen, Editieren und l&ouml;schen von Benutzergruppen.
    <p/>&nbsp;&nbsp;&nbsp;&nbsp;
<li><b>Sitzungs-Verwaltung:</b><br/>
    <i>Session's anzeigen::</i><br/>
Aktuelle Sitzungen, IP's, Login-Zeit, Inaktivit&auml;tszeit und die M&ouml;glichkeit sessions zu beenden (kill).<br/>
<i>Zugriffsaufzeichnungen (Access Log) anzeigen:</i><br/>
    LoginId, IP, Login Time (Zeit), Logout Time (Zeit), Total time spent (gesamte 
    Verbrachte Zeit).</li>
  <p/>&nbsp;&nbsp;&nbsp;&nbsp;
<li><b>Headline-Seiten (Schlagzeilen):</b><br/>
    Administrieren von Headline-Seiten die von den Benutzern im Headline Programm 
    gesehen werden.<br/>
<i>Edit:</i> Optionen f&uuml;r die Headline-Seiten:<br/>
    Anzeige, BasisURL, NewsDatei, Minuten zwischen Neuladungen (reloads), Angezeigte 
    Auflistung und die Art der Neuigkeiten.<br/>
    <i>L&ouml;schen:</i>Entfernen von einer existierenden Headline-Seite, klicken 
    auf L�schen bringt Sie auf eine &Uuml;berpr�ffungsseite, um das l&ouml;schen 
    zu verifizieren.<br/>
<i>Anzeigen:</i>Zeigt die Optionen wie in Edit.<br/>
<i>Hinzuf&uuml;gen:</i>Formular um eine neue Headline-Seite hinzuzuf&uuml;gen, wie in Edit.</li><p/>&nbsp;&nbsp;&nbsp;&nbsp;
<li><b>Netzwerk News:</b><br/>
Manuelle Aktualisierung f&uuml;r Newsgruppen.</li><p/>&nbsp;&nbsp;&nbsp;&nbsp;
<li><b>Server Information:</b><br/>
Zeigt die phpinfo(); des Servers an.</li><p/>&nbsp;&nbsp;&nbsp;&nbsp;
</ul></font>
<?php $phpgw->common->phpgw_footer(); ?>

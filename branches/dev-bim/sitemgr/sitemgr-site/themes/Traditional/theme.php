<?php

$thename = "Cuadriculado";
$bgcolor1 = "#cccccc";
$bgcolor2 = "#999999";
$bgcolor3 = "#cccccc";
$textcolor1 = "#ffffff";
$textcolor2 = "#000000";

function OpenTable() {
    global $bgcolor1, $bgcolor2;
    $content = "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
    $content .= "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"$bgcolor1\"><tr><td>\n";
	return $content;
}

function CloseTable() {
    return "</td></tr></table></td></tr></table>\n";
}

function OpenTable2() {
    global $bgcolor1, $bgcolor2;
    $content = "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\" align=\"center\"><tr><td>\n";
    $content .= "<table border=\"0\" cellspacing=\"1\" cellpadding=\"8\" bgcolor=\"$bgcolor1\"><tr><td>\n";
	return $content;
}

function CloseTable2() {
    return "</td></tr></table></td></tr></table>\n";
}

function FormatStory($thetext, $notes, $aid, $informant) {
    global $anonymous;
    if ($notes != "") {
	$notes = "<b>"._NOTE."</b> <i>$notes</i>\n";
    } else {
	$notes = "";
    }
    if ("$aid" == "$informant") {
	echo "<font class=\"content\" color=\"#505050\">$thetext<br>$notes</font>\n";
    } else {
	if($informant != "") {
	    $boxstuff = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&amp;uname=$informant\">$informant</a> ";
	} else {
	    $boxstuff = "$anonymous ";
	}
	$boxstuff .= "".translate("writes")." <i>\"$thetext\"</i> $notes\n";
	echo "<font class=\"content\" color=\"#505050\">$boxstuff</font>\n";
    }
}

function themeheader() {
    echo "<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#000000\">"
	."<br>";
    if ($banners) {
	include("banners.php");
    }
    echo parse_theme_vars("<br>"
	."<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\" bgcolor=\"FFFFFF\">"
	."<tr><td width=\"100%\">"
	."<a href=\"index.php\"><img src=\"themes/Traditional/images/logo.gif\" alt=\"Welcome to $sitename\" border=\"0\"></a>{header}"
	."</td><td align=\"right\">"
	."&nbsp;"
	."</td>"
	."<td width=\"60\" align=\"left\">&nbsp;</td>"
	."</tr></table></form>"
	."<br>"
	."<table border=\"0\" width=\"100%\" cellspacing=\"5\"><tr><td valign=\"top\">");
}

function themefooter() {
    global $index;
    if ($index == 1) {
	echo "<td>&nbsp;</td><td valign=\"top\" width=\"200\">";
	blocks(left);
	blocks(right);
    }
    echo "</td></tr></table></td></tr></table>";
    footmsg();
}

function themesidebox($title, $content) {
    echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"200\" bgcolor=\"#000000\"><tr><td>"
        ."<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\"><tr><td bgcolor=\"#cccccc\">"
        ."<img src=\"themes/Traditional/images/tic.gif\" border=\"0\" alt=\"\">"
        ."<font class=\"option\">$title</font></td></tr>"
        ."<tr><td bgcolor=\"#ffffff\">"
        ."<font class=\"content\">$content</font>"
        ."</td></tr></table></td></tr></table>"
        ."<br>";
}



function themeindex ($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext) {
	global $anonymous;
	if ("$aid" == "$informant") { ?>


<table border=0 cellpadding=3 cellspacing=1 width=100%>
<tr><td bgcolor=CCCCCC>
<font class=title>
<b><?php echo"$title"; ?></b><br>
</td></tr><tr><td bgcolor=FFFFFF>
<a href="modules.php?name=Search&amp;query=&topic=<?php echo"$topic"; ?>&author="><img src=<?php echo"images/topics/$topicimage"; ?> border=0 Alt=<?php echo"\"$topictext\""; ?> align=right hspace=10 vspace=10></a>
<font class=tiny>
<?php echo translate("Posted by "); ?> <b><?php formatAidHeader($aid); echo "$aid"; ?></b> <?php echo translate("on"); ?> <?php echo"$time $timezone"; ?><br>(<?php echo $counter; ?> <?php echo translate("reads"); ?>)
</font><br><br>
<font class=content>
<?php echo"$thetext<br><br></font>
</td></tr><tr><td align=left>
<font class=content>$morelink"; ?></font>
</td>
</tr>
</table>
<br>


<?php	} else {
		if($informant != "") $boxstuff = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a> ";
		else $boxstuff = "$anonymous ";
		$boxstuff .= "".translate("writes")." <i>\"$thetext\"</i> $notes";
?>

<table border=0 cellpadding=3 cellspacing=1 width=100%>
<tr><td bgcolor=CCCCCC>
<font class=title>
<b><?php echo"$title"; ?></b><br>
<font class=option>
</td></tr><tr><td bgcolor=FFFFFF>
<a href="modules.php?name=Search&amp;query=&topic=<?php echo"$topic"; ?>&author="><img src=<?php echo"images/topics/$topicimage"; ?> border=0 Alt=<?php echo"\"$topictext\""; ?> align=right hspace=10 vspace=10></a>
<font class=option>
<?php echo translate("Posted by "); ?> <?php formatAidHeader($aid); ?> <?php echo translate("on"); ?> <?php echo"$time $timezone"; ?><br>(<?php echo $counter; ?> <?php echo translate("reads"); ?>)
<br><br>
</font>
<font class=content>
<?php echo"$boxstuff<br><br></font>
</td></tr><tr><td align=left>
<font class=option>$morelink"; ?></font>
</td>
</tr>
</table>
<br>

<?php	}
}

function themearticle ($aid, $informant, $datetime, $title, $thetext, $topic, $topicname, $topicimage, $topictext) {
	global $admin, $sid;
	if ("$aid" == "$informant") {
echo"

<table border=0 cellpadding=0 cellspacing=0 align=center bgcolor=000000 width=100%>
<tr><td>

<table border=0 cellpadding=3 cellspacing=1 width=100%>
<tr><td bgcolor=CCCCCC>
$font2
<b>$title</b><br>$font2 Enviado el $datetime
";
if ($admin) {
    echo "&nbsp;&nbsp; $font2 [ <a href=admin.php?op=EditStory&sid=$sid>".translate("Edit")."</a> | <a href=admin.php?op=RemoveStory&sid=$sid>".translate("Delete")."</a> ]";
}
echo "
</td>
</tr>
<tr>
<td bgcolor=ffffff>
<a href=modules.php?name=Search&amp;query=&topic=$topic&author=><img src=images/topics/$topicimage border=0 Alt=\"$topictext\" align=right hspace=10 vspace=10></a>
$thetext
</td>
</tr>
</table>
</td>
</tr>
</table><br>
";

	} else {
		if($informant != "") $informant = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a> ";
		else $boxstuff = "$anonymous ";
		$boxstuff .= "".translate("writes")." <i>\"$thetext\"</i> $notes";
echo "

<table border=0 cellpadding=0 cellspacing=0 align=center bgcolor=000000 width=100%>
<tr><td>
<table border=0 cellpadding=3 cellspacing=1 width=100%>
<tr><td bgcolor=CCCCCC>
$font3
<b>$title</b><br>$font2 ".translate("Contributed by")." $informant ".translate("on")." $datetime</font>
";
if ($admin) {
    echo "&nbsp;&nbsp; $font2 [ <a href=admin.php?op=EditStory&sid=$sid>".translate("Edit")."</a> | <a href=admin.php?op=RemoveStory&sid=$sid>".translate("Delete")."</a> ]";
}
echo "
</td>
</tr>
<tr>
<td bgcolor=ffffff>
<a href=modules.php?name=Search&amp;query=&topic=$topic&author=><img src=images/topics/$topicimage border=0 Alt=\"$topictext\" align=right hspace=10 vspace=10></a>
$font3 $thetext
</td>
</tr>
</table>
</td>
</tr>
</table><br>
";

	}
}

?>

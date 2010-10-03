<?php

$bgcolor1 = "#FFFFFF";
$bgcolor2 = "#101070";
$bgcolor3 = "#e6e6e6";
$bgcolor4 = "#e6e6e6";
$textcolor1 = "#FFFFFF";
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
    global $slogan, $sitename, $banners;
echo "<body bgcolor=FFFFFF text=000000 link=101070 vlink=101070>
<br>";
if ($banners) {
    include("banners.php");
}
echo parse_theme_vars("<br>
<center>
<table cellpadding=0 cellspacing=0 border=0 width=99% align=center><tr><td align=left>
<a href=\"www.phpgroupware.org\"><img src=themes/SlashOcean/images/logo.gif Alt=\"Welcome to $sitename\" border=0></a>
</td><td align=center width=100%>
	<font class=content>{header}</font>
	</td>
	<td align=right>&nbsp;&nbsp;</td>
    </td></tr></table><br>
<table cellpadding=0 cellspacing=0 border=0 width=99% bgcolor=101070><tr><td>
<table cellpadding=5 cellspacing=1 border=0 width=100% bgcolor=FFFFFF><tr><td>
<font class=content>$slogan</td></tr></table></td></tr></table><P>
<table width=99% align=center cellpadding=0 cellspacing=0 border=0><tr><td valign=top rowspan=15>");

blocks(left);

echo "</td><td>&nbsp;&nbsp;</td><td valign=top width=100%>";
}

function themefooter() {
    global $index;
    echo "</td>";
    if ($index == 1) {
	echo "<td>&nbsp;&nbsp;</td><td valign=\"top\">";
	blocks(right);
    }
    echo "</tr></table></td></tr></table>";
    footmsg();
}

function themeindex ($aid, $informant, $datetime, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext) {
	global $anonymous;
	if ("$aid" == "$informant") { ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr valign="top" bgcolor="#101070">
			<td><img src="themes/SlashOcean/images/cl.gif" width="7" height="10" alt=""><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="storytitle"><B><?php echo"$title"; ?></B></font>
			</td></tr></table>
			</td><td align="right"><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""><img src="themes/SlashOcean/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/SlashOcean/images/gl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?> (<?php echo $counter; ?> <?php echo translate("reads"); ?>)</font></td>
				</tr></table>
			</td>
			<td background="themes/SlashOcean/images/gr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/SlashOcean/images/wl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
			
			<a href="modules.php?name=Search&amp;query=&topic=<?php echo"$topic"; ?>&author="><img src=<?php echo"images/topics/$topicimage"; ?> border=0 Alt=<?php echo"\"$topictext\""; ?> align=right hspace=10 vspace=10></a>
			
			<?php echo "<font class=content>$thetext"; ?>
                 </td></tr></table></td>
                 <td background="themes/SlashOcean/images/wr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		</table><table border="0" cellpadding="0" cellspacing="0">
		<tr bgcolor="#ffffff">
			<td background="themes/SlashOcean/images/wl_cccccc.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="5" cellspacing="0"> 
			<tr><td bgcolor="#cccccc"><font class="content"><?php echo"$morelink"; ?></font></td></tr></table>
			</td>
			<td background="themes/SlashOcean/images/wr_cccccc.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		</table><BR><BR>
<?php	} else {
		if($informant != "") $boxstuff = "<font class=content><a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a> ";
		else $boxstuff = "$anonymous ";
		$boxstuff .= "<font class=content>".translate("writes")." <i>\"$thetext\"</i> $notes";
?>		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr valign="top" bgcolor="#101070">
			<td><img src="themes/SlashOcean/images/cl.gif" width="7" height="10" alt=""><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="storytitle"><B><?php echo"$title"; ?></B></font>
			</td></tr></table></td>
                 	<td align="right"><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""><img src="themes/SlashOcean/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/SlashOcean/images/gl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?> (<?php echo $counter; ?> <?php echo translate("reads"); ?>)</font></td>
				</tr></table>
			</td>
			<td background="themes/SlashOcean/images/gr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/SlashOcean/images/wl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
			<a href="modules.php?name=Search&amp;query=&topic=<?php echo"$topic"; ?>&author="><img src=<?php echo"images/topics/$topicimage"; ?> border=0 Alt=<?php echo"\"$topictext\""; ?> align=right hspace=10 vspace=10></a>
			<?php echo "$boxstuff"; ?>
                 </td></tr></table></td>
                 <td background="themes/SlashOcean/images/wr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		</table><table border="0" cellpadding="0" cellspacing="0">
		<tr bgcolor="#ffffff">
			<td background="themes/SlashOcean/images/wl_cccccc.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td bgcolor="#cccccc"><font class="content"><?php echo"$morelink"; ?></font></td></tr></table>
			</td>
			<td background="themes/SlashOcean/images/wr_cccccc.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		</table><BR><BR>
<?php	}
}

function themearticle ($aid, $informant, $datetime, $title, $thetext, $topic, $topicname, $topicimage, $topictext) {
	global $admin, $sid;
	if ("$aid" == "$informant") { ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr valign="top" bgcolor="#101070">
			<td><img src="themes/SlashOcean/images/cl.gif" width="7" height="10" alt=""><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="option" color="#FFFFFF"><B><?php echo"$title"; ?></B></font>
			</td></tr></table>
			</td><td align="right"><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""><img src="themes/SlashOcean/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/SlashOcean/images/gl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?></font>

<?php
if ($admin) {
    echo "&nbsp;&nbsp; <font class=content> [ <a href=admin.php?op=EditStory&sid=$sid>".translate("Edit")."</a> | <a href=admin.php?op=RemoveStory&sid=$sid>".translate("Delete")."</a> ]";
}
?>
				</td>
				</tr></table>
			</td>
			<td background="themes/SlashOcean/images/gr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/SlashOcean/images/wl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
		<?php echo "<a href=modules.php?name=Search&amp;query=&topic=$topic&author=><img src=images/topics/$topicimage border=0 Alt=\"$topictext\" align=right hspace=10 vspace=10></a>"; ?>
			<?php echo "<font class=content>$thetext"; ?>
                 </td></tr></table></td>
                 <td background="themes/SlashOcean/images/wr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		</table>
<?php	} else {
		if($informant != "") $boxstuff = "<font class=content><a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a> ";
		else $boxstuff = "$anonymous ";
		$boxstuff .= "<font class=content>".translate("writes")." <i>\"$thetext\"</i> $notes";
?>		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr valign="top" bgcolor="#101070">
			<td><img src="themes/SlashOcean/images/cl.gif" width="7" height="10" alt=""><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="option" color="#FFFFFF"><B><?php echo"$title"; ?></B></font>
			</td></tr></table>
			</td><td align="right"><img src="themes/SlashOcean/images/pix.gif" width="4" height="4" alt=""><img src="themes/SlashOcean/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/SlashOcean/images/gl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?></font>
				
<?php
if ($admin) {
    echo "&nbsp;&nbsp; <font class=content> [ <a href=admin.php?op=EditStory&sid=$sid>Editar</a> | <a href=admin.php?op=RemoveStory&sid=$sid>Borrar</a> ]";
}
?>
<br><?php echo "<font class=tiny"; ?>
<?php echo "".translate("Contributed by ").""; ?> <?php echo "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a>"; ?>
				</td>
				</tr></table>
			</td>
			<td background="themes/SlashOcean/images/gr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/SlashOcean/images/wl.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
		<?php echo "<a href=modules.php?name=Search&amp;query=&topic=$topic&author=><img src=images/topics/$topicimage border=0 Alt=\"$topictext\" align=right hspace=10 vspace=10></a>"; ?>
			<?php echo "<font class=content>$thetext"; ?>
                 </td></tr></table></td>
                 <td background="themes/SlashOcean/images/wr.gif"><img src="themes/SlashOcean/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
		</table>
<?php	}
}

function themesidebox($title, $content) {
	?>
	<table width="150" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top" bgcolor="#101070">
		<td bgcolor="#FFFFFF"><img src="themes/SlashOcean/images/pix.gif" width="3" height="3" alt=""></td>
		<td><img src="themes/SlashOcean/images/cl.gif" width="7" height="10" alt=""></td>
		<td><font class="btiny"><B><?php echo"$title"; ?></B></font></td>
		<td align="right"><img src="themes/SlashOcean/images/cr.gif" width="7" height="10" alt=""></td>
		<td bgcolor="#FFFFFF" align="right"><img src="themes/SlashOcean/images/pix.gif" width="3" height="3" alt=""></td>
	</tr>
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
	<tr bgcolor="#ffffff">
		<td background="themes/SlashOcean/images/sl.gif"><img src="themes/SlashOcean/images/pix.gif" width="3" height="3" alt=""></td>
		<td width="100%">
		<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td><font class=tiny><?php echo"$content"; ?>
		</td></tr></table></td>
		<td background="themes/SlashOcean/images/sr.gif" align="right"><img src="themes/SlashOcean/images/pix.gif" width="3" height="3" alt=""></td>
	</tr>
	<tr bgcolor="#101070"><td colspan="3"><img src="themes/SlashOcean/images/pix.gif" width="1" height="1"></td></tr>
	</table><br><br>
<?php
}

?>

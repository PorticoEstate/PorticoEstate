<?php

$bgcolor1 = "#FFFFFF";
$bgcolor2 = "#660000";
$bgcolor3 = "#e6e6e6";
$bgcolor4 = "#660000";
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
    global $slogan, $sitename;
echo "<body bgcolor=DDDDDD text=222222 link=660000 vlink=222222>
<br>";
if ($banners) {
    include("banners.php");
}
echo parse_theme_vars("<br>
<center>
<table cellpadding=0 cellspacing=0 border=0 width=99% align=center><tr><td align=left>
<a href=\"http://www.phpgroupware.org/\"><img src=themes/Slash/images/logo.gif border=0></a>
</td><td align=center width=100%>
	&nbsp;{header}
    </td>
    <td align=right>&nbsp;&nbsp;</td>
    </td></tr></table><br>
<table cellpadding=0 cellspacing=1 border=0 width=99% bgcolor=660000><tr><td>
<table cellpadding=5 cellspacing=1 border=0 width=100% bgcolor=FFFFFF><tr><td>
<font class=content>$slogan</td></tr></table></td></tr></table><P>
<table width=99% align=center cellpadding=0 cellspacing=0 border=0><tr><td valign=top rowspan=5>");

blocks(left);

echo "</td><td>&nbsp;</td><td valign=top width=100%>";
}

function themefooter() {
    global $index;
if ($index == 1) {
    echo "<td>&nbsp;</td><td valign=\"top\">";
    blocks(right);
    echo "</td>";
}
echo "</tr></table></td></tr></table>";
footmsg();
}

function themeindex ($aid, $informant, $datetime, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext) {
	global $anonymous;
	if ("$aid" == "$informant") { ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DDDDDD"><tr valign="top" bgcolor="#660000">
			<td><img src="themes/Slash/images/cl.gif" width="7" height="10" alt=""><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="option" color="#FFFFFF"><B><?php echo"$title"; ?></B></font>
			</td></tr></table>
			</td><td align="right"><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""><img src="themes/Slash/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/Slash/images/gl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?> (<?php echo $counter; ?> <?php echo translate("reads"); ?>)</font></td>
				</tr></table>
			</td>
			<td background="themes/Slash/images/gr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#006666"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/Slash/images/wl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
			
			<a href="modules.php?name=Search&amp;query=&topic=<?php echo"$topic"; ?>&author="><img src=<?php echo"images/topics/$topicimage"; ?> border=0 Alt=<?php echo"\"$topictext\""; ?> align=right hspace=10 vspace=10></a>
			
			<?php echo "$thetext"; ?>
                 </td></tr></table></td>
                 <td background="themes/Slash/images/wr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		</table><table border="0" cellpadding="0" cellspacing="0">
		<tr bgcolor="#ffffff">
			<td background="themes/Slash/images/wl_cccccc.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="5" cellspacing="0"> 
			<tr><td bgcolor="#cccccc"><font class="content"><?php echo"$morelink"; ?></font></td></tr></table>
			</td>
			<td background="themes/Slash/images/wr_cccccc.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		</table><BR><BR>
<?php	} else {
		if($informant != "") $boxstuff = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a> ";
		else $boxstuff = "$anonymous ";
		$boxstuff .= "".translate("writes")." <i>\"$thetext\"</i> $notes";
?>		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DDDDDD"><tr valign="top" bgcolor="#660000">
			<td><img src="themes/Slash/images/cl.gif" width="7" height="10" alt=""><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="option" color="#FFFFFF"><B><?php echo"$title"; ?></B></font>
			</td></tr></table></td>
                 	<td align="right"><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""><img src="themes/Slash/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/Slash/images/gl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?> (<?php echo $counter; ?> <?php echo translate("reads"); ?>)</font></td>
				</tr></table>
			</td>
			<td background="themes/Slash/images/gr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#006666"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/Slash/images/wl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
			<a href="modules.php?name=Search&amp;query=&topic=<?php echo"$topic"; ?>&author="><img src=<?php echo"images/topics/$topicimage"; ?> border=0 Alt=<?php echo"\"$topictext\""; ?> align=right hspace=10 vspace=10></a>
			<?php echo "$boxstuff"; ?>
                 </td></tr></table></td>
                 <td background="themes/Slash/images/wr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		</table><table border="0" cellpadding="0" cellspacing="0">
		<tr bgcolor="#ffffff">
			<td background="themes/Slash/images/wl_cccccc.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td bgcolor="#cccccc"><font class="content"><?php echo"$morelink"; ?></font></td></tr></table>
			</td>
			<td background="themes/Slash/images/wr_cccccc.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		</table><BR><BR>
<?php	}
}

function themearticle ($aid, $informant, $datetime, $title, $thetext, $topic, $topicname, $topicimage, $topictext) {
	global $admin, $sid;
	if ("$aid" == "$informant") { ?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DDDDDD"><tr valign="top" bgcolor="#660000">
			<td><img src="themes/Slash/images/cl.gif" width="7" height="10" alt=""><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="option" color="#FFFFFF"><B><?php echo"$title"; ?></B></font>
			</td></tr></table>
			</td><td align="right"><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""><img src="themes/Slash/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/Slash/images/gl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?></font>

<?php
if ($admin) {
    echo "&nbsp;&nbsp; $font2 [ <a href=admin.php?op=EditStory&sid=$sid>".translate("Edit")."</a> | <a href=admin.php?op=RemoveStory&sid=$sid>".translate("Delete")."</a> ]";
}
?>
				</td>
				</tr></table>
			</td>
			<td background="themes/Slash/images/gr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#006666"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/Slash/images/wl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
		<?php echo "<a href=modules.php?name=Search&amp;query=&topic=$topic&author=><img src=images/topics/$topicimage border=0 Alt=\"$topictext\" align=right hspace=10 vspace=10></a>"; ?>
			<?php echo "$thetext"; ?>
                 </td></tr></table></td>
                 <td background="themes/Slash/images/wr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		</table>
<?php	} else {
		if($informant != "") $boxstuff = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a> ";
		else $boxstuff = "$anonymous ";
		$boxstuff .= "".translate("writes")." <i>\"$thetext\"</i> $notes";
?>		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DDDDDD"><tr valign="top" bgcolor="#660000">
			<td><img src="themes/Slash/images/cl.gif" width="7" height="10" alt=""><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""></td>
			<td width="100%">
			<table width="100%" border="0" cellpadding="2" cellspacing="0"><tr><td>
			<font class="option" color="#FFFFFF"><B><?php echo"$title"; ?></B></font>
			</td></tr></table>
			</td><td align="right"><img src="themes/Slash/images/pix.gif" width="4" height="4" alt=""><img src="themes/Slash/images/cr.gif" width="7" height="10" alt=""></td>
         	</tr></table>
		<table border="0" cellpadding="0" cellspacing="0"><tr bgcolor="#e6e6e6">
			<td background="themes/Slash/images/gl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%">
				<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
				<td><font class="tiny"><?php echo translate("Posted by "); ?> <?php formatAidHeader($aid) ?> <?php echo translate("on"); ?> <?php echo"$datetime $timezone"; ?></font>
				
<?php
if ($admin) {
    echo "&nbsp;&nbsp; $font2 [ <a href=admin.php?op=EditStory&sid=$sid>Editar</a> | <a href=admin.php?op=RemoveStory&sid=$sid>Borrar</a> ]";
}
?>
<br><?php echo "$font1"; ?>
<?php echo "".translate("Contributed by ").""; ?> <?php echo "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&uname=$informant\">$informant</a>"; ?>
				</td>
				</tr></table>
			</td>
			<td background="themes/Slash/images/gr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#006666"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		<tr bgcolor="#ffffff">
			<td background="themes/Slash/images/wl.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
			<td width="100%"><table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td>
		<?php echo "<a href=modules.php?name=Search&amp;query=&topic=$topic&author=><img src=images/topics/$topicimage border=0 Alt=\"$topictext\" align=right hspace=10 vspace=10></a>"; ?>
			<?php echo "$thetext"; ?>
                 </td></tr></table></td>
                 <td background="themes/Slash/images/wr.gif"><img src="themes/Slash/images/pix.gif" width="11" height="11" alt=""></td>
		</tr>
		<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
		</table>
<?php	}
}

function themesidebox($title, $content) { 
?>	
    <table width="150" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top" bgcolor="#660000">
		<td bgcolor="#dddddd"><img src="themes/Slash/images/pix.gif" width="3" height="3" alt=""></td>
		<td><img src="themes/Slash/images/cl.gif" width="7" height="10" alt=""></td>
		<td><font class="tiny" color="#ffffff"><B><?php echo"$title"; ?></B></font></td>
		<td align="right"><img src="themes/Slash/images/cr.gif" width="7" height="10" alt=""></td>
		<td bgcolor="#dddddd" align="right"><img src="themes/Slash/images/pix.gif" width="3" height="3" alt=""></td>
	</tr>
	</table>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
	<tr bgcolor="#ffffff">
		<td background="themes/Slash/images/sl.gif"><img src="themes/Slash/images/pix.gif" width="3" height="3" alt=""></td>
		<td width="100%">
		<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr><td><?php echo"$font2"; ?><?php echo"$content"; ?>
		</td></tr></table></td>
		<td background="themes/Slash/images/sr.gif" align="right"><img src="themes/Slash/images/pix.gif" width="3" height="3" alt=""></td>
	</tr>
	<tr bgcolor="#660000"><td colspan="3"><img src="themes/Slash/images/pix.gif" width="1" height="1"></td></tr>
	</table><br><br>
<?php
}

?>

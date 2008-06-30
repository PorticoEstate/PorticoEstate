<?php 
class right_bt
{
	function apply_transform($title,$content)
	{
		return '
<table border="0" cellspacing="0" cellpadding="0" width="180"><tr>
<td width="15" height="15"><img src="templates/3D-Fantasy/images/up-left2.gif" alt="" border="0"></td>
<td><img src="templates/3D-Fantasy/images/up2.gif" width="100%" height="15"></td>
<td><img src="templates/3D-Fantasy/images/up-right2.gif" width="15" height="15" alt="" border="0"></td></tr>
<tr>
<td background="templates/3D-Fantasy/images/left2.gif" width="15">&nbsp;</td>
<td bgcolor="ffffff" width="100%">
<b>' .$title . '</b><br><br>' .
$content .'</td>
<td background="templates/3D-Fantasy/images/right2.gif">&nbsp;</td></tr>
<tr>
<td width="15" height="15"><img src="templates/3D-Fantasy/images/down-left2.gif" alt="" border="0"></td>
<td><img src="templates/3D-Fantasy/images/down2.gif" width="100%" height="15"></td>
<td><img src="templates/3D-Fantasy/images/down-right2.gif" width="15" height="15" alt="" border="0"></td></tr></table>
<br>';
	}
}
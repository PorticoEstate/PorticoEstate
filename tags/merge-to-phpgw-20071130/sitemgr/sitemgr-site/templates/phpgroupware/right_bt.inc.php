<?php 
class right_bt
{
	function apply_transform($title,$content)
	{
		return '
<br>
<table border="0" width="142" border="0" cellspacing="0" cellpadding="0">
	<tr> 
		<td height="20" background="templates/phpgroupware/images/side_top.bg.gif" valign="top">
			<div align="center" style="color:#537991; font-face:Verdana, Arial, Helvetica, sans-serif;font-weight:bold">' .
	$title . '
			</div>
		</td>
	</tr>
	<tr> 
		<td background="templates/phpgroupware/images/side_bg.gif" valign="top">
			<div style="color:#FFFFFF;font-face:Verdana, Arial, Helvetica, sans-serif; padding:1mm">' .
				$content . '
			</div>
		</td>
	</tr>
</table>';
	}
}
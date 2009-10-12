<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{outline_manager}</div>
<div><b><font color='#FF0000' size='2'>{message}</font></b></div>
<div>
{addcategory}
</div>
<!-- BEGIN CategoryBlock -->
<div style="margin-left:{indent}mm; border-width:2px;border-style:solid; padding:5mm" width='85%'>
	<p style="font-weight:bold;text-decoration:underline" valign='bottom'>{category}</p>	
	<p>{editcat} {deletecat} {addpage}
	</p>
	<p>{moduleconfig} {catcontent}
	</p>
	<table width="100%">
	<!-- BEGIN PageBlock -->
	<tr bgcolor='dddddd'>
		<td align='left'>
			{page}
		</td>
		<td align="center">
			{editpage} {deletepage} {pagecontent}
		</td>
	</tr>
	<!-- END PageBlock -->
	</table>	
</div>
<!-- END CategoryBlock -->

<br clear="all">
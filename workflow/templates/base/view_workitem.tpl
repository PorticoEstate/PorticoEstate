<LINK href="{monitors_css_link}"  type="text/css" rel="StyleSheet">
<div class="message">{message}</div>
{monitor_tabs}
<table width="100%">
	<tr>
		<td style="width:50%" valign="top">
			<table style="border: 1px solid black;width:100%">
				<tr class="th">
					<td colspan="7" style="font-size: 120%; font-weight:bold;">
						{lang_Workitem_information}
					</td>
				</tr>
				<tr class="row_on">
					<td><b>{lang_id}</b></td>
					<td>{wi_itemId}</td>
				</tr>
				<tr class="row_off">
					<td><b>#</b></td>
					<td>{wi_orderId}</td>
				</tr>
				<tr class="row_on">
					<td><b>{lang_Process}</b></td>
					<td>{wi_wf_procname} {wi_version}</td>
				</tr>
				<tr class="row_off">
					<td><b>{lang_Activity}</b></td>
					<td>{act_icon} {wi_name}</td>
				</tr>
				<tr class="row_on">
					<td><b>{lang_User}</b></td>
					<td>{wi_user}</td>
				</tr>
				<tr class="row_off">
					<td><b>{lang_Started}</b></td>
					<td>{wi_started}</td>
				</tr>
				<tr class="row_on">
					<td><b>{lang_Duration}</b></td>
					<td>{wi_duration}</td>
				</tr>
			</table>
		</td>
		<td style="width=50%" valign="top">
			<table style="border: 1px solid black;width:100%;">
				<tr class="th">
					<td colspan="7" style="font-size: 120%; font-weight:bold;">
						{lang_Properties}
					</td>
				</tr>
				<tr class="th" style="font-weight:bold">
					<td>{lang_Property}</td>
					<td>{lang_Value}</td>
				</tr>
				<!-- BEGIN block_properties -->
				<tr class="{class_alternate_row}">
					<td>
					 <b>{key}</b>
					 </td>
					<td>
					{prop_value}
					</td>
				</tr>
				<!-- END block_properties -->
			</table>
		</td>
	</tr>
</table>

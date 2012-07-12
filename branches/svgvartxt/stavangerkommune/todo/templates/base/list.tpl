<!-- $Id$ -->
<!-- BEGIN page_header -->
<h1>{lang_action}</h1>
<center>
	<table border="0" cellspacing="2" cellpadding="2" width="98%">
		<tr>
			<td colspan="9" align="left">
				<table border="0" width="100%">
					<tr>
					{left}
						<td align="center">{total_matchs}</td>
					{right}
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="33%" align="left">
				<form method="POST" name="cat" action="{cat_action}">
				{lang_category}&nbsp;&nbsp;<select name="cat_id" onChange="this.form.submit();">
				<option value="0">{lang_all}</option>
				{categories}
				</select>
				<noscript><input type="submit" name="cats" value="{lang_select}"></noscript>
				</form>
			</td >
			<td width="33%" align="center"><form method="POST" name="filter" action="{filter_action}">{filter_list}</form></td>
			<td width="33%" align="right"><form method="POST" name="query" action="{search_action}">{search_list}</form></td>
		</tr>
	</table>
<!-- END page_header -->

<!-- BEGIN table_header -->
	<table id="todo_list">
		<thead>
			<tr>
				<td width="5%" align="right">{sort_status}</td>
				<td width="5%" align="center">{sort_urgency}</td>
				<td>{sort_title}</td>
				<td width="9%" align="center">{sort_sdate}</td>
				<td width="9%" align="center">{sort_edate}</td>
				<td width="15%">{sort_owner}</td>
				<td width="15%">{sort_assigned}</td>
				<td width="9%" align="center">{h_lang_sub}</td>
				<td width="5%" align="center">{h_lang_view}</td>
				<td width="5%" align="center">{h_lang_edit}</td>
			</tr>
		</thead>
		<tbody>
<!-- END table_header -->
  
<!-- BEGIN todo_list -->
	  <tr class="{tr_class}">
	    <td align="right">{status}%</td>
	    <td align="center">{pri}</font></td>
	    <td>{title}</td>
	    <td align="center">{datecreated}</td>
	    <td align="center">{datedue}</td>
	    <td>{owner}</td>
	    <td>{assigned}</td>
	    <td align="center">{subadd}</td>
	    <td align="center">{view}</td>
	    <td align="center">{edit}</td>
	  </tr>
<!-- END todo_list -->

<!-- BEGIN table_footer -->
		</tbody>
	</table>
<!-- END table_footer -->

<!-- BEGIN page_footer -->
<div class="button_group">
	{add}
	{matrixview}
</div>
<!-- END page_footer -->

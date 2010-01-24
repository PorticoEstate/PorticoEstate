<!-- BEGIN header -->
<p>
 <center><h2>{lang_title}</h2></center>
 <center>{lang_showing}</center>

 <center>
 <table border="0" width="90%" align="center">
  <tr bgcolor="{bg_color}">
   {next_matchs}
  </tr>
 </table><!-- End Next Matchs -->
 </center>

 <center><!-- My Center Tag -->
  <table border=0 width=90% cellspacing=1 cellpadding=3>
   <tr bgcolor="{th_bg}">
    <th width="20%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_customer}
	</font>
    </th>
    <th width="6%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_job_num}
	</font>
    </th>
    <th width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_revision}
	</font>
    </th>
    <th width="38%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_summary}
	</font>
    </th>
    <th width="10%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_quoted}
	</font>
    </th>
    <th width="10%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_hours}
	</font>
    </th>
    <th width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_view}
	</font>
    </th>
    <th width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_edit}
	</font>
    </th>
    <th width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{lang_delete}
	</font>
    </th>
   </tr>
<!-- END header -->

<!-- BEGIN row -->
   <tr bgcolor="{tr_color}">
    <td width="20%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_customer}
	</font>
    </td>
    <td width="6%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_job_num}
	</font>
    </td>
    <td width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_revision}
	</font>
    </td>
    <td width="38%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_summary}
	</font>
    </td>
    <td width="10%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_quoted}
	</font>
    </td>
    <td width="10%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_hours}
	</font>
    </td>
    <td width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_view}
	</font>
    </td>
    <td width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_edit}
	</font>
    </td>
    <td width="4%">
	<font size="-1" face="Arial, Helvetica, sans-serif">
	{row_delete}
	</font>
    </td>
   </tr>
<!-- END row -->

<!-- BEGIN footer -->
  </table>
 </center>

  <form method="POST" action="{actionurl}">
   <input type="hidden" name="sort" value="{h_sort}">
   <input type="hidden" name="order" value="{h_order}">
   <input type="hidden" name="query" value="{h_query}">
   <input type="hidden" name="start" value="{h_start}">
   <input type="hidden" name="filter" value="{h_filter}">
   <input type="hidden" name="qfield" value="{h_qfield}">
  <center><table width="75%" border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td width="4%">
        <div align="right">
          <input type="submit" name="Add" value="{lang_add}">
        </div>
      </td>
      <td width="72%">&nbsp;</td>
      <td width="24%">&nbsp;</td>
    </tr>
  </table></center>
  </form>

<!-- END footer -->

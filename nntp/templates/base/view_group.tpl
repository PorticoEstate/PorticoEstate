<!-- $Id: view_group.tpl 16907 2006-07-23 10:42:06Z skwashd $ -->
<!-- BEGIN vg -->
<font face="{th_font}">
  <table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
  <tr>
    <td>
      <table border="0" width="100%">
	<tr>
	 {nml}
	 <td>&nbsp;</td>
	 {nmr}
	</tr>
      </table>
      <table border="0" cellpadding="0" cellspacing="1" width="100%" cols="3">
        <tr align="left">
	  <td colspan="3" valign="top" bgcolor="{th_em_folder}">
	    <font face="{th_font}" color="{th_em_text}">{folder}</font>
	  </td>
        </tr>
	{rows}
      </table>
    </td>
  </tr>
  </table>
</font>
<!-- END vg -->
<!-- BEGIN vg_row -->
	<tr bgcolor="{row_color}">
	  <td valign="top" width="55%">
	    {subject}
	  </td>
	  <td valign="top" width="27%">
	    {from}
	  </td>
	  <td valign="top" width="18%">
	    {date}
	  </td>
	</tr>
<!-- END vg_row -->



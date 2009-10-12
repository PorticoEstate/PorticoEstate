<!-- $Id: msg.tpl 16907 2006-07-23 10:42:06Z skwashd $ -->
<!-- BEGIN msg -->
<table cellpadding="1" cellspacing="1" width="95%" align="center"><form>
  <tr>
    <td colspan="2" bgcolor="{th_em_folder}">
      <table border="0" cellpadding="0" cellspacing="1" width="100%">
        <tr>
	  <td>
	    <font size="3" face="{th_font}" color="{th_em_text}">
	      <a href="{folder_url}">{folder}</a>
	    </font>
	  </td>
          <td align="right">
{rows}
  <tr>
  </tr>
  <tr>
    <table border="0" cellpadding="1" cellspacing="1" width="100%" align="center">
      <tr>
	<td align="center">
	  <table border="0" align="left" cellpadding="10" width="100%">
	    <tr>
	      <td>
	        {textbody}
	      </td>
	    </tr>
	  </table>
	</td></form>
      </tr>
    </table>
  </tr>
</table>
<!-- END msg -->
<!-- BEGIN action -->
	    {url}
<!-- END action -->
<!-- BEGIN next_prev -->
	  </td>
	  <td align="right">
	    {pm}{nm}
	  </td>
	</tr>
      </table>
    </td>
  </tr>
<!-- END next_prev -->
<!-- BEGIN header -->
  <tr>
    <td bgcolor="{th_bg}" valign="top">
      <font size="2" face="{th_font}">
	<b>{label}</b>
      </font>
    </td>
    <td bgcolor="{th_row_on}" valign="top" width="570">
{data}
    </td>
  </tr>
<!-- END header -->
<!-- BEGIN header_data -->
		<font size="2" face="{th_font}">{header_title}</font>
		&nbsp;<font size="2" face="{th_font}">{header_icon}</font>
<!-- END header_data -->

<script language="javascript" type="text/javascript"><!--
  function do_action(act)
   {
    flag = 0;
    for (i=0; i < document.delmov.elements.length; ++i)
     {
      // alert(document.delmov.elements[i].type);
      if (document.delmov.elements[i].type == "checkbox")
       {
        if (document.delmov.elements[i].checked)
         {
          flag = 1;
         }
       }
     }
    if (flag != 0)
     {
      document.delmov.what.value = act;
      document.delmov.submit();
     }
    else
     {
      alert("{select_msg}");
      document.delmov.tofolder.selectedIndex = 0;
     }
   }


  function check_all()
   {
    for (i=0; i < document.delmov.elements.length; ++i)
     {
      if (document.delmov.elements[i].type == "checkbox")
       {
        if (document.delmov.elements[i].checked)
         {
          document.delmov.elements[i].checked = false;
         }
        else
         {
          document.delmov.elements[i].checked = true;
         }
       }
     }
   }
  // -->
</script>

{auto_refresh_widget}
{widget_toolbar}
{V_arrows_form_table}
{stats_data_display}

<table class="basic" align="center" style="table-layout: fixed;">
  <tr class="bg_color1" style="font-weight: bold;">
		<td style="width: 44px" colspan="2"></td>
    <td style="width: 40%">{hdr_from}</td>
    <td style="width: 60%">{hdr_subject}</td>
    <td style="width: 100px; text-align: center">{hdr_date}</td>
    <td style="width: 100px; text-align: center">{hdr_size}</td>
  </tr>
<!-- BEGIN B_no_messages -->
  <tr class="bg_color2">
    <td colspan="6" class="center">
      {V_mlist_form_init}
      {report_no_msgs}
    </td>
  </tr>
<!-- END B_no_messages -->

<!-- BEGIN B_msg_list -->
	<tr class="bg_color2">
		<td class="center">
			{V_mlist_form_init}
			<input type="checkbox" name="{mlist_checkbox_name}" value="{mlist_embedded_uri}" />
		</td>
		<td class="center">
			{mlist_attach}
			{all_flags_images}
		</td>
    <td style="overflow: hidden; white-space: nowrap;">
			{open_strikethru}
				{open_newbold}
					{mlist_from} {mlist_from_extra}
				{close_newbold}
			{close_strikethru}
    </td>
		<td style="overflow: hidden; white-space: nowrap;">
			{open_strikethru}
				{open_newbold}
					<a href="{mlist_subject_link}">{mlist_subject}</a>
				{close_newbold}
			{close_strikethru}
    </td>
    <td class="center">{mlist_date}</td>
    <td class="center">{mlist_size}</td>
  </tr>
<!-- END B_msg_list -->
  <tr class="header">
		<td>
			<a href="javascript:check_all()"><img src="{check_image}"></a>
		</td>
    <td colspan="2">&nbsp;{delmov_button}</td>
    <td colspan="3" class="right">{delmov_listbox}&nbsp;</td>
</form>
  </tr>
</table>

{geek_bar}
{debugdata}
<br />


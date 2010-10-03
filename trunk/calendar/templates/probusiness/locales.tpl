<!-- BEGIN list -->
<p>
 	<table class="basic" align="center">
 	 <tr>
  	 <td class="left">{left_next_matchs}</td>
	   {center}
  	 <td class="right">{right_next_matchs}</td>
	  </tr>
 	</table>


 <table class="basic">
  <tr>
   <td class="header">&nbsp;{sort_name}</td>
   {header_rule}
   <td class="header">{header_edit}</td>
   <td class="header">{header_delete}</td>
   <td class="header">{header_extra}</td>
  </tr>

  {rows}

 </table>


 <table class="basic">
  <tr>
   <td class="left">
    <form method="POST" action="{new_action}">
     <input type="submit" value="{lang_add}" />
    </form>
   </td>
   {back_button}
   <td class="right">
    <form method="POST" action="{search_action}">
     {lang_search}&nbsp;<input name="query" />
    </form>
   </td>
  </tr>
 </table>
<!-- END list -->
<!-- BEGIN row -->
 <tr>
  <td>&nbsp;{group_name}</td>
  {rule}
  <td>{edit_link}</td>
  <td>{delete_link}</td>
  <td class="center" {extra_width}>{extra_link}</td>
 </tr>
<!-- END row -->
<!-- BEGIN row_empty -->
   <tr>
    <td colspan="5" class="center">{message}</td>
   </tr>
<!-- END row_empty -->
<!-- BEGIN back_button_form -->
   <td class="center">
    <form method="POST" action="{back_action}">
     <input type="submit" value="{lang_back}" />
    </form>
   </td>
<!-- END back_button_form -->

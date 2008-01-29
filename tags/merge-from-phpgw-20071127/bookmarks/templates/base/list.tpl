<!-- BEGIN page_header -->
<form action="{list_mass_select_form}" method="POST">
<!-- END page_header -->

<!-- BEGIN list_section -->
{header}
{LIST_HDR}
{LIST_ITEMS}
{LIST_FTR}
{footer}
<!-- END list_section -->

<!-- BEGIN list_keyw -->
[<em>{BOOKMARK_KEYW}</em>]
<!-- END list_keyw -->

<!-- BEGIN list_item -->
 <tr valign="top">
  <td width="2%" rowspan="2" align="right">
   {checkbox}
  </td>
  <td width="15%" rowspan="2" align="right" valign="center">
    {mail_link}
    {maintain_link}
    {view_link}
    <br>{bookmark_rating}
  </td>
  <td bgcolor="#FFFF99" >
   <a href="{bookmark_url}" target="_new">{bookmark_name}</a>
  </td>
 </tr>

 <tr valign="top">
  <td>
   {bookmark_desc} {bookmark_keywords} &nbsp;
  </td>
 </tr>
<!-- END list_item -->

<!-- BEGIN list_header -->
<strong class=bk><!-- <a href="{CATEGORY_SEARCH}"> -->{CATEGORY}
<!-- <a href="{SUBCATEGORY_SEARCH}"> --></strong>

<table width="100%" border="0" cellspacing="1">
 <tr>
  <td colspan="3" align="right">
   {lang_massupdate}
   {massupdate_delete_icon}
   {massupdate_mail_icon}
  </td>
 </tr>
<!-- END list_header -->

<!-- BEGIN list_footer -->
</table>
<br>
<!-- END list_footer -->

<!-- BEGIN page_footer -->
</form>
<!-- END page_footer -->

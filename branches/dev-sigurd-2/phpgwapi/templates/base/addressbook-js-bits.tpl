<!-- begin addressbook-js-bits.tpl -->
&nbsp; <!-- === block seperator == --> &nbsp;

<!-- BEGIN B_hidden_emails_list -->
 	<input type="hidden" name="{hidden_name}" value="{hidden_value}"> 
<!-- END B_hidden_emails_list -->

&nbsp; <!-- === block seperator == --> &nbsp;
<!-- BEGIN B_toselectbox -->
	<option value="{toselvalue}" {tosel_is_selected}>{toselname}</options>
<!-- END B_toselectbox -->

&nbsp; <!-- === block seperator == --> &nbsp;

<!-- BEGIN B_ccselectbox -->
	<option value="{ccselvalue}" {ccsel_is_selected}>{ccselname}</options>
<!-- END B_ccselectbox -->

&nbsp; <!-- === block seperator == --> &nbsp;

<!-- BEGIN B_bccselectbox -->
	<option value="{bccselvalue}" {bccsel_is_selected}>{bccselname}</options>
<!-- END B_bccselectbox -->

&nbsp; <!-- === block seperator == --> &nbsp;

&nbsp; <!-- === block seperator == --> &nbsp;
<!-- BEGIN B_addressbook_record_inner -->
{field_name}{space_to_fit}{field_value}
<!-- END B_addressbook_record_inner -->


&nbsp; <!-- === block seperator == --> &nbsp;

<!-- end addressbook-js-bits.tpl -->

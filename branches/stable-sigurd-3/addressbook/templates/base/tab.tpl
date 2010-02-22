<!-- BEGIN tab -->
<div class="yui-navset">
	<ul id="contacts_section_tabs" class="yui-nav">
		{buttons}
	</ul>
<input type="hidden" name="bname" />
<input type="hidden" name="_submit" />
<script language="JavaScript" type="text/javascript">
<!--
function changetab(selectedtype)
{
  document.body_form.bname.value = selectedtype ;
  document.body_form.submit() ;
}
function submit_form(selectedtype)
{
  document.body_form._submit.value = selectedtype ;
  document.body_form.submit() ;
}
-->
</script>


</div>
<!-- END tab -->

<!-- BEGIN button -->
	<!--<li class="{tab_css}"><input type="submit" name="{tab_name}" value="{tab_caption}"></li>-->
	<li class="{tab_css}"><a href="javascript:changetab('{tab_caption}')"><em>{tab_caption}</em></a></li>
<!-- END button -->



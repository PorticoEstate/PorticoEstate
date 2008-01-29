<!-- BEGIN Contentarea -->
<h2>Contentarea {contentarea}</h2>
<!-- BEGIN Moduleeditor -->
<h4>Module {moduleinfo}</h4>
<form method="POST">
{interface}
<input type="hidden" value="{blockid}" name="blockid" />
<input type="submit" value="Save" name="btnSaveModule" />
</form>
<!-- END Moduleeditor -->
<form method="POST">
<input type="text" name="modulename" />
<input type="hidden" value="{areaid}" name="areaid" />
<input type="submit" name="btnAddModuletoArea" value="{lang_addmodule}" />
</form>
<!-- END Contentarea -->

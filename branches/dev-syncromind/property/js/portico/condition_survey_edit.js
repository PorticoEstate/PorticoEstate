//	call to AutoCompleteHelper JQUERY
	var oArgs = {menuaction:'property.uicondition_survey.get_users'};
	var strURL = phpGWLink('index.php', oArgs, true);
	JqueryPortico.autocompleteHelper(strURL, 'coordinator_name', 'coordinator_id', 'coordinator_container');
	
	var oArgs = {menuaction:'property.uicondition_survey.get_vendors'};
	var strURL = phpGWLink('index.php', oArgs, true);
    JqueryPortico.autocompleteHelper(strURL, 'vendor_name', 'vendor_id', 'vendor_container');

	this.fileuploader = function()
	{
		var requestUrl = phpGWLink('index.php', fileuploader_action);
		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true});
	}


	function refresh_files()
	{
		var oArgs = {menuaction:'property.uicondition_survey.get_files', id:survey_id};
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable0, strURL);
	}
	
	function lightbox_hide()
	{
		TINY.box.hide();
		return true;
	}



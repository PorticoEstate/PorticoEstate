	function refresh_files()
	{
		var cat_id = document.getElementById("cat_id").value;
		var oArgs = {menuaction:'manual.uidocuments.get_files', cat_id:cat_id};
		var strURL = phpGWLink('index.php', oArgs, true);
		YAHOO.portico.updateinlineTableHelper('datatable-container_0', strURL);
	}

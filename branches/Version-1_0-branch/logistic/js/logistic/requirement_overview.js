	function load_requirement_edit( activity_id ){
		var oArgs = {menuaction: 'logistic.uirequirement.edit', activity_id:activity_id, nonavbar: true, lean: true};
		var requestUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){closeJS_local()}});
	}

	function load_requirement_edit_id( id ){
		var oArgs = {menuaction: 'logistic.uirequirement.edit', id:id, nonavbar: true, lean: true};
		var requestUrl = phpGWLink('index.php', oArgs);

		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){closeJS_local()}});
	}

	function load_requirement_delete_id( id ){
		confirm_msg = 'Slette behov?';
		if(confirm(confirm_msg))
		{
			var oArgs = {menuaction: 'logistic.uirequirement.delete', id:id};
			var requestUrl = phpGWLink('index.php', oArgs, true);

			var callback =	{	success: function(o){
								//	var message_delete = o.responseText.toString().replace("\"","").replace("\"","");
									var reqUrl = datatable_source;
									YAHOO.portico.inlineTableHelper('requirement-container', reqUrl, YAHOO.portico.columnDefs);
									},
							failure: function(o){window.alert('failed')},
							timeout: 10000
						};
			var request = YAHOO.util.Connect.asyncRequest('POST', requestUrl, callback);
		}
	}


	function load_delete_allocation( id ){
		confirm_msg = 'Slette allokering?';
		if(confirm(confirm_msg))
		{
			var oArgs = {menuaction: 'logistic.uirequirement_resource_allocation.delete', id:id};
			var requestUrl = phpGWLink('index.php', oArgs, true);

			var callback =	{	success: function(o){
									//message_delete = YAHOO.lang.JSON.parse(o.responseText);
									//console.log(message_delete);
									YAHOO.portico.updateinlineTableHelper('requirement-container');
									YAHOO.portico.updateinlineTableHelper('allocation-container');
									},
							failure: function(o){window.alert('failed')},
							timeout: 10000
						};
			var request = YAHOO.util.Connect.asyncRequest('POST', requestUrl, callback);
		}
	}


	function load_assign_task(frm, id ){

		var assign_requirement = new Array();
		var message = "";

		//For each checkbox see if it has been checked, record the value.
		for (i = 0; i < frm.assign_requirement.length; i++)
		{
			if (!frm.assign_requirement[i].disabled)
			{
				if (frm.assign_requirement[i].checked)
				{
        			assign_requirement.push(frm.assign_requirement[i].value)
        		}
        	}
      	}

//console.log(assign_requirement);

		assign_requirement = encodeURI(YAHOO.lang.JSON.stringify(assign_requirement));

//alert(assign_requirement);

		var oArgs = {menuaction: 'logistic.uirequirement.assign_job', id:id, assign_requirement: assign_requirement};
		var requestUrl = phpGWLink('index.php', oArgs);


		TINY.box.show({iframe:requestUrl, boxid:'frameless',width:750,height:450,fixed:false,maskid:'darkmask',maskopacity:40, mask:true, animate:true, close: true,closejs:function(){closeJS_local_allocation(id)}});
	}


	function closeJS_local()
	{
		var reqUrl = datatable_source;
		YAHOO.portico.inlineTableHelper('requirement-container', reqUrl, YAHOO.portico.columnDefs);
	}


	function closeJS_local_allocation(requirement_id)
	{
			YAHOO.portico.updateinlineTableHelper('allocation-container');
	}

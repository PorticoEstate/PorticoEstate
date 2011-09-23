 /* Specific js-code for controller::item
 *
 */

//alert('dette er respons fra "controller/js/yahoo/controller.item.js"');

	this.get_translations = function()
	{
		var callback =	{success: function(o){
							lang = JSON.parse(o.responseText);
	//						console.log(lang);
							alert(lang);
							},
							failure: function(o){window.alert('Server or your connection is dead.')},
							timeout: 10000
						};
		var oArgs = {menuaction:'controller.uicontrol_item2.js_poll',poll:''};
		var strURL = phpGWLink('index.php', oArgs, true);
		var request = YAHOO.util.Connect.asyncRequest('POST', strURL, callback);
	}

	get_translations();

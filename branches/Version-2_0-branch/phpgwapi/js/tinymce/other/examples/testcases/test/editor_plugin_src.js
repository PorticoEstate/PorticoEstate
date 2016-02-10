/**
 * $RCSfile$
 * $Revision$
 * $Date$
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

tinyMCE.importPluginLanguagePack('test', 'en,tr,he,nb,ru,ru_KOI8-R,ru_UTF-8,nn,fi,cy,es,is,pl');

var TinyMCE_TestPlugin = {
	getInfo : function() {
		return {
			longname : 'Test plugin',
			author : 'Your name',
			authorurl : 'http://www.yoursite.com',
			infourl : 'http://www.yoursite.com/docs/template.html',
			version : "1.0"
		};
	},

	initInstance : function(inst) {
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "test":
				return tinyMCE.getButtonHTML(cn, 'lang_test_desc', '{$pluginurl}/images/test.gif', 'mceTest', true);
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		switch (command) {
			case "mceTest":
				alert('Test');
				return true;
		}

		return false;
	}
};

tinyMCE.addPlugin("test", TinyMCE_TestPlugin);

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
	<meta http-equiv="CONTENT-TYPE"
	content="text/html; charset=utf-8">
	<title>{title}</title>
	<STYLE TYPE="text/css">
		SELECT { font-size: {widget_font_size}; }
		INPUT{ font-size: {widget_font_size}; }
		TEXTAREA { font-size: {widget_font_size}; color: blue }
		body { margin-top: 0px; margin-right: 0px; margin-left: 0px; font-family: Arial, Helvetica, san-serif }
	</STYLE>
	<script language="javascript">
		//Variable declarations
		// All these are data expected to be here by the mainframe or the secondary frame at
		//an undetermined time
		var toselectbox_name;
		var toselectbox_email;
		var toselectbox_keys;
		var ccselectbox_name;
		var ccselectbox_email;
		var ccselectbox_keys;
		var bccselectbox_name;
		var bccselectbox_email;
		var bccselectbox_keys;
		var mainfirsttimeflag;
		//This last var is an array populated just before the window closes (upon done)
		//this is filled with a simple array filled with the selected contact ids
		//the mainframe then calls the update_caller event which we implement bellow
		//that, if the php framework required it, will call a callback function in
		//its opener. This is the way to get the selected contact id's in javascript
		//
		var finally_selected_contact_ids;
		mainfirsttimeflag=1;
		function errorhandle(msg, url, line)
		{
			return true;
		}

		self.onerror=function () { return true; }
		window.onerror=self.onerror;
	
		//@function set_destboxes
		//@param getvars The string of GET style http variables
		//@discussion
		//This function tells the secondary frame to go and set the data for the destination boxes
		//The acutal values of the destination boxes where converted to get style vars
		//by a call to option2get method of the selectMover js object in selectboxes.js
		function set_destboxes(getvars)
		{
			//alert("{set_destboxes_link}"+getvars);
			//window.secondaryframe.location="{set_destboxes_link}"+getvars;
			create_destboxes_set(getvars);
		}
		function create_destboxes_set(getvars)
		{
			window.secondaryframe.document.open();
			window.secondaryframe.document.write('<form name="bigdataform" action="{set_destboxes_link}" method="POST"><input type="hidden" name="big_select" value="'+getvars+'"></form>');
			window.secondaryframe.document.close();
			window.secondaryframe.document.forms['bigdataform'].submit();
			
		}
		//@function destboxes_set
		//@discussion
		//Upon loading the resultiong arrays from the set_destboxes call, the php backend
		//will output an onLoad event which calls this function. Before that, it will have sent
		//three arrays, one per destination box. This arrays hold names, emails and addressbook
		//entry id's respectivly per destination box.
		//Obviously, the index to each of the arrays elements directly relate
		//the email with the id and the name. So, the email of the addressbook entry
		// Whose fullname is toselectbox_name[i], is in toselectbox_email[i]...and
		//so is the corresponding key in toselectbox_key[i]
		//When this frameset receives this event, it will initialize its arrays
		//and call the mainframe so it can update itself with this newly gotten data
		function destboxes_set()
		{
			
			window.toselectbox_name=window.secondaryframe.toselectbox_name;
			window.toselectbox_email=window.secondaryframe.toselectbox_email;
			window.toselectbox_keys=window.secondaryframe.toselectbox_keys;
			window.ccselectbox_name=window.secondaryframe.ccselectbox_name;
			window.ccselectbox_email=window.secondaryframe.ccselectbox_email;
			window.ccselectbox_keys=window.secondaryframe.ccselectbox_keys;
			window.bccselectbox_name=window.secondaryframe.bccselectbox_name;
			window.bccselectbox_email=window.secondaryframe.bccselectbox_email;
			window.bccselectbox_keys=window.secondaryframe.bccselectbox_keys;
			if(window.mainframe)
			{
				window.mainframe.event_destboxes_saved();
			}
		}
		//show userdata event variables
		var userdata;
		var userdata_width;
		var userdata_height;
		//@function get_user_data
		//@param usertofetch This should be a nameselect[0]=id string allways
		//@discussion will tell the secondary frame to go and call the get_userdata
		//public method of the uijsaddressbook php class. This class will get the id
		//from the nameselect var and will query the backend for us. It will then urlencode
		//the generated record (it formats it with spaces and all) and send it back to us in the
		//userdata variable.
		function get_user_data(usertofetch)
		{
			window.secondaryframe.location="{get_user_data_link}"+usertofetch;
		}
		//@function userdata_got
		//@discussion
		//Upon loading the data got from the get_userdata call to the php uijsaddressbook class, 
		//the secondary frame's body tag will have an call to this function in its onLoad event
		//This will call us. The contents of the resulting document in the secondary frame will
		//already have a urlencoded string representing the userdata the way we want to show it
		//Also, the userdata_height and width will be set in as variables in the secondary frame
		//This two variables are used by the mainframe's event_userdata_got function
		//to dynamically set the size of the userdata textbox.
		function userdata_got()
		{
			window.userdata=window.secondaryframe.userdata;
			window.userdata_rows=window.secondaryframe.userdata_rows;
			window.userdata_cols=window.secondaryframe.userdata_cols;
			window.mainframe.event_userdata_got();
		}
		//@function forget_destboxes
		//@discussion will direct the secondary frame to the forget_all public
		//phpgw method in the uijsaddressbook class
		//This will cause the php backend to forget everything in cache
		function forget_destboxes()
		{
			window.secondaryframe.location="{forget_all_link}";
		}
		//@function all_forgoten
		//@discussion Upon forgeting all, the php backend will output just a call to this function
		//we let know the mainframe that all has been forgoten by calling the corresponding event function
		//in it
		function all_forgoten()
		{
			window.mainframe.event_all_forgoten();
		}
		//@function update_opener
		//@discussion In the final moment, finally_selected_contact_ids is populated
		//with a flat array that has all the selected contact id's. If the php framework
		//decides so (through the ui constructor's parameter), it will set the template
		//variable please_update_opener to 1, so the if bellow will evaluate to 1 and
		//will call a jsaddybook_closing(array) method in the opener, 
		//to which it will send the finally_selected_contact_ids array with the 
		//ids that were selected.
		function update_opener()
		{
			if({please_update_opener} == 1)
			{
			//	alert('Updating Opener'+window.finally_selected_contact_ids);
				window.opener.jsaddybook_closing(window.finally_selected_contact_ids);
			}
		}
		
	</script>
	</head>
	<frameset cols="100%,0%">
		<FRAME NAME="mainframe" SRC="{mainframe_link}">
		<FRAME NAME="secondaryframe" SRC="{set_destboxes_link}">
	</frameset>
</html>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
     
  <meta http-equiv="CONTENT-TYPE"
	 content="text/html; charset=utf-8">
  <STYLE TYPE="text/css">
  	SELECT { font-size: {widget_font_size};}
	INPUT{ font-size: {widget_font_size};}
  	TEXTAREA { font-size: {widget_font_size};}
	 body { margin-top: 0px; margin-right: 0px; margin-left: 0px; font-family: "Arial", Helvetica, sans-serif; }
	#wait { visibility:hidden;}
	
</STYLE>
  <script src="{include_link}"> 

  
  </script>
  <script language="javascript">
  	var savedestboxes;
	var backtobox;
	var closewhenfinished;
	var forget_after;
	var submittheform;
	var hidetoselect;
	var hideccselect;
	var hidebccselect;
	var fetch_udata;
	var toparser;
	var ccparser;
	var bccparser;
	var forget;
	var clicked_destination;
	self.onerror=function(){ return true; }
//	window.onerror=self.onerror;
	//Global variables to know if we neednt work on a given destination box
	//since the php framwork wants it hidden
	window.hidetoselect={hidetoselectjs};
	window.hideccselect={hideccselectjs};
	window.hidebccselect={hidebccselectjs};
	//Dummy
	window.event_destboxes_saved=event_destboxes_saved;
	//@function event_userdata_got
	//@discussion called by the frameset upon receiving a call fromt he secondary frame telling it
	//the userdata is here.....we get it from the frameset and act accordingly
	//unhiding the textarea userdata box and seting its proper width and heigth
	function event_userdata_got()
	{
		document.forms["{main_form_name}"].elements['userdata'].value=unescape(window.parent.userdata);
		document.forms["{main_form_name}"].elements['userdata'].rows=window.parent.userdata_rows;
		document.forms["{main_form_name}"].elements['userdata'].cols=window.parent.userdata_cols;
		hider('wait',1);
	 	hider('udata',0);	
	}
	//@function event_all_forgoten
	//@discussion Well...upon knowing we have forgoten everything we execute this code
	//Which checks out if some event wants us to close ourselves....if so is the case, we do it
	function event_all_forgoten()
	{
		
		if(window.closewhenfinished==1)
		{
			window.update_opener();	
			window.parent.close();
			
		}
		hider('wait',1);
		
	}
	function update_opener()
	{
		//alert("Emails updated emails-> "+window.mover.email_values);
		//alert("Keys updated keys-> "+window.mover.email_keys);
		var tosel;
		var ccsel;
		var bccsel;
		var flatarray;
		flatarray=Array();
		if(window.hidetoselect==0)
		{
			tosel=mover.keys_from_selectbox('toselectbox[]');
			flatarray=tosel;
		//	alert("Options Array TO-> "+tosel);
		}
		if(window.hideccselect==0)
		{
			ccsel=mover.keys_from_selectbox('ccselectbox[]');
			flatarray=flatarray.concat(ccsel);
		//	alert("Options Array CC-> "+ccsel);
		}
		if(window.hidebccselect==0)
		{
			bccsel=mover.keys_from_selectbox('bccselectbox[]');
			flatarray=flatarray.concat(bccsel);
		//	alert("Options Array BCC-> "+bccsel);
		}
		window.parent.finally_selected_contact_ids=flatarray;
								
		//alert("catenated Array finally_selected_contact_ids -> "+window.parent.finally_selected_contact_ids);
		window.parent.update_opener();
	}
	//@function event_destboxes_saved
	//@discussion Okay, the destinationbox data we sent in the save_destination_boxes trigger have been saved
	//This function lets us decide what we want to do when we are told that the data was saved, and that new email
	//data is here.
	//If an event wants us to copy all the destination box data back to the compose textboxes, we do it
	//If an event wants u to die after that, we do so
	//If an event wants the form submited to the phpn backend now, we do it as well (category select does this)
	//If an event wants us to remove the selected options in the destination boxes, we gladly comply
	function event_destboxes_saved()
	{
		//alert("The destination boxes have been saved");
		window.hidetoselect={hidetoselectjs};
		window.hideccselect={hideccselectjs};
		window.hidebccselect={hidebccselectjs};
		mover.sortSelect('nameselect[]');
		if(window.backtobox==1)
		{
			
			window.all_to_dest();
		}
		if(window.closewhenfinished==1)
		{
			//What to do when we are closing
			window.update_opener();
			window.parent.close();
		}
		if(window.remove_selections==1)
		{
			if(window.hidetoselect==0 && window.remove_selections==1)
			{
				mover.update_emails(window.parent.toselectbox_email,window.parent.toselectbox_keys);
				mover.removeSelectedFromParser('toselectbox[]',window.toparser);
				mover.moveSelectedOptions('toselectbox[]','nameselect[]'); 

			}
			if(window.hidebccselect==0 && window.remove_selections==1)
			{
				mover.update_emails(window.parent.bccselectbox_email,window.parent.bccselectbox_keys);
				mover.removeSelectedFromParser('bccselectbox[]',window.bccparser);
				mover.moveSelectedOptions('bccselectbox[]','nameselect[]'); 
			}
			if(window.hideccselect==0 && window.remove_selections==1)
			{
				mover.update_emails(window.parent.ccselectbox_email,window.parent.ccselectbox_keys);
				mover.removeSelectedFromParser('ccselectbox[]',window.ccparser);
				mover.moveSelectedOptions('ccselectbox[]','nameselect[]');
			}
			window.savedestboxes=1;
			window.remove_selections=0;
			hider('wait',1);
		}
		if(window.submittheform==1)
		{
			document.forms["{main_form_name}"].submit();
		}
		window.submittheform=0;
		window.backtobox=0;
	}
	//@function all_to_dest
	//@abstract NOT AN EVENT HANDLER
	//@discussion This function syncronizes the data in the destination boxes to the 
	//destination textboxes in the compose window. It does it very smartly by using
	//parsers which are array represented comma separated lists. So, in this case,
	//This function uses the selectToParser function which makes shure we dont repeat
	//the same address in the destination box. Of course, it expects that all emails
	//addresses for the destination boxes are here. That is why we only call this function
	//after a save_destboxes event
	function all_to_dest()
	{
		window.hidetoselect={hidetoselectjs};
		window.hideccselect={hideccselectjs};
		window.hidebccselect={hidebccselectjs};
		if(window.hidetoselect==0)
		{
			mover.selectToParser('toselectbox[]',window.parent.toselectbox_keys
						,window.parent.toselectbox_name
						,window.parent.toselectbox_email
						,window.toparser);
			mover.stringToTextbox(window.toparser.parse_out(),window.parent.opener.document.doit.to);
		}
		if(window.hidebccselect==0)
		{
			mover.selectToParser('bccselectbox[]',window.parent.bccselectbox_keys
						,window.parent.bccselectbox_name
						,window.parent.bccselectbox_email
						,window.bccparser);
			mover.stringToTextbox(window.bccparser.parse_out(),window.parent.opener.document.doit.bcc);
		
		}
		if(window.hideccselect==0)
		{
			mover.selectToParser('ccselectbox[]',window.parent.ccselectbox_keys
						,window.parent.ccselectbox_name
						,window.parent.ccselectbox_email
						,window.ccparser);
			mover.stringToTextbox(window.ccparser.parse_out(),window.parent.opener.document.doit.cc);
		}
	}
	//@function trigger_save_destboxes
	//@abstract A trigger to tell this thing to go save the destboxes data
	//@discussion triggers are functions that really go and 
	//tell the frameset to move its ass and tell the secondary frame
	//to do something for us. This trigger um...triggers the save_destboxes
	//event which will save all data in the destination boxes in the bo class
	//of the phpgw backend. It previously converts all destination boxes
	//to GET type http vars and looks if it has to flag the backend to
	//completely purge a destination box (if its empty)
	function trigger_save_destboxes()
	{
		var str;
		window.hidetoselect={hidetoselectjs};
		window.hideccselect={hideccselectjs};
		window.hidebccselect={hidebccselectjs};
		if(window.hidetoselect==0)
		{
			str=mover.option2get('toselectbox[]');
			if(mover.getSize('toselectbox[]')< 1)
			{
				str=str+"&deleted[toselectbox]=true";
			}
		}
		if(window.hideccselect==0)
		{
			str=str+mover.option2get('ccselectbox[]');
			if(mover.getSize('ccselectbox[]')< 1)
			{
				str=str+"&deleted[ccselectbox]=true";
			}
		}
		if(window.hidebccselect==0)
		{
			str=str+mover.option2get('bccselectbox[]');
			if(mover.getSize('bccselectbox[]')< 1)
			{
				str=str+"&deleted[bccselectbox]=true";
			}
		}
		if((window.closewhenfinished==1) && (window.forget_after==1))
		{
			str=str+"&forget_after=1";
		}
	
		window.parent.set_destboxes(str);
	}
	//@function trigger_forget
	//@abstract triggers the forget_destboxes event
	//@discussion WARNING
	//This event is badly named in the parent window. Should be named forget_all
	//Im not touching it cause it works, but well whatever...
	//By now you should know what this does. For the sake of the mentally chalenged
	//i will repeat: This calls the forget_destboxes event on the partent frameset
	//which will direct the secondary frame to the forget_all method on the uijsaddressbook class
	//in turn, that class will output an onLoad event for the body of the page it generates
	//which will call window.parent.destboxes_forgoten...that, in turn, calls the event_all_forgoten
	//which is declared and explained up there ^ .. u see? ....More Up THERE...there
	
	function trigger_forget()
	{
		window.parent.forget_destboxes();
	}
	//@function go_triggers
	//@abstract Machine gun... will call all triggers flagged to GO!
	//@discussion 
	//Okay now, lets go through this again:
	//This file declares some nasty global variables. 
	//Understand that all of them are flags. They tell this functions, and the events above
	//which other functions to call. This function is called by anything in this file that
	//needs data updated, like when you remove elements from the destboxes, we need to know
	//the emails of the destinataries so that we can also remove them from the parsers which
	//actually will end up being the string we insert in the compose destination boxes. So the onCLick
	//event of the remove button, sets savedestboxes to 1, and removeselections also to 1 and calls
	//this go_triggers function. Thisone then, necesarily calls trigger_save_destboxes
	//Whcih will transform all the select boxes into GET vars and tell the frameset to
	//send that to the php backend through the secondary frame. The corresponding funciton
	//in the php backend will output the emails arrays which have whatever data we dont have
	//here in the emails hidden fields (which ONLY get generated when the form in this frame is submited)
	//Finaly, the event_saved_destboxes will be called and it will se what it needs to do upon such
	//notification. It checks the flags and finds that it also should remove the selected options
	//on the destination checkboxes. "Okay", it says, and calls all those mover.update_email function and
	//mover.removeSelectedFromParser functions that actually reflect what you are removing in the resulting
	//emails string that will go into the compose's destination textboxes (bear with me...this are the parsers)..
	//Now, obviously, this could get ugly if say, saveddestboxes is on, and fetch_udata is on too...
	//Well, thats something we leave for the caller to be carefull of.
	function go_triggers()
	{
		if(window.savedestboxes==1)
		{
			window.trigger_save_destboxes();
		}
		if(window.fetch_udata)
		{
			var selidx;
			window.parent.get_user_data(fetch_udata); 
			window.fetch_udata=false;
		}
		if(window.forget==1)
		{
			window.trigger_forget();
		}
		hider("wait",0);
		

	}
  </script>
</head>
<body onload="//The onload function initializes the autoselect framework
		setUpVisual('{main_form_name}','nameselect[]','searchautocomplete');
		//Makes shure that, if there is a value in  the autocomplete textbox,
		//the nameselectbox reflects this (thats what bldUpdate does after youve
		//initialized obj1 with setUpVisual
		obj1.bldUpdate();
		//Sorts the main selectbox
		mover.sortSelect('nameselect[]');
		//This function also takes whatever is in the destination textboxes 
		//of the compose window (if u guys change the names of them ill kill u)
		//and creates parsers for all of them. Parsers are cool cause they
		//make an array that has an email address per comma separated value
		//in the sourcer strings... so this allows us to do funcky suff like bellow
		window.toparser=new box_parser(window.parent.opener.document.doit.to.value);
		window.ccparser=new box_parser(window.parent.opener.document.doit.cc.value);
		window.bccparser=new box_parser(window.parent.opener.document.doit.bcc.value);
		if(window.parent.mainfirsttimeflag==1)//first time loaded
		{
			//If its our first time here
			//Which means the firsttime flag in the frameset is set to 1,
			//we also try and sync whatever is in the destination boxes with what
			//we have in the parser.... this allows us for stuff like:
			//Uopen the addressbook, select some destinataries in the to box, click done
			//change your mind, delete by hand a destinatary in the textbox
			//click addressbook again...and its gone from the corresponding selectbox as well..
			//cool? ... well i think so
			mover.update_emails(window.parent.toselectbox_email,window.parent.toselectbox_keys);
			if(window.hidetoselect != 1)
			{
				mover.removeParsedFromSelect('toselectbox[]',window.toparser);
			}
			if(window.hidebccselect != 1)
			{
				mover.removeParsedFromSelect('bccselectbox[]',window.bccparser);
			}
			if(window.hideccselect != 1)
			{
			mover.removeParsedFromSelect('ccselectbox[]',window.ccparser);
			}
			window.parent.mainfirsttimeflag=0;
			window.savedestboxes=1;
			mover.selectOptionOfValue('cat_id',{selected_cat_value});
			}
			hider('wait',1);
		" >
<table style="width: {optimal_width}px;" cellpadding="0" border="1"
 cellspacing="0">
    <thead>
	<tr>
	<td valign="top" align="center" bgcolor="#336699" style="width: 30%;">		
		<font size="-1" face="Helvetica, Arial, sans-serif" color="#ffffff">			
			{first_column_label}
		</font>
		<font color="#ffffff">		
			<br>
		</font>		
		</td>
		<td valign="top" align="center" bgcolor="#336699" style="width: 30%">		              
			<div align="center">			
			      <font face="Helvetica, Arial, sans-serif" size="-1" color="#ffffff">
				      {second_column_label} 
			      </font>			
			      <font   style="color:#ffffff; font-weight:bold;">				
				      <br>
				      {selected_cat_name}
				</font>		
			</div>
		<font color="#ffffff">			
		<br>
		</font>		
		</td>
		<td valign="top"  style="text-align: center; background-color: rgb(51, 102, 153);"  rowspan="1" colspan="3">
	      <div align="center"><font face="Helvetica, Arial, sans-serif" size="-1" color="#ffffff">
		{third_column_label}
	      </font>			
	      <font color="#ffffff">			
	      <br>
	      </font>		
	      </div>
	      </td>
	      </tr>
    <tr bgcolor="#cccccc">
      <td colspan="5" valign="middle">
        <div ID="wait">
	<center>
	<!--
		<font style="font-weight=bold; font-family=Helvetica,Arial,sans-serif">
			Please Wait!
		</font>
	-->
		 <img src="{wait_img_path}">
	</center>

	</div>
	</td>
    </tr>
    <tr>

     <form name="{main_form_name}" action="{main_form_action}" method="POST" onSubmit="
     												"> 
	<td rowspan="1" style="width: 30%" valign="top" align="center" bgcolor="#cccccc">             
      <select name="cat_id"  size="30"   onChange="//A cat_id selection makes us submit the whole form
      						//We could make an event for this
							window.savedestboxes=1;
      							window.submittheform=1;
							window.go_triggers(); ">
	{cats_list}
      	<option value="-2">All (can be very slow)</option>
      	<option value="-3">Personal</option>
      </select>
      <!--jarg_SOG s -->
      <br>
      <input type="checkbox" name="sel_all_cat" {sel_all_cat_checked} onClick="
                                                                       window.savedestboxes=1;
                                                                       window.submittheform=1;
                                                                       window.go_triggers();">
          <font face="Helvetica, Arial, san-serif" size="-1" style="font-size:{widget_font_size};">Send to all category</font>
      <!--jarg_SOG e -->

      </td>
      <td colspan="1" style="width: 30%" valign="top" rowspan="1" bgcolor="#cccccc">             
      <table style="width:100%">
      <tbody>
	<tr>
		<td style="width:50%">			
			<input type="text" size="15" name="searchbox" value="{searchbox_value}">
		</td>
		<td style="width:50%">		
			<select name="querycommand"  onChange=" 
								//Get what was selected
												var selidx;
												selidx=mover.getSelectedIdx('querycommand');
								//If it was a goquery command
												if(this.form.elements['querycommand'][selidx].value 
													== 'cleanquery')
												{
								//Clean the searchbox and proceed to submit
													this.form.searchbox.value='';
												}
												selidx=mover.getSelectedIdx('cat_id');
												if(selidx == -1)
												{
								//If no category is selected, user wants to search through the whole thing
								//which is the option of value -2 in the cat_id selectbox (hardcoded, look
								//down there)
													mover.selectOptionOfValue('cat_id',-2);
												}
												window.savedestboxes=1;
												window.submittheform=1;
												window.go_triggers();">
			<option value="selectquery" selected>{select_query_command} </option> 
			<option value="goquery">{go_query_command} </option> 
			<option value="cleanquery">{clear_query_command} </option> 
			</select>
		</td>
	</tr>
	                  
        </tbody>             
      </table>
             
      <table style="width:100%">
	<tbody>
	<tr>
		<td style="width:50%">
			<input type="text" name="searchautocomplete" value="{search_ac_value}" size="15"  onkeyup="
													javascript:
													//We only care if
													//a key was hit
													//so we can update
													///the nameselectbox
													//to autolimit itself
													//to what it should
														obj1.bldUpdate();
														
														">	
	            </td>
		    <td style="width:50%;align:left;">
		    <select name="filter" onChange="
							//Get what was selected
							var selidx;
							selidx=mover.getSelectedIdx('filter');
							if( (this.form.elements['filter'][selidx].value=='user_only') ||
							    (this.form.elements['filter'][selidx].value=='private'))
							    {
							    	//This query is over all categories
								//if you want to see private or owner=you entries
							    	mover.selectOptionOfValue('cat_id',-2);
							    }
							window.savedestboxes=1;
							window.submittheform=1;
							window.go_triggers();">
		    {hide_directory_option_open}
        	    <option value="directory" {directory_is_selected}> System </option>
		    {hide_directory_option_close}
        	    <option value="none" {global_is_selected}> Global </option>
	            <option value="user_only" {mine_is_selected}> Mine </option>
        	    <option value="private" {private_is_selected}> Private </option>
	            </select>
		   </td>
	</tr>
	                  
        </tbody>             
      </table>
      <br>
      <select name="nameselect[]" size="15" multiple="multiple" onChange="
											var selidx;
	//Im not crazy...all this is necesary. If we hit an option in the selectbox
	//...one and only one, we need to see if the view more data checkbox is on
	//in which case, we unselect all other boxes to avoid confusion for the user
	//and we trigger a get_userdata event....by setting the window.fetch_udata flag
	//and calling go_triggers
											selidx=mover.getSelectedIdx('nameselect[]');
											if(mover.numberSelectedOptions('nameselect[]') == 1)
												{
													if(this.form.viewmore.checked==1)
													{
														selidx=mover.getSelectedIdx('nameselect[]');
											
												
														window.fetch_udata='&nameselect='+this.form.elements['nameselect[]'][selidx].value;
														if(window.hideccselect!=1)
														{
															mover.unselectAll('ccselectbox[]');
														}
														if(window.hidetoselect!=1)
														{
															mover.unselectAll('toselectbox[]');
														}
														if(window.hidebccselect!=1)
														{
													
															mover.unselectAll('bccselectbox[]');
														}
														window.go_triggers();			
													}
													
												}
										
										">
      <!-- BEGIN addressbook_names -->
      	<option value="{name_option_value}" {name_option_selected}>{name_option_name}</option>
      <!-- END addressbook_names -->
	</select>
{V_hidden_emails_list}
	<table style="width:100%">
        	<tbody>
         	<tr>
                	<td style="width:100%">

			<font face="Helvetica, Arial, san-serif" size="-1" style="font-size:{widget_font_size};">
				More Data </font>
				<input type="checkbox" name="viewmore" {viewmore_checked} onClick="
				//If u click the viewmore checkbox, it checks what is selected in the
				//nameselect selectbox. If only one value is selected, it fetches that user
				//and unselects every other selection in the rest of the selectboxes
				//If its not in the name select box (u have more than one selected there
				//or none, it will set the fetch_udata flag to whatever is in the 
				//window.clicked_destination var. This var was set when u clicked a single
				//option in any of the destination boxes.
				//In the end, if it needs to (something was selected somwewhere) it calls go_triggers
														javascript:
														var selidx;
														selidx=mover.getSelectedIdx('nameselect[]');
														if(this.form.elements['nameselect[]'][selidx])
														{
															window.fetch_udata='&nameselect='+this.form.elements['nameselect[]'][selidx].value;
															if(window.hideccselect != 1)
															{
																mover.unselectAll('ccselectbox[]');
															}
															if(window.hidetoselect != 1)
															{
															  mover.unselectAll('toselectbox[]');
															}
															if(window.hidebccselect != 1)
															{
															   mover.unselectAll('bccselectbox[]');
															}
														}
														else
														{
															window.fetch_udata=window.clicked_destination;
														}
														if(this.form.viewmore.checked)
														{
															if(window.fetch_udata)
															{
																window.go_triggers();
															}
														}
														else
														{
															
															hider('udata',1);
														}
														
														return true;
														
														">        
			</td>
			<td style="width:100%">
				<tr valign="middle" align="center">
				<td valign="top" style="width:100%;" colspan="3" rowspan="4"align="center" bgcolor="#cccccc">
				<!-- This is allways dinamically set! -->
				<textarea id="udata" name="userdata" wrap="off">
				</textarea>
				</td>
			</td>
		</tr>
	</table>
		<td rowspan="1" style="width:48" valign="top" align="center" bgcolor="#cccccc">					
		
		{hideto_open}
			<br>
			<br>
			<p>
				<input type="button" name="tobutton" value="{top_dest_button_value} :" onClick="
												javascript:
								//See? this is cool, for every button we hit
								//to move something to the destination boxes, we flag
								//that the data should be updated, but we dont actually call
								//go_triggers cause we dont need to yet.
								//We do that only when we really need to commuicate to the backend
								//like when we click done or remove (which respectively need
								//to save the destination boxes content in cache or get fresh
								//email data from them)
												mover.moveSelectedOptions('nameselect[]','toselectbox[]');window.savedestboxes=1; ">
			</p>
			<br>
			<br>
 		{hideto_close}
		{hidecc_open}
		
			<br>
			<br>

			<p>
				<input type="button" style="color:blue;font-weight:bold;" name="ccbutton" value="{middle_dest_button_value} :" onClick="javascript:mover.moveSelectedOptions('nameselect[]','ccselectbox[]');window.savedestboxes=1; ">
			</p>
			<br>
		{hidecc_close}
		{hidebcc_open}

			<br>
			<br>
			<br>

			<p>
				<input type="button" name="bccbutton" style="color:#ff6600;font-weight:bold;" value="{bottom_dest_button_value}:" onClick="javascript:mover.moveSelectedOptions('nameselect[]','bccselectbox[]');window.savedestboxes=1;">
			</p>
			<br>
		{hidebcc_close}
			<br>
			<hr>
			<p>
				<input type="button" name="nonebutton" value="{none_button_value}" onClick="javascript:
												window.hidetoselect={hidetoselectjs};
												window.hideccselect={hideccselectjs};
												window.hidebccselect={hidebccselectjs};
												if(window.hidetoselect==0)
												{
													mover.selectAll('toselectbox[]'); 
												}
												if(window.hideccselect==0)
												{
													mover.selectAll('ccselectbox[]'); 
												}
												if(window.hidebccselect==0)
												{
													mover.selectAll('bccselectbox[]');
												}
												window.remove_selections=1;
												window.savedestboxes=1;
												window.go_triggers();">
			</p>
			<p>
				<br>
			</p>
		</td>
		<td rowspan="1" style="width:30%" align="center" valign="top" colspan="1" bgcolor="#cccccc">
		
		{hideto_open}
		<p>             
			<select name="toselectbox[]" size="6" multiple="multiple" onChange="javascript:
											var selidx;
											selidx=mover.getSelectedIdx('toselectbox[]');
											if(mover.numberSelectedOptions('toselectbox[]') == 1)
												{
														selidx=mover.getSelectedIdx('toselectbox[]');
													if(this.form.viewmore.checked==1)
													{
														selidx=mover.getSelectedIdx('toselectbox[]');
														window.fetch_udata='&nameselect='+this.form.elements['toselectbox[]'][selidx].value;
												   	 if(window.hideccselect != 1)
													 {
												 		 mover.unselectAll('ccselectbox[]');
													 }
								//					 mover.unselectAll('toselectbox[]');
													 if(window.hidebccselect!=1)
													 {
													 	 mover.unselectAll('bccselectbox[]');
													 }
													 mover.unselectAll('nameselect[]');
													window.go_triggers();
													}
													window.clicked_destination='&nameselect='+this.form.elements['toselectbox[]'][selidx].value;
												}
										"
										
										>
			{V_toselectbox}
			</select>
		</p>
		{hideto_close}

		{hidecc_open}
		<p>             
			<select style="color:blue;" name="ccselectbox[]" size="6" multiple="multiple" onChange="javascript:
											var selidx;
											selidx=mover.getSelectedIdx('ccselectbox[]');
											if(mover.numberSelectedOptions('ccselectbox[]') == 1)
												{
														selidx=mover.getSelectedIdx('ccselectbox[]');
													if(this.form.viewmore.checked==1)
													{
														window.fetch_udata='&nameselect='+this.form.elements['ccselectbox[]'][selidx].value;
								//						mover.unselectAll('ccselectbox[]');
														if(window.hidetoselect!=1)
														{
															mover.unselectAll('toselectbox[]');
														}
														if(window.hidebccselect!=1)
														{
													
															mover.unselectAll('bccselectbox[]');
														}
													 mover.unselectAll('nameselect[]');
													window.go_triggers();
													}
													window.clicked_destination='&nameselect='+this.form.elements['ccselectbox[]'][selidx].value;
													
												}"
			
										>
			{V_ccselectbox}
			</select>
			<br>
		</p>

		{hidecc_close}
		{hidebcc_open}
		<p style="margin-bottom: 0cm;">             
			<select name="bccselectbox[]" style="color:#ff6600;" size="6" multiple="multiple" onChange="javascript:
      									if(this.form.viewmore.checked)
      										{
											var selidx;
											selidx=mover.getSelectedIdx('bccselectbox[]');
											if(mover.numberSelectedOptions('bccselectbox[]') == 1)
											{
												selidx=mover.getSelectedIdx('bccselectbox[]');
												if(this.form.viewmore.checked==1)
													{
														window.fetch_udata='&nameselect='+this.form.elements['bccselectbox[]'][selidx].value;
														if(window.hideccselect!=1)
														{
															mover.unselectAll('ccselectbox[]');
														}
														if(window.hidetoselect!=1)
														{
															mover.unselectAll('toselectbox[]');
														}
								//					mover.unselectAll('bccselectbox[]');
													 mover.unselectAll('nameselect[]');
													window.go_triggers();
													
													}
													window.clicked_destination='&nameselect='+this.form.elements['bccselectbox[]'][selidx].value;
													
												}
										}"
			
											>
			{V_bccselectbox}
			</select>
		</p>
		&nbsp;              
		{hidebcc_close}
		<p>
		<table cellspacing="3">
		<tr>
		<td valign="left">
		<input type="button" name="removeselectedbutton" value="{remove_button_value}" onClick="javascript:
													window.remove_selections=1;
													window.savedestboxes=1;
													window.go_triggers();">
		</td>
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
		<td valign="right">
		<input type="button" name="cancel" value="{cancel_button_value}" onClick="javascript:
										window.closewhenfinished=1;
										window.forget=1;
										window.go_triggers();">
		</td>
		</tr>
		<tr>
		<td>
		</td>

		<td colspan="1" valign="left">
		<p>
		<br>
		<input type="button" name="done" value="{done_button_value}" onClick="javascript:
							///last documentation on event flags
							//savedestboxes = 1 ... gotriggers will go and save the destboxes data
							//backtobox =1 event savedestboxes will update the destination boxes
							//		when the savedestboxes event triggers that fromt he
							//		secondary frame
							//forget_after =1 The GET string for the save destboxes event
							//		will be poisoned with a forget_after GET var
							//		the framework will understand and delete everything from
							//		its cache upon serving the last save_destboxes
							//fetch_udata = false Ensures that the go_triggers will not
							//		call upon the secondary frame to fetch user data 
									window.savedestboxes=1;
									window.backtobox=1;
									window.closewhenfinished=1;
									window.forget_after=0;
									window.fetch_udata=false;
									window.go_triggers();
									 ">
		</p>
		</td>
		</tr>
		</table>
		
		<br>
		<br>
	</td>
	</tr>
	</thead>     
</form>
</table>
<br>
</body>
</html>

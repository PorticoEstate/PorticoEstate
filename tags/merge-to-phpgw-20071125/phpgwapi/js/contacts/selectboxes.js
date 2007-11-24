/**************************************************************************\
 * phpGroupWare - Todo list                                                 *
 * http://www.phpgroupware.org                                              *
 * Written by Alex Borges <alex@sogrp.com>                          *
 * Low Level Design also by    Dave Hall [dave.hall@mbox.com.au]                          *
 * UI Design and market research by Gerardo Ramirez [gramirez@grupogonher.com]
 * Code ripped off deveral public domain and gpl sites. Credits in each function
 * for those
 *-----------------------------------------------                          *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
 \**************************************************************************/


self.onerror=function () { return true; } 


//@class box_parser 
//@abstract Class that represents a csv list of strings. Each string has to hold an email address
//@discussion This object is all mine. Its an interface to parse csv lists of "name" <email> style
//entries such as those found in the to,cc,bcc boxes of a webmail client.
//Example: ["Name Of Destiny Mail"][<]user@host[>]
//@param str The constructor takes as input the string of csv's addresses and immediatly
//parses it into an array csvlist, one entry per value in the string
function box_parser(str)
{
	//This is a class attribute that constitutes an array of strings
	var csvlist;
	var i;
	//This is just an attribute that gets used by other functions
	var tempstr;
	//This is the input text, the one received as parameter by the constructor
	var thetext;
	var comma;
	//-------------------------------------------
	//This are class method declarations as public for each method
	
	this.csvparse=csvparse;
	this.get_index_of=get_index_of;
	this.remove_entry=remove_entry;
	this.parse_out=parse_out;
	this.add_entry=add_entry;
	//---------------------------------------------
	//---------------------------------------------
	//Attribute initializations
	//The input string gets parsed by the csvparse method
	//and the attribute csvlist is initialized with the
	//array said fucntion generates
	this.csvlist=this.csvparse(str);
	
	
	
}
//@function parse_out
//@abstract Generates a string from the internal array of strings
//@discussion This function will take the csvlist array and parse it back into csv....its quite simple
//actually it just calls the toString method on the array
function parse_out()
{
	return this.csvlist.toString();
}

//@function remove_entry
//@abstract Removes an element from the csvlist array
//@param str The string or substring that will matcha against the removed element
//@discussion This function takes a  string as a parameter. 
//It will be searched as per the get_index_of method throughout the whole 
//csvlist array. When it finds the string that matches with its input, 
//it will remove it from the array.
//POSTCONDITION: If no such string is found, this function will return -2
function remove_entry(str)
{
	var index;
	index=this.get_index_of(str);
	if(index!=-2)
	{
		this.csvlist.splice(index,1);
	}
}

//@function add_entry
//@abstract This function takes the full thing (a string) as parameter
//and adds it to the end of the list. 
//@param completeout The string that u want added to this object's csvlist
//@discussion When u parse_out this object
//this string will be appended to the resulting string
function add_entry(completeout)
{
	//alert("adding"+completeout);
	this.csvlist[this.csvlist.length]=completeout;
}

//@function get_index_of 
//@abstract This  function gets you the index number for a particular string.
//@param str The string u want searched within each of the elements of the csvlist array
//@discussion Its a free regular expression search within each string of the array
//The first match is what it returns as a string
//POSTCONDITION: If no such string is found, this function will return -2
function get_index_of(str)
{
	var emailfind;
	var i;
	emailfind=new RegExp("(.*)("+str+")(.*)");
	for(var i=0;i<this.csvlist.length;i++)
	{
		if(emailfind.test(this.csvlist[i]))
		{
			//alert("str "+str+" found in index "+i+" record is "+this.csvlist[i]);
			return i;
		}
	}
	//Lets avoid the 0/false problem
	//alert("str "+str+" Not found");
	return -2;
}
//@function csvparse
//@abstract Parses a csv into an array
//@discussion This function splits the incoming string
//into an array, an element per csv value in it
function csvparse(str)
{
	return str.split(',');

}


//@class getObj 
//@abstract An object that can search within a document object
//any html element, div or layer
//@param name The name of the box type element we want to search for
//@discussion This simple functions get us whatever element we want from
//the current document .... its pretty damn cool
//since we can get any div regardless of browser type

function getObj(name)
{
	if (document.getElementById)
	{
		this.obj = document.getElementById(name);
		this.style = document.getElementById(name).style;
	}
	else if (document.all)
	{
		this.obj = document.all[name];
		this.style = document.all[name].style;
	}
	else if (document.layers)
	{
		this.obj = getObjNN4(document,name);
		this.style = this.obj;
	}
}
//@function getObjNN4 
//@abstract internal function that allows getObj to work with netscape4
function getObjNN4(obj,name)
{
	var x = obj.layers;
	var thereturn;
	for (var i=0;i<x.length;i++)
	{
		if (x[i].id == name)
			thereturn = x[i];
		else if (x[i].layers.length)
			var tmp = getObjNN4(x[i],name);
		if (tmp) thereturn = tmp;
	}
	return thereturn;
}

//@function hider
//@param oname The element name as per ID="name"
//@param hidunhid A flag. If its 0, it will unhide it, if its 1 it will hide it
//@discusion A free function that allows us to hide or unhide a box type element
//such as a div or a  textarea
function hider(oname,hidunhid)
{
	var DHTML = (document.getElementById || document.all || document.layers);
	if (!DHTML) return;
	var x = new getObj(oname);
	x.style.visibility = (hidunhid) ? 'hidden' : 'visible'
}



//This code blatantly ripped off from http://javascript.internet.com/
//@class SelObj
//@abstract Suposedly, this represents a select box....
//@discussion I dont understand this code throughly yet
//i only use it for autocompletion so, dont trust this object
//as an abstraction of the selectbox html element
function
SelObj (formname, selname, textname, str)
{
  this.formname = formname;
  this.selname = selname;
  this.textname = textname;
  this.select_str = str || '';
  this.selectArr = new Array ();
  this.initialize = initialize;
  this.bldInitial = bldInitial;
  this.bldUpdate = bldUpdate;
}

function
initialize ()
{
  if (this.select_str == '')
    {
      for (var
	   i = 0;
	   i < document.forms[this.formname][this.selname].options.length;
	   i++)
	{
	  this.selectArr[i] =
	    document.forms[this.formname][this.selname].options[i];
	  this.select_str += (i>0 ? ',':'') +
	    document.forms[this.formname][this.selname].options[i].value +
	    ":" +
	    document.forms[this.formname][this.selname].options[i].text;
	}
    }
  else
    {
      var tempArr = this.select_str.split (',');
      for (var i = 0; i < tempArr.length; i++)
	{
	  var prop = tempArr[i].split (':');
	  this.selectArr[i] = new Option (prop[1], prop[0]);
	}
    }
  return;
}

function
bldInitial ()
{
  this.initialize ();
  for (var i = 0; i < this.selectArr.length; i++)
    document.forms[this.formname][this.selname].options[i] =
      this.selectArr[i];
  document.forms[this.formname][this.selname].options.length =
    this.selectArr.length;
  return;
}

function
bldUpdate ()
{
  var str =
    document.forms[this.formname][this.textname].value.replace ('^\\s*', '');
  if (str == '')
    {
      this.bldInitial ();
      return;
    }
  this.initialize ();
  var j = 0;
  pattern1 = new RegExp ("^" + str, "i");
  for (var i = 0; i < this.selectArr.length; i++)
    if (pattern1.test (this.selectArr[i].text))
      document.forms[this.formname][this.selname].options[j++] =
	this.selectArr[i];
  document.forms[this.formname][this.selname].options.length = j;
  if (j == 1)
    {
      document.forms[this.formname][this.selname].options[0].selected = true;
//document.forms[this.formname][this.textname].value =
//      document.forms[this.formname][this.selname].options[0].text;
    }
}
//@function This function serves us to instantiate two objects
//@discussion Both SelObj and selectMover need the formname as
//parameter for them to function. This is where we instantiate them
//and pass them what they need for instantiation
//SelObj needs also the textbox to autocomplete for and the name of the selectbox
//where autocompletion is supposed to happen.
function
setUpVisual (form, selectbox, textbox)
{
  obj1 = new SelObj (form, selectbox, textbox);
  mover = new selectMover(form);
// menuform is the name of the form you use
// itemlist is the name of the select pulldown menu you use
// entry is the name of text box you use for typing in
  obj1.bldInitial ();
  return obj1;
}
//@class selectMover
//@abstract This object encapsulates many selectbox functions
//@param formname The name of the form where all the selectboxes are. 
//All selectboxes need to be in the same form for this to work!
//@discussion This object is very stupidly built. As i was needing function
//I just started pushing them here. Thus, this needs braking down into
//a bunch of objects. One interesting thing however is that
//it does provide many generic selectbox functions that easy handling of
//selctbox data by name. This is important because in php we almost allways
//need selectboxes to be named like selectbox[index] so that php gives us back arrays
//and, of course, form.selectbox[1] means another thing completely.
//So this provides many functions to encapsulate that.
//
function
selectMover(formname)
{
	//@param formname 
	//@abstract This is a class attribute, the name of the form all selectboxes are in
	//-------------------
	//Public functions declarations
	this.formname=formname;
	this.moveSelectedOptions=moveSelectedOptions;
	this.killLeftOver=killLeftOver;
	this.moveOption=moveOption;
	this.moveAll=moveAll;
	this.sortSelect=sortSelect;
	this.selectToParser=selectToParser;
	this.stringToTextbox=stringToTextbox;
	this.numberSelectedOptions=numberSelectedOptions;
	this.selectAll=selectAll;
	this.getSelectedIdx=getSelectedIdx;
	this.optionvalue=optionvalue;
	this.option2get=option2get;
	this.getSize=getSize;
	this.removeSelectedFromParser=removeSelectedFromParser;
	this.update_emails=update_emails;
	this.removeParsedFromSelect=removeParsedFromSelect;
	this.selectOptionOfValue=selectOptionOfValue;
	this.unselectAll=unselectAll;
	this.clearSelectbox=clearSelectbox;
	this.keys_from_selectbox=keys_from_selectbox;
	this.extract_real_key=extract_real_key;
	//--------- End decalrations
	//Important class attributes that get filled up with the update_emails method
	//The removeParsedFromSelect and removeFromParser depend on the update_emails 
	//function to be called before, so it knows exactly the available emails that
	//we have in the html document.... emails are a very large issue of this applicacion
	//We generaly have two places where we can get them. Look at the discussion on this functions
	//to know what i mean.
	this.email_values=Array();
	this.email_keys=Array();
}
//@function update_emails
//@abstract This function will update the email_keys and email_values class attributes
//@param emails a javascript array with email addresses
//@param keys a javascript array with addressbook id's
//@discussion Searches hidden fields from the document looking for type=hidden name=email[id]
//value=user@host tags, it also receives the  emails and keys arrays that may be set
//by the secondary frame given a set_destboxes event
//PRECONDIDTION: The email and keys should be tied by array key. Where email[i] has an id in 
//the addressbook backend given by keys[i]
function
update_emails(emails,keys)
{
	        var retstring;
		var pseudov;
		var selectbox;
		var sizeextra;
		var exlude;
		var reg_obj;
		var keyextractor;
		var emailidx;
		emailidx=0;
		//We need to extract the keys from the forms...this keys are inside the name
		//as emails[key] ....so, we need to extract it with a regexp
		keyextractor=RegExp("^emails.?([0-9]+).?$",'g');
		retstring="";
		if(keys)
		{
			sizeextra=keys.length;
		}
		else
		{
			sizeextra=0;
		}
		//We look for emails and keys in the form
		for(var j=0;j<document.forms[this.formname].elements.length;j++)
		{
			//This regex helps us find a field with name "emails"
			reg_obj=RegExp(".*emails.*",'g');
			if(reg_obj.test(document.forms[this.formname].elements[j].name))
			{
				//found an email[ named field, this is for us
				this.email_values[emailidx]=document.forms[this.formname].elements[j].value;	
				this.email_keys[emailidx]=document.forms[this.formname].elements[j].name.replace(keyextractor,'$1');
				//alert("email,key pair found in hidden email "+this.email_values[emailidx]+" key "+this.email_keys[emailidx]);
				emailidx++;
			}
			else
			{
				
				//alert("this does not qualify as hidden good info "+document.forms[this.formname].elements[j].name);
			}
		}
		//Now we see if we look for emails and keys in the javascript arrays
		if(sizeextra != 0)
		{
			for(var i=0;i<sizeextra;i++)
			{
				this.email_values[emailidx]=emails[i];
				this.email_keys[emailidx]=keys[i];
			//	alert("email,key pair found in updated email "+this.email_values[emailidx]+" key "+this.email_keys[emailidx]);
				emailidx++;
			}
		}
//		alert("Emails updated emails-> "+this.email_values);
//		alert("Keys updated keys-> "+this.email_keys);
		return this.email_values;	

}
//@function index_of_value
//@param ar Array to search in
//@param value Value to search for
//@discussion Free function to search any value in any array
function index_of_value(ar,value)
{
	for(var i=0;i<ar.length;i++)
	{
		//alert("Searching "+ar[i]+" != "+value);
		if(ar[i]==value)
		{
			//alert("Found key for id "+value);
			return i;
		}
	}
	return -2;
}
//@function removeParsedFromSelect
//@param select The selectbox's name from which we will remove anything not found in the parser
//@param parser A parser object in which we will search all options from the selectbox. Every option
//not found in the corresponding parser will be killed and buried
//@discussion Will remove the options from the selectbox not matching
//addresses found in the parser.... IM your daddy!!!
//This is used when the mainframe is loaded to sync the destination boxes with their parsers.
// The final efect is that, if you remove by hand from the compose destination textboxes, the
//the selectboxes will not include that element for the user
function
removeParsedFromSelect(select,parser)
{
	var fromlen;
	var tolen;
	var j;
	var debuggs;
	var selidx;
	var namelen;
	namelen=document.forms[this.formname]['nameselect[]'].options.length;
	fromlen=document.forms[this.formname][select].length;
	fromlimit=fromlen;
	for (var i = 0; i < fromlimit; i=i+1)
	{
		selidx=index_of_value(this.email_keys,document.forms[this.formname][select].options[i].value);
		if(selidx != -2)
		{
			//This option is not in the parser, killit!
			if(parser.get_index_of(this.email_values[selidx])==-2)
			{
				//By default, put this back into the nameselect
				document.forms[this.formname]['nameselect[]'].options[namelen]=new Option(document.forms[this.formname][select].options[i].text,
												document.forms[this.formname][select].options[i].value);
				document.forms[this.formname][select].options[i].text="";
				document.forms[this.formname][select].options[i].value="";
			}

		}

	}
	this.killLeftOver(document.forms[this.formname][select]);
	this.sortSelect(select);
	this.sortSelect('nameselect[]');

}
//@function removeSelectedFromParser
//@param selectfrom The selectbox whose selected options are to be removed from the parser
//@param parser The parser from which we will remove all matching addresses that have been selected
//@discussion Will remove the selected options on a given selectbox from
//the parser received. The update emails function shoulve been
//called first.
//This is used when the remove event is called for the destination boxes.
//When u hit remove, the next chain of events happen:
// 1.- The framework turns on the set destination boxes flag, and the remove selected flag
//     and calls the go_triggers function
// 2.- As a result, the go triggers function tells the frameset to go and update the destination
//     boxes values in the server through the secondary frame
// 3.- The server invariably returns an array of keys, emails and name of all the ppl selected in the destination boxes
//      and triggers the destination boxes set event
// 4.- As a result, the frameset updates its emails/keys/names arrays and calles the destination boxes set event on the 
//     mainframe.
// 5.- Since the mainframe has the remove selected flag turned on, the destination boxes set event will call this function
//     for each of the destination boxes right after calling the update_emails function.
// 6.- This function will then remove from the parser all emails matching the selections 
//     from the destination boxes as they are being removed
function
removeSelectedFromParser(selectfrom,parser)
{
	var fromlen;
	var tolen;
	var j;
	var debuggs;
	var selidx;
	fromlen=document.forms[this.formname][selectfrom].length;
	fromlimit=fromlen;
//	alert("Parser, pre removal"+parser.parse_out());
//	Look in the selectbox for selected options
	for (var i = 0; i < fromlimit; i=i+1)
	{
			if(document.forms[this.formname][selectfrom].options[i])
			{
					debuggs=debuggs+" \n"+document.forms[this.formname][selectfrom].options[i];
				//	alert("This is selected "+document.forms[this.formname][selectfrom].options[i].text+" "+
//						document.forms[this.formname][selectfrom].options[i].value);
				if(document.forms[this.formname][selectfrom].options[i].selected)
				{
					selidx=index_of_value(this.email_keys,document.forms[this.formname][selectfrom].options[i].value);
					if(selidx != -2)
					{
						parser.remove_entry(this.email_values[selidx]);
						
					}
				}
			
			}
	}
	//alert("Parser, post removal"+parser.parse_out());
	
	return;
}
//@function sortSelect A selectbox sorter
//@param selname The to-be-sorted selectbox's name
//@Discussion 
//Um... it sorts a selectbox by option.text
function sortSelect(selname) 
{
	var o = new Array();
	var nex;
	var obj;
	obj=document.forms[this.formname][selname];
	if(obj.length < 1)
	{
		return 0;
	}
	for (var i=0; i<obj.options.length; i++) 
	{
		o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected) ;
	}
	nex = o.sort( 
			function(a,b) 
			{ 
				if ((a.text+"") < (b.text+"")) 
				{ 
				return -1; 
				}
				if ((a.text+"") > (b.text+"")) 
				{ 
					return 1; 
				}
				return 0;
			} 
		  );

	for (var i=0; i<nex.length; i++) 
	{
		obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
	}
}
//@function moveSelectedOptions
//@param selectfrom The name of the selectbox from which we are moving options
//@param selectto The name of the selectbox to which we are moving options
//@Discussion
//Will copy the options from one select box to another 
function
moveSelectedOptions(selectfrom,selectto)
{
	var fromlen;
	var tolen;
	var j;
	var debuggs;
	var selidx;
	fromlen=document.forms[this.formname][selectfrom].length;
	tolen=document.forms[this.formname][selectto].length;
	j=tolen;
	fromlimit=fromlen;
	debuggs="Select From "+selectfrom+"  Select To"+selectto;
	
	for (var i = 0; i < fromlimit; i=i+1)
	{
			if(document.forms[this.formname][selectfrom].options[i])
			{
				if(document.forms[this.formname][selectfrom].options[i].selected)
				{
					debuggs=debuggs+" \n"+document.forms[this.formname][selectfrom].options[i];
					
					document.forms[this.formname][selectto].options[j]=new Option(document.forms[this.formname][selectfrom].options[i].text, document.forms[this.formname][selectfrom].options[i].value);
					j++;
					document.forms[this.formname][selectfrom].options[i].name="";
					document.forms[this.formname][selectfrom].options[i].value="";
			
				}
			}
	}
	//Important.... we need killleftover every time we attempt to move anything out of a selctbox...see bellow
	this.killLeftOver(document.forms[this.formname][selectfrom]);
	this.sortSelect(selectfrom);
	this.sortSelect(selectto);
	return;
}
//function moveOption
//And THIS IS MINE..... and thus, GPL
//Will copy the option pointed to by index 
//@param selectfrom is the name of the box from where we copy
//@param selectto is the name of the box to where we are copying the option
//@param fromoption depends on indexkind DEPRECATED
//@param  indexkind is "text", fromoption has the text of the option. DEPRECATED
//As this can be repeated, it will stop at the first
//if indexkind is "value", fromoption has the value of the option
//if indexkind is "index". fromoption has the directo position of our option
//@discussion Actually, the last two arguments where never implemented
//this moves the given fromoption, which is the array index to the options
//array int he source box, to the last of the selectto selectbox
function moveOption(selectfrom,selectto,fromoption,indexkind)
{
	var fromlen;
	var tolen;
	var j;
	var debuggs;
	var selidx;
	tolen=document.forms[this.formname][selectto].length;
	document.forms[this.formname][selectto].options[tolen]=new Option(document.forms[this.formname][selectfrom].options[fromoption].text, document.forms[this.formname][selectfrom].options[fromoption].value);
	j++;
	document.forms[this.formname][selectfrom].options[fromoption].name="";
	document.forms[this.formname][selectfrom].options[fromoption].value="";
}
	
//@function moveAll
//@param selectfrom Name of the selectbox we are moving from
//@param selectto Name of the selectbox we are moving to
//@discussion uses moveOption to move option by option all of the options in the
//from selectbox into the to selectbox
//POSTCONDITION: This function sorts and kills leftover values from both selectboxes

function moveAll(selectfrom,selectto)
{
	var fromlen;
	var tolen;
	var j;
	var debuggs;
	var selidx;
	fromlen=document.forms[this.formname][selectfrom].length;
	tolen=document.forms[this.formname][selectto].length;
	j=tolen;
	fromlimit=fromlen;
	debuggs="Select From "+selectfrom+"  Select To"+selectto;
	for (var i = 0; i < fromlimit; i++)
	{
			if(document.forms[this.formname][selectfrom].options[i])
			{
				this.moveOption(selectfrom,selectto,i,'index');
			}
	}
	this.killLeftOver(document.forms[this.formname][selectfrom]);		
	this.killLeftOver(document.forms[this.formname][selectto]);
	this.sortSelect(selectfrom);
	this.sortSelect(selectto);


}
//@function killLeftOver
//@param selectbox a selectbox OBJECT... NOT THE NAME
//@discussion This interesting function removes all options
//which have a value equal to "" from the selectbox
//This is important because until we actually call this function
//All move operations will only set the value of the text of the moved options
//to "".
//The problem is that javascript is pretty week in this respect, so this is a
//recursive function that ensures that we only have valid text values and
//that the length of the given selectbox is correct....

function killLeftOver(selectbox)
{
	for(var i=0; i<selectbox.options.length; i++) 
	{
		if(selectbox.options[i].value == "")  
		{
			for(var j=i; j<selectbox.options.length-1; j++)  
			{
				selectbox.options[j].value = selectbox.options[j+1].value;
				selectbox.options[j].text = selectbox.options[j+1].text;
				selectbox.options[j].selected=null;
				
			}

			var ln = i;
			break;
		}
		
	}
	
		if(ln < selectbox.options.length)  
		{
			selectbox.options.length -= 1;
			killLeftOver(selectbox);
		}
}
//@function selectToParser Perhaps should be named another way
//@param sbox The selectbox that we are going to send into the parser
//@param keys Array of keys sent by the framework
//@param names corresponding names to the keys array
//@param emails corresponding emails to the keys array
//@param parser the destination parser
//@discussion This is a very speciffic function, we know destination boxes (passed as sbox)
//have values like 122321 which are the id numbers of the entries of the emails
//in the contacts backend (no matter if its sql or ldap)
//Furthermore, our phpcode passes back hidden form fields named email[122321]
//where the number corresponds to the said uid for the selectboxe's value
//This means that php is passing us back the corresponding emails in this hidden
//values.
//BUT, thats not all, there are times where you can have several ppl in the destination
//boxes but u dont have the email info here, in this cases, the js framework queries
//the php backend through the secondary frame and the uijsaddressbook.set_destboxes method.
//In javascript, this happens in the primary frame template whenever the trigger_save_destboxes
//function is called. This function calles the frameset's set_destboxes function which, in turn
//sends the secondary frame (through a location=uri call), the url containing the information
//of what the user has selected in the destination boxes.
//We convert the destination boxes to GET values to be used on the url that gets passed to the secondary
//frame using the option2get function.
//The php method gets the sent selectboxes and saves in cache whatever we are sending back to it though GET 
//AND gives back the emails,names and ids of this missing-in-cache/missing-in-hidden people
//It does this by outputing three arrays to the secondary frame plus the function destboxes_set that
//tells the frameset it has loaded this arrays for us. The frameset gets those arrays into its own 
//variables expresly declared for that and calls the event_destboxes_set function on the main frame
//This function selecetivly calls other functions depending on the state of the addressbook
//the case where this function, selectToString gets called is when the addressbook needs to write
//the destination boxes to a string where the actual comma separated list of name and email pairs
//should go. For example, in the mail app, this would be the To,cc and bcc textboxes.
//The original text of those destination strings is the exclude parameter, in which we search
//if the email we have in the destbox is already there.... that being the case, we exclude it
//from the output. The keys, names and emails parameters are the arrays the php framework passes
//back. They are here so we can contstruct the email addresses even if there are no corresponding
//hidden email[] field for a given selection in the destination boxes.
function selectToParser(sbox,keys,names,emails,parser)
{
	var retstring;
	var pseudov;
	var selectbox;
	var sizeextra;
	var exlude;
	var reg_obj;
	var i;
	var j;
	retstring="";
	if(keys)
	{
		sizeextra=keys.length;
	}
	else
	{
		sizeextra=0;
	}
//	alert("CAlle to selectroparser!");
	selectbox=document.forms[this.formname][sbox];
	if(selectbox.options.length<1)
	{
		return "";
	}
	for(var i=0;i<selectbox.options.length;i++)
	{
		pseudov="emails["+selectbox.options[i].value+"]";
		for(var j=0;j<document.forms[this.formname].elements.length;j++)
		{
			//find corresponding mail address
			if(document.forms[this.formname].elements[j].name==pseudov)
			{
//			alert("document length "+document.forms[this.formname].elements.length+" j = "+j+" slectbox ln "+selectbox.options.length+
		//		"selectbox value "+selectbox.options[i].value+" i= "+i);
				if(document.forms[this.formname].elements[j].value != "undefined")
				{
					if(document.forms[this.formname].elements[j].value == "")
					{
						alert("Contact "+selectbox.options[i].text+" \n has no email field in your addressbook record");
					}
					else
					{
						if(parser.get_index_of(document.forms[this.formname].elements[j].value) == -2)
						{
							parser.add_entry(appendmailstring("","",selectbox.options[i].text,document.forms[this.formname].elements[j].value));
						}
					}
				}
				if(sizeextra > 0)
				{
					for(var k=0;k<sizeextra;k++)
					{
						if(keys[k]==selectbox.options[i].value)
						{
						keys[k]=-33;
						}
					}
				}

			}
		}
	}
	if(sizeextra > 0)//we have extra info to build
	{
		for(var k=0;k<sizeextra;k++)
		{
			if(keys[k] != -33)
			{
				if(parser.get_index_of(emails[k]) == -2)
				{								
					parser.add_entry(appendmailstring("","",names[k],emails[k]));
				}
				
			}
		}
	}
	return parser;
}
//@function appendmailstring
//@param st the appended string
//@param comma just the separator between the st, and what we are building
//@param name should be the name as in "name" <email>
//@param email should be the email as in "name" <email>
//@discussion Auxiliary to the previous function. This one builds propper email addreses
//as per "name" <email>
function appendmailstring(st,comma,name,email)
{
	return st+comma+'"'+name+'" <'+email+'>';
	
}
//@function stringToTextbox
//@param str The string we are setting
//@param tbox An input of type textbox object (real form element)
//@discussion We just kill leftover commas and set the value of the textbox to the input string
function stringToTextbox(str,tbox)
{
	var commakill;
	commakill=/^,(.*)$/;
	tbox.value=str.replace(commakill,"$1");
}
//@function numberSelectedOptions 
//@param selbox The name of the selectbox
//@discussion We use this to count the number of selected options in the given select box
function numberSelectedOptions(selbox)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	j=0;
	for(var i=0;i<len;i++)
	{
		if(sbox.options[i].selected)
		{
			j++;
		}
	}
	return j;
}
//@function option2get
//@param selbox The name of the selectbox
//@discussion
//We use this function to turn all options of a selectbox into GET parameters
//for the php server backend. For example, the toselectbox[] select box's options
//would reach the server as a toselectbox[] array in HTTP_POST_VAR
//We turn that into HTTP_GET_VAR variables by building a GET variable style
//array for the selectbox. 
//ATTENTION: This does NOT discriminate between selected options or not, 
//it takes  all of the options.... 
function option2get(selbox)
{
	var len;
	var sbox;
	var j;
	var retstr;
	retstr='';
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	for(var i=0;i<len;i++)
	{
		retstr=retstr+"&";
		retstr=retstr+selbox+"["+sbox.options[i].value+"]="+sbox.options[i].text;
	}
	return retstr;
}
//@function extract_real_key

function extract_real_key(name,value)
{
/*	var keyextractor;
	keyextractor=RegExp("^"+name+".?([0-9]+).?$",'g');
	ret=value.replace(keyextractor,'$1');
	alert("Extracting real key name is "+name+" value is "+value+ " result was "+ret);
	return ret;
*/
	return value;
}
//@function keys_from_selectbox 
//@param selbox The name of the selectbox
//@discussion
//ATTENTION: This does NOT discriminate between selected options or not, 
//it takes  all of the options.... 
function keys_from_selectbox(selbox)
{
	var len;
	var sbox;
	var j;
	var retarray;
	var realkey;
	retarray=new Array();
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return retarray;
	}
	for(var i=0;i<len;i++)
	{
		
		retarray.push(this.extract_real_key(selbox,sbox.options[i].value));
	}
	//alert("\nFound Got Array for "+selbox+" is "+retarray);
	return retarray;
}


//@function selectAll
//@param selbox the selectboxe's name 
//@discussion It selects all options within a selectbox

function selectAll(selbox)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	for(var i=0;i<len;i++)
	{
			sbox.options[i].selected=1;
	}
}
//@function unselectAll
//@param selbox the selectbox's name
//@discussion This function unselects all values on a selectbox
function unselectAll(selbox)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	for(var i=0;i<len;i++)
	{
			sbox.options[i].selected=0;
	}
}
//@function selectOptionOfValue
//@param selbox the selectbox's name
//@param value the value we are looking for
function selectOptionOfValue(selbox,value)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	for(var i=0;i<len;i++)
	{
			if(sbox.options[i].value==value)
			{
				sbox.options[i].selected=1;
				return true;
			}
	}
	return false;
	
}
//@param selbox the selectbox's name
//@discussion This function unselects all values on a selectbox
function unselectAll(selbox)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	for(var i=0;i<len;i++)
	{
			sbox.options[i].selected=0;
	}
}
//@function clearSelectbox
//@param selbox the selectbox's name
//@discussion Sets all values of a selectbox to ""
//POSTCONDITION: U MUST call killLeftOver on the selectbox after calling this
//if you want NO option elements in the selectbox 
function clearSelectbox(selbox)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	len=sbox.options.length;
	if(len<1)
	{
		return "";
	}
	for(var i=0;i<len;i++)
	{
		sbox.options[i].value="";
	}

}
//@function getSize
//@param sbox The name of the selectbox
//@discussion Returns the number of options in the selectbox
function getSize(sbox)
{
	return document.forms[this.formname][sbox].options.length;
}
//@function getSelectedIdx
//@param selbox The name of the selectbox
//@discussion Returns the selected index as normal js selectedIndex call
//We only use this because we use funny names for our selectboxes
function getSelectedIdx(selbox)
{
	var len;
	var sbox;
	var j;
	sbox=document.forms[this.formname][selbox];
	return sbox.selectedIndex;
}
//@function optionvalue 
//@param selectbox The name of the selectbox
//@param selectedidx The index of the selected option
//@discussion Returns the value of the option pointed to by selectedidx
function optionvalue(selectbox,selectedidx)
{
	var sbox;
	sbox=document.forms[this.formname][selectbox];
	return sbox.options[selectedidx].value;
}

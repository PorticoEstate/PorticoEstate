var d;
var vendor_id = 0;

this.local_DrawCallback4 = function()
{
	//console.log(oTable4);
			var api = oTable4.api();
			// Remove the formatting to get integer data for summation
			var intVal = function ( i )
			{
				return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
						i : 0;
			};

			var columns = ["1"];

			columns.forEach(function(col)
			{
				data = api.column( col, { page: 'current'} ).data();
				pageTotal = data.length ?
					data.reduce(function (a, b){
							return intVal(a) + intVal(b);
					}) : 0;

				$(api.column(col).footer()).html("<div align=\"right\">"+pageTotal+"</div>");
			});

};
/********************************************************************************/	
var FormatterCenter = function(key, oData)
{

	return "<center>"+oData[key]+"</center>";
};

var FormatterAmount2 = function(key, oData)
{
	return "<div align=\"right\">"+oData[key]+"</div>";
};

 /********************************************************************************/

	this.confirm_session = function(action)
	{
		if(action == 'save' || action == 'apply')
		{
			conf = {
					modules : 'location, date, security, file',
					validateOnBlur : false,
					scrollToTopOnError : true,
					errorMessagePosition : 'top',
					language : validateLanguage
				};
			var test =  $('form').validateForm(validateLanguage, conf);
			if(!test)
			{
				return;
			}
		}

		var oArgs = {menuaction:'property.bocommon.confirm_session'};
		var strURL = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: strURL,
			success: function(data) {
				if( data != null)
				{
					if(data['sessionExpired'] == true)
					{
						window.alert('sessionExpired - please log in');
						JqueryPortico.lightboxlogin();//defined in common.js
					}
					else
					{
						document.getElementById(action).value = 1;
						try
						{
							validate_submit();
						}
						catch (e)
						{
							document.form.submit();
						}
					}
				}
			},
			failure: function(o)
			{
				window.alert('failure - try again - once');
			},
			timeout: 5000
		});
	};


	function SmsCountKeyUp(maxChar)
	{
		var msg  = document.getElementsByName("values[response_text]")[0];
	    var left = document.forms.form.charNumberLeftOutput;
	    var smsLenLeft = maxChar  - msg.value.length;
	    if (smsLenLeft >= 0) 
	    {
			left.value = smsLenLeft;
	    } 
	    else 
	    {
			var msgMaxLen = maxChar;
			left.value = 0;
			msg.value = msg.value.substring(0, msgMaxLen);
	    }
	}

	function SmsCountKeyDown(maxChar)
	{
		var msg  = document.getElementsByName("values[response_text]")[0];
	    var left = document.forms.form.charNumberLeftOutput;
	    var smsLenLeft = maxChar  - msg.value.length;
	    if (smsLenLeft >= 0) 
	    {
			left.value = smsLenLeft;
	    } 
	    else 
	    {
			var msgMaxLen = maxChar;
			left.value = 0; 
			msg.value = msg.value.substring(0, msgMaxLen);
	    }
	}



	this.fetch_vendor_email=function()
	{
		if(document.getElementById('vendor_id').value)
		{
			base_java_url['vendor_id'] = document.getElementById('vendor_id').value;
		}

		if(document.getElementById('vendor_id').value != vendor_id)
		{
			base_java_url['action'] = 'get_vendor';
			var oArgs = base_java_url;
			var strURL = phpGWLink('index.php', oArgs, true);
			JqueryPortico.updateinlineTableHelper(oTable3, strURL);
			vendor_id = document.getElementById('vendor_id').value;
		}
	};


	this.onDOMAttrModified = function(e)
	{
		var attr = e.attrName || e.propertyName;
		var target = e.target || e.srcElement;
		if (attr.toLowerCase() === 'vendor_id')
		{
			fetch_vendor_email();
		}
	};

	this.fileuploader = function()
	{
		var sUrl = phpGWLink('index.php', fileuploader_action);
		TINY.box.show({iframe:sUrl, boxid:"frameless",width:750,height:450,fixed:false,maskid:"darkmask",maskopacity:40, mask:true, animate:true, close: true}); //refresh_files is called after upload
	};

	this.refresh_files = function()
	{
		base_java_url['action'] = 'get_files';
		var oArgs = base_java_url;
		var strURL = phpGWLink('index.php', oArgs, true);
		JqueryPortico.updateinlineTableHelper(oTable2, strURL);
	};

	window.addEventListener("load", function()
	{
		d = document.getElementById('vendor_id');
		if(d)
		{

			if (d.attachEvent)
			{
				d.attachEvent('onpropertychange', onDOMAttrModified, false);
			}
			else
			{
				d.addEventListener('DOMAttrModified', onDOMAttrModified, false);
			}
		}
	});
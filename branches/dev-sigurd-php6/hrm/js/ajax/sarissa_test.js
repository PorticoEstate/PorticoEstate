//this assumes you have used the JS class to include sarissa and json


/**
* @var object oParams the normal arguments you would use for phpgw::link
*/


/**
* @var object the XMLHttpRequest object for request (may use sarissa)
*/

var xmlhttp = new XMLHttpRequest();

function Airport() 
{
	var oParams = {menuaction: 'hrm.sarissa_test.airport', id: 1};
	xmlhttp.open('GET', phpGWLink('/index.php', oParams, true), true);
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4) 
		{
			if (xmlhttp.status!=404)
			{
				var local=new Function("return "+xmlhttp.responseText)();
				alert("Code - Name\n"+local[0].id+' - '+local[0].name);
			}
			else
			{
				alert("Airport not found");
			}
		}
	}
	xmlhttp.send(null);
}

function HelloWorld()
{
	var oParams = {menuaction: 'hrm.sarissa_test.HelloWorld'};
	xmlhttp.open('GET', phpGWLink('/index.php', oParams, true), true);
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4)
		{
			if (xmlhttp.status!=404)
			{
				var local=new Function("return "+xmlhttp.responseText)();
				alert(local);
			}
			else
			{
				alert("Error");
			}
		}
	}
	xmlhttp.send(null);
}


function HelloWorldParams(Firstname,Lastname)
{
	var oParams = {menuaction: 'hrm.sarissa_test.HelloWorldParams', firstname: Firstname, lastname: Lastname};
	xmlhttp.open('GET', phpGWLink('/index.php', oParams, true), true);
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4) 
  		{
			if (xmlhttp.status!=404)
			{
			    	var local=new Function("return "+xmlhttp.responseText)();
				alert(local);
			}
			else
			{
				alert("Error");
			}
		}
	}
	xmlhttp.send(null);
}

function HelloWorldArray(name)
{
	name = escape(name.toJSONString());
	var oParams = {menuaction: 'hrm.sarissa_test.HelloWorldArray', name: name};
	xmlhttp.open('GET', phpGWLink('/index.php', oParams, true), true);
	xmlhttp.onreadystatechange=function() 
	{
		if (xmlhttp.readyState==4) 
		{
			if (xmlhttp.status!=404)
			{
				var local=new Function("return "+xmlhttp.responseText)();
				alert(local[0]);
				alert(local[1]);
				alert(local[2]);
			}
			else
			{
				alert("Error");
			}
		}
 	}
	xmlhttp.send(null);
}


var strCurPath = '';

function getFile(strFilename)
{
	window.location = phpGWLink('/index.php' , {menuaction: 'filemanager.bofilemanager.f_download', path : encodeURI(strCurPath), file: strFilename});
}

function getFolder(strPath)
{
	if ( strCurPath == strPath )
	{
		return;//no need to do anything
	}

	strCurPath = strPath;
	var xhr = new XMLHttpRequest();
	xhr.open('GET', phpGWLink('/index.php' , {menuaction: 'filemanager.bofilemanager.load_files', path : encodeURI(strPath), sortby: 'name'}, true) );
	xhr.onreadystatechange = function()
	{
		if ( xhr.readyState == 4 )
		{
			var elmParent = document.getElementById('sitemgr_site_nnv_file_list');
			while ( elmParent.childNodes.length )
			{
				elmParent.removeChild(elmParent.firstChild); //
			}

			if (xhr.status == 200)
			{
				var elmIMG, elmA, elmDIV;
				var elmTarget = document.createElement('ul');

				var iFound = 0;

				var arFiles = eval(xhr.responseText);
				var iCount = arFiles.length;
				for ( var i = 0; i < iCount; ++i )
				{
					if (  arFiles[i]['mime_type'] == 'Directory' )
					{
						continue;
					}
					++iFound;
					elmIMG = document.createElement('img');
					elmIMG.src = '{mime_img_dir}' + arFiles[i]['mime_type'].replace('\/', '-') + '.png';
					elmIMG.alt = arFiles[i]['name'];

					elmA = document.createElement('a');
					elmA.href = 'javascript:getFile("' + arFiles[i]['name'] + '")';
					elmA.appendChild(elmIMG);
					elmA.appendChild(document.createElement('br'));
					elmA.appendChild(document.createTextNode(arFiles[i]['name']));

					elmLI = document.createElement('li');
					elmLI.className = 'file';
					elmLI.appendChild(elmA);

					elmTarget.appendChild(elmLI);
				}

				if ( iFound )
				{
					elmParent.appendChild(elmTarget); //
				}
				else
				{
					elmParent.appendChild(document.createTextNode('Directory Empty'));
				}
			}
			else if ( xhr.status == 403 )
			{
				alert('ERROR: access denied!');
			}
			else
			{
				alert('ERROR: unknown!');
			}
		}
	}
	xhr.send(null);
}

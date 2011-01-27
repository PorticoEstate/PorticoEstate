function include_yui3(filename) {
	var head = document.getElementsByTagName('head')[0];
	script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	head.appendChild(script)
}
// Load YUI3
//include_yui3('http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js');

// Because of the way portico does loading, this function must be called specifically
function doDelegateDeleteModel(){
	function deleteModelCall(e) {
		var path_update = new Array();
		path_update["menuaction"] = "property.uiitem.removeModelJson";
		var postUrl = phpGWLink('index.php',path_update);
		console.log(this);
		var modelDatabaseId = this.getAttribute("value"); // getAttribute("value");
		
		var inputAlert = confirm("Are you sure you want to delete this model ?");
		if(inputAlert) {
			deleteModel(postUrl, modelDatabaseId);
		}
		
		//var Y = new YUI({ debug : true });
	};
	YUI().use("event-delegate", function(Y){
		Y.delegate("click", deleteModelCall, "#bimModelList2", "button.del");
	});
};

function doDelegateLoadModel(){
	function loadModelCall(e) {
		var path_update = new Array();
		path_update["menuaction"] = "property.uiitem.getFacilityManagementXmlByModelId";
		var postUrl = phpGWLink('index.php',path_update);
		console.log(this);
		var modelDatabaseId = this.getAttribute("value");  //getAttribute("value");
		
		var inputAlert = confirm("Are you sure you want to load this model(id:"+modelDatabaseId+") ?");
		if(inputAlert) {
			loadModel(postUrl, modelDatabaseId);
		}
		
		//var Y = new YUI({ debug : true });
	};
	YUI().use("event-delegate", function(Y){
		Y.delegate("click", loadModelCall, "#bimModelList2", "button.load");
	});
};

/*
function populateModelList() {
	YUI().use('io-base','node', function(Y) {
    	var modelTable = Y.one('#bimModelList2');
    	var rowCount = modelTable.get("rows").size();
		var currentRow = modelTable.invoke("insertRow",rowCount);
		console.log(currentRow);
	});
	
}
*/
function hideLoadingDiv() {
	YUI().use('node', function(Y) {
		 var loadingDiv = Y.one('#modelsLoader');
		 loadingDiv.hide();
	});
}


function showLoadingDiv() {
	YUI().use('node', function(Y) {
		 var loadingDiv = Y.one('#modelsLoader');
		 loadingDiv.show();
	});
}

function clearModelList() {
	YUI().use('node', function(Y) {
		var modelTable = Y.one('#bimModelList2');
		var rowCount = modelTable.get("rows").size();
		while ( rowCount > 1) {
			modelTable.invoke("deleteRow", rowCount -1);
			rowCount = modelTable.get("rows").size();
		}
	});
}
function getModelList() {
	YUI().use('io-base','node', 'json-parse', function(Y) {
		showLoadingDiv;
		var modelTable = Y.one('#bimModelList2');
		var rowCount = modelTable.get("rows").size();
		//object in its second argument:
		function successHandler(id, o){
			Y.log("Success handler called; handler will parse the retrieved XML and insert into DOM.", "info", "example");
			
			var root = o.responseText;
			try {
			    var data = Y.JSON.parse(root);
			} catch (e) {
			    alert("Invalid data");
			}
			
			for (var i = 0; i < data.length; i++) {
			    var bimModel = data[i];
				var currentRow = modelTable.invoke("insertRow",rowCount);
				var cell = currentRow.invoke("insertCell", 0);
				cell.appendChild(document.createTextNode(bimModel.databaseId));
				cell = currentRow.invoke("insertCell", 1);
				cell.appendChild(document.createTextNode(bimModel.name));
				cell = currentRow.invoke("insertCell", 2);
				cell.appendChild(document.createTextNode(bimModel.creationDate));
				cell = currentRow.invoke("insertCell", 3);
				cell.appendChild(document.createTextNode(bimModel.fileSize));
			   	cell = currentRow.invoke("insertCell", 4);
				cell.appendChild(document.createTextNode(bimModel.fileName));
				cell = currentRow.invoke("insertCell", 5);
				cell.appendChild(document.createTextNode(bimModel.usedItemCount));
				cell = currentRow.invoke("insertCell", 6);
				cell.appendChild(document.createTextNode(bimModel.vfsFileId));
				cell = currentRow.invoke("insertCell", 7);
				cell.appendChild(document.createTextNode(bimModel.used));
				cell = currentRow.invoke("insertCell", 8);
				cell.appendChild(createLoadButton(bimModel.databaseId));
				cell = currentRow.invoke("insertCell", 9);
				cell.appendChild(createDeleteButton(bimModel.databaseId));
				
				rowCount++;
			}
			hideLoadingDiv();
			Y.log("Success handler is complete.", "info", "example");
		}
		
		function createDeleteButton(modelId) {
			var buttonNode = document.createElement("button");
			buttonNode.setAttribute('value', modelId);
			buttonNode.setAttribute('class', "del");
			buttonNode.appendChild(document.createTextNode("Remove"));
			return buttonNode;
		}
		
		function createLoadButton(modelId) {
			var buttonNode = document.createElement("button");
			buttonNode.setAttribute('value', modelId);
			buttonNode.setAttribute('class', "load");
			buttonNode.appendChild(document.createTextNode("Load"));
			return buttonNode;
		}
 
		//Provide a function that can help debug failed
		//requests:
		function failureHandler(id, o){
			Y.log("Failure handler called; http status: " + o.status, "info", "example");
			var currentRow = modelTable.invoke("insertRow",rowCount);
			var cell = currentRow.invoke("insertCell", 0);
			cell.appendChild(document.createTextNode( o.status + " " + o.statusText));
		}
 
		function getModule(){
			var entryPoint = '/share/html/dev-bim2/index.php?menuaction=property.uiitem.getModelsJson';
 			var sUrl = entryPoint;
 			Y.log("Submitting request; ","info", "example");
 			var request = Y.io(sUrl, {
				method:"POST",
				on:
					{
						success:successHandler,
						failure:failureHandler
					}
				}
			);
		}
 
		//Use the Event Utility to wire the Get RSS button
		//to the getModule function:
		Y.on("load", getModule);

	});
}

function reloadModelList() {
	showLoadingDiv();
	clearModelList();
	getModelList();
	hideLoadingDiv();
}

function deleteModel(targetUrl, modelId) {
	YUI().use('io-base','node', 'json-parse', function(Y) {
		
		function successHandler(id, o){
			Y.log("Success handler called; handler will parse the retrieved XML and insert into DOM.", "info", "example");
			
			var root = o.responseText;
			try {
			    var data = Y.JSON.parse(root);
				Y.log(data);
				if(data.result == 1) {
					Y.log("Delete was successful", "info");
				} else {
					Y.log("Error deleting!");
					Y.log(data);
					var string = "An error occurred! \n error:"+data.error;
					alert(string);
				}
			} catch (e) {
			    alert("Invalid data");
			}
			reloadModelList();
		}
	
		function failureHandler(id, o){
			Y.log("Failure handler called; http status: " + o.status, "info", "example");
			alert(o.status + " " + o.statusText);
		}
 
		function removeModel(){
			var entryPoint = targetUrl;
 			var sUrl = entryPoint;
 			Y.log("Submitting request; ","info", "example");
 			var request = Y.io(sUrl, {
				method:"POST",
				data: "modelId="+modelId,
				on:
					{
						success:successHandler,
						failure:failureHandler
					}
				}
			);
		}
		removeModel();
	});
	
}

function loadModel(targetUrl, modelId) {
	YUI().use('io-base','node', 'json-parse', function(Y) {
		
		function successHandler(id, o){
			Y.log("Success handler called; handler will parse the retrieved XML and insert into DOM.", "info", "example");
			
			var root = o.responseText;
			try {
			    var data = Y.JSON.parse(root);
			} catch (e) {
			    alert("Invalid data");
			}
			Y.log(data);
			Y.log("Success handler is complete.", "info", "example");
		}
	
		function failureHandler(id, o){
			Y.log("Failure handler called; http status: " + o.status, "info", "example");
			alert(o.status + " " + o.statusText);
		}
 
		function loadModel(){
			var entryPoint = targetUrl;
 			var sUrl = entryPoint;
 			Y.log("Submitting request; ","info", "example");
 			var request = Y.io(sUrl, {
				method:"POST",
				data: "modelId="+modelId,
				on:
					{
						success:successHandler,
						failure:failureHandler
					}
				}
			);
		}
		loadModel();
	});
	
}

function addModel(targetUrl, modelId) {
	YUI().use('io-base','node', 'json-parse', function(Y) {
		
		function successHandler(id, o){
			Y.log("Success handler called; handler will parse the retrieved XML and insert into DOM.", "info", "example");
			
			var root = o.responseText;
			try {
			    var data = Y.JSON.parse(root);
			} catch (e) {
			    alert("Invalid data");
			}
			Y.log(data);
			Y.log("Success handler is complete.", "info", "example");
		}
	
		function failureHandler(id, o){
			Y.log("Failure handler called; http status: " + o.status, "info", "example");
			alert(o.status + " " + o.statusText);
		}
 
		function removeModel(){
			var entryPoint = targetUrl;
 			var sUrl = entryPoint;
 			Y.log("Submitting request; ","info", "example");
 			var request = Y.io(sUrl, {
				method:"POST",
				data: "modelId="+modelId,
				on:
					{
						success:successHandler,
						failure:failureHandler
					}
				}
			);
		}
		removeModel();
	});
	
}
/*
function addListeners() {
	
	var Dom = YAHOO.util.Dom;
console.log("running");
	var onDeleteModel = function (event, matchedEl, container) {
		console.log(event, matchedEl, container);
		alert('asdf');
		
var panelHtmlId = matchedEl.value;//parentNode.parentNode.id;// getCategoryId(matchedEl.parentNode.parentNode);
		
		if(panelHtmlId != null) {
			console.log(panelHtmlId);
		}

		
	};
	
	
	
	try
  {
 console.log(YAHOO.util.Event.delegate("container", "click", alert('smile'), "li"));
  }
catch(err)
  {
 alert(err);
  }
};

console.log("doing stuff");
YAHOO.util.Event.onDOMReady(addListeners);
//addListeners();

//YAHOO.util.Event.addListener("container44", "click", addListeners);

var testFunc = function() {
	console.log("My test function");
}
console.log(YAHOO.util.Event.delegate("container44", "click", testFunc, "li"));
*/
/*
(function() {

var Dom = YAHOO.util.Dom,
	Event = YAHOO.util.Event;

var onLIClick = function (event, matchedEl, container) {

	alert('y');

};

//	Use the "delegate" method to attach a "click" event listener to the 
//	container (<div id="container">).  The listener will only be called if the 
//	target of the click event matches the element specified via the CSS 
//	selector passed as the fourth argument to the delegate method.

console.log(Event.delegate("container44", "click", onLIClick, "li"));

})();
*/
YUI().use('node-base', function(Y) {
		Y.on("load",  getModelList);
	//	Y.on("load", doDelegateDeleteModel);

		}); 


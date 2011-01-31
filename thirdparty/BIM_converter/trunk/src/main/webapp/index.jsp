<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>BIM converter Rest services overview</title>
</head>
<body>
	<h1>BIM converter</h1>
	<h2>Introduction</h2>
	<p>This is an overview of the available rest services, and some examples</p>
	<h2>Upload Ifc</h2>
	<p>This web services accepts and IFC file via a POST request. 
	The IFC file is processed and the Facility managment XML is returned</p>
	<p>The location of the service relative to this location is:</p>
	<div><a href="./rest/uploadIfc">./rest/uploadIfc</a></div>
	<p>If the rest service link is opened via a web browser, it will return an informational message
	(which also means that invoking a browser's view source on the REST url will give the same message)</p>
	<h3>Example Form</h3>
	<p>The following Form allows upload of an IFC file to the REST service, if the IFC file is 
	valid, the REST service will return XML data with Facility Management information</p>
	<form enctype="multipart/form-data" action="./rest/uploadIfc" method="POST">
		<label for="file">Choose an IFC file to upload: </label>
		<input  id="file" name="file" type="file" /><br />
		<input type="submit" value="Upload File" />
	</form>asdf
</body>
</html>
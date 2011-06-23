// Correctly handle PNG transparency in Win IE 5.5 or higher.
// http://homepage.ntlworld.com/bobosola. Updated 02-March-2004
// written by Bob Osola ( bobosola@ntlworld.com )
// extended by Philipp Kamps ( pkamps@probusiness.de )

function correctPNG() 
{

	for(var i=0; i < document.images.length; i++)
	{
		var img = document.images[i]
		var imgName = img.src.toUpperCase()
		if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
    {
			var imgID = (img.id) ? "id='" + img.id + "' " : ""
			var imgClass = (img.className) ? "class='" + img.className + "' " : ""
			var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
			var imgStyle = "display:inline-block;" + img.style.cssText 
			if (img.align == "left") imgStyle = "float:left;" + imgStyle
			if (img.align == "right") imgStyle = "float:right;" + imgStyle
			if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle		
			var strNewHTML = "<span " + imgID + imgClass + imgTitle
			+ " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
			+ "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
			+ "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>" 
			img.outerHTML = strNewHTML
			i = i-1
		}
	}

	myinputs = document.getElementsByTagName('input')
	for (j = 0; j < myinputs.length; j++)
	{
		myinput = myinputs[j]
		if ( myinput.src != '' )
		{
			var myinputName  = (myinput.name) ? "name=\"" + myinput.name + "\" " : ""
			var myinputID    = (myinput.id) ? "id=\"" + myinput.id + "\" " : ""
			var myinputClass = (myinput.className) ? "class='" + myinput.className + "' " : ""
			var myinputTitle = (myinput.title) ? "title=\"" + myinput.title + "\" " : "title='" + myinput.alt + "' "
			var myinputStyle = "border: 0px solid #FFFFFF" //+ myinput.style.cssText 
			if (myinput.align == "left") myinputStyle = "float:left;" + myinputStyle
			if (myinput.align == "right") myinputStyle = "float:right;" + myinputStyle

			myinputOnClick = ''
			if (myinput.onclick)
			{
				str = myinput.onclick.toString();
				var pattern = /\s{\s(.*)\s}/;
				result = pattern.exec(str);
				myinputOnClick = "onClick=\"" + result[1] + "\""
				//alert('result = ' + result);
				//alert('result.length = ' + result.length);
				//alert('result[0] = ' + result[0]);
				//alert('result[1] = ' + result[1]);
				//alert('RegExp.$1 = ' + RegExp.$1);
			}

			var strNewHTML = "<input value=\"\" type=\"submit\" " + myinputName + myinputID + myinputClass + myinputTitle
			+ " style=\"" + "width:" + myinput.width + "px; height:" + myinput.height + "px;" + myinputStyle + ";"
			+ "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
			+ "(src=\'" + myinput.src + "\', sizingMethod='scale');\" " + myinputOnClick + " />" 
			myinput.outerHTML = strNewHTML
		}
	}
}
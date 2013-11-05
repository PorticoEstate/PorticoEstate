		</div>
<div id="footer">

</div>
<div id="footer_address">
Stavanger kommune | Olav Kyrres gate 19 | Postboks 8001 | 4068 Stavanger
</div>
<script type="text/javascript">
	function test() {
		var orgarray = eval('(' + '{organization_json}' + ')');
		var div = document.createElement('div');
		document.body.appendChild(div);
		div.className = 'changeorg';
		div.id = 'test';
		div.innerHTML = '<div style="float: right;" id="changeClose"><i class="fa fa-times-circle"></i></div>';
		div.innerHTML += '<div id="changeHeader">{change_org_header}</div>';
		div.innerHTML += '<div id="orglist"></div>';
		var oList = document.getElementById("orglist");
		for(var i=0,len=orgarray.length; i < len; i++) {
			oList.innerHTML += '<div style="padding-bottom: 5px;"><a href="change.php?orgnumber='+orgarray[i]['orgnumber']+'">'+orgarray[i]['orgname']+'</a></div>';
		}	
		div.style.backgroundColor= 'white';
		div.style.border = '1px solid black';
		div.style.height = 'auto';
		div.style.padding = '5px 10px 5px 10px';
		var oHeader = document.getElementById("changeHeader");
		oHeader.style.paddingBottom="5px";
		var oClose = document.getElementById("changeClose")
		oClose.style.cursor='pointer';
       	oClose.onclick = function(){
			div.parentNode.removeChild(div);
		}
	}
	


	if ('{organization_json}' != 'null' && '{organization_json}' != '') {
		var oElement = document.getElementById("change");
 		oElement.innerHTML = '<i class="fa fa-users"></i>';
		oElement.setAttribute('onclick', 'test();');
		oElement.style.color='black';
		oElement.style.padding='6px 0px 0px 5px';
		oElement.style.cursor='pointer';
		

	}
</script>
	</body>
</html>


<script type="text/javascript">

function check_all(form_name)
{
	for (i=0; i<document.forms[form_name].elements.length; i++) {
		if (document.forms[form_name].elements[i].type == "checkbox") {
			if (document.forms[form_name].elements[i].checked) {
				document.forms[form_name].elements[i].checked = false;
			} else {
				document.forms[form_name].elements[i].checked = true;
			}
		} 
	}
}

function do_action(form_name, act)
{
	flag = 0;
	for (i=0; i<document.forms[form_name].elements.length; i++) {
		//alert(document.forms[form_name].elements[i].type);
		if (document.forms[form_name].elements[i].type == "checkbox") {
			if (document.forms[form_name].elements[i].checked) {
				flag = 1;
			}
		}
	}
	if (flag != 0) {
		document.forms[form_name].what.value = act;
		document.forms[form_name].submit();
	} else {
		alert("Please select a message first");
		document.forms[form_name].tofolder.selectedIndex = 0;
	}
}

</script>


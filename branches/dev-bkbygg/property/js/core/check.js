
function check_all_radio(which)
{
	for (i = 0; i < document.form.elements.length; i++)
	{
		if (document.form.elements[i].type == "radio" && document.form.elements[i].value == which)
		{
			document.form.elements[i].checked = true;
		}
	}
}

function check_all_checkbox(which)
{
	for (i = 0; i < document.form.elements.length; i++)
	{
		if (document.form.elements[i].type == "checkbox" && document.form.elements[i].name.substring(0, which.length) == which)
		{
			if (document.form.elements[i].checked)
			{
				document.form.elements[i].checked = false;
			}
			else
			{
				document.form.elements[i].checked = true;
			}
		}
	}
}

function check_all_radio2(which)
{
	for (i = 0; i < document.form2.elements.length; i++)
	{
		if (document.form2.elements[i].type == "radio" && document.form2.elements[i].value == which)
		{
			document.form2.elements[i].checked = true;
		}
	}
}

function check_all_checkbox2(which)
{
	for (i = 0; i < document.form2.elements.length; i++)
	{
		if (document.form2.elements[i].type == "checkbox" && document.form2.elements[i].name.substring(0, which.length) == which)
		{
			if (document.form2.elements[i].checked)
			{
				document.form2.elements[i].checked = false;
			}
			else
			{
				document.form2.elements[i].checked = true;
			}
		}
	}
}

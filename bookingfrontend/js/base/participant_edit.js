function check(input)
{
	value = input.value;

	var phoneno = /^\d{8}$/;
	if (value.match(phoneno))
	{
		input.setCustomValidity('');
	}
	else
	{
		input.setCustomValidity('Must be at least 8 digits');
	}
}

function validate_submit(type)
{
	document.getElementById("register_type").value = type;
//	document.form.submit();
}


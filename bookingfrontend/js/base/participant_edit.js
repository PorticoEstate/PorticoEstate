function check(input)
{
	return;

	//handled by init_intl_tel_input
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


$(document).ready(function ()
{
	setTimeout(function ()
	{
		document.getElementById("phone").focus();
	}, 40);

	// need to perform the validation first
	$("#phone").keydown(function (e)
	{
		if (e.keyCode === 13)
		{
			e.preventDefault();
		}
	});
});



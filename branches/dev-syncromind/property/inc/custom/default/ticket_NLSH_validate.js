
$(document).ready(function()
{
	show_feilkoder();

	$("#global_category_id").change(function()
	{
		show_feilkoder();
	});

});

function show_feilkoder()
{
	if ($("#global_category_id").val() == 20)
	{
		document.getElementById('label_feilkoder').style.display = 'block';
		document.getElementById('id_feilkoder').style.display = 'block';
	}
	else
	{
		document.getElementById('label_feilkoder').style.display = 'none';
		document.getElementById('id_feilkoder').style.display = 'none';
	}

}
function validate_submit()
{
	var error = false;
	var feilkode_id = $("#id_feilkoder").val();
	var category_id = $("#global_category_id").val();
	var group_id = $("#global_category_id").val();
	var status_id = $("#status_id").val();

	if (category_id == 20)
	{
		if (!feilkode_id && status_id == 'X')
		{
			error = true;
		}
	}

	if (error)
	{
		alert('Feilkode må velges før meldingen kan avsluttes');
	}
	else
	{
		document.form.submit();
	}
}


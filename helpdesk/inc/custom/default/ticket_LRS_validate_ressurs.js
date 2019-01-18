
$(document).ready(function ()
{

	$("#id_ressursnr").attr("data-validation-error-msg", "Ressursnummer");
	show_ressursnr();

	$("#global_category_id").change(function ()
	{
		show_ressursnr();
	});

	$("#id_ressursnr").change(function ()
	{
		get_ressursname();
	});

	if(parent_cat_id == 301) //LRS-EDD telefoni
	{
		$("#arbeidssted_name").removeAttr("data-validation");
		$("#arbeidssted_name").hide();
		$("#label_arbeidssted").hide();
	}

});

function show_ressursnr()
{
	$("#label_ressursnr").hide();
	$("#id_ressursnr").hide();
	$("#label_ressursnr_navn").hide();
	$("#id_ressursnr_navn").hide();
	$("#id_ressursnr").removeAttr("data-validation");

	var category_id = $("#global_category_id").val();

	switch (category_id)
	{
		case '247': //Stans
			$("#id_ressursnr").removeAttr("data-validation-optional");
			$("#id_ressursnr").attr("data-validation", "required");
			$("#label_ressursnr").show();
			$("#id_ressursnr").show();
			$("#label_ressursnr_navn").show();
			$("#id_ressursnr_navn").show();
			break;
		case '306': //xAltinn
			$("#arbeidssted_name").removeAttr("data-validation");
			$("#arbeidssted_name").hide();
			$("#label_arbeidssted").hide();
			break;
		default:
			$("#arbeidssted_name").removeAttr("data-validation-optional");
			$("#arbeidssted_name").attr("data-validation", "required");
			$("#arbeidssted_name").show();
			$("#label_arbeidssted").show();
	}
}

function validate_submit()
{
	var error = false;
	var arbeidssted_id = $("#arbeidssted_id").val();
	var ressursnr_id = $("#id_ressursnr").val();
	var category_id = $("#global_category_id").val();

	switch (category_id)
	{
		case '247': //Stans
			if (!ressursnr_id)
			{
				error = true;
				alert('Ressursnr må angis');
			}
			break;
		default:
	}

	if(!arbeidssted_id)
	{
		if(category_id != 306) //xAltinn
		{
			$("#arbeidssted_name").removeClass('valid');
			$("#arbeidssted_name").addClass('error');
			$("#arbeidssted_name").attr("style", 'border-color: rgb(185, 74, 72);');
			$("#arbeidssted_name").focus();

			error = true;
			alert('arbeidssted må angis (velg fra liste)');
		}
	}

	if (!error)
	{
		document.form.submit();
	}
}

function get_ressursname()
{
	var ressursnr_id = $("#id_ressursnr").val();

	var oArgs = {menuaction: 'helpdesk.uitts.custom_ajax',method:'get_ressurs_name', acl_location: '.ticket', ressursnr_id: ressursnr_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			$("#id_ressursnr_navn").val('');

			if (data != null)
			{
				var ressurs_name = data.ressurs_name;
				$("#id_ressursnr_navn").val(ressurs_name);
			}
		}
	});
}
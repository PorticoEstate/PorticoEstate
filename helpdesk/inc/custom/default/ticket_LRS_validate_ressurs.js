
var conf_on_changed = {
	modules: 'date, file',
	validateOnBlur: true,
	scrollToTopOnError: false,
	errorMessagePosition: 'inline'
};


$(document).ready(function ()
{

	switch (parent_cat_id)
	{
		case 255:  //LRS Lønn
		case 256:  //LRS Refusjon
			$("#arbeidssted_name").attr("data-validation", "required");
			// Leave as is...
			break;
		default:
			$("#arbeidssted_name").removeAttr("data-validation");
			$("#arbeidssted_name").hide();
			$("#label_arbeidssted").hide();

	}

	$("#id_ressursnr").attr("data-validation-error-msg", "Ressursnummer");
	show_fields();

	$("#global_category_id").change(function ()
	{
		show_fields();
	});

	$("#id_ressursnr").change(function ()
	{
		get_ressursname();
	});

});

function show_fields()
{
	$("#label_ressursnr").hide();
	$("#id_ressursnr").hide();
	$("#label_ressursnr_navn").hide();
	$("#id_ressursnr_navn").hide();
	$("#id_ressursnr").removeAttr("data-validation");


	$("#label_kundenummer").hide();
	$("#id_kundenummer").hide();
	$("#label_bilagsnr").hide();
	$("#id_bilagsnr").hide();
	$("#label_aarsak").hide();
	$("#id_aarsak").hide();
	$("#label_betalingsoppfolging_type").hide();
	$("#id_betalingsoppfolging_type").hide();
	$("#id_kundenummer").removeAttr("data-validation");
	$("#id_bilagsnr").removeAttr("data-validation");
	$("#id_aarsak").removeAttr("data-validation");
	$("#id_betalingsoppfolging_type").removeAttr("data-validation");

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
		case '344': //UF betalingsoppfølgingReversering/Utligning reskontro
			$("#label_kundenummer").show();
			$("#id_kundenummer").show();
			$("#label_bilagsnr").show();
			$("#id_bilagsnr").show();
			$("#label_aarsak").show();
			$("#id_aarsak").show();
			$("#label_betalingsoppfolging_type").show();
			$("#id_betalingsoppfolging_type").show();
			$("#id_kundenummer").attr("data-validation", "length");
			$("#id_kundenummer").attr("data-validation-length", "10");
			$("#id_kundenummer").attr("data-validation-error-msg", "Eksakt 10 siffer");
			$("#id_bilagsnr").attr("data-validation", "length");
			$("#id_bilagsnr").attr("data-validation-length", "10");
			$("#id_bilagsnr").attr("data-validation-error-msg", "Eksakt 10 siffer");
			$("#id_aarsak").attr("data-validation", "length");
			$("#id_aarsak").attr("data-validation-length", "1-200");
			$("#id_betalingsoppfolging_type").attr("data-validation", "required");
//			$("#id_betalingsoppfolging_type_1").attr("data-validation", "checkbox_group");
//			$("#id_betalingsoppfolging_type_1").attr("data-validation-qty", "min1");
			break;
		case '345': //UF betalingsoppfølging/tilbakebetaling
			$("#label_kundenummer").show();
			$("#id_kundenummer").show();
			$("#label_bilagsnr").show();
			$("#id_bilagsnr").show();
			$("#id_kundenummer").attr("data-validation", "length");
			$("#id_kundenummer").attr("data-validation-length", "10");
			$("#id_kundenummer").attr("data-validation-error-msg", "Eksakt 10 siffer");
			$("#id_bilagsnr").attr("data-validation", "length");
			$("#id_bilagsnr").attr("data-validation-length", "10");
			$("#id_bilagsnr").attr("data-validation-error-msg", "Eksakt 10 siffer");
			break;
		default:
//			$("#arbeidssted_name").removeAttr("data-validation-optional");
//			$("#arbeidssted_name").attr("data-validation", "required");
//			$("#arbeidssted_name").show();
//			$("#label_arbeidssted").show();
	}

	$('form').isValid(validateLanguage, conf_on_changed, true);
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

	if (!arbeidssted_id)
	{
		if ($("#arbeidssted_name").attr("data-validation") == "required")  //LRS-EDD telefoni og LRS-Refusjon xAltinn
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

	var oArgs = {menuaction: 'helpdesk.uitts.custom_ajax', method: 'get_ressurs_name', acl_location: '.ticket', ressursnr_id: ressursnr_id};
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
var Window1;
$(document).ready(function ()
{

	// When janitor is selected, vouchers are fetched from db and voucer select list is populated
	$("#janitor_lid").change(function ()
	{
		update_voucher_filter();
	});

	$("#supervisor_lid").change(function ()
	{
		update_voucher_filter();
	});

	$("#budget_responsible_lid").change(function ()
	{
		update_voucher_filter();
	});

	$("#search").click(function (e)
	{
		update_voucher_filter();
	});

	$("#get_template").click(function (e)
	{
		var oArgs_template = {menuaction: 'property.uiinvoice2.get_split_template', voucher_id: $("#voucher_id").val()};
		var requestUrl_template = phpGWLink('index.php', oArgs_template);
		window.open(requestUrl_template);
	});

	$("#voucher_id_filter").change(function ()
	{

		$("#voucher_id").val('');
		$("#voucher_id_text").html('');
		$("#line_id").val('');
		$("#line_text").val('');
		$("#order_id").val('');
		$("#order_id_orig").val('');
		$("#external_project_id").val('');
		$("#invoice_id").html('');
		$("#kid_nr").html('');
		$("#vendor").html('');
		$("#close_order_orig").val('');
		$("#my_initials").val('');
		$("#sign_orig").val('');
		$("#invoice_date").html('');
		$("#payment_date").html('');
		$("#b_account_id").val('');
		$("#currency").html('');
		$("#oppsynsmannid").html('');
		$("#saksbehandlerid").html('');
		$("#budsjettansvarligid").html('');
//		$("#remark").html( '' );
		$("#process_log").val('');
		$("#dim_a").val('');
		$("#dim_b").html("<option>Velg</option>");
		$("#dim_e").html("<option>Velg</option>");
		$("#period").html("<option>Velg</option>");
		$("#periodization").html("<option>Velg</option>");
		$("#periodization_start").html("<option>Velg</option>");
		$("#process_code").html("<option>Velg</option>");
		$("#tax_code").html("<option>0</option>");
		$("#approve_as").html("<option>Velg</option>");
		$("#order_text").html('Bestilling');
		$("#invoice_id_text").html('FakturaNr');
		$("#close_order").html('');
		$("#close_order_orig").val('');
		$("#park_order").html('');
		$("#receipt").html('');
		$("#email_link").html('');

		var voucher_id = $(this).val();
		var oArgs = {menuaction: 'property.uiinvoice2.get_first_line'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var line_id = 0;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&voucher_id=" + voucher_id,
			success: function (data)
			{
				if (data != null)
				{
					line_id = data['line_id'];
					var oArgs_table = {menuaction: 'property.uiinvoice2.query', line_id: line_id, voucher_id_filter: voucher_id};
					var requestUrl_table = phpGWLink('index.php', oArgs_table, true);
					JqueryPortico.updateinlineTableHelper('datatable-container_1', requestUrl_table);
					update_form_values(line_id, 0);
				}
			}
		});
	});

	$(document).on("click", "#approve_line", function (e)
	{
		$("#receipt").html('');
		var line_id = $(this).val();
		var voucher_id_orig = $("#voucher_id").val();
		update_form_values(line_id, voucher_id_orig);
	});

	$("#dim_e").change(function ()
	{
		var oArgs = {menuaction: 'property.boworkorder.get_category', cat_id: $(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					if (data.active != 1)
					{
						alert('Denne kan ikke velges');
					}
				}
			}
		});
	});

	$("#voucher_form").on("submit", function (e)
	{
		e.preventDefault();
		var line_id = $("#line_id").val();
		var voucher_id_orig = $("#voucher_id").val();
		if (!line_id)
		{
			alert('Du må velge linje i bilag');
			return;
		}

		var approved_amount = $("#approved_amount_" + line_id).val();
		var process_log = $("#process_log").val();
		if (approved_amount == 0 && !process_log)
		{
			alert('Du må begrunne i fakturaloggen hvorfor godkjent beløp = 0 ');
			return;
		}

		var periodization = document.getElementById("periodization").value;
		var periodization_start = document.getElementById("periodization_start").value;
		var dim_e = document.getElementById("dim_e").value;
		var dim_b = document.getElementById("dim_b").value;
		var dim_a = document.getElementById("dim_a").value;
		var order_id = document.getElementById("order_id").value;
		var order_id_orig = document.getElementById("order_id_orig").value;

		if (!dim_b)
		{
			alert('Du må velge Ansvarssted');
			return;
		}

		if (order_id_orig == order_id)
		{
			if (periodization && !periodization_start)
			{
				alert('Du må velge startperiode');
				return;
			}

			if (!dim_e)
			{
				alert('Du må velge Kategori');
				return;
			}

			if (!dim_a)
			{
				alert('Du må angi Dim A');
				return;
			}
		}
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");

//		var oArgs = { menuaction:'property.uiinvoice2.update_voucher'};

//		var requestUrl = phpGWLink('index.php', oArgs, true);
		/*
		 var fileInput = document.getElementById('file');
		 var file = fileInput.files[0];
		 var formData = new FormData();
		 formData.append('file', file);
		 document.getElementsByName("file")[0].value = "";
		 */
		$.ajax({
			type: 'POST',
//			url: requestUrl + $(thisForm).serialize(),
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
//			data: formData,
//			processData: false,
//			contentType: false,
			success: function (data)
			{
				if (data)
				{
					if (data.sessionExpired)
					{
						alert('Sesjonen er utløpt - du må logge inn på nytt');
						return;
					}

					var obj = data;
					if (typeof (obj.line_id) != 'undefined')
					{
						if (obj.line_id > 0 && obj.line_id != line_id)
						{
							line_id = obj.line_id;
						}
					}

					var submitBnt = $(thisForm).find("input[type='submit']");
					if (obj.status == "updated")
					{
						$(submitBnt).val("Lagret");

						var oArgs_table = {menuaction: 'property.uiinvoice2.query', line_id: line_id, voucher_id_filter: $("#voucher_id").val()};
						var requestUrl_table = phpGWLink('index.php', oArgs_table, true);
						JqueryPortico.updateinlineTableHelper('datatable-container_1', requestUrl_table);

					}
					else
					{
						$(submitBnt).val("Feil ved lagring");
					}

					// Changes text on save button back to original
					window.setTimeout(function ()
					{
						$(submitBnt).val('Lagre Linje');
						$(submitBnt).addClass("not_active");
					}, 1000);

					var htmlString = "";
					if (typeof (data['receipt']['error']) != 'undefined')
					{
						for (var i = 0; i < data['receipt']['error'].length; ++i)
						{
							htmlString += "<div class=\"error\">";
							htmlString += data['receipt']['error'][i]['msg'];
							htmlString += '</div>';
						}

					}
					if (typeof (data['receipt']['message']) != 'undefined')
					{
						for (var i = 0; i < data['receipt']['message'].length; ++i)
						{
							htmlString += "<div class=\"msg_good\">";
							htmlString += data['receipt']['message'][i]['msg'];
							htmlString += '</div>';
						}

					}
					update_form_values(line_id, voucher_id_orig);
					update_voucher_filter();
					$("#receipt").html(htmlString);

					$("html, body").animate({
						scrollTop: 150
					}, 600);

				}
			}
		});
	});
});



function update_voucher_filter()
{

	var oArgs = {
		menuaction: 'property.uiinvoice2.get_vouchers',
		janitor_lid: $("#janitor_lid").val(),
		supervisor_lid: $("#supervisor_lid").val(),
		budget_responsible_lid: $("#budget_responsible_lid").val(),
		criteria: $("#criteria").val(),
		query: $("#query").val()
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);

	var htmlString = "";

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				if (data.sessionExpired)
				{
					alert('Sesjonen er utløpt - du må logge inn på nytt');
					return;
				}

				htmlString = "<option>" + data.length + " bilag funnet</option>"
				var obj = data;

				$.each(obj, function (i)
				{
					htmlString += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
				});

				$("#voucher_id_filter").html(htmlString);
			}
			else
			{
				htmlString += "<option>Ingen bilag</option>";
				$("#voucher_id_filter").html(htmlString);
			}
		}
	});
}


function update_form_values(line_id, voucher_id_orig)
{
	var oArgs = {menuaction: 'property.uiinvoice2.get_single_line'};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl + "&line_id=" + line_id,
		success: function (data)
		{
			if (data.sessionExpired)
			{
				alert('Sesjonen er utløpt - du må logge inn på nytt');
				return;
			}
			var voucher = data['voucher'];
			if (voucher != null && voucher.length > 0)
			{
				$("#line_id").val(line_id);

				var update_image = false;

				if (voucher_id_orig != voucher[0].voucher_id)
				{
					update_image = true;
				}
				$("#voucher_id").val(voucher[0].voucher_id);
				if (voucher[0].voucher_out_id)
				{
					var voucher_id_text = voucher[0].voucher_out_id;
				}
				else
				{
					var voucher_id_text = voucher[0].voucher_id;
				}
				$("#voucher_id_text").html(voucher_id_text);

				var htmlString_split = " <a class=\"pure-button pure-button-primary\" style=\"color: white\" href=\"javascript:load_split(" + voucher[0].voucher_id + ");\" title=\"" + voucher_id_text + "\" >"
					htmlString_split += "<i class=\"fas fa-share-alt\"></i> Splitt " + voucher_id_text + "</a>";

				$("#split_text").html(htmlString_split);

				$("#order_id").val(voucher[0].order_id);
				$("#order_id_orig").val(voucher[0].order_id);

				if (voucher[0].order_id)
				{
					var oArgs_order = {menuaction: 'property.uiinvoice.view_order', order_id: voucher[0].order_id};
					var requestUrl_order = phpGWLink('index.php', oArgs_order);
//					var htmlString_order  =  " <a target= \"_blank\" href=\"" + requestUrl_order + "\" title=\"" + voucher[0].status + "\" > Bestilling</a>";

					var htmlString_order = " <a href=\"javascript:load_order(" + voucher[0].order_id + ");\" title=\"" + voucher[0].status + "\" > Bestilling</a>";

					$("#order_text").html(htmlString_order);
				}
				else
				{
					$("#order_text").html('Bestilling');
				}

				$("#external_project_id").val(voucher[0].external_project_id);

				if (update_image)
				{
					try
					{
						if (voucher[0].external_ref)
						{
							if(Window1)
							{
								if(false === Window1.closed)
								{
								   Window1.close();
								}
							}
							Window1 = window.open(voucher[0].image_url,"invoiceimage","left=50,top=100,width=1000,height=700,toolbar=no,scrollbars=yes,resizable=yes");
							Window1.focus();
//							$("#invoice_id_text").html(voucher[0].external_ref);
//							document.getElementById('image_content').src = voucher[0].image_url;
						}
						else
						{
//							$("#invoice_id_text").html('FakturaNr');
//							document.getElementById('image_content').src = '';
						}
					}
					catch (e)
					{
						
					}
				}

				$("#invoice_id").html(voucher[0].invoice_id);
				$("#kid_nr").html(voucher[0].kid_nr);
				$("#vendor").html(voucher[0].vendor);
				$("#invoice_date").html(voucher[0].invoice_date);
				$("#payment_date").html(voucher[0].payment_date);
				$("#b_account_id").val(voucher[0].b_account_id);
				$("#dim_a").val(voucher[0].dim_a);
				$("#currency").html(voucher[0].currency);


				$("#process_log").val('');

				if (data['generic'].process_log)
				{
					$("#process_log").val(data['generic'].process_log);
				}

				$("#my_initials").val(data['generic'].my_initials);
				$("#sign_orig").val(data['generic'].sign_orig);
				$("#line_text").val(voucher[0].line_text);

				if (voucher[0].merknad)
				{
					var oArgs_remark = {menuaction: 'property.uiinvoice.remark', id: voucher[0].id};
					var requestUrl_remark = phpGWLink('index.php', oArgs_remark);
					var htmlString = " <a href=\"javascript:openwindow('" + requestUrl_remark + "','550','400')\" > Remark</a>";

					$("#remark").html(htmlString);
				}
//---------
				var checked_park_invoice = "";
				var park_invoice_status = "";
				if (voucher[0].parked)
				{
					checked_park_invoice = "checked = \"checked\"";
					var park_invoice_status = " X";
				}
				var htmlString_park_invoice = "<input type=\"checkbox\" name=\"values[park_invoice]\" value=\"1\" title=\"park invoice\"" + checked_park_invoice + "></input>" + park_invoice_status;
				$("#park_order").html(htmlString_park_invoice);
//---------
				var checked_close_order = "";
				var close_order_status = " " + voucher[0].status;
				if (voucher[0].closed)
				{
					checked_close_order = "checked = \"checked\"";
				}
				else if (voucher[0].project_type_id == 1 && voucher[0].periodization_id) // operation projekts
				{
					checked_close_order = "checked = \"checked\"";
				}
				else if (!voucher[0].continuous)
				{
					checked_close_order = "checked = \"checked\"";
				}

				var htmlString_close_order = "<input type=\"checkbox\" name=\"values[close_order]\" value=\"1\" title=\"close order\"" + checked_close_order + "></input>" + close_order_status;
				$("#close_order").html(htmlString_close_order);
				$("#close_order_orig").val(voucher[0].closed);
//---------

				if (typeof (data['generic']['dimb_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var obj = data['generic']['dimb_list']['options'];

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].dim_b)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});

					$("#dim_b").html(htmlString);
				}
				if (typeof (data['generic']['dime_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var obj = data['generic']['dime_list']['options'];

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].dim_e)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});

					$("#dim_e").html(htmlString);
				}
				if (typeof (data['generic']['tax_code_list']['options']) != 'undefined')
				{
					var htmlString = "";

					htmlString = "<option>Velg</option>"

					var obj = data['generic']['tax_code_list']['options'];

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].tax_code || obj[i].selected)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});

					$("#tax_code").html(htmlString);
				}

				if (typeof (data['generic']['period_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var obj = data['generic']['period_list']['options'];

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].period)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});
					$("#period").html(htmlString);
				}
				if (typeof (data['generic']['periodization_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['periodization_list']['options'];

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].periodization)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});

					$("#periodization").html(htmlString);
				}
				if (typeof (data['generic']['periodization_start_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['periodization_start_list']['options'];

					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].periodization_start)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});

					$("#periodization_start").html(htmlString);
				}

				if (typeof (data['generic']['process_code_list']['options']) != 'undefined')
				{
					var htmlString = "";

					var obj = data['generic']['process_code_list']['options'];
					$.each(obj, function (i)
					{
						var selected = '';
						if (obj[i].id == voucher[0].process_code)
						{
							selected = ' selected';
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					});

					$("#process_code").html(htmlString);
				}

				if (typeof (data['generic']['approved_list']) != 'undefined')
				{
					for (var i = 0; i < data['generic']['approved_list'].length; ++i)
					{
						var role_sign = data['generic']['approved_list'][i].role_sign;

						var role_initials = data['generic']['approved_list'][i].initials;

						if (data['generic']['approved_list'][i].date)
						{
							var htmlString = role_initials + ": " + data['generic']['approved_list'][i].date;
						}
						else
						{
							var htmlString = "<select  class=\"pure-u-md-1\" id=\"_" + role_sign + "\" name=\"values[forward][" + role_sign + "]\">";
							var obj = data['generic']['approved_list'][i]['user_list'].options;
							$.each(obj, function (i)
							{
								var selected = '';
								if (obj[i].id == role_initials)
								{
									selected = ' selected';
								}
								htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
							});
							htmlString += "</select>";
						}

						$("#" + role_sign).html(htmlString);
					}
				}

				if (typeof (data['generic']['approve_list']['options']) != 'undefined')
				{
					var htmlString = "";
					var htmlString2 = "<table class='pure-table'><thead><tr>";

					var obj = data['generic']['approve_list']['options'];
					$.each(obj, function (i)
					{
						htmlString2 += "<th align=\"center\">" + obj[i].name + "</th>";
					});
					htmlString2 += "</tr><thead><tbody><tr>";
					$.each(obj, function (i)
					{
						var checked = '';
						var selected = '';
						if (typeof (obj[i].selected) != 'undefined' && obj[i].selected == 1)
						{
							selected = ' selected';
							checked = "checked = \"checked\"";
						}
						htmlString += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
						htmlString2 += "<td align=\"center\"><input type =\"radio\" name=\"values[approve]\" value='" + obj[i].id + "'" + checked + "></input></td>";
					});

					htmlString2 += "</tr><tbody></table>";
					$("#approve_as2").html(htmlString2);
					//		$("#approve_as").html( htmlString );
				}
				var Url_email = email_base_url + '&voucher_id=' + voucher[0].voucher_id;

				var email_buttons = "<input type=\"button\" class=\"pure-button pure-button-primary\" name=\"Kopier til utklippstavle\" onClick=\"copyToClipboard('" + Url_email + "');\" value=\"Kopier til utklippstavle\" title=\"Kopier til utklippstavle\">";
				email_buttons += "<input type=\"button\" class=\"pure-button pure-button-primary\" value=\"Åpne epost\" onClick=\"javascript:location.href = 'mailto:?subject=Link til faktura&body=Lim inn linken her...';\" />";
				$("#email_link").html(email_buttons);
			}
			else
			{
				$("#line_text").val('');
				$("#voucher_id").val('');
				$("#voucher_id_text").html('');
				$("#order_id").val('');
				$("#order_id_orig").val('');
				$("#external_project_id").val('');
				$("#invoice_id").html('');
				$("#kid_nr").html('');
				$("#vendor").html('');
				$("#close_order_orig").val('');
				$("#my_initials").val('');
				$("#sign_orig").val('');
				$("#invoice_date").html('');
				$("#payment_date").html('');
				$("#b_account_id").val('');
				$("#currency").html('');
				$("#oppsynsmannid").html('');
				$("#saksbehandlerid").html('');
				$("#budsjettansvarligid").html('');
				//	$("#remark").html( '' );
				$("#process_log").val('');
				$("#dim_a").val('');
				$("#dim_b").html("<option>Velg</option>");
				$("#dim_e").html("<option>Velg</option>");
				$("#period").html("<option>Velg</option>");
				$("#periodization").html("<option>Velg</option>");
				$("#periodization_start").html("<option>Velg</option>");
				$("#process_code").html("<option>Velg</option>");
				$("#tax_code").html("<option>0</option>");
				$("#approve_as").html("<option>Velg</option>");
				$("#order_text").html('Bestilling');
				$("#invoice_id_text").html('FakturaNr');
				$("#receipt").html('');
				$("#email_link").html('');
				document.getElementById('image_content').src = '';
			}
		}
	});
}

//------------

function load_order(id)
{
	var oArgs = {menuaction: 'property.uiinvoice.view_order', order_id: id, nonavbar: true, lean: true};
	var requestUrl = phpGWLink('index.php', oArgs);
	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			closeJS_local();
		}});
}

function load_split(voucher_id)
{
	var oArgs = {menuaction: 'property.uiinvoice2.split_voucher', voucher_id: voucher_id, nonavbar: true, lean: true};
	var requestUrl = phpGWLink('index.php', oArgs);
	TINY.box.show({iframe: requestUrl, boxid: 'frameless', width: 750, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true, close: true, closejs: function ()
		{
			reset_table(voucher_id);
		}});
}

function reset_table(voucher_id)
{
	var oArgs = {menuaction: 'property.uiinvoice2.get_first_line', voucher_id: voucher_id};
	var requestUrl = phpGWLink('index.php', oArgs, true);
	var line_id = 0;

	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: requestUrl,
		success: function (data)
		{
			if (data != null)
			{
				line_id = data['line_id'];
				var oArgs_table = {menuaction: 'property.uiinvoice2.query', line_id: line_id, voucher_id_filter: voucher_id};
				var requestUrl_table = phpGWLink('index.php', oArgs_table, true);
				JqueryPortico.updateinlineTableHelper('datatable-container_1', requestUrl_table);

				update_form_values(line_id, 0);
			}
		}
	});

}

function closeJS_local()
{
	var line_id = $("#line_id").val( );
	var voucher_id_orig = $("#voucher_id").val();
	$("#curtain").hide();
	$("#popupBox").hide();
	update_form_values(line_id, voucher_id_orig);
}

function closeJS_remote()
{
	TINY.box.hide();
}

function hide_popupBox( )
{
	var line_id = $("#line_id").val( );
	var voucher_id_orig = $("#voucher_id").val();
	$("#curtain").hide();
	$("#popupBox").hide();
	update_form_values(line_id, voucher_id_orig);
}



function copyToClipboard(text)
{
	window.prompt("Kopier til utklippstavle: Ctrl+C, Enter", text);
	// window.open("mailto:?&subject=Link til faktura");
	// window.close();
}

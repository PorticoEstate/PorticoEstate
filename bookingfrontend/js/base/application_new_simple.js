/* global lang */

var booking_month_horizon = 3;
var GlobalStartTime;
var day_default_lenght = 0;
var dow_default_start = null;
var dow_default_end = null;
var time_default_start = 15;
var time_default_end = 10;
var availlableTimeSlots = {};

var day_default_lenght_fallback = 0;
var dow_default_start_fallback = null;
var dow_default_end_fallback = null;
var time_default_start_fallback = 15;
var time_default_end_fallback = 10;
//var disabledDates = ko.observableArray();

$(".navbar-search").removeClass("d-none");
var baseURL = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/";
$(".termAcceptDocsUrl").attr('data-bind', "text: docName, attr: {'href': itemLink }");
$(".maleInput").attr('data-bind', "textInput: inputCountMale, attr: {'name': malename }, value: 1");
$(".femaleInput").attr('data-bind', "textInput: inputCountFemale, attr: {'name': femalename }, value: 1");
var urlParams = [];
CreateUrlParams(window.location.search);

var agegroup = ko.observableArray();
ko.validation.locale('nb-NO');
var am;


function applicationModel()
{
	var self = this;
	self.showErrorMessages = ko.observable(false);
	self.applicationCartItems = ko.computed(function ()
	{
		return bc.applicationCartItems();
	});


	self.activityId = ko.observable();
	self.date = ko.observableArray();

	self.aboutArrangement = ko.observable("");
	self.agegroupList = agegroup;
	self.disabledDatesList = ko.observableArray();
	self.specialRequirements = ko.observable("");
	self.attachment = ko.observable();
	self.termAcceptDocs = ko.observableArray();
	self.termAccept = ko.computed(function ()
	{
		var notAccepted = ko.utils.arrayFirst(self.termAcceptDocs(), function (current)
		{
			return current.checkedStatus() == false;
		});
		if (!notAccepted)
		{
			return true;
		}
		else
		{
			return false;
		}
	});

	self.termAcceptedDocs = ko.computed(function ()
	{
		var list = [];
		for (var i = 0; i < self.termAcceptDocs().length; i++)
		{
			if (self.termAcceptDocs()[i].checkedStatus())
			{
				list.push("building::" + self.termAcceptDocs()[i].docId);
			}
		}
		return list;
	});

	self.removeDocs = function ()
	{
		var length = self.termAcceptDocs().length;
		var temp_docs = [];
		for (var i = 0; i < length; i++)
		{
			temp_docs.push(self.termAcceptDocs()[i].docName);
		}
		for (var i = 0; i < length; i++)
		{
			self.removeDoc(temp_docs[i]);
		}
	};

	self.removeDoc = function (docName)
	{
		self.termAcceptDocs.remove(function (doc)
		{
			return doc.docName == docName;
		});
	};

}


$(document).ready(function ()
{

	var activityId;

	getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiapplication.add", building_id: urlParams['building_id'], phpgw_return_as: "json"}, true);
	$.getJSON(getJsonURL, function (result)
	{
		$("#inputTargetAudience").val(result.audience[0].id);
		activityId = result.application.activity_id;
		for (var i = 0; i < result.agegroups.length; i++)
		{
			agegroup.push({
				name: result.agegroups[i].name, agegroupLabel: result.agegroups[i].name,
				inputCountMale: ko.observable("").extend({number: true}),
				inputCountFemale: ko.observable("").extend({number: true}),
				malename: 'male[' + result.agegroups[i].id + ']',
				femalename: 'female[' + result.agegroups[i].id + ']',
				id: result.agegroups[i].id
			});
		}
		if (initialAgegroups != null)
		{
			for (var i = 0; i < initialAgegroups.length; i++)
			{
				var id = initialAgegroups[i].agegroup_id;
				var find = ko.utils.arrayFirst(agegroup(), function (current)
				{
					return current.id == id;
				});
				if (find)
				{
					find.inputCountMale(initialAgegroups[i].male);
					find.inputCountFemale(initialAgegroups[i].female);
				}
			}
		}
		for (var i = 0; i < result.audience.length; i++)
		{
			if ($.inArray(result.audience[i].id, initialAudience) > -1)
			{
				$("#audienceDropdownBtn").text(result.audience[i].name);
			}
			$("#inputTargetAudience").val(result.audience[i].id);

		}


		getJsonURL = phpGWLink('bookingfrontend/', {menuaction: "bookingfrontend.uiresource.index_json", filter_building_id: urlParams['building_id'], sort: "name", phpgw_return_as: "json"}, true);
		$.getJSON(getJsonURL, function (result)
		{
			for (var i = 0; i < result.results.length; i++)
			{
				if ($("#resource_id").val() == result.results[i].id)
				{
					if (result.results[i].booking_day_default_lenght && result.results[i].booking_day_default_lenght != -1)
					{
						day_default_lenght = result.results[i].booking_day_default_lenght;
					}
					if (result.results[i].booking_time_default_end && result.results[i].booking_time_default_end != -1)
					{
						time_default_end = result.results[i].booking_time_default_end;
					}
					if (result.results[i].booking_time_default_start && result.results[i].booking_time_default_start != -1)
					{
						time_default_start = result.results[i].booking_time_default_start;
					}
					$('#item-description').html('<b>' + result.results[i].name + '</b>' + result.results[i].description);
					$('#resource_list').hide();

//					set_conditional_translation(result.results[i].type);

				}
				if (!day_default_lenght)
				{
					time_default_end = time_default_start + 1;
				}

			}
		}).done(function ()
		{
			PopulatePostedDate();

		});

		if ($("#resource_id").val())
		{
			var parameter = {
				menuaction: "bookingfrontend.uidocument_view.regulations",
				'owner[]': "building::" + urlParams['building_id'],
				sort: "name"
			};
			getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
			getJsonURL += '&owner[]=resource::' + $("#resource_id").val();

			$.getJSON(getJsonURL, function (result)
			{
				for (var i = 0; i < result.data.length; i++)
				{
					var checked = false;
					if (initialAcceptedDocs != null)
					{
						if (initialAcceptedDocs[i] == "on")
						{
							checked = true;
						}
					}
					am.termAcceptDocs.push({docName: result.data[i].name, itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'), checkedStatus: ko.observable(checked), docId: result.data[i].id.replace(/^\D+/g, '')});
				}
			});
		}

	}).done(function ()
	{
		am = new applicationModel();
		am.activityId(activityId);
		ko.applyBindings(am, document.getElementById("new-application-page"));
		if (typeof initialAudience !== "undefined" && initialAudience != null)
		{
			$("#inputTargetAudience").val(initialAudience);
		}
	});


	$("#application_form").submit(function (event)
	{
		var allowSubmit = am.termAccept();
		if (!allowSubmit)
		{
			alert(errorAcceptedDocs);
			event.preventDefault();
		}
	});

});

/**
 * Note: deprecated
 */
function set_conditional_translation(type)
{
	if (type == 'Equipment')
	{
		$('#lang_checkin').text('Utlevering');
		$('#lang_checkout').text('Innlevering');
	}
	else
	{
		$('#lang_checkin').text('Innsjekk');
		$('#lang_checkout').text('Utsjekk');
	}
}
function PopulatePostedDate()
{
	var StartTime, EndTime;

	if (!$("#resource_id").val())
	{
		$('#time_select').hide();
		$('#item-description').html('');
		$('#selected_period').html('<b>Velg leieobjekt</b>');
	}

	if (initialDates != null)
	{
		for (var i = 0; i < initialDates.length; i++)
		{
			var from_ = (initialDates[i].from_).replace(" ", "T");
			var to_ = (initialDates[i].to_).replace(" ", "T");
			StartTime = new Date(from_);
			EndTime = new Date(to_);
		}

		if (initialDates.length > 0)
		{
			time_default_start = StartTime.getHours();
			time_default_end = EndTime.getHours();
			$('#start_date').datetimepicker('destroy');
		}
	}
	else
	{
		if (typeof urlParams['start'] !== "undefined" && typeof urlParams['end'] !== "undefined")
		{
			if (urlParams['start'].length > 0 && urlParams['end'].length > 0)
			{
				StartTime = new Date(parseInt(urlParams['start']));

				if (day_default_lenght == -1 || day_default_lenght == 0)
				{
					time_default_start = StartTime.getHours();
				}

				StartTime.setHours(time_default_start, 0);
				EndTime = new Date(parseInt(urlParams['start']));
				EndTime.setDate(EndTime.getDate() + day_default_lenght);

				if (day_default_lenght == -1 || day_default_lenght == 0)
				{
					EndTime = new Date(parseInt(urlParams['end']));
					time_default_end = EndTime.getHours();
				}

				EndTime.setHours(time_default_end, 0);

				$('#time_select').hide();

			}
		}
//		else
//		{
//			var StartTime = new Date();
//			StartTime.setHours(time_default_start, 0);
//
//			EndTime = new Date();
//			EndTime.setDate(EndTime.getDate() + day_default_lenght);
//			EndTime.setHours(time_default_end, 0);
//
//		}

	}

	if ($("#resource_id").val() && StartTime)
	{
		GlobalStartTime = StartTime;
		$('#from_').val(formatSingleDate(StartTime));
		$('#to_').val(formatSingleDate(EndTime));
		$('#start_date').val(formatSingleDateWithoutHours(StartTime));
		$('#selected_period').html(formatPeriodeHours(StartTime, EndTime));
		$('#start_date').prop('disabled', true);

	}
	$('#bookingStartTime').val(time_default_start);
	$('#bookingEndTime').val(time_default_end);

	if ($("#resource_id").val())
	{
		getFreetime();

		var parameter = {
			menuaction: "bookingfrontend.uiapplication.set_block",
			resource_id: $("#resource_id").val(),
			from_: StartTime.toJSON(),
			to_: EndTime.toJSON()
		};

		$.getJSON(phpGWLink('bookingfrontend/', parameter, true), function (result)
		{
			if (result.status == 'reserved')
			{
				alert('Opptatt');
				window.location.replace(phpGWLink('bookingfrontend/',
				{
					menuaction: "bookingfrontend.uiresource.show",
					building_id: urlParams['building_id'],
					id: $("#resource_id").val()}
				));
			}
		});
	}
}

function cancel_block()
{
		var parameter = {
			menuaction: "bookingfrontend.uiapplication.cancel_block",
			resource_id: $("#resource_id").val(),
			building_id: urlParams['building_id'],
			from_: $('#from_').val(),
			to_: $('#to_').val()
		};

		window.location.replace(phpGWLink('bookingfrontend/', parameter));

}

function getFreetime()
{
	var checkDate = new Date();
	var EndDate = new Date(checkDate.getFullYear(), checkDate.getMonth() + booking_month_horizon, 0);

	var getJsonURL = phpGWLink('bookingfrontend/', {
		menuaction: "bookingfrontend.uibooking.get_freetime",
		building_id: urlParams['building_id'],
		resource_id: $("#resource_id").val(),
		start_date: formatSingleDateWithoutHours(new Date()),
		end_date: formatSingleDateWithoutHours(EndDate),
	}, true);

	$.getJSON(getJsonURL, function (result)
	{
		for (var key in result)
		{
			for (var i = 0; i < result[key].length; i++)
			{
				if (typeof result[key][i].applicationLink != 'undefined')
				{
					result[key][i].applicationLink = phpGWLink('bookingfrontend/', result[key][i].applicationLink);
				}


			}
			availlableTimeSlots[key] = result[key];
		}

	}).done(function ()
	{
		var resource_id = $("#resource_id").val();
//		console.log(availlableTimeSlots);
		if (typeof (availlableTimeSlots[resource_id]) != 'undefined')
		{
			var TimeSlots = availlableTimeSlots[resource_id];
			var start;
			var allowed_dates = [-1]; // block everything
			var AllowDate;

			for (var i = 0; i < TimeSlots.length; i++)
			{
				if (TimeSlots[i].overlap == false)
				{
					start = new Date(parseInt(TimeSlots[i].start));
					AllowDate = dateFormat(start, 'yyyy/mm/dd');
					allowed_dates.push(AllowDate);

				}
			}

			if (allowed_dates.length == 1)
			{

				$('#from_').val('');
				$('#to_').val('');
				$('#start_date').val('');
				$('#selected_period').html('');
			}

			$('#start_date').datetimepicker('setOptions', {allowDates: allowed_dates});

		}

	});
}


function validate()
{

}



$('#start_date').datetimepicker({onSelectDate: function (ct, $i)
	{

		var StartTime = new Date(ct);
		StartTime.setHours(time_default_start, 0);
		var EndTime = new Date(ct);
		EndTime.setDate(ct.getDate() + day_default_lenght);
		EndTime.setHours(time_default_end, 0);

		if (StartTime.getTime() < EndTime.getTime())
		{
			GlobalStartTime = StartTime;
			$('#from_').val(formatSingleDate(StartTime));
			$('#to_').val(formatSingleDate(EndTime));
			$('#selected_period').html(formatPeriodeHours(StartTime, EndTime));
		}
		else if (StartTime.getTime() >= EndTime.getTime())
		{
			alert("Starttid må være tidligere enn sluttid");
		}

	}});

var post_handle_table = function()
{

	var tr = $('#articles_container').find('tr')[1];

	if(!tr || typeof(tr) == 'undefined')
	{
		return;
	}

	tr.classList.add("table-success");
	tr.childNodes[0].childNodes[0].setAttribute('style', 'display:none;');
	tr.childNodes[5].childNodes[0].setAttribute('style', 'display:none;');
	tr.childNodes[9].childNodes[0].setAttribute('style', 'display:none;');

	var xTable = tr.parentNode.parentNode;

	set_sum(xTable);
};

function populateTableArticles(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'table table-bordered table-hover table-sm table-responsive', null, post_handle_table);
}

function add_to_bastet(element)
{
	var tr = element.parentNode.parentNode;
	if (tr.rowIndex == 1)
	{
		return;
	}

	tr.classList.add("table-success");

	var id = element.parentNode.parentNode.childNodes[1].childNodes[0].value;
	var quantity = element.parentNode.parentNode.childNodes[5].childNodes[0].value;
	var price = element.parentNode.parentNode.childNodes[4].innerText;

	/**
	 * set selected items
	 */
	var temp = element.parentNode.parentNode.childNodes[7].childNodes[0].value;

	var selected_quantity = 0;

	if (temp)
	{
		selected_quantity = parseInt(temp.split("_")[1]);
	}

	selected_quantity = selected_quantity + parseInt(quantity);

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 0;
	/**
	 * Reset button to disabled
	 */
	element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);

	var target = element.parentNode.parentNode.childNodes[7].childNodes[0];
	target.value = id + '_' + selected_quantity;

	var elem = element.parentNode.parentNode.childNodes[6];

// add text
	elem.innerText = selected_quantity;

	var sum_cell = element.parentNode.parentNode.childNodes[8]
	sum_cell.innerText = (selected_quantity * parseFloat(price)).toFixed(2);

	var tableFooter = document.getElementById('tfoot');
	if (tableFooter)
	{
		tableFooter.parentNode.removeChild(tableFooter);
	}
	var xTable = element.parentNode.parentNode.parentNode.parentNode;

	set_sum(xTable);
}

function set_sum(xTable)
{
	var xTableBody = xTable.childNodes[1];
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 6);
	tableFooterTrTd.innerHTML = "Sum:";
	tableFooterTr.appendChild(tableFooterTrTd);
	var tableFooterTrTd2 = document.createElement('td');
	tableFooterTrTd2.setAttribute('id', 'sum_price_table');
	tableFooterTrTd2.classList.add("text-right");

	tableFooterTrTd2.innerHTML = temp_total_sum.toFixed(2);

	tableFooterTr.appendChild(tableFooterTrTd2);

	tableFooter.appendChild(tableFooterTr);
	xTable.appendChild(tableFooter);

}

function empty_from_bastet(element)
{
	var tr = element.parentNode.parentNode;
	tr.classList.remove("table-success");

	/**
	 * Reset quantity
	 */
	element.parentNode.parentNode.childNodes[6].innerText = '';
	element.parentNode.parentNode.childNodes[5].childNodes[0].value = 0;
	element.parentNode.parentNode.childNodes[8].innerText = '';
	element.parentNode.parentNode.childNodes[7].childNodes[0].value = '';

	/**
	 * Reset button to disabled
	 */
	element.parentNode.parentNode.childNodes[0].childNodes[0].setAttribute('disabled', true);
	element.parentNode.parentNode.childNodes[9].childNodes[0].setAttribute('disabled', true);

	var xTableBody = element.parentNode.parentNode.parentNode;
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
		}
	}

	$('#sum_price_table').html(temp_total_sum.toFixed(2));


}

$(document).ready(function ()
{
	$('#articles_container').on('change', '.quantity', function ()
	{
		var tr = $(this).parents('tr')[0];
		if (tr.rowIndex == 1)
		{
			$(this).val(0);
			tr.classList.add("table-success");
			return;
		}
		var quantity = $(this).val();
		var button = $(this).parents('tr').find("button");

		if (quantity > 0)
		{
			button.prop('disabled', false);
		}
		else
		{
			button.prop('disabled', true);
		}
	});

	var resource_id = $("#resource_id").val();
	var resources = [resource_id];
	var selection = [resource_id];
	populateTableChkArticles(selection, resources);

	function populateTableChkArticles(selection, resources)
	{
		var oArgs = {
			menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
			sort: 'name',
		};
		var url = phpGWLink('bookingfrontend/', oArgs, true);

		for (var r in resources)
		{
			url += '&resources[]=' + resources[r];
		}

		var container = 'articles_container';
		var colDefsRegulations = [
			{
				label: lang['Select'],
				attrs: [{name: 'class', value: "align-middle"}],
				object: [
					{
						type: 'button',
						attrs: [
							{name: 'type', value: 'button'},
							{name: 'disabled', value: true},
							{name: 'class', value: 'btn btn-success'},
							{name: 'onClick', value: 'add_to_bastet(this);'},
							{name: 'innerHTML', value: 'Legg til <i class="fas fa-shopping-basket"></i>'},
						]
					}
				]
			},
			{
				/**
				 * Hidden field for holding article id
				 */
				attrs: [{name: 'style', value: "display:none;"}],
				object: [
					{type: 'input', attrs: [
							{name: 'type', value: 'hidden'}
						]
					}
				], value: 'id'},
			{
				key: 'name',
				label: lang['article'],
				attrs: [{name: 'class', value: "align-middle"}],
			},
			{
				key: 'unit',
				label: lang['unit'],
				attrs: [{name: 'class', value: "align-middle"}],
			},
			{
				key: 'price',
				label: lang['unit cost'],
				attrs: [{name: 'class', value: "align-middle"}],
			},
			{
				key: 'quantity',
				label: lang['quantity'],
				attrs: [{name: 'class', value: "align-middle"}],
				object: [
					{type: 'input', attrs: [
							{name: 'type', value: 'number'},
							{name: 'min', value: 0},
							{name: 'value', value: 0},
							{name: 'size', value: 3},
							{name: 'class', value: 'quantity'},
						]
					}
				]},
			{
				key: 'selected_quantity',
				label: lang['Selected'],
				attrs: [{name: 'class', value: "text-right align-middle"}]
			},
			{
				label: 'hidden',
				attrs: [{name: 'style', value: "display:none;"}],
				object: [
					{type: 'input', attrs: [
							{name: 'type', value: 'text'},
							{name: 'name', value: 'selected_articles[]'}
						]
					}
				], value: 'selected_article_quantity'
			},
			{
				key: 'selected_sum',
				label: lang['Sum'],
				attrs: [
					{name: 'class', value: "text-right align-middle selected_sum"}
				]
			},
			{
				label: lang['Delete'],
				attrs: [{name: 'class', value: "align-middle"}],
				object: [
					{
						type: 'button',
						attrs: [
							{name: 'type', value: 'button'},
							{name: 'disabled', value: true},
							{name: 'class', value: 'btn btn-danger'},
							{name: 'onClick', value: 'empty_from_bastet(this);'},
							{name: 'innerHTML', value: 'Slett <i class="far fa-trash-alt"></i>'},
						]
					}
				]
			}

		];

		populateTableArticles(url, container, colDefsRegulations);

	}


	$("#resource_id").change(function ()
	{

		day_default_lenght = day_default_lenght_fallback;
		time_default_end = time_default_end_fallback;
		time_default_start = time_default_start_fallback;
		dow_default_start = dow_default_start_fallback;
		dow_default_end = dow_default_end_fallback;

		if (!day_default_lenght)
		{
			time_default_end = time_default_start + 1;
		}


		//reset...
		am.removeDocs();


		if (!$(this).val())
		{
			$('#time_select').hide();
			$('#item-description').html('');
			$('#selected_period').html('<b>Velg leieobjekt</b>');

		}
		else
		{
			var parameter = {
				menuaction: "bookingfrontend.uiresource.read_single",
				id: $(this).val()
			};
			getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
			$.getJSON(getJsonURL, function (resource)
			{
				if (resource.booking_day_default_lenght !== null && resource.booking_day_default_lenght != -1)
				{
					day_default_lenght = resource.booking_day_default_lenght;
				}
				if (resource.booking_time_default_end !== null && resource.booking_time_default_end != -1)
				{
					time_default_end = resource.booking_time_default_end;
				}
				if (resource.booking_time_default_start !== null && resource.booking_time_default_start != -1)
				{
					time_default_start = resource.booking_time_default_start;
				}

				if (Number(resource.booking_month_horizon) > (booking_month_horizon + 1))
				{
					booking_month_horizon = Number(resource.booking_month_horizon) + 1;
				}

//				set_conditional_translation(resource.type);
				$('#item-description').html(resource.description);

				$('#bookingStartTime').val(time_default_start);
				$('#bookingEndTime').val(time_default_end);
				$('#from_').val();
				$('#to_').val();
				$('#selected_period').html('<b>Velg dato</b>');
				$('#start_date').val('');

			}).done(function ()
			{
				$('#time_select').show();

			});
			var parameter = {
				menuaction: "bookingfrontend.uidocument_view.regulations",
				'owner[]': "building::" + urlParams['building_id'],
				sort: "name"
			};
			getJsonURL = phpGWLink('bookingfrontend/', parameter, true);
			getJsonURL += '&owner[]=resource::' + $("#resource_id").val();

			$.getJSON(getJsonURL, function (result)
			{
				for (var i = 0; i < result.data.length; i++)
				{
					var checked = false;
					if (initialAcceptedDocs != null)
					{
						if (initialAcceptedDocs[i] == "on")
						{
							checked = true;
						}
					}
					am.termAcceptDocs.push({docName: result.data[i].name, itemLink: RemoveCharacterFromURL(result.data[i].link, 'amp;'), checkedStatus: ko.observable(checked), docId: result.data[i].id.replace(/^\D+/g, '')});
				}
			});
			getFreetime();

		}
	});

});


window.onload = function ()
{
	const error = document.getElementById("submit-error");
	const targetAudience = document.getElementById("inputTargetAudience")


	const validateTargetAudience = function ()
	{
		if (!$("#inputTargetAudience").val())
		{
			$("#inputTargetAudience").val(-1);
		}
	}

	const validateInputs = function ()
	{
//    !eventName.value ? eventName.classList.add("is-invalid") : eventName.classList.replace("is-invalid", "is-valid") || eventName.classList.add("is-valid");

//    !organizerName.value ? organizerName.classList.add("is-invalid") : organizerName.classList.replace("is-invalid", "is-valid") || organizerName.classList.add("is-valid")
	}

	form.addEventListener("submit", function (e)
	{
		if (!$("#resource_id").val() || !$('#from_').val())
		{
			e.preventDefault();
			e.stopPropagation();
			validateInputs();
			validateTargetAudience();
			error.style.display = "block";
			setTimeout(function ()
			{
				error.style.display = "none";
			}, 5000)
		}
		else
		{
			return;
		}
	})
}
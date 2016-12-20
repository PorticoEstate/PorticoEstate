/* 
 * Copyright (C) 2016 hc483
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


var contract_id_selection;
var lang;
var oArgs = {menuaction: 'rental.uicontract.index', organization_number: true};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'contract_name', 'contract_id', 'contract_container', 'old_contract_id');

$(window).on('load', function ()
{
	contract_id = $('#contract_id').val();
	if (contract_id)
	{
		contract_id_selection = contract_id;
	}
	$("#contract_name").on("autocompleteselect", function (event, ui)
	{
		var contract_id = ui.item.value;
		if (contract_id !== contract_id_selection)
		{
			populateContractParty(contract_id);
		}
	});
});

function populateContractParty(contract_id)
{
	contract_id = contract_id || $('#contract_id').val();

	if (!contract_id)
	{
		return;
	}
	oArgs = {
		menuaction: 'rental.uicontract.get',
		id: contract_id
	};

	var requestUrl = phpGWLink('index.php', oArgs, true);
	var data = {};

	JqueryPortico.execute_ajax(requestUrl,
		function (result)
		{

			$("#executive_officer").html(result.executive_officer);
			$("#composite").html(result.composite);
			$("#rented_area").html(result.rented_area);
			$("#security_amount").html(result.security_amount);
			$("#date_start").html(result.date_start);
			$("#date_end").html(result.date_end);
			$("#type").html(result.type);
			$("#party").html(result.party);
			$("#identifier").html(result.identifier);
			$("#mobile_phone").html(result.mobile_phone);
			$("#department").html(result.department);
			$("#contract_status").html(result.contract_status);
			$("#rented_area").html(result.rented_area);
			$("#term_label").html(result.term_label);

		}, data, "POST", "json"
		);
}

/*
$(document).ready(function ()
{

	var wrapper = document.getElementById("signature-pad"),
		clearButton = wrapper.querySelector("[data-action=clear]"),
		saveButton = wrapper.querySelector("[data-action=save]"),
		canvas = wrapper.querySelector("canvas"),
		signaturePad;

// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
	function resizeCanvas()
	{
		// When zoomed out to less than 100%, for some very strange reason,
		// some browsers report devicePixelRatio as less than 1
		// and only part of the canvas is cleared then.
		var ratio = Math.max(window.devicePixelRatio || 1, 1);
		canvas.width = canvas.offsetWidth * ratio;
		canvas.height = canvas.offsetHeight * ratio;
		canvas.getContext("2d").scale(ratio, ratio);
	}

	window.onresize = resizeCanvas;
	resizeCanvas();

	signaturePad = new SignaturePad(canvas);

	clearButton.addEventListener("click", function (event)
	{
		signaturePad.clear();
	});

	saveButton.addEventListener("click", function (event)
	{
		if (signaturePad.isEmpty())
		{
			alert("Please provide signature first.");
		}
		else
		{
			window.open(signaturePad.toDataURL());
		}
	});
});
 */
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
			$("#contract_party_name").val(result.party_name);
			$("#contract_party_email").val(result.party_email);
			$("#contract_party_phone").val(result.party_phone);

			var location = result.address_1;
			if (result.address_2)
			{
				location += "\n" + result.address_2;
			}
			location += "\n" + result.zip_code;
			location += ' ' + result.city;
			$("#location").val(location);

		}, data, "POST", "json"
		);
}

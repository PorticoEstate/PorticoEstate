/*
 * Copyright (C) 2017 hc483
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

var mod11OfNumberWithControlDigit = function (input)
{
	var controlNumber = 2,
		sumForMod = 0,
		i;

	for (i = input.length - 2; i >= 0; --i)
	{
		sumForMod += input.charAt(i) * controlNumber;
		if (++controlNumber > 7)
			controlNumber = 2;
	}
	var result = (11 - sumForMod % 11);

	return result === 11 ? 0 : result;
};

$(document).ready(function ()
{

	$.formUtils.addValidator({
		name: 'organization_number',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;

			var organization_number = value.replace(/\s/g, '');
			if (!organization_number.match(/(^\d{9}$)/))
			{
				return false;
			}

			v = parseInt(organization_number.charAt(organization_number.length - 1), 10) === mod11OfNumberWithControlDigit(organization_number)
			return v;
		},
		errorMessage: lang['please enter a valid organization number'] || 'please enter a valid organization number',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'account_number',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = false;

			var account_number = value.toString().replace(/\./g, '');
			if (account_number.length !== 11)
			{
				return false;
			}
			v = parseInt(account_number.charAt(account_number.length - 1), 10) === mod11OfNumberWithControlDigit(account_number);

			return v;
		},
		errorMessage: lang['please enter a valid account number'] || 'please enter a valid account number',
		errorMessageKey: ''
	});
});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'rescategory_activities',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#activities_container table input[name="activities[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			return v;
		},
		errorMessage: 'Please choose at least 1 activity',
		errorMessageKey: 'rescategory_activities'
	});
}

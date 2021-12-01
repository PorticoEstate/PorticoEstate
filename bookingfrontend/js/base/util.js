/* global monthList, months */

var Util = function ()
{


	//Formattering
	var format = function ()
	{

		var formatDateForBackend = function (date)
		{
			if (date === "")
			{
				return "";
			}
			var fDate = new Date(date);
			return fDate.getFullYear() + "-" + (fDate.getMonth() + 1) + "-" + fDate.getDate() + " " + (fDate.getHours()) + ":" + fDate.getMinutes() + ":" + fDate.getSeconds() + "";
		};

		var getDateFormat = function (from, to)
		{
			let ret = [];
			let fromDate = new Date(from.replace(" ", "T"));
			let toDate = new Date(to.replace(" ", "T"));
//			let fromDate = new Date(from);
//			let toDate = new Date(to);

			if (fromDate.getDate() === toDate.getDate())
			{
				ret.push(fromDate.getDate() + ". ");
				let month = monthList[fromDate.getMonth()];
				ret.push(months[month]);
				return ret;
			}
			else
			{
				ret.push(fromDate.getDate() + ".-" + toDate.getDate() + ".");
				let month = monthList[fromDate.getMonth()];
				ret.push(months[month]);
				return ret;
			}
		};

		var getTimeFormat = function (from, to)
		{
			let fromDate = new Date(from.replace(" ", "T"));
			let toDate = new Date(to.replace(" ", "T"));
//			let fromDate = new Date(from);
//			let toDate = new Date(to);
			let ret;

			ret = (fromDate.getHours() < 10 ? '0' + fromDate.getHours() : fromDate.getHours()) + ":"
				+ (fromDate.getMinutes() < 10 ? '0' + fromDate.getMinutes() : fromDate.getMinutes()) + " - "
				+ (toDate.getHours() < 10 ? '0' + toDate.getHours() : toDate.getHours()) + ":"
				+ (toDate.getMinutes() < 10 ? '0' + toDate.getMinutes() : toDate.getMinutes());
			return ret;
		}

		return {
			FormatDateForBackend: formatDateForBackend,
			GetDateFormat: getDateFormat,
			GetTimeFormat: getTimeFormat
		};
	}();


	return {
		Format: format
	};

}();

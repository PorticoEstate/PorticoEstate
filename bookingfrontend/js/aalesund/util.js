var Util = function () {


	//Formattering
	var format = function() {

		var formatDateForBackend = function(date) {
			if (date === "") {
				return "";
			}
			var fDate = new Date(date);
			return fDate.getFullYear()+"-"+(fDate.getMonth()+1)+"-"+fDate.getDate()+" "+(fDate.getHours())+":"+fDate.getMinutes()+":"+fDate.getSeconds()+"";
		}

		var getDateFormat = function(from, to) {
			let ret = [];
			let fromDate = new Date(from);
			let toDate = new Date(to);

			if (fromDate.getDate() === toDate.getDate()) {
				ret.push(fromDate.getDate()+". ")
				ret.push(months[fromDate.getMonth()]);
				return ret;
			} else {
				ret.push(fromDate.getDate() + ".-" + toDate.getDate() + ".");
				ret.push(months[fromDate.getMonth()]);
				return ret;
			}
		}

		var getTimeFormat = function(from, to) {
			let fromDate = new Date(from);
			let toDate = new Date(to);
			let ret;

			ret = (fromDate.getHours() + ":" + fromDate.getMinutes()+"-"+toDate.getHours() + ":" + toDate.getMinutes());
			return ret;
		}

		return {
			FormatDateForBackend: formatDateForBackend,
			GetDateFormat: getDateFormat,
			GetTimeFormat: getTimeFormat
		}
	}();


	return {
		Format: format
	}

}();

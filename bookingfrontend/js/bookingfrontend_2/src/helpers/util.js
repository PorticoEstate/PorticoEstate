/* global monthList, months */
import './translation';
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
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


/**
 * Emulate phpGW's link function
 *
 * @param String strURL target URL
 * @param Object oArgs Query String args as associate array object
 * @param bool bAsJSON ask that the request be returned as JSON (experimental feature)
 * @param String baseURL (optional) Base URL to use instead of strBaseURL
 * @returns String URL
 */
export function phpGWLink(strURL, oArgs, bAsJSON, baseURL) {
    // console.log(strBaseURL)
    if (baseURL) {
        const baseURLParts = (baseURL).split('/').filter(a => a !== '' && !a.includes('http'));
        baseURL = '//'+baseURLParts.slice(0, baseURLParts.length - 1).join('/') + '/'; // Remove last element (file name)
    }
    const urlParts = (baseURL || strBaseURL).split('?');
    let newURL = urlParts[0] + strURL + '?';

    if (oArgs == null) {
        oArgs = new Object();
    }
    for (const key in oArgs) {
        newURL += key + '=' + oArgs[key] + '&';
    }
    if(urlParts[1]) {
        newURL += urlParts[1];
    }

    if (bAsJSON) {
        newURL += '&phpgw_return_as=json';
    }
    return newURL;
}


$(document).ready(function () {
    $("input[type=radio][name=select_template]").change(function () {
        var template = $(this).val();
        var oArgs = {
            menuaction: 'bookingfrontend.preferences.set'
        };

        var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {template_set: template},
            url: requestUrl,
            success: function (data)
            {
                //		console.log(data);
                location.reload(true);
            }
        });
    });

	$("input[type=radio][name=select_language]").change(function () {
        var lang = $(this).val();
        var oArgs = {
            menuaction: 'bookingfrontend.preferences.set'
        };

        var requestUrl = phpGWLink('bookingfrontend/', oArgs, true);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {lang: lang},
            url: requestUrl,
            success: function (data)
            {
                location.reload(true);
            }
        });
    });
});

export function onDocumentReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}
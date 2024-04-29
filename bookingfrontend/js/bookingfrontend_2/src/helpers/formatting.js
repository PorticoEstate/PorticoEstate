var dateformat_javascript = dateformat_backend.replace(/d/gi, "dd").replace(/m/gi, "mm").replace(/y/gi, "yyyy");

export function CreateUrlParams(params)
{
    var allParams = params.split("&");
    let urlParams = [];
    for (var i = 0; i < allParams.length; i++)
    {
        var splitParam = allParams[i].split("=");
        urlParams[splitParam[0]] = splitParam[1];
    }
    return urlParams;
}


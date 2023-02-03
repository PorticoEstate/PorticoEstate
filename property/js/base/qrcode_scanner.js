var html5QrcodeScanner;

function onScanSuccess(decodedText, decodedResult)
{
	// Handle on success condition with the decoded text or result.
	console.log(`Scan result: ${decodedText}`, decodedResult);

	document.getElementById("filter_location").value = decodedText;

	// ...
	html5QrcodeScanner.clear();
	// ^ this will stop the scanner (video feed) and clear the scan area.
}

function onScanError(errorMessage)
{
	// handle on error condition, with error message
}


const element = document.getElementById("filter_location");
element.addEventListener("click", function (event)
{
	if(!this.value)
	{
		init_scanner(this);
	}
}, {once: false});


init_scanner = function ()
{
	html5QrcodeScanner = new Html5QrcodeScanner(
		"reader_location", {fps: 10, qrbox: 250});
	html5QrcodeScanner.render(onScanSuccess, onScanError);

};
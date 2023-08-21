$(document).ready(function ()
{
	update_geolocation = function (location_id, component_id)
	{
		if (navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition((position) => {
				showPosition(position, location_id, component_id);
			}, showError);
		}
		else
		{
			alert("Geolocation is not supported by this browser.");
		}
	};

	function showPosition(position, location_id, component_id)
	{
		var latitude = position.coords.latitude;
		var longitude = position.coords.longitude;
		alert("Latitude : " + latitude + " Longitude: " + longitude);
		alert("location_id : " + location_id + " component_id: " + component_id);

		var coordinates = latitude + ',' + longitude;
		//	var url = "https://www.google.com.sa/maps/@" + coordinates + ",12.21z?hl=en";
		//	https://www.google.com.sa/maps/@61.3560914,15.4156856,10z?hl=en&entry=ttu

		//	window.open(url, '_blank');

		var attribution = new ol.control.Attribution({
			collapsible: false
		});

		var map = new ol.Map({
			controls: ol.control.defaults({attribution: false}).extend([attribution]),
			layers: [
				new ol.layer.Tile({
					source: new ol.source.OSM({
						url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
//						attributions: [
//							ol.source.OSM.ATTRIBUTION, 'Tiles courtesy of <a href="https://geo6.be/">GEO-6</a>'
//						],
						maxZoom: 18
					})
				})
			],
			target: 'map',
			view: new ol.View({
				center: ol.proj.fromLonLat([longitude, latitude]),
				maxZoom: 18,
				zoom: 12
			})
		});

		var layer = new ol.layer.Vector({
			source: new ol.source.Vector({
				features: [
					new ol.Feature({
						geometry: new ol.geom.Point(ol.proj.fromLonLat([longitude, latitude]))
					})
				]
			})
		});
		map.addLayer(layer);

		var container = document.getElementById('popup');
		var content = document.getElementById('popup-content');
		var closer = document.getElementById('popup-closer');

		var overlay = new ol.Overlay({
			element: container,
			autoPan: true,
			autoPanAnimation: {
				duration: 250
			}
		});
		map.addOverlay(overlay);

		closer.onclick = function ()
		{
			overlay.setPosition(undefined);
			closer.blur();
			return false;
		};

		map.on('singleclick', function (event)
		{
			if (map.hasFeatureAtPixel(event.pixel) === true)
			{
				var coordinate = event.coordinate;

				content.innerHTML = '<b>Hello world!</b><br />I am a popup.';
				overlay.setPosition(coordinate);
			}
			else
			{
				overlay.setPosition(undefined);
				closer.blur();
			}
		});

		content.innerHTML = '<b>Her står jeg.';
		overlay.setPosition(ol.proj.fromLonLat([longitude, latitude]));


	}

	function showError(error)
	{
		switch (error.code)
		{
			case error.PERMISSION_DENIED:
				alert("User denied the request for Geolocation.");
				break;
			case error.POSITION_UNAVAILABLE:
				alert("Location information is unavailable.");
				break;
			case error.TIMEOUT:
				alert("The request to get user location timed out.");
				break;
			case error.UNKNOWN_ERROR:
				alert("An unknown error occurred.");
				break;
		}
	}

	$("#choose-child-on-component").select2({
		placeholder: lang['Select'],
		language: "no",
		width: '75%'
	});

	$('#choose-child-on-component').on('select2:open', function (e)
	{

		$(".select2-search__field").each(function ()
		{
			if ($(this).attr("aria-controls") == 'select2-choose-child-on-component-results')
			{
				$(this)[0].focus();
			}
		});
	});


	// Display submit button on click
	$(".inspectors").on("click", function (e)
	{
		var check_list_id = $("#check_list_id").val();
		var check = $(this);
		var checked = check.prop("checked");
		var user_id = check.val();

		var oArgs = {
			menuaction: 'controller.uicheck_list.set_inspector',
			check_list_id: check_list_id,
			checked: checked == true ? 1 : 0,
			user_id: user_id,
		};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					var status = data.status;
					if (status === 'ok')
					{
//						alert('ok');
					}
				}
			}
		});

	});

	$("#categories").change(function ()
	{
		var check_list_id = $("#check_list_id").val();
		var cat_id = $(this).val();

		var oArgs = {
			menuaction: 'controller.uicheck_list.set_category',
			check_list_id: check_list_id,
			cat_id: cat_id,
		};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			url: requestUrl,
			success: function (data)
			{
				if (data)
				{
					var status = data.status;
					if (status === 'ok')
					{
//						alert('ok');
					}
				}
			}
		});

	});




	// ADD CHECKLIST
	$("#frm_add_check_list").on("submit", function (e)
	{
		var thisForm = $(this);
		var statusFieldVal = $("#status").val();
		var statusRow = $("#status");
		var plannedDateVal = $("#planned_date").val();
		var plannedDateRow = $("#planned_date");
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date");

		$(thisForm).find(".input_error_msg").remove();

		// Is COMPLETED DATE assigned when STATUS is done 
		if (statusFieldVal == 1 && completedDateVal == '')
		{
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
		}
		// Is COMPLETED DATE assigned when STATUS is not done
		else if (statusFieldVal == 0 && completedDateVal != '')
		{
			$("#status").val(1);
//			e.preventDefault();
			// Displays error message above completed date
//			$(statusRow).before("<div class='input_error_msg'>Du har angitt utførtdato, men status er Ikke utført. Vennligst endre status til utført</div>");
		}
	});

	// Display submit button on click
	$("#frm_add_check_list").on("click", function (e)
	{
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});



	// UPDATE CHECKLIST DETAILS	
	$("#frm_update_check_list").on("submit", function (e)
	{
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");

		var check_list_id = $("#check_list_id").val();

		var statusFieldVal = $("#status").val();

		// Cancelled
		if (statusFieldVal == 3 || statusFieldVal == 0)
		{
			$("#completed_date").val('');
		}
		var statusRow = $("#status");
		var plannedDateVal = $("#planned_date").val();
		var plannedDateRow = $("#planned_date");
		var completedDateVal = $("#completed_date").val();
		var completedDateRow = $("#completed_date");

		$(thisForm).find('.input_error_msg').remove();

		// Checks that COMPLETE DATE is set if status is set to DONE 
		if (statusFieldVal == 1 & completedDateVal == '')
		{
			e.preventDefault();
			// Displays error message above completed date
			$(completedDateRow).before("<div class='input_error_msg'>Vennligst angi når kontrollen ble utført</div>");
		}
		else if (statusFieldVal == 0 && completedDateVal != '')
		{
			$("#status").val(1);
//			e.preventDefault();
			// Displays error message above completed date
//			$(statusRow).before("<div class='input_error_msg'>Vennligst endre status til utført eller slett utførtdato</div>");
		}
		else if (statusFieldVal == 0 & plannedDateVal == '')
		{
			e.preventDefault();
			// Displays error message above planned date
			if (!$(plannedDateRow).prev().hasClass("input_error_msg"))
			{
				$(plannedDateRow).before("<div class='input_error_msg'>Vennligst endre status for kontroll eller angi planlagtdato</div>");
			}
		}
	});
});

function getWidth()
{
	return Math.max(
		document.body.scrollWidth,
		document.documentElement.scrollWidth,
		document.body.offsetWidth,
		document.documentElement.offsetWidth,
		document.documentElement.clientWidth
		);
}
this.fileuploader = function ()
{
	var sUrl = phpGWLink('index.php', multi_upload_parans);
	var width = Math.min(Math.floor(getWidth() * 0.9), 750);
	TINY.box.show({iframe: sUrl, boxid: 'frameless', width: width, height: 450, fixed: false, maskid: 'darkmask', maskopacity: 40, mask: true, animate: true,
		close: true,
		closejs: function ()
		{
			refresh_files()
		}
	});
};

this.refresh_files = function ()
{
	JqueryPortico.updateinlineTableHelper('datatable-container_0');
};

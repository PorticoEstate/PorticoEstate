	var pageLayout;

	$(document).ready(function(){
		// create page layout
		pageLayout = $('body').layout({
			stateManagement__enabled:	true
		,	defaults: {
			}
		,	north: {
				size:					"auto"
			,	spacing_open:			0
			,	closable:				false
			,	resizable:				false
			}
		,	west: {
				size:					250
			,	spacing_closed:			22
			,	togglerLength_closed:	140
			,	togglerAlign_closed:	"top"
			,	togglerContent_closed:	"C<BR>o<BR>n<BR>t<BR>e<BR>n<BR>t<BR>s"
			,	togglerTip_closed:		"Open & Pin Contents"
			,	sliderTip:				"Slide Open Contents"
			,	slideTrigger_open:		"mouseover"
			,	initClosed:				true
			}
		,	south: {
			maxSize:				200
		,	spacing_closed:			0			// HIDE resizer & toggler when 'closed'
		,	slidable:				false		// REFERENCE - cannot slide if spacing_closed = 0
		,	initClosed:				false
			}

		});

		$('#collapseNavbar').on('click', function () {
			$(this).attr('href', 'javascript:;');
			$('#navbar').jstree('close_all');
		})

		$('#expandNavbar').on('click', function () {
			$(this).attr('href', 'javascript:;');
			$('#navbar').jstree('open_all');
		});

	});
$(document).ready(function(){

//	$("body").layout(
//		{
//			applyDemoStyles:			true,
//			east__size:					350,
//			east__fxSpeed:				100,
//			livePaneResizing:			true,
//			animatePaneSizing:			true,
//			stateManagement__enabled:	true
// });



$(document).ready(function () {
	$('body').layout({ applyDemoStyles: true });
});

	var api = oTable0.api();
	api.on( 'draw', sum_columns );

});


	function sum_columns()
	{
		var api = oTable0.api();
		var data = api.ajax.json().data;
		var amount = 0;
		var approved_amount = 0;
		var intVal = function ( i )
		{
			return typeof i === 'string' ?
				i.replace(/[\$,]/g, '')*1 :
				typeof i === 'number' ?
					i : 0;
		};

		for ( var i=0 ; i < data.length ; i++ )
		{
			amount += intVal(data[i]['amount']);
			approved_amount += intVal(data[i]['approved_amount_hidden']);
		}
		amount = $.number( amount, 2, ',', '' );
		approved_amount = $.number( approved_amount, 2, ',', '' );
		$(api.column(0).footer()).html("Sum:");
		$(api.column(2).footer()).html("<div align=\"right\">"+amount+"</div>");
		$(api.column(3).footer()).html("<div align=\"right\">"+approved_amount+"</div>");
	}

var arURLParts = strBaseURL.split('?');
var comboBase = arURLParts[0] + 'phpgwapi/inc/yui-combo-master/combo.php?';

YUI_config = {
    //Don't combine the files
    combine: true,
    //Ignore things that are already loaded (in this process)
    ignoreRegistered: false,
    //Set the base path
	comboBase: comboBase,
    base: '',
    //And the root
    root: '',
    //Require your deps
    require: [ ]
};


//YUI({
//  classNamePrefix: 'pure'
//}).use(
//	'gallery-sm-menu',
//		function(Y) {
//                Y.on("domready", function () {
//                   var horizontalMenu = new Y.Menu(
//                        {
//							container         : '#horizontal-menu',
//							sourceNode        : '#std-menu-items',
//							orientation       : 'horizontal',
//							hideOnOutsideClick: false,
//							hideOnClick       : false
//                        }
//                    );
//					horizontalMenu.render();
//					horizontalMenu.show();
//                });
//});

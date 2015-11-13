$(function() {

	var HomeUrl = phpGWLink('home.php', {}, false);

//	The menu
	$('#menu').mmenu({
		extensions	: [ 'effect-slide-menu', 'pageshadow' ],
		searchfield	: true,
		counters	: true,
		header			: {
			add		: true,
			update	: true
		},
		navbar 		: {
	//		title		: 'Advanced menu'
		},
		navbars		: [
			{
				position	: 'top',
				content		: [ 'searchfield' ]
			},
			{
				position	: 'top',
				content		: [
					'prev',
					'title',
					'close'
				]
			},
			{
				position	: 'bottom',
				content		: [
					'<a href="' + HomeUrl + '">Home</a>'
				]
			}
		]
	});





	//	Collapse tablerows
//	$('.table-collapsed')
//		.find( '.sub-start' )
//		.each(
//			function()
//			{
//				var $parent = $(this).prev().find( 'td' ).eq( 1 ).addClass( 'toggle' ),
//					$args = $parent.find( 'span' ),
//					$subs = $(this);
//
//				var searching = true;
//				$(this).nextAll().each(
//					function()
//					{
//						if ( searching )
//						{
//							$subs = $subs.add( this );
//							if ( !$(this).is( '.sub' ) )
//							{
//								searching = false;
//							}
//						}
//					}
//				);
//				$subs.hide();
//				$parent.click(
//					function()
//					{
//						$args.toggle();
//						$subs.toggle();
//					}
//				);
//			}
//		);
});

$(document).ready(function(){

	$('input[name="update_bookmark_menu"]').click( function( e ) {
		e.preventDefault();
		var id = $(this).attr('id');
		var checked =  $(this).is(':checked');
		var oArgs = {menuaction:'phpgwapi.menu.update_bookmark_menu', bookmark_candidate:$(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			  type: 'POST',
			  url: requestUrl,
			  dataType: 'json',
			  success: function(data) {
				  if(data)
				  {
					  alert(data.status);
				  }
			  }
		   });
		   setTimeout(function() { $('#' + id).prop('checked', checked); }, 1);
	});


});	

function update_bookmark_menu(bookmark_candidate){
	var oArgs = {menuaction:'phpgwapi.menu.update_bookmark_menu', bookmark_candidate:bookmark_candidate};
	var requestUrl = phpGWLink('index.php', oArgs, true);

	$.ajax({
		  type: 'POST',
		  url: requestUrl,
		  dataType: 'json',
		  success: function(data) {
			  if(data)
			  {
				  alert(data.status);
			  }
		  }
	   });
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


YUI({
  classNamePrefix: 'pure'
}).use(
	'gallery-sm-menu',
		function(Y) {
                Y.on("domready", function () {
                   var horizontalMenu = new Y.Menu(
                        {
							container         : '#horizontal-menu',
							sourceNode        : '#std-menu-items',
							orientation       : 'horizontal',
							hideOnOutsideClick: false,
							hideOnClick       : false
                        }
                    );
					horizontalMenu.render();
					horizontalMenu.show();
                });
});

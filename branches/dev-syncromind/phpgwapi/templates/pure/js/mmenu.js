$(function() {

	//	The menu
	$('#menu')
		.mmenu({
			classes			: 'mm-white',
			searchfield		: true,
			counters		: true,
			header			: {
				add		: true,
				update	: true
			//	title	: 'mmenu'
			}
		});


	//	Collapse tablerows
	$('.table-collapsed')
		.find( '.sub-start' )
		.each(
			function()
			{
				var $parent = $(this).prev().find( 'td' ).eq( 1 ).addClass( 'toggle' ),
					$args = $parent.find( 'span' ),
					$subs = $(this);
	
				var searching = true;
				$(this).nextAll().each(
					function()
					{
						if ( searching )
						{
							$subs = $subs.add( this );
							if ( !$(this).is( '.sub' ) )
							{
								searching = false;
							}
						}
					}
				);
				$subs.hide();
				$parent.click(
					function()
					{
						$args.toggle();
						$subs.toggle();
					}
				);
			}
		);

});	

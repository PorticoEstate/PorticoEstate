						</div>
					</div>
				</div>
				<div class="split-bar split-bar-c-e">
					<div class="split-bar-handle"></div>
				</div>

				<div class="layout-east">
					<div class="panel">
						<div class="header">
							<h2>Widgetpanel</h2>
						</div>
						<div class="body" id="debug">

						</div>
					</div>
				</div>

				<div class="layout-south">
					<div class="panel">
						<div class="header">
							<div class="button-bar">
								{powered_by}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	<script type="text/javascript">
		//FIXME: move to external js file

		//FIXME: should create own class for this
		//YAHOO.namespace("OFM");
/*
		var dd1 = new YAHOO.util.DD("split-bar");
		dd1.setYConstraint(0,0,0);
		//dd1.setXConstraint(50,100,1);
		// Have a look at this for left / right constraints
		// http://developer.yahoo.com/yui/examples/dragdrop/dd-region.html

		var layout_west = document.getElementById('layout-west');
		var layout_center = document.getElementById('layout-center');


		dd1.endDrag = function(e ) {
	 		var x = dd1.getEl().offsetLeft;
            var new_x = Math.max(x, 100);

            layout_west.style.width=new_x + 'px';

			dd1.getEl().style.left = new_x + 'px';

			new_x +=10;
			layout_center.style.marginLeft= new_x + 'px';


	    }
*/
		var toolbars = YAHOO.util.Dom.getElementsByClassName( "toolbar" , "div" );

		for(var toolbar=0;toolbar<toolbars.length;toolbar++)
		{

			var buttons = toolbars[toolbar].getElementsByTagName("a");
			var menus = toolbars[toolbar].getElementsByTagName("form");

			for(var button=0;button<buttons.length;button++)
			{
					new YAHOO.widget.Button(buttons[button]);
			}
			for(var menu=0;menu<menus.length;menu++)
			{
				//FIXME: class can contain several classes
				//alert(menus[menu].className.split(" "));
				if(menus[menu].className == "menu")
				{
					var submit = menus[menu].getElementsByTagName("input")[0];
					var select = menus[menu].getElementsByTagName("select")[0];
					var label = menus[menu].title || submit.value;

					if(select.value)
					{
						label += ": " + select.options[select.selectedIndex].innerHTML;
					}

					new YAHOO.widget.Button(submit, { type: "menu", menu: select, label: label });
				}
			}
		}
	</script>
	</body>
</html>

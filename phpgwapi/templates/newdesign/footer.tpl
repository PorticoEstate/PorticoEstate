						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		//FIXME: should create own class for this
		//YAHOO.namespace("OFM");

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
	</script>
	</body>
</html>

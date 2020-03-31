			<div class="ui-layout-south">
					<div class="body">
						<div class="button-bar">
							{powered_by}
						</div>
					</div>
				</div>
			</div>
		<div id="popupBox"></div>	
		<div id="curtain"></div>
		{javascript_end}
		<script>
			/**
			 * Disable doubleklick on links
			 */
			$("a").click(function (event)
			{
				if ($(this).hasClass("disabledouble"))
				{
					event.preventDefault();
					var link = $(this);
					window.setTimeout(function ()
					{
						$(link).removeClass("disabledouble");
					}, 500);
				}

				$(this).addClass("disabledouble");
			});

		</script>
	</body>
</html>

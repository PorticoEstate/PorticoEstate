
			</div>
		</div>

		<footer class="page-footer font-small text-center fixed-bottom bg-light text-gray border-top border-gray-light">

				<p>{powered_by}</p>
		</footer>
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

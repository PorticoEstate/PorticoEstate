
    </main>
	<footer class="py-4 bg-light mt-auto">
		 <div class="container-fluid px-4">
				<div class="text-gray text-center">{site_title}</div>
		 </div>
	 </footer>



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


			</div>
		</div>

		<footer class="page-footer font-small text-center fixed-bottom bg-light text-gray border-top border-gray-light">

				<p>{powered_by}</p>
		</footer>
	</div>

	<div id="popupBox"></div>
	<div id="curtain"></div>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{logout_text}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{logout_url}">{logout_text}</a>
                </div>
            </div>
        </div>
    </div>

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

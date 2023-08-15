
						</div>
					</div>
				</div>
			</main>

			<!--footer class="font-small text-center fixed-bottom bg-light text-gray border-top border-gray-light">
					<p>{powered_by}</p>
			</footer-->

			<footer class="py-4 bg-light mt-auto">
				 <div class="container-fluid px-4">
						<div class="text-gray text-center">{powered_by}</div>
				 </div>
			 </footer>
		</div>
	</div>

	<div id="popupBox"></div>
	<div id="curtain"></div>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">{logout_text}</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">{lang_logout_header}</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{logout_url}">{logout_text}</a>
                </div>
            </div>
        </div>
    </div>

	<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header bg-dark">
					<button class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<iframe id="iframepopupModal" src="about:blank" width="100%" height="380" frameborder="0" sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-top-navigation"
							allowtransparency="true"></iframe>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->

	{javascript_end}
	<script>
		$('[data-bs-target="#popupModal"]').on('click', function (e) {
			e.preventDefault();

			var _linky = $(this).attr('href');

			const urlParams = new URLSearchParams(_linky);

			if(urlParams.has('height'))
			{
				const height = urlParams.get('height');
				$("#iframepopupModal").attr('height', height);
			}

			$('#popupModal').on('shown.bs.modal', function (e)
			{
				$("#iframepopupModal").attr("src", _linky);
			});

			$('#popupModal').on('hidden.bs.modal', function (e)
			{
				$("#iframepopupModal").attr("src", 'about:blank');
			});
			
		});

		/**
		* Disable doubleklick on links
		*/
		$("a").click(function (event)
		{
			var link = $(this);
			if ($(this).hasClass("disabledouble"))
			{
				event.preventDefault();
			}

			$(this).addClass("disabledouble");
			window.setTimeout(function ()
			{
				$(link).removeClass("disabledouble");
			}, 1000);
		});

	$('#sidebarToggle').on('click', function ()
	{
		document.body.classList.toggle('sb-sidenav-toggled');

		var oArgs = {menuaction: 'phpgwapi.template_portico.store', location: 'menu_state'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var state_data = {menu_state: document.body.classList.contains('sb-sidenav-toggled')};


		$.ajax({
			type: 'POST',
			url: requestUrl,
			data: {data: JSON.stringify(state_data)},
			dataType: "json",
			success: function (data)
			{
				if (data)
				{
					console.log(data);
				}
			}
		});
	});


	</script>

</body>
</html>

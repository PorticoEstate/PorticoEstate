
			</div>
		</div>

		<footer class="page-footer font-small text-center fixed-bottom bg-light text-gray border-top border-gray-light">

				<p>{powered_by}</p>
		</footer>
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
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">{lang_logout_header}</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{logout_url}">{logout_text}</a>
                </div>
            </div>
        </div>
    </div>

	<div class="modal fade" id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<iframe src="about:blank" width="100%" height="380" frameborder="0" sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-top-navigation"
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
			var _target = $(this).data('target');
			var $target = $(_target);

			const urlParams = new URLSearchParams(_linky);

			if(urlParams.has('height'))
			{
				const height = urlParams.get('height');
				$target.find('iframe').attr('height', height);
			}

			$('#popupModal').on('show.bs.modal', function (e)
			{
				$target.find('iframe').attr("src", _linky);
			});

			$('#popupModal').on('hidden.bs.modal', function (e)
			{
				$target.find('iframe').attr('src', 'about:blank');
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

	</script>

</body>
</html>

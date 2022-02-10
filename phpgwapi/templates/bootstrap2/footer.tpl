
                    <!-- 404 Error Text -->

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>{powered_by}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

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
                    <a class="btn btn-primary" href="{logout_url}">Logout</a>
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
		</div>
	</div>

{javascript_end}

	<script>
		$('[data-target="#popupModal"]').on('click', function (e) {
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

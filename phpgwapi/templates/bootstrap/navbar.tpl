<body>

	<script>
		function logout()
		{
			if (typeof (Storage) !== "undefined")
			{
				sessionStorage.cached_menu_tree_data = '';
				localStorage.clear();
			}
			var sUrl = phpGWLink('logout.php');
			window.open(sUrl, '_self');
		}

	</script>

	<div class="wrapper">
		<!-- Sidebar  -->
		<nav id="sidebar">
			<div class="sidebar-header">
                <h5>{user_fullname}</h5>
            </div>
			<div class="sidebar-sticky">
				{treemenu}
			</div>
		</nav>

		<!-- Page Content  -->
        <div id="content">
            <!--nav class="navbar navbar-expand-lg navbar-light bg-light"-->
			<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">

				<div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>Toggle Sidebar</span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>

					<!-- Brand -->
					<a class="navbar-brand" href="#">{site_title}</a>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
						{topmenu}
					</div>
				</div>
			</nav>

			<h1 id="top">{current_app_title}</h1>




<body>
	<script>
		{support_request}
	</script>
	<div class="wrapper">
		<!-- Sidebar  -->
		<nav id="sidebar" class="{menu_state}">
			<div class="sidebar-header">
                <h1>{user_fullname}</h1>
            </div>
			<div class="sidebar-sticky">
				{treemenu}
			</div>
		</nav>

		<!-- Page Content  -->
        <div id="page_content">
			<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">

				<div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>Sidemeny</span>
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
			{breadcrumb}

			<h1 id="top">{current_app_title}</h1>

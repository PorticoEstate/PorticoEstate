<body>
	<div class="wrapper">
		<!-- Start sidebar  -->

		{sidebar}

		<!-- Page Content  -->
        <div id="page_content">
			<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">

				<div class="container-fluid">
 					{sidebar_button}
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
			{landing}

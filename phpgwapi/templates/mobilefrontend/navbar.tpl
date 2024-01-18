<body>

    <header class="bg-dark text-white p-2">
        <a class="navbar-brand" href="{home_url}">{site_title} / {user_fullname}</a>
        <!-- Top Menu -->
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
			 {topmenu}
            </div>
        </nav>
    </header>

	{sidebar_button}

    <!-- Offcanvas Sidebar -->
	{sidebar}

   <!-- Main Content -->
    <main role="main" class="container-fluid">
       {breadcrumb}

		<h1 id="top">{current_app_title}</h1>
			{landing}

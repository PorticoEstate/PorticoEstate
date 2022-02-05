<body id="page-top">

	<!-- Page Wrapper -->
	<div id="wrapper">

		<!-- Sidebar -->
		<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

			<!-- Sidebar - Brand -->
			<a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
				<div class="sidebar-brand-icon">
					<img src="{img_site_logo}" alt="{site_title}" width="50">
				</div>
				<div class="sidebar-brand-text mr-3"><img src="{img_site_cover}" alt="{site_title}" width="140"></div>
			</a>

			<!-- Divider -->
			<hr class="sidebar-divider my-0">

			<!-- Nav Item - Dashboard -->
			<li class="nav-item active">
				<a class="nav-link" href="{home_url}">
					<i class="fas fa-fw fa-tachometer-alt"></i>
					<span>Dashboard</span></a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider">

			<!-- Heading -->
			<div class="sidebar-heading">
				Boligforvaltning
			</div>

			{treemenu}

			<!-- Nav Item - Pages Collapse Menu -->
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
				   aria-expanded="true" aria-controls="collapseTwo">
					<i class="fas fa-fw fa-cog"></i>
					<span>Administrasjon</span>
				</a>
				<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Admin for utleieobjekt</h6>
						<a class="collapse-item" href="buttons.html">Bygninger</a>
						<a class="collapse-item" href="cards.html">Leiligheter</a>
					</div>
				</div>
			</li>

			<!-- Nav Item - Utilities Collapse Menu -->
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
				   aria-expanded="true" aria-controls="collapseUtilities">
					<i class="fas fa-building"></i>
					<span>Eiendom</span>
				</a>
				<div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Eiendomsforvaltning:</h6>
						<a class="collapse-item" href="utilities-color.html">Lokasjon</a>
						<a class="collapse-item" href="utilities-border.html">Meldinger</a>
						<a class="collapse-item" href="utilities-animation.html">Rapport</a>
						<a class="collapse-item" href="utilities-other.html">Prosjekt</a>
					</div>
				</div>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider">

			<!-- Heading -->
			<div class="sidebar-heading">
				Addons
			</div>

			<!-- Nav Item - Pages Collapse Menu -->
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
				   aria-expanded="true" aria-controls="collapsePages">
					<i class="fas fa-fw fa-folder"></i>
					<span>Pages</span>
				</a>
				<div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Login Screens:</h6>
						<a class="collapse-item" href="login.html">Login</a>
						<a class="collapse-item" href="register.html">Register</a>
						<a class="collapse-item" href="forgot-password.html">Forgot Password</a>
						<div class="collapse-divider"></div>
						<h6 class="collapse-header">Other Pages:</h6>
						<a class="collapse-item" href="404.html">404 Page</a>
						<a class="collapse-item" href="blank.html">Blank Page</a>
					</div>
				</div>
			</li>

			<!-- Nav Item - Charts -->
			<li class="nav-item">
				<a class="nav-link" href="charts.html">
					<i class="fas fa-fw fa-chart-area"></i>
					<span>Charts</span></a>
			</li>

			<!-- Nav Item - Tables -->
			<li class="nav-item">
				<a class="nav-link" href="tables.html">
					<i class="fas fa-fw fa-table"></i>
					<span>Tables</span></a>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider d-none d-md-block">

			<!-- Sidebar Toggler (Sidebar) -->
			<div class="text-center d-none d-md-inline">
				<button class="rounded-circle border-0" id="sidebarToggle"></button>
			</div>

			<!-- Sidebar Message -->
			<div class="sidebar-card">
				<img class="sidebar-card-illustration mb-2" src="{img_undraw_rocket}" alt="">
				<p class="text-center mb-2"><strong>BkBygg</strong> er nå lansert i en ny forbedret utgave.</p>
				<a class="btn btn-success btn-sm" href="https://github.com/startbootstrap/startbootstrap-sb-admin-2" target="_blank">Les mer her</a>
			</div>

		</ul>
		<!-- End of Sidebar -->

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">

			<!-- Main Content -->
			<div id="content">

				<!-- Topbar -->
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

					<!-- Sidebar Toggle (Topbar) -->
					<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
						<i class="fa fa-bars"></i>
					</button>

					{topbar}


				</nav>
				<!-- End of Topbar -->

				<!-- Begin Page Content -->
				<div class="container-fluid">
					{breadcrumb}
					<!-- Page Heading -->
					<h1 class="h3 mb-2 text-gray-800">{current_app_title}</h1>


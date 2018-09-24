 <!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
        <!-- BEGIN stylesheet -->
        <link href="{stylesheet_uri}" type="text/css" rel="StyleSheet">
        <!-- END stylesheet -->
    		<!-- BEGIN javascript -->
		<script type="text/javascript" src="{javascript_uri}"></script>
		<!-- END javascript -->

		<script type="text/javascript">
			<!--
				var strBaseURL = '{str_base_url}';
			//-->
		</script>
     </head>
    <body>

         <nav class="navbar navbar-expand-md bg-light navbar-light fixed-top">
            <!-- Brand -->
            <a href="{site_url}"><img class="navbar-brand brand-site-img" src="{logoimg}" width="40"/></a>
            <a class="navbar-brand brand-site-title" href="{site_url}">{site_title}</a>

            <!-- Toggler/collapsibe Button -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
              <ul class="navbar-nav navbar-search d-none">
                  <li class="nav-item">
                      <form>
                      <div class="input-group mb-3">
                        <input class="form-control" type="text" placeholder="Søk sted, hall, aktivitet, utstyr" />
                          <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                          </div>
                      </div>
                      </form>
                  </li>
              </ul>
              <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                  <a class="nav-link" href="#">Om tjenesten</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{manual_url}">{manual_text}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="{login_url}"><strong>{login_text}</strong></a>
                </li>
              </ul>
            </div>
          </nav> 
                
         <div class="overlay">
            <div id="loading-img"><i class="fas fa-spinner fa-spin fa-3x"></i></div>
        </div>
                
        <div class="showMe" style="display: none;">
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
        <!--<nav class="navbar navbar-default">
            <div class="container-fluid">
              <div class="navbar-header">
                  <img class="navbar-left" src="https://kgv.doffin.no/SupplierLogoTypes/2165/Logo-redusert.jpg" width="50" height="50" />
                  <a class="navbar-brand" href="#"><span>Bergen</span><span>Kommune</span></a>
              </div>
              <ul class="nav navbar-nav pull-right">
                <li><a href="#"></a></li>
                <li><a href="#"></a></li>
                <li><a href="#" class="loginLink"></a></li>
              </ul>
            </div>
        </nav>-->

        
         <nav class="navbar navbar-expand-md bg-light navbar-light fixed-top">
            <!-- Brand -->
            <img class="navbar-brand" src="{logoimg}" width="50" height="57"/>
            <a class="navbar-brand" href="{site_url}"><span>Aktiv Kommune</span><span>Bergen</span></a>

            <!-- Toggler/collapsibe Button -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
              <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                  <a class="nav-link" href="#">Om tjenesten</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Brukerveiledning</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary" href="{login_url}"><strong>Logg inn</strong></a>
                </li>
              </ul>
            </div>
          </nav> 
                
         <div class="overlay">
            <div id="loading-img"><i class="fas fa-spinner fa-spin fa-3x"></i></div>
        </div>
                
        <div class="showMe" style="display: none;">
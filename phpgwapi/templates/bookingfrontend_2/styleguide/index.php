<html lang="en">
  <head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="../../../../phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/base/css/fontawesome/css/all.min.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/bookingfrontend/css/normalize.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/bookingfrontend/css/rubik-font.css" type="text/css" rel="StyleSheet">
    <link href="../../../js/select2/css/select2.min.css" rel="stylesheet" />
    <link href="../../../../phpgwapi/js/jquery/css/redmond/jquery-ui.min.css?n=621960364497" type="text/css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans" type="text/css" rel="StyleSheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

 
    <script src="../../../../phpgwapi/js/jquery/js/jquery-3.7.1.min.js"></script>
    <script src="../../../../phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../../../phpgwapi/js/jquery/ui/jquery-ui-1.13.2.min.js"></script>
    <script src="../../../js/select2/js/select2.min.js"></script>
    <script src="js/main.js?v=<?php echo uniqid(); ?>"></script>

    <link href="main.css?v=<?php echo uniqid(); ?>" type="text/css" rel="StyleSheet">

    <title>Styleguide - Aktiv kommune</title>
  </head>
  <body>
    <div class="container-fluid container-lg">
      <h1 id="home">STYLEGUIDE</h1>

      <?php
        $componentLinks = array(
          'Typography' => array(
            'name' => 'Typografi',
            'folder' => 'typography',
            'filename' => 'typography',
          ),
          'Colors' => array(
            'name' => 'Farger',
            'folder' => 'colors',
            'filename' => 'colors',
          ),
          'Effects' => array(
            'name' => 'Effekter',
            'folder' => 'effects',
            'filename' => 'effects',
          ),
          'Button' => array(
            'name' => 'Knapper',
            'folder' => 'button',
            'filename' => 'button',
          ),
          'Pill' => array(
            'name' => 'Piller',
            'folder' => 'pill',
            'filename' => 'pill',
          ),
          'Link' => array(
            'name' => 'Lenke',
            'folder' => 'link',
            'filename' => 'link',
          ),
          'Dropdown' => array(
            'name' => 'Nedtrekksmeny',
            'folder' => 'dropdown',
            'filename' => 'dropdown',
          ),
          'Input' => array(
            'name' => 'Input',
            'folder' => 'input',
            'filename' => 'input',
          ),
          'Datovelger' => array(
            'name' => 'Datovelger',
            'folder' => 'datepicker',
            'filename' => 'datepicker',
          ),
          'Menu' => array(
            'name' => 'Meny',
            'folder' => 'menu',
            'filename' => 'menu',
          ),
          'Modal' => array(
            'name' => 'Modal',
            'folder' => 'modal',
            'filename' => 'modal',
          ),
          'Filter' => array(
            'name' => 'Filter',
            'folder' => 'filter',
            'filename' => 'filter',
          ),
          'Status' => array(
            'name' => 'Status',
            'folder' => 'status',
            'filename' => 'status',
          ),
          'Navbar' => array(
            'name' => 'Navigasjon',
            'folder' => 'navbar',
            'filename' => 'navbar',
          ),
          'MultiSearch' => array(
            'name' => 'Flervalgssøk',
            'folder' => 'multi-search',
            'filename' => 'multi-search',
          ),
          'Search result' => array(
            'name' => 'Søkeresultat',
            'folder' => 'search-result',
            'filename' => 'search-result',
          ),
          'Shortcut' => array(
            'name' => 'Snarvei',
            'folder' => 'shortcut',
            'filename' => 'shortcut',
          ),
          'Footer' => array(
            'name' => 'Footer',
            'folder' => 'footer',
            'filename' => 'footer',
          ),
        );

        echo '<ul class="list-unstyled d-flex flex-wrap">';
          foreach ($componentLinks as $key => $componentLink) {
            echo '<li><a href="#'.$key.'" class="me-4">'.$componentLink['name'].'</a></li>';
          }
        echo '</ul>';

        foreach ($componentLinks as $key => $componentLink) {
          echo '<span id="'.$key.'"></span>';
          
          if ($key !== array_key_first($componentLinks)) {
            echo '<div class="d-flex justify-content-between mt-5 border-bottom border-2">
                    <a href="#home">Til toppen</a>
                    <span>'.$componentLink['name'].'</span>
                  </div>';
          } else {
            echo '<div class="d-flex justify-content-end mt-5 border-bottom border-2">
                    <span>'.$componentLink['name'].'</span>
                  </div>';
          } 

          include $componentLink['folder'].'/'.$componentLink['filename'].'.php';
        }
      ?>
    </div>
  </body>
</html>
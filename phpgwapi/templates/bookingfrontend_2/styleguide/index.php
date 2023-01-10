<html>
  <head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="../../../../phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/base/css/fontawesome/css/all.min.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css" type="text/css" rel="StyleSheet">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans" type="text/css" rel="StyleSheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="../../../../phpgwapi/templates/bookingfrontend/css/normalize.css" type="text/css" rel="StyleSheet">
    <link href="../../../../phpgwapi/templates/bookingfrontend/css/rubik-font.css" type="text/css" rel="StyleSheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="../../../js/select2/css/select2.min.css" rel="stylesheet" />
    <script src="../../../js/select2/js/select2.min.js"></script>

    <link href="main.css" type="text/css" rel="StyleSheet">

    <title>Styleguide - Aktiv kommune</title>
  </head>
  <body>
    <div class="container">
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
          'Icons' => array(
            'name' => 'Ikoner',
            'folder' => 'icons',
            'filename' => 'icons',
          ),
          'Button' => array(
            'name' => 'Knapper',
            'folder' => 'button',
            'filename' => 'button',
          ),

          'Link' => array(
            'name' => 'Link',
            'folder' => 'link',
            'filename' => 'link',
          ),
          'Input' => array(
            'name' => 'Input',
            'folder' => 'input',
            'filename' => 'input',
          ),
          'Dropdown' => array(
            'name' => 'Nedtrekksmeny',
            'folder' => 'dropdown',
            'filename' => 'dropdown',
          ),
        );

        echo '<ul class="list-unstyled d-flex">';
          foreach ($componentLinks as $key => $componentLink) {
            echo '<li><a href="#'.$key.'" class="me-4">'.$componentLink['name'].'</a></li>';
          }
        echo '</ul>';

        foreach ($componentLinks as $key => $componentLink) {
          echo '<span id="'.$key.'"></span>';
          
          if ($key !== array_key_first($componentLinks)) {
            echo '<div class="d-flex justify-content-between mt-5">
                    <a href="#home">Til toppen</a>
                    <span>'.$componentLink['name'].'</span>
                  </div>';
          } else {
            echo '<div class="d-flex justify-content-end mt-5">
                    <span>'.$componentLink['name'].'</span>
                  </div>';
          } 

          include $componentLink['folder'].'/'.$componentLink['filename'].'.php';

        }
      ?>
    </div>
  </body>
</html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/portico/phpgwapi/js/bootstrap5/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" type="text/css" rel="StyleSheet">
    <link href="/portico/phpgwapi/templates/base/css/fontawesome/css/all.min.css" type="text/css" rel="StyleSheet">
    <link href="/portico/phpgwapi/templates/bookingfrontend/css/jquery.autocompleter.css" type="text/css" rel="StyleSheet">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans" type="text/css" rel="StyleSheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="/portico/phpgwapi/templates/bookingfrontend/css/normalize.css" type="text/css" rel="StyleSheet">
    <link href="/portico/phpgwapi/templates/bookingfrontend/css/rubik-font.css" type="text/css" rel="StyleSheet">
    <link href="main.css" type="text/css" rel="StyleSheet">

    <title>Styleguide - Aktiv kommune</title>
  </head>
  <body>
    <div class="container">
      <h1 id="home">STYLEGUIDE</h1>

      <?php
        $sectionLinks = array(
          'Typography', 'Colors', 'Effects', 'Icons', 'Button', 'Link', 'Input'
        );

        echo '<ul class="list-unstyled d-flex">';
        foreach ($sectionLinks as $sectionLink) {
         echo '<li><a href="#'.$sectionLink.'" class="me-4">'.$sectionLink.'</a></li>';
        }
        echo '</ul>';

        echo '<span id="Typography"></span>';
        include 'typography/typography.php';
        echo '<span id="Colors"></span>';
        include 'colors/colors.php';
        echo '<span id="Effects"></span>';
        include 'effects/effects.php';
        echo '<span id="Icons"></span>';
        include 'icons/icons.php';
        echo '<span id="Button"></span>';
        include 'button/button.php';
        echo '<span id="Link"></span>';
        include 'link/link.php';
        echo '<span id="Input"></span>';
        include 'input/input.php';
      ?>
    </div>
  </body>
</html>
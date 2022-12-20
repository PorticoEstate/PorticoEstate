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
      <ul class="list-unstyled d-flex">
        <li>
            <a href="#typography" class="pe-4">Typography</a>
        <li>
        <li>
            <a href="#colors">Colors</a>
        <li>
      </ul>
      <?php
        echo '<span id="typography"></span>';
        include 'typography/typography.php';
        echo '<span id="colors"></span>';
        include 'colors/colors.php';
      ?>
    </div>
  </body>
</html>
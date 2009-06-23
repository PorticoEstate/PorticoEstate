<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Bergen kommune leiemodul</title>
    <? include('includes/head.inc.php') ?>
</head>

<body class="yui-skin-sam">
  <? include('includes/sitetitle.inc.php') ?>
  <? include('includes/menumarkup.inc.php') ?>

  <!--- Content area -->
  <div id="center1">
    <img src="images/content/dashboard.png" alt="Forside" width="710" height="673" border="0" usemap="#map" />

    <map name="map">
      <area shape="rect" coords="663,1,708,49" href="redigere_forside.php" />
      <area shape="rect" coords="2,62,709,396" href="kontrakter.php" />
      <area shape="rect" coords="2,401,304,539" href="leieobjekter.php" />
    </map>
  </div>
  <!-- End content area -->

  <? include('includes/layoutscript.inc.php') ?>
</body>
</html>

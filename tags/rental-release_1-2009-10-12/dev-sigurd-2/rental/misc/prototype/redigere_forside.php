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
    <img src="images/content/dashboard-edit.png" alt="Rediger forside" border="0" usemap="#map" />

    <map name="map">
      <area shape="rect" coords="619,1,665,44" href="index.php" />
      <area shape="rect" coords="667,0,709,44" href="javascript:if (confirm('Vil du virkelig lagre endringene?')) { location.href='index.php'; }" />
    </map>
  </div>
  <!-- End content area -->

  <? include('includes/layoutscript.inc.php') ?>
</body>
</html>


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
    <script type="text/javascript" src="js/data.js"></script>
    <script type="text/javascript" src="js/tablehelper.js"></script>

    <script type="text/javascript">

    formatLink = function(elCell, oRecord, oColumn, oData) { 
        var id = oRecord.getData('id');
        var name = oRecord.getData('from');
        elCell.innerHTML = "<a href=\"application.php?id=" + id + "\">" + name + "</a>"; 
    }; 

    setupPage = function() {
        var columnDefs = [{key:"from", label: 'From', sortable: true, formatter: formatLink},
                          {key:"org", label: 'Organisation', sortable: true},
                          {key:"date", label: 'Date', sortable: true},
                          {key:"status", label: 'Status', sortable: true}];
        var myDataSource = createDataSource(YAHOO.booking.Data.applications, columnDefs);
        var myDataTable = new YAHOO.widget.DataTable('applications',
                columnDefs, myDataSource, {});
        var columnDefs = [{key:"name", label: 'Name', sortable: true},
                          {key:"date", label: 'Date', sortable: true},
                          {key:"time", label: 'Time', sortable: true}];
        var myDataSource = createDataSource(YAHOO.booking.Data.cancelledbookings, columnDefs);
        var myDataTable = new YAHOO.widget.DataTable('cancelledbookings',
                columnDefs, myDataSource, {});
    };

    YAHOO.util.Event.addListener(window, "load", setupPage);
    </script>

    <h2>Recent applications</h2>
    <div id="applications"></div>

    <h2>Cancelled bookings</h2>

    <div id="cancelledbookings"></div>
  </div>
  <!-- End content area -->

  <? include('includes/layoutscript.inc.php') ?>
</body>
</html>

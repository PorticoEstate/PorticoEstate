<html>
	<head>
		<script language="JavaScript" type="text/javascript">
			<!--
      function submitWindowSize()
      {
        window.moveTo(0,0);
        window.resizeTo(screen.width,screen.height);
        document.HiddenForm.screen_width.value  = screen.width;
        document.HiddenForm.screen_height.value = screen.height;
        document.HiddenForm.submit();
      }
    //-->
    	</script>
	</head>
	<body onLoad="submitWindowSize();">
		<form method="POST" Name="HiddenForm" action="{redirect_url}">
			<input type="hidden" name="screen_width" value="800">
			<input type="hidden" name="screen_height" value="600">
			<input type="hidden" name="menuaction" value="projects.uistatistics.project_gantt">
			<input type="hidden" name="sessionid" value="{sessionid}">
			<input type="hidden" name="project_id" value="{project_id}">
			<input type="hidden" name="action" value="{action}">
			<input type="hidden" name="gantt_popup" value="1">
		</form>
	</body>
</html>

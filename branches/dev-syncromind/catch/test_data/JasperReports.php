<?php

 	//http://blogs.vinuthomas.com/2007/11/22/installing-the-php-java-bridge-in-ubuntu-gutsy-gibbon/
 	//http://jagadmaya.com/integration-phpjasperreports.html
 	
 	$system = new Java('java.lang.System');
 	echo 'Java version=' . $system->getProperty('java.version') . ' <br />';
 	echo 'Java vendor=' . $system->getProperty('java.vendor') . '<br />';
 	echo 'OS=' . $system->getProperty('os.name') . ' ' .$system->getProperty('os.version') . ' on ' .$system->getProperty('os.arch') . '<br />';
 	// Example :
 	/*
 		java.util.Date$formater = new Java('java.text.SimpleDateFormat',"EEEE, MMMM dd, yyyy 'at' h:mm:ss a zzzz");
 		echo $formater->format(new Java('java.util.Date'));
 	*/

	$reportsPath ="/home/ccharly/publichtml/utils/reports/";
	$reportFileName = "CommandesClients1";
	$jasperReportsLib = "/home/ccharly/publichtml/utils/jasperlib";
	if(extension_loaded('java'))
	{
		$handle = @opendir($jasperReportsLib);
	
	while(($new_item = readdir($handle))!==false)
	{
		$java_library_path .= 'file:'.$jasperReportsLib.'/'.$new_item .';';
	}
	
	try
	{
		java_require($java_library_path);
		$Conn = new Java("org.altic.jasperReports.JdbcConnection");// driver 
		$Conn->setDriver("com.mysql.jdbc.Driver");// url de connexion
		$Conn->setConnectString("jdbc:mysql://localhost/erpmart");
		$Conn->setUser("root");// mot de passe
		$Conn->setPassword(null);

		$sJcm = new JavaClass("net.sf.jasperreports.engine.JasperCompileManager");

		$report = $sJcm->compileReport($reportsPath .$reportFileName.".jrxml");

		$sJfm = new JavaClass("net.sf.jasperreports.engine.JasperFillManager");

		$print = $sJfm->fillReport(

		$report,

		new Java("java.util.HashMap"),

		$Conn->getConnection());

		$sJem = new JavaClass("net.sf.jasperreports.engine.JasperExportManager");

		$sJem->exportReportToPdfFile($print, $reportsPath .$reportFileName.".pdf");

		if (file_exists($reportsPath .$reportFileName.".pdf"))
		{
			header('Content-disposition: attachment; filename="'.$reportFileName.'.pdf"');
			header('Content-Type: application/pdf');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '. @filesize($reportsPath . $reportFileName.".pdf"));
			header('Pragma: no-cache');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0');

			set_time_limit(0);

			@readfile($reportsPath .$reportFileName.".pdf") or die("problem occurs.");
		}

	}
	
	catch (JavaException $ex)
	{
		$trace = new Java("java.io.ByteArrayOutputStream");
		$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
		print "java stack trace: $trace\n";
	}

}

?>
